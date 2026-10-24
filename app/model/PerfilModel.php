<?php

namespace app\model;

class PerfilModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerDatosUsuario($userId) {
        return [
            'nombre' => 'Emanuel Ruiz',
            'usuario' => 'maanuu.rd',
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