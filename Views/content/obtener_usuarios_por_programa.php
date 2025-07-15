<?php
header("Content-Type: application/json");

// Incluir el archivo que contiene la conexión o consulta
require_once "../../Model/Consultas.php"; 

// Obtener el ID del programa desde los parámetros GET
$id_programa = $_GET['id_programa'];

$ins_MainModelo = new Consultas();

try {
    // Preparar la consulta para obtener la información de los usuarios asignados al programa
    $consulta_usuarios = "SELECT 
        u.nombre_usuario, 
        u.apellidos_usuario, 
        u.correo_usuario, 
        u.imagen_usuario, 
        u.telefono_usuario, 
        r.nombre_rol
    FROM 
        Asignar_usuario_facultades auf
    INNER JOIN 
        usuarios u ON auf.numero_documento = u.numero_documento
    INNER JOIN 
        roles_usuarios r ON u.id_rol = r.id_rol
    WHERE 
        auf.id_programa = :id_programa
    GROUP BY 
        u.numero_documento
";

    
    // Ejecutar la consulta
    $stmt = $ins_MainModelo->ejecutar_consultas_simples_two_ajax($consulta_usuarios, [
        ':id_programa' => $id_programa
    ]);

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver la información de los usuarios en formato JSON
    echo json_encode($usuarios);
} catch (Exception $e) {
    // Manejar errores y devolverlos en formato JSON
    echo json_encode(['error' => $e->getMessage()]);
}
