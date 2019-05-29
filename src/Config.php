<?php

namespace Project;

use Exception;

class Config
{
    /**
     * @var string
     */
    private $configPath;

    /**
     * Config constructor.
     *
     * @param string $configPath
     * @throws Exception
     */
    public function __construct(string $configPath)
    {
        if (!is_dir($configPath)) {
            throw new Exception('Config path is not directory');
        }
        $this->configPath = $configPath;
    }

    /**
     * @param null $file
     * @return array|mixed
     * @throws Exception
     */
    public function get($file = null)
    {
        $configFiles = glob(
            $this->configPath . DIRECTORY_SEPARATOR . ($file !== null ? $file : '*') . '.{php}',
            GLOB_BRACE
        );

        $countFiles = count($configFiles);

        if ($countFiles > 0) {
            $configArr = [];

            foreach ($configFiles as $configFile) {
                $key = str_replace([$this->configPath, '.php', '/', '\\'], '', $configFile);
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
