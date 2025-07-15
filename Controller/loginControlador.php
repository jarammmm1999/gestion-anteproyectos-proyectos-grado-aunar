<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");



if($peticionAjax){

    require_once "../Model/loginModelo.php";

}else{
    
    require_once "./Model/loginModelo.php";
}

Class LoginControlador extends LoginModelo {

    public function iniciar_sesion_controlador() {
        $numero_documento = MainModel::limpiar_cadenas($_POST['DocumentoUserLog']);
        $contrasena_usuario = MainModel::limpiar_cadenas($_POST['passwordUserLog']);
        $rolSeleccionado = isset($_POST['rolSeleccionado']) ? $_POST['rolSeleccionado'] : null;
    
        // Validar los datos
        if (empty($numero_documento) || empty($contrasena_usuario)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No has llenado todos los campos requeridos.",
                "Tipo" => "error"
            ]);
            exit();
        }
    
        // Encriptar la contraseña
        $clave = MainModel::encryption($contrasena_usuario);
    
        $datos = [
            "numero_documento" => $numero_documento,
            "contrasena_usuario" => $clave
        ];
    
        $datos_cuenta = LoginModelo::iniciar_session_modelo($datos);
    
        if ($datos_cuenta->rowCount() > 0) {
            $perfiles = $datos_cuenta->fetchAll(PDO::FETCH_ASSOC);
    
            // Si se seleccionó un rol, procesarlo
            if ($rolSeleccionado) {
                foreach ($perfiles as $perfil) {
                    if ($perfil['id_rol'] == $rolSeleccionado) {

                        // Crear sesión
                        session_start(['name' => 'Smp']);
                        $_SESSION['id_usuario'] = $perfil['id'];
                        $_SESSION['numero_documento'] = $perfil['numero_documento'];
                        $_SESSION['nombre_usuario'] = $perfil['nombre_usuario'];
                        $_SESSION['apellido_usuario'] = $perfil['apellidos_usuario'];
                        $_SESSION['correo_usuario'] = $perfil['correo_usuario'];
                        $_SESSION['privilegio'] = $perfil['id_rol'];
                        $_SESSION['token_usuario'] = md5(uniqid(mt_rand(), true));

                        $id_usuario = $perfil['id'];

                        $tokenSesion = $_SESSION['token_usuario'];

                        if ($perfil['estado'] != 1) {
                            session_unset();
                            session_destroy();
                            echo json_encode([
                                "Alerta" => "simple",
                                "Titulo" => "Cuenta bloqueada",
                                "Texto" => "Tu cuenta ha sido suspendida. Contacta con soporte.",
                                "Tipo" => "warning"
                            ]);
                            exit();
                        }
                        

                        
                        // Obtener IP y navegador
                        $ipCliente = MainModel::obtenerDireccionIP();
                        $navCliente = MainModel::obtenerNavegador();

                        // Datos del usuario
                        $idUsuario = $perfil['id'];
                        $documentoUSer = $perfil['numero_documento'];

                        // Actualizar estado del usuario a "conectado"
                        $actualizar_estado_user = MainModel::ejecutar_consultas_simples(
                            "UPDATE usuarios SET estado_conexion = 1 WHERE id = $idUsuario"
                        );

                        // Verificar si la actualización fue exitosa
                        if ($actualizar_estado_user->rowCount() >= 0) {
                            // Registrar el inicio de sesión en el historial
                            $registrar_login_user = MainModel::ejecutar_consultas_simples(
                                "INSERT INTO historial_sesiones (id_usuario, numero_documento, inicio_sesion, ip_usuario, navegador_usuario)
                                VALUES ($idUsuario, '$documentoUSer', NOW(), '$ipCliente', '$navCliente')"
                            );

                                        // Verificar si el registro fue exitoso
                            if ($registrar_login_user->rowCount() >= 1) {
                                echo json_encode([
                                    "Alerta" => "redireccionar",
                                    "URL" => SERVERURL . "home/"
                                ]);
                                exit();
                            }
                        }

    
                    }
                }
    
                // Si no se encuentra el rol seleccionado, mostrar error
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto" => "El rol seleccionado no es válido.",
                    "Tipo" => "error"
                ]);
                exit();
            }
    
            // Si hay más de un rol y no se seleccionó ninguno, mostrar el modal
            if (count($perfiles) > 1) {
                echo json_encode([
                    "Alerta" => "mostrar_modal",
                    "Perfiles" => $perfiles,
                    "url" => SERVERURL
                ]);
                exit();
            }
    
            // Si solo hay un perfil, iniciar sesión directamente
            $perfil = $perfiles[0];
            session_start(['name' => 'Smp']);
            $_SESSION['id_usuario'] = $perfil['id'];
            $_SESSION['numero_documento'] = $perfil['numero_documento'];
            $_SESSION['nombre_usuario'] = $perfil['nombre_usuario'];
            $_SESSION['apellido_usuario'] = $perfil['apellidos_usuario'];
            $_SESSION['correo_usuario'] = $perfil['correo_usuario'];
            $_SESSION['privilegio'] = $perfil['id_rol'];
            $_SESSION['token_usuario'] = md5(uniqid(mt_rand(), true));

            if($perfil['estado'] !=1){
                session_unset();
                session_destroy();
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Cuenta bloqueada",
                    "Texto" => "Tu cuenta ha sido suspendida. Contacta con soporte.",
                    "Tipo" => "warning"
                ]);
                exit();
            }

           

            // Obtener IP y navegador
             $ipCliente = MainModel::obtenerDireccionIP();
             $navCliente = MainModel::obtenerNavegador();

               // Datos del usuario
               $idUsuario = $perfil['id'];
               $documentoUSer = $perfil['numero_documento'];

               // Actualizar estado del usuario a "conectado"
               $actualizar_estado_user = MainModel::ejecutar_consultas_simples(
                   "UPDATE usuarios SET estado_conexion = 1 WHERE id = $idUsuario"
               );

               // Verificar si la actualización fue exitosa
               if ($actualizar_estado_user->rowCount() >= 0) {
                   // Registrar el inicio de sesión en el historial
                   $registrar_login_user = MainModel::ejecutar_consultas_simples(
                       "INSERT INTO historial_sesiones (id_usuario, numero_documento, inicio_sesion, ip_usuario, navegador_usuario)
                       VALUES ($idUsuario, '$documentoUSer', NOW(), '$ipCliente', '$navCliente')"
                   );

                   // Verificar si el registro fue exitoso
                   if ($registrar_login_user->rowCount() >= 1) {
                    echo json_encode([
                        "Alerta" => "redireccionar",
                        "URL" => SERVERURL . "home/"
                    ]);
                    exit();
                   }
               }
    
          
               
        } else {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Credenciales incorrectas.",
                "Tipo" => "error"
            ]);
            exit();
        }
    }
    
    

    /****************Controlador Para Cerrar Sesión ***********************/
    public function cerrar_sesion_controlador(){
        session_unset();
        session_destroy();
        if(headers_sent()){
            header("Location: ".SERVERURL."login");
            exit();
        }else{ 
            header("Location: ".SERVERURL."login");
            exit();
        }
      
    }

    public function cerrar_sesion_usuarios_controlador(){
        session_start(['name' => 'Smp']);
        $token = MainModel::decryption($_POST['token']);
        $usuario = MainModel::decryption($_POST['usuario']);
        $idusuario = MainModel::decryption($_POST['idusuario']);

        // Asegurar consistencia de tipos
            $idusuario = (int)$idusuario;
            $sessionIdUsuario = (int)$_SESSION['id_usuario'];

            // Asegurar eliminación de espacios
            $token = trim($token);
            $usuario = trim($usuario);

            // Comparar los valores
            if ($token === $_SESSION['token_usuario'] && 
                $usuario === $_SESSION['nombre_usuario'] && 
                $idusuario === $sessionIdUsuario) {


                //actualiza el estado del usuario
                $actualizar_estado_user = MainModel::ejecutar_consultas_simples(
                    "UPDATE usuarios SET estado_conexion = 0 WHERE id = $idusuario"
                );

                // Verificar si la actualización fue exitosa
                if ($actualizar_estado_user->rowCount() >= 0) {
                
                    //actualiza el estado de cierre session del usuario
                      $ultimo_id_sesion = MainModel::ejecutar_consultas_simples(
                        "SELECT id_sesion 
                         FROM historial_sesiones 
                         WHERE id_usuario = $idusuario 
                         ORDER BY id_sesion DESC 
                         LIMIT 1"
                    );

                    if ($ultimo_id_sesion->rowCount() > 0) {

                        $resultado = $ultimo_id_sesion->fetch(PDO::FETCH_ASSOC);

                        $idSesion = $resultado['id_sesion'];

                        $fechaHoraActual = date('Y-m-d H:i:s');

                        $actualizar_estado_cerrarSession_user = MainModel::ejecutar_consultas_simples(
                            "UPDATE historial_sesiones  SET cierre_sesion = '$fechaHoraActual' WHERE id_sesion  = $idSesion"
                        );

                        if ($actualizar_estado_cerrarSession_user->rowCount() >= 0) {
                            session_unset();
                            session_destroy();
                            $alerta = [
                                "Alerta" => "redireccionar",
                                "URL" => SERVERURL . "login/",
                            ];
                        }

                    }
        

                }
                

            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado",
                    "Texto" => "No se pudo cerrar la sesión en el sistema",
                    "Tipo" => "error"
                ];
            }

            echo json_encode($alerta);
            exit();
        
    }


    public function consulta_information_user($documento) {
        // Desencriptar el número de documento
        $numero_documento = mainModel::decryption($documento);
        
        // Preparar la consulta SQL con un parámetro para evitar inyecciones
        $consulta = MainModel::conectar()->prepare("SELECT * FROM usuarios WHERE numero_documento = :numero_documento");
        
        // Vincular el parámetro
        $consulta->bindParam(':numero_documento', $numero_documento, PDO::PARAM_INT);
        
        // Ejecutar la consulta
        $consulta->execute();
        
        // Retornar el objeto de consulta
        return $consulta;
    }
    
    public function consulta_information($dato, $consulta){

        $data = mainModel::decryption($dato);

        $consulta = MainModel::conectar()->prepare($consulta);

        $consulta->bindParam(':datos', $data, PDO::PARAM_INT);

        // Ejecutar la consulta
        $consulta->execute();
        // Retornar el objeto de consulta
        return $consulta;

      
    }

      
    public function consulta_informationtwo($consulta, $parametros = []){
        // Preparar la consulta
        $stmt = MainModel::conectar()->prepare($consulta);
    
        // Ejecutar la consulta con los parámetros pasados (si existen)
        if (!empty($parametros)) {
            $stmt->execute($parametros);
        } else {
            $stmt->execute();
        }
    
        // Retornar el objeto de consulta
        return $stmt;
    }
    
}