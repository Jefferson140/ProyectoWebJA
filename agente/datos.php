<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
if (!es_agente()) redirigir('../login.php');
$id = $_SESSION['id'];
$mensaje = '';
$u = $conn->query("SELECT * FROM usuarios WHERE id=$id")->fetch_assoc();

// Procesar formulario de colores

// Procesar formulario de datos personales
if (isset($_POST['actualizar_datos'])) {
    $nombre = $_POST['nombre'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $email = $_POST['email'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    if ($nombre && $usuario) {
        $sql = "UPDATE usuarios SET nombre=?, telefono=?, correo=?, email=?, usuario=?";
        $params = [$nombre, $telefono, $correo, $email, $usuario];
        $types = 'sssss';
        if ($contrasena) {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $sql .= ", contrasena=?";
            $params[] = $hash;
            $types .= 's';
        }
        $sql .= " WHERE id=?";
        $params[] = $id;
        $types .= 'i';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            $mensaje = 'Datos actualizados correctamente.';
            $u = $conn->query("SELECT * FROM usuarios WHERE id=$id")->fetch_assoc();
        } else {
            $mensaje = 'Error al actualizar datos.';
        }
        $stmt->close();
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-900 text-white p-4 flex justify-between items-center">
        <span class="font-bold">Actualizar Datos Personales</span>
        <a href="panel.php" class="bg-yellow-400 text-blue-900 px-3 py-1 rounded font-bold">Volver al panel</a>
    </header>
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
            <button type="submit" name="actualizar_datos" class="bg-blue-900 text-white px-4 py-2 rounded font-bold">Actualizar Datos</button>
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
