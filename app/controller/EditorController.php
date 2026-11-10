<?php

namespace app\controller;

use app\Enum\Rol;
use Request;

class EditorController
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
        if ($_SESSION['rol'] !== Rol::Editor->name) {
            header('Location: ' . BASE_URL);
            exit();
        }

        $data = [
            'nombreUsuario' => $_SESSION['usuario'],
            'totalSugerencias' => $this->model->contarPendientes('pendiente'),
            'totalReportes' => $this->model->contarPendientes('reportada')
        ];

        $this->render->renderiza('editor/inicio', $data);
    }

    //SUGERENCIAS
    public function listarSugerencias(){
        $moderaciones = $this->model->listar('pendiente');
        $data = [
            'esReporte' => false,
            'hayModeraciones' => !empty($moderaciones),
            'moderaciones' => $moderaciones,
            'urlVolver' => BASE_URL . '/editor/show',
        ];

        $this->render->renderiza('editor/moderacionListado', $data);
    }

    public function detalleSugerencia()
    {
        $preguntaId = $_GET['id'] ?? null;
        
        if (! $preguntaId) {
            $this->redirigir('Sugerencias');
        }

        $data = $this->model->obtenerSugerencia($preguntaId);

        if(! $data){
            $this->redirigir('Sugerencias');
        }

        $data['esReporte'] = false;
        $data['idPregunta'] = $preguntaId;
        $data['urlListado'] = BASE_URL . '/editor/listarSugerencias';
        $data['urlProcesar'] = BASE_URL . '/editor/procesarSugerencia';

        $this->render->renderiza('editor/moderacionDetalle', $data);
    }

    public function procesarSugerencia()
    {
        $id = Request::post('id');          
        $accion = Request::post('accion'); 

        $modificada = $this->model->procesarSugerencia($id, $accion);

        if (!$modificada) {
            $mensaje = 'No se pudo actualizar la pregunta.';
        } elseif ($accion === 'aceptar') {
            $mensaje = 'La sugerencia fue aceptada.';
        } else {
            $mensaje = 'La sugerencia fue rechazada.';
        }

        $data = [
            'confirmacion' => $modificada,
            'mensaje' => $mensaje,
            'volverUrl' => BASE_URL . '/editor/listarSugerencias'
        ];

        $this->render->renderiza('editor/resultado', $data);
    }

    //REPORTES
    public function listarReportes(){
        $moderaciones =  $this->model->listar('reportada');
        $data = [
            'esReporte' => true,
            'hayModeraciones' => !empty($moderaciones),
            'moderaciones' => $moderaciones,
            'urlVolver' => BASE_URL . '/editor/show',
        ];
        $this->render->renderiza('editor/moderacionListado', $data);
    }

    public function detalleReporte()
    {
        $preguntaId = $_GET['id'] ?? null;
        
        if (! $preguntaId) {
            $this->redirigir('Reportes');
        }

        $data = $this->model->obtenerReporte($preguntaId);

        if(! $data){
            $this->redirigir('Reportes');
        }

        $data['esReporte'] = true;
        $data['preguntaId'] = $preguntaId;
        $data['urlListado'] = BASE_URL . '/editor/listarReportes';
        $data['urlProcesar'] = BASE_URL . '/editor/procesarReporte';

        $this->render->renderiza('editor/moderacionDetalle', $data);
    }

    public function procesarReporte()
    {
        $preguntaId = Request::post('pregunta_id');
        $reporteId = Request::post('reporte_id');
        $accion = Request::post('accion');

        //Aceptar -> Inactivar o modificar pregunta
        if ($accion === 'aceptar') {
            $this->aceptarReporte($preguntaId);
            return;
        }

        //Rechazar -> Reactivar pregunta
        $rechazado = $this->model->rechazarReporte($preguntaId, $reporteId);
        $mensaje = $rechazado
            ? 'El reporte se rechazó y la pregunta volvió a estar activa.'
            : 'No se pudo actualizar el estado del reporte.';

        $this->render->renderiza('editor/resultado', [
            'confirmacion' => $rechazado,
            'mensaje' => $mensaje,
            'volverUrl' => BASE_URL . '/editor/listarReportes',
        ]);
    }

    private function aceptarReporte($preguntaId)
    {
        $resolucion = $this->model->obtenerResolucionReporte($preguntaId);

        if (!$resolucion) {
            $this->redirigir('Reportes');
        }

        $letras = ['A', 'B', 'C', 'D'];
        $opciones = [];

        foreach ($resolucion['opciones'] as $indice => $opcion) {
            $opciones[] = [
                'indice' => $indice,
                'letra' => $letras[$indice] ?? (string) ($indice + 1),
                'id' => $opcion['id'],
                'texto' => $opcion['texto'],
                'esCorrecta' => (int) $opcion['es_correcta'] === 1,
            ];
        }

        $categorias = [];

        foreach ($resolucion['categorias'] as $categoria) {
            $categorias[] = [
                'id' => $categoria['id'],
                'nombre' => $categoria['nombre'],
                'icono' => $categoria['icono'],
                'seleccionada' => $categoria['id'] === $resolucion['pregunta']['categoria_id'],
            ];
        }

        $this->render->renderiza('editor/reporteAceptado', [
            'reporte' => $resolucion['reporte'],
            'pregunta' => [
                'id' => $resolucion['pregunta']['id'],
                'texto' => $resolucion['pregunta']['texto'],
            ],
            'respuestas' => $opciones,
            'categorias' => $categorias,
            'urlInactivar' => BASE_URL . '/editor/inactivarPregunta',
            'urlGuardarEdicion' => BASE_URL . '/editor/modificarPregunta',
            'urlListado' => BASE_URL . '/editor/listarReportes',
        ]);
    }

    public function inactivarPregunta(){
        $reporteId = $_POST['reporte_id'];
        $preguntaId = $_POST['pregunta_id'];

        $inactiva = $this->model->inactivarPregunta($preguntaId, $reporteId);
        $mensaje = $inactiva
            ? 'La pregunta se inactivó correctamente.'
            : 'No se pudo aceptar el reporte ni inactivar la pregunta.';

        $this->render->renderiza('editor/resultado', [
            'confirmacion' => $inactiva,
            'mensaje' => $mensaje,
            'volverUrl' => BASE_URL . '/editor/listarReportes',
        ]);
    }

    public function modificarPregunta(){
        $reporteId = Request::post('reporte_id') ?? null;
        $preguntaId = Request::post('pregunta_id') ?? null;
        
        $preguntaTexto = Request::post('pregunta_texto') ?? null;
        $categoriaId =  Request::post('categoria_id') ?? null;
        $respuestas = Request::post('respuestas') ?? null;
        $respuestaCorrectaId = Request::post('respuesta_correcta_id') ?? null;

        if(
            $reporteId === null || $preguntaId === null || $preguntaTexto === null ||
            $categoriaId === null || $respuestas === null || $respuestaCorrectaId === null)
        {
            return;
        }

        $modificada = $this->model->modificarPregunta($preguntaId, $preguntaTexto, $categoriaId, $respuestas, $respuestaCorrectaId, $reporteId);
        $mensaje = $modificada
            ? 'La pregunta se modificó correctamente.'
            : 'No se pudo modificar la pregunta.';

        $this->render->renderiza('editor/resultado', [
            'confirmacion' => $modificada,
            'mensaje' => $mensaje,
            'volverUrl' => BASE_URL . '/editor/listarReportes',
        ]);
    }

    //HELPERS
    private function redirigir($tipoModeracion){
        header('Location: ' . BASE_URL . '/editor/listar'. $tipoModeracion);
        exit();
    }
}