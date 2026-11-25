<?php

namespace app\controller;

use app\enum\EstadoPregunta;
use Request;

class EditorController
{
    private $model;
    private $render;
    private const PREGUNTAS_POR_PAGINA = 10;
    private const CATEGORIAS_POR_PAGINA = 10;
    // Íconos que se pueden elegir al crear o editar una categoría
    private const ICONOS = [
        '🔬', '🏛️', '🌎', '🎨', '⚽', '🎬', '🎵', '📚', '💻', '🚀', '🧠', '🧪',
        '🔭', '🌿', '🐶', '🍔', '🍕', '🏀', '🏆', '🎮', '🎲', '🎭', '📺', '✈️',
        '🚗', '💡', '🏥', '🌍',
    ];

    public function __construct($model, $render)
    {
        $this->model = $model;
        $this->render = $render;
    }

    //MODERACION
    public function show()
    {
        $data = [
            'nombreUsuario' => $_SESSION['usuario'],
            'totalSugerencias' => $this->model->contarPreguntasPorEstado('pendiente'),
            'totalReportes' => $this->model->contarPreguntasPorEstado('reportada')
        ];

        $this->render->renderiza('editor/moderacion', $data);
    }

    //BANCO DE PREGUNTAS
    public function listarBanco()
    {
        $total = $this->model->contarBanco();
        $totalPaginas = max(1, (int) ceil($total / self::PREGUNTAS_POR_PAGINA));

        // La página viene por URL: se fuerza a que sea un número entre 1 y la última
        $pagina = (int) Request::get('pagina', 1);
        $pagina = min(max(1, $pagina), $totalPaginas);

        $preguntas = $this->model->listarBanco(
            self::PREGUNTAS_POR_PAGINA,
            ($pagina - 1) * self::PREGUNTAS_POR_PAGINA
        );

        // Números de página que se muestran: la actual y 2 hacia cada lado
        $urlBase = BASE_URL . '/editor/listarBanco?pagina=';
        $paginas = [];
        for ($i = max(1, $pagina - 2); $i <= min($totalPaginas, $pagina + 2); $i++) {
            $paginas[] = [
                'numero'   => $i,
                'url'      => $urlBase . $i,
                'esActual' => $i === $pagina,
            ];
        }

        $data = [
            'hayPreguntas'    => !empty($preguntas),
            'preguntas'       => $preguntas,
            'urlNueva'        => BASE_URL . '/editor/nuevaPregunta',
            'hayPaginacion'   => $totalPaginas > 1,
            'paginas'         => $paginas,
            'hayAnterior'     => $pagina > 1,
            'urlAnterior'     => $urlBase . ($pagina - 1),
            'haySiguiente'    => $pagina < $totalPaginas,
            'urlSiguiente'    => $urlBase . ($pagina + 1),
            'paginaActual'    => $pagina,
            'totalPaginas'    => $totalPaginas,
            'totalPreguntas'  => $total,
        ];

        $this->render->renderiza('editor/bancoListado', $data);
    }

    public function nuevaPregunta()
    {   
        $respuestasVacias = array_fill(0, 4, ['id' => '', 'texto' => '', 'es_correcta' => 0]);

        $data = [
            'urlForm' => BASE_URL . '/editor/guardarNuevaPregunta',
            'textoBtnForm' => 'Crear pregunta', 
            'respuestas' => $this->armarRespuestas($respuestasVacias), 
            'categorias' => $this->armarCategorias($this->model->listarCategorias(), null), 
            'urlListado' => BASE_URL . '/editor/listarBanco'
        ];

        $this->render->renderiza('editor/nuevaPregunta', $data);
    }

    public function guardarNuevaPregunta()
    {   
        $urlVolver = BASE_URL . '/editor/listarBanco';
        $dataValidada = $this->validarPregunta($urlVolver);
        
        $confirmacion = $this->model->guardarNuevaPregunta($dataValidada['preguntaTexto'], $dataValidada['categoriaId'], $dataValidada['respuestas'], $dataValidada['respuestaCorrecta']);

        if (!$confirmacion) {
            $mensaje = 'No se pudo agregar la pregunta.';
        } else {
            $mensaje = 'Se pudo agregar la pregunta exitosamente.';
        }

        $data = [
            'confirmacion' => $confirmacion,
            'mensaje' => $mensaje,
            'volverUrl' => BASE_URL . '/editor/listarBanco'
        ];

        $this->render->renderiza('editor/resultado', $data);
    }

