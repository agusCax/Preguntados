<?php

namespace app\controller;

use app\enum\Rol;
use Redirect;
use CpChartLib;

class AdministradorController
{
    private $model;
    private $render;

    public function __construct($model, $render)
    {
        $this->model = $model;
        $this->render = $render;
    }

    // Muestra la pantalla del panel
    public function show()
    {
        if (($_SESSION['rol'] ?? '') !== Rol::Administrador->name) {
            header('Location: ' . BASE_URL);
            exit();
        }

        $this->render->renderiza('administrador/inicio');
    }

    // Devuelve todos los datos del panel en JSON (la vista los pide con fetch)
    public function datos()
    {
        if (($_SESSION['rol'] ?? '') !== Rol::Administrador->name) {
            http_response_code(403);
            exit();
        }

        $desde = $this->calcularDesde($_GET['periodo'] ?? 'mes');

        header('Content-Type: application/json');
        echo json_encode([
            'totales' => [
                'jugadores'         => $this->model->contarJugadores(),
                'partidas'          => $this->model->contarPartidas($desde),
                'preguntas_activas' => $this->model->contarPreguntasActivas(),
                'preguntas_creadas' => $this->model->contarPreguntasCreadas($desde),
                'usuarios_nuevos'   => $this->model->contarUsuariosNuevos($desde),
            ],
            'pais'     => $this->model->usuariosPorPais($desde),
            'sexo'     => $this->model->usuariosPorSexo($desde),
            'edad'     => $this->model->usuariosPorGrupoEdad($desde),
            'aciertos' => $this->model->porcentajeAciertosPorUsuario($desde),
        ]);
        exit();
    }

    // Devuelve el gráfico de usuarios por país como imagen PNG (dibujada con CpChart)
    public function graficoPais()
    {
        if (($_SESSION['rol'] ?? '') !== Rol::Administrador->name) {
            http_response_code(403);
            exit();
        }

        $desde = $this->calcularDesde($_GET['periodo'] ?? 'mes');
        $filas = $this->model->usuariosPorPais($desde);

        CpChartLib::graficoBarras(
            'Usuarios por país',
            array_column($filas, 'etiqueta'),
            array_map('intval', array_column($filas, 'valor'))
        );
        exit();
    }

    // Convierte el período elegido en una fecha. Solo acepta valores de la lista
    private function calcularDesde($periodo)
    {
        $intervalo = match ($periodo) {
            'dia'    => '-1 day',
            'semana' => '-7 days',
            'anio'   => '-1 year',
            default  => '-1 month',
        };

        return date('Y-m-d H:i:s', strtotime($intervalo));
    }

    // Pantalla con la lista de usuarios y su rol
    public function usuarios()
    {
        if (($_SESSION['rol'] ?? '') !== Rol::Administrador->name) {
            header('Location: ' . BASE_URL);
            exit();
        }

        $usuarios = $this->model->obtenerUsuarios();

        // Se arma cada fila con las opciones de rol, marcando la que tiene hoy
        foreach ($usuarios as &$usuario) {
            $usuario['roles'] = [];
            foreach (Rol::cases() as $rol) {
                $usuario['roles'][] = [
                    'valor'        => $rol->value,
                    'nombre'       => $rol->name,
                    'seleccionado' => $rol->value == $usuario['rol_id'],
                ];
            }
            $usuario['es_usuario_actual'] = $usuario['id'] == $_SESSION['usuario_id'];
        }

        $this->render->renderiza('administrador/usuarios', [
            'usuarios' => $usuarios,
            'mensaje'  => $_GET['mensaje'] ?? '',
        ]);
    }

    // Recibe el formulario y guarda el nuevo rol
    public function cambiarRol()
    {
        if (($_SESSION['rol'] ?? '') !== Rol::Administrador->name) {
            http_response_code(403);
            exit();
        }

        $idUsuario = (int) ($_POST['usuario_id'] ?? 0);
        $rolId     = (int) ($_POST['rol_id'] ?? 0);

        // Solo se aceptan roles que existan en el enum
        if (Rol::tryFrom($rolId) === null) {
            Redirect::to(BASE_URL . '/administrador/usuarios?mensaje=Rol no válido');
        }

        // El administrador no puede cambiarse el rol a sí mismo (se quedaría sin acceso)
        if ($idUsuario === (int) $_SESSION['usuario_id']) {
            Redirect::to(BASE_URL . '/administrador/usuarios?mensaje=No podés cambiar tu propio rol');
        }

        $this->model->cambiarRol($idUsuario, $rolId);
        Redirect::to(BASE_URL . '/administrador/usuarios?mensaje=Rol actualizado');
    }
}