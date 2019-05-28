<?php

namespace Project;

use Exception;
use src\routers\WebRouter;

class View
{
    private $viewsPath;
    private $templatesPath;
    private $layoutFile;

    /**
     * View constructor.
     */
    public function __construct()
    {
        $this->viewsPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR .
            'views' . DIRECTORY_SEPARATOR;
        $this->layoutFile = $this->viewsPath . 'layouts' . DIRECTORY_SEPARATOR . 'main.php';
        $this->templatesPath = $this->viewsPath . WebRouter::getCurrentControllerName() . DIRECTORY_SEPARATOR;
    }

    /**
     * @param $view
     * @param array $params
     * @throws Exception
     */
    public function render($view, $params = [])
    {
        $templateFile = $this->templatesPath . $view . '.php';

        if (file_exists($templateFile)) {

            ob_start();
            ob_implicit_flush(false);
            extract($params, EXTR_OVERWRITE);

            require $templateFile;

            $templateItems['content'] = ob_get_clean();

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