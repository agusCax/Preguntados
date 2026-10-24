<?php

namespace app\model;

class PerfilModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerDatosUsuario($userId) {

        $usuario = $this->db->queryOne(
            "SELECT nombre_usuario FROM usuarios WHERE id = :id",
            [':id' => $userId]
        );

        $nombre = 'Emanuel Ruiz';
        $nombreUsuario = $usuario['nombre_usuario'] ?? '';

        return [
            'nombre' => $nombre,
            'usuario' => $nombreUsuario,
            // Letra del avatar: primera letra del nombre (o del usuario si no hay nombre)
            'inicial' => mb_strtoupper(mb_substr($nombre !== '' ? $nombre : $nombreUsuario, 0, 1)),
            'email' => 'maanuu.rd@gmail.com',
            'ubicacion' => 'Argentina',
            'bio' => '¡Apasionado por las trivia y los desafíos!',
            'nivel' => 17,
            'preguntasCorrectas' => 174,
            'seguidores' => 1,
            'seguidos' => 1,
            'especialidad' => 'DEPORTES',
            'especialidadAciertos' => 97,
            'desafiante' => 'CIENCIA',
            'desafianteAciertos' => 65,
            'partidasGanadas' => 3,
            'partidasPerdidas' => 9,
            'partidasRendidas' => 0,
            'logrosObtenidos' => 0,
            'totalLogros' => 47
        ];
    }

    public function actualizarPerfil($userId, $datos) {
        // Aquí agregarías la lógica de actualización en tu base de datos
        return true;
    }
}