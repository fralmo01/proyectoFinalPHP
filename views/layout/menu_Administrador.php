<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nombre = $_SESSION['nombre'] ?? '';
$apePat = $_SESSION['apellidoPaterno'] ?? '';
$apeMat = $_SESSION['apellidoMaterno'] ?? '';
$nombreCompleto = trim("$nombre $apePat $apeMat");
if ($nombreCompleto === '') {
    $nombreCompleto = 'Administrador';
}
$emailAdmin = $_SESSION['email'] ?? '';
?>

<style>
    :root {
        --admin-sidebar-width: 260px;
    }

    .admin-menu-toggle {
        position: fixed;
        top: 24px;
        left: 24px;
        background: linear-gradient(135deg, rgba(96, 165, 250, 0.95), rgba(99, 102, 241, 0.95));
        color: #fff;
        border: none;
        border-radius: 14px;
        padding: 12px 18px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 12px 32px rgba(99, 102, 241, 0.35);
        z-index: 950;
        display: none;
        align-items: center;
        gap: 10px;
        letter-spacing: 0.4px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .admin-menu-toggle:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 34px rgba(99, 102, 241, 0.45);
    }

    .admin-menu-toggle span {
        font-size: 18px;
        line-height: 1;
    }

    .admin-sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.4);
        z-index: 940;
    }

    .admin-sidebar-overlay.show {
        display: block;
    }

    .admin-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: var(--admin-sidebar-width);
        background: linear-gradient(180deg, rgba(49, 46, 129, 0.94) 0%, rgba(99, 102, 241, 0.9) 55%, rgba(232, 121, 249, 0.9) 100%);
        color: #f8fafc;
        display: flex;
        flex-direction: column;
        padding: 28px 22px 24px;
        box-shadow: 0 15px 45px rgba(79, 70, 229, 0.35);
        z-index: 930;
    }

    .admin-sidebar .brand {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 32px;
        letter-spacing: 0.6px;
    }

    .admin-sidebar .brand span {
        display: block;
        font-size: 13px;
        font-weight: 500;
        opacity: 0.8;
        margin-top: 6px;
    }

    .admin-profile-box {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.08));
        border-radius: 18px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(6px);
    }

    .admin-profile-box .name {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .admin-profile-box .email {
        font-size: 13px;
        opacity: 0.75;
        word-break: break-word;
    }

    .admin-nav {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex: 1;
    }

    .admin-nav a {
        display: block;
        padding: 14px 16px;
        border-radius: 14px;
        color: #f8fafc;
        text-decoration: none;
        font-weight: 600;
        letter-spacing: 0.3px;
        transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        background: rgba(15, 23, 42, 0.12);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .admin-nav a:hover,
    .admin-nav a:focus {
        background: rgba(255, 255, 255, 0.22);
        transform: translateX(8px);
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.25);
    }

    .admin-sidebar-footer {
        margin-top: 20px;
    }

    .admin-sidebar-footer a {
        display: inline-block;
        padding: 40px 30px;
        border-radius: 14px;
        text-decoration: none;
        color: #0f172a;
        background: #f8fafc;
        font-weight: 600;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        letter-spacing: 0.3px;
    }

    .admin-sidebar-footer a:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.25);
    }

    @media (max-width: 900px) {
        .admin-menu-toggle {
            display: inline-flex;
        }

        .admin-sidebar {
            transform: translateX(-105%);
            transition: transform 0.3s ease;
            box-shadow: 0 15px 45px rgba(15, 23, 42, 0.45);
        }

        .admin-sidebar.open {
            transform: translateX(0);
        }
    }
</style>

<button class="admin-menu-toggle" id="adminMenuToggle">
    <span>&#9776;</span> Menú
</button>
<div class="admin-sidebar-overlay" id="adminSidebarOverlay"></div>

<aside class="admin-sidebar" id="adminSidebar">
    <div class="brand">
        Panel Admin
        <span>Control general</span>
    </div>

    <div class="admin-profile-box">
        <div class="name"><?= htmlspecialchars($nombreCompleto) ?></div>
        <?php if (!empty($emailAdmin)): ?>
            <div class="email"><?= htmlspecialchars($emailAdmin) ?></div>
        <?php endif; ?>
    </div>

    <ul class="admin-nav">
        <li>
            <a href="index.php?controller=admin&action=dashboard">Inicio</a>
        </li>
        <li>
            <a href="index.php?controller=admin&action=usuarios">Gestión de Usuarios</a>
        </li>
        <li>
            <a href="index.php?controller=admin&action=empresas">Gestión de Empresas</a>
        </li>
        <li>
            <a href="index.php?controller=admin&action=convocatorias">Gestión de Convocatorias</a>
        </li>
        <li>
            <a href="index.php?controller=admin&action=reportes">Reportes</a>
        </li>
    </ul>

    <div class="admin-sidebar-footer">
        <a href="index.php?controller=usuario&action=logout">Cerrar sesión</a>
    </div>
</aside>

<script>
    (function () {
        const toggle = document.getElementById('adminMenuToggle');
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('adminSidebarOverlay');

        if (!toggle || !sidebar || !overlay) {
            return;
        }

        const openSidebar = () => {
            sidebar.classList.add('open');
            overlay.classList.add('show');
        };

        const closeSidebar = () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        };

        toggle.addEventListener('click', (event) => {
            event.stopPropagation();
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        overlay.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 900) {
                closeSidebar();
            }
        });
    })();
</script>
