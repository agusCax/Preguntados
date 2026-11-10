<?php

namespace app\model;

use app\Enum\Rol;

class LoginModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function autenticar($usuario, $password)
    {
        $passwordHash = md5($password);

        // A propósito sin prepared statements: lo arreglamos en la clase de refactor
        $sql = "SELECT id, nombre_usuario, estado, rol_id FROM usuarios WHERE nombre_usuario = '$usuario' AND password = '$passwordHash'";

        $userData = $this->database->queryOne($sql);

        if((int)$userData['estado'] === 0) {
            return 'inactivo';
        }

        $rol = $this->obtenerRol((int)$userData['rol_id']);

        if($rol === null){
            return 'no autorizado';
        }

        $userData['rol'] = $rol;

        return $userData;
    }

    public function obtenerRol($rolId)
    {
        return match ($rolId) {
            Rol::Administrador->value => Rol::Administrador->name,
            Rol::Editor->value => Rol::Editor->name,
            Rol::Jugador->value => Rol::Jugador->name,
            default => null
        };
    }
}