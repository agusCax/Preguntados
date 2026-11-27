<?php
// Se usan nombres con prefijo "hd" para no pisar variables de MustacheRender::renderiza().

use app\enum\Rol;

$hdSeccion = $_GET['controller'] ?? 'inicio';
$hdMetodo = $_GET['method'] ?? 'show';
$hdUsuario = isset($_SESSION['usuario'])
        ? htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8')
        : null;
$hdRol = isset($_SESSION['rol'])
        ? htmlspecialchars($_SESSION['rol'], ENT_QUOTES, 'UTF-8')
        : null;

//EDITOR
$hdMetodosBanco = [
        'listarBanco', 'nuevaPregunta', 'guardarNuevaPregunta', 'detallePregunta',
        'cambiarEstado', 'obtenerPreguntaAModificar', 'modificarPregunta',
];
$hdEditorEnBanco = $hdSeccion === 'editor' && in_array($hdMetodo, $hdMetodosBanco, true);
$hdMetodosCategorias = [
        'listarCategorias', 'nuevaCategoria', 'guardarNuevaCategoria',
        'editarCategoria', 'guardarCategoria', 'cambiarEstadoCategoria',
];
$hdEditorEnCategorias = $hdSeccion === 'editor' && in_array($hdMetodo, $hdMetodosCategorias, true);
$hdEditorEnModeracion = $hdSeccion === 'editor' && !$hdEditorEnBanco && !$hdEditorEnCategorias;

