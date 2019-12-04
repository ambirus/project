<?php

namespace Project\db;

use PDO;
use Exception;
use PDOStatement;
use Project\dictionaries\db\ParamsDictionary;
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
     * @var QueryInstance
     */
    private $queryInstance;

    /**
     * QueryBuilder constructor.
     * @param QueryInstance $queryInstance
     * @throws Exception
     */
    public function __construct(QueryInstance $queryInstance)
    {
        $this->connection = Mysql::getConnection();
        $this->queryInstance = $queryInstance;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function makeCreate(): string
    {
        $data = $this->queryInstance->getData();

        if (count($data) === 0) {
            throw new Exception("Data required for create method");
        }

        $preparedDataValue = $this->getPreparedDataValue($data);

        $sql = "INSERT INTO `" . $this->queryInstance->getTableInstance()->getName() . "`
        (" . implode(', ', $preparedDataValue->getColumns()) . ")
        VALUES (" . implode(',', $preparedDataValue->getValues()) . ")";

        $query = $this->connection->prepare($sql);

        $this->execute($query, $preparedDataValue->getPreparedData());

        return $this->connection->lastInsertId();
    }

    /**
     * @return array|mixed
     * @throws Exception
     */
    public function makeRead()
    {
        $preparedData = [];
        $sqlData = $this->queryInstance->getSqlData();
        $preparedData = $this->getPreparedWheres();

        if (is_null($sqlData)) {
            $fields = $this->getFields();
            $joins = $this->getJoins();
            $wheres = $this->getWheres();
            $groupBy = $this->getGroupBy();
            $having = $this->getHaving();
            $order = $this->getOrder();
            $limit = $this->getLimit();

            $fields = "SQL_CALC_FOUND_ROWS " . $fields;

            $sql = "SELECT " . $fields . " 
            FROM `{$this->queryInstance->getTableInstance()->getName()}` 
            {$joins} 
            {$wheres} 
            {$groupBy} 
            {$having} 
            {$order} 
            {$limit}";
        } else {
            $sql = $sqlData[0];
            $bindValues = $sqlData[1];
            $preparedData += $bindValues;
        }

        $query = $this->connection->prepare($sql);
        $this->execute($query, $preparedData);

        if ($this->queryInstance->getOne()) {
            return $query->fetch();
        }

        $queryGetTotalCount = $this->connection->prepare("SELECT FOUND_ROWS() AS totalCount");
        $this->execute($queryGetTotalCount);
        $result = $queryGetTotalCount->fetch();
        $totalCount = $result['totalCount'];

        return ['totalCount' => $totalCount, 'items' => $query->fetchAll()];
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function makeUpdate(): bool
    {
        $data = $this->queryInstance->getData();

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

        $where = $this->getWheres();
        $preparedData += $this->getPreparedWheres();

        $sql = "UPDATE `" . $this->queryInstance->getTableInstance()->getName() . "`        
        SET " . implode(',', $columnsValues) . " {$where}";

        $query = $this->connection->prepare($sql);

        return $this->execute($query, $preparedData);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function makeDelete(): bool
    {
        $where = $this->getWheres();
        $preparedData = $this->getPreparedWheres();

        $sql = "DELETE FROM `{$this->queryInstance->getTableInstance()->getName()}` {$where}";

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
     * @return string
     */
    private function getFields(): string
    {
        return $this->queryInstance->getFields() ?? '`' . $this->queryInstance->getTableInstance()->getName() . '`' . '.*';
    }

    /**
     * @return string
     */
    private function getJoins(): string
    {
        $joins = $this->queryInstance->getJoin();

        if (is_null($joins)) {
            return '';
        }

        $joinArr = [];

        foreach ($joins as $join) {
            $joinArr[] = $join[2] . ' JOIN ' . $join[0] . ' ON ' . $join[1];
        }

        return implode(' ', $joinArr);
    }

    /**
     * @return string
     */
    private function getWheres(): string
    {
        $wheres = $this->queryInstance->getWhere();

        if (is_null($wheres)) {
            return '';
        }

        $whereArr = [];

        foreach ($wheres as $where) {
            $whereArr[] = $where[0];
        }

        return ' WHERE ' . implode(' AND ', $whereArr);
    }

    /**
     * @return string
     */
    private function getGroupBy(): string
    {
        $groupBy = $this->queryInstance->getGroupBy();

        if (is_null($groupBy)) {
            return '';
        }

        return ' GROUP BY ' . $groupBy;
    }

    /**
     * @return string
     */
    private function getHaving(): string
    {
        $having = $this->queryInstance->getHaving();

        if (is_null($having)) {
            return '';
        }

        return ' HAVING ' . $having;
    }

    /**
     * @return string
     */
    private function getOrder(): string
    {
        $orders = $this->queryInstance->getOrder();

        if (is_null($orders)) {
            return '';
        }

        foreach ($orders as $order) {
            $tmp[] = ' ' . $order[0] . ' ' . $order[1];
        }

        return ' ORDER BY ' . implode(', ', $tmp);
    }

    /**
     * @return string
     */
    private function getLimit(): string
    {
        $limit = $this->queryInstance->getLimit();
        return empty($limit) ? '' : ' LIMIT ' . $limit;
    }

    /**
     * @return array
     */
    private function getPreparedWheres(): array
    {
        $wheres = $this->queryInstance->getWhere();

        if (is_null($wheres)) {
            return [];
        }

        $whereArr = [];

        foreach ($wheres as $where) {
            if (isset($where[1])) {
                foreach ($where[1] as $k => $v) {
                    $whereArr[$k] = $v;
                }
            }
        }

        return $whereArr;
    }

    /**
     * @param PDOStatement $query
     * @param array $preparedData
     * @return bool
     * @throws DbException
     */
    private function execute(PDOStatement $query, array $preparedData = []): bool
    {
        if (!empty($preparedData)) {
            foreach ($preparedData as $value => $type) {
                $bindValue = $type;
                $bindType = ParamsDictionary::PARAM_STR;
                if (is_array($type)) {
                    $bindValue = $type[0];
                    $bindType = $type[1];
                }
                $query->bindValue(":{$value}", $bindValue, $bindType);
            }
        }
        
        if (!$query->execute()) {
            throw new DbException("DB error while performing the query: " . $query->queryString . " | Errors: " .
                json_encode($query->errorInfo()));
        }

        return true;
    }
}
