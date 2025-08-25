<?php
// Enviar correo y guardar contacto usando PHPMailer
require_once 'includes/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = trim($_POST['nombre'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $mensaje  = trim($_POST['mensaje'] ?? '');

    if (empty($nombre) || empty($email) || empty($telefono) || empty($mensaje)) {
        header('Location: index.php?contacto=error');
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO mensajes (nombre, email, telefono, mensaje) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $email, $telefono, $mensaje);
    $stmt->execute();
    $stmt->close();

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'compu2025178@gmail.com';
    $mail->Password   = 'xllo qnkz rxrl gevq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

    $mail->setFrom('compu2025178@gmail.com', 'UTN Solutions Real State');
    $mail->addAddress('compu2025178@gmail.com', 'UTN Solutions Real State');
    $mail->addReplyTo($email, $nombre);
        $mail->isHTML(true);
        $mail->Subject = "Mensaje de contacto desde la web";
        $mail->Body    = "<h3>Has recibido un nuevo mensaje de contacto</h3><p><b>Nombre:</b> {$nombre}</p><p><b>Email:</b> {$email}</p><p><b>Teléfono:</b> {$telefono}</p><p><b>Mensaje:</b><br>{$mensaje}</p>";
        $mail->send();
        header('Location: index.php?contacto=ok');
        exit;
    } catch (Exception $e) {
        header('Location: index.php?contacto=error');
        exit;
    }
} else {
    header('Location: index.php?contacto=error');
    exit;
}
