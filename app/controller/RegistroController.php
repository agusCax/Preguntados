<?php

namespace app\controller;

use Redirect;
use Request;

class RegistroController
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
        $data["error"] = Request::get('registro_error') !== null ? "Error al registrar su usuario y/o contraseña, intente nuevamente." : ""; 
        $this->render->renderiza("registro", $data);
    }

    public function registrar()
    {
        $datos = [
            'nombre_usuario' => Request::post('nombre_usuario'),
            'password' => Request::post('password'),
            'nombre_1' => Request::post('nombre_1'),
            'nombre_2' => Request::post('nombre_2') ?: null,
            'apellido_1' => Request::post('apellido_1'),
            'apellido_2' => Request::post('apellido_2') ?: null,
            'anio_nacimiento' => Request::post('anio_nacimiento'),
            'sexo' => Request::post('sexo'),
            'pais' => Request::post('pais'),
            'ciudad' => Request::post('ciudad'),
            // 'latitud' => Request::post('latitud'),
            // 'longitud' => Request::post('longitud'),
            'email' => Request::post('email'),
            'foto_perfil' => Request::post('foto_perfil') ?: null
        ];

        $registroExitoso = $this->model->registrar($datos);

        if ($registroExitoso !== 1) {
            Redirect::to(BASE_URL . '/registro/show?registro_error=1');
        }
        
        Redirect::toIndex();
    }
}