<?php

namespace app\model;

class RankingModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    // Todos los jugadores con la suma de los puntos de sus partidas terminadas (de mayor a menor), tambien metodo reutilizado (podriamos unificar los metodos posteriormente)
    public function getRanking()
    {
        $sql = "SELECT u.id, u.nombre_usuario,
                       COUNT(p.id) AS cantidad_partidas,
                       COALESCE(SUM(p.puntaje), 0) AS puntaje_total
                FROM usuarios u
                LEFT JOIN partidas p ON p.usuario_id = u.id AND p.estado = 'terminada'
                WHERE u.estado = 1
                GROUP BY u.id, u.nombre_usuario
                ORDER BY puntaje_total DESC, u.nombre_usuario ASC";

        return $this->database->queryAll($sql);
    }

    // Todas las partidas de un jugador, de la más nueva a la más vieja, metodo casi reutilizado de lo que veniamos trabajando
    public function getPartidasDeUsuario($usuarioId)
    {
        $sql = "SELECT id, puntaje, estado, fecha_inicio
                FROM partidas
                WHERE usuario_id = :usuario_id
                ORDER BY fecha_inicio DESC, id DESC";

        return $this->database->queryAll($sql, [':usuario_id' => $usuarioId]);
    }
}
