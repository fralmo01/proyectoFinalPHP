<?php 
// views/vistas/inicio.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bolsa de Trabajo - Inicio</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Fondo animado -->
    <?php include __DIR__ . "/../layout/fondo.php"; ?>

    <!-- Barra de navegación -->
    <?php include __DIR__ . "/../layout/menuinicio.php"; ?>

    <!-- Contenido principal -->
    <div class="container mt-5 text-center">
        <h1 class="mb-4">Bienvenido a la Bolsa de Trabajo</h1>
        <p class="lead">Conecta empresas y postulantes de manera rápida y sencilla.</p>
    </div>

</body>
</html>
