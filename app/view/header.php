<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preguntados</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333333;
        }

        .preguntados-nav {
            background-color: #2c3e50;
        }

        .preguntados-nav a {
            color: #ffffff;
            font-weight: bold;
            padding: 14px 20px;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }

        .preguntados-nav a:hover,
        .preguntados-nav a:focus {
            background-color: #ff7b00;
            color: #ffffff;
        }

        .header-evento {
            background-color: #f8f9fa;
            border-bottom: 4px solid #2c3e50;
            color: #2c3e50;
            font-family: Arial, sans-serif;
            margin-bottom: 24px;
        }

        .header-evento h1 {
            color: #ff7b00;
            font-weight: 800;
            margin: 0 0 8px;
        }

        .header-evento h2 {
            color: #2c3e50;
            font-size: 1.25em;
            margin: 0;
        }
    </style>
</head>
<body>

<nav class="w3-bar preguntados-nav" aria-label="Navegación principal">
    <a href="<?= BASE_URL ?>/inicio/show" class="w3-bar-item w3-button">Inicio</a>
    <?php if (isset($_SESSION['usuario'])): ?>
        <a href="<?= BASE_URL ?>/login/logout" class="w3-bar-item w3-button w3-right">Salir</a>
    <?php endif; ?>
</nav>

<div class="w3-container header-evento w3-padding-32 w3-center">
    <h1>Preguntados</h1>
    <h2>Segunda etapa</h2>
</div>
