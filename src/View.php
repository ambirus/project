<?php

namespace Project;

use Exception;
use Project\routers\WebRouter;

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
    public function __construct()
    {
        $this->viewsPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'protected' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;
        $this->layoutFile = $this->viewsPath . 'layouts' . DIRECTORY_SEPARATOR . 'main.php';
        $this->templatesPath = $this->viewsPath . WebRouter::getCurrentControllerName() . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $view
     * @param array $params
     * @param bool $isPartial
     * @throws Exception
     */
    public function render(string $view, array $params = [], bool $isPartial = false)
    {
        $templateFile = $this->templatesPath . $view . '.php';

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
