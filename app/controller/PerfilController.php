<?php

namespace app\controller;
use Redirect;
use Request;

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
        $data['esPropietario']  = true;
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
            $_SESSION['usuario'] = $datos['usuario'];
            Redirect::to(BASE_URL . '/perfil/show');
            exit();
        }
    }

    //QR
    public function compartir() {
        $userId = $_SESSION['usuario_id'] ?? null;
        
        $urlQr = $this->perfilModel->obtenerRutaImagenQr($userId);
        $data = ['urlQr' => $urlQr];

        $this->renderer->renderiza("compartirPerfil", $data); 
    }

    public function showUsuario() {
        $usuarioLogueado = $_SESSION['usuario_id'] ?? null;

        if ($usuarioLogueado === null) {
            Redirect::to(BASE_URL . '/login/show');
            exit();
        }

        $usuarioAMostrar = Request::get('usuario_id');

        if($usuarioAMostrar === null){
            $data['error']  = 'El enlace del perfil no es válido.';
            $this->renderer->renderiza('perfil', $data);
            return;
        }
        
        $data = $this->perfilModel->obtenerDatosUsuario($usuarioAMostrar);

        if($data === null){
            $data['error']  = 'No se encontró el usuario buscado.';
            $this->renderer->renderiza('perfil', $data);
            return;
        }

        $data['esPropietario'] = (int) $usuarioLogueado === (int) $usuarioAMostrar ? true : false;

        $this->renderer->renderiza("perfil", $data); 
    }

    public function subirFoto() {
        $userId = $_SESSION['usuario_id'] ?? null;
        $foto = $_FILES['foto'] ?? null;

        // Sin sesión, sin archivo o con error de subida: volver al perfil
        if ($userId === null || $foto === null || $foto['error'] !== UPLOAD_ERR_OK) {
            Redirect::to(BASE_URL . '/perfil/show');
        }

        // Solo imágenes, de hasta 2 MB
        $extension = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $permitidas) || $foto['size'] > 2 * 1024 * 1024) {
            Redirect::to(BASE_URL . '/perfil/show');
        }

        // Crear la carpeta si no existe
        $carpeta = dirname(__DIR__, 2) . '/public/uploads/';
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        // Nombre propio (no el original) con la hora, para evitar el caché del navegador
        $nombreArchivo = 'perfil_' . $userId . '_' . time() . '.' . $extension;
        move_uploaded_file($foto['tmp_name'], $carpeta . $nombreArchivo);

        $this->perfilModel->guardarFotoPerfil($userId, 'public/uploads/' . $nombreArchivo);
        Redirect::to(BASE_URL . '/perfil/show');
    }
}