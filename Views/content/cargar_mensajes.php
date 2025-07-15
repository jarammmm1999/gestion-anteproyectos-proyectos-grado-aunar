<?php

header("Content-Type: application/json");

$peticionAjax = true;
require_once "../../Model/Consultas.php"; 

$ins_MainModelo = new Consultas();

try {
    $emisor_id = $_GET['emisor_id'];
    $receptor_id = $_GET['receptor_id'];

    // Verificar si los parámetros existen
    if (!isset($emisor_id) || !isset($receptor_id)) {
        echo json_encode(['error' => 'Parámetros faltantes: emisor_id y/o receptor_id']);
        exit;
    }

    $sql = "SELECT * FROM mensajes 
            WHERE (id_emisor = :id_emisor AND id_receptor = :id_receptor)
            OR (id_emisor = :id_receptor AND id_receptor = :id_emisor)
            ORDER BY fecha_envio ASC";

    $consulta_usuarios = $ins_MainModelo->ejecutar_consultas_simples_two_ajax($sql, [
        ':id_emisor' => $emisor_id,
        ':id_receptor' => $receptor_id
    ]);

    if ($consulta_usuarios->rowCount() > 0) {
        $mensajes = [];
        while ($mensaje = $consulta_usuarios->fetch(PDO::FETCH_ASSOC)) {
            $mensajes[] = $mensaje;
        }
        echo json_encode($mensajes);
    } else {
        echo json_encode(['error' => 'No se encontraron mensajes entre los usuarios']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
    exit;
}
