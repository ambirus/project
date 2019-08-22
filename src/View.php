<?php

namespace Project;

use Exception;
use Project\routers\WebRouter;

/**
 * Class View
 * @package Project
 */
class View
{
    /**
     * @var string
     */
    private $viewsPath;
    /**
     * @var string
     */
    private $templatesPath;
    /**
     * @var string
     */
    private $layoutFile;

    /**
     * View constructor.
     */
    public function __construct(string $viewsPath = '')
    {
        $this->viewsPath = __DIR__;
        $applicationPath = DIRECTORY_SEPARATOR . 'protected' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR;

        for ($i = 0; $i < 4; $i++) {
            $this->viewsPath .= DIRECTORY_SEPARATOR . '..';
        }

        if (!empty($viewsPath)) {
            $this->templatesPath = $this->viewsPath . $applicationPath . $viewsPath;
        } else {
            $this->viewsPath .= $applicationPath . 'views' . DIRECTORY_SEPARATOR;

            $this->layoutFile = $this->viewsPath . 'layouts' . DIRECTORY_SEPARATOR . 'main.php';

            if (!empty(WebRouter::getCurrentModuleName())) {
                $this->viewsPath = str_replace('application', 'application' . DIRECTORY_SEPARATOR . 'modules'
                    . DIRECTORY_SEPARATOR . WebRouter::getCurrentModuleName(), $this->viewsPath);
            }
            $this->templatesPath = $this->viewsPath . WebRouter::getCurrentControllerName();
        }
    }

    /**
     * @param string $view
     * @param array $params
     * @param bool $isPartial
     * @throws Exception
     */
    public function render(string $view, array $params = [], bool $isPartial = false)
    {
        $templateFile = $this->templatesPath . DIRECTORY_SEPARATOR . $view . '.php';

        if (file_exists($templateFile)) {
            ob_start();
            ob_implicit_flush(false);
            extract($params, EXTR_OVERWRITE);

            require $templateFile;

            $content = ob_get_clean();
            if ($isPartial) {
                echo $content;
                return;
            } else {
                $templateItems['content'] = $content;
            }
        } else {
            throw new Exception('No such template &laquo;' . $templateFile . '&raquo;! ');
        }

        ob_start();
        ob_implicit_flush(false);
        extract($templateItems, EXTR_OVERWRITE);

        require $this->layoutFile;

        echo ob_get_clean();
    }
}
