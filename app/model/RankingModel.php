<?php

namespace app\model;

class RankingModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getRanking()
    {
        $sql = "SELECT u.id,
                   u.nombre_usuario,
                   COUNT(p.id) AS cantidad_partidas,
                   COALESCE(MAX(p.puntaje), 0) AS puntaje_maximo
            FROM usuarios u
            LEFT JOIN partidas p
                   ON p.usuario_id = u.id
                  AND p.estado = 'terminada'
            WHERE u.estado = 1
            GROUP BY u.id, u.nombre_usuario
            ORDER BY puntaje_maximo DESC, u.id ASC";

        return $this->database->queryAll($sql);
    }

    // Modifique el orden, ahora muestra las partidas del usuario por mayor puntaje, y sino por id de partida
    public function getPartidasDeUsuario($usuarioId)
    {
        $sql = "SELECT id, puntaje, estado, fecha_inicio
                FROM partidas
                WHERE usuario_id = :usuario_id
                ORDER BY puntaje DESC, id ASC";

        return $this->database->queryAll($sql, [':usuario_id' => $usuarioId]);
    }
}
