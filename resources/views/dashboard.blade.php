<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Cortes Medellin</title>
        <style>
            :root {
                --bg: #f3efe6;
                --ink: #1f2a2f;
                --panel: rgba(255, 252, 245, 0.82);
                --line: rgba(31, 42, 47, 0.14);
                --accent: #0b6e4f;
                --accent-soft: #d8efe6;
                --warn: #b55d2d;
                --shadow: 0 24px 70px rgba(36, 28, 18, 0.12);
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: Georgia, "Times New Roman", serif;
                color: var(--ink);
                background:
                    radial-gradient(circle at top left, rgba(11, 110, 79, 0.18), transparent 28%),
                    radial-gradient(circle at top right, rgba(181, 93, 45, 0.16), transparent 24%),
                    linear-gradient(180deg, #f7f4ed 0%, var(--bg) 100%);
                min-height: 100vh;
            }

            .page {
                width: min(1120px, calc(100% - 32px));
                margin: 0 auto;
                padding: 40px 0 56px;
            }

            .hero {
                background: var(--panel);
                border: 1px solid var(--line);
                border-radius: 28px;
                padding: 32px;
                box-shadow: var(--shadow);
                backdrop-filter: blur(14px);
            }

            .eyebrow {
                display: inline-block;
                margin-bottom: 14px;
                padding: 6px 10px;
                border-radius: 999px;
                background: var(--accent-soft);
                color: var(--accent);
                font-size: 12px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            h1 {
                margin: 0 0 14px;
                font-size: clamp(2rem, 4vw, 4.4rem);
                line-height: 0.98;
                max-width: 10ch;
            }

            .lead {
                max-width: 67ch;
                margin: 0;
                font-size: 1.08rem;
                line-height: 1.7;
            }

            .grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 16px;
                margin-top: 24px;
            }

            .card,
            .panel {
                background: rgba(255, 255, 255, 0.7);
                border: 1px solid var(--line);
                border-radius: 22px;
                box-shadow: var(--shadow);
            }

            .card {
                padding: 18px 20px;
            }

            .card small {
                display: block;
                color: rgba(31, 42, 47, 0.65);
                margin-bottom: 8px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                font-size: 11px;
            }

            .card strong {
                font-size: 2rem;
            }

            .panels {
                display: grid;
                grid-template-columns: 1.15fr 0.85fr;
                gap: 18px;
                margin-top: 18px;
            }

            .panel {
                padding: 24px;
            }

            h2 {
                margin: 0 0 14px;
                font-size: 1.3rem;
            }

            ul {
                margin: 0;
                padding-left: 18px;
            }

            li {
                margin-bottom: 12px;
                line-height: 1.6;
            }

            .pill {
                display: inline-block;
                margin-top: 12px;
                padding: 10px 14px;
                border-radius: 999px;
                background: rgba(181, 93, 45, 0.12);
                color: var(--warn);
                font-size: 0.95rem;
            }

            code {
                background: rgba(31, 42, 47, 0.08);
                border-radius: 6px;
                padding: 2px 6px;
                font-family: Consolas, "Courier New", monospace;
                font-size: 0.92em;
            }

            @media (max-width: 900px) {
                .grid,
                .panels {
                    grid-template-columns: 1fr 1fr;
                }
            }

            @media (max-width: 640px) {
                .page {
                    width: min(100% - 20px, 1120px);
                    padding-top: 20px;
                }

                .hero,
                .panel,
                .card {
                    border-radius: 18px;
                }

                .grid,
                .panels {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <main class="page">
            <section class="hero">
                <span class="eyebrow">Sistema Base</span>
                <h1>Cortes de obra con memoria por partida.</h1>
                <p class="lead">
                    La base del proyecto ya separa contrato importado, captura en campo y corte emitido.
                    El siguiente paso es cargar la plantilla de Excel del cliente y comenzar el flujo real de
                    importacion, captura por item y exportacion exacta.
                </p>

                <div class="grid">
                    <article class="card">
                        <small>Proyectos</small>
                        <strong>{{ $stats['projects'] }}</strong>
                    </article>
                    <article class="card">
                        <small>Lineas contractuales</small>
                        <strong>{{ $stats['contract_lines'] }}</strong>
                    </article>
                    <article class="card">
                        <small>Reportes de campo</small>
                        <strong>{{ $stats['field_reports'] }}</strong>
                    </article>
                    <article class="card">
                        <small>Cortes</small>
                        <strong>{{ $stats['estimates'] }}</strong>
                    </article>
                </div>
            </section>

            <section class="panels">
                <article class="panel">
                    <h2>Ruta de implementacion</h2>
                    <ul>
                        @foreach ($milestones as $milestone)
                            <li>{{ $milestone }}</li>
                        @endforeach
                    </ul>
                </article>

                <article class="panel">
                    <h2>Decisiones ya aterrizadas</h2>
                    <ul>
                        <li>La identidad real de una partida es la linea importada del Excel, no solo <code>concept_code</code>.</li>
                        <li>La memoria se construye con multiples filas en <code>field_report_rows</code>, no con un total plano.</li>
                        <li>Las evidencias van ligadas a la fila ejecutada para poder exportar anexos por item.</li>
                        <li>El corte se congela en <code>estimate_line_snapshots</code> para no depender de cambios posteriores.</li>
                    </ul>
                    <span class="pill">Pendiente inmediato: importador del Excel original + configuracion MySQL.</span>
                </article>
            </section>
        </main>
    </body>
</html>
