    
<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/session.php';
// Leer configuración personalizada
$config = $conn->query("SELECT * FROM configuracion WHERE id=1")->fetch_assoc();
$default_principal = '#25344b';
$default_secundario = '#ffe600';
if (isset($_SESSION['id'])) {
    $usuario = $conn->query("SELECT color_principal, color_secundario FROM usuarios WHERE id=" . intval($_SESSION['id']))->fetch_assoc();
    $color_principal = ($usuario['color_principal'] && $usuario['color_principal'] !== $default_principal) ? $usuario['color_principal'] : ($config['color_principal'] ?? $default_principal);
    $color_secundario = ($usuario['color_secundario'] && $usuario['color_secundario'] !== $default_secundario) ? $usuario['color_secundario'] : ($config['color_secundario'] ?? $default_secundario);
} else {
    $color_principal = $config['color_principal'] ?? $default_principal;
    $color_secundario = $config['color_secundario'] ?? $default_secundario;
}
$mensaje_banner = $config['mensaje_banner'] ?? 'PERMITENOS SAYUDARTE A CUMPLIR<br>TUS SUEÑOS';
$info_quienes = $config['quienes_somos'] ?? '';
$facebook = $config['facebook'] ?? '#';
$instagram = $config['instagram'] ?? '#';
$youtube = $config['youtube'] ?? '#';
$direccion = $config['direccion'] ?? 'Cañas Guanacaste, 100 mts Este Parque de Cañas';
$telefono = $config['telefono'] ?? '8890-2030';
$email = $config['email'] ?? 'info@utnrealstate.com';
// Consulta para cargar propiedades
function cargar_propiedades($tipo = null, $destacada = null) {
    global $conn;
    $sql = "SELECT p.*, u.nombre as agente FROM propiedades p JOIN usuarios u ON p.agente_id = u.id WHERE 1";
    $params = [];
    $types = '';
    if ($tipo) {
        $sql .= " AND p.tipo = ?";
        $params[] = $tipo;
        $types .= 's';
    }
    if ($destacada !== null) {
        $sql .= " AND p.destacada = ?";
        $params[] = $destacada ? 1 : 0;
        $types .= 'i';
    }
    $sql .= " ORDER BY p.fecha_creacion DESC LIMIT 3";
    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();
    return $res;
}
$destacados = cargar_propiedades(null, true);
$ventas = cargar_propiedades('venta');
$alquileres = cargar_propiedades('alquiler');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTN Solutions Real State</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="js/main.js" defer></script>
</head>
<body class="bg-gray-100">
        <!-- Header -->
        <header style="background-color: #181c2a; color: #fff;" class="w-full px-0 pt-2 pb-0">
            <div class="max-w-full mx-auto w-full flex flex-row justify-between items-start">
                <div class="flex flex-col items-start">
                    <div class="flex flex-row items-center gap-2 mb-1">
                        <img src="img/logo.png" alt="Logo" class="h-10 mr-2">
                        <span class="font-bold text-sm leading-tight">UTN SOLUTIONS<br>REAL STATE</span>
                    </div>
                    <div class="flex flex-row gap-3 mt-1 ml-1">
                        <a href="<?= $facebook ?>" target="_blank"><img src="img/facebook.png" class="h-7" alt="Facebook"></a>
                        <a href="<?= $youtube ?>" target="_blank"><img src="img/youtube.png" class="h-7" alt="YouTube"></a>
                        <a href="<?= $instagram ?>" target="_blank"><img src="img/instagram.png" class="h-7" alt="Instagram"></a>
                    </div>
                </div>
                <div class="flex flex-col items-end">
                    <nav class="flex gap-6 font-bold text-base mt-1">
                        <a href="index.php" class="text-yellow-400">INICIO</a>
                        <a href="#quienes" class="text-yellow-400">QUINES SOMOS</a>
                        <a href="propiedades/listar.php?tipo=alquiler" class="text-yellow-400">ALQUILERES</a>
                        <a href="propiedades/listar.php?tipo=venta" class="text-yellow-400">VENTAS</a>
                        <a href="#contacto" class="text-yellow-400">CONTACTENOS</a>
                    </nav>
                    <div class="flex items-center gap-2 mt-2">
                        <form action="propiedades/buscar.php" method="get" class="flex items-center">
                            <input type="text" name="q" placeholder="Buscar..." class="rounded px-3 py-1 text-black bg-white w-40">
                            <button type="submit" class="bg-white text-black px-2 py-1 rounded ml-2 flex items-center justify-center"><svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></button>
                        </form>
                        <a href="login.php" class="ml-2"><img src="img/login.png" class="h-8" alt="Login"></a>
                    </div>
                </div>
            </div>
        </header>
    <!-- Banner -->
    <section class="relative h-72 flex items-center justify-center" style="background-image:url('img/banner.jpg'); background-size:cover; background-position:center;">
                <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <h1 class="text-white text-2xl md:text-3xl font-bold text-center tracking-wide px-4" style="font-family: 'Montserrat', Arial, sans-serif; letter-spacing:1px;">
                        <?= str_replace('<br>', ' ', $mensaje_banner) ?>
                    </h1>
                </div>
    </section>
    <!-- Quienes Somos -->
    <section id="quienes" class="bg-white py-12">
        <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center gap-8">
            <div class="flex-1">
                <h2 class="text-2xl font-bold mb-2 text-blue-900">QUIENES SOMOS</h2>
                <p class="text-gray-700 mb-4"><?= nl2br(htmlspecialchars($info_quienes)) ?></p>
            </div>
            <div class="flex-1 flex justify-center">
                <img src="img/quienes.jpg" alt="Quienes Somos" class="rounded shadow-lg h-44 w-80 object-cover">
            </div>
        </div>
    </section>
    <!-- Propiedades Destacadas -->
    <section class="bg-blue-900 py-12 text-white">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold mb-6 text-center tracking-wide">PROPIEDADES DESTACADAS</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php while($p = $destacados->fetch_assoc()): ?>
                <div class="bg-blue-900 border border-blue-900 rounded-lg shadow-lg p-4 flex flex-col items-center">
                    <img src="<?= htmlspecialchars($p['imagen_destacada']) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>" class="h-32 w-full object-cover mb-2 rounded">
                    <h3 class="font-bold text-lg italic mb-1"><?= htmlspecialchars($p['titulo']) ?></h3>
                    <p class="mb-2 text-center"><?= htmlspecialchars($p['descripcion_breve']) ?></p>
                    <p class="font-bold text-yellow-500 mb-2">Precio: $<?= number_format($p['precio'],0) ?></p>
                    <a href="propiedades/detalle.php?id=<?= $p['id'] ?>" class="mt-2 block bg-blue-900 text-white px-4 py-2 rounded text-center font-semibold">VER MAS...</a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <!-- Propiedades en Venta -->
    <section class="bg-white py-12">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-2xl font-bold mb-8 text-center text-blue-900">PROPIEDADES EN VENTA</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php while($p = $ventas->fetch_assoc()): ?>
                <div class="bg-gray-100 rounded shadow p-4 flex flex-col items-center">
                    <img src="<?= htmlspecialchars($p['imagen_destacada']) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>" class="h-32 w-full object-cover mb-2 rounded">
                    <h3 class="font-bold text-lg italic mb-1"><?= htmlspecialchars($p['titulo']) ?></h3>
                    <p class="mb-2 text-center"><?= htmlspecialchars($p['descripcion_breve']) ?></p>
                    <p class="font-bold text-blue-900 mb-2">Precio: $<?= number_format($p['precio'],0) ?></p>
                    <a href="propiedades/detalle.php?id=<?= $p['id'] ?>" class="mt-2 block border border-yellow-400 text-yellow-600 px-4 py-2 rounded text-center font-semibold">VER MAS...</a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <!-- Propiedades en Alquiler -->
    <section style="background-color: <?= $color_principal ?>; color: #fff;" class="py-12">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-2xl font-bold mb-8 text-center">PROPIEDADES EN ALQUILER</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php while($p = $alquileres->fetch_assoc()): ?>
                <div class="bg-white text-black rounded shadow p-4 flex flex-col items-center">
                    <img src="<?= htmlspecialchars($p['imagen_destacada']) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>" class="h-32 w-full object-cover mb-2 rounded">
                    <h3 class="font-bold text-lg italic mb-1"><?= htmlspecialchars($p['titulo']) ?></h3>
                    <p class="mb-2 text-center"><?= htmlspecialchars($p['descripcion_breve']) ?></p>
                    <p class="font-bold text-yellow-500 mb-2">Precio: $<?= number_format($p['precio'],0) ?></p>
                    <a href="propiedades/detalle.php?id=<?= $p['id'] ?>" class="mt-2 block bg-blue-900 text-white px-4 py-2 rounded text-center font-semibold">VER MAS...</a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <!-- Footer y Contacto -->
    <footer id="contacto" style="background-color: <?= $color_secundario ?>;" class="pt-0 pb-0">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 py-8 items-center">
        <div class="flex flex-col justify-center">
            <div class="flex items-center mb-2">
                <svg class="h-6 w-6 mr-2 text-black inline" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
                <span class="font-bold">Dirección:</span> <span class="ml-2"><?= htmlspecialchars($direccion) ?></span>
            </div>
            
            <div class="flex items-center mb-2">
                <svg class="h-6 w-6 mr-2 text-black inline" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.21c1.21.49 2.53.76 3.88.76a1 1 0 011 1v3.5a1 1 0 01-1 1C5.92 22 2 18.08 2 13.5a1 1 0 011-1h3.5a1 1 0 011 1c0 1.35.27 2.67.76 3.88a1 1 0 01-.21 1.11l-2.2 2.2z"/></svg>
                <span class="font-bold">Teléfono:</span> <span class="ml-2"><?= htmlspecialchars($telefono) ?></span>
            </div>
            <div class="flex items-center mb-2">
                <svg class="h-6 w-6 mr-2 text-black inline" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 2v.01L12 13 4 6.01V6h16zM4 18V8.99l8 7.99 8-7.99V18H4z"/></svg>
                <span class="font-bold">Email:</span> <span class="ml-2"><?= htmlspecialchars($email) ?></span>
            </div>
        </div>
        <div class="flex flex-col items-center justify-center">
            <img src="img/logo.png" class="h-36 mb-0" alt="Logo">
            <div class="font-bold text-xl text-center mb-4 text-white">UTN SOLUTIONS<br>REAL STATE</div>
            <div class="flex flex-row items-center gap-6 mt-2 justify-center">
                <a href="<?= $facebook ?>" aria-label="Facebook" target="_blank">
                    <svg fill="#1877F3" viewBox="0 0 24 24" class="h-10 w-15"><path d="M22.675 0h-21.35C.6 0 0 .6 0 1.326v21.348C0 23.4.6 24 1.326 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.797.143v3.24l-1.918.001c-1.504 0-1.797.715-1.797 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116C23.4 24 24 23.4 24 22.674V1.326C24 .6 23.4 0 22.675 0"></path></svg>
                </a>
                <a href="<?= $youtube ?>" aria-label="YouTube" target="_blank">
                    <svg fill="#FF0000" viewBox="0 0 24 24" class="h-10 w-10"><path d="M23.498 6.186a2.994 2.994 0 0 0-2.112-2.112C19.633 3.5 12 3.5 12 3.5s-7.633 0-9.386.574a2.994 2.994 0 0 0-2.112-2.112C0 7.939 0 12 0 12s0 4.061.502 5.814a2.994 2.994 0 0 0 2.112 2.112C4.367 20.5 12 20.5 12 20.5s7.633 0 9.386-.574a2.994 2.994 0 0 0 2.112-2.112C24 16.061 24 12 24 12s0-4.061-.502-5.814zM9.545 15.568V8.432l6.545 3.568-6.545 3.568z"></path></svg>
                </a>
                <a href="<?= $instagram ?>" aria-label="Instagram" target="_blank">
                    <svg fill="#E4405F" viewBox="0 0 24 24" class="h-10 w-10"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.334 3.608 1.308.975.974 1.246 2.242 1.308 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.334 2.633-1.308 3.608-.974.975-2.242 1.246-3.608 1.308-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.334-3.608-1.308-.975-.974-1.246-2.242-1.308-3.608C2.175 15.647 2.163 15.267 2.163 12s.012-3.584.07-4.85c.062-1.366.334-2.633 1.308-3.608C4.516 2.497 5.784 2.226 7.15 2.163 8.416 2.105 8.796 2.093 12 2.093zm0-2.163C8.741 0 8.332.013 7.052.072 5.771.131 4.659.414 3.678 1.395c-.98.98-1.263 2.092-1.322 3.373C2.013 5.741 2 6.151 2 12c0 5.849.013 6.259.072 7.539.059 1.281.342 2.393 1.322 3.373.981.981 2.093 1.264 3.374 1.323C8.332 23.987 8.741 24 12 24s3.668-.013 4.948-.072c1.281-.059 2.393-.342 3.374-1.323.98-.98 1.263-2.092 1.322-3.373.059-1.28.072-1.69.072-7.539 0-5.849-.013-6.259-.072-7.539-.059-1.281-.342-2.393-1.322-3.373-.981-.981-2.093-1.264-3.374-1.323C15.668.013 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zm0 10.162a3.999 3.999 0 1 1 0-7.998 3.999 3.999 0 0 1 0 7.998zm6.406-11.845a1.44 1.44 0 1 0 0 2.88 1.44 1.44 0 0 0 0-2.88z"></path></svg>
                </a>
            </div>
        </div>
        <div class="flex flex-col items-center justify-center">
            <form method="post" action="contacto.php" class="bg-gray-100 rounded-lg p-4 shadow-md w-full max-w-xs mx-auto">
                <h3 class="text-center font-bold mb-2">Contactanos</h3>
                <div class="mb-2">
                    <input type="text" name="nombre" placeholder="Nombre" class="w-full px-2 py-1 border rounded" required>
                </div>
                <div class="mb-2">
                    <input type="email" name="email" placeholder="Email" class="w-full px-2 py-1 border rounded" required>
                </div>
                <div class="mb-2">
                    <input type="text" name="telefono" placeholder="Teléfono" class="w-full px-2 py-1 border rounded" required>
                </div>
                <div class="mb-2">
                    <textarea name="mensaje" placeholder="Mensaje" class="w-full px-2 py-1 border rounded" rows="2" required></textarea>
                </div>
                <button type="submit" class="bg-blue-900 text-white px-4 py-1 rounded font-bold w-full">Enviar</button>
            </form>
        </div>
    </div>
        <div class="w-full bg-blue-900 py-2 mt-8">
            <div class="text-center text-white font-bold italic">Derechos Reservados 2024</div>
        </div>
    </footer>
</body>
</html>
