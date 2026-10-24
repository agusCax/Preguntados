<?php

namespace app\controller;

use Request;

class InicioController
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
        if(isset($_SESSION['usuario'])){
            $data['nombre_usuario'] = $_SESSION['usuario'];
            $data['usuarios'] = $this->model->getInicio();
            $this->render->renderiza("inicio", $data);
        }else{
            $this->render->renderiza("bienvenida", [], false);
        }
    }
}