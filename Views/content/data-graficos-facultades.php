<?php
// Inicializar un arreglo para los datos de facultades y programas
$data = [
    'facultades' => [],
    'programas' => [],
    'usuarios' => []
];

// Consulta para obtener todas las facultades
$consulta_facultades = "SELECT id_facultad, nombre_facultad FROM facultades ORDER BY nombre_facultad";
$resultado_facultades = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_facultades);

if ($resultado_facultades->rowCount() > 0) {
    while ($row = $resultado_facultades->fetch(PDO::FETCH_ASSOC)) {
        $id_facultad = $row['id_facultad'];
        $data['facultades'][] = $row['nombre_facultad']; // Agregar el nombre de la facultad al array

        // Obtener los programas y usuarios por facultad
        $consulta_programas = "SELECT 
            p.nombre_programa,
            (SELECT COUNT(DISTINCT auf.numero_documento) 
             FROM Asignar_usuario_facultades auf 
             WHERE auf.id_programa = p.id_programa) AS total_usuarios
        FROM programas_academicos p
        WHERE p.id_facultad = '$id_facultad'";

        $resultado_programas = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_programas);

        $programas = [];
        $usuarios = [];
        if ($resultado_programas->rowCount() > 0) {
            while ($programa = $resultado_programas->fetch(PDO::FETCH_ASSOC)) {
                $programas[] = $programa['nombre_programa']; // Nombre del programa
                $usuarios[] = $programa['total_usuarios'];   // Total de usuarios
            }
        }

        // Agregar programas y usuarios al array
        $data['programas'][] = $programas;
        $data['usuarios'][] = $usuarios;
    }
}

// Convertir los datos a JSON y pasarlos a JavaScript
echo "<script>const chartDataFacultades = " . json_encode($data) . ";</script>";

