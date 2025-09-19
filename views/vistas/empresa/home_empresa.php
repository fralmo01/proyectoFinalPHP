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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Mis Convocatorias</h4>
                <a class="btn btn-success" href="index.php?controller=convocatoria&action=crear">
                    <i class="fas fa-plus"></i> Nueva Convocatoria
                </a>
            </div>
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
                                <th class="text-center">Acciones</th>
                             </tr>
                         </thead>
                         <tbody>
                             <?php foreach ($convocatorias as $c): ?>
                                 <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($c['titulo']) ?></strong><br>
                                        <small class="text-muted"><?= nl2br(htmlspecialchars($c['descripcion'] ?? '')) ?></small>
                                    </td>
                                     <td><?= htmlspecialchars($c['fechaInicio']) ?></td>
                                     <td><?= htmlspecialchars($c['fechaFin']) ?></td>
                                     <td><?= htmlspecialchars($c['nombreJornada']) ?></td>
                                     <td><?= htmlspecialchars($c['nombreModalidad']) ?></td>
                                     <td>
                                        <?php if (!empty($c['estado'])): ?>
                                             <span class="badge bg-success">Activo</span>
                                         <?php else: ?>
                                             <span class="badge bg-secondary">Inactivo</span>
                                         <?php endif; ?>
                                     </td>
                                    <td class="text-center">
                                        <a
                                            href="index.php?controller=convocatoria&action=editar&id=<?= $c['idConvocatoria'] ?>"
                                            class="btn btn-sm btn-warning me-1"
                                            title="Editar"
                                        >
                                             <i class="fas fa-edit"></i>
                                         </a>
                                        <form
                                            action="index.php?controller=convocatoria&action=eliminar"
                                            method="POST"
                                           class="d-inline form-eliminar"
                                        >
                                            <input type="hidden" name="idConvocatoria" value="<?= $c['idConvocatoria'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                     </td>
                                 </tr>
                             <?php endforeach; ?>
                         </tbody>
                     </table>
                 </div>
             <?php endif; ?>
         </div>
     </div>
 
    <script>
    document.querySelectorAll('.form-eliminar').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Eliminar convocatoria?',
                text: 'La convocatoria se desactivará y no estará visible para los postulantes.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    const params = new URLSearchParams(window.location.search);
    if (params.get('success') === 'created') {
        Swal.fire({ icon: 'success', title: 'Convocatoria creada', text: 'La convocatoria se registró correctamente.' });
    } else if (params.get('success') === 'updated') {
        Swal.fire({ icon: 'success', title: 'Convocatoria actualizada', text: 'Los cambios se guardaron correctamente.' });
    } else if (params.get('success') === 'deleted') {
        Swal.fire({ icon: 'success', title: 'Convocatoria eliminada', text: 'La convocatoria fue desactivada.' });
    } else if (params.get('error') === 'delete') {
        Swal.fire({ icon: 'error', title: 'Error al eliminar', text: 'No se pudo eliminar la convocatoria.' });
    } else if (params.get('error') === 'notfound') {
        Swal.fire({ icon: 'error', title: 'Convocatoria no encontrada', text: 'No se encontró la información solicitada.' });
    }
    </script>
 </body>
 </html>
