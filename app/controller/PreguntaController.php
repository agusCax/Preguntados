<?php

namespace app\controller;

use Redirect;

class PreguntaController
{
    private $render;
    private $preguntaModel;
    private const MAX_PREGUNTAS_POR_PARTIDA = 10;

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
        }

        $idPartida = $_SESSION['partida_activa'];
        $preguntasRespondidas = $this->preguntaModel->obtenerPreguntasRespondidas($idPartida);

        if (count($preguntasRespondidas) >= self::MAX_PREGUNTAS_POR_PARTIDA) {
            $this->terminarPartida(
                'Partida terminada',
                'Completaste todas las preguntas de esta partida.',
                'ganada'
            );
        }

        $pregunta = $this->preguntaModel->obtenerPreguntaParaPartida($idPartida);

        if (!$pregunta) {
            // No quedan preguntas: se cierra la partida con el puntaje actual
            $this->terminarPartida(
                'Partida terminada',
                'No quedan más preguntas disponibles.',
                'ganada'
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
            'segundos_totales'   => 30,
            'segundos_restantes' => 30,
            'pregunta_id'        => $pregunta['id'],
            'pregunta_texto'     => $pregunta['texto'],
            'respuestas'         => $respuestasFormateadas
        ];

        $this->render->renderiza("pregunta", $datos, false);
    }

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

    private function terminarPartida($titulo, $mensaje, $tipoResultado)
    {
        $puntaje = $_SESSION['puntaje'] ?? 0;

        if (isset($_SESSION['partida_activa'])) {
            $this->preguntaModel->terminarPartida($_SESSION['partida_activa'], $_SESSION['puntaje'] ?? 0);
        }
        
        unset($_SESSION['partida_activa'], $_SESSION['puntaje']);
        
        $this->render->renderiza('resultado', [
            'pantalla_resultado' => true,
            'resultado_titulo' => $titulo,
            'resultado_mensaje' => $mensaje,
            'resultado_tipo' => $tipoResultado,
            'puntaje' => $puntaje
        ], false);

        exit();
    }
}