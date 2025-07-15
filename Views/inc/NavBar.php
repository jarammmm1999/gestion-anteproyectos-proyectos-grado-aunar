<?php
// Obtener el número de documento y ejecutar la consulta
$numero_documento_user =  $_SESSION['numero_documento'];
$numero_documento_user = $ins_loginControlador->encryption($numero_documento_user);
$consulta = $ins_loginControlador->consulta_information_user($numero_documento_user);
if ($consulta) {
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);
    if ($usuario) {
        if ($usuario['imagen_usuario'] == null) {
            $imagen = 'AvatarNone.png';
        } else {
            $imagen = $usuario['imagen_usuario'];

        
            /*************************Extraemos el tipo de usuario ********************************** */

            $id_rol =  $_SESSION['privilegio'];

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
        }
    } else {
        echo "No se encontró el usuario.";
    }
} else {
    echo "Error en la consulta.";
}


$id_usuario_registrado =  $_SESSION['id_usuario'];

if(isset($id_usuario_registrado)){

    $sql_estado_usuario = "SELECT estado from usuarios where id = $id_usuario_registrado";

    $consulta_estado_usuario = $ins_loginControlador->ejecutar_consultas_simples_two($sql_estado_usuario);

    if ($consulta_estado_usuario) {
        $estado_usuario = $consulta_estado_usuario->fetch(PDO::FETCH_ASSOC);
        if ($estado_usuario) {
            $estado_usuario = $estado_usuario['estado'];
        } else {
            $estado_usuario = "No se encontró el rol.";
        }
    }
    

    if($estado_usuario == 2){
    ?>
        <div class="modal-bloqueo">
            <div class="contenido-bloqueo">
            <img class="imagen-bloqueo" src="<?= SERVERURL ?>Views/assets/images/computadora-bloqueada.png" alt="imagen login">
                <h2>Tu cuenta ha sido bloqueada</h2>
                <p>Por seguridad, tu acceso ha sido restringido. Si crees que esto es un error, contacta con soporte.</p>
                <button ><span id="contador"></span></button>
            </div>
        </div>

        <script>
            let segundos = 5; // Tiempo en segundos
            let contador = document.getElementById("contador");

            let intervalo = setInterval(() => {
                segundos--;
                contador.innerText = "Serás redirigido en " + segundos + " segundos...";
                
                if (segundos <= 0) {
                    clearInterval(intervalo); // Detiene el contador
                    window.location.href = "<?= SERVERURL ?>Ajax/CerrarSesionAjax.php/"; // Redirige
                }
            }, 1000); // Ejecutar cada 1 segundo
        </script>


    <?php
    

    }

    
    $sqlLogo = "SELECT nombre_logo 
    FROM configuracion_aplicacion 
    LIMIT 1";
    $consulta_logo = $ins_loginControlador->ejecutar_consultas_simples_two($sqlLogo);

    if ($consulta_logo->rowCount() > 0) {
    $resultado = $consulta_logo->fetch(PDO::FETCH_ASSOC);
    $nombre_logo = $resultado['nombre_logo'];
    } else {
    $nombre_logo ="logo-autonoma.png";
    }

}





?>

<div class="title-head">
    <h1>Gestión de anteproyectos y proyectos de grados - Autónoma de nariño</h1>
</div>
<header>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="<?= SERVERURL ?>home/"><img src="<?= SERVERURL ?>Views/assets/images/<?=$nombre_logo?>" alt="Logo Universidad" class="logo"></a>
            <!-- Botón del menú hamburguesa -->
            <button class="menu-toggle" aria-label="Abrir menú">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <!-- Contenido del navbar que se ocultará en pantallas pequeñas -->
        <div class="navbar-right">
            <div class="notification-icon">
                
                <div class="notification-menu">
                    <p>No hay notificaciones nuevas.</p>
                </div>
            </div>
            <div class="message-icon mx-5">
                <button type="button " onclick="abrirModal('<?= SERVERURL ?>','<?= $_SESSION['id_usuario'] ?>')" class="btn  position-relative">
                    <i class="fas fa-comment-alt"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        0
                    </span>
                </button>

            </div>
            <div class="user-menu my-2">
                <span> <?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></span>
                <img src="<?= SERVERURL ?>/Views/assets/images/avatar/<?= $imagen ?>" alt="Avatar Usuario" class="avatar">
                <i class="fas fa-chevron-down user-down "></i>
                <div class="dropdown-menu">
                    <a href="<?= SERVERURL ?>configuration-user/<?= $ins_loginControlador->encryption($_SESSION['privilegio']) ?>/<?= $ins_loginControlador->encryption($_SESSION['numero_documento']) ?>" class="dropdown-item"><i class="fas fa-cog"></i> Configuración</a>
                    <a class=" btn-exit-system dropdown-item"><i class=" fas fa-sign-out-alt"></i> Salir</a>
                </div>
            </div>
        </div>
    </nav>
