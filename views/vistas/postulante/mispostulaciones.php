<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Postulaciones</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php include __DIR__ . "/../../layout/fondo.php"; ?>
<?php include __DIR__ . "/../../layout/menu_postulante.php"; ?>

<div class="container mt-5 pt-5" style="position:relative; z-index:1;">
    <h3 class="mb-4">Mis Postulaciones</h3>

    <?php if (!empty($postulaciones)): ?>
        <table class="table table-striped table-hover shadow">
            <thead class="table-primary">
                <tr>
                    <th>Convocatoria</th>
                    <th>Empresa</th>
                    <th>Etapa</th>
                    <th>Comentario</th>
                    <th>Fecha Postulación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($postulaciones as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['titulo'] ?? 'Sin título') ?></td>
                        <td><?= htmlspecialchars($p['empresaNombre'] ?? 'Sin empresa') ?></td>
                        <td><?= htmlspecialchars($p['nombreEtapa'] ?? 'No asignada') ?></td>
                        <td><?= htmlspecialchars($p['comentario'] ?? 'Sin comentario') ?></td>
                        <td><?= htmlspecialchars($p['fechaPostulacion'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">Aún no tienes postulaciones registradas.</div>
    <?php endif; ?>
</div>

<?php if (isset($_GET['success'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: '¡Postulación registrada!',
    text: 'Has postulado correctamente a la convocatoria.',
});
</script>
<?php endif; ?>

</body>
</html>

