<?php

namespace Project\db;

use Exception;
use Project\dictionaries\db\OrderDirectionsDictionary;
use ReflectionException;
use Project\dictionaries\db\JoinTypesDictionary;
use Project\dictionaries\db\MethodsDictionary;

/**
 * Class QueryInstance
 * @package Project\db
 */
class QueryInstance
{
    /**
     * @var string
     */
    private $method;
    /**
     * @var string
     */
    private $tableName;
    /**
     * @var array
     */
    private $sqlData;
    /**
     * @var string
     */
    private $fields;
    /**
     * @var array
     */
    private $join;
    /**
     * @var array
     */
    private $where;
    /**
     * @var string
     */
    private $groupBy;
    /**
     * @var string
     */
    private $having;
    /**
     * @var array
     */
    private $order;
    /**
     * @var string
     */
    private $limit = 20;
    /**
     * @var bool
     */
    private $one;
    /**
     * @var array
     */
    private $data;

    /**
     * QueryInstance constructor.
     *
     * Example:
     *
     * new QueryInstance('create', 'table1', ['id' => 1, 'name' => 'Peter'])
     *
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
    }

    /**
     * Example:
     *
     * query('SELECT id, name FROM table1 WHERE id > :id', ['id' => 10])
     *
     * @param string $sql
     * @param array $bindValues
     * @return QueryInstance
     */
    public function query(string $sql, array $bindValues = []): QueryInstance
    {
        $this->sqlData = [$sql, $bindValues];
        return $this;
    }

    /**
     * Example:
     *
     * select('table1.id, table2.name')
     *
     * @param string $fields
     * @return QueryInstance
     */
    public function select(string $fields): QueryInstance
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Example:
     *
     * join('table2', 'table2.id = table1.post_id', 'LEFT')
     *
     * @param string $joinedTable
     * @param string $joinedCondition
     * @param string $typeOfJoin
     * @return QueryInstance
     * @throws ReflectionException
     */
    public function join(string $joinedTable, string $joinedCondition, string $typeOfJoin = ''): QueryInstance
    {
        if (!in_array($typeOfJoin, JoinTypesDictionary::get())) {
            throw new Exception('Valid type of join required');
        }
        $this->join[] = [$joinedTable, $joinedCondition, $typeOfJoin];
        return $this;
    }

    /**
     * Example:
     *
     * where('id = :id OR name = :name', ['id' => 1, 'name' => 'Peter'], ['id' => PDO::PARAM_INT])
     *
     * @param string $condition
     * @param array $bindValues
     * @return QueryInstance
     */
    public function where(string $condition, array $bindValues = [], array $bindValuesTypes = []): QueryInstance
    {
        $this->where[] = [$condition, $bindValues, $bindValuesTypes];
        return $this;
    }

    /**
     * Example:
     *
     * groupBy('table1.id')
     *
     * @param string $condition
     * @return QueryInstance
     */
    public function groupBy(string $condition): QueryInstance
    {
        $this->groupBy = $condition;
        return $this;
    }

    /**
     * Example:
     *
     * having('postsCount > 5')
     *
     * @param string $condition
     * @return QueryInstance
     */
    public function having(string $condition): QueryInstance
    {
        $this->having = $condition;
        return $this;
    }

    /**
     * Example:
     *
     * orderBy('table.created_at', 'DESC')
     *
     * @param string $condition
     * @param string $orderDirection
     * @return QueryInstance
     * @throws ReflectionException
     */
    public function orderBy(string $condition, string $orderDirection = OrderDirectionsDictionary::ASC): QueryInstance
    {
        if (!in_array($orderDirection, OrderDirectionsDictionary::get())) {
            throw new Exception('Valid order direction required');
        }
        $this->order[] = [$condition, $orderDirection];
        return $this;
    }

    /**
     * Example:
     *
     * limit(10, 10)
     *
     * @param int $count
     * @param int $offset
     * @return QueryInstance
     */
    public function limit(int $count, int $offset = 0): QueryInstance
    {
        $this->limit = $count;
        if ($offset > 0) {
            $this->limit = $offset . ', ' . $this->limit;
        }
        return $this;
    }

    /**
     * @return QueryInstance
     */
    public function one(): QueryInstance
    {
        $this->one = true;
        return $this;
    }

    /**
     * @return null|array
     */
    public function getSqlData(): ?array
    {
        return $this->sqlData;
    }

    /**
     * @return null|string
     */
    public function getFields(): ?string
    {
        return $this->fields;
    }

    /**
     * @return null|array
     */
    public function getJoin(): ?array
    {
        return $this->join;
    }

    /**
     * @return null|array
     */
    public function getWhere(): ?array
    {
        return $this->where;
    }

    /**
     * @return null|string
     */
    public function getGroupBy(): ?string
    {
        return $this->groupBy;
    }

    /**
     * @return null|string
     */
    public function getHaving(): ?string
    {
        return $this->having;
    }

    /**
     * @return null|array
     */
    public function getOrder(): ?array
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getLimit(): string
    {
        return $this->limit;
    }

    /**
     * @return null|bool
     */
    public function getOne(): ?bool
    {
        return $this->one;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
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

        return (new QueryBuilder($this))->$method();
    }
}
