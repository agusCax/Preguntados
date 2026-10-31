<?php

namespace app\controller;

use Redirect;

class RankingController
{
    private $model;
    private $render;

    public function __construct($model, $render)
    {
        $this->model = $model;
        $this->render = $render;
    }

    public function show()
    {
        $usuarioId = $_SESSION['usuario_id'] ?? null;

        if ($usuarioId === null) {
            Redirect::to(BASE_URL . '/login/show');
        }

        $jugadores = $this->model->getRanking();
        $partidas = $this->model->getPartidasDeUsuario($usuarioId);

        $data = $this->armarRanking($jugadores, $usuarioId);
        $data += $this->armarMisPartidas($partidas);

        $this->render->renderiza("ranking", $data);
    }

    // Numera a los jugadores (1°, 2°, 3°...) y marca cuál es el usuario que está mirando
    private function armarRanking($jugadores, $usuarioId)
    {
        $ranking = [];
        $miPosicion = null;
        $miTotal = 0;

        foreach ($jugadores as $i => $jugador) {
            $posicion = $i + 1;
            $puntaje = (int) $jugador['puntaje_total'];
            $esYo = (int) $jugador['id'] === (int) $usuarioId;

            if ($esYo) {
                $miPosicion = $posicion;
                $miTotal = $puntaje;
            }

            $ranking[] = [
                'posicion' => $posicion,
                'id' => $jugador['id'],
                'nombre' => $jugador['nombre_usuario'],
                'cantidad_partidas' => (int) $jugador['cantidad_partidas'],
                'puntaje' => $puntaje,
                // Solo los 3 primeros con puntos llevan una medalla
                'medalla' => ($puntaje > 0 && $posicion <= 3) ? $posicion : false,
                'esYo' => $esYo
            ];
        }

        return [
            'ranking' => $ranking,
            'hayRanking' => !empty($ranking),
            'miPosicion' => $miPosicion,
            'miTotal' => $miTotal
        ];
    }

    // Lista las partidas del usuario con su puntaje y calcula un pequeño resumen
    private function armarMisPartidas($partidas)
    {
        $misPartidas = [];
        $total = count($partidas);
        $mejor = 0;
        $terminadas = 0;

        foreach ($partidas as $i => $partida) {
            $terminada = $partida['estado'] === 'terminada';
            $puntaje = (int) $partida['puntaje'];

            if ($terminada) {
                $terminadas++;
                $mejor = max($mejor, $puntaje);
            }

            $misPartidas[] = [
                // La más vieja es la partida 1 y la más nueva tiene el número más alto
                'numero' => $total - $i,
                'fecha' => date('d/m/Y H:i', strtotime($partida['fecha_inicio'])),
                'puntaje' => $puntaje,
                'terminada' => $terminada
            ];
        }

        return [
            'misPartidas' => $misPartidas,
            'hayMisPartidas' => !empty($misPartidas),
            'misPartidasTerminadas' => $terminadas,
            'miMejorPartida' => $mejor
        ];
    }
}
