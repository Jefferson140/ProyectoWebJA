<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';
if (!es_admin()) redirigir('../login.php');
$mensaje = '';
// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errores = [];
    $color_principal = $_POST['color_principal'] ?? '';
    $color_secundario = $_POST['color_secundario'] ?? '';
    $mensaje_banner = $_POST['mensaje_banner'] ?? '';
    $quienes_somos = $_POST['quienes_somos'] ?? '';
    $facebook = $_POST['facebook'] ?? '';
    $instagram = $_POST['instagram'] ?? '';
    $youtube = $_POST['youtube'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';
    // Validaciones mínimas: solo validar email si se ingresa
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errores['email'] = 'Ingrese un email válido.';
    // Validación de archivos
    $icono_principal = $icono_blanco = $imagen_banner = $imagen_quienes_somos = '';
    $max_size = 2 * 1024 * 1024; // 2MB
    $allowed_types = ['image/jpeg','image/png','image/gif','image/webp'];
    if (isset($_FILES['icono_principal']) && $_FILES['icono_principal']['tmp_name']) {
        if ($_FILES['icono_principal']['size'] > $max_size) $errores['icono_principal'] = 'El ícono principal es muy grande.';
        if (!in_array($_FILES['icono_principal']['type'], $allowed_types)) $errores['icono_principal'] = 'Formato de ícono principal no permitido.';
        if (empty($errores['icono_principal'])) {
            $ext = pathinfo($_FILES['icono_principal']['name'], PATHINFO_EXTENSION);
            $icono_principal = 'img/icono_principal_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['icono_principal']['tmp_name'], '../' . $icono_principal);
        }
    }
    if (isset($_FILES['icono_blanco']) && $_FILES['icono_blanco']['tmp_name']) {
        if ($_FILES['icono_blanco']['size'] > $max_size) $errores['icono_blanco'] = 'El ícono blanco es muy grande.';
        if (!in_array($_FILES['icono_blanco']['type'], $allowed_types)) $errores['icono_blanco'] = 'Formato de ícono blanco no permitido.';
        if (empty($errores['icono_blanco'])) {
            $ext = pathinfo($_FILES['icono_blanco']['name'], PATHINFO_EXTENSION);
            $icono_blanco = 'img/icono_blanco_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['icono_blanco']['tmp_name'], '../' . $icono_blanco);
        }
    }
    if (isset($_FILES['imagen_banner']) && $_FILES['imagen_banner']['tmp_name']) {
        if ($_FILES['imagen_banner']['size'] > $max_size) $errores['imagen_banner'] = 'La imagen del banner es muy grande.';
        if (!in_array($_FILES['imagen_banner']['type'], $allowed_types)) $errores['imagen_banner'] = 'Formato de banner no permitido.';
        if (empty($errores['imagen_banner'])) {
            $ext = pathinfo($_FILES['imagen_banner']['name'], PATHINFO_EXTENSION);
            $imagen_banner = 'img/banner_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['imagen_banner']['tmp_name'], '../' . $imagen_banner);
        }
    }
    if (isset($_FILES['imagen_quienes_somos']) && $_FILES['imagen_quienes_somos']['tmp_name']) {
        if ($_FILES['imagen_quienes_somos']['size'] > $max_size) $errores['imagen_quienes_somos'] = 'La imagen de quienes somos es muy grande.';
        if (!in_array($_FILES['imagen_quienes_somos']['type'], $allowed_types)) $errores['imagen_quienes_somos'] = 'Formato de imagen quienes somos no permitido.';
        if (empty($errores['imagen_quienes_somos'])) {
            $ext = pathinfo($_FILES['imagen_quienes_somos']['name'], PATHINFO_EXTENSION);
            $imagen_quienes_somos = 'img/quienes_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['imagen_quienes_somos']['tmp_name'], '../' . $imagen_quienes_somos);
        }
    }
    // Guardar colores personales del admin
    if (isset($_POST['color_personal_principal']) && isset($_POST['color_personal_secundario'])) {
        $color_personal_principal = $_POST['color_personal_principal'];
        $color_personal_secundario = $_POST['color_personal_secundario'];
        $id_admin = $_SESSION['id'];
        $stmt = $conn->prepare("UPDATE usuarios SET color_principal=?, color_secundario=? WHERE id=?");
        $stmt->bind_param('ssi', $color_personal_principal, $color_personal_secundario, $id_admin);
        $stmt->execute();
        $stmt->close();
    }
    if (empty($errores)) {
    // Actualizar configuración sin campos personalizados del formulario de contacto
    $sql = "UPDATE configuracion SET color_principal=?, color_secundario=?, mensaje_banner=?, quienes_somos=?, facebook=?, instagram=?, youtube=?, direccion=?, telefono=?, email=?";
    $params = [$color_principal, $color_secundario, $mensaje_banner, $quienes_somos, $facebook, $instagram, $youtube, $direccion, $telefono, $email];
    if ($icono_principal) { $sql .= ", icono_principal=?"; $params[] = $icono_principal; }
    if ($icono_blanco) { $sql .= ", icono_blanco=?"; $params[] = $icono_blanco; }
    if ($imagen_banner) { $sql .= ", imagen_banner=?"; $params[] = $imagen_banner; }
    if ($imagen_quienes_somos) { $sql .= ", imagen_quienes_somos=?"; $params[] = $imagen_quienes_somos; }
    $sql .= " WHERE id=1";
    $types = str_repeat('s', count($params));
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $ok = $stmt->execute();
        $stmt->close();
        // Actualizar colores en todos los usuarios
        $stmt2 = $conn->prepare("UPDATE usuarios SET color_principal=?, color_secundario=?");
        $stmt2->bind_param('ss', $color_principal, $color_secundario);
        $ok2 = $stmt2->execute();
        $stmt2->close();
        if ($ok && $ok2) {
            $mensaje = 'Configuración y colores de usuarios actualizados.';
        } else {
            $mensaje = 'Error al actualizar.';
        }
    } else {
        $mensaje = 'Corrija los errores indicados.';
    }
}
// Obtener configuración actual
// Colores por defecto en formato hexadecimal
$config = $conn->query("SELECT * FROM configuracion WHERE id=1");
if ($config && $config->num_rows > 0) {
    $config = $config->fetch_assoc();
    // Si el color no está en formato hexadecimal, lo convertimos
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $config['color_principal'])) {
        switch(strtolower($config['color_principal'])) {
            case 'azul': $config['color_principal'] = '#25344b'; break;
            case 'amarillo': $config['color_principal'] = '#ffe600'; break;
            case 'gris': $config['color_principal'] = '#6c757d'; break;
            case 'blanco': $config['color_principal'] = '#ffffff'; break;
            default: $config['color_principal'] = '#25344b';
        }
    }
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $config['color_secundario'])) {
        switch(strtolower($config['color_secundario'])) {
            case 'azul': $config['color_secundario'] = '#25344b'; break;
            case 'amarillo': $config['color_secundario'] = '#ffe600'; break;
            case 'gris': $config['color_secundario'] = '#6c757d'; break;
            case 'blanco': $config['color_secundario'] = '#ffffff'; break;
            default: $config['color_secundario'] = '#ffe600';
        }
    }
} else {
    $config = [
        'color_principal' => '#25344b',
        'color_secundario' => '#ffe600',
        'mensaje_banner' => '',
        'quienes_somos' => '',
        'facebook' => '',
        'instagram' => '',
        'youtube' => '',
        'direccion' => '',
        'telefono' => '',
        'email' => '',
        'icono_principal' => 'img/logo.png',
        'icono_blanco' => 'img/logo.png',
    'imagen_banner' => 'img/banner.jpg',
    'imagen_quienes_somos' => 'img/quienes.jpg'
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personalizar Página</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-900 text-white p-4 flex justify-between items-center">
        <span class="font-bold text-lg">Personalizar Página</span>
        <a href="panel.php" class="bg-yellow-400 text-blue-900 px-3 py-1 rounded font-bold">Volver al panel</a>
    </header>
    <main class="max-w-4xl mx-auto py-8">
        <div class="bg-white rounded-2xl shadow-2xl p-10 border border-gray-200">
            <h1 class="text-3xl font-extrabold mb-8 text-blue-900 text-center drop-shadow">Personalizar Página</h1>
            <?php if($mensaje): ?>
                <div class="bg-green-100 text-green-700 p-3 mb-6 rounded-xl text-center font-semibold shadow">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>
            <?php
            $id_admin = $_SESSION['id'];
            $admin = $conn->query("SELECT color_principal, color_secundario FROM usuarios WHERE id=$id_admin")->fetch_assoc();
            ?>
            <form method="post" enctype="multipart/form-data" class="space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <label class="block font-bold mb-2 text-blue-900">Color principal:</label>
                        <input type="color" name="color_principal" value="<?= htmlspecialchars($config['color_principal']) ?>" class="border-2 border-blue-900 rounded-lg w-20 h-12 shadow">
                    </div>
                    <div>
                        <label class="block font-bold mb-2 text-blue-900">Color secundario:</label>
                        <input type="color" name="color_secundario" value="<?= htmlspecialchars($config['color_secundario']) ?>" class="border-2 border-yellow-400 rounded-lg w-20 h-12 shadow">
                    </div>
                    <div class="md:col-span-2 flex justify-end mt-2">
                        <button type="button" class="bg-gray-300 text-blue-900 px-5 py-2 rounded-lg font-bold shadow hover:bg-gray-400 transition" onclick="document.querySelector('input[name=\'color_principal\']').value='#25344b';document.querySelector('input[name=\'color_secundario\']').value='#ffe600';">Colores por defecto</button>
                    </div>
                    <div>
                        <label class="block font-bold mb-2 text-blue-900">Ícono principal:</label>
                        <input type="file" name="icono_principal" accept="image/*" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg shadow">
                        <?php if($config['icono_principal']): ?><img src="../<?= $config['icono_principal'] ?>" class="h-12 mb-2 mx-auto rounded shadow" alt="Ícono principal"><?php endif; ?>
                    </div>
                    <div>
                        <label class="block font-bold mb-2 text-blue-900">Ícono blanco:</label>
                        <input type="file" name="icono_blanco" accept="image/*" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg shadow">
                        <?php if($config['icono_blanco']): ?><img src="../<?= $config['icono_blanco'] ?>" class="h-12 mb-2 mx-auto rounded shadow bg-gray-100" alt="Ícono blanco"><?php endif; ?>
                    </div>
                    <div>
                        <label class="block font-bold mb-2 text-blue-900">Imagen principal del banner:</label>
                        <input type="file" name="imagen_banner" accept="image/*" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg shadow">
                        <?php if($config['imagen_banner']): ?><img src="../<?= $config['imagen_banner'] ?>" class="h-20 mb-2 mx-auto rounded shadow" alt="Banner"><?php endif; ?>
                    </div>
                    <div>
                        <label class="block font-bold mb-2 text-blue-900">Mensaje del banner:</label>
                        <input type="text" name="mensaje_banner" value="<?= htmlspecialchars($config['mensaje_banner']) ?>" class="w-full px-3 py-2 border-2 border-blue-900 rounded-lg shadow">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block font-bold mb-2 text-blue-900">Información de quienes somos:</label>
                        <textarea name="quienes_somos" class="w-full px-3 py-2 border-2 border-blue-900 rounded-lg shadow" rows="3"><?= htmlspecialchars($config['quienes_somos']) ?></textarea>
                    </div>
                    <div>
                        <label class="block font-bold mb-2 text-blue-900">Imagen quienes somos:</label>
                        <input type="file" name="imagen_quienes_somos" accept="image/*" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg shadow">
                        <?php if($config['imagen_quienes_somos']): ?><img src="../<?= $config['imagen_quienes_somos'] ?>" class="h-20 mb-2 mx-auto rounded shadow" alt="Quienes somos"><?php endif; ?>
                    </div>
                    <div>
                        <label class="block font-bold mb-2 text-blue-900">Facebook:</label>
                        <input type="text" name="facebook" value="<?= htmlspecialchars($config['facebook']) ?>" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg shadow">
                    </div>
                    <div>
                        <label class="block font-bold mb-2 text-blue-900">Instagram:</label>
                        <input type="text" name="instagram" value="<?= htmlspecialchars($config['instagram']) ?>" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg shadow">
                    </div>
                    <div>
                        <label class="block font-bold mb-2 text-blue-900">YouTube:</label>
                        <input type="text" name="youtube" value="<?= htmlspecialchars($config['youtube']) ?>" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg shadow">
                    </div>
                    <div>
                        <label class="block font-bold mb-2 text-blue-900">Dirección:</label>
                        <input type="text" name="direccion" value="<?= htmlspecialchars($config['direccion']) ?>" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg shadow">
                    </div>
                    <div>
                        <label class="block font-bold mb-2 text-blue-900">Teléfono:</label>
                        <input type="text" name="telefono" value="<?= htmlspecialchars($config['telefono']) ?>" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg shadow">
                    </div>
                    <div>
                        <label class="block font-bold mb-2 text-blue-900">Email:</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($config['email']) ?>" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg shadow">
                    </div>
                </div>
                <button type="submit" class="bg-blue-900 text-white px-8 py-3 rounded-xl font-extrabold w-full shadow-lg hover:bg-blue-800 transition text-lg">Guardar Cambios</button>
            </form>
            <a href="../index.php" class="block mt-10 text-blue-900 font-bold text-center hover:underline">Volver al inicio</a>
        </div>
    </main>
</body>
</html>
