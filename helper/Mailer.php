<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private $mail;

    public function __construct()
    {
        require_once dirname(__DIR__) . '/vendor/phpmailer/src/PHPMailer.php';
        require_once dirname(__DIR__) . '/vendor/phpmailer/src/SMTP.php';
        require_once dirname(__DIR__) . '/vendor/phpmailer/src/Exception.php';

        //se instancia el mailer con true para habilitar las excepciones
        $this->mail = new PHPMailer(true);

        //configuraciones de server
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com'; //voy a usar gmail para probar - otra opcion es mailtrap
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'santiagojavier1903@gmail.com'; //la direccion a la que va a enviar el mail
        $this->mail->Password = 'iwdommorqqjudfkn'; //contraseña de la aplicacion de esta cuenta^
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port = 587; //puerto de gmail por default

        $this->mail->setFrom('no-reply@cerebrito.com', 'Cerebrito');
        $this->mail->isHTML(true);
    }

    public function enviarMail($destinatario, $asunto, $mensaje){
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($destinatario);
            $this->mail->Subject = $asunto;
            $this->mail->Body = $mensaje;
            $this->mail->send();

            return true;

        } catch (Exception $e) {
            //ver como manejar las excepciones
            return false;
        }
    }
}