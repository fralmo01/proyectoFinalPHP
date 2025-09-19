<style>
    body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 5px;
        margin: 0;
    }

    .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 90%;
        max-width: 1000px;
        background: #ffffff8a;
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-radius: 16px;
        padding: 15px 25px;
        box-shadow: 0 8px 32px rgba(206, 152, 184, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.4);
        position: relative;
        z-index: 10;
    }

    .nav-center {
        display: flex;
        justify-content: center;
        flex: 1;
    }

    .nav-center>div {
        margin: 0 15px;
    }

    .login-container {
        display: flex;
        gap: 10px;
        margin-left: auto;
    }

    .container a {
        text-decoration: none;
        color: #333;
        font-weight: 500;
        padding: 12px 28px;
        display: inline-block;
        border-radius: 10px;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    .container a:hover {
        background: rgba(255, 228, 237, 0.6);
        box-shadow: 0 6px 12px rgba(206, 152, 184, 0.25);
        transform: translateY(-2px);
    }

    .container a:active {
        transform: translateY(0);
    }

    .inicio a {
        background: rgba(255, 215, 228, 0.35);
    }

    .convocatorias a {
        background: rgba(255, 240, 245, 0.5);
    }

    .buscar a {
        background: rgba(200, 230, 255, 0.5);
    }

    .login a {
        background: rgba(96, 125, 139, 1);
        display: flex;
        align-items: center;
        gap: 8px;
        color: white;
    }

    .crear-cuenta a {
        background: rgba(76, 175, 80, 0.9);
        color: white;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    @media (max-width: 768px) {
        .container {
            flex-direction: column;
            gap: 10px;
        }

        .nav-center {
            flex-direction: column;
        }

        .login-container {
            flex-direction: column;
            width: 100%;
            gap: 5px;
        }
    }
</style>

<div class="container">
    <div class="nav-center">
        <div class="inicio">
            <a href="index.php?controller=home&action=index"><i class="fas fa-home"></i> Inicio</a>
        </div>
        <div class="convocatorias">
            <a href="index.php?controller=convocatoria&action=listar"><i class="fas fa-bullhorn"></i> Convocatorias</a>
        </div>
        <div class="buscar">
            <a href="index.php?controller=usuario&action=create&tipo=empresa">
                <i class="fas fa-search"></i> Buscar Trabajadores
            </a>
        </div>
    </div>
    <div class="login-container">
        <div class="login">
            <a href="index.php?controller=usuario&action=login">
                <i class="fas fa-user"></i> Login
            </a>
        </div>
        <div class="crear-cuenta">
            <a href="index.php?controller=usuario&action=create&tipo=postulante">
                <i class="fas fa-user-plus"></i> Crear cuenta
            </a>
        </div>
    </div>
</div>