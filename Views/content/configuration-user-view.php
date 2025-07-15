<?php

if (isset($_GET['views'])) {
    $ruta = explode(
        "/",
        $_GET['views']
    );

    $privilegio = $ruta[1];
    $numero_documento_user = $ruta[2];
    $privilegio = $ins_loginControlador->decryption_two($privilegio);
    $numero_documento_user = $ins_loginControlador->decryption_two($numero_documento_user);
}




$consulta = "SELECT * FROM usuarios WHERE numero_documento = '$numero_documento_user'";
$consulta_informacion_user = $ins_loginControlador->ejecutar_consultas_simples_two($consulta);

if ($consulta_informacion_user) {
    $usuario = $consulta_informacion_user->fetch(PDO::FETCH_ASSOC);
    $nombre_usuario = $usuario['nombre_usuario'];
    $apellido_usuario = $usuario['apellidos_usuario'];
    $telefono_usuario = $usuario['telefono_usuario'];
    $correo_usuario = $usuario['correo_usuario'];

    if ($usuario['imagen_usuario'] == null) {
        $imagen = 'AvatarNone.png';
    } else {
        $imagen = $usuario['imagen_usuario'];
    }

    $id_rol =  $usuario['id_rol'];

    $sql_rol = "SELECT nombre_rol from roles_usuarios where id_rol = $id_rol";

    $consulta_rol = $ins_loginControlador->ejecutar_consultas_simples_two($sql_rol);
    if ($consulta_rol) {
        $rol = $consulta_rol->fetch(PDO::FETCH_ASSOC);
        if ($rol) {
            $rol = $rol['nombre_rol'];
        } else {
            $rol = "No se encontró el rol.";
        }
    }

    /**************consulta facultades******************** */


    $documento_user_logueado = $_SESSION['numero_documento'];
    $consulta = "SELECT 
    auf.numero_documento,
    f.nombre_facultad, 
    p.nombre_programa
    FROM Asignar_usuario_facultades auf
    INNER JOIN facultades f ON auf.id_facultad = f.id_facultad
    LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
    WHERE auf.numero_documento = '$documento_user_logueado'
    GROUP BY 
    auf.numero_documento, 
    f.nombre_facultad, 
    p.nombre_programa;";

    $consulta_facultades = $ins_loginControlador->ejecutar_consultas_simples_two($consulta);

    $facultades = [];
    $programas = [];

    if ($consulta_facultades->rowCount() > 0) {
        while ($row = $consulta_facultades->fetch(PDO::FETCH_ASSOC)) {
            // Agregar facultad y programa al array si no están repetidos
            if (!in_array($row['nombre_facultad'], $facultades)) {
                $facultades[] = $row['nombre_facultad'];
            }
            if (!in_array($row['nombre_programa'], $programas)) {
                $programas[] = $row['nombre_programa'];
            }
        }
    }
}



?>

