<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$fechaInicio = $fechaInicio ?? null;
$fechaFin = $fechaFin ?? null;
$historial = $historial ?? [];
$accionesPorUsuario = $accionesPorUsuario ?? [];
$mensaje = $mensaje ?? null;
$error = $error ?? null;

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes del Historial de Acciones</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #f4f6fb;
        }

        .admin-main {
            margin-left: 260px;
            padding: 40px;
        }

        @media (max-width: 900px) {
            .admin-main {
                margin-left: 0;
                padding: 100px 20px 40px;
            }
        }

        .report-header {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 24px;
        }

        .report-header h1 {
            font-size: 32px;
            color: #1f2937;
            margin: 0;
        }

        .report-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .report-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            background: #ffffff;
            padding: 12px 16px;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.15);
        }

        .report-filter label {
            font-weight: 600;
            color: #4b5563;
        }

        .report-filter input[type="date"],
        .report-filter button {
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            background-color: #fff;
            font-size: 14px;
            transition: box-shadow 0.2s ease;
        }

        .report-filter input[type="date"]:focus,
        .report-filter button:hover {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            outline: none;
        }

        .btn-primary,
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 18px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.25);
        }

        .btn-secondary {
            background: #fff;
            color: #4f46e5;
            border: 1px solid rgba(99, 102, 241, 0.4);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.12);
        }

        .btn-primary:hover,
        .btn-secondary:hover {
            transform: translateY(-2px);
        }

        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.1);
            margin-bottom: 28px;
            padding: 24px;
        }

        .card h2 {
            margin-top: 0;
            font-size: 24px;
            color: #1f2937;
        }

        .card p {
            color: #6b7280;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 860px;
        }

        thead {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
        }

        th,
        td {
            padding: 12px 14px;
            text-align: left;
            font-size: 14px;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tbody tr:hover {
            background-color: #eef2ff;
        }

        .table-empty {
            text-align: center;
            color: #6b7280;
            padding: 40px 16px;
        }

        .note-libraries {
            font-size: 13px;
            color: #6366f1;
            background: #eef2ff;
            padding: 12px 16px;
            border-radius: 12px;
            margin-top: 12px;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . "/../../layout/menu_Administrador.php"; ?>

    <main class="admin-main">
        <section class="report-header">
            <h1>Reportes del Historial</h1>
            <p class="note-libraries">
                Las librerías externas Dompdf y PhpSpreadsheet se encuentran disponibles en la carpeta
                <strong>app/libreria/</strong> (también referida como <strong>app/librerias/</strong>) del proyecto, dedicada a
                las dependencias externas.
            </p>
            <div class="report-actions">
                <form class="report-filter" method="get" action="index.php">
                    <input type="hidden" name="controller" value="admin">
                    <input type="hidden" name="action" value="reportes">
                    <label>
                        Desde
                        <input type="date" name="fechaInicio" value="<?= htmlspecialchars($fechaInicio ?? '') ?>">
                    </label>
                    <label>
                        Hasta
                        <input type="date" name="fechaFin" value="<?= htmlspecialchars($fechaFin ?? '') ?>">
                    </label>
                    <button type="submit">Aplicar filtros</button>
                </form>
                <form id="excelForm" method="post" action="index.php?controller=admin&action=exportarHistorialExcel">
                    <input type="hidden" name="fechaInicio" value="<?= htmlspecialchars($fechaInicio ?? '') ?>">
                    <input type="hidden" name="fechaFin" value="<?= htmlspecialchars($fechaFin ?? '') ?>">
                    <button type="submit" class="btn-secondary">Exportar a Excel</button>
                </form>
                <form id="pdfForm" method="post" action="index.php?controller=admin&action=descargarGraficoPdf">
                    <input type="hidden" name="fechaInicio" value="<?= htmlspecialchars($fechaInicio ?? '') ?>">
                    <input type="hidden" name="fechaFin" value="<?= htmlspecialchars($fechaFin ?? '') ?>">
                    <input type="hidden" name="grafico" id="graficoBase64">
                    <button type="button" id="pdfButton" class="btn-primary">Descargar gráfico en PDF</button>
                </form>
            </div>
        </section>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <section class="card">
            <h2>Acciones por usuario</h2>
            <p>Resumen de acciones registradas por usuario en el rango seleccionado.</p>
            <canvas id="accionesChart" height="140"></canvas>
        </section>

        <section class="card">
            <h2>Historial detallado</h2>
            <div class="table-container">
                <?php if (!empty($historial)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID acción</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Tabla afectada</th>
                                <th>ID registro</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historial as $registro): ?>
                                <tr>
                                    <td><?= htmlspecialchars($registro['idAccion']) ?></td>
                                    <td>
                                        <?php
                                        $nombreUsuario = trim($registro['nombreUsuario'] ?? '');
                                        if (!empty($registro['idUsuario'])):
                                            $nombreVisible = $nombreUsuario !== '' ? $nombreUsuario : 'Usuario sin nombre';
                                            $url = 'index.php?controller=admin&action=editarUsuario&id=' . urlencode((string) $registro['idUsuario']);
                                            ?>
                                            <a href="<?= $url ?>" title="Ver detalle del usuario">
                                                <?= htmlspecialchars($nombreVisible) ?>
                                            </a>
                                        <?php else: ?>
                                            <?= htmlspecialchars($nombreUsuario !== '' ? $nombreUsuario : 'Sin usuario asociado') ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($registro['accion']) ?></td>
                                    <td><?= htmlspecialchars($registro['tablaAfectada'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($registro['idRegistro'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($registro['fechaAccion']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="table-empty">
                        No se encontraron registros para el criterio seleccionado.
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        const accionesData = <?= json_encode($accionesPorUsuario, JSON_UNESCAPED_UNICODE) ?>;
        const labels = accionesData.map(item => item.nombreUsuario);
        const data = accionesData.map(item => Number(item.totalAcciones));

        const ctx = document.getElementById('accionesChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Acciones registradas',
                    data,
                    backgroundColor: 'rgba(99, 102, 241, 0.6)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        const pdfButton = document.getElementById('pdfButton');
        const pdfForm = document.getElementById('pdfForm');
        const graficoBase64 = document.getElementById('graficoBase64');

        pdfButton.addEventListener('click', () => {
            pdfButton.disabled = true;
            chart.update('none');
            setTimeout(() => {
                const url = chart.toBase64Image('image/png', 1);
                graficoBase64.value = url;
                pdfForm.submit();
                pdfButton.disabled = false;
            }, 200);
        });
    </script>
</body>

</html>