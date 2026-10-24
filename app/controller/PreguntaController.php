<?php

namespace app\controller;

use Request;

class PreguntaController
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
        $data = [
            'categoria_nombre' => 'Ciencia',
            'categoria_color' => '#10b981',
            'categoria_icono' => '🔬',
            'pregunta_id' => 1,
            'pregunta_texto' => '¿Cuál es el planeta más cercano al Sol?',
            'respuestas' => [
                ['id' => 1, 'letra' => 'A', 'texto' => 'Venus'],
                ['id' => 2, 'letra' => 'B', 'texto' => 'Mercurio'],
                ['id' => 3, 'letra' => 'C', 'texto' => 'Marte'],
                ['id' => 4, 'letra' => 'D', 'texto' => 'Tierra'],
            ],
            'puntaje' => 0,
            'segundos_totales' => 20,
            'segundos_restantes' => 20,
        ];

        $this->render->renderiza("pregunta", $data, false);
    }
}