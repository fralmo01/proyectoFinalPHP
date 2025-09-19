<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Evaluaciones por Etapa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include __DIR__ . "/../../layout/fondo.php"; ?>
<?php include __DIR__ . "/../../layout/menu_empresa.php"; ?>

<div class="container mt-5 pt-5" style="position: relative; z-index: 1;">
    <div class="d-flex align-items-center mb-4">
        <a href="index.php?controller=Evaluacion&action=listar" class="btn btn-link text-decoration-none me-3">
            <i class="fas fa-arrow-left"></i> Volver al panel
        </a>
        <div>
            <h2 class="mb-0">Gestión integral del postulante</h2>
            <small class="text-muted">Registra evaluaciones, gestiona etapas y define la decisión final.</small>
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

    <?php if (!empty($detalle)): ?>
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-user-tie text-success me-2"></i>Datos del postulante</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Postulante:</strong><br><?= htmlspecialchars(trim($detalle['postulante'])) ?></p>
                        <p class="mb-2"><strong>Convocatoria:</strong><br><?= htmlspecialchars($detalle['convocatoria']) ?></p>
                        <p class="mb-2"><strong>Etapa actual:</strong><br>
                            <?php if (!empty($detalle['etapa'])): ?>
                               <span class="badge bg-primary"><?= htmlspecialchars($detalle['etapa']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Sin etapa asignada</span>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($detalle['comentario'])): ?>
                            <p class="mb-2"><strong>Comentario del postulante:</strong><br><?= nl2br(htmlspecialchars($detalle['comentario'])) ?></p>
                        <?php endif; ?>
                        <p class="mb-2"><strong>Correo:</strong><br><?= htmlspecialchars($detalle['email'] ?? 'No registrado') ?></p>
                        <p class="mb-0"><strong>Teléfono:</strong><br><?= htmlspecialchars($detalle['telefono'] ?? 'No registrado') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 mb-4" id="form-evaluacion">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-clipboard text-success me-2"></i>Registrar evaluación</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="index.php?controller=Evaluacion&action=detalle&idPostulacion=<?= (int)$detalle['idPostulacion'] ?>">
                            <input type="hidden" name="accion" value="registrar_evaluacion">
                            <div class="mb-3">
                                <label class="form-label">Puntaje obtenido</label>
                                <input type="number" name="puntaje" class="form-control" min="0" step="0.01" placeholder="Ej. 85.50" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Observaciones</label>
                                <textarea name="observaciones" class="form-control" rows="3" placeholder="Notas sobre la evaluación"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Guardar evaluación</button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4" id="form-etapa">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-route text-primary me-2"></i>Cambiar etapa</h5>
                        <small class="text-muted">Selecciona la etapa a la que avanzará el postulante.</small>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($etapasPosteriores)): ?>
                            <form method="post" action="index.php?controller=Evaluacion&action=detalle&idPostulacion=<?= (int)$detalle['idPostulacion'] ?>">
                                <input type="hidden" name="accion" value="cambiar_etapa">
                                <div class="mb-3">
                                    <label class="form-label">Nueva etapa</label>
                                    <select name="idEtapaNueva" class="form-select" required>
                                        <option value="">Selecciona una etapa</option>
                                        <?php foreach ($etapasPosteriores as $etapa): ?>
                                            <option value="<?= (int)$etapa['idEtapa'] ?>"><?= htmlspecialchars($etapa['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-arrow-up me-2"></i>Actualizar etapa</button>
                            </form>
                        <?php else: ?>
                            <p class="text-muted mb-0">No hay etapas siguientes disponibles para esta postulación.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card shadow-sm border-0" id="form-resultado">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-gavel text-dark me-2"></i>Decisión final</h5>
                        <small class="text-muted">Define el resultado final del proceso.</small>
                    </div>
                    <div class="card-body">
                        <form method="post" action="index.php?controller=Evaluacion&action=detalle&idPostulacion=<?= (int)$detalle['idPostulacion'] ?>">
                            <input type="hidden" name="accion" value="guardar_resultado">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-8">
                                    <label class="form-label">Estado del resultado</label>
                                    <select name="idEstadoResultado" class="form-select" required>
                                        <option value="">Selecciona un estado</option>
                                        <?php foreach ($estadosResultado as $estado): ?>
                                            <option value="<?= (int)$estado['idEstadoResultado'] ?>" <?= (!empty($resultado['idEstadoResultado']) && (int)$resultado['idEstadoResultado'] === (int)$estado['idEstadoResultado']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($estado['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 d-grid">
                                    <button type="submit" class="btn btn-dark"><i class="fas fa-check me-2"></i>Guardar decisión</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-history text-primary me-2"></i>Historial de etapas</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $historialEtapas = array_filter($historialAcciones, function ($accion) {
                            return stripos($accion['accion'] ?? '', 'etapa') !== false;
                        });
                        ?>
                        <?php if (!empty($historialEtapas)): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($historialEtapas as $registro): ?>
                                    <li class="list-group-item px-0">
                                        <div class="fw-semibold"><?= htmlspecialchars($registro['accion']) ?></div>
                                        <div class="text-muted small">
                                           <i class="fas fa-clock me-1"></i><?= htmlspecialchars(date('d/m/Y H:i', strtotime($registro['fechaAccion']))) ?>
                                            <?php if (!empty($registro['usuario'])): ?>
                                                &nbsp;|&nbsp;<i class="fas fa-user me-1"></i><?= htmlspecialchars($registro['usuario']) ?>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted mb-0">Aún no hay registros de cambios de etapa para este postulante.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-star text-warning me-2"></i>Evaluaciones registradas</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($evaluaciones)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Puntaje</th>
                                            <th>Observaciones</th>
                                            <th>Fecha</th>
                                            <th>Evaluador</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($evaluaciones as $evaluacion): ?>
                                            <tr>
                                                <td><span class="badge bg-success fs-6"><?= number_format((float)$evaluacion['puntaje'], 2) ?></span></td>
                                                <td><?= !empty($evaluacion['observaciones']) ? nl2br(htmlspecialchars($evaluacion['observaciones'])) : '<span class="text-muted">Sin observaciones</span>' ?></td>
                                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($evaluacion['fechaEvaluacion']))) ?></td>
                                                <td><?= htmlspecialchars($evaluacion['evaluador'] ?? 'No registrado') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No se han registrado evaluaciones para esta postulación.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-flag-checkered text-success me-2"></i>Resultado final</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($resultado)): ?>
                            <p class="mb-2"><strong>Estado:</strong> <span class="badge bg-dark"><?= htmlspecialchars($resultado['estado']) ?></span></p>
                            <p class="mb-0 text-muted"><i class="fas fa-clock me-1"></i>Registrado el <?= htmlspecialchars(date('d/m/Y H:i', strtotime($resultado['fechaResultado']))) ?></p>
                        <?php else: ?>
                            <p class="text-muted mb-0">Aún no se ha definido un resultado final para esta postulación.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <i class="fas fa-circle-info me-2"></i>No se encontró información de la postulación solicitada.
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
