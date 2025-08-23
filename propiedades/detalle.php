<?php
require_once '../includes/db.php';
$id = $_GET['id'] ?? null;
if (!$id) die('ID no especificado');
$stmt = $conn->prepare('SELECT p.*, u.nombre as agente FROM propiedades p JOIN usuarios u ON p.agente_id = u.id WHERE p.id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$p) die('Propiedad no encontrada');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($p['titulo']) ?> - Detalle</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-900 text-white p-4 flex justify-between items-center">
        <span class="font-bold text-lg">Detalle de Propiedad</span>
        <a href="listar.php" class="bg-yellow-400 text-blue-900 px-3 py-1 rounded font-bold">Volver al listado</a>
    </header>
    <main class="max-w-3xl mx-auto py-8">
        <div class="bg-white rounded shadow p-6 flex flex-col items-center">
            <?php if($p['imagen_destacada']): ?>
            <img src="../<?= htmlspecialchars($p['imagen_destacada']) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>" class="h-48 w-full object-cover mb-4 rounded">
            <?php endif; ?>
            <h1 class="text-2xl font-bold mb-2 text-blue-900"><?= htmlspecialchars($p['titulo']) ?></h1>
            <p class="mb-2 text-gray-700 text-center"><span class="font-bold">Descripción breve:</span> <?= htmlspecialchars($p['descripcion_breve'] ?? $p['descripcion']) ?></p>
            <p class="mb-2 text-gray-700 text-center"><span class="font-bold">Descripción larga:</span> <?= nl2br(htmlspecialchars($p['descripcion_larga'] ?? '')) ?></p>
            <p class="font-bold text-yellow-500 mb-2">Precio: $<?= number_format($p['precio'],0) ?></p>
            <p class="mb-2 text-blue-900"><span class="font-bold">Agente:</span> <?= htmlspecialchars($p['agente']) ?></p>
            <p class="mb-2 text-gray-600"><span class="font-bold">Tipo:</span> <?= htmlspecialchars($p['tipo']) ?></p>
            <p class="mb-2 text-gray-600"><span class="font-bold">Destacada:</span> <?= $p['destacada'] ? 'Sí' : 'No' ?></p>
            <p class="mb-2 text-gray-600"><span class="font-bold">Ubicación:</span> <?= htmlspecialchars($p['ubicacion'] ?? '') ?></p>
            <p class="mb-2 text-gray-600"><span class="font-bold">Mapa:</span> <?php if(!empty($p['mapa'])): ?><a href="<?= htmlspecialchars($p['mapa']) ?>" target="_blank" class="text-blue-900 underline">Ver mapa</a><?php endif; ?></p>
        </div>
    </main>
</body>
</html>
