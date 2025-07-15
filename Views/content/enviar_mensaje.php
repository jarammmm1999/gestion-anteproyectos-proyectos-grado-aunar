<?php

header("Content-Type: application/json");

require_once "../../Model/Consultas.php"; 

$ins_MainModelo = new Consultas();

// Obtener el cuerpo de la solicitud y decodificar el JSON
$data = json_decode(file_get_contents("php://input"), true);

$emisor_id = $data['emisor_id'];
$receptor_id = $data['receptor_id'];
$mensaje = $data['mensaje'];


// Verificar que todos los datos necesarios están presentes
if (!isset($emisor_id) || !isset($receptor_id) || !isset($mensaje) || trim($mensaje) === '') {
    echo json_encode(['success' => false, 'error' => 'Datos faltantes o mensaje vacío']);
    exit;
}

try {
    // Preparar y ejecutar la consulta para insertar el mensaje
    $sql = "INSERT INTO mensajes (id_emisor, id_receptor, mensaje, fecha_envio, leido)
            VALUES (:id_emisor, :id_receptor, :mensaje, NOW(), 0)";

    $stmt = $ins_MainModelo->ejecutar_consultas_simples_two_ajax($sql, [
        ':id_emisor' => $emisor_id,
        ':id_receptor' => $receptor_id,
        ':mensaje' => $mensaje
    ]);

    if ($stmt) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo guardar el mensaje']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error en la consulta: ' . $e->getMessage()]);
}
