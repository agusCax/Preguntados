<?php

namespace app\model;

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
        $sql = "SELECT id, nombre_usuario, estado FROM usuarios WHERE nombre_usuario = '$usuario' AND password = '$passwordHash'";

        $userData = $this->database->queryOne($sql);

        if($userData['estado'] === 0) {
            return 'inactivo';
        }
        return $userData;
    }
}