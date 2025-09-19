<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Convocatoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php include __DIR__ . "/../../layout/fondo.php"; ?>
<?php include __DIR__ . "/../../layout/menu_empresa.php"; ?>

<div class="container mt-5 pt-5">
    <div class="card shadow p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Editar Convocatoria</h3>
            <a href="index.php?controller=empresa&action=home" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <form action="index.php?controller=convocatoria&action=actualizar" method="POST">
            <input type="hidden" name="idConvocatoria" value="<?= htmlspecialchars($convocatoria['idConvocatoria']) ?>">

            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" required value="<?= htmlspecialchars($convocatoria['titulo']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="5"><?= htmlspecialchars($convocatoria['descripcion']) ?></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Fecha de Inicio</label>
                    <input type="date" name="fechaInicio" class="form-control" required value="<?= htmlspecialchars($convocatoria['fechaInicio']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha de Fin</label>
                    <input type="date" name="fechaFin" class="form-control" required value="<?= htmlspecialchars($convocatoria['fechaFin']) ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Jornada</label>
                    <select name="idJornada" class="form-select" required>
                        <option value="">-- Selecciona Jornada --</option>
                        <?php foreach ($jornadas as $j): ?>
                            <option value="<?= $j['idJornada'] ?>" <?= $j['idJornada'] == $convocatoria['idJornada'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($j['nombreJornada']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Modalidad</label>
                    <select name="idModalidad" class="form-select" required>
                        <option value="">-- Selecciona Modalidad --</option>
                        <?php foreach ($modalidades as $m): ?>
                            <option value="<?= $m['idModalidad'] ?>" <?= $m['idModalidad'] == $convocatoria['idModalidad'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['nombreModalidad']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="text-center">
                <button class="btn btn-primary"><i class="fas fa-save"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_GET['error']) && $_GET['error'] === 'update'): ?>
<script>
Swal.fire({ icon:'error', title:'Error al actualizar', text:'No se pudo actualizar la convocatoria.' });
</script>
<?php endif; ?>
</body>
</html>
