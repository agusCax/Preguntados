<?php

use app\enum\Rol;

class AccessControl
{
    private $render;
    private const MAPA = [
        //publico indica que pueden acceder todos, con o sin sesion
        //no_logueado unicamente ellos pueden acceder al metodo de login y registro
        //logueado quiere decir que unicamente se puede acceder con un usuario logueado
        //despues esta el rol especifico que puede acceder
//        controlador => ['todo' (todo del controlador) / metodo (metodo en espicifico)  => 'quien puede acceder']

        'inicio' => ['todo' => 'publico'],
        'login' => ['metodo' => ['show' => 'no_logueado', 'login' => 'no_logueado', 'logout' => 'logueado']],
        'registro' => ['metodo' => ['show' => 'no_logueado', 'registrar' => 'no_logueado', 'validarcuenta' => 'publico']],
        'perfil' => ['todo' => 'logueado'],
        'ranking' => ['todo' => 'logueado'],
        'pregunta' => ['todo' => ['Jugador'], 'rutas_json' => ['reportar']],
        'editor' => ['todo' => ['Editor']],
        'administrador' => ['todo' => ['Administrador']],
        'reporte' => ['todo' => ['Administrador']],
    ];

    public function __construct($render)
    {
        $this->render = $render;
    }

    public function verificar($controlador, $metodo)
    {
        $controladorMinuscula = strtolower($controlador);
        $metodoMinuscula = strtolower($metodo);

        $rol= $this->obtenerRolActual();

        $regla = $this->buscarRegla($controladorMinuscula, $metodoMinuscula);

        $esJson = $this->esRutaJson($controladorMinuscula,$metodoMinuscula);

        $decision = $this->resolverAcceso($regla, $rol);

        match ($decision) {
            'inicio'  => $this->redirigirAlInicio(),
            'login'   => $this->redirigirAlLogin($esJson),
            'denegar' => $this->denegar($esJson),
            default   => null
        };
    }

    private function obtenerRolActual()
    {
        if (!isset($_SESSION['usuario_id'])) {
            return null;
        }

        $rolDeSesion = $_SESSION['rol'] ?? null;

        foreach (Rol::cases() as $rol) {
            if ($rol->name === $rolDeSesion) {
                return $rol->name;
            }
        }
        return null;
    }

    private function buscarRegla($controlador, $metodo)
    {
        $reglas = self::MAPA[$controlador] ?? null;

        if ($reglas === null) {
            return null;
        }

        if (isset($reglas['metodo'][$metodo])) {
            return $reglas['metodo'][$metodo];
        }

        return $reglas['todo'] ?? null;
    }

    private function esRutaJson($controlador, $metodo)
    {
        $rutasJson = self::MAPA[$controlador]['rutas_json'] ?? [];
        return in_array($metodo, $rutasJson, true);
    }

    private function resolverAcceso($regla, $rol)
    {
        return match (true) {
            $regla === null          => 'inicio',
            $regla === 'publico'     => 'permitir',
            $regla === 'no_logueado'   => $rol === null ? 'permitir' : 'inicio',
            $rol === null            => 'login',
            $regla === 'logueado'    => 'permitir',
            is_array($regla) && in_array($rol, $regla, true) => 'permitir',
            default                  => 'denegar',
        };
    }

    private function redirigirAlLogin($esJson)
    {
        if ($esJson) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['exito' => false, 'mensaje' => 'Tu sesión expiró. Iniciá sesión nuevamente.']);
            exit();
        }

        Redirect::to(BASE_URL . '/login/show');
        exit();
    }

    private function redirigirAlInicio()
    {
        Redirect::to(BASE_URL . '/inicio/show');
        exit();
    }

    private function denegar($esJson)
    {
        http_response_code(403);

        if ($esJson) {
            header('Content-Type: application/json');
            echo json_encode(['exito' => false, 'mensaje' => 'No tenés permisos para realizar esta acción.']);
            exit();
        }

        $this->render->renderiza('accesoDenegado', [], false);
        exit();
    }
}