</header>



<!-- Modal de Chat -->
<div id="miChatModal" class="mi-modal">
    <div class="mi-modal-contenido">
        <div class="mi-modal-header">
            <h2>Mensajes</h2>
            <span class="mi-modal-cerrar" onclick="cerrarModal()">&times;</span>
        </div>
        <div class="mi-modal-body">
            <div class="mi-usuarios-container" id="usuariosContainer"></div>
            <div class="mi-chat-container">
                <div class="mi-mensajes-header" id="miChatHeader">Seleccione un usuario</div>
                <div class="mi-mensajes" id="miMensajesContainer"></div>
                <div class="mi-enviar-mensaje">
                    <input type="text" id="miMensajeInput" placeholder="Escribe un mensaje">
                    <button id="enviarBtn" onclick="enviarMensaje('<?= SERVERURL ?>','<?= $_SESSION['id_usuario'] ?>')">Enviar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- cierra modal  para el chat -->
<?php

if ($_SESSION['privilegio'] != 1) {

    $documento_user_logueado = $_SESSION['numero_documento'];
    $consulta = "SELECT 
        auf.numero_documento,
        f.nombre_facultad, 
        GROUP_CONCAT(p.nombre_programa SEPARATOR ', ') AS nombre_programa,  -- Agrupa todos los programas en una sola fila separados por comas
        f.id_facultad
    FROM Asignar_usuario_facultades auf
    INNER JOIN facultades f ON auf.id_facultad = f.id_facultad
    LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
    WHERE auf.numero_documento = '$documento_user_logueado'
    GROUP BY 
        auf.numero_documento, 
        f.nombre_facultad, 
        f.id_facultad;";


    // Ejecutar la consulta utilizando parámetros seguros
    $consulta_facultades = $ins_loginControlador->ejecutar_consultas_simples_two($consulta);

    /*******************consultar si tiene anteproyecto asignado************************** */

    if ($_SESSION['privilegio'] == 3) {

        $consulta_anteprotecto_estudiante = "SELECT 
                ae.numero_documento,
                ae.codigo_anteproyecto,  
                a.titulo_anteproyecto, 
                a.palabras_claves, 
                a.fecha_creacion, 
                f.nombre_facultad, 
                p.nombre_programa 
            FROM asignar_estudiante_anteproyecto ae
            INNER JOIN anteproyectos a ON ae.codigo_anteproyecto = a.codigo_anteproyecto
            INNER JOIN facultades f ON a.id_facultad = f.id_facultad
            INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
            WHERE ae.numero_documento = '$documento_user_logueado'";

        $resultado_consulta_anteproyecto = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_anteprotecto_estudiante);
    } else if ($_SESSION['privilegio'] == 4) {

        $consulta_protecto_estudiante = "SELECT 
                ae.numero_documento,
                ae.codigo_proyecto,  
                a.titulo_proyecto, 
                a.palabras_claves, 
                a.fecha_creacion, 
                f.nombre_facultad, 
                p.nombre_programa 
            FROM asignar_estudiante_proyecto ae
            INNER JOIN proyectos a ON ae.codigo_proyecto = a.codigo_proyecto
            INNER JOIN facultades f ON a.id_facultad = f.id_facultad
            INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
            WHERE ae.numero_documento = '$documento_user_logueado'";

        $resultado_consulta_proyecto = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_protecto_estudiante);
    }




