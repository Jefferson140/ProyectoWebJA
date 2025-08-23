<?php
require_once '../includes/db.php';
require_once '../includes/session.php';
if (!es_admin()) redirigir('../login.php');
$id = $_GET['id'] ?? 0;
if ($id) {
    $conn->query("DELETE FROM usuarios WHERE id=$id");
    header('Location: usuarios.php');
    exit;
}
?>
