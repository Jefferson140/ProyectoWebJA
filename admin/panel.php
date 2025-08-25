<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
proteger_ruta_admin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-100 to-yellow-100">
    <header class="bg-blue-900 text-white p-6 flex justify-between items-center shadow-lg">
        <span class="font-extrabold text-2xl tracking-wide flex items-center gap-2">
            <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v4a1 1 0 001 1h3m10-5h3a1 1 0 011 1v4a1 1 0 01-1 1h-3m-10 0v4a1 1 0 001 1h3m10-5h3a1 1 0 011 1v4a1 1 0 01-1 1h-3" /></svg>
            Panel de Administrador
        </span>
        <div class="flex gap-3">
            <a href="../index.php" class="bg-yellow-400 text-blue-900 px-4 py-2 rounded-lg font-bold shadow hover:bg-yellow-300 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" /></svg>
                Ir al inicio
            </a>
            <a href="../logout.php" class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold shadow hover:bg-red-700 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7" /></svg>
                Cerrar sesión
            </a>
        </div>
    </header>
    <main class="flex justify-center items-center py-16">
        <div class="bg-white rounded-2xl shadow-2xl p-10 w-full max-w-4xl border border-blue-100">
            <h1 class="text-3xl font-extrabold mb-10 text-blue-900 text-center tracking-wide">Bienvenido, Administrador</h1>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
                <a href="configuracion.php" class="group block bg-yellow-400 text-blue-900 font-bold p-6 rounded-xl shadow-lg text-center hover:bg-yellow-300 transition transform hover:-translate-y-1 flex flex-col items-center gap-2">
                    <svg class="w-8 h-8 mb-2 group-hover:scale-110 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Personalizar Página
                </a>
                <a href="usuarios.php" class="group block bg-blue-900 text-white font-bold p-6 rounded-xl shadow-lg text-center hover:bg-blue-800 transition transform hover:-translate-y-1 flex flex-col items-center gap-2">
                    <svg class="w-8 h-8 mb-2 group-hover:scale-110 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20h6M3 20h5v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 010 7.75" /></svg>
                    Gestionar Usuarios
                </a>
                <a href="../agente/propiedades.php" class="group block bg-gray-200 text-blue-900 font-bold p-6 rounded-xl shadow-lg text-center hover:bg-gray-300 transition transform hover:-translate-y-1 flex flex-col items-center gap-2">
                    <svg class="w-8 h-8 mb-2 group-hover:scale-110 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v4a1 1 0 001 1h3m10-5h3a1 1 0 011 1v4a1 1 0 01-1 1h-3m-10 0v4a1 1 0 001 1h3m10-5h3a1 1 0 011 1v4a1 1 0 01-1 1h-3" /></svg>
                    Gestionar Propiedades
                </a>
                <a href="../propiedades/listar.php" class="group block bg-blue-900 text-white font-bold p-6 rounded-xl shadow-lg text-center hover:bg-blue-800 transition transform hover:-translate-y-1 flex flex-col items-center gap-2">
                    <svg class="w-8 h-8 mb-2 group-hover:scale-110 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6" /></svg>
                    Ver Propiedades
                </a>
                <a href="datos.php" class="group block bg-gray-100 text-blue-900 font-bold p-6 rounded-xl shadow-lg text-center hover:bg-gray-200 transition transform hover:-translate-y-1 flex flex-col items-center gap-2">
                    <svg class="w-8 h-8 mb-2 group-hover:scale-110 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.657 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Mis Datos
                </a>
            </div>
        </div>
    </main>
</body>
</html>
