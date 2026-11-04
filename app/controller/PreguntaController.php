<?php

namespace app\controller;

use Redirect;

class PreguntaController
{
    private $render;
    private $preguntaModel;
    private const MAX_PREGUNTAS_POR_PARTIDA = 10;

    private const SEGUNDOS_POR_PREGUNTA = 30;

    public function __construct($preguntaModel, $render)
    {
        $this->preguntaModel = $preguntaModel;
        $this->render = $render;
    }

    public function show()
    {
        if (!isset($_SESSION['usuario_id'])) {
            Redirect::to(BASE_URL . '/login/show');
            exit();
        }

        //PENDIENTE: Si hay partida en curso?

        // Si no hay partida en curso, se crea una en la base y se guarda su id (INT)
        if (!isset($_SESSION['partida_activa'])) {
            $_SESSION['partida_activa'] = $this->preguntaModel->crearPartida($_SESSION['usuario_id']);
            $_SESSION['puntaje'] = 0;
            $_SESSION['contadorCorrectas'] = 0;
        }

        // Sonido pendiente de la respuesta anterior (se usa una sola vez, así F5 no lo repite)
        $sonido = $_SESSION['sonido'] ?? '';
        unset($_SESSION['sonido']);

        $idPartida = $_SESSION['partida_activa'];
        $preguntasRespondidas = $this->preguntaModel->obtenerPreguntasRespondidas($idPartida);

        /*
        if (count($preguntasRespondidas) >= self::MAX_PREGUNTAS_POR_PARTIDA) {
            $this->terminarPartida(
                'Partida terminada',
                'Completaste todas las preguntas de esta partida.',
                'ganada'
            );
        }*/

        $pregunta = $this->preguntaModel->obtenerPreguntaParaPartida($idPartida);

        if (!$pregunta) {
            // No quedan preguntas: se cierra la partida con el puntaje actual
            $this->terminarPartida(
                'Partida terminada',
                'No quedan más preguntas disponibles.',
                'ganada'
            );
        }

        // Se guarda el inicio solo si la pregunta es nueva, para que recargar (F5) no reinicie el tiempo
        if (($_SESSION['pregunta_actual'] ?? null) != $pregunta['id']) {
            $_SESSION['pregunta_actual'] = $pregunta['id'];
            $_SESSION['inicio_pregunta'] = time();
        }

        $segundos_usados = time() - $_SESSION['inicio_pregunta'];
        $segundos_restantes = max(0, self::SEGUNDOS_POR_PREGUNTA - $segundos_usados);

        // Si el tiempo ya se agotó (ej: recargó la página tarde), se termina la partida
        if ($segundos_restantes <= 0) {
            $this->preguntaModel->registrarTiempoAgotado($idPartida, $pregunta['id']);
            $this->terminarPartida(
                'Se acabó el tiempo',
                'La partida terminó porque se agotó el tiempo.',
                'tiempo'
            );
        }

        $letras = ['A', 'B', 'C', 'D'];
        $respuestasFormateadas = [];
        foreach ($pregunta['opciones'] as $index => $opcion) {
            $respuestasFormateadas[] = [
                'id'    => $opcion['id'],
                'letra' => $letras[$index] ?? '',
                'texto' => $opcion['texto']
            ];
        }

        $datos = [
            'pantalla_pregunta'   => true,
            'categoria_color'    => $pregunta['color'] ?? '#0284c7',
            'categoria_icono'    => $pregunta['icono'] ?? '❓',
            'categoria_nombre'   => $pregunta['categoria'] ?? 'General',
            'puntaje'            => $_SESSION['puntaje'] ?? 0,
            'segundos_totales'   => self::SEGUNDOS_POR_PREGUNTA,
            'segundos_restantes' => $segundos_restantes,
            'pregunta_id'        => $pregunta['id'],
            'pregunta_texto'     => $pregunta['texto'],
            'respuestas'         => $respuestasFormateadas,
            'sonido'             => $sonido
        ];

        $this->render->renderiza("pregunta", $datos, false);
    }
/*
    public function responder()
    {
        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['partida_activa'])) {
            Redirect::to(BASE_URL . '/login/show');
            exit();
        }

        $idPartida = $_SESSION['partida_activa'];
        $idPregunta = $_POST['pregunta_id'] ?? null;
        $idRespuesta = $_POST['respuesta_id'] ?? null;
        $tiempoAgotado = ($_POST['tiempo_agotado'] ?? '0') === '1';
        $segundosUsados = time() - ($_SESSION['inicio_pregunta'] ?? time());
        $tiempoAgotado = $tiempoAgotado || $segundosUsados > self::SEGUNDOS_POR_PREGUNTA;

        if (!$idPregunta) {
            Redirect::to(BASE_URL . '/pregunta/show');
            exit();
        }

        if ($tiempoAgotado || !$idRespuesta) {
            $this->preguntaModel->registrarTiempoAgotado($idPartida, $idPregunta);
            $this->terminarPartida(
                'Se acabó el tiempo',
                'La partida terminó porque se agotó el tiempo.',
                'tiempo'
            );
        }

        $esCorrecta = $this->preguntaModel->registrarRespuesta($idPartida, $idPregunta, $idRespuesta);

        if ($esCorrecta) {
            $_SESSION['puntaje'] = ($_SESSION['puntaje'] ?? 0) + 1;
            $_SESSION['sonido'] = 'correcta'; // suena al cargar la siguiente pregunta
            Redirect::to(BASE_URL . '/pregunta/show');
        } else {
            $this->terminarPartida(
                'Respuesta incorrecta',
                'La partida terminó porque la respuesta no era correcta.',
                'perdida'
            );
        }
        exit();
    }
    */

