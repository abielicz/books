<?php
/**
 * Tag repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;

/**
 * Class TagRepository.
 *
 * @package Repository
 */
class TagRepository
{
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


}