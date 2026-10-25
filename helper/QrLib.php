<?php 

require_once __DIR__ . '/../vendor/phpqrcode/qrlib.php';

class QrLib
{
    public static function generarQr($data, $filename)
    {
        // Genera código QR formato .png con $data y lo guarda en $filename
        QRcode::png($data, $filename, QR_ECLEVEL_L, 8);
    }
}