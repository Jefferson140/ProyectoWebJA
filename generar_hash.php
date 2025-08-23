<?php
// Script para generar el hash de la contraseña "123"
$hash = password_hash("123", PASSWORD_DEFAULT);
echo "Hash para '123':<br>" . $hash;
?>