<div class="container-fluid content-text-information">
    <div class="user-container">
        <div class="user-image">
            <img src="<?= SERVERURL ?>/Views/assets/images/avatar/<?= $imagen ?>" alt="imagen user">
        </div>
        <div class="user-details">
            <h2 class="user-title">Detalles de usuario</h2>
            <p><strong>Nombre: </strong> <?= $nombre_usuario . ' ' . $apellido_usuario ?></p>
            <p><strong>Correo: </strong> <?= $correo_usuario ?></p>
            <p><strong>Teléfono: </strong><?= $telefono_usuario ?></p>
            <p><strong>Rol: </strong> <?= $rol ?></p>
            <p><strong>Facultad: </strong>
                <?php
                echo empty($facultades) ? "No se encuentran facultades asociadas." : implode(", ", $facultades);
                ?>
            </p>

            <p><strong>Programa: </strong>
                <?php
                echo empty($programas) ? "No se encuentran programas asociados." : implode(", ", $programas);
                ?>
            </p>

            <div class="accordion" id="collageAccordion">
            <!-- Actualizar informacion usuarios -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collage1">
                        Actualizar Información usuario
                    </button>
                </h2>
                <div id="collage1" class="accordion-collapse collapse" data-bs-parent="#collageAccordion">
                    <div class="accordion-body">
                        <form class="user-form-two mt-1 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/UsuarioAjax.php" method="POST" data-form="save" autocomplete="off">
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control input_border" id="floatingNombre" name="nombre_usuario_reg" placeholder="Password" value="<?= $nombre_usuario ?>">
                                <label for="floatingNombre mb-4">Nombre de usuario</label>
                            </div>

                            <div class="form-floating mb-2">
                                <input type="text" class="form-control input_border" id="floatingNombre" name="apellido_usuario_reg" placeholder="Password" value="<?= $apellido_usuario ?>">
                                <label for="floatingNombre mb-4">Apellido de usuario</label>
                            </div>

                            <div class="form-floating mb-2">
                                <input type="email" class="form-control input_border" id="floatingNombre" name="email_usuario_reg" placeholder="Password" value="<?= $correo_usuario ?>">
                                <label for="floatingNombre mb-4">Correo de usuario</label>
                            </div>

                            <div class="form-floating mb-2">
                                <input type="password" class="form-control input_border" id="floatingNombre" name="password_usuario_actual" placeholder="Password">
                                <label for="floatingNombre mb-4">Contraseña actual usuario</label>
                            </div>

                            <div class="form-floating mb-2">
                                <input type="password" class="form-control input_border" id="floatingNombre" name="password_usuario_reg" placeholder="Password">
                                <label for="floatingNombre mb-4">Contraseña</label>
                            </div>

                            <div class="form-floating mb-2">
                                <input type="password" class="form-control input_border" id="floatingNombre" name="confirm_password_usuario_reg" placeholder="Password">
                                <label for="floatingNombre mb-4">Confirmar Contraseña</label>
                            </div>


                            <input type="hidden" name="numero_documento_user_logueado" value="<?= $ins_loginControlador->encryption($_SESSION['numero_documento']) ?>">



                            <div class="mb-2">
                                <label for="archivo" class="form-label"><b>Imagen</b></label>
                                <div class="drag-area">
                                    <label for="archivo" class="upload-label">
                                        <input type="file" id="archivo" name="imagen_user" hidden>
                                        <div class="file-display">
                                            <p>Arrastra y suelta el archivo aquí, o haz clic para seleccionarlo</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="form-actions mt-5 mb-5">
                                <button type="submit"><i class="fa-regular fa-floppy-disk"></i> &nbsp;Actualizar Información</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php
            
            if($_SESSION['privilegio'] == 1){

                ?>
                  <!-- Configuración aplicación -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collage2">
                                Configuración aplicación
                            </button>
                        </h2>
                        <div id="collage2" class="accordion-collapse collapse" data-bs-parent="#collageAccordion">
                            <div class="accordion-body">
                                <div class="text-center mt-2 mb-5">
                                    <h4>Configuración Aplicación</h4>
                                </div>
                                <?php
                                    $sql = "SELECT * FROM configuracion_aplicacion";
                                    $consulta_roles = $ins_loginControlador->ejecutar_consultas_simples_two($sql);
                                    $configuracion = $consulta_roles ? $consulta_roles->fetch(PDO::FETCH_ASSOC) : false;

                                    // Verificamos si $configuracion contiene datos
                                    if ($configuracion) {
                                        $consecutivo_id_encrypted = $ins_loginControlador->encryption($configuracion['consecutivo']);
                                        $numero_estudiantes = $configuracion['numero_estudiantes_proyectos'];
                                        $numero_jurados_proyectos = $configuracion['numero_jurados_proyectos'];
                                        $logo_aplicacion = $configuracion['nombre_logo'];
                                        $texto_boton_configuracion = "Actualizar datos configuración";
                                        $opcion_configuracion_aplicacion = 2;
                                        $icono_configuracion_aplicacion = '<i class="fa-solid fa-arrows-rotate"></i>';
                                    } else {
                                        // Si no hay registros, asignamos valores por defecto
                                        $consecutivo_id_encrypted = "";
                                        $numero_estudiantes = 0;
                                        $numero_jurados_proyectos = 0;
                                        $logo_aplicacion = "logo-autonoma.png"; // Nombre por defecto del logo
                                        $texto_boton_configuracion = "Guardar datos configuración";
                                        $opcion_configuracion_aplicacion = 1;
                                        $icono_configuracion_aplicacion = '<i class="fa-regular fa-floppy-disk"></i>';
                                    }
                                ?>
                                
                                <form class="FormulariosAjax " action="<?= SERVERURL ?>Ajax/UsuarioAjax.php" method="POST" data-form="save" autocomplete="off">
                                    <div class="row">
                                        <div class=" col-md-12">
                                            <div class="form-floating">
                                                <input type="hidden" name="opcion_configuracion_aplicacion" value="<?=$ins_loginControlador->encryption($opcion_configuracion_aplicacion)?>">
                                                <input type="hidden" name="consecutivo" value="<?= $consecutivo_id_encrypted?>">
                                                <input type="number" class="form-control input_border" id="floatingNombre"
                                                    name="configuration_numero_estudiantes_proyectos" value="<?=$numero_estudiantes?>"
                                                    placeholder="Password">
                                                <label for="floatingNombre mb-4">Numero estudiante por proyectos</label>
                                            </div>
                                            <div class="form-floating mt-2">
                                            <input type="number" class="form-control input_border" id="floatingNombre"
                                                name="configuration_numero_jurados_proyecto"  value="<?=$numero_jurados_proyectos?>"
                                                    placeholder="Password">
                                                <label for="floatingNombre mb-4">Numero de jurados por proyectos </label>
                                            </div>

                                            <div class="row justify-content-center mt-2">
                                                <!-- Contenedor del logo con la capa superpuesta -->
                                                <div class="col-md-12">
                                                <div class="alert alert-success" role="alert">
                                                Logo aplicación
                                                </div>
                                                
                                                    <div class="logo-container position-relative">
                                                        <!-- Imagen del logo -->
                                                        <img id="logoPreview" src="<?= SERVERURL ?>Views/assets/images/<?= $logo_aplicacion ?>" alt="Logo de la universidad" class="img-fluid logo-img shadow-lg rounded">

                                                        <!-- Input oculto para subir un nuevo logo -->
                                                        <input type="file" name="nuevo_logo" id="logoInput" class="logo-input d-none" accept="image/*">

                                                        <!-- Capa superpuesta -->
                                                        <div class="overlay" onclick="document.getElementById('logoInput').click();">
                                                            <span>Actualizar imagen</span>
                                                        </div>
                                                    </div>
                                                </div>

                                            
                                            </div>


                                        </div>
                                    </div>
                                    <div class="text-center mt-3 ">
                                        <button type="submit" class="button-configuration"><?= $icono_configuracion_aplicacion?> &nbsp; <?=$texto_boton_configuracion?></button>
                                    </div>
                                </form>

                                <div class="text-center mt-3 mb-3">
                                    <!-- Botón para abrir el modal -->
                                    <button class="btn-abrir-cargar-imagenes-portadas button-configuration"><i class="fa-solid fa-upload"></i> &nbsp;Subir Imágenes portada</button>
                                </div>

                            </div>
                        </div>
                    </div>

                     <!--  Registro facultades y programa académicos -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collage3">
                            Registrar facultades academicas
                            </button>
                        </h2>
                        <div id="collage3" class="accordion-collapse collapse" data-bs-parent="#collageAccordion">
                            <div class="accordion-body">
                            <div class="alert alert-success text-center" role="alert">
                            Registrar facultades academicas
                            </div>
                            <form class="FormulariosAjax " action="<?= SERVERURL ?>Ajax/UsuarioAjax.php" method="POST" data-form="save" autocomplete="off">
                                    <div class="row">
                                        <div class=" col-md-9">
                                            <div class="form-floating">
                                                <input type="text" class="form-control input_border" id="floatingNombre" name="configuration_name_facultad"
                                                    placeholder="Password">
                                                <label for="floatingNombre mb-4">Nombre facultad academica</label>
                                            </div>
                                        </div>
                                        <div class=" col-md-2">
                                            <button type="submit" class="button-facultad-programa mt-3">Registrar</button>
                                        </div>
                                    </div>
                            </form>
                            <div class="alert alert-info text-center mt-3" role="alert">
                            Facultades academicas registradas
                            </div>
                            <div class="list-group">
                                    <ul class="list-group">
                                        <?php
                                        $sql = "SELECT * FROM facultades";
                                        $consulta_facultades = $ins_loginControlador->ejecutar_consultas_simples_two($sql);

                                        if ($consulta_facultades) {
                                            
                                            while ($facultad = $consulta_facultades->fetch(PDO::FETCH_ASSOC)) {
                                                $facultad_id_encrypted = $ins_loginControlador->encryption($facultad['id_facultad']);
                                                $facultad_id = htmlspecialchars($facultad['id_facultad']);
                                                
                                                echo '
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <!-- Input editable -->
                                                    <input type="text" class="form-control w-50" id="inputOriginal_' . $facultad_id . '" name="nombre_facultad" 
                                                        value="' . htmlspecialchars($facultad['nombre_facultad']) . '" 
                                                        oninput="syncInput(' . $facultad_id . ')">
                                            
                                            
                                                    <div class="d-flex ms-auto"> 
                                                        <form class="FormulariosAjax me-2" action="' . SERVERURL . 'Ajax/UsuarioAjax.php" method="POST" data-form="update">
                                                        <input type="hidden" name="text_facultad_upd" class="form-control w-50 ms-2" id="inputDestino_' . $facultad_id . '" readonly>
                                                            <input type="hidden" name="configuration_id_facultad_upd" value="' . $facultad_id_encrypted . '">
                                                            <button type="submit" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen text-white"></i></button>
                                                        </form>
                                            
                                                        <form class="FormulariosAjax" action="' . SERVERURL . 'Ajax/UsuarioAjax.php" method="POST" data-form="delete">
                                                            <input type="hidden" name="configuration_id_facultad" value="' . $facultad_id_encrypted . '">
                                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </li>';
                                            }
                                        
                                            
                                        } else {
                                            echo ' <div class="alert alert-danger alertas-ms " role="alert">
                                                    <div class="text-center">No hay facultades registradas </div>
                                                </div>';
                                        }

                                        ?>
                                    </ul>
                            </div>
                            <div class="alert alert-info text-center mt-3" role="alert">
                                Registrar programas académicos
                            </div>

                            <form class="FormulariosAjax  mb-4" action="<?= SERVERURL ?>Ajax/UsuarioAjax.php" method="POST" data-form="save" autocomplete="off">
                                    <div class="row">
                                        <div class=" col-md-12">
                                            <div class="form-floating">
                                                <select class="form-select input_border" id="floatingSelect" name="tipo_faculta_reg" aria-label="Floating label select example">
                                                    <option selected></option>
                                                    <?php
                                                    $sql = "SELECT * from facultades";
                                                    $consulta_roles = $ins_loginControlador->ejecutar_consultas_simples_two($sql);
                                                    if ($consulta_roles) {
                                                        while ($facultad = $consulta_roles->fetch(PDO::FETCH_ASSOC)) {
                                                            echo '<option value="' . $ins_loginControlador->encryption($facultad['id_facultad']) . '">' . $facultad['nombre_facultad'] . '</option>';
                                                        }
                                                    } else {
                                                        echo ' <div class="alert alert-danger alertas-ms " role="alert">
                                                            <div class="text-center">No hay facultades registradass </div>
                                                            </div>';
                                                    }

                                                    ?>

                                                </select>
                                                <label for="floatingSelect">Programas</label>
                                            </div>
                                            <div class="form-floating mt-2">
                                                <input type="text" class="form-control input_border" id="floatingNombre" name="configuration_name_programa"
                                                    placeholder="Password">
                                                <label for="floatingNombre mb-4">Nombre programa</label>
                                            </div>
                                        </div>
                                        <div class=" mt-4 col-md-12">
                                            <div class="text-center">
                                                <button type="submit" class="button-facultad-programa"><i class="fa-regular fa-floppy-disk"></i> &nbsp; Guardar Programas</button>
                                            </div>
                                        </div>
                                    </div>
                            </form>

                            <?php
                                // Consulta para obtener todas las facultades
                                $sql_facultades = "SELECT * FROM facultades";
                                $consulta_facultades = $ins_loginControlador->ejecutar_consultas_simples_two($sql_facultades);

                                while ($facultad = $consulta_facultades->fetch(PDO::FETCH_ASSOC)) {
                                    // Encriptamos el ID de la facultad
                                    $facultad_id_encrypted = $ins_loginControlador->encryption($facultad['id_facultad']);

                                    echo '
                                    <div class="list-group mb-3">
                                        <button type="button" class="list-group-item list-group-item-action name-title-program text-center" aria-current="true">
                                            ' . htmlspecialchars($facultad['nombre_facultad']) . '
                                        </button>';

                                    // Consulta para obtener los programas de la facultad actual
                                    $sql_programas = "SELECT * FROM programas_academicos WHERE id_facultad = '" . $facultad['id_facultad'] . "'";
                                    $consulta_programas = $ins_loginControlador->ejecutar_consultas_simples_two($sql_programas);

                                // Generar la lista de programas
                                while ($programa = $consulta_programas->fetch(PDO::FETCH_ASSOC)) {
                                    // Encriptamos el ID del programa
                                    $programa_id_encrypted = $ins_loginControlador->encryption($programa['id_programa']);
                                    $programa_id = htmlspecialchars($programa['id_programa']);
                                
                                    echo '
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <!-- Input para editar el nombre del programa -->
                                        <input type="text" class="form-control w-50" id="inputOriginal_' . $programa_id . '" 
                                            name="nombre_programa" 
                                            value="' . htmlspecialchars($programa['nombre_programa']) . '" 
                                            oninput="syncInput_programas(' . $programa_id . ')">
                                        
                                        <div class="d-flex ms-auto">
                                            <!-- Input donde se reflejará el texto -->
                                            <input type="hidden" class="form-control w-50 me-2" id="inputDestino_' . $programa_id . '" 
                                                name="nombre_programa_reflejado" readonly>
                                
                                            <!-- Formulario para actualizar -->
                                            <form class="FormulariosAjax me-2" action="' . SERVERURL . 'Ajax/UsuarioAjax.php" method="POST" data-form="update">
                                                <input type="hidden" name="configuration_id_programa_upd" value="' . $programa_id_encrypted . '">
                                                <input type="hidden" name="nombre_programa_actualizado" id="hiddenInput_' . $programa_id . '">
                                                <button type="submit" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen text-white"></i></button>
                                            </form>

                                            
                                            <form action="' . SERVERURL . 'Ajax/UsuarioAjax.php" method="POST" class="d-inline FormulariosAjax">
                                                <input type="hidden" name="id_facultad_configuration_delete" value="' . $facultad_id_encrypted . '">
                                                <input type="hidden" name="id_programa_configuration_delete" value="' . $programa_id_encrypted . '">
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </li>';
                                }


                                    echo '</div>';
                                }
                            ?>


                        </div>
                    </div>

                    
                    <!--  Registro de modalidades -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collage4">
                            Registro de modalidades 
                            </button>
                        </h2>
                        <div id="collage4" class="accordion-collapse collapse" data-bs-parent="#collageAccordion">
                            <div class="accordion-body">
                                <div class="alert alert-success text-center" role="alert">
                                    Registrar modalidades academicas
                                </div>

                                <form class="FormulariosAjax " action="<?= SERVERURL ?>Ajax/UsuarioAjax.php" method="POST" data-form="save" autocomplete="off">
                                    <div class="row">
                                        <div class=" col-md-9">
                                            <div class="form-floating">
                                                <input type="text" class="form-control input_border" id="floatingNombre" name="configuration_name_modalidad"
                                                    placeholder="Password">
                                                <label for="floatingNombre mb-4">Nombre modalidad</label>
                                            </div>
                                        </div>
                                        <div class=" col-md-2">
                                            <button type="submit" class="button-facultad-programa mt-3">Registrar</button>
                                        </div>
                                    </div>
                                </form>

                                <div class="alert alert-success text-center mt-4" role="alert">
                                Modalidades registradas
                                </div>

                                <div class="list-group">
                                    <ul class="list-group">
                                        <?php
                                        $sql = "SELECT * FROM modalidad_grados";
                                        $consulta_modalidad = $ins_loginControlador->ejecutar_consultas_simples_two($sql);

                                        if ($consulta_modalidad) {
                                            
                                        
                                        // Generar la lista de modalidades
                                        while ($modalidad = $consulta_modalidad->fetch(PDO::FETCH_ASSOC)) {
                                            // Encriptamos el ID de la modalidad
                                            $modalidad_id_encrypted = $ins_loginControlador->encryption($modalidad['id_modalidad']);
                                            $modalidad_id = htmlspecialchars($modalidad['id_modalidad']);

                                            echo '
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <!-- Input donde el usuario escribe -->
                                                <input type="text" class="form-control w-50 me-2" 
                                                    id="modalidadInput_' . $modalidad_id . '" 
                                                    name="nombre_modalidad" 
                                                    value="' . htmlspecialchars($modalidad['nombre_modalidad']) . '" 
                                                    oninput="syncInputModalidad(' . $modalidad_id . ')">

                                                <div class="d-flex ms-auto">
                                                    <!-- Input oculto donde se refleja el texto automáticamente -->
                                                    <input type="hidden" class="form-control w-50 me-2" 
                                                        id="modalidadOutput_' . $modalidad_id . '" 
                                                        name="nombre_modalidad_copia" 
                                                        readonly>

                                                    <!-- Botón para actualizar -->
                                                    <form class="FormulariosAjax me-2" action="' . SERVERURL . 'Ajax/UsuarioAjax.php" method="POST" data-form="update">
                                                        <input type="hidden" name="modalidad_id" value="' . $modalidad_id_encrypted . '">
                                                        <input type="hidden" name="modalidad_nombre_actualizado" id="hiddenInput_' . $modalidad_id . '">
                                                        <button type="submit" class="btn btn-warning btn-sm">
                                                            <i class="fa-solid fa-pen text-white"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Botón para eliminar -->
                                                    <form class="FormulariosAjax " action="' . SERVERURL . 'Ajax/UsuarioAjax.php"  method="POST" data-form="delete" class="d-inline">
                                                        <input type="hidden" name="configuration_id_modalidad" value="' . $modalidad_id_encrypted . '">
                                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </li>';
                                        }


                                        } else {
                                            echo ' <div class="alert alert-danger alertas-ms " role="alert">
                                                    <div class="text-center">No hay modalidades registradas </div>
                                                </div>';
                                        }


                                        ?>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </div>

                    
                    <!-- Agregar registros calificados programas -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collage6">
                                Agregar registros calificados programas
                            </button>
                        </h2>
                        <div id="collage6" class="accordion-collapse collapse" data-bs-parent="#collageAccordion">
                            <div class="accordion-body">
                                <div class="alert alert-success text-center" role="alert">
                                    Registros calificados programas
                                </div>
                                <form class="FormulariosAjax  mb-4" action="<?= SERVERURL ?>Ajax/UsuarioAjax.php" method="POST"     data-form="save" autocomplete="off">
                                    <div class="row">
                                        <div class=" col-md-12">
                                            <div class="form-floating">
                                                <select class="form-select input_border" id="floatingSelect" name="registro_calificado_programa" aria-label="Floating label select example">
                                                    <option selected></option>
                                                    <?php
                                                    $sql = "SELECT * from programas_academicos";
                                                    $consulta_roles = $ins_loginControlador->ejecutar_consultas_simples_two($sql);
                                                    if ($consulta_roles) {
                                                        while ($programas = $consulta_roles->fetch(PDO::FETCH_ASSOC)) {
                                                            echo '<option value="' . $ins_loginControlador->encryption($programas['id_programa']) . '">' . $programas['nombre_programa'] . '</option>';
                                                        }
                                                    } else {
                                                        echo ' <div class="alert alert-danger alertas-ms " role="alert">
                                                            <div class="text-center">No hay programas registrados </div>
                                                            </div>';
                                                    }
                                                    ?>
                                                </select>
                                                <label for="floatingSelect">Programas</label>
                                            </div>
                                            <div class="form-floating mt-2">
                                                <input type="text" class="form-control input_border" id="floatingNombre" name="name_regisro_calificado"
                                                    placeholder="Password">
                                                <label for="floatingNombre mb-4">Registro caliicado</label>
                                            </div>
                                        </div>
                                        <div class=" mt-4 col-md-12">
                                            <div class="text-center">
                                                <button type="submit" class="button-facultad-programa"><i class="fa-regular fa-floppy-disk"></i> &nbsp; Guardar registro programa</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <!----------------imprimir registros calificados---------------------->

                                <?php
                                    // Consulta para obtener todas las facultades
                                    $sql_registro_calificados = "SELECT * FROM programas_academicos";
                                    $consulta_registro_calificados = $ins_loginControlador->ejecutar_consultas_simples_two($sql_registro_calificados);

                                    while ($registroC = $consulta_registro_calificados->fetch(PDO::FETCH_ASSOC)) {
                                        // Encriptamos el ID de la facultad
                                        $programa_id_encrypted = $ins_loginControlador->encryption($registroC['id_programa']);

                                        echo '
                                        <div class="list-group mb-3">
                                            <button type="button" class="list-group-item list-group-item-action name-title-program text-center" aria-current="true">
                                                ' . htmlspecialchars($registroC['nombre_programa']) . '
                                            </button>';

                                        // Consulta para obtener los programas de la facultad actual
                                        $sql_programas = "SELECT * FROM registros_calificados_programas WHERE id_programa = '" . $registroC['id_programa'] . "'";
                                        $consulta_programas = $ins_loginControlador->ejecutar_consultas_simples_two($sql_programas);

                                        while ($programa = $consulta_programas->fetch(PDO::FETCH_ASSOC)) {
                                            // Encriptamos el ID del programa
                                            $registroC_id_encrypted = $ins_loginControlador->encryption($programa['id']);
                                            $programa_id = htmlspecialchars($programa['id']);
                                            $nombre_registro = htmlspecialchars($programa['nombre_registro']);
                                        
                                            echo '
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <!-- Input editable donde el usuario escribe -->
                                                <input type="text" class="form-control w-50 me-2" 
                                                    id="inputEditable_' . $programa_id . '" 
                                                    name="nombre_programa" 
                                                    value="' . $nombre_registro . '" 
                                                    oninput="syncProgramaInput(' . $programa_id . ')">
                                        
                                                <div class="d-flex ms-auto">
                                                <!-- Formulario para actualizar -->
                                                <form action="' . SERVERURL . 'Ajax/UsuarioAjax.php" method="POST" class="d-inline mx-2 FormulariosAjax">
                                                    <input type="hidden" name="id_programa_registro_calificado_upd" value="' . $programa_id_encrypted . '">
                                                    <input type="hidden" name="id_registro_calificado_upd" value="' . $registroC_id_encrypted . '">
                                                    <input type="hidden" name="nombre_registro" id="inputHidden_' . $programa_id . '" value="' . $nombre_registro . '">
                                                    <button type="submit" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen text-white"></i></button>
                                                </form>
                                        
                                                <!-- Formulario para eliminar -->
                                                
                                                <form action="' . SERVERURL . 'Ajax/UsuarioAjax.php" method="POST" class="d-inline FormulariosAjax">
                                                    <input type="hidden" name="id_programa_registro_calificado" value="' . $programa_id_encrypted . '">
                                                    <input type="hidden" name="id_registro_calificado" value="' . $registroC_id_encrypted . '">
                                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                                                </form>
                                            </div>

                                            </li>';
                                        }
                                        echo '</div>';
                                    }
                                ?>


                            </div>
                        </div>
                    </div>

                    <!-- Agregar firma digital  -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collage5">
                            Agregar firma digital
                            </button>
                        </h2>
                        <div id="collage5" class="accordion-collapse collapse" data-bs-parent="#collageAccordion">
                            <div class="accordion-body">                    
                            <?php

                                $consulta_firma_usuarios = "SELECT * FROM firma_digital_usuarios WHERE numero_documento = '$numero_documento_user'";
                                $consulta_exec_resultado = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_firma_usuarios);

                                // Verificar si se obtuvieron resultados
                                if ($consulta_exec_resultado && $consulta_exec_resultado->rowCount() > 0) {
                                    $resultado_usuario_firma = $consulta_exec_resultado->fetch(PDO::FETCH_ASSOC);
                                    $firma_usuario = $resultado_usuario_firma['firma'];
                                    $prefijo = '_upd';
                                    $mensaje = '<div class="alert alert-success text-center" role="alert">
                                                        El usuario  tiene firma registrada
                                                    </div>';
                                    $mostrar_imagen = '<div class="mostrar_firma_registrada">
                                                    <img src="'. SERVERURL.'Views/assets/images/FirmasUsuarios/'.$firma_usuario.'" alt="firma_usuarios">
                                                </div>';

                                    $textoboton = "Actualizar firma";
                                                
                                } else {
                                    $prefijo = '';
                                    $mensaje = '<div class="alert alert-danger text-center" role="alert">
                                                El usuario no tiene firma registrada
                                                    </div>';
                                    $mostrar_imagen = '';

                                    $textoboton = "Cargar firma";
                                }

                                ?>

                                <form class="user-form-two mt-1 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/UsuarioAjax.php" method="POST" data-form="save" autocomplete="off">
                                
                                    <input type="hidden" name="numero_documento_user_logueado" value="<?= $ins_loginControlador->encryption($_SESSION['numero_documento']) ?>">
                                    <?=$mensaje?>
                                    <?=$mostrar_imagen?>
                                    <div class="mb-2">
                                        <div class="firma-container">
                                            <label for="firma-input" class="form-label"><b>Subir Firma Digital</b></label>
                                            
                                            <div class="firma-area" id="firma-dropzone">
                                                <input type="file" id="firma-input" name="firma_digital<?=$prefijo?>" hidden accept="image/*">
                                                <p>Arrastra y suelta tu firma aquí, o haz clic para seleccionarla</p>
                                                <img id="firma-preview" class="firma-preview" alt="Vista previa de la firma">
                                            </div>
                                            
                                        </div>
                                    </div>

                                    <div class="form-actions mt-5 mb-5">
                                        <button type="submit"><i class="fa-regular fa-floppy-disk"></i> &nbsp;<?=$textoboton?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                <?php 


            }else if($_SESSION['privilegio'] != 3 && $_SESSION['privilegio'] != 4){
                ?>

                <!-- Agregar firma digital  -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collage5">
                        Agregar firma digital
                        </button>
                    </h2>
                    <div id="collage5" class="accordion-collapse collapse" data-bs-parent="#collageAccordion">
                        <div class="accordion-body">                    
                        <?php

                            $consulta_firma_usuarios = "SELECT * FROM firma_digital_usuarios WHERE numero_documento = '$numero_documento_user'";
                            $consulta_exec_resultado = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_firma_usuarios);

                            // Verificar si se obtuvieron resultados
                            if ($consulta_exec_resultado && $consulta_exec_resultado->rowCount() > 0) {
                                $resultado_usuario_firma = $consulta_exec_resultado->fetch(PDO::FETCH_ASSOC);
                                $firma_usuario = $resultado_usuario_firma['firma'];
                                $prefijo = '_upd';
                                $mensaje = '<div class="alert alert-success text-center" role="alert">
                                                    El usuario  tiene firma registrada
                                                </div>';
                                $mostrar_imagen = '<div class="mostrar_firma_registrada">
                                                <img src="'. SERVERURL.'Views/assets/images/FirmasUsuarios/'.$firma_usuario.'" alt="firma_usuarios">
                                            </div>';

                                $textoboton = "Actualizar firma";
                                            
                            } else {
                                $prefijo = '';
                                $mensaje = '<div class="alert alert-danger text-center" role="alert">
                                            El usuario no tiene firma registrada
                                                </div>';
                                $mostrar_imagen = '';

                                $textoboton = "Cargar firma";
                            }

                            ?>

                            <form class="user-form-two mt-1 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/UsuarioAjax.php" method="POST" data-form="save" autocomplete="off">
                            
                                <input type="hidden" name="numero_documento_user_logueado" value="<?= $ins_loginControlador->encryption($_SESSION['numero_documento']) ?>">
                                <?=$mensaje?>
                                <?=$mostrar_imagen?>
                                <div class="mb-2">
                                    <div class="firma-container">
                                        <label for="firma-input" class="form-label"><b>Subir Firma Digital</b></label>
                                        
                                        <div class="firma-area" id="firma-dropzone">
                                            <input type="file" id="firma-input" name="firma_digital<?=$prefijo?>" hidden accept="image/*">
                                            <p>Arrastra y suelta tu firma aquí, o haz clic para seleccionarla</p>
                                            <img id="firma-preview" class="firma-preview" alt="Vista previa de la firma">
                                        </div>
                                        
                                    </div>
                                </div>

                                <div class="form-actions mt-5 mb-5">
                                    <button type="submit"><i class="fa-regular fa-floppy-disk"></i> &nbsp;<?=$textoboton?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php
            }

            ?>

          

           


            


        </div>

        </div>
    </div>
