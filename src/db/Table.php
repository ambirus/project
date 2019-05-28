<?php

namespace src\db;

use Exception;

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
     */
    public function create(array $data): QueryInstance
    {
        return new QueryInstance('create', $this->name, $data);
    }

    /**
     * @return QueryInstance
     */
    public function read(): QueryInstance
    {
        return new QueryInstance('read', $this->name);
    }

    /**
     * @return QueryInstance
     */
    public function update(): QueryInstance
    {
        return new QueryInstance('update', $this->name);
    }

    /**
     * @return QueryInstance
     */
    public function delete(): QueryInstance
    {
        return new QueryInstance('delete', $this->name);
    }
}