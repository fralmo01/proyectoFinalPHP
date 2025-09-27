<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Convocatorias</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --admin-spacing: 24px;
            --admin-bg: #f1f5f9;
            --admin-card-bg: #ffffff;
            --admin-primary: #4f46e5;
            --admin-primary-dark: #4338ca;
            --admin-danger: #dc2626;
            --admin-success: #16a34a;
            --admin-muted: #64748b;
            --admin-border: #e2e8f0;
            --admin-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        }

        body {
            margin: 0;
            background-color: var(--admin-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #0f172a;
        }

        .admin-main {
            margin-left: calc(var(--admin-sidebar-width, 260px) + 20px);
            padding: 40px var(--admin-spacing);
        }

        .admin-header {
            margin-bottom: 30px;
        }

        .admin-header h1 {
            margin: 0 0 10px;
            font-size: 2rem;
            font-weight: 700;
        }

        .admin-header p {
            margin: 0;
            color: var(--admin-muted);
        }

        .alerts {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 24px;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: var(--admin-shadow);
        }

        .alert-success {
            background-color: rgba(22, 163, 74, 0.12);
            color: #166534;
        }

        .alert-error {
            background-color: rgba(220, 38, 38, 0.12);
            color: #b91c1c;
        }

        .filters-card {
            background: var(--admin-card-bg);
            border-radius: 18px;
            padding: 24px;
            margin-bottom: 32px;
            box-shadow: var(--admin-shadow);
        }

        .filters-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .filters-title i {
            color: var(--admin-primary);
        }

        .filters-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 600;
            color: #1e293b;
        }

        .form-group input,
        .form-group select {
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid var(--admin-border);
            background-color: #f8fafc;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        .filters-actions {
            display: flex;
            align-items: flex-end;
            gap: 14px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            padding: 12px 18px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-dark));
            color: #fff;
        }

        .btn-secondary {
            background: #fff;
            color: var(--admin-primary);
            border: 1px solid var(--admin-border);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
        }

        .btn-small {
            padding: 10px 16px;
            font-size: 0.9rem;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
        }

        .btn-success {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }

        .conv-card {
            background: var(--admin-card-bg);
            border-radius: 18px;
            box-shadow: var(--admin-shadow);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            min-height: 320px;
        }

        .conv-header {
            display: flex;
            align-items: center;
            padding: 22px 22px 18px;
            gap: 16px;
            border-bottom: 1px solid var(--admin-border);
        }

        .conv-logo {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            object-fit: cover;
            border: 1px solid var(--admin-border);
            padding: 6px;
            background: #fff;
        }

        .conv-company {
            font-size: 0.95rem;
            color: var(--admin-muted);
            margin-bottom: 6px;
        }

        .conv-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
        }

        .status-badge {
            margin-left: auto;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-active {
            background: rgba(22, 163, 74, 0.15);
            color: #15803d;
        }

        .status-inactive {
            background: rgba(220, 38, 38, 0.15);
            color: #b91c1c;
        }

        .conv-body {
            padding: 20px 22px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .conv-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: var(--admin-muted);
        }

        .conv-description {
            font-size: 0.9rem;
            color: var(--admin-muted);
            line-height: 1.5;
        }

        .conv-footer {
            padding: 0 22px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .date-range {
            font-size: 0.85rem;
            color: var(--admin-muted);
        }

        .empty-state {
            background: var(--admin-card-bg);
            border-radius: 18px;
            padding: 40px;
            text-align: center;
            box-shadow: var(--admin-shadow);
        }

        .empty-state i {
            font-size: 2rem;
            color: var(--admin-muted);
            margin-bottom: 12px;
        }

        @media (max-width: 900px) {
            .admin-main {
                margin-left: 0;
                padding-top: 120px;
            }
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../../layout/fondo.php'; ?>
    <?php include __DIR__ . '/../../layout/menu_Administrador.php'; ?>

    <main class="admin-main">
        <header class="admin-header">
            <h1>Gestión de convocatorias</h1>
            <p>Revisa, filtra y administra las convocatorias publicadas por todas las empresas.</p>
        </header>

        <?php if (!empty($mensaje) || !empty($error)): ?>
            <div class="alerts">
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-circle-exclamation"></i>
                        <?= htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <section class="filters-card">
            <div class="filters-title">
                <i class="fas fa-filter"></i>
                <span>Filtrar convocatorias</span>
            </div>
            <form method="get" action="index.php" class="filters-form">
                <input type="hidden" name="controller" value="admin">
                <input type="hidden" name="action" value="convocatorias">

                <div class="form-group">
                    <label for="buscar">Buscar</label>
                    <input type="text" name="buscar" id="buscar" placeholder="Título o empresa" value="<?= htmlspecialchars($buscar ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="modalidad">Modalidad</label>
                    <select name="modalidad" id="modalidad">
                        <option value="">Todas</option>
                        <?php foreach ($modalidades as $modalidad): ?>
                            <option value="<?= $modalidad['idModalidad']; ?>" <?= ($idModalidad ?? null) === (int) $modalidad['idModalidad'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($modalidad['nombreModalidad']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="jornada">Jornada</label>
                    <select name="jornada" id="jornada">
                        <option value="">Todas</option>
                        <?php foreach ($jornadas as $jornada): ?>
                            <option value="<?= $jornada['idJornada']; ?>" <?= ($idJornada ?? null) === (int) $jornada['idJornada'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($jornada['nombreJornada']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado">
                        <option value="">Todos</option>
                        <option value="1" <?= ($estadoFiltro ?? '') === 1 ? 'selected' : ''; ?>>Activas</option>
                        <option value="0" <?= ($estadoFiltro ?? '') === 0 ? 'selected' : ''; ?>>Inactivas</option>
                    </select>
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Aplicar filtros
                    </button>
                    <a href="index.php?controller=admin&action=convocatorias" class="btn btn-secondary">
                        <i class="fas fa-rotate-left"></i>
                        Limpiar
                    </a>
                </div>
            </form>
        </section>

        <?php if (!empty($convocatorias)): ?>
            <section class="cards-grid">
                <?php foreach ($convocatorias as $convocatoria): ?>
                    <?php
                        $logo = !empty($convocatoria['logoEmpresa'])
                            ? 'fotos/empresalogo/' . $convocatoria['logoEmpresa']
                            : 'fotos/empresalogo/default_logo.png';
                        $descripcion = trim($convocatoria['descripcion'] ?? '');
                        $descripcion = $descripcion !== '' ? $descripcion : 'La empresa no proporcionó una descripción detallada.';
                        $resumen = function_exists('mb_strimwidth')
                            ? mb_strimwidth($descripcion, 0, 180, '...')
                            : (strlen($descripcion) > 180 ? substr($descripcion, 0, 177) . '...' : $descripcion);
                        $estaActiva = (int) ($convocatoria['estado'] ?? 0) === 1;
                    ?>
                    <article class="conv-card">
                        <div class="conv-header">
                            <img src="<?= htmlspecialchars($logo); ?>" alt="Logo de empresa" class="conv-logo">
                            <div>
                                <div class="conv-company"><?= htmlspecialchars($convocatoria['empresaNombre']); ?></div>
                                <h2 class="conv-title"><?= htmlspecialchars($convocatoria['titulo']); ?></h2>
                            </div>
                            <div class="status-badge <?= $estaActiva ? 'status-active' : 'status-inactive'; ?>">
                                <i class="fas fa-circle"></i>
                                <?= $estaActiva ? 'Activa' : 'Inactiva'; ?>
                            </div>
                        </div>
                        <div class="conv-body">
                            <div class="conv-details">
                                <div class="detail-item">
                                    <i class="fas fa-briefcase"></i>
                                    <span><?= htmlspecialchars($convocatoria['nombreModalidad']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-business-time"></i>
                                    <span><?= htmlspecialchars($convocatoria['nombreJornada']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-calendar-day"></i>
                                    <span>Inicio: <?= htmlspecialchars($convocatoria['fechaInicio']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <span>Fin: <?= htmlspecialchars($convocatoria['fechaFin']); ?></span>
                                </div>
                            </div>
                            <div class="conv-description">
                                <?= nl2br(htmlspecialchars($resumen)); ?>
                            </div>
                        </div>
                        <div class="conv-footer">
                            <div class="date-range">
                                Creada el <?= htmlspecialchars(substr($convocatoria['fechaCreacion'], 0, 10)); ?>
                            </div>
                            <form method="post" action="index.php?controller=admin&action=actualizarEstadoConvocatoria">
                                <input type="hidden" name="idConvocatoria" value="<?= (int) $convocatoria['idConvocatoria']; ?>">
                                <input type="hidden" name="estado" value="<?= $estaActiva ? 0 : 1; ?>">
                                <input type="hidden" name="f_modalidad" value="<?= htmlspecialchars($idModalidad ?? ''); ?>">
                                <input type="hidden" name="f_jornada" value="<?= htmlspecialchars($idJornada ?? ''); ?>">
                                <input type="hidden" name="f_estado" value="<?= htmlspecialchars($estadoFiltro ?? ''); ?>">
                                <input type="hidden" name="f_buscar" value="<?= htmlspecialchars($buscar ?? ''); ?>">
                                <button type="submit" class="btn btn-small <?= $estaActiva ? 'btn-danger' : 'btn-success'; ?>">
                                    <i class="fas <?= $estaActiva ? 'fa-ban' : 'fa-check'; ?>"></i>
                                    <?= $estaActiva ? 'Desactivar' : 'Activar'; ?>
                                </button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h2>No se encontraron convocatorias</h2>
                <p>Ajusta los filtros o intenta nuevamente más tarde.</p>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>