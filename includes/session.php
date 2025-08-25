<?php
// Manejo de sesiones y protección de rutas
session_start();

function usuario_autenticado() {
    return isset($_SESSION['usuario']) && isset($_SESSION['privilegio']);
}

function es_admin() {
    return isset($_SESSION['privilegio']) && ($_SESSION['privilegio'] === 'admin' || $_SESSION['privilegio'] === 'administrador');
}

function es_agente() {
    return isset($_SESSION['privilegio']) && $_SESSION['privilegio'] === 'agente';
}

function proteger_ruta_admin() {
    if (!usuario_autenticado() || !es_admin()) {
        header('Location: ../login.php');
        exit;
    }
}

function proteger_ruta_agente() {
    if (!usuario_autenticado() || !es_agente()) {
        header('Location: ../login.php');
        exit;
    }
}

function cerrar_sesion() {
    session_unset();
    session_destroy();
}

// Cierre de sesión centralizado (logout.php)
// No se cierra sesión automáticamente en ningún otro archivo
?>
