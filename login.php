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
<body class="bg-blue-900 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded shadow-lg p-8 w-full max-w-md">
        <div class="flex items-center gap-2 mb-6 justify-center">
            <img src="img/logo.png" alt="Logo" class="h-10">
            <span class="font-bold text-lg text-blue-900 leading-tight">UTN SOLUTIONS<br>REAL STATE</span>
        </div>
        <h2 class="text-2xl font-bold mb-4 text-blue-900 text-center">Iniciar Sesión</h2>
        <?php if($error): ?><div class="bg-red-100 text-red-700 p-2 mb-2 rounded text-center"><?= $error ?></div><?php endif; ?>
        <form method="post" class="space-y-4">
            <div>
                <label class="block mb-1 font-semibold text-blue-900">Usuario:</label>
                <input type="text" name="usuario" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-900" required>
            </div>
            <div>
                <label class="block mb-1 font-semibold text-blue-900">Contraseña:</label>
                <input type="password" name="contrasena" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-900" required>
            </div>
            <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded w-full font-bold">Ingresar</button>
        </form>
        <a href="index.php" class="block text-center mt-4 text-blue-900 font-semibold">Volver al inicio</a>
    </div>
</body>
</html>
