<?php

namespace app\controller;

use Redirect;
use Request;

class LoginController
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
        $data["error"] = Request::get('login_error') !== null ? "Usuario o contraseña incorrectos." : "";

        $activacion = $_GET['activacion'] ?? null;
        $data['mensaje_exito'] = "";

        if ($activacion === 'revisa_tu_mail') {
            $data['mensaje_exito'] = "¡Registro exitoso! Te enviamos un correo para activar tu cuenta.";
        } elseif ($activacion === 'cuenta_activada') {
            $data['mensaje_exito'] = "¡Tu cuenta ha sido activada! Ya podés iniciar sesión.";
        } elseif ($activacion === 'cuenta_inactiva') {
            $data['error'] = "No se puede iniciar sesion sin activar primero tu cuenta.";
        }

        $this->render->renderiza("login", $data, false);
    }

    public function login()
    {
        $usuario = $this->model->autenticar(Request::post('usuario'), Request::post('password'));


        if ($usuario === null) {
            Redirect::to(BASE_URL . '/login/show?login_error=1');
        }

        if ($usuario === 'inactivo') {
            Redirect::to(BASE_URL . '/login/show?activacion=cuenta_inactiva');
        }

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario'] = $usuario['nombre_usuario'];
        Redirect::toIndex();
    }

    public function logout()
    {
        session_destroy();
        Redirect::toIndex();
    }
}