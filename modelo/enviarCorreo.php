<?php

require '..\src\PHPMailer.php';

require '..\src\SMTP.php';

require '..\src\Exception.php';

use PHPMailer\PHPMailer\PHPMailer; // Importa la clase PHPMailer
use PHPMailer\PHPMailer\Exception; // Importa la clase Exception para manejo de errores
// Crear una instancia de PHPMailer
$mail = new PHPMailer(true);

$nombre = $_POST["nombre"];
$correo = $_POST["correo"];
$asunto = $_POST["asunto"];
$mensaje = $_POST["mensaje"];
try {
    // Configuración del servidor SMTP
    $mail->isSMTP(); // Usar SMTP
    $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de Gmail
    $mail->SMTPAuth = true; // Autenticación SMTP
    $mail->Username = $correo; // Tu correo de Gmail
    $mail->Password = 'fpbk vpkv jusn tmkx'; // Tu contraseña de Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Cifrado TLS
    $mail->Port = 587; // Puerto SMTP de Gmail
    // Destinatarios
    $mail->setFrom($correo, $nombre);
    $mail->addAddress($correo, $nombre); // El correo de destino
    // Contenido del mensaje
    $mail->isHTML(true); // Correo en formato HTML
    $mail->Subject = $asunto;
    $mail->Body = $mensaje;
    $mail->AltBody = 'adios';
    // Enviar el correo
    $mail->send();
    echo 'Correo enviado con éxito';
} catch (Exception $e) {
    echo "El correo no pudo enviarse. Error de Mailer: {$mail->ErrorInfo}";
}
