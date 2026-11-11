<?php

require_once __DIR__ . '/../vendor/cpchart/constants.php';

// CpChart usa namespaces (CpChart\Data, CpChart\Image, ...): sus clases se cargan desde vendor/cpchart/src
spl_autoload_register(function ($clase) {
    $prefijo = 'CpChart\\';

    if (strpos($clase, $prefijo) === 0) {
        $archivo = __DIR__ . '/../vendor/cpchart/src/' . str_replace('\\', '/', substr($clase, strlen($prefijo))) . '.php';

        if (file_exists($archivo)) {
            require_once $archivo;
        }
    }
});

use CpChart\Data;
use CpChart\Image;

class CpChartLib
{
    // Dibuja un gráfico de barras y lo envía al navegador como imagen PNG
    public static function graficoBarras($titulo, array $etiquetas, array $valores)
    {
        $ancho = 700;
        $alto = 400;

        // Sin datos no se puede dibujar el gráfico: se muestra una imagen con un aviso
        if (count($valores) === 0) {
            self::imagenSinDatos($ancho, $alto);
            return;
        }

        // Los datos: una serie con los valores y otra con las etiquetas del eje X
        $datos = new Data();
        $datos->addPoints($valores, 'Valores');
        $datos->setSerieDescription('Valores', $titulo);
        $datos->setPalette('Valores', ['R' => 2, 'G' => 132, 'B' => 199]);
        $datos->addPoints($etiquetas, 'Etiquetas');
        $datos->setAbscissa('Etiquetas');

        // La imagen: fondo blanco, título, ejes y barras
        $imagen = new Image($ancho, $alto, $datos);
        $imagen->drawFilledRectangle(0, 0, $ancho, $alto, ['R' => 255, 'G' => 255, 'B' => 255]);
        $imagen->setFontProperties(['FontName' => 'verdana.ttf', 'FontSize' => 10]);
        $imagen->drawText(20, 28, $titulo, ['R' => 15, 'G' => 23, 'B' => 42, 'FontSize' => 14]);
        $imagen->setGraphArea(60, 50, $ancho - 20, $alto - 50);
        $imagen->drawScale(['Mode' => SCALE_MODE_START0, 'DrawSubTicks' => false]);
        $imagen->drawBarChart(['DisplayValues' => true]);

        // Envía el PNG al navegador
        $imagen->stroke();
    }

    private static function imagenSinDatos($ancho, $alto)
    {
        $imagen = imagecreatetruecolor($ancho, $alto);
        imagefill($imagen, 0, 0, imagecolorallocate($imagen, 255, 255, 255));
        imagestring($imagen, 5, 20, 20, 'Sin datos para este periodo', imagecolorallocate($imagen, 100, 116, 139));

        header('Content-Type: image/png');
        imagepng($imagen);
    }
}
