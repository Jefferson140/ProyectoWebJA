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
            $icono_principal = 'img/icono_principal.png';
            move_uploaded_file($_FILES['icono_principal']['tmp_name'], '../' . $icono_principal);
        }
    }
    if (isset($_FILES['icono_blanco']) && $_FILES['icono_blanco']['tmp_name']) {
        if ($_FILES['icono_blanco']['size'] > $max_size) $errores['icono_blanco'] = 'El ícono blanco es muy grande.';
        if (!in_array($_FILES['icono_blanco']['type'], $allowed_types)) $errores['icono_blanco'] = 'Formato de ícono blanco no permitido.';
        if (empty($errores['icono_blanco'])) {
            $icono_blanco = 'img/icono_blanco.png';
            move_uploaded_file($_FILES['icono_blanco']['tmp_name'], '../' . $icono_blanco);
        }
    }
    if (isset($_FILES['imagen_banner']) && $_FILES['imagen_banner']['tmp_name']) {
        if ($_FILES['imagen_banner']['size'] > $max_size) $errores['imagen_banner'] = 'La imagen del banner es muy grande.';
        if (!in_array($_FILES['imagen_banner']['type'], $allowed_types)) $errores['imagen_banner'] = 'Formato de banner no permitido.';
        if (empty($errores['imagen_banner'])) {
            $imagen_banner = 'img/banner.jpg';
            move_uploaded_file($_FILES['imagen_banner']['tmp_name'], '../' . $imagen_banner);
        }
    }
    if (isset($_FILES['imagen_quienes_somos']) && $_FILES['imagen_quienes_somos']['tmp_name']) {
        if ($_FILES['imagen_quienes_somos']['size'] > $max_size) $errores['imagen_quienes_somos'] = 'La imagen de quienes somos es muy grande.';
        if (!in_array($_FILES['imagen_quienes_somos']['type'], $allowed_types)) $errores['imagen_quienes_somos'] = 'Formato de imagen quienes somos no permitido.';
        if (empty($errores['imagen_quienes_somos'])) {
            $imagen_quienes_somos = 'img/quienes.jpg';
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
        // Actualizar configuración
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
    <main class="max-w-4xl mx-auto py-8">
        <div class="bg-white rounded shadow-lg p-8">
            <h1 class="text-3xl font-bold mb-8 text-blue-900 text-center">Personalizar Página</h1>
            <?php if($mensaje): ?><div class="bg-green-100 text-green-700 p-2 mb-4 rounded text-center font-semibold"><?= $mensaje ?></div><?php endif; ?>
            <?php
            // Obtener colores personales del admin
            $id_admin = $_SESSION['id'];
            $admin = $conn->query("SELECT color_principal, color_secundario FROM usuarios WHERE id=$id_admin")->fetch_assoc();
            ?>
            <form method="post" enctype="multipart/form-data" class="space-y-8">
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <label class="font-semibold">Color principal:</label>
                    <input type="color" name="color_principal" value="<?= htmlspecialchars($config['color_principal']) ?>" class="border-2 border-gray-300 rounded w-16 h-10">
                    <label class="font-semibold">Color secundario:</label>
                    <input type="color" name="color_secundario" value="<?= htmlspecialchars($config['color_secundario']) ?>" class="border-2 border-gray-300 rounded w-16 h-10">
                    <div class="col-span-2 flex justify-end mt-2">
                        <button type="button" class="bg-gray-300 text-blue-900 px-4 py-2 rounded font-bold" onclick="document.querySelector('input[name=\'color_principal\']').value='#25344b';document.querySelector('input[name=\'color_secundario\']').value='#ffe600';">Volver a colores por defecto</button>
                    </div>
                    <label class="font-semibold">Ícono principal:</label>
                    <input type="file" name="icono_principal" accept="image/*" class="mb-2">
                    <?php if($config['icono_principal']): ?><img src="../<?= $config['icono_principal'] ?>" class="h-10 mb-2 mx-auto" alt="Ícono principal"><?php endif; ?>
                    <label class="font-semibold">Ícono blanco:</label>
                    <input type="file" name="icono_blanco" accept="image/*" class="mb-2">
                    <?php if($config['icono_blanco']): ?><img src="../<?= $config['icono_blanco'] ?>" class="h-10 mb-2 bg-gray-100 mx-auto" alt="Ícono blanco"><?php endif; ?>
                    <label class="font-semibold">Imagen principal del banner:</label>
                    <input type="file" name="imagen_banner" accept="image/*" class="mb-2">
                    <?php if($config['imagen_banner']): ?><img src="../<?= $config['imagen_banner'] ?>" class="h-20 mb-2 mx-auto" alt="Banner"><?php endif; ?>
                    <label class="font-semibold">Mensaje del banner:</label>
                    <input type="text" name="mensaje_banner" value="<?= htmlspecialchars($config['mensaje_banner']) ?>" class="border rounded px-2 py-1">
                    <label class="font-semibold">Información de quienes somos:</label>
                    <textarea name="quienes_somos" class="border rounded px-2 py-1" rows="3"><?= htmlspecialchars($config['quienes_somos']) ?></textarea>
                    <label class="font-semibold">Imagen quienes somos:</label>
                    <input type="file" name="imagen_quienes_somos" accept="image/*" class="mb-2">
                    <?php if($config['imagen_quienes_somos']): ?><img src="../<?= $config['imagen_quienes_somos'] ?>" class="h-20 mb-2 mx-auto" alt="Quienes somos"><?php endif; ?>
                    <label class="font-semibold">Facebook:</label>
                    <input type="text" name="facebook" value="<?= htmlspecialchars($config['facebook']) ?>" class="border rounded px-2 py-1">
                    <label class="font-semibold">Instagram:</label>
                    <input type="text" name="instagram" value="<?= htmlspecialchars($config['instagram']) ?>" class="border rounded px-2 py-1">
                    <label class="font-semibold">YouTube:</label>
                    <input type="text" name="youtube" value="<?= htmlspecialchars($config['youtube']) ?>" class="border rounded px-2 py-1">
                    <label class="font-semibold">Dirección:</label>
                    <input type="text" name="direccion" value="<?= htmlspecialchars($config['direccion']) ?>" class="border rounded px-2 py-1">
                    <label class="font-semibold">Teléfono:</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($config['telefono']) ?>" class="border rounded px-2 py-1">
                    <label class="font-semibold">Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($config['email']) ?>" class="border rounded px-2 py-1">
                    <hr class="my-4">
                    <h2 class="text-lg font-bold mb-2 text-blue-900 col-span-2">Personalizar formulario de contacto (footer)</h2>
                    <label class="font-semibold">Texto Nombre:</label>
                    <input type="text" name="contacto_nombre" value="<?= htmlspecialchars($config['contacto_nombre'] ?? 'Nombre:') ?>" class="border rounded px-2 py-1">
                    <label class="font-semibold">Texto Email:</label>
                    <input type="text" name="contacto_email" value="<?= htmlspecialchars($config['contacto_email'] ?? 'Email:') ?>" class="border rounded px-2 py-1">
                    <label class="font-semibold">Texto Teléfono:</label>
                    <input type="text" name="contacto_telefono" value="<?= htmlspecialchars($config['contacto_telefono'] ?? 'Teléfono:') ?>" class="border rounded px-2 py-1">
                    <label class="font-semibold">Texto Mensaje:</label>
                    <input type="text" name="contacto_mensaje" value="<?= htmlspecialchars($config['contacto_mensaje'] ?? 'Mensaje:') ?>" class="border rounded px-2 py-1">
                    <label class="font-semibold">Texto Botón:</label>
                    <input type="text" name="contacto_boton" value="<?= htmlspecialchars($config['contacto_boton'] ?? 'Enviar') ?>" class="border rounded px-2 py-1">
            </div>
                <button type="submit" class="bg-blue-900 text-white px-6 py-2 rounded font-bold w-full">Guardar Cambios</button>
            </form>
            <a href="../index.php" class="block mt-8 text-blue-900 font-bold text-center">Volver al inicio</a>
        </div>
    </main>
</body>
</html>
