<?php
require_once '../includes/db.php';
require_once '../includes/session.php';
if (!es_admin()) redirigir('../login.php');
$id = $_GET['id'] ?? 0;
$mensaje = '';
$u = $conn->query("SELECT * FROM usuarios WHERE id=$id")->fetch_assoc();
if (!$u) die('Usuario no encontrado');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $email = $_POST['email'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $privilegio = $_POST['privilegio'] ?? 'agente';
    if ($nombre && $usuario && $privilegio) {
        $sql = "UPDATE usuarios SET nombre='" . $conn->real_escape_string($nombre) . "', telefono='" . $conn->real_escape_string($telefono) . "', correo='" . $conn->real_escape_string($correo) . "', email='" . $conn->real_escape_string($email) . "', usuario='" . $conn->real_escape_string($usuario) . "', privilegio='" . $conn->real_escape_string($privilegio) . "' WHERE id=$id";
        if ($conn->query($sql)) {
            $mensaje = 'Usuario actualizado correctamente.';
        } else {
            $mensaje = 'Error al actualizar usuario.';
        }
    } else {
        $mensaje = 'Complete los campos obligatorios.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link href="../css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <main class="max-w-4xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6 text-blue-900">Editar Usuario</h1>
        <?php if($mensaje): ?><div class="bg-green-100 text-green-700 p-2 mb-2 rounded"><?= $mensaje ?></div><?php endif; ?>
        <form method="post" class="bg-white p-4 rounded shadow mb-8">
            <div class="grid grid-cols-2 gap-4 mb-2">
                <input type="text" name="nombre" value="<?= htmlspecialchars($u['nombre']) ?>" placeholder="Nombre" class="border rounded px-2 py-1" required>
                <input type="text" name="telefono" value="<?= htmlspecialchars($u['telefono']) ?>" placeholder="Teléfono" class="border rounded px-2 py-1">
                <input type="text" name="correo" value="<?= htmlspecialchars($u['correo']) ?>" placeholder="Correo" class="border rounded px-2 py-1">
                <input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" placeholder="Email" class="border rounded px-2 py-1">
                <input type="text" name="usuario" value="<?= htmlspecialchars($u['usuario']) ?>" placeholder="Usuario" class="border rounded px-2 py-1" required>
                <select name="privilegio" class="border rounded px-2 py-1">
                    <option value="administrador" <?= $u['privilegio']=='administrador'?'selected':'' ?>>Administrador</option>
                    <option value="agente" <?= $u['privilegio']=='agente'?'selected':'' ?>>Agente de Ventas</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded font-bold">Actualizar Usuario</button>
        </form>
        <a href="usuarios.php" class="block mt-6 text-blue-900 font-bold">Volver a usuarios</a>
    </main>
</body>
</html>
