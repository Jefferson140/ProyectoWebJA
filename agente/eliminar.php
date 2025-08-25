<?php
require_once '../includes/db.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';
if (!es_agente() && !es_admin()) redirigir('../login.php');
$id = $_GET['id'] ?? 0;
$id_agente = $_SESSION['id'];
if ($id) {
    if (es_admin()) {
        $conn->query("DELETE FROM propiedades WHERE id=$id");
    } else {
        $conn->query("DELETE FROM propiedades WHERE id=$id AND agente_id=$id_agente");
    }
    header('Location: propiedades.php');
    exit;
}
?>
