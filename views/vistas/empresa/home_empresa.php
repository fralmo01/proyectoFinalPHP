<?php
// views/vistas/empresa/home_empresa.php
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel Empresa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <!-- Fondo animado -->
    <?php include __DIR__ . "/../../layout/fondo.php"; ?>

    <!-- Barra de navegación -->
    <?php include __DIR__ . "/../../layout/menu_empresa.php"; ?>

    <!-- Contenido principal -->
    <div class="container mt-5 pt-5" style="position: relative; z-index: 1;">
        <div class="text-center mb-4">
            <h2>Bienvenido, Empresa</h2>
            <p>Aquí podrás gestionar convocatorias, solicitudes y evaluaciones.</p>
        </div>

        <div class="card shadow p-4">
            <h4 class="mb-3">Mis Convocatorias</h4>
            <?php if (empty($convocatorias)): ?>
                <div class="alert alert-info">No tienes convocatorias creadas.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Jornada</th>
                                <th>Modalidad</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($convocatorias as $c): ?>
                                <tr>
                                    <td><?= htmlspecialchars($c['titulo']) ?></td>
                                    <td><?= htmlspecialchars($c['fechaInicio']) ?></td>
                                    <td><?= htmlspecialchars($c['fechaFin']) ?></td>
                                    <td><?= htmlspecialchars($c['nombreJornada']) ?></td>
                                    <td><?= htmlspecialchars($c['nombreModalidad']) ?></td>
                                    <td>
                                        <?php if ($c['estado']): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="index.php?controller=convocatoria&action=editar&id=<?= $c['idConvocatoria'] ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?controller=convocatoria&action=eliminar&id=<?= $c['idConvocatoria'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta convocatoria?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
