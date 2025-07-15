<?php

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

if ($peticionAjax) {
    require_once "../Model/AnteproyectoModelo.php";
} else {
    require_once "./Model/AnteproyectoModelo.php";
}

class AnteproyectoControlador extends AnteproyectoModelo
{
    public function registrar_ideas_anteproyectos()
    {
        $codigo = MainModel::limpiar_cadenas($_POST['codigo_anteproyecto_reg']);
        $titulo = MainModel::limpiar_cadenas($_POST['titulo_anteproyecto_reg']);
        $palabrasClaves = MainModel::limpiar_cadenas($_POST['palabras_claves_anteproyecto_reg']);
        $tipo_faculta_reg = MainModel::limpiar_cadenas($_POST['tipo_faculta_reg']);
        $tipo_programa_reg = MainModel::limpiar_cadenas($_POST['tipo_programa_reg']);
        $tipo_modalidad_reg = MainModel::limpiar_cadenas($_POST['tipo_modalidad_reg']);

        if (empty($codigo) || empty($titulo) || empty($palabrasClaves) || empty($tipo_faculta_reg) || empty($tipo_programa_reg)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $tipo_modalidad_reg = (int) MainModel::decryption($tipo_modalidad_reg);
        
        if (!in_array($tipo_modalidad_reg, [1, 2, 3])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error de validación",
                "Texto" => "El valor ingresado para la modalidad no es válido.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $check_codigo = MainModel::ejecutar_consultas_simples(
            "SELECT codigo_anteproyecto FROM anteproyectos WHERE codigo_anteproyecto = '$codigo'"
        );

        if ($check_codigo->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El código del proyecto ya está registrado en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        
        $check_titulo = MainModel::ejecutar_consultas_simples(
            "SELECT titulo_anteproyecto FROM anteproyectos WHERE titulo_anteproyecto = '$titulo'"
        );

        if ($check_titulo->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Ya hay un proyecto registrado con ese título.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $tipo_faculta = (int) MainModel::decryption($tipo_faculta_reg);
        $check_tipo_faculta_usuario = MainModel::ejecutar_consultas_simples("SELECT id_facultad FROM facultades 
        WHERE id_facultad = '$tipo_faculta'");

        if ($check_tipo_faculta_usuario->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El código de facultad que intentas ingresar no se encuentra registrados",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        
        $tipo_programa = (int) MainModel::decryption($tipo_programa_reg);
        $check_tipo_programa_usuario = MainModel::ejecutar_consultas_simples("SELECT id_programa FROM programas_academicos 
        WHERE id_programa = '$tipo_programa'");

        if ($check_tipo_programa_usuario->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El código de programa que intentas ingresar no se encuentra registrados",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_tipo_programa_usuario = MainModel::ejecutar_consultas_simples(
            "SELECT id_programa 
             FROM programas_academicos 
             WHERE id_programa = '$tipo_programa' 
             AND id_facultad = '$tipo_faculta'"
        );
        
        // Verificar si se encontró un programa que pertenece a la facultad
        if ($check_tipo_programa_usuario->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El programa seleccionado no pertenece a la facultad correspondiente.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }



        $datos_anteproyecto = [
            "codigo_anteproyecto" => $codigo,
            "titulo_anteproyecto" => $titulo,
            "palabras_claves_anteproyecto" => $palabrasClaves,
            "id_facultad" => (int) $tipo_faculta,
            "id_programa" => (int) $tipo_programa,
            "modalidad" => (int) $tipo_modalidad_reg
        ];

        $guardar_anteproyecto = AnteproyectoModelo::agregar_anteproyecto_modelo($datos_anteproyecto);


        if ($guardar_anteproyecto->rowCount() > 0) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Operación exitosa",
                "Texto" => "El anteproyecto ha sido registrado correctamente.",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo registrar el anteproyecto.",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
        exit();
    }
    
    public function Asignar_estudiante_proyecto(){

        $extraer_datos_configuracion = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM configuracion_aplicacion"
        );
        
        if ($extraer_datos_configuracion->rowCount() > 0) { 
            $configuracion = $extraer_datos_configuracion->fetch(PDO::FETCH_ASSOC);
            $logo = $configuracion['nombre_logo'];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo extraer el nombre del logo",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        

        $check_numero_horas_registradas = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM configuracion_aplicacion "
        );
        if ($check_numero_horas_registradas->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento del usuario no existe en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        
        $datos_configuracion = $check_numero_horas_registradas->fetch(PDO::FETCH_ASSOC);
        

        $numero_maximo_estudiantes = (int) $datos_configuracion['numero_estudiantes_proyectos'];

        $codigo = MainModel::limpiar_cadenas($_POST['codigo_anteproyecto_regA']);

        $numero_documento = MainModel::limpiar_cadenas($_POST['numero_documento_regA']);

        $id_programa_user = MainModel::limpiar_cadenas($_POST['id_programa_user']);



        if (empty($codigo) || empty($numero_documento) || empty($id_programa_user)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_codigo = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM anteproyectos WHERE codigo_anteproyecto = '$codigo'"
        );

        // Verificar si el código existe en la base de datos
        if ($check_codigo->rowCount() <= 0) {
            // Si no existe, enviar un mensaje de alerta
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Código no encontrado",
                "Texto" => "El código del anteproyecto no está registrado en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        
        $datos_anteproyecto = $check_codigo->fetch(PDO::FETCH_ASSOC);

        $id_programa_anteproyecto = $datos_anteproyecto['id_programa']; 

        $id_faculta_anteproyecto = $datos_anteproyecto['id_facultad']; 

        $titulo_anteproyecto = $datos_anteproyecto['titulo_anteproyecto']; 


        $check_estudiante = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM usuarios WHERE numero_documento = '$numero_documento'"
        );
        if ($check_estudiante->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento del estudiante no existe en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        
        $usuario = $check_estudiante->fetch(PDO::FETCH_ASSOC);

        $id_rol_estudiante = $usuario['id_rol'];  

        $nombre_estudiante =  $usuario['nombre_usuario'] .' ' .$usuario['apellidos_usuario']; 

        $nombre_usuario = $usuario['nombre_usuario'];

        $apellido_usuario = $usuario['apellidos_usuario'];

        $correo_usuario = $usuario['correo_usuario'];

        

        $check_estudiante_asignacion = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM Asignar_usuario_facultades WHERE numero_documento = '$numero_documento'"
        );
        if ($check_estudiante_asignacion->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento del estudiante " .$nombre_estudiante."  no tiene asignado una facultad y un programa academico.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
      

        $datos_asignacion = $check_estudiante_asignacion->fetch(PDO::FETCH_ASSOC);

        $id_programa_asignacion_estudiante = $datos_asignacion['id_programa'];  

        $id_faculta_asignacion_estudiante = $datos_asignacion['id_facultad'];
    

        if($id_programa_anteproyecto === $id_programa_asignacion_estudiante && $id_faculta_anteproyecto === $id_faculta_asignacion_estudiante){

            
            $consulta_numero_estudiantes = MainModel::ejecutar_consultas_simples(
                "SELECT COUNT(numero_documento) AS total_estudiantes 
                FROM asignar_estudiante_anteproyecto 
                WHERE codigo_anteproyecto = '$codigo'"
            );
            if ($consulta_numero_estudiantes->rowCount() > 0) {
                
                $datos_asignacion = $consulta_numero_estudiantes->fetch(PDO::FETCH_ASSOC);

                $total_estudiantes = $datos_asignacion['total_estudiantes'];


                if ($id_rol_estudiante != 3) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Acceso denegado",
                        "Texto" => "El usuario " . $nombre_estudiante . " no tiene el rol adecuado para esta operación, no es un estudiante de anteproyectos",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit(); 
                }

                // Verificar si el usuario ya está asignado al proyecto

                $consulta = MainModel::ejecutar_consultas_simples(
                    " SELECT numero_documento 
                    FROM asignar_estudiante_anteproyecto 
                    WHERE codigo_anteproyecto = '$codigo'
                    AND numero_documento = '$numero_documento'"
                );

                if ($consulta->rowCount() > 0) {
                    // Si ya está asignado, enviar un mensaje de alerta
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Usuario ya asignado",
                        "Texto" => "El estudiante  " . $nombre_estudiante . " ya está asignado al anteproyecto " . $codigo . ".",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                $consulta = MainModel::ejecutar_consultas_simples(
                    "SELECT numero_documento 
                    FROM asignar_estudiante_anteproyecto 
                    WHERE numero_documento = '$numero_documento' 
                    AND codigo_anteproyecto != '$codigo'"
                );

                if ($consulta->rowCount() > 0) {
                    // Si ya está asignado a otro proyecto, enviar un mensaje de alerta
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Usuario asignado a otro proyecto",
                        "Texto" => "El estudiante  " . $nombre_estudiante . " ya está asignado a otro anteproyecto y no puede ser asignado a este.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                if($total_estudiantes >= $numero_maximo_estudiantes){
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Número máximo de estudiantes",
                        "Texto" => "El número máximo de estudiantes para este anteproyecto ha sido alcanzado.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                $datos_asignar = [
                    "codigo_anteproyecto" => $codigo,
                    "numero_documento" => $numero_documento
                ];

                $guardar_asignar = AnteproyectoModelo::asignar_estudiante_proyecto_modelo($datos_asignar);

                if ($guardar_asignar->rowCount() > 0) {


                    $message = "<p>🎉 ¡Tienes un nuevo reto por delante! Se te ha asignado un <b>anteproyecto</b>, una oportunidad única para demostrar tu capacidad, creatividad y compromiso académico. 📚🚀</p>

                    <p>Desde ahora, cada paso que des será clave en el desarrollo de este trabajo. Contarás con el apoyo de asesores, herramientas especializadas y un espacio diseñado para que avances de manera organizada y eficiente. 🛠️✨</p>

                    <p>Recuerda, <b>cada esfuerzo que pongas en este proyecto es un paso más hacia tu meta profesional</b>. Confía en tu potencial, mantén la disciplina y aprovecha al máximo esta experiencia. ¡El éxito está en tus manos! 💡🔥</p>";


                    include __DIR__ . '/../Mail/enviar-correo.php';

                    $asunto = "Notificación de Asignación de Anteproyecto";

                        $cuerpo_html = '
                
                        <!DOCTYPE html>
                        <html lang="es">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Bienvenido a nuestra plataforma</title>'
                            .STYLESCORREO.'
                        </head>
                        <body>
                            <div class="email-container">
                                <div class="email-header">
                                    <img src="'.SERVERURL.'Views/assets/images/'.$logo.'" alt="Logo Universidad">
                                      <h2>Asignación de Anteproyecto</h2>
                                </div>
                                <div class="email-body">
                                    <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                                    '.$message.'
                                    <p>Nos complace informarte que se te ha asignado el siguiente anteproyecto:</p>
                                    <div class="credentials">
                                        <ul>
                                            <li><b>Código del Anteproyecto:</b> ' . $codigo . '</li>
                                            <li><b>Título del Anteproyecto:</b> ' . $titulo_anteproyecto . '</li>
                                        </ul>
                                    </div>

                                    <p><b>Atentamente,</b><br>
                                    <i>Corporación Universitaria Autónoma de Nariño</i></p>
                                </div>
                                <div class="email-footer">
                                    Este mensaje se envió desde una dirección de correo electrónico no supervisada. No responda a este mensaje.
                                </div>
                            </div>
                        </body>
                        </html>
                        ';

                    $cuerpo_texto = "Estimado $nombre_usuario $apellido_usuario, nos complace informarte que se te ha asignado el anteproyecto con el código $codigo y el título $titulo_anteproyecto. ¡Estamos seguros de que harás un excelente trabajo!";

                    $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);
                    
                    if ($enviado) {
                        $alerta = [
                            "Alerta" => "Recargar",
                            "Titulo" => "Operación exitosa",
                            "Texto" => "El estudiante ha sido asignado al anteproyecto correctamente.",
                            "Tipo" => "success"
                        ];
                    }else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "El estudiante ha sido asignado al anteproyecto correctamente. pero no se pudo enviar el correo electrónico al estudiante.",
                            "Tipo" => "error"
                        ];
                    }

                echo json_encode($alerta);
                exit();


                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado",
                        "Texto" => "No se pudo asignar el estudiante al anteproyecto.",
                        "Tipo" => "error"
                    ];
                }

                echo json_encode($alerta);
                exit();



            }


        }else{
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El programa del estudiante no coincide con el del anteproyecto.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

       

        /***************si el usuario tiene rol de administrador  *****************/
            
        if($id_programa_user===7){

            
        if($id_programa_anteproyecto === $id_programa_asignacion_estudiante && $id_faculta_anteproyecto === $id_faculta_asignacion_estudiante){

            
            $consulta_numero_estudiantes = MainModel::ejecutar_consultas_simples(
                "SELECT COUNT(numero_documento) AS total_estudiantes 
                FROM asignar_estudiante_anteproyecto 
                WHERE codigo_anteproyecto = '$codigo'"
            );
    
            if ($consulta_numero_estudiantes->rowCount() > 0) {
                
                $datos_asignacion = $consulta_numero_estudiantes->fetch(PDO::FETCH_ASSOC);

                $total_estudiantes = $datos_asignacion['total_estudiantes'];
                
            
                if ($id_rol_estudiante != 3) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Acceso denegado",
                        "Texto" => "El usuario " . $nombre_estudiante . " no tiene el rol adecuado para esta operación.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit(); 
                }

                // Verificar si el usuario ya está asignado al proyecto

                $consulta = MainModel::ejecutar_consultas_simples(
                    " SELECT numero_documento 
                    FROM asignar_estudiante_anteproyecto 
                    WHERE codigo_anteproyecto = '$codigo'
                    AND numero_documento = '$numero_documento'"
                );

                if ($consulta->rowCount() > 0) {
                    // Si ya está asignado, enviar un mensaje de alerta
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Usuario ya asignado",
                        "Texto" => "El estudiante  " . $nombre_estudiante . " ya está asignado al proyecto " . $codigo . ".",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                $consulta = MainModel::ejecutar_consultas_simples(
                    "SELECT numero_documento 
                    FROM asignar_estudiante_anteproyecto 
                    WHERE numero_documento = '$numero_documento' 
                    AND codigo_anteproyecto != '$codigo'"
                );

                if ($consulta->rowCount() > 0) {
                    // Si ya está asignado a otro proyecto, enviar un mensaje de alerta
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Usuario asignado a otro proyecto",
                        "Texto" => "El estudiante  " . $nombre_estudiante . " ya está asignado a otro proyecto y no puede ser asignado a este.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
                
                if($total_estudiantes >= $numero_maximo_estudiantes){
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Número máximo de estudiantes",
                        "Texto" => "El número máximo de estudiantes para este anteproyecto ha sido alcanzado.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                $datos_asignar = [
                    "codigo_anteproyecto" => $codigo,
                    "numero_documento" => $numero_documento
                ];

                $guardar_asignar = AnteproyectoModelo::asignar_estudiante_proyecto_modelo($datos_asignar);

                if ($guardar_asignar->rowCount() > 0) {
                   
                     // Enviar correo al usuarios

                     
                    include __DIR__ . '/../Mail/enviar-correo.php';

                    $asunto = "Notificación de Asignación de Anteproyecto";

                        $cuerpo_html = '
                
                        <!DOCTYPE html>
                        <html lang="es">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Bienvenido a nuestra plataforma</title>'
                            .STYLESCORREO.'
                        </head>
                        <body>
                            <div class="email-container">
                                <div class="email-header">
                                    <img src="'.SERVERURL.'Views/assets/images/'.$logo.'" alt="Logo Universidad">
                                      <h2>Asignación de Anteproyecto</h2>
                                </div>
                                <div class="email-body">
                                    <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                                    '.$message.'
                                    <p>Nos complace informarte que se te ha asignado el siguiente anteproyecto:</p>
                                    <div class="credentials">
                                        <ul>
                                            <li><b>Código del Anteproyecto:</b> ' . $codigo . '</li>
                                            <li><b>Título del Anteproyecto:</b> ' . $titulo_anteproyecto . '</li>
                                        </ul>
                                    </div>

                                    <p><b>Atentamente,</b><br>
                                    <i>Corporación Universitaria Autónoma de Nariño</i></p>
                                </div>
                                <div class="email-footer">
                                    Este mensaje se envió desde una dirección de correo electrónico no supervisada. No responda a este mensaje.
                                </div>
                            </div>
                        </body>
                        </html>
                        ';

                    $cuerpo_texto = "Estimado $nombre_usuario $apellido_usuario, nos complace informarte que se te ha asignado el anteproyecto con el código $codigo y el título $titulo_anteproyecto. ¡Estamos seguros de que harás un excelente trabajo!";

                     $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);
                     
                     if ($enviado) {
                         $alerta = [
                             "Alerta" => "Recargar",
                             "Titulo" => "Operación exitosa",
                             "Texto" => "El estudiante ha sido asignado al anteproyecto correctamente.",
                             "Tipo" => "success"
                         ];
                     }else {
                         $alerta = [
                             "Alerta" => "simple",
                             "Titulo" => "Ocurrió un error inesperado",
                             "Texto" => "El estudiante ha sido asignado al anteproyecto correctamente. pero no se pudo enviar el correo electrónico al estudiante.",
                             "Tipo" => "error"
                         ];
                     }
 
                 echo json_encode($alerta);
                 exit();


                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado",
                        "Texto" => "No se pudo asignar el estudiante al proyecto.",
                        "Tipo" => "error"
                    ];
                }

                echo json_encode($alerta);
                exit();



            }
            

        }else{
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El programa del estudiante no coincide con el del anteproyecto.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        }else{
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "El usuario " . $numero_documento . " no tiene el rol adecuado para esta operación.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

      




    }/**********aqui cierra */
   
    public function Asignar_horas_profesor(){

        
        $extraer_datos_configuracion = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM configuracion_aplicacion"
        );
        
        if ($extraer_datos_configuracion->rowCount() > 0) { 
            $configuracion = $extraer_datos_configuracion->fetch(PDO::FETCH_ASSOC);
            $logo = $configuracion['nombre_logo'];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo extraer el nombre del logo",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_numero_horas_registradas = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM configuracion_aplicacion "
        );
        if ($check_numero_horas_registradas->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento del usuario no existe en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        
        $datos_configuracion = $check_numero_horas_registradas->fetch(PDO::FETCH_ASSOC);
        

        $numero_horas_maximas_profesores = (int) $datos_configuracion['numero_horas_asesorias'];
        
        $numero_horas = MainModel::limpiar_cadenas($_POST['numero_horas_asesorias_reg']);
        $numero_documento = MainModel::limpiar_cadenas($_POST['numero_documento_regP']);
        $numero_documento_user_logueado = MainModel::limpiar_cadenas($_POST['numero_documento_user_logueado']);

        if (empty($numero_horas) || empty($numero_documento)  || empty($numero_documento_user_logueado) ) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if ($numero_horas % 2 !== 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error de validación",
                "Texto" => "El número ingresado debe  debe ser par.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        if($numero_horas > $numero_horas_maximas_profesores ){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Número de horas excedido",
                "Texto" => "El número de horas de asesorías no puede superar las $numero_horas_maximas_profesores horas.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        /**************validar si el usuario existe ********************* */

        $check_profesor = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM usuarios WHERE numero_documento = '$numero_documento'"
        );
        if ($check_profesor->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento del usuario no existe en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        
        $usuario = $check_profesor->fetch(PDO::FETCH_ASSOC);

        $id_rol_usuario = $usuario['id_rol'];  

        $nombre_usuario_registrado =  $usuario['nombre_usuario'] .' ' .$usuario['apellidos_usuario']; 
        
        $nombre_usuario = $usuario['nombre_usuario'];

        $apellido_usuario = $usuario['apellidos_usuario'];

        $correo_usuario = $usuario['correo_usuario'];

        
        /**************validar si el usuario tiene el rol correcto ********************* */

        if ($id_rol_usuario!= 5) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "El usuario ". $nombre_usuario_registrado . " no tiene el rol adecuado para asignar horas de asesorías .",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $check_profesor_facultad = MainModel::ejecutar_consultas_simples(
            "SELECT 
                    u.id,
                    u.numero_documento,
                    u.nombre_usuario,
                    u.apellidos_usuario,
                    u.correo_usuario,
                    u.telefono_usuario,
                    u.id_rol,
                    auf.id_facultad,
                    auf.id_programa,
                    f.nombre_facultad
                FROM usuarios u
                INNER JOIN Asignar_usuario_facultades auf ON u.numero_documento = auf.numero_documento
                INNER JOIN facultades f ON auf.id_facultad = f.id_facultad
                WHERE u.numero_documento = '$numero_documento'");

        if ($check_profesor_facultad->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento de ". $nombre_usuario_registrado ." no tiene asignada una facultad y un programa ",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        
        $profesor_facultad = $check_profesor_facultad->fetch(PDO::FETCH_ASSOC);

        $id_facultad_profesor = $profesor_facultad['id_facultad'];

        $id_programa_profesor = $profesor_facultad['id_programa'];


        /**************validar informacion logueado  ********************* */
    
        $numero_documento_user_logueado =  MainModel::decryption($numero_documento_user_logueado);

        $check_rol_usuario_logueado = MainModel::ejecutar_consultas_simples(
            "SELECT id_rol FROM usuarios WHERE numero_documento = '$numero_documento_user_logueado'"
        );
        if ($check_profesor->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento del usuario no existe en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $profesor_rol = $check_rol_usuario_logueado->fetch(PDO::FETCH_ASSOC);

        $rol_usuario_logueado = $profesor_rol['id_rol'];

        if($rol_usuario_logueado == 1){

            $check_usuario = MainModel::ejecutar_consultas_simples(
                "SELECT numero_documento FROM asignar_horas_profesor WHERE numero_documento = '$numero_documento'"
            );
    
            if ($check_usuario->rowCount() > 0) {
                // Si se encontró un registro, el usuario ya está registrado
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El profesor ". $nombre_usuario ." ya tiene horas registradas",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            // Si el usuario no está registrado, insertarlo en la base de datos
        $datos_asignar = [
            "numero_documento" => $numero_documento,
            "numero_horas" => $numero_horas
        ];

        $guardar_asignar = AnteproyectoModelo::asignar_horas_profesor_modelos($datos_asignar);

        if ($guardar_asignar->rowCount() > 0) {

            

            $message = "<p>📢 ¡Nueva asignación! Se te han programado <b>horas de asesoría</b>, una oportunidad clave para guiar y potenciar el desarrollo académico de los estudiantes. 🎓✨</p>

            <p>Tu conocimiento y experiencia serán fundamentales para orientar a los futuros profesionales en la construcción de sus proyectos. Cada sesión representa un paso más en su formación, y tu apoyo marcará una gran diferencia en su éxito. 📖💡</p>

            <p><b>Tu labor es esencial.</b> Inspira, motiva y comparte tu sabiduría con quienes confían en ti. ¡Juntos, estamos construyendo el futuro! 🚀🔥</p>";



                    include __DIR__ . '/../Mail/enviar-correo.php';

                    $asunto = "Notificación de Asignación de Horas de Asesoría";

                        $cuerpo_html = '
                
                        <!DOCTYPE html>
                        <html lang="es">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Bienvenido a nuestra plataforma</title>'
                            .STYLESCORREO.'
                        </head>
                        <body>
                            <div class="email-container">
                                <div class="email-header">
                                    <img src="'.SERVERURL.'Views/assets/images/'.$logo.'" alt="Logo Universidad">
                                     <h2>Asignación de Horas de Asesoría</h2>
                                </div>
                                <div class="email-body">
                                    <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                                    '.$message.'
                                    <p>Esperamos que estas horas te permitan brindar un apoyo óptimo a los estudiantes en sus proyectos y anteproyectos.</p>
                                    <div class="credentials">
                                        <ul>
                                            <li><b>Numero de horas asignadas</b> ' . $numero_horas . '</li>
                                            
                                        </ul>
                                    </div>

                                    <p><b>Atentamente,</b><br>
                                    <i>Corporación Universitaria Autónoma de Nariño</i></p>
                                </div>
                                <div class="email-footer">
                                    Este mensaje se envió desde una dirección de correo electrónico no supervisada. No responda a este mensaje.
                                </div>
                            </div>
                        </body>
                        </html>
                        ';
                        $cuerpo_texto = "Estimado $nombre_usuario $apellido_usuario, nos complace informarte que se te han asignado $numero_horas horas de asesoría para el período académico actual. ¡Esperamos que puedas apoyar a los estudiantes de la mejor manera!";

            // Enviar el correo
            $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);


            if ($enviado) {
                $alerta = [
                    "Alerta" => "Recargar",
                    "Titulo" => "Operación exitosa",
                    "Texto" => "Se le asignaron las horas de asesorias al profesor ". $nombre_usuario_registrado ." ha sido registrado con éxito",
                    "Tipo" => "success"
                ];
            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "No se pudo enviar el correo electrónico al profesor.". $nombre_usuario_registrado,
                    "Tipo" => "error"
                ];
            }

             
            echo json_encode($alerta);
            exit();


        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo registrar las horas de asesoria al profesor.". $nombre_usuario_registrado ,
                "Tipo" => "error"
            ];
        }
        
        echo json_encode($alerta);
        exit();


        }elseif($rol_usuario_logueado == 2){

            $check_cordinador_logueado = MainModel::ejecutar_consultas_simples(
                "SELECT 
                        u.id,
                        u.numero_documento,
                        u.nombre_usuario,
                        u.apellidos_usuario,
                        u.correo_usuario,
                        u.telefono_usuario,
                        u.id_rol,
                        auf.id_facultad,
                        auf.id_programa,
                        f.nombre_facultad
                    FROM usuarios u
                    INNER JOIN Asignar_usuario_facultades auf ON u.numero_documento = auf.numero_documento
                    INNER JOIN facultades f ON auf.id_facultad = f.id_facultad
                    WHERE u.numero_documento = '$numero_documento_user_logueado'"
            );
            if ($check_cordinador_logueado->rowCount() <= 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El número de documento del coordiador o administrador no existe en el sistema.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
    
            // Crear arreglos para almacenar las facultades y programas del coordinador logueado
                $facultades_coordinador = [];
                $programas_coordinador = [];

                // Recorrer todas las facultades y programas del coordinador logueado
                while ($row = $check_cordinador_logueado->fetch(PDO::FETCH_ASSOC)) {
                    $facultades_coordinador[] = $row['id_facultad'];  // Almacenar id de facultades
                    $programas_coordinador[] = $row['id_programa'];   // Almacenar id de programas
                }

                // Comparar las facultades y programas del coordinador logueado con el usuario
                if (in_array($id_facultad_profesor, $facultades_coordinador)) {
                        // Si el usuario no está registrado, insertarlo en la base de datos
                        $check_usuario = MainModel::ejecutar_consultas_simples(
                            "SELECT numero_documento FROM asignar_horas_profesor WHERE numero_documento = '$numero_documento'"
                        );
                
                        if ($check_usuario->rowCount() > 0) {
                            // Si se encontró un registro, el usuario ya está registrado
                            $alerta = [
                                "Alerta" => "simple",
                                "Titulo" => "Ocurrió un error inesperado",
                                "Texto" => "El profesor ". $nombre_usuario_registrado ." ya tiene horas registradas",
                                "Tipo" => "error"
                            ];
                            echo json_encode($alerta);
                            exit();
                        }

                    $datos_asignar = [
                        "numero_documento" => $numero_documento,
                        "numero_horas" => $numero_horas
                    ];

                    $guardar_asignar = AnteproyectoModelo::asignar_horas_profesor_modelos($datos_asignar);

                    if ($guardar_asignar->rowCount() > 0) {
                        $asunto = "Notificación de Asignación de Horas de Asesoría";

                        $cuerpo_html = '
                
                        <!DOCTYPE html>
                        <html lang="es">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Bienvenido a nuestra plataforma</title>'
                            .STYLESCORREO.'
                        </head>
                        <body>
                            <div class="email-container">
                                <div class="email-header">
                                    <img src="'.SERVERURL.'Views/assets/images/'.$logo.'" alt="Logo Universidad">
                                     <h2>Asignación de Horas de Asesoría</h2>
                                </div>
                                <div class="email-body">
                                    <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                                    '.$message.'
                                    <p>Esperamos que estas horas te permitan brindar un apoyo óptimo a los estudiantes en sus proyectos y anteproyectos.</p>
                                    <div class="credentials">
                                        <ul>
                                            <li><b>Numero de horas asignadas</b> ' . $numero_horas . '</li>
                                            
                                        </ul>
                                    </div>

                                    <p><b>Atentamente,</b><br>
                                    <i>Corporación Universitaria Autónoma de Nariño</i></p>
                                </div>
                                <div class="email-footer">
                                    Este mensaje se envió desde una dirección de correo electrónico no supervisada. No responda a este mensaje.
                                </div>
                            </div>
                        </body>
                        </html>
                        ';
                        $cuerpo_texto = "Estimado $nombre_usuario $apellido_usuario, nos complace informarte que se te han asignado $numero_horas horas de asesoría para el período académico actual. ¡Esperamos que puedas apoyar a los estudiantes de la mejor manera!";

                        // Enviar el correo
                        $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);


                        if ($enviado) {
                            $alerta = [
                                "Alerta" => "Recargar",
                                "Titulo" => "Operación exitosa",
                                "Texto" => "Se le asignaron las horas de asesorias al profesor ". $nombre_usuario_registrado ." ha sido registrado con éxito",
                                "Tipo" => "success"
                            ];
                        } else {
                            $alerta = [
                                "Alerta" => "simple",
                                "Titulo" => "Ocurrió un error inesperado",
                                "Texto" => "No se pudo enviar el correo electrónico al profesor.". $nombre_usuario_registrado,
                                "Tipo" => "error"
                            ];
                        }

                        
                        echo json_encode($alerta);
                        exit();
                    } else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "No se pudo registrar las horas de asesoria al profesor.". $nombre_usuario_registrado ,
                            "Tipo" => "error"
                        ];
                    }
                    
                    echo json_encode($alerta);
                    exit();

                    
                }else{
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado",
                        "Texto" => "El número de documento de ". $nombre_usuario_registrado ."  no pertenece a ninguna de las facultades y programas del coordinador logueado",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
                            

        }



    }

    public function actualizar_idea_controlador(){

        $codigo = MainModel::limpiar_cadenas($_POST['codigo_idea_upd']);
        $titulo = MainModel::limpiar_cadenas($_POST['titulo_idea_upd']);
        $palabrasClaves = MainModel::limpiar_cadenas($_POST['palabras_claves_upd']);

        if (empty($codigo) || empty($palabrasClaves) || empty($titulo) ) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        
        $codigo =  MainModel::decryption($codigo);

        $check_codigo = MainModel::ejecutar_consultas_simples(
            "SELECT codigo_anteproyecto FROM anteproyectos WHERE codigo_anteproyecto = '$codigo'"
        );

        if ($check_codigo->rowCount() == 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El código del proyecto ingresado no existe",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $datos_idea = [
            "codigo_anteproyecto" => $codigo,
            "titulo_idea" => $titulo,
            "palabras_claves" => $palabrasClaves
        ];

        $actualizar_idea = AnteproyectoModelo::actualizar_idea_modelos($datos_idea);
        
        if ($actualizar_idea->rowCount() > 0) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Operación exitosa",
                "Texto" => "Se actualizó la idea del proyecto con éxito",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo actualizar la idea del proyecto",
                "Tipo" => "error"
            ];
        }
        
        echo json_encode($alerta);
        exit();
       
    

    }

    public function eliminar_estudiante_ideas(){
        
        $codigo = MainModel::limpiar_cadenas($_POST['codigoDA']);
        $numero_documento = MainModel::limpiar_cadenas($_POST['documento_userDA']);

        if (empty($codigo) || empty($numero_documento) ) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $numero_documento =  MainModel::decryption($numero_documento);

        $codigo =  MainModel::decryption($codigo);

        $check_codigo = MainModel::ejecutar_consultas_simples(
            "SELECT codigo_anteproyecto FROM anteproyectos WHERE codigo_anteproyecto = '$codigo'"
        );

        if ($check_codigo->rowCount() == 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El codigo ingresado no existe",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_id_usuario = MainModel::ejecutar_consultas_simples("SELECT numero_documento FROM usuarios WHERE numero_documento = '$numero_documento'");

        if ($check_id_usuario->rowCount() == 0) {  // Si no hay coincidencias
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El usuario no existe en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $eliminar_estudiante = AnteproyectoModelo::eliminar_estudiante_ideas_modelos($codigo, $numero_documento);
        
        if ($eliminar_estudiante->rowCount() > 0) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Operación exitosa",
                "Texto" => "El estudiante fue eliminado de las ideas del anteproyecto",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo eliminar al estudiante del anteproyecto",
                "Tipo" => "error"
            ];
        }
        
        echo json_encode($alerta);
        exit();


    }

    public function eliminar_idea_controlador(){

         $codigo = MainModel::limpiar_cadenas($_POST['codigo_idea_delete']);
         if (empty($codigo)) {  
             $alerta = [
                 "Alerta" => "simple",
                 "Titulo" => "Ocurrió un error inesperado",
                 "Texto" => "Todos los campos son obligatorios",
                 "Tipo" => "error"
             ];
             echo json_encode($alerta);
             exit();
        }
        



         $codigo =  MainModel::decryption($codigo);

         $check_codigo = MainModel::ejecutar_consultas_simples(
             "SELECT codigo_anteproyecto FROM anteproyectos WHERE codigo_anteproyecto = '$codigo'"
         );

         if ($check_codigo->rowCount() == 0) {
             $alerta = [
                 "Alerta" => "simple",
                 "Titulo" => "Ocurrió un error inesperado",
                 "Texto" => "El código del proyecto ingresado no existe",
                 "Tipo" => "error"
             ];
             echo json_encode($alerta);
             exit();
         }


         $eliminar_idea = AnteproyectoModelo::eliminar_idea_modelos($codigo);

         if ($eliminar_idea->rowCount() > 0) {
             $alerta = [
                 "Alerta" => "Recargar",
                 "Titulo" => "Operación exitosa",
                 "Texto" => "La idea del proyecto fue eliminada",
                 "Tipo" => "success"
             ];
         } else {
             $alerta = [
                 "Alerta" => "simple",
                 "Titulo" => "Ocurrió un error inesperado",
                 "Texto" => "No se pudo eliminar la idea del proyecto",
                 "Tipo" => "error"
             ];
         }
         echo json_encode($alerta);
         exit();

    }

    public function Asignar_horas_jurado_profesor(){
     
        $extraer_datos_configuracion = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM configuracion_aplicacion"
        );
        
        if ($extraer_datos_configuracion->rowCount() > 0) { 
            $configuracion = $extraer_datos_configuracion->fetch(PDO::FETCH_ASSOC);
            $logo = $configuracion['nombre_logo'];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo extraer el nombre del logo",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        
        $check_numero_horas_registradas = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM configuracion_aplicacion "
        );
        if ($check_numero_horas_registradas->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No hay horas registradas en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        
        $datos_configuracion = $check_numero_horas_registradas->fetch(PDO::FETCH_ASSOC);
        

        $numero_horas_maximas_profesores = (int) $datos_configuracion['numero_horas_jurados'];
        
        $numero_horas = MainModel::limpiar_cadenas($_POST['numero_horas_jurado_reg']);
        $numero_documento = MainModel::limpiar_cadenas($_POST['numero_documento_regP']);
        $numero_documento_user_logueado = MainModel::limpiar_cadenas($_POST['numero_documento_user_logueado']);

        if (empty($numero_horas) || empty($numero_documento)  || empty($numero_documento_user_logueado) ) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if ($numero_horas % 2 !== 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error de validación",
                "Texto" => "El número ingresado debe  debe ser par.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if($numero_horas > $numero_horas_maximas_profesores ){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Número de horas excedido",
                "Texto" => "El número de horas de jurados no puede superar las $numero_horas_maximas_profesores horas.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        /**************validar si el usuario existe ********************* */

        $check_profesor = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM usuarios WHERE numero_documento = '$numero_documento'"
        );
        if ($check_profesor->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento del usuario no existe en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        
        $usuario = $check_profesor->fetch(PDO::FETCH_ASSOC);

        $id_rol_usuario = $usuario['id_rol'];  

        $nombre_usuario_registrado =  $usuario['nombre_usuario'] .' ' .$usuario['apellidos_usuario']; 
        
        $nombre_usuario = $usuario['nombre_usuario'];

        $apellido_usuario = $usuario['apellidos_usuario'];

        $correo_usuario = $usuario['correo_usuario'];
        
        /**************validar si el usuario tiene el rol correcto ********************* */

        if ($id_rol_usuario!= 5) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "El usuario ". $nombre_usuario_registrado . " no tiene el rol adecuado para asignar horas de asesorías .",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $check_profesor_facultad = MainModel::ejecutar_consultas_simples(
            "SELECT 
                    u.id,
                    u.numero_documento,
                    u.nombre_usuario,
                    u.apellidos_usuario,
                    u.correo_usuario,
                    u.telefono_usuario,
                    u.id_rol,
                    auf.id_facultad,
                    auf.id_programa,
                    f.nombre_facultad
                FROM usuarios u
                INNER JOIN Asignar_usuario_facultades auf ON u.numero_documento = auf.numero_documento
                INNER JOIN facultades f ON auf.id_facultad = f.id_facultad
                WHERE u.numero_documento = '$numero_documento'");

        if ($check_profesor_facultad->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento de ". $nombre_usuario_registrado ." no tiene asignada una facultad y un programa ",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        
        $profesor_facultad = $check_profesor_facultad->fetch(PDO::FETCH_ASSOC);

        $id_facultad_profesor = $profesor_facultad['id_facultad'];

        $id_programa_profesor = $profesor_facultad['id_programa'];


        /**************validar informacion logueado  ********************* */
    
        $numero_documento_user_logueado =  MainModel::decryption($numero_documento_user_logueado);

        $check_rol_usuario_logueado = MainModel::ejecutar_consultas_simples(
            "SELECT id_rol FROM usuarios WHERE numero_documento = '$numero_documento_user_logueado'"
        );
        if ($check_profesor->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento del usuario no existe en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $profesor_rol = $check_rol_usuario_logueado->fetch(PDO::FETCH_ASSOC);

        $rol_usuario_logueado = $profesor_rol['id_rol'];

        if($rol_usuario_logueado == 1){

            $check_usuario = MainModel::ejecutar_consultas_simples(
                "SELECT numero_documento FROM asignar_horas_jurado_profesor WHERE numero_documento = '$numero_documento'"
            );
    
            if ($check_usuario->rowCount() > 0) {
                // Si se encontró un registro, el usuario ya está registrado
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El profesor ". $nombre_usuario_registrado ." ya tiene horas registradas",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            // Si el usuario no está registrado, insertarlo en la base de datos
        $datos_asignar = [
            "numero_documento" => $numero_documento,
            "numero_horas" => $numero_horas
        ];

        $guardar_asignar = AnteproyectoModelo::asignar_horas_jurados_profesor_modelos($datos_asignar);

        if ($guardar_asignar->rowCount() > 0) {

            
                /******************************************************** */

                $message = "<p>📢 ¡Nueva asignación! Se te han programado <b>horas como jurado</b>, una responsabilidad clave en la evaluación de los proyectos académicos. 🎓⚖️</p>

                <p>Tu criterio y experiencia serán fundamentales para valorar el esfuerzo, la investigación y la innovación de los estudiantes. Cada decisión que tomes contribuirá al crecimiento profesional de quienes están dando sus últimos pasos en su formación. 📖💡</p>
                
                <p><b>Tu labor es crucial.</b> Con tu análisis y retroalimentación, estarás ayudando a garantizar la calidad y excelencia académica. ¡Gracias por ser parte de este proceso! 🚀🔥</p>";
                



                    include __DIR__ . '/../Mail/enviar-correo.php';

                    $asunto = "Notificación de Asignación de Horas de Jurado";

                        $cuerpo_html = '
                
                        <!DOCTYPE html>
                        <html lang="es">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Bienvenido a nuestra plataforma</title>'
                            .STYLESCORREO.'
                        </head>
                        <body>
                            <div class="email-container">
                                <div class="email-header">
                                    <img src="'.SERVERURL.'Views/assets/images/'.$logo.'" alt="Logo Universidad">
                                       <h2>Asignación de Horas de Jurado</h2>
                                </div>
                                <div class="email-body">
                                    <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                                    '.$message.'
                                    <p>Confiamos en que tu experiencia y criterio serán de gran valor en la evaluación de los proyectos y anteproyectos presentados por los estudiantes.</p>
                                    <div class="credentials">
                                        <ul>
                                            <li><b>Numero de horas asignadas</b> ' . $numero_horas . '</li>
                                            
                                        </ul>
                                    </div>

                                    <p><b>Atentamente,</b><br>
                                    <i>Corporación Universitaria Autónoma de Nariño</i></p>
                                </div>
                                <div class="email-footer">
                                    Este mensaje se envió desde una dirección de correo electrónico no supervisada. No responda a este mensaje.
                                </div>
                            </div>
                        </body>
                        </html>
                        ';
                        $cuerpo_texto = "Estimado $nombre_usuario $apellido_usuario, nos complace informarte que se te han asignado $numero_horas horas para desempeñar tu función como jurado en el período académico actual. Confiamos en que harás un excelente trabajo.";

                $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);

                if ($enviado) {
                    $alerta = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Operación exitosa",
                        "Texto" => "Se le asignaron las horas de jurado al profesor ". $nombre_usuario_registrado ." ha sido registrado con éxito",
                        "Tipo" => "success"
                    ];
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado",
                        "Texto" => "No se pudo enviar el correo electrónico al profesor.". $nombre_usuario_registrado,
                        "Tipo" => "error"
                    ];
                }
    
                 
                echo json_encode($alerta);
                exit();

        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo registrar las horas de jurado al profesor.". $nombre_usuario_registrado ,
                "Tipo" => "error"
            ];
        }
        
        echo json_encode($alerta);
        exit();


        }elseif($rol_usuario_logueado == 2){

            $check_cordinador_logueado = MainModel::ejecutar_consultas_simples(
                "SELECT 
                        u.id,
                        u.numero_documento,
                        u.nombre_usuario,
                        u.apellidos_usuario,
                        u.correo_usuario,
                        u.telefono_usuario,
                        u.id_rol,
                        auf.id_facultad,
                        auf.id_programa,
                        f.nombre_facultad
                    FROM usuarios u
                    INNER JOIN Asignar_usuario_facultades auf ON u.numero_documento = auf.numero_documento
                    INNER JOIN facultades f ON auf.id_facultad = f.id_facultad
                    WHERE u.numero_documento = '$numero_documento_user_logueado'"
            );
            if ($check_cordinador_logueado->rowCount() <= 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El número de documento del coordiador o administrador no existe en el sistema.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
    
            // Crear arreglos para almacenar las facultades y programas del coordinador logueado
                $facultades_coordinador = [];
                $programas_coordinador = [];

                // Recorrer todas las facultades y programas del coordinador logueado
                while ($row = $check_cordinador_logueado->fetch(PDO::FETCH_ASSOC)) {
                    $facultades_coordinador[] = $row['id_facultad'];  // Almacenar id de facultades
                    $programas_coordinador[] = $row['id_programa'];   // Almacenar id de programas
                }

                // Comparar las facultades y programas del coordinador logueado con el usuario
                if (in_array($id_facultad_profesor, $facultades_coordinador)) {
                        // Si el usuario no está registrado, insertarlo en la base de datos
                        $check_usuario = MainModel::ejecutar_consultas_simples(
                            "SELECT numero_documento FROM asignar_horas_jurado_profesor WHERE numero_documento = '$numero_documento'"
                        );
                
                        if ($check_usuario->rowCount() > 0) {
                            // Si se encontró un registro, el usuario ya está registrado
                            $alerta = [
                                "Alerta" => "simple",
                                "Titulo" => "Ocurrió un error inesperado",
                                "Texto" => "El profesor ". $nombre_usuario_registrado ." ya tiene horas registradas",
                                "Tipo" => "error"
                            ];
                            echo json_encode($alerta);
                            exit();
                        }

                    $datos_asignar = [
                        "numero_documento" => $numero_documento,
                        "numero_horas" => $numero_horas
                    ];

                    $guardar_asignar = AnteproyectoModelo::asignar_horas_jurados_profesor_modelos($datos_asignar);

                    if ($guardar_asignar->rowCount() > 0) {
                
                        include __DIR__ . '/../Mail/enviar-correo.php';

                    $asunto = "Notificación de Asignación de Horas de Jurado";

                        $cuerpo_html = '
                
                        <!DOCTYPE html>
                        <html lang="es">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Bienvenido a nuestra plataforma</title>'
                            .STYLESCORREO.'
                        </head>
                        <body>
                            <div class="email-container">
                                <div class="email-header">
                                    <img src="'.SERVERURL.'Views/assets/images/'.$logo.'" alt="Logo Universidad">
                                       <h2>Asignación de Horas de Jurado</h2>
                                </div>
                                <div class="email-body">
                                    <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                                    '.$message.'
                                    <p>Confiamos en que tu experiencia y criterio serán de gran valor en la evaluación de los proyectos y anteproyectos presentados por los estudiantes.</p>
                                    <div class="credentials">
                                        <ul>
                                            <li><b>Numero de horas asignadas</b> ' . $numero_horas . '</li>
                                            
                                        </ul>
                                    </div>

                                    <p><b>Atentamente,</b><br>
                                    <i>Corporación Universitaria Autónoma de Nariño</i></p>
                                </div>
                                <div class="email-footer">
                                    Este mensaje se envió desde una dirección de correo electrónico no supervisada. No responda a este mensaje.
                                </div>
                            </div>
                        </body>
                        </html>
                        ';
                        $cuerpo_texto = "Estimado $nombre_usuario $apellido_usuario, nos complace informarte que se te han asignado $numero_horas horas para desempeñar tu función como jurado en el período académico actual. Confiamos en que harás un excelente trabajo.";

                        $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);

                        if ($enviado) {
                            $alerta = [
                                "Alerta" => "Recargar",
                                "Titulo" => "Operación exitosa",
                                "Texto" => "Se le asignaron las horas de jurado al profesor ". $nombre_usuario_registrado ." ha sido registrado con éxito",
                                "Tipo" => "success"
                            ];
                        } else {
                            $alerta = [
                                "Alerta" => "simple",
                                "Titulo" => "Ocurrió un error inesperado",
                                "Texto" => "No se pudo enviar el correo electrónico al profesor.". $nombre_usuario_registrado,
                                "Tipo" => "error"
                            ];
                        }
            
                        
                        echo json_encode($alerta);
                        exit();
                
                    } else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "No se pudo registrar las horas de jurado al profesor.". $nombre_usuario_registrado ,
                            "Tipo" => "error"
                        ];
                    }
                    
                    echo json_encode($alerta);
                    exit();

                    
                }else{
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado",
                        "Texto" => "El número de documento de ". $nombre_usuario_registrado ."  no pertenece a ninguna de las facultades y programas del coordinador logueado",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
                            

        }



    }

    public function cargarDocuemntosAnteproyectos() {

        
        $extraer_datos_configuracion = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM configuracion_aplicacion"
        );
        
        if ($extraer_datos_configuracion->rowCount() > 0) { 
            $configuracion = $extraer_datos_configuracion->fetch(PDO::FETCH_ASSOC);
            $logo = $configuracion['nombre_logo'];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo extraer el nombre del logo",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $codigo = MainModel::limpiar_cadenas($_POST['codigo_anteproyecto_subir']);
        $identificador_carga_documento = MainModel::limpiar_cadenas($_POST['identificador_carga_documento']);
        $numero_documento_user_logueado = MainModel::limpiar_cadenas($_POST['numero_documento_user_logueado']);
        $nombre_archivo = $_FILES['archivo_user_anteproyecto']['name'];
        $tipo_archivo = $_FILES['archivo_user_anteproyecto']['type'];
        $estado = 1;

        if (empty($codigo) || empty($numero_documento_user_logueado)  || empty($nombre_archivo) || empty($identificador_carga_documento) ) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $identificador_carga_documento = (int) MainModel::decryption($identificador_carga_documento);

        // Verificar si se ha subido un archivo
        if (!isset($_FILES['archivo_user_anteproyecto']) || empty($_FILES['archivo_user_anteproyecto']['name'][0])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "No se ha subido ningún documento",
                "Texto" => "Por favor, selecciona un documento antes de enviar.",
                "Tipo" => "warning"
            ];
            echo json_encode($alerta);
            exit();
        }

      
        // Verifica si se cargaron más de dos archivos
        if (count($_FILES['archivo_user_anteproyecto']['name']) > 2) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Cantidad de archivos excedida",
                "Texto" => "Solo puedes subir un máximo de dos archivos.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        foreach ($_FILES['archivo_user_anteproyecto']['name'] as $index => $nombre_archivo) {
            $tipo_archivo = $_FILES['archivo_user_anteproyecto']['type'][$index];

            // Validar si el archivo es PDF o Word
            if ($tipo_archivo != "application/pdf" && $tipo_archivo != "application/msword" && $tipo_archivo != "application/vnd.openxmlformats-officedocument.wordprocessingml.document") {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Formato de archivo no válido",
                    "Texto" => "Los archivo debe estar en formato PDF o Word (.doc o .docx).",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }

        $codigo =  MainModel::decryption($codigo);
        $numero_documento_user_logueado =  MainModel::decryption($numero_documento_user_logueado);

        $check_usuario = MainModel::ejecutar_consultas_simples(
            "SELECT nombre_usuario, apellidos_usuario, id_rol,numero_documento FROM usuarios WHERE numero_documento = '$numero_documento_user_logueado'"
        );
        if ($check_usuario->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento del usuario no existe en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_codigo = MainModel::ejecutar_consultas_simples(
            "SELECT codigo_anteproyecto FROM anteproyectos WHERE codigo_anteproyecto = '$codigo'"
        );

        if ($check_codigo->rowCount() == 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El código del proyecto ingresado no existe",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        /*******************************validamos el identificador carga documento ******************************************** */

        if($identificador_carga_documento == 1){ // si no se ha actualizado el estado

          

            $extraer_datos_documentos_cargador = MainModel::ejecutar_consultas_simples(
                "SELECT * 
                    FROM cargar_documento_anteproyectos 
                    WHERE codigo_anteproyecto = '$codigo'
                    ORDER BY id DESC 
                    LIMIT 1"
            );
            
            // Verificar si hay resultados
            if ($extraer_datos_documentos_cargador->rowCount() > 0) {
                $datos_documento = $extraer_datos_documentos_cargador->fetch(PDO::FETCH_ASSOC);

                $ruta_carpeta = '../Views/document/anteproyectos/'.$codigo.'/';


                // Extraer los datos individuales
                $nombre_archivo_pdf = $datos_documento['documento'];
                $nombre_archivo_word = $datos_documento['nombre_archivo_word'];

                // Construcción de las rutas completas de los archivos
                $ruta_archivo_word = $ruta_carpeta . $nombre_archivo_word;
                $ruta_archivo_pdf = $ruta_carpeta . $nombre_archivo_pdf;

                $ruta_archivo_word = rtrim($ruta_carpeta, '/') . '/' . $nombre_archivo_word;

                $ruta_archivo_pdf = rtrim($ruta_carpeta, '/') . '/' . $nombre_archivo_pdf;

             
                // Verificar y eliminar los archivos
                if (file_exists($ruta_archivo_word)) {
                    if (unlink($ruta_archivo_word)) {
                       
                    } else {

                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrio un error insperado",
                            "Texto" => "Error al eliminar el archivo Word.",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();

                    }
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrio un error insperado",
                        "Texto" => "El archivo Word no existe.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();

                }

                if (file_exists($ruta_archivo_pdf)) {
                    if (unlink($ruta_archivo_pdf)) {
                       
                    } else {

                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrio un error insperado",
                            "Texto" => "Error al eliminar el archivo PDF.",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();

            
                    }
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrio un error insperado",
                        "Texto" => "El archivo Pdf no existe.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                $nombre_carpeta = '../Views/document/anteproyectos/'. $codigo;

                // Verificar si la carpeta existe, si no, crearla
                if (!file_exists($nombre_carpeta)) {
                    mkdir($nombre_carpeta, 0755, true);
                }

                // Obtener los archivos subidos
                $archivos = $_FILES['archivo_user_anteproyecto'];
                $nombre_base = $numero_documento_user_logueado . '_' . time();
                $nombre_pdf = $nombre_base . '.pdf';
                $nombre_word = $nombre_base . '.docx';

                // Ruta completa donde se guardarán los archivos
                $ruta_destino_pdf = $nombre_carpeta . '/' . $nombre_pdf;
                $ruta_destino_word = $nombre_carpeta . '/' . $nombre_word;

                // Variable para controlar si se actualizó la base de datos
                $actualizado_en_bd = false;

                // Iterar a través de los archivos subidos
                foreach ($archivos['tmp_name'] as $index => $tmpName) {
                    $extension = pathinfo($archivos['name'][$index], PATHINFO_EXTENSION);

                    if ($extension === 'pdf') {
                        // Mover el archivo PDF a la carpeta destino
                        if (move_uploaded_file($tmpName, $ruta_destino_pdf)) {
                            // Actualizar la base de datos solo una vez
                            if (!$actualizado_en_bd) {
                                $actualizar_documento = MainModel::ejecutar_consultas_simples(
                                    "UPDATE cargar_documento_anteproyectos 
                                        SET documento = '$nombre_pdf', 
                                            nombre_archivo_word = '$nombre_word' 
                                        WHERE codigo_anteproyecto = '$codigo'
                                        ORDER BY id DESC 
                                        LIMIT 1"
                                );

                                if ($actualizar_documento) {
                                    $actualizado_en_bd = true;
                                } else {
                                    // Error al actualizar la base de datos
                                    $alerta = [
                                        "Alerta" => "simple",
                                        "Titulo" => "Error al actualizar la base de datos",
                                        "Texto" => "Los documentos fueron subidos correctamente, pero no se pudo actualizar la información en la base de datos.",
                                        "Tipo" => "error"
                                    ];
                                    echo json_encode($alerta);
                                    exit();
                                }
                            }
                        } else {
                            // Error al mover el archivo PDF
                            $alerta = [
                                "Alerta" => "simple",
                                "Titulo" => "Error al subir el documento",
                                "Texto" => "No se pudo subir el archivo PDF. Verifica la conexión e intenta nuevamente.",
                                "Tipo" => "error"
                            ];
                            echo json_encode($alerta);
                            exit();
                        }
                    } elseif ($extension === 'docx' || $extension === 'doc') {
                        // Mover el archivo Word
                        move_uploaded_file($tmpName, $ruta_destino_word);
                    }
                }

                // Mostrar mensaje de éxito si se actualizaron los documentos en la base de datos y fueron movidos correctamente
                if ($actualizado_en_bd) {
                    $alerta = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Documentos actualizados con éxito",
                        "Texto" => "Los documentos se han actualizado correctamente en la base de datos y han sido almacenados en la carpeta correspondiente.",
                        "Tipo" => "success"
                    ];
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Error en la actualización",
                        "Texto" => "Hubo un problema con la actualización en la base de datos, pero los archivos fueron subidos correctamente.",
                        "Tipo" => "warning"
                    ];
                }

                echo json_encode($alerta);
                exit();


            
            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error ",
                    "Texto" => "No se encontraron documento en la base de datos",
                    "Tipo" => "warning"
                ];
                echo json_encode($alerta);
                exit();
            }
            
            exit();
        }


        $fecha_actual = date('Y-m-d');  // Obtener la fecha actual en formato Y-m-d

        $segunda_fecha_actual = date('Y-m-d H:i:s'); // Obtener la fecha y hora actual en formato completo

        $extraer_ultimafecha_entregavances = MainModel::ejecutar_consultas_simples(
            "SELECT fecha_entrega_avances 
            FROM retroalimentacion_anteproyecto 
            WHERE codigo_anteproyecto = '$codigo'
            ORDER BY fecha_entrega_avances DESC 
            LIMIT 1"
        );
        
        if ($extraer_ultimafecha_entregavances->rowCount() > 0) {
            $datos = $extraer_ultimafecha_entregavances->fetch(PDO::FETCH_ASSOC);
            $fecha_entrega_avances = date('Y-m-d H:i:s', strtotime($datos['fecha_entrega_avances']));
        
            if ($segunda_fecha_actual > $fecha_entrega_avances) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "⏳ Plazo de entrega vencido",
                    "Texto" => "Lo sentimos, el tiempo para la entrega del documento ha finalizado. 
                                Ya no es posible cargar nuevos archivos en este momento. 
                                Para más información, por favor comunícate con tu asesor académico.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }

        $check_documento = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM cargar_documento_anteproyectos 
            WHERE numero_documento = '$numero_documento_user_logueado' 
            AND DATE(fecha_creacion) = '$fecha_actual'"
        );

        if ($check_documento->rowCount() > 0) {
            // Si el usuario ya ha enviado un documento hoy
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Documento ya enviado",
                "Texto" => "Este usuario ya ha cargado un documento para el anteproyecto el día de hoy.",
                "Tipo" => "warning"
            ];
            echo json_encode($alerta);
            exit();
        } 

        

        $check_documento_otros_usuarios = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM cargar_documento_anteproyectos 
             WHERE codigo_anteproyecto = '$codigo' 
             AND numero_documento != '$numero_documento_user_logueado' AND DATE(fecha_creacion) = '$fecha_actual'"
        );
        
        if ($check_documento_otros_usuarios->rowCount() > 0) {
            // Si otro usuario ya subió el documento
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Documento ya subido",
                "Texto" => "Tu compañero ya cargo el documento el dia de hoy",
                "Tipo" => "warning"
            ];
            echo json_encode($alerta);
            exit();
        } 

