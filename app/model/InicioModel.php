<?php

namespace app\model;
class InicioModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getInicio()
    {
        $sql_usuarios = "SELECT usuarios.* FROM usuarios";

        $usuarios = $this->database->queryAll($sql_usuarios);

        foreach ($usuarios as &$u) {
            $u['usuarios'] = $u['usuario'];
        }

        return $usuarios;

    }
}