<?php

namespace Project\db;

use Exception;
use Project\dictionaries\db\MethodsDictionary;

abstract class Table
{
    protected $name;

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
     * @param array $data
     * @return QueryInstance
     * @throws Exception
     */
    public function create(array $data): QueryInstance
    {
        return new QueryInstance(MethodsDictionary::CREATE, $this->name, $data);
    }

    /**
     * @return QueryInstance
     * @throws Exception
     */
    public function read(): QueryInstance
    {
        return new QueryInstance(MethodsDictionary::READ, $this->name);
    }

    /**
     * @param array $data
     * @return QueryInstance
     * @throws Exception
     */
    public function update(array $data): QueryInstance
    {
        return new QueryInstance(MethodsDictionary::UPDATE, $this->name, $data);
    }

    /**
     * @return QueryInstance
     * @throws Exception
     */
    public function delete(): QueryInstance
    {
        return new QueryInstance(MethodsDictionary::DELETE, $this->name);
    }
}
