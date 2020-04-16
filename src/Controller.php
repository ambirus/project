<?php

namespace Project;

/**
 * Class Controller.
 */
abstract class Controller
{
    protected $view;

    public function __construct()
    {
        if (method_exists($this, 'init')) {
            $this->init();
        }

        $this->view = new View();
    }
}