    public function detallePregunta()
    {
        $preguntaId = $_GET['id'] ?? null;

        if (!$preguntaId) {
            $this->redirigir('Banco');
        }

        $data = $this->model->obtenerPreguntaConOpciones($preguntaId);

        if (!$data) {
            $this->redirigir('Banco');
        }

        switch($data['pregunta']['estado']){
            case EstadoPregunta::Reportada->value: 
                $this->detalleReporte();
                break;
            case EstadoPregunta::Pendiente->value:
                $this->detalleSugerencia();
                break;
            case EstadoPregunta::Activa->value: 
                $this->detalleActivaInactiva($preguntaId, $data);
                break;
            case EstadoPregunta::Inactiva->value:
                $this->detalleActivaInactiva($preguntaId, $data);
                break;
        }
    }

    private function detalleActivaInactiva($preguntaId, $data)
    {        
        $estado = $data['pregunta']['estado'];

        $data['estado'] = $estado;
        $data['textoBtn'] = $estado === EstadoPregunta::Activa->value ? 'Inactivar' : 'Activar';        
        $data['confirmacionEstado'] = '¿Está seguro de que desea modificar el estado?';
        $data['nuevoEstado'] = $estado === EstadoPregunta::Activa->value ? EstadoPregunta::Inactiva->value : EstadoPregunta::Activa->value;
        $data['urlCambiarEstado'] = BASE_URL . '/editor/cambiarEstado';
        $data['urlEditar'] = BASE_URL . '/editor/obtenerPreguntaAModificar?id=' . $preguntaId;
        $data['urlListado'] = BASE_URL . '/editor/listarBanco';

        $this->render->renderiza('editor/bancoDetalle', $data);
    }

    public function cambiarEstado()
    {
        $preguntaId = Request::post('pregunta_id') ?? null;
        $nuevoEstado = Request::post('nuevo_estado') ?? null;

        if (!$preguntaId || 
            $nuevoEstado !== EstadoPregunta::Activa->value && $nuevoEstado !== EstadoPregunta::Inactiva->value) {
            $this->redirigir('Banco');
        }
        
        $confirmacion = $nuevoEstado === EstadoPregunta::Activa->value ? $this->model->activarPregunta($preguntaId) : $this->model->inactivarPregunta($preguntaId);
        
        if (!$confirmacion) {
            $mensaje = 'No se pudo modificar el estado de la pregunta.';
        } else {
            $mensaje = 'El estado de la pregunta fue modificado exitosamente.';
        }

        $data = [
            'confirmacion' => $confirmacion,
            'mensaje' => $mensaje,
            'volverUrl' => BASE_URL . '/editor/listarBanco'
        ];

        $this->render->renderiza('editor/resultado', $data);
    }

    public function obtenerPreguntaAModificar()
    {
        $preguntaId = $_GET['id'] ?? null;

        if (!$preguntaId) {
            $this->redirigir('Banco');
        }

        $data = $this->model->obtenerPreguntaConOpciones($preguntaId);

        if (!$data) {
            $this->redirigir('Banco');
        }

        $this->render->renderiza('editor/bancoEditar', [
            'pregunta' => [
                'id' => $data['pregunta']['id'],
                'texto' => $data['pregunta']['texto'],
            ],
            'respuestas' => $this->armarRespuestas($data['opciones']),
            'categorias' => $this->armarCategorias($this->model->listarCategorias($data['pregunta']['categoria_id']), $data['pregunta']['categoria_id']),
            'urlForm' => BASE_URL . '/editor/modificarPregunta',
            'textoBtnForm' => 'Guardar cambios',
            'confirmacionForm' => '¿Confirmás los cambios en la pregunta?',
            'urlListado' => BASE_URL . '/editor/listarBanco',
        ]);
    }

