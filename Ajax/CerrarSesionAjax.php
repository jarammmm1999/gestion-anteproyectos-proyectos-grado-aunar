<?php

session_start(['name' => 'Smp']);

$peticionAjax = true;

require_once "../Model/Consultas.php"; 

$ins_MainModelo = new Consultas();

$sessionIdUsuario = (int)$_SESSION['id_usuario'];

$token = $_SESSION['token_usuario'];

// 1️⃣ Actualizar el estado del usuario (Desconectado)
$actualizar_estado_user =  $ins_MainModelo->ejecutar_consultas_simples_two_ajax(
    "UPDATE usuarios SET estado_conexion = 0 WHERE id = :idusuario",
    [":idusuario" => $sessionIdUsuario]
);

// 2️⃣ Verificar si la actualización fue exitosa
if ($actualizar_estado_user->rowCount() >= 0) {

    // 3️⃣ Obtener el último ID de sesión del usuario
    $ultimo_id_sesion =  $ins_MainModelo->ejecutar_consultas_simples_two_ajax(
        "SELECT id_sesion 
         FROM historial_sesiones 
         WHERE id_usuario = :idusuario 
         ORDER BY id_sesion DESC 
         LIMIT 1",
        [":idusuario" => $sessionIdUsuario]
    );

    if ($ultimo_id_sesion->rowCount() > 0) {
        $resultado = $ultimo_id_sesion->fetch(PDO::FETCH_ASSOC);
        $idSesion = $resultado['id_sesion'];

        // 4️⃣ Obtener la fecha y hora actual
        $fechaHoraActual = date('Y-m-d H:i:s');

        // 5️⃣ Actualizar el cierre de sesión del usuario
        $actualizar_estado_cerrarSession_user =  $ins_MainModelo->ejecutar_consultas_simples_two_ajax(
            "UPDATE historial_sesiones SET cierre_sesion = :fechaHora WHERE id_sesion = :idSesion",
            [
                ":fechaHora" => $fechaHoraActual,
                ":idSesion" => $idSesion
            ]
        );

        if ($actualizar_estado_cerrarSession_user->rowCount() >= 0) {
            session_unset();
            session_destroy();

            header("Location: " . SERVERURL . "home/");
            exit();
        }
    }
}



session_unset();
session_destroy();

// Retornar una respuesta exitosa
http_response_code(200);
exit();
?>