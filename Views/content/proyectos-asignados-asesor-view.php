<?php

if ($_SESSION['privilegio'] != 5 && $_SESSION['privilegio'] != 6) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}

    $consulta_proyectos = "SELECT 
        aa.codigo_proyecto, 
        a.titulo_proyecto, 
        a.palabras_claves, 
        a.fecha_creacion,
        f.nombre_facultad,
        p.nombre_programa
    FROM Asignar_asesor_anteproyecto_proyecto aa
    INNER JOIN proyectos a ON aa.codigo_proyecto = a.codigo_proyecto
    INNER JOIN facultades f ON a.id_facultad = f.id_facultad
    INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
    WHERE aa.numero_documento = '$documento_user_logueado'";

    $resultado_anteproyectos = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_proyectos);

    if($resultado_anteproyectos->rowCount() > 0){

        ?>
            <div class="card-container mt-5 mb-5">
        <?php
                            
        foreach ($resultado_anteproyectos->fetchAll(PDO::FETCH_ASSOC) as $row): 
            $codido_proyecto = $row['codigo_proyecto'];

            $consulta_compañero = "SELECT u.nombre_usuario, u.apellidos_usuario, a.codigo_proyecto,
            ae.titulo_proyecto, 
            ae.palabras_claves, 
            f.nombre_facultad, 
            p.nombre_programa 
            FROM asignar_estudiante_proyecto a
            INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
            INNER JOIN proyectos ae ON ae.codigo_proyecto = a.codigo_proyecto
            LEFT JOIN programas_academicos p ON ae.id_programa = p.id_programa
            LEFT JOIN facultades f ON p.id_facultad = f.id_facultad
            WHERE a.codigo_proyecto = '$codido_proyecto'";
        
            $resultado_consulta_compañero = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_compañero);
        ?>
        <a href="<?= SERVERURL ?>entregas-proyectos/<?= $row['codigo_proyecto']; ?>" class="card">
                <div class="card-header"><?= $row['codigo_proyecto']; ?> &nbsp; 
                <button type="button" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $row['titulo_proyecto']; ?> ">
                <i class="fa-solid fa-eye"></i>
                </button> 
                <button type="button" class="btn btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="
                 <?php foreach ($resultado_consulta_compañero->fetchAll(PDO::FETCH_ASSOC) as $raw): ?>
                    <?= $raw['nombre_usuario']. ' '.$raw['apellidos_usuario'] ; ?>
                
                <?php endforeach; ?>
                
                
                ">
                <i class="fa-solid fa-users"></i>
                </button></div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/anteproyectos.png" alt="Consulta de ideas">
                </div>
                <div class="card-footer">
                    <?= $row['nombre_programa']; ?>
                </div>
            </a>
        <?php endforeach; }else{
        echo ' <div class="alert alert-danger alertas-ms " role="alert">
                <div class="text-center">No hay proyectos asignados </div>
                </div>';
        $codido_idea = false;
        }

        ?>
        </div>
        