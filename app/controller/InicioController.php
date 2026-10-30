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

            $top = $this->model->getTopUsuarios();
            $data['hayPodio'] = !empty($top);
            $data['podio'] = $this->armarPodio($top);
            $this->render->renderiza("inicio", $data);
        }else{
            $this->render->renderiza("bienvenida", [], false);
        }
    }

    private function armarPodio($top)
    {
        $podio = [];

        for ($i = 0; $i < 3; $i++) {
            if (isset($top[$i])) {
                $podio[] = [
                    'posicion' => $i + 1,
                    'hayJugador' => true,
                    'nombre' => $top[$i]['nombre_usuario'],
                    'puntaje' => $top[$i]['puntaje_total']
                ];
            } else {
                $podio[] = ['posicion' => $i + 1, 'hayJugador' => false];
            }
        }

        return $podio;
    }
}