        /************* creamos el directorio donde quedara guaradada el documento******** */

        $nombre_carpeta = '../Views/document/anteproyectos/'.$codigo;

        // Verifica si el directorio ya existe
        if (!file_exists($nombre_carpeta)) {
            // Intenta crear el directorio con permisos de escritura
            if (mkdir($nombre_carpeta, 0755, true)) {
                
                $archivos = $_FILES['archivo_user_anteproyecto'];
                $nombre_base = $numero_documento_user_logueado . '_' . time();
                $nombre_pdf = $nombre_base . '.pdf';
                $nombre_word = $nombre_base . '.docx'; // Sin extensión para Word
    
                // Ruta completa donde se guardará el archivo
                $ruta_destino_pdf = $nombre_carpeta . '/' . $nombre_pdf;
                $ruta_destino_word = $nombre_carpeta . '/' . $nombre_word; // Opcionalmente convierte el archivo a .docx
    
                // Variable para verificar si se guardó en la base de datos
                $guardado_en_bd = false;
    
                // Iterar a través de archivos subidos
                foreach ($archivos['tmp_name'] as $index => $tmpName) {
                    $extension = pathinfo($archivos['name'][$index], PATHINFO_EXTENSION);
    
                    if ($extension === 'pdf' && move_uploaded_file($tmpName, $ruta_destino_pdf)) {
                        // Guardar el PDF en la base de datos solo una vez
                        if (!$guardado_en_bd) {
                            $datos = [
                                'numero_documento' => $numero_documento_user_logueado,
                                'codigo_anteproyecto' => $codigo,
                                'nombre_archivo' => $nombre_pdf,
                                'nombre_archivo_word' => $nombre_word,
                                'estado' => $estado,
                                'fecha_creacion' => date('Y-m-d H:i:s')
                            ];
                            $guardar_documento = AnteproyectoModelo::cargar_documento_anteproyecto_modelo($datos);
    
                            if ($guardar_documento) {
                                $guardado_en_bd = true;
                            } else {
                                // Error al guardar en la base de datos
                                $alerta = [
                                    "Alerta" => "simple",
                                    "Titulo" => "Error al guardar en la base de datos",
                                    "Texto" => "No se pudo guardar el documento PDF en la base de datos.",
                                    "Tipo" => "error"
                                ];
                                echo json_encode($alerta);
                                exit();
                            }
                        }
                    } elseif ($extension === 'docx' || $extension === 'doc') {
                        // Mover el archivo Word solo
                        move_uploaded_file($tmpName, $ruta_destino_word);
                    }
                }
    
                // Notificar éxito si se guardaron archivos y datos en la base de datos
                if ($guardado_en_bd) {

                    $consulta_profesor_asignado = MainModel::ejecutar_consultas_simples(
                        "SELECT 
                            a.codigo_proyecto,
                            e.titulo_anteproyecto,
                            u.numero_documento,
                            u.nombre_usuario,
                            u.apellidos_usuario,
                            u.correo_usuario,
                            u.telefono_usuario
                        FROM Asignar_asesor_anteproyecto_proyecto a
                        INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                        INNER JOIN anteproyectos e ON e.codigo_anteproyecto = a.codigo_proyecto
                        WHERE a.codigo_proyecto = '$codigo'"
                    );
                    $correosEnviados = true;

                    include_once __DIR__ . '/../Mail/enviar-correo.php';

                    while ($row = $consulta_profesor_asignado->fetch(PDO::FETCH_ASSOC)) {

                        $nombre_profesor = $row['nombre_usuario'];
                        $apellido_profesor = $row['apellidos_usuario'];
                        $nombre_usuario_profesor =  $nombre_profesor.'  '.$apellido_profesor;
                        $correo_usuario_profesor = $row['correo_usuario'];
                        $titulo_anteproyecto = $row['titulo_anteproyecto'];

                        $asunto = "Notificación de Documento Subido para Revisión y Retroalimentación";

                        $message = "<p>📢 ¡Nuevo avance subido! Como asesor, tu conocimiento y guía son fundamentales para el desarrollo exitoso de cada proyecto. 📝✨</p>

                        <p>Se ha cargado un nuevo avance de un proyecto de grado en la plataforma. Te invitamos a revisarlo y proporcionar las recomendaciones necesarias para su mejora.</p>
                    
                        <p>Tu retroalimentación es clave para garantizar que cada estudiante refine su trabajo y alcance los estándares académicos esperados. <b>Gracias a tu dedicación, los proyectos pueden avanzar con calidad y precisión.</b> 🚀📚</p>";


                        $cuerpo_html = '
                
                        <!DOCTYPE html>
                        <html lang="es">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Bienvenido a nuestra plataforma</title>'
                            .STYLESCORREO.'
                        </head>
                        <body>
                            <div class="email-container">
                                <div class="email-header">
                                    <img src="'.SERVERURL.'Views/assets/images/'.$logo.'" alt="Logo Universidad">
                                    <h2>¡Notificación de registro en la plataforma!</h2>
                                </div>
                                <div class="email-body">
                                    <p><b>Estimado  ' . $nombre_usuario_profesor . ',</b></p>
                                    '.$message.'
                                     <p>Te informamos que se ha subido un nuevo documento para revisión y retroalimentación correspondiente al siguiente anteproyecto:</p>
                                    <div class="credentials">
                                        <ul>
                                            <li><b>Código del Anteproyecto:</b> ' . $codigo . '</li>
                                            <li><b>Título del Anteproyecto::</b> ' . $titulo_anteproyecto . '</li>
                                        </ul>
                                    </div>
                                    <p>Te solicitamos que, por favor, revises el documento y proporciones la retroalimentación correspondiente a los estudiantes asignados.</p>
    
                                    <p><b>Atentamente,</b><br>
                                    <i>Corporación Universitaria Autónoma de Nariño</i></p>
                                </div>
                                <div class="email-footer">
                                    Este mensaje se envió desde una dirección de correo electrónico no supervisada. No responda a este mensaje.
                                </div>
                            </div>
                        </body>
                        </html>
                        ';

                        $cuerpo_texto = "Estimado Asesor $nombre_usuario_profesor, se ha subido un nuevo documento para revisión y retroalimentación del anteproyecto con el código $codigo y el título $titulo_anteproyecto. Te solicitamos que revises el documento y proporciones la retroalimentación correspondiente.";

                        $enviado = enviarCorreo($correo_usuario_profesor, $nombre_profesor, $apellido_profesor, $asunto, $cuerpo_html, $cuerpo_texto);

                        
                        if (!$enviado) {
                            $correosEnviados = false; // Si algún correo no se envía, marcar como falso
                        }
                    }
                    // Mostrar mensaje de confirmación si todos los correos se enviaron correctamente
                    if ($correosEnviados) {
                        $mensaje = [
                            "Alerta" => "Recargar",
                            "Titulo" => "Correos Enviados",
                            "Texto" => "Se ha notificado al asesor y se han subido correctamente los documentos",
                            "Tipo" => "success"
                        ];
                    } else {
                        $mensaje = [
                            "Alerta" => "simple",
                            "Titulo" => "Error en el Envío",
                            "Texto" => "Hubo un problema al enviar el correos. pero el documento subio correctamente",
                            "Tipo" => "error"
                        ];
                    }
                    
                    echo json_encode($mensaje);

                    exit();

                    
                } else {
                    // Notificar si no hubo archivos PDF válidos para subir o no se guardaron en la BD
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Sin documentos PDF válidos",
                        "Texto" => "No se pudo cargar un archivo PDF válido.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }


            } else {
                echo "Error al crear la carpeta.";
            }
        } else {
           
            $archivos = $_FILES['archivo_user_anteproyecto'];
            $nombre_base = $numero_documento_user_logueado . '_' . time();
            $nombre_pdf = $nombre_base . '.pdf';
            $nombre_word = $nombre_base . '.docx'; // Sin extensión para Word

            // Ruta completa donde se guardará el archivo
            $ruta_destino_pdf = $nombre_carpeta . '/' . $nombre_pdf;
            $ruta_destino_word = $nombre_carpeta . '/' . $nombre_word; // Opcionalmente convierte el archivo a .docx

            // Variable para verificar si se guardó en la base de datos
            $guardado_en_bd = false;

            // Iterar a través de archivos subidos
            foreach ($archivos['tmp_name'] as $index => $tmpName) {
                $extension = pathinfo($archivos['name'][$index], PATHINFO_EXTENSION);

                if ($extension === 'pdf' && move_uploaded_file($tmpName, $ruta_destino_pdf)) {
                    // Guardar el PDF en la base de datos solo una vez
                    if (!$guardado_en_bd) {
                        $datos = [
                            'numero_documento' => $numero_documento_user_logueado,
                            'codigo_anteproyecto' => $codigo,
                            'nombre_archivo' => $nombre_pdf,
                            'nombre_archivo_word' => $nombre_word,
                            'estado' => $estado,
                            'fecha_creacion' => date('Y-m-d H:i:s')
                        ];
                        $guardar_documento = AnteproyectoModelo::cargar_documento_anteproyecto_modelo($datos);

                        if ($guardar_documento) {
                            $guardado_en_bd = true;
                        } else {
                            // Error al guardar en la base de datos
                            $alerta = [
                                "Alerta" => "simple",
                                "Titulo" => "Error al guardar en la base de datos",
                                "Texto" => "No se pudo guardar el documento PDF en la base de datos.",
                                "Tipo" => "error"
                            ];
                            echo json_encode($alerta);
                            exit();
                        }
                    }
                } elseif ($extension === 'docx' || $extension === 'doc') {
                    // Mover el archivo Word solo
                    move_uploaded_file($tmpName, $ruta_destino_word);
                }
            }

            // Notificar éxito si se guardaron archivos y datos en la base de datos
            if ($guardado_en_bd) {
                $consulta_profesor_asignado = MainModel::ejecutar_consultas_simples(
                    "SELECT 
                        a.codigo_proyecto,
                        e.titulo_anteproyecto,
                        u.numero_documento,
                        u.nombre_usuario,
                        u.apellidos_usuario,
                        u.correo_usuario,
                        u.telefono_usuario
                    FROM Asignar_asesor_anteproyecto_proyecto a
                    INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                    INNER JOIN anteproyectos e ON e.codigo_anteproyecto = a.codigo_proyecto
                    WHERE a.codigo_proyecto = '$codigo'"
                );
                $correosEnviados = true;

                include_once __DIR__ . '/../Mail/enviar-correo.php';

                while ($row = $consulta_profesor_asignado->fetch(PDO::FETCH_ASSOC)) {

                    $nombre_profesor = $row['nombre_usuario'];
                    $apellido_profesor = $row['apellidos_usuario'];
                    $nombre_usuario_profesor =  $nombre_profesor.'  '.$apellido_profesor;
                    $correo_usuario_profesor = $row['correo_usuario'];
                    $titulo_anteproyecto = $row['titulo_anteproyecto'];

                    $asunto = "Notificación de Documento Subido para Revisión y Retroalimentación";

                    $asunto = "Notificación de Documento Subido para Revisión y Retroalimentación";

                        $message = "<p>📢 ¡Nuevo avance subido! Como asesor, tu conocimiento y guía son fundamentales para el desarrollo exitoso de cada proyecto. 📝✨</p>

                        <p>Se ha cargado un nuevo avance de un proyecto de grado en la plataforma. Te invitamos a revisarlo y proporcionar las recomendaciones necesarias para su mejora.</p>
                    
                        <p>Tu retroalimentación es clave para garantizar que cada estudiante refine su trabajo y alcance los estándares académicos esperados. <b>Gracias a tu dedicación, los proyectos pueden avanzar con calidad y precisión.</b> 🚀📚</p>";


                        $cuerpo_html = '
                
                        <!DOCTYPE html>
                        <html lang="es">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Bienvenido a nuestra plataforma</title>'
                            .STYLESCORREO.'
                        </head>
                        <body>
                            <div class="email-container">
                                <div class="email-header">
                                    <img src="'.SERVERURL.'Views/assets/images/'.$logo.'" alt="Logo Universidad">
                                    <h2>¡Notificación de registro en la plataforma!</h2>
                                </div>
                                <div class="email-body">
                                    <p><b>Estimado  ' . $nombre_usuario_profesor . ',</b></p>
                                    '.$message.'
                                     <p>Te informamos que se ha subido un nuevo documento para revisión y retroalimentación correspondiente al siguiente anteproyecto:</p>
                                    <div class="credentials">
                                        <ul>
                                            <li><b>Código del Anteproyecto:</b> ' . $codigo . '</li>
                                            <li><b>Título del Anteproyecto::</b> ' . $titulo_anteproyecto . '</li>
                                        </ul>
                                    </div>
                                    <p>Te solicitamos que, por favor, revises el documento y proporciones la retroalimentación correspondiente a los estudiantes asignados.</p>
    
                                    <p><b>Atentamente,</b><br>
                                    <i>Corporación Universitaria Autónoma de Nariño</i></p>
                                </div>
                                <div class="email-footer">
                                    Este mensaje se envió desde una dirección de correo electrónico no supervisada. No responda a este mensaje.
                                </div>
                            </div>
                        </body>
                        </html>
                        ';

                    $cuerpo_texto = "Estimado Asesor $nombre_usuario_profesor, se ha subido un nuevo documento para revisión y retroalimentación del anteproyecto con el código $codigo y el título $titulo_anteproyecto. Te solicitamos que revises el documento y proporciones la retroalimentación correspondiente.";

                    $enviado = enviarCorreo($correo_usuario_profesor, $nombre_profesor, $apellido_profesor, $asunto, $cuerpo_html, $cuerpo_texto);

                    
                    if (!$enviado) {
                        $correosEnviados = false; // Si algún correo no se envía, marcar como falso
                    }
                }
                // Mostrar mensaje de confirmación si todos los correos se enviaron correctamente
                if ($correosEnviados) {
                    $mensaje = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Correos Enviados",
                        "Texto" => "Se ha notificado al asesor y se han subido correctamente los documentos",
                        "Tipo" => "success"
                    ];
                } else {
                    $mensaje = [
                        "Alerta" => "simple",
                        "Titulo" => "Error en el Envío",
                        "Texto" => "Hubo un problema al enviar el correos. pero el documento subio correctamente",
                        "Tipo" => "error"
                    ];
                }
                
                echo json_encode($mensaje);

                exit();
            } else {
                // Notificar si no hubo archivos PDF válidos para subir o no se guardaron en la BD
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Sin documentos PDF válidos",
                    "Texto" => "No se pudo cargar un archivo PDF válido.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            
        }

     


        
       
    }

