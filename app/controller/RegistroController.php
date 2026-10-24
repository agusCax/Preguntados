<?php

namespace app\controller;

use Log;
use PDOException;
use PHPMailer\PHPMailer\PHPMailer;
use Redirect;
use Request;
use Mailer;

class RegistroController
{
    // Foto de perfil: tamaño máximo y carpeta donde se guarda
    private const FOTO_MAX_BYTES = 2 * 1024 * 1024;
    private const FOTO_CARPETA = 'public/uploads/perfiles';

    private $model;
    private $render;

    private $mailer;

    public function __construct($model, $render, $mailer)
    {
        $this->model = $model;
        $this->render = $render;
        $this->mailer = $mailer;
    }

    public function show()
    {
        //$codigo = Request::get('registro_error');
        $codigo = $_GET['registro_error'] ?? null;
        $data["error"] = $codigo !== null ? $this->mensajeError($codigo) : "";

        // Años para el desplegable de año de nacimiento (de más nuevo a más viejo)
        $anioActual = (int) date('Y');
        $data["anios"] = range($anioActual - 10, $anioActual - 100);

        $this->render->renderiza("registro", $data, false);
    }

    public function registrar()
    {
        $password = Request::post('password');

        $this->validarContraseniaVacia($password);

        $this->validarLongitudContrasenia($password);

        $this->validarRepeticionDeContrasenia($password);

        $latitud = Request::post('latitud');
        $longitud = Request::post('longitud');

        // Se valida antes de guardar la foto, para no dejar archivos sueltos si falla
        $this->validarUbicacion(Request::post('pais'), Request::post('ciudad'), $latitud, $longitud);

        $foto = $this->guardarFoto();

        $this->validarSubidaDeFoto($foto);

        $token= md5(uniqid(rand(), true));


        $datos = [
            'nombre_usuario' => Request::post('nombre_usuario'),
            'password' => $password,
            'nombre_1' => Request::post('nombre_1'),
            'nombre_2' => Request::post('nombre_2') ?: null,
            'apellido_1' => Request::post('apellido_1'),
            'apellido_2' => Request::post('apellido_2') ?: null,
            'anio_nacimiento' => Request::post('anio_nacimiento'),
            'sexo' => Request::post('sexo'),
            'pais' => Request::post('pais'),
            'ciudad' => Request::post('ciudad'),
            'latitud' => $latitud,
            'longitud' => $longitud,
            'email' => Request::post('email'),
            'token_validacion' => $token,
            'foto_perfil' => $foto
        ];

        try {
            $registroExitoso = $this->model->registrar($datos);

            if ($registroExitoso === 1){
                $link = "http://localhost/proyectoWeb/registro/validarCuenta?token=" . $token;
                $cuerpo = "
<html lang='es'>
<head>
    <meta charset='UTF-8'>
</head>
<body style='margin: 0; padding: 0; background-color: #f8fafc; font-family: \"Outfit\", \"Nunito\", Arial, sans-serif;'>
    <table width='100%' cellpadding='0' cellspacing='0' style='padding: 40px 20px;'>
        <tr>
            <td align='center'>
                <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 520px; background-color: #ffffff; border: 2px solid #e2e8f0; border-radius: 24px; overflow: hidden; box-shadow: 0 12px 24px rgba(3, 105, 161, 0.08);'>
                    
                    <!-- Encabezado -->
                    <tr>
                        <td style='background-color: #0284c7; padding: 35px 20px; text-align: center;'>
                            <h1 style='color: #ffffff; margin: 0; font-size: 34px; font-weight: 800; letter-spacing: -1px;'>Cerebrito</h1>
                        </td>
                    </tr>

                    <!-- Cuerpo principal (Con Entidades HTML para acentos y emojis) -->
                    <tr>
                        <td style='padding: 40px 30px; text-align: center;'>
                            <h2 style='color: #0f172a; font-size: 24px; font-weight: 800; margin: 0 0 15px 0;'>&iexcl;Tu cuenta casi est&aacute; lista!</h2>
                            
                            <p style='font-size: 16px; color: #334155; line-height: 24px; margin: 0 0 25px 0; font-weight: 600;'>
                                Est&aacute;s a un solo clic de unirte a nuestra comunidad. Demostr&aacute; tus conocimientos, respond&eacute; r&aacute;pido y escal&aacute; en el ranking general.
                            </p>
                            
                            <!-- Caja destacada -->
                            <div style='background-color: #f1f5ff; border-radius: 16px; padding: 20px; margin-bottom: 35px; text-align: left; border: 1px solid #e0f2fe;'>
                                <p style='margin: 0 0 10px 0; color: #0369a1; font-weight: 800; font-size: 15px;'>&iquest;Qu&eacute; te espera adentro?</p>
                                <p style='margin: 0; color: #334155; font-size: 15px; line-height: 26px; font-weight: 600;'>
                                    &#128300; Ciencia &nbsp;|&nbsp; &#127963; Historia &nbsp;|&nbsp; &#127758; Geograf&iacute;a <br>
                                    &#127912; Arte &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp; &#9917; Deportes &nbsp;|&nbsp; &#127916; Cine
                                </p>
                            </div>
                            
                            <a href='{$link}' style='display: inline-block; background-color: #10b981; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 800; padding: 16px 36px; border-radius: 50px; border-bottom: 4px solid #047857; text-transform: uppercase;'>Activar mi cuenta</a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style='background-color: #f8fafc; padding: 25px 20px; text-align: center; border-top: 1px solid #e2e8f0;'>
                            <p style='margin: 0; color: #64748b; font-size: 13px; font-weight: 600; line-height: 20px;'>
                                Si no te registraste en Cerebrito, pod&eacute;s ignorar este correo sin problemas.<br>
                                &copy; " . date('Y') . " Cerebrito. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>";

                $this->mailer->enviarMail($datos['email'] ,"Activa tu cuenta de Cerebrito", $cuerpo);

                Redirect::to(BASE_URL . '/login/show?activacion=revisa_tu_mail');
            }
        } catch (PDOException $e) {
            Log::error($e->getMessage());
            $this->borrarFoto($foto);

            // 23000 = violación de UNIQUE (nombre de usuario o email repetidos)
            $codigo = $e->getCode() === '23000' ? 'duplicado' : '1';
            Redirect::to(BASE_URL . '/registro/show?registro_error=' . $codigo);
        }

        if ($registroExitoso !== 1) {
            $this->borrarFoto($foto);
            Redirect::to(BASE_URL . '/registro/show?registro_error=1');
        }

        Redirect::toIndex();
    }

