<?php include __DIR__ . '/../layout/fondo.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .form-container {
        width: 90%;
        max-width: 450px;
        margin: 60px auto;
        padding: 30px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        text-align: center;
    }

    .form-container h2 {
        margin-bottom: 25px;
        color: #333;
        font-size: 26px;
    }

    .form-container input {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 10px;
        border: 1px solid rgba(200, 200, 200, 0.7);
        outline: none;
        background: rgba(255, 255, 255, 0.6);
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-container input:focus {
        border-color: #ff99cc;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 0 8px rgba(255, 153, 204, 0.4);
    }

    .form-container button {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, #ff99cc, #ffccff);
        color: #333;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .form-container button:hover {
        background: linear-gradient(135deg, #ff80b3, #ffb3ff);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(255, 153, 204, 0.3);
    }

    .form-container p {
        margin-top: 15px;
        font-size: 14px;
    }

    .form-container a {
        color: #ff6699;
        text-decoration: none;
        font-weight: bold;
    }

    .form-container a:hover {
        text-decoration: underline;
    }

    .back-home {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 20px;
        color: #ff6699;
        font-weight: 600;
        text-decoration: none;
        transition: transform 0.2s ease, color 0.2s ease;
    }

    .back-home:hover {
        color: #ff4d88;
        transform: translateX(-2px);
        text-decoration: none;
    }
</style>

<div class="form-container">
    <h2>Iniciar Sesión</h2>
    <a class="back-home" href="index.php?controller=home&action=index">
        &#8592; Volver al inicio
    </a>
    <form method="POST" action="index.php?controller=usuario&action=auth">
        <input type="text" name="usuario" placeholder="Usuario" required><br>
        <input type="password" name="clave" placeholder="Contraseña" required><br>
        <button type="submit">Ingresar</button>
    </form>
    <p>¿No tienes cuenta?
        <a href="index.php?controller=usuario&action=create">Registrarse</a>
    </p>
</div>

<?php if (isset($mensajeError)): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error de autenticación',
            text: '<?= $mensajeError ?>',
            confirmButtonColor: '#ff6699'
        });
    </script>
<?php endif; ?>

<?php if (isset($_GET['logout'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Sesión cerrada',
            text: 'Has cerrado sesión correctamente.',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php endif; ?>