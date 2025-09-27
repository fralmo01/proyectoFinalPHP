<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nombre = $_SESSION['nombre'] ?? '';
$apePat = $_SESSION['apellidoPaterno'] ?? '';
$apeMat = $_SESSION['apellidoMaterno'] ?? '';
$nombreCompleto = trim("$nombre $apePat $apeMat");
if ($nombreCompleto === '') {
    $nombreCompleto = 'Administrador';
}
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel administrativo</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #0f172a;
        }

        .admin-layout {
            margin-left: var(--admin-sidebar-width, 260px);
            padding: 48px 56px 80px;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        .admin-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .admin-content h1 {
            font-size: 36px;
            margin-bottom: 10px;
            color: #1e293b;
            letter-spacing: -0.5px;
        }

        .admin-content .subtitle {
            font-size: 16px;
            color: #475569;
            margin-bottom: 36px;
        }

        .admin-widgets {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 28px;
            margin-bottom: 40px;
        }

        .admin-card {
            background: rgba(255, 255, 255, 0.75);
            border-radius: 20px;
            padding: 26px;
            box-shadow: 0 25px 50px -12px rgba(99, 102, 241, 0.32);
            border: 1px solid rgba(148, 163, 184, 0.25);
            backdrop-filter: blur(6px);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .admin-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 35px 70px -18px rgba(99, 102, 241, 0.38);
        }

        .admin-card h2 {
            font-size: 18px;
            margin: 0 0 12px;
            color: #1f2937;
        }

        .admin-card .value {
            font-size: 34px;
            font-weight: 700;
            color: #4338ca;
        }

        .admin-card .description {
            margin-top: 12px;
            font-size: 14px;
            color: #475569;
        }

        .admin-info-panel {
            display: grid;
            gap: 24px;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        }

        .admin-info-panel .panel-card {
            background: rgba(15, 23, 42, 0.72);
            color: #f8fafc;
            border-radius: 22px;
            padding: 32px;
            box-shadow: 0 30px 60px -18px rgba(15, 23, 42, 0.48);
            position: relative;
            overflow: hidden;
        }

        .admin-info-panel .panel-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(96, 165, 250, 0.4), rgba(232, 121, 249, 0.35));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .admin-info-panel .panel-card:hover::after {
            opacity: 1;
        }

        .admin-info-panel .panel-card > * {
            position: relative;
            z-index: 1;
        }

        .admin-info-panel h3 {
            font-size: 22px;
            margin-bottom: 12px;
        }

        .admin-info-panel p {
            font-size: 15px;
            line-height: 1.6;
            color: rgba(241, 245, 249, 0.9);
        }

        @media (max-width: 1024px) {
            .admin-layout {
                margin-left: 0;
                padding: 120px 24px 96px;
            }
        }

        @media (max-width: 640px) {
            .admin-content h1 {
                font-size: 28px;
            }

            .admin-card {
                padding: 22px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . "/../../layout/fondo.php"; ?>
    <?php include __DIR__ . "/../../layout/menu_Administrador.php"; ?>
    <div class="admin-layout">
        <main class="admin-content">
            <h1>Bienvenido, <?= htmlspecialchars($nombreCompleto ?? 'Administrador'); ?></h1>
            <p class="subtitle">Gestiona usuarios, empresas, convocatorias y reportes desde un mismo lugar.</p>

            <section class="admin-widgets">
                <article class="admin-card">
                    <h2>Usuarios activos</h2>
                    <div class="value">128</div>
                    <p class="description">Usuarios verificados en los últimos 30 días.</p>
                </article>

                <article class="admin-card">
                    <h2>Empresas aliadas</h2>
                    <div class="value">42</div>
                    <p class="description">Organizaciones publicando nuevas convocatorias.</p>
                </article>

                <article class="admin-card">
                    <h2>Convocatorias activas</h2>
                    <div class="value">16</div>
                    <p class="description">Oportunidades disponibles para los postulantes.</p>
                </article>
            </section>

            <section class="admin-info-panel">
                <div class="panel-card">
                    <h3>Organiza tus tareas</h3>
                    <p>Utiliza el menú para acceder rápidamente a la gestión de usuarios, empresas y convocatorias. Mantén la información actualizada para ofrecer una mejor experiencia a los postulantes.</p>
                </div>
                <div class="panel-card">
                    <h3>Reportes detallados</h3>
                    <p>Genera reportes para analizar el desempeño de las publicaciones y la interacción de los usuarios. Toma decisiones informadas apoyándote en la visualización de métricas clave.</p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
