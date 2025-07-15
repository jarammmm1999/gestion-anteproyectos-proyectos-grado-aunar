<?php
header("Content-Type: application/json");

// Incluir el archivo que contiene la conexión o consulta
require_once "../../Model/Consultas.php"; 

// Obtener el ID del usuario desde los parámetros GET
$id_usuario_logueado = $_GET['id_usuario_logueado'];

$ins_MainModelo = new Consultas();

try {
    $consulta_notificaciones = "SELECT COUNT(*) AS mensajes_no_leidos
                                FROM mensajes
                                WHERE id_receptor = :id_usuario_logueado
                                AND leido = 0";
    $stmt = $ins_MainModelo->ejecutar_consultas_simples_two_ajax($consulta_notificaciones, [
        ':id_usuario_logueado' => $id_usuario_logueado
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $mensajes_no_leidos = $result['mensajes_no_leidos'] ?? 0; // Establecer en 0 si no hay datos

    echo json_encode(['mensajes_no_leidos' => $mensajes_no_leidos]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
