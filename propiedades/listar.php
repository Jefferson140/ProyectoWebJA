<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
if (!usuario_autenticado()) {
    header('Location: ../login.php');
    exit;
}
$tipo = $_GET['tipo'] ?? null;
$destacada = isset($_GET['destacada']) ? intval($_GET['destacada']) : null;
$sql = "SELECT p.*, u.nombre as agente FROM propiedades p JOIN usuarios u ON p.agente_id = u.id WHERE 1";
if ($tipo) {
    $sql .= " AND p.tipo = '" . $conn->real_escape_string($tipo) . "'";
}
if ($destacada === 1) {
    $sql .= " AND p.destacada = 1";
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
        <span class="font-bold">Ver Propiedades</span>
        <?php
        if (es_admin()) {
            echo '<a href="../admin/panel.php" class="bg-yellow-400 text-blue-900 px-3 py-1 rounded font-bold">Volver al panel</a>';
        } elseif (es_agente()) {
            echo '<a href="../agente/panel.php" class="bg-yellow-400 text-blue-900 px-3 py-1 rounded font-bold">Volver al panel</a>';
        }
        ?>
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
                        <?php 
                        $img_path = !empty($p['imagen_destacada']) && file_exists('../'.$p['imagen_destacada']) 
                            ? '../'.htmlspecialchars($p['imagen_destacada']) 
                            : '../img/default.jpg';
                        ?>
                        <img src="<?= $img_path ?>" class="h-12 w-20 object-cover rounded" alt="Imagen">
                    </td>
                    <td class="p-2"><a href="detalle.php?id=<?= $p['id'] ?>" class="text-blue-900 font-bold">Ver más</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php mysqli_data_seek($res, 0); while($p = $res->fetch_assoc()): ?>
            <div class="bg-white rounded shadow p-4 flex flex-col items-center">
                <?php 
                $img_path = !empty($p['imagen_destacada']) && file_exists('../'.$p['imagen_destacada']) 
                    ? '../'.htmlspecialchars($p['imagen_destacada']) 
                    : '../img/default.jpg';
                ?>
                    <img src="<?= $img_path ?>" alt="<?= htmlspecialchars($p['titulo']) ?>" class="w-full max-h-64 object-cover rounded-xl mb-2 cursor-pointer" onclick="mostrarModal(<?= $p['id'] ?>)">
                <h3 class="font-bold text-lg italic mb-1 text-blue-900"><?= htmlspecialchars($p['titulo']) ?></h3>
                <p class="mb-2 text-center text-gray-700"><?= htmlspecialchars($p['descripcion_breve']) ?></p>
                <p class="font-bold text-yellow-500 mb-2">Precio: $<?= number_format($p['precio'],0) ?></p>
            </div>
            <?php endwhile; ?>
            <?php mysqli_data_seek($res, 0); while($p = $res->fetch_assoc()): ?>
            <div id="modal-<?= $p['id'] ?>" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full relative">
                    <button onclick="cerrarModal(<?= $p['id'] ?>)" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
                    <img src="<?= !empty($p['imagen_destacada']) && file_exists('../'.$p['imagen_destacada']) ? '../'.htmlspecialchars($p['imagen_destacada']) : '../img/default.jpg' ?>" alt="<?= htmlspecialchars($p['titulo']) ?>" class="h-48 w-full object-cover mb-2 rounded">
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
            <?php endwhile; ?>
        <script>
        function mostrarModal(id) {
            document.getElementById('modal-' + id).classList.remove('hidden');
        }
        function cerrarModal(id) {
            document.getElementById('modal-' + id).classList.add('hidden');
        }
        </script>
        </div>
    </main>
</body>
</html>
