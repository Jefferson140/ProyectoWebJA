<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
if (!es_agente() && !es_admin()) redirigir('../login.php');
require_once '../includes/db.php';
$id_agente = es_admin() ? ($_POST['agente_id'] ?? 1) : $_SESSION['id'];
$mensaje = '';
// Agregar propiedad
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $errores = [];
    $tipo = $_POST['tipo'] ?? '';
    $destacada = isset($_POST['destacada']) ? 1 : 0;
    $titulo = $_POST['titulo'] ?? '';
    $descripcion_breve = $_POST['descripcion_breve'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $descripcion_larga = $_POST['descripcion'] ?? '';
    // Validaciones
    if (!$tipo) $errores['tipo'] = 'Seleccione el tipo.';
    if (!$titulo) $errores['titulo'] = 'Ingrese el título.';
    if (!$descripcion_breve) $errores['descripcion_breve'] = 'Ingrese la descripción breve.';
    if (!$precio || !is_numeric($precio) || $precio <= 0) $errores['precio'] = 'Ingrese un precio válido.';
    if (!$descripcion_larga) $errores['descripcion'] = 'Ingrese la descripción completa.';
    // Validación de imagen
    $imagen_destacada = '';
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
        $sql = "INSERT INTO propiedades (tipo, destacada, titulo, descripcion_breve, precio, agente_id, imagen_destacada, descripcion_larga) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sississs",
            $tipo,
            $destacada,
            $titulo,
            $descripcion_breve,
            $precio,
            $id_agente,
            $imagen_destacada,
            $descripcion_larga
        );
        if ($stmt->execute()) {
            $mensaje = 'Propiedad agregada correctamente.';
        } else {
            $mensaje = 'Error al agregar propiedad.';
        }
        $stmt->close();
    } else {
        $mensaje = 'Corrija los errores indicados.';
    }
}
// Listar propiedades
if (es_admin()) {
    $propiedades = $conn->query("SELECT * FROM propiedades");
} else {
    $propiedades = $conn->query("SELECT * FROM propiedades WHERE agente_id = $id_agente");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Propiedades</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-900 text-white p-4 flex justify-between items-center">
        <span class="font-bold text-lg">Gestionar Propiedades</span>
        <a href="panel.php" class="bg-yellow-400 text-blue-900 px-3 py-1 rounded font-bold">Volver al panel</a>
    </header>
    <main class="max-w-4xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6 text-blue-900">Mis Propiedades</h1>
        <?php if($mensaje): ?><div class="bg-green-100 text-green-700 p-2 mb-2 rounded"><?= $mensaje ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow mb-8 space-y-4">
            <h2 class="text-lg font-bold mb-2 text-blue-900">Agregar nueva propiedad</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold mb-1">Título:</label>
                    <input type="text" name="titulo" class="w-full px-2 py-1 border rounded" required>
                    <?php if(isset($errores['titulo'])): ?><div class="text-red-600 text-sm"><?= $errores['titulo'] ?></div><?php endif; ?>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Tipo:</label>
                    <select name="tipo" class="w-full px-2 py-1 border rounded" required>
                        <option value="">Seleccione</option>
                        <option value="venta">Venta</option>
                        <option value="alquiler">Alquiler</option>
                    </select>
                    <?php if(isset($errores['tipo'])): ?><div class="text-red-600 text-sm"><?= $errores['tipo'] ?></div><?php endif; ?>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Precio:</label>
                    <input type="number" name="precio" class="w-full px-2 py-1 border rounded" required>
                    <?php if(isset($errores['precio'])): ?><div class="text-red-600 text-sm"><?= $errores['precio'] ?></div><?php endif; ?>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Imagen destacada:</label>
                    <input type="file" name="imagen_destacada" accept="image/*" class="w-full">
                    <?php if(isset($errores['imagen_destacada'])): ?><div class="text-red-600 text-sm"><?= $errores['imagen_destacada'] ?></div><?php endif; ?>
                </div>
                <div class="col-span-2">
                    <label class="block font-semibold mb-1">Descripción breve:</label>
                    <input type="text" name="descripcion_breve" class="w-full px-2 py-1 border rounded" required>
                    <?php if(isset($errores['descripcion_breve'])): ?><div class="text-red-600 text-sm"><?= $errores['descripcion_breve'] ?></div><?php endif; ?>
                </div>
                <div class="col-span-2">
                    <label class="block font-semibold mb-1">Descripción completa:</label>
                    <textarea name="descripcion" class="w-full px-2 py-1 border rounded" rows="3" required></textarea>
                    <?php if(isset($errores['descripcion'])): ?><div class="text-red-600 text-sm"><?= $errores['descripcion'] ?></div><?php endif; ?>
                </div>
            </div>
            <button type="submit" name="agregar" class="bg-blue-900 text-white px-4 py-2 rounded font-bold">Agregar propiedad</button>
        </form>
        <table class="w-full bg-white rounded shadow mb-8">
            <thead class="bg-blue-900 text-white">
                <tr>
                    <th class="p-2">ID</th>
                    <th class="p-2">Título</th>
                    <th class="p-2">Tipo</th>
                    <th class="p-2">Precio</th>
                    <th class="p-2">Imagen</th>
                    <th class="p-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($p = $propiedades->fetch_assoc()): ?>
                <tr class="border-b">
                    <td class="p-2"><?= $p['id'] ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['titulo']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['tipo']) ?></td>
                    <td class="p-2">$<?= number_format($p['precio'],0) ?></td>
                    <td class="p-2">
                        <?php if($p['imagen_destacada']): ?>
                        <img src="../<?= htmlspecialchars($p['imagen_destacada']) ?>" class="h-12 w-20 object-cover rounded" alt="Imagen">
                        <?php endif; ?>
                    </td>
                    <td class="p-2">
                        <a href="editar.php?id=<?= $p['id'] ?>" class="text-blue-900 font-bold mr-2">Editar</a>
                        <a href="eliminar.php?id=<?= $p['id'] ?>" class="text-red-600 font-bold" onclick="return confirm('¿Eliminar propiedad?')">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
