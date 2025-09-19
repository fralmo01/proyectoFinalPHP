<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Convocatoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php include __DIR__ . "/../../layout/fondo.php"; ?>
<?php include __DIR__ . "/../../layout/menu_empresa.php"; ?>

<div class="container mt-5 pt-5">
    <div class="card p-4 shadow">
        <h3 class="mb-4">Nueva Convocatoria</h3>

        <form action="index.php?controller=convocatoria&action=store" method="POST">
            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="5"></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Fecha de Inicio</label>
                    <input type="date" name="fechaInicio" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha de Fin</label>
                    <input type="date" name="fechaFin" class="form-control" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Jornada</label>
                    <select name="idJornada" class="form-select" required>
                        <option value="">-- Selecciona Jornada --</option>
                        <?php foreach ($jornadas as $j): ?>
                            <option value="<?= $j['idJornada'] ?>"><?= htmlspecialchars($j['nombreJornada']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Modalidad</label>
                    <select name="idModalidad" class="form-select" required>
                        <option value="">-- Selecciona Modalidad --</option>
                        <?php foreach ($modalidades as $m): ?>
                            <option value="<?= $m['idModalidad'] ?>"><?= htmlspecialchars($m['nombreModalidad']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="text-center">
                <button class="btn btn-success"><i class="fas fa-save"></i> Crear</button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<script>
Swal.fire({ icon:'success', title:'¡Convocatoria creada!', text:'La convocatoria se guardó correctamente.' });
</script>
<?php elseif (isset($_GET['error'])): ?>
<script>
Swal.fire({ icon:'error', title:'Error', text:'Hubo un problema al guardar la convocatoria.' });
</script>
<?php endif; ?>
</body>
</html>
