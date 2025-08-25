<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
proteger_ruta_agente();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Agente</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-900 text-white p-4 flex justify-between items-center">
        <span class="font-bold text-lg">Panel de Agente</span>
        <div class="flex gap-2">
            <a href="../index.php" class="bg-yellow-400 text-blue-900 px-3 py-1 rounded font-bold">Ir al inicio</a>
            <a href="../logout.php" class="bg-red-600 text-white px-3 py-1 rounded">Cerrar sesión</a>
        </div>
    </header>
    <?php
    require_once '../includes/db.php';
    $config = $conn->query("SELECT * FROM configuracion WHERE id=1")->fetch_assoc();
    $color_principal = $config['color_principal'] ?? '#25344b';
    ?>
    <main class="max-w-3xl mx-auto py-8">
        <section style="background-color: <?= $color_principal ?>; color: #fff;" class="py-12 rounded-lg mb-8">
            <h2 class="text-3xl font-bold mb-6 text-center tracking-wide">PROPIEDADES EN ALQUILER</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="propiedades.php" class="block bg-blue-900 text-white font-bold p-4 rounded shadow text-center hover:bg-blue-800">Gestionar Alquiler</a>
                <a href="datos.php" class="block bg-gray-200 text-blue-900 font-bold p-4 rounded shadow text-center hover:bg-gray-300">Mis Datos</a>
            </div>
        </section>
    </main>
</body>
</html>
