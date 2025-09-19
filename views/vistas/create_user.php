<?php  
$tipo = $_GET['tipo'] ?? 'postulante'; 
include __DIR__ . '/../layout/fondo.php'; 
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .form-container {
        width: 90%;
        max-width: 500px;
        margin: 40px auto;
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
        margin-bottom: 20px;
        color: #333;
    }

    .form-container label {
        font-weight: bold;
        display: block;
        margin-bottom: 8px;
        text-align: left;
    }

    .form-container select,
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

    .form-container input:focus,
    .form-container select:focus {
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
</style>

<div class="form-container">
    <h2>Crear Cuenta</h2>

    <form method="POST" action="index.php?controller=usuario&action=store">
        <!-- Selector de rol -->
        <label for="rol">Tipo de cuenta:</label>
        <select id="rol" name="rol" onchange="cambiarCampos()" required>
            <option value="">-- Selecciona --</option>
            <option value="postulante" <?= $tipo === 'postulante' ? 'selected' : '' ?>>Postulante</option>
            <option value="empresa" <?= $tipo === 'empresa' ? 'selected' : '' ?>>Empresa</option>
        </select>

        <!-- Campos comunes -->
        <input type="text" name="nombre" placeholder="Nombre" required maxlength="100"><br>
        <input type="text" name="apellidoPaterno" placeholder="Apellido Paterno" maxlength="100"><br>
        <input type="text" name="apellidoMaterno" placeholder="Apellido Materno" maxlength="100"><br>
        <input type="text" name="usuario" placeholder="Usuario" required maxlength="50"><br>
        <input type="password" name="clave" placeholder="Contraseña" required
               pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$"
               title="Mínimo 8 caracteres, con letras y números"><br>
        <input type="email" name="email" placeholder="Correo electrónico" required maxlength="150"><br>

        <!-- Campos dinámicos -->
        <div id="campos-extra"></div>

        <button type="submit">Registrar</button>
    </form>

    <p>¿Ya tienes cuenta? 
        <a href="index.php?controller=usuario&action=login">Iniciar sesión</a>
    </p>
</div>

<script>
    function cambiarCampos() {
        let rol = document.getElementById("rol").value;
        let camposExtra = "";

        if (rol === "empresa") {
            camposExtra = `
                <h4>Datos de Empresa</h4>
                <input type="text" name="razonSocial" placeholder="Razón Social" required><br>
                <input type="text" name="telefono" placeholder="Teléfono"
                       pattern="[0-9]{9}" maxlength="9"
                       title="Debe contener exactamente 9 dígitos numéricos" required><br>
                <input type="text" name="direccion" placeholder="Dirección"><br>
            `;
        }

        document.getElementById("campos-extra").innerHTML = camposExtra;
    }

    // Ejecutar al cargar la página
    window.onload = cambiarCampos;
</script>

<?php if (isset($mensajes) && !empty($mensajes)): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        html: `<?= implode("<br>", $mensajes) ?>`
    });
</script>
<?php endif; ?>

<?php if (isset($success) && !empty($success)): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '<?= $success ?>',
        confirmButtonText: 'Ir a login'
    }).then(() => {
        window.location.href = "index.php?controller=usuario&action=login";
    });
</script>
<?php endif; ?>
