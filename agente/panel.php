<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
if (!es_agente() && !es_admin()) redirigir('../login.php');
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
            <form action="../login.php" method="post">
                <button type="submit" name="logout" class="bg-red-600 text-white px-3 py-1 rounded">Cerrar sesión</button>
            </form>
        </div>
    </header>
    <main class="max-w-3xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6 text-blue-900">
            Bienvenido, <?php echo es_admin() ? 'Administrador' : 'Agente'; ?>
        </h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="propiedades.php" class="block bg-blue-900 text-white font-bold p-4 rounded shadow text-center hover:bg-blue-800">Gestionar Propiedades</a>
            <a href="datos.php" class="block bg-gray-200 text-blue-900 font-bold p-4 rounded shadow text-center hover:bg-gray-300">Mis Datos</a>
        </div>
    </main>
</body>
</html>
