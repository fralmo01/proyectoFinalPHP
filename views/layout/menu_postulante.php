<?php
// views/layout/menu_postulante.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nombre completo del postulante
$nombre = $_SESSION['nombre'] ?? '';
$apePat = $_SESSION['apellidoPaterno'] ?? '';
$apeMat = $_SESSION['apellidoMaterno'] ?? '';
$nombreFull = trim("$nombre $apePat $apeMat");
if ($nombreFull === '')
    $nombreFull = 'Postulante';

// Foto de perfil
$foto = $_SESSION['fotoPerfil'] ?? '';
$fsFoto = __DIR__ . "/../../public/fotos/" . $foto;
if ($foto === '' || !is_file($fsFoto)) {
    $foto = "default.jpg";
}
$fotoSrc = "fotos/" . rawurlencode($foto);
?>

<style>
    .menu-bar {
        position: fixed;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 95%;
        background: #ffffffd9;
        backdrop-filter: blur(15px);
        border-radius: 16px;
        padding: 12px 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .nav-center {
        display: flex;
        gap: 18px;
        align-items: center;
    }

    .nav-center a {
        text-decoration: none;
        color: #333;
        font-weight: 500;
        padding: 8px 14px;
        border-radius: 10px;
        transition: all .25s ease;
        background: rgba(255, 255, 255, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .nav-center a:hover {
        background: rgba(200, 230, 255, 0.55);
        transform: translateY(-1px);
    }

    .perfil {
        position: relative;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .perfil img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e5e7eb;
    }

    .perfil-nombre {
        font-weight: 600;
        color: #444;
        max-width: 220px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .perfil-caret {
        font-size: 12px;
        color: #666;
        transition: transform .2s ease;
    }

    .perfil.open .perfil-caret {
        transform: rotate(180deg);
    }

    .dropdown {
        position: absolute;
        top: 56px;
        right: 0;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
        min-width: 230px;
        overflow: hidden;
        border: 1px solid #eef0f4;
        display: none;
        z-index: 2000;
    }

    .dropdown a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        text-decoration: none;
        color: #333;
        font-size: 14px;
        transition: background .2s;
    }

    .dropdown a:hover {
        background: #f5f7fb;
    }
</style>

<div class="menu-bar">
    <div class="nav-center">
        <!-- Convocatorias -->
        <a href="index.php?controller=postulante&action=home">
            <i class="fas fa-briefcase"></i> Convocatorias
        </a>

        <!-- Mis Postulaciones -->
        <a href="index.php?controller=postulante&action=misPostulaciones">
            <i class="fas fa-list"></i> Mis Postulaciones
        </a>

        <!-- Mi Perfil -->
        <a href="index.php?controller=postulante&action=perfil">
            <i class="fas fa-user"></i> Mi Perfil
        </a>
    </div>

    <div class="perfil" id="perfilTrigger">
        <img src="<?= htmlspecialchars($fotoSrc) ?>" alt="Foto perfil">
        <span class="perfil-nombre"><?= htmlspecialchars($nombreFull) ?></span>
        <i class="fas fa-chevron-down perfil-caret"></i>

        <div class="dropdown" id="perfilDropdown">
            <!-- Mis Documentos -->
            <a href="index.php?controller=postulante&action=documentos">
                <i class="fas fa-file-alt"></i> Mis Documentos
            </a>

            <!-- Cerrar sesión -->
            <a href="index.php?controller=usuario&action=logout">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </div>
</div>


<script>
    (function () {
        const trigger = document.getElementById('perfilTrigger');
        const dropdown = document.getElementById('perfilDropdown');

        function toggle(open) {
            if (open === true) {
                dropdown.style.display = 'block';
                trigger.classList.add('open');
            } else if (open === false) {
                dropdown.style.display = 'none';
                trigger.classList.remove('open');
            } else {
                const isOpen = dropdown.style.display === 'block';
                toggle(!isOpen);
            }
        }

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            toggle();
        });
        document.addEventListener('click', function () {
            toggle(false);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') toggle(false);
        });
    })();
</script>