<?php

namespace Project\managers;

use Project\exceptions\HttpNotFoundException;

/**
 * Class FileManager.
 */
class FileManager
{
    /**
     * @param string $fileName
     * @param string $errorMessage
     *
     * @throws HttpNotFoundException
     *
     * @return false|string
     */
    public function get(string $fileName, string $errorMessage = '')
    {
        if (!file_exists($fileName)) {
            $errorMessage = $errorMessage ?? 'File '.$fileName." doesn't exist";
            throw new HttpNotFoundException($errorMessage, 404);
        }

        return file_get_contents($fileName);
    }

    /**
     * @param string $fileName
     * @param string $content
     * @param bool   $isAppend
     *
     * @return bool|int
     */
    public function put(string $fileName, string $content, $isAppend = false)
    {
        $isAppendFlag = $isAppend ? FILE_APPEND : FILE_BINARY;

        return file_put_contents($fileName, $content, $isAppendFlag);
    }
}
