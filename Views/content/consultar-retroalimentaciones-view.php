<?php
if ($_SESSION['privilegio'] != 3 && $_SESSION['privilegio'] != 4) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}

if($_SESSION['privilegio']== 3){

    if(isset( $codido_idea_estudiante)){
       
        $consulta_documentos = "SELECT * 
        FROM cargar_documento_anteproyectos
        WHERE codigo_anteproyecto = '$codido_idea_estudiante'";

        $resultado_documentos = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_documentos);
        if($resultado_documentos->rowCount() > 0){

            ?>
                <div class="card-container mt-5 mb-5">

                
                <a href="<?= SERVERURL ?>evidencias-reuniones/1/<?=$codido_idea_estudiante ?>" class="card">
                     <div class="card-header">Evidencia reuniones</div>
                     <div class="card-content">
                         <img src="<?= SERVERURL ?>/Views/assets/images/investigacion.png" alt="Consulta de ideas">
                     </div>
                </a>

            <?php
                                
            foreach ($resultado_documentos->fetchAll(PDO::FETCH_ASSOC) as $row): 
            
                if($row['estado'] ==1){
                    $estado = "Pendiente por revisar";
                    $color = "danger";
                }else {
                    $estado = "Revisado";
                    $color = "success";
                }
            ?>
            <a href="<?= SERVERURL ?>ver-documentos-anteproyectos-asesor/<?= $row['codigo_anteproyecto']; ?>/<?= $ins_loginControlador->encryption($row['id']) ?>" class="card">
                    <div class="card-header"><?= $row['fecha_creacion']; ?> &nbsp; <button type="button" class="btn btn-<?= $color?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $estado; ?> ">
                    <i class="fa-solid fa-eye"></i>
                    </button></div>
                    <div class="card-content">
                        <img src="<?= SERVERURL ?>/Views/assets/images/anteproyectos.png" alt="Consulta de ideas">
                    </div>
                </a>
            <?php endforeach; }else{
            echo ' <div class="alert alert-danger alertas-ms " role="alert">
            <div class="text-center">No hay retroalimentaciones registradas para el anteproyecto </div>
            </div>';
            $codido_idea = false;
            ?>
            </div>
            <?php

        }
        
           
    }else{
        echo ' <div class="alert alert-danger alertas-ms " role="alert">
        <div class="text-center">No hay retroalimentaciones que mostrar </div>
        </div>';
    }


}else if($_SESSION['privilegio']==4){

    if(isset( $codido_proyecto_estudiante)){
       
        $consulta_documentos = "SELECT * 
        FROM cargar_documento_proyectos
        WHERE codigo_proyecto = '$codido_proyecto_estudiante'";

        $resultado_documentos = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_documentos);
        if($resultado_documentos->rowCount() > 0){

            ?>
                <div class="card-container mt-5 mb-5">

                <a href="<?= SERVERURL ?>evidencias-reuniones/2/<?=$codido_proyecto_estudiante ?>" class="card">
                     <div class="card-header">Evidencia reuniones</div>
                     <div class="card-content">
                         <img src="<?= SERVERURL ?>/Views/assets/images/investigacion.png" alt="Consulta de ideas">
                     </div>
                </a>

            <?php
                                
            foreach ($resultado_documentos->fetchAll(PDO::FETCH_ASSOC) as $row): 
            
                if($row['estado'] ==1){
                    $estado = "Pendiente por revisar";
                    $color = "danger";
                }else {
                    $estado = "Revisado";
                    $color = "success";
                }
            ?>
            <a href="<?= SERVERURL ?>ver-documentos-proyectos-asesor/<?= $row['codigo_proyecto']; ?>/<?= $ins_loginControlador->encryption($row['id']) ?>" class="card">
                    <div class="card-header"><?= $row['fecha_creacion']; ?> &nbsp; <button type="button" class="btn btn-<?= $color?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $estado; ?> ">
                    <i class="fa-solid fa-eye"></i>
                    </button></div>
                    <div class="card-content">
                        <img src="<?= SERVERURL ?>/Views/assets/images/anteproyectos.png" alt="Consulta de ideas">
                    </div>
                </a>
            <?php endforeach; }else{
            echo ' <div class="alert alert-danger alertas-ms " role="alert">
            <div class="text-center">No hay retroalimentaciones registradas para el Proyecto </div>
            </div>';
            $codido_idea = false;
            ?>
            </div>
            <?php
        }
    }
}