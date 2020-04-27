<?php

namespace Project;

/***
 * Interface Logger
 * @package Project
 */
interface Logger
{
    /**
     * @param string $errorType
     * @param string $errorTitle
     * @param string $errorMessage
     *
     * @return void
     */
    public function log(string $errorType, string $errorTitle, string $errorMessage);
}
