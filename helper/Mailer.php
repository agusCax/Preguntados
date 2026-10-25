<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{
    private $mail;

    public function __construct($config)
    {
        require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
        require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
        require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';

        //se instancia el mailer con true para habilitar las excepciones
        $this->mail = new PHPMailer(true);

        //configuraciones de server
        $this->mail->isSMTP();
        $this->mail->Host = $config['host'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $config['username'];
        $this->mail->Password = $config['password'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = $config['port'];
        $this->mail->CharSet = 'UTF-8';
        $this->mail->setFrom($config['username'], 'Cerebrito');
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
            Log::error("Fallo al enviar mail a {$destinatario} por el siguiente motivo: " . $e->getMessage());
            return false;
        }
    }
}