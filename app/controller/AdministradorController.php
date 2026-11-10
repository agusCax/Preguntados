<?php

namespace app\controller;

use app\Enum\Rol;

class AdministradorController
{
    private $model;
    private $render;

    public function __construct($model, $render)
    {
        $this->model = $model;
        $this->render = $render;
    }

    public function show()
    {
        if ($_SESSION['rol'] !== Rol::Administrador->name) {
            header('Location: ' . BASE_URL);
            exit();
        }

        $this->render->renderiza('administrador/inicio');
    }
}