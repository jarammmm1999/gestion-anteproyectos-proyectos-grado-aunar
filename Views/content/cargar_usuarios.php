<?php
header("Content-Type: application/json");

$peticionAjax = true;
require_once "../../Model/Consultas.php";

session_start();

if (!isset($_GET['emisor_id'])) {
    echo json_encode(['error' => 'ID de usuario no encontrado en la sesión.']);
    exit;
}

$id_usuario_logueado = $_GET['emisor_id'];

$ins_MainModelo = new Consultas();

$sql_usuarios = " SELECT u.id, u.nombre_usuario, u.apellidos_usuario, u.imagen_usuario,
           (SELECT COUNT(*) FROM mensajes 
            WHERE mensajes.id_emisor = u.id 
              AND mensajes.id_receptor = :id_usuario_logueado 
              AND mensajes.leido = 0) AS mensajes_no_leidos,
           (SELECT COUNT(*) FROM mensajes 
            WHERE mensajes.id_emisor = u.id 
              AND mensajes.id_receptor = :id_usuario_logueado) AS mensajes_totales
    FROM usuarios u
    WHERE u.id != :id_usuario_logueado";

$consulta_usuarios = $ins_MainModelo->ejecutar_consultas_simples_two_ajax($sql_usuarios, [
    ':id_usuario_logueado' => $id_usuario_logueado
]);

if ($consulta_usuarios->rowCount() > 0) {
    $usuarios_array = array();
    while ($usuarios = $consulta_usuarios->fetch(PDO::FETCH_ASSOC)) {
        $usuarios_array[] = array(
            'id' => $usuarios['id'],
            'nombre' => $usuarios['nombre_usuario'] . ' ' . $usuarios['apellidos_usuario'],
            'imagen' => $usuarios['imagen_usuario'], // Añadir la imagen del usuario
            'mensajes_no_leidos' => $usuarios['mensajes_no_leidos'],
            'mensajes_totales' => $usuarios['mensajes_totales']
        );
    }
    echo json_encode($usuarios_array);
} else {
    echo json_encode(['error' => 'No se encontraron usuarios en el sistema.']);
}
