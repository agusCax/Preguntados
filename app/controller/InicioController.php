<?php

namespace app\controller;

use app\enum\Rol;
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
        $usuario = $_SESSION['usuario'] ?? null;
        $rol = $_SESSION['rol'] ?? null;

        if(! isset($usuario)){
            $this->render->renderiza("bienvenida", [], false);
        }

        switch ($rol) {
            case Rol::Jugador->name:
                $data['nombre_usuario'] = $usuario;
    
                $top = $this->model->getTopUsuarios();
                $data['hayPodio'] = !empty($top);
                $data['podio'] = $this->armarPodio($top);
                $this->render->renderiza("inicio", $data);
                break;
    
            case Rol::Editor->name:
                header('Location: ' . BASE_URL . '/editor/show');
                break;
    
            case Rol::Administrador->name:
                header('Location: ' . BASE_URL . '/administrador/show');
                break;
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