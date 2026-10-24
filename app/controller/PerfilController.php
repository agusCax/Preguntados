<?php

namespace app\controller;
use Redirect;

class PerfilController {
    private $renderer;
    private $perfilModel;

    public function __construct($renderer, $perfilModel) {
        $this->renderer = $renderer;
        $this->perfilModel = $perfilModel;
    }

    public function show() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario_id'] ?? null;
        $data = $this->perfilModel->obtenerDatosUsuario($userId);
        $this->renderer->renderiza("perfil", $data); // <--- Solo "perfil"
    }

    public function editar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario_id'] ?? null;
        $data = $this->perfilModel->obtenerDatosUsuario($userId);
        $this->renderer->renderiza("editarPerfil", $data); // <--- Solo "editarPerfil"
    }

    public function actualizar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario_id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'usuario' => $_POST['usuario'] ?? '',
                'bio' => $_POST['bio'] ?? '',
                'ubicacion' => $_POST['ubicacion'] ?? ''
            ];

            $this->perfilModel->actualizarPerfil($userId, $datos);
            Redirect::to(BASE_URL . '/perfil/show');
            exit();
        }
    }
}