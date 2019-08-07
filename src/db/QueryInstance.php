<?php

namespace Project\db;

use Exception;
use Project\dictionaries\db\MethodsDictionary;

class QueryInstance
{
    private $method;
    private $tableName;
    private $connection;
    private $sql;
    private $fields;
    private $joinCondition;
    /**
     * @var string
     */
    private $whereCondition;
    /**
     * @var array
     */
    private $whereParams = [];
    private $groupByCondition;
    private $havingCondition;
    private $limit = 2;
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
     * @param string $sql
     * @return QueryInstance
     */
    public function query(string $sql): QueryInstance
    {
        $this->sql = $sql;
        return $this;
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
        $this->joinCondition[] = 'JOIN ' . $joinCondition;
        return $this;
    }

    /**
     * @param string $condition
     * @param array $params
     * @return QueryInstance
     */
    public function where(string $condition, array $params = []): QueryInstance
    {
        $this->whereCondition = $condition;
        $this->whereParams = $params;
        return $this;
    }

    /**
     * @param string $condition
     * @return QueryInstance
     */
    public function groupBy(string $condition): QueryInstance
    {
        $this->groupByCondition = $condition;
        return $this;
    }

    /**
     * @param string $condition
     * @return QueryInstance
     */
    public function having(string $condition): QueryInstance
    {
        $this->havingCondition = $condition;
        return $this;
    }

    /**
     * @param string $orderCondition
     * @param string $orderDirection
     * @return $this
     */
    public function orderBy(string $orderCondition, string $orderDirection = 'ASC')
    {
        $this->order = $orderCondition . ' ' . $orderDirection;
        return $this;
    }

    /**
     * @param int $count
     * @return $this
     */
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
        if (!in_array($this->method, MethodsDictionary::get())) {
            throw new Exception('CRUD method required');
        }

        $method = 'make' . ucfirst($this->method);

        return $this->$method();
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
        if ($this->sql !== null) {
            $sql = $this->sql;
        } else {
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
                $join = implode(' ', $this->joinCondition);
            }

            $limit = '';
            if (!is_null($this->limit)) {
                $limit = ' LIMIT ' . $this->limit;
            }

            $order = '';
            if (!is_null($this->order)) {
                $order = ' ORDER BY ' . $this->order;
            }

            $groupBy = '';
            if (!is_null($this->groupByCondition)) {
                $groupBy = ' GROUP BY ' . $this->groupByCondition;
            }

            $sql = "SELECT " . $fields . " FROM `{$this->tableName}` {$join} {$where} {$groupBy} {$order} {$limit}";
        }

        $query = $this->connection->prepare($sql);

        if (!empty($this->whereParams)) {
            foreach ($this->whereParams as $key => $param) {
                $query->bindValue(':' . $key, $param);
            }
        }

        $res = $query->execute();

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

        $columnsValues = $preparedData = [];

        foreach ($this->data as $k => $v) {
            $columnsValues[] = $k . ' = ' . ':' . $k;
            $preparedData[$k] = $v;
        }

        $where = '';
        if (!is_null($this->whereCondition)) {
            $where = ' WHERE ' . $this->whereCondition;
        }

        $sql = "UPDATE `" . $this->tableName . "`        
        SET " . implode(',', $columnsValues) . " {$where}";

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