    public function modificarPregunta()
    {
        $urlVolver = BASE_URL . '/editor/listarBanco';

        $dataValidada = $this->validarPreguntaAModificar($urlVolver);
        
        $confirmacion = $this->model->modificarPregunta($dataValidada['preguntaId'], $dataValidada['preguntaTexto'], $dataValidada['categoriaId'], $dataValidada['respuestas'], $dataValidada['respuestaCorrecta']);
        
        $mensaje = $confirmacion
            ? 'La pregunta se modificó correctamente.'
            : 'No se pudo modificar la pregunta.';

        $this->render->renderiza('editor/resultado', [
            'confirmacion' => $confirmacion,
            'mensaje' => $mensaje,
            'volverUrl' => $urlVolver,
        ]);
    }

    //SUGERENCIAS
    public function listarSugerencias()
    {
        $moderaciones = $this->model->listarPreguntasPorEstado('pendiente');
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

        if (!$preguntaId) {
            $this->redirigir('Sugerencias');
        }

        $data = $this->model->obtenerSugerencia($preguntaId);

        if (!$data) {
            $this->redirigir('Sugerencias');
        }

        $data['esReporte'] = false;
        $data['idPregunta'] = $preguntaId;
        $data['urlListado'] = BASE_URL . '/editor/listarSugerencias';
        $data['urlBanco'] = BASE_URL . '/editor/listarBanco';
        $data['urlProcesar'] = BASE_URL . '/editor/procesarSugerencia';

        $this->render->renderiza('editor/moderacionDetalle', $data);
    }

    public function procesarSugerencia()
    {
        $id = Request::post('pregunta_id');
        $accion = Request::post('accion');

        $confirmacion = $this->model->procesarSugerencia($id, $accion);

        if (!$confirmacion) {
            $mensaje = 'No se pudo actualizar la pregunta.';
        } elseif ($accion === 'aceptar') {
            $mensaje = 'La sugerencia fue aceptada.';
        } else {
            $mensaje = 'La sugerencia fue rechazada.';
        }

        $data = [
            'confirmacion' => $confirmacion,
            'mensaje' => $mensaje,
            'volverUrl' => BASE_URL . '/editor/listarSugerencias'
        ];

        $this->render->renderiza('editor/resultado', $data);
    }