    public function retroalimentacion_anteproyectos(){

        $extraer_datos_configuracion = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM configuracion_aplicacion"
        );
        
        if ($extraer_datos_configuracion->rowCount() > 0) { 
            $configuracion = $extraer_datos_configuracion->fetch(PDO::FETCH_ASSOC);
            $logo = $configuracion['nombre_logo'];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo extraer el nombre del logo",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        

        $estado_revision = 2;
        $observaciones_generales = MainModel::limpiar_cadenas($_POST['observacion_general_retroalimentacion']);
        $estado = MainModel::limpiar_cadenas($_POST['estado_retroalimentacion']);
        // los datos que vienen encriptados
        $numero_documento_user_logueado = MainModel::limpiar_cadenas($_POST['numero_documento_user_logueado']);
        $codigo_anteproyecto = MainModel::limpiar_cadenas($_POST['codigo_anteproyecto']);
        $id_documento_cargado = MainModel::limpiar_cadenas($_POST['id_documento_cargado']);

        $nombre_archivo = $_FILES['archivo_user_anteproyecto']['name'];
        $tipo_archivo = $_FILES['archivo_user_anteproyecto']['type'];

        $fecha_revision = MainModel::limpiar_cadenas($_POST['fecha_revision']);

        

        if (empty($numero_documento_user_logueado) || empty($estado) || empty($observaciones_generales) 
            || empty($codigo_anteproyecto) || empty($id_documento_cargado)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $codigo_anteproyecto =  MainModel::decryption($codigo_anteproyecto);
        $id_documento_cargado =  MainModel::decryption($id_documento_cargado);
        $numero_documento_user_logueado =  MainModel::decryption($numero_documento_user_logueado);
        $estado =  MainModel::decryption($estado);

        
        if (!in_array($estado, [1, 2, 3])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Estado no válido",
                "Texto" => "Por favor seleccione un estado e intenta nuevamente.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_usuario = MainModel::ejecutar_consultas_simples(
            "SELECT nombre_usuario, apellidos_usuario, id_rol,numero_documento FROM usuarios WHERE numero_documento = '$numero_documento_user_logueado'"
        );
        if ($check_usuario->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento del usuario no existe en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_codigo = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM anteproyectos WHERE codigo_anteproyecto = '$codigo_anteproyecto'"
        );

        if ($check_codigo->rowCount() == 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El código del anteproyecto ingresado no existe",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        
        $check_id_documento = MainModel::ejecutar_consultas_simples(
            "SELECT id FROM cargar_documento_anteproyectos WHERE id = '$id_documento_cargado'"
        );

        if ($check_id_documento->rowCount() == 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Id del documento no existe",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_id_retroalimententacion = MainModel::ejecutar_consultas_simples(
            "SELECT id FROM retroalimentacion_anteproyecto WHERE id = '$id_documento_cargado'"
        );
        
        if ($check_id_retroalimententacion->rowCount() > 0) {
            // Si ya existe una retroalimentación para el documento
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El documento ya tiene una retroalimentación registrada",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if(empty($nombre_archivo)){

            $nombre_nuevo_archivo = "None";

            $datos = [
                'numero_documento' => $numero_documento_user_logueado,
                'codigo_anteproyecto' => $codigo_anteproyecto,
                'id_documento_cargado' => $id_documento_cargado,
                'observacion_general' => $observaciones_generales,
                'estado' => $estado,
                'nombre_archivo_word' => $nombre_nuevo_archivo,
                'estado_revision' => $estado_revision,
                'fecha_entrega' => !empty($fecha_revision) ? $fecha_revision : date('Y-m-d')

            ];

            /****** validamos el estado del proyecto **********************/

            if($estado == 2){ // si es aprobado el anteproyecto

                $guardar_retroalimentacion = AnteproyectoModelo::guardar_retroalimentacion_anteproyecto_modelo($datos);

                if ($guardar_retroalimentacion) {

                    include __DIR__ . '/../Mail/enviar-correo.php';

                    $correosEnviados = true; // Variable para comprobar si todos los correos se enviaron con éxito
                
                    // 1. Verificar si el código del anteproyecto existe
                    $check_codigo = MainModel::ejecutar_consultas_simples(
                        "SELECT * FROM anteproyectos WHERE codigo_anteproyecto = '$codigo_anteproyecto'"
                    );

                    if ($check_codigo->rowCount() == 0) {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "El código del anteproyecto ingresado no existe",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }


                    // Extraer datos del anteproyecto
                    $datos_anteproyecto = $check_codigo->fetch(PDO::FETCH_ASSOC);

                    $titulo_anteproyecto = $datos_anteproyecto['titulo_anteproyecto'];
                    $palabras_claves_anteproyecto = $datos_anteproyecto['palabras_claves'];
                    $facultad_anteproyecto_registrada = $datos_anteproyecto['id_facultad'];
                    $programa_anteproyecto_registrada = $datos_anteproyecto['id_programa'];
                    $modalidad_anteproyecto_registrada = $datos_anteproyecto['modalidad'];


                    // validamos que el proyecto no este registrado

                    $check_codigo_proyecto = MainModel::ejecutar_consultas_simples(
                        "SELECT * FROM proyectos WHERE codigo_proyecto = '$codigo_anteproyecto'"
                    );

                    if ($check_codigo_proyecto->rowCount() > 0) {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "El código del proyectos  ya esta registrado",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }
                   
                    // 2. Registrar los datos en la tabla 'proyectos'
                    $registrar_proyecto_nuevo = MainModel::ejecutar_consultas_simples(
                        "INSERT INTO proyectos (codigo_proyecto, titulo_proyecto, palabras_claves, id_facultad, id_programa, fecha_creacion,modalidad) 
                        VALUES ('$codigo_anteproyecto', '$titulo_anteproyecto', '$palabras_claves_anteproyecto', '$facultad_anteproyecto_registrada', '$programa_anteproyecto_registrada', NOW(),$modalidad_anteproyecto_registrada)"
                    );

                    // actualizamos el estado del anteproyecto 
    
                    $actualizar_estado_anteproyecto = MainModel::ejecutar_consultas_simples(
                        "UPDATE anteproyectos 
                        SET estado = 'Aprobado'
                        WHERE codigo_anteproyecto = '$codigo_anteproyecto'"
                    );
                    

                    if ($registrar_proyecto_nuevo) {
                        // 3. Consultar los estudiantes asignados al anteproyecto
                        $consulta_anteproyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                            "SELECT 
                                a.codigo_anteproyecto,
                                u.numero_documento,
                                u.nombre_usuario,
                                u.apellidos_usuario,
                                u.correo_usuario,
                                u.telefono_usuario
                            FROM asignar_estudiante_anteproyecto a
                            INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                            WHERE a.codigo_anteproyecto = '$codigo_anteproyecto'"
                        );


                        // Iniciar el bucle para actualizar roles y registrar en 'asignar_estudiante_proyecto'
                        while ($row = $consulta_anteproyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {

                            // Extraer datos del estudiante
                            $numero_documento__estudiante = $row['numero_documento'];
                            $nombre_estudiante = $row['nombre_usuario'];
                            $apellido_estudiante = $row['apellidos_usuario'];
                            $correo_usuario_estudiante = $row['correo_usuario'];
                            $nuevo_rol = 4; // Nuevo rol que deseas asignar

                            // Actualizar el rol del estudiante en la tabla 'usuarios'
                            MainModel::ejecutar_consultas_simples(
                                "UPDATE usuarios 
                                SET id_rol = '$nuevo_rol'
                                WHERE numero_documento = '$numero_documento__estudiante'"
                            );

                            // Verificar si el estudiante ya existe en 'asignar_estudiante_proyecto'
                            $consulta_verificar = MainModel::ejecutar_consultas_simples(
                                "SELECT * FROM asignar_estudiante_proyecto 
                                WHERE numero_documento = '$numero_documento__estudiante'"
                            );

                            // Insertar en 'asignar_estudiante_proyecto' si no existe
                            if ($consulta_verificar->rowCount() == 0) {
                                MainModel::ejecutar_consultas_simples(
                                    "INSERT INTO asignar_estudiante_proyecto (
                                        codigo_proyecto, 
                                        numero_documento, 
                                        fecha_creacion
                                    ) VALUES (
                                        '$codigo_anteproyecto', 
                                        '$numero_documento__estudiante', 
                                        NOW()
                                    )"
                                );
                            }


                            $asunto = "Notificación de Aprobación de Anteproyecto";

                          
                            $message .= "Tu anteproyecto ha sido aprobado con éxito ✅👏. Este es un gran paso en tu camino académico y un reflejo de tu esfuerzo, dedicación y compromiso. ¡Has demostrado que estás listo para llevar tu proyecto al siguiente nivel! 🚀✨";

                            $message .= "Ahora es el momento de seguir adelante con la siguiente fase, con la misma pasión y determinación. Recuerda que cada avance te acerca más a tu meta. ¡Sigue así, el éxito te espera! 💪🔥";

                            $cuerpo_html = '
                
                        <!DOCTYPE html>
                        <html lang="es">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Bienvenido a nuestra plataforma</title>'
                            .STYLESCORREO.'
                        </head>
                        <body>
                            <div class="email-container">
                                <div class="email-header">
                                    <img src="'.SERVERURL.'Views/assets/images/'.$logo.'" alt="Logo Universidad">
                                    <h2>¡Notificación de registro en la plataforma!</h2>
                                </div>
                                <div class="email-body">
                                    <p><b>Estimado  ' . $nombre_estudiante . ' ' . $apellido_estudiante . ',</b></p>
                                    '.$message.'
                                    
                                    <div class="credentials">
                                        <ul>
                                            <li><b>Código del Anteproyecto:</b> ' . $codigo_anteproyecto . '</li>
                                            <li><b>Título del Anteproyecto:</b> ' . $titulo_anteproyecto . '</li>
                                        </ul>
                                    </div>
                                    <p class="highlight">¡Felicidades por este importante logro en tu formación académica!</p>
                                    <p>Te invitamos a continuar trabajando con el mismo compromiso y dedicación en esta nueva etapa.</p>
                                      <p>Si tienes dudas o inquietudes, no dudes en comunicarte con tu asesor o el departamento académico correspondiente.</p>

                                    <p> 🎯 ¡Vamos por más logros! 🏆🎓</p>

                                    <p><b>Atentamente,</b><br>
                                    <i>Corporación Universitaria Autónoma de Nariño</i></p>
                                </div>
                                <div class="email-footer">
                                    Este mensaje se envió desde una dirección de correo electrónico no supervisada. No responda a este mensaje.
                                </div>
                            </div>
                        </body>
                        </html>
                        ';

                            $cuerpo_texto = "Hola $nombre_estudiante $apellido_estudiante, Nos complace informarte que tu idea de anteproyecto ha sido aprobada y ahora ha pasado formalmente a la fase de Proyecto de Grado.";

                            // Enviar el correo
                            $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);

                            if (!$enviado2) {
                                $correosEnviados = false; // Si algún correo falla, marcar como falso
                            }


                        }


                        // Mostrar mensaje de confirmación si todos los correos se enviaron correctamente
                        if ($correosEnviados) {
                            $mensaje = [
                                "Alerta" => "Recargar",
                                "Titulo" => "Correos Enviados",
                                "Texto" => "Se ha notificado a los estudiantes asignados que su anteproyecto ha sido aprobado exitosamente.",
                                "Tipo" => "success"
                            ];
                        } else {
                            $mensaje = [
                                "Alerta" => "simple",
                                "Titulo" => "Error en el Envío",
                                "Texto" => "Hubo un problema al enviar algunos correos, pero el proceso se ha completado.",
                                "Tipo" => "error"
                            ];
                        }

                        echo json_encode($mensaje);
                        exit();

                    } else {
                        // Mensaje de error si falla el registro del proyecto
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "No se pudo registrar el proyecto. Intente de nuevo.",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }


                }else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Error al cargar la retroalimentación",
                        "Texto" => "Ocurrió un error al cargar la retroalimentación en el sistema",
                        "Tipo" => "error"
                    ];
                }
                echo json_encode($alerta);
                exit();

                    
        

            }else if($estado == 3){ //anteproyecto cancelado

                $guardar_retroalimentacion = AnteproyectoModelo::guardar_retroalimentacion_anteproyecto_modelo($datos);

                if ($guardar_retroalimentacion) {

                    include __DIR__ . '/../Mail/enviar-correo.php';

                    $correosEnviados = true; // Variable para comprobar si todos los correos se enviaron con éxito
                
                    // 1. Verificar si el código del anteproyecto existe
                    $check_codigo = MainModel::ejecutar_consultas_simples(
                        "SELECT * FROM anteproyectos WHERE codigo_anteproyecto = '$codigo_anteproyecto'"
                    );

                    if ($check_codigo->rowCount() == 0) {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "El código del anteproyecto ingresado no existe",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }


                    // Extraer datos del anteproyecto
                    $datos_anteproyecto = $check_codigo->fetch(PDO::FETCH_ASSOC);

                    $titulo_anteproyecto = $datos_anteproyecto['titulo_anteproyecto'];
                    $palabras_claves_anteproyecto = $datos_anteproyecto['palabras_claves'];
                    $facultad_anteproyecto_registrada = $datos_anteproyecto['id_facultad'];
                    $programa_anteproyecto_registrada = $datos_anteproyecto['id_programa'];


                    // actualizamos el estado del anteproyecto 
    
                    $actualizar_estado_anteproyecto = MainModel::ejecutar_consultas_simples(
                        "UPDATE anteproyectos 
                        SET estado = 'Cancelado'
                        WHERE codigo_anteproyecto = '$codigo_anteproyecto'"
                    );
                    

                    if ($actualizar_estado_anteproyecto) {
                        // 3. Consultar los estudiantes asignados al anteproyecto
                        $consulta_anteproyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                            "SELECT 
                                a.codigo_anteproyecto,
                                u.numero_documento,
                                u.nombre_usuario,
                                u.apellidos_usuario,
                                u.correo_usuario,
                                u.telefono_usuario
                            FROM asignar_estudiante_anteproyecto a
                            INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                            WHERE a.codigo_anteproyecto = '$codigo_anteproyecto'"
                        );


                        // Iniciar el bucle para actualizar roles y registrar en 'asignar_estudiante_proyecto'
                        while ($row = $consulta_anteproyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {

                            // Extraer datos del estudiante
                            $numero_documento__estudiante = $row['numero_documento'];
                            $nombre_estudiante = $row['nombre_usuario'];
                            $apellido_estudiante = $row['apellidos_usuario'];
                            $correo_usuario_estudiante = $row['correo_usuario'];
                           
                            $asunto = "Notificación de Cancelación de Anteproyecto";

                           

                            $message .= "Lamentamos informarle que su anteproyecto ha sido cancelado ❌. Sabemos que esta noticia puede ser desmotivadora, pero cada obstáculo es una oportunidad para mejorar y fortalecerse. 💪✨";

                            $message .= "Le animamos a revisar las observaciones y a trabajar en los aspectos necesarios para retomar su proyecto con más claridad y determinación. El éxito no es la ausencia de caídas, sino la capacidad de levantarse y seguir adelante. 🚀🔥";

                            
    
                            $cuerpo_html = '
                
                        <!DOCTYPE html>
                        <html lang="es">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Bienvenido a nuestra plataforma</title>'
                            .STYLESCORREO.'
                        </head>
                        <body>
                            <div class="email-container">
                                <div class="email-header">
                                    <img src="'.SERVERURL.'Views/assets/images/'.$logo.'" alt="Logo Universidad">
                                    <h2>¡Notificación de registro en la plataforma!</h2>
                                </div>
                                <div class="email-body">
                                    <p><b>Estimado  ' . $nombre_estudiante . ' ' . $apellido_estudiante . ',</b></p>
                                    '.$message.'
                                    
                                    <div class="credentials">
                                        <ul>
                                           <li><b>Código del Anteproyecto:</b> ' . $codigo_anteproyecto . '</li>
                                            <li><b>Título del Anteproyecto:</b> ' . $titulo_anteproyecto . '</li>
                                        </ul>
                                    </div>

                                        <p><b>Indicaciones:</b></p>
                                        <p>Debes realizar las correcciones que el asesor te ha indicado en el sistema o en la reunión correspondiente. Una vez realizadas las correcciones, podrás someter nuevamente tu anteproyecto a evaluación.</p>

        
                                      <p>Si tienes dudas o inquietudes, no dudes en comunicarte con tu asesor o el departamento académico correspondiente.</p>

                                    <p> ¡No se rinda, siga adelante! 💡🏆</p>

                                    <p><b>Atentamente,</b><br>
                                    <i>Corporación Universitaria Autónoma de Nariño</i></p>
                                </div>
                                <div class="email-footer">
                                    Este mensaje se envió desde una dirección de correo electrónico no supervisada. No responda a este mensaje.
                                </div>
                            </div>
                        </body>
                        </html>
                        ';
                            

                            $cuerpo_texto = "Hola $nombre_estudiante $apellido_estudiante, Nos complace informarte que tu idea de anteproyecto ha sido cancelado.";


                            // Enviar el correo
                            $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);

                            if (!$enviado2) {
                                $correosEnviados = false; // Si algún correo falla, marcar como falso
                            }


                        }

                        // Mostrar mensaje de confirmación si todos los correos se enviaron correctamente
                        if ($correosEnviados) {
                            $mensaje = [
                                "Alerta" => "Recargar",
                                "Titulo" => "Correos Enviados",
                                "Texto" => "Se ha notificado a los estudiantes asignados que su anteproyecto ha sido cancelado.",
                                "Tipo" => "success"
                            ];
                        } else {
                            $mensaje = [
                                "Alerta" => "simple",
                                "Titulo" => "Error en el Envío",
                                "Texto" => "Hubo un problema al enviar algunos correos, pero el proceso se ha completado.",
                                "Tipo" => "error"
                            ];
                        }

                        echo json_encode($mensaje);
                        exit();




                    } else {
                        // Mensaje de error si falla el registro del proyecto
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "No se pudo registrar el proyecto. Intente de nuevo.",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }


                }else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Error al cargar la retroalimentación",
                        "Texto" => "Ocurrió un error al cargar la retroalimentación en el sistema",
                        "Tipo" => "error"
                    ];
                }
                echo json_encode($alerta);
                exit();


            } /***** proyecto esta en revision */

            if(empty($fecha_revision)){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "Asegurate que esté establecido la fecha de entrega del siguiente avance",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
    
            $guardar_retroalimentacion = AnteproyectoModelo::guardar_retroalimentacion_anteproyecto_modelo($datos);
    
            if ($guardar_retroalimentacion) {
                
                include_once __DIR__ . '/../Mail/enviar-correo.php';

                $consulta_anteproyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                    "SELECT 
                        a.codigo_anteproyecto,
                        u.numero_documento,
                        u.nombre_usuario,
                        u.apellidos_usuario,
                        u.correo_usuario,
                        u.telefono_usuario
                    FROM asignar_estudiante_anteproyecto a
                    INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                    WHERE a.codigo_anteproyecto = '$codigo_anteproyecto'"
                );

                $correosEnviados = true; // Para verificar si todos los correos fueron enviados

                // Iterar sobre los estudiantes y enviar correos
                while ($row = $consulta_anteproyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {
                    $nombre_estudiante = $row['nombre_usuario'];
                    $apellido_estudiante = $row['apellidos_usuario'];
                    $correo_usuario_estudiante = $row['correo_usuario'];

                    $asunto = "Notificación de Retroalimentación de Documento";

                    $message .= "Su anteproyecto ha recibido retroalimentación por parte del asesor 📌✍️. Este es un paso clave para fortalecer su trabajo y encaminarlo hacia el éxito. 🚀";

                    $message .= "Le invitamos a revisar las observaciones realizadas y realizar las mejoras necesarias dentro del plazo establecido. Cada ajuste es una oportunidad para perfeccionar su proyecto y acercarse más a su objetivo. 💡📚";

                $cuerpo_html = '
        
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Bienvenido a nuestra plataforma</title>'
                    .STYLESCORREO.'
                </head>
                <body>
                    <div class="email-container">
                        <div class="email-header">
                            <img src="'.SERVERURL.'Views/assets/images/'.$logo.'" alt="Logo Universidad">
                            <h2>¡Notificación de registro en la plataforma!</h2>
                        </div>
                        <div class="email-body">
                            <p><b>Estimado  ' . $nombre_estudiante . ' ' . $apellido_estudiante . ',</b></p>
                            '.$message.'
                            
                            <div class="credentials">
                                <ul>
                                   <li><b>Código del Anteproyecto:</b> ' . $codigo_anteproyecto . '</li>
                                    <li><b>Título del Anteproyecto:</b> ' . $titulo_anteproyecto . '</li>
                                </ul>
                            </div>

                            <p>Si tiene dudas o necesita orientación, no dude en comunicarse con su asesor. ¡Siga avanzando con determinación, estamos seguros de que logrará grandes resultados! 💪🔥</p>
                      
                            <p><b>Atentamente,</b><br>
                            <i>Corporación Universitaria Autónoma de Nariño</i></p>
                        </div>
                        <div class="email-footer">
                            Este mensaje se envió desde una dirección de correo electrónico no supervisada. No responda a este mensaje.
                        </div>
                    </div>
                </body>
                </html>
                ';

                    $cuerpo_texto = "Estimado $nombre_estudiante $apellido_estudiante, te informamos que se ha realizado la retroalimentación de tu documento para el anteproyecto con el código $codigo_anteproyecto. Te invitamos a revisar los comentarios y realizar las correcciones necesarias.";

                    $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);
                    
                    // Si falla el envío, marcar como false
                    if (!$enviado2) {
                        $correosEnviados = false;
                    }
                }

                // Mostrar mensaje de confirmación si todos los correos se enviaron correctamente
                if ($correosEnviados) {
                    $mensaje = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Correos Enviados",
                        "Texto" => "Se ha notificado a los estudiantes asignados, retroalimentación cargada con éxito.",
                        "Tipo" => "success"
                    ];
                } else {
                    $mensaje = [
                        "Alerta" => "simple",
                        "Titulo" => "Error en el Envío",
                        "Texto" => "Hubo un problema al enviar algunos correos. La retroalimentación se cargó con éxito.",
                        "Tipo" => "error"
                    ];
                }

                
                echo json_encode($mensaje);

                exit();


            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error al cargar la retroalimentación",
                    "Texto" => "Ocurrió un error al cargar la retroalimentación en el sistema",
                    "Tipo" => "error"
                ];
            }
            echo json_encode($alerta);
            exit();

        }else{ // si existe documento registrado

            
        if ($tipo_archivo != "application/pdf" && $tipo_archivo != "application/msword" && $tipo_archivo != "application/vnd.openxmlformats-officedocument.wordprocessingml.document") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Formato de archivo no válido",
                "Texto" => "El archivo debe estar en formato PDF o Word (.doc o .docx).",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


            $nombre_carpeta = '../Views/document/anteproyectos/'.$codigo_anteproyecto;

            if (file_exists($nombre_carpeta)) {
    
                $extension = pathinfo($_FILES['archivo_user_anteproyecto']['name'], PATHINFO_EXTENSION);
                $nombre_nuevo_archivo = $numero_documento_user_logueado . '_' . time() . '.' . $extension;
                $nombre_carpeta = '../Views/document/anteproyectos/' . $codigo_anteproyecto;
                $ruta_destino = $nombre_carpeta . '/' . $nombre_nuevo_archivo;
    
                if (move_uploaded_file($_FILES['archivo_user_anteproyecto']['tmp_name'], $ruta_destino)) {
    
                    $datos = [
                        'numero_documento' => $numero_documento_user_logueado,
                        'codigo_anteproyecto' => $codigo_anteproyecto,
                        'id_documento_cargado' => $id_documento_cargado,
                        'observacion_general' => $observaciones_generales,
                        'estado' => $estado,
                        'nombre_archivo_word' => $nombre_nuevo_archivo,
                        'estado_revision' => $estado_revision,
                        'fecha_entrega' => $fecha_revision
                    ];

                  

                    /*************************** si el estado es aprobado******************************* */

                    if($estado == 2){ // si es aporbado el anteproyecto

                        $guardar_retroalimentacion = AnteproyectoModelo::guardar_retroalimentacion_anteproyecto_modelo($datos);
        
                        if ($guardar_retroalimentacion) {
        
                            include __DIR__ . '/../Mail/enviar-correo.php';
        
                            $correosEnviados = true; // Variable para comprobar si todos los correos se enviaron con éxito
                        
                            // 1. Verificar si el código del anteproyecto existe
                            $check_codigo = MainModel::ejecutar_consultas_simples(
                                "SELECT * FROM anteproyectos WHERE codigo_anteproyecto = '$codigo_anteproyecto'"
                            );
        
                            if ($check_codigo->rowCount() == 0) {
                                $alerta = [
                                    "Alerta" => "simple",
                                    "Titulo" => "Ocurrió un error inesperado",
                                    "Texto" => "El código del anteproyecto ingresado no existe",
                                    "Tipo" => "error"
                                ];
                                echo json_encode($alerta);
                                exit();
                            }
        
        
                            // Extraer datos del anteproyecto
                            $datos_anteproyecto = $check_codigo->fetch(PDO::FETCH_ASSOC);
        
                            $titulo_anteproyecto = $datos_anteproyecto['titulo_anteproyecto'];
                            $palabras_claves_anteproyecto = $datos_anteproyecto['palabras_claves'];
                            $facultad_anteproyecto_registrada = $datos_anteproyecto['id_facultad'];
                            $programa_anteproyecto_registrada = $datos_anteproyecto['id_programa'];
        
        
                            // validamos que el proyecto no este registrado
        
                            $check_codigo_proyecto = MainModel::ejecutar_consultas_simples(
                                "SELECT * FROM proyectos WHERE codigo_proyecto = '$codigo_anteproyecto'"
                            );
        
                            if ($check_codigo_proyecto->rowCount() > 0) {
                                $alerta = [
                                    "Alerta" => "simple",
                                    "Titulo" => "Ocurrió un error inesperado",
                                    "Texto" => "El código del proyectos  ya esta registrado",
                                    "Tipo" => "error"
                                ];
                                echo json_encode($alerta);
                                exit();
                            }
                           
                            // 2. Registrar los datos en la tabla 'proyectos'
                            $registrar_proyecto_nuevo = MainModel::ejecutar_consultas_simples(
                                "INSERT INTO proyectos (codigo_proyecto, titulo_proyecto, palabras_claves, id_facultad, id_programa, fecha_creacion) 
                                VALUES ('$codigo_anteproyecto', '$titulo_anteproyecto', '$palabras_claves_anteproyecto', '$facultad_anteproyecto_registrada', '$programa_anteproyecto_registrada', NOW())"
                            );

                            $actualizar_estado_anteproyecto = MainModel::ejecutar_consultas_simples(
                                "UPDATE anteproyectos 
                                SET estado = 'Aprobado'
                                WHERE codigo_anteproyecto = '$codigo_anteproyecto'"
                            );
        
            
        
                            if ($registrar_proyecto_nuevo) {
                                // 3. Consultar los estudiantes asignados al anteproyecto
                                $consulta_anteproyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                                    "SELECT 
                                        a.codigo_anteproyecto,
                                        u.numero_documento,
                                        u.nombre_usuario,
                                        u.apellidos_usuario,
                                        u.correo_usuario,
                                        u.telefono_usuario
                                    FROM asignar_estudiante_anteproyecto a
                                    INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                                    WHERE a.codigo_anteproyecto = '$codigo_anteproyecto'"
                                );
        
        
                                // Iniciar el bucle para actualizar roles y registrar en 'asignar_estudiante_proyecto'
                                while ($row = $consulta_anteproyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {
        
                                    // Extraer datos del estudiante
                                    $numero_documento__estudiante = $row['numero_documento'];
                                    $nombre_estudiante = $row['nombre_usuario'];
                                    $apellido_estudiante = $row['apellidos_usuario'];
                                    $correo_usuario_estudiante = $row['correo_usuario'];
                                    $nuevo_rol = 4; // Nuevo rol que deseas asignar
        
                                    // Actualizar el rol del estudiante en la tabla 'usuarios'
                                    MainModel::ejecutar_consultas_simples(
                                        "UPDATE usuarios 
                                        SET id_rol = '$nuevo_rol'
                                        WHERE numero_documento = '$numero_documento__estudiante'"
                                    );
        
                                    // Verificar si el estudiante ya existe en 'asignar_estudiante_proyecto'
                                    $consulta_verificar = MainModel::ejecutar_consultas_simples(
                                        "SELECT * FROM asignar_estudiante_proyecto 
                                        WHERE numero_documento = '$numero_documento__estudiante'"
                                    );
        
                                    // Insertar en 'asignar_estudiante_proyecto' si no existe
                                    if ($consulta_verificar->rowCount() == 0) {
                                        MainModel::ejecutar_consultas_simples(
                                            "INSERT INTO asignar_estudiante_proyecto (
                                                codigo_proyecto, 
                                                numero_documento, 
                                                fecha_creacion
                                            ) VALUES (
                                                '$codigo_anteproyecto', 
                                                '$numero_documento__estudiante', 
                                                NOW()
                                            )"
                                        );
                                    }
        
        
                                    $asunto = "Notificación de Aprobación de Anteproyecto";
        
                                    $message .= "Tu anteproyecto ha sido aprobado con éxito ✅👏. Este es un gran paso en tu camino académico y un reflejo de tu esfuerzo, dedicación y compromiso. ¡Has demostrado que estás listo para llevar tu proyecto al siguiente nivel! 🚀✨";

                                    $message .= "Ahora es el momento de seguir adelante con la siguiente fase, con la misma pasión y determinación. Recuerda que cada avance te acerca más a tu meta. ¡Sigue así, el éxito te espera! 💪🔥";

                                    $cuerpo_html = '
                        
                                <!DOCTYPE html>
                                <html lang="es">
                                <head>
                                    <meta charset="UTF-8">
                                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                    <title>Bienvenido a nuestra plataforma</title>'
                                    .STYLESCORREO.'
                                </head>
                                <body>
                                    <div class="email-container">
                                        <div class="email-header">
                                            <img src="'.SERVERURL.'Views/assets/images/'.$logo.'" alt="Logo Universidad">
                                            <h2>¡Notificación de registro en la plataforma!</h2>
                                        </div>
                                        <div class="email-body">
                                            <p><b>Estimado  ' . $nombre_estudiante . ' ' . $apellido_estudiante . ',</b></p>
                                            '.$message.'
                                            
                                            <div class="credentials">
                                                <ul>
                                                    <li><b>Código del Anteproyecto:</b> ' . $codigo_anteproyecto . '</li>
                                                    <li><b>Título del Anteproyecto:</b> ' . $titulo_anteproyecto . '</li>
                                                </ul>
                                            </div>
                                            <p class="highlight">¡Felicidades por este importante logro en tu formación académica!</p>
                                            <p>Te invitamos a continuar trabajando con el mismo compromiso y dedicación en esta nueva etapa.</p>
                                            <p>Si tienes dudas o inquietudes, no dudes en comunicarte con tu asesor o el departamento académico correspondiente.</p>

                                            <p> 🎯 ¡Vamos por más logros! 🏆🎓</p>

                                            <p><b>Atentamente,</b><br>
                                            <i>Corporación Universitaria Autónoma de Nariño</i></p>
                                        </div>
                                        <div class="email-footer">
                                            Este mensaje se envió desde una dirección de correo electrónico no supervisada. No responda a este mensaje.
                                        </div>
                                    </div>
                                </body>
                                </html>
                                ';
                
                                    $cuerpo_texto = "Hola $nombre_estudiante $apellido_estudiante, Nos complace informarte que tu idea de anteproyecto ha sido aprobada y ahora ha pasado formalmente a la fase de Proyecto de Grado.";
        
        
                                    // Enviar el correo
                                    $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);
        
                                    if (!$enviado2) {
                                        $correosEnviados = false; // Si algún correo falla, marcar como falso
                                    }
        
        
                                }
        
        
                                // Mostrar mensaje de confirmación si todos los correos se enviaron correctamente
                                if ($correosEnviados) {
                                    $mensaje = [
                                        "Alerta" => "Recargar",
                                        "Titulo" => "Correos Enviados",
                                        "Texto" => "Se ha notificado a los estudiantes asignados que su anteproyecto ha sido aprobado exitosamente.",
                                        "Tipo" => "success"
                                    ];
                                } else {
                                    $mensaje = [
                                        "Alerta" => "simple",
                                        "Titulo" => "Error en el Envío",
                                        "Texto" => "Hubo un problema al enviar algunos correos, pero el proceso se ha completado.",
                                        "Tipo" => "error"
                                    ];
                                }
        
                                echo json_encode($mensaje);
                                exit();
        
                            } else {
                                // Mensaje de error si falla el registro del proyecto
                                $alerta = [
                                    "Alerta" => "simple",
                                    "Titulo" => "Ocurrió un error inesperado",
                                    "Texto" => "No se pudo registrar el proyecto. Intente de nuevo.",
                                    "Tipo" => "error"
                                ];
                                echo json_encode($alerta);
                                exit();
                            }
        
        
                        }else {
                            $alerta = [
                                "Alerta" => "simple",
                                "Titulo" => "Error al cargar la retroalimentación",
                                "Texto" => "Ocurrió un error al cargar la retroalimentación en el sistema",
                                "Tipo" => "error"
                            ];
                        }
                        echo json_encode($alerta);
                        exit();
        
                            
                
        
                    }else if($estado == 3){ //anteproyecto cancelado

                        $guardar_retroalimentacion = AnteproyectoModelo::guardar_retroalimentacion_anteproyecto_modelo($datos);
        
                        if ($guardar_retroalimentacion) {
        
                            include __DIR__ . '/../Mail/enviar-correo.php';
        
                            $correosEnviados = true; // Variable para comprobar si todos los correos se enviaron con éxito
                        
                            // 1. Verificar si el código del anteproyecto existe
                            $check_codigo = MainModel::ejecutar_consultas_simples(
                                "SELECT * FROM anteproyectos WHERE codigo_anteproyecto = '$codigo_anteproyecto'"
                            );
        
                            if ($check_codigo->rowCount() == 0) {
                                $alerta = [
                                    "Alerta" => "simple",
                                    "Titulo" => "Ocurrió un error inesperado",
                                    "Texto" => "El código del anteproyecto ingresado no existe",
                                    "Tipo" => "error"
                                ];
                                echo json_encode($alerta);
                                exit();
                            }
        
        
                            // Extraer datos del anteproyecto
                            $datos_anteproyecto = $check_codigo->fetch(PDO::FETCH_ASSOC);
        
                            $titulo_anteproyecto = $datos_anteproyecto['titulo_anteproyecto'];
                            $palabras_claves_anteproyecto = $datos_anteproyecto['palabras_claves'];
                            $facultad_anteproyecto_registrada = $datos_anteproyecto['id_facultad'];
                            $programa_anteproyecto_registrada = $datos_anteproyecto['id_programa'];
        
        
                            // actualizamos el estado del anteproyecto 
            
                            $actualizar_estado_anteproyecto = MainModel::ejecutar_consultas_simples(
                                "UPDATE anteproyectos 
                                SET estado = 'Cancelado'
                                WHERE codigo_anteproyecto = '$codigo_anteproyecto'"
                            );
                            
        
                            if ($actualizar_estado_anteproyecto) {
                                // 3. Consultar los estudiantes asignados al anteproyecto
                                $consulta_anteproyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                                    "SELECT 
                                        a.codigo_anteproyecto,
                                        u.numero_documento,
                                        u.nombre_usuario,
                                        u.apellidos_usuario,
                                        u.correo_usuario,
                                        u.telefono_usuario
                                    FROM asignar_estudiante_anteproyecto a
                                    INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                                    WHERE a.codigo_anteproyecto = '$codigo_anteproyecto'"
                                );
        
        
                                // Iniciar el bucle para actualizar roles y registrar en 'asignar_estudiante_proyecto'
                                while ($row = $consulta_anteproyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {
        
                                    // Extraer datos del estudiante
                                    $numero_documento__estudiante = $row['numero_documento'];
                                    $nombre_estudiante = $row['nombre_usuario'];
                                    $apellido_estudiante = $row['apellidos_usuario'];
                                    $correo_usuario_estudiante = $row['correo_usuario'];
                                   
        
                                    $asunto = "Notificación de Cancelación de Anteproyecto";
        
                                    $message .= "Lamentamos informarle que su anteproyecto ha sido cancelado ❌. Sabemos que esta noticia puede ser desmotivadora, pero cada obstáculo es una oportunidad para mejorar y fortalecerse. 💪✨";

                                    $message .= "Le animamos a revisar las observaciones y a trabajar en los aspectos necesarios para retomar su proyecto con más claridad y determinación. El éxito no es la ausencia de caídas, sino la capacidad de levantarse y seguir adelante. 🚀🔥";

                                    
            
                                    $cuerpo_html = '
                        
                                <!DOCTYPE html>
                                <html lang="es">
                                <head>
                                    <meta charset="UTF-8">
                                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                    <title>Bienvenido a nuestra plataforma</title>'
                                    .STYLESCORREO.'
                                </head>
                                <body>
                                    <div class="email-container">
                                        <div class="email-header">
                                            <img src="'.SERVERURL.'Views/assets/images/'.$logo.'" alt="Logo Universidad">
                                            <h2>¡Notificación de registro en la plataforma!</h2>
                                        </div>
                                        <div class="email-body">
                                            <p><b>Estimado  ' . $nombre_estudiante . ' ' . $apellido_estudiante . ',</b></p>
                                            '.$message.'
                                            
                                            <div class="credentials">
                                                <ul>
                                                <li><b>Código del Anteproyecto:</b> ' . $codigo_anteproyecto . '</li>
                                                    <li><b>Título del Anteproyecto:</b> ' . $titulo_anteproyecto . '</li>
                                                </ul>
                                            </div>

                                                <p><b>Indicaciones:</b></p>
                                                <p>Debes realizar las correcciones que el asesor te ha indicado en el sistema o en la reunión correspondiente. Una vez realizadas las correcciones, podrás someter nuevamente tu anteproyecto a evaluación.</p>

                
                                            <p>Si tienes dudas o inquietudes, no dudes en comunicarte con tu asesor o el departamento académico correspondiente.</p>

                                            <p> ¡No se rinda, siga adelante! 💡🏆</p>

                                            <p><b>Atentamente,</b><br>
                                            <i>Corporación Universitaria Autónoma de Nariño</i></p>
                                        </div>
                                        <div class="email-footer">
                                            Este mensaje se envió desde una dirección de correo electrónico no supervisada. No responda a este mensaje.
                                        </div>
                                    </div>
                                </body>
                                </html>
                                ';
                                    
                                
                                    $cuerpo_texto = "Hola $nombre_estudiante $apellido_estudiante, Nos complace informarte que tu idea de anteproyecto ha sido cancelada.";
        
        
                                    // Enviar el correo
                                    $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);
        
                                    if (!$enviado2) {
                                        $correosEnviados = false; // Si algún correo falla, marcar como falso
                                    }
        
        
                                }
        
        
                                // Mostrar mensaje de confirmación si todos los correos se enviaron correctamente
                                if ($correosEnviados) {
                                    $mensaje = [
                                        "Alerta" => "Recargar",
                                        "Titulo" => "Correos Enviados",
                                        "Texto" => "Se ha notificado a los estudiantes asignados que su anteproyecto ha sido cancelado.",
                                        "Tipo" => "success"
                                    ];
                                } else {
                                    $mensaje = [
                                        "Alerta" => "simple",
                                        "Titulo" => "Error en el Envío",
                                        "Texto" => "Hubo un problema al enviar algunos correos, pero el proceso se ha completado.",
                                        "Tipo" => "error"
                                    ];
                                }
        
                                echo json_encode($mensaje);
                                exit();
        
                            } else {
                                // Mensaje de error si falla el registro del proyecto
                                $alerta = [
                                    "Alerta" => "simple",
                                    "Titulo" => "Ocurrió un error inesperado",
                                    "Texto" => "No se pudo registrar el proyecto. Intente de nuevo.",
                                    "Tipo" => "error"
                                ];
                                echo json_encode($alerta);
                                exit();
                            }
        
        
                        }else {
                            $alerta = [
                                "Alerta" => "simple",
                                "Titulo" => "Error al cargar la retroalimentación",
                                "Texto" => "Ocurrió un error al cargar la retroalimentación en el sistema",
                                "Tipo" => "error"
                            ];
                        }
                        echo json_encode($alerta);
                        exit();
        
        
                    }

                    if(empty($fecha_revision)){
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "Asegurate que esté establecido la fecha de entrega del siguiente avance",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }
              

                    $guardar_retroalimentacion = AnteproyectoModelo::guardar_retroalimentacion_anteproyecto_modelo($datos);
            
                    if ($guardar_retroalimentacion) {

                        include_once __DIR__ . '/../Mail/enviar-correo.php';

                        $consulta_anteproyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                            "SELECT 
                                a.codigo_anteproyecto,
                                u.numero_documento,
                                u.nombre_usuario,
                                u.apellidos_usuario,
                                u.correo_usuario,
                                u.telefono_usuario
                            FROM asignar_estudiante_anteproyecto a
                            INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                            WHERE a.codigo_anteproyecto = '$codigo_anteproyecto'"
                        );
                        
                        $correosEnviados = true; // Para verificar si todos los correos fueron enviados
                        
                        // Iterar sobre los estudiantes y enviar correos
                        while ($row = $consulta_anteproyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {
                            $nombre_estudiante = $row['nombre_usuario'];
                            $apellido_estudiante = $row['apellidos_usuario'];
                            $correo_usuario_estudiante = $row['correo_usuario'];
                        
                            $asunto = "Notificación de Retroalimentación de Documento";
                        
                           
                                $message .= "Su anteproyecto ha recibido retroalimentación por parte del asesor 📌✍️. Este es un paso clave para fortalecer su trabajo y encaminarlo hacia el éxito. 🚀";

                                $message .= "Le invitamos a revisar las observaciones realizadas y realizar las mejoras necesarias dentro del plazo establecido. Cada ajuste es una oportunidad para perfeccionar su proyecto y acercarse más a su objetivo. 💡📚";

                            $cuerpo_html = '
                    
                            <!DOCTYPE html>
                            <html lang="es">
                            <head>
                                <meta charset="UTF-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title>Bienvenido a nuestra plataforma</title>'
                                .STYLESCORREO.'
                            </head>
                            <body>
                                <div class="email-container">
                                    <div class="email-header">
                                        <img src="'.SERVERURL.'Views/assets/images/'.$logo.'" alt="Logo Universidad">
                                        <h2>¡Notificación de registro en la plataforma!</h2>
                                    </div>
                                    <div class="email-body">
                                        <p><b>Estimado  ' . $nombre_estudiante . ' ' . $apellido_estudiante . ',</b></p>
                                        '.$message.'
                                        
                                        <div class="credentials">
                                            <ul>
                                            <li><b>Código del Anteproyecto:</b> ' . $codigo_anteproyecto . '</li>
                                                <li><b>Título del Anteproyecto:</b> ' . $titulo_anteproyecto . '</li>
                                            </ul>
                                        </div>

                                        <p>Si tiene dudas o necesita orientación, no dude en comunicarse con su asesor. ¡Siga avanzando con determinación, estamos seguros de que logrará grandes resultados! 💪🔥</p>
                                
                                        <p><b>Atentamente,</b><br>
                                        <i>Corporación Universitaria Autónoma de Nariño</i></p>
                                    </div>
                                    <div class="email-footer">
                                        Este mensaje se envió desde una dirección de correo electrónico no supervisada. No responda a este mensaje.
                                    </div>
                                </div>
                            </body>
                            </html>
                            ';
                        
                            $cuerpo_texto = "Estimado $nombre_estudiante $apellido_estudiante, te informamos que se ha realizado la retroalimentación de tu documento para el anteproyecto con el código $codigo_anteproyecto. Te invitamos a revisar los comentarios y realizar las correcciones necesarias.";
                        
                            $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);
                            
                            // Si falla el envío, marcar como false
                            if (!$enviado2) {
                                $correosEnviados = false;
                            }
                        }
                        
                        // Mostrar mensaje de confirmación si todos los correos se enviaron correctamente
                        if ($correosEnviados) {
                            $mensaje = [
                                "Alerta" => "Recargar",
                                "Titulo" => "Correos Enviados",
                                "Texto" => "Se ha notificado a los estudiantes asignados, retroalimentación cargada con éxito.",
                                "Tipo" => "success"
                            ];
                        } else {
                            $mensaje = [
                                "Alerta" => "simple",
                                "Titulo" => "Error en el Envío",
                                "Texto" => "Hubo un problema al enviar algunos correos. La retroalimentación se cargó con éxito.",
                                "Tipo" => "error"
                            ];
                        }
                        
                        echo json_encode($mensaje);
        
                        exit();



                    } else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Error al cargar la retroalimentación",
                            "Texto" => "Ocurrió un error al cargar la retroalimentación en el sistema",
                            "Tipo" => "error"
                        ];
                    }
                    echo json_encode($alerta);
                    exit();
    
    
                }
    
    
            }else{
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "Los Estudiantes no han creado la carpeta para guardar el documento",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
    


        }




    }

    public function actualizar_fecha_retroalimentacion(){
        $fecha_revision = MainModel::limpiar_cadenas($_POST['fecha_revision']); 
        $id_retrolimentacion_editar = MainModel::limpiar_cadenas($_POST['id_retrolimentacion_editar']);
        $id_retrolimentacion_editar =  MainModel::decryption($id_retrolimentacion_editar);
      

        if ( empty($fecha_revision)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $actualizar_fecha_entrega = MainModel::ejecutar_consultas_simples(
            "UPDATE retroalimentacion_anteproyecto 
            SET fecha_entrega_avances = '$fecha_revision' 
            WHERE id = '$id_retrolimentacion_editar'"
        );
        
        if ($actualizar_fecha_entrega->rowCount() > 0) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "✅ Fecha actualizada con éxito",
                "Texto" => "La fecha de entrega de avances ha sido actualizada correctamente.",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "❌ No se pudo actualizar la fecha",
                "Texto" => "Ocurrió un error al intentar actualizar la fecha de entrega. Por favor, inténtelo nuevamente.",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
        exit();
        
    }

    public function actualizar_estado_anteproyectos (){
        $estado = MainModel::limpiar_cadenas($_POST['actualizar_estado_anteproyecto']);
        $codigo = MainModel::limpiar_cadenas($_POST['codigo_idea_upd_estado']);
        if (empty($estado) ||  empty($codigo)){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $estado =  MainModel::decryption($estado);
        $codigo =  MainModel::decryption($codigo);

        $check_codigo = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM anteproyectos WHERE codigo_anteproyecto = '$codigo'"
        );

        if ($check_codigo->rowCount() == 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El código del anteproyecto ingresado no existe",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (!in_array($estado, [1, 2, 3])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Estado inválido",
                "Texto" => "El estado proporcionado no es válido. Solo se permiten los valores 1, 2 o 3.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if($estado ==1){
            $estado = "Revisión";
        }else if($estado ==2){
            $estado = "Aprobado";
        }else{
            $estado = "Cancelado";
        }

        $actualizar_estado_anteproyecto = MainModel::ejecutar_consultas_simples(
            "UPDATE anteproyectos 
            SET estado = ' $estado'
            WHERE codigo_anteproyecto = '$codigo'"
        );

        if($actualizar_estado_anteproyecto->rowCount() > 0){
            
            include __DIR__ . '/../Mail/enviar-correo.php';

            $correosEnviados = true; // Variable para comprobar si todos los correos se enviaron con éxito
             // 3. Consultar los estudiantes asignados al anteproyecto
             $consulta_anteproyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                "SELECT 
                    a.codigo_anteproyecto,
                    u.numero_documento,
                    u.nombre_usuario,
                    u.apellidos_usuario,
                    u.correo_usuario,
                    u.telefono_usuario
                FROM asignar_estudiante_anteproyecto a
                INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                WHERE a.codigo_anteproyecto = '$codigo'"
            );

            while ($row = $consulta_anteproyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {

                // Extraer datos del estudiante
                $nombre_estudiante = $row['nombre_usuario'];
                $apellido_estudiante = $row['apellidos_usuario'];
                $correo_usuario_estudiante = $row['correo_usuario'];

                $asunto = "Notificación de Actualización de Estado de Anteproyecto";

                $cuerpo_html = '
                <html>
                <head>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                        }
                        .email-header {
                            background-color: #F8DC0B;
                            padding: 10px;
                            color: white;
                            text-align: center;
                        }
                        .email-body {
                            padding: 20px;
                            background-color: #f9f9f9;
                        }
                        .email-footer {
                            font-size: 12px;
                            color: gray;
                            text-align: center;
                            margin-top: 20px;
                            padding-top: 10px;
                            border-top: 1px solid #dddddd;
                        }
                        .highlight {
                            color: #008000;
                            font-weight: bold;
                        }
                    </style>
                </head>
                <body>
                    <div class="email-header">
                        <img src="https://aunarvillavicencio.edu.co/wp-content/uploads/2024/04/cropped-Logo-AUNAR-AZUL.-03-2048x777.png" alt="Logo Universidad" width="150">
                        <h2>Actualización de Estado de Anteproyecto</h2>
                    </div>
                    <div class="email-body">
                        <p>Estimado/a <b>' . $nombre_estudiante . ' ' . $apellido_estudiante . '</b>,</p>
                        <p>Te informamos que el estado de tu <b>anteproyecto</b> ha sido actualizado. Es fundamental que prestes atención a las recomendaciones proporcionadas por tu asesor para evitar posibles cancelaciones en el futuro.</p>
                        <p>A continuación, te compartimos los detalles:</p>
                        <p><b>Código del Proyecto:</b> ' . $codigo . '</p>
                        <p class="highlight">Recuerda seguir las orientaciones de tu asesor para garantizar el éxito en esta etapa de tu formación académica.</p>
                        <p>Si tienes dudas o inquietudes, no dudes en comunicarte con tu asesor o el departamento académico correspondiente.</p>
                        <p>Atentamente,<br>
                        <i>Corporación Universitaria Autónoma de Nariño</i></p>
                    </div>
                    <div class="email-footer">
                        Este mensaje se envió desde una dirección de correo electrónico no supervisada. Por favor, no respondas a este mensaje.
                    </div>
                </body>
                </html>
                ';

                $cuerpo_texto = "Hola $nombre_estudiante $apellido_estudiante, Te informamos que el estado de tu anteproyecto ha sido actualizado. Por favor, sigue las recomendaciones de tu asesor para evitar posibles cancelaciones en el futuro.";

                // Enviar el correo
                $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);

                if (!$enviado2) {
                    $correosEnviados = false; // Si algún correo falla, marcar como falso
                }

            }

                 // Mostrar mensaje de confirmación si todos los correos se enviaron correctamente
                 if ($correosEnviados) {
                     $mensaje = [
                         "Alerta" => "Recargar",
                         "Titulo" => "Correos Enviados",
                         "Texto" => "Se ha notificado a los estudiantes asignados que se actualio el estado de su proyecto",
                         "Tipo" => "success"
                     ];
                 } else {
                     $mensaje = [
                         "Alerta" => "simple",
                         "Titulo" => "Error en el Envío",
                         "Texto" => "Hubo un problema al enviar algunos correos, pero el proceso se ha completado.",
                         "Tipo" => "error"
                     ];
                 }
            
                echo json_encode($mensaje);
                exit();
        }


    }

    public function cargar_evidencias_reuniones_anteproyectos(){
        $codigo = MainModel::limpiar_cadenas($_POST['codigo_anteproyecto_evidencia']);
        $numero_documento_user_logueado = MainModel::limpiar_cadenas($_POST['numero_documento_user_logueado_evidencia']);
    
        // Verificar que el input contenga archivos
        if (empty($codigo) || empty($numero_documento_user_logueado) || empty($_FILES['evidencia_user_anteproyecto']['name'][0])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios y debe seleccionar al menos un archivo.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
    
        // Validación de archivos y condiciones iniciales
        foreach ($_FILES['evidencia_user_anteproyecto']['name'] as $index => $nombre_archivo) {
            $tipo_archivo = $_FILES['evidencia_user_anteproyecto']['type'][$index];
            if (!in_array($tipo_archivo, ["image/jpeg", "image/png", "image/gif"])) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Formato de archivo no válido",
                    "Texto" => "Los archivos deben estar en formato de imagen (JPEG, PNG o GIF).",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }
    
        // Desencriptación y verificación de existencia en la BD
        $codigo = MainModel::decryption($codigo);
        $numero_documento_user_logueado = MainModel::decryption($numero_documento_user_logueado);
        $check_usuario = MainModel::ejecutar_consultas_simples("SELECT * FROM usuarios WHERE numero_documento = '$numero_documento_user_logueado'");
        $check_codigo = MainModel::ejecutar_consultas_simples("SELECT * FROM anteproyectos WHERE codigo_anteproyecto = '$codigo'");
    
        if ($check_usuario->rowCount() <= 0 || $check_codigo->rowCount() == 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El usuario o el código del anteproyecto no existen en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
    
        // Crear carpeta si no existe
        $nombre_carpeta = '../Views/document/anteproyectos/' . $codigo . '/evidencia/';
        if (!file_exists($nombre_carpeta)) mkdir($nombre_carpeta, 0755, true);
    
        $guardado_exitoso = true;  // Bandera de éxito
        foreach ($_FILES['evidencia_user_anteproyecto']['name'] as $index => $nombre_archivo) {
            $tmp_name = $_FILES['evidencia_user_anteproyecto']['tmp_name'][$index];
            $nombre_unico = uniqid() . '_' . basename($nombre_archivo);
            $ruta_completa = $nombre_carpeta . $nombre_unico;
    
            // Intento de mover el archivo
            if (move_uploaded_file($tmp_name, $ruta_completa)) {
                $datos = [
                    'numero_documento' => $numero_documento_user_logueado,
                    'codigo_anteproyecto' => $codigo,
                    'imagenes' => $nombre_unico,
                    'fecha_creacion' => date('Y-m-d')
                ];
                if (!AnteproyectoModelo::cargar_evidencia_reunion_modelo($datos)) {
                    $guardado_exitoso = false;
                    break;
                }
            } else {
                $guardado_exitoso = false;
                break;
            }
        }
    
        // Respuesta final basada en el estado de guardado
        if ($guardado_exitoso) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Documentos cargados",
                "Texto" => "Todas las imágenes de evidencia se han cargado correctamente.",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error al guardar",
                "Texto" => "Hubo un error al cargar una o más imágenes de evidencia.",
                "Tipo" => "error"
            ];
        }
    
        echo json_encode($alerta);
        exit();
    }
    

    public function eliminar_asesor_proyectos(){
        
        $codigo = MainModel::limpiar_cadenas($_POST['codigoProyecto']);
        $numero_documento = MainModel::limpiar_cadenas($_POST['documento_user_asesor']);


        if (empty($codigo) || empty($numero_documento) ) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $numero_documento =  MainModel::decryption($numero_documento);

        $codigo =  MainModel::decryption($codigo);

        $check_codigo = MainModel::ejecutar_consultas_simples(
            "SELECT codigo_proyecto FROM Asignar_asesor_anteproyecto_proyecto WHERE codigo_proyecto = '$codigo'"
        );

        if ($check_codigo->rowCount() == 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El codigo ingresado no existe",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_id_usuario = MainModel::ejecutar_consultas_simples("SELECT numero_documento FROM usuarios WHERE numero_documento = '$numero_documento'");

        if ($check_id_usuario->rowCount() == 0) {  // Si no hay coincidencias
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El usuario no existe en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $eliminar_estudiante = AnteproyectoModelo::eliminar_asesor_proyecto_modelo($codigo, $numero_documento);
        
        if ($eliminar_estudiante->rowCount() > 0) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Operación exitosa",
                "Texto" => "El asesor fue eliminado del proyecto",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo eliminar al asesor del proyecto",
                "Tipo" => "error"
            ];
        }
        
        echo json_encode($alerta);
        exit();


    }
     

    public function delete_evidecia_anteproyecto(){

        $codigo = MainModel::limpiar_cadenas($_POST['delete_evidencia_anteproyectos']);

        $fecha = MainModel::limpiar_cadenas($_POST['fecha']);
     

        if (empty($codigo)  ) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $codigo =  MainModel::decryption($codigo);
        $fecha =  MainModel::decryption($fecha);

        $check_codigo = MainModel::ejecutar_consultas_simples(
            "SELECT codigo_anteproyecto FROM evidencia_reuniones_anteproyectos WHERE codigo_anteproyecto = '$codigo'"
        );

        if ($check_codigo->rowCount() == 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El codigo ingresado no existe",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $extraer_datos = MainModel::ejecutar_consultas_simples(
            "SELECT * 
            FROM evidencia_reuniones_anteproyectos 
            WHERE codigo_anteproyecto = '$codigo' 
            AND DATE(fecha_creacion) = '$fecha'"
        );
        
        // Verificar si hay datos
        if ($extraer_datos->rowCount() > 0) {
            $datos = $extraer_datos->fetchAll(PDO::FETCH_ASSOC);
            $errorEliminar = false; // Bandera para verificar si hubo errores al eliminar archivos
        
            foreach ($datos as $fila) {
                $imagen = trim($fila['imagenes']); // Eliminar espacios en blanco
                if (!empty($imagen)) { // Verifica que la imagen no esté vacía
                    $rutaImagen = '../Views/document/anteproyectos/' . $codigo . '/evidencia/' . $imagen;
            
                    // Verificar si la imagen existe antes de eliminarla
                    if (file_exists($rutaImagen) && is_file($rutaImagen)) {
                        if (!unlink($rutaImagen)) {
                            $errorEliminar = true;
                            error_log("Error al eliminar la imagen: " . $rutaImagen);
                        }
                    } else {
                        $errorEliminar = true;
                        error_log("La imagen no existe o no es un archivo válido: " . $rutaImagen);
                    }
                }
            }
            
        
            // Si no hubo errores eliminando archivos, procedemos con la eliminación en la base de datos
            if (!$errorEliminar) {
                $eliminarDB = MainModel::ejecutar_consultas_simples(
                    "DELETE FROM evidencia_reuniones_anteproyectos 
                    WHERE codigo_anteproyecto = '$codigo' 
                    AND DATE(fecha_creacion) = '$fecha'"
                );
        
                // Verificar si la eliminación en la base de datos fue exitosa
                if ($eliminarDB->rowCount() > 0) {
                    $alerta = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Evidencia eliminada",
                        "Texto" => "Se ha eliminado toda la evidencia correctamente.",
                        "Tipo" => "success"
                    ];
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Error en la eliminación",
                        "Texto" => "No se pudo eliminar la evidencia en la base de datos.",
                        "Tipo" => "error"
                    ];
                }
                echo json_encode($alerta);
                exit();
            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error al eliminar archivos",
                    "Texto" => "Hubo un problema al eliminar algunos archivos.",
                    "Tipo" => "error"
                ];
            }
            echo json_encode($alerta);
            exit();
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se encontraron registros.",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
        exit();

    }

}
