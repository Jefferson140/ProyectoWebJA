<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
if (!es_admin()) redirigir('../login.php');
require_once '../includes/db.php';

// Crear usuario administrador por defecto si no existe
$admin_check = $conn->query("SELECT * FROM usuarios WHERE usuario='Admin'");
if ($admin_check->num_rows === 0) {
    $hash = password_hash('123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nombre, telefono, correo, email, usuario, contrasena, privilegio) VALUES ('Administrador', '8890-2030', 'admin@correo.com', 'admin@correo.com', 'Admin', '$hash', 'administrador')";
    $conn->query($sql);
}

// Acciones: crear, editar, eliminar
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errores = [];
    $nombre = $_POST['nombre'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $email = $_POST['email'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    $privilegio = $_POST['privilegio'] ?? 'agente';
    if (!$nombre) $errores['nombre'] = 'Ingrese el nombre.';
    if (!$usuario) $errores['usuario'] = 'Ingrese el usuario.';
    if (!$contrasena) $errores['contrasena'] = 'Ingrese la contraseña.';
    if (!$privilegio) $errores['privilegio'] = 'Seleccione el privilegio.';
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errores['email'] = 'Email inválido.';
    if (empty($errores)) {
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, telefono, correo, email, usuario, contrasena, privilegio) VALUES ('"
            . $conn->real_escape_string($nombre) . "', '"
            . $conn->real_escape_string($telefono) . "', '"
            . $conn->real_escape_string($correo) . "', '"
            . $conn->real_escape_string($email) . "', '"
            . $conn->real_escape_string($usuario) . "', '"
            . $hash . "', '"
            . $conn->real_escape_string($privilegio) . "')";
        if ($conn->query($sql)) {
            $mensaje = 'Usuario creado correctamente.';
        } else {
            $mensaje = 'Error al crear usuario.';
        }
    } else {
        $mensaje = 'Corrija los errores indicados.';
    }
}

// Listar usuarios
$usuarios = $conn->query("SELECT * FROM usuarios");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-900 text-white p-4 flex justify-between items-center">
        <span class="font-bold text-lg">Gestionar Usuarios</span>
        <a href="panel.php" class="bg-yellow-400 text-blue-900 px-3 py-1 rounded font-bold">Volver al panel</a>
    </header>
    <main class="max-w-4xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6 text-blue-900">Usuarios</h1>
        <?php if($mensaje): ?><div class="bg-green-100 text-green-700 p-2 mb-2 rounded"><?= $mensaje ?></div><?php endif; ?>
        <form method="post" class="bg-white p-4 rounded shadow mb-8">
            <h2 class="font-bold mb-2">Crear Usuario</h2>
            <div class="grid grid-cols-2 gap-4 mb-2">
                <div>
                    <input type="text" name="nombre" placeholder="Nombre" class="border rounded px-2 py-1" required>
                    <?php if(isset($errores['nombre'])): ?><div class="text-red-600 text-sm"><?= $errores['nombre'] ?></div><?php endif; ?>
                </div>
                <div>
                    <input type="text" name="telefono" placeholder="Teléfono" class="border rounded px-2 py-1">
                </div>
                <div>
                    <input type="text" name="correo" placeholder="Correo" class="border rounded px-2 py-1">
                </div>
                <div>
                    <input type="email" name="email" placeholder="Email" class="border rounded px-2 py-1">
                    <?php if(isset($errores['email'])): ?><div class="text-red-600 text-sm"><?= $errores['email'] ?></div><?php endif; ?>
                </div>
                <div>
                    <input type="text" name="usuario" placeholder="Usuario" class="border rounded px-2 py-1" required>
                    <?php if(isset($errores['usuario'])): ?><div class="text-red-600 text-sm"><?= $errores['usuario'] ?></div><?php endif; ?>
                </div>
                <div>
                    <input type="password" name="contrasena" placeholder="Contraseña" class="border rounded px-2 py-1" required>
                    <?php if(isset($errores['contrasena'])): ?><div class="text-red-600 text-sm"><?= $errores['contrasena'] ?></div><?php endif; ?>
                </div>
                <div>
                    <select name="privilegio" class="border rounded px-2 py-1">
                        <option value="administrador">Administrador</option>
                        <option value="agente">Agente de Ventas</option>
                    </select>
                    <?php if(isset($errores['privilegio'])): ?><div class="text-red-600 text-sm"><?= $errores['privilegio'] ?></div><?php endif; ?>
                </div>
            </div>
            <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded font-bold">Crear Usuario</button>
        </form>
        <table class="w-full bg-white rounded shadow mb-8">
            <thead class="bg-blue-900 text-white">
                <tr>
                    <th class="p-2">ID</th>
                    <th class="p-2">Nombre</th>
                    <th class="p-2">Usuario</th>
                    <th class="p-2">Privilegio</th>
                    <th class="p-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($u = $usuarios->fetch_assoc()): ?>
                <tr class="border-b">
                    <td class="p-2"><?= $u['id'] ?></td>
                    <td class="p-2"><?= htmlspecialchars($u['nombre']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($u['usuario']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($u['privilegio']) ?></td>
                    <td class="p-2">
                        <a href="editar_usuario.php?id=<?= $u['id'] ?>" class="text-blue-900 font-bold mr-2">Editar</a>
                        <a href="eliminar_usuario.php?id=<?= $u['id'] ?>" class="text-red-600 font-bold">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="panel.php" class="block text-blue-900 font-bold">Volver al panel</a>
    </main>
</body>
</html>
