<?php

if (isset($ruta[0]) && $ruta[0] == "restore-password") {

    if (!empty($ruta[1])) {

        $id_usuario =  $ruta[1];

        $id_usuario = $ins_loginControlador->decryption_two($id_usuario);

        $sqlFecha = "SELECT * 
        FROM recuperacion_contrasena  
        WHERE id_usuario = '$id_usuario' 
        ORDER BY id DESC 
        LIMIT 1";

    $consulta_fecha_user = $ins_loginControlador->ejecutar_consultas_simples_two($sqlFecha);

    if ($consulta_fecha_user->rowCount() > 0) {

         // Obtener la fecha de creación del token y la fecha actual
         $data = $consulta_fecha_user->fetch(PDO::FETCH_ASSOC);
         $fecha_creacion = $data['fecha_creacion'];
         $fecha_actual = date("Y-m-d H:i:s");
         $token_usuario =  $data['token'];
         $estado_token =  $data['estado'];

         // Convertir las fechas a objetos DateTime para compararlas
        $fecha_creacion_dt = new DateTime($fecha_creacion);
        $fecha_actual_dt = new DateTime($fecha_actual);

        // Calcular la diferencia entre las fechas
        $diferencia = $fecha_creacion_dt->diff($fecha_actual_dt);

        // Definir el tiempo máximo de validez (por ejemplo, 24 horas)
        $horas_maximas = 24;

  

        if($estado_token == 0){
            
            if ($diferencia->days == 0 && $diferencia->h < $horas_maximas) {
            
                $sql = "SELECT * from usuarios where id = '$id_usuario';";
    
                $consulta_informacion_user = $ins_loginControlador->ejecutar_consultas_simples_two($sql);
    
                if ($consulta_informacion_user && $consulta_informacion_user->rowCount() > 0) {
    
                    
                    $data = $consulta_informacion_user->fetch(PDO::FETCH_ASSOC);
    
                    if ($data) {
                        $nombre_usuario = $data['nombre_usuario'];
    
                        $apellidos_usuario = $data['apellidos_usuario'];
    
                        $documento_user_logueado = $data['numero_documento'];
    
                        $id_rol = $data['id_rol'];
    
                        $sql = "SELECT 
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
    
                        $consulta_nombre_programa = $ins_loginControlador->ejecutar_consultas_simples_two($sql);
    
                         /*************************Extraemos el programa academico del usuario ********************************** */
    
                        if ($consulta_nombre_programa) {
    
                            $nombre_programa = $consulta_nombre_programa->fetch(PDO::FETCH_ASSOC);
    
                            if ($nombre_programa) {
    
                                $nombre_programa = $nombre_programa['nombre_programa'];
                            } else {
                                $nombre_programa = "No se encontró el programa.";
                            }
                        }
    
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
    
                        ?>
                        <div class="container-main-login">
                            <div class="container-section image">
                                <img src="<?= SERVERURL ?>/Views/assets/images/Reset password.png" alt="imagen login">
                            </div>
                            <div class="container-section login">
                                <div class="container-image mb-5">
                                    <img src="<?= SERVERURL ?>/Views/assets/images/logo-autonoma.png" alt="imagen login">
                                </div>
                                <div class="container mt-3 container-form">
                                    <h3 class="text-center mb-3 title-login two"><?= strtoupper('Restaurar Contrasena') ?></h3>
                                    <h5 class="text-center mb-3 title-login two"><?= $nombre_usuario . '  ' . $apellidos_usuario ?></h5>
                                    <h6 class="text-center mb-5 title-login two"><?= $rol . ' - ' . $nombre_programa ?></h6>
                                    <form class="FormulariosAjaxLogin" action="<?= SERVERURL ?>Ajax/UsuarioAjax.php" method="POST" class="mt-5" autocomplete="off">
                                        <div class="mb-3 mt-3">
                                            <input type="hidden" class="form-control input-text" id="IdPassword" name="IdPassword" value="<?= $ins_loginControlador->encryption($id_usuario) ?>" readonly>
                                        </div>
                                        <div class="mb-3 mt-3">
                                            <input type="password" class="form-control input-text" id="RestorPassword" name="RestorPassword" placeholder="Nueva Contraseña">
                                        </div>
                                        <div class="mb-3">
                                            <input type="password" class="form-control input-text" id="RestorPasswordConfirm" name="RestorPasswordConfirm" placeholder="Confirmar Contraseña">
                                        </div>
    
                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-send">Restaurar</button>
                                        </div>
    
                                    </form>
                                </div>
                            </div>
                        </div>
    
    
                    <?php
    
                    }
    
                }
    
    
    
            } else {
                ?>
                <div class="error-container">
                    <img src="<?= SERVERURL ?>/Views/assets/images/404.jpg" alt="imagen login">
                        <div class="overlayes">
                            <h1> Acceso denegado</h1>
                            <p>El token ha expirado. Solicita uno nuevo.</p>
                            <a href="<?=SERVERURL?>login"><button type="button">Volver al inicio</button></a>
                        </div>
                    </div>
                <?php
            
            }

        }else{
            ?>
            <div class="error-container">
                <img src="<?= SERVERURL ?>/Views/assets/images/404.jpg" alt="imagen login">
                    <div class="overlayes">
                        <h1> Acceso denegado</h1>
                        <p>El token ya fue ultilizado.</p>
                        <a href="<?=SERVERURL?>login"><button type="button">Volver al inicio</button></a>
                    </div>
                </div>
            <?php
        }
       
       

        
    }



    }else{
        ?>
        <div class="error-container">
            <img src="<?= SERVERURL ?>/Views/assets/images/404.jpg" alt="imagen login">
                <div class="overlayes">
                    <h1> Acceso denegado</h1>
                    <p>Verfica que se esten enviando los datos esperados.</p>
                    <a href="<?=SERVERURL?>login"><button type="button">Volver al inicio</button></a>
                </div>
            </div>
        <?php

    }

}




?>