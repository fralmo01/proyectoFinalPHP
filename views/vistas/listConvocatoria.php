<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convocatorias</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --light: #f8fafc;
            --dark: #1e293b;
            --success: #10b981;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            background-color: #f1f5f9;
            color: #334155;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            z-index: 1;
        }

        /* Contenedor principal con z-index mayor */
        .contenedor-principal {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
            z-index: 2;
            /* Z-index mayor que el fondo */
            background-color: rgba(255, 255, 255, 0);
            /* Fondo transparente */
        }

        /* Header */
        .page-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
        }

        .page-header h1 {
            font-size: 2.5rem;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .page-header p {
            font-size: 1.1rem;
            color: var(--secondary);
            max-width: 700px;
            margin: 0 auto;
        }

        /* Filtros */
        .filters-container {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--card-shadow);
            position: relative;
            z-index: 2;
        }

        .filters-title {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: var(--dark);
        }

        .filters-title i {
            margin-right: 10px;
            color: var(--primary);
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }

        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: var(--light);
            transition: var(--transition);
        }

        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        .form-actions {
            display: flex;
            align-items: flex-end;
            gap: 15px;
        }

        /* Botones */
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--secondary);
            color: var(--secondary);
        }

        .btn-outline:hover {
            background-color: var(--secondary);
            color: white;
        }

        /* Convocatorias */
        .convocatorias-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .convocatoria-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            position: relative;
            z-index: 2;
        }

        .convocatoria-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .company-logo {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
            border: 1px solid #eee;
            padding: 5px;
            background: white;
        }

        .company-name {
            font-size: 0.9rem;
            color: var(--secondary);
            margin-bottom: 5px;
        }

        .convocatoria-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
        }

        .card-body {
            padding: 20px;
        }

        .convocatoria-details {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .detail-item i {
            margin-right: 5px;
            color: var(--primary);
        }

        .convocatoria-desc {
            margin-bottom: 20px;
            color: var(--secondary);
            line-height: 1.6;
            word-break: break-word;
        }

        .date-info {
            background-color: #f8fafc;
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 0.9rem;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }

        .card-footer {
            padding: 0 20px 20px;
        }

        .btn-apply {
            width: 100%;
            background-color: var(--success);
            color: white;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            font-weight: 600;
            display: block;
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-apply:hover {
            background-color: #0d9669;
        }

        /* No results */
        .no-results {
            text-align: center;
            padding: 40px;
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            grid-column: 1 / -1;
            position: relative;
            z-index: 2;
        }

        .no-results i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 15px;
        }

        .no-results h3 {
            color: var(--dark);
            margin-bottom: 10px;
        }

        .no-results p {
            color: var(--secondary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .filter-form {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .convocatorias-list {
                grid-template-columns: 1fr;
            }

            .page-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../layout/fondo.php'; ?>
    <?php include __DIR__ . '/../layout/menuinicio.php'; ?>

    <!-- Aquí uso contenedor-principal en lugar de container -->
    <div class="contenedor-principal">
        <header class="page-header">
            <h1>Convocatorias disponibles</h1>
            <p>Explora las vacantes activas y conoce los detalles antes de postular. Para completar tu postulación debes
                iniciar sesión.</p>
        </header>

        <div class="filters-container">
            <div class="filters-title">
                <i class="fas fa-filter"></i>
                <h2>Filtrar convocatorias</h2>
            </div>

            <form method="get" action="index.php" class="filter-form">
                <input type="hidden" name="controller" value="convocatoria">
                <input type="hidden" name="action" value="listar">

                <div class="form-group">
                    <label for="modalidad">Modalidad</label>
                    <select name="modalidad" id="modalidad">
                        <option value="">Todas las modalidades</option>
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
                        <option value="">Todas las jornadas</option>
                        <?php foreach ($jornadas as $jornada): ?>
                            <option value="<?= $jornada['idJornada']; ?>" <?= ($idJornada ?? null) === (int) $jornada['idJornada'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($jornada['nombreJornada']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="index.php?controller=convocatoria&action=listar" class="btn btn-outline">
                        <i class="fas fa-times"></i> Limpiar filtros
                    </a>
                </div>
            </form>
        </div>

        <div class="convocatorias-list">
            <?php if (!empty($convocatorias)): ?>
                <?php foreach ($convocatorias as $convocatoria): ?>
                    <article class="convocatoria-card">
                        <div class="card-header">
                            <?php
                            $logo = !empty($convocatoria['logoEmpresa'])
                                ? 'fotos/empresalogo/' . $convocatoria['logoEmpresa']
                                : 'fotos/empresalogo/default_logo.png';
                            ?>
                            <img src="<?= htmlspecialchars($logo); ?>" alt="Logo de empresa" class="company-logo">
                            <div class="company-info">
                                <div class="company-name"><?= htmlspecialchars($convocatoria['empresaNombre']); ?></div>
                                <h3 class="convocatoria-title"><?= htmlspecialchars($convocatoria['titulo']); ?></h3>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="convocatoria-details">
                                <div class="detail-item"><i
                                        class="fas fa-business-time"></i><span><?= htmlspecialchars($convocatoria['nombreJornada']); ?></span>
                                </div>
                                <div class="detail-item"><i
                                        class="fas fa-location-dot"></i><span><?= htmlspecialchars($convocatoria['nombreModalidad']); ?></span>
                                </div>
                            </div>

                            <div class="convocatoria-desc">
                                <?php
                                $descripcion = trim($convocatoria['descripcion'] ?? '');
                                $descripcion = $descripcion !== '' ? $descripcion : 'La empresa no proporcionó una descripción detallada.';
                                $resumen = function_exists('mb_strimwidth')
                                    ? mb_strimwidth($descripcion, 0, 180, '...')
                                    : (strlen($descripcion) > 180 ? substr($descripcion, 0, 177) . '...' : $descripcion);
                                echo nl2br(htmlspecialchars($resumen));
                                ?>
                            </div>

                            <div class="date-info">
                                <div><i class="fas fa-play-circle"></i> <strong>Inicio:</strong>
                                    <?= htmlspecialchars($convocatoria['fechaInicio']); ?></div>
                                <div><i class="fas fa-flag-checkered"></i> <strong>Fin:</strong>
                                    <?= htmlspecialchars($convocatoria['fechaFin']); ?></div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <a href="index.php?controller=usuario&action=login" class="btn-apply">
                                <i class="fas fa-paper-plane"></i> Postular
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>No se encontraron convocatorias</h3>
                    <p>Intenta ajustar los criterios de búsqueda o limpiar los filtros.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>