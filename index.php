<?php
use app\Configuration;

session_start();
define('BASE_URL', '/proyectoWeb');
require_once __DIR__ . "/app/Configuration.php";

$configuration = new Configuration();
$router = $configuration->getRouter();

$router->dispatch(
    Request::get("controller", "inicio"),
    Request::get("method", "show")
);