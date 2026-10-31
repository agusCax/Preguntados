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
            $sorteada = $this->database->queryOne(
                "SELECT p.id
                FROM preguntas p
                WHERE p.estado = 'activa'
                AND p.id NOT IN (
                    SELECT pregunta_id FROM partida_preguntas WHERE partida_id = :partida_id
                )
                ORDER BY RAND()
                LIMIT 1",
                [':partida_id' => $idPartida]
            );

            if (!$sorteada) {
                return null; // no quedan preguntas
            }


            $idPregunta = $sorteada['id'];

            $porcentajeDificultad = $this->obtenerPorcentajePreguntaCorrecta($idPregunta);


            $dificultadPregunta = match (true) {
                $porcentajeDificultad >= 70.00 => 'Facil',
                $porcentajeDificultad <= 30.00 => 'Dificil',
                default => 'Regular',
            };

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

    public function obtenerPorcentajePreguntaCorrecta($idPregunta){

        $sql = "SELECT COUNT(*) AS total_intentos, SUM(pp.fue_correcta) AS total_correctas
        FROM partida_preguntas pp
        WHERE pregunta_id = :pregunta_id AND respondida_en IS NOT NULL,
        ['pregunta_id' => $idPregunta]";

        $totalIntentos = (int)$sql['total_intentos'];

        $totalCorrectas = (int)$sql['total_correctas'];

        $porcentajeCorrectas = ($totalCorrectas / $totalIntentos) * 100;

        return round($porcentajeCorrectas,2);
    }

    //SEGUIR HACIENDO ESTA FUNCION
    // agus dijo que se puede hacer totalpartidasganadas(=10 puntos/totalpartidas)


    public function obtenerPorcentajeRespuestasUsuarios($usuarioId){
        $sql = "SELECT COUNT(*) AS total_partidas, SUM(p.puntaje) AS total_correctas
        FROM partidas p
        WHERE usuario_id = :usuario_id AND estado = 'terminada',
        ['usuario_id' => $usuarioId]";

        $totalPartidas = (int)$sql['total_partidas'];

        $totalCorrectas = (int)$sql['total_correctas'];

        $porcentajeCorrectas = ($totalCorrectas / $totalIntentos) * 100;

        return round($porcentajeCorrectas,2);

    }


}