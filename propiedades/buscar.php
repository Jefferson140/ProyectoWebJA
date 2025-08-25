<?php
require_once '../includes/db.php';
$q = $_GET['q'] ?? '';
$error = '';
$resultados = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (trim($q) === '') {
        $error = 'Por favor ingrese un término de búsqueda.';
    } else {
    $sql = "SELECT p.*, u.nombre as agente FROM propiedades p JOIN usuarios u ON p.agente_id = u.id WHERE p.titulo LIKE ? OR p.descripcion_breve LIKE ?";
    $like = "%$q%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $like, $like);
        $stmt->execute();
        $res = $stmt->get_result();
        while($row = $res->fetch_assoc()) {
            $resultados[] = $row;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Propiedades</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-900 text-white p-4 flex justify-between items-center">
        <span class="font-bold text-lg">Buscar Propiedades</span>
        <a href="../index.php" class="bg-yellow-400 text-blue-900 px-3 py-1 rounded font-bold">Volver al inicio</a>
    </header>
    <main class="max-w-5xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6 text-blue-900">Resultados de búsqueda</h1>
        <form method="get" class="mb-6 flex gap-2">
            <input type="text" name="q" class="w-full px-2 py-1 border rounded" placeholder="Buscar propiedades..." value="<?= htmlspecialchars($q) ?>">
            <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded font-bold">Buscar</button>
        </form>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-2 mb-4 rounded text-center font-semibold"><?= $error ?></div>
        <?php endif; ?>
        <?php if (!$error): ?>
            <?php if (count($resultados) === 0): ?>
                <div class="bg-yellow-100 text-yellow-700 p-2 mb-4 rounded text-center font-semibold">No se encontraron propiedades para "<?= htmlspecialchars($q) ?>".</div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php foreach($resultados as $p): ?>
                    <div class="bg-white rounded shadow p-4 flex flex-col items-center">
                        <?php
                        $img_path = !empty($p['imagen_destacada']) ? '../' . $p['imagen_destacada'] : '';
                        if (!empty($p['imagen_destacada']) && file_exists($img_path)) {
                            $img_src = '../' . $p['imagen_destacada'];
                        } else {
                            $img_src = '../img/default.jpg';
                        }
                        ?>
                        <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>" class="h-32 w-full object-cover mb-2 rounded cursor-pointer" onclick="mostrarModal(<?= $p['id'] ?>)">
                        <h3 class="font-bold text-lg italic mb-1 text-blue-900"><?= htmlspecialchars($p['titulo']) ?></h3>
                        <p class="mb-2 text-center text-gray-700"><?= htmlspecialchars($p['descripcion_breve']) ?></p>
                        <p class="font-bold text-yellow-500 mb-2">Precio: $<?= number_format($p['precio'],0) ?></p>
                    <!-- Modal para mostrar detalles -->
                    <div id="modal-<?= $p['id'] ?>" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full relative">
                            <button onclick="cerrarModal(<?= $p['id'] ?>)" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
                            <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>" class="h-40 w-full object-cover mb-4 rounded">
                            <h2 class="font-bold text-2xl mb-2 text-blue-900"><?= htmlspecialchars($p['titulo']) ?></h2>
                            <p class="mb-2 text-gray-700"><span class="font-bold">Tipo:</span> <?= htmlspecialchars($p['tipo']) ?></p>
                            <p class="mb-2 text-gray-700"><span class="font-bold">Descripción breve:</span> <?= htmlspecialchars($p['descripcion_breve']) ?></p>
                            <?php if (!empty($p['descripcion_larga'])): ?>
                            <p class="mb-2 text-gray-700"><span class="font-bold">Descripción larga:</span> <?= htmlspecialchars($p['descripcion_larga']) ?></p>
                            <?php endif; ?>
                            <p class="mb-2 text-gray-700"><span class="font-bold">Precio:</span> $<?= number_format($p['precio'],0) ?></p>
                            <p class="mb-2 text-gray-700"><span class="font-bold">Agente:</span> <?= htmlspecialchars($p['agente']) ?></p>
                            <?php if (!empty($p['ubicacion'])): ?>
                            <p class="mb-2 text-gray-700"><span class="font-bold">Ubicación:</span> <?= htmlspecialchars($p['ubicacion']) ?></p>
                            <?php endif; ?>
                            <p class="mb-2 text-gray-700"><span class="font-bold">Fecha de creación:</span> <?= htmlspecialchars($p['fecha_creacion']) ?></p>
                        </div>
                    </div>
<script>
function mostrarModal(id) {
    document.getElementById('modal-' + id).classList.remove('hidden');
}
function cerrarModal(id) {
    document.getElementById('modal-' + id).classList.add('hidden');
}
</script>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</body>
</html>
