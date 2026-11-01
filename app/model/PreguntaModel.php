<?php

namespace app\model;


class PreguntaModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }


    public function crearPartida($idUsuario)
    {
        $this->database->execute(
            "INSERT INTO partidas (usuario_id) VALUES (:usuario_id)",
            [':usuario_id' => $idUsuario]
        );

        return (int)$this->database->lastInsertId();
    }

    public function terminarPartida($idPartida, $puntaje)
    {
        $this->database->execute(
            "UPDATE partidas
            SET estado = 'terminada', puntaje = :puntaje, fecha_fin = NOW()
            WHERE id = :id",
            [':puntaje' => $puntaje, ':id' => $idPartida]
        );
    }

    // Si ya hay una pregunta entregada y sin responder en la partida (ej: F5), la devuelve.
    // Si no, sortea una nueva que no se haya usado en la partida y la registra.
    public function obtenerPreguntaParaPartida($idPartida)
    {
        $pendiente = $this->database->queryOne(
            "SELECT pregunta_id FROM partida_preguntas
            WHERE partida_id = :partida_id AND respondida_en IS NULL
            LIMIT 1",
            [':partida_id' => $idPartida]
        );

        if ($pendiente) {
            $idPregunta = $pendiente['pregunta_id'];
        } else {
            $idUsuario = $this->obtenerIdUsuarioDePartida($idPartida);

            // 1) Preguntas que el usuario nunca vio, en ninguna partida
            $candidatas = $this->database->queryAll(
                "SELECT p.id
                FROM preguntas p
                WHERE p.estado = 'activa'
                AND p.id NOT IN (
                    SELECT pp.pregunta_id
                    FROM partida_preguntas pp
                    JOIN partidas pa ON pa.id = pp.partida_id
                    WHERE pa.usuario_id = :usuario_id
                )
                ORDER BY RAND()",
                [':usuario_id' => $idUsuario]
            );

            // 2) Si ya las vio todas, se permiten repetidas (menos las de esta partida)
            if (!$candidatas) {
                $candidatas = $this->database->queryAll(
                    "SELECT p.id
                    FROM preguntas p
                    WHERE p.estado = 'activa'
                    AND p.id NOT IN (
                        SELECT pregunta_id FROM partida_preguntas WHERE partida_id = :partida_id
                    )
                    ORDER BY RAND()",
                    [':partida_id' => $idPartida]
                );}

            if (!$candidatas) {
                return null; // no quedan preguntas
            }

            $idPregunta = $this->elegirPreguntaSegunNivel($candidatas, $idUsuario);

            $this->database->execute(
                "INSERT INTO partida_preguntas (partida_id, pregunta_id) VALUES (:partida_id, :pregunta_id)",
                [':partida_id' => $idPartida, ':pregunta_id' => $idPregunta]
            );
        }

        $pregunta = $this->database->queryOne(
            "SELECT p.id, p.texto, c.nombre AS categoria, c.color, c.icono
            FROM preguntas p
            JOIN categorias c ON c.id = p.categoria_id
            WHERE p.id = :id",
            [':id' => $idPregunta]
        );

        $pregunta['opciones'] = $this->database->queryAll(
            "SELECT id, texto FROM respuestas WHERE pregunta_id = :id ORDER BY RAND()",
            [':id' => $idPregunta]
        );

        return $pregunta;
    }

    // Devuelve true/false si la respuesta es correcta. Guarda el resultado en partida_preguntas.
    public function registrarRespuesta($idPartida, $idPregunta, $idRespuesta)
    {
        $opcion = $this->database->queryOne(
            "SELECT es_correcta FROM respuestas WHERE id = :id AND pregunta_id = :pregunta_id",
            [':id' => $idRespuesta, ':pregunta_id' => $idPregunta]
        );

        $esCorrecta = $opcion ? (bool)$opcion['es_correcta'] : false;

        $this->database->execute(
            "UPDATE partida_preguntas
            SET respuesta_id = :respuesta_id, fue_correcta = :correcta, respondida_en = NOW()
            WHERE partida_id = :partida_id AND pregunta_id = :pregunta_id",
            [
                ':respuesta_id' => $opcion ? $idRespuesta : null,
                ':correcta'     => $esCorrecta ? 1 : 0,
                ':partida_id'   => $idPartida,
                ':pregunta_id'  => $idPregunta,
            ]
        );

        return $esCorrecta;
    }

    // Tiempo agotado: queda respondida sin respuesta y como incorrecta.
    public function registrarTiempoAgotado($idPartida, $idPregunta)
    {
        $this->database->execute(
            "UPDATE partida_preguntas
            SET fue_correcta = 0, respondida_en = NOW()
            WHERE partida_id = :partida_id AND pregunta_id = :pregunta_id",
            [':partida_id' => $idPartida, ':pregunta_id' => $idPregunta]
        );
    }

    public function obtenerPreguntasRespondidas($idPartida)
    {
        return $this->database->queryAll(
            "SELECT pregunta_id, respuesta_id, fue_correcta, respondida_en
            FROM partida_preguntas
            WHERE partida_id = :partida_id AND respondida_en IS NOT NULL",
            [':partida_id' => $idPartida]
        );
    }


    // % de veces que se respondió bien la pregunta (50 si es nueva y nadie la respondió)
    public function obtenerPorcentajePreguntaCorrecta($idPregunta)
    {
        $sql = $this->database->queryOne(
            "SELECT COUNT(*) AS total_intentos, SUM(pp.fue_correcta) AS total_correctas
            FROM partida_preguntas pp
            WHERE pregunta_id = :pregunta_id AND respondida_en IS NOT NULL",
            [':pregunta_id' => $idPregunta]
        );

        $totalIntentos = (int)$sql['total_intentos'];
        $totalCorrectas = (int)$sql['total_correctas'];

        if ($totalIntentos === 0) {
            return 50.00; // si la pregunta sorteada tiene 0 intentes, osea, es nueva, queda la DIFULTAD REGULAR, POR AHORA
        }

        return round(($totalCorrectas / $totalIntentos) * 100, 2);
    }

    //SEGUIR HACIENDO ESTA FUNCION
    // agus dijo que se puede hacer totalpartidasganadas(=10 puntos/totalpartidas)

    // Clasifica la pregunta: Facil (>= 70%), Dificil (<= 30%) o Regular
    public function obtenerDificultadPregunta($idPregunta)
    {
        $porcentaje = $this->obtenerPorcentajePreguntaCorrecta($idPregunta);

        return match (true) {
            $porcentaje >= 70.00 => 'Facil',
            $porcentaje <= 30.00 => 'Dificil',
            default              => 'Regular',
        };
    }


    private function obtenerIdUsuarioDePartida($idPartida)
    {
        $fila = $this->database->queryOne(
            "SELECT usuario_id FROM partidas WHERE id = :id",
            [':id' => $idPartida]
        );

        return (int)$fila['usuario_id'];
    }

    // % de aciertos del jugador en todo su historial (50 si es nuevo)
    public function obtenerNivelDelJugador($idUsuario)
    {
        $datos = $this->database->queryOne(
            "SELECT COUNT(*) AS total, SUM(pp.fue_correcta) AS correctas
            FROM partida_preguntas pp
            JOIN partidas pa ON pa.id = pp.partida_id
            WHERE pa.usuario_id = :usuario_id AND pp.respondida_en IS NOT NULL",
            [':usuario_id' => $idUsuario]
        );

        $total = (int)$datos['total'];

        if ($total === 0) {
            return 50.00; // jugador nuevo: nivel regular
        }

        return round(((int)$datos['correctas'] / $total) * 100, 2);
    }

    // Jugador que acierta mucho recibe preguntas difíciles; el que acierta poco recibe fáciles.
    private function elegirPreguntaSegunNivel($candidatas, $idUsuario)
    {
        $nivel = $this->obtenerNivelDelJugador($idUsuario);

        $dificultadBuscada = match (true) {
            $nivel >= 70.00 => 'Dificil',
            $nivel <= 30.00 => 'Facil',
            default         => 'Regular',
        };

        foreach ($candidatas as $candidata) {
            if ($this->obtenerDificultadPregunta($candidata['id']) === $dificultadBuscada) {
                return $candidata['id'];
            }
        }

        return $candidatas[0]['id']; // si no hay de ese nivel, cualquiera
    }
}