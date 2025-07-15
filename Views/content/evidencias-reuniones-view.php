<?php

if (isset($_GET['views'])) {
    $ruta = explode(
        "/",
        $_GET['views']
    );

    $codigoproyecto = $ruta[1];

    $codigo = $ruta[2];
}

if ($_SESSION['privilegio'] != 5 &&  $_SESSION['privilegio'] != 1 &&  $_SESSION['privilegio'] != 2 

&&  $_SESSION['privilegio'] != 3 &&  $_SESSION['privilegio'] != 4 ) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}

$privilegio_user_registrado = $_SESSION['privilegio'];

$consulta_documentos = "SELECT * 
    FROM anteproyectos
    WHERE codigo_anteproyecto = '$codigo'";


if ($codigoproyecto == 1) {

    $consulta_fechas = "SELECT DISTINCT DATE(fecha_creacion) as fecha_unica
    FROM evidencia_reuniones_anteproyectos
    WHERE codigo_anteproyecto = '$codigo'
    ORDER BY fecha_unica ASC
";

$resultado_evidencias_anteproyecto = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_fechas);

?>

<div class="card-container mt-5 mb-5">

<?php

if ($resultado_evidencias_anteproyecto->rowCount() > 0) {
    
    foreach ($resultado_evidencias_anteproyecto->fetchAll(PDO::FETCH_ASSOC) as $raw):

        $fecha_unica = $raw['fecha_unica'];

        ?>

        <a href="<?= SERVERURL ?>ver-evidencia/<?=$codigoproyecto?>/<?=$ins_loginControlador->encryption($fecha_unica)?>/<?=$ins_loginControlador->encryption($codigo)?>" class="card">
                <div class="card-header"><?=$fecha_unica?></div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/entrega.png" alt="Consulta de ideas">
                </div>
            </a>

        <?php

    endforeach;
}else{
    echo ' <div class="alert alert-danger alertas-ms " role="alert">
    <div class="text-center">No hay envidencia registrada para este proyecto </div>
    </div>';
}

?>
</div>

<?php

?>
    <form class="user-form mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/AnteproyectoAjax.php" method="POST" enctype="multipart/form-data" data-form="save" autocomplete="off">
        <h2><i class="fa-solid fa-upload"></i> Subir archivo</h2>
        <div class="form-grid two mt-3 mb-3">
            <?php

            $consulta_anteprotecto_estudiante = "SELECT * 
        FROM anteproyectos
        WHERE codigo_anteproyecto = '$codigo'";

            $resultado_consulta_anteproyecto = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_anteprotecto_estudiante);

            if ($resultado_consulta_anteproyecto->rowCount() > 0) {

                foreach ($resultado_consulta_anteproyecto->fetchAll(PDO::FETCH_ASSOC) as $row):

                    $codido_idea_estudiante = $row['codigo_anteproyecto'];
            ?>

                    <div class="form-floating">
                        <input type="text" class="form-control input_border" id="telefono" value="<?= $row['titulo_anteproyecto']; ?>" disabled>
                        <label for="nombreEstudiante">Título</label>
                    </div>

                    <div class="form-floating">
                        <input type="text" class="form-control input_border" id="correoasesor" value="<?= $row['palabras_claves']; ?>" disabled>
                        <label for="tituloProyecto">Palabras claves</label>
                    </div>

                    <input type="hidden" name="numero_documento_user_logueado_evidencia" value="<?= $ins_loginControlador->encryption($_SESSION['numero_documento']) ?>">

                    <input type="hidden" name="codigo_anteproyecto_evidencia" value="<?= $ins_loginControlador->encryption($codido_idea_estudiante) ?>">

            <?php endforeach;
            } ?>
        </div>
        <?php

        if (isset($_SESSION['privilegio']) && $_SESSION['privilegio'] == 5 || $_SESSION['privilegio'] == 2 || $_SESSION['privilegio'] == 1) {
        ?>
        
            <!-- Campo para adjuntar archivo -->
            <div class="mb-3">
                <label for="archivo" class="form-label">Archivo adjunto</label>
                <div class="drag-area">
                    <label for="archivo" class="upload-label">
                        <input type="file" id="archivo" name="evidencia_user_anteproyecto[]" multiple hidden>
                        <div class="file-display">
                            <p>Arrastra y suelta el archivo aquí, o haz clic para seleccionarlo</p>
                        </div>
                    </label>
                </div>
            </div>
        
            <!-- Botón para enviar -->
            <div class="form-actions mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane"></i> Enviar archivo
                </button>
            </div>
        
        <?php
        }
        
        ?>
       
    </form>
