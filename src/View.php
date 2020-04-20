<?php

namespace Project;

use Exception;
use Project\routers\WebRouter;

/**
 * Class View.
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
    private $layoutPath;

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
        $applicationPath = DIRECTORY_SEPARATOR.'protected'.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR;

        for ($i = 0; $i < 4; $i++) {
            $this->viewsPath .= DIRECTORY_SEPARATOR.'..';
        }

        if (!empty($viewsPath)) {
            $this->templatesPath = $this->viewsPath.$applicationPath.$viewsPath;
        } else {
            $this->viewsPath .= $applicationPath.'views'.DIRECTORY_SEPARATOR;
            $this->layoutPath = $this->viewsPath.'layouts';

            if (!empty(WebRouter::getCurrentModuleName())) {
                $this->viewsPath = str_replace('application', 'application'.DIRECTORY_SEPARATOR.'modules'
                    .DIRECTORY_SEPARATOR.WebRouter::getCurrentModuleName(), $this->viewsPath);
                if (is_dir($this->viewsPath.'layouts')) {
                    $this->layoutPath = $this->viewsPath.'layouts';
                }
            }

            $this->templatesPath = $this->viewsPath.WebRouter::getCurrentControllerName();
        }
    }

    /**
     * @param string $view
     * @param array  $params
     * @param bool   $isPartial
     *
     * @throws Exception
     *
     * @return false|string
     */
    public function render(string $view, array $params = [], bool $isPartial = false)
    {
        $templateFile = $this->templatesPath.DIRECTORY_SEPARATOR.$view.'.php';

        if (!file_exists($templateFile)) {
            throw new Exception('No such template &laquo;'.$templateFile.'&raquo;! ');
        }

        ob_start();
        ob_implicit_flush(false);
        extract($params, EXTR_OVERWRITE);

        require $templateFile;

        $content = ob_get_clean();

        if ($isPartial) {
            return $content;
        }

        $templateItems['content'] = $content;

        ob_start();
        ob_implicit_flush(false);
        extract($templateItems, EXTR_OVERWRITE);

        $layoutFile = $this->layoutFile ?? 'main';
        $this->layoutFile = $this->layoutPath.DIRECTORY_SEPARATOR.$layoutFile.'.php';
        require $this->layoutFile;

        return ob_get_clean();
    }

    /**
     * @param string $layoutFile
     *
     * @return View
     */
    public function setLayout(string $layoutFile): self
    {
        $this->layoutFile = $layoutFile;

        return $this;
    }
}
