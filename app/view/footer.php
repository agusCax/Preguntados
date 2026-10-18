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

            margin-top: 2.5rem;
            padding: 2rem var(--ft-pad) 1.5rem;
            font-family: var(--font-text, "Nunito", system-ui, sans-serif);
            text-align: center;
            color: var(--color-ink-soft, #334155);
            background-color: var(--color-white, #ffffff);
            border-top: 1px solid var(--color-border, #e2e8f0);
        }

        .ft__inner {
            display: grid;
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .ft__brand {
            margin: 0 0 0.25rem;
            font-family: var(--font-family, "Outfit", system-ui, sans-serif);
            font-size: 24px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.01em;
            color: var(--color-primary-dark, #0369a1);
        }

        .ft__desc {
            max-width: 36ch;
            margin: 0 auto;
            font-size: 0.95rem;
            line-height: 1.5;
            color: #64748b;
        }

        .ft__title {
            margin: 0 0 0.5rem;
            font-family: var(--font-text, "Nunito", system-ui, sans-serif);
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--color-primary-dark, #0369a1);
        }

        .ft__list {
            display: grid;
            gap: 0.25rem;
            margin: 0;
            padding: 0;
            list-style: none;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .ft__copy {
            max-width: 1200px;
            margin: 1.5rem auto 0;
            padding-top: 1rem;
            font-size: 0.85rem;
            color: #64748b;
            border-top: 1px solid var(--color-border, #e2e8f0);
        }

        /* Tablet y desktop: marca a la izquierda, integrantes a la derecha */
        @media (min-width: 768px) {
            .ft {
                --ft-pad: 1.5rem;
            }

            .ft__inner {
                grid-template-columns: 1fr 1fr;
                align-items: start;
                text-align: left;
            }

            .ft__desc {
                margin-inline: 0;
            }

            .ft__team {
                justify-self: end;
            }

            .ft__list {
                grid-template-columns: 1fr 1fr;
                gap: 0.35rem 2rem;
            }

            .ft__copy {
                text-align: left;
            }
        }

        @media (min-width: 1024px) {
            .ft {
                --ft-pad: 2.5rem;
            }
        }
    </style>

    <div class="ft__inner">
        <div class="ft__about">
            <p class="ft__brand">Cerebrito</p>
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