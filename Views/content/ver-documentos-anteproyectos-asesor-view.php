<?php

if ($_SESSION['privilegio'] != 5  && $_SESSION['privilegio'] != 3 && $_SESSION['privilegio'] != 4 

&&  $_SESSION['privilegio'] != 1 &&  $_SESSION['privilegio'] != 2 &&  $_SESSION['privilegio'] != 6) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}

if (isset($_GET['views'])) {
    $ruta = explode(
        "/",
        $_GET['views']
    );

    $codigo = $ruta[1];
    $id = $ruta[2];
    $id = $ins_loginControlador->decryption_two($id);
}

$consulta_documentos = "SELECT * 
    FROM cargar_documento_anteproyectos
    WHERE id = '$id'";

$resultado_documentos = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_documentos);


?>

<div class="container-fluid container-documento">

<?php
    
 /****** Consultar quién realizó la retroalimentación **********/

    $consulta_info_ultima_retroalimentacion = "SELECT * 
    FROM retroalimentacion_anteproyecto 
    WHERE id = '$id'
    ORDER BY id_retroalimentacion DESC
    LIMIT 1";

    $resultado_ultima_retroalimentacion = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_info_ultima_retroalimentacion);

    if ($resultado_ultima_retroalimentacion->rowCount() > 0) {
    $datos_retroalimentacion = $resultado_ultima_retroalimentacion->fetch(PDO::FETCH_ASSOC);
    $numero_documento = $datos_retroalimentacion['numero_documento'];

    // Consulta para obtener la información del usuario
    $consulta_usuario = "SELECT * FROM usuarios WHERE numero_documento = '$numero_documento'";
    $resultado_usuario = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_usuario);

    if ($resultado_usuario->rowCount() > 0) {
        $datos_usuario = $resultado_usuario->fetch(PDO::FETCH_ASSOC);

        $nombre_completo = $datos_usuario['nombre_usuario'] . ' ' . $datos_usuario['apellidos_usuario'];
        $rol_usuario = $datos_usuario['id_rol'];

        if ($rol_usuario == 5) {
            echo '
            <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
                <strong>El director:</strong> ' . $nombre_completo . ' ha calificado este documento.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';

        }else if($rol_usuario == 6){
            echo '
            <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
                <strong>El director externo:</strong> ' . $nombre_completo . ' ha calificado este documento.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }


    } else {
        echo "No se encontró información del usuario con el documento: $numero_documento";
    }

    } else {
        echo '
        <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
             No se encontró ninguna retroalimentación.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }


    
    ?>


    <div class="row">
        <div class="col-9">
            <?php
            if ($resultado_documentos->rowCount() > 0) {
                $nombre_carpeta = SERVERURL . 'Views/document/anteproyectos/' . $codigo;
                foreach ($resultado_documentos->fetchAll(PDO::FETCH_ASSOC) as $row):
                    $ruta_completa = $nombre_carpeta . '/' . $row['documento'];
                    $ruta_completa_word = $nombre_carpeta . '/' . $row['nombre_archivo_word'];
            ?>
                    <embed src="<?= $ruta_completa ?>" width="100%" height="1024px" type="application/pdf">
            <?php
                endforeach;
            } else {
                echo ' <div class="alert alert-danger alertas-ms " role="alert">
                <div class="text-center">No hay documentos registrados </div>
                </div>';
                $codido_idea = false;
            }

            ?>
        </div>
        <?php
        $consulta_retroalimentacion = "SELECT * 
        FROM retroalimentacion_anteproyecto
        WHERE id = '$id'";

        $resultado_retroalimentacion = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_retroalimentacion);

        if ($resultado_retroalimentacion->rowCount() > 0) {

            $nombre_carpeta = SERVERURL . 'Views/document/anteproyectos/' . $codigo;

            $data = $resultado_retroalimentacion->fetch(PDO::FETCH_ASSOC);

            $ruta_completa_word_asesor = $nombre_carpeta . '/' . $data['documento'];

            if ($data['estado'] == 1) {
                $estado = 'En revision';
            }  if ($data['estado'] == 3) {
                $estado = 'Cancelado';
            }if ($data['estado'] == 2){
                $estado = 'Aprobado';
            }
           
        ?>
            <div class="col-3 ">
                <form class="mt-1 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/AnteproyectoAjax.php" method="POST" data-form="save" autocomplete="off">

                    <div class="mb-3 mb-2">
                        <label for="exampleFormControlTextarea1" class="form-label"><b>Observaciones generales </b></label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" disabled><?= $data['observacion_general'] ?></textarea>
                    </div>

                    <input type="hidden" name="id_retrolimentacion_editar" value ="<?= $ins_loginControlador->encryption($id) ?>">

                    <div class="mb-3 mb-2">
                        <label for="exampleFormControlTextarea6" class="form-label"><b>Estado </b></label>
                        <textarea class="form-control" id="exampleFormControlTextarea6" rows="3" disabled><?= $estado ?></textarea>
                    </div>
                   
                   <?php
                   if ($data['documento'] !== "None") { // Cambié la validación
                   ?>
                       <div class="mb-3 mb-2">
                           <label for="exampleFormControlTextarea1" class="form-label"><b>Documento word </b></label>
                           <a target="_blank" class="btn descargas" href="<?=$ruta_completa_word_asesor ?>" role="button">Descargar Documento &nbsp; <i class="fa-solid fa-download"></i></a>
                       </div>
                   <?php
                   }

                   if(in_array($_SESSION['privilegio'], [5, 6])){
                    ?>
                      
                   <div class="mb-3 mb-2">
                        <label for="exampleFormControlTextarea6" class="form-label"><b>Actualizar fecha entrega: </b></label>
                        <input type="datetime-local" id="fecha_revision" name="fecha_revision" class="input-fecha" >
                    </div>

                    
                    <div class="form-actions mt-5 mb-5">
                        <button type="submit"><i class="fa-regular fa-floppy-disk"></i> &nbsp; Actualizar fecha entrega</button>
                    </div>
                    <?php
                   }
                   ?>
                   
                 
                  

                </form>
            </div>
            <?php
        } else {

            if (in_array($_SESSION['privilegio'], [5, 6])) {
            ?>
                <div class="col-3 ">
                    <form class="user-form-two mt-1 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/AnteproyectoAjax.php" method="POST" data-form="save" autocomplete="off">

                        <div class="mb-3 mb-2">
                            <label for="exampleFormControlTextarea1" class="form-label"><b>Documento word </b></label>
                            <a target="_blank" class="btn descargas" href="<?= $ruta_completa_word ?>" role="button">Descargar Documento &nbsp; <i class="fa-solid fa-download"></i></a>
                        </div>


                        <div class="mb-3 mb-2">
                            <label for="exampleFormControlTextarea1" class="form-label"><b>Observacion General </b></label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" name="observacion_general_retroalimentacion" rows="3"></textarea>
                        </div>
                        
                        <input type="hidden" name="numero_documento_user_logueado" value="<?= $ins_loginControlador->encryption($_SESSION['numero_documento']) ?>">
                        <input type="hidden" name="codigo_anteproyecto" value="<?= $ins_loginControlador->encryption($codigo) ?>">
                        <input type="hidden" name="id_documento_cargado" value="<?= $ins_loginControlador->encryption($id) ?>">


                        <div class="mb-2">
                            <label for="archivo" class="form-label"><b>Archivo adjunto</b></label>
                            <div class="drag-area">
                                <label for="archivo" class="upload-label">
                                    <input type="file" id="archivo" name="archivo_user_anteproyecto"  hidden>
                                    <div class="file-display">
                                        <p>Arrastra y suelta el archivo aquí, o haz clic para seleccionarlo</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <label for="exampleFormControlTextarea1" class="form-label"><b>Esatdo anteproyecto: </b></label>
                        <select class="form-select" name="estado_retroalimentacion" aria-label="Default select example">
                            <option selected>Estado</option>
                            <option value="<?= $ins_loginControlador->encryption(1) ?>">En revisión</option>
                            <option value="<?= $ins_loginControlador->encryption(2) ?>">Aprobado</option>
                            <option value="<?= $ins_loginControlador->encryption(3) ?>">Cancelado</option>
                        </select>

                        <div class="mb-3 mt-3">
                            <label for="exampleFormControlTextarea1" class="form-label"><b>Fecha entrega siguiente avances: </b></label>
                            <input type="datetime-local" id="fecha_revision" name="fecha_revision" class="input-fecha" >
                        </div>

                        <div class="form-actions mt-5 mb-5">
                            <button type="submit"><i class="fa-regular fa-floppy-disk"></i> &nbsp; Registrar retroalimentación</button>
                        </div>
                    </form>

                </div>

        <?php
            }
        }

        ?>
    </div>
</div>