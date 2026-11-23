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

    //BANCO DE PREGUNTAS - TOTAL
    public function listarBanco()
    {
        $resultado = $this->database->queryAll("SELECT * FROM preguntas p");

        $listado = [];

        foreach ($resultado as $fila) {
            $idPregunta = $fila['id'];
            $estadoPregunta = $fila['estado'];
            $preguntaConOpciones = $this->obtenerPreguntaConOpciones($idPregunta, $estadoPregunta);

            $preguntaConOpciones['urlDetalle'] = BASE_URL . '/editor/detallePregunta?id=' . $idPregunta;
            
            $estado = $preguntaConOpciones['pregunta']['estado'];
            $estadoFormateado = mb_strtoupper(mb_substr($estado, 0, 1)) . mb_strtolower(mb_substr($estado, 1));
            $preguntaConOpciones['pregunta']['estado'] = $estadoFormateado;

            $listado[] = $preguntaConOpciones;
        }

        return $listado;
    }

    public function obtenerPreguntaConOpciones($idPregunta, $estadoPregunta = null)
    {
        $sql = "SELECT p.id, p.texto, p.estado,
            c.nombre AS categoria, c.color, c.icono, c.id AS categoria_id
            FROM preguntas p
            JOIN categorias c ON c.id = p.categoria_id
            WHERE p.id = :id";

        $parametros = [':id' => $idPregunta];

        if ($estadoPregunta !== null) {
            $sql .= " AND p.estado = :estado";
            $parametros[':estado'] = $estadoPregunta;
        }

        $pregunta = $this->database->queryOne($sql, $parametros);

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

    public function activarPregunta($idPregunta)
    {    
        return $this->actualizarEstadoPregunta($idPregunta, EstadoPregunta::Activa->value, EstadoPregunta::Inactiva->value);
    }

    public function inactivarPregunta($idPregunta)
    {
        return $this->actualizarEstadoPregunta($idPregunta, EstadoPregunta::Inactiva->value, EstadoPregunta::Activa->value);
    }

    public function guardarNuevaPregunta($preguntaTexto, $categoriaId, $respuestas, $respuestaCorrecta)
    {
        $preguntaGuardada = $this->database->execute( 
            "INSERT INTO preguntas 
            (categoria_id, texto, estado) 
            VALUES (:categoria_id, :texto, :estado)", 
            [':categoria_id' => (int) $categoriaId,
            ':texto' => $preguntaTexto,
            ':estado' => EstadoPregunta::Activa->value]
        );

        if ($preguntaGuardada === false) {
            return false;
        }

        $idPregunta = (int)$this->database->lastInsertId();

        $respuestasGuardadas = true; 

        foreach ($respuestas as $indice => $respuesta) {
            $respuestaGuardada = $this->database->execute(
                "INSERT INTO 
                respuestas 
                (pregunta_id, texto, es_correcta) 
                VALUES (:pregunta_id, :texto, :es_correcta)",
                [':pregunta_id' => $idPregunta,
                ':texto' => $respuesta['texto'],
                ':es_correcta' => (string) $indice === (string) $respuestaCorrecta ? 1 : 0]
            );

            if ($respuestaGuardada === false) {
                $respuestasGuardadas = false;
            }
        }

        return $preguntaGuardada !== false && $respuestasGuardadas;
    }

    //SUGERENCIAS + REPORTES
    public function contarPreguntasPorEstado($estado)
    {
        $resultado = $this->database->queryOne(
            "SELECT COUNT(*) AS total
             FROM preguntas p
             WHERE p.estado = :estado",
            [':estado' => $estado]
        );

        return (int) $resultado['total'];
    }

    public function listarPreguntasPorEstado($estadoPregunta)
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
    
        if ($preguntaConOpciones === null) {
            return null;
        }
    
        $preguntaConOpciones['reporte'] = $this->obtenerReportePendiente($idPregunta);
    
        return $preguntaConOpciones;
    }

    public function obtenerResolucionReporte($idPregunta)
    {
        $preguntaReportada = $this->obtenerReporte($idPregunta);

        if($preguntaReportada === null){
            return null;
        }

        $preguntaReportada['categorias'] = $this->listarCategorias();

        return $preguntaReportada;
    }

    public function rechazarReporte($idPregunta, $idReporte)
    {
        $preguntaReactivada = $this->actualizarEstadoPregunta($idPregunta, EstadoPregunta::Activa->value);
        $reporteRechazado = $this->actualizarEstadoReporte($idReporte, EstadoReporte::Rechazado->value, true);

        return $preguntaReactivada && $reporteRechazado;
    }

    public function inactivarPreguntaReportada($idPregunta, $idReporte)
    {
        $preguntaInactivada = $this->actualizarEstadoPregunta($idPregunta, EstadoPregunta::Inactiva->value);
        $reporteAceptado = $this->actualizarEstadoReporte($idReporte, EstadoReporte::Aceptado->value, true);

        return $preguntaInactivada && $reporteAceptado;
    }

    public function modificarPreguntaReportada($idPregunta, $preguntaTexto, $categoriaId, $respuestas, $respuestaCorrecta, $idReporte)
    {
        $contenidoModificado = $this->modificarContenidoPregunta($idPregunta, $preguntaTexto, $categoriaId, $respuestas, $respuestaCorrecta);
        
        if (!$contenidoModificado) {
            return false;
        }

        $preguntaReactivada = $this->actualizarEstadoPregunta($idPregunta, EstadoPregunta::Activa->value);
        $reporteAceptado = $this->actualizarEstadoReporte($idReporte, EstadoReporte::Aceptado->value, true);

        return $preguntaReactivada && $reporteAceptado;
    }

    //BANCO DE PREGUNTAS - EDICION (no cambia el estado de la pregunta)
    public function modificarPregunta($idPregunta, $preguntaTexto, $categoriaId, $respuestas, $respuestaCorrecta)
    {
        return $this->modificarContenidoPregunta($idPregunta, $preguntaTexto, $categoriaId, $respuestas, $respuestaCorrecta);
    }

    //SUGERENCIAS
    public function obtenerSugerencia($idPregunta)
    {
        return $this->obtenerPreguntaConOpciones($idPregunta, EstadoPregunta::Pendiente->value);
    }

    public function procesarSugerencia($idPregunta, $accion)
    {
        $estado = $accion === 'aceptar' ? EstadoPregunta::Activa->value : EstadoPregunta::Inactiva->value;
        $procesada = $this->actualizarEstadoPregunta($idPregunta, $estado);

        return $procesada;
    }

    //HELPERS
    private function modificarContenidoPregunta($idPregunta, $preguntaTexto, $categoriaId, $respuestas, $respuestaCorrecta)
    {
        $preguntaModificada = $this->database->execute(
            "UPDATE preguntas p
            SET
            p.texto = :texto,
            p.categoria_id = :idCategoria
            WHERE p.id = :idPregunta",
            [
                ':texto' => $preguntaTexto,
                ':idCategoria' => $categoriaId,
                ':idPregunta' => $idPregunta
            ]
        );

        if ($preguntaModificada === false) {
            return false;
        }

        $respuestasModificadas = true;

        foreach ($respuestas as $indice => $respuesta) {
            $modificada = $this->database->execute(
                "UPDATE respuestas r
                SET
                r.texto = :texto,
                r.es_correcta = :esCorrecta
                WHERE r.id = :idRespuesta
                AND r.pregunta_id = :idPregunta",
                [
                    ':texto' => $respuesta['texto'],
                    ':esCorrecta' => (string) $indice === (string) $respuestaCorrecta ? 1 : 0,
                    ':idRespuesta' => $respuesta['id'], 
                    ':idPregunta' => $idPregunta
                ]
            );

            if ($modificada === false) {
                $respuestasModificadas = false;
            }
        }

        return $preguntaModificada !== false && $respuestasModificadas;
    }

    private function actualizarEstadoPregunta($idPregunta, $nuevoEstadoPregunta, $estadoAVerificar = null)
    {
        $sql = "UPDATE preguntas p
                SET p.estado = :estado
                WHERE p.id = :idPregunta";

        $parametros = [
            ':estado' => $nuevoEstadoPregunta,
            ':idPregunta' => $idPregunta
        ];

        if ($estadoAVerificar !== null) {
            $sql .= " AND p.estado = :estadoActual";
            $parametros[':estadoActual'] = $estadoAVerificar;
        }

        $actualizado = $this->database->execute($sql, $parametros);

        if ($actualizado === false) {
            return false;
        }

        // Si se exigió un estado previo, tiene que haberse modificado alguna fila
        return $estadoAVerificar === null || $actualizado > 0;
    }

    public function listarCategorias()
    {
        return $this->database->queryAll(
            "SELECT id, nombre, icono
             FROM categorias
             ORDER BY id"
        );
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

    private function actualizarEstadoReporte($idReporte, $nuevoEstadoReporte, $resuelto)
    {
        $actualizado = $this->database->execute(
            "UPDATE reportes r
            SET r.estado = :estado,
            r.fecha_resolucion = :resuelto
            WHERE r.id = :idReporte",
            [
                ':estado' => $nuevoEstadoReporte,
                ':resuelto' => $resuelto ? date('Y-m-d H:i:s') : null,
                ':idReporte' => $idReporte
            ]
        );

        return $actualizado !== false;
    }
}
