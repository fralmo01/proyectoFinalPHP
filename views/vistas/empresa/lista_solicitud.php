<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitudes Recibidas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include __DIR__ . "/../../layout/fondo.php"; ?>
<?php include __DIR__ . "/../../layout/menu_empresa.php"; ?>

<div class="container mt-5 pt-5" style="position: relative; z-index: 1;">
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Solicitudes de Postulantes</h3>
            <span class="badge bg-primary">Total: <?= count($solicitudes ?? []) ?></span>
        </div>
        <div class="card-body">
            <?php if (empty($solicitudes)): ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>Aún no tienes postulantes registrados en tus convocatorias.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Postulante</th>
                                <th>Convocatoria</th>
                                <th>Etapa</th>
                                <th>Comentario</th>
                                <th>Contacto</th>
                                <th>Fecha Postulación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudes as $solicitud): ?>
                                <tr>
                                    <td class="fw-semibold">
                                        <i class="fas fa-user-circle text-secondary me-2"></i>
                                        <?= htmlspecialchars(trim($solicitud['postulante'])) ?: 'Sin nombre' ?>
                                    </td>
                                    <td><?= htmlspecialchars($solicitud['convocatoria']) ?></td>
                                    <td>
                                        <?php if (!empty($solicitud['etapa'])): ?>
                                            <span class="badge bg-info text-dark"><?= htmlspecialchars($solicitud['etapa']) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Sin etapa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $solicitud['comentario'] ? nl2br(htmlspecialchars($solicitud['comentario'])) : '<span class="text-muted">Sin comentarios</span>'; ?></td>
                                    <td>
                                        <?php if (!empty($solicitud['email'])): ?>
                                            <div><i class="fas fa-envelope me-2 text-muted"></i><a href="mailto:<?= htmlspecialchars($solicitud['email']) ?>"><?= htmlspecialchars($solicitud['email']) ?></a></div>
                                        <?php endif; ?>
                                        <?php if (!empty($solicitud['telefono'])): ?>
                                            <div><i class="fas fa-phone me-2 text-muted"></i><?= htmlspecialchars($solicitud['telefono']) ?></div>
                                        <?php endif; ?>
                                        <?php if (empty($solicitud['email']) && empty($solicitud['telefono'])): ?>
                                            <span class="text-muted">Sin datos de contacto</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($solicitud['fechaPostulacion']))) ?></td>
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