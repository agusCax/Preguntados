<?php

namespace app\model;

use app\enum\EstadoPregunta;
use app\enum\EstadoReporte;

class EditorModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    //SUGERENCIAS + REPORTES
    public function contarPendientes($estado)
    {
        $resultado = $this->database->queryOne(
            "SELECT COUNT(*) AS total_pendientes
             FROM preguntas p
             WHERE p.estado = :estado",
            [':estado' => $estado]
        );

        return (int) $resultado['total_pendientes'];
    }

    public function listar($estadoPregunta)
    {
        $listadoIdPreguntas = $this->database->queryAll(
            "SELECT id
            FROM preguntas
            WHERE estado = :estado",
            [':estado' => $estadoPregunta]
        );

        $listado = [];

        foreach ($listadoIdPreguntas as $fila) {
            $idPregunta = $fila['id'];
            $preguntaConOpciones = $this->obtenerPreguntaConOpciones($idPregunta, $estadoPregunta);

            if ($estadoPregunta === EstadoPregunta::Reportada->value) {
                $preguntaConOpciones['reporte'] = $this->obtenerReportePendiente($idPregunta);
                $preguntaConOpciones['urlDetalle'] = BASE_URL . '/editor/detalleReporte?id=' . $idPregunta;
            } else {
                $preguntaConOpciones['urlDetalle'] = BASE_URL . '/editor/detalleSugerencia?id=' . $idPregunta;
            }

            $listado[] = $preguntaConOpciones;
        }

        return $listado;
    }

    //REPORTES
    public function obtenerReporte($idPregunta)
    {
        $preguntaConOpciones = $this->obtenerPreguntaConOpciones($idPregunta, EstadoPregunta::Reportada->value);
        $preguntaConOpciones['reporte'] = $this->obtenerReportePendiente($idPregunta);

        $preguntaConOpciones = $preguntaConOpciones !== null ? $preguntaConOpciones :  null;
        
        return $preguntaConOpciones;
    }

    public function obtenerResolucionReporte($idPregunta)
    {
        $preguntaReportada = $this->obtenerReporte($idPregunta);
        $preguntaReportada['categorias'] = 
            $this->database->queryAll(
                "SELECT id, nombre, icono
                 FROM categorias
                 ORDER BY id"
            );

        return $preguntaReportada;
    }

    public function rechazarReporte($idPregunta, $idReporte)
    {
        $preguntaReactivada = $this->actualizarEstadoPregunta($idPregunta, EstadoPregunta::Activa->value);
        $reporteRechazado = $this->actualizarEstadoReporte($idReporte, EstadoReporte::Rechazado->value, true);
        
        return $preguntaReactivada && $reporteRechazado;
    }

    public function inactivarPregunta($idPregunta, $idReporte){
        $preguntaInactivada = $this->actualizarEstadoPregunta($idPregunta, EstadoPregunta::Inactiva->value);
        $reporteAceptado = $this->actualizarEstadoReporte($idReporte, EstadoReporte::Aceptado->value, true);
        
        return $preguntaInactivada && $reporteAceptado;
    }

    public function modificarPregunta($idPregunta, $preguntaTexto, $categoriaId, $respuestas, $respuestaCorrectaId, $idReporte)
    {
        $preguntaModificada = $this->database->execute(
            "UPDATE preguntas p
            SET 
            p.texto = :texto, 
            p.categoria_id = :idCategoria
            WHERE p.id = :idPregunta",
            [ ':texto' => $preguntaTexto,
            ':idCategoria' => $categoriaId,
            ':idPregunta' => $idPregunta]
        );

        $respuestasModificadas = true;

        foreach($respuestas as $respuesta){
            $modificada = $this->database->execute(
                "UPDATE respuestas r
                SET 
                r.texto = :texto, 
                r.es_correcta = :esCorrecta
                WHERE r.id = :idRespuesta",
                [ ':texto' => $respuesta['texto'],
                ':esCorrecta' => $respuestaCorrectaId === $respuesta['id'] ? 1 : 0,
                ':idRespuesta' => $respuesta['id']]
            );

            if ($modificada === false) {
                $respuestasModificadas = false;
            }
        }

        $preguntaReactivada = $this->actualizarEstadoPregunta($idPregunta, EstadoPregunta::Activa->value);
        $reporteAceptado = $this->actualizarEstadoReporte($idReporte, EstadoReporte::Aceptado->value, true);
        
        return $preguntaModificada !== false
            && $respuestasModificadas 
            && $preguntaReactivada !== false
            && $reporteAceptado !== false;
    }

    //SUGERENCIAS
    public function obtenerSugerencia($idPregunta)
    {
        $preguntaConOpciones = $this->obtenerPreguntaConOpciones($idPregunta, EstadoPregunta::Pendiente->value);
        return $preguntaConOpciones;
    }

    public function procesarSugerencia($idPregunta, $accion)
    {
        $estado = $accion === 'aceptar' ? EstadoPregunta::Activa->value : EstadoPregunta::Inactiva->value;
        $procesada = $this->database->execute(
            "UPDATE preguntas p
            SET p.estado = :estado
            WHERE p.id = :idPregunta",
            [
                ':estado' => $estado,
                ':idPregunta' => $idPregunta
            ]
        );

        return $procesada !== false;
    }

    //HELPERS
    private function obtenerPreguntaConOpciones($idPregunta, $estadoPregunta)
    {
        $pregunta = $this->database->queryOne(
            "SELECT p.id, p.texto,
                    c.nombre AS categoria, c.color, c.icono, c.id AS categoria_id
            FROM preguntas p
            JOIN categorias c ON c.id = p.categoria_id
            WHERE p.id = :id
            AND p.estado = :estado",
            [
                ':id' => $idPregunta,
                ':estado' => $estadoPregunta
            ]
        );

        $opciones = $this->database->queryAll(
            "SELECT id, texto, es_correcta
            FROM respuestas
            WHERE pregunta_id = :id",
            [':id' => $idPregunta]
        );

        $preguntaConOpciones =
            $pregunta !== null && $opciones !== null ?
            ['pregunta' => $pregunta, 'opciones' => $opciones] :
            null;

        return $preguntaConOpciones;
    }

    private function obtenerReportePendiente($idPregunta)
    {
        return $this->database->queryOne(
            "SELECT r.id AS reporte_id,
                    r.usuario_id,
                    r.motivo,
                    r.descripcion,
                    r.estado,
                    r.fecha_creacion as fecha
            FROM reportes r
            WHERE r.pregunta_id = :id
            AND r.estado = 'pendiente'",
            [':id' => $idPregunta]
        );
    }

    private function actualizarEstadoReporte($idReporte, $nuevoEstadoReporte, $resuelto){
        $actualizado = $this->database->execute(
            "UPDATE reportes r
            SET r.estado = :estado,
            r.fecha_resolucion = :resuelto
            WHERE r.id = :idReporte",
            [':estado' => $nuevoEstadoReporte, 
            ':resuelto' => $resuelto ? date('Y-m-d H:i:s') : null,
            ':idReporte' => $idReporte]
        );

        return $actualizado !== false;
    }

    private function actualizarEstadoPregunta($idPregunta, $nuevoEstadoPregunta){
        $actualizado = $this->database->execute(
                "UPDATE preguntas p
                SET p.estado = :estado
                WHERE p.id = :idPregunta",
                [':estado' => $nuevoEstadoPregunta, 
                ':idPregunta' => $idPregunta]
        );

        return $actualizado !== false;
    }
}
