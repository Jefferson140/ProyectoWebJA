
<?php
// Envío de mensajes de contacto por correo
function enviar_correo($nombre, $email, $telefono, $mensaje) {
    $destino = 'jefrodriguezgon@est.utn.ac.cr'; // Cambia por el correo deseado o usa el de configuración
    $asunto = 'Nuevo mensaje de contacto';
    $contenido = "Nombre: $nombre\nEmail: $email\nTeléfono: $telefono\nMensaje: $mensaje";
    $headers = "From: $email";
    return mail($destino, $asunto, $contenido, $headers);
}
