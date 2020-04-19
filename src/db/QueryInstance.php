<?php

namespace Project\db;

use Exception;
use ReflectionException;
use Project\dictionaries\db\MethodsDictionary;
use Project\dictionaries\db\JoinTypesDictionary;
use Project\dictionaries\db\OrderDirectionsDictionary;

/**
 * Class QueryInstance.
 */
class QueryInstance
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var Table
     */
    private $tableInstance;

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
    private $limit = '20';

    /**
     * @var string
     */
    private $offset;

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
     * new QueryInstance('create', new Users(), ['id' => 1, 'name' => 'Peter'])
     *
     * @param string $method
     * @param Table  $tableInstance
     * @param array  $data
     *
     * @throws Exception
     */
    public function __construct(string $method, Table $tableInstance, array $data = [])
    {
        $this->method = $method;
        $this->tableInstance = $tableInstance;
        $this->data = $data;
    }

    /**
     * Example:.
     *
     * query('SELECT id, name FROM table1 WHERE id > :id', ['id' => [$id, PDO::PARAM_INT]])
     *
     * @param string $sql
     * @param array  $bindValues
     *
     * @return QueryInstance
     */
    public function query(string $sql, array $bindValues = []): self
    {
        $this->sqlData = [$sql, $bindValues];

        return $this;
    }

    /**
     * Example:.
     *
     * select('table1.id, table2.name')
     *
     * @param string $fields
     *
     * @return QueryInstance
     */
    public function select(string $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Example:.
     *
     * join('table2', 'table2.id = table1.post_id', 'LEFT')
     *
     * @param string $joinedTable
     * @param string $joinedCondition
     * @param string $typeOfJoin
     *
     * @throws ReflectionException
     *
     * @return QueryInstance
     */
    public function join(string $joinedTable, string $joinedCondition, string $typeOfJoin = ''): self
    {
        if (!in_array($typeOfJoin, JoinTypesDictionary::get())) {
            throw new Exception('Valid type of join required');
        }
        $this->join[] = [$joinedTable, $joinedCondition, $typeOfJoin];

        return $this;
    }

    /**
     * Example:.
     *
     * where('id = :id OR name = :name', ['id' => [$id, PDO::PARAM_INT], 'name' => $name])
     *
     * @param string $condition
     * @param array  $bindValues
     *
     * @return QueryInstance
     */
    public function where(string $condition, array $bindValues = []): self
    {
        $this->where[] = [$condition, $bindValues];

        return $this;
    }

    /**
     * Example:.
     *
     * groupBy('table1.id')
     *
     * @param string $condition
     *
     * @return QueryInstance
     */
    public function groupBy(string $condition): self
    {
        $this->groupBy = $condition;

        return $this;
    }

    /**
     * Example:.
     *
     * having('postsCount > 5')
     *
     * @param string $condition
     *
     * @return QueryInstance
     */
    public function having(string $condition): self
    {
        $this->having = $condition;

        return $this;
    }

    /**
     * Example:.
     *
     * orderBy('table.created_at', 'DESC')
     *
     * @param string $condition
     * @param string $orderDirection
     *
     * @throws ReflectionException
     *
     * @return QueryInstance
     */
    public function orderBy(string $condition, string $orderDirection = OrderDirectionsDictionary::ASC): self
    {
        if (!in_array($orderDirection, OrderDirectionsDictionary::get())) {
            throw new Exception('Valid order direction required');
        }
        $this->order[] = [$condition, $orderDirection];

        return $this;
    }

    /**
     * Example:.
     *
     * limit(10)
     *
     * @param int $count
     *
     * @return QueryInstance
     */
    public function limit(int $count): self
    {
        $this->limit = $count;

        return $this;
    }

    /**
     * Example:.
     *
     * offset(10)
     *
     * @param int $count
     *
     * @return QueryInstance
     */
    public function offset(int $count): self
    {
        if ($count > 0) {
            $this->limit = $count.', '.$this->limit;
        }

        return $this;
    }

    /**
     * @return QueryInstance
     */
    public function one(): self
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
     * @return Table
     */
    public function getTableInstance(): Table
    {
        return $this->tableInstance;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @throws ReflectionException
     *
     * @return mixed
     */
    public function execute()
    {
        if (!in_array($this->method, MethodsDictionary::get())) {
            throw new Exception('CRUD method required');
        }

        $method = 'make'.ucfirst($this->method);

        $result = (new QueryBuilder($this))->$method();

        if (!empty($result['items'])) {
            $this->tableInstance->setTotalCount($result['totalCount']);

            return $result['items'];
        }

        return $result;
    }
}
