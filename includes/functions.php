<?php
// Funciones generales
function encriptar_contrasena($contrasena) {
    return password_hash($contrasena, PASSWORD_DEFAULT);
}
function verificar_contrasena($contrasena, $hash) {
    return password_verify($contrasena, $hash);
}
function redirigir($url) {
    header('Location: ' . $url);
    exit;
}
?>
