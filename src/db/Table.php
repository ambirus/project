<?php

namespace Project\db;

use Exception;
use Project\dictionaries\db\MethodsDictionary;

/**
 * Class Table
 * @package Project\db
 */
abstract class Table
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $definitions = [];

    /**
     * Table constructor.
     * @throws Exception
     */
    public function __construct()
    {
        if (is_null($this->name)) {
            throw new Exception("Table name is required");
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    /**
     * Example:
     *
     * (new Post())
     * ->create(['title' => 'Post 1'])
     * ->execute();
     *
     * @param array $data
     * @return QueryInstance
     * @throws Exception
     */
    public function create(array $data): QueryInstance
    {
        return new QueryInstance(MethodsDictionary::CREATE, $this, $data);
    }

    /**
     * Example:
     *
     * (new Post())
     * ->read()
     * ->execute();
     *
     * @return QueryInstance
     * @throws Exception
     */
    public function read(): QueryInstance
    {
        return new QueryInstance(MethodsDictionary::READ, $this);
    }

    /**
     * Example:
     *
     * (new Post())
     * ->update(['title' => 'Post 2'])
     * ->execute();
     *
     * @param array $data
     * @return QueryInstance
     * @throws Exception
     */
    public function update(array $data): QueryInstance
    {
        return new QueryInstance(MethodsDictionary::UPDATE, $this, $data);
    }

    /**
     * Example:
     *
     * (new Post())
     * ->delete()
     * ->execute();
     *
     * @return QueryInstance
     * @throws Exception
     */
    public function delete(): QueryInstance
    {
        return new QueryInstance(MethodsDictionary::DELETE, $this);
    }
}