</div>






<!-- Modal para cargar imágenes -->
<div class="modal-cargar-imagenes-portadas">
    <div class="modal-contenido-cargar-imagenes-portadas">
        <span class="cerrar-modal-cargar-imagenes-portadas">&times;</span>
        <h2>Subir Imágenes</h2>

        <!-- Formulario para enviar las imágenes -->
        <form id="form-imagenes" class ="FormulariosAjaxLogin" action="<?=SERVERURL?>Ajax/UsuarioAjax.php" method="POST"  enctype="multipart/form-data">
            <!-- Área Drag & Drop -->
            <div class="dropzone-cargar-imagenes-portadas" id="dropzone">
                <p>Arrastra y suelta las imágenes aquí o <span>haz clic</span> para seleccionarlas</p>
                <input type="file" class="input-imagenes-portadas" id="input-imagenes" name="imagenes_portadas[]" multiple accept="image/*">
            </div>

            <!-- Vista previa de imágenes -->
            <div class="vista-previa-cargar-imagenes-portadas" id="vista-previa"></div>

            <!-- Botón para subir imágenes -->
            <button type="submit" class="btn-subir-cargar-imagenes-portadas">Subir Imágenes</button>
            
        </form>

        <div class="text-center mt-3 mb-3">
                <h3>Imágenes Guardadas</h3>
            </div>
            <div class="container-mostrar-imagenes-portadas">
            <?php
            $sql_imagenes_portadas = "SELECT * FROM imagenes_portada";
            $consulta_imagenes_portadas = $ins_loginControlador->ejecutar_consultas_simples_two($sql_imagenes_portadas);

            if ($consulta_imagenes_portadas->rowCount() > 0) {
                while ($fila = $consulta_imagenes_portadas->fetch(PDO::FETCH_ASSOC)) {
                    $imagenes = json_decode($fila['nombre_imagenes'], true);
                    $id = $fila['id']; // ID del registro
                    $estado = $fila['estado']; // Estado actual de las imágenes (A = Activa, I = Inactiva)

                    if($estado == 'A'){
                        $texto = 'Activado';
                        $color = 'success';
                        $icono = '<i class="fa-solid fa-circle-check"></i>';

                    }else if($estado == 'I'){
                        $texto = 'Activar';
                        $color = 'warning';
                        $icono = '<i class="fa-solid fa-ban"></i>';
                    }

                    echo '<div class="content-group">';
                    
                    // Contenedor de imágenes (muestra todas las imágenes de un mismo ID)
                    echo '<div class="content content-imagenes">';
                    foreach ($imagenes as $imagen) {
                        echo '
                        <div class="card-img-portada">
                            <img src="'.SERVERURL.'Views/assets/images/ImagenesPortada/' . $imagen . '" alt="Imagen Portada">
                        </div>';
                    }
                    echo '</div>'; // Cierre de content-imagenes

                    // Contenedor de botones (uno por cada grupo de imágenes de un mismo ID)
                    echo '<div class="content content-botones">';
                    
                    ?>

                        <form class="FormulariosAjax" action="<?=SERVERURL?>Ajax/UsuarioAjax.php" method="POST" data-form="delete" autocomplete="off">
                            <input type="hidden" name="codigo_id_portada_delete" value="<?=$ins_loginControlador->encryption($id)?>">
                            <button type="submit" class="btn btn-danger"><i class="far fa-trash-alt"></i> &nbsp; Eliminar</button>
                        </form>

                        <form class="FormulariosAjax" action="<?=SERVERURL?>Ajax/UsuarioAjax.php" method="POST" data-form="update" autocomplete="off">
                            <input type="hidden" name="codigo_id_portada_update" value="<?=$ins_loginControlador->encryption($id)?>">
                            <button type="submit" class="btn btn-<?=$color?>"><?=$icono?>
                            &nbsp; <?=$texto?></button>
                        </form>

                    <?php

                    echo '</div>'; // Cierre de content-botones

                    echo '</div>'; // Cierre de content-group
                }
            } else {
                echo '<p>No hay imágenes disponibles.</p>';
            }
            ?>
        </div>


    </div>
