<?php
/**
 * Tag repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;
use Utils\Paginator;

/**
 * Class TagRepository.
 *
 * @package Repository
 */
class TagRepository
{

    const NUM_ITEMS = 3;
    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    /**
     * TagRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Fetch all records.
     *
     * @return array Result
     */
    protected function queryAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select('tag_id', 'name')
            ->from('tags', 't');
    }


    public function findAll()
    {
     //   $query = 'SELECT `tag_id`, `name` FROM `tags`';
     //   return $this->db->fetchAll($query);
        $queryBuilder = $this->queryAll();

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Find one record.
     *
     * @param string $id Element id
     *
     * @return array|mixed Result
     */
    public function findOneById($id)
    {
     /*   $query = 'SELECT `tag_id`, `name` FROM `tags` WHERE tag_id= :id';
        $statement = $this->db->prepare($query);
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return !$result ? [] : current($result);
       */
        $queryBuilder = $this->queryAll();
        $queryBuilder->where('tag_id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetch();

        return !$result ? [] : $result;
    }


    public function findAllPaginated($page = 1)
    {
        $countQueryBuilder = $this->queryAll()
            ->select('COUNT(DISTINCT tag_id) AS total_results')
            ->setMaxResults(1);

        $paginator = new Paginator($this->queryAll(), $countQueryBuilder);
        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage(self::NUM_ITEMS);

        return $paginator->getCurrentPageResults();
        /*
            $queryBuilder = $this->queryAll();
            $queryBuilder->setFirstResult(($page - 1) * self::NUM_ITEMS)
                ->setMaxResults(self::NUM_ITEMS);

            $pagesNumber = $this->countAllPages();

            $paginator = [
                'page' => ($page < 1 || $page > $pagesNumber) ? 1 : $page,
                'max_results' => self::NUM_ITEMS,
                'pages_number' => $pagesNumber,
                'data' => $queryBuilder->execute()->fetchAll(),
            ];
         */
    }


    protected function countAllPages()
    {
        $pagesNumber = 1;

        $queryBuilder = $this->queryAll();
        $queryBuilder->select('COUNT(DISTINCT tag_id) AS total_results')
            ->setMaxResults(1);

        $result = $queryBuilder->execute()->fetch();

        if ($result) {
            $pagesNumber =  ceil($result['total_results'] / self::NUM_ITEMS);
        } else {
            $pagesNumber = 1;
        }

        return $pagesNumber;
    }


}