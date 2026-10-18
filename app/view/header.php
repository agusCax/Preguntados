<?php
// Se usan nombres con prefijo "hd" para no pisar variables de MustacheRender::renderiza().
//De momento nuestro header tendra unicamente esto para esta entrega
$hdSeccion = $_GET['controller'] ?? 'inicio';
if ($hdSeccion !== 'ranking') {
    $hdSeccion = 'inicio';
}
$hdUsuario = isset($_SESSION['usuario'])
        ? htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8')
        : null;
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
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@600;700&family=Nunito:wght@600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* =========================================================
           TOKENS (los mismos que usamos en bienvenida, login y registro)
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

            --font-family: "Outfit", system-ui, -apple-system, "Segoe UI", sans-serif;
            --font-display: "Fredoka", "Outfit", system-ui, sans-serif;
            --font-text: "Nunito", "Outfit", system-ui, sans-serif;

            --radius-full: 9999px;
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
            background-color: rgba(255, 255, 255, 0.92);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--color-border);
            font-family: var(--font-text);
        }

        .hd a {
            text-decoration: none;
        }

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
            padding: 0.75rem var(--hd-pad);
        }

        /* Marca */
        .hd__brand {
            grid-area: brand;
            justify-self: start;
            font-family: var(--font-family);
            font-size: 26px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.01em;
            color: var(--color-primary-dark);
        }

        /* Perfil + salir */
        .hd__user {
            grid-area: user;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .hd__avatar {
            display: grid;
            place-items: center;
            width: 40px;
            height: 40px;
            color: var(--color-primary);
            background-color: #e0f2fe;
            border: 2px solid var(--color-primary);
            border-radius: 50%;
        }

        .hd__logout {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 40px;
            height: 40px;
            font-family: var(--font-text);
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--color-ink-soft);
            background-color: var(--color-white);
            border: 2px solid var(--color-border);
            border-radius: var(--radius-full);
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
        }

        .hd__logout:hover {
            color: var(--color-primary-dark);
            background-color: var(--color-shape);
            border-color: var(--color-primary);
        }

        /* En mobile el botón es solo el ícono (el link lleva aria-label="Salir") */
        .hd__logout-text {
            display: none;
        }

        /* Pestañas */
        .hd__nav {
            grid-area: nav;
            display: flex;
            gap: 0.5rem;
        }

        .hd__link {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 1.25rem;
            font-family: var(--font-text);
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--color-ink-soft);
            background-color: var(--color-shape);
            border-radius: var(--radius-full);
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .hd__link:hover {
            color: var(--color-primary-dark);
            background-color: #e4ecfd;
        }

        /* Página actual: igual que el botón primario */
        .hd__link.is-active {
            color: var(--color-white);
            background-color: var(--color-primary);
            box-shadow: 0 3px 0 var(--color-primary-dark);
        }

        /* Foco de teclado */
        .hd a:focus-visible {
            outline: 3px solid rgba(2, 132, 199, 0.4);
            outline-offset: 3px;
        }

        @media (prefers-reduced-motion: reduce) {
            .hd * {
                transition: none !important;
            }
        }

        /* =========================================================
           TABLET Y DESKTOP (≥ 768px): todo en una sola fila
           ========================================================= */
        @media (min-width: 768px) {
            .hd {
                --hd-pad: 1.5rem;
            }

            .hd__bar {
                grid-template-columns: 1fr auto 1fr;
                grid-template-areas: "brand nav user";
                padding-block: 0.85rem;
            }

            .hd__brand {
                font-size: 30px;
            }

            .hd__nav {
                justify-self: center;
            }

            .hd__link {
                flex: none;
            }

            .hd__user {
                justify-self: end;
            }

            .hd__logout {
                width: auto;
                padding: 0 1rem;
                border-radius: var(--radius-full);
            }

            .hd__logout-text {
                display: inline;
            }
        }

        @media (min-width: 1024px) {
            .hd {
                --hd-pad: 2.5rem;
            }
        }

        /* Base de las pantallas con sesión */
        body {
            margin: 0;
            font-family: var(--font-text);
        }
    </style>
</head>
<body>

<header class="hd">
    <div class="hd__bar">

        <a href="<?= BASE_URL ?>/inicio/show" class="hd__brand">Cerebrito</a>

        <nav class="hd__nav" aria-label="Navegación principal">
            <a href="<?= BASE_URL ?>/inicio/show"
               class="hd__link<?= $hdSeccion === 'inicio' ? ' is-active' : '' ?>"<?= $hdSeccion === 'inicio' ? ' aria-current="page"' : '' ?>>Inicio</a>
            <a href="<?= BASE_URL ?>/ranking/show"
               class="hd__link<?= $hdSeccion === 'ranking' ? ' is-active' : '' ?>"<?= $hdSeccion === 'ranking' ? ' aria-current="page"' : '' ?>>Ranking</a>
        </nav>

        <?php if ($hdUsuario !== null): ?>
            <div class="hd__user">
                <!-- Por ahora es solo un círculo que representa la foto de perfil, luego lo modificaremos mejor -->
                <span class="hd__avatar" title="@<?= $hdUsuario ?>">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    <span class="sr-only">Perfil de @<?= $hdUsuario ?></span>
                </span>

                <a href="<?= BASE_URL ?>/login/logout" class="hd__logout" aria-label="Salir">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
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