    public function validarCuenta()
    {
        $token = Request::get('token');

        if ($token) {
            $this->model->activarUsuario($token);
            Redirect::to(BASE_URL . '/login/show?activacion=cuenta_activada');
        } else {
            Redirect::toIndex();
        }
    }


    private function mensajeError($codigo)
    {
        switch ((string) $codigo) {
            case 'password':
                return "Las contraseñas no coinciden.";
            case 'password_length' :
                return "La contraseña debe tener al menos 8 caracteres.";
            case 'duplicado':
                return "El nombre de usuario o el email ya están registrados.";
            case 'ubicacion':
                return "Elegí tu ubicación tocando el mapa.";
            case 'foto':
                return "La foto de perfil no es válida. Usá una imagen JPG, PNG, WEBP o GIF de hasta 2 MB.";
            default:
                return "Error al registrar su usuario y/o contraseña, intente nuevamente.";
        }
    }

    // Devuelve la ruta relativa guardada, null si no se subió foto, o false si la foto no es válida
    private function guardarFoto()
    {
        if (!isset($_FILES['foto_perfil']) || $_FILES['foto_perfil']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $archivo = $_FILES['foto_perfil'];

        if ($archivo['error'] !== UPLOAD_ERR_OK || $archivo['size'] > self::FOTO_MAX_BYTES) {
            return false;
        }

        // getimagesize comprueba que el contenido sea realmente una imagen
        $info = @getimagesize($archivo['tmp_name']);
        $extensiones = [
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_WEBP => 'webp',
            IMAGETYPE_GIF => 'gif',
        ];

        if ($info === false || !isset($extensiones[$info[2]])) {
            return false;
        }

        $carpeta = dirname(__DIR__, 2) . '/' . self::FOTO_CARPETA;
        if (!is_dir($carpeta) && !mkdir($carpeta, 0755, true)) {
            return false;
        }

        // Nombre aleatorio: no se usa el nombre que mandó el usuario
        $nombre = bin2hex(random_bytes(16)) . '.' . $extensiones[$info[2]];

        if (!move_uploaded_file($archivo['tmp_name'], $carpeta . '/' . $nombre)) {
            return false;
        }

        return self::FOTO_CARPETA . '/' . $nombre;
    }

    private function borrarFoto($ruta)
    {
        if ($ruta) {
            $archivo = dirname(__DIR__, 2) . '/' . $ruta;
            if (is_file($archivo)) {
                unlink($archivo);
            }
        }
    }

    /**
     * @param mixed $password
     * @return void
     */
    public function validarContraseniaVacia(mixed $password): void
    {
        if ($password === null || $password === '') {
            Redirect::to(BASE_URL . '/registro/show?registro_error=1');
        }
    }

    /**
     * @param mixed $password
     * @return void
     */
    public function validarLongitudContrasenia(mixed $password): void
    {
        if (strlen($password) < 8) {
            Redirect::to(BASE_URL . '/registro/show?registro_error=password_length');
        }
    }

    /**
     * @param mixed $password
     * @return void
     */
    public function validarRepeticionDeContrasenia(mixed $password): void
    {
        if ($password !== Request::post('password_repetir')) {
            Redirect::to(BASE_URL . '/registro/show?registro_error=password');
        }
    }

    /**
     * @param false|string|null $foto
     * @return void
     */
    public function validarSubidaDeFoto(false|string|null $foto): void
    {
        if ($foto === false) {
            Redirect::to(BASE_URL . '/registro/show?registro_error=foto');
        }
    }

    /**
     * Comprueba que se haya elegido una ubicación en el mapa (país, ciudad y coordenadas válidas)
     */
    public function validarUbicacion($pais, $ciudad, $latitud, $longitud): void
    {
        $coordenadasValidas = is_numeric($latitud) && is_numeric($longitud)
            && $latitud >= -90 && $latitud <= 90
            && $longitud >= -180 && $longitud <= 180;

        if (!$pais || !$ciudad || !$coordenadasValidas) {
            Redirect::to(BASE_URL . '/registro/show?registro_error=ubicacion');
        }
    }
}
