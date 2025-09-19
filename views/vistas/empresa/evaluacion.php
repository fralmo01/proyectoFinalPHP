<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguimiento de Postulantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include __DIR__ . "/../../layout/fondo.php"; ?>
<?php include __DIR__ . "/../../layout/menu_empresa.php"; ?>

<div class="container mt-5 pt-5" style="position: relative; z-index: 1;">
    <div class="d-flex align-items-center mb-4">
        <i class="fas fa-clipboard-list fa-2x text-success me-3"></i>
        <div>
            <h2 class="mb-0">Panel de Evaluación de Postulantes</h2>
            <small class="text-muted">Gestiona las etapas, evaluaciones y decisiones finales de tus candidatos.</small>
        </div>
    </div>

    <?php if (!empty($mensajes['exito'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($mensajes['exito']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($mensajes['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-triangle-exclamation me-2"></i><?= htmlspecialchars($mensajes['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php
    $bloques = [
        'pendientes' => [
            'titulo' => 'Postulantes sin modificar / pendientes',
            'descripcion' => 'Postulaciones sin una etapa definida o en espera de gestión.',
            'color' => 'secondary',
            'icono' => 'fa-circle-question'
        ],
        'revision' => [
            'titulo' => 'Revisión inicial',
            'descripcion' => 'Candidatos que se encuentran en validación de requisitos.',
            'color' => 'primary',
            'icono' => 'fa-magnifying-glass'
        ],
        'entrevistas' => [
            'titulo' => 'Entrevistas (teléfono/presencial)',
            'descripcion' => 'Personas convocadas a entrevistas telefónicas o presenciales.',
            'color' => 'warning',
            'icono' => 'fa-comments'
        ],
        'pruebas' => [
            'titulo' => 'Prueba técnica / Selección final',
            'descripcion' => 'Postulantes en evaluación técnica o decisión final.',
            'color' => 'success',
            'icono' => 'fa-clipboard-check'
        ],
    ];
    ?>

    <?php foreach ($bloques as $clave => $info): ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">
                        <i class="fas <?= $info['icono'] ?> text-<?= $info['color'] ?> me-2"></i>
                        <?= htmlspecialchars($info['titulo']) ?>
                    </h4>
                    <small class="text-muted"><?= htmlspecialchars($info['descripcion']) ?></small>
                </div>
                <span class="badge bg-<?= $info['color'] ?> rounded-pill fs-6">
                    <?= isset($categorias[$clave]) ? count($categorias[$clave]) : 0 ?> postulantes
                </span>
            </div>
            <div class="card-body">
                <?php if (empty($categorias[$clave])): ?>
                    <p class="text-muted mb-0">No hay postulantes en esta etapa.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 220px;">Postulante</th>
                                    <th style="min-width: 200px;">Convocatoria</th>
                                    <th>Etapa actual</th>
                                    <th>Resultado</th>
                                    <th style="width: 220px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categorias[$clave] as $postulacion): ?>
                                    <tr>
                                        <td class="fw-semibold">
                                            <?= htmlspecialchars(trim($postulacion['postulante'])) ?: 'Sin nombre registrado' ?>
                                        </td>
                                        <td><?= htmlspecialchars($postulacion['convocatoria']) ?></td>
                                        <td>
                                            <?php if (!empty($postulacion['etapa'])): ?>
                                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($postulacion['etapa']) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Sin etapa</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($postulacion['estadoResultado'])): ?>
                                                <span class="badge bg-dark"><?= htmlspecialchars($postulacion['estadoResultado']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Pendiente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a class="btn btn-outline-success btn-sm" href="index.php?controller=Evaluacion&action=detalle&idPostulacion=<?= (int)$postulacion['idPostulacion'] ?>#form-evaluacion">
                                                    <i class="fas fa-clipboard"></i> Evaluar
                                                </a>
                                                <form method="post" action="index.php?controller=Evaluacion&action=listar" class="d-inline">
                                                    <input type="hidden" name="accion" value="cambiar_etapa">
                                                    <input type="hidden" name="idPostulacion" value="<?= (int)$postulacion['idPostulacion'] ?>">
                                                    <input type="hidden" name="idEtapaActual" value="<?= (int)($postulacion['idEtapa'] ?? 0) ?>">
                                                    <button type="submit" class="btn btn-outline-primary btn-sm" <?= empty($postulacion['idEtapa']) ? 'disabled' : '' ?>>
                                                        <i class="fas fa-arrow-up"></i> Cambiar etapa
                                                    </button>
                                                </form>
                                                <a class="btn btn-outline-dark btn-sm" href="index.php?controller=Evaluacion&action=detalle&idPostulacion=<?= (int)$postulacion['idPostulacion'] ?>#form-resultado">
                                                    <i class="fas fa-gavel"></i> Decidir
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
