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
   <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <main class="w-full max-w-lg bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-3xl font-bold mb-8 text-blue-900 text-center">Editar Usuario</h1>
        <?php if($mensaje): ?>
            <div class="mb-4 px-4 py-3 rounded-lg <?= strpos($mensaje, 'correctamente') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                <?= $mensaje ?>
            </div>
        <?php endif; ?>
        <form method="post" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($u['nombre']) ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-900" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="text" name="telefono" value="<?= htmlspecialchars($u['telefono']) ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-900">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                <input type="text" name="correo" value="<?= htmlspecialchars($u['correo']) ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-900">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-900">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Usuario <span class="text-red-500">*</span></label>
                <input type="text" name="usuario" value="<?= htmlspecialchars($u['usuario']) ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-900" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Privilegio</label>
                <select name="privilegio" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    <option value="administrador" <?= $u['privilegio']=='administrador'?'selected':'' ?>>Administrador</option>
                    <option value="agente" <?= $u['privilegio']=='agente'?'selected':'' ?>>Agente de Ventas</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-900 text-white py-2 rounded-lg font-bold hover:bg-blue-800 transition">Actualizar Usuario</button>
        </form>
        <a href="usuarios.php" class="block mt-8 text-blue-900 font-bold text-center hover:underline">Volver