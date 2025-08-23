<?php
// Manejo de sesiones
session_start();
function usuario_autenticado() {
    return isset($_SESSION['usuario']);
}
function es_admin() {
    return isset($_SESSION['privilegio']) && $_SESSION['privilegio'] === 'administrador';
}
function es_agente() {
    return isset($_SESSION['privilegio']) && $_SESSION['privilegio'] === 'agente';
}
function cerrar_sesion() {
    session_unset();
    session_destroy();
}
if ((isset($_GET['action']) && $_GET['action'] === 'logout') || (isset($_POST['logout']))) {
    cerrar_sesion();
    header('Location: ../login.php');
    exit;
}
?>
