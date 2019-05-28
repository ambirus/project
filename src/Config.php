<?php

namespace Project;

use Exception;

class Config
{
    /**
     * @param null $file
     * @return mixed
     * @throws Exception
     */
    public static function get($file = null)
    {
        $configPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'configs';
        $configFiles = glob($configPath . DIRECTORY_SEPARATOR . ($file !== null ? $file : '*') . '.{php}',
            GLOB_BRACE);

        $countFiles = count($configFiles);

        if ($countFiles > 0) {
            $configArr = [];

            foreach ($configFiles as $configFile) {
                $key = str_replace([$configPath, '.php', '/', '\\'], '', $configFile);
                $configFile = include $configFile;
                if ($countFiles === 1) {
                    return $configFile;
                }
                $configArr[$key] = $configFile;
            }

            return $configArr;
        } else {
            throw new Exception("No configs files found!");
        }
    }
}