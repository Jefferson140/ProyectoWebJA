<?php
require_once 'includes/session.php';
cerrar_sesion();
header('Location: login.php');
exit;
