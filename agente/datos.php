<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
if (!usuario_autenticado()) redirigir('../login.php');
$id_usuario = $_SESSION['id'];
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $color_principal = $_POST['color_principal'] ?? '#25344b';
    $color_secundario = $_POST['color_secundario'] ?? '#ffe600';
    $stmt = $conn->prepare("UPDATE usuarios SET color_principal=?, color_secundario=? WHERE id=?");
    $stmt->bind_param('ssi', $color_principal, $color_secundario, $id_usuario);
    if ($stmt->execute()) {
        $mensaje = 'Colores actualizados correctamente.';
    } else {
        $mensaje = 'Error al actualizar los colores.';
    }
    $stmt->close();
}
$usuario = $conn->query("SELECT * FROM usuarios WHERE id=$id_usuario")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personalizar Colores</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <main class="max-w-xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6 text-blue-900">Personalizar Colores</h1>
        <?php if($mensaje): ?><div class="bg-green-100 text-green-700 p-2 mb-2 rounded"><?= $mensaje ?></div><?php endif; ?>
        <form method="post" class="bg-white p-6 rounded shadow">
            <label class="block mb-2 font-semibold">Color principal:</label>
            <input type="color" name="color_principal" value="<?= htmlspecialchars($usuario['color_principal']) ?>" class="mb-4">
            <label class="block mb-2 font-semibold">Color secundario:</label>
            <input type="color" name="color_secundario" value="<?= htmlspecialchars($usuario['color_secundario']) ?>" class="mb-4">
            <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded font-bold">Guardar Cambios</button>
        </form>
        <a href="panel.php" class="block mt-6 text-blue-900 font-bold">Volver al panel</a>
    </main>
</body>
</html>
<?php
require_once '../includes/db.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';
if (!es_agente()) redirigir('../login.php');
$id = $_SESSION['id'];
$mensaje = '';
$u = $conn->query("SELECT * FROM usuarios WHERE id=$id")->fetch_assoc();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $email = $_POST['email'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    if ($nombre && $usuario) {
        $sql = "UPDATE usuarios SET nombre='" . $conn->real_escape_string($nombre) . "', telefono='" . $conn->real_escape_string($telefono) . "', correo='" . $conn->real_escape_string($correo) . "', email='" . $conn->real_escape_string($email) . "', usuario='" . $conn->real_escape_string($usuario) . "'";
        if ($contrasena) {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $sql .= ", contrasena='$hash'";
        }
        $sql .= " WHERE id=$id";
        if ($conn->query($sql)) {
            $mensaje = 'Datos actualizados correctamente.';
        } else {
            $mensaje = 'Error al actualizar datos.';
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
    <title>Actualizar Datos Personales</title>
    <link href="../css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <main class="max-w-4xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6 text-blue-900">Actualizar Datos Personales</h1>
        <?php if($mensaje): ?><div class="bg-green-100 text-green-700 p-2 mb-2 rounded"><?= $mensaje ?></div><?php endif; ?>
        <form method="post" class="bg-white p-4 rounded shadow mb-8">
            <div class="grid grid-cols-2 gap-4 mb-2">
                <input type="text" name="nombre" value="<?= htmlspecialchars($u['nombre']) ?>" placeholder="Nombre" class="border rounded px-2 py-1" required>
                <input type="text" name="telefono" value="<?= htmlspecialchars($u['telefono']) ?>" placeholder="Teléfono" class="border rounded px-2 py-1">
                <input type="text" name="correo" value="<?= htmlspecialchars($u['correo']) ?>" placeholder="Correo" class="border rounded px-2 py-1">
                <input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" placeholder="Email" class="border rounded px-2 py-1">
                <input type="text" name="usuario" value="<?= htmlspecialchars($u['usuario']) ?>" placeholder="Usuario" class="border rounded px-2 py-1" required>
                <input type="password" name="contrasena" placeholder="Nueva Contraseña (opcional)" class="border rounded px-2 py-1">
            </div>
            <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded font-bold">Actualizar Datos</button>
        </form>
        <a href="panel.php" class="block mt-6 text-blue-900 font-bold">Volver al panel</a>
    </main>
</body>
</html>
