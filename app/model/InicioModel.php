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
            $u['usuarios'] = $u['nombre_usuario'];
        }

        return $usuarios;

    }

    public function getTopUsuarios() {

        $sql =
            "SELECT nombre_usuario, SUM(p.puntaje) AS puntaje_total
            FROM usuarios 
            LEFT JOIN partidas p ON usuarios.id = p.usuario_id
            GROUP BY usuarios.nombre_usuario, usuarios.id 
            HAVING puntaje_total > 0
            ORDER BY puntaje_total DESC, usuarios.nombre_usuario ASC
            LIMIT 3";

        return $this->database->queryAll($sql);
    }
}