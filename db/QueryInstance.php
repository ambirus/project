<?php

namespace lib\db;

use Exception;

class QueryInstance
{
    private $method;
    private $tableName;
    private $connection;
    private $fields;
    private $joinCondition;
    private $whereCondition;
    private $limit;
    private $order;
    private $oneCondition;
    private $data;

    /**
     * QueryInstance constructor.
     * @param string $method
     * @param string $tableName
     * @param array $data
     * @throws Exception
     */
    public function __construct(string $method, string $tableName, array $data = [])
    {
        $this->method = $method;
        $this->tableName = $tableName;
        $this->data = $data;
        $this->connection = Mysql::getConnection();
    }

    /**
     * @param string $fields
     * @return QueryInstance
     */
    public function select(string $fields): QueryInstance
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @param string $joinCondition
     * @return QueryInstance
     */
    public function join(string $joinCondition): QueryInstance
    {
        $this->joinCondition = $joinCondition;
        return $this;
    }

    /**
     * @param string $condition
     * @return QueryInstance
     */
    public function where(string $condition): QueryInstance
    {
        $this->whereCondition = $condition;
        return $this;
    }

    public function groupBy()
    {
        return $this;
    }

    public function having()
    {
        return $this;
    }

    public function orderBy(string $orderCondition, string $orderDirection = 'ASC')
    {
        $this->order = $orderCondition . ' ' . $orderDirection;
        return $this;
    }

    public function limit(int $count = 20)
    {
        $this->limit = $count;
        return $this;
    }

    public function one()
    {
        $this->oneCondition = true;
        return $this;
    }

    /**
     * @return array|mixed|string
     * @throws Exception
     */
    public function execute()
    {
        if ($this->method === 'create') {
            return $this->makeCreate();
        }

        if ($this->method === 'read') {
            return $this->makeRead();
        }

        if ($this->method === 'update') {
            return $this->makeUpdate();
        }

        if ($this->method === 'delete') {
            return $this->makeDelete();
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    private function makeCreate()
    {
        if (count($this->data) === 0) {
            throw new Exception("Data required");
        }

        $columns = $values = $preparedData = [];

        foreach ($this->data as $k => $v) {
            $columns[] = $k;
            $values[] = ':' . $k;
            $preparedData[$k] = $v;
        }

        $sql = "INSERT INTO `" . $this->tableName . "`
        (" . implode(', ', $columns) . ")
        VALUES (" . implode(',', $values) . ")";

        $query = $this->connection->prepare($sql);
        $query->execute($preparedData);

        return $this->connection->lastInsertId();
    }

    /**
     * @return array|mixed
     */
    private function makeRead()
    {
        $fields = "`{$this->tableName}`.*";

        if (!is_null($this->fields)) {
            $fields = $this->fields;
        }
        $where = '';
        if (!is_null($this->whereCondition)) {
            $where = ' WHERE ' . $this->whereCondition;
        }
        $join = '';
        if (!is_null($this->joinCondition)) {
            $join = ' JOIN ' . $this->joinCondition;
        }

        $limit = '';
        if (!is_null($this->limit)) {
            $limit = ' LIMIT ' . $this->limit;
        }

        $order = '';
        if (!is_null($this->order)) {
            $order = ' ORDER BY ' . $this->order;
        }

        $sql = "SELECT " . $fields . " FROM `{$this->tableName}` {$join} {$where} {$order} {$limit}";

        $query = $this->connection->query($sql);

        if ($this->oneCondition) {
            return $query->fetch();
        } else {
            return $query->fetchAll();
        }
    }

    /**
     * @throws Exception
     */
    public function makeUpdate()
    {
        if (count($this->data) === 0) {
            throw new Exception("Data required");
        }

        $columns = $values = $preparedData = [];

        foreach ($this->data as $k => $v) {
            $columns[] = $k;
            $values[] = ':' . $k;
            $preparedData[$k] = $v;
        }

        $where = '';
        if (!is_null($this->whereCondition)) {
            $where = ' WHERE ' . $this->whereCondition;
        }

        $sql = "UPDATE `" . $this->tableName . "`
        (" . implode(', ', $columns) . ")
        SET (" . implode(',', $values) . ") {$where}";

        $query = $this->connection->prepare($sql);

        return $query->execute($preparedData);
    }

    /**
     * @return bool
     */
    public function makeDelete()
    {
        $where = '';
        if (!is_null($this->whereCondition)) {
            $where = ' WHERE ' . $this->whereCondition;
        }
        $sql = "DELETE FROM `{$this->tableName}` {$where}";
        $query = $this->connection->prepare($sql);

        return $query->execute();
    }
}