<?php

namespace Project\db;

use PDO;
use Exception;
use PDOStatement;

/**
 * Class QueryBulider
 * @package Project\db
 */
class QueryBuilder
{
    /**
     * @var PDO
     */
    private $connection;

    /**
     * QueryBulider constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->connection = Mysql::getConnection();
    }

    /**
     * @param QueryInstance $queryInstance
     * @return string
     * @throws Exception
     */
    public function makeCreate(QueryInstance $queryInstance): string
    {
        $data = $queryInstance->getData();

        if (count($data) === 0) {
            throw new Exception("Data required for create method");
        }

        $columns = $values = $preparedData = [];

        foreach ($data as $k => $v) {
            $columns[] = $k;
            $values[] = ':' . $k;
            $preparedData[$k] = $v;
        }

        $sql = "INSERT INTO `" . $queryInstance->getTableName() . "`
        (" . implode(', ', $columns) . ")
        VALUES (" . implode(',', $values) . ")";

        $query = $this->connection->prepare($sql);

        $this->execute($query, $preparedData);

        return $this->connection->lastInsertId();
    }

    /**
     * @param QueryInstance $queryInstance
     * @return array|mixed
     * @throws Exception
     */
    public function makeRead(QueryInstance $queryInstance)
    {
        $sql = $queryInstance->getSql() ?? $this->buildSql($queryInstance);

        $query = $this->connection->prepare($sql);

        $whereParam = $queryInstance->getWhere();
        if (!is_null($whereParam)) {
            foreach ($whereParam as $param) {
                if (!empty($param[1])) {
                    foreach ($param[1] as $key => $value) {
                        $query->bindValue(':' . $key, $value);
                    }
                }
            }
        }

        $this->execute($query);

        return $queryInstance->getOne() ? $query->fetch() : $query->fetchAll();
    }

    /**
     * @param QueryInstance $queryInstance
     * @return bool
     * @throws Exception
     */
    public function makeUpdate(QueryInstance $queryInstance): bool
    {
        $data = $queryInstance->getData();

        if (count($data) === 0) {
            throw new Exception("Data required for update method");
        }

        $columnsValues = $preparedData = [];

        foreach ($data as $k => $v) {
            $columnsValues[] = $k . ' = ' . ':' . $k;
            $preparedData[$k] = $v;
        }

        $whereParam = $queryInstance->getWhere();

        if (!is_null($whereParam)) {
            foreach ($whereParam as $param) {
                if (!empty($param[1])) {
                    foreach ($param[1] as $key => $value) {
                        $preparedData[$key] = $value;
                    }
                }
            }
        }

        $where = $this->getWhereCondition($whereParam);

        $sql = "UPDATE `" . $queryInstance->getTableName() . "`        
        SET " . implode(',', $columnsValues) . " {$where}";

        $query = $this->connection->prepare($sql);

        return $this->execute($query, $preparedData);
    }

    /**
     * @param QueryInstance $queryInstance
     * @return bool
     * @throws Exception
     */
    public function makeDelete(QueryInstance $queryInstance): bool
    {
        $whereParam = $queryInstance->getWhere();
        $where = $this->getWhereCondition($whereParam);

        $sql = "DELETE FROM `{$queryInstance->getTableName()}` {$where}";

        $query = $this->connection->prepare($sql);

        return $this->execute($query);
    }

    /**
     * @param QueryInstance $queryInstance
     * @return string
     */
    private function buildSql(QueryInstance $queryInstance): string
    {
        $fields = $queryInstance->getFields() ?? "`{$queryInstance->getTableName()}`.*";
        $join = $where = $groupBy = $having = $order = '';
        $limit = ' LIMIT ' . $queryInstance->getLimit();

        $joinParam = $queryInstance->getJoin();

        if (!is_null($joinParam)) {
            foreach ($joinParam as $value) {
                $join .= ' ' . $value[2] . ' JOIN ' . $value[0] . ' ON ' . $value[1];
            }
        }

        $whereParam = $queryInstance->getWhere();
        $where = $this->getWhereCondition($whereParam);

        if (!is_null($queryInstance->getGroupBy())) {
            $groupBy = ' GROUP BY ' . $queryInstance->getGroupBy();
        }

        if (!is_null($queryInstance->getHaving())) {
            $having = ' HAVING ' . $queryInstance->getHaving();
        }

        $orderParam = $queryInstance->getOrder();

        if (!is_null($orderParam)) {
            $order = ' ORDER BY ';
            foreach ($orderParam as $value) {
                $tmp[] = ' ' . $value[0] . ' ' . $value[1];
            }
            $order .= implode(', ', $tmp);
        }

        return "SELECT " . $fields . " 
        FROM `{$queryInstance->getTableName()}` 
        {$join} 
        {$where} 
        {$groupBy} 
        {$having} 
        {$order} 
        {$limit}";
    }

    /**
     * @param array|null $whereParam
     * @return string
     */
    private function getWhereCondition(?array $whereParam): string
    {
        $where = '';

        if (!is_null($whereParam)) {
            foreach ($whereParam as $value) {
                $conditions[] = $value[0];
            }
            $where = ' WHERE ' . implode(' AND ', $conditions);
        }

        return $where;
    }

    private function execute(PDOStatement $query, array $preparedData = []): bool
    {
        $res = $query->execute($preparedData);
        if (!$res) {
             throw new Exception("DB error while performing the query: " . $sql . " | Errors: " .
                json_encode($query->errorInfo()));
        }

        return true;
    }
}
