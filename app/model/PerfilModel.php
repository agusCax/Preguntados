<?php

namespace app\model;

use Log;
use QrLib;

class PerfilModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerDatosUsuario($userId) {

        $usuario = $this->db->queryOne(
            "SELECT nombre_usuario, nombre_1, email, pais, ciudad, bio, latitud, longitud, foto_perfil FROM usuarios WHERE id = :id",
            [':id' => $userId]
        );

        if ($usuario === null) {
            return null;
        }

        $nombre = $usuario['nombre_1'] ?? '';
        $nombreUsuario = $usuario['nombre_usuario'] ?? '';
        $emailUsuario = $usuario['email'] ?? '';
        $paisUsuario = $usuario['pais'] ?? '';
        $ciudadUsuario = $usuario['ciudad'] ?? '';
        $bioUsuario = $usuario['bio'] ?? '';
        $latitud = $usuario['latitud'] ?? null;
        $longitud = $usuario['longitud'] ?? null;
        $fotoPerfil = $usuario['foto_perfil'] ?? '';

        $especialidad = $this->obtenerEspecialidad($userId);
        $desafiante = $this->obtenerDesafiante($userId);

        return [
            'nombre' => $nombre,
            'usuario' => $nombreUsuario,
            // Letra del avatar: primera letra del nombre (o del usuario si no hay nombre)
            'inicial' => mb_strtoupper(mb_substr($nombre !== '' ? $nombre : $nombreUsuario, 0, 1)),
            'foto' => $fotoPerfil,
            'email' => $emailUsuario,
            'ubicacion' => $paisUsuario,
            'ciudad' => $ciudadUsuario,
            'latitud' => $latitud,
            'longitud' => $longitud,
            'bio' => $bioUsuario,
            'nivel' => 17,
            'preguntasCorrectas' => 174,
            'especialidad' => $especialidad['nombre'] ?? 'Sin datos',
            'especialidadAciertos' => (int) ($especialidad['aciertos'] ?? 0),
            'especialidadIcono' => $especialidad['icono'] ?? '❓',
            'desafiante' => $desafiante['nombre'] ?? 'Sin datos',
            'desafianteAciertos' => (int) ($desafiante['aciertos'] ?? 0),
            'desafianteIcono' => $desafiante['icono'] ?? '❓',
            'partidasGanadas' => 3,
            'partidasPerdidas' => 9,
            'partidasRendidas' => 0,
            'logrosObtenidos' => 0,
            'totalLogros' => 47
        ];
    }

    // Aciertos del jugador por categoría, de mayor a menor % de aciertos
    private function obtenerAciertosPorCategoria($userId) {
        return $this->db->queryAll(
            "SELECT c.nombre, c.icono,
                    COUNT(*) AS respondidas,
                    ROUND(SUM(pp.fue_correcta) * 100 / COUNT(*)) AS aciertos
             FROM partida_preguntas pp
             JOIN partidas pa ON pa.id = pp.partida_id
             JOIN preguntas q ON q.id = pp.pregunta_id
             JOIN categorias c ON c.id = q.categoria_id
             WHERE pa.usuario_id = :id AND pp.respondida_en IS NOT NULL
             GROUP BY c.id, c.nombre, c.icono
             HAVING COUNT(*) >= 3
             ORDER BY aciertos DESC, respondidas DESC, c.nombre ASC",
            [':id' => $userId]
        );
    }

    // La categoría donde más acierta (null si todavía no respondió nada)
    public function obtenerEspecialidad($userId) {
        $categorias = $this->obtenerAciertosPorCategoria($userId);
        return $categorias[0] ?? null;
    }

    // La categoría donde menos acierta (null si jugó menos de 2 categorías)
    public function obtenerDesafiante($userId) {
        $categorias = $this->obtenerAciertosPorCategoria($userId);

        if (count($categorias) < 2) {
            return null;
        }

        return $categorias[count($categorias) - 1];
    }


    public function actualizarPerfil($userId, $datos) {

        $this->db->execute(
            "UPDATE usuarios 
                SET nombre_usuario = :nombreUsuario,
                    nombre_1 = :nombreNuevo,
                        pais = :paisNuevo,
                        bio = :bioNuevo
                        WHERE id = :id",
            [
            ':nombreUsuario' => $datos['usuario'] ?? '',
                   ':nombreNuevo' => $datos['nombre'] ?? '',
                         ':paisNuevo' => $datos['ubicacion'] ?? '',
                                 ':bioNuevo' => $datos['bio'] ?? '',
                                      ':id' => $userId
                                                                        ]
                                                                    );
                                                                    return true;
                                                                }

    public function guardarFotoPerfil($userId, $ruta) {
        return $this->db->execute(
            "UPDATE usuarios 
                    SET foto_perfil = :foto 
                        WHERE id = :id",
                             [':foto' => $ruta, ':id' => $userId]
                                                                    );
                                                                }

    public function obtenerRutaImagenQr($userId){

        $qrDir = dirname(__DIR__, 2) . '/public/qr/'; //Busca ubicación donde se guardará el código QR
        if (!is_dir($qrDir) && !mkdir($qrDir, 0755, true) && !is_dir($qrDir)) { //De no encontrarla, intenta crear la carpeta
            Log::error("No se pudo crear la carpeta para los códigos QR.");
            return "";
        }

        $data = APP_URL . BASE_URL . '/perfil/showUsuario?usuario_id=' . $userId;
        $filename = $qrDir . 'usuario_id_' . $userId . '.png'; 

        QrLib::generarQr($data, $filename); 

        $urlQr = BASE_URL . '/public/qr/' . 'usuario_id_' . $userId . '.png';

        return $urlQr; 
    }
}