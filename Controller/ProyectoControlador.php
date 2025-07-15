<?php

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

if ($peticionAjax) {
    require_once "../Model/ProyectoModelo.php";
} else {
    require_once "./Model/ProyectoModelo.php";
}

class ProyectoControlador extends ProyectoModelo{

    public function registrar_proyectos_controlador()
    {
        $codigo = MainModel::limpiar_cadenas($_POST['codigo_proyecto_reg']);
        $titulo = MainModel::limpiar_cadenas($_POST['titulo_proyecto_reg']);
        $palabrasClaves = MainModel::limpiar_cadenas($_POST['palabras_claves_proyecto_reg']);
        $tipo_faculta_reg = MainModel::limpiar_cadenas($_POST['tipo_faculta_reg']);
        $tipo_programa_reg = MainModel::limpiar_cadenas($_POST['tipo_programa_reg']);
        $tipo_modalidad_reg = MainModel::limpiar_cadenas($_POST['tipo_modalidad_reg']);

        if (empty($codigo) || empty($titulo) || empty($palabrasClaves) || empty($tipo_faculta_reg) || empty($tipo_programa_reg)
        
        || empty($tipo_modalidad_reg)) {
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
            "SELECT codigo_proyecto FROM proyectos WHERE codigo_proyecto = '$codigo'"
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
            "SELECT titulo_proyecto FROM proyectos WHERE titulo_proyecto = '$titulo'"
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


        
        $datos_proyectos = [
            "codigo_proyecto" => $codigo,
            "titulo_proyecto" => $titulo,
            "palabras_claves_proyecto" => $palabrasClaves,
            "id_facultad" => (int) $tipo_faculta,
            "id_programa" => (int) $tipo_programa,
            "modalidad" => (int) $tipo_modalidad_reg
        ];

        $guardar_proyecto = ProyectoModelo::agregar_proyecto_modelo($datos_proyectos);


        if ($guardar_proyecto->rowCount() > 0) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Operación exitosa",
                "Texto" => "El Proyecto ha sido registrado correctamente.",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo registrar el Proyecto.",
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

        $codigo = MainModel::limpiar_cadenas($_POST['codigo_proyecto_regAsig']);

        $numero_documento = MainModel::limpiar_cadenas($_POST['numero_documento_regP']);

        $privilegio_user_logueado = MainModel::limpiar_cadenas($_POST['privilegio_user_logueado']);



        if (empty($codigo) || empty($numero_documento) || empty($privilegio_user_logueado)) {
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
            "SELECT * FROM proyectos WHERE codigo_proyecto = '$codigo'"
        );

        // Verificar si el código existe en la base de datos
        if ($check_codigo->rowCount() <= 0) {
            // Si no existe, enviar un mensaje de alerta
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Código no encontrado",
                "Texto" => "El código del Proyecto no está registrado en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $datos_proyecto = $check_codigo->fetch(PDO::FETCH_ASSOC);

        $id_programa_proyecto = $datos_proyecto['id_programa'];
        
        $id_faculta_proyecto = $datos_proyecto['id_facultad']; 

        $titulo_proyecto = $datos_proyecto['titulo_proyecto'];

        


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
                "Texto" => "El número de documento del estudiante no tiene asignado una facultad y un programa academico.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $datos_asignacion = $check_estudiante_asignacion->fetch(PDO::FETCH_ASSOC);

        $id_programa_asignacion_estudiante = $datos_asignacion['id_programa'];  

        $id_faculta_asignacion_estudiante = $datos_asignacion['id_facultad'];

        if($id_programa_proyecto === $id_programa_asignacion_estudiante && $id_faculta_proyecto === $id_faculta_asignacion_estudiante){

            $consulta_numero_estudiantes = MainModel::ejecutar_consultas_simples(
                "SELECT COUNT(numero_documento) AS total_estudiantes 
                FROM asignar_estudiante_proyecto 
                WHERE codigo_proyecto = '$codigo'"
            );
    
            if ($consulta_numero_estudiantes->rowCount() > 0) {
                
                $datos_asignacion = $consulta_numero_estudiantes->fetch(PDO::FETCH_ASSOC);

                $total_estudiantes = $datos_asignacion['total_estudiantes'];

            
                if ($id_rol_estudiante != 4) {
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
                    FROM asignar_estudiante_proyecto 
                    WHERE codigo_proyecto = '$codigo'
                    AND numero_documento = '$numero_documento'"
                );

                if ($consulta->rowCount() > 0) {
                    // Si ya está asignado, enviar un mensaje de alerta
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Usuario ya asignado",
                        "Texto" => "El usuario  " . $nombre_estudiante . " ya está asignado al proyecto " . $codigo . ".",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

        

                $consulta = MainModel::ejecutar_consultas_simples(
                    "SELECT numero_documento 
                    FROM asignar_estudiante_proyecto 
                    WHERE numero_documento = '$numero_documento' 
                    AND codigo_proyecto != '$codigo'"
                );

                if ($consulta->rowCount() > 0) {
                    // Si ya está asignado a otro proyecto, enviar un mensaje de alerta
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Usuario asignado a otro proyecto",
                        "Texto" => "El usuario  " . $nombre_estudiante . " ya está asignado a otro proyecto y no puede ser asignado a este.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                if($total_estudiantes >= $numero_maximo_estudiantes){
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Número máximo de estudiantes",
                        "Texto" => "El número máximo de estudiantes para este proyecto ha sido alcanzado.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                $datos_asignar = [
                    "codigo_proyecto" => $codigo,
                    "numero_documento" => $numero_documento
                ];

                $guardar_asignar = ProyectoModelo::asignar_estudiante_proyecto_modelo($datos_asignar);

                if ($guardar_asignar->rowCount() > 0) {
                    

                    $message = "<p>🎉 ¡Lo lograste! Has alcanzado la <b>etapa final de tu proyecto</b>, el último paso antes de hacer realidad tu meta académica. 🚀✨</p>

                    <p>Este es el momento de demostrar todo tu conocimiento, esfuerzo y dedicación. Ahora, cada ajuste, cada análisis y cada decisión marcarán la diferencia en la calidad de tu trabajo. 🔍📚</p>
                    
                    <p>Recuerda, <b>estás a un paso de la meta</b>. Mantén la motivación, confía en tus habilidades y da lo mejor de ti. ¡Tu éxito está más cerca que nunca, y este esfuerzo definirá tu futuro profesional! 💡🔥</p>";
                    


                    include __DIR__ . '/../Mail/enviar-correo.php';

                    $asunto = "Notificación de Asignación de Proyecto";

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
                                       <h2>Asignación de Proyecto</h2>
                                </div>
                                <div class="email-body">
                                    <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                                    '.$message.'
                                    <p>Nos complace informarte que se te ha asignado el siguiente proyecto:</p>
                                    <div class="credentials">
                                        <ul>
                                            <li><b>Código del proyecto:</b> ' . $codigo . '</li>
                                            <li><b>Título del proyecto:</b> ' . $titulo_proyecto . '</li>
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

                    $cuerpo_texto = "Estimado $nombre_usuario $apellido_usuario, nos complace informarte que se te ha asignado el proyecto con el código $codigo y el título $titulo_proyecto. ¡Estamos seguros de que harás un excelente trabajo!";

                    $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);
                    
                    if ($enviado) {
                        $alerta = [
                            "Alerta" => "Recargar",
                            "Titulo" => "Operación exitosa",
                            "Texto" => "El estudiante ha sido asignado al proyecto correctamente.",
                            "Tipo" => "success"
                        ];
                    }else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "El estudiante ha sido asignado al proyecto correctamente. pero no se pudo enviar el correo electrónico al estudiante.",
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
                "Texto" => "El programa del estudiante no coincide con el del proyecto o la facultad del estudiante no coincide con la facultad del proyecto.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $privilegio_user_logueado =  MainModel::decryption($privilegio_user_logueado);
        
        
        if($privilegio_user_logueado==1){

            if($id_programa_proyecto === $id_programa_asignacion_estudiante && $id_faculta_proyecto === $id_faculta_asignacion_estudiante){

                $consulta_numero_estudiantes = MainModel::ejecutar_consultas_simples(
                    "SELECT COUNT(numero_documento) AS total_estudiantes 
                    FROM asignar_estudiante_proyecto 
                    WHERE codigo_proyecto = '$codigo'"
                );
        
                if ($consulta_numero_estudiantes->rowCount() > 0) {
                    
                    $datos_asignacion = $consulta_numero_estudiantes->fetch(PDO::FETCH_ASSOC);
    
                    $total_estudiantes = $datos_asignacion['total_estudiantes'];
    
    
                    if ($id_rol_estudiante != 4) {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Acceso denegado",
                            "Texto" => "El usuario " . $nombre_estudiante . " no tiene el rol adecuado para esta operación.",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit(); 
                    }

                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "". $titulo_proyecto,
                        "Texto" => "hasta aqui.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
    
                    // Verificar si el usuario ya está asignado al proyecto
    
                    $consulta = MainModel::ejecutar_consultas_simples(
                        " SELECT numero_documento 
                        FROM asignar_estudiante_proyecto 
                        WHERE codigo_proyecto = '$codigo'
                        AND numero_documento = '$numero_documento'"
                    );
    
                    if ($consulta->rowCount() > 0) {
                        // Si ya está asignado, enviar un mensaje de alerta
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Usuario ya asignado",
                            "Texto" => "El usuario  " . $nombre_estudiante . " ya está asignado al proyecto " . $codigo . ".",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }
    
                    $consulta = MainModel::ejecutar_consultas_simples(
                        "SELECT numero_documento 
                        FROM asignar_estudiante_proyecto 
                        WHERE numero_documento = '$numero_documento' 
                        AND codigo_proyecto != '$codigo'"
                    );
    
                    if ($consulta->rowCount() > 0) {
                        // Si ya está asignado a otro proyecto, enviar un mensaje de alerta
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Usuario asignado a otro proyecto",
                            "Texto" => "El usuario  " . $nombre_estudiante . " ya está asignado a otro proyecto y no puede ser asignado a este.",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }
                    
                    if($total_estudiantes >= $numero_maximo_estudiantes){
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Número máximo de estudiantes",
                            "Texto" => "El número máximo de estudiantes para este proyecto ha sido alcanzado.",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }
    
                    $datos_asignar = [
                        "codigo_proyecto" => $codigo,
                        "numero_documento" => $numero_documento
                    ];
    
                    $guardar_asignar = ProyectoModelo::asignar_estudiante_proyecto_modelo($datos_asignar);
    
                    if ($guardar_asignar->rowCount() > 0) {
                     
                    $message = "<p>🎉 ¡Lo lograste! Has alcanzado la <b>etapa final de tu proyecto</b>, el último paso antes de hacer realidad tu meta académica. 🚀✨</p>

                    <p>Este es el momento de demostrar todo tu conocimiento, esfuerzo y dedicación. Ahora, cada ajuste, cada análisis y cada decisión marcarán la diferencia en la calidad de tu trabajo. 🔍📚</p>
                    
                    <p>Recuerda, <b>estás a un paso de la meta</b>. Mantén la motivación, confía en tus habilidades y da lo mejor de ti. ¡Tu éxito está más cerca que nunca, y este esfuerzo definirá tu futuro profesional! 💡🔥</p>";
                    


                    include __DIR__ . '/../Mail/enviar-correo.php';

                    $asunto = "Notificación de Asignación de Proyecto";

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
                                       <h2>Asignación de Proyecto</h2>
                                </div>
                                <div class="email-body">
                                    <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                                    '.$message.'
                                    <p>Nos complace informarte que se te ha asignado el siguiente proyecto:</p>
                                    <div class="credentials">
                                        <ul>
                                            <li><b>Código del proyecto:</b> ' . $codigo . '</li>
                                            <li><b>Título del proyecto:</b> ' . $titulo_proyecto . '</li>
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

                    $cuerpo_texto = "Estimado $nombre_usuario $apellido_usuario, nos complace informarte que se te ha asignado el proyecto con el código $codigo y el título $titulo_proyecto. ¡Estamos seguros de que harás un excelente trabajo!";

                    $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);
                    
                    if ($enviado) {
                        $alerta = [
                            "Alerta" => "Recargar",
                            "Titulo" => "Operación exitosa",
                            "Texto" => "El estudiante ha sido asignado al proyecto correctamente.",
                            "Tipo" => "success"
                        ];
                    }else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "El estudiante ha sido asignado al proyecto correctamente. pero no se pudo enviar el correo electrónico al estudiante.",
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

    public function actualizar_proyecto_controlador(){

        $codigo = MainModel::limpiar_cadenas($_POST['codigo_proyecto_upd']);
        $titulo = MainModel::limpiar_cadenas($_POST['titulo_proyecto_upd']);
        $palabrasClaves = MainModel::limpiar_cadenas($_POST['palabras_clavesP_upd']);

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
            "SELECT codigo_proyecto FROM proyectos WHERE codigo_proyecto = '$codigo'"
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
            "codigo_proyecto" => $codigo,
            "titulo_proyecto" => $titulo,
            "palabras_claves" => $palabrasClaves
        ];

        $actualizar_idea = ProyectoModelo::actualizar_proyecto_modelos($datos_idea);
        
        if ($actualizar_idea->rowCount() > 0) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Operación exitosa",
                "Texto" => "Se actualizó el proyecto con éxito",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo actualizar el proyecto",
                "Tipo" => "error"
            ];
        }
        
        echo json_encode($alerta);
        exit();
       

    }

    public function eliminar_estudiante_proyecto(){
        
        $codigo = MainModel::limpiar_cadenas($_POST['codigoDP']);
        $numero_documento = MainModel::limpiar_cadenas($_POST['documento_userDP']);

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
            "SELECT codigo_proyecto FROM proyectos WHERE codigo_proyecto = '$codigo'"
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


        $eliminar_estudiante = ProyectoModelo::eliminar_estudiante_proyecto_modelos($codigo, $numero_documento);
        
        if ($eliminar_estudiante->rowCount() > 0) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Operación exitosa",
                "Texto" => "El estudiante fue eliminado  del proyecto",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo eliminar al estudiante del proyecto",
                "Tipo" => "error"
            ];
        }
        
        echo json_encode($alerta);
        exit();


    }

    public function eliminar_proyecto_controlador(){
        $codigo = MainModel::limpiar_cadenas($_POST['codigo_proyecto_delete']);
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
            "SELECT codigo_proyecto FROM proyectos WHERE codigo_proyecto = '$codigo'"
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

        $eliminar_proyecto = ProyectoModelo::eliminar_proyecto_modelos($codigo);

        if ($eliminar_proyecto->rowCount() > 0) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Operación exitosa",
                "Texto" => "El proyecto fue eliminado",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo eliminar el proyecto",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
        exit();

    }
   
    public function asignar_asesor_proyecto_controlador() {

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

      

        $codigo = MainModel::limpiar_cadenas($_POST['codigo_proyecto_asignar']);
        $numero_documento_profesor = MainModel::limpiar_cadenas($_POST['documento_user_asignar']);
        $numero_documento_user_logueado = MainModel::limpiar_cadenas($_POST['numero_documento_user_logueado']);
        $tipoProyectoAnteproyecto = MainModel::limpiar_cadenas($_POST['tipoProyectoAnteproyecto']);
        $idfacultaProyectoAnteproyecto = MainModel::limpiar_cadenas($_POST['idfacultaProyectoAnteproyecto']);
        $idProgramaProyectoAnteproyecto = MainModel::limpiar_cadenas($_POST['idProgramaProyectoAnteproyecto']);
        

        if (empty($codigo) || empty($numero_documento_profesor) || empty($numero_documento_user_logueado)
        
        || empty($tipoProyectoAnteproyecto) || empty($idfacultaProyectoAnteproyecto) || empty($idProgramaProyectoAnteproyecto) ) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $numero_documento_user_logueado =  MainModel::decryption($numero_documento_user_logueado);
        $tipoProyectoAnteproyecto =  MainModel::decryption($tipoProyectoAnteproyecto);
        $idfacultaProyectoAnteproyecto =  MainModel::decryption($idfacultaProyectoAnteproyecto);
        $idProgramaProyectoAnteproyecto =  MainModel::decryption($idProgramaProyectoAnteproyecto);


        /**************validar si el usuario existe - profesor ********************* */

        $check_profesor = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM usuarios WHERE numero_documento = '$numero_documento_profesor'"
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

        $nombre_usuario_profesor =  $usuario['nombre_usuario'] .' ' .$usuario['apellidos_usuario']; 

        $nombre_usuario = $usuario['nombre_usuario'];

        $apellido_usuario = $usuario['apellidos_usuario'];

        $correo_usuario = $usuario['correo_usuario'];

       
         /**************validar informacion logueado  ********************* */

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
 
        if($rol_usuario_logueado == 1){ /**** administrador */

            /**** filstros del profesor */

            if ($id_rol_usuario!= 5 && $id_rol_usuario!= 6) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Acceso denegado",
                    "Texto" => "El usuario ". $nombre_usuario_profesor . " no es un asesor.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }


            $consulta_facultad = MainModel::ejecutar_consultas_simples(
                "SELECT * FROM facultades WHERE id_facultad = '$idfacultaProyectoAnteproyecto'"
            );
            
            if ($consulta_facultad->rowCount() <= 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Facultad no válida",
                    "Texto" => "La facultad o programa es invalido. Por favor, ingrese un valor válido.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            // Consulta para verificar si el id_programa existe en la tabla `programas_academicos`

                $consulta_programa = MainModel::ejecutar_consultas_simples(
                    "SELECT * FROM programas_academicos WHERE id_programa = '$idProgramaProyectoAnteproyecto'"
                );
                
                if ($consulta_programa->rowCount() <= 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Programa no válido",
                        "Texto" => "La facultad o programa es invalido. Por favor, ingrese un valor válido.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                
                /***************************** registrar información asesor externo ******************************* */

                if( $id_rol_usuario == 6){

                 
                    if($tipoProyectoAnteproyecto == 1){ /*** anteproyecto */

                        $consulta_anteproyecto = MainModel::ejecutar_consultas_simples(
                            "SELECT * FROM anteproyectos WHERE codigo_anteproyecto = '$codigo'"
                        );
                        
                        if ($consulta_anteproyecto->rowCount() <= 0) {
                            $alerta = [
                                "Alerta" => "simple",
                                "Titulo" => "Código de proyecto no válido",
                                "Texto" => "El código de anteproyecto es invalido. Por favor, ingrese un valor válido.",
                                "Tipo" => "error"
                            ];
                            echo json_encode($alerta);
                            exit();
                        }
        
                        $datos_anteproyecto = $consulta_anteproyecto->fetch(PDO::FETCH_ASSOC);
        
                        $titulo_anteproyecto = $datos_anteproyecto['titulo_anteproyecto']; 
        
        
                            // Consulta para verificar si el número de documento ya tiene asignado el código de proyecto
                            $consulta_asignacion = MainModel::ejecutar_consultas_simples(
                                "SELECT * FROM Asignar_asesor_anteproyecto_proyecto WHERE numero_documento = '$numero_documento_profesor' AND codigo_proyecto = '$codigo'"
                            );
        
                            if ($consulta_asignacion->rowCount() > 0) {
                                $alerta = [
                                    "Alerta" => "simple",
                                    "Titulo" => "Asignación existente",
                                    "Texto" => "El número de documento de ". $nombre_usuario_profesor ." ya tiene asignado un anteproyecto con el código ". $codigo,
                                    "Tipo" => "error"
                                ];
                                echo json_encode($alerta);
                                exit();
                            }
        
                            $datos_asignar = [
                                "codigo_proyecto" => $codigo,
                                "numero_documento" => $numero_documento_profesor
                            ];
        
        
                            $guardar_asignar = ProyectoModelo::Asignar_asesor_externo_anteproyecto_proyecto_modelos($datos_asignar);

                            if ($guardar_asignar->rowCount() >= 1) {

                                $alerta = [
                                    "Alerta" => "Recargar",
                                    "Titulo" => "Registro exitoso",
                                     "Texto" => "se ha  asignado correctamente el director externo al anteproyecto",
                                    "Tipo" => "success"
                                ];
                                
                                echo json_encode($alerta);
                                exit();

                            }else {
                                $alerta = [
                                   "Alerta" => "simple",
                                   "Titulo" => "Ocurrió un error inesperado",
                                    "Texto" => "No se pudo asignar el directo externo al anteproyecto",
                                   "Tipo" => "error"
                               ];
                               
                               echo json_encode($alerta);
                               exit();
                           }

                    }else if($tipoProyectoAnteproyecto == 2) {

                        
                        $consulta_anteproyecto = MainModel::ejecutar_consultas_simples(
                            "SELECT * FROM proyectos WHERE codigo_proyecto = '$codigo'"
                        );
                        
                        if ($consulta_anteproyecto->rowCount() <= 0) {
                            $alerta = [
                                "Alerta" => "simple",
                                "Titulo" => "Código de proyecto no válido",
                                "Texto" => "El código de proyecto es invalido. Por favor, ingrese un valor válido.",
                                "Tipo" => "error"
                            ];
                            echo json_encode($alerta);
                            exit();
                        }

                        $datos_proyecto = $consulta_anteproyecto->fetch(PDO::FETCH_ASSOC);

                        $titulo_proyecto = $datos_proyecto['titulo_proyecto']; 


                            // Consulta para verificar si el número de documento ya tiene asignado el código de proyecto
                            $consulta_asignacion = MainModel::ejecutar_consultas_simples(
                                "SELECT * FROM Asignar_asesor_anteproyecto_proyecto WHERE numero_documento = '$numero_documento_profesor' AND codigo_proyecto = '$codigo'"
                            );

                            if ($consulta_asignacion->rowCount() > 0) {
                                $alerta = [
                                    "Alerta" => "simple",
                                    "Titulo" => "Asignación existente",
                                    "Texto" => "El número de documento de ". $nombre_usuario_profesor ." ya tiene asignado un proyecto con el código ". $codigo,
                                    "Tipo" => "error"
                                ];
                                echo json_encode($alerta);
                                exit();
                            }

                            
                            $datos_asignar = [
                                "codigo_proyecto" => $codigo,
                                "numero_documento" => $numero_documento_profesor
                            ];

                            $guardar_asignar = ProyectoModelo::Asignar_asesor_externo_anteproyecto_proyecto_modelos($datos_asignar);

                            if ($guardar_asignar->rowCount() >= 1) {

                                $alerta = [
                                    "Alerta" => "Recargar",
                                    "Titulo" => "Registro exitoso",
                                     "Texto" => "se ha asignado correctamente el director externo  al proyecto",
                                    "Tipo" => "success"
                                ];
                                
                                echo json_encode($alerta);
                                exit();

                            }else {
                                $alerta = [
                                   "Alerta" => "simple",
                                   "Titulo" => "Ocurrió un error inesperado",
                                    "Texto" => "No se pudo agregar el usuario",
                                   "Tipo" => "error"
                               ];
                               
                               echo json_encode($alerta);
                               exit();
                           }
                            

                    }

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
                        WHERE u.numero_documento = '$numero_documento_profesor'"
                );
                
                // Verificar si el profesor tiene asignadas facultades y programas
                if ($check_profesor_facultad->rowCount() <= 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado",
                        "Texto" => "El número de documento de ". $nombre_usuario_profesor ." no tiene asignada ninguna facultad y programa.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
                
                // Convertir las facultades y programas asociados al profesor en un arreglo
                $facultades_programas_profesor = $check_profesor_facultad->fetchAll(PDO::FETCH_ASSOC);
                
                // Validar si el proyecto pertenece a alguna de las facultades y programas asignados
                $validacion_exitosa = false;
                
                foreach ($facultades_programas_profesor as $facultad_programa) {
                    if (
                        $facultad_programa['id_facultad'] == $idfacultaProyectoAnteproyecto &&
                        $facultad_programa['id_programa'] == $idProgramaProyectoAnteproyecto
                    ) {
                        $validacion_exitosa = true;
                        break; // Salir del bucle si hay una coincidencia
                    }
                }
                
                // Si no hay coincidencia, mostrar un error
                if (!$validacion_exitosa) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Facultad y programa no válidos",
                        "Texto" => "El programa del proyecto o anteproyecto no coincide con ninguna facultad y programa asignado al profesor.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
                
                // Si llega aquí, la validación fue exitosa y puedes continuar con tu lógica
                


            

            /************** validamos que tipo de proyecto es ***************** */
            if ($tipoProyectoAnteproyecto != 1 && $tipoProyectoAnteproyecto != 2) {
                // El tipo de proyecto no es válido, mostrar un mensaje de error o detener la ejecución
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Tipo de Proyecto no válido",
                    "Texto" => "El tipo de proyecto o anteproyecto es invalido. Por favor, ingrese un valor válido.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit(); 
            }



            /************** validamos que tipo de proyecto es ***************** */

           if($tipoProyectoAnteproyecto ==1){

        
            /*********** validamos que id facultad exista en la base de datos**********/

                // validar si el codigo pertenece a un anteproyecto 

                $consulta_anteproyecto = MainModel::ejecutar_consultas_simples(
                    "SELECT * FROM anteproyectos WHERE codigo_anteproyecto = '$codigo'"
                );
                
                if ($consulta_anteproyecto->rowCount() <= 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Código de proyecto no válido",
                        "Texto" => "El código de anteproyecto es invalido. Por favor, ingrese un valor válido.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                $datos_anteproyecto = $consulta_anteproyecto->fetch(PDO::FETCH_ASSOC);

                $titulo_anteproyecto = $datos_anteproyecto['titulo_anteproyecto']; 


                    // Consulta para verificar si el número de documento ya tiene asignado el código de proyecto
                    $consulta_asignacion = MainModel::ejecutar_consultas_simples(
                        "SELECT * FROM Asignar_asesor_anteproyecto_proyecto WHERE numero_documento = '$numero_documento_profesor' AND codigo_proyecto = '$codigo'"
                    );

                    if ($consulta_asignacion->rowCount() > 0) {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Asignación existente",
                            "Texto" => "El número de documento de ". $nombre_usuario_profesor ." ya tiene asignado un anteproyecto con el código ". $codigo,
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }
                  

                    $datos_asignar = [
                        "codigo_proyecto" => $codigo,
                        "numero_documento" => $numero_documento_profesor
                    ];


                    $guardar_asignar = ProyectoModelo::Asignar_asesor_anteproyecto_proyecto_modelos($datos_asignar);

                    if ($guardar_asignar->rowCount() > 0) {

                    
                    /****************************************************************** */

                    $message = "<p>📢 ¡Tienes una nueva asignación! Se te ha confiado un <b>anteproyecto</b>, una oportunidad para guiar y fortalecer el desarrollo académico de un estudiante. 🎓✨</p>

                    <p>Tu conocimiento y experiencia serán clave en este proceso. Acompañarás, orientarás y brindarás las herramientas necesarias para que este trabajo alcance su máximo potencial. Cada consejo que des y cada retroalimentación que ofrezcas marcarán la diferencia. 📖💡</p>
                    
                    <p><b>Tu labor es fundamental.</b> Gracias a tu guía, los futuros profesionales pueden enfrentar sus desafíos con seguridad y avanzar con éxito. ¡Sigamos formando grandes talentos! 🚀🔥</p>";
                    

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
                                    <p>Confiamos en que su experiencia y orientación serán fundamentales para el éxito de este proyecto.</p>
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
                        
                        $correosEnviados = true; // Variable para comprobar si todos los correos se enviaron con éxito
                        
                        // Iterar sobre cada estudiante y enviar correos
                        while ($row = $consulta_anteproyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {
                            // Extraer los valores del resultado
                            $nombre_estudiante = $row['nombre_usuario'];
                            $apellido_estudiante = $row['apellidos_usuario'];
                            $correo_usuario_estudiante = $row['correo_usuario'];
            

                /************************************************************************* */
                         

                $message = "<p>📢 ¡Buenas noticias! Se te ha asignado un <b>asesor</b> para tu anteproyecto, quien te acompañará en este camino académico y te brindará el apoyo necesario para desarrollar tu trabajo con éxito. 🎓✨</p>

                <p>Tu asesor será una guía clave para resolver dudas, mejorar tu propuesta y asegurarte de cumplir con los estándares académicos. Aprovecha cada sesión, cada consejo y cada retroalimentación para fortalecer tu proyecto. 📖💡</p>
                
                <p><b>Recuerda, no estás solo en este proceso.</b> Con dedicación, esfuerzo y el apoyo de tu asesor, estarás cada vez más cerca de alcanzar tu meta profesional. ¡Es hora de dar lo mejor de ti! 🚀🔥</p>";
                

                    $asunto = "Notificación de Asignación de Asesor para Anteproyecto";

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
                                    <p><b>Estimado  ' . $nombre_estudiante . ' ' . $apellido_estudiante . ',</b></p>
                                    '.$message.'
                                    <p>Confiamos en que su experiencia y orientación serán fundamentales para el éxito de este proyecto.</p>
                                    <div class="credentials">
                                        <ul>
                                            <li><b>Código del Anteproyecto:</b> ' . $codigo . '</li>
                                            <li><b>Título del Anteproyecto:</b> ' . $titulo_anteproyecto . '</li>
                                            <li><b>Nombre del Asesor:</b> ' . $nombre_usuario_profesor . '</li>
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
                       
                        $cuerpo_texto = "Estimado $nombre_estudiante $apellido_estudiante, nos complace informarte que se te ha asignado el anteproyecto con el código $codigo y el título $titulo_anteproyecto. ¡Estamos seguros de que harás un excelente trabajo!";
                        
                            // Intentar enviar el correo y verificar si se envió con éxito
                            $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);
                        
                            if (!$enviado2) {
                                $correosEnviados = false; // Si algún correo no se envía, marcar como falso
                            }
                        }
                        
                        // Mostrar mensaje de confirmación si todos los correos se enviaron correctamente
                        if ($correosEnviados) {
                            $mensaje = [
                                "Alerta" => "Recargar",
                                "Titulo" => "Correos Enviados",
                                "Texto" => "Se ha notificado al asesor y a todos los estudiantes asignados.",
                                "Tipo" => "success"
                            ];
                        } else {
                            $mensaje = [
                                "Alerta" => "simple",
                                "Titulo" => "Error en el Envío",
                                "Texto" => "Hubo un problema al enviar algunos correos. Por favor, verifique los correos no enviados.",
                                "Tipo" => "error"
                            ];
                        }
                        
                        echo json_encode($mensaje);

                        exit();

                    }else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Error al intentar enviar correo",
                            "Texto" => "Se Asigno correctamnete el anteproyecto al profesor, pero no se pudo enviar el correo electrónico",
                            "Tipo" => "error"
                        ];
                    }

                    echo json_encode($alerta);
                    exit();


                       
                    } else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Error al intentar guardar",
                            "Texto" => "No se pudo guardar la información en la base de datos. Por favor, intente nuevamente.",
                            "Tipo" => "error"
                        ];
                    
                    }

                    echo json_encode($alerta);
                    exit();
                        

                



           }elseif($tipoProyectoAnteproyecto ==2){

                // validar si el codigo pertenece a un anteproyecto 

                $consulta_anteproyecto = MainModel::ejecutar_consultas_simples(
                    "SELECT * FROM proyectos WHERE codigo_proyecto = '$codigo'"
                );
                
                if ($consulta_anteproyecto->rowCount() <= 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Código de proyecto no válido",
                        "Texto" => "El código de proyecto es invalido. Por favor, ingrese un valor válido.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                $datos_proyecto = $consulta_anteproyecto->fetch(PDO::FETCH_ASSOC);

                $titulo_proyecto = $datos_proyecto['titulo_proyecto']; 


                    // Consulta para verificar si el número de documento ya tiene asignado el código de proyecto
                    $consulta_asignacion = MainModel::ejecutar_consultas_simples(
                        "SELECT * FROM Asignar_asesor_anteproyecto_proyecto WHERE numero_documento = '$numero_documento_profesor' AND codigo_proyecto = '$codigo'"
                    );

                    if ($consulta_asignacion->rowCount() > 0) {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Asignación existente",
                            "Texto" => "El número de documento de ". $nombre_usuario_profesor ." ya tiene asignado un proyecto con el código ". $codigo,
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }
                  
                    $datos_asignar = [
                        "codigo_proyecto" => $codigo,
                        "numero_documento" => $numero_documento_profesor
                    ];

                    $guardar_asignar = ProyectoModelo::Asignar_asesor_anteproyecto_proyecto_modelos($datos_asignar);

                    if ($guardar_asignar->rowCount() > 0) {
                        
                        /**************** proyectos ******************************* */

                    /****************************************************************** */

                    $message = "<p>📢 ¡Tienes una nueva asignación! Se te ha confiado un <b>proyecto</b>, una oportunidad para guiar y fortalecer el desarrollo académico de un estudiante. 🎓✨</p>

                    <p>Tu conocimiento y experiencia serán clave en este proceso. Acompañarás, orientarás y brindarás las herramientas necesarias para que este trabajo alcance su máximo potencial. Cada consejo que des y cada retroalimentación que ofrezcas marcarán la diferencia. 📖💡</p>
                    
                    <p><b>Tu labor es fundamental.</b> Gracias a tu guía, los futuros profesionales pueden enfrentar sus desafíos con seguridad y avanzar con éxito. ¡Sigamos formando grandes talentos! 🚀🔥</p>";
                    

                    include __DIR__ . '/../Mail/enviar-correo.php';

                    $asunto = "Notificación de Asignación de Proyecto";


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
                                         <h2>Asignación de Proyecto</h2>
                                </div>
                                <div class="email-body">
                                    <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                                    '.$message.'
                                    <p>Confiamos en que su experiencia y orientación serán fundamentales para el éxito de este proyecto.</p>
                                    <div class="credentials">
                                        <ul>
                                            <li><b>Código del Proyecto:</b> ' . $codigo . '</li>
                                            <li><b>Título del Proyecto:</b> ' . $titulo_proyecto . '</li>
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

                        $cuerpo_texto = "Estimado $nombre_usuario $apellido_usuario, nos complace informarte que se te ha asignado el proyecto con el código $codigo y el título $titulo_proyecto. ¡Estamos seguros de que harás un excelente trabajo!";

                    $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);
                    
                    if ($enviado) {
                       
                        $consulta_proyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                            "SELECT 
                                a.codigo_proyecto,
                                u.numero_documento,
                                u.nombre_usuario,
                                u.apellidos_usuario,
                                u.correo_usuario,
                                u.telefono_usuario
                            FROM asignar_estudiante_proyecto a
                            INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                            WHERE a.codigo_proyecto = '$codigo'"
                        );
                        
                        $correosEnviados = true; // Variable para comprobar si todos los correos se enviaron con éxito
                        
                        // Iterar sobre cada estudiante y enviar correos
                        while ($row = $consulta_proyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {
                            // Extraer los valores del resultado
                            $nombre_estudiante = $row['nombre_usuario'];
                            $apellido_estudiante = $row['apellidos_usuario'];
                            $correo_usuario_estudiante = $row['correo_usuario'];
                        
                        
                            $message = "<p>📢 ¡Buenas noticias! Se te ha asignado un <b>asesor</b> para tu proyecto, quien te acompañará en este camino académico y te brindará el apoyo necesario para desarrollar tu trabajo con éxito. 🎓✨</p>

                            <p>Tu asesor será una guía clave para resolver dudas, mejorar tu propuesta y asegurarte de cumplir con los estándares académicos. Aprovecha cada sesión, cada consejo y cada retroalimentación para fortalecer tu proyecto. 📖💡</p>
                            
                            <p><b>Recuerda, no estás solo en este proceso.</b> Con dedicación, esfuerzo y el apoyo de tu asesor, estarás cada vez más cerca de alcanzar tu meta profesional. ¡Es hora de dar lo mejor de ti! 🚀🔥</p>";
                            
            
                                $asunto = "Notificación de Asignación de Asesor para Proyecto";
            
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
                                                   <h2>Asignación de Proyecto</h2>
                                            </div>
                                            <div class="email-body">
                                                <p><b>Estimado  ' . $nombre_estudiante . ' ' . $apellido_estudiante . ',</b></p>
                                                '.$message.'
                                                <p>Confiamos en que su experiencia y orientación serán fundamentales para el éxito de este proyecto.</p>
                                                <div class="credentials">
                                                    <ul>
                                                        <li><b>Código del proyecto:</b> ' . $codigo . '</li>
                                                        <li><b>Título del proyecto:</b> ' . $titulo_proyecto . '</li>
                                                        <li><b>Nombre del Asesor:</b> ' . $nombre_usuario_profesor . '</li>
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
                                   
                                    $cuerpo_texto = "Estimado $nombre_estudiante $apellido_estudiante, nos complace informarte que se te ha asignado el anteproyecto con el código $codigo y el título $titulo_proyecto. ¡Estamos seguros de que harás un excelente trabajo!";
                        
                            // Intentar enviar el correo y verificar si se envió con éxito
                            $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);
                        
                            if (!$enviado2) {
                                $correosEnviados = false; // Si algún correo no se envía, marcar como falso
                            }
                        }
                        
                        // Mostrar mensaje de confirmación si todos los correos se enviaron correctamente
                        if ($correosEnviados) {
                            $mensaje = [
                                "Alerta" => "Recargar",
                                "Titulo" => "Correos Enviados",
                                "Texto" => "Se ha notificado al asesor y a todos los estudiantes asignados.",
                                "Tipo" => "success"
                            ];
                        } else {
                            $mensaje = [
                                "Alerta" => "simple",
                                "Titulo" => "Error en el Envío",
                                "Texto" => "Hubo un problema al enviar algunos correos. Por favor, verifique los correos no enviados.",
                                "Tipo" => "error"
                            ];
                        }
                        
                        echo json_encode($mensaje);

                        exit();
                        
                        

                    }else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Error al intentar enviar correo",
                            "Texto" => "Se Asigno correctamnete el anteproyecto al profesor, pero no se pudo enviar el correo electrónico",
                            "Tipo" => "error"];
                    }

                    echo json_encode($alerta);
                    exit();

                    /*************fin enviar correo************** */

                       
                    } else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Error al intentar guardar",
                            "Texto" => "No se pudo guardar la información en la base de datos. Por favor, intente nuevamente.",
                            "Tipo" => "error"
                        ];
                    
                    }

                    echo json_encode($alerta);
                    exit();

            }else{
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El tipo de proyecto no es válido.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            

        
           /******************************************************************************* */

        }elseif ($rol_usuario_logueado == 2) { /*****coordinador */

            if ($id_rol_usuario != 5 && $id_rol_usuario != 6) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Acceso denegado",
                    "Texto" => "El usuario " . $nombre_usuario_profesor . " no tiene el rol adecuado para ser un asesor.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
            
            // Obtener las facultades y programas del coordinador logueado
            $check_coordinador_facultades_programas = MainModel::ejecutar_consultas_simples(
                "SELECT 
                    auf.id_facultad,
                    auf.id_programa
                FROM Asignar_usuario_facultades auf
                WHERE auf.numero_documento = '$numero_documento_user_logueado'"
            );

            // Validar si se encontraron registros para el coordinador logueado
            if ($check_coordinador_facultades_programas->rowCount() <= 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El coordinador no tiene asignada ninguna facultad ni programa.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            // Guardar las combinaciones de facultades y programas del coordinador
            $coordinador_facultades_programas = [];
            while ($row = $check_coordinador_facultades_programas->fetch(PDO::FETCH_ASSOC)) {
                $coordinador_facultades_programas[] = $row['id_facultad'] . '-' . $row['id_programa'];
            }

            // Obtener las facultades y programas del profesor
            $check_profesor_facultades_programas = MainModel::ejecutar_consultas_simples(
                "SELECT 
                    auf.id_facultad,
                    auf.id_programa
                FROM Asignar_usuario_facultades auf
                WHERE auf.numero_documento = '$numero_documento_profesor'"
            );

            // Validar si se encontraron registros para el profesor
            if ($check_profesor_facultades_programas->rowCount() <= 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El profesor no tiene asignada ninguna facultad ni programa.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            // Guardar las combinaciones de facultades y programas del profesor
            $profesor_facultades_programas = [];
            while ($row = $check_profesor_facultades_programas->fetch(PDO::FETCH_ASSOC)) {
                $profesor_facultades_programas[] = $row['id_facultad'] . '-' . $row['id_programa'];
            }

            // Validar si hay alguna coincidencia entre las combinaciones de facultades y programas
            $coincide_combinacion = false;

            foreach ($profesor_facultades_programas as $facultad_programa_profesor) {
                if (in_array($facultad_programa_profesor, $coordinador_facultades_programas)) {
                    $coincide_combinacion = true;
                    break; // Salir del ciclo si se encuentra una coincidencia
                }
            }

            // Si no hay coincidencias, mostrar un mensaje de error
            if (!$coincide_combinacion) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Facultades y programas no coinciden",
                    "Texto" => "Las facultades y programas asignados al profesor no coinciden con ninguna de las asignaciones del coordinador logueado.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            // Si llega aquí, las combinaciones coinciden y puedes continuar con la lógica



            /************** validamos que tipo de proyecto es ***************** */
            if ($tipoProyectoAnteproyecto != 1 && $tipoProyectoAnteproyecto != 2) {
                // El tipo de proyecto no es válido, mostrar un mensaje de error o detener la ejecución
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Tipo de Proyecto no válido",
                    "Texto" => "El tipo de proyecto o anteproyecto es invalido. Por favor, ingrese un valor válido.",
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
                    WHERE u.numero_documento = '$numero_documento_profesor'"
            );
            
            // Verificar si el profesor tiene asignadas facultades y programas
            if ($check_profesor_facultad->rowCount() <= 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El número de documento de ". $nombre_usuario_profesor ." no tiene asignada ninguna facultad y programa.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
            
            // Convertir las facultades y programas asociados al profesor en un arreglo
            $facultades_programas_profesor = $check_profesor_facultad->fetchAll(PDO::FETCH_ASSOC);
            
            // Validar si el proyecto pertenece a alguna de las facultades y programas asignados
            $validacion_exitosa = false;
            
            foreach ($facultades_programas_profesor as $facultad_programa) {
                if (
                    $facultad_programa['id_facultad'] == $idFacultadProyectoAnteproyecto &&
                    $facultad_programa['id_programa'] == $idProgramaProyectoAnteproyecto
                ) {
                    $validacion_exitosa = true;
                    break; // Salir del bucle si hay una coincidencia
                }
            }
            
            // Si no hay coincidencia, mostrar un error
            if (!$validacion_exitosa) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Facultad y programa no válidos",
                    "Texto" => "El programa del proyecto o anteproyecto no coincide con ninguna facultad y programa asignado al profesor.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
            
            // Si llega aquí, la validación fue exitosa y puedes continuar con tu lógica
            

            

            if($tipoProyectoAnteproyecto ==1){

                /*********** validamos que id facultad exista en la base de datos**********/
                

                $consulta_facultad = MainModel::ejecutar_consultas_simples(
                    "SELECT * FROM facultades WHERE id_facultad = '$idfacultaProyectoAnteproyecto'"
                );
                
                if ($consulta_facultad->rowCount() <= 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Facultad no válida",
                        "Texto" => "La facultad o programa es invalido. Por favor, ingrese un valor válido.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                    // Consulta para verificar si el id_programa existe en la tabla `programas_academicos`

                    $consulta_programa = MainModel::ejecutar_consultas_simples(
                        "SELECT * FROM programas_academicos WHERE id_programa = '$idProgramaProyectoAnteproyecto'"
                    );
                    
                    if ($consulta_programa->rowCount() <= 0) {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Programa no válido",
                            "Texto" => "La facultad o programa es invalido. Por favor, ingrese un valor válido.",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }

            
                    $consulta_anteproyecto = MainModel::ejecutar_consultas_simples(
                        "SELECT * FROM anteproyectos WHERE codigo_anteproyecto = '$codigo'"
                    );
                    
                    if ($consulta_anteproyecto->rowCount() <= 0) {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Código de proyecto no válido",
                            "Texto" => "El código de anteproyecto es invalido. Por favor, ingrese un valor válido.",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }

                    $datos_anteproyecto = $consulta_anteproyecto->fetch(PDO::FETCH_ASSOC);

                    $titulo_anteproyecto = $datos_anteproyecto['titulo_anteproyecto']; 

                    
            

                
                    // Consulta para verificar si el número de documento ya tiene asignado el código de proyecto
                    $consulta_asignacion = MainModel::ejecutar_consultas_simples(
                        "SELECT * FROM Asignar_asesor_anteproyecto_proyecto WHERE numero_documento = '$numero_documento_profesor' AND codigo_proyecto = '$codigo'" 
                    );

                    if ($consulta_asignacion->rowCount() > 0) {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Asignación existente",
                            "Texto" => "El número de documento de ". $nombre_usuario_profesor ." ya tiene asignado un anteproyecto con el código ". $codigo,
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }

    

                    $datos_asignar = [
                        "codigo_proyecto" => $codigo,
                        "numero_documento" => $numero_documento_profesor
                    ];

                    $guardar_asignar = ProyectoModelo::Asignar_asesor_anteproyecto_proyecto_modelos($datos_asignar);

                    if ($guardar_asignar->rowCount() > 0) {
                       
                          /****************************************************************** */

                    $message = "<p>📢 ¡Tienes una nueva asignación! Se te ha confiado un <b>anteproyecto</b>, una oportunidad para guiar y fortalecer el desarrollo académico de un estudiante. 🎓✨</p>

                    <p>Tu conocimiento y experiencia serán clave en este proceso. Acompañarás, orientarás y brindarás las herramientas necesarias para que este trabajo alcance su máximo potencial. Cada consejo que des y cada retroalimentación que ofrezcas marcarán la diferencia. 📖💡</p>
                    
                    <p><b>Tu labor es fundamental.</b> Gracias a tu guía, los futuros profesionales pueden enfrentar sus desafíos con seguridad y avanzar con éxito. ¡Sigamos formando grandes talentos! 🚀🔥</p>";
                    

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
                                    <p>Confiamos en que su experiencia y orientación serán fundamentales para el éxito de este proyecto.</p>
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
                        
                        $correosEnviados = true; // Variable para comprobar si todos los correos se enviaron con éxito
                        
                        // Iterar sobre cada estudiante y enviar correos
                        while ($row = $consulta_anteproyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {
                            // Extraer los valores del resultado
                            $nombre_estudiante = $row['nombre_usuario'];
                            $apellido_estudiante = $row['apellidos_usuario'];
                            $correo_usuario_estudiante = $row['correo_usuario'];
                        
                            $message = "<p>📢 ¡Buenas noticias! Se te ha asignado un <b>asesor</b> para tu anteproyecto, quien te acompañará en este camino académico y te brindará el apoyo necesario para desarrollar tu trabajo con éxito. 🎓✨</p>

                            <p>Tu asesor será una guía clave para resolver dudas, mejorar tu propuesta y asegurarte de cumplir con los estándares académicos. Aprovecha cada sesión, cada consejo y cada retroalimentación para fortalecer tu proyecto. 📖💡</p>
                            
                            <p><b>Recuerda, no estás solo en este proceso.</b> Con dedicación, esfuerzo y el apoyo de tu asesor, estarás cada vez más cerca de alcanzar tu meta profesional. ¡Es hora de dar lo mejor de ti! 🚀🔥</p>";
                            
            
                                $asunto = "Notificación de Asignación de Asesor para Anteproyecto";
            
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
                                                <p><b>Estimado  ' . $nombre_estudiante . ' ' . $apellido_estudiante . ',</b></p>
                                                '.$message.'
                                                <p>Confiamos en que su experiencia y orientación serán fundamentales para el éxito de este proyecto.</p>
                                                <div class="credentials">
                                                    <ul>
                                                        <li><b>Código del Anteproyecto:</b> ' . $codigo . '</li>
                                                        <li><b>Título del Anteproyecto:</b> ' . $titulo_anteproyecto . '</li>
                                                        <li><b>Nombre del Asesor:</b> ' . $nombre_usuario_profesor . '</li>
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
                                   
                                    $cuerpo_texto = "Estimado $nombre_estudiante $apellido_estudiante, nos complace informarte que se te ha asignado el anteproyecto con el código $codigo y el título $titulo_anteproyecto. ¡Estamos seguros de que harás un excelente trabajo!";
                                    
                        
                            // Intentar enviar el correo y verificar si se envió con éxito
                            $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);
                        
                            if (!$enviado2) {
                                $correosEnviados = false; // Si algún correo no se envía, marcar como falso
                            }
                        }
                        
                        // Mostrar mensaje de confirmación si todos los correos se enviaron correctamente
                        if ($correosEnviados) {
                            $mensaje = [
                                "Alerta" => "Recargar",
                                "Titulo" => "Correos Enviados",
                                "Texto" => "Se ha notificado al asesor y a todos los estudiantes asignados.",
                                "Tipo" => "success"
                            ];
                        } else {
                            $mensaje = [
                                "Alerta" => "simple",
                                "Titulo" => "Error en el Envío",
                                "Texto" => "Hubo un problema al enviar algunos correos. Por favor, verifique los correos no enviados.",
                                "Tipo" => "error"
                            ];
                        }
                        
                        echo json_encode($mensaje);

                        exit();

                    }else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Error al intentar enviar correo",
                            "Texto" => "Se Asigno correctamnete el anteproyecto al profesor, pero no se pudo enviar el correo electrónico",
                            "Tipo" => "error"
                        ];
                    }

                    echo json_encode($alerta);
                    exit();

                        
                    
                    } else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Error al intentar guardar",
                            "Texto" => "No se pudo guardar la información en la base de datos. Por favor, intente nuevamente.",
                            "Tipo" => "error"
                        ];
                    
                    }

                    echo json_encode($alerta);
                    exit();

                



            }elseif($tipoProyectoAnteproyecto ==2){


                  /*********** validamos que id facultad exista en la base de datos**********/

                  $consulta_facultad = MainModel::ejecutar_consultas_simples(
                    "SELECT * FROM facultades WHERE id_facultad = '$idfacultaProyectoAnteproyecto'"
                );
                
                if ($consulta_facultad->rowCount() <= 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Facultad no válida",
                        "Texto" => "La facultad o programa es invalido. Por favor, ingrese un valor válido.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                // Consulta para verificar si el id_programa existe en la tabla `programas_academicos`

                    $consulta_programa = MainModel::ejecutar_consultas_simples(
                        "SELECT * FROM programas_academicos WHERE id_programa = '$idProgramaProyectoAnteproyecto'"
                    );
                    
                    if ($consulta_programa->rowCount() <= 0) {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Programa no válido",
                            "Texto" => "La facultad o programa es invalido. Por favor, ingrese un valor válido.",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }

                   
                    // validar si el codigo pertenece a un anteproyecto 

                    $consulta_anteproyecto = MainModel::ejecutar_consultas_simples(
                        "SELECT * FROM proyectos WHERE codigo_proyecto = '$codigo'"
                    );
                    
                    if ($consulta_anteproyecto->rowCount() <= 0) {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Código de proyecto no válido",
                            "Texto" => "El código de proyecto es invalido. Por favor, ingrese un valor válido.",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }

                    $datos_proyecto = $consulta_anteproyecto->fetch(PDO::FETCH_ASSOC);

                    $titulo_proyecto = $datos_proyecto['titulo_proyecto']; 
                   

                        // Consulta para verificar si el número de documento ya tiene asignado el código de proyecto
                        $consulta_asignacion = MainModel::ejecutar_consultas_simples(
                            "SELECT * FROM Asignar_asesor_anteproyecto_proyecto WHERE numero_documento = '$numero_documento_profesor' AND codigo_proyecto = '$codigo'"
                        );


                        if ($consulta_asignacion->rowCount() > 0) {
                            $alerta = [
                                "Alerta" => "simple",
                                "Titulo" => "Asignación existente",
                                "Texto" => "El número de documento de ". $nombre_usuario_profesor ." ya tiene asignado un proyecto con el código ". $codigo,
                                "Tipo" => "error"
                            ];
                            echo json_encode($alerta);
                            exit();
                        }


                        $datos_asignar = [
                            "codigo_proyecto" => $codigo,
                            "numero_documento" => $numero_documento_profesor
                        ];

                        $guardar_asignar = ProyectoModelo::Asignar_asesor_anteproyecto_proyecto_modelos($datos_asignar);

                        if ($guardar_asignar->rowCount() > 0) {
                            
                             /**************** proyectos ******************************* */

                             $message = "<p>📢 ¡Tienes una nueva asignación! Se te ha confiado un <b>proyecto</b>, una oportunidad para guiar y fortalecer el desarrollo académico de un estudiante. 🎓✨</p>

                             <p>Tu conocimiento y experiencia serán clave en este proceso. Acompañarás, orientarás y brindarás las herramientas necesarias para que este trabajo alcance su máximo potencial. Cada consejo que des y cada retroalimentación que ofrezcas marcarán la diferencia. 📖💡</p>
                             
                             <p><b>Tu labor es fundamental.</b> Gracias a tu guía, los futuros profesionales pueden enfrentar sus desafíos con seguridad y avanzar con éxito. ¡Sigamos formando grandes talentos! 🚀🔥</p>";
                             
         
                             include __DIR__ . '/../Mail/enviar-correo.php';
         
                             $asunto = "Notificación de Asignación de Proyecto";
         
         
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
                                                  <h2>Asignación de Proyecto</h2>
                                         </div>
                                         <div class="email-body">
                                             <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                                             '.$message.'
                                             <p>Confiamos en que su experiencia y orientación serán fundamentales para el éxito de este proyecto.</p>
                                             <div class="credentials">
                                                 <ul>
                                                     <li><b>Código del Proyecto:</b> ' . $codigo . '</li>
                                                     <li><b>Título del Proyecto:</b> ' . $titulo_proyecto . '</li>
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
         
                                 $cuerpo_texto = "Estimado $nombre_usuario $apellido_usuario, nos complace informarte que se te ha asignado el proyecto con el código $codigo y el título $titulo_proyecto. ¡Estamos seguros de que harás un excelente trabajo!";

                        $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);
                        
                        if ($enviado) {
                        
                            $consulta_proyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                                "SELECT 
                                    a.codigo_proyecto,
                                    u.numero_documento,
                                    u.nombre_usuario,
                                    u.apellidos_usuario,
                                    u.correo_usuario,
                                    u.telefono_usuario
                                FROM asignar_estudiante_proyecto a
                                INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                                WHERE a.codigo_proyecto = '$codigo'"
                            );
                            
                            $correosEnviados = true; // Variable para comprobar si todos los correos se enviaron con éxito
                            
                            // Iterar sobre cada estudiante y enviar correos
                            while ($row = $consulta_proyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {
                                // Extraer los valores del resultado
                                $nombre_estudiante = $row['nombre_usuario'];
                                $apellido_estudiante = $row['apellidos_usuario'];
                                $correo_usuario_estudiante = $row['correo_usuario'];
                            
                            
                                $message = "<p>📢 ¡Buenas noticias! Se te ha asignado un <b>asesor</b> para tu proyecto, quien te acompañará en este camino académico y te brindará el apoyo necesario para desarrollar tu trabajo con éxito. 🎓✨</p>

                                <p>Tu asesor será una guía clave para resolver dudas, mejorar tu propuesta y asegurarte de cumplir con los estándares académicos. Aprovecha cada sesión, cada consejo y cada retroalimentación para fortalecer tu proyecto. 📖💡</p>
                                
                                <p><b>Recuerda, no estás solo en este proceso.</b> Con dedicación, esfuerzo y el apoyo de tu asesor, estarás cada vez más cerca de alcanzar tu meta profesional. ¡Es hora de dar lo mejor de ti! 🚀🔥</p>";
                                
                
                                    $asunto = "Notificación de Asignación de Asesor para Proyecto";
                
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
                                                       <h2>Asignación de Proyecto</h2>
                                                </div>
                                                <div class="email-body">
                                                    <p><b>Estimado  ' . $nombre_estudiante . ' ' . $apellido_estudiante . ',</b></p>
                                                    '.$message.'
                                                    <p>Confiamos en que su experiencia y orientación serán fundamentales para el éxito de este proyecto.</p>
                                                    <div class="credentials">
                                                        <ul>
                                                            <li><b>Código del proyecto:</b> ' . $codigo . '</li>
                                                            <li><b>Título del proyecto:</b> ' . $titulo_proyecto . '</li>
                                                            <li><b>Nombre del Asesor:</b> ' . $nombre_usuario_profesor . '</li>
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
                                       
                                        $cuerpo_texto = "Estimado $nombre_estudiante $apellido_estudiante, nos complace informarte que se te ha asignado el anteproyecto con el código $codigo y el título $titulo_proyecto. ¡Estamos seguros de que harás un excelente trabajo!";
                            
                                // Intentar enviar el correo y verificar si se envió con éxito
                                $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);
                            
                                // Intentar enviar el correo y verificar si se envió con éxito
                                $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);
                            
                                if (!$enviado2) {
                                    $correosEnviados = false; // Si algún correo no se envía, marcar como falso
                                }
                            }
                            
                            // Mostrar mensaje de confirmación si todos los correos se enviaron correctamente
                            if ($correosEnviados) {
                                $mensaje = [
                                    "Alerta" => "Recargar",
                                    "Titulo" => "Correos Enviados",
                                    "Texto" => "Se ha notificado al asesor y a todos los estudiantes asignados.",
                                    "Tipo" => "success"
                                ];
                            } else {
                                $mensaje = [
                                    "Alerta" => "simple",
                                    "Titulo" => "Error en el Envío",
                                    "Texto" => "Hubo un problema al enviar algunos correos. Por favor, verifique los correos no enviados.",
                                    "Tipo" => "error"
                                ];
                            }
                            
                            echo json_encode($mensaje);

                            exit();
                            
                            

                        }else {
                            $alerta = [
                                "Alerta" => "simple",
                                "Titulo" => "Error al intentar enviar correo",
                                "Texto" => "Se Asigno correctamnete el anteproyecto al profesor, pero no se pudo enviar el correo electrónico",
                                "Tipo" => "error"];
                        }

