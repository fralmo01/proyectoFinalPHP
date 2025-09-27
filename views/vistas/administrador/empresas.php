<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$categoriaFiltro = $categoriaFiltro ?? 'todas';
$estadoFiltro = $estadoFiltro ?? 'activos';
$categorias = $categorias ?? [];
$empresas = $empresas ?? [];
$mensaje = $mensaje ?? null;
$error = $error ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empresas</title>
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

        .admin-header {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 28px;
        }

        .admin-header__title {
            font-size: 32px;
            color: #1f2937;
            margin: 0;
        }

        .filters-card {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            background: #ffffff;
            padding: 16px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.15);
            align-items: center;
        }

        .filters-card label {
            font-size: 13px;
            font-weight: 600;
            color: #4c1d95;
        }

        .filters-card select,
        .filters-card button,
        .filters-card a {
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            background-color: #fff;
            font-size: 14px;
            transition: box-shadow 0.2s ease;
            text-decoration: none;
            color: #111827;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .filters-card select:focus,
        .filters-card button:hover,
        .filters-card a:hover {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            outline: none;
        }

        .filters-card button {
            border: none;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            font-weight: 600;
            cursor: pointer;
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
            min-width: 880px;
        }

        thead {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
        }

        th, td {
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

        .company-logo {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid rgba(79, 70, 229, 0.2);
        }

        .company-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .company-name {
            font-weight: 600;
            color: #111827;
        }

        .company-meta {
            font-size: 13px;
            color: #6b7280;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
            background-color: #fecaca;
            color: #7f1d1d;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn-action {
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

        .btn-danger {
            background-color: #dc2626;
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #b91c1c;
            transform: translateY(-1px);
        }

        .btn-success {
            background-color: #16a34a;
            color: #fff;
        }

        .btn-success:hover {
            background-color: #15803d;
            transform: translateY(-1px);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
            font-size: 15px;
        }

        .table-action-form {
            margin: 0;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . "/../../layout/fondo.php"; ?>
    <?php include __DIR__ . "/../../layout/menu_Administrador.php"; ?>

    <main class="admin-main">
        <div class="admin-header">
            <h1 class="admin-header__title">Gestión de Empresas</h1>
            <form class="filters-card" method="GET" action="index.php">
                <input type="hidden" name="controller" value="admin">
                <input type="hidden" name="action" value="empresas">

                <div>
                    <label for="categoria">Categoría</label><br>
                    <select name="categoria" id="categoria">
                        <option value="todas" <?= $categoriaFiltro === 'todas' ? 'selected' : '' ?>>Todas</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= htmlspecialchars($categoria) ?>" <?= $categoriaFiltro === $categoria ? 'selected' : '' ?>>
                                <?= htmlspecialchars($categoria) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="estado">Estado</label><br>
                    <select name="estado" id="estado">
                        <option value="activos" <?= $estadoFiltro === 'activos' ? 'selected' : '' ?>>Activas</option>
                        <option value="inactivos" <?= $estadoFiltro === 'inactivos' ? 'selected' : '' ?>>Inactivas</option>
                        <option value="todos" <?= $estadoFiltro === 'todos' ? 'selected' : '' ?>>Todas</option>
                    </select>
                </div>

                <button type="submit">Aplicar filtros</button>
                <a href="index.php?controller=admin&action=empresas">Limpiar</a>
            </form>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>Categoría</th>
                        <th>Contacto</th>
                        <th>Sitio web</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($empresas)): ?>
                    <tr>
                        <td colspan="6" class="empty-state">No se encontraron empresas con los filtros seleccionados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($empresas as $empresa): ?>
                        <?php
                            $logo = !empty($empresa['logoEmpresa'])
                                ? 'fotos/empresalogo/' . $empresa['logoEmpresa']
                                : 'fotos/empresalogo/default_logo.png';
                            $fechaCreacion = !empty($empresa['fechaCreacion'])
                                ? date('d/m/Y', strtotime($empresa['fechaCreacion']))
                                : null;
                        ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:14px;">
                                    <img src="<?= htmlspecialchars($logo) ?>" alt="Logo de <?= htmlspecialchars($empresa['nombre']) ?>" class="company-logo">
                                    <div class="company-info">
                                        <span class="company-name"><?= htmlspecialchars($empresa['nombre']) ?></span>
                                        <span class="company-meta">
                                            Creada <?= $fechaCreacion ? 'el ' . htmlspecialchars($fechaCreacion) : 'sin registro' ?>
                                        </span>
                                        <?php if (!empty($empresa['direccion'])): ?>
                                            <span class="company-meta"><?= htmlspecialchars($empresa['direccion']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($empresa['categoria'] ?? 'Sin categoría') ?></td>
                            <td>
                                <div class="company-info">
                                    <?php if (!empty($empresa['email'])): ?>
                                        <span class="company-meta">📧 <?= htmlspecialchars($empresa['email']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($empresa['telefono'])): ?>
                                        <span class="company-meta">📞 <?= htmlspecialchars($empresa['telefono']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($empresa['sitioWeb'])): ?>
                                    <a href="<?= htmlspecialchars($empresa['sitioWeb']) ?>" target="_blank" rel="noopener noreferrer">
                                        <?= htmlspecialchars($empresa['sitioWeb']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="company-meta">Sin registro</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($empresa['estado'])): ?>
                                    <span class="badge badge-success">Activa</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="actions">
                                    <form class="table-action-form" method="POST" action="index.php?controller=admin&action=cambiarEstadoEmpresa" onsubmit="return confirm('¿Estás seguro de que deseas <?= !empty($empresa['estado']) ? 'desactivar' : 'activar' ?> esta empresa?');">
                                        <input type="hidden" name="idEmpresa" value="<?= (int) $empresa['idEmpresa'] ?>">
                                        <?php if (!empty($empresa['estado'])): ?>
                                            <input type="hidden" name="accion" value="desactivar">
                                            <button type="submit" class="btn-action btn-danger">Desactivar</button>
                                        <?php else: ?>
                                            <input type="hidden" name="accion" value="activar">
                                            <button type="submit" class="btn-action btn-success">Activar</button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>