<?php

namespace app;

use app\controller\InicioController;
use app\controller\LoginController;
use app\controller\RegistroController;
use app\model\InicioModel;
use app\controller\PreguntaController;
use app\model\PreguntaModel;
use app\model\LoginModel;
use app\model\RegistroModel;
use MustacheRender;
use MyDatabase;
use phpmailer\src\PHPMailer;
use Router;
use Mailer;

require_once __DIR__ . "/../helper/Autoloader.php";

class Configuration
{

    public function __construct()
    {
    }

    //Login
    public function getLoginController()
    {
        return new LoginController(
            $this->getLoginModel(),
            $this->getRender()
        );
    }

    public function getPreguntaController()
    {
        return new PreguntaController(
            $this->getPreguntaModel(),
            $this->getRender()
        );
    }

    private function getPreguntaModel()
    {
        return new PreguntaModel(
            $this->getDatabase()
        );
    }

    private function getLoginModel()
    {
        return new LoginModel(
            $this->getDatabase()
        );
    }

    // Registro
    public function getRegistroController()
    {
        return new RegistroController(
            $this->getRegistroModel(),
            $this->getRender(),
            $this->getMailer()
        );
    }

    public function getMailer(){
        $config = parse_ini_file(__DIR__ . "/../config/config.ini", true);

        return new Mailer($config["mailer"]);
    }

    private function getRegistroModel()
    {
        return new RegistroModel(
            $this->getDatabase()
        );
    }

    //Inicio
    public function getInicioController()
    {
        return new InicioController(
            $this->getInicioModel(),
            $this->getRender()
        );
    }

    private function getInicioModel()
    {
        return new InicioModel(
            $this->getDatabase()
        );
    }

    private function getDatabase()
    {
        $config = parse_ini_file(__DIR__ . "/../config/config.ini");

        return new MyDatabase($config["db_host"],
            $config["db_user"],
            $config["db_pass"],
            $config["db_name"]
        );
    }

    private function getRender()
    {
        return new MustacheRender();
    }

    public function getRouter()
    {
        return new Router($this, "inicio", "show");
    }
}
