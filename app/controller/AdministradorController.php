<?php

namespace app\controller;

use app\enum\Rol;

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
}