?>
    <div class="title-head">
        <h1 class="mx-2"><?= $rol ?></h1>
        <?php
        if ($_SESSION['privilegio'] != 1) {

            if ($consulta_facultades->rowCount() > 0) {

                if ($_SESSION['privilegio'] == 3) {
        ?>
                    <!-- Button to Open the Modal -->
                    <button type="button" class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#modalAnteproyecto"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Anteproyecto asignados">
                        Anteproyecto Asignado
                    </button>

                    <!-- Modal Structure -->
                    <div class="modal fade" id="modalAnteproyecto" tabindex="-1" aria-labelledby="modalAnteproyectoLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalAnteproyectoLabel">Anteproyectos Asignados</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
                                        <thead>
                                            <tr>
                                                <th scope="col">Código Anteproyecto</th>
                                                <th scope="col">Título</th>
                                                <th scope="col">Palabras Claves</th>
                                                <th scope="col">Facultad</th>
                                                <th scope="col">Programa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Aquí se insertarán dinámicamente las filas de la tabla -->
                                            <?php

                                            if ($resultado_consulta_anteproyecto->rowCount() > 0) {

                                                foreach ($resultado_consulta_anteproyecto->fetchAll(PDO::FETCH_ASSOC) as $row):

                                                    $codido_idea_estudiante = $row['codigo_anteproyecto'];
                                            ?>
                                                    <tr>
                                                        <td><?= $row['codigo_anteproyecto']; ?></td>
                                                        <td><?= $row['titulo_anteproyecto']; ?></td>
                                                        <td><?= $row['palabras_claves']; ?></td>
                                                        <td><?= $row['nombre_facultad']; ?></td>
                                                        <td><?= $row['nombre_programa']; ?></td>
                                                    </tr>
                                            <?php endforeach;
                                            } else {
                                                echo ' <tr><td colspan="5" class="text-center">No hay anteproyectos asignados.</td></tr>';
                                                $codido_idea_estudiante = false;
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <?php

                    if (isset($codido_idea_estudiante)) {

                    ?>

                        <button type="button" class="btn btn-info mx-1" data-bs-toggle="modal" data-bs-target="#modalCompañero"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Compañeros asignados">
                            Compañeros asignados
                        </button>

                        <?php

                        /************  Cosnulta de compañeros asignados******************** */

                        $consulta_compañero = "SELECT u.nombre_usuario, u.apellidos_usuario, a.codigo_anteproyecto,
                        ae.titulo_anteproyecto, 
                        ae.palabras_claves 
                        FROM asignar_estudiante_anteproyecto a
                        INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                        INNER JOIN anteproyectos ae ON ae.codigo_anteproyecto = a.codigo_anteproyecto
                        WHERE a.codigo_anteproyecto = '$codido_idea_estudiante'";

                            $resultado_consulta_compañero = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_compañero);


                            $consulta_asesor = "SELECT 
                                ae.codigo_anteproyecto, 
                                ae.titulo_anteproyecto, 
                                ae.palabras_claves,
                                ua.id_rol,
                                ua.nombre_usuario AS nombre_asesor,  -- Nombre del asesor
                                ua.apellidos_usuario AS apellidos_asesor  -- Apellidos del asesor
                            FROM anteproyectos ae
                            LEFT JOIN Asignar_asesor_anteproyecto_proyecto ap ON ae.codigo_anteproyecto = ap.codigo_proyecto
                            LEFT JOIN usuarios ua ON ap.numero_documento = ua.numero_documento
                            WHERE ae.codigo_anteproyecto = '$codido_idea_estudiante'
                            GROUP BY ae.codigo_anteproyecto, ae.titulo_anteproyecto, ae.palabras_claves, ua.nombre_usuario, ua.apellidos_usuario;
                            ";

                        $resultado_consulta_asesor = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_asesor);

                        if ($resultado_consulta_asesor) {
                        ?>


                            <button type="button" class="btn btn-warning mx-1" data-bs-toggle="modal" data-bs-target="#modalAsesor"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Asesor Asignado">
                                Director Asignado
                            </button>

                            <!-- Modal asesor -->
                            <div class="modal fade" id="modalAsesor" tabindex="-1" aria-labelledby="modalAnteproyectoLabel" aria-hidden="true">
                                <div class="modal-dialog modal-fullscreen">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalAnteproyectoLabel">Director Asignados</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Código Anteproyecto</th>
                                                        <th scope="col">Nombre</th>
                                                        <th scope="col">Tipo</th>
                                                        <th scope="col">Título</th>
                                                        <th scope="col">Palabras Claves</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Aquí se insertarán dinámicamente las filas de la tabla -->
                                                    <?php foreach ($resultado_consulta_asesor->fetchAll(PDO::FETCH_ASSOC) as $raw):
                                                        if ($raw['nombre_asesor'] == "") {
                                                            $nombre_asesor = "Sin asignar Director";
                                                        } else {
                                                            $nombre_asesor = $raw['nombre_asesor'] . " " . $raw['apellidos_asesor'];
                                                        }

                                                        if($raw['id_rol'] == 5){
                                                            $tipo = '<span class="badge bg-success">Director</span>';
                                                        }else{
                                                            $tipo = '<span class="badge bg-warning text-dark">Director Externo</span>';
                                                        }

                                                    ?>
                                                        <tr>
                                                            <td><?= $raw['codigo_anteproyecto']; ?></td>
                                                            <td><?= $nombre_asesor; ?></td>
                                                            <td><?= $tipo; ?></td>
                                                            <td><?= $raw['titulo_anteproyecto']; ?></td>
                                                            <td><?= $raw['palabras_claves']; ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php

                        }


                        ?>


                        <!-- Modal compañero -->
                        <div class="modal fade" id="modalCompañero" tabindex="-1" aria-labelledby="modalAnteproyectoLabel" aria-hidden="true">
                            <div class="modal-dialog modal-fullscreen">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalAnteproyectoLabel">Compañero Asignados</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Código Anteproyecto</th>
                                                    <th scope="col">Nombre</th>
                                                    <th scope="col">Apellidos</th>
                                                    <th scope="col">Título</th>
                                                    <th scope="col">Palabras Claves</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Aquí se insertarán dinámicamente las filas de la tabla -->
                                                <?php foreach ($resultado_consulta_compañero->fetchAll(PDO::FETCH_ASSOC) as $row): ?>
                                                    <tr>
                                                        <td><?= $row['codigo_anteproyecto']; ?></td>
                                                        <td><?= $row['nombre_usuario']; ?></td>
                                                        <td><?= $row['apellidos_usuario']; ?></td>
                                                        <td><?= $row['titulo_anteproyecto']; ?></td>
                                                        <td><?= $row['palabras_claves']; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php


                    }
                } else if ($_SESSION['privilegio'] == 4) {

                    ?>
                    <!-- Button to Open the Modal -->
                    <button type="button" class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#modalproyecto"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Anteproyecto asignados">
                        Proyecto Asignado
                    </button>

                    <!-- Modal Structure -->
                    <div class="modal fade" id="modalproyecto" tabindex="-1" aria-labelledby="modalAnteproyectoLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalAnteproyectoLabel">Proyectos Asignados</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
                                        <thead>
                                            <tr>
                                                <th scope="col">Código Proyecto</th>
                                                <th scope="col">Título</th>
                                                <th scope="col">Palabras Claves</th>
                                                <th scope="col">Facultad</th>
                                                <th scope="col">Programa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Aquí se insertarán dinámicamente las filas de la tabla -->
                                            <?php

                                            if ($resultado_consulta_proyecto->rowCount() > 0) {

                                                foreach ($resultado_consulta_proyecto->fetchAll(PDO::FETCH_ASSOC) as $row):

                                                    $codido_proyecto_estudiante = $row['codigo_proyecto'];
                                            ?>
                                                    <tr>
                                                        <td><?= $row['codigo_proyecto']; ?></td>
                                                        <td><?= $row['titulo_proyecto']; ?></td>
                                                        <td><?= $row['palabras_claves']; ?></td>
                                                        <td><?= $row['nombre_facultad']; ?></td>
                                                        <td><?= $row['nombre_programa']; ?></td>
                                                    </tr>
                                            <?php endforeach;
                                            } else {
                                                echo ' <tr><td colspan="5" class="text-center">No hay anteproyectos asignados.</td></tr>';
                                                $codido_idea_estudiante = false;
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <?php

                    if (isset($codido_proyecto_estudiante)) {

                    ?>

                        <button type="button" class="btn btn-info mx-1" data-bs-toggle="modal" data-bs-target="#modalCompañero"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Compañeros asignados">
                            Compañeros asignados
                        </button>

                        <?php

                        /************  Cosnulta de compañeros asignados******************** */

                        $consulta_compañero = "SELECT u.nombre_usuario, u.apellidos_usuario, a.codigo_proyecto,
                        ae.titulo_proyecto, 
                        ae.palabras_claves 
                        FROM asignar_estudiante_proyecto a
                        INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                        INNER JOIN proyectos ae ON ae.codigo_proyecto = a.codigo_proyecto
                        WHERE a.codigo_proyecto = '$codido_proyecto_estudiante'";

                        $resultado_consulta_compañero = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_compañero);


                        $consulta_asesor = "SELECT 
                            ae.codigo_proyecto, 
                            ae.titulo_proyecto, 
                            ae.palabras_claves,
                            ua.id_rol,
                            ua.nombre_usuario AS nombre_asesor,  -- Nombre del asesor
                            ua.apellidos_usuario AS apellidos_asesor  -- Apellidos del asesor
                        FROM proyectos ae
                        LEFT JOIN Asignar_asesor_anteproyecto_proyecto ap ON ae.codigo_proyecto = ap.codigo_proyecto
                        LEFT JOIN usuarios ua ON ap.numero_documento = ua.numero_documento
                        WHERE ae.codigo_proyecto = '$codido_proyecto_estudiante'
                        GROUP BY ae.codigo_proyecto, ae.titulo_proyecto, ae.palabras_claves, ua.nombre_usuario, ua.apellidos_usuario;
                        ";

                        $resultado_consulta_asesor = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_asesor);

                        if ($resultado_consulta_asesor) {
                        ?>


                            <button type="button" class="btn btn-warning mx-1" data-bs-toggle="modal" data-bs-target="#modalAsesor"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Asesor Asignado">
                                Director Asignado
                            </button>

                            <!-- Modal asesor -->
                            <div class="modal fade" id="modalAsesor" tabindex="-1" aria-labelledby="modalAnteproyectoLabel" aria-hidden="true">
                                <div class="modal-dialog modal-fullscreen">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalAnteproyectoLabel">Director Asignados</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Código Proyecto</th>
                                                        <th scope="col">Nombre</th>
                                                        <th scope="col">Tipo</th>
                                                        <th scope="col">Título</th>
                                                        <th scope="col">Palabras Claves</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Aquí se insertarán dinámicamente las filas de la tabla -->
                                                    <?php foreach ($resultado_consulta_asesor->fetchAll(PDO::FETCH_ASSOC) as $raw):
                                                        if ($raw['nombre_asesor'] == "") {
                                                            $nombre_asesor = "Sin asignar profesor";
                                                        } else {
                                                            $nombre_asesor = $raw['nombre_asesor'] . " " . $raw['apellidos_asesor'];
                                                        }
                                                        
                                                        if($raw['id_rol'] == 5){
                                                            $tipo = '<span class="badge bg-success">Director</span>';
                                                        }else{
                                                            $tipo = '<span class="badge bg-warning text-dark">Director Externo</span>';
                                                        }

                                                    ?>
                                                        <tr>
                                                            <td><?= $raw['codigo_proyecto']; ?></td>
                                                            <td><?= $nombre_asesor; ?></td>
                                                            <td><?= $tipo; ?></td>
                                                            <td><?= $raw['titulo_proyecto']; ?></td>
                                                            <td><?= $raw['palabras_claves']; ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php

                        }


                        ?>


                        <!-- Modal compañero -->
                        <div class="modal fade" id="modalCompañero" tabindex="-1" aria-labelledby="modalAnteproyectoLabel" aria-hidden="true">
                            <div class="modal-dialog modal-fullscreen">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalAnteproyectoLabel">Compañero Asignados</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Código Proyecto</th>
                                                    <th scope="col">Nombre</th>
                                                    <th scope="col">Apellidos</th>
                                                    <th scope="col">Título</th>
                                                    <th scope="col">Palabras Claves</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Aquí se insertarán dinámicamente las filas de la tabla -->
                                                <?php foreach ($resultado_consulta_compañero->fetchAll(PDO::FETCH_ASSOC) as $row): ?>
                                                    <tr>
                                                        <td><?= $row['codigo_proyecto']; ?></td>
                                                        <td><?= $row['nombre_usuario']; ?></td>
                                                        <td><?= $row['apellidos_usuario']; ?></td>
                                                        <td><?= $row['titulo_proyecto']; ?></td>
                                                        <td><?= $row['palabras_claves']; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                
                        

                    <?php


                    }
                } else if ($_SESSION['privilegio'] == 5) {
                    
                    ?>
                    <button type="button" class="btn btn-info mx-1" data-bs-toggle="modal" data-bs-target="#AnteproyectosAsignadosProfesor"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Anteproyectos Asignados">
                        Anteproyectos Asignados
                    </button>

                    <?php

                    $consulta_anteproyectos = "SELECT 
                    aa.codigo_proyecto, 
                    a.titulo_anteproyecto, 
                    a.palabras_claves, 
                    a.fecha_creacion,
                    f.nombre_facultad,
                    p.nombre_programa,
                    IFNULL(GROUP_CONCAT(CONCAT(u.nombre_usuario, ' ', u.apellidos_usuario) SEPARATOR ', '), 'Sin estudiantes asignados') AS estudiantes_asignados
                    FROM Asignar_asesor_anteproyecto_proyecto aa
                    INNER JOIN anteproyectos a ON aa.codigo_proyecto = a.codigo_anteproyecto
                    INNER JOIN facultades f ON a.id_facultad = f.id_facultad
                    INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
                    LEFT JOIN asignar_estudiante_anteproyecto ae ON a.codigo_anteproyecto = ae.codigo_anteproyecto
                    LEFT JOIN usuarios u ON ae.numero_documento = u.numero_documento
                    WHERE aa.numero_documento = '$documento_user_logueado'
                    GROUP BY aa.codigo_proyecto, a.titulo_anteproyecto, a.palabras_claves, a.fecha_creacion, f.nombre_facultad, p.nombre_programa";

                    $resultado_anteproyectos = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_anteproyectos);


                    ?>

                     <!-- Anteproyectos Asignados Profesores -->
                    <div class="modal fade" id="AnteproyectosAsignadosProfesor" tabindex="-1" aria-labelledby="modalAnteproyectoproLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalAnteproyectoLabel">Anteproyectos Asignados</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
                                        <thead>
                                            <tr>
                                                <th scope="col">Código Anteproyecto</th>
                                                <th scope="col">Título</th>
                                                <th scope="col">Estudiantes Asignados</th>
                                                <th scope="col">Palabras Claves</th>
                                                <th scope="col">Facultad</th>
                                                <th scope="col">Programa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Aquí se insertarán dinámicamente las filas de la tabla -->
                                            <?php 
                                            if ($resultado_anteproyectos->rowCount() > 0) { 
                                                foreach ($resultado_anteproyectos->fetchAll(PDO::FETCH_ASSOC) as $row): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['codigo_proyecto']); ?></td>
                                                        <td><?= htmlspecialchars($row['titulo_anteproyecto']); ?></td>
                                                        <td><?= htmlspecialchars($row['estudiantes_asignados']); ?></td>
                                                        <td><?= htmlspecialchars($row['palabras_claves']); ?></td>
                                                        <td><?= htmlspecialchars($row['nombre_facultad']); ?></td>
                                                        <td><?= htmlspecialchars($row['nombre_programa']); ?></td>
                                                    </tr>
                                                <?php endforeach; 
                                            } else { ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No hay anteproyectos asignados</td>
                                                </tr>
                                            <?php } ?>

                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <?php

                    ?>
                        <button type="button" class="btn btn-warning mx-1" data-bs-toggle="modal" data-bs-target="#ProyectosAsignadosProfesor"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Proyectos asignados">
                            Proyectos Asignados
                        </button>

                    <?php


                    $consulta_proyectos = "SELECT 
                    aa.codigo_proyecto, 
                    a.titulo_proyecto, 
                    a.palabras_claves, 
                    a.fecha_creacion,
                    f.nombre_facultad,
                    p.nombre_programa,
                    IFNULL(GROUP_CONCAT(CONCAT(u.nombre_usuario, ' ', u.apellidos_usuario) SEPARATOR ', '), 'Sin estudiantes asignados') AS estudiantes_asignados
                    FROM Asignar_asesor_anteproyecto_proyecto aa
                    INNER JOIN proyectos a ON aa.codigo_proyecto = a.codigo_proyecto
                    INNER JOIN facultades f ON a.id_facultad = f.id_facultad
                    INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
                    LEFT JOIN asignar_estudiante_proyecto ae ON a.codigo_proyecto = ae.codigo_proyecto
                    LEFT JOIN usuarios u ON ae.numero_documento = u.numero_documento
                    WHERE aa.numero_documento = '$documento_user_logueado'
                    GROUP BY aa.codigo_proyecto, a.titulo_proyecto, a.palabras_claves, a.fecha_creacion, f.nombre_facultad, p.nombre_programa";

                    $resultado_proyectos = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_proyectos);

                    ?>
                    <!-- Proyectos Asignados Profesores -->
                    <div class="modal fade" id="ProyectosAsignadosProfesor" tabindex="-1" aria-labelledby="modalProyectoLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalProyectoLabel">Proyectos Asignados</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
                                        <thead>
                                            <tr>
                                                <th scope="col">Código Proyecto</th>
                                                <th scope="col">Título</th>
                                                <th scope="col">Estudiantes Asignados</th>
                                                <th scope="col">Palabras Claves</th>
                                                <th scope="col">Facultad</th>
                                                <th scope="col">Programa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Aquí se insertarán dinámicamente las filas de la tabla -->
                                            <?php 
                                            if ($resultado_proyectos->rowCount() > 0) { 
                                                foreach ($resultado_proyectos->fetchAll(PDO::FETCH_ASSOC) as $row): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['codigo_proyecto']); ?></td>
                                                        <td><?= htmlspecialchars($row['titulo_proyecto']); ?></td>
                                                        <td><?= htmlspecialchars($row['estudiantes_asignados']); ?></td>
                                                        <td><?= htmlspecialchars($row['palabras_claves']); ?></td>
                                                        <td><?= htmlspecialchars($row['nombre_facultad']); ?></td>
                                                        <td><?= htmlspecialchars($row['nombre_programa']); ?></td>
                                                    </tr>
                                                <?php endforeach; 
                                            } else { ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No hay proyectos asignados</td>
                                                </tr>
                                            <?php } ?>

                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php


                        ?>
                        <button type="button" class="btn btn-dark mx-1" data-bs-toggle="modal" data-bs-target="#ProyectosAsignadosProfesorJurados"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Proyectos asignados">
                            Proyectos Asignados Jurado
                        </button>

                     <?php

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

                        $resultado_proyectos_jurados = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_proyectos_jurado_Asignado);

                        ?>
                        <!-- Proyectos Asignados Profesores -->
                        <div class="modal fade" id="ProyectosAsignadosProfesorJurados" tabindex="-1" aria-labelledby="modalProyectoLabel" aria-hidden="true">
                            <div class="modal-dialog modal-fullscreen">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalProyectoLabel">Proyectos Asignados como jurado</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Código Proyecto</th>
                                                    <th scope="col">Título</th>
                                                    <th scope="col">Estudiantes Asignados</th>
                                                    <th scope="col">Palabras Claves</th>
                                                    <th scope="col">Facultad</th>
                                                    <th scope="col">Programa</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Aquí se insertarán dinámicamente las filas de la tabla -->
                                                <?php 
                                                if ($resultado_proyectos_jurados->rowCount() > 0) { 
                                                    foreach ($resultado_proyectos_jurados->fetchAll(PDO::FETCH_ASSOC) as $row): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($row['codigo_proyecto']); ?></td>
                                                            <td><?= htmlspecialchars($row['titulo_proyecto']); ?></td>
                                                            <td><?= htmlspecialchars($row['estudiantes_asignados']); ?></td>
                                                            <td><?= htmlspecialchars($row['palabras_claves']); ?></td>
                                                            <td><?= htmlspecialchars($row['nombre_facultad']); ?></td>
                                                            <td><?= htmlspecialchars($row['nombre_programa']); ?></td>
                                                        </tr>
                                                    <?php endforeach; 
                                                } else { ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">No hay proyectos asignados</td>
                                                    </tr>
                                                <?php } ?>
    
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <?php
                }
            } else {
                echo ' <span class="badge bg-warning text-dark line-height-custom">El usuariono tiene asignado un anteproyecto o proyecto</span>';
            }
        }



        if ($consulta_facultades->rowCount() > 0) {
            // Botón para abrir el modal si tiene facultades y programas asignados
            echo '<button type="button" class="btn btn-success mx-2" data-bs-toggle="modal" data-bs-target="#facultadesModal"
            data-bs-toggle="tooltip" data-bs-placement="top" title="facultades y programas asignados">
                    Facultades y Programas
                  </button>';

            // Recorrer los resultados y guardar las facultades y programas
            $facultades_programas = []; // Arreglo para almacenar las facultades y programas

            while ($row = $consulta_facultades->fetch(PDO::FETCH_ASSOC)) {
                $facultades_programas[] = [
                    'facultad' => $row['nombre_facultad'],
                    'programa' => $row['nombre_programa']
                ];
            }

            // Crear un modal para mostrar las facultades y programas
            echo '<div class="modal fade" id="facultadesModal" tabindex="-1" aria-labelledby="facultadesModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="facultadesModalLabel">Facultades y Programas Asignados</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
                                        <thead>
                                            <tr>
                                                <th>Facultad</th>
                                                <th>Programa</th>
                                            </tr>
                                        </thead>
                                        <tbody>';

            // Recorrer el arreglo y mostrar las facultades y programas en la tabla
            foreach ($facultades_programas as $item) {
                echo '<tr>
                                    <td>' . $item['facultad'] . '</td>
                                    <td>' . $item['programa'] . '</td>
                                </tr>';
            }

            echo '              </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                  </div>';
        } else {
            // Mensaje si no tiene facultades y programas asignados
            echo ' <span class="badge bg-danger text-white line-height-custom ">No hay facultad ni programa asignado al usuario.</span>';
        }
    }


    ?>
    </div>

  
    <script>

    // Agregar el evento keydown al input
    document.getElementById('miMensajeInput').addEventListener('keydown', function(event) {
        // Verificar si la tecla presionada es "Enter" (codigo de tecla 13)
        if (event.key === 'Enter') {
            event.preventDefault(); // Evitar salto de línea o acción predeterminada
            // Verificar que no está vacío el mensaje antes de enviar
            let mensajeInput = document.getElementById('miMensajeInput');
            if (mensajeInput.value.trim() !== "") {
                // Llamar la función que simula el clic en el botón de enviar
                document.getElementById('enviarBtn').click();
                mensajeInput.value = ""; // Limpiar el input después de enviar
            } else {
                console.log("El mensaje está vacío"); // Mensaje opcional en consola si el campo está vacío
            }
        }
    });

        function actualizarMessageIcon(url, id_usuario_logueado) {
            fetch(`${url}Views/content/obtener_mensajes_no_leidos.php?id_usuario_logueado=${id_usuario_logueado}`)
                .then(response => response.json())
                .then(data => {
                    const badge = document.querySelector('.message-icon .badge');
                    if (badge) {
                        badge.textContent = data.mensajes_no_leidos || '0'; // Actualiza el contador o muestra '0' si no hay mensajes
                    }
                })
                .catch(error => console.error('Error al actualizar el contador de mensajes:', error));
        }

        // Llama a la función cada 10 segundos
        setInterval(() => actualizarMessageIcon('<?= SERVERURL ?>', '<?= $_SESSION['id_usuario'] ?>'), 3000);
    </script>