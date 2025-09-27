<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rolFiltro = $rolFiltro ?? 'todos';
$estadoFiltro = $estadoFiltro ?? 'activos';
$mensaje = $mensaje ?? null;
$error = $error ?? null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #f4f6fb;
        }

        .admin-main {
            margin-left: 260px;
            padding: 40px;
        }

        @media (max-width: 900px) {
            .admin-main {
                margin-left: 0;
                padding: 100px 20px 40px;
            }
        }

        .admin-users__header {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 30px;
        }

        .admin-users__header h1 {
            font-size: 32px;
            color: #1f2937;
            margin: 0;
        }

        .admin-users__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .admin-users__filters {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            background: #ffffff;
            padding: 12px 16px;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.15);
        }

        .admin-users__filters select,
        .admin-users__filters button {
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            background-color: #fff;
            font-size: 14px;
            transition: box-shadow 0.2s ease;
        }

        .admin-users__filters select:focus,
        .admin-users__filters button:hover {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            outline: none;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            padding: 12px 18px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.25);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.32);
        }

        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .table-container {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.1);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        thead {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
        }

        th,
        td {
            padding: 14px 16px;
            text-align: left;
            font-size: 14px;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tbody tr:hover {
            background-color: #eef2ff;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background-color: #22c55e1a;
            color: #16a34a;
        }

        .badge-danger {
            background-color: #fca5a5;
            color: #7f1d1d;
        }

        .table-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-secondary,
        .btn-danger,
        .btn-outline {
            border: none;
            border-radius: 10px;
            padding: 9px 14px;
            font-size: 13px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s ease, transform 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-secondary {
            background-color: #1d4ed8;
            color: #fff;
        }

        .btn-secondary:hover {
            background-color: #1e40af;
        }

        .btn-outline {
            background-color: transparent;
            color: #dc2626;
            border: 1px solid #dc2626;
        }

        .btn-outline:hover {
            background-color: #fee2e2;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . "/../../layout/fondo.php"; ?>
    <?php include __DIR__ . "/../../layout/menu_Administrador.php"; ?>

    <main class="admin-main">
        <section class="admin-users">
            <div class="admin-users__header">
                <h1>Gestión de Usuarios</h1>
                <div class="admin-users__actions">
                    <form class="admin-users__filters" method="get" action="index.php">
                        <input type="hidden" name="controller" value="admin">
                        <input type="hidden" name="action" value="usuarios">
                        <label>
                            Rol
                            <select name="rol">
                                <option value="todos" <?= $rolFiltro === 'todos' ? 'selected' : '' ?>>Todos</option>
                                <option value="administrador" <?= $rolFiltro === 'administrador' ? 'selected' : '' ?>>Administradores</option>
                                <option value="empresa" <?= $rolFiltro === 'empresa' ? 'selected' : '' ?>>Empresas</option>
                                <option value="postulante" <?= $rolFiltro === 'postulante' ? 'selected' : '' ?>>Postulantes</option>
                            </select>
                        </label>
                        <label>
                            Estado
                            <select name="estado">
                                <option value="activos" <?= $estadoFiltro === 'activos' ? 'selected' : '' ?>>Activos</option>
                                <option value="inactivos" <?= $estadoFiltro === 'inactivos' ? 'selected' : '' ?>>Inactivos</option>
                                <option value="todos" <?= $estadoFiltro === 'todos' ? 'selected' : '' ?>>Todos</option>
                            </select>
                        </label>
                        <button type="submit">Aplicar filtros</button>
                    </form>
                    <a class="btn-primary" href="index.php?controller=admin&action=nuevoUsuario">➕ Crear usuario</a>
                </div>
            </div>

            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="table-container">
                <?php if (!empty($usuarios)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre completo</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Empresa</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuarioItem): ?>
                                <?php
                                $nombreCompleto = trim(($usuarioItem['nombre'] ?? '') . ' ' . ($usuarioItem['apellidoPaterno'] ?? '') . ' ' . ($usuarioItem['apellidoMaterno'] ?? ''));
                                if ($nombreCompleto === '') {
                                    $nombreCompleto = 'Sin nombre';
                                }
                                $estadoActivo = (int)($usuarioItem['estado'] ?? 0) === 1;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($nombreCompleto) ?></td>
                                    <td><?= htmlspecialchars($usuarioItem['usuario'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($usuarioItem['rolNombre'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($usuarioItem['email'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($usuarioItem['telefono'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($usuarioItem['empresaNombre'] ?? 'No asignada') ?></td>
                                    <td>
                                        <span class="badge <?= $estadoActivo ? 'badge-success' : 'badge-danger' ?>">
                                            <?= $estadoActivo ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <a class="btn-secondary" href="index.php?controller=admin&action=editarUsuario&id=<?= (int)$usuarioItem['idUsuario'] ?>">✏️ Editar</a>
                                            <form method="post" action="index.php?controller=admin&action=cambiarEstadoUsuario" onsubmit="return confirmarCambioEstado(this);">
                                                <input type="hidden" name="idUsuario" value="<?= (int)$usuarioItem['idUsuario'] ?>">
                                                <input type="hidden" name="nuevoEstado" value="<?= $estadoActivo ? 0 : 1 ?>">
                                                <button type="submit" class="btn-outline"><?= $estadoActivo ? 'Desactivar' : 'Activar' ?></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        No se encontraron usuarios para los filtros seleccionados.
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        function confirmarCambioEstado(form) {
            const boton = form.querySelector('button');
            const accion = boton.textContent.trim().toLowerCase();
            return confirm(`¿Seguro que deseas ${accion} este usuario?`);
        }
    </script>
</body>

</html>