<?php

if ($peticionAjax) {

    require_once "../Model/UsuarioModelo.php";
} else {

    require_once "./Model/UsuarioModelo.php";
}

class UsuarioControlador extends UsuarioModelo
{

    /****************Controlador Para Agregar los usuarios ***********************/

    public function agregar_usuario_controlador()
    {

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



        // Validar los datos del formulario
        $numero_documento = MainModel::limpiar_cadenas($_POST['documento_usuario_reg']);
        $nombre_usuario = MainModel::limpiar_cadenas($_POST['nombre_usuario_reg']);
        $apellido_usuario = MainModel::limpiar_cadenas($_POST['apellido_usuario_reg']);
        $correo_usuario = MainModel::limpiar_cadenas($_POST['correo_usuarrio_reg']);
        $telefono_usuario = MainModel::limpiar_cadenas($_POST['tefelefono_usuario_reg']);
        $tipo_usuario = MainModel::limpiar_cadenas($_POST['tipo_usuario_reg']);
        $password_usuario = MainModel::limpiar_cadenas($_POST['password_usuario_reg']);
        $confirm_password_usuario = MainModel::limpiar_cadenas($_POST['confirm-password_usuario_reg']);
        $imagen_usuario = "AvatarNone.png";
        $estado = 1;



        if (
            empty($numero_documento) || empty($nombre_usuario) || empty($apellido_usuario) ||
            empty($correo_usuario) || empty($telefono_usuario) || empty($tipo_usuario) ||
            empty($password_usuario) || empty($confirm_password_usuario)
        ) {

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios  ",
                "Tipo" => "error"
            ];

            echo json_encode($alerta);
            exit();
        }


