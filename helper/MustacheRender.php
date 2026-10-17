<?php

require_once __DIR__ . "/../vendor/mustache/Mustache/Autoloader.php";
Mustache_Autoloader::register(__DIR__ . "/../vendor/mustache");

class MustacheRender
{
    private $mustache;
    private $viewPath;

    public function __construct()
    {
        $this->viewPath = dirname(__DIR__) . "/app/view";
        $this->mustache = new Mustache_Engine([
            "loader" => new Mustache_Loader_FilesystemLoader($this->viewPath, ["extension" => ".mustache"])
        ]);
    }

    // $conLayout = false: renderiza solo la vista (para páginas completas con su propio <head>)
    public function renderiza($view, $data = [], $conLayout = true)
    {
        $data['base_url'] = BASE_URL;

        if ($conLayout) {
            require_once($this->viewPath . "/header.php");
        }

        echo $this->mustache->render($view . "View", $data);

        if ($conLayout) {
            require_once($this->viewPath . "/footer.php");
        }
    }
}
