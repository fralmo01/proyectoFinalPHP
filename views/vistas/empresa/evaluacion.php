<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Evaluaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include __DIR__ . "/../../layout/fondo.php"; ?>
<?php include __DIR__ . "/../../layout/menu_empresa.php"; ?>

<div class="container mt-5 pt-5" style="position: relative; z-index: 1;">
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-check-circle me-2 text-success"></i>Evaluaciones de Postulantes</h3>
            <span class="badge bg-success">Total: <?= count($evaluaciones ?? []) ?></span>
        </div>
        <div class="card-body">
            <?php if (empty($evaluaciones)): ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>No se registran evaluaciones para tus convocatorias.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Postulante</th>
                                <th>Convocatoria</th>
                                <th>Etapa</th>
                                <th>Puntaje</th>
                                <th>Observaciones</th>
                                <th>Resultado</th>
                                <th>Fecha Evaluación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($evaluaciones as $evaluacion): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars(trim($evaluacion['postulante'])) ?: 'Sin nombre' ?></div>
                                        <div class="small text-muted">
                                            <?php if (!empty($evaluacion['email'])): ?>
                                                <div><i class="fas fa-envelope me-1"></i><a href="mailto:<?= htmlspecialchars($evaluacion['email']) ?>"><?= htmlspecialchars($evaluacion['email']) ?></a></div>
                                            <?php endif; ?>
                                            <?php if (!empty($evaluacion['telefono'])): ?>
                                                <div><i class="fas fa-phone me-1"></i><?= htmlspecialchars($evaluacion['telefono']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($evaluacion['convocatoria']) ?></td>
                                    <td><?= !empty($evaluacion['etapa']) ? htmlspecialchars($evaluacion['etapa']) : 'Sin etapa' ?></td>
                                    <td>
                                        <span class="badge bg-primary fs-6"><?= number_format((float) ($evaluacion['puntaje'] ?? 0), 2) ?></span>
                                    </td>
                                    <td><?= $evaluacion['observaciones'] ? nl2br(htmlspecialchars($evaluacion['observaciones'])) : '<span class="text-muted">Sin observaciones</span>'; ?></td>
                                    <td>
                                        <?php if (!empty($evaluacion['estadoResultado'])): ?>
                                            <span class="badge bg-dark"><?= htmlspecialchars($evaluacion['estadoResultado']) ?></span>
                                            <?php if (!empty($evaluacion['fechaResultado'])): ?>
                                                <div class="small text-muted"><?= htmlspecialchars(date('d/m/Y', strtotime($evaluacion['fechaResultado']))) ?></div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($evaluacion['fechaEvaluacion']))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>