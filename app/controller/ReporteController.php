<?php

namespace app\controller;

class ReporteController
{
    private $model;
    private $render;
    private $pdfReportGenerator;

    public function __construct($model, $render, $pdfReportGenerator)
    {
        $this->model = $model;
        $this->render = $render;
        $this->pdfReportGenerator = $pdfReportGenerator;
    }

    // Pantalla: el reporte en HTML
    public function ranking()
    {
        $this->render->renderiza('reporteRanking', $this->armarDatos(false));
    }

    // El mismo reporte, convertido a PDF
    public function rankingPdf()
    {
        $html = $this->render->render('reporteRanking', $this->armarDatos(true));

        $this->pdfReportGenerator->renderPdf($html, 'ranking.pdf');
        exit(); // obligatorio: después del PDF no puede salir nada más
    }

    private function armarDatos($esPdf)
    {
        $filas = [];

        foreach ($this->model->getRanking() as $i => $jugador) {
            $filas[] = [
                'posicion' => $i + 1,
                'nombre'   => $jugador['nombre_usuario'],
                'puntaje'  => (int) $jugador['puntaje_maximo']
            ];
        }

        return [
            'esPdf'  => $esPdf,
            'titulo' => 'Ranking de jugadores',
            'fecha'  => date('d/m/Y H:i'),
            'filas'  => $filas
        ];
    }
}