</div>

<footer class="ft">
    <style>
        .ft,
        .ft *,
        .ft *::before,
        .ft *::after {
            box-sizing: border-box;
        }

        .ft {
            --ft-pad: 1rem;

            flex-shrink: 0;
            margin-top: 0;
            padding: 2rem var(--ft-pad) 1.5rem;
            font-family: var(--font-text, "Nunito", system-ui, sans-serif);
            text-align: center;
            color: var(--color-ink, #0f172a);
            background-color: var(--color-sky-soft, #bfe6fb);
            border-top: var(--edge, 3px solid #0f172a);
            box-shadow: 0 -4px 0 rgba(15, 23, 42, 0.1);
        }

        .ft__inner {
            display: grid;
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .ft__brand {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            margin: 0 0 0.5rem;
            font-family: var(--font-display, "Fredoka", system-ui, sans-serif);
            font-size: 28px;
            line-height: 1;
            font-weight: 700;
            color: var(--color-primary-dark, #0369a1);
            text-shadow: 2px 2px 0 #fff;
        }

        .ft__logo {
            display: grid;
            place-items: center;
            width: 32px;
            height: 32px;
            font-size: 19px;
            color: var(--color-ink, #0f172a);
            text-shadow: none;
            background-color: var(--color-sun, #fcd34d);
            border: 2.5px solid var(--color-ink, #0f172a);
            border-radius: 50%;
            box-shadow: 0 3px 0 var(--color-ink, #0f172a);
            transform: rotate(-8deg);
        }

        .ft__desc {
            max-width: 36ch;
            margin: 0 auto;
            font-size: 0.95rem;
            line-height: 1.5;
            font-weight: 700;
            color: var(--color-ink-soft, #334155);
        }

        .ft__title {
            display: inline-block;
            margin: 0 0 0.9rem;
            padding: 0.2rem 0.9rem;
            font-family: var(--font-text, "Nunito", system-ui, sans-serif);
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--color-ink, #0f172a);
            background-color: var(--color-sun, #fcd34d);
            border: 2px solid var(--color-ink, #0f172a);
            border-radius: 9999px;
            box-shadow: 0 3px 0 var(--color-ink, #0f172a);
        }

        .ft__list {
            display: grid;
            gap: 0.6rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .ft__list li {
            padding: 0.4rem 0.9rem;
            font-size: 0.9rem;
            line-height: 1.2;
            font-weight: 700;
            color: var(--color-ink, #0f172a);
            background-color: #fff;
            border: 2px solid var(--color-ink, #0f172a);
            border-radius: 1rem;
            box-shadow: 0 3px 0 var(--color-ink, #0f172a);
        }

        .ft__copy {
            max-width: 1200px;
            margin: 1.5rem auto 0;
            padding-top: 1rem;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--color-ink-soft, #334155);
            border-top: 2px dashed rgba(15, 23, 42, 0.35);
        }

        /* Tablet y desktop: marca a la izquierda, integrantes a la derecha */
        @media (min-width: 768px) {
            .ft { --ft-pad: 1.5rem; }

            .ft__inner {
                grid-template-columns: 1fr 1fr;
                align-items: start;
                text-align: left;
            }

            .ft__desc { margin-inline: 0; }
            .ft__team { justify-self: end; }
            .ft__list { grid-template-columns: 1fr 1fr; gap: 0.7rem 0.8rem; }
            .ft__copy { text-align: left; }
        }

        @media (min-width: 1024px) {
            .ft { --ft-pad: 2.5rem; }
        }
    </style>

    <div class="ft__inner">
        <div class="ft__about">
            <p class="ft__brand"><span class="ft__logo" aria-hidden="true">?</span>Cerebrito</p>
            <p class="ft__desc">Trabajo Práctico de Programación Web 2, Universidad Nacional de La Matanza.</p>
        </div>

        <div class="ft__team">
            <h2 class="ft__title">Integrantes</h2>
            <ul class="ft__list">
                <li>Emanuel Abel Ruiz Díaz</li>
                <li>Agustina Cáceres Pérez</li>
                <li>Santiago Javier Falconi</li>
                <li>Juan Manuel Pena</li>
            </ul>
        </div>
    </div>

    <p class="ft__copy">© <?= date('Y') ?> Cerebrito</p>
</footer>

</body>
</html>