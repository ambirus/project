<?php

namespace Project\db;

use PDO;
use Exception;
use PDOStatement;
use Project\exceptions\DbException;
use Project\values\db\PreparedDataValue;

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

        $preparedDataValue = $this->getPreparedDataValue($data);

        $sql = "INSERT INTO `" . $queryInstance->getTableName() . "`
        (" . implode(', ', $preparedDataValue->getColumns()) . ")
        VALUES (" . implode(',', $preparedDataValue->getValues()) . ")";

        $query = $this->connection->prepare($sql);

        $this->execute($query, $preparedDataValue->getPreparedData());

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
        $preparedData = [];
        $whereParam = $queryInstance->getWhere();

        if (!is_null($whereParam)) {
            $data = [];
            foreach ($whereParam as $param) {
                if (isset($param[1])) {
                    foreach ($param[1] as $key => $value) {
                        $data[$key] = $value;
                    }
                }
            }
            $preparedDataValue = $this->getPreparedDataValue($data);
            $preparedData = $preparedDataValue->getPreparedData();
        }

        $query = $this->connection->prepare($sql);
        $this->execute($query, $preparedData);

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

        $preparedDataValue = $this->getPreparedDataValue($data);

        $columns = $preparedDataValue->getColumns();
        $values = $preparedDataValue->getValues();
        $preparedData = $preparedDataValue->getPreparedData();

        foreach ($columns as $key => $column) {
            $columnsValues[] = $column . ' = ' . $values[$key];
        }

        $whereParam = $queryInstance->getWhere();

        if (!is_null($whereParam)) {
            $data = [];
            foreach ($whereParam as $param) {
                if (isset($param[1])) {
                    foreach ($param[1] as $key => $value) {
                        $data[$key] = $value;
                    }
                }
            }
            $preparedDataValue = $this->getPreparedDataValue($data);
            $preparedData += $preparedDataValue->getPreparedData();
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
        $preparedData = [];
        $whereParam = $queryInstance->getWhere();

        if (!is_null($whereParam)) {
            $data = [];
            foreach ($whereParam as $param) {
                if (isset($param[1])) {
                    foreach ($param[1] as $key => $value) {
                        $data[$key] = $value;
                    }
                }
            }
            $preparedDataValue = $this->getPreparedDataValue($data);
            $preparedData = $preparedDataValue->getPreparedData();
        }

        $where = $this->getWhereCondition($whereParam);

        $sql = "DELETE FROM `{$queryInstance->getTableName()}` {$where}";

        $query = $this->connection->prepare($sql);

        return $this->execute($query, $preparedData);
    }

    /**
     * @param array $data
     * @return PreparedDataValue
     */
    private function getPreparedDataValue(array $data): PreparedDataValue
    {
        $preparedDataValue = new PreparedDataValue();

        foreach ($data as $k => $v) {
            $preparedDataValue->setColumns($k);
            $preparedDataValue->setValues(':' . $k);
            $preparedDataValue->setPreparedData([$k => $v]);
        }

        return $preparedDataValue;
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

    /**
     * @param PDOStatement $query
     * @param array $preparedData
     * @return bool
     * @throws DbException
     */
    private function execute(PDOStatement $query, array $preparedData = []): bool
    {
        $res = $query->execute($preparedData);
        if (!$res) {
            throw new DbException("DB error while performing the query: " . $sql . " | Errors: " .
                json_encode($query->errorInfo()));
        }

        return true;
    }
}
