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

<?php
$color_principal = isset($color_principal) ? $color_principal : '#25344b';
$color_secundario = isset($color_secundario) ? $color_secundario : '#ffe600';
?>
<body class="min-h-screen flex items-center justify-center" 
      style="background: linear-gradient(135deg, <?php echo $color_principal; ?> 60%, <?php echo $color_secundario; ?> 100%);">

    <div class="bg-white/90 rounded-2xl shadow-2xl p-10 w-full max-w-md backdrop-blur-md border border-[<?php echo $color_secundario; ?>] animate-fade-in">
        
        <!-- Logo + Título -->
        <div class="flex flex-col items-center mb-8 text-center">
            <img src="img/logo.png" alt="Logo" class="h-20 drop-shadow-lg mb-3">
            <h1 class="text-4xl font-extrabold bg-gradient-to-r from-black via-gray-700 to-black bg-clip-text text-transparent drop-shadow-lg">
                UTN REAL STATE
            </h1>
        </div>

        <!-- Subtítulo -->
        <h2 class="text-2xl font-bold mb-6 text-center" style="color: <?php echo $color_principal; ?>;">
            Iniciar Sesión
        </h2>

        <!-- Errores -->
        <?php if($error): ?>
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded-lg text-center shadow">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <form method="post" class="space-y-6">
            <!-- Usuario -->
            <div class="relative">
                <label class="block mb-2 font-semibold text-black">Usuario</label>
                <input type="text" name="usuario" 
                       class="w-full pl-3 pr-3 py-3 border-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 text-black placeholder-gray-600 bg-gray-100"
                       placeholder="Ingrese su usuario"
                       style="border-color: <?php echo $color_secundario; ?>;"
                       required>
            </div>

            <!-- Contraseña -->
            <div class="relative">
                <label class="block mb-2 font-semibold text-black">Contraseña</label>
                <input type="password" name="contrasena" 
                       class="w-full pl-3 pr-3 py-3 border-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 text-black placeholder-gray-600 bg-gray-100"
                       placeholder="Ingrese su contraseña"
                       style="border-color: <?php echo $color_secundario; ?>;"
                       required>
            </div>

            <!-- Botón -->
            <button type="submit" 
                    class="w-full font-bold shadow-lg px-6 py-3 rounded-lg transition-transform duration-200 hover:scale-105"
                    style="background: linear-gradient(90deg, <?php echo $color_secundario; ?> 0%, <?php echo $color_principal; ?> 100%); color: #fff;">
                Ingresar
            </button>
        </form>
    </div>

    <style>
        @keyframes fade-in { 
            from { opacity: 0; transform: translateY(20px);} 
            to { opacity: 1; transform: none; } 
        }
        .animate-fade-in { animation: fade-in 0.8s cubic-bezier(.4,0,.2,1) both; }
    </style>
</body>
</html>
