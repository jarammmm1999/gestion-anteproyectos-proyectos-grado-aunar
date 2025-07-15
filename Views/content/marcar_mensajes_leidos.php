<?php
header("Content-Type: application/json");

// Incluir el archivo que contiene la conexión o consulta
require_once "../../Model/Consultas.php"; 

// Obtener el ID del usuario logueado y el usuario con el que está conversando desde GET
$id_usuario_logueado = $_GET['id_usuario_logueado'];
$id_usuario_conversacion = $_GET['id_usuario_conversacion'];

$ins_MainModelo = new Consultas();

try {
    $sql = "UPDATE mensajes 
            SET leido = 1 
            WHERE id_emisor = :id_usuario_conversacion 
              AND id_receptor = :id_usuario_logueado
              AND leido = 0";

    $stmt = $ins_MainModelo->ejecutar_consultas_simples_two_ajax($sql, [
        ':id_usuario_conversacion' => $id_usuario_conversacion,
        ':id_usuario_logueado' => $id_usuario_logueado
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
