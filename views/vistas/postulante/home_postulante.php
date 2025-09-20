<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Convocatorias</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php include __DIR__ . "/../../layout/fondo.php"; ?>
<?php include __DIR__ . "/../../layout/menu_postulante.php"; ?>

<div class="container mt-5 pt-5" style="position:relative; z-index:1;">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title">Filtrar convocatorias</h5>
                    <form method="get" action="index.php">
                        <input type="hidden" name="controller" value="postulante">
                        <input type="hidden" name="action" value="home">
                        <div class="mb-3">
                            <label for="modalidad" class="form-label">Modalidad</label>
                            <select name="modalidad" id="modalidad" class="form-select">
                                <option value="">Todas</option>
                                <?php foreach ($modalidades as $modalidad): ?>
                                    <option value="<?= $modalidad['idModalidad']; ?>" <?= ($idModalidad ?? null) === (int) $modalidad['idModalidad'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($modalidad['nombreModalidad']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jornada" class="form-label">Jornada</label>
                            <select name="jornada" id="jornada" class="form-select">
                                <option value="">Todas</option>
                                <?php foreach ($jornadas as $jornada): ?>
                                    <option value="<?= $jornada['idJornada']; ?>" <?= ($idJornada ?? null) === (int) $jornada['idJornada'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($jornada['nombreJornada']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if (!empty($detalle)): ?>
                            <input type="hidden" name="idConvocatoria" value="<?= (int) $detalle['idConvocatoria']; ?>">
                        <?php endif; ?>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i>Aplicar</button>
                            <a class="btn btn-outline-secondary" href="index.php?controller=postulante&action=home">Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>

            <h4 class="mb-3 text-white">Convocatorias activas</h4>
            <ul class="list-group shadow">
                <?php if (!empty($convocatorias)): ?>
                    <?php foreach ($convocatorias as $c): ?>
                        <a href="index.php?controller=postulante&action=home&idConvocatoria=<?= $c['idConvocatoria'] ?><?= ($idModalidad ? '&modalidad=' . $idModalidad : '') ?><?= ($idJornada ? '&jornada=' . $idJornada : '') ?>"
                           class="list-group-item list-group-item-action <?= ($detalle && $detalle['idConvocatoria'] == $c['idConvocatoria']) ? 'active' : '' ?>">
                            <strong><?= htmlspecialchars($c['titulo']) ?></strong><br>
                            <small><?= htmlspecialchars($c['empresaNombre']) ?></small>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item">No hay convocatorias que coincidan con los filtros seleccionados.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="col-md-8">
            <?php if ($detalle): ?>
                <div class="card shadow p-4">
                    <div class="d-flex align-items-center mb-3">
                        <?php
                            $logo = !empty($detalle['logoEmpresa']) ? "fotos/empresalogo/" . $detalle['logoEmpresa'] : "fotos/empresalogo/default_logo.png"; 
                        ?>
                        <img src="<?= htmlspecialchars($logo) ?>" alt="Logo empresa" class="me-3" style="width:60px; height:60px; object-fit:cover; border-radius:8px;">
                        <div>
                            <h3 class="mb-0"><?= htmlspecialchars($detalle['titulo']) ?></h3>
                            <small class="text-muted"><?= htmlspecialchars($detalle['empresaNombre']) ?></small>
                        </div>
                    </div>

                    <p><strong>Jornada:</strong> <?= htmlspecialchars($detalle['nombreJornada']) ?></p>
                    <p><strong>Modalidad:</strong> <?= htmlspecialchars($detalle['nombreModalidad']) ?></p>
                    <p><strong>Desde:</strong> <?= $detalle['fechaInicio'] ?> <strong>Hasta:</strong> <?= $detalle['fechaFin'] ?></p>
                    <hr>
                    <p><?= nl2br(htmlspecialchars($detalle['descripcion'])) ?></p>

                    <form method="POST" action="index.php?controller=postulante&action=postular">
                        <input type="hidden" name="idConvocatoria" value="<?= $detalle['idConvocatoria'] ?>">
                        <div class="mb-3">
                            <textarea name="comentario" class="form-control" placeholder="Escribe un comentario opcional..." rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> Postular
                        </button>
                    </form>
                </div>
            <?php elseif (!empty($convocatorias)): ?>
                <div class="alert alert-info">Selecciona una convocatoria para ver los detalles.</div>
            <?php else: ?>
                <div class="alert alert-warning">No se encontraron convocatorias con los filtros seleccionados. Ajusta los criterios de búsqueda para ver nuevas opciones.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (isset($_GET['error']) && $_GET['error'] === 'postulacion'): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Ya postulaste',
    text: 'Ya te has postulado a esta convocatoria.'
});
</script>
<?php endif; ?>

</body>
</html>