</div>





<!-- JavaScript para Vista Previa de la Imagen -->
<script>
    document.getElementById('logoInput').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('logoPreview').src = e.target.result; // Actualiza la vista previa
            };
            reader.readAsDataURL(file);
        }
    });

    function syncInput(id) {
        let original = document.getElementById('inputOriginal_' + id);
        let destino = document.getElementById('inputDestino_' + id);                            
        if (original && destino) {
            destino.value = original.value; // Reflejar en el otro input
        }
    }

   function syncInput_programas(programa_id) {
        let inputOriginal = document.getElementById('inputOriginal_' + programa_id);
        let inputDestino = document.getElementById('inputDestino_' + programa_id);
        let hiddenInput = document.getElementById('hiddenInput_' + programa_id);

        if (inputOriginal && inputDestino && hiddenInput) {
            inputDestino.value = inputOriginal.value; // Reflejar el texto en el otro input
            hiddenInput.value = inputOriginal.value;  // Asegurar que el valor actualizado se envíe en el formulario
        }
    }

    function syncInputModalidad(id) {
    // Obtener los valores de los inputs
    let originalInput = document.getElementById('modalidadInput_' + id);
    let outputInput = document.getElementById('modalidadOutput_' + id);
    let hiddenInput = document.getElementById('hiddenInput_' + id);

    // Copiar el valor del input principal a los otros inputs
    if (originalInput && outputInput && hiddenInput) {
        outputInput.value = originalInput.value;
        hiddenInput.value = originalInput.value;
    }
}

function syncProgramaInput(id) {
    // Obtener el valor del input editable
    let nuevoValor = document.getElementById("inputEditable_" + id).value;

    // Pasarlo al input oculto del formulario
    document.getElementById("inputHidden_" + id).value = nuevoValor;
}

</script>

