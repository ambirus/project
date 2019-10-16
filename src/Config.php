<?php

namespace Project;

use Exception;
use Project\exceptions\HttpNotFoundException;
use Project\managers\FileManager;

/**
 * Class Config
 * @package Project
 */
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
     * @param string $path
     * @return array
     * @throws Exception
     */
    public function get(string $path): array
    {
        if (strpos($path, '.php')) {
            return $this->getFromFile($path);
        }

        return $this->parseFromEnv($path);

    }

    /**
     * @param string $param
     * @return array
     * @throws Exception
     */
    private function parseFromEnv(string $path): array
    {
        $envFile = $this->configPath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            '.env';

        if (!file_exists($envFile)) {
            throw new Exception('".env" file is not found!');
        }

        $content = file_get_contents($envFile);
        $configArr = parse_ini_string($content, true, INI_SCANNER_TYPED);

        $blockParts = explode('.', $path);

        if (empty($configArr[$blockParts[0]])) {
            throw new Exception('Check ".env" file with path "' . $path . '"!');
        }

        if (isset($blockParts[1]) && isset($configArr[$blockParts[0]][$blockParts[1]])) {
            return [
                $configArr[$blockParts[0]][$blockParts[1]]
            ];
        }

        return $configArr[$blockParts[0]];
    }

    /**
     * @param string $file
     * @return array
     * @throws Exception
     */
    private function getFromFile(string $file): array
    {
        $configFile = $this->configPath . DIRECTORY_SEPARATOR . $file;

        if (!file_exists($configFile)) {
            throw new Exception('Config file "' . $file . '" is not found!');
        }

        return include $configFile;
    }
}
