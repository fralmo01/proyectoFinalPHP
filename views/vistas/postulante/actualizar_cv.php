<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Documentos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include __DIR__ . "/../../layout/fondo.php"; ?>
<?php include __DIR__ . "/../../layout/menu_postulante.php"; ?>

<div class="container mt-5 pt-5" style="position: relative; z-index:1;">
    <div class="card shadow-lg p-4">
        <h3 class="mb-4 text-center">Subir / Actualizar Documentos</h3>

        <form action="index.php?controller=postulante&action=guardarDocumento" 
      method="POST" enctype="multipart/form-data">
            <!-- Tipo de documento -->
            <div class="mb-3">
                <label class="form-label">Tipo de documento</label>
                <select name="idTipoDocumento" class="form-select" required>
                    <option value="">-- Selecciona --</option>
                    <?php foreach ($tipos as $t): ?>
                        <option value="<?= $t['idTipoDocumento'] ?>"><?= htmlspecialchars($t['nombreTipoDocumento']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Archivo -->
            <div class="mb-3">
                <label class="form-label">Archivo</label>
                <input type="file" name="documento" class="form-control" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Subir Documento
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de documentos subidos -->
    <div class="card shadow-lg p-4 mt-4">
        <h4 class="mb-3">Mis Documentos</h4>
        <?php if (empty($documentos)): ?>
            <div class="alert alert-info">No has subido documentos aún.</div>
        <?php else: ?>
            <ul class="list-group">
                <?php foreach ($documentos as $doc): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-file-alt"></i>
                            <?= htmlspecialchars($doc['nombreTipoDocumento']) ?>
                        </div>
                        <a href="<?= htmlspecialchars($doc['rutaArchivo']) ?>" target="_blank" class="btn btn-sm btn-secondary">
                            <i class="fas fa-download"></i> Ver
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: '¡Documento actualizado!',
    text: 'Tu archivo se guardó correctamente.'
});
</script>
<?php elseif (isset($_GET['error'])): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: 'Hubo un problema al subir el documento.'
});
</script>
<?php endif; ?>

</body>
</html>
