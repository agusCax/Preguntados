<?php
use app\Configuration;

session_start();
define('BASE_URL', '/proyectoWeb');
define('APP_URL', 'https://preguntados.infinityfree.io');
require_once __DIR__ . "/app/Configuration.php";

$configuration = new Configuration();
$router = $configuration->getRouter();
$accessControl = $configuration->getAccessControl();

$controlador = Request::get("controller", "inicio");
$metodo = Request::get("method", "show");

$router->dispatch($controlador, $metodo);