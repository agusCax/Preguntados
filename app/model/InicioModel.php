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
            "SELECT u.nombre_usuario, MAX(p.puntaje) AS puntaje_total
        FROM usuarios u
        INNER JOIN partidas p ON p.usuario_id = u.id AND p.estado = 'terminada'
        GROUP BY u.id, u.nombre_usuario
        HAVING puntaje_total > 0
        ORDER BY puntaje_total DESC, u.id ASC
        LIMIT 3";

        return $this->database->queryAll($sql);
    }
}