        // Verificar número de documento (ejemplo: solo números de entre 10 y 20 dígitos)
        if (MainModel::verificar_datos("[0-9]{8,20}$", $numero_documento)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El Número de documento no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Verificar nombre de usuario (ejemplo: solo letras y espacios, longitud entre 3 y 50 caracteres)
        if (MainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,50}$", $nombre_usuario)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El Nombre de usuario no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Verificar apellidos de usuario (similar a la verificación del nombre)
        if (MainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,50}$", $apellido_usuario)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Los Apellidos de usuario no coinciden con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Verificar teléfono (ejemplo: solo números de entre 7 y 15 dígitos)
        if (MainModel::verificar_datos("[0-9]{7,15}$", $telefono_usuario)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El Teléfono de usuario no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Validar el formato del correo electrónico
        if (!filter_var($correo_usuario, FILTER_VALIDATE_EMAIL)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El formato del correo electrónico no es válido.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_correo_usuario = MainModel::ejecutar_consultas_simples("SELECT correo_usuario FROM usuarios WHERE correo_usuario = '$correo_usuario'");

        if ($check_correo_usuario->rowCount() >= 1) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Ya existe un usuario registrado con ese correo electrónico",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).{8,}$/', $password_usuario)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "La contraseña debe tener al menos 8 caracteres, incluir una letra mayúscula, un número y un símbolo especial.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        // Validar que las contraseñas coincidan
        if ($password_usuario !== $confirm_password_usuario) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Las contraseñas no coinciden.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }



        /************** validamos el tipo de usuario este desencriptado y que vedaderamente exista en la base de datos ********** */

        $tipo_usuario = (int) MainModel::decryption($tipo_usuario);

        $check_tipo_usuario = MainModel::ejecutar_consultas_simples("SELECT id_rol FROM roles_usuarios WHERE id_rol = '$tipo_usuario'");


        if ($check_tipo_usuario->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El tipo de usuario seleccionado no existe en la base de datos.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $check_numero_documento_usuario = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM usuarios WHERE numero_documento = '$numero_documento'"
        );

        if ($check_numero_documento_usuario->rowCount() >= 1) {

            // Si el número de documento existe, extraer sus datos
            $datos_usuario_registrado = $check_numero_documento_usuario->fetch(PDO::FETCH_ASSOC);
            // Ejemplo: Imprimir los datos (puedes usar estos datos como necesites)
            $rol_usuario_registrado = $datos_usuario_registrado['id_rol'];
            $nombre_usuario_registrao = $datos_usuario_registrado['nombre_usuario'];
            $apellido_usuario_registrao = $datos_usuario_registrado['apellidos_usuario'];
            $correo_usuario_registrao = $datos_usuario_registrado['correo_usuario'];
            $telefono_usuario_registrao = $datos_usuario_registrado['telefono_usuario'];
            $contraseña_usuario_registrao = $datos_usuario_registrado['contrasena_usuario'];

            if ($rol_usuario_registrado == 1) {

                // Consulta para validar si el número de documento y el rol ya existen
                $consulta_validar_rol = MainModel::ejecutar_consultas_simples(
                    "SELECT * FROM usuarios WHERE numero_documento = '$numero_documento' AND id_rol = '$tipo_usuario'"
                );

                if ($consulta_validar_rol->rowCount() > 0) {
                    // Si ya existe, mostrar mensaje de error
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Rol ya asignado",
                        "Texto" => "El administrador ya tiene asignado el rol seleccionado y no se puede registrar nuevamente.",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                if ($tipo_usuario == 2) {

                    $datos_usuario = [
                        "numero_documento" => $numero_documento,
                        "nombre_usuario" => $nombre_usuario_registrao,
                        "apellido_usuario" => $apellido_usuario_registrao,
                        "correo_usuario" => $correo_usuario_registrao,
                        "telefono_usuario" => $telefono_usuario_registrao,
                        "tipo_usuario" => (int) $tipo_usuario,
                        "password_usuario" => $contraseña_usuario_registrao,
                        "estado_usuario" => $estado,
                        "imagen_usuario" => $imagen_usuario,
                    ];

                    $agregar_usuario = UsuarioModelo::Agregar_usuarios_modelo($datos_usuario);

                    if ($agregar_usuario->rowCount() >= 1) {

                        $password_usuario = mainModel::decryption($contraseña_usuario_registrao);

                        $message = "<p>¡Bienvenido a nuestra plataforma! Como coordinador, tu liderazgo y visión son esenciales para garantizar que cada proyecto académico avance con éxito. 📌✨</p>

                        <p>Desde este espacio, podrás supervisar, organizar y dar seguimiento a los proyectos de grado, asegurando que cada estudiante reciba el apoyo y las herramientas necesarias para su desarrollo profesional.</p>
                    
                        <p>Tu compromiso impulsa la excelencia académica y permite que cada idea se convierta en un logro. <b>Gracias a tu trabajo, los estudiantes tienen la oportunidad de alcanzar sus metas y construir un futuro brillante.</b> 🚀🔥</p>";

                        include __DIR__ . '/../Mail/enviar-correo.php';

                        $asunto = "Bienvenido a nuestra plataforma";

                        $cuerpo_html = '
                
                        <!DOCTYPE html>
                        <html lang="es">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Bienvenido a nuestra plataforma</title>'
                            . STYLESCORREO . '
                        </head>
                        <body>
                            <div class="email-container">
                                <div class="email-header">
                                    <img src="' . SERVERURL . 'Views/assets/images/' . $logo . '" alt="Logo Universidad">
                                    <h2>¡Notificación de registro en la plataforma!</h2>
                                </div>
                                <div class="email-body">
                                    <p><b>Estimado  ' . $nombre_usuario_registrao . ' ' . $apellido_usuario_registrao . ',</b></p>
                                    ' . $message . '
                                    <h3>🔑 Tus credenciales de acceso</h3>
                                    <div class="credentials">
                                        <ul>
                                            <li><b>Usuario:</b> ' . $numero_documento . '</li>
                                            <li><b>Contraseña:</b> ' . $password_usuario . '</li>
                                        </ul>
                                    </div>
        
                                    <p class="highlight"><b>Importante:</b> Asegúrate de cambiar tu contraseña tras el primer inicio de sesión para proteger tu cuenta.</p>
        
                                    <p>Ya puedes acceder a la plataforma y comenzar a explorar todas sus funcionalidades. Haz clic en el siguiente botón para iniciar sesión:</p>
        
                                    <a href="' . SERVERURL . 'login" class="login-button">Iniciar sesión</a>
        
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

                        $cuerpo_texto = "Hola $nombre_usuario" . "$apellido_usuario, bienvenido a nuestra plataforma.";

                        $enviado = enviarCorreo($correo_usuario_registrao, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);

                        if ($enviado) {
                            $alerta = [
                                "Alerta" => "Recargar",
                                "Titulo" => "Registro exitoso",
                                "Texto" => "El registro del usuario $nombre_usuario " . " $apellido_usuario se ha completado exitosamente. Se le ha enviado un correo electrónico de verificación con sus credenciales de acceso.",
                                "Tipo" => "success"
                            ];
                        } else {
                            $alerta = [
                                "Alerta" => "simple",
                                "Titulo" => "Ocurrió un error",
                                "Texto" => "El usuario se ha registrado correctamente, pero no se pudo enviar el correo.",
                                "Tipo" => "warning"
                            ];
                        }

                        echo json_encode($alerta);
                        exit();
                    } else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "No se pudo agregar el usuario",
                            "Tipo" => "error"
                        ];

                        echo json_encode($alerta);
                        exit();
                    }
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Rol no permitido",
                        "Texto" => "El administrador no puede tener otro rol diferente a 'Administrador' o 'Coordinador'. Por favor, verifica los datos ingresados.",
                        "Tipo" => "warning"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El número de documento ya está registrado en el sistema.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        } else {
            // Continuar con el registro del nuevo usuario si no existe
            $datos_usuario = [
                "numero_documento" => $numero_documento,
                "nombre_usuario" => $nombre_usuario,
                "apellido_usuario" => $apellido_usuario,
                "correo_usuario" => $correo_usuario,
                "telefono_usuario" => $telefono_usuario,
                "tipo_usuario" => (int) $tipo_usuario,
                "password_usuario" => MainModel::encryption($password_usuario),
                "estado_usuario" => $estado,
                "imagen_usuario" => $imagen_usuario,
            ];

            $agregar_usuario = UsuarioModelo::Agregar_usuarios_modelo($datos_usuario);

            if ($agregar_usuario->rowCount() >= 1) {

                $contraseña_usuario_porcesda =  UsuarioModelo::ocultar_contrasena($password_usuario);

                if ($tipo_usuario == 4 || $tipo_usuario == 3) {
                    $message = "¡Estás a un paso más de alcanzar tus metas académicas! Sigue adelante, el éxito está más cerca de lo que piensas. Con esfuerzo y dedicación, cada día estás más cerca de completar tu carrera. ¡Sigue así, tienes todo nuestro apoyo!";
                } else if ($tipo_usuario == 5) {
                    $message = "Tu guía y compromiso son clave para que los estudiantes alcancen sus objetivos académicos. Gracias a tu apoyo, estás marcando una diferencia significativa en su formación. Sigue inspirando con tu experiencia y dedicación. ¡El impacto de tu labor es invaluable!";
                } else if ($tipo_usuario == 2) {
                    $message = "Tu liderazgo es fundamental para garantizar que cada proyecto académico avance con éxito. Gracias a tu visión y organización, el equipo puede superar cualquier desafío. Continúa impulsando la excelencia académica con tu compromiso y dedicación. ¡Eres pieza clave en este proceso!";
                }

                // incluimos el archivo para enviar los correos

                include __DIR__ . '/../Mail/enviar-correo.php';

                $asunto = "Bienvenido a nuestra plataforma";

                if ($tipo_usuario == 4 || $tipo_usuario == 3) { // Estudiantes
                    $message = "<p>Nos complace darte la más cálida bienvenida a nuestra plataforma. Hoy inicias un nuevo capítulo en tu camino académico, donde cada paso que des te acercará más a tu meta: <b>¡tu graduación!</b> 🎓✨</p>
                    
                    <p>Este espacio ha sido diseñado para facilitar tu proceso de aprendizaje y gestión académica. Aquí podrás acceder a herramientas clave para organizar tus proyectos, recibir asesorías y mantenerte al día con cada avance en tu proceso de grado.</p>
                
                    <p>Queremos que recuerdes algo importante: <b>cada esfuerzo que hagas, cada reto que enfrentes y cada logro que alcances te llevará un paso más cerca de cumplir tu sueño profesional.</b> 💡🔥</p>
                
                    <p>¡Estás a un paso más de alcanzar tus metas académicas! Sigue adelante, el éxito está más cerca de lo que piensas. Con esfuerzo y dedicación, cada día estás más cerca de completar tu carrera. ¡Sigue así, tienes todo nuestro apoyo!</p>";
                } else if ($tipo_usuario == 5) { // Asesores
                    $message = "<p>¡Bienvenido a nuestra plataforma! Tu rol como Director es clave para el éxito de los estudiantes. Aquí encontrarás herramientas para guiarlos en su proceso de grado, brindándoles el apoyo y la orientación que necesitan para alcanzar sus metas. 📚✨</p>
                
                    <p>Gracias a tu dedicación, cada estudiante tiene una mejor oportunidad de crecer y avanzar en su formación académica. Tu conocimiento y experiencia marcan la diferencia en cada paso del proceso.</p>
                
                    <p><b>Tu compromiso y guía son fundamentales</b> para que los estudiantes superen sus desafíos y se conviertan en profesionales exitosos. ¡Sigue inspirando con tu labor, el impacto de tu trabajo es invaluable! 👏🔥</p>";
                } else if ($tipo_usuario == 2) { // Coordinadores
                    $message = "<p>¡Bienvenido a nuestra plataforma! Como coordinador, tu liderazgo y visión son esenciales para garantizar que cada proyecto académico avance con éxito. 📌✨</p>
                
                    <p>Desde este espacio, podrás supervisar, organizar y dar seguimiento a los proyectos de grado, asegurando que cada estudiante reciba el apoyo y las herramientas necesarias para su desarrollo profesional.</p>
                
                    <p>Tu compromiso impulsa la excelencia académica y permite que cada idea se convierta en un logro. <b>Gracias a tu trabajo, los estudiantes tienen la oportunidad de alcanzar sus metas y construir un futuro brillante.</b> 🚀🔥</p>";
                }


                $cuerpo_html = '
                
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Bienvenido a nuestra plataforma</title>'
                    . STYLESCORREO . '
                </head>
                <body>
                    <div class="email-container">
                        <div class="email-header">
                            <img src="' . SERVERURL . 'Views/assets/images/' . $logo . '" alt="Logo Universidad">
                            <h2>¡Notificación de registro en la plataforma!</h2>
                        </div>
                        <div class="email-body">
                            <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                            ' . $message . '
                            <h3>🔑 Tus credenciales de acceso</h3>
                            <div class="credentials">
                                <ul>
                                    <li><b>Usuario:</b> ' . $numero_documento . '</li>
                                    <li><b>Contraseña:</b> ' . $password_usuario . '</li>
                                </ul>
                            </div>

                            <p class="highlight"><b>Importante:</b> Asegúrate de cambiar tu contraseña tras el primer inicio de sesión para proteger tu cuenta.</p>

                            <p>Ya puedes acceder a la plataforma y comenzar a explorar todas sus funcionalidades. Haz clic en el siguiente botón para iniciar sesión:</p>

                            <a href="' . SERVERURL . 'login" class="login-button">Iniciar sesión</a>

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

                $cuerpo_texto = "Hola $nombre_usuario" . "$apellido_usuario, bienvenido a nuestra plataforma.";

                $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);

                if ($enviado) {
                    $alerta = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Registro exitoso",
                        "Texto" => "El registro del usuario $nombre_usuario " . " $apellido_usuario se ha completado exitosamente. Se le ha enviado un correo electrónico de verificación con sus credenciales de acceso.",
                        "Tipo" => "success"
                    ];
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error",
                        "Texto" => "El usuario se ha registrado correctamente, pero no se pudo enviar el correo.",
                        "Tipo" => "warning"
                    ];
                }

                echo json_encode($alerta);
                exit();
            } else {
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
        /** hasta aqui termina */
    }

    public function agregar_usuario_archivo_controlador()
    {


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

        ob_start(); // Iniciar buffer de salida

        $usuariosJSON  = $_POST['jsonData'];
        $usuarios = json_decode($usuariosJSON, true); // Convertir a array

        ob_end_clean(); // Limpiar cualquier salida inesperada


        if (!is_array($usuarios) || empty($usuarios)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error en los datos",
                "Texto" => "No se recibieron datos válidos para procesar.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $usuarios_procesados = [];

        foreach ($usuarios as $usuario) {
            $usuarios_procesados[] = json_encode($usuario);
        }
        
        $estado = 1;
        $imagen_usuario = "AvatarNone.png"; // Imagen por defecto
        $fechaHora = date("Y-m-d H:i:s");

        
        // incluimos el archivo para enviar los correos
        include __DIR__ . '/../Mail/enviar-correo.php';
    
        // Procesar cada usuario decodificando su JSON
        $usuarios_extraidos = [];
        $usuarios_registrados = [];
        foreach ($usuarios_procesados as $usuario_json) {
            $usuario_array = json_decode($usuario_json, true); // Decodificar JSON a array
            if ($usuario_array) {

                
                if (
                    empty($usuario_array['numero_documento']) || empty($usuario_array['nombre']) || empty($usuario_array['tipo_usuario']) || empty($usuario_array['apellidos']) || empty($usuario_array['contrasena']) || empty($usuario_array['correo'])
                ) {
                    $usuarios_registrados[] = [
                        "mensaje" => "Por favor verifica que todos los campos del usuario: '{$usuario_array['nombre']}' '{$usuario_array['apellidos']}' esten llenos",
                        "icono" => "warning" // Puedes usar también "error" o cualquier clave que represente un ícono
                    ];
                    continue; 

                }

                // Validar el formato del correo electrónico
                if (!filter_var($usuario_array['correo'], FILTER_VALIDATE_EMAIL)) {
                   
                    $usuarios_registrados[] = [
                        "mensaje" => "El formato del correo electrónico no es válido del usuario: '{$usuario_array['nombre']}' '{$usuario_array['apellidos']}' ",
                        "icono" => "warning" // Puedes usar también "error" o cualquier clave que represente un ícono
                    ];
                    continue;
                }
            
                // Verificar si el número de documento ya está registrado
                $check_documento = MainModel::ejecutar_consultas_simples("SELECT numero_documento FROM usuarios WHERE numero_documento = '{$usuario_array['numero_documento']}'");

                // Verificar si el correo ya está registrado
                $check_correo = MainModel::ejecutar_consultas_simples("SELECT correo_usuario FROM usuarios WHERE correo_usuario = '{$usuario_array['correo']}'");

                if (!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).{8,}$/', $usuario_array['contrasena'])) {
                   
                    $usuarios_registrados[] = [
                        "mensaje" => "La contraseña del numero de documento: '{$usuario_array['numero_documento']}' no cumple con el parametro solicitado para las contraseñas (La contraseña debe tener al menos 8 caracteres, incluir una letra mayúscula, un número y un símbolo especial.",
                        "icono" => "error" // Puedes usar también "error" o cualquier clave que represente un ícono
                    ];
                    continue; 
                }

                if ($check_documento->rowCount() > 0) {
                
                    $usuarios_registrados[] = [
                        "mensaje" => "El numero de documento: '{$usuario_array['numero_documento']}' ya está registrado registrado en el sistema y no pueden haber dos personas con el mismo numero de documento.",
                        "icono" => "warning" // Puedes usar también "error" o cualquier clave que represente un ícono
                    ];
                    continue; 
                }

                if ($check_correo->rowCount() > 0) {
                    
                    $usuarios_registrados[] = [
                        "mensaje" => "El correo electronico: '{$usuario_array['correo']}' ya está registrado en el sistema, y no puede haber 2 correos iguales",
                        "icono" => "warning" // Puedes usar también "error" o cualquier clave que represente un ícono
                    ];
                    continue; 
                }
                
                $tipo_usuario = $usuario_array['tipo_usuario'];

                $nombre_usuario = $usuario_array['nombre'];

                $apellido_usuario = $usuario_array['apellidos'];

                $numero_documento = $usuario_array['numero_documento'];

                $password_usuario = $usuario_array['contrasena'];

                $correo_usuario = $usuario_array['correo'];

                $registrar_usuarios_nuevo = MainModel::ejecutar_consultas_simples("INSERT INTO usuarios (numero_documento, nombre_usuario, apellidos_usuario, correo_usuario, telefono_usuario, id_rol, contrasena_usuario, estado, imagen_usuario, created_at, estado_conexion) 
                VALUES ('{$usuario_array['numero_documento']}', 
                '{$usuario_array['nombre']}', 
                '{$usuario_array['apellidos']}', 
                '{$usuario_array['correo']}', 
                '{$usuario_array['telefono']}',
                '{$usuario_array['tipo_usuario']}',
                '" . MainModel::encryption($usuario_array['contrasena']) . "',  
                '{$estado}', 
                '{$imagen_usuario}', 
                '{$fechaHora}', 
                 0)
            ");

            if ($registrar_usuarios_nuevo->rowCount() > 0) {

                $asunto = "Bienvenido a nuestra plataforma";

                if ($tipo_usuario == 4 || $tipo_usuario == 3) { // Estudiantes
                    $message = "<p>Nos complace darte la más cálida bienvenida a nuestra plataforma. Hoy inicias un nuevo capítulo en tu camino académico, donde cada paso que des te acercará más a tu meta: <b>¡tu graduación!</b> 🎓✨</p>
                    
                    <p>Este espacio ha sido diseñado para facilitar tu proceso de aprendizaje y gestión académica. Aquí podrás acceder a herramientas clave para organizar tus proyectos, recibir asesorías y mantenerte al día con cada avance en tu proceso de grado.</p>
                
                    <p>Queremos que recuerdes algo importante: <b>cada esfuerzo que hagas, cada reto que enfrentes y cada logro que alcances te llevará un paso más cerca de cumplir tu sueño profesional.</b> 💡🔥</p>
                
                    <p>¡Estás a un paso más de alcanzar tus metas académicas! Sigue adelante, el éxito está más cerca de lo que piensas. Con esfuerzo y dedicación, cada día estás más cerca de completar tu carrera. ¡Sigue así, tienes todo nuestro apoyo!</p>";
                } else if ($tipo_usuario == 5 || $tipo_usuario == 6) { // Asesores
                    $message = "<p>¡Bienvenido a nuestra plataforma! Tu rol como director es clave para el éxito de los estudiantes. Aquí encontrarás herramientas para guiarlos en su proceso de grado, brindándoles el apoyo y la orientación que necesitan para alcanzar sus metas. 📚✨</p>
                
                    <p>Gracias a tu dedicación, cada estudiante tiene una mejor oportunidad de crecer y avanzar en su formación académica. Tu conocimiento y experiencia marcan la diferencia en cada paso del proceso.</p>
                
                    <p><b>Tu compromiso y guía son fundamentales</b> para que los estudiantes superen sus desafíos y se conviertan en profesionales exitosos. ¡Sigue inspirando con tu labor, el impacto de tu trabajo es invaluable! 👏🔥</p>";
                } else if ($tipo_usuario == 2) { // Coordinadores
                    $message = "<p>¡Bienvenido a nuestra plataforma! Como coordinador, tu liderazgo y visión son esenciales para garantizar que cada proyecto académico avance con éxito. 📌✨</p>
                
                    <p>Desde este espacio, podrás supervisar, organizar y dar seguimiento a los proyectos de grado, asegurando que cada estudiante reciba el apoyo y las herramientas necesarias para su desarrollo profesional.</p>
                
                    <p>Tu compromiso impulsa la excelencia académica y permite que cada idea se convierta en un logro. <b>Gracias a tu trabajo, los estudiantes tienen la oportunidad de alcanzar sus metas y construir un futuro brillante.</b> 🚀🔥</p>";
                }

                $cuerpo_html = '
                
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Bienvenido a nuestra plataforma</title>'
                    . STYLESCORREO . '
                </head>
                <body>
                    <div class="email-container">
                        <div class="email-header">
                            <img src="' . SERVERURL . 'Views/assets/images/' . $logo . '" alt="Logo Universidad">
                            <h2>¡Notificación de registro en la plataforma!</h2>
                        </div>
                        <div class="email-body">
                            <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                            ' . $message . '
                            <h3>🔑 Tus credenciales de acceso</h3>
                            <div class="credentials">
                                <ul>
                                    <li><b>Usuario:</b> ' . $numero_documento . '</li>
                                    <li><b>Contraseña:</b> '.$usuario_array['contrasena'].'</li>
                                </ul>
                            </div>

                            <p class="highlight"><b>Importante:</b> Asegúrate de cambiar tu contraseña tras el primer inicio de sesión para proteger tu cuenta.</p>

                            <p>Ya puedes acceder a la plataforma y comenzar a explorar todas sus funcionalidades. Haz clic en el siguiente botón para iniciar sesión:</p>

                            <a href="' . SERVERURL . 'login" class="login-button">Iniciar sesión</a>

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

                $cuerpo_texto = "Hola $nombre_usuario" . "$apellido_usuario, bienvenido a nuestra plataforma.";

                $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);

                if ($enviado) {
                
                    $usuarios_registrados[] = [
                        "mensaje" => "El registro del usuario '{$usuario_array['nombre']}' '{$usuario_array['apellidos']}' se ha completado exitosamente. Se le ha enviado un correo electrónico de verificación con sus credenciales de acceso.",
                        "icono" => "success" // Puedes usar también "error" o cualquier clave que represente un ícono
                    ];

                }else{
                  
                    $usuarios_registrados[] = [
                        "mensaje" => "El  usuario '{$usuario_array['nombre']}' '{$usuario_array['apellidos']}' se ha registrado correctamente, pero no se pudo enviar el correo.",
                        "icono" => "success" // Puedes usar también "error" o cualquier clave que represente un ícono
                    ];
                }

                
            
            } else {
                $usuarios_registrados[] = [
                    "mensaje" => "Error al registrar el usuario '{$usuario_array['nombre']}' '{$usuario_array['apellidos']}' ",
                    "icono" => "success" // Puedes usar también "error" o cualquier clave que represente un ícono
                ];
            }

            }
        }
        
      
        if (!empty($usuarios_registrados)) {
            echo json_encode([
                "Alerta" => "errores",
                "Titulo" => "Usuario registrado correctamente",
                "Errores" => $usuarios_registrados, // Enviamos los errores como array
                "Tipo" => "success"
            ]);
            exit();
        }
        
    }

    /****************Controlador para asignar los usuarios a las facultades  ***********************/

    public function agregar_usuarios_facultades_controlador()
    {

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



        $numero_documento = MainModel::limpiar_cadenas($_POST['documento_usuario_regASG']);
        $tipo_faculta = MainModel::limpiar_cadenas($_POST['tipo_faculta_reg']);
        $tipo_programa = MainModel::limpiar_cadenas($_POST['tipo_programa_reg']);

        if (empty($numero_documento) || empty($tipo_faculta) || empty($tipo_programa)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_numero_documento_usuario = MainModel::ejecutar_consultas_simples("SELECT * FROM usuarios 
        WHERE numero_documento = '$numero_documento'");

        if ($check_numero_documento_usuario->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento ingresado no existe",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $usuario_info = $check_numero_documento_usuario->fetch(PDO::FETCH_ASSOC);

        $id_rol = $usuario_info['id_rol'];

        $nombre_usuario = $usuario_info['nombre_usuario'];

        $apellido_usuario = $usuario_info['apellidos_usuario'];

        $correo_usuario = $usuario_info['correo_usuario'];

        $tipo_usuario =  $id_rol;


        $tipo_faculta = (int) MainModel::decryption($tipo_faculta);
        $check_tipo_faculta_usuario = MainModel::ejecutar_consultas_simples("SELECT * FROM facultades 
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

        $facultad_correo = $check_tipo_faculta_usuario->fetch(PDO::FETCH_ASSOC);

        $nombre_facultad_correo = $facultad_correo['nombre_facultad'];



        $tipo_programa = (int) MainModel::decryption($tipo_programa);
        $check_tipo_programa_usuario = MainModel::ejecutar_consultas_simples("SELECT * FROM programas_academicos 
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

        $programa_correo = $check_tipo_programa_usuario->fetch(PDO::FETCH_ASSOC);

        $nombre_programa_correo = $programa_correo['nombre_programa'];


        if ($tipo_usuario == 4 || $tipo_usuario == 3) {
            $message = "¡Estás a un paso más de alcanzar tus metas académicas! Sigue adelante, el éxito está más cerca de lo que piensas. Con esfuerzo y dedicación, cada día estás más cerca de completar tu carrera. ¡Sigue así, tienes todo nuestro apoyo!";
        } else if ($tipo_usuario == 5) {
            $message = "Tu guía y compromiso son clave para que los estudiantes alcancen sus objetivos académicos. Gracias a tu apoyo, estás marcando una diferencia significativa en su formación. Sigue inspirando con tu experiencia y dedicación. ¡El impacto de tu labor es invaluable!";
        } else if ($tipo_usuario == 2) {
            $message = "Tu liderazgo es fundamental para garantizar que cada proyecto académico avance con éxito. Gracias a tu visión y organización, el equipo puede superar cualquier desafío. Continúa impulsando la excelencia académica con tu compromiso y dedicación. ¡Eres pieza clave en este proceso!";
        }


        if ($id_rol == 1) {
            /*************** administrador *****************/

            $check_facultad_programa = MainModel::ejecutar_consultas_simples(
                "SELECT numero_documento, id_facultad, id_programa 
                FROM Asignar_usuario_facultades 
                WHERE numero_documento = '$numero_documento' 
                AND id_facultad = '$tipo_faculta' 
                AND id_programa = '$tipo_programa'"
            );

            if ($check_facultad_programa->rowCount() > 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Registro duplicado",
                    "Texto" => "El profesor " . $nombre_usuario . ' ' . $apellido_usuario . " ya tiene registrada la misma facultad y el mismo programa.",
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

            $datos_usuario_faculta = [
                "numero_documento" => $numero_documento,
                "tipo_faculta" => $tipo_faculta,
                "tipo_programa" => $tipo_programa
            ];
            $agregar_usuario_faculta = UsuarioModelo::Agregar_usuarios_facultades_modelo($datos_usuario_faculta);

            if ($agregar_usuario_faculta->rowCount() >= 1) {

                $alerta = [
                    "Alerta" => "Recargar",
                    "Titulo" => "Registro exitoso",
                    "Texto" => "Se te asignó correctamente a a facultad, para que trabajes con el rol de coordinador",
                    "Tipo" => "success"
                ];
            } else {

                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "No se pudo agregar la asociación del administrador " . $nombre_usuario . ' ' . $apellido_usuario . " con la facultad.",
                    "Tipo" => "error"
                ];
            }
            echo json_encode($alerta);
            exit();
        } else if ($id_rol == 3 || $id_rol == 4) {
            /*************** estudiantes *****************/

            // Consulta para verificar si el número de documento ya está registrado en la tabla Asignar_usuario_facultades
            $check_numero_documento = MainModel::ejecutar_consultas_simples(
                "SELECT numero_documento, id_facultad, id_programa 
                FROM Asignar_usuario_facultades 
                WHERE numero_documento = '$numero_documento'"
            );

            // Validar si el número de documento ya está registrado
            if ($check_numero_documento->rowCount() >= 1) {
                // Generar alerta en formato JSON
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El estudiante " . $nombre_usuario . ' ' . $apellido_usuario . " ya está tiene asignada una facultad y un programa.",
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

            $datos_usuario_faculta = [
                "numero_documento" => $numero_documento,
                "tipo_faculta" => $tipo_faculta,
                "tipo_programa" => $tipo_programa
            ];
            $agregar_usuario_faculta = UsuarioModelo::Agregar_usuarios_facultades_modelo($datos_usuario_faculta);

            if ($agregar_usuario_faculta->rowCount() >= 1) {


                include __DIR__ . '/../Mail/enviar-correo.php';

                $asunto = "Notificación de Asignación de Facultad y Programa";

                $cuerpo_html = '
    
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">'
                    . STYLESCORREO . '
            </head>
            <body>
                <div class="email-container">
                    <div class="email-header">
                        <img src="' . SERVERURL . 'Views/assets/images/' . $logo . '" alt="Logo Universidad">
                        <h2>Notificación de Asignación de Facultad y Programa</h2>
                    </div>
                    <div class="email-body">
                        <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                        ' . $message . '
                        <h3>Facultad y Programas asignados</h3>
                        <div class="credentials">
                            <ul>
                                <li><b>Facultad:</b> ' . $nombre_facultad_correo . '</li>
                                <li><b>Programa:</b> ' . $nombre_programa_correo . '</li>
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


                $cuerpo_texto = "Hola $nombre_usuario $apellido_usuario, nos complace informarte que se te ha asignado la facultad $nombre_facultad_correo y el programa $nombre_programa_correo. ¡Estás a un paso más de alcanzar tus metas académicas! Sigue adelante, el éxito está más cerca de lo que piensas.";


                $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);

                if ($enviado) {
                    $alerta = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Registro exitoso",
                        "Texto" => "La asociación del  estudiante " . $nombre_usuario . ' ' . $apellido_usuario . " con la facultad se ha completado exitosamente.",
                        "Tipo" => "success"
                    ];
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado",
                        "Texto" => "No se pudo enviar el correo electrónico de notificación.",
                        "Tipo" => "error"
                    ];
                }
                echo json_encode($alerta);
                exit();
            } else {

                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "No se pudo agregar la asociación del estudiante " . $nombre_usuario . ' ' . $apellido_usuario . " con la facultad.",
                    "Tipo" => "error"
                ];
            }
            echo json_encode($alerta);
            exit();
        } else if ($id_rol == 5) {
            /*************** asesor *****************/


            $check_facultad_programa = MainModel::ejecutar_consultas_simples(
                "SELECT numero_documento, id_facultad, id_programa 
                FROM Asignar_usuario_facultades 
                WHERE numero_documento = '$numero_documento' 
                AND id_facultad = '$tipo_faculta' 
                AND id_programa = '$tipo_programa'"
            );

            if ($check_facultad_programa->rowCount() > 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Registro duplicado",
                    "Texto" => "El profesor " . $nombre_usuario . ' ' . $apellido_usuario . " ya tiene registrada la misma facultad y el mismo programa.",
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

            $datos_usuario_faculta = [
                "numero_documento" => $numero_documento,
                "tipo_faculta" => $tipo_faculta,
                "tipo_programa" => $tipo_programa
            ];
            $agregar_usuario_faculta = UsuarioModelo::Agregar_usuarios_facultades_modelo($datos_usuario_faculta);
            if ($agregar_usuario_faculta->rowCount() >= 1) {

                include __DIR__ . '/../Mail/enviar-correo.php';

                $asunto = "Notificación de Asignación de Facultad y Programa";

                $cuerpo_html = '
    
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">'
                    . STYLESCORREO . '
            </head>
            <body>
                <div class="email-container">
                    <div class="email-header">
                        <img src="' . SERVERURL . 'Views/assets/images/' . $logo . '" alt="Logo Universidad">
                        <h2>Notificación de Asignación de Facultad y Programa</h2>
                    </div>
                    <div class="email-body">
                        <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                        ' . $message . '
                        <h3>Facultad y Programas asignados</h3>
                        <div class="credentials">
                            <ul>
                                <li><b>Facultad:</b> ' . $nombre_facultad_correo . '</li>
                                <li><b>Programa:</b> ' . $nombre_programa_correo . '</li>
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


                $cuerpo_texto = "Hola $nombre_usuario $apellido_usuario, nos complace informarte que se te ha asignado la facultad $nombre_facultad_correo y el programa $nombre_programa_correo. ¡Estás a un paso más de alcanzar tus metas académicas! Sigue adelante, el éxito está más cerca de lo que piensas.";


                $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);

                if ($enviado) {
                    $alerta = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Registro exitoso",
                        "Texto" => "La asociación del  profesor " . $nombre_usuario . ' ' . $apellido_usuario . " con la facultad se ha completado exitosamente.",
                        "Tipo" => "success"
                    ];
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado",
                        "Texto" => "No se pudo enviar el correo electrónico de notificación.",
                        "Tipo" => "error"
                    ];
                }
                echo json_encode($alerta);
                exit();
            } else {

                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "No se pudo agregar la asociación del profesor " . $nombre_usuario . ' ' . $apellido_usuario . " con la facultad.",
                    "Tipo" => "error"
                ];
            }
            echo json_encode($alerta);
            exit();
        } else {
            /** coorinador  */


            $check_facultad_programa = MainModel::ejecutar_consultas_simples(
                "SELECT numero_documento, id_facultad, id_programa 
                FROM Asignar_usuario_facultades 
                WHERE numero_documento = '$numero_documento' 
                AND id_facultad = '$tipo_faculta' 
                AND id_programa = '$tipo_programa'"
            );

            if ($check_facultad_programa->rowCount() > 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Registro duplicado",
                    "Texto" => "El profesor " . $nombre_usuario . ' ' . $apellido_usuario . " ya tiene registrada la misma facultad y el mismo programa.",
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



            $datos_usuario_faculta = [
                "numero_documento" => $numero_documento,
                "tipo_faculta" => $tipo_faculta,
                "tipo_programa" => $tipo_programa
            ];
            $agregar_usuario_faculta = UsuarioModelo::Agregar_usuarios_facultades_modelo($datos_usuario_faculta);
            if ($agregar_usuario_faculta->rowCount() >= 1) {
                // Enviar correo al usuarios

                include __DIR__ . '/../Mail/enviar-correo.php';

                $asunto = "Notificación de Asignación de Facultad y Programa";

                $cuerpo_html = '
    
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">'
                    . STYLESCORREO . '
            </head>
            <body>
                <div class="email-container">
                    <div class="email-header">
                        <img src="' . SERVERURL . 'Views/assets/images/' . $logo . '" alt="Logo Universidad">
                        <h2>Notificación de Asignación de Facultad y Programa</h2>
                    </div>
                    <div class="email-body">
                        <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                        ' . $message . '
                        <h3>Facultad y Programas asignados</h3>
                        <div class="credentials">
                            <ul>
                                <li><b>Facultad:</b> ' . $nombre_facultad_correo . '</li>
                                <li><b>Programa:</b> ' . $nombre_programa_correo . '</li>
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


                $cuerpo_texto = "Hola $nombre_usuario $apellido_usuario, nos complace informarte que se te ha asignado la facultad $nombre_facultad_correo y el programa $nombre_programa_correo. ¡Estás a un paso más de alcanzar tus metas académicas! Sigue adelante, el éxito está más cerca de lo que piensas.";


                $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);

                if ($enviado) {
                    $alerta = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Registro exitoso",
                        "Texto" => "La asociación del  coordinador " . $nombre_usuario . ' ' . $apellido_usuario . " con la facultad se ha completado exitosamente.",
                        "Tipo" => "success"
                    ];
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado",
                        "Texto" => "No se pudo enviar el correo electrónico de notificación.",
                        "Tipo" => "error"
                    ];
                }
                echo json_encode($alerta);
                exit();
            } else {

                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "No se pudo agregar la asociación del coordinador " . $nombre_usuario . ' ' . $apellido_usuario . " con la facultad.",
                    "Tipo" => "error"
                ];
            }
            echo json_encode($alerta);
            exit();
        }
    }

    public function agregar_usuario_facultad_archivo_controlador()
    {


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

        ob_start(); // Iniciar buffer de salida

        $usuariosJSON  = $_POST['DatosArchivosFacultad'];
        $usuarios = json_decode($usuariosJSON, true); // Convertir a array

        ob_end_clean(); // Limpiar cualquier salida inesperada


        if (!is_array($usuarios) || empty($usuarios)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error en los datos",
                "Texto" => "No se recibieron datos válidos para procesar.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $usuarios_procesados = [];

        foreach ($usuarios as $usuario) {
            $usuarios_procesados[] = json_encode($usuario);
        }


         
        // incluimos el archivo para enviar los correos
        include __DIR__ . '/../Mail/enviar-correo.php';

         // Procesar cada usuario decodificando su JSON
         $usuarios_extraidos = [];
         $usuarios_registrados = [];
         foreach ($usuarios_procesados as $usuario_json) {
             $usuario_array = json_decode($usuario_json, true); // Decodificar JSON a array
             if ($usuario_array) {

                if(empty($usuario_array['select1Value']) || empty($usuario_array['select2Value'])){
                    $usuarios_registrados[] = [
                        "mensaje" => "Pr favor selecciona la facultad o el programa del usuario '{$usuario_array['nombre']}' '{$usuario_array['apellidos']}' no pueden estar vacios",
                        "icono" => "warning" // Puedes usar también "error" o cualquier clave que represente un ícono
                    ];
                     continue; 
                }
             
                 // Verificar si el número de documento ya está registrado
                 $check_documento = MainModel::ejecutar_consultas_simples("SELECT * FROM usuarios 
                 WHERE numero_documento  = '{$usuario_array['numero_documento']}'");
 
                 // Verificar si el correo ya está registrado
                 $check_tipo_faculta_usuario = MainModel::ejecutar_consultas_simples("SELECT * FROM facultades 
                 WHERE id_facultad = '{$usuario_array['select1Value']}'");

                 $check_tipo_programa = MainModel::ejecutar_consultas_simples("SELECT * FROM programas_academicos 
                 WHERE id_programa = '{$usuario_array['select2Value']}'");

                $check_tipo_programa_usuario = MainModel::ejecutar_consultas_simples(
                    "SELECT id_programa 
                    FROM programas_academicos 
                    WHERE id_programa = '{$usuario_array['select2Value']}' 
                    AND id_facultad = '{$usuario_array['select1Value']}'"
                );
 
              

                 /******************************************************************************************** */
 
                 if ($check_documento->rowCount() <=0) {
                 
                     $usuarios_registrados[] = [
                        "mensaje" => "El numero de documento: '{$usuario_array['numero_documento']}' que intentas ingresar no exite en el sistema",
                        "icono" => "warning" // Puedes usar también "error" o cualquier clave que represente un ícono
                    ];
                     continue; 
                 }
 
                 if ($check_tipo_faculta_usuario->rowCount() <=0) {
                   
                     $usuarios_registrados[] = [
                        "mensaje" => "El código de facultad que intentas ingresar para el usuario: '{$usuario_array['numero_documento']}'no se existe en el sistema.",
                        "icono" => "warning" // Puedes usar también "error" o cualquier clave que represente un ícono
                    ];
                     continue; 
                 }

                 if ($check_tipo_programa->rowCount() <=0) {
                    $usuarios_registrados[] = [
                        "mensaje" => "El código de programa que intentas ingresar para el usuario: '{$usuario_array['numero_documento']}'no se existe en el sistema.",
                        "icono" => "warning" // Puedes usar también "error" o cualquier clave que represente un ícono
                    ];
                    continue; 
                }


                    $check_facultad_programa = MainModel::ejecutar_consultas_simples(
                        "SELECT numero_documento, id_facultad, id_programa 
                        FROM Asignar_usuario_facultades 
                        WHERE numero_documento = '{$usuario_array['numero_documento']}' 
                        AND id_facultad = '{$usuario_array['select1Value']}' 
                        AND id_programa = '{$usuario_array['select2Value']}'"
                    );

                    if ($check_facultad_programa->rowCount() > 0) {
                
                        $usuarios_registrados[] = [
                            "mensaje" => "El usuario: '{$usuario_array['nombre']}' '{$usuario_array['apellidos']}' ya tiene 
                        registrada la misma facultad y el mismo programa.",
                            "icono" => "error" // Puedes usar también "error" o cualquier clave que represente un ícono
                        ];

                        continue; 
                    }

                

                if ($check_tipo_programa_usuario->rowCount() <=0) {
            
                    $usuarios_registrados[] = [
                        "mensaje" => "El programa seleccionado de: '{$usuario_array['nombre']}' '{$usuario_array['nombre']}' no pertenece a la facultad correspondiente.",
                        "icono" => "warning" // Puedes usar también "error" o cualquier clave que represente un ícono
                    ];

                    continue; 
                }


                /********************************************************************* */

        
                $facultad_correo = $check_tipo_faculta_usuario->fetch(PDO::FETCH_ASSOC);
        
                $nombre_facultad_correo = $facultad_correo['nombre_facultad'];


                $programa_correo = $check_tipo_programa->fetch(PDO::FETCH_ASSOC);

                $nombre_programa_correo = $programa_correo['nombre_programa'];

                 
                 $tipo_usuario = $usuario_array['tipo_usuario'];
 
                 $nombre_usuario = $usuario_array['nombre'];
 
                 $apellido_usuario = $usuario_array['apellidos'];
 
                 $numero_documento = $usuario_array['numero_documento'];
 
                 $password_usuario = $usuario_array['contrasena'];
 
                 $correo_usuario = $usuario_array['correo'];

                 
 
                 $registrar_usuarios_nuevo = MainModel::ejecutar_consultas_simples("INSERT INTO Asignar_usuario_facultades (numero_documento, id_facultad, id_programa) 
                 VALUES ('{$usuario_array['numero_documento']}', 
                 '{$usuario_array['select1Value']}', 
                 '{$usuario_array['select2Value']}')");
 
             if ($registrar_usuarios_nuevo->rowCount() > 0) {

                
 
                $asunto = "Notificación de Asignación de Facultad y Programa";
                if ($tipo_usuario == 4 || $tipo_usuario == 3) {
                    $message = "¡Estás a un paso más de alcanzar tus metas académicas! Sigue adelante, el éxito está más cerca de lo que piensas. Con esfuerzo y dedicación, cada día estás más cerca de completar tu carrera. ¡Sigue así, tienes todo nuestro apoyo!";
                } else if ($tipo_usuario == 5 || $tipo_usuario == 6) {
                    $message = "Tu guía y compromiso son clave para que los estudiantes alcancen sus objetivos académicos. Gracias a tu apoyo, estás marcando una diferencia significativa en su formación. Sigue inspirando con tu experiencia y dedicación. ¡El impacto de tu labor es invaluable!";
                } else if ($tipo_usuario == 2) {
                    $message = "Tu liderazgo es fundamental para garantizar que cada proyecto académico avance con éxito. Gracias a tu visión y organización, el equipo puede superar cualquier desafío. Continúa impulsando la excelencia académica con tu compromiso y dedicación. ¡Eres pieza clave en este proceso!";
                }
        
 
                $cuerpo_html = '
    
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">'
                        . STYLESCORREO . '
                </head>
                <body>
                    <div class="email-container">
                        <div class="email-header">
                            <img src="' . SERVERURL . 'Views/assets/images/' . $logo . '" alt="Logo Universidad">
                            <h2>Notificación de Asignación de Facultad y Programa</h2>
                        </div>
                        <div class="email-body">
                            <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellido_usuario . ',</b></p>
                            ' . $message . '
                            <h3>Facultad y Programas asignados</h3>
                            <div class="credentials">
                                <ul>
                                    <li><b>Facultad:</b> ' . $nombre_facultad_correo . '</li>
                                    <li><b>Programa:</b> ' . $nombre_programa_correo . '</li>
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
    
    
                    $cuerpo_texto = "Hola $nombre_usuario $apellido_usuario, nos complace informarte que se te ha asignado la facultad $nombre_facultad_correo y el programa $nombre_programa_correo. ¡Estás a un paso más de alcanzar tus metas académicas! Sigue adelante, el éxito está más cerca de lo que piensas.";
    
    
                    $enviado = enviarCorreo($correo_usuario, $nombre_usuario, $apellido_usuario, $asunto, $cuerpo_html, $cuerpo_texto);
 
                 if ($enviado) {
                     $usuarios_registrados[] = [
                        "mensaje" => "La asociación del  usuario  '{$usuario_array['nombre']}' '{$usuario_array['apellidos']}'con la facultad se ha completado exitosamente.",
                        "icono" => "success" // Puedes usar también "error" o cualquier clave que represente un ícono
                    ];
 
                 }else{
                     
                     $usuarios_registrados[] = [
                        "mensaje" => "El  usuario '{$usuario_array['nombre']}' '{$usuario_array['apellidos']}' se le asigno correctamente la facultad, pero no se pudo enviar el correo.",
                        "icono" => "success" // Puedes usar también "error" o cualquier clave que represente un ícono
                    ];
                 }
 
                 
             
             } else {
                $usuarios_registrados[] = [
                    "mensaje" => "No se pudo agregar la asociación del usuario '{$usuario_array['nombre']}' '{$usuario_array['apellidos']}' con la facultad.",
                    "icono" => "success" // Puedes usar también "error" o cualquier clave que represente un ícono
                ];
                
             }
 
             }
         }
         
        
         if (!empty($usuarios_registrados)) {
             echo json_encode([
                 "Alerta" => "errores",
                 "Titulo" => "Usuario registrado correctamente",
                 "Errores" => $usuarios_registrados, // Enviamos los errores como array
                 "Tipo" => "success"
             ]);
             exit();
         }
        
    }


    /****************Controlador Para eliminar a los usuarios ***********************/

    public function eliminar_usuarios_controlador()
    {
        $numero_documento = MainModel::limpiar_cadenas($_POST['idUsuario_del']);

        if (empty($numero_documento)) {

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];

            echo json_encode($alerta);
            exit();
        }

        $numero_documento =  MainModel::decryption($numero_documento);

        
        

        $check_numero_documento_usuario = MainModel::ejecutar_consultas_simples("SELECT * FROM usuarios 
        WHERE numero_documento = '$numero_documento'");

        if ($check_numero_documento_usuario->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento ingresado no existe",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $usuario_info = $check_numero_documento_usuario->fetch(PDO::FETCH_ASSOC);

        $id_rol = $usuario_info['id_rol'];

        if ($id_rol == 1) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El administrador no puede ser eliminado del sistema",
                "Tipo" => "warning"
            ];
            echo json_encode($alerta);
            exit();
        }

        $numero_documento = (int) $numero_documento;  // Convierte a int el número de documento

        $eliminar_usuario = UsuarioModelo::Eliminar_usuarios_modelo($numero_documento);  // Pasar el valor, no un array

        if ($eliminar_usuario->rowCount() >= 1) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Usuario Eliminado",
                "Texto" => "El usuario se ha eliminado correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo eliminar el usuario",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
    }

    /****************Controlador Para eliminar Asignacion usuarios faculta  ***********************/

    public function eliminar_asignacion_usuarios_faculta_controlador()
    {
        $idFacultad_del = MainModel::limpiar_cadenas($_POST['idFacultad_del']);
        $idPrograma_del = MainModel::limpiar_cadenas($_POST['idPrograma_del']);
        $documentoFPuser_del = MainModel::limpiar_cadenas($_POST['documentoFPuser_del']);

        if (empty($idFacultad_del) || empty($idPrograma_del) || empty($documentoFPuser_del)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $documentoFPuser_del = MainModel::decryption($documentoFPuser_del);

        $check_usuario = MainModel::ejecutar_consultas_simples("SELECT numero_documento FROM Asignar_usuario_facultades WHERE numero_documento = '$documentoFPuser_del'");

        if ($check_usuario->rowCount() == 0) {  // Si no hay coincidencias
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El número de documento ingresado no existe,  o no está asignado una facultad y un programa.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $idFacultad_del = MainModel::decryption($idFacultad_del);

        $check_faculta = MainModel::ejecutar_consultas_simples("SELECT id_facultad FROM facultades WHERE id_facultad = '$idFacultad_del'");

        if ($check_faculta->rowCount() == 0) {  // Si no hay coincidencias
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El código del faculta ingresado no existe. ",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $idPrograma_del = MainModel::decryption($idPrograma_del);

        $check_programa = MainModel::ejecutar_consultas_simples("SELECT id_programa FROM programas_academicos WHERE id_programa = '$idPrograma_del'");

        if ($check_programa->rowCount() == 0) {  // Si no hay coincidencias
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El código del programa ingresado no existe. ",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Consulta para verificar que el programa y facultad estén asignados al usuario
        $check_asignacion = MainModel::ejecutar_consultas_simples("
            SELECT id_facultad, id_programa 
            FROM Asignar_usuario_facultades 
            WHERE numero_documento = '$documentoFPuser_del' 
            AND id_facultad = '$idFacultad_del' 
            AND id_programa = '$idPrograma_del'
            ");

        // Verificar si la consulta no arrojó coincidencias
        if ($check_asignacion->rowCount() == 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El programa y facultad ingresados no están asignados al usuario.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Si la consulta arrojó coincidencias, entonces eliminar la asociación
        $eliminar_asignacion = UsuarioModelo::Eliminar_asignacion_usuarios_faculta_modelo($documentoFPuser_del, $idFacultad_del, $idPrograma_del);

        if ($eliminar_asignacion->rowCount() >= 1) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Asignación Eliminada",
                "Texto" => "La asociación entre el usuario y la facultad y el programa se ha eliminado correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo eliminar la asociación entre el usuario y la facultad y el programa.",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
        exit();
    }

    /****************Controlador Para editar los usuarios ***********************/

    public function editar_usuarios_controlador()
    {
        $id_usuario = MainModel::limpiar_cadenas($_POST['id_usuario_upd']);
        $numero_documento = MainModel::limpiar_cadenas($_POST['documento_usuario_upd']);
        $nombre_usuario = MainModel::limpiar_cadenas($_POST['nombre_usuario_upd']);
        $apellido_usuario = MainModel::limpiar_cadenas($_POST['apellido_usuario_upd']);
        $correo_usuario = MainModel::limpiar_cadenas($_POST['correo_usuario_upd']);
        $telefono_usuario = MainModel::limpiar_cadenas($_POST['telefono_usuario_upd']);
        $tipo_usuario = MainModel::limpiar_cadenas($_POST['tipo_usuario_upd']);
        $estado = MainModel::limpiar_cadenas($_POST['estado_usuario_upd']);
        $password_usuario = MainModel::limpiar_cadenas($_POST['password_usuario_upd']);
        $confirm_password_usuario = MainModel::limpiar_cadenas($_POST['confirm_password_usuario_upd']);

        if (
            empty($numero_documento) || empty($nombre_usuario) || empty($apellido_usuario) ||
            empty($correo_usuario) || empty($telefono_usuario) || empty($id_usuario)
        ) {

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];

            echo json_encode($alerta);
            exit();
        }



        if (MainModel::verificar_datos("[0-9]{8,20}$", $numero_documento)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El Número de documento no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Verificar nombre de usuario (ejemplo: solo letras y espacios, longitud entre 3 y 50 caracteres)
        if (MainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,50}$", $nombre_usuario)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El Nombre de usuario no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Verificar apellidos de usuario (similar a la verificación del nombre)
        if (MainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,50}$", $apellido_usuario)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Los Apellidos de usuario no coinciden con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Verificar teléfono (ejemplo: solo números de entre 7 y 15 dígitos)
        if (MainModel::verificar_datos("[0-9]{7,15}$", $telefono_usuario)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El Teléfono de usuario no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Validar el formato del correo electrónico
        if (!filter_var($correo_usuario, FILTER_VALIDATE_EMAIL)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El formato del correo electrónico no es válido.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }



        $id_usuario = MainModel::decryption($id_usuario);

        // Verificar si el id_usuario no existe en la tabla
        $check_id_usuario = MainModel::ejecutar_consultas_simples("SELECT id FROM usuarios WHERE id = '$id_usuario'");

        if ($check_id_usuario->rowCount() == 0) {  // Si no hay coincidencias
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El ID del usuario no existe en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }




        // Verificar si el id_usuario y el numero_documento pertenecen al mismo usuario
        $check_usuario = MainModel::ejecutar_consultas_simples(
            "SELECT id FROM usuarios WHERE id = '$id_usuario' AND numero_documento = '$numero_documento'"
        );

        if ($check_usuario->rowCount() == 0) {  // Si no hay coincidencias
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El ID y el número de documento no coinciden para el mismo usuario.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // validamos que los campos de las contraseñas tengan valores definidos
        if (!empty($password_usuario) && !empty($confirm_password_usuario)) {


            // Si se ingresan nuevas contraseñas
            if ($password_usuario !== $confirm_password_usuario) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "Las contraseñas no coinciden.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            } else {
                // Encriptar la nueva contraseña si se cambian
                $contrasena_usuario = MainModel::encryption($password_usuario);
            }


            if (!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).{8,}$/', $password_usuario)) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "La contraseña debe tener al menos 8 caracteres, incluir una letra mayúscula, un número y un símbolo especial.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        } else {
            // Si no se ingresan nuevas contraseñas, recuperar la existente
            $check_contraseña = MainModel::ejecutar_consultas_simples(
                "SELECT contrasena_usuario FROM usuarios WHERE id = '$id_usuario'"
            );

            // Verificar si el usuario existe
            if ($check_contraseña->rowCount() == 0) {
                // Si no hay coincidencias
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El ID del usuario no existe en el sistema.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            } else {
                // Extraer la contraseña actual (ya encriptada)
                $usuario = $check_contraseña->fetch(PDO::FETCH_ASSOC);
                $contrasena_usuario = $usuario['contrasena_usuario'];  // Usar la contraseña actual ya encriptada
            }
        }


        // validamos que el campos estado  tengan valores definidos

        if (isset($estado) && !empty($estado)) {
            $estado = (int) MainModel::decryption($estado);

            // Verificar si el estado no es ni 1 ni 2
            if ($estado != 1 && $estado != 2) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El estado no es válido: " . $estado,
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            $estado_usuario_reg = $estado;

        } else {
            // Si no hay estado proporcionado, busca el estado del usuario en la base de datos
            $check_estado_usuario = MainModel::ejecutar_consultas_simples(
                "SELECT estado FROM usuarios WHERE id = '$id_usuario'"
            );

            // Extraer el estado del usuario
            $usuario_estado = $check_estado_usuario->fetch(PDO::FETCH_ASSOC);
            $estado_usuario_reg = $usuario_estado['estado'];
        }


        // validamos que el campos tipo usuario  tengan valores definidos

        if (isset($tipo_usuario) && !empty($tipo_usuario)) {

            $tipo_usuario = (int) MainModel::decryption($tipo_usuario);

            if (!in_array($tipo_usuario, [1, 2, 3, 4, 5, 6])) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El tipo de usuario no es válido. " . $tipo_usuario,
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }


            $tipo_usuario_reg = $tipo_usuario;
        } else {

            $check_tipo_usuario = MainModel::ejecutar_consultas_simples(
                "SELECT id_rol FROM usuarios WHERE id = '$id_usuario'"
            );

            // Extraer la contraseña
            $usuario_rol = $check_tipo_usuario->fetch(PDO::FETCH_ASSOC);

            $tipo_usuario_reg = $usuario_rol['id_rol'];
        }

        $datos_usuario_upd = [
            "numero_documento" => $numero_documento,
            "nombre_usuario" => $nombre_usuario,
            "apellido_usuario" => $apellido_usuario,
            "correo_usuario" => $correo_usuario,
            "telefono_usuario" => $telefono_usuario,
            "tipo_usuario" => (int) $tipo_usuario_reg,
            "password_usuario" => $contrasena_usuario,
            "estado_usuario" => $estado_usuario_reg,
            "id_usuario" => $id_usuario
        ];

        // Ejecutar la actualización
        $editar_usuario = UsuarioModelo::Editar_usuarios_modelo($datos_usuario_upd);

        // Verificar si la consulta se ejecutó correctamente (independientemente de si se afectaron filas o no)
        if ($editar_usuario->errorCode() == '00000') {  // '00000' indica que no hubo errores
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Usuario Actualizado",
                "Texto" => "El usuario se ha actualizado correctamente.",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo actualizar el usuario.",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
        exit();
    }

    /****************Controlador Para paginar los usuarios ***********************/

    public function paginar_usuarios_controlador($pagina, $registros, $privilegio, $id, $url, $busqueda, $numero_documento_user)
    {

        // Limpiar todas las variables que se utilizan para evitar inyecciones
        $pagina = MainModel::limpiar_cadenas($pagina);
        $registros = MainModel::limpiar_cadenas($registros);
        $privilegio = MainModel::limpiar_cadenas($privilegio);
        $numero_documento_user = MainModel::limpiar_cadenas($numero_documento_user);
        $id_programa = 0;
        $id = MainModel::limpiar_cadenas($id);
        $url = MainModel::limpiar_cadenas($url);
        $url = SERVERURL . $url . "/";
        $busqueda = MainModel::limpiar_cadenas($busqueda);

        // Configurar la página inicial y el inicio de los registros
        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        // Inicializar variable para la consulta
        $consulta = "";

        // Verificar si hay búsqueda
        if (isset($busqueda) && $busqueda != "") {
            // Consulta para buscar usuarios cuando hay búsqueda
            $consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM usuarios WHERE ((id != '$id' 
        AND id != '1') AND (numero_documento LIKE '%$busqueda%' OR nombre_usuario LIKE '%$busqueda%' 
        OR apellidos_usuario LIKE '%$busqueda%' OR correo_usuario LIKE '%$busqueda%' 
        OR telefono_usuario LIKE '%$busqueda%' )) 
        ORDER BY nombre_usuario ASC LIMIT $inicio, $registros";
        } else {
            // Consultas basadas en el privilegio del usuario
            if ($privilegio == 1) {
                $consulta = "SELECT SQL_CALC_FOUND_ROWS 
                    u.id AS ID_Usuario,
                    u.numero_documento,
                    u.nombre_usuario,
                    u.apellidos_usuario,
                    u.correo_usuario,
                    u.telefono_usuario,
                    ru.nombre_rol,
                    u.estado,
                    u.id_rol,
                    u.imagen_usuario,
                    u.created_at
                FROM usuarios u
                INNER JOIN roles_usuarios ru ON u.id_rol = ru.id_rol
                WHERE u.id != '$id' AND u.id != '1'
                ORDER BY u.nombre_usuario ASC
                LIMIT $inicio, $registros;";
            } elseif ($privilegio == 2) {
                // Consulta para obtener las facultades y programas asignados al usuario
                $sql = "SELECT  
                auf.numero_documento,
                IFNULL(f.nombre_facultad, 'Sin asignar') AS nombre_facultad, 
                IFNULL(p.nombre_programa, 'Sin asignar') AS nombre_programa, 
                f.id_facultad, 
                p.id_programa
            FROM Asignar_usuario_facultades auf
            LEFT JOIN facultades f ON auf.id_facultad = f.id_facultad
            LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
            WHERE auf.numero_documento = :numero_documento_user
            ORDER BY f.nombre_facultad, p.nombre_programa";

                // Ejecutar la consulta inicial
                $check_tipo_facultad_programa = MainModel::conectar()->prepare($sql);
                $check_tipo_facultad_programa->bindParam(':numero_documento_user', $numero_documento_user, PDO::PARAM_STR);
                $check_tipo_facultad_programa->execute();

                // Construir la consulta basada en facultades y programas asignados
                $consulta = ""; // Reiniciar consulta para combinar
                if ($check_tipo_facultad_programa->rowCount() > 0) {
                    // Crear una consulta principal para todos los resultados de facultad y programa
                    $subConsultas = [];
                    while ($row = $check_tipo_facultad_programa->fetch(PDO::FETCH_ASSOC)) {
                        $id_facultad = $row['id_facultad'];
                        $id_programa = $row['id_programa'];
                        $nombre_facultad = $row['nombre_facultad'];
                        $nombre_programa = $row['nombre_programa'];

                        $consulta = "SELECT  
                        u.numero_documento,
                        u.nombre_usuario,
                        u.apellidos_usuario,
                        u.correo_usuario,
                        u.telefono_usuario,
                        u.estado,
                        u.id_rol,
                        u.imagen_usuario,
                        ru.nombre_rol,
                        GROUP_CONCAT(DISTINCT f.nombre_facultad SEPARATOR ', ') AS nombre_facultad,
                        GROUP_CONCAT(DISTINCT p.nombre_programa SEPARATOR ', ') AS nombre_programa
                    FROM usuarios u
                    INNER JOIN Asignar_usuario_facultades auf ON u.numero_documento = auf.numero_documento
                    INNER JOIN roles_usuarios ru ON u.id_rol = ru.id_rol
                    LEFT JOIN facultades f ON auf.id_facultad = f.id_facultad
                    LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
                    WHERE u.numero_documento != '$numero_documento_user'
                    AND auf.id_facultad = '$id_facultad'
                    GROUP BY u.numero_documento
                    ORDER BY u.nombre_usuario ASC
                    LIMIT $inicio, $registros;";
                    }
                } else {
                    // Si no hay facultades y programas asignados, mostrar como "Sin asignar"
                    $consulta = "SELECT  
                        u.numero_documento,
                        u.nombre_usuario,
                        u.apellidos_usuario,
                        u.correo_usuario,
                        u.telefono_usuario,
                        u.estado,
                        u.id_rol,
                        u.imagen_usuario,
                        ru.nombre_rol,
                        'Sin asignar' AS nombre_facultad,
                        'Sin asignar' AS nombre_programa
                    FROM usuarios u
                    INNER JOIN roles_usuarios ru ON u.id_rol = ru.id_rol
                    WHERE u.numero_documento = :numero_documento_user
                    ORDER BY nombre_usuario ASC LIMIT $inicio, $registros;";
                }
            }
        }

        // Conectar y ejecutar la consulta solo si no está vacía
        if (!empty($consulta)) {
            $conexion = mainModel::conectar();
            $datos = $conexion->query($consulta);
            $datos = $datos->fetchAll();

            // Obtener el total de registros para la paginación
            $total = $conexion->query("SELECT FOUND_ROWS() as total");
            $total = (int)$total->fetchColumn();

            // Calcular el número total de páginas
            $Npaginas = ceil($total / $registros);


            // Generar la tabla con los datos obtenidos
            $tabla = '<div class="table-responsive">
            <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
            <thead>
                <tr>
                <th>ID</th>
                <th>Imagen</th>
                <th>Documento</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Correo Electrónico</th>
                <th>Teléfono</th>
                <th>Rol</th>
                <th>Proyecto asignado</th>
                <th>Director asignado</th>
                <th>Facultad</th>';
            // Agregar la columna "Programa" si el privilegio es igual a 2
            if ($privilegio == 2) {
                $tabla .= '<th>Programa</th>';
            }
            $tabla .= '<th>Estado</th>
                    <th>Editar</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>';

            // Verificar si hay registros para mostrar
            if ($total >= 1 && $pagina <= $Npaginas) {
                $contador = $inicio + 1;
                $reg_inicio = $inicio + 1;

                foreach ($datos as $row) {
                    $estado = ($row['estado'] == 1) ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>';

                    if ($privilegio == 1) {

                        $numero_documento = $row['numero_documento'];

                        $id_rol_usuarios = $row['id_rol'];

                        /****** Consulta para verificar si el usuario tiene facultades asignadas ******** */
                        $sql = "SELECT f.nombre_facultad, p.nombre_programa 
                        FROM Asignar_usuario_facultades auf
                        INNER JOIN facultades f ON auf.id_facultad = f.id_facultad
                        LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
                        WHERE auf.numero_documento = :numero_documento";

                        $data_information_user = MainModel::conectar()->prepare($sql);
                        $data_information_user->bindParam(':numero_documento', $numero_documento, PDO::PARAM_STR);
                        $data_information_user->execute();


                        include('./Views/content/mostrar-informacion-usuarios-controlador.php');

                        $tiene_facultades = $data_information_user->rowCount() > 0;
                    } else if ($privilegio == 2) {

                        $numero_documento = $row['numero_documento'];
                        $id_rol_usuarios = $row['id_rol'];

                        include('./Views/content/mostrar-informacion-usuarios-controlador.php');
                    }

                    $tabla .= '<tr>';
                    $tabla .= '<td>' . $contador++ . '</td>';
                    $tabla .= '<td> 
                    <a href="' . SERVERURL . 'Views/assets/images/avatar/' . $row['imagen_usuario'] . '" 
                       data-lightbox="gallery" 
                       data-title="' . htmlspecialchars($row['imagen_usuario'], ENT_QUOTES, 'UTF-8') . '" 
                       class="gallery-item">
                
                        <img src="' . SERVERURL . 'Views/assets/images/avatar/' . $row['imagen_usuario'] . '" 
                             alt="Usuario" 
                             width="40" 
                             height="40">
                
                      
                    </a>
                </td>';

                    $tabla .= '<td>' . $row['numero_documento'] . '</td>';
                    $tabla .= '<td>' . $row['nombre_usuario'] . '</td>';
                    $tabla .= '<td>' . $row['apellidos_usuario'] . '</td>';
                    $tabla .= '<td>' . $row['correo_usuario'] . '</td>';
                    $tabla .= '<td>' . $row['telefono_usuario'] . '</td>';
                    $tabla .= '<td>' . $row['nombre_rol'] . '</td>';
                    $tabla .= '<td>' . $tiene_proyecto . '</td>';
                    $tabla .= '<td>' . $tiene_asesor . '</td>';



                    if ($privilegio == 2) {

                        $tabla .= '<td>' . ($privilegio == 2 ? $row['nombre_facultad'] : 'N/A') . '</td>';
                    } else if ($privilegio == 1) {
                        if ($tiene_facultades) {
                            $tabla .= '<td><button type="button" class="btn btn-info " data-bs-toggle="modal" data-bs-target="#facultadesModal" onclick="mostrarFacultadesUsuario(\'' . SERVERURL . '\', \'' . $numero_documento . '\')">
                        <i class="fas fa-eye"></i></button></td>';
                        } else {
                            $tabla .= '<td><span class="badge bg-danger">Sin asignar</span></td>';
                        }
                    }

                    if ($privilegio == 2) {
                        $tabla .= '<td>' . $row['nombre_programa'] . '</td>';
                    }

                    $tabla .= '<td>' . $estado . '</td>';
                    $tabla .= '<td><a href="' . SERVERURL . 'user-update/' . MainModel::encryption($row['numero_documento']) . '/" class="btn btn-success"><i class="far fa-edit"></i></a></td>';
                    $tabla .= '<td><form class="FormulariosAjax" action="' . SERVERURL . 'Ajax/UsuarioAjax.php" method="POST" data-form="delete" autocomplete="off">
                    <input type="hidden" name="idUsuario_del" value="' . MainModel::encryption($row['numero_documento']) . '">
                    <button type="submit" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
                    </form></td>';
                    $tabla .= '</tr>';
                }
                $reg_final = $contador - 1;
            } else {
                $tabla .= '<tr class="text-center"><td colspan="13">No hay datos para mostrar</td></tr>';
            }

            // Cerrar la tabla
            $tabla .= '</tbody></table></div>';

            // Mostrar información adicional
            if ($total >= 1 && $pagina <= $Npaginas) {
                $tabla .= '<p class="text-right">Mostrando usuarios ' . $reg_inicio . ' al ' . $reg_final . ' de un total de ' . $total . '</p>';
            }


            // Generar el paginador si hay más de una página
            if ($Npaginas >= 1) {
                $tabla .= MainModel::paginador_tablas($pagina, $Npaginas, $url, 7);
            }

            if ($privilegio == 1) {

                $tabla .= '<div class="modal fade" id="facultadesModal" tabindex="-1" aria-labelledby="facultadesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="facultadesModalLabel">Facultades y Programas Asignados</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Aquí se insertarán los datos dinámicamente -->
                        <div id="facultades-content"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>';
            }
        } else {
            // Si no hay consulta válida o no hay datos que mostrar
            $tabla = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                       <div class="text-center"> <strong>¡Atención!</strong> No hay usuarios que estén asignado a las facultades del usuario logueado </div>
                    </div>';
        }


        return $tabla;
    }

    /****************Controlador Para paginar las ideas ***********************/

    public function paginar_ideas_controlador($pagina, $registros, $privilegio, $url, $numero_documento_user)
    {
        // Limpiar todas las variables que se utilizan para evitar inyecciones
        $pagina = MainModel::limpiar_cadenas($pagina);
        $registros = MainModel::limpiar_cadenas($registros);
        $privilegio = MainModel::limpiar_cadenas($privilegio);
        $url = MainModel::limpiar_cadenas($url);
        $url_modal = $url;
        $url = SERVERURL . $url . "/";

        // Configurar la página inicial y el inicio de los registros
        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        // Inicializar la consulta
        $consulta = "";

        if ($privilegio == 1) {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS 
                a.codigo_anteproyecto,
                a.titulo_anteproyecto,
                a.palabras_claves,
                a.estado,
                a.modalidad,
                p.nombre_programa,
                f.nombre_facultad
            FROM anteproyectos a
            INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
            INNER JOIN facultades f ON p.id_facultad = f.id_facultad
            LIMIT $inicio, $registros;";
        } else if ($privilegio == 2) {

            // Consulta para obtener las facultades y programas asignados al usuario
            $sql = "SELECT  SQL_CALC_FOUND_ROWS 
            auf.numero_documento,
            f.nombre_facultad, 
            p.nombre_programa, 
            f.id_facultad, 
            p.id_programa
            FROM Asignar_usuario_facultades auf
            INNER JOIN facultades f ON auf.id_facultad = f.id_facultad
            LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
            WHERE auf.numero_documento = :numero_documento_user
            ORDER BY f.nombre_facultad, p.nombre_programa";

            // Ejecutar la consulta inicial
            $check_tipo_facultad_programa = MainModel::conectar()->prepare($sql);
            $check_tipo_facultad_programa->bindParam(':numero_documento_user', $numero_documento_user, PDO::PARAM_STR);
            $check_tipo_facultad_programa->execute();

            $consulta = ""; // Reiniciar consulta para combinar
            if ($check_tipo_facultad_programa->rowCount() > 0) {
                // Crear una consulta principal para todos los resultados de facultad y programa
                $subConsultas = [];
                while ($row = $check_tipo_facultad_programa->fetch(PDO::FETCH_ASSOC)) {
                    $id_facultad = $row['id_facultad'];
                    $id_programa = $row['id_programa'];

                    // Construir la subconsulta sin `LIMIT` ni `ORDER BY`
                    $subConsultas[] = "SELECT  
                            a.codigo_anteproyecto,
                            a.titulo_anteproyecto,
                            a.palabras_claves,
                            a.estado,
                            a.modalidad,
                            p.nombre_programa,
                            f.nombre_facultad
                        FROM anteproyectos a
                        INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
                        INNER JOIN facultades f ON p.id_facultad = f.id_facultad
                        WHERE f.id_facultad = ' $id_facultad' AND p.id_programa = '$id_programa '";
                }

                // Unir todas las subconsultas para que se ejecuten como una sola
                if (!empty($subConsultas)) {
                    $consulta = implode(" UNION ALL ", $subConsultas);

                    // Añadir `ORDER BY` y `LIMIT` a la consulta global
                    $consulta .= " ORDER BY titulo_anteproyecto ASC LIMIT $inicio, $registros;";
                }
            }
        } else if ($privilegio == 3) {

            $sql = "SELECT  SQL_CALC_FOUND_ROWS 
            auf.numero_documento,
            f.nombre_facultad, 
            p.nombre_programa, 
            f.id_facultad, 
            p.id_programa
            FROM Asignar_usuario_facultades auf
            INNER JOIN facultades f ON auf.id_facultad = f.id_facultad
            LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
            WHERE auf.numero_documento = :numero_documento_user
            ORDER BY f.nombre_facultad, p.nombre_programa";

            // Ejecutar la consulta inicial
            $check_tipo_facultad_programa = MainModel::conectar()->prepare($sql);
            $check_tipo_facultad_programa->bindParam(':numero_documento_user', $numero_documento_user, PDO::PARAM_STR);
            $check_tipo_facultad_programa->execute();

            $consulta = ""; // Reiniciar consulta para combinar
            if ($check_tipo_facultad_programa->rowCount() > 0) {
                // Crear una consulta principal para todos los resultados de facultad y programa
                $subConsultas = [];
                while ($row = $check_tipo_facultad_programa->fetch(PDO::FETCH_ASSOC)) {
                    $id_facultad = $row['id_facultad'];
                    $id_programa = $row['id_programa'];

                    // Construir la subconsulta sin `LIMIT` ni `ORDER BY`
                    $subConsultas[] = "SELECT  
                            a.codigo_anteproyecto,
                            a.titulo_anteproyecto,
                            a.palabras_claves,
                            a.estado,
                            a.modalidad,
                            p.nombre_programa,
                            f.nombre_facultad
                        FROM anteproyectos a
                        INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
                        INNER JOIN facultades f ON p.id_facultad = f.id_facultad
                        WHERE f.id_facultad = ' $id_facultad' AND p.id_programa = '$id_programa '";
                }

                // Unir todas las subconsultas para que se ejecuten como una sola
                if (!empty($subConsultas)) {
                    $consulta = implode(" UNION ALL ", $subConsultas);

                    // Añadir `ORDER BY` y `LIMIT` a la consulta global
                    $consulta .= " ORDER BY titulo_anteproyecto ASC LIMIT $inicio, $registros;";
                }
            }
        } else if ($privilegio == 5 || $privilegio == 6) {
            $sql = "SELECT  
            auf.numero_documento,
            f.nombre_facultad, 
            p.nombre_programa, 
            f.id_facultad, 
            p.id_programa
            FROM Asignar_usuario_facultades auf
            INNER JOIN facultades f ON auf.id_facultad = f.id_facultad
            LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
            WHERE auf.numero_documento = :numero_documento_user
            ORDER BY f.nombre_facultad, p.nombre_programa";

            // Ejecutar la consulta inicial
            $check_tipo_facultad_programa = MainModel::conectar()->prepare($sql);
            $check_tipo_facultad_programa->bindParam(':numero_documento_user', $numero_documento_user, PDO::PARAM_STR);
            $check_tipo_facultad_programa->execute();

            $consulta = ""; // Reiniciar consulta para combinar
            if ($check_tipo_facultad_programa->rowCount() > 0) {
                // Crear una consulta principal para todos los resultados de facultad y programa
                $subConsultas = [];
                while ($row = $check_tipo_facultad_programa->fetch(PDO::FETCH_ASSOC)) {
                    $id_facultad = $row['id_facultad'];
                    $id_programa = $row['id_programa'];

                    // Construir la subconsulta sin `LIMIT` ni `ORDER BY`
                    $subConsultas[] = "SELECT  
                            a.codigo_anteproyecto,
                            a.titulo_anteproyecto,
                            a.palabras_claves,
                            a.estado,
                            a.modalidad,
                            p.nombre_programa,
                            f.nombre_facultad
                        FROM anteproyectos a
                        INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
                        INNER JOIN facultades f ON p.id_facultad = f.id_facultad
                        WHERE f.id_facultad = ' $id_facultad' AND p.id_programa = '$id_programa '";
                }

                // Unir todas las subconsultas para que se ejecuten como una sola
                if (!empty($subConsultas)) {
                    $consulta = implode(" UNION ALL ", $subConsultas);

                    // Añadir `ORDER BY` y `LIMIT` a la consulta global
                    $consulta .= " ORDER BY titulo_anteproyecto ASC LIMIT $inicio, $registros;";
                }
            }
        }



        // Conectar y ejecutar la consulta
        if (!empty($consulta)) {
            $conexion = mainModel::conectar();
            $datos = $conexion->query($consulta);
            $datos = $datos->fetchAll();

            // Obtener el total de registros para la paginación
            $total = $conexion->query("SELECT FOUND_ROWS() as total");
            $total = (int)$total->fetchColumn();

            // Calcular el número total de páginas
            $Npaginas = ceil($total / $registros);

            // Generar la tabla con los datos obtenidos
            $tabla = '<div class="table-responsive">
         <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
         <thead>
             <tr>
                 <th>ID</th>
                 <th>Código</th>
                 <th>Título</th>
                 <th>Palabras</th>
                 <th>Estudiantes</th>
                 <th>Director</th>
                 <th>Estado</th>
                 <th>Facultad</th>
                 <th>Programa</th>
                 <th>Modalidad</th>
                 <th>Usuarios</th>';

            // Condición para mostrar Editar y Eliminar si el privilegio es diferente de 3
            if ($privilegio != 3  && $privilegio != 5) {
                $tabla .= '<th>Observar</th>';
                $tabla .= '<th>Editar</th>';
                $tabla .= '<th>Eliminar</th>';
            }

            $tabla .= '</tr>
         </thead>
         <tbody>';


            // Verificar si hay registros para mostrar
            if ($total >= 1 && $pagina <= $Npaginas) {
                $contador = $inicio + 1;
                $reg_inicio = $inicio + 1;
                foreach ($datos as $row) {

                    $codigo_idea = $row['codigo_anteproyecto'];

                    $estados_anteproyectos = $row['estado'];

                    /************************extraemos la modadlidad de los anteproyectos ******************************** */

                    $modalidad_anteproyectos = $row['modalidad'];


                    $consultar_modalidad = MainModel::ejecutar_consultas_simples(
                        "SELECT nombre_modalidad FROM modalidad_grados WHERE id_modalidad = '$modalidad_anteproyectos'"
                    );

                    if ($consultar_modalidad->rowCount() > 0) {
                        $modalidad_anteproyecto_asignadas = $consultar_modalidad->fetch(PDO::FETCH_ASSOC);
                        $nombre_modalidad = $modalidad_anteproyecto_asignadas['nombre_modalidad'];

                        // Asignamos colores según la modalidad
                        $colores_modalidad = [
                            "TRABAJO DE GRADO" => "bg-primary",
                            "PASANTIAS" => "bg-success",
                            "PARTICIPACIÓN EN GRUPOS DE INVESTIGACIÓN" => "bg-warning text-dark"
                        ];

                        // Si la modalidad existe en el array, se usa su color, de lo contrario, color por defecto
                        $color_badge = $colores_modalidad[$nombre_modalidad] ?? "bg-secondary";
                    } else {
                        $nombre_modalidad = "No asignada";
                    }


                    /************************************************************************************************************* */
                    $estado_idea = MainModel::ejecutar_consultas_simples(
                        "SELECT codigo_anteproyecto FROM asignar_estudiante_anteproyecto WHERE codigo_anteproyecto = '$codigo_idea'"
                    );

                    if ($estado_idea->rowCount() == 0) {

                        $estado = '<span class="badge bg-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Proyecto sin asignar estudiantes"><i class="fa-solid fa-users-slash"></i></span>';
                    } else {
                        // Extraer el estado del usuario

                        $estado = '<span class="badge bg-success" data-bs-toggle="tooltip" data-bs-placement="top" title="proyecto con estudiantes asignados"><i class="fa-solid fa-users-line"></i></span>';
                    }


                    if ($estados_anteproyectos == "Aprobado") {

                        $estados_anteproyectos_see = '<span class="badge bg-success">Aprobado</span>';
                    } else if ($estados_anteproyectos == "Revisión") {

                        $estados_anteproyectos_see = '<span class="badge bg-info">Revisión</span>';
                    } else {
                        // Extraer el estado del usuario

                        $estados_anteproyectos_see = '<span class="badge bg-danger">Cancelado</span>';
                    }

                    /***********************************verificar si tiene asesor *******************+*/

                    $estado_asesor_idea_query = MainModel::ejecutar_consultas_simples(
                        "SELECT codigo_proyecto FROM Asignar_asesor_anteproyecto_proyecto WHERE codigo_proyecto = '$codigo_idea'"
                    );


                    if ($estado_asesor_idea_query->rowCount() > 0) {

                        $estado_asesor_idea = '<span class="badge bg-success see-span"  data-bs-toggle="modal" data-bs-target="#proyectosasignadosasesor' . $codigo_idea . '"
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Revisar"><i class="fa-regular fa-eye"></i></span> ';


                        $tabla .= '<!-- Modal -->
                    <div class="modal fade" id="proyectosasignadosasesor' . $codigo_idea . '" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Código: ' . htmlspecialchars($codigo_proyecto) . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">';
                        // Realizamos la consulta para extraer la información del proyecto
                        // Consulta para obtener la información del profesor asignado al proyecto
                        $query_profesor = "SELECT u.* 
                        FROM Asignar_asesor_anteproyecto_proyecto aa
                        INNER JOIN usuarios u ON aa.numero_documento = u.numero_documento
                        WHERE aa.codigo_proyecto = :codigo_proyecto";
                        $stmt_profesor = MainModel::conectar()->prepare($query_profesor);
                        $stmt_profesor->bindParam(":codigo_proyecto", $codigo_idea, PDO::PARAM_STR);
                        $stmt_profesor->execute();
                        $resultado_profesor = $stmt_profesor->fetch(PDO::FETCH_ASSOC);


                        $cedula_asesor_registrado = $resultado_profesor['numero_documento'];

                        $nombre_asesor_registrado = $resultado_profesor['nombre_usuario'];

                        $apellido_asesor_registrado = $resultado_profesor['apellidos_usuario'];

                        $correo_asesor_registrado = $resultado_profesor['correo_usuario'];

                        $telefono_asesor_registrado = $resultado_profesor['telefono_usuario'];



                        $tabla .= '<!-- Contenedor con la tarjeta del proyecto -->
                        <div id="project-container">
                        <div class="project-card">
                            <div class="project-header">
                            <div class="project-code">Documento: ' . htmlspecialchars($cedula_asesor_registrado) . '</div>
                            <h2 class="project-title">' . htmlspecialchars($nombre_asesor_registrado . ' ' .  $apellido_asesor_registrado) . '</h2>
                            </div>
                            <div class="project-body">
                            <p><strong>Telefono:</strong> ' . htmlspecialchars($telefono_asesor_registrado) . '</p>
                            <p><strong>Correo electronico: </strong> ' . $correo_asesor_registrado . '</p>
                            
                            ';

                        $tabla .= '    </div>
                        </div>
                        </div>';




                        $tabla .= '</div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                        </div>
                    </div>
                    </div>';
                    } else {

                        $estado_asesor_idea = '<span class="badge  bg-danger text-dark"><i class="fa-regular fa-eye"></i></span>';
                    }





                    $tabla .= '<tr>';
                    $tabla .= '<td>' . $contador++ . '</td>';
                    $tabla .= '<td>' . $row['codigo_anteproyecto'] . '</td>';
                    $tabla .= '<td>' . $row['titulo_anteproyecto'] . '</td>';
                    $tabla .= '<td>' . $row['palabras_claves'] . '</td>';
                    $tabla .= '<td>' .  $estado . '</td>';
                    $tabla .= '<td>' .  $estado_asesor_idea . '</td>';
                    $tabla .= '<td>' . $estados_anteproyectos_see . '</td>';
                    $tabla .= '<td>' . $row['nombre_facultad'] . '</td>';
                    $tabla .= '<td>' . $row['nombre_programa'] . '</td>';
                    $tabla .= "<td><span class='badge $color_badge'>$nombre_modalidad</span></td>";

                    // Supongamos que tienes la URL de cada proyecto almacenada en $row['url_anteproyecto']
                    $tabla .= '<td><button type="button" 
                onclick="mostrarUsuariosProyecto(\'' . $row['codigo_anteproyecto'] . '\', \'' . addslashes($row['titulo_anteproyecto']) . '\', \'' . SERVERURL . '\')" 
                class="btn btn-warning" 
                data-bs-toggle="modal" 
                data-bs-target="#usuariosModal">
                <i class="fa-solid fa-users"></i>
                </button></td>';

                    if ($privilegio != 3 && $privilegio != 5) {
                        $tabla .= '<td><a href="' . SERVERURL . 'entregas-anteproyectos/' . $row['codigo_anteproyecto']  . '/" class="btn btn-success"><i class="fa-solid fa-eye"></i></a></td>';
                        $tabla .= '<td><a href="' . SERVERURL . 'ideas-update/' . MainModel::encryption($row['codigo_anteproyecto']) . '/" class="btn btn-success"><i class="far fa-edit"></i></a></td>';
                        $tabla .= '<td><form class="FormulariosAjax" action="' . SERVERURL . 'Ajax/AnteproyectoAjax.php" method="POST" data-form="delete" autocomplete="off">
                            <input type="hidden" name="codigo_idea_delete" value="' . MainModel::encryption($row['codigo_anteproyecto']) . '">
                            <button type="submit" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
                            </form></td>';
                        $tabla .= '</tr>';
                    }
                }


                $reg_final = $contador - 1;
            } else {
                $tabla .= '<tr class="text-center"><td colspan="12">No hay datos para mostrar</td></tr>';
            }

            // Cerrar la tabla
            $tabla .= '</tbody></table></div>';

            // Mostrar información adicional
            if ($total >= 1 && $pagina <= $Npaginas) {
                $tabla .= '<p class="text-right">Mostrando ideas ' . $reg_inicio . ' al ' . $reg_final . ' de un total de ' . $total . '</p>';
            }

            // Generar el paginador si hay más de una página
            if ($Npaginas >= 1) {
                $tabla .= MainModel::paginador_tablas($pagina, $Npaginas, $url, 7);
            }


            $tabla .= '<div class="modal fade" id="usuariosModal" tabindex="-1" aria-labelledby="usuariosModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-xl">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title text-center" id="usuariosModalLabel">Usuarios asignados</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <h5 class="mt-2 mb-4" id="modal-titulo"></h5>
                     <!-- Contenedor para la tabla responsive -->
                     <div class="table-responsive">
                         <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="tabla_usuarios">
                             <thead>
                                 <tr>
                                     <th>Número de Documento</th>
                                     <th>Nombre</th>
                                     <th>Apellido</th>
                                     <th>Correo</th>
                                 </tr>
                             </thead>
                             <tbody id="tabla-usuarios-registrados">
                                 <!-- Aquí se mostrarán los usuarios -->
                             </tbody>
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
            // Si no hay consulta válida o no hay datos que mostrar
            $tabla = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <div class="text-center"> <strong>¡Atención!</strong> No hay ideas de anteproyectos para mostrar al usuario</div>
                    </div>';
        }



        return $tabla;
    }

    public function pagina_ideas_registradas_controlador($pagina, $registros, $url)
    {
        $pagina = MainModel::limpiar_cadenas($pagina);
        $registros = MainModel::limpiar_cadenas($registros);
        $url = MainModel::limpiar_cadenas($url);
        $url_modal = $url;
        $url = SERVERURL . $url . "/";
        // Configurar la página inicial y el inicio de los registros
        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        $consulta = "SELECT SQL_CALC_FOUND_ROWS 
            a.codigo_anteproyecto,
            a.titulo_anteproyecto,
            a.palabras_claves,
            a.estado,
            p.nombre_programa,
            f.nombre_facultad
        FROM anteproyectos a
        INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
        INNER JOIN facultades f ON p.id_facultad = f.id_facultad
        LIMIT $inicio, $registros;";


        // Conectar y ejecutar la consulta
        if (!empty($consulta)) {
            $conexion = mainModel::conectar();
            $datos = $conexion->query($consulta);
            $datos = $datos->fetchAll();

            // Obtener el total de registros para la paginación
            $total = $conexion->query("SELECT FOUND_ROWS() as total");
            $total = (int)$total->fetchColumn();

            // Calcular el número total de páginas
            $Npaginas = ceil($total / $registros);

            // Generar la tabla con los datos obtenidos
            $tabla = '<div class="table-responsive">
            <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Título</th>
                    <th>Palabras</th>
                    <th>Estudiantes</th>
                    <th>Director</th>
                    <th>Estado</th>
                    <th>Facultad</th>
                    <th>Programa</th>
                    <th>Usuarios</th>
                    <th>Documentos</th>
                    ';
                    

            $tabla .= '</tr>
            </thead>
            <tbody>';

            // Verificar si hay registros para mostrar
            if ($total >= 1 && $pagina <= $Npaginas) {

                $contador = $inicio + 1;
                $reg_inicio = $inicio + 1;
                foreach ($datos as $row) {

                    $codigo_idea = $row['codigo_anteproyecto'];

                    $estados_anteproyectos = $row['estado'];

                    $estado_idea = MainModel::ejecutar_consultas_simples(
                        "SELECT codigo_anteproyecto FROM asignar_estudiante_anteproyecto WHERE codigo_anteproyecto = '$codigo_idea'"
                    );

                    if ($estado_idea->rowCount() == 0) {

                        $estado = '<span class="badge bg-danger">Sin asignar estudiantes</span>';
                    } else {
                        // Extraer el estado del usuario

                        $estado = '<span class="badge bg-success">Estudiantes Asignados</span>';
                    }


                    if ($estados_anteproyectos == "Aprobado") {

                        $estados_anteproyectos_see = '<span class="badge bg-success">Aprobado</span>';
                    } else if ($estados_anteproyectos == "Revisión") {

                        $estados_anteproyectos_see = '<span class="badge bg-info">Revisión</span>';
                    } else {
                        // Extraer el estado del usuario

                        $estados_anteproyectos_see = '<span class="badge bg-danger">Cancelado</span>';
                    }

                    /***********************************verificar si tiene asesor *******************+*/

                    $estado_asesor_idea_query = MainModel::ejecutar_consultas_simples(
                        "SELECT codigo_proyecto FROM Asignar_asesor_anteproyecto_proyecto WHERE codigo_proyecto = '$codigo_idea'"
                    );


                    if ($estado_asesor_idea_query->rowCount() > 0) {

                        $estado_asesor_idea = '<span class="badge bg-success see-span"  data-bs-toggle="modal" data-bs-target="#proyectosasignadosasesor' . $codigo_idea . '"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="Revisar"><i class="fa-regular fa-eye"></i></span> ';


                        $tabla .= '<!-- Modal -->
                        <div class="modal fade" id="proyectosasignadosasesor' . $codigo_idea . '" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">Código: ' . htmlspecialchars($codigo_idea) . '</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">';
                        // Realizamos la consulta para extraer la información del proyecto
                        // Consulta para obtener la información del profesor asignado al proyecto
                        $query_profesor = "SELECT u.* 
                            FROM Asignar_asesor_anteproyecto_proyecto aa
                            INNER JOIN usuarios u ON aa.numero_documento = u.numero_documento
                            WHERE aa.codigo_proyecto = :codigo_proyecto";
                        $stmt_profesor = MainModel::conectar()->prepare($query_profesor);
                        $stmt_profesor->bindParam(":codigo_proyecto", $codigo_idea, PDO::PARAM_STR);
                        $stmt_profesor->execute();
                        $resultado_profesor = $stmt_profesor->fetch(PDO::FETCH_ASSOC);


                        $cedula_asesor_registrado = $resultado_profesor['numero_documento'];

                        $nombre_asesor_registrado = $resultado_profesor['nombre_usuario'];

                        $apellido_asesor_registrado = $resultado_profesor['apellidos_usuario'];

                        $correo_asesor_registrado = $resultado_profesor['correo_usuario'];

                        $telefono_asesor_registrado = $resultado_profesor['telefono_usuario'];



                        $tabla .= '<!-- Contenedor con la tarjeta del proyecto -->
                            <div id="project-container">
                            <div class="project-card">
                                <div class="project-header">
                                <div class="project-code">Documento: ' . htmlspecialchars(MainModel::encryption($cedula_asesor_registrado)) . '</div>
                                <h2 class="project-title">' . htmlspecialchars($nombre_asesor_registrado . ' ' .  $apellido_asesor_registrado) . '</h2>
                                </div>
                                <div class="project-body">
                               
                                <p><strong>Correo electronico: </strong> ' . $correo_asesor_registrado . '</p>
                                
                                ';

                        $tabla .= '    </div>
                            </div>
                            </div>';




                        $tabla .= '</div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                            </div>
                        </div>
                        </div>';
                    } else {

                        $estado_asesor_idea = '<span class="badge  bg-danger text-dark"><i class="fa-regular fa-eye"></i></span>';
                    }
                    

                   

                   /************** Extraer el último documento del proyecto aprobado ****************/

                   $codigo_ideas_documentos = $row['codigo_anteproyecto'];

                   $documento_proyecto_query = MainModel::ejecutar_consultas_simples(
                       "SELECT * FROM cargar_documento_proyectos 
                        WHERE codigo_proyecto = (
                           SELECT codigo_proyecto 
                           FROM proyectos 
                           WHERE codigo_proyecto = '$codigo_ideas_documentos' 
                           AND estado = 'Aprobado'
                        ) 
                        ORDER BY fecha_creacion DESC 
                        LIMIT 1"
                   );

                    $boton = '<span class="badge bg-danger">Sin docmuento</span>'; // Inicializar por si no hay resultado

                    if ($documento_proyecto_query->rowCount() > 0) {
                        $datos_documento = $documento_proyecto_query->fetch(PDO::FETCH_ASSOC);

                        $nombre_archivo_pdf = $datos_documento['documento'];
                        $ruta_carpeta = SERVERURL.'Views/document/proyectos/' . $codigo_ideas_documentos . '/';
                        $ruta_archivo_pdf = rtrim($ruta_carpeta, '/') . '/' . $nombre_archivo_pdf;

                        $boton = '<a href="' . $ruta_archivo_pdf . '" target="_blank" class="btn btn-primary boton_descargar_documentos">
                                    <i class="fa fa-file-pdf text-dark"></i> 
                                </a>';
                    }



                    $tabla .= '<tr>';
                    $tabla .= '<td>' . $contador++ . '</td>';
                    $tabla .= '<td>' . $row['codigo_anteproyecto'] . '</td>';
                    $tabla .= '<td>' . $row['titulo_anteproyecto'] . '</td>';
                    $tabla .= '<td>' . $row['palabras_claves'] . '</td>';
                    $tabla .= '<td>' .  $estado . '</td>';
                    $tabla .= '<td>' .  $estado_asesor_idea . '</td>';
                    $tabla .= '<td>' . $estados_anteproyectos_see . '</td>';
                    $tabla .= '<td>' . $row['nombre_facultad'] . '</td>';
                    $tabla .= '<td>' . $row['nombre_programa'] . '</td>';
                    // Supongamos que tienes la URL de cada proyecto almacenada en $row['url_anteproyecto']
                    $tabla .= '<td><button type="button" 
                    onclick="mostrarUsuariosProyecto(\'' . $row['codigo_anteproyecto'] . '\', \'' . addslashes($row['titulo_anteproyecto']) . '\', \'' . SERVERURL . '\')" 
                    class="btn btn-warning" 
                    data-bs-toggle="modal" 
                    data-bs-target="#usuariosModal">
                    <i class="fa-solid fa-users"></i>
                    </button></td>';
                    $tabla .= '<td>' . $boton . '</td>';
                }

                $reg_final = $contador - 1;
            } else {

                $tabla .= '<tr class="text-center"><td colspan="12">No hay datos para mostrar</td></tr>';
            }

            $tabla .= '</tbody></table></div>';

            // Mostrar información adicional
            if ($total >= 1 && $pagina <= $Npaginas) {
                $tabla .= '<p class="text-right">Mostrando ideas ' . $reg_inicio . ' al ' . $reg_final . ' de un total de ' . $total . '</p>';
            }

            // Generar el paginador si hay más de una página
            if ($Npaginas >= 1) {
                $tabla .= MainModel::paginador_tablas($pagina, $Npaginas, $url, 7);
            }


            $tabla .= '<div class="modal fade" id="usuariosModal" tabindex="-1" aria-labelledby="usuariosModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-center" id="usuariosModalLabel">Usuarios asignados</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h5 class="mt-2 mb-4" id="modal-titulo"></h5>
                            <!-- Contenedor para la tabla responsive -->
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="tabla_usuarios">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Correo</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla-usuarios-registrados">
                                        <!-- Aquí se mostrarán los usuarios -->
                                    </tbody>
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
            // Si no hay consulta válida o no hay datos que mostrar
            $tabla = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <div class="text-center"> <strong>¡Atención!</strong> No hay ideas de anteproyectos para mostrar al usuario</div>
                    </div>';
        }

        return $tabla;
    }


    /****************Controlador Para paginar los proyecto ***********************/
    public function paginar_proyectos_controlador($pagina, $registros, $privilegio, $url, $numero_documento_user)
    {
        // Limpiar todas las variables que se utilizan para evitar inyecciones
        $pagina = MainModel::limpiar_cadenas($pagina);
        $registros = MainModel::limpiar_cadenas($registros);
        $privilegio = MainModel::limpiar_cadenas($privilegio);
        $url = MainModel::limpiar_cadenas($url);
        $url_modal = $url;
        $url = SERVERURL . $url . "/";

        // Configurar la página inicial y el inicio de los registros
        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        // Inicializar la consulta
        $consulta = "";

        if ($privilegio == 1) {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS 
                 a.codigo_proyecto,
                 a.titulo_proyecto,
                 a.palabras_claves,
                  a.estado,
                 p.nombre_programa,
                 f.nombre_facultad
             FROM proyectos a
             INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
             INNER JOIN facultades f ON p.id_facultad = f.id_facultad
             LIMIT $inicio, $registros;";
        } else if ($privilegio == 2) {

            // Consulta para obtener las facultades y programas asignados al usuario
            $sql = "SELECT  
             auf.numero_documento,
             f.nombre_facultad, 
             p.nombre_programa, 
             f.id_facultad, 
             p.id_programa
             FROM Asignar_usuario_facultades auf
             INNER JOIN facultades f ON auf.id_facultad = f.id_facultad
             LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
             WHERE auf.numero_documento = :numero_documento_user
             ORDER BY f.nombre_facultad, p.nombre_programa";

            // Ejecutar la consulta inicial
            $check_tipo_facultad_programa = MainModel::conectar()->prepare($sql);
            $check_tipo_facultad_programa->bindParam(':numero_documento_user', $numero_documento_user, PDO::PARAM_STR);
            $check_tipo_facultad_programa->execute();

            $consulta = ""; // Reiniciar consulta para combinar
            if ($check_tipo_facultad_programa->rowCount() > 0) {
                // Crear una consulta principal para todos los resultados de facultad y programa
                $subConsultas = [];
                while ($row = $check_tipo_facultad_programa->fetch(PDO::FETCH_ASSOC)) {
                    $id_facultad = $row['id_facultad'];
                    $id_programa = $row['id_programa'];

                    // Construir la subconsulta sin `LIMIT` ni `ORDER BY`
                    $subConsultas[] = "SELECT   
                            a.codigo_proyecto,
                            a.titulo_proyecto,
                            a.palabras_claves,
                             a.estado,
                            p.nombre_programa,
                            f.nombre_facultad
                         FROM proyectos a
                         INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
                         INNER JOIN facultades f ON p.id_facultad = f.id_facultad
                         WHERE f.id_facultad = ' $id_facultad' AND p.id_programa = '$id_programa '";
                }

                // Unir todas las subconsultas para que se ejecuten como una sola
                if (!empty($subConsultas)) {
                    $consulta = implode(" UNION ALL ", $subConsultas);

                    // Añadir `ORDER BY` y `LIMIT` a la consulta global
                    $consulta .= " ORDER BY titulo_proyecto ASC LIMIT $inicio, $registros;";
                }
            }
        } else if ($privilegio == 4 || $privilegio == 5) {

            // Consulta para obtener las facultades y programas asignados al usuario
            $sql = "SELECT  
             auf.numero_documento,
             f.nombre_facultad, 
             p.nombre_programa, 
             f.id_facultad, 
             p.id_programa
             FROM Asignar_usuario_facultades auf
             INNER JOIN facultades f ON auf.id_facultad = f.id_facultad
             LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
             WHERE auf.numero_documento = :numero_documento_user
             ORDER BY f.nombre_facultad, p.nombre_programa";

            // Ejecutar la consulta inicial
            $check_tipo_facultad_programa = MainModel::conectar()->prepare($sql);
            $check_tipo_facultad_programa->bindParam(':numero_documento_user', $numero_documento_user, PDO::PARAM_STR);
            $check_tipo_facultad_programa->execute();

            $consulta = ""; // Reiniciar consulta para combinar
            if ($check_tipo_facultad_programa->rowCount() > 0) {
                // Crear una consulta principal para todos los resultados de facultad y programa
                $subConsultas = [];
                while ($row = $check_tipo_facultad_programa->fetch(PDO::FETCH_ASSOC)) {
                    $id_facultad = $row['id_facultad'];
                    $id_programa = $row['id_programa'];

                    // Construir la subconsulta sin `LIMIT` ni `ORDER BY`
                    $subConsultas[] = "SELECT   
                            a.codigo_proyecto,
                            a.titulo_proyecto,
                            a.palabras_claves,
                             a.estado,
                            p.nombre_programa,
                            f.nombre_facultad
                         FROM proyectos a
                         INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
                         INNER JOIN facultades f ON p.id_facultad = f.id_facultad
                         WHERE f.id_facultad = ' $id_facultad' AND p.id_programa = '$id_programa '";
                }

                // Unir todas las subconsultas para que se ejecuten como una sola
                if (!empty($subConsultas)) {
                    $consulta = implode(" UNION ALL ", $subConsultas);

                    // Añadir `ORDER BY` y `LIMIT` a la consulta global
                    $consulta .= " ORDER BY titulo_proyecto ASC LIMIT $inicio, $registros;";
                }
            }
        }



        // Conectar y ejecutar la consulta
        if (!empty($consulta)) {
            $conexion = mainModel::conectar();
            $datos = $conexion->query($consulta);
            $datos = $datos->fetchAll();

            // Obtener el total de registros para la paginación
            $total = $conexion->query("SELECT FOUND_ROWS() as total");
            $total = (int)$total->fetchColumn();

            // Calcular el número total de páginas
            $Npaginas = ceil($total / $registros);

            // Generar la tabla con los datos obtenidos
            $tabla = '<div class="table-responsive">
         <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
         <thead>
             <tr>
                 <th>ID</th>
                 <th>Codigo </th>
                 <th>Titulo</th>
                 <th>Palabras</th>
                 <th>Estudiantes</th>
                 <th>Director</th>
                 <th>Estado</th>
                 <th>Facultad</th>
                 <th>Programa</th>
                 <th>Usuarios</th>';
            if ($privilegio != 4  && $privilegio != 5) {
                $tabla .= '<th>Observar</th>';
                $tabla .= '<th>Editar</th>';
                $tabla .= '<th>Eliminar</th>';
            }
            $tabla .= '</tr>
         </thead>
         <tbody>';

            // Verificar si hay registros para mostrar
            if ($total >= 1 && $pagina <= $Npaginas) {
                $contador = $inicio + 1;
                $reg_inicio = $inicio + 1;
                foreach ($datos as $row) {

                    $codigo_idea = $row['codigo_proyecto'];

                    $estados_proyectos = $row['estado'];

                    $estado_idea = MainModel::ejecutar_consultas_simples(
                        "SELECT codigo_proyecto FROM asignar_estudiante_proyecto WHERE codigo_proyecto = '$codigo_idea'"
                    );

                    if ($estado_idea->rowCount() == 0) {

                        $estado = '<span class="badge bg-danger">Sin asignar estudiantes</span>';
                    } else {
                        // Extraer el estado del usuario

                        $estado = '<span class="badge bg-success">Estudiantes Asignados</span>';
                    }



                    if ($estados_proyectos == "Aprobado") {

                        $estados_proyectos_see = '<span class="badge bg-success">Aprobado</span>';
                    } else if ($estados_proyectos == "Revisión") {

                        $estados_proyectos_see = '<span class="badge bg-info">Revisión</span>';
                    } else {
                        // Extraer el estado del usuario

                        $estados_proyectos_see = '<span class="badge bg-danger">Cancelado</span>';
                    }


                    /***********************************verificar si tiene asesor *******************+*/

                    $estado_asesor_idea_query = MainModel::ejecutar_consultas_simples(
                        "SELECT codigo_proyecto FROM Asignar_asesor_anteproyecto_proyecto WHERE codigo_proyecto = '$codigo_idea'"
                    );


                    if ($estado_asesor_idea_query->rowCount() > 0) {

                        $estado_asesor_idea = '<span class="badge bg-success see-span"  data-bs-toggle="modal" data-bs-target="#proyectosasignadosasesor' . $codigo_idea . '"
                data-bs-toggle="tooltip" data-bs-placement="right" title="Revisar"><i class="fa-regular fa-eye"></i></span> ';


                        $tabla .= '<!-- Modal -->
                <div class="modal fade" id="proyectosasignadosasesor' . $codigo_idea . '" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Código: ' . htmlspecialchars($codigo_proyecto) . '</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">';
                        // Realizamos la consulta para extraer la información del proyecto
                        // Consulta para obtener la información del profesor asignado al proyecto
                        $query_profesor = "SELECT u.* 
                    FROM Asignar_asesor_anteproyecto_proyecto aa
                    INNER JOIN usuarios u ON aa.numero_documento = u.numero_documento
                    WHERE aa.codigo_proyecto = :codigo_proyecto";
                        $stmt_profesor = MainModel::conectar()->prepare($query_profesor);
                        $stmt_profesor->bindParam(":codigo_proyecto", $codigo_idea, PDO::PARAM_STR);
                        $stmt_profesor->execute();
                        $resultado_profesor = $stmt_profesor->fetch(PDO::FETCH_ASSOC);


                        $cedula_asesor_registrado = $resultado_profesor['numero_documento'];

                        $nombre_asesor_registrado = $resultado_profesor['nombre_usuario'];

                        $apellido_asesor_registrado = $resultado_profesor['apellidos_usuario'];

                        $correo_asesor_registrado = $resultado_profesor['correo_usuario'];

                        $telefono_asesor_registrado = $resultado_profesor['telefono_usuario'];



                        $tabla .= '<!-- Contenedor con la tarjeta del proyecto -->
                    <div id="project-container">
                      <div class="project-card">
                        <div class="project-header">
                          <div class="project-code">Documento: ' . htmlspecialchars($cedula_asesor_registrado) . '</div>
                          <h2 class="project-title">' . htmlspecialchars($nombre_asesor_registrado . ' ' .  $apellido_asesor_registrado) . '</h2>
                        </div>
                        <div class="project-body">
                          <p><strong>Telefono:</strong> ' . htmlspecialchars($telefono_asesor_registrado) . '</p>
                          <p><strong>Correo electronico: </strong> ' . $correo_asesor_registrado . '</p>
                          
                          ';

                        $tabla .= '    </div>
                    </div>
                    </div>';




                        $tabla .= '</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                    </div>
                </div>
                </div>';
                    } else {

                        $estado_asesor_idea = '<span class="badge  bg-danger text-dark"><i class="fa-regular fa-eye"></i></span>';
                    }



                    $tabla .= '<tr>';
                    $tabla .= '<td>' . $contador++ . '</td>';
                    $tabla .= '<td>' . $row['codigo_proyecto'] . '</td>';
                    $tabla .= '<td>' . $row['titulo_proyecto'] . '</td>';
                    $tabla .= '<td>' . $row['palabras_claves'] . '</td>';
                    $tabla .= '<td>' .  $estado . '</td>';
                    $tabla .= '<td>' .  $estado_asesor_idea . '</td>';
                    $tabla .= '<td>' .  $estados_proyectos_see . '</td>';
                    $tabla .= '<td>' . $row['nombre_facultad'] . '</td>';
                    $tabla .= '<td>' . $row['nombre_programa'] . '</td>';
                    // Supongamos que tienes la URL de cada proyecto almacenada en $row['url_anteproyecto']
                    $tabla .= '<td><button type="button" 
                 onclick="mostrarUsuariosProyecto(\'' . $row['codigo_proyecto'] . '\', \'' . addslashes($row['titulo_proyecto']) . '\', \'' . SERVERURL . '\')" 
                 class="btn btn-warning" 
                 data-bs-toggle="modal" 
                 data-bs-target="#usuariosModal">
                 <i class="fa-solid fa-users"></i>
                 </button></td>';

                    if ($privilegio != 4 && $privilegio != 5) {
                        $tabla .= '<td><a href="' . SERVERURL . 'entregas-proyectos/' . $row['codigo_proyecto']  . '/" class="btn btn-success"><i class="fa-solid fa-eye"></i></a></td>';
                        $tabla .= '<td><a href="' . SERVERURL . 'proyecto-update/' . MainModel::encryption($row['codigo_proyecto']) . '/" class="btn btn-success"><i class="far fa-edit"></i></a></td>';
                        $tabla .= '<td><form class="FormulariosAjax" action="' . SERVERURL . 'Ajax/ProyectoAjax.php" method="POST" data-form="delete" autocomplete="off">
                         <input type="hidden" name="codigo_proyecto_delete" value="' . MainModel::encryption($row['codigo_proyecto']) . '">
                         <button type="submit" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
                         </form></td>';
                        $tabla .= '</tr>';
                    }
                }


                $reg_final = $contador - 1;
            } else {
                $tabla .= '<tr class="text-center"><td colspan="12">No hay datos para mostrar</td></tr>';
            }

            // Cerrar la tabla
            $tabla .= '</tbody></table></div>';

            // Mostrar información adicional
            if ($total >= 1 && $pagina <= $Npaginas) {
                $tabla .= '<p class="text-right">Mostrando ideas ' . $reg_inicio . ' al ' . $reg_final . ' de un total de ' . $total . '</p>';
            }

            // Generar el paginador si hay más de una página
            if ($Npaginas >= 1) {
                $tabla .= MainModel::paginador_tablas($pagina, $Npaginas, $url, 7);
            }


            $tabla .= '<div class="modal fade" id="usuariosModal" tabindex="-1" aria-labelledby="usuariosModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title text-center" id="usuariosModalLabel">Usuarios asignados</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                      <h5 class="mt-2 mb-4" id="modal-titulo"></h5>
                      <!-- Contenedor para la tabla responsive -->
                      <div class="table-responsive">
                          <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="tabla_usuarios">
                              <thead>
                                  <tr>
                                      <th>Número de Documento</th>
                                      <th>Nombre</th>
                                      <th>Apellido</th>
                                      <th>Correo</th>
                                  </tr>
                              </thead>
                              <tbody id="tabla-usuarios-registrados">
                                  <!-- Aquí se mostrarán los usuarios -->
                              </tbody>
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
            // Si no hay consulta válida o no hay datos que mostrar
            $tabla = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <div class="text-center"> <strong>¡Atención!</strong> No hay proyectos para mostrar al usuario</div>
                    </div>';
        }



        return $tabla;
    }


    /****************Controlador recuperar contraseña de los usuarios ***********************/

    public function recuperar_contrasena_controlador()
    {

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

        $correo = MainModel::limpiar_cadenas($_POST["correoresetpassword"]);

        if (empty($correo)) {

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];

            echo json_encode($alerta);
            exit();
        }

        // Validar el formato del correo electrónico
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El formato del correo electrónico no es válido.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        // Ejecutar la consulta para verificar si el correo existe
        $check_correo_usuario = MainModel::ejecutar_consultas_simples("SELECT * FROM usuarios WHERE correo_usuario = '$correo'");

        // Verificar si hay coincidencias
        if ($check_correo_usuario->rowCount() == 0) {  // Si no hay coincidencias
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El correo proporcionado no existe en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        } else {
            // Si el correo existe, extraer la información del usuario
            $usuario = $check_correo_usuario->fetch(PDO::FETCH_ASSOC);  // Extrae toda la información del usuario

            // Puedes acceder a los datos del usuario de esta manera
            $id_usuario = $usuario['id'];
            $nombre_usuario = $usuario['nombre_usuario'];
            $apellidos_usuario = $usuario['apellidos_usuario'];


            /******************************************************************* */

            include __DIR__ . '/../Mail/enviar-correo.php';

            $asunto = "Confirmación de actualización de contraseña - Plataforma";

            $cuerpo_html = '
                
        <!DOCTYPE html>
            <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Bienvenido a nuestra plataforma</title>'
                . STYLESCORREO . '
                </head>
                <body>
                    <div class="email-container">
                                <div class="email-header">
                                    <img src="' . SERVERURL . 'Views/assets/images/' . $logo . '" alt="Logo Universidad">
                                   <h2>Recuperación de contraseña</h2>
                                </div>
                                <div class="email-body">
                                    <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellidos_usuario . ', has solicitado recuperar tu contraseña.</b></p>
                                      
                                    <p>Este enlace es válido por 24 horas. Si no solicitaste este cambio, puedes ignorar este mensaje.</p>

                                    <p>Para restablecer tu contraseña, por favor haz clic en el botón a continuación:</p>
        
                                    <a href="' . SERVERURL . 'restore-password/' . MainModel::encryption($id_usuario) . '" class="login-button">Iniciar sesión</a>
        
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

            $cuerpo_texto = "Hola $nombre_usuario" . "$apellidos_usuario, ";


            $token_correo = UsuarioModelo::CreartokenCorreoUSuarios($id_usuario);

            if ($token_correo->rowCount() >= 1) {


                $enviado = enviarCorreo($correo, $nombre_usuario, $apellidos_usuario, $asunto, $cuerpo_html, $cuerpo_texto);

                if ($enviado) {
                    $alerta = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Correo enviado con éxito",
                        "Texto" => "Se le ha enviado un correo al usuario para recuperar su contraseña",
                        "Tipo" => "success"
                    ];
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Error al enviar correo",
                        "Texto" => "NO se le pudo enviar el correo al usuario para recuperar su contraseña",
                        "Tipo" => "warning"
                    ];
                }

                echo json_encode($alerta);
                exit();
            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error al generar token de correo",
                    "Texto" => "NO se pudo generar el token de correo para el usuario",
                    "Tipo" => "warning"
                ];
            }

            echo json_encode($alerta);
            exit();
        }
    }

    /****************Controlador actualizar contraseña de los usuarios ***********************/
    public function actulalizar_contrasena_usuario()
    {


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


        $id_usuario = MainModel::limpiar_cadenas($_POST['IdPassword']);
        $password_usuario = MainModel::limpiar_cadenas($_POST['RestorPassword']);
        $confirm_password_usuario = MainModel::limpiar_cadenas($_POST['RestorPasswordConfirm']);

        if (empty($password_usuario) || empty($confirm_password_usuario) || empty($id_usuario)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];

            echo json_encode($alerta);
            exit();
        }

        if ($password_usuario !== $confirm_password_usuario) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Las contraseñas no coinciden.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).{8,}$/', $password_usuario)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "La contraseña debe tener al menos 8 caracteres, incluir una letra mayúscula, un número y un símbolo especial.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $id_usuario = (int)  MainModel::decryption($id_usuario);

        $password_usuario = MainModel::encryption($password_usuario);



        $check_usuario = MainModel::ejecutar_consultas_simples(
            "SELECT numero_documento,nombre_usuario, apellidos_usuario, correo_usuario FROM usuarios WHERE id = '$id_usuario'"
        );
        if ($check_usuario->rowCount() > 0) {
            $usuario = $check_usuario->fetch(PDO::FETCH_ASSOC);

            $nombre_usuario = $usuario['nombre_usuario'];

            $apellidos_usuario = $usuario['apellidos_usuario'];

            $correo = $usuario['correo_usuario'];

            $numero_documento = $usuario['numero_documento'];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se encontró el usuario para actualizar la contraseña",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $datos_usuario = [
            "numero_documento" => $numero_documento,
            "password_usuario" => $password_usuario,
            "id" => $id_usuario
        ];


        /******************************************************************* */

        $message = "<p>¡Contraseña actualizada con éxito!</p>
        <p>Tu nueva contraseña se ha guardado correctamente. Ahora puedes iniciar sesión con ella. Recuerda mantenerla segura y no compartirla con nadie.</p>
        <p>Si no realizaste este cambio o encuentras algún inconveniente, por favor contacta a nuestro soporte de inmediato.</p>";

        $password_usuario =  MainModel::decryption($password_usuario);

        include __DIR__ . '/../Mail/enviar-correo.php';

        $asunto = "Confirmación de actualización de contraseña - Plataforma";

        $cuerpo_html = '
                
        <!DOCTYPE html>
            <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Bienvenido a nuestra plataforma</title>'
            . STYLESCORREO . '
                </head>
                <body>
                    <div class="email-container">
                                <div class="email-header">
                                    <img src="' . SERVERURL . 'Views/assets/images/' . $logo . '" alt="Logo Universidad">
                                    <h2>Confirmación de actualización de contraseña</h2>
                                </div>
                                <div class="email-body">
                                    <p><b>Estimado  ' . $nombre_usuario . ' ' . $apellidos_usuario . ',</b></p>
                                    ' . $message . '
                                    <h3>🔑 Tus nuevas credenciales de acceso</h3>
                                    <div class="credentials">
                                        <ul>
                                            <li><b>Usuario:</b> ' . $numero_documento . '</li>
                                            <li><b>Contraseña:</b> ' . $password_usuario . '</li>
                                        </ul>
                                    </div>
                  
        
                                    <p>Ya puedes acceder a la plataforma y comenzar a explorar todas sus funcionalidades. Haz clic en el siguiente botón para iniciar sesión:</p>
        
                                    <a href="' . SERVERURL . 'login" class="login-button">Iniciar sesión</a>
        
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

        $cuerpo_texto = "Hola $nombre_usuario" . "$apellidos_usuario, ";


        $actualizar_contrasena = UsuarioModelo::Actualizar_contraseña_user($datos_usuario);

        if ($actualizar_contrasena && $actualizar_contrasena->rowCount() > 0) {

            $actualizarEstado =  MainModel::ejecutar_consultas_simples(
                "UPDATE recuperacion_contrasena SET estado = 1 WHERE id_usuario = $id_usuario"
            );

            $enviado = enviarCorreo($correo, $nombre_usuario, $apellidos_usuario, $asunto, $cuerpo_html, $cuerpo_texto);

            if ($enviado) {
                $alerta = [
                    "Alerta" => "Recargar",
                    "Titulo" => "Contraseña actualizada correctamente",
                    "Texto" => "Se ha actualizado la contraseña del usuario",
                    "Tipo" => "success"
                ];
            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error al enviar correo",
                    "Texto" => "NO se pudo enviar el correo al usuario para confirmar la actualización de la contraseña",
                    "Tipo" => "warning"
                ];
            }

            echo json_encode($alerta);
            exit();
        } elseif ($actualizar_contrasena && $actualizar_contrasena->rowCount() == 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Sin cambios",
                "Texto" => "No se realizó ningún cambio en la contraseña.",
                "Tipo" => "info"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo actualizar la contraseña del usuario. ",
                "Tipo" => "error"
            ];
        }


        echo json_encode($alerta);
        exit();
    }

    /****************Controlador consultar horas asesorias  de los usuarios ***********************/

    public function consultar_horas_asesorias_usuario($pagina, $registros, $privilegio, $url, $numero_documento_user)
    {
        $pagina = MainModel::limpiar_cadenas($pagina);
        $registros = MainModel::limpiar_cadenas($registros);
        $privilegio = MainModel::limpiar_cadenas($privilegio);
        $url = MainModel::limpiar_cadenas($url);
        $url_modal = $url;
        $url = SERVERURL . $url . "/";

        // Configurar la página inicial y el inicio de los registros
        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        // Inicializar la consulta
        $consulta = "";

        if ($privilegio == 1) {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS 
                           u.*,
                            ru.nombre_rol,
                            COALESCE(GROUP_CONCAT(DISTINCT f.nombre_facultad ORDER BY f.nombre_facultad SEPARATOR ', '), 'Sin asignar') AS facultades,
                            COALESCE(GROUP_CONCAT(DISTINCT p.nombre_programa ORDER BY p.nombre_programa SEPARATOR ', '), 'Sin asignar') AS programas,
                            IFNULL(SUM(ahp.numero_hora), 0) AS total_horas_asignadas,
                            IFNULL(SUM(ahj.numero_hora), 0) AS total_horas_jurado
                        FROM usuarios u
                        INNER JOIN roles_usuarios ru ON u.id_rol = ru.id_rol
                        LEFT JOIN Asignar_usuario_facultades auf ON u.numero_documento = auf.numero_documento
                        LEFT JOIN facultades f ON auf.id_facultad = f.id_facultad
                        LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
                        LEFT JOIN asignar_horas_profesor ahp ON u.numero_documento = ahp.numero_documento
                        LEFT JOIN asignar_horas_jurado_profesor ahj ON u.numero_documento = ahj.numero_documento
                        WHERE u.id_rol = 5
                        GROUP BY u.numero_documento, u.nombre_usuario, u.apellidos_usuario, ru.nombre_rol
                        ORDER BY u.nombre_usuario ASC
                        LIMIT $inicio, $registros;";
        } else if ($privilegio == 2 || $privilegio == 5) {

            $check_tipo_facultad_programa = MainModel::ejecutar_consultas_simples(
                "SELECT auf.id_facultad, auf.id_programa
                 FROM Asignar_usuario_facultades auf
                 WHERE auf.numero_documento = '$numero_documento_user'"
            );

            if ($check_tipo_facultad_programa->rowCount() > 0) {
                $consulta = "SELECT SQL_CALC_FOUND_ROWS 
                                u.*,
                                ru.nombre_rol,
                                COALESCE(GROUP_CONCAT(DISTINCT f.nombre_facultad ORDER BY f.nombre_facultad SEPARATOR ', '), 'Sin asignar') AS facultades,
                                COALESCE(GROUP_CONCAT(DISTINCT p.nombre_programa ORDER BY p.nombre_programa SEPARATOR ', '), 'Sin asignar') AS programas,
                                IFNULL(SUM(ahp.numero_hora), 0) AS total_horas_asignadas,
                                IFNULL(SUM(ahj.numero_hora), 0) AS total_horas_jurado
                            FROM usuarios u
                            INNER JOIN roles_usuarios ru ON u.id_rol = ru.id_rol
                            LEFT JOIN Asignar_usuario_facultades auf ON u.numero_documento = auf.numero_documento
                            LEFT JOIN facultades f ON auf.id_facultad = f.id_facultad
                            LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
                            LEFT JOIN asignar_horas_profesor ahp ON u.numero_documento = ahp.numero_documento
                            LEFT JOIN asignar_horas_jurado_profesor ahj ON u.numero_documento = ahj.numero_documento
                            WHERE ru.id_rol = 5 AND auf.id_facultad IN (SELECT id_facultad FROM Asignar_usuario_facultades WHERE numero_documento = '$numero_documento_user')
                            GROUP BY u.numero_documento, u.nombre_usuario, u.apellidos_usuario, ru.nombre_rol
                            ORDER BY u.nombre_usuario ASC
                            LIMIT $inicio, $registros;";
            } else {
                return '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <div class="text-center"><strong>¡Atención!</strong> No hay usuarios que estén asignados a las facultades del usuario logueado.</div>
                        </div>';
            }
        }

        if (!empty($consulta)) {
            $conexion = mainModel::conectar();
            $datos = $conexion->query($consulta);
            $datos = $datos->fetchAll();

            // Obtener el total de registros para la paginación
            $total = $conexion->query("SELECT FOUND_ROWS() as total");
            $total = (int)$total->fetchColumn();

            // Calcular el número total de páginas
            $Npaginas = ceil($total / $registros);

            // Generar la tabla con los datos obtenidos
            $tabla = '<div class="table-responsive">
              <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
              <thead>
                  <tr>
                      <th>ID</th>
                      <th>Imagen</th>
                      <th>Documento</th>
                      <th>Horas asesoría</th>
                      <th>Horas jurado</th>
                      <th>Nombre</th>
                      <th>Apellidos</th>
                      <th>Rol</th>
                      <th>Facultad</th>
                      <th>Programa</th>';

            // Asegúrate de cerrar las comillas y concatenar la cadena condicionalmente
            if ($privilegio != 5) {
                $tabla .= '
                                    <th>Sumar asesoría</th>
                                    <th>Restar asesoría</th>
                                    <th>Sumar jurado</th>
                                    <th>Restar jurado</th>
                                    <th>Eliminar</th>';
            }

            $tabla .= '</tr>
                            </thead>
                            <tbody>';


            // Verificar si hay registros para mostrar
            if ($total >= 1 && $pagina <= $Npaginas) {
                $contador = $inicio + 1;
                $reg_inicio = $inicio + 1;
                foreach ($datos as $row) {

                    $numero_documento_asesor = $row['numero_documento'];

                    $estado_registros = MainModel::ejecutar_consultas_simples(
                        "SELECT COUNT(*) as total FROM Asignar_usuario_facultades WHERE numero_documento = '$numero_documento_asesor'"
                    );

                    $datos = $estado_registros->fetch(PDO::FETCH_ASSOC);

                    $total_registros = (int) $datos['total'];

                    $total_horas_asesorias = (int) $row['total_horas_asignadas'];

                    $total_horas_jurado = (int) $row['total_horas_jurado'];

                    if ($total_registros > 0) {

                        $total_horas_asesorias = $total_horas_asesorias / $total_registros;

                        $total_horas_jurado =   $total_horas_jurado / $total_registros;
                    } else {
                        // Manejar el caso en que no hay registros para evitar división por cero
                        $total_horas_asesorias = 0;
                        $total_horas_jurado = 0;
                    }


                    $tabla .= '<tr>';
                    $tabla .= '<td>' . $contador++ . '</td>';
                    $tabla .= '<td><img src="' . SERVERURL . '/Views/assets/images/avatar/' . $row['imagen_usuario'] . '" alt="Usuario" width="40" height="40"></td>';
                    if ($row['numero_documento'] == $numero_documento_user) {
                        $tabla .= '<td><span class="badge bg-success">' . $row['numero_documento'] . '</span></td>';
                    } else {
                        $tabla .= '<td>' . $row['numero_documento'] . '</td>';
                    }
                    $tabla .= '<td>' . ($total_horas_asesorias) . '</td>';
                    $tabla .= '<td>' . ($total_horas_jurado) . '</td>';
                    $tabla .= '<td>' . $row['nombre_usuario'] . '</td>';
                    $tabla .= '<td>' . $row['apellidos_usuario'] . '</td>';
                    $tabla .= '<td>' . $row['nombre_rol'] . '</td>';
                    $tabla .= '<td>' . $row['facultades'] . '</td>';
                    $tabla .= '<td>' . $row['programas'] . '</td>';
                    // Supongamos que tienes la URL de cada proyecto almacenada en $row['url_anteproyecto']

                    if ($privilegio != 5) {

                        $tabla .= '<td><form class="FormulariosAjax" action="' . SERVERURL . 'Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
                                <input type="hidden" name="numero_documento_sum_user" value="' . MainModel::encryption($row['numero_documento']) . '">
                                <button type="submit" class="btn btn-success"><i class="fa-solid fa-plus"  data-bs-toggle="tooltip" data-bs-placement="left" title="Tooltip on left"></i></i></button>
                                </form></td>';
                        $tabla .= '<td><form class="FormulariosAjax" action="' . SERVERURL . 'Ajax/ProyectoAjax.php" method="POST" data-form="delete" autocomplete="off">
                                <input type="hidden" name="numero_documento_res_user" value="' . MainModel::encryption($row['numero_documento']) . '">
                                <button type="submit" class="btn btn-warning"><i class="fa-solid fa-minus"></i></button>
                                </form></td>';

                        /************jurado********************* */


                        $tabla .= '<td><form class="FormulariosAjax" action="' . SERVERURL . 'Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
                                <input type="hidden" name="numero_documento_sum_user_jurado" value="' . MainModel::encryption($row['numero_documento']) . '">
                                <button type="submit" class="btn btn-success"><i class="fa-solid fa-plus"  data-bs-toggle="tooltip" data-bs-placement="left" title="Tooltip on left"></i></i></button>
                                </form></td>';
                        $tabla .= '<td><form class="FormulariosAjax" action="' . SERVERURL . 'Ajax/ProyectoAjax.php" method="POST" data-form="delete" autocomplete="off">
                                <input type="hidden" name="numero_documento_res_user_jurado" value="' . MainModel::encryption($row['numero_documento']) . '">
                                <button type="submit" class="btn btn-warning"><i class="fa-solid fa-minus"></i></button>
                                </form></td>';

                        $tabla .= '<td><form class="FormulariosAjax" action="' . SERVERURL . 'Ajax/ProyectoAjax.php" method="POST" data-form="delete" autocomplete="off">
                                <input type="hidden" name="delete_horas_asesor" value="' . MainModel::encryption($row['numero_documento']) . '">
                                <button type="submit" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
                                </form></td>';
                    }

                    $tabla .= '</tr>';
                }


                $reg_final = $contador - 1;
            } else {
                $tabla .= '<tr class="text-center"><td colspan="12">No hay datos para mostrar</td></tr>';
            }

            // Cerrar la tabla
            $tabla .= '</tbody></table></div>';

            // Mostrar información adicional
            if ($total >= 1 && $pagina <= $Npaginas) {
                $tabla .= '<p class="text-right">Mostrando usuarios ' . $reg_inicio . ' al ' . $reg_final . ' de un total de ' . $total . '</p>';
            }

            // Generar el paginador si hay más de una página
            if ($Npaginas > 1) {
                $tabla .= MainModel::paginador_tablas($pagina, $Npaginas, $url, 7);
            }
        } else {
            // Si no hay consulta válida o no hay datos que mostrar
            $tabla = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
             <div class="text-center"> <strong>¡Atención!</strong> No hay usuarios para mostrar al usuario</div>
             </div>';
        }


        return $tabla;
    }

    public function Actualizar_informacion_user()
    {

        $nombre = MainModel::limpiar_cadenas($_POST['nombre_usuario_reg']);
        $apellido = MainModel::limpiar_cadenas($_POST['apellido_usuario_reg']);
        $correo = MainModel::limpiar_cadenas($_POST['email_usuario_reg']);
        $contraseña = MainModel::limpiar_cadenas($_POST['password_usuario_reg']);
        $confirmarcontraseña = MainModel::limpiar_cadenas($_POST['confirm_password_usuario_reg']);
        $contraseña_actual_usuario = MainModel::limpiar_cadenas($_POST['password_usuario_actual']);
        $nombre_archivo = $_FILES['imagen_user']['name'];
        $tipo_archivo = $_FILES['imagen_user']['type'];
        $numero_documento_user_logueado = MainModel::limpiar_cadenas($_POST['numero_documento_user_logueado']);

        if (empty($nombre) || empty($apellido) || empty($correo) || empty($numero_documento_user_logueado)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $numero_documento_user_logueado =  MainModel::decryption($numero_documento_user_logueado);

        $check_usuario = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM usuarios WHERE numero_documento = '$numero_documento_user_logueado'"
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



        $datos_usuario = $check_usuario->fetch(PDO::FETCH_ASSOC);

        $contraseña_user_registrada = $datos_usuario['contrasena_usuario'];

        $imagen_usuario_registrada = $datos_usuario['imagen_usuario'];




        if (!empty($contraseña) && !empty($confirmarcontraseña)) {

            $contraseña =  MainModel::encryption($contraseña);

            $contraseña_actual_usuario =  MainModel::encryption($contraseña_actual_usuario);


            if ($contraseña_actual_usuario != $contraseña_user_registrada) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Contraseña no válida",
                    "Texto" => "La contraseña ingresada no es la que tiene registrada, intente nuevamente",
                    "Tipo" => "warning"
                ];
                echo json_encode($alerta);
                exit();
            }


            if ($contraseña_user_registrada == $contraseña) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Contraseña no válida",
                    "Texto" => "La contraseña debe ser diferente a la contraseña anterior.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }



            $contraseña =  MainModel::decryption($contraseña);

            if ($contraseña != $confirmarcontraseña) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Contraseñas no coinciden",
                    "Texto" => "Las contraseñas no coinciden.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if (!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).{8,}$/', $contraseña)) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "La contraseña debe tener al menos 8 caracteres, incluir una letra mayúscula, un número y un símbolo especial.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if (empty($_FILES['imagen_user']['name'][0])) {


                $nombre_archivo = $datos_usuario['imagen_usuario'];

                $contraseña =  MainModel::encryption($contraseña);

                $datos = [
                    'numero_documento' => $numero_documento_user_logueado,
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'email' => $correo,
                    'contrasena_usuario' =>  $contraseña,
                    'imagenes' => $nombre_archivo // Guardamos el nombre único generado
                ];

                $resultado_actualizacion =  UsuarioModelo::actualizar_informacion_usuario_modelo($datos);

                if ($resultado_actualizacion) {
                    $alerta = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Información Actualizada",
                        "Texto" => "Se ha actualizado correctamente la información",
                        "Tipo" => "success"
                    ];
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Error al actualizar",
                        "Texto" => "No se pudo actualizar la información del usuario",
                        "Tipo" => "error"
                    ];
                }

                echo json_encode($alerta);
                exit();
            } else {


                $nombre_carpeta = '../Views/assets/images/avatar/';

                // Verificar si el archivo es una imagen válida
                $tipo_archivo = $_FILES['imagen_user']['type'];
                if (!in_array($tipo_archivo, ["image/jpeg", "image/png", "image/gif"])) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Formato de archivo no válido",
                        "Texto" => "El archivo debe estar en formato de imagen (JPEG, PNG o GIF).",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                $guardado_exitoso = true;  // Bandera de éxito

                // Procesar solo una imagen
                if (isset($_FILES['imagen_user']['name']) && !empty($_FILES['imagen_user']['name'])) {
                    $tmp_name = $_FILES['imagen_user']['tmp_name'];
                    $nombre_unico = uniqid() . '_' . basename($_FILES['imagen_user']['name']);
                    $ruta_completa = $nombre_carpeta . $nombre_unico;

                    // Eliminar la imagen anterior si no es la imagen base
                    if ($imagen_usuario_registrada && $imagen_usuario_registrada !== 'AvatarNone.png') {
                        $ruta_imagen_actual = $nombre_carpeta . $imagen_usuario_registrada;
                        if (file_exists($ruta_imagen_actual)) {
                            unlink($ruta_imagen_actual);
                        }
                    }

                    // Intento de mover el archivo
                    if (move_uploaded_file($tmp_name, $ruta_completa)) {
                        // Preparar los datos para actualizar en la base de datos
                        $contraseña =  MainModel::encryption($contraseña);

                        $datos = [
                            'numero_documento' => $numero_documento_user_logueado,
                            'nombre' => $nombre,
                            'apellido' => $apellido,
                            'email' => $correo,
                            'contrasena_usuario' => $contraseña,
                            'imagenes' => $nombre_unico // Guardamos el nombre único generado
                        ];

                        // Llamar a la función de actualización del modelo
                        if (!UsuarioModelo::actualizar_informacion_usuario_modelo($datos)) {
                            $guardado_exitoso = false;
                        }
                    } else {
                        $guardado_exitoso = false;
                    }
                } else {
                    $guardado_exitoso = false;
                }

                if ($guardado_exitoso) {
                    $alerta = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Información Actualizada",
                        "Texto" => "Se ha actualizado correctamente la información",
                        "Tipo" => "success"
                    ];
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Error al actualizar",
                        "Texto" => "No se pudo actualizar la información del usuario",
                        "Tipo" => "error"
                    ];
                }

                echo json_encode($alerta);
                exit();
            }
        } else {


            if (empty($_FILES['imagen_user']['name'][0])) {

                $nombre_archivo = $datos_usuario['imagen_usuario'];

                $datos = [
                    'numero_documento' => $numero_documento_user_logueado,
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'email' => $correo,
                    'contrasena_usuario' => $contraseña_user_registrada,
                    'imagenes' => $nombre_archivo // Guardamos el nombre único generado
                ];

                $resultado_actualizacion =  UsuarioModelo::actualizar_informacion_usuario_modelo($datos);

                if ($resultado_actualizacion) {
                    $alerta = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Información Actualizada",
                        "Texto" => "Se ha actualizado correctamente la información",
                        "Tipo" => "success"
                    ];
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Error al actualizar",
                        "Texto" => "No se pudo actualizar la información del usuario",
                        "Tipo" => "error"
                    ];
                }

                echo json_encode($alerta);
                exit();
            } else {




                $nombre_carpeta = '../Views/assets/images/avatar/';

                // Verificar si el archivo es una imagen válida
                $tipo_archivo = $_FILES['imagen_user']['type'];
                if (!in_array($tipo_archivo, ["image/jpeg", "image/png", "image/gif"])) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Formato de archivo no válido",
                        "Texto" => "El archivo debe estar en formato de imagen (JPEG, PNG o GIF).",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }


                $guardado_exitoso = true;  // Bandera de éxito

                // Procesar solo una imagen
                if (isset($_FILES['imagen_user']['name']) && !empty($_FILES['imagen_user']['name'])) {
                    $tmp_name = $_FILES['imagen_user']['tmp_name'];
                    $nombre_unico = uniqid() . '_' . basename($_FILES['imagen_user']['name']);
                    $ruta_completa = $nombre_carpeta . $nombre_unico;

                    // Eliminar la imagen anterior si no es la imagen base
                    if ($imagen_usuario_registrada && $imagen_usuario_registrada !== 'AvatarNone.png') {
                        $ruta_imagen_actual = $nombre_carpeta . $imagen_usuario_registrada;
                        if (file_exists($ruta_imagen_actual)) {
                            unlink($ruta_imagen_actual);
                        }
                    }

                    // Intento de mover el archivo
                    if (move_uploaded_file($tmp_name, $ruta_completa)) {
                        // Preparar los datos para actualizar en la base de datos

                        $datos = [
                            'numero_documento' => $numero_documento_user_logueado,
                            'nombre' => $nombre,
                            'apellido' => $apellido,
                            'email' => $correo,
                            'contrasena_usuario' => $contraseña_user_registrada,
                            'imagenes' => $nombre_unico // Guardamos el nombre único generado
                        ];

                        // Llamar a la función de actualización del modelo
                        if (!UsuarioModelo::actualizar_informacion_usuario_modelo($datos)) {
                            $guardado_exitoso = false;
                        }
                    } else {
                        $guardado_exitoso = false;
                    }
                } else {
                    $guardado_exitoso = false;
                }

                if ($guardado_exitoso) {
                    $alerta = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Información Actualizada",
                        "Texto" => "Se ha actualizado correctamente la información",
                        "Tipo" => "success"
                    ];
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Error al actualizar",
                        "Texto" => "No se pudo actualizar la información del usuario",
                        "Tipo" => "error"
                    ];
                }

                echo json_encode($alerta);
                exit();
            }
        }
    }

    public function Actualizar_logo_aplication()
    {

        $nombre_archivo = $_FILES['nuevo_logo']['name'];
        $tipo_archivo = $_FILES['nuevo_logo']['type'];

        if (empty($_FILES['nuevo_logo']['name'])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Por favor seleccione una imagen.",
                "Tipo" => "warning"
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $nombre_carpeta = '../Views/assets/images/';

            // Verificar si el archivo es una imagen válida
            $tipo_archivo = $_FILES['nuevo_logo']['type'];
            if (!in_array($tipo_archivo, ["image/jpeg", "image/png", "image/gif"])) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Formato de archivo no válido",
                    "Texto" => "El archivo debe estar en formato de imagen (JPEG, PNG o GIF).",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            // Verificar si existe una imagen registrada en la base de datos
            $check_imagen_aplicacion = MainModel::ejecutar_consultas_simples(
                "SELECT * FROM configuracion_aplicacion"
            );

            if ($check_imagen_aplicacion->rowCount() <= 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "No hay imagen registrada en el sistema.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            $aplicacion = $check_imagen_aplicacion->fetch(PDO::FETCH_ASSOC);
            $imagen_logo_registrada = $aplicacion['nombre_logo'];

            // Ruta del archivo antiguo
            $ruta_imagen_antigua = $nombre_carpeta . $imagen_logo_registrada;

            // Ruta del nuevo archivo
            $nombre_archivo_nuevo = uniqid() . '_' . $nombre_archivo; // Generar un nombre único
            $ruta_archivo_nuevo = $nombre_carpeta . $nombre_archivo_nuevo;

            // Mover la nueva imagen a la carpeta
            if (move_uploaded_file($_FILES['nuevo_logo']['tmp_name'], $ruta_archivo_nuevo)) {
                // Eliminar la imagen anterior si existe
                if (file_exists($ruta_imagen_antigua) && $imagen_logo_registrada != "") {
                    unlink($ruta_imagen_antigua);
                }

                // Actualizar la base de datos con el nuevo nombre de archivo
                $actualizar_imagen = MainModel::ejecutar_consultas_simples(
                    "UPDATE configuracion_aplicacion SET nombre_logo = '$nombre_archivo_nuevo'"
                );

                if ($actualizar_imagen) {
                    $alerta = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Operación exitosa",
                        "Texto" => "La imagen fue actualizada correctamente.",
                        "Tipo" => "success"
                    ];
                } else {
                    // Si la actualización en la base de datos falla, eliminar la nueva imagen
                    if (file_exists($ruta_archivo_nuevo)) {
                        unlink($ruta_archivo_nuevo);
                    }

                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado",
                        "Texto" => "No se pudo actualizar la imagen en la base de datos.",
                        "Tipo" => "error"
                    ];
                }
                echo json_encode($alerta);
                exit();
            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "No se pudo subir la nueva imagen.",
                    "Tipo" => "error"
                ];
            }

            echo json_encode($alerta);
            exit();
        }
    }

    public function registrar_facultada_controlador()
    {

        $configuration_name_facultad = MainModel::limpiar_cadenas($_POST['configuration_name_facultad']);

        if (empty($configuration_name_facultad)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_facultad = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM facultades WHERE nombre_facultad = '$configuration_name_facultad'"
        );

        if ($check_facultad->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Ya existe una facultad registrada con ese nombre",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $regitrar_facultad = UsuarioModelo::Registrar_facultad_modelo($configuration_name_facultad);  // Pasar el valor, no un array

        if ($regitrar_facultad->rowCount() >= 1) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Facultad Registrada",
                "Texto" => "Se registro correctamente la facultad",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo registrar la facultad",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
    }

    public function registrar_modalidad_controlador()
    {



        $configuration_name_modalidad = MainModel::limpiar_cadenas($_POST['configuration_name_modalidad']);

        if (empty($configuration_name_modalidad)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_facultad = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM modalidad_grados WHERE nombre_modalidad  = '$configuration_name_modalidad'"
        );

        if ($check_facultad->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Ya existe una modalidad registrada con ese nombre",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $registrar_modalidad = UsuarioModelo::Registrar_modalidad_modelo($configuration_name_modalidad);

        if ($registrar_modalidad) { // Si devuelve un ID, se insertó correctamente
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Modalidad Registrada",
                "Texto" => "Se registró correctamente la modalidad.",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo registrar la modalidad.",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
        exit();
    }

    public function eliminar_facultad_controlador()
    {
        $configuration_id_facultad = MainModel::limpiar_cadenas($_POST['configuration_id_facultad']);

        if (empty($configuration_id_facultad)) {

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];

            echo json_encode($alerta);
            exit();
        }

        $configuration_id_facultad =  MainModel::decryption($configuration_id_facultad);

        $check_facultad = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM facultades WHERE id_facultad = '$configuration_id_facultad'"
        );

        if ($check_facultad->rowCount() < 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se encontro una faculta con el id proporcianado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_facultad_programa = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM programas_academicos WHERE id_facultad = '$configuration_id_facultad'"
        );

        if ($check_facultad_programa->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Esta facultad no se puede eliminar porque ya tiene asignada un programa académico ",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $eliminar_facultad = UsuarioModelo::Eliminar_facultad_modelo($configuration_id_facultad);  // Pasar el valor, no un array

        if ($eliminar_facultad->rowCount() >= 1) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Usuario Eliminado",
                "Texto" => "La Facultad se ha eliminado correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo eliminar la Facultad",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
    }

    public function actualizar_facultad_controlador()
    {
        $configuration_id_facultad = MainModel::limpiar_cadenas($_POST['configuration_id_facultad_upd']);

        $texto = MainModel::limpiar_cadenas($_POST['text_facultad_upd']);

        if (empty($configuration_id_facultad) || empty($texto)) {

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios, o no se detectaron cambios en nombre de la facultad",
                "Tipo" => "error"
            ];

            echo json_encode($alerta);
            exit();
        };

        $configuration_id_facultad =  MainModel::decryption($configuration_id_facultad);

        $check_facultad = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM facultades WHERE id_facultad = '$configuration_id_facultad'"
        );

        if ($check_facultad->rowCount() < 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se encontro una facultad con el id proporcianado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_facultad_programa = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM programas_academicos WHERE id_facultad = '$configuration_id_facultad'"
        );

        if ($check_facultad_programa->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Esta facultad no se puede eliminar porque ya tiene asignada un programa académico ",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $actualizar_facultad = UsuarioModelo::Actualizar_facultad_modelo($configuration_id_facultad, $texto);  // Pasar el ID y el nuevo nombre

        if ($actualizar_facultad && $actualizar_facultad->rowCount() >= 1) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Facultad Actualizada",
                "Texto" => "El nombre de la facultad se ha actualizado correctamente.",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo actualizar el nombre de la facultad. Verifica los datos e intenta nuevamente.",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
        exit();
    }

    public function actualizar_programas_controlador()
    {
        $configuration_id_facultad = MainModel::limpiar_cadenas($_POST['configuration_id_programa_upd']);

        $texto = MainModel::limpiar_cadenas($_POST['nombre_programa_actualizado']);

        if (empty($configuration_id_facultad) || empty($texto)) {

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios, o no se detectaron cambios en nombre de la facultad",
                "Tipo" => "error"
            ];

            echo json_encode($alerta);
            exit();
        }



        $configuration_id_facultad =  MainModel::decryption($configuration_id_facultad);

        $check_facultad = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM programas_academicos WHERE id_programa = '$configuration_id_facultad'"
        );

        if ($check_facultad->rowCount() < 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se encontro un programa con el id proporcianado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }




        $actualizar_facultad = UsuarioModelo::Actualizar_programa_modelo($configuration_id_facultad, $texto);  // Pasar el ID y el nuevo nombre

        if ($actualizar_facultad && $actualizar_facultad->rowCount() >= 1) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Facultad Actualizada",
                "Texto" => "El nombre del programa se ha actualizado correctamente.",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo actualizar el nombre del programa. Verifica los datos e intenta nuevamente.",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
        exit();
    }

    public function actualizar_modaidad_controlador()
    {

        $configuration_id_facultad = MainModel::limpiar_cadenas($_POST['modalidad_id']);

        $texto = MainModel::limpiar_cadenas($_POST['modalidad_nombre_actualizado']);

        if (empty($configuration_id_facultad) || empty($texto)) {

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios, o no se detectaron cambios en nombre de la modalidad",
                "Tipo" => "error"
            ];

            echo json_encode($alerta);
            exit();
        }



        $configuration_id_facultad =  MainModel::decryption($configuration_id_facultad);

        $check_facultad = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM modalidad_grados WHERE id_modalidad = '$configuration_id_facultad'"
        );

        if ($check_facultad->rowCount() < 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se encontro una modalidad con el id proporcianado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }




        $actualizar_modalidad = UsuarioModelo::Actualizar_modalidad_modelo($configuration_id_facultad, $texto);  // Pasar el ID y el nuevo nombre

        if ($actualizar_modalidad && $actualizar_modalidad->rowCount() >= 1) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Facultad Actualizada",
                "Texto" => "El nombre de la modalidad  se ha actualizado correctamente.",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo actualizar el nombre de la modalidad. Verifica los datos e intenta nuevamente.",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
        exit();
    }

    public function eliminar_modalidad_controlador()
    {
        $configuration_id_modalidad = MainModel::limpiar_cadenas($_POST['configuration_id_modalidad']);

        if (empty($configuration_id_modalidad)) {

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];

            echo json_encode($alerta);
            exit();
        }

        $configuration_id_modalidad =  MainModel::decryption($configuration_id_modalidad);

        $check_facultad = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM modalidad_grados WHERE id_modalidad = '$configuration_id_modalidad'"
        );

        if ($check_facultad->rowCount() < 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se encontro una modalidad con el id proporcianado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $eliminar_modalidad = UsuarioModelo::Eliminar_modalidad_modelo($configuration_id_modalidad);  // Pasar el ID

        if ($eliminar_modalidad && $eliminar_modalidad > 0) { // Verificar si se eliminó al menos un registro
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Modalidad Eliminada",
                "Texto" => "La modalidad se ha eliminado correctamente.",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo eliminar la modalidad.",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
        exit();
    }

    public function registrar_programas_controlador()
    {

        $tipo_faculta = MainModel::limpiar_cadenas($_POST['tipo_faculta_reg']);
        $nombre_programa = MainModel::limpiar_cadenas($_POST['configuration_name_programa']);


        if (empty($nombre_programa) || empty($tipo_faculta)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $tipo_faculta = (int) MainModel::decryption($tipo_faculta);
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

        $check_programa = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM programas_academicos WHERE nombre_programa = '$nombre_programa' AND id_facultad = '$tipo_faculta'"
        );

        if ($check_programa->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Ya existe un programa académico registrado con ese nombre y en esa facultad",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $datos_registrar = [
            "id_facultad" => $tipo_faculta,
            "nombre_programa" => $nombre_programa
        ];

        $regitrar_programa = UsuarioModelo::Registrar_programas_modelo($datos_registrar);

        if ($regitrar_programa->rowCount() >= 1) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Programa Registrado",
                "Texto" => "Se registro correctamente el programa académico",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo registrar el programa académico",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
        exit();
    }

    public function eliminar_programas_controlador()
    {

        $tipo_faculta = MainModel::limpiar_cadenas($_POST['id_facultad_configuration_delete']);
        $tipo_programa = MainModel::limpiar_cadenas($_POST['id_programa_configuration_delete']);


        if (empty($tipo_programa) || empty($tipo_faculta)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $tipo_faculta = (int) MainModel::decryption($tipo_faculta);
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


        $tipo_programa = (int) MainModel::decryption($tipo_programa);
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

        $datos_registrar = [
            "id_facultad" => $tipo_faculta,
            "id_programa" => $tipo_programa
        ];

        $delete_programa = UsuarioModelo::Eliminar_programa_academico_modelo($datos_registrar);

        if ($delete_programa->rowCount() >= 1) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Programa Registrado",
                "Texto" => "Se elimino correctamente el programa académico",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo eliminar el programa académico",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
        exit();
    }

    public function actualizar_numero_estudiantes_proyectos_controlador()
    {

        $numero_estudiantes = MainModel::limpiar_cadenas($_POST['configuration_numero_estudiantes_proyectos']);


        $configuration_numero_jurados_proyecto = MainModel::limpiar_cadenas($_POST['configuration_numero_jurados_proyecto']);


        $nombre_archivo = $_FILES['nuevo_logo']['name'];

        $tipo_archivo = $_FILES['nuevo_logo']['type'];


        $opcion_configuracion_aplicacion = MainModel::limpiar_cadenas($_POST['opcion_configuracion_aplicacion']);

        $consecutivo = MainModel::limpiar_cadenas($_POST['consecutivo']);

        $opcion_configuracion_aplicacion = (int) MainModel::decryption($opcion_configuracion_aplicacion);

        $consecutivo = (int) MainModel::decryption($consecutivo);


        if (!in_array($opcion_configuracion_aplicacion, [1, 2])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error de validación",
                "Texto" => "La opción que ha seleccionado no existe, ingrese una valida ",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        if (
            empty($numero_estudiantes) && empty($configuration_numero_jurados_proyecto)

        ) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }



        if (MainModel::verificar_datos("[0-9]{1,20}$", $numero_estudiantes)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El numero de estudiantes no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }




        if (!empty($_FILES['nuevo_logo']['name'])) { // si la imagen no viene definida como vacia

            $nombre_carpeta = '../Views/assets/images/';

            // Verificar si el archivo es una imagen válida
            $tipo_archivo = $_FILES['nuevo_logo']['type'];
            if (!in_array($tipo_archivo, ["image/jpeg", "image/png", "image/gif"])) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Formato de archivo no válido",
                    "Texto" => "El archivo debe estar en formato de imagen (JPEG, PNG o GIF).",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            // Verificar si existe una imagen registrada en la base de datos
            $check_imagen_aplicacion = MainModel::ejecutar_consultas_simples(
                "SELECT * FROM configuracion_aplicacion"
            );

            if ($check_imagen_aplicacion->rowCount() <= 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "No hay imagen registrada en el sistema.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            $aplicacion = $check_imagen_aplicacion->fetch(PDO::FETCH_ASSOC);
            $imagen_logo_registrada = $aplicacion['nombre_logo'];

            // Ruta del archivo antiguo
            $ruta_imagen_antigua = $nombre_carpeta . $imagen_logo_registrada;

            // Ruta del nuevo archivo
            $nombre_archivo_nuevo = uniqid() . '_' . $nombre_archivo; // Generar un nombre único
            $ruta_archivo_nuevo = $nombre_carpeta . $nombre_archivo_nuevo;

            if (move_uploaded_file($_FILES['nuevo_logo']['tmp_name'], $ruta_archivo_nuevo)) {

                // Verificar si la imagen existe y si su nombre no es "logo-autonoma.png"
                if (file_exists($ruta_imagen_antigua) && $imagen_logo_registrada != "" && $imagen_logo_registrada !== "logo-autonoma.png") {
                    unlink($ruta_imagen_antigua); // Eliminar la imagen antigua
                }

                if ($opcion_configuracion_aplicacion == 1) {

                    $datos_guardar = [
                        "numero_estudiantes" => $numero_estudiantes,
                        "numero_jurados_proyectos" => $configuration_numero_jurados_proyecto,
                        "nombre_logo" => $nombre_archivo_nuevo
                    ];

                    $guardar_informacion = UsuarioModelo::Registrar_configuracion_aplicacion_modelo($datos_guardar);


                    // Verificar si se guardó correctamente
                    if ($guardar_informacion) { // Si devuelve un ID o true, significa que se guardó correctamente
                        $alerta = [
                            "Alerta" => "Recargar",
                            "Titulo" => "Registro exitoso",
                            "Texto" => "Se guardó la información correctamente.",
                            "Tipo" => "success"
                        ];
                    } else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrió un error inesperado",
                            "Texto" => "No se pudo guardar la información.",
                            "Tipo" => "error"
                        ];
                    }

                    echo json_encode($alerta);
                    exit();
                } else if ($opcion_configuracion_aplicacion == 2) {

                    $datos_actualizar = [
                        "consecutivo" => $consecutivo,
                        "numero_estudiantes" => $numero_estudiantes,
                        "numero_jurados_proyectos" => $configuration_numero_jurados_proyecto,
                        "nombre_logo" => $nombre_archivo_nuevo
                    ];

                    $actualizar_informacion = UsuarioModelo::Actualizar_configuracion_aplicacion_modelo($datos_actualizar);

                    // Verificar si se actualizó correctamente
                    if ($actualizar_informacion && $actualizar_informacion > 0) {
                        $alerta = [
                            "Alerta" => "Recargar",
                            "Titulo" => "Actualización exitosa",
                            "Texto" => "La información se actualizó correctamente.",
                            "Tipo" => "success"
                        ];
                    } else {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Sin cambios",
                            "Texto" => "No se realizaron cambios en la información.",
                            "Tipo" => "warning"
                        ];
                    }

                    echo json_encode($alerta);
                    exit();
                }
            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "No se pudo subir la nueva imagen.",
                    "Tipo" => "error"
                ];

                echo json_encode($alerta);
                exit();
            }
        } else {

            // Verificar si existe una imagen registrada en la base de datos
            $check_imagen_aplicacion = MainModel::ejecutar_consultas_simples(
                "SELECT * FROM configuracion_aplicacion"
            );

            if ($check_imagen_aplicacion->rowCount() <= 0) {
                // Si no hay imagen registrada, asignamos un nombre específico
                $imagen_logo_registrada = "logo-autonoma.png";
            } else {
                // Si existe, obtenemos el nombre de la imagen de la base de datos
                $aplicacion = $check_imagen_aplicacion->fetch(PDO::FETCH_ASSOC);

                $imagen_logo_registrada = $aplicacion['nombre_logo'];
            }

            if ($opcion_configuracion_aplicacion == 1) {

                $datos_guardar = [
                    "numero_estudiantes" => $numero_estudiantes,
                    "numero_jurados_proyectos" => $configuration_numero_jurados_proyecto,
                    "nombre_logo" => $imagen_logo_registrada
                ];

                $guardar_informacion = UsuarioModelo::Registrar_configuracion_aplicacion_modelo($datos_guardar);


                // Verificar si se guardó correctamente
                if ($guardar_informacion) { // Si devuelve un ID o true, significa que se guardó correctamente
                    $alerta = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Registro exitoso",
                        "Texto" => "Se guardó la información correctamente.",
                        "Tipo" => "success"
                    ];
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado",
                        "Texto" => "No se pudo guardar la información.",
                        "Tipo" => "error"
                    ];
                }

                echo json_encode($alerta);
                exit();
            } else if ($opcion_configuracion_aplicacion == 2) {

                $datos_actualizar = [
                    "consecutivo" => $consecutivo,
                    "numero_estudiantes" => $numero_estudiantes,
                    "numero_jurados_proyectos" => $configuration_numero_jurados_proyecto,
                    "nombre_logo" => $imagen_logo_registrada
                ];

                $actualizar_informacion = UsuarioModelo::Actualizar_configuracion_aplicacion_modelo($datos_actualizar);

                // Verificar si se actualizó correctamente
                if ($actualizar_informacion && $actualizar_informacion > 0) {
                    $alerta = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Actualización exitosa",
                        "Texto" => "La información se actualizó correctamente.",
                        "Tipo" => "success"
                    ];
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Sin cambios",
                        "Texto" => "No se realizaron cambios en la información.",
                        "Tipo" => "warning"
                    ];
                }

                echo json_encode($alerta);
                exit();
            }
        }
    }


    public function actualizar_asesor_anteproyecto_proyecto()
    {
        $codigo = MainModel::limpiar_cadenas($_POST['codigo_idea_actualizar_asesor']);
        $codigo_proyecto = MainModel::limpiar_cadenas($_POST['codigo_proyecto']);
        $numero_documento_profesor = MainModel::limpiar_cadenas($_POST['documento_asesor_actualizar']);



        if (empty($codigo) || empty($numero_documento_profesor)  || empty($codigo_proyecto)) {
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
        $codigo_proyecto =  MainModel::decryption($codigo_proyecto);



        if (!in_array($codigo_proyecto, [1, 2])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Código de proyecto invalido",
                "Texto" => "El código de proyecto o nateproyecto proporcionado no es válido. ",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

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

        $nombre_usuario_profesor =  $usuario['nombre_usuario'] . ' ' . $usuario['apellidos_usuario'];

        $nombre_usuario = $usuario['nombre_usuario'];

        $apellido_usuario = $usuario['apellidos_usuario'];

        $correo_usuario = $usuario['correo_usuario'];

        if (!in_array($id_rol_usuario, [5, 6])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "El usuario " . $nombre_usuario_profesor . " no es un director.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }



        /************ verificar información asesor ************** */


        $consulta_anteproyecto_asesor_externo = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM Asignar_asesor_anteproyecto_proyecto WHERE codigo_proyecto = '$codigo' AND numero_documento = '$numero_documento_profesor'"
        );

        if ($consulta_anteproyecto_asesor_externo->rowCount() > 0) {
            $datos_asignacion = $consulta_anteproyecto_asesor_externo->fetch(PDO::FETCH_ASSOC);

            // Ya tienes todos los datos de la tabla en $datos_asignacion
            $documento_asesor = $datos_asignacion['numero_documento'];

            $consulta_info_usuario = MainModel::ejecutar_consultas_simples(
                "SELECT * FROM usuarios WHERE numero_documento = '$documento_asesor'"
            );

            if ($consulta_info_usuario->rowCount() > 0) {

                $datos_usuario = $consulta_info_usuario->fetch(PDO::FETCH_ASSOC);
            
                $rol_profesor = $datos_usuario['id_rol'];

                if($rol_profesor == 6){
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Acción no permitida",
                        "Texto" => "El director externo no puede ser asignado o modificado como director del proyecto. Por favor, seleccione un director interno válido.",
                        "Tipo" => "error"
                    ];
                    
                    echo json_encode($alerta);
                    exit();
                    
                }
            
            }

        
        }else{

            $consulta_info_usuario = MainModel::ejecutar_consultas_simples(
                "SELECT * FROM usuarios WHERE numero_documento = '$numero_documento_profesor'"
            );

            if ($consulta_info_usuario->rowCount() > 0) {

                $datos_usuario = $consulta_info_usuario->fetch(PDO::FETCH_ASSOC);
            
                $rol_profesor = $datos_usuario['id_rol'];

                if($rol_profesor == 6){
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Acción no permitida",
                        "Texto" => "No se le puede asignar un director externo al proyecto, seleccione un director interno válido.",
                        "Tipo" => "error"
                    ];
                    
                    echo json_encode($alerta);
                    exit();
                    
                }
            
            }


        }




        if ($codigo_proyecto == 1) { //anteproyecto

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

        

            $check_faculta = MainModel::ejecutar_consultas_simples(
                "SELECT 
                ae.codigo_anteproyecto,
                ae.titulo_anteproyecto,
                f.nombre_facultad,
                f.id_facultad,
                p.nombre_programa,
                p.id_programa
            FROM 
                anteproyectos ae
            LEFT JOIN 
                programas_academicos p ON ae.id_programa = p.id_programa
            LEFT JOIN 
                facultades f ON p.id_facultad = f.id_facultad
            WHERE 
                ae.codigo_anteproyecto = '$codigo '"
            );

            if ($check_faculta->rowCount() <= 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El codigo de anteproyecto no tiene programa ni facultada asignado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            $datos_codigo = $check_faculta->fetch(PDO::FETCH_ASSOC);

            $codigo_facultad_proyecto = $datos_codigo['id_facultad'];

            $codigo_programa_proyecto = $datos_codigo['id_programa'];


            /*********************************************************** */

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
                    "Texto" => "El número de documento de " . $nombre_usuario_profesor . " no tiene asignada ninguna facultad y programa.",
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
                    $facultad_programa['id_facultad'] == $codigo_facultad_proyecto &&
                    $facultad_programa['id_programa'] == $codigo_programa_proyecto
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

         

            // Consulta para verificar si el número de documento ya tiene asignado el código de proyecto
            $consulta_asignacion = MainModel::ejecutar_consultas_simples(
                "SELECT * FROM Asignar_asesor_anteproyecto_proyecto WHERE numero_documento = '$numero_documento_profesor' AND codigo_proyecto = '$codigo'"
            );

            if ($consulta_asignacion->rowCount() > 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Asignación existente",
                    "Texto" => "El número de documento de " . $nombre_usuario_profesor . " ya tiene asignado un anteproyecto con el código " . $codigo,
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

           

            $consulta_actualizar_asesor = MainModel::ejecutar_consultas_simples(
                "UPDATE Asignar_asesor_anteproyecto_proyecto 
             SET numero_documento = '$numero_documento_profesor' 
             WHERE codigo_proyecto = '$codigo'"
            );

            if ($consulta_actualizar_asesor->rowCount() > 0) {

                $alerta = [
                    "Alerta" => "Recargar",
                    "Titulo" => "Actualización exitosa",
                    "Texto" => "El Director fue actualizado correctamente",
                    "Tipo" => "success"
                ];
                echo json_encode($alerta);
                exit();
                


            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error al Actualizar Director",
                    "Texto" => "No se pudo actualizar el Director",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        } else if ($codigo_proyecto == 2) {

            $consulta_proyecto = MainModel::ejecutar_consultas_simples(
                "SELECT * FROM proyectos WHERE codigo_proyecto = '$codigo'"
            );

            if ($consulta_proyecto->rowCount() <= 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Código de proyecto no válido",
                    "Texto" => "El código de proyecto es invalido. Por favor, ingrese un valor válido.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            $check_faculta = MainModel::ejecutar_consultas_simples(
                "SELECT 
                ae.codigo_proyecto,
                ae.titulo_proyecto,
                f.nombre_facultad,
                f.id_facultad,
                p.nombre_programa,
                p.id_programa
            FROM 
                proyectos ae
            LEFT JOIN 
                programas_academicos p ON ae.id_programa = p.id_programa
            LEFT JOIN 
                facultades f ON p.id_facultad = f.id_facultad
            WHERE 
                ae.codigo_proyecto = '$codigo '"
            );

            if ($check_faculta->rowCount() <= 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "El codigo de anteproyecto no tiene programa ni facultada asignado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            $datos_codigo = $check_faculta->fetch(PDO::FETCH_ASSOC);

            $codigo_facultad_proyecto = $datos_codigo['id_facultad'];

            $codigo_programa_proyecto = $datos_codigo['id_programa'];


            /*********************************************************** */

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
                    "Texto" => "El número de documento de " . $nombre_usuario_profesor . " no tiene asignada ninguna facultad y programa.",
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
                    $facultad_programa['id_facultad'] == $codigo_facultad_proyecto &&
                    $facultad_programa['id_programa'] == $codigo_programa_proyecto
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

            // Consulta para verificar si el número de documento ya tiene asignado el código de proyecto
            $consulta_asignacion = MainModel::ejecutar_consultas_simples(
                "SELECT * FROM Asignar_asesor_anteproyecto_proyecto WHERE numero_documento = '$numero_documento_profesor' AND codigo_proyecto = '$codigo'"
            );

            if ($consulta_asignacion->rowCount() > 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Asignación existente",
                    "Texto" => "El número de documento de " . $nombre_usuario_profesor . " ya tiene asignado un anteproyecto con el código " . $codigo,
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
  

            $consulta_actualizar_asesor = MainModel::ejecutar_consultas_simples(
                "UPDATE Asignar_asesor_anteproyecto_proyecto 
             SET numero_documento = '$numero_documento_profesor' 
             WHERE codigo_proyecto = '$codigo'"
            );

            if ($consulta_actualizar_asesor->rowCount() > 0) {
                    $alerta = [
                        "Alerta" => "Recargar",
                        "Titulo" => "Actualización exitosa",
                        "Texto" => "El Director fue actualizado correctamente",
                        "Tipo" => "success"
                    ];
                    echo json_encode($alerta);
                    exit();
                
            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error al Actualizar Director",
                    "Texto" => "No se pudo actualizar el Director",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }
    }

    public function cargar_imagenes_portadas()
    {

        $formatosPermitidos = ["image/jpg", "image/jpeg", "image/png", "image/gif"];

        $nombre_archivo = $_FILES['imagenes_portadas']['name'];
        $tipo_archivo = $_FILES['imagenes_portadas']['type'];


        if (empty($_FILES['imagenes_portadas']['name'][0])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Por favor selecciona una o varias imágenes",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        foreach ($_FILES['imagenes_portadas']['name'] as $key => $nombre_archivo) {
            $tipo_archivo = $_FILES['imagenes_portadas']['type'][$key];

            // Validar formato de archivo
            if (!in_array($tipo_archivo, $formatosPermitidos)) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Formato de archivo no válido",
                    "Texto" => "El archivo '$nombre_archivo' no es una imagen válida (JPEG, PNG o GIF).",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }


        $carpetaDestino = "../Views/assets/images/ImagenesPortada/";

        // Verificar si la carpeta existe, si no, crearla
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true); // 0777 otorga todos los permisos, 'true' permite crear subdirectorios
        }

        $imagenesGuardadas = [];

        foreach ($_FILES['imagenes_portadas']['tmp_name'] as $key => $tmp_name) {
            // Obtener la extensión del archivo original
            $extension = pathinfo($_FILES['imagenes_portadas']['name'][$key], PATHINFO_EXTENSION);

            // Generar un nombre único
            $nombreArchivo = uniqid("img_", true) . "." . $extension;

            // Ruta de destino con el nuevo nombre
            $rutaArchivo = $carpetaDestino . $nombreArchivo;

            // Mover el archivo al servidor
            if (move_uploaded_file($tmp_name, $rutaArchivo)) {
                $imagenesGuardadas[] = $nombreArchivo; // Agregar a la lista con el nuevo nombre
            }
        }


        if (!empty($imagenesGuardadas)) {
            $jsonImagenes = json_encode($imagenesGuardadas); // Convertir a JSON
            $estado = 'I'; // Estado por defecto (Inactivas)

            // Insertar en la base de datos
            $consulta = MainModel::ejecutar_consultas_simples(
                "INSERT INTO imagenes_portada (nombre_imagenes, estado) VALUES (' $jsonImagenes', '$estado')"
            );

            if ($consulta->rowCount() >= 1) {
                $alerta = [
                    "Alerta" => "Recargar",
                    "Titulo" => "Buen trabajo",
                    "Texto" => "Imágenes subidas correctamente",
                    "Tipo" => "success"
                ];
                echo json_encode($alerta);
                exit();
            } else {

                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "No se pudieron guardar las imágenes.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        } else {

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudieron guardar las imágenes.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
    }

    public function eliminar_imagenes_portada()
    {

        $codigo = MainModel::limpiar_cadenas($_POST['codigo_id_portada_delete']);


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

        $check_profesor = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM imagenes_portada WHERE id = '$codigo'"
        );
        if ($check_profesor->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El código ingresado no existe en el sistema ",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $imagenes = $check_profesor->fetch(PDO::FETCH_ASSOC);

        $listaImagenes = json_decode($imagenes['nombre_imagenes'], true);

        $directorio = "../Views/assets/images/ImagenesPortada/"; // Ruta de la carpeta donde están las imágenes

        $errorEliminar = false;


        foreach ($listaImagenes as $imagen) {
            $rutaImagen = $directorio . $imagen;
            if (file_exists($rutaImagen)) {
                if (!unlink($rutaImagen)) { // Intentar eliminar la imagen
                    $errorEliminar = true;
                }
            }
        }

        if (!$errorEliminar) {
            $eliminarDB = MainModel::ejecutar_consultas_simples(
                "DELETE FROM imagenes_portada WHERE id = ' $codigo'"
            );

            if ($eliminarDB->rowCount() > 0) {

                $alerta = [
                    "Alerta" => "Recargar",
                    "Titulo" => "Eliminado",
                    "Texto" => "Todas las imágenes fueron eliminadas correctamente.",
                    "Tipo" => "success"
                ];
                echo json_encode($alerta);
                exit();
            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error al eliminar",
                    "Texto" => "Hubo un problema al eliminar los archivos. Inténtalo nuevamente.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }
    }

    public function actualizar_estado_imagenes_portada()
    {

        $codigo = MainModel::limpiar_cadenas($_POST['codigo_id_portada_update']);


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

        $check_profesor = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM imagenes_portada WHERE id = '$codigo'"
        );
        if ($check_profesor->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El código ingresado no existe en el sistema ",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $extraer_estados_imagenes = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM imagenes_portada"
        );

        if ($extraer_estados_imagenes->rowCount() > 0) {

            $imagenes = $extraer_estados_imagenes->fetchAll(PDO::FETCH_ASSOC);

            foreach ($imagenes as $imagen) {

                $id_imagenes = $imagen['id'];

                $nuevoEstado = 'I';

                $actualizar_estado = MainModel::ejecutar_consultas_simples(
                    "UPDATE imagenes_portada SET estado = '$nuevoEstado' WHERE id = '$id_imagenes'"
                );
            }
        }

        // Ahora, actualizar solo la imagen con el ID especificado a "A" (Activo)
        $actualizar_estado_codigo = MainModel::ejecutar_consultas_simples(
            "UPDATE imagenes_portada SET estado = 'A' WHERE id = '$codigo'"
        );

        if ($actualizar_estado_codigo->rowCount() > 0) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Buen trabajo",
                "Texto" => "Se ha actualizado el estado correctamente.",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo actualizar el estado de la imagen con ID: $codigo.",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
        exit();
    }

    public function cargar_firmas_usuarios()
    {

        $formatosPermitidos = ["image/jpg", "image/jpeg", "image/png", "image/gif"];

        $nombre_archivo = $_FILES['firma_digital']['name'];

        $tipo_archivo = $_FILES['firma_digital']['type'];

        $numero_documento = MainModel::limpiar_cadenas($_POST['numero_documento_user_logueado']);

        $numero_documento =  MainModel::decryption($numero_documento);

        if (empty($_FILES['firma_digital']['name'][0])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Por favor selecciona una imagen",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }



        // Validar formato de archivo
        if (!in_array($tipo_archivo, $formatosPermitidos)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Formato de archivo no válido",
                "Texto" => "El archivo '$nombre_archivo' no es una imagen válida (JPEG, PNG o GIF).",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        // Consulta para verificar si el usuario ya tiene una firma
        $consulta_firma = MainModel::ejecutar_consultas_simples(
            "SELECT COUNT(*) AS total FROM firma_digital_usuarios WHERE numero_documento = '$numero_documento'"
        );

        $firma_existente = $consulta_firma->fetch(PDO::FETCH_ASSOC);

        if ($firma_existente['total'] > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error al guardar en la base de datos",
                "Texto" => "El usuario ya tiene una firma registrada.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }


        $carpetaDestino = "../Views/assets/images/FirmasUsuarios/";

        // Verificar si la carpeta existe, si no, crearla
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true); // 0777 otorga todos los permisos, 'true' permite crear subdirectorios
        }

        $extension = pathinfo($_FILES['firma_digital']['name'], PATHINFO_EXTENSION);

        $nombreArchivo = uniqid("firma_", true) . "_" . bin2hex(random_bytes(5)) . "." . $extension;

        $rutaArchivo = $carpetaDestino . $nombreArchivo;

        if (move_uploaded_file($_FILES['firma_digital']['tmp_name'], $rutaArchivo)) {

            $consulta = MainModel::ejecutar_consultas_simples(
                "INSERT INTO firma_digital_usuarios (numero_documento , firma ) VALUES ('$numero_documento','$nombreArchivo')"
            );

            if ($consulta->rowCount() >= 1) {
                $alerta = [
                    "Alerta" => "Recargar",
                    "Titulo" => "Buen trabajo",
                    "Texto" => "Firma subida correctamente",
                    "Tipo" => "success"
                ];
                echo json_encode($alerta);
                exit();
            } else {
                // Error al insertar en la base de datos
                unlink($rutaArchivo); // Eliminar la imagen si la base de datos falla
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error al guardar en la base de datos",
                    "Texto" => "La imagen se subió, pero no se pudo registrar en la base de datos.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        } else {
            // Error al mover la imagen al directorio
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error al subir la imagen",
                "Texto" => "No se pudo mover la imagen al servidor.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
    }

    public function editar_firmas_usuarios()
    {

        $formatosPermitidos = ["image/jpg", "image/jpeg", "image/png", "image/gif"];

        // Verificar si el usuario ha subido una nueva firma
        if (!isset($_FILES['firma_digital_upd']) || $_FILES['firma_digital_upd']['error'] !== 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Por favor selecciona una imagen válida",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $numero_documento = MainModel::limpiar_cadenas($_POST['numero_documento_user_logueado']);
        $numero_documento = MainModel::decryption($numero_documento);

        $nombre_archivo = $_FILES['firma_digital_upd']['name'];
        $tipo_archivo = $_FILES['firma_digital_upd']['type'];

        // Validar formato de archivo
        if (!in_array($tipo_archivo, $formatosPermitidos)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Formato de archivo no válido",
                "Texto" => "El archivo '$nombre_archivo' no es una imagen válida (JPEG, PNG o GIF).",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $carpetaDestino = "../Views/assets/images/FimasUsuarios/";


        // **1️⃣ Extraer la firma actual del usuario**
        $consulta_firma = MainModel::ejecutar_consultas_simples(
            "SELECT firma FROM firma_digital_usuarios WHERE numero_documento = '$numero_documento'"
        );
        $firma_actual = $consulta_firma->fetch(PDO::FETCH_ASSOC);

        $firma_usuario_registrada = $firma_actual['firma'];



        $rutaImagenAntigua = $carpetaDestino . $firma_usuario_registrada;

        if (file_exists($rutaImagenAntigua)) {
            unlink($rutaImagenAntigua); // Eliminar la imagen del servidor
        }


        // **2️⃣ Generar nombre único de la imagen**
        $extension = pathinfo($_FILES['firma_digital_upd']['name'], PATHINFO_EXTENSION);
        $nuevaFirma = uniqid("firma_", true) . "_" . bin2hex(random_bytes(5)) . "." . $extension;
        $rutaArchivo = $carpetaDestino . $nuevaFirma;

        // **3️⃣ Mover la nueva firma al directorio**
        if (move_uploaded_file($_FILES['firma_digital_upd']['tmp_name'], $rutaArchivo)) {
            // **4️⃣ Verificar que el archivo se guardó correctamente**
            if (!file_exists($rutaArchivo)) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error de almacenamiento",
                    "Texto" => "El archivo no se guardó correctamente en el servidor.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            // **5️⃣ Actualizar la base de datos con la nueva firma**
            $consulta_update = MainModel::ejecutar_consultas_simples(
                "UPDATE firma_digital_usuarios SET firma = '$nuevaFirma' WHERE numero_documento = '$numero_documento'"
            );

            if ($consulta_update->rowCount() > 0) {
                $alerta = [
                    "Alerta" => "Recargar",
                    "Titulo" => "Firma actualizada",
                    "Texto" => "Tu firma ha sido actualizada correctamente.",
                    "Tipo" => "success"
                ];
                echo json_encode($alerta);
                exit();
            } else {
                // Si la actualización en la base de datos falla, eliminar la nueva imagen subida
                unlink($rutaArchivo);
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error al actualizar",
                    "Texto" => "No se pudo actualizar la firma en la base de datos.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        } else {
            // Error al mover la imagen al servidor
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error al subir la imagen",
                "Texto" => "No se pudo mover la nueva firma al servidor.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
    }

    public function registrar_registros_calificados_controlador()
    {

        $tipo_registro_calificado_programa = MainModel::limpiar_cadenas($_POST['registro_calificado_programa']);
        $name_regisro_calificado = MainModel::limpiar_cadenas($_POST['name_regisro_calificado']);


        if (empty($name_regisro_calificado) || empty($tipo_registro_calificado_programa)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios  ",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }



        $tipo_registro_calificado_programa = (int) MainModel::decryption($tipo_registro_calificado_programa);



        $check_tipo_programa_usuario = MainModel::ejecutar_consultas_simples("SELECT id_programa FROM programas_academicos 
        WHERE id_programa = '$tipo_registro_calificado_programa'");

        if ($check_tipo_programa_usuario->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El código del programa que intentas ingresar no se encuentra registrados",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }



        $check_programa = MainModel::ejecutar_consultas_simples(
            "SELECT * FROM registros_calificados_programas WHERE id_programa  = '$tipo_registro_calificado_programa'"
        );

        if ($check_programa->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "Ya existe un registro calificado registrado para este programa",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }



        $datos_registrar = [
            "id_programa" => $tipo_registro_calificado_programa,
            "nombre_registro" => $name_regisro_calificado // Eliminar el espacio extra
        ];


        $regitrar_programa = UsuarioModelo::Registrar_registros_calificados_modelo($datos_registrar);

        if ($regitrar_programa->rowCount() >= 1) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Registro exitoso ",
                "Texto" => "Los datos se han guardado correctamente.",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo registrar la información. Verifica los datos e intenta nuevamente.",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
        exit();
    }

    public function eliminar_registro_calificado_controlador()
    {

        $id_registro = MainModel::limpiar_cadenas($_POST['id_registro_calificado']);
        $id_programa = MainModel::limpiar_cadenas($_POST['id_programa_registro_calificado']);


        if (empty($id_registro) || empty($id_programa)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }




        $id_registro = (int) MainModel::decryption($id_registro);
        $check_tipo_faculta_usuario = MainModel::ejecutar_consultas_simples("SELECT id FROM registros_calificados_programas 
    WHERE id = '$id_registro'");

        if ($check_tipo_faculta_usuario->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El código de registro calificado que intentas ingresar no se encuentra registrado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }




        $id_programa = (int) MainModel::decryption($id_programa);
        $check_tipo_programa_usuario = MainModel::ejecutar_consultas_simples("SELECT id_programa FROM programas_academicos 
    WHERE id_programa = '$id_programa'");

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


        $eliminar_registro_calificado = MainModel::ejecutar_consultas_simples("DELETE FROM registros_calificados_programas WHERE id = '$id_registro'");

        if ($eliminar_registro_calificado->rowCount() >= 1) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Registro Eliminado",
                "Texto" => "Se elimino correctamente el registro calificado",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo eliminar el registro calificado",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
        exit();
    }
public function actualizar_registro_calificado_controlador()
    {

        $id_registro = MainModel::limpiar_cadenas($_POST['id_registro_calificado_upd']);
        $id_programa = MainModel::limpiar_cadenas($_POST['id_programa_registro_calificado_upd']);
        $texto = MainModel::limpiar_cadenas($_POST['nombre_registro']);


        if (empty($id_registro) || empty($id_programa) || empty($texto)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Todos los campos son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }




        $id_registro = (int) MainModel::decryption($id_registro);
        $check_tipo_faculta_usuario = MainModel::ejecutar_consultas_simples("SELECT id FROM registros_calificados_programas 
        WHERE id = '$id_registro'");

        if ($check_tipo_faculta_usuario->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "El código de registro calificado que intentas ingresar no se encuentra registrado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }




        $id_programa = (int) MainModel::decryption($id_programa);
        $check_tipo_programa_usuario = MainModel::ejecutar_consultas_simples("SELECT id_programa FROM programas_academicos 
        WHERE id_programa = '$id_programa'");

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


        // Preparar la consulta para actualizar el nombre
        $actualizar_registro_calificado = MainModel::ejecutar_consultas_simples("
        UPDATE registros_calificados_programas 
        SET nombre_registro = '$texto' 
        WHERE id = '$id_registro' AND id_programa = '$id_programa'
        ");

        // Verificar si la actualización fue exitosa
        if ($actualizar_registro_calificado->rowCount() >= 1) {
            $alerta = [
                "Alerta" => "Recargar",
                "Titulo" => "Registro Actualizado",
                "Texto" => "El nombre del registro se actualizó correctamente.",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se pudo actualizar el registro.",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
        exit();
    }
}//*** cierre clase */