//ADMINISTRADOR
$hdAdminEnUsuarios = $hdSeccion === 'administrador' && in_array($hdMetodo, ['usuarios', 'cambiarRol'], true);
$hdAdminEnReporte = $hdSeccion === 'reporte';
$hdAdminEnPanel = $hdSeccion === 'administrador' && !$hdAdminEnUsuarios;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cerebrito</title>

    <!-- w3.css se mantiene mientras inicioView y footer.php usen sus clases -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

    <!-- Fuentes: Outfit (marca), Fredoka (títulos), Nunito (texto) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@600;700&family=Nunito:wght@600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* =========================================================
           TOKENS (se suman los del estilo cartoon: contorno y fondo cálido)
           ========================================================= */
        :root {
            --color-primary: #0284c7;
            --color-primary-dark: #0369a1;
            --color-ink: #0f172a;
            --color-ink-soft: #334155;
            --color-canvas: #f8fafc;
            --color-white: #ffffff;
            --color-border: #e2e8f0;
            --color-shape: #f1f5ff;

            --color-cream: #fff8e8;
            --color-sky-soft: #bfe6fb;
            --color-sun: #fcd34d;
            --color-amber: #f59e0b;
            --edge: 3px solid var(--color-ink);

            --font-family: "Outfit", system-ui, -apple-system, "Segoe UI", sans-serif;
            --font-display: "Fredoka", "Outfit", system-ui, sans-serif;
            --font-text: "Nunito", "Outfit", system-ui, sans-serif;

            --radius-full: 9999px;
        }

        /* Fondo cálido para todas las pantallas con sesión */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-height: 100dvh;
            margin: 0;
            font-family: var(--font-text);
            background-color: var(--color-cream);
        }

        /* Envuelve la vista (se abre en header.php y se cierra en footer.php) */
        .page-body {
            flex: 1 0 auto;
            padding-bottom: 2.5rem;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            margin: -1px;
            padding: 0;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* =========================================================
           FONDO DECORATIVO (sutil, detrás de todo el contenido)
           ========================================================= */
        .bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
        }

        .bg > * { position: absolute; }

        .bg__blob {
            aspect-ratio: 1;
            border-radius: 58% 42% 55% 45% / 50% 56% 44% 50%;
            animation: bg-drift 16s ease-in-out infinite alternate;
        }
        .bg__blob--sky    { width: clamp(220px, 34vw, 460px); left: -9%;  bottom: -12%; background: var(--color-sky-soft); opacity: 0.5; }
        .bg__blob--sun    { width: clamp(170px, 24vw, 320px); right: -6%; top: 16%;     background: #fde68a; opacity: 0.5; animation-delay: -6s; }
        .bg__blob--violet { width: clamp(120px, 15vw, 210px); left: 5%;   top: 26%;     background: #ddd6fe; opacity: 0.45; animation-delay: -11s; }

        .bg__dots {
            width: 120px;
            height: 88px;
            background-image: radial-gradient(var(--color-ink) 2px, transparent 2.6px);
            background-size: 16px 16px;
            opacity: 0.1;
        }
        .bg__dots--a { left: 3%;  top: 62%; }
        .bg__dots--b { right: 4%; bottom: 10%; }

        .bg__star { width: 34px; opacity: 0.16; }
        .bg__star--a { left: 11%;  top: 14%; }
        .bg__star--b { right: 8%;  top: 48%; width: 28px; }
        .bg__star--c { left: 17%;  bottom: 18%; width: 24px; }

        .bg__zig { width: 92px; opacity: 0.22; }
        .bg__zig--a { right: 13%; top: 11%; }
        .bg__zig--b { left: 4%;   top: 46%; }

        @keyframes bg-drift {
            from { transform: translateY(0); }
            to   { transform: translateY(-14px); }
        }

        /* En pantallas chicas solo quedan las manchas y una estrella */
        @media (max-width: 767px) {
            .bg__dots, .bg__zig, .bg__star--b, .bg__star--c, .bg__blob--violet { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            .bg__blob { animation: none; }
        }

        /* Impresión: sin encabezado, pie ni decoración (los reportes salen limpios) */
        @media print {
            .hd, .bg, .ft { display: none !important; }
            body { background: #fff; }
        }

        /* =========================================================
           HEADER (mobile first)
           Mobile:  fila 1 = marca + perfil + salir | fila 2 = pestañas
           ========================================================= */
        .hd,
        .hd *,
        .hd *::before,
        .hd *::after {
            box-sizing: border-box;
        }

        .hd {
            --hd-pad: 1rem;

            position: sticky;
            top: 0;
            z-index: 50;
            padding-top: env(safe-area-inset-top, 0px);
            background-color: var(--color-white);
            border-bottom: var(--edge);
            box-shadow: 0 4px 0 rgba(15, 23, 42, 0.14);
            font-family: var(--font-text);
        }

        .hd a { text-decoration: none; }

        .hd__bar {
            display: grid;
            grid-template-columns: 1fr auto;
            grid-template-areas:
                "brand user"
                "nav   nav";
            align-items: center;
            gap: 0.75rem 1rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0.75rem var(--hd-pad) 0.85rem;
        }

        /* Marca: logo "?" tipo sticker + nombre */
        .hd__brand {
            grid-area: brand;
            justify-self: start;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            font-family: var(--font-display);
            font-size: 28px;
            line-height: 1;
            font-weight: 700;
            color: var(--color-primary-dark);
            text-shadow: 2px 2px 0 var(--color-sky-soft);
        }

        .hd__logo {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            font-size: 21px;
            color: var(--color-ink);
            text-shadow: none;
            background-color: var(--color-sun);
            border: 2.5px solid var(--color-ink);
            border-radius: 50%;
            box-shadow: 0 3px 0 var(--color-ink);
            transform: rotate(-8deg);
            transition: transform 0.2s ease;
        }
        .hd__brand:hover .hd__logo { transform: rotate(8deg) scale(1.06); }

        /* Perfil + salir */
        .hd__user {
            grid-area: user;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .hd__avatar,
        .hd__logout {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            color: var(--color-ink);
            border: 2.5px solid var(--color-ink);
            border-radius: 50%;
            box-shadow: 0 3px 0 var(--color-ink);
            transition: transform 0.1s ease, box-shadow 0.1s ease, background-color 0.15s ease;
        }

        .hd__avatar { background-color: var(--color-sun); }

        .hd__logout {
            gap: 0.5rem;
            font-family: var(--font-text);
            font-size: 0.95rem;
            font-weight: 800;
            background-color: var(--color-white);
            border-radius: var(--radius-full);
        }

        .hd__avatar:hover,
        .hd__logout:hover { transform: translateY(-1px); box-shadow: 0 4px 0 var(--color-ink); }
        .hd__logout:hover { background-color: var(--color-sky-soft); }

        .hd__avatar:active,
        .hd__logout:active { transform: translateY(3px); box-shadow: 0 0 0 var(--color-ink); }

        /* En mobile el botón es solo el ícono (el link lleva aria-label="Salir") */
        .hd__logout-text { display: none; }

        /* Pestañas */
        .hd__nav {
            grid-area: nav;
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
        }

        .hd__link {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 1.25rem;
            white-space: nowrap;
            font-family: var(--font-text);
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--color-ink);
            background-color: var(--color-white);
            border: 2.5px solid var(--color-ink);
            border-radius: var(--radius-full);
            box-shadow: 0 3px 0 var(--color-ink);
            transition: transform 0.1s ease, box-shadow 0.1s ease, background-color 0.15s ease;
        }

        .hd__link:hover { background-color: var(--color-sky-soft); transform: translateY(-1px); box-shadow: 0 4px 0 var(--color-ink); }
        .hd__link:active { transform: translateY(3px); box-shadow: 0 0 0 var(--color-ink); }

        /* Página actual */
        .hd__link.is-active {
            color: var(--color-white);
            background-color: var(--color-primary);
        }

        .hd a:focus-visible {
            outline: 3px solid var(--color-primary);
            outline-offset: 3px;
        }

        @media (prefers-reduced-motion: reduce) {
            .hd * { transition: none !important; }
        }

        /* =========================================================
           TABLET Y DESKTOP (≥ 768px): todo en una sola fila
           ========================================================= */
        @media (min-width: 768px) {
            .hd { --hd-pad: 1.5rem; }

            .hd__bar {
                grid-template-columns: 1fr auto 1fr;
                grid-template-areas: "brand nav user";
                padding-block: 0.85rem;
            }

            .hd__brand { font-size: 32px; }
            .hd__nav { justify-self: center; }
            .hd__link { flex: none; }
            .hd__user { justify-self: end; }

            .hd__logout { width: auto; padding: 0 1.1rem; }
            .hd__logout-text { display: inline; }
        }

        @media (min-width: 1024px) {
            .hd { --hd-pad: 2.5rem; }
        }
    </style>
</head>
<body>

<!-- Decoración de fondo: no es contenido, los lectores de pantalla la ignoran -->
<div class="bg" aria-hidden="true">
    <span class="bg__blob bg__blob--sky"></span>
    <span class="bg__blob bg__blob--sun"></span>
    <span class="bg__blob bg__blob--violet"></span>
    <span class="bg__dots bg__dots--a"></span>
    <span class="bg__dots bg__dots--b"></span>
    <svg class="bg__star bg__star--a" viewBox="0 0 40 40"><path d="M20 2 L25 15 L38 20 L25 25 L20 38 L15 25 L2 20 L15 15 Z" fill="#fff" stroke="#0f172a" stroke-width="2.5" stroke-linejoin="round"/></svg>
    <svg class="bg__star bg__star--b" viewBox="0 0 40 40"><path d="M20 2 L25 15 L38 20 L25 25 L20 38 L15 25 L2 20 L15 15 Z" fill="#fff" stroke="#0f172a" stroke-width="2.5" stroke-linejoin="round"/></svg>
    <svg class="bg__star bg__star--c" viewBox="0 0 40 40"><path d="M20 2 L25 15 L38 20 L25 25 L20 38 L15 25 L2 20 L15 15 Z" fill="#fff" stroke="#0f172a" stroke-width="2.5" stroke-linejoin="round"/></svg>
    <svg class="bg__zig bg__zig--a" viewBox="0 0 80 20"><polyline points="2,16 14,4 26,16 38,4 50,16 62,4 78,16" fill="none" stroke="#7c3aed" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <svg class="bg__zig bg__zig--b" viewBox="0 0 80 20"><polyline points="2,16 14,4 26,16 38,4 50,16 62,4 78,16" fill="none" stroke="#0284c7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
</div>

<header class="hd">
    <div class="hd__bar">

        <a href="<?= BASE_URL ?>/inicio/show" class="hd__brand">
            <span class="hd__logo" aria-hidden="true">?</span>
            Cerebrito
        </a>

        <nav class="hd__nav" aria-label="Navegación principal">

            <?php if ($hdRol === Rol::Jugador->name): ?>
                <a href="<?= BASE_URL ?>/inicio/show" class="hd__link<?= $hdSeccion === 'inicio' ? ' is-active' : '' ?>" <?= $hdSeccion === 'inicio' ? ' aria-current="page"' : '' ?>>Inicio</a>
                <a href="<?= BASE_URL ?>/ranking/show" class="hd__link<?= $hdSeccion === 'ranking' ? ' is-active' : '' ?>" <?= $hdSeccion === 'ranking' ? ' aria-current="page"' : '' ?>>Ranking</a>

            <?php elseif ($hdRol === Rol::Editor->name): ?>
                <a href="<?= BASE_URL ?>/editor/show" class="hd__link<?= $hdEditorEnModeracion ? ' is-active' : '' ?>" <?= $hdEditorEnModeracion ? ' aria-current="page"' : '' ?>>Moderacion</a>
                <a href="<?= BASE_URL ?>/editor/listarBanco" class="hd__link<?= $hdEditorEnBanco ? ' is-active' : '' ?>" <?= $hdEditorEnBanco ? ' aria-current="page"' : '' ?>>Banco de preguntas</a>
                <a href="<?= BASE_URL ?>/editor/listarCategorias" class="hd__link<?= $hdEditorEnCategorias ? ' is-active' : '' ?>" <?= $hdEditorEnCategorias ? ' aria-current="page"' : '' ?>>Categorías</a>

            <?php elseif ($hdRol === Rol::Administrador->name): ?>
                <a href="<?= BASE_URL ?>/administrador/show" class="hd__link<?= $hdAdminEnPanel ? ' is-active' : '' ?>" <?= $hdAdminEnPanel ? ' aria-current="page"' : '' ?>>Panel</a>
                <a href="<?= BASE_URL ?>/administrador/usuarios" class="hd__link<?= $hdAdminEnUsuarios ? ' is-active' : '' ?>" <?= $hdAdminEnUsuarios ? ' aria-current="page"' : '' ?>>Usuarios</a>
                <a href="<?= BASE_URL ?>/reporte/ranking" class="hd__link<?= $hdAdminEnReporte ? ' is-active' : '' ?>" <?= $hdAdminEnReporte ? ' aria-current="page"' : '' ?>>Reporte de ranking</a>
            <?php endif; ?>
        </nav>

        <?php if ($hdUsuario !== null): ?>
            <div class="hd__user">
                <!-- Avatar hacia el perfil -->
                <a href="<?= BASE_URL ?>/perfil/show" class="hd__avatar" title="Perfil de @<?= $hdUsuario ?>">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    <span class="sr-only">Perfil de @<?= $hdUsuario ?></span>
                </a>

                <a href="<?= BASE_URL ?>/login/logout" class="hd__logout" aria-label="Salir">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                    <span class="hd__logout-text" aria-hidden="true">Salir</span>
                </a>
            </div>
        <?php endif; ?>

    </div>
</header>

<div class="page-body">