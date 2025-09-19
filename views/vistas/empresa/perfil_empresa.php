<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Empresa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include __DIR__ . "/../../layout/fondo.php"; ?>
<?php include __DIR__ . "/../../layout/menu_empresa.php"; ?>

<div class="container mt-5 pt-5" style="position: relative; z-index:1;">
    <div class="card shadow-lg p-4">
        <h3 class="mb-4 text-center">Actualizar datos de mi Empresa</h3>

        <form action="index.php?controller=empresa&action=update" method="POST" enctype="multipart/form-data">
            
            <!-- Logo -->
            <div class="mb-3 text-center">
                <?php if (!empty($_SESSION['logoEmpresa'])): ?>
                    <img src="fotos/empresalogo/<?= $_SESSION['logoEmpresa'] ?>" 
                         alt="Logo empresa" class="rounded mb-2" 
                         width="120" height="120">
                <?php else: ?>
                    <p class="text-muted">No has subido un logo aún</p>
                <?php endif; ?>
                <input type="file" name="logoEmpresa" class="form-control mt-2" accept="image/*">
            </div>

            <!-- Razón social -->
            <div class="mb-3">
                <label class="form-label">Razón Social</label>
                <input type="text" name="nombre" value="<?= $_SESSION['razonSocial'] ?? '' ?>" class="form-control" required>
            </div>

            <!-- Email y Teléfono -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Correo Empresa</label>
                    <input type="email" name="email" value="<?= $_SESSION['empresaEmail'] ?? '' ?>" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" value="<?= $_SESSION['empresaTelefono'] ?? '' ?>" class="form-control">
                </div>
            </div>

            <!-- Dirección -->
            <div class="mb-3">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" value="<?= $_SESSION['empresaDireccion'] ?? '' ?>" class="form-control">
            </div>

            <!-- Sitio web -->
            <div class="mb-3">
                <label class="form-label">Sitio Web</label>
                <input type="text" name="sitioWeb" value="<?= $_SESSION['empresaWeb'] ?? '' ?>" class="form-control">
            </div>

            <!-- Botones -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar cambios
                </button>
                <a href="index.php?controller=empresa&action=home" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: '¡Actualización exitosa!',
    text: 'Los datos de tu empresa han sido actualizados.',
    confirmButtonText: 'Aceptar'
});
</script>
<?php elseif (isset($_GET['error'])): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: 'No se pudieron actualizar los datos de la empresa.',
    confirmButtonText: 'Intentar de nuevo'
});
</script>
<?php endif; ?>

</body>
</html>