    public function responder()
    {

        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['partida_activa'])) {
            Redirect::to(BASE_URL . '/login/show');
            exit();
        }

        $idPartida = $_SESSION['partida_activa'];
        $idPregunta = $_POST['pregunta_id'] ?? null;
        $idRespuesta = $_POST['respuesta_id'] ?? null;
        $tiempoAgotado = ($_POST['tiempo_agotado'] ?? '0') === '1';
        $segundosUsados = time() - ($_SESSION['inicio_pregunta'] ?? time());
        $tiempoAgotado = $tiempoAgotado || $segundosUsados > self::SEGUNDOS_POR_PREGUNTA;

        if (!$idPregunta) {
            Redirect::to(BASE_URL . '/pregunta/show');
            exit();
        }

        if ($tiempoAgotado || !$idRespuesta) {
            $this->preguntaModel->registrarTiempoAgotado($idPartida, $idPregunta);
            $this->terminarPartida(
                'Se acabó el tiempo',
                'La partida terminó porque se agotó el tiempo.',
                'tiempo'
            );
        }

        $esCorrecta = $this->preguntaModel->registrarRespuesta($idPartida, $idPregunta, $idRespuesta);

        if ($esCorrecta) {
            $_SESSION['puntaje'] = ($_SESSION['puntaje'] ?? 0) + 1;
            $_SESSION['contadorCorrectas'] = ($_SESSION['contadorCorrectas'] ?? 0) + 1; //CONTADOR

            //BONO
            if ($_SESSION['contadorCorrectas'] === 5) {
                $_SESSION['puntaje'] += 5;
                $_SESSION['contadorCorrectas'] = 0; //REINICIA
            }
            Redirect::to(BASE_URL . '/pregunta/show');

        } else {
            $penalizacion = $this->penalizacionIncorrectas($idPregunta);
            $_SESSION['puntaje'] = max(0,($_SESSION['puntaje'] ?? 0)- $penalizacion);
            $this->terminarPartida(
                'Respuesta incorrecta',
                'La partida terminó porque la respuesta no era correcta.',
                'perdida'
            );
        }
        exit();
    }

    // Cuántos puntos resta fallar una pregunta, según su dificultad
    private function penalizacionIncorrectas($idPregunta)
    {
        $dificultad = $this->preguntaModel->obtenerDificultadPregunta($idPregunta);

        return match ($dificultad) {
            'Facil'   => 3,
            'Regular' => 2,
            'Dificil' => 1,
        };
    }


    private function terminarPartida($titulo, $mensaje, $tipoResultado)
    {
        $puntaje = $_SESSION['puntaje'] ?? 0;

        if (isset($_SESSION['partida_activa'])) {
            $this->preguntaModel->terminarPartida($_SESSION['partida_activa'], $_SESSION['puntaje'] ?? 0);
        }
        
        unset($_SESSION['partida_activa'], $_SESSION['puntaje'],$_SESSION['pregunta_actual'], $_SESSION['inicio_pregunta'], $_SESSION['contador_correctas']);
        
        $this->render->renderiza('resultado', [
            'sonido' => $sonido,
            'pantalla_resultado' => true,
            'resultado_titulo' => $titulo,
            'resultado_mensaje' => $mensaje,
            'resultado_tipo' => $tipoResultado,
            'puntaje' => $puntaje
        ], false);

        exit();
    }
}