    //REPORTES
    public function listarReportes()
    {
        $moderaciones = $this->model->listarPreguntasPorEstado('reportada');
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

        if (!$preguntaId) {
            $this->redirigir('Reportes');
        }

        $data = $this->model->obtenerReporte($preguntaId);

        if (!$data) {
            $this->redirigir('Reportes');
        }

        $data['esReporte'] = true;
        $data['preguntaId'] = $preguntaId;
        $data['urlListado'] = BASE_URL . '/editor/listarReportes';
        $data['urlBanco'] = BASE_URL . '/editor/listarBanco';
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
        $confirmacion = $this->model->rechazarReporte($preguntaId, $reporteId);
        $mensaje = $confirmacion
            ? 'El reporte se rechazó y la pregunta volvió a estar activa.'
            : 'No se pudo actualizar el estado del reporte.';

        $this->render->renderiza('editor/resultado', [
            'confirmacion' => $confirmacion,
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

        $this->render->renderiza('editor/reporteAceptado', [
            'reporte' => $resolucion['reporte'],
            'pregunta' => [
                'id' => $resolucion['pregunta']['id'],
                'texto' => $resolucion['pregunta']['texto'],
            ],
            'respuestas' => $this->armarRespuestas($resolucion['opciones']),
            'categorias' => $this->armarCategorias($resolucion['categorias'], $resolucion['pregunta']['categoria_id']),

            //Inactivar
            'urlInactivar' => BASE_URL . '/editor/inactivarPreguntaReportada',
            //Voler
            'urlListado' => BASE_URL . '/editor/listarReportes',
            //Editar
            'urlForm' => BASE_URL . '/editor/modificarPreguntaReportada',
            'textoBtnForm' => 'Guardar cambios y resolver reporte',
            'confirmacionForm' => '¿Confirmás los cambios y la resolución del reporte?',
            'infoForm' => 'Al guardar los cambios, el reporte quedará resuelto y la pregunta volverá a estar activa en el juego.',
        ]);
    }

    public function inactivarPreguntaReportada()
    {
        $reporteId = Request::post('reporte_id');
        $preguntaId = Request::post('pregunta_id');

        if ($reporteId === null || $preguntaId === null) {
            $this->mostrarError(
                'Faltan datos para inactivar la pregunta.',
                BASE_URL . '/editor/listarReportes'
            );
        }

        $confirmacion = $this->model->inactivarPreguntaReportada($preguntaId, $reporteId);
        $mensaje = $confirmacion
            ? 'La pregunta se inactivó correctamente.'
            : 'No se pudo aceptar el reporte ni inactivar la pregunta.';

        $this->render->renderiza('editor/resultado', [
            'confirmacion' => $confirmacion,
            'mensaje' => $mensaje,
            'volverUrl' => BASE_URL . '/editor/listarReportes',
        ]);
    }

    public function modificarPreguntaReportada()
    {
        $reporteId = Request::post('reporte_id') ?? null;
        $urlVolver = BASE_URL . '/editor/listarReportes';

        if ($reporteId === null) {
            $this->mostrarError('Faltan datos para modificar la pregunta. Completá todos los campos e intentá de nuevo.', $urlVolver);
        }
        
        $dataValidada = $this->validarPreguntaAModificar($urlVolver);

        $confirmacion = $this->model->modificarPreguntaReportada($dataValidada['preguntaId'], $dataValidada['preguntaTexto'], $dataValidada['categoriaId'], $dataValidada['respuestas'], $dataValidada['respuestaCorrecta'], $reporteId);

        $mensaje = $confirmacion
            ? 'La pregunta se modificó correctamente.'
            : 'No se pudo modificar la pregunta.';

        $this->render->renderiza('editor/resultado', [
            'confirmacion' => $confirmacion,
            'mensaje' => $mensaje,
            'volverUrl' => $urlVolver,
        ]);
    }

    //HELPERS
    private function validarPreguntaAModificar($urlVolver)
    {
        $preguntaId = Request::post('pregunta_id') ?? null;

        if ($preguntaId === null) {
            $this->mostrarError('Faltan datos para modificar la pregunta. Completá todos los campos e intentá de nuevo.', $urlVolver);
        }
        
        $dataValidada = $this->validarPregunta($urlVolver);
        
        $dataValidada['preguntaId'] = $preguntaId;

        return $dataValidada;
    }
    private function validarPregunta($urlVolver)
    {
        $preguntaTexto = Request::post('pregunta_texto') ?? null;
        $categoriaId = Request::post('categoria_id') ?? null;
        $respuestas = Request::post('respuestas');
        $respuestaCorrecta = Request::post('respuesta_correcta') ?? null;

        if ($preguntaTexto === null || $categoriaId === null || !is_array($respuestas) || $respuestaCorrecta === null) {
            $this->mostrarError('Faltan datos para modificar la pregunta. Completá todos los campos e intentá de nuevo.', $urlVolver);
        }

        if (!isset($respuestas[$respuestaCorrecta])) {
            $this->mostrarError('La respuesta correcta seleccionada no es válida.', $urlVolver);
        }

        return [
            'preguntaTexto' => $preguntaTexto, 
            'categoriaId' => $categoriaId, 
            'respuestas' => $respuestas, 
            'respuestaCorrecta' => $respuestaCorrecta];
    }

    //BANCO DE CATEGORIAS
    public function listarCategorias()
    {
        $total = $this->model->contarCategorias();
        $totalPaginas = max(1, (int) ceil($total / self::CATEGORIAS_POR_PAGINA));

        // La página viene por URL: se fuerza a que sea un número entre 1 y la última
        $pagina = (int) Request::get('pagina', 1);
        $pagina = min(max(1, $pagina), $totalPaginas);

        $filas = $this->model->listarBancoCategorias(
            self::CATEGORIAS_POR_PAGINA,
            ($pagina - 1) * self::CATEGORIAS_POR_PAGINA
        );

        $categorias = [];
        foreach ($filas as $fila) {
            $categorias[] = [
                'id'             => $fila['id'],
                'nombre'         => $fila['nombre'],
                'color'          => $fila['color'],
                'icono'          => $fila['icono'],
                'totalPreguntas' => $fila['total_preguntas'],
                'esActiva'       => (int) $fila['activa'] === 1,
                'urlEditar'      => BASE_URL . '/editor/editarCategoria?id=' . $fila['id'],
            ];
        }

        // Números de página que se muestran: la actual y 2 hacia cada lado
        $urlBase = BASE_URL . '/editor/listarCategorias?pagina=';
        $paginas = [];
        for ($i = max(1, $pagina - 2); $i <= min($totalPaginas, $pagina + 2); $i++) {
            $paginas[] = [
                'numero'   => $i,
                'url'      => $urlBase . $i,
                'esActual' => $i === $pagina,
            ];
        }

        $data = [
            'hayCategorias'    => !empty($categorias),
            'categorias'       => $categorias,
            'urlNueva'         => BASE_URL . '/editor/nuevaCategoria',
            'urlCambiarEstado' => BASE_URL . '/editor/cambiarEstadoCategoria',
            'hayPaginacion'    => $totalPaginas > 1,
            'paginas'          => $paginas,
            'hayAnterior'      => $pagina > 1,
            'urlAnterior'      => $urlBase . ($pagina - 1),
            'haySiguiente'     => $pagina < $totalPaginas,
            'urlSiguiente'     => $urlBase . ($pagina + 1),
            'paginaActual'     => $pagina,
            'totalPaginas'     => $totalPaginas,
            'totalCategorias'  => $total,
        ];

        $this->render->renderiza('editor/categoriasListado', $data);
    }

    public function nuevaCategoria()
    {
        $data = [
            'titulo'      => 'Nueva categoría',
            'descripcion' => 'Completá los datos para agregar una categoría al juego.',
            'textoBoton'  => 'Crear categoría',
            'urlForm'     => BASE_URL . '/editor/guardarNuevaCategoria',
            'idCategoria' => '',
            'nombre'      => '',
            'iconos'      => $this->armarIconos(''),
            'color'       => '#0284c7',
            'urlVolver'   => BASE_URL . '/editor/listarCategorias',
        ];

        $this->render->renderiza('editor/categoriaForm', $data);
    }

    public function guardarNuevaCategoria()
    {
        $urlVolver = BASE_URL . '/editor/listarCategorias';
        $datos = $this->validarCategoria($urlVolver, 0);

        $confirmacion = $this->model->crearCategoria($datos['nombre'], $datos['color'], $datos['icono']);

        $this->render->renderiza('editor/resultado', [
            'confirmacion' => $confirmacion,
            'mensaje'      => $confirmacion ? 'La categoría se creó correctamente.' : 'No se pudo crear la categoría.',
            'volverUrl'    => $urlVolver,
        ]);
    }

    public function editarCategoria()
    {
        $urlVolver = BASE_URL . '/editor/listarCategorias';
        $categoria = $this->model->obtenerCategoria((int) Request::get('id', 0));

        if (!$categoria) {
            $this->mostrarError('La categoría que querés editar no existe.', $urlVolver);
        }

        $data = [
            'titulo'      => 'Editar categoría',
            'descripcion' => 'Modificá los datos de la categoría. Sus preguntas se mantienen.',
            'textoBoton'  => 'Guardar cambios',
            'urlForm'     => BASE_URL . '/editor/guardarCategoria',
            'idCategoria' => $categoria['id'],
            'nombre'      => $categoria['nombre'],
            'iconos'      => $this->armarIconos($categoria['icono']),
            'color'       => $categoria['color'],
            'urlVolver'   => $urlVolver,
        ];

        $this->render->renderiza('editor/categoriaForm', $data);
    }

    public function guardarCategoria()
    {
        $urlVolver = BASE_URL . '/editor/listarCategorias';
        $idCategoria = (int) Request::post('id', 0);

        if (!$this->model->obtenerCategoria($idCategoria)) {
            $this->mostrarError('La categoría que querés editar no existe.', $urlVolver);
        }

        $datos = $this->validarCategoria($urlVolver, $idCategoria);

        $confirmacion = $this->model->actualizarCategoria($idCategoria, $datos['nombre'], $datos['color'], $datos['icono']);

        $this->render->renderiza('editor/resultado', [
            'confirmacion' => $confirmacion,
            'mensaje'      => $confirmacion ? 'La categoría se modificó correctamente.' : 'No se pudo modificar la categoría.',
            'volverUrl'    => $urlVolver,
        ]);
    }

    // Desactivar o reactivar (nunca se elimina una categoría)
    public function cambiarEstadoCategoria()
    {
        $urlVolver = BASE_URL . '/editor/listarCategorias';
        $idCategoria = (int) Request::post('id', 0);
        $accion = Request::post('accion');

        $categoria = $this->model->obtenerCategoria($idCategoria);

        if (!$categoria || ($accion !== 'desactivar' && $accion !== 'reactivar')) {
            $this->mostrarError('No se pudo identificar la categoría o la acción pedida.', $urlVolver);
        }

        $activar = $accion === 'reactivar';

        // Tiene que quedar al menos una categoría activa para poder jugar
        if (!$activar && (int) $categoria['activa'] === 1 && $this->model->contarCategoriasActivas() <= 1) {
            $this->mostrarError('No se puede desactivar la única categoría activa. Tiene que haber al menos una para poder jugar.', $urlVolver);
        }

        $confirmacion = $this->model->cambiarEstadoCategoria($idCategoria, $activar);

        if ($confirmacion) {
            $mensaje = $activar
                ? 'La categoría se reactivó. Sus preguntas vuelven a estar disponibles.'
                : 'La categoría se desactivó. Sus preguntas dejan de aparecer en el juego.';
        } else {
            $mensaje = 'No se pudo cambiar el estado de la categoría.';
        }

        $this->render->renderiza('editor/resultado', [
            'confirmacion' => $confirmacion,
            'mensaje'      => $mensaje,
            'volverUrl'    => $urlVolver,
        ]);
    }

    private function validarCategoria($urlVolver, $idActual)
    {
        $nombre = trim((string) Request::post('nombre', ''));
        $icono = trim((string) Request::post('icono', ''));
        $color = trim((string) Request::post('color', ''));

        if ($nombre === '' || mb_strlen($nombre) > 50) {
            $this->mostrarError('El nombre es obligatorio y puede tener hasta 50 caracteres.', $urlVolver);
        }

        // Solo vale un ícono de la lista (o el que la categoría ya tenía)
        $iconosValidos = self::ICONOS;
        $categoriaActual = $this->model->obtenerCategoria($idActual);
        if ($categoriaActual) {
            $iconosValidos[] = $categoriaActual['icono'];
        }

        if (!in_array($icono, $iconosValidos, true)) {
            $this->mostrarError('Elegí un ícono de la lista.', $urlVolver);
        }

        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            $this->mostrarError('El color no es válido.', $urlVolver);
        }

        if ($this->model->existeNombreCategoria($nombre, $idActual)) {
            $this->mostrarError('Ya existe una categoría con ese nombre.', $urlVolver);
        }

        return ['nombre' => $nombre, 'icono' => $icono, 'color' => $color];
    }

    // Lista de íconos para el desplegable, con el actual seleccionado
    // (si la categoría ya tenía uno que no está en la lista, también se muestra)
    private function armarIconos($iconoActual)
    {
        $iconos = self::ICONOS;

        if ($iconoActual !== '' && !in_array($iconoActual, $iconos, true)) {
            array_unshift($iconos, $iconoActual);
        }

        $listado = [];
        foreach ($iconos as $icono) {
            $listado[] = ['icono' => $icono, 'seleccionado' => $icono === $iconoActual];
        }

        return $listado;
    }

    //Convierte las opciones de la base en el formato que espera formPreguntaView
    private function armarRespuestas($opciones)
    {
        $letras = ['A', 'B', 'C', 'D'];
        $respuestas = [];

        foreach ($opciones as $indice => $opcion) {
            $respuestas[] = [
                'indice' => $indice,
                'letra' => $letras[$indice] ?? (string) ($indice + 1),
                'id' => $opcion['id'],
                'texto' => $opcion['texto'],
                'esCorrecta' => (int) $opcion['es_correcta'] === 1,
            ];
        }

        return $respuestas;
    }

    //Marca como seleccionada la categoria actual de la pregunta
    private function armarCategorias($categorias, $categoriaActualId)
    {
        $listado = [];

        foreach ($categorias as $categoria) {
            $listado[] = [
                'id' => $categoria['id'],
                'nombre' => $categoria['nombre'],
                'icono' => $categoria['icono'],
                'seleccionada' => $categoria['id'] === $categoriaActualId,
            ];
        }

        return $listado;
    }

    private function mostrarError($mensaje, $volverUrl, $titulo = 'No se pudo completar la acción')
    {
        $this->render->renderiza('editor/error', [
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'volverUrl' => $volverUrl,
        ]);
        exit();
    }

    private function redirigir($destino)
    {
        header('Location: ' . BASE_URL . '/editor/listar' . $destino);
        exit();
    }
}