<?php
// Conexión a la base de datos
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'utn_realstate';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>
