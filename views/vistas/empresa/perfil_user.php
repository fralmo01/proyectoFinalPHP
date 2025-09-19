<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Perfil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include __DIR__ . "/../../layout/fondo.php"; ?>
<?php include __DIR__ . "/../../layout/menu_empresa.php"; ?>

<div class="container mt-5 pt-5" style="position: relative; z-index:1;">
    <div class="card shadow-lg p-4">
        <h3 class="mb-4 text-center">Actualizar mis datos</h3>

        <form action="index.php?controller=usuario&action=updatePerfil" method="POST" enctype="multipart/form-data">
            <!-- Foto -->
            <div class="mb-3 text-center">
                <img src="fotos/<?= $_SESSION['fotoPerfil'] ?? 'default.png' ?>" 
                     alt="Foto perfil" class="rounded-circle mb-2" width="120" height="120">
                <input type="file" name="fotoPerfil" class="form-control mt-2" accept="image/*">
            </div>

            <!-- Nombre y Apellidos -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" value="<?= $_SESSION['nombre'] ?? '' ?>" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Apellido Paterno</label>
                    <input type="text" name="apellidoPaterno" value="<?= $_SESSION['apellidoPaterno'] ?? '' ?>" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Apellido Materno</label>
                    <input type="text" name="apellidoMaterno" value="<?= $_SESSION['apellidoMaterno'] ?? '' ?>" class="form-control">
                </div>
            </div>

            <!-- Email y Teléfono -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" value="<?= $_SESSION['email'] ?? '' ?>" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" value="<?= $_SESSION['telefono'] ?? '' ?>" class="form-control">
                </div>
            </div>

            <!-- Dirección, Nacionalidad y Sexo -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" value="<?= $_SESSION['direccion'] ?? '' ?>" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nacionalidad</label>
                    <input type="text" name="nacionalidad" value="<?= $_SESSION['nacionalidad'] ?? '' ?>" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sexo</label>
                    <select name="idSexo" class="form-select">
                        <option value="">-- Selecciona --</option>
                        <option value="1" <?= ($_SESSION['idSexo'] ?? '') == 1 ? 'selected' : '' ?>>Masculino</option>
                        <option value="2" <?= ($_SESSION['idSexo'] ?? '') == 2 ? 'selected' : '' ?>>Femenino</option>
                        <option value="3" <?= ($_SESSION['idSexo'] ?? '') == 3 ? 'selected' : '' ?>>Otro</option>
                    </select>
                </div>
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
    title: '¡Perfil actualizado!',
    text: 'Tus datos han sido guardados correctamente.',
    confirmButtonText: 'Aceptar'
});
</script>
<?php elseif (isset($_GET['error'])): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: 'No se pudieron actualizar tus datos.',
    confirmButtonText: 'Intentar de nuevo'
});
</script>
<?php endif; ?>

</body>
</html>
