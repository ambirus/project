<?php

namespace Project\values\db;

/**
 * Class PreparedDataValue.
 */
class PreparedDataValue
{
    /**
     * @var array
     */
    private $columns;

    /**
     * @var array
     */
    private $values;

    /**
     * @var array
     */
    private $preparedData;

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return array
     */
    public function getPreparedData(): array
    {
        return $this->preparedData;
    }

    /**
     * @param string $columns
     */
    public function setColumns(string $columns)
    {
        $this->columns[] = $columns;
    }

    /**
     * @param string $values
     */
    public function setValues(string $values)
    {
        $this->values[] = $values;
    }

    /**
     * @param array $preparedData
     */
    public function setPreparedData(array $preparedData)
    {
        foreach ($preparedData as $key => $value) {
            $this->preparedData[$key] = $value;
        }
    }
}