                        echo json_encode($alerta);
                        exit();

                        /*************fin enviar correo************** */
                            
                            } else {
                                $alerta = [
                                    "Alerta" => "simple",
                                    "Titulo" => "Error al intentar guardar",
                                    "Texto" => "No se pudo guardar la información en la base de datos. Por favor, intente nuevamente.",
                                    "Tipo" => "error"
                                ];
                            
                            }

                            echo json_encode($alerta);
                            exit();
                   




            }else{
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El tipo de proyecto no es válido.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
    
            }else{
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El usuario no tiene permisos para realizar esta acción.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
    }

    public function sumar_horas_profesores_controlador() {

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

        $numero_horas_sumar = 2;

        $numero_documento_sum_user = MainModel::limpiar_cadenas($_POST['numero_documento_sum_user']);

        
        if (empty($numero_documento_sum_user) ) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $numero_documento_sum_user =  MainModel::decryption($numero_documento_sum_user);

        
        $check_profesor = MainModel::ejecutar_consultas_simples(
            "SELECT nombre_usuario, apellidos_usuario, id_rol,numero_documento FROM usuarios WHERE numero_documento = '$numero_documento_sum_user'"
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

        $nombre_usuario =  $usuario['nombre_usuario'] .' ' .$usuario['apellidos_usuario']; 

        
        $consulta_horas_profesor = MainModel::ejecutar_consultas_simples(
            "SELECT numero_hora, numero_documento
             FROM asignar_horas_profesor 
             WHERE numero_documento = '$numero_documento_sum_user'"
        );
        
        if ($consulta_horas_profesor->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento de ". $nombre_usuario ." no tiene horas asignadas.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $horas = $consulta_horas_profesor->fetch(PDO::FETCH_ASSOC);

        $numero_horas_profesor = (int) $horas['numero_hora'];

        
        if ($id_rol_usuario!= 5) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "El usuario ". $nombre_usuario . " no es un asesor.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $numero_horas_nuevas = (int) $numero_horas_profesor + (int) $numero_horas_sumar;


        if($numero_horas_nuevas > $numero_horas_maximas_profesores){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se puede sumar más de ".$numero_horas_maximas_profesores." horas al profesor.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $datos_horas = [
            "numero_documento" => $numero_documento_sum_user,
            "numero_hora" => $numero_horas_nuevas
        ];
        
        $actualizar_horas = ProyectoModelo::Actualizar_horas_profesor_modelos($datos_horas);
        
        if ($actualizar_horas->rowCount() > 0) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Actualización exitosa",
                "Texto" => "El número de documento de ". $nombre_usuario ." ha sido actualizado con éxito. Nuevas horas: ". $numero_horas_nuevas,
                "Tipo" => "success"
            ];
            
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error al intentar actualizar",
                "Texto" => "No se pudo actualizar la información en la base de datos. Por favor, intente nuevamente.",
                "Tipo" => "error"
            ];
            
        }
        echo json_encode($alerta);
        exit();

        
    }

    public function restar_horas_profesores_controlador() {

        $numero_horas_restar = 2;

        $numero_documento_sum_user = MainModel::limpiar_cadenas($_POST['numero_documento_res_user']);

        
        if (empty($numero_documento_sum_user) ) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $numero_documento_sum_user =  MainModel::decryption($numero_documento_sum_user);

        
        $check_profesor = MainModel::ejecutar_consultas_simples(
            "SELECT nombre_usuario, apellidos_usuario, id_rol,numero_documento FROM usuarios WHERE numero_documento = '$numero_documento_sum_user'"
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

        $nombre_usuario =  $usuario['nombre_usuario'] .' ' .$usuario['apellidos_usuario']; 

        
        $consulta_horas_profesor = MainModel::ejecutar_consultas_simples(
            "SELECT numero_hora, numero_documento
             FROM asignar_horas_profesor 
             WHERE numero_documento = '$numero_documento_sum_user'"
        );
        
        if ($consulta_horas_profesor->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento de ". $nombre_usuario ." no tiene horas asignadas.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $horas = $consulta_horas_profesor->fetch(PDO::FETCH_ASSOC);

        $numero_horas_profesor = (int) $horas['numero_hora'];

        
        if ($id_rol_usuario!= 5) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "El usuario ". $nombre_usuario . " no es un asesor.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $numero_horas_nuevas = (int) $numero_horas_profesor - (int) $numero_horas_restar;

        if($numero_horas_nuevas < 0){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El profesor no puede tener horas de asesorias negativas",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $datos_horas = [
            "numero_documento" => $numero_documento_sum_user,
            "numero_hora" => $numero_horas_nuevas
        ];
        
        $actualizar_horas = ProyectoModelo::Actualizar_horas_profesor_modelos($datos_horas);
        
        if ($actualizar_horas->rowCount() > 0) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Actualización exitosa",
                "Texto" => "El número de documento de ". $nombre_usuario ." ha sido actualizado con éxito. Nuevas horas: ". $numero_horas_nuevas,
                "Tipo" => "success"
            ];
            
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error al intentar actualizar",
                "Texto" => "No se pudo actualizar la información en la base de datos. Por favor, intente nuevamente.",
                "Tipo" => "error"
            ];
            
        }
        echo json_encode($alerta);
        exit();

        
    }

    public function eliminar_horas_profesores_controlador() {


        $numero_documento_sum_user = MainModel::limpiar_cadenas($_POST['delete_horas_asesor']);

        
        if (empty($numero_documento_sum_user) ) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $numero_documento_sum_user =  MainModel::decryption($numero_documento_sum_user);

        
        $check_profesor = MainModel::ejecutar_consultas_simples(
            "SELECT nombre_usuario, apellidos_usuario, id_rol,numero_documento FROM usuarios WHERE numero_documento = '$numero_documento_sum_user'"
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

        $nombre_usuario =  $usuario['nombre_usuario'] .' ' .$usuario['apellidos_usuario']; 

        



        $check_profesor_horas = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM asignar_horas_profesor WHERE numero_documento = '$numero_documento_sum_user'"
        );
        if ($check_profesor_horas->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento del usuario ". $nombre_usuario ." no tiene asignada horas de asesoria.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        
        if ($id_rol_usuario!= 5) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "El usuario ". $nombre_usuario . " no es un asesor.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        
        $actualizar_horas = ProyectoModelo::Eliminar_horas_profesor_modelos($numero_documento_sum_user);
        
        if ($actualizar_horas) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Actualización exitosa",
                "Texto" => "El número de documento de ". $nombre_usuario ." ha sido eliminado con éxito.",
                "Tipo" => "success"
            ];
            
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error al intentar actualizar",
                "Texto" => "No se pudo eliminar la información en la base de datos. Por favor, intente nuevamente.",
                "Tipo" => "error"
            ];
            
        }
        echo json_encode($alerta);
        exit();

        
    }


    public function asignar_jurado_proyecto_controlador() {

        
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

        $check_configuracion = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM configuracion_aplicacion "
        );
        if ($check_configuracion->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "error en la consulta",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        
        $datos_configuracion = $check_configuracion->fetch(PDO::FETCH_ASSOC);
        

        $numero_maximo_jurados = (int) $datos_configuracion['numero_jurados_proyectos'];


        $numero_horas_restar = 2;

        $codigo = MainModel::limpiar_cadenas($_POST['codigo_proyecto_asignar_jurado']);
        $numero_documento_profesor = MainModel::limpiar_cadenas($_POST['documento_user_asignar_jurado']);
        $numero_documento_user_logueado = MainModel::limpiar_cadenas($_POST['numero_documento_user_logueado']);
        $tipoProyectoAnteproyecto = MainModel::limpiar_cadenas($_POST['tipoProyectoAnteproyecto']);
        $idfacultaProyectoAnteproyecto = MainModel::limpiar_cadenas($_POST['idfacultaProyectoAnteproyecto']);
        $idProgramaProyectoAnteproyecto = MainModel::limpiar_cadenas($_POST['idProgramaProyectoAnteproyecto']);
        

        if (empty($codigo) || empty($numero_documento_profesor) || empty($numero_documento_user_logueado)
        
        || empty($tipoProyectoAnteproyecto) || empty($idfacultaProyectoAnteproyecto) || empty($idProgramaProyectoAnteproyecto) ) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $numero_documento_user_logueado =  MainModel::decryption($numero_documento_user_logueado);
        $tipoProyectoAnteproyecto =  MainModel::decryption($tipoProyectoAnteproyecto);
        $idfacultaProyectoAnteproyecto =  MainModel::decryption($idfacultaProyectoAnteproyecto);
        $idProgramaProyectoAnteproyecto =  MainModel::decryption($idProgramaProyectoAnteproyecto);

        $check_numero_jurados = MainModel::ejecutar_consultas_simples(
            "SELECT COUNT(*) AS total_jurados FROM Asignar_jurados_proyecto WHERE codigo_proyecto = '$codigo'"
        );
        if ($check_numero_jurados->rowCount() > 0) {

            $resultado = $check_numero_jurados->fetch(PDO::FETCH_ASSOC);

            $total_jurados = $resultado['total_jurados'];
        
            // Puedes usar $total_jurados según sea necesario
        } 

        if ($total_jurados >= $numero_maximo_jurados) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número máximo de jurados para este proyecto ya ha sido alcanzado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        /**************validar si el usuario existe - profesor ********************* */

        $check_profesor = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM usuarios WHERE numero_documento = '$numero_documento_profesor'"
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

        $nombre_usuario_completo =  $usuario['nombre_usuario'] .' ' .$usuario['apellidos_usuario']; 
        
        $nombre_usuario = $usuario['nombre_usuario'];

        $apellido_usuario = $usuario['apellidos_usuario'];

        $correo_usuario = $usuario['correo_usuario'];


         /**************validar informacion logueado  ********************* */

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

         /********************validamos el********************************* */

         $check_estado_proyecto = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM proyectos WHERE codigo_proyecto = '$codigo' AND estado = 'Aprobado'"
        );
        
        if ($check_estado_proyecto->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El estado del proyecto no ha sido aprobado por lo tanto, no se puede asignar un jurado todavía ",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        
 
         if($rol_usuario_logueado == 1){ 

            /**** filstros del profesor */

            if ($id_rol_usuario!= 5) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Acceso denegado",
                    "Texto" => "El usuario ". $nombre_usuario_completo . " no es un asesor.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if ($tipoProyectoAnteproyecto != 2) {
                // Generar un mensaje de error si el tipo de proyecto no es 2
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El código no pertenece a un proyecto",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

        

                $consulta_anteproyecto = MainModel::ejecutar_consultas_simples(
                    "SELECT * FROM proyectos WHERE codigo_proyecto = '$codigo'"
                );
                
                if ($consulta_anteproyecto->rowCount() <= 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Código de proyecto no válido",
                        "Texto" => "El código de proyecto es invalido. Por favor, ingrese un valor válido.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                 
                $datos_proyecto = $consulta_anteproyecto->fetch(PDO::FETCH_ASSOC);

                $titulo_proyecto = $datos_proyecto['titulo_proyecto'];

                $consulta_asignacion = MainModel::ejecutar_consultas_simples(
                    "SELECT * FROM Asignar_jurados_proyecto WHERE numero_documento = '$numero_documento_profesor' AND codigo_proyecto = '$codigo'"
                );

                if ($consulta_asignacion->rowCount() > 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Asignación existente",
                        "Texto" => "El número de documento de ". $nombre_usuario_completo ." ya tiene asignado un proyecto con el código ". $codigo,
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                $consulta_asignacion = MainModel::ejecutar_consultas_simples(
                    "SELECT * FROM Asignar_asesor_anteproyecto_proyecto WHERE numero_documento = '$numero_documento_profesor' AND codigo_proyecto = '$codigo'" 
                );

                if ($consulta_asignacion->rowCount() > 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Asignación existente",
                        "Texto" => "El profesor ". $nombre_usuario_completo ." es el asesor del proyecto, por lo tanto no puede ser el jurado del mismo ",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                        // Obtener las facultades y programas del profesor en base a su número de documento
                    $check_profesor_facultad = MainModel::ejecutar_consultas_simples(
                        "SELECT 
                            auf.id_facultad,
                            auf.id_programa
                        FROM Asignar_usuario_facultades auf
                        WHERE auf.numero_documento = '$numero_documento_profesor'"
                    );
                    
                    // Validar si se encontraron registros para el profesor
                    if ($check_profesor_facultad->rowCount() <= 0) {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "El número de documento de " . $nombre_usuario_completo . " no tiene asignada una facultad y un programa.",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }

               
            

                $datos_asignar = [
                    "codigo_proyecto" => $codigo,
                    "numero_documento" => $numero_documento_profesor
                ];
    
                $guardar_asignar = ProyectoModelo::asignar_jurado_proyecto($datos_asignar);
    
                if ($guardar_asignar->rowCount() > 0) {
                    

                    /******************************************************** */

                    $message = "<p>📢 ¡Atención! Se te ha asignado un <b>proyecto para ser evaluado como jurado</b>, una tarea clave en la validación y certificación del esfuerzo académico de los estudiantes. 🎓⚖️</p>

                    <p>Tu experiencia y criterio serán fundamentales para analizar el trabajo presentado, valorar la investigación y proporcionar una retroalimentación que fortalezca la calidad del proyecto. Cada observación y recomendación que realices contribuirá al desarrollo profesional de los futuros graduados. 📖💡</p>

                    <p><b>Tu rol es esencial.</b> Gracias a tu compromiso, se garantiza la excelencia académica y la formación de profesionales altamente capacitados. ¡Tu análisis marcará la diferencia! 🚀🔥</p>";

                
    
                    include __DIR__ . '/../Mail/enviar-correo.php';
    
                       
                    $asunto = "Notificación de Asignación como Jurado para Proyecto";
    
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
                                        <h2>Asignación como Jurado para Proyecto</h2>
                                    </div>
                                    <div class="email-body">
                                        <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                                        '.$message.'
                                        <p>Tu experiencia y conocimientos serán fundamentales para evaluar y brindar retroalimentación en este proyecto.</p>
                                        <div class="credentials">
                                            <ul>
                                                <li><b>Código del Proyecto:</b> ' . $codigo . '</li>
                                                <li><b>Título del Proyecto:</b> ' . $titulo_proyecto . '</li>
                                                
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
                            $cuerpo_texto = "Estimado $nombre_usuario $apellido_usuario, nos complace informarte que se te ha asignado como jurado para el proyecto con el código $codigo y el título $titulo_proyecto. Tu experiencia será clave en este proceso.";


                    $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);
                    
                    if ($enviado) {
                        $alerta = [
                            "Alerta" => "Recargar",
                            "Titulo" => "Operación exitosa",
                            "Texto" => "Se ha asignado correctamente el jurado al proyecto, tambien se ha notificado al profesor ".$nombre_usuario_completo,
                            "Tipo" => "success"
                        ];
                    }else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "Error al enviar el correo al profesor, pero se asigno como jurado a proyecto correctamente",
                            "Tipo" => "error"
                        ];
                    }

                    echo json_encode($alerta);
                    exit();


                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Error al intentar guardar",
                        "Texto" => "No se pudo guardar la información en la base de datos. Por favor, intente nuevamente.",
                        "Tipo" => "error"
                    ];
                
                }
    
                echo json_encode($alerta);
                exit();

            

         }else if ($rol_usuario_logueado == 2){

           
            if ($id_rol_usuario!= 5) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Acceso denegado",
                    "Texto" => "El usuario ". $nombre_usuario_completo . " no es un asesor.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if ($tipoProyectoAnteproyecto != 2) {
                // Generar un mensaje de error si el tipo de proyecto no es 2
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El código no pertenece a un proyecto",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

        

                $consulta_anteproyecto = MainModel::ejecutar_consultas_simples(
                    "SELECT * FROM proyectos WHERE codigo_proyecto = '$codigo'"
                );
                
                if ($consulta_anteproyecto->rowCount() <= 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Código de proyecto no válido",
                        "Texto" => "El código de proyecto es invalido. Por favor, ingrese un valor válido.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                 
                $datos_proyecto = $consulta_anteproyecto->fetch(PDO::FETCH_ASSOC);

                $titulo_proyecto = $datos_proyecto['titulo_proyecto'];

                $consulta_asignacion = MainModel::ejecutar_consultas_simples(
                    "SELECT * FROM Asignar_jurados_proyecto WHERE numero_documento = '$numero_documento_profesor' AND codigo_proyecto = '$codigo'"
                );

                if ($consulta_asignacion->rowCount() > 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Asignación existente",
                        "Texto" => "El número de documento de ". $nombre_usuario_completo ." ya tiene asignado un proyecto con el código ". $codigo,
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                $consulta_asignacion = MainModel::ejecutar_consultas_simples(
                    "SELECT * FROM Asignar_asesor_anteproyecto_proyecto WHERE numero_documento = '$numero_documento_profesor' AND codigo_proyecto = '$codigo'" 
                );

                if ($consulta_asignacion->rowCount() > 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Asignación existente",
                        "Texto" => "El profesor ". $nombre_usuario_completo ." es el asesor del proyecto, por lo tanto no puede ser el jurado del mismo ",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                        // Obtener las facultades y programas del profesor en base a su número de documento
                    $check_profesor_facultad = MainModel::ejecutar_consultas_simples(
                        "SELECT 
                            auf.id_facultad,
                            auf.id_programa
                        FROM Asignar_usuario_facultades auf
                        WHERE auf.numero_documento = '$numero_documento_profesor'"
                    );
                    
                    // Validar si se encontraron registros para el profesor
                    if ($check_profesor_facultad->rowCount() <= 0) {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "El número de documento de " . $nombre_usuario_completo . " no tiene asignada una facultad y un programa.",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }

               
    


                $datos_asignar = [
                    "codigo_proyecto" => $codigo,
                    "numero_documento" => $numero_documento_profesor   
                ];
    
                $guardar_asignar = ProyectoModelo::asignar_jurado_proyecto($datos_asignar);
    
                if ($guardar_asignar->rowCount() > 0) {
                    
                    
                    /******************************************************** */

                    $message = "<p>📢 ¡Atención! Se te ha asignado un <b>proyecto para ser evaluado como jurado</b>, una tarea clave en la validación y certificación del esfuerzo académico de los estudiantes. 🎓⚖️</p>

                    <p>Tu experiencia y criterio serán fundamentales para analizar el trabajo presentado, valorar la investigación y proporcionar una retroalimentación que fortalezca la calidad del proyecto. Cada observación y recomendación que realices contribuirá al desarrollo profesional de los futuros graduados. 📖💡</p>

                    <p><b>Tu rol es esencial.</b> Gracias a tu compromiso, se garantiza la excelencia académica y la formación de profesionales altamente capacitados. ¡Tu análisis marcará la diferencia! 🚀🔥</p>";

                
    
                    include __DIR__ . '/../Mail/enviar-correo.php';
    
                       
                    $asunto = "Notificación de Asignación como Jurado para Proyecto";
    
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
                                        <h2>Asignación como Jurado para Proyecto</h2>
                                    </div>
                                    <div class="email-body">
                                        <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                                        '.$message.'
                                        <p>Tu experiencia y conocimientos serán fundamentales para evaluar y brindar retroalimentación en este proyecto.</p>
                                        <div class="credentials">
                                            <ul>
                                                <li><b>Código del Proyecto:</b> ' . $codigo . '</li>
                                                <li><b>Título del Proyecto:</b> ' . $titulo_proyecto . '</li>
                                                
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
                            $cuerpo_texto = "Estimado $nombre_usuario $apellido_usuario, nos complace informarte que se te ha asignado como jurado para el proyecto con el código $codigo y el título $titulo_proyecto. Tu experiencia será clave en este proceso.";


                    $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);
                    
                    if ($enviado) {
                        $alerta = [
                            "Alerta" => "Recargar",
                            "Titulo" => "Operación exitosa",
                            "Texto" => "Se ha asignado correctamente el jurado al proyecto, tambien se ha notificado al profesor ".$nombre_usuario_completo,
                            "Tipo" => "success"
                        ];
                    }else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "Error al enviar el correo al profesor, pero se asigno como jurado a proyecto correctamente",
                            "Tipo" => "error"
                        ];
                    }

                    echo json_encode($alerta);
                    exit();


                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Error al intentar guardar",
                        "Texto" => "No se pudo guardar la información en la base de datos. Por favor, intente nuevamente.",
                        "Tipo" => "error"
                    ];
                
                }
    
                echo json_encode($alerta);
                exit();



         }else {
             $alerta = [
                 "Alerta" => "simple",
                 "Titulo" => "Acceso denegado",
                 "Texto" => "El usuario ". $nombre_usuario_completo . " no es un profesor.",
                 "Tipo" => "error"
             ];
             echo json_encode($alerta);
             exit();
         }
         
    }

    public function sumar_horas_jurado_profesores_controlador() {


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
        

        $numero_horas_maximas_profesores = (int) $datos_configuracion['numero_horas_jurados'];


        $numero_horas_sumar = 2;

        $numero_documento_sum_user = MainModel::limpiar_cadenas($_POST['numero_documento_sum_user_jurado']);

        
        if (empty($numero_documento_sum_user) ) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $numero_documento_sum_user =  MainModel::decryption($numero_documento_sum_user);

        
        $check_profesor = MainModel::ejecutar_consultas_simples(
            "SELECT nombre_usuario, apellidos_usuario, id_rol,numero_documento FROM usuarios WHERE numero_documento = '$numero_documento_sum_user'"
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

        $nombre_usuario =  $usuario['nombre_usuario'] .' ' .$usuario['apellidos_usuario']; 

        
        $consulta_horas_profesor = MainModel::ejecutar_consultas_simples(
            "SELECT numero_hora, numero_documento
             FROM asignar_horas_jurado_profesor 
             WHERE numero_documento = '$numero_documento_sum_user'"
        );
        
        if ($consulta_horas_profesor->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento de ". $nombre_usuario ." no tiene horas asignadas.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $horas = $consulta_horas_profesor->fetch(PDO::FETCH_ASSOC);

        $numero_horas_profesor = (int) $horas['numero_hora'];

        
        if ($id_rol_usuario!= 5) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "El usuario ". $nombre_usuario . " no es un asesor.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $numero_horas_nuevas = (int) $numero_horas_profesor + (int) $numero_horas_sumar;

        if($numero_horas_nuevas > $numero_horas_maximas_profesores){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se puede sumar más de ". $numero_horas_maximas_profesores. " horas al profesor.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $datos_horas = [
            "numero_documento" => $numero_documento_sum_user,
            "numero_hora" => $numero_horas_nuevas
        ];
        
        $actualizar_horas = ProyectoModelo::Actualizar_horas_jurados_profesor_modelos($datos_horas);
        
        if ($actualizar_horas->rowCount() > 0) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Actualización exitosa",
                "Texto" => "El número de documento de ". $nombre_usuario ." ha sido actualizado con éxito. Nuevas horas: ". $numero_horas_nuevas,
                "Tipo" => "success"
            ];
            
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error al intentar actualizar",
                "Texto" => "No se pudo actualizar la información en la base de datos. Por favor, intente nuevamente.",
                "Tipo" => "error"
            ];
            
        }
        echo json_encode($alerta);
        exit();

        
    }
    
    public function restar_horas_jurado_profesores_controlador() {

        $numero_horas_restar = 2;

        $numero_documento_sum_user = MainModel::limpiar_cadenas($_POST['numero_documento_res_user_jurado']);

        
        if (empty($numero_documento_sum_user) ) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $numero_documento_sum_user =  MainModel::decryption($numero_documento_sum_user);

        
        $check_profesor = MainModel::ejecutar_consultas_simples(
            "SELECT nombre_usuario, apellidos_usuario, id_rol,numero_documento FROM usuarios WHERE numero_documento = '$numero_documento_sum_user'"
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

        $nombre_usuario =  $usuario['nombre_usuario'] .' ' .$usuario['apellidos_usuario']; 

        
        $consulta_horas_profesor = MainModel::ejecutar_consultas_simples(
            "SELECT numero_hora, numero_documento
             FROM asignar_horas_jurado_profesor 
             WHERE numero_documento = '$numero_documento_sum_user'"
        );
        
        if ($consulta_horas_profesor->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento de ". $nombre_usuario ." no tiene horas asignadas.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $horas = $consulta_horas_profesor->fetch(PDO::FETCH_ASSOC);

        $numero_horas_profesor = (int) $horas['numero_hora'];

        
        if ($id_rol_usuario!= 5) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "El usuario ". $nombre_usuario . " no es un asesor.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $numero_horas_nuevas = (int) $numero_horas_profesor - (int) $numero_horas_restar;

        if($numero_horas_nuevas < 0){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El profesor no puede tener horas de jurado negativas",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $datos_horas = [
            "numero_documento" => $numero_documento_sum_user,
            "numero_hora" => $numero_horas_nuevas
        ];
        
        $actualizar_horas = ProyectoModelo::Actualizar_horas_jurados_profesor_modelos($datos_horas);
        
        if ($actualizar_horas->rowCount() > 0) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Actualización exitosa",
                "Texto" => "El número de documento de ". $nombre_usuario ." ha sido actualizado con éxito. Nuevas horas: ". $numero_horas_nuevas,
                "Tipo" => "success"
            ];
            
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error al intentar actualizar",
                "Texto" => "No se pudo actualizar la información en la base de datos. Por favor, intente nuevamente.",
                "Tipo" => "error"
            ];
            
        }
        echo json_encode($alerta);
        exit();

        
    }

    public function cargarDocuemntosProyectos() {

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

        if (empty($codigo) || empty($numero_documento_user_logueado)  || empty($nombre_archivo) || empty($identificador_carga_documento)) {
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
                    "Texto" => "El archivo debe estar en formato PDF o Word (.doc o .docx).",
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
            "SELECT codigo_proyecto FROM proyectos WHERE codigo_proyecto = '$codigo'"
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
                    FROM cargar_documento_proyectos 
                    WHERE codigo_proyecto = '$codigo'
                    ORDER BY id DESC 
                    LIMIT 1"
            );
            
            // Verificar si hay resultados
            if ($extraer_datos_documentos_cargador->rowCount() > 0) {
                $datos_documento = $extraer_datos_documentos_cargador->fetch(PDO::FETCH_ASSOC);

                $ruta_carpeta = '../Views/document/proyectos/'.$codigo.'/';


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

                $nombre_carpeta = '../Views/document/proyectos/'. $codigo;

                // Verificar si la carpeta existe, si no, crearla
                if (!file_exists($nombre_carpeta)) {
                    mkdir($nombre_carpeta, 0777, true);
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
                                    "UPDATE cargar_documento_proyectos 
                                        SET documento = '$nombre_pdf', 
                                            nombre_archivo_word = '$nombre_word' 
                                        WHERE codigo_proyecto = '$codigo'
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
            FROM retroalimentacion_proyecto 
            WHERE codigo_proyecto = '$codigo'
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
            "SELECT * FROM cargar_documento_proyectos 
            WHERE numero_documento = '$numero_documento_user_logueado' 
            AND DATE(fecha_creacion) = '$fecha_actual'"
        );

        if ($check_documento->rowCount() > 0) {
            // Si el usuario ya ha enviado un documento hoy
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Documento ya enviado",
                "Texto" => "Este usuario ya ha cargado un documento para el proyecto el día de hoy.",
                "Tipo" => "warning"
            ];
            echo json_encode($alerta);
            exit();
        } 

        

        $check_documento_otros_usuarios = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM cargar_documento_proyectos 
             WHERE codigo_proyecto = '$codigo' 
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

        $nombre_carpeta = '../Views/document/proyectos/'.$codigo;

        // Verifica si el directorio ya existe
        if (!file_exists($nombre_carpeta)) {
            // Intenta crear el directorio con permisos de escritura
            if (mkdir($nombre_carpeta, 0777, true)) {
                
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
                                'codigo_proyecto' => $codigo,
                                'nombre_archivo' => $nombre_pdf,
                                'nombre_archivo_word' => $nombre_word,
                                'estado' => $estado,
                                'fecha_creacion' => date('Y-m-d H:i:s')
                            ];
                            $guardar_documento = ProyectoModelo::cargar_documento_proyecto_modelo($datos);
    
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
                            e.titulo_proyecto,
                            u.numero_documento,
                            u.nombre_usuario,
                            u.apellidos_usuario,
                            u.correo_usuario,
                            u.telefono_usuario
                        FROM Asignar_asesor_anteproyecto_proyecto a
                        INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                        INNER JOIN proyectos e ON e.codigo_proyecto = a.codigo_proyecto
                        WHERE a.codigo_proyecto = '$codigo'"
                    );
                    $correosEnviados = true;

                    include_once __DIR__ . '/../Mail/enviar-correo.php';

                    while ($row = $consulta_profesor_asignado->fetch(PDO::FETCH_ASSOC)) {
                        $nombre_profesor = $row['nombre_usuario'];
                        $apellido_profesor = $row['apellidos_usuario'];
                        $nombre_usuario_profesor =  $nombre_profesor.'  '.$apellido_profesor;
                        $correo_usuario_profesor = $row['correo_usuario'];
                        $titulo_proyecto = $row['titulo_proyecto'];

                        $asunto = "Notificación de Documento Subido para Revisión y Retroalimentación";

                        
                        $message = "📢 Estimado asesor, le informamos que un estudiante ha enviado un proyecto para su revisión.  
                        📄 El documento ya está disponible en la plataforma para que pueda evaluarlo y proporcionar las observaciones correspondientes.  
                        ⌛ Le recordamos que la retroalimentación oportuna es clave para el avance del estudiante en su proceso académico.  
                        🔍 Puede acceder al proyecto desde su módulo de revisión. ¡Gracias por su compromiso con la excelencia académica! 🎓✨";
                        

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
                                    <p><b>Estimado/a Asesor/a  ' .$nombre_usuario_profesor . ',</b></p>
                                    '.$message.'
                                    
                                    <div class="credentials">
                                        <ul>
                                        <li><b>Código del Proyecto:</b> ' . $codigo . '</li>
                                            <li><b>Título del Proyecto:</b> ' . $titulo_proyecto . '</li>
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

                        $cuerpo_texto = "Estimado Asesor $nombre_usuario_profesor, se ha subido un nuevo documento para revisión y retroalimentación del proyecto con el código $codigo y el título $titulo_proyecto. Te solicitamos que revises el documento y proporciones la retroalimentación correspondiente.";

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
                            'codigo_proyecto' => $codigo,
                            'nombre_archivo' => $nombre_pdf,
                            'nombre_archivo_word' => $nombre_word,
                            'estado' => $estado,
                            'fecha_creacion' => date('Y-m-d H:i:s')
                        ];
                        $guardar_documento = ProyectoModelo::cargar_documento_proyecto_modelo($datos);

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
                        e.titulo_proyecto,
                        u.numero_documento,
                        u.nombre_usuario,
                        u.apellidos_usuario,
                        u.correo_usuario,
                        u.telefono_usuario
                    FROM Asignar_asesor_anteproyecto_proyecto a
                    INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                    INNER JOIN proyectos e ON e.codigo_proyecto = a.codigo_proyecto
                    WHERE a.codigo_proyecto = '$codigo'"
                );
                $correosEnviados = true;

                include_once __DIR__ . '/../Mail/enviar-correo.php';

                while ($row = $consulta_profesor_asignado->fetch(PDO::FETCH_ASSOC)) {
                    $nombre_profesor = $row['nombre_usuario'];
                    $apellido_profesor = $row['apellidos_usuario'];
                    $nombre_usuario_profesor =  $nombre_profesor.'  '.$apellido_profesor;
                    $correo_usuario_profesor = $row['correo_usuario'];
                    $titulo_proyecto = $row['titulo_proyecto'];

                    $asunto = "Notificación de Documento Subido para Revisión y Retroalimentación";

                    $message = "📢 Estimado asesor, le informamos que un estudiante ha enviado un proyecto para su revisión.  
                    📄 El documento ya está disponible en la plataforma para que pueda evaluarlo y proporcionar las observaciones correspondientes.  
                    ⌛ Le recordamos que la retroalimentación oportuna es clave para el avance del estudiante en su proceso académico.  
                    🔍 Puede acceder al proyecto desde su módulo de revisión. ¡Gracias por su compromiso con la excelencia académica! 🎓✨";
                    

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
                                <p><b>Estimado/a Asesor/a  ' .$nombre_usuario_profesor . ',</b></p>
                                '.$message.'
                                
                                <div class="credentials">
                                    <ul>
                                    <li><b>Código del Proyecto:</b> ' . $codigo . '</li>
                                        <li><b>Título del Proyecto:</b> ' . $titulo_proyecto . '</li>
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

                    $cuerpo_texto = "Estimado Asesor $nombre_usuario_profesor, se ha subido un nuevo documento para revisión y retroalimentación del proyecto con el código $codigo y el título $titulo_proyecto. Te solicitamos que revises el documento y proporciones la retroalimentación correspondiente.";

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

    public function retroalimentacion_proyectos(){

        
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
        // los datos que vienen encriptados
        $numero_documento_user_logueado = MainModel::limpiar_cadenas($_POST['numero_documento_user_logueado']);
        $codigo_anteproyecto = MainModel::limpiar_cadenas($_POST['codigo_anteproyecto']);
        $id_documento_cargado = MainModel::limpiar_cadenas($_POST['id_documento_cargado']);
        $estado = MainModel::limpiar_cadenas($_POST['estado_retroalimentacion']);
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
            "SELECT * FROM proyectos WHERE codigo_proyecto = '$codigo_anteproyecto'"
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

        
        $datos_proyecto = $check_codigo->fetch(PDO::FETCH_ASSOC);

        $tituloproyecto = $datos_proyecto['titulo_proyecto']; 
       
        $check_id_documento = MainModel::ejecutar_consultas_simples(
            "SELECT id FROM cargar_documento_proyectos WHERE id = '$id_documento_cargado'"
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
            "SELECT id FROM retroalimentacion_proyecto WHERE id = '$id_documento_cargado'"
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

        

        if(empty($nombre_archivo)){ // si no hay documento registrado

            $nombre_nuevo_archivo = "None";

            $datos = [
                'numero_documento' => $numero_documento_user_logueado,
                'codigo_anteproyecto' => $codigo_anteproyecto,
                'id_documento_cargado' => $id_documento_cargado,
                'observacion_general' => $observaciones_generales,
                'estado_revision' => $estado,
                'nombre_archivo_word' => $nombre_nuevo_archivo,
                'estado' => $estado_revision,
                'fecha_entrega' => !empty($fecha_revision) ? $fecha_revision : date('Y-m-d')
            ];



            if($estado == 2){ // si es aprobado el proyecto

                $guardar_retroalimentacion = ProyectoModelo::guardar_retroalimentacion_proyecto_modelo($datos);

                if ($guardar_retroalimentacion) {

                    include_once __DIR__ . '/../Mail/enviar-correo.php';

                    $correosEnviados = true; 

                    $consulta_proyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                        "SELECT 
                            a.codigo_proyecto,
                            u.numero_documento,
                            u.nombre_usuario,
                            u.apellidos_usuario,
                            u.correo_usuario,
                            u.telefono_usuario
                        FROM asignar_estudiante_proyecto a
                        INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                        WHERE a.codigo_proyecto = '$codigo_anteproyecto'"
                    );

                    $actualizar_estado_proyecto = MainModel::ejecutar_consultas_simples(
                        "UPDATE proyectos 
                        SET estado = 'Aprobado'
                        WHERE codigo_proyecto = '$codigo_anteproyecto'"
                    );

                    // Iterar sobre cada estudiante y enviar correos
                    while ($row = $consulta_proyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {
                        $nombre_estudiante = $row['nombre_usuario'];
                        $apellido_estudiante = $row['apellidos_usuario'];
                        $correo_usuario_estudiante = $row['correo_usuario'];

                        $asunto = "Notificación de Aprobación de Proyecto";

                    
                        $message .= "🎉 ¡Felicidades! Tu proyecto ha sido **aprobado con éxito** 🎓✅. Este logro es el resultado de tu esfuerzo, dedicación y perseverancia. Has demostrado tu capacidad para afrontar desafíos académicos y avanzar con determinación hacia tu meta. 🚀✨";

                        $message .= "Ahora, solo queda esperar la programación de la fecha para la **sustentación de tu proyecto**. 📅✨ Mantente atento a las indicaciones y prepárate para esta última etapa, donde podrás demostrar todo el conocimiento y trabajo que has desarrollado. ¡Confíamos en tu éxito y sabemos que harás una gran presentación! 🏆🔥";


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
                                            <li><b>Código del Proyecto:</b> ' . $codigo_anteproyecto . '</li>
                                            <li><b>Título del Proyecto:</b> ' . $tituloproyecto . '</li>
                                        </ul>
                                    </div>
                                    
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

                        $cuerpo_texto = "Estimado/a $nombre_estudiante $apellido_estudiante, nos complace informarte que tu proyecto con el código $codigo_anteproyecto y el título $tituloproyecto ha sido aprobado con éxito. Felicitaciones por este logro académico.";

                        $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);

                        if (!$enviado2) {
                            $correosEnviados = false; // Si algún correo no se envía, marcar como falso
                        }

                    }

                    // Mostrar el mensaje según el resultado del envío
                    if ($correosEnviados) {
                        $mensaje = [
                            "Alerta" => "Recargar",
                            "Titulo" => "Correos Enviados",
                            "Texto" => "Se ha notificado a los estudiantes asignados que el proyecto fue aprobado con éxito.",
                            "Tipo" => "success"
                        ];
                    } else {
                        $mensaje = [
                            "Alerta" => "simple",
                            "Titulo" => "Error en el Envío",
                            "Texto" => "Hubo un problema al enviar algunos correos, pero la informacion se guardo correctamente",
                            "Tipo" => "error"
                        ];
                    }

                    echo json_encode($mensaje);
                    exit();

                    

                }else{
                    $mensaje = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado",
                        "Texto" => "No se pudo guardar la información en el sistema",
                        "Tipo" => "warning"
                    ];
                    echo json_encode($mensaje);
                    exit();
                }
               


            }else if($estado == 3){ // proyecto cancelado

                $guardar_retroalimentacion = ProyectoModelo::guardar_retroalimentacion_proyecto_modelo($datos);

                if ($guardar_retroalimentacion) {
                    
                    // actualizamos el estado del proyecto 
                    $actualizar_estado_proyecto = MainModel::ejecutar_consultas_simples(
                        "UPDATE proyectos 
                        SET estado = 'Cancelado'
                        WHERE codigo_proyecto = '$codigo_anteproyecto'"
                    );

                    if ($actualizar_estado_proyecto) {

                        include_once __DIR__ . '/../Mail/enviar-correo.php';

                        $correosEnviados = true;
                    
                        $consulta_proyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                            "SELECT 
                                a.codigo_proyecto,
                                u.numero_documento,
                                u.nombre_usuario,
                                u.apellidos_usuario,
                                u.correo_usuario,
                                u.telefono_usuario
                            FROM asignar_estudiante_proyecto a
                            INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                            WHERE a.codigo_proyecto = '$codigo_anteproyecto'"
                        );
                        while ($row = $consulta_proyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {
                            $nombre_estudiante = $row['nombre_usuario'];
                            $apellido_estudiante = $row['apellidos_usuario'];
                            $correo_usuario_estudiante = $row['correo_usuario'];

                            $asunto = "Notificación de Cancelación de Proyecto";

                            $message .= "Lamentamos informarle que su proyecto ha sido cancelado ❌. Sabemos que esta noticia puede ser desmotivadora, pero cada obstáculo es una oportunidad para mejorar y fortalecerse. 💪✨";

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
                                                <li><b>Título del Anteproyecto:</b> ' . $tituloproyecto . '</li>
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
                            

                            $cuerpo_texto = "Estimado/a $nombre_estudiante $apellido_estudiante, lamentamos informarte que tu proyecto con el código $codigo_anteproyecto y el título $tituloproyecto ha sido cancelado debido a observaciones realizadas durante su evaluación. Te invitamos a revisar los comentarios proporcionados y realizar las correcciones necesarias.";

                            $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);

                            if (!$enviado2) {
                                $correosEnviados = false; // Si algún correo no se envía, marcar como falso
                            }

                        }

                        if ($correosEnviados) {
                            $mensaje = [
                                "Alerta" => "Recargar",
                                "Titulo" => "Correos Enviados",
                                "Texto" => "Se ha notificado a los estudinates asignados, que el proyecto fue cancelado",
                                "Tipo" => "success"
                            ];
                        } else {
                            $mensaje = [
                                "Alerta" => "simple",
                                "Titulo" => "Error en el Envío",
                                "Texto" => "Hubo un problema al enviar algunos correos, retroalimentación cargada con exito",
                                "Tipo" => "error"
                            ];
                        }
                        
                        echo json_encode($mensaje);
                        exit();

                    }else{
                        $mensaje = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "No se pudo actualizar el estado del proyecto  en el sistema",
                            "Tipo" => "warning"
                        ];
                        echo json_encode($mensaje);
                        exit();
                    }


                }else{
                    $mensaje = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado",
                        "Texto" => "No se pudo guardar la información en el sistema",
                        "Tipo" => "warning"
                    ];
                    echo json_encode($mensaje);
                    exit();
                }


            }else{ // proyecto en revision

                
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
                
               
                $guardar_retroalimentacion = ProyectoModelo::guardar_retroalimentacion_proyecto_modelo($datos);

        

                if ($guardar_retroalimentacion) {

                    
                    include_once __DIR__ . '/../Mail/enviar-correo.php';

                    $correosEnviados = true;

                    $consulta_proyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                        "SELECT 
                            a.codigo_proyecto,
                            u.numero_documento,
                            u.nombre_usuario,
                            u.apellidos_usuario,
                            u.correo_usuario,
                            u.telefono_usuario
                        FROM asignar_estudiante_proyecto a
                        INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                        WHERE a.codigo_proyecto = '$codigo_anteproyecto'"
                    );

                    while ($row = $consulta_proyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {
                        $nombre_estudiante = $row['nombre_usuario'];
                        $apellido_estudiante = $row['apellidos_usuario'];
                        $correo_usuario_estudiante = $row['correo_usuario'];


                        $asunto = "Notificación de Retroalimentación de Documento";

                        $message .= "Su proyecto ha recibido retroalimentación por parte del asesor 📌✍️. Este es un paso clave para fortalecer su trabajo y encaminarlo hacia el éxito. 🚀";
    
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
                                       <li><b>Código del Proyecto:</b> ' . $codigo_anteproyecto . '</li>
                                        <li><b>Título del Proyecto:</b> ' . $tituloproyecto . '</li>
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

                    $cuerpo_texto = "Estimado $nombre_estudiante $apellido_estudiante, te informamos que se ha realizado la retroalimentación de tu documento para el proyecto con el código $codigo_anteproyecto y el título $tituloproyecto. Te invitamos a revisar los comentarios y realizar las correcciones necesarias.";

                    $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);

                         
                    if (!$enviado2) {
                        $correosEnviados = false; // Si algún correo no se envía, marcar como falso
                    }

                }

                    if ($correosEnviados) {
                        $mensaje = [
                            "Alerta" => "Recargar",
                            "Titulo" => "Correos Enviados",
                            "Texto" => "Se ha notificado a los estudinates asignados, retroalimentación cargada con exito",
                            "Tipo" => "success"
                        ];
                    } else {
                        $mensaje = [
                            "Alerta" => "simple",
                            "Titulo" => "Error en el Envío",
                            "Texto" => "Hubo un problema al enviar algunos correos, retroalimentación cargada con exito",
                            "Tipo" => "error"
                        ];
                    }
                    
                    echo json_encode($mensaje);
                    exit();


                }else{
                    $mensaje = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado",
                        "Texto" => "No se pudo guardar la información en el sistema",
                        "Tipo" => "warning"
                    ];
                    echo json_encode($mensaje);
                    exit();
                }

            }

        }else{ // si hay documento registrado

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

            $nombre_carpeta = '../Views/document/proyectos/'.$codigo_anteproyecto;

            if (file_exists($nombre_carpeta)) {

                $extension = pathinfo($_FILES['archivo_user_anteproyecto']['name'], PATHINFO_EXTENSION);
                $nombre_nuevo_archivo = $numero_documento_user_logueado . '_' . time() . '.' . $extension;
                $nombre_carpeta = '../Views/document/proyectos/' . $codigo_anteproyecto;
                $ruta_destino = $nombre_carpeta . '/' . $nombre_nuevo_archivo;

                if (move_uploaded_file($_FILES['archivo_user_anteproyecto']['tmp_name'], $ruta_destino)) {

                    $datos = [
                        'numero_documento' => $numero_documento_user_logueado,
                        'codigo_anteproyecto' => $codigo_anteproyecto,
                        'id_documento_cargado' => $id_documento_cargado,
                        'observacion_general' => $observaciones_generales,
                        'estado_revision' => $estado,
                        'nombre_archivo_word' => $nombre_nuevo_archivo,
                        'estado' => $estado_revision,
                        'fecha_entrega' => $fecha_revision
                    ];
        

                    if($estado == 2){ // si es aprobado el proyecto

                        $guardar_retroalimentacion = ProyectoModelo::guardar_retroalimentacion_proyecto_modelo($datos);
        
                        if ($guardar_retroalimentacion) {
        
                            include_once __DIR__ . '/../Mail/enviar-correo.php';
        
                            $correosEnviados = true; 
        
                            $consulta_proyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                                "SELECT 
                                    a.codigo_proyecto,
                                    u.numero_documento,
                                    u.nombre_usuario,
                                    u.apellidos_usuario,
                                    u.correo_usuario,
                                    u.telefono_usuario
                                FROM asignar_estudiante_proyecto a
                                INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                                WHERE a.codigo_proyecto = '$codigo_anteproyecto'"
                            );

                            $actualizar_estado_proyecto = MainModel::ejecutar_consultas_simples(
                                "UPDATE proyectos 
                                SET estado = 'Aprobado'
                                WHERE codigo_proyecto = '$codigo_anteproyecto'"
                            );
        
                            // Iterar sobre cada estudiante y enviar correos
                            while ($row = $consulta_proyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {
                                $nombre_estudiante = $row['nombre_usuario'];
                                $apellido_estudiante = $row['apellidos_usuario'];
                                $correo_usuario_estudiante = $row['correo_usuario'];
        
        
                                $asunto = "Notificación de Aprobación de Proyecto";
                    
                                $message .= "🎉 ¡Felicidades! Tu proyecto ha sido **aprobado con éxito** 🎓✅. Este logro es el resultado de tu esfuerzo, dedicación y perseverancia. Has demostrado tu capacidad para afrontar desafíos académicos y avanzar con determinación hacia tu meta. 🚀✨";

                                $message .= "Ahora, solo queda esperar la programación de la fecha para la **sustentación de tu proyecto**. 📅✨ Mantente atento a las indicaciones y prepárate para esta última etapa, donde podrás demostrar todo el conocimiento y trabajo que has desarrollado. ¡Confíamos en tu éxito y sabemos que harás una gran presentación! 🏆🔥";


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
                                                    <li><b>Código del Proyecto:</b> ' . $codigo_anteproyecto . '</li>
                                                    <li><b>Título del Proyecto:</b> ' . $tituloproyecto . '</li>
                                                </ul>
                                            </div>
                                            
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
        
                                $cuerpo_texto = "Estimado/a $nombre_estudiante $apellido_estudiante, nos complace informarte que tu proyecto con el código $codigo_anteproyecto y el título $tituloproyecto ha sido aprobado con éxito. Felicitaciones por este logro académico.";
        
                                $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);
        
                                if (!$enviado2) {
                                    $correosEnviados = false; // Si algún correo no se envía, marcar como falso
                                }
        
                            }
        
                            // Mostrar el mensaje según el resultado del envío
                            if ($correosEnviados) {
                                $mensaje = [
                                    "Alerta" => "Recargar",
                                    "Titulo" => "Correos Enviados",
                                    "Texto" => "Se ha notificado a los estudiantes asignados que el proyecto fue aprobado con éxito.",
                                    "Tipo" => "success"
                                ];
                            } else {
                                $mensaje = [
                                    "Alerta" => "simple",
                                    "Titulo" => "Error en el Envío",
                                    "Texto" => "Hubo un problema al enviar algunos correos, pero la informacion se guardo correctamente",
                                    "Tipo" => "error"
                                ];
                            }
        
                            echo json_encode($mensaje);
                            exit();
        
                            
        
                        }else{
                            $mensaje = [
                                "Alerta" => "simple",
                                "Titulo" => "Ocurrió un error inesperado",
                                "Texto" => "No se pudo guardar la información en el sistema",
                                "Tipo" => "warning"
                            ];
                            echo json_encode($mensaje);
                            exit();
                        }
                       
        
        
                    }else if($estado == 3){ // proyecto cancelado

                        $guardar_retroalimentacion = ProyectoModelo::guardar_retroalimentacion_proyecto_modelo($datos);
        
                        if ($guardar_retroalimentacion) {
                            
                            // actualizamos el estado del proyecto 
                            $actualizar_estado_proyecto = MainModel::ejecutar_consultas_simples(
                                "UPDATE proyectos 
                                SET estado = 'Cancelado'
                                WHERE codigo_proyecto = '$codigo_anteproyecto'"
                            );
        
                            if ($actualizar_estado_proyecto) {
        
                                include_once __DIR__ . '/../Mail/enviar-correo.php';
        
                                $correosEnviados = true;
                            
                                $consulta_proyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                                    "SELECT 
                                        a.codigo_proyecto,
                                        u.numero_documento,
                                        u.nombre_usuario,
                                        u.apellidos_usuario,
                                        u.correo_usuario,
                                        u.telefono_usuario
                                    FROM asignar_estudiante_proyecto a
                                    INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                                    WHERE a.codigo_proyecto = '$codigo_anteproyecto'"
                                );
                                while ($row = $consulta_proyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {
                                    $nombre_estudiante = $row['nombre_usuario'];
                                    $apellido_estudiante = $row['apellidos_usuario'];
                                    $correo_usuario_estudiante = $row['correo_usuario'];
        
                                    $asunto = "Notificación de Cancelación de Proyecto";

                                    $message .= "Lamentamos informarle que su proyecto ha sido cancelado ❌. Sabemos que esta noticia puede ser desmotivadora, pero cada obstáculo es una oportunidad para mejorar y fortalecerse. 💪✨";
        
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
                                                    <li><b>Código del proyecto:</b> ' . $codigo_anteproyecto . '</li>
                                                        <li><b>Título del proyecto:</b> ' . $tituloproyecto . '</li>
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
        
                                    $cuerpo_texto = "Estimado/a $nombre_estudiante $apellido_estudiante, lamentamos informarte que tu proyecto con el código $codigo_anteproyecto y el título $tituloproyecto ha sido cancelado debido a observaciones realizadas durante su evaluación. Te invitamos a revisar los comentarios proporcionados y realizar las correcciones necesarias.";
        
                                    $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);
        
                                    if (!$enviado2) {
                                        $correosEnviados = false; // Si algún correo no se envía, marcar como falso
                                    }
        
                                }
        
                                if ($correosEnviados) {
                                    $mensaje = [
                                        "Alerta" => "Recargar",
                                        "Titulo" => "Correos Enviados",
                                        "Texto" => "Se ha notificado a los estudinates asignados, que el proyecto fue cancelado",
                                        "Tipo" => "success"
                                    ];
                                } else {
                                    $mensaje = [
                                        "Alerta" => "simple",
                                        "Titulo" => "Error en el Envío",
                                        "Texto" => "Hubo un problema al enviar algunos correos, retroalimentación cargada con exito",
                                        "Tipo" => "error"
                                    ];
                                }
                                
                                echo json_encode($mensaje);
                                exit();
        
                            }else{
                                $mensaje = [
                                    "Alerta" => "simple",
                                    "Titulo" => "Ocurrió un error inesperado",
                                    "Texto" => "No se pudo actualizar el estado del proyecto  en el sistema",
                                    "Tipo" => "warning"
                                ];
                                echo json_encode($mensaje);
                                exit();
                            }
        
        
                        }else{
                            $mensaje = [
                                "Alerta" => "simple",
                                "Titulo" => "Ocurrió un error inesperado",
                                "Texto" => "No se pudo guardar la información en el sistema",
                                "Tipo" => "warning"
                            ];
                            echo json_encode($mensaje);
                            exit();
                        }
        
        
                    }else{ // proyecto en revision

                        $guardar_retroalimentacion = ProyectoModelo::guardar_retroalimentacion_proyecto_modelo($datos);
        
                        if ($guardar_retroalimentacion) {
                            
                            include_once __DIR__ . '/../Mail/enviar-correo.php';
        
                            $correosEnviados = true;
        
                            $consulta_proyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                                "SELECT 
                                    a.codigo_proyecto,
                                    u.numero_documento,
                                    u.nombre_usuario,
                                    u.apellidos_usuario,
                                    u.correo_usuario,
                                    u.telefono_usuario
                                FROM asignar_estudiante_proyecto a
                                INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                                WHERE a.codigo_proyecto = '$codigo_anteproyecto'"
                            );
        
                            while ($row = $consulta_proyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {
                                $nombre_estudiante = $row['nombre_usuario'];
                                $apellido_estudiante = $row['apellidos_usuario'];
                                $correo_usuario_estudiante = $row['correo_usuario'];
        
                                $asunto = "Notificación de Retroalimentación de Documento";

                                $message .= "Su proyecto ha recibido retroalimentación por parte del asesor 📌✍️. Este es un paso clave para fortalecer su trabajo y encaminarlo hacia el éxito. 🚀";
            
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
                                            <li><b>Código del Proyecto:</b> ' . $codigo_anteproyecto . '</li>
                                                <li><b>Título del Proyecto:</b> ' . $tituloproyecto . '</li>
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

                            
                            $cuerpo_texto = "Estimado $nombre_estudiante $apellido_estudiante, te informamos que se ha realizado la retroalimentación de tu documento para el proyecto con el código $codigo_anteproyecto y el título $tituloproyecto. Te invitamos a revisar los comentarios y realizar las correcciones necesarias.";
                
                            $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);
                                
                            if (!$enviado2) {
                                $correosEnviados = false; // Si algún correo no se envía, marcar como falso
                            }


                            }
        
                            if ($correosEnviados) {
                                $mensaje = [
                                    "Alerta" => "Recargar",
                                    "Titulo" => "Correos Enviados",
                                    "Texto" => "Se ha notificado a los estudinates asignados, retroalimentación cargada con exito",
                                    "Tipo" => "success"
                                ];
                            } else {
                                $mensaje = [
                                    "Alerta" => "simple",
                                    "Titulo" => "Error en el Envío",
                                    "Texto" => "Hubo un problema al enviar algunos correos, retroalimentación cargada con exito",
                                    "Tipo" => "error"
                                ];
                            }
                            
                            echo json_encode($mensaje);
                            exit();
        
        
                        }else{
                            $mensaje = [
                                "Alerta" => "simple",
                                "Titulo" => "Ocurrió un error inesperado",
                                "Texto" => "No se pudo guardar la información en el sistema",
                                "Tipo" => "warning"
                            ];
                            echo json_encode($mensaje);
                            exit();
                        }
        
                    }

                }else{
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado",
                        "Texto" => "No se pudo guardar los documentos en la carpeta",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

            }else{
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "No se encontro la ruta para guardar los documentos",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

        }



    }

    public function actualizar_fecha_retroalimentacion(){
        $fecha_revision = MainModel::limpiar_cadenas($_POST['fecha_revision']); 
        $id_retrolimentacion_editar = MainModel::limpiar_cadenas($_POST['id_retrolimentacion_editar_proyecto']);
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
            "UPDATE retroalimentacion_proyecto 
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
    public function cargar_evidencias_reuniones_proyectos(){
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
        $check_codigo = MainModel::ejecutar_consultas_simples("SELECT * FROM proyectos WHERE codigo_proyecto = '$codigo'");
    
        if ($check_usuario->rowCount() <= 0 || $check_codigo->rowCount() == 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El usuario o el código del proyecto no existen en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
    
        // Crear carpeta si no existe
        $nombre_carpeta = '../Views/document/proyectos/' . $codigo . '/evidencia/';
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
                    'codigo_proyecto' => $codigo,
                    'imagenes' => $nombre_unico,
                    'fecha_creacion' => date('Y-m-d')
                ];
                if (!ProyectoModelo::cargar_evidencia_reunion_modelo($datos)) {
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

    public function actualizar_estado_proyectos (){
        
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
        


        $estado = MainModel::limpiar_cadenas($_POST['actualizar_estado_proyecto']);
        $codigo = MainModel::limpiar_cadenas($_POST['codigo_proyecto_upd_estado']);
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
            "SELECT * FROM proyectos WHERE codigo_proyecto = '$codigo'"
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
            "UPDATE proyectos 
            SET estado = '$estado'
            WHERE codigo_proyecto = '$codigo'"
        );

        if($actualizar_estado_anteproyecto->rowCount() > 0){
            
            include __DIR__ . '/../Mail/enviar-correo.php';

            $correosEnviados = true; // Variable para comprobar si todos los correos se enviaron con éxito
             // 3. Consultar los estudiantes asignados al anteproyecto
             $consulta_anteproyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                "SELECT 
                    a.codigo_proyecto,
                    u.numero_documento,
                    u.nombre_usuario,
                    u.apellidos_usuario,
                    u.correo_usuario,
                    u.telefono_usuario
                FROM asignar_estudiante_proyecto a
                INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                WHERE a.codigo_proyecto = '$codigo'"
            );

            while ($row = $consulta_anteproyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {

                // Extraer datos del estudiante
                $nombre_estudiante = $row['nombre_usuario'];
                $apellido_estudiante = $row['apellidos_usuario'];
                $correo_usuario_estudiante = $row['correo_usuario'];

                $asunto = "Notificación de Actualización de Estado de Proyecto";

                $cuerpo_html = '
                <html>
                <head>
                      <title>Bienvenido a nuestra plataforma</title>'
                            .STYLESCORREO.'
                </head>
                <body>
                    <div class="email-header">
                        <img src="'.SERVERURL.'Views/assets/images/'.$logo.'" alt="Logo Universidad">
                        <h2>Actualización de Estado de Proyecto</h2>
                    </div>
                    <div class="email-body">
                        <p>Estimado/a <b>' . $nombre_estudiante . ' ' . $apellido_estudiante . '</b>,</p>
                        <p>Te informamos que el estado de tu <b>Proyecto</b> ha sido actualizado. Es fundamental que prestes atención a las recomendaciones proporcionadas por tu asesor para evitar posibles cancelaciones en el futuro.</p>
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

                $cuerpo_texto = "Hola $nombre_estudiante $apellido_estudiante, Te informamos que el estado de tu proyecto ha sido actualizado. Por favor, sigue las recomendaciones de tu asesor para evitar posibles cancelaciones en el futuro.";

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

    public function delete_evidecia_proyecto(){

        $codigo = MainModel::limpiar_cadenas($_POST['delete_evidencia_proyectos']);

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
            "SELECT codigo_proyecto FROM evidencia_reuniones_proyectos WHERE codigo_proyecto = '$codigo'"
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
            FROM evidencia_reuniones_proyectos 
            WHERE codigo_proyecto = '$codigo' 
            AND DATE(fecha_creacion) = '$fecha'"
        );
        
        // Verificar si hay datos
        if ($extraer_datos->rowCount() > 0) {
            $datos = $extraer_datos->fetchAll(PDO::FETCH_ASSOC);
            $errorEliminar = false; // Bandera para verificar si hubo errores al eliminar archivos
        
            foreach ($datos as $fila) {
                $imagen = trim($fila['imagenes']); // Eliminar espacios en blanco
                if (!empty($imagen)) { // Verifica que la imagen no esté vacía
                    $rutaImagen = '../Views/document/proyectos/' . $codigo . '/evidencia/' . $imagen;
            
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
                    "DELETE FROM evidencia_reuniones_proyectos 
                    WHERE codigo_proyecto = '$codigo' 
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

    public function calificacion_rubrica_evaluacion(){

        $identificador_jurado_evaluador = MainModel::limpiar_cadenas($_POST['identificador_jurado_evaluador']);
        $identificador_jurado_evaluador = MainModel::decryption($identificador_jurado_evaluador);

        $codigo_proyecto = MainModel::limpiar_cadenas($_POST['codigo_Proyecto']);
        $codigo_proyecto = MainModel::decryption($codigo_proyecto);

        if (empty($identificador_jurado_evaluador) || empty($codigo_proyecto) ) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ]);
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $evaluacion = [];
            $errores = [];

            // Secciones a capturar
            $secciones = ["titulo", "problema", "justificacion", "objetivos", "marco", "diseno", "resultados", "referencias", "anexos", "sustentacion"];

            foreach ($secciones as $seccion) {
                foreach ($_POST as $key => $value) {
                    if (strpos($key, "item_{$seccion}_") === 0) {
                        $id = str_replace("item_{$seccion}_", '', $key);
                        $observacion_key = "observacion_{$seccion}_$id";
            
                        $calificacion = $_POST[$key] ?? null;
                        $observacion = $_POST[$observacion_key] ?? "";
            
                        // ✅ Verificar que la calificación no esté vacía y que no sea "0"
                        if (trim($calificacion) === ""  ) { 
                            $errores[] = [
                                "mensaje" => "El campo <b>$key</b> no puede estar vacío ni ser 0.",
                                "icono" => "warning" // Puedes usar también "error" o cualquier clave que represente un ícono
                            ];
                        }
            
                        // ✅ La observación puede estar vacía, así que no se valida como error
                        $evaluacion[$seccion][$id] = [
                            "calificacion" => (int)$calificacion,
                            "observacion" => $observacion
                        ];
                    }
                }
            }
            
            

            if (!empty($errores)) {
                echo json_encode([
                    "Alerta" => "errores",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Errores" => $errores, // Enviamos los errores como array
                    "Tipo" => "error"
                ]);
                exit();
            }

           
            

            // Convertir evaluación a JSON
            $json_evaluacion = json_encode($evaluacion, JSON_UNESCAPED_UNICODE);

            // Verificar si ya existe un registro del proyecto en la base de datos
            $check_codigo = MainModel::ejecutar_consultas_simples("SELECT * FROM evaluaciones_proyectos WHERE codigo_proyecto = '$codigo_proyecto'");

            if ($check_codigo->rowCount() > 0) {
                // Si ya existe, obtener los datos actuales
                $registro = $check_codigo->fetch();

                // Verificar qué campo actualizar según el jurado
                if ($identificador_jurado_evaluador == 1) {
                    $nuevo_valor = json_encode(array_merge(json_decode($registro['evaluacion_jurado1'], true) ?? [], $evaluacion), JSON_UNESCAPED_UNICODE);
                    $actualizar = MainModel::ejecutar_consultas_simples("UPDATE evaluaciones_proyectos SET evaluacion_jurado1 = '$nuevo_valor' WHERE codigo_proyecto = '$codigo_proyecto'");
                } else {
                    $nuevo_valor = json_encode(array_merge(json_decode($registro['evaluacion_jurado2'], true) ?? [], $evaluacion), JSON_UNESCAPED_UNICODE);
                    $actualizar = MainModel::ejecutar_consultas_simples("UPDATE evaluaciones_proyectos SET evaluacion_jurado2 = '$nuevo_valor' WHERE codigo_proyecto = '$codigo_proyecto'");
                }

                if ($actualizar->rowCount() > 0) {
                    echo json_encode([
                        "Alerta" => "simple",
                        "Titulo" => "Actualización exitosa",
                        "Texto" => "La evaluación ha sido actualizada correctamente.",
                        "Tipo" => "success",
                        "Valores" => $evaluacion
                    ]);
                } else {
                    echo json_encode([
                        "Alerta" => "simple",
                        "Titulo" => "Error",
                        "Texto" => "No se pudo actualizar la evaluación.",
                        "Tipo" => "error"
                    ]);
                }
            } else {

               // Si no existe, crear el registro con los datos del jurado correspondiente
               

                // Si el jurado es 1, su evaluación se guarda y el jurado 2 queda con "{}"
                $campo_jurado1 = ($identificador_jurado_evaluador == 1) ? "'$json_evaluacion'" : "'{}'";
                $campo_jurado2 = ($identificador_jurado_evaluador == 2) ? "'$json_evaluacion'" : "'{}'";

                // Resumen general también se inicializa con "{}"
                $resumen_general = "'{}'";

                $fecha_actual = '';

                // Guardar en la base de datos
                $guardar = MainModel::ejecutar_consultas_simples("INSERT INTO evaluaciones_proyectos (codigo_proyecto, resumen_general, evaluacion_jurado1, evaluacion_jurado2, fecha) 
                    VALUES ('$codigo_proyecto',$resumen_general, $campo_jurado1, $campo_jurado2, '$fecha_actual')
                ");

                if ($guardar->rowCount() > 0) {
                    echo json_encode([
                        "Alerta" => "Recargar",
                        "Titulo" => "Registro exitoso",
                        "Texto" => "La evaluación ha sido guardada correctamente.",
                        "Tipo" => "success",
                        "Valores" => $evaluacion
                    ]);
                } else {
                    echo json_encode([
                        "Alerta" => "simple",
                        "Titulo" => "Error",
                        "Texto" => "No se pudo guardar la evaluación.",
                        "Tipo" => "error"
                    ]);
                }
            }

            exit();
        }

    }

    public function registrar_acta_calificacion_proyectos(){
        $number_acta = MainModel::limpiar_cadenas($_POST['number_acta']);
        $codigo_proyecto = MainModel::limpiar_cadenas($_POST['codigo_Proyecto']);

        $codigo_proyecto = MainModel::decryption($codigo_proyecto);

        if (empty($number_acta) || empty($codigo_proyecto)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios.",
                "Tipo" => "error"
            ]);
            exit();
        }

         // Verificar si ya existe un registro del proyecto en la base de datos
         $check_codigo = MainModel::ejecutar_consultas_simples("SELECT * FROM proyectos WHERE codigo_proyecto = '$codigo_proyecto'");
        
         if ($check_codigo->rowCount() == 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "❌ El código que intentas registrar no existe en la base de datos.",
                "Tipo" => "error"
            ]);
            exit();
        }


       // Paso 1: Verificar si ya existe un registro para el proyecto
            $verificar = MainModel::ejecutar_consultas_simples("
            SELECT id FROM evaluaciones_proyectos WHERE codigo_proyecto = '$codigo_proyecto'
            ");

            $fecha_actual = date("Y-m-d H:i:s");

            if ($verificar->rowCount() > 0) {
            // Paso 2: Actualizar si ya existe
            $actualizar = MainModel::ejecutar_consultas_simples("UPDATE evaluaciones_proyectos 
                SET resumen_general = '$number_acta' 
                WHERE codigo_proyecto = '$codigo_proyecto'
            ");
            

            if ($actualizar->rowCount() > 0) {
                echo json_encode([
                    "Alerta" => "Recargar",
                    "Titulo" => "Actualización exitosa",
                    "Texto" => "El valor del acta ha sido actualizada correctamente.",
                    "Tipo" => "success"
                ]);
            } else {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Sin cambios",
                    "Texto" => "No se realizaron modificaciones.",
                    "Tipo" => "info"
                ]);
            }


            exit();

            } else {
            // Paso 3: Insertar si no existe
            $insertar = MainModel::ejecutar_consultas_simples("
                INSERT INTO evaluaciones_proyectos 
                (codigo_proyecto, resumen_general, evaluacion_jurado1, evaluacion_jurado2, fecha) 
                VALUES 
                ('$codigo_proyecto', '$number_acta', '{}', '{}', '$fecha_actual')
            ");

            if ($insertar->rowCount() > 0) {
                echo json_encode([
                    "Alerta" => "Recargar",
                    "Titulo" => "Registro exitoso",
                    "Texto" => "El valor del acta ha sido registrada correctamente.",
                    "Tipo" => "success"
                ]);
            } else {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto" => "No se pudo registrar el valor del acta.",
                    "Tipo" => "error"
                ]);
            }
            }


        exit();




    }

    
    public function actualizar_opcion_jurados(){

        $opcion_jurado = MainModel::limpiar_cadenas($_POST['opcion_jurado']);
        $codigo_proyecto = MainModel::limpiar_cadenas($_POST['codigo_Proyecto']);
        $documento_jurado = MainModel::limpiar_cadenas($_POST['documento_jurado']);

        $codigo_proyecto = MainModel::decryption($codigo_proyecto);
        $documento_jurado = MainModel::decryption($documento_jurado);

        if (empty($opcion_jurado) || empty($codigo_proyecto) || empty($documento_jurado)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "⚠️ Todos los campos son obligatorios.",
                "Tipo" => "error"
            ]);
            exit();
        }

        // Definir el rango permitido (1 y 2 en este caso)
        $opciones_validas = [1, 2];

        if (!in_array($opcion_jurado, $opciones_validas)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "⚠️ La opción seleccionada no es válida. Debes elegir Jurado 1 o Jurado 2.",
                "Tipo" => "error"
            ]);
            exit();
        }

        // Verificar que el usuario exista en la tabla usuarios
        $check_usuario = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM usuarios WHERE numero_documento = '$documento_jurado'"
        );

        if ($check_usuario->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "❌ El número de documento del usuario no existe en el sistema.",
                "Tipo" => "error"
            ]);
            exit();
        }

        // Obtener la información de los jurados asignados al proyecto
        $check_jurados = MainModel::ejecutar_consultas_simples(
            "SELECT numero_documento, opcion_jurado FROM Asignar_jurados_proyecto 
            WHERE codigo_proyecto = '$codigo_proyecto'"
        );
        if ($check_jurados->rowCount() < 2) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "❌ No hay suficientes jurados asignados a este proyecto, tienen que ser 2 jurados",
                "Tipo" => "error"
            ]);
            exit();
        }

        $jurados = $check_jurados->fetchAll(PDO::FETCH_ASSOC);

        // Determinar la otra opción para el segundo jurado
        $opcion_contraria = ($opcion_jurado == 1) ? 2 : 1;
        $otro_jurado = null;

        // Buscar el otro jurado asignado
        foreach ($jurados as $jurado) {
            if ($jurado['numero_documento'] != $documento_jurado) {
                $otro_jurado = $jurado['numero_documento'];
                break;
            }
        }

        // Actualizar la opción del jurado seleccionado
        $actualizar_jurado = MainModel::ejecutar_consultas_simples(
            "UPDATE Asignar_jurados_proyecto 
            SET opcion_jurado = '$opcion_jurado' 
            WHERE numero_documento = '$documento_jurado' AND codigo_proyecto = '$codigo_proyecto'"
        );

        // Si hay otro jurado, asignarle la opción contraria
        if ($otro_jurado) {
            $actualizar_otro_jurado = MainModel::ejecutar_consultas_simples(
                "UPDATE Asignar_jurados_proyecto 
                SET opcion_jurado = '$opcion_contraria' 
                WHERE numero_documento = '$otro_jurado' AND codigo_proyecto = '$codigo_proyecto'"
            );
        }

        if ($actualizar_jurado->rowCount() > 0) {
            echo json_encode([
                "Alerta" => "Recargar",
                "Titulo" => "Éxito",
                "Texto" => "✅ La opción del jurado ha sido actualizada correctamente. El otro jurado ha sido asignado automáticamente.",
                "Tipo" => "success"
            ]);
        } else {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "❌ No se pudo actualizar la opción del jurado. Inténtalo nuevamente.",
                "Tipo" => "error"
            ]);
        }
        exit();


       
    }

    public function actualizar_fecha_sustentacion(){

            
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

        $nueva_fecha_sustentacion = MainModel::limpiar_cadenas($_POST['nueva_fecha_sustentacion']);
        $codigo_proyecto = MainModel::limpiar_cadenas($_POST['codigo_proyecto']);
        
        $codigo_proyecto = MainModel::decryption($codigo_proyecto);
        
        if (empty($codigo_proyecto) || empty($nueva_fecha_sustentacion)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Todos los campos son obligatorios. ",
                "Tipo" => "error"
            ]);
            exit();
        }
        
        // Verificar si el proyecto existe
        $verificar_proyecto = MainModel::ejecutar_consultas_simples("
            SELECT * FROM evaluaciones_proyectos 
            WHERE codigo_proyecto = '$codigo_proyecto'
        ");
        
        if ($verificar_proyecto->rowCount() == 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "No encontrado",
                "Texto" => "El proyecto no existe en la base de datos.",
                "Tipo" => "error"
            ]);
            exit();
        }
        
        // Actualizar la fecha
        $actualizar_fecha = MainModel::ejecutar_consultas_simples("
            UPDATE evaluaciones_proyectos 
            SET fecha = '$nueva_fecha_sustentacion' 
            WHERE codigo_proyecto = '$codigo_proyecto'
        ");
        
        if ($actualizar_fecha->rowCount() > 0) {


            include_once __DIR__ . '/../Mail/enviar-correo.php';

            $correosEnviados = true; 

            $consulta_proyecto_estudiantes = MainModel::ejecutar_consultas_simples(
                "SELECT 
                    a.codigo_proyecto,
                    u.numero_documento,
                    u.nombre_usuario,
                    u.apellidos_usuario,
                    u.correo_usuario,
                    u.telefono_usuario
                FROM asignar_estudiante_proyecto a
                INNER JOIN usuarios u ON a.numero_documento = u.numero_documento
                WHERE a.codigo_proyecto = '$codigo_proyecto'"
            );

           

            // Iterar sobre cada estudiante y enviar correos
            while ($row = $consulta_proyecto_estudiantes->fetch(PDO::FETCH_ASSOC)) {
                $nombre_estudiante = $row['nombre_usuario'];
                $apellido_estudiante = $row['apellidos_usuario'];
                $correo_usuario_estudiante = $row['correo_usuario'];

                $asunto = "📅 Notificación: Fecha de Sustentación Asignada para tu Proyecto";

                $message = "Te informamos que ya ha sido programada la fecha para la **sustentación de tu proyecto** 🎓. Este es un paso fundamental en tu proceso académico, donde tendrás la oportunidad de presentar los resultados de tu trabajo ante los jurados asignados.";
                
                $message .= "🗓️ **Fecha de Sustentación:** " . $nueva_fecha_sustentacion; // asegúrate de definir esta variable previamente
                
                $message .= "  Prepárate con tiempo, revisa todas las observaciones previas y asegúrate de contar con una presentación clara y sólida. ¡Estamos seguros de que harás un excelente trabajo! 💪✨";
                

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
                           
                            <div class="fecha-box">
                                <p><strong>🗓️ Fecha de Sustentación:</strong> <span style="color:#0a9396;">' . $nueva_fecha_sustentacion . '</span></p>
                            </div>

                            <p>Te invitamos a preparar con dedicación tu presentación, tener en cuenta las retroalimentaciones recibidas y demostrar todo tu conocimiento y compromiso.</p>

                            <p>¡Mucho éxito! 💼🎓</p>


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

                $cuerpo_texto = "Estimado/a $nombre_estudiante $apellido_estudiante, nos complace informarte que tu proyecto con el código $codigo_proyecto  ya se le asigno una fecha de sustentación";

                $enviado2 = enviarCorreo($correo_usuario_estudiante, $nombre_estudiante, $apellido_estudiante, $asunto, $cuerpo_html, $cuerpo_texto);

                if (!$enviado2) {
                    $correosEnviados = false; // Si algún correo no se envía, marcar como falso
                }

            }

            // Mostrar el mensaje según el resultado del envío
            if ($correosEnviados) {
            echo json_encode([
                "Alerta" => "Recargar",
                "Titulo" => "Fecha actualizada",
                "Texto" => "La fecha de sustentación fue actualizada correctamente y se han notifiado a los estudiantes",
                "Tipo" => "success"
            ]);
            } else {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error en el Envío",
                    "Texto" => "Hubo un problema al enviar algunos correos, pero la informacion se guardo correctamente",
                    "Tipo" => "error"
                ]);
            }
            exit();


        
        } else {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Sin cambios",
                "Texto" => "No se realizaron cambios. La fecha puede ser igual a la anterior.",
                "Tipo" => "info"
            ]);
        }
        exit();
        


       
    }
 
}