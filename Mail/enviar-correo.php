<?php

require_once __DIR__ . '/../Model/MainModel.php';

// Incluir los archivos necesarios de PHPMailer
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

// Usar las clases de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Función para enviar correos
function enviarCorreo($correo_destinatario, $nombre_destinatario, $apellidos_destinatario, $asunto, $cuerpo_html, $cuerpo_texto)
{
    $mail = new PHPMailer(true);  // Instanciar PHPMailer

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';                                           
        $mail->Host       = HOSTMAIL;
        $mail->SMTPAuth   = true;
        $mail->Username   = USERNAMEMAIL;
        $mail->Password   = PASSWORDMAIL;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = PORTMAIL;

        // Remitente y destinatarios
        $mail->setFrom(USERNAMEMAIL, 'Aunar Gestion de Proyectos');
        $mail->addAddress($correo_destinatario, "$nombre_destinatario $apellidos_destinatario");

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpo_html;
        $mail->AltBody = $cuerpo_texto;

        // Enviar el correo y verificar el resultado
        return $mail->send();
    } catch (Exception $e) {
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
        return false;
    }
}
