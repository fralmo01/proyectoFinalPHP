<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuario = $usuario ?? ['estado' => 1];
$roles = $roles ?? [];
$empresas = $empresas ?? [];
$errores = $errores ?? [];
$esEdicion = !empty($usuario['idUsuario']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $esEdicion ? 'Editar usuario' : 'Crear usuario' ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #f4f6fb;
            
        }

        .admin-main {
            margin-left: 260px;
            padding: 40px;
            z-index: 10;
        }

        @media (max-width: 900px) {
            .admin-main {
                margin-left: 0;
                padding: 100px 20px 40px;
            }
        }

        .form-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 32px;
            max-width: 900px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.15);
            position: relative;
            z-index: 10;
        }

        .form-card h1 {
            margin-top: 0;
            margin-bottom: 24px;
            font-size: 30px;
            color: #1f2937;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        label {
            display: flex;
            flex-direction: column;
            font-weight: 600;
            color: #374151;
            gap: 8px;
        }

        input,
        select {
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            font-size: 14px;
            transition: border 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus,
        select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            outline: none;
            background-color: #fff;
        }

        .form-actions {
            margin-top: 28px;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .btn-primary,
        .btn-secondary {
            padding: 12px 18px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.35);
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #111827;
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 16px 18px;
            margin-bottom: 20px;
        }

        .alert-error ul {
            margin: 0;
            padding-left: 20px;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . "/../../layout/fondo.php"; ?>
    <?php include __DIR__ . "/../../layout/menu_Administrador.php"; ?>

    <main class="admin-main">
        <div class="form-card">
            <h1><?= $esEdicion ? 'Editar usuario' : 'Crear nuevo usuario' ?></h1>

            <?php if (!empty($errores)): ?>
                <div class="alert-error">
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul>
                        <?php foreach ($errores as $mensaje): ?>
                            <li><?= htmlspecialchars($mensaje) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="index.php?controller=admin&action=<?= $esEdicion ? 'actualizarUsuario' : 'guardarUsuario' ?>">
                <?php if ($esEdicion): ?>
                    <input type="hidden" name="idUsuario" value="<?= (int)$usuario['idUsuario'] ?>">
                <?php endif; ?>
                <div class="form-grid">
                    <label>
                        Nombre
                        <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" required>
                    </label>
                    <label>
                        Apellido paterno
                        <input type="text" name="apellidoPaterno" value="<?= htmlspecialchars($usuario['apellidoPaterno'] ?? '') ?>">
                    </label>
                    <label>
                        Apellido materno
                        <input type="text" name="apellidoMaterno" value="<?= htmlspecialchars($usuario['apellidoMaterno'] ?? '') ?>">
                    </label>
                    <label>
                        Usuario
                        <input type="text" name="usuario" value="<?= htmlspecialchars($usuario['usuario'] ?? '') ?>" required>
                    </label>
                    <label>
                        Correo electrónico
                        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required>
                    </label>
                    <label>
                        Teléfono
                        <input type="text" name="telefono" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
                    </label>
                    <label>
                        Dirección
                        <input type="text" name="direccion" value="<?= htmlspecialchars($usuario['direccion'] ?? '') ?>">
                    </label>
                    <label>
                        Rol
                        <select name="idRol" id="selectRol" required onchange="actualizarSeccionEmpresa()">
                            <option value="">Selecciona un rol</option>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?= (int)$rol['idRol'] ?>" data-role-name="<?= htmlspecialchars(strtolower($rol['nombre'])) ?>" <?= ((int)($usuario['idRol'] ?? 0) === (int)$rol['idRol']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($rol['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>
                        Estado
                        <select name="estado">
                            <option value="1" <?= (int)($usuario['estado'] ?? 1) === 1 ? 'selected' : '' ?>>Activo</option>
                            <option value="0" <?= (int)($usuario['estado'] ?? 1) === 0 ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </label>
                    <label>
                        Contraseña <?= $esEdicion ? '(dejar en blanco para no cambiar)' : '' ?>
                        <input type="password" name="clave" <?= $esEdicion ? '' : 'required' ?>>
                    </label>
                </div>

                <div id="seccionEmpresa" class="form-grid <?= ((int)($usuario['idRol'] ?? 0) === 2) ? '' : 'hidden' ?>" style="margin-top: 24px;">
                    <label>
                        Empresa asociada
                        <select name="idEmpresa" id="selectEmpresa">
                            <option value="">Selecciona una empresa</option>
                            <?php foreach ($empresas as $empresa): ?>
                                <option value="<?= (int)$empresa['idEmpresa'] ?>" <?= ((int)($usuario['idEmpresa'] ?? 0) === (int)$empresa['idEmpresa']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($empresa['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary"><?= $esEdicion ? 'Actualizar usuario' : 'Crear usuario' ?></button>
                    <a class="btn-secondary" href="index.php?controller=admin&action=usuarios">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        function actualizarSeccionEmpresa() {
            const selectRol = document.getElementById('selectRol');
            const seccionEmpresa = document.getElementById('seccionEmpresa');
            if (!selectRol || !seccionEmpresa) {
                return;
            }

            const selectedOption = selectRol.options[selectRol.selectedIndex];
            const roleName = selectedOption ? selectedOption.dataset.roleName : '';

            if (roleName === 'empresa') {
                seccionEmpresa.classList.remove('hidden');
            } else {
                seccionEmpresa.classList.add('hidden');
                const selectEmpresa = document.getElementById('selectEmpresa');
                if (selectEmpresa) {
                    selectEmpresa.value = '';
                }
            }
        }

        actualizarSeccionEmpresa();
    </script>
</body>
</html>