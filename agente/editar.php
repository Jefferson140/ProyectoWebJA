<?php
require_once '../includes/db.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';
if (!es_agente() && !es_admin()) redirigir('../login.php');
$id = $_GET['id'] ?? 0;
$id_agente = $_SESSION['id'];
$mensaje = '';
// Obtener propiedad
$p = $conn->query("SELECT * FROM propiedades WHERE id=$id AND agente_id=$id_agente")->fetch_assoc();
if (!$p) die('Propiedad no encontrada');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errores = [];
    $tipo = $_POST['tipo'] ?? '';
    $destacada = isset($_POST['destacada']) ? 1 : 0;
    $titulo = $_POST['titulo'] ?? '';
    $descripcion_breve = $_POST['descripcion_breve'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $imagen_destacada = $p['imagen_destacada'];
    $descripcion_larga = $_POST['descripcion_larga'] ?? '';
    $mapa = $_POST['mapa'] ?? '';
    $ubicacion = $_POST['ubicacion'] ?? '';
    if (!$tipo) $errores['tipo'] = 'Seleccione el tipo.';
    if (!$titulo) $errores['titulo'] = 'Ingrese el título.';
    if (!$descripcion_breve) $errores['descripcion_breve'] = 'Ingrese la descripción breve.';
    if (!$precio || !is_numeric($precio) || $precio <= 0) $errores['precio'] = 'Ingrese un precio válido.';
    if (!$descripcion_larga) $errores['descripcion_larga'] = 'Ingrese la descripción larga.';
    // Validación de imagen
    $max_size = 2 * 1024 * 1024; // 2MB
    $allowed_types = ['image/jpeg','image/png','image/gif','image/webp'];
    if (isset($_FILES['imagen_destacada']) && $_FILES['imagen_destacada']['tmp_name']) {
        if ($_FILES['imagen_destacada']['size'] > $max_size) $errores['imagen_destacada'] = 'La imagen es muy grande.';
        if (!in_array($_FILES['imagen_destacada']['type'], $allowed_types)) $errores['imagen_destacada'] = 'Formato de imagen no permitido.';
        if (empty($errores['imagen_destacada'])) {
            $imagen_destacada = 'img/prop_' . time() . '_' . rand(100,999) . '.jpg';
            move_uploaded_file($_FILES['imagen_destacada']['tmp_name'], '../' . $imagen_destacada);
        }
    }
    if (empty($errores)) {
        $sql = "UPDATE propiedades SET tipo='" . $conn->real_escape_string($tipo) . "', destacada=$destacada, titulo='" . $conn->real_escape_string($titulo) . "', descripcion_breve='" . $conn->real_escape_string($descripcion_breve) . "', precio=" . $conn->real_escape_string($precio) . ", imagen_destacada='" . $conn->real_escape_string($imagen_destacada) . "', descripcion_larga='" . $conn->real_escape_string($descripcion_larga) . "', mapa='" . $conn->real_escape_string($mapa) . "', ubicacion='" . $conn->real_escape_string($ubicacion) . "' WHERE id=$id AND agente_id=$id_agente";
        if ($conn->query($sql)) {
            $mensaje = 'Propiedad actualizada correctamente.';
        } else {
            $mensaje = 'Error al actualizar propiedad.';
        }
    } else {
        $mensaje = 'Corrija los errores indicados.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Propiedad</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-900 text-white p-4 flex justify-between items-center">
        <span class="font-bold text-lg">Editar Propiedad</span>
        <div class="flex gap-2">
            <a href="propiedades.php" class="bg-yellow-400 text-blue-900 px-3 py-1 rounded font-bold">Volver</a>
            <a href="../index.php" class="bg-yellow-400 text-blue-900 px-3 py-1 rounded font-bold">Ir al inicio</a>
            <form action="../login.php" method="post">
                <button type="submit" name="logout" class="bg-red-600 text-white px-3 py-1 rounded">Cerrar sesión</button>
            </form>
        </div>
    </header>
    <main class="max-w-4xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6 text-blue-900">Editar Propiedad</h1>
        <?php if($mensaje): ?><div class="bg-green-100 text-green-700 p-2 mb-2 rounded"><?= $mensaje ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow space-y-4">
            <div class="grid grid-cols-2 gap-4 mb-2">
                <div>
                    <label class="block font-semibold mb-1">Tipo:</label>
                    <select name="tipo" class="border rounded px-2 py-1" required>
                        <option value="">Seleccione</option>
                        <option value="venta" <?= $p['tipo']=='venta'?'selected':'' ?>>Venta</option>
                        <option value="alquiler" <?= $p['tipo']=='alquiler'?'selected':'' ?>>Alquiler</option>
                    </select>
                    <?php if(isset($errores['tipo'])): ?><div class="text-red-600 text-sm"><?= $errores['tipo'] ?></div><?php endif; ?>
                </div>
                <div class="flex items-center">
                    <label class="flex items-center"><input type="checkbox" name="destacada" <?= $p['destacada']?'checked':'' ?>> Destacada</label>
                </div>
                <div>
                    <input type="text" name="titulo" value="<?= htmlspecialchars($p['titulo']) ?>" placeholder="Título" class="border rounded px-2 py-1" required>
                    <?php if(isset($errores['titulo'])): ?><div class="text-red-600 text-sm"><?= $errores['titulo'] ?></div><?php endif; ?>
                </div>
                <div>
                    <input type="text" name="descripcion_breve" value="<?= htmlspecialchars($p['descripcion_breve']) ?>" placeholder="Descripción breve" class="border rounded px-2 py-1" required>
                    <?php if(isset($errores['descripcion_breve'])): ?><div class="text-red-600 text-sm"><?= $errores['descripcion_breve'] ?></div><?php endif; ?>
                </div>
                <div>
                    <input type="number" name="precio" value="<?= htmlspecialchars($p['precio']) ?>" placeholder="Precio" class="border rounded px-2 py-1" required>
                    <?php if(isset($errores['precio'])): ?><div class="text-red-600 text-sm"><?= $errores['precio'] ?></div><?php endif; ?>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Imagen destacada:</label>
                    <input type="file" name="imagen_destacada" accept="image/*" class="w-full">
                    <?php if(isset($errores['imagen_destacada'])): ?><div class="text-red-600 text-sm"><?= $errores['imagen_destacada'] ?></div><?php endif; ?>
                    <?php if($p['imagen_destacada']): ?>
                    <img src="../<?= htmlspecialchars($p['imagen_destacada']) ?>" class="h-20 w-32 object-cover rounded mt-2" alt="Imagen actual">
                    <?php endif; ?>
                </div>
                <div>
                    <input type="text" name="ubicacion" value="<?= htmlspecialchars($p['ubicacion']) ?>" placeholder="Ubicación" class="border rounded px-2 py-1">
                </div>
                <div>
                    <input type="text" name="mapa" value="<?= htmlspecialchars($p['mapa']) ?>" placeholder="URL Mapa" class="border rounded px-2 py-1">
                </div>
                <div class="col-span-2">
                    <textarea name="descripcion_larga" placeholder="Descripción larga" class="border rounded px-2 py-1" required><?= htmlspecialchars($p['descripcion_larga']) ?></textarea>
                    <?php if(isset($errores['descripcion_larga'])): ?><div class="text-red-600 text-sm"><?= $errores['descripcion_larga'] ?></div><?php endif; ?>
                </div>
            </div>
            <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded font-bold">Actualizar Propiedad</button>
        </form>
    </main>
</body>
</html>
