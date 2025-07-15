<?php

if ($_SESSION['privilegio'] != 5 && $_SESSION['privilegio'] != 1 && $_SESSION['privilegio'] != 2 && $_SESSION['privilegio'] != 6) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}


if($_SESSION['privilegio'] == 5){
    $consulta_proyectos_jurado_Asignado = "SELECT 
    aa.codigo_proyecto, 
    a.titulo_proyecto, 
    a.palabras_claves, 
    a.fecha_creacion,
    f.nombre_facultad,
    p.nombre_programa,
    IFNULL(GROUP_CONCAT(CONCAT(u.nombre_usuario, ' ', u.apellidos_usuario) SEPARATOR ', '), 'Sin estudiantes asignados') AS estudiantes_asignados
    FROM Asignar_jurados_proyecto aa
    INNER JOIN proyectos a ON aa.codigo_proyecto = a.codigo_proyecto
    INNER JOIN facultades f ON a.id_facultad = f.id_facultad
    INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
    LEFT JOIN asignar_estudiante_proyecto ae ON a.codigo_proyecto = ae.codigo_proyecto
    LEFT JOIN usuarios u ON ae.numero_documento = u.numero_documento
    WHERE aa.numero_documento = '$documento_user_logueado'
    GROUP BY aa.codigo_proyecto, a.titulo_proyecto, a.palabras_claves, a.fecha_creacion, f.nombre_facultad, p.nombre_programa";

}else if($_SESSION['privilegio'] == 2){

    $documento_user_logueado = $_SESSION['numero_documento'];

    $privilegio_usuario_registrado = $_SESSION['privilegio'];

    $consulta = "SELECT 
        p.id_programa
    FROM Asignar_usuario_facultades auf
    INNER JOIN programas_academicos p ON auf.id_programa = p.id_programa
    WHERE auf.numero_documento = '$documento_user_logueado'";
    
    // Ejecutar la consulta
    $consulta_facultades = $ins_loginControlador->ejecutar_consultas_simples_two($consulta);
    
    // Crear un array para almacenar los ID de los programas
    $programas_ids = [];
    
    while ($fila = $consulta_facultades->fetch(PDO::FETCH_ASSOC)) {
        $programas_ids[] = (int) $fila['id_programa'];
    }
    
    // Verificar que el array no esté vacío antes de usarlo en la siguiente consulta
    if (!empty($programas_ids)) {
        $programas_ids_str = implode(',', $programas_ids); // Convertir a cadena separada por comas para la consulta SQL
    
        // **Paso 2: Usar el array en la consulta de proyectos**
        $consulta_proyectos_jurado_Asignado = "SELECT 
            aa.codigo_proyecto, 
            a.titulo_proyecto, 
            a.palabras_claves, 
            a.fecha_creacion,
            f.nombre_facultad,
            p.nombre_programa,
            IFNULL(GROUP_CONCAT(CONCAT(u.nombre_usuario, ' ', u.apellidos_usuario) SEPARATOR ', '), 'Sin estudiantes asignados') AS estudiantes_asignados
        FROM Asignar_jurados_proyecto aa
        INNER JOIN proyectos a ON aa.codigo_proyecto = a.codigo_proyecto
        INNER JOIN facultades f ON a.id_facultad = f.id_facultad
        INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
        LEFT JOIN asignar_estudiante_proyecto ae ON a.codigo_proyecto = ae.codigo_proyecto
        LEFT JOIN usuarios u ON ae.numero_documento = u.numero_documento
        WHERE a.id_programa IN ($programas_ids_str) 
        GROUP BY aa.codigo_proyecto, a.titulo_proyecto, a.palabras_claves, a.fecha_creacion, f.nombre_facultad, p.nombre_programa;
        ";
    } else {
        // Si el usuario no tiene programas asignados, la consulta no traerá resultados
        $consulta_proyectos_jurado_Asignado = "";
    }
    

}else if($_SESSION['privilegio'] == 1){

        $consulta_proyectos_jurado_Asignado = "SELECT 
        aa.codigo_proyecto, 
        aa.titulo_proyecto, 
        aa.palabras_claves, 
        aa.fecha_creacion,
        f.nombre_facultad,
        p.nombre_programa,
        IFNULL(GROUP_CONCAT(CONCAT(u.nombre_usuario, ' ', u.apellidos_usuario) SEPARATOR ', '), 'Sin estudiantes asignados') AS estudiantes_asignados
    FROM proyectos aa
    INNER JOIN facultades f ON aa.id_facultad = f.id_facultad
    INNER JOIN programas_academicos p ON aa.id_programa = p.id_programa
    LEFT JOIN asignar_estudiante_proyecto ae ON aa.codigo_proyecto = ae.codigo_proyecto
    LEFT JOIN usuarios u ON ae.numero_documento = u.numero_documento
    WHERE aa.estado = 'Aprobado'
    GROUP BY aa.codigo_proyecto, aa.titulo_proyecto, aa.palabras_claves, aa.fecha_creacion, f.nombre_facultad, p.nombre_programa";


}

$resultado_proyectos_jurados = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_proyectos_jurado_Asignado);

if ($resultado_proyectos_jurados->rowCount() > 0) {

?>
    <div class="card-container mt-5 mb-5">

    <?php
    
    if($_SESSION['privilegio'] == 1){
        ?>
    <a href="<?= SERVERURL ?>fecha-sustentacion/" class="card">
            <div class="card-header">Establecer fecha sustentación </div>
            <div class="card-content">
            <img src="<?= SERVERURL ?>/Views/assets/images/calendario.png">
            </div>
        </a>    
        <?php
    }
    
    ?>

    
        <?php
        foreach ($resultado_proyectos_jurados->fetchAll(PDO::FETCH_ASSOC) as $row): ?>

            <a href="<?= SERVERURL ?>calificar-proyectos/<?= $row['codigo_proyecto']; ?>/" class="card">
                <div class="card-header"><?= $row['codigo_proyecto']; ?>
                    <button type="button" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $row['titulo_proyecto']; ?> ">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= htmlspecialchars($row['estudiantes_asignados']); ?>">
                        <i class="fa-solid fa-users"></i>
                    </button>
                </div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/calificacion.png" alt="Consulta de ideas">
                </div>
                <div class="card-footer">
                 <?= $row['nombre_programa']; ?>
                </div>
            </a>

    <?php

        endforeach;
    } else {

            if($_SESSION['privilegio'] == 1){

                echo ' <div class="alert alert-danger alertas-ms " role="alert">
                <div class="text-center">No hay proyectos que mostrar </div>
                </div>';

            }else{
                echo ' <div class="alert alert-danger alertas-ms " role="alert">
                <div class="text-center">No hay proyectos asignados </div>
                </div>';
            }

       

        $codido_idea = false;
    }

    ?>
    </div>
