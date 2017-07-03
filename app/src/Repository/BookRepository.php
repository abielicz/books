<?php
/**
 * Created by PhpStorm.
 * User: Ada
 * Date: 30.06.2017
 * Time: 14:11
 */

namespace Repository;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Utils\Paginator;

/**
 * Class BookRepository.
 *
 * @package Repository
 */
class BookRepository
{

    const NUM_ITEMS=3;

    protected $db;

    public function __construct(Connection $db)
    {
        $this->db= $db;
        $this->tagRepository = new TagRepository($db);
    }



    public function findAll()
    {
        $queryBuilder = $this->queryAll();

        return $queryBuilder->execute()->fetchAll();
    }

    public function findAllPaginated($page = 1)
    {
        $countQueryBuilder = $this->queryAll()
            ->select('COUNT(DISTINCT book_id) AS total_results')
            ->setMaxResults(1);

        $paginator = new Paginator($this->queryAll(), $countQueryBuilder);
        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage(self::NUM_ITEMS);

        return $paginator->getCurrentPageResults();
    }


    public function findOneById($id)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->where('book_id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetch();

        if ($result) {
            $result['tags'] = $this->findLinkedTagsIds($result['book_id']);
        }

        return $result;
      //  dump( $result);
    }


    /**
     * @param $id
     * @return array
     * all for one book
     */
    public function findAllWhere($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('c.comment_id', 'u.login', 'c.book_id', 'c.matter')
            ->from('comments', 'c')
            ->innerJoin('c', 'users', 'u', 'u.user_id = c.user_id')
            ->where('c.book_id = :id')
            ->setParameter(':id', $id);

        $result = $queryBuilder->execute()->fetchAll();

        dump( $result);
        return $result;
    }
/*
    protected function findLinkedCategories($questionId)
    {
        $queryBuilder = $this->db->createQueryBuilder()
            ->select('c.id','c.nazwa')
            ->from('si_pytanie', 'q')
            ->innerJoin('q', 'si_kategoria', 'c', 'q.si_kategoria_id = c.id')
            ->where('q.id = :id')
            ->setParameter(':id', $questionId, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetchAll();
*/


        /**
     * Save record.
     *
     * @param array $book Book
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function save($book)
    {
        $this->db->beginTransaction();

        try {
            $currentDateTime = new \DateTime();
            $book['modified_at'] = $currentDateTime->format('Y-m-d H:i:s');
            $tagsIds = isset($book['tags']) ? array_column($book['tags'], 'tag_id') : [];
            unset($book['tags']);

            if (isset($book['book_id']) && ctype_digit((string) $book['book_id'])) {
                // update record
                $bookId = $book['book_id'];
                unset($book['book_id']);
                $this->removeLinkedTags($bookId);
                $this->addLinkedTags($bookId, $tagsIds);
                $this->db->update('books', $book, ['book_id' => $bookId]);
            } else {
                // add new record
                $book['created_at'] = $currentDateTime->format('Y-m-d H:i:s');

                $this->db->insert('books', $book);
                $bookId = $this->db->lastInsertId();
                $this->addLinkedTags($bookId, $tagsIds);
            }
            $this->db->commit();
        } catch (DBALException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Remove record.
     *
     * @param array $bookmark Book
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return boolean Result
     */
    public function delete($book)
    {
        $this->db->beginTransaction();

        try {
            $this->removeLinkedTags($book['book_id']);
            $this->db->delete('books', ['book_id' => $book['book_id']]);
            $this->db->commit();
        } catch (DBALException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Finds linked tags Ids.
     *
     * @param int $bookmarkId Book Id
     *
     * @return array Result
     */
    protected function findLinkedTagsIds($bookId)
    {
        $queryBuilder = $this->db->createQueryBuilder()
            ->select('books_tags.tag_id' )
            ->from('books_tags')
            ->where('book_id = :book_id')
            ->setParameter(':book_id', $bookId, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetchAll();

        return isset($result) ? array_column($result, 'tag_id') : [];
    }
    /**
     * Remove linked tags.
     *
     * @param int $bookId Book Id
     *
     * @return boolean Result
     */
    protected function removeLinkedTags($bookId)
    {
        return $this->db->delete('books_tags', ['book_id' => $bookId]);
    }

    /**
     * Add linked tags.
     *
     * @param int $bookId Book Id
     * @param array $tagsIds Tags Ids
     */
    protected function addLinkedTags($bookId, $tagsIds)
    {
        if (!is_array($tagsIds)) {
            $tagsIds = [$tagsIds];
        }

        foreach ($tagsIds as $tagId) {
            $this->db->insert(
                'books_tags',
                [
                    'book_id' => $bookId,
                    'tag_id' => $tagId,
                ]
            );
        }
    }

    /**
     * Query all records.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder Result
     */
    protected function queryAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select(
            'books.book_id',
            'books.title',
            'books.author'
        )->from('books');
    }

}