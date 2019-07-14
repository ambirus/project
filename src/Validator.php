<?php

namespace Project;

abstract class Validator
{
    private $on = true;

    public function isActive()
    {
        return empty($this->on) || $this->on;
    }
}
