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
    $ubicacion = $_POST['ubicacion'] ?? '';
    $url_mapa = $_POST['url_mapa'] ?? '';
    $descripcion_larga = $_POST['descripcion'] ?? '';
    // Validaciones
    if (!$tipo) $errores['tipo'] = 'Seleccione el tipo.';
    if (!$titulo) $errores['titulo'] = 'Ingrese el título.';
    if (!$descripcion_breve) $errores['descripcion_breve'] = 'Ingrese la descripción breve.';
    if (!$precio || !is_numeric($precio) || $precio <= 0) $errores['precio'] = 'Ingrese un precio válido.';
    if (!$descripcion_larga) $errores['descripcion'] = 'Ingrese la descripción completa.';
    if (!$ubicacion) $errores['ubicacion'] = 'Ingrese la ubicación.';
    if (!$url_mapa) $errores['url_mapa'] = 'Ingrese la URL del mapa.';
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
        $sql = "INSERT INTO propiedades (tipo, destacada, titulo, descripcion_breve, precio, agente_id, imagen_destacada, descripcion_larga, ubicacion, url_mapa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sississsss",
            $tipo,
            $destacada,
            $titulo,
            $descripcion_breve,
            $precio,
            $id_agente,
            $imagen_destacada,
            $descripcion_larga,
            $ubicacion,
            $url_mapa
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
        <?php if(es_admin()): ?>
            <a href="../admin/panel.php" class="bg-yellow-400 text-blue-900 px-3 py-1 rounded font-bold">Volver al panel</a>
        <?php else: ?>
            <a href="panel.php" class="bg-yellow-400 text-blue-900 px-3 py-1 rounded font-bold">Volver al panel</a>
        <?php endif; ?>
    </header>
    <main class="max-w-4xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6 text-blue-900">Mis Propiedades</h1>
        <?php if($mensaje): ?>
            <div class="bg-green-100 text-green-700 p-2 mb-2 rounded shadow-lg border border-green-300 text-center font-semibold animate-bounce">
                <svg class="inline-block w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                <?= $mensaje ?>
            </div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="bg-white p-8 rounded-xl shadow-lg mb-10 border border-gray-200">
            <h2 class="text-xl font-bold mb-4 text-blue-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-900" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v4a1 1 0 001 1h3m10-5h3a1 1 0 011 1v4a1 1 0 01-1 1h-3m-10 0v4a1 1 0 001 1h3m10-5h3a1 1 0 011 1v4a1 1 0 01-1 1h-3" /></svg>
                Agregar nueva propiedad
            </h2>
            <hr class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold mb-2 text-blue-900" title="Nombre de la propiedad">Título:</label>
                    <input type="text" name="titulo" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                    <?php if(isset($errores['titulo'])): ?><div class="text-red-600 text-sm mt-1"><?= $errores['titulo'] ?></div><?php endif; ?>
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-blue-900" title="Tipo de operación">Tipo:</label>
                    <select name="tipo" id="tipo" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required onchange="cambiarColorBoton()">
                        <option value="">Seleccione</option>
                        <option value="venta">Venta</option>
                        <option value="alquiler">Alquiler</option>
                    </select>
                    <?php if(isset($errores['tipo'])): ?><div class="text-red-600 text-sm mt-1"><?= $errores['tipo'] ?></div><?php endif; ?>
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-blue-900" title="Precio de la propiedad">Precio:</label>
                    <input type="number" name="precio" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                    <?php if(isset($errores['precio'])): ?><div class="text-red-600 text-sm mt-1"><?= $errores['precio'] ?></div><?php endif; ?>
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-blue-900" title="Ubicación de la propiedad">Ubicación:</label>
                    <input type="text" name="ubicacion" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                    <?php if(isset($errores['ubicacion'])): ?><div class="text-red-600 text-sm mt-1"><?= $errores['ubicacion'] ?></div><?php endif; ?>
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-blue-900" title="URL del mapa">URL Mapa:</label>
                    <input type="text" name="url_mapa" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                    <?php if(isset($errores['url_mapa'])): ?><div class="text-red-600 text-sm mt-1"><?= $errores['url_mapa'] ?></div><?php endif; ?>
                </div>
                <div>
                    <label class="block font-semibold mb-2 text-blue-900" title="Imagen principal de la propiedad">Imagen destacada:</label>
                    <input type="file" name="imagen_destacada" accept="image/*" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg" onchange="mostrarPreview(event)">
                    <div id="preview" class="mt-2"></div>
                    <?php if(isset($errores['imagen_destacada'])): ?><div class="text-red-600 text-sm mt-1"><?= $errores['imagen_destacada'] ?></div><?php endif; ?>
                </div>
                <div class="flex items-center mt-2 md:col-span-2" title="Marca si quieres que la propiedad sea destacada">
                    <input type="checkbox" name="destacada" id="destacada" class="mr-2 h-5 w-5 text-blue-900 focus:ring-blue-500">
                    <label for="destacada" class="font-semibold text-blue-900">¿Propiedad destacada?</label>
                </div>
                <div class="md:col-span-2">
                    <label class="block font-semibold mb-2 text-blue-900" title="Breve descripción para mostrar en listados">Descripción breve:</label>
                    <input type="text" name="descripcion_breve" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                    <?php if(isset($errores['descripcion_breve'])): ?><div class="text-red-600 text-sm mt-1"><?= $errores['descripcion_breve'] ?></div><?php endif; ?>
                </div>
                <div class="md:col-span-2">
                    <label class="block font-semibold mb-2 text-blue-900" title="Descripción completa de la propiedad">Descripción completa:</label>
                    <textarea name="descripcion" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" rows="3" required></textarea>
                    <?php if(isset($errores['descripcion'])): ?><div class="text-red-600 text-sm mt-1"><?= $errores['descripcion'] ?></div><?php endif; ?>
                </div>
            </div>
            <hr class="mt-8 mb-4">
            <button type="submit" name="agregar" id="btnAgregar" class="bg-blue-900 text-white px-6 py-2 rounded-lg font-bold mt-6 hover:bg-blue-800 transition flex items-center gap-2">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Agregar propiedad
            </button>
        </form>
        <script>
        function mostrarPreview(event) {
            const preview = document.getElementById('preview');
            preview.innerHTML = '';
            if(event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src='${e.target.result}' class='h-24 w-40 object-cover rounded-lg border border-gray-300 shadow'>`;
                };
                reader.readAsDataURL(event.target.files[0]);
            }
        }
        function cambiarColorBoton() {
            const tipo = document.getElementById('tipo').value;
            const btn = document.getElementById('btnAgregar');
            if(tipo === 'venta') {
                btn.classList.remove('bg-blue-900','hover:bg-blue-800');
                btn.classList.add('bg-yellow-400','hover:bg-yellow-300','text-blue-900');
            } else if(tipo === 'alquiler') {
                btn.classList.remove('bg-yellow-400','hover:bg-yellow-300','text-blue-900');
                btn.classList.add('bg-blue-900','hover:bg-blue-800','text-white');
            } else {
                btn.classList.remove('bg-yellow-400','hover:bg-yellow-300','text-blue-900');
                btn.classList.add('bg-blue-900','hover:bg-blue-800','text-white');
            }
        }
        </script>
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-xl shadow-lg mb-10 border border-gray-200">
                <thead class="bg-blue-900 text-white">
                    <tr>
                        <th class="p-3 text-left">ID</th>
                        <th class="p-3 text-left">Título</th>
                        <th class="p-3 text-left">Tipo</th>
                        <th class="p-3 text-left">Precio</th>
                        <th class="p-3 text-left">Imagen</th>
                        <th class="p-3 text-left">Destacada</th>
                        <th class="p-3 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($p = $propiedades->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="p-3 align-middle font-semibold text-blue-900"><?= $p['id'] ?></td>
                        <td class="p-3 align-middle">
                            <?= htmlspecialchars($p['titulo']) ?>
                        </td>
                        <td class="p-3 align-middle capitalize">
                            <?= htmlspecialchars($p['tipo']) ?>
                        </td>
                        <td class="p-3 align-middle font-bold text-yellow-600">$<?= number_format($p['precio'],0) ?></td>
                        <td class="p-3 align-middle">
                            <?php if($p['imagen_destacada']): ?>
                            <img src="../<?= htmlspecialchars($p['imagen_destacada']) ?>" class="h-14 w-24 object-cover rounded-lg border border-gray-300" alt="Imagen">
                            <?php endif; ?>
                        </td>
                        <td class="p-3 align-middle">
                            <?php if($p['destacada']): ?>
                                <span class="inline-block px-2 py-1 rounded-full bg-yellow-400 text-blue-900 font-bold text-xs shadow" title="Propiedad destacada">
                                    <svg class="w-4 h-4 inline-block mr-1 text-blue-900" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    Destacada
                                </span>
                            <?php else: ?>
                                <span class="inline-block px-2 py-1 rounded-full bg-gray-200 text-gray-600 font-bold text-xs shadow" title="Propiedad normal">Normal</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 align-middle">
                            <div class="flex flex-row gap-2 justify-center">
                                <a href="editar.php?id=<?= $p['id'] ?>" class="bg-blue-900 text-white px-4 py-1 rounded-lg font-bold hover:bg-blue-800 transition">Editar</a>
                                <a href="eliminar.php?id=<?= $p['id'] ?>" class="bg-red-600 text-white px-4 py-1 rounded-lg font-bold hover:bg-red-700 transition" onclick="return confirm('¿Eliminar propiedad?')">Eliminar</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
