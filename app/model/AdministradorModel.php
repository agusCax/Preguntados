<?php

namespace app\model;

class AdministradorModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    // ----- Números sueltos -----

    public function contarJugadores()
    {
        return (int) $this->database->queryOne(
            "SELECT COUNT(*) AS total FROM usuarios WHERE estado = 1")['total'];
    }

    public function contarPreguntasActivas()
    {
        return (int) $this->database->queryOne(
            "SELECT COUNT(*) AS total FROM preguntas WHERE estado = 'activa'")['total'];
    }

    public function contarPartidas($desde)
    {
        return (int) $this->database->queryOne(
            "SELECT COUNT(*) AS total FROM partidas WHERE fecha_inicio >= :desde",
            [':desde' => $desde])['total'];
    }

    public function contarPreguntasCreadas($desde)
    {
        return (int) $this->database->queryOne(
            "SELECT COUNT(*) AS total FROM preguntas WHERE fecha_creacion >= :desde",
            [':desde' => $desde])['total'];
    }

    public function contarUsuariosNuevos($desde)
    {
        return (int) $this->database->queryOne(
            "SELECT COUNT(*) AS total FROM usuarios WHERE fecha_registro >= :desde",
            [':desde' => $desde])['total'];
    }

    // ----- Reportes: cada fila tiene "etiqueta" y "valor" -----

    public function usuariosPorPais($desde)
    {
        return $this->database->queryAll(
            "SELECT pais AS etiqueta, COUNT(*) AS valor
             FROM usuarios
             WHERE estado = 1 AND fecha_registro >= :desde
             GROUP BY pais
             ORDER BY valor DESC",
            [':desde' => $desde]);
    }

    public function usuariosPorSexo($desde)
    {
        return $this->database->queryAll(
            "SELECT sexo AS etiqueta, COUNT(*) AS valor
             FROM usuarios
             WHERE estado = 1 AND fecha_registro >= :desde
             GROUP BY sexo
             ORDER BY valor DESC",
            [':desde' => $desde]);
    }

    public function usuariosPorGrupoEdad($desde)
    {
        return $this->database->queryAll(
            "SELECT CASE
                        WHEN (YEAR(CURDATE()) - anio_nacimiento) < 18 THEN 'Menores'
                        WHEN (YEAR(CURDATE()) - anio_nacimiento) >= 65 THEN 'Jubilados'
                        ELSE 'Medio'
                    END AS etiqueta,
                    COUNT(*) AS valor
             FROM usuarios
             WHERE estado = 1 AND fecha_registro >= :desde
             GROUP BY etiqueta",
            [':desde' => $desde]);
    }

    public function porcentajeAciertosPorUsuario($desde)
    {
        return $this->database->queryAll(
            "SELECT u.nombre_usuario AS etiqueta,
                    ROUND(100 * SUM(pp.fue_correcta) / COUNT(*), 1) AS valor
             FROM partida_preguntas pp
             JOIN partidas p ON p.id = pp.partida_id
             JOIN usuarios u ON u.id = p.usuario_id
             WHERE pp.respondida_en IS NOT NULL AND pp.respondida_en >= :desde
             GROUP BY u.id, u.nombre_usuario
             ORDER BY valor DESC
             LIMIT 10",
            [':desde' => $desde]);
    }
}