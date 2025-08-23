<?php
require_once '../includes/db.php';
$tipo = $_GET['tipo'] ?? null;
$sql = "SELECT p.*, u.nombre as agente FROM propiedades p JOIN usuarios u ON p.agente_id = u.id";
if ($tipo) {
    $sql .= " WHERE p.tipo = '" . $conn->real_escape_string($tipo) . "'";
}
$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propiedades</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-900 text-white p-4 flex justify-between items-center">
        <span class="font-bold text-lg">Propiedades</span>
        <a href="../index.php" class="bg-yellow-400 text-blue-900 px-3 py-1 rounded font-bold">Volver al inicio</a>
    </header>
    <main class="max-w-5xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6 text-blue-900">Propiedades <?= $tipo ? ucfirst($tipo) : '' ?></h1>
        <table class="w-full bg-white rounded shadow mb-8">
            <thead class="bg-blue-900 text-white">
                <tr>
                    <th class="p-2">Título</th>
                    <th class="p-2">Tipo</th>
                    <th class="p-2">Precio</th>
                    <th class="p-2">Agente</th>
                    <th class="p-2">Imagen</th>
                    <th class="p-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php mysqli_data_seek($res, 0); while($p = $res->fetch_assoc()): ?>
                <tr class="border-b">
                    <td class="p-2"><a href="detalle.php?id=<?= $p['id'] ?>" class="text-blue-900 font-bold underline"><?= htmlspecialchars($p['titulo']) ?></a></td>
                    <td class="p-2"><?= htmlspecialchars($p['tipo']) ?></td>
                    <td class="p-2">$<?= number_format($p['precio'],0) ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['agente']) ?></td>
                    <td class="p-2">
                        <?php if($p['imagen_destacada']): ?>
                        <img src="../<?= htmlspecialchars($p['imagen_destacada']) ?>" class="h-12 w-20 object-cover rounded" alt="Imagen">
                        <?php endif; ?>
                    </td>
                    <td class="p-2"><a href="detalle.php?id=<?= $p['id'] ?>" class="text-blue-900 font-bold">Ver más</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php mysqli_data_seek($res, 0); while($p = $res->fetch_assoc()): ?>
            <div class="bg-white rounded shadow p-4 flex flex-col items-center">
                <img src="<?= htmlspecialchars($p['imagen_destacada']) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>" class="h-32 w-full object-cover mb-2 rounded">
                <h3 class="font-bold text-lg italic mb-1 text-blue-900"><?= htmlspecialchars($p['titulo']) ?></h3>
                <p class="mb-2 text-center text-gray-700"><?= htmlspecialchars($p['descripcion_breve']) ?></p>
                <p class="font-bold text-yellow-500 mb-2">Precio: $<?= number_format($p['precio'],0) ?></p>
                <a href="detalle.php?id=<?= $p['id'] ?>" class="mt-2 block bg-blue-900 text-white px-4 py-2 rounded text-center font-semibold">VER MAS...</a>
            </div>
            <?php endwhile; ?>
        </div>
    </main>
</body>
</html>