<?php
} else if ($codigoproyecto == 2) {

    $consulta_fechas = "SELECT DISTINCT DATE(fecha_creacion) as fecha_unica
    FROM evidencia_reuniones_proyectos
    WHERE codigo_proyecto = '$codigo'
    ORDER BY fecha_unica ASC
";

$resultado_evidencias_anteproyecto = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_fechas);

?>

<div class="card-container mt-5 mb-5">

<?php

if ($resultado_evidencias_anteproyecto->rowCount() > 0) {
    
    foreach ($resultado_evidencias_anteproyecto->fetchAll(PDO::FETCH_ASSOC) as $raw):

        $fecha_unica = $raw['fecha_unica'];

        ?>

        <a href="<?= SERVERURL ?>ver-evidencia/<?=$codigoproyecto?>/<?=$ins_loginControlador->encryption($fecha_unica)?>/<?=$ins_loginControlador->encryption($codigo)?>" class="card">
                <div class="card-header"><?=$fecha_unica?></div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/entrega.png" alt="Consulta de ideas">
                </div>
            </a>

        <?php

    endforeach;
}

?>
</div>

<?php

?>
    <form class="user-form mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" enctype="multipart/form-data" data-form="save" autocomplete="off">
        <h2><i class="fa-solid fa-upload"></i> Subir archivo</h2>
        <div class="form-grid two mt-3 mb-3">
            <?php

            $consulta_anteprotecto_estudiante = "SELECT * 
        FROM proyectos
        WHERE codigo_proyecto = '$codigo'";

            $resultado_consulta_anteproyecto = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_anteprotecto_estudiante);

            if ($resultado_consulta_anteproyecto->rowCount() > 0) {

                foreach ($resultado_consulta_anteproyecto->fetchAll(PDO::FETCH_ASSOC) as $row):

                    $codido_proyecto_estudiante = $row['codigo_proyecto'];
            ?>

                    <div class="form-floating">
                        <input type="text" class="form-control input_border" id="telefono" value="<?= $row['titulo_proyecto']; ?>" disabled>
                        <label for="nombreEstudiante">Título</label>
                    </div>

                    <div class="form-floating">
                        <input type="text" class="form-control input_border" id="correoasesor" value="<?= $row['palabras_claves']; ?>" disabled>
                        <label for="tituloProyecto">Palabras claves</label>
                    </div>

                    <input type="hidden" name="numero_documento_user_logueado_evidencia" value="<?= $ins_loginControlador->encryption($_SESSION['numero_documento']) ?>">

                    <input type="hidden" name="codigo_anteproyecto_evidencia" value="<?= $ins_loginControlador->encryption($codido_proyecto_estudiante) ?>">

            <?php endforeach;
            } ?>
        </div>
        <?php

                if (isset($_SESSION['privilegio']) && $_SESSION['privilegio'] == 5 || $_SESSION['privilegio'] == 2 || $_SESSION['privilegio'] == 1) {
                    ?>

                <!-- Campo para adjuntar archivo -->
                <div class="mb-3">
                    <label for="archivo" class="form-label">Archivo adjunto</label>
                    <div class="drag-area">
                        <label for="archivo" class="upload-label">
                            <input type="file" id="archivo" name="evidencia_user_anteproyecto[]" multiple hidden>
                            <div class="file-display">
                                <p>Arrastra y suelta el archivo aquí, o haz clic para seleccionarlo</p>
                            </div>
                        </label>
                    </div>
                </div>


                <!-- Botón para enviar -->
                <div class="form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Enviar archivo</button>
                </div>

            </form>
        <?php
        }
    



} else {
}



?>