<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';
if (!es_agente()) {
    redirigir('../login.php');
}
$id = $_SESSION['id'];
$usuario = $conn->query("SELECT nombre FROM usuarios WHERE id=$id")->fetch_assoc();
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
    <header class="bg-blue-900 text-white p-6 flex justify-between items-center shadow-lg">
        <span class="font-extrabold text-2xl tracking-wide flex items-center gap-2">
            <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v4a1 1 0 001 1h3m10-5h3a1 1 0 011 1v4a1 1 0 01-1 1h-3m-10 0v4a1 1 0 001 1h3m10-5h3a1 1 0 011 1v4a1 1 0 01-1 1h-3" /></svg>
            Panel de Agente
        </span>
        <div class="flex gap-3">
            <a href="../index.php" class="bg-yellow-400 text-blue-900 px-5 py-2 rounded-xl font-bold shadow hover:bg-yellow-300 transition">Ir al inicio</a>
            <a href="../logout.php" class="bg-red-600 text-white px-5 py-2 rounded-xl font-bold shadow hover:bg-red-700 transition">Cerrar sesión</a>
        </div>
    </header>
    <main class="max-w-4xl mx-auto py-12">
        <div class="bg-white rounded-2xl shadow-2xl p-10 border border-gray-200">
            <h1 class="text-3xl font-extrabold mb-10 text-blue-900 text-center drop-shadow">
                Bienvenido, Agente<?= isset($usuario['nombre']) ? ', ' . htmlspecialchars($usuario['nombre']) : '' ?>
            </h1>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <a href="propiedades.php" class="flex flex-col items-center justify-center bg-blue-900 text-white font-bold p-8 rounded-xl shadow-lg hover:bg-blue-800 transition gap-2">
                    <svg class="w-10 h-10 text-yellow-400 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v4a1 1 0 001 1h3m10-5h3a1 1 0 011 1v4a1 1 0 01-1 1h-3m-10 0v4a1 1 0 001 1h3m10-5h3a1 1 0 011 1v4a1 1 0 01-1 1h-3" /></svg>
                    Gestionar Propiedades
                </a>
                <a href="datos.php" class="flex flex-col items-center justify-center bg-gray-100 text-blue-900 font-bold p-8 rounded-xl shadow-lg hover:bg-gray-200 transition gap-2">
                    <svg class="w-10 h-10 text-blue-900 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.657 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Mis Datos
                </a>
                <a href="../propiedades/listar.php?tipo=alquiler" class="flex flex-col items-center justify-center bg-yellow-400 text-blue-900 font-bold p-8 rounded-xl shadow-lg hover:bg-yellow-300 transition gap-2">
                    <svg class="w-10 h-10 text-blue-900 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6" /></svg>
                    Ver Alquileres
                </a>
                <a href="../propiedades/listar.php?tipo=venta" class="flex flex-col items-center justify-center bg-gray-100 text-blue-900 font-bold p-8 rounded-xl shadow-lg hover:bg-gray-200 transition gap-2">
                    <svg class="w-10 h-10 text-yellow-400 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 1.343-3 3v4a3 3 0 006 0v-4c0-1.657-1.343-3-3-3z" /></svg>
                    Ver Ventas
                </a>
            </div>
        </div>
    </main>
</body>
</html>
