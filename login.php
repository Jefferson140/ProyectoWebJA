<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/session.php';
// La sesión solo se cierra desde logout.php o el botón de los paneles
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    $stmt = $conn->prepare('SELECT * FROM usuarios WHERE usuario = ? LIMIT 1');
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if (verificar_contrasena($contrasena, $user['contrasena'])) {
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['privilegio'] = $user['privilegio'];
            $_SESSION['id'] = $user['id'];
            if ($user['privilegio'] === 'admin' || $user['privilegio'] === 'administrador') {
                redirigir('admin/panel.php');
            } elseif ($user['privilegio'] === 'agente') {
                redirigir('agente/panel.php');
            } else {
                redirigir('index.php');
            }
        } else {
            $error = 'Contraseña incorrecta.';
        }
    } else {
        $error = 'Usuario no encontrado.';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UTN Solutions Real State</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-900 via-blue-700 to-blue-400">
    <div class="bg-white/90 rounded-2xl shadow-2xl p-10 w-full max-w-md backdrop-blur-md border border-blue-200 animate-fade-in">
        <div class="flex items-center gap-3 mb-8 justify-center">
            <img src="img/logo.png" alt="Logo" class="h-12 drop-shadow-lg">
            <span class="font-extrabold text-2xl text-blue-900 leading-tight tracking-wide">UTN SOLUTIONS<br><span class="text-blue-600">REAL STATE</span></span>
        </div>
        <h2 class="text-3xl font-extrabold mb-6 text-blue-900 text-center">Iniciar Sesión</h2>
        <?php if($error): ?><div class="bg-red-100 text-red-700 p-3 mb-4 rounded-lg text-center shadow"><?= $error ?></div><?php endif; ?>
        <form method="post" class="space-y-6">
            <div class="relative">
                <label class="block mb-2 font-semibold text-blue-900">Usuario</label>
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9.001 9.001 0 0112 15c2.21 0 4.21.805 5.879 2.146M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </span>
                <input type="text" name="usuario" class="w-full pl-10 pr-3 py-2 border-2 border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
            </div>
            <div class="relative">
                <label class="block mb-2 font-semibold text-blue-900">Contraseña</label>
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.104 0 2-.896 2-2V7a2 2 0 10-4 0v2c0 1.104.896 2 2 2zm6 2v5a2 2 0 01-2 2H8a2 2 0 01-2-2v-5a6 6 0 1112 0z" /></svg>
                </span>
                <input type="password" name="contrasena" class="w-full pl-10 pr-3 py-2 border-2 border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
            </div>
            <button type="submit" class="bg-gradient-to-r from-blue-700 to-blue-900 text-white px-6 py-3 rounded-lg w-full font-bold shadow-lg hover:scale-105 hover:from-blue-800 hover:to-blue-950 transition-transform duration-200">Ingresar</button>
        </form>
        <a href="index.php" class="block text-center mt-6 text-blue-700 font-semibold hover:underline">Volver al inicio</a>
    </div>
    <style>
        @keyframes fade-in { from { opacity: 0; transform: translateY(20px);} to { opacity: 1; transform: none; } }
        .animate-fade-in { animation: fade-in 0.8s cubic-bezier(.4,0,.2,1) both; }
    </style>
</body>
</html>