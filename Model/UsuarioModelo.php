<?php

require_once "MainModel.php";

Class UsuarioModelo extends MainModel {

    /****************Modelo Para Agregar los usuarios ***********************/

    protected static function Agregar_usuarios_modelo($data) {
        $consulta = MainModel::conectar()->prepare(
            "INSERT INTO usuarios (
                numero_documento, nombre_usuario, apellidos_usuario, correo_usuario, 
                telefono_usuario, id_rol, contrasena_usuario, estado, 
                imagen_usuario, created_at
            ) 
            VALUES (
                :numero_documento, :nombre_usuario, :apellidos_usuario, :correo_usuario, 
                :telefono_usuario, :id_rol, :contrasena_usuario, :estado, 
                :imagen, :created_at)"
        );
    
        $consulta->bindParam(':numero_documento', $data['numero_documento']);
        $consulta->bindParam(':nombre_usuario', $data['nombre_usuario']);
        $consulta->bindParam(':apellidos_usuario', $data['apellido_usuario']);
        $consulta->bindParam(':correo_usuario', $data['correo_usuario']);
        $consulta->bindParam(':telefono_usuario', $data['telefono_usuario']);
        $consulta->bindParam(':id_rol', $data['tipo_usuario']);
        $consulta->bindParam(':contrasena_usuario', $data['password_usuario']);
        $consulta->bindParam(':estado', $data['estado_usuario']);
        $consulta->bindParam(':imagen', $data['imagen_usuario']);
       

    
        // Añadir created_at con la fecha actual
        $fecha_actual = date("Y-m-d H:i:s");
        $consulta->bindParam(':created_at', $fecha_actual);
    
        // Ejecutar la consulta
        $consulta->execute();
    
        return $consulta;
    }
     /****************Modelo Para editar un usuario ***********************/

     protected static function Editar_usuarios_modelo($data) {
        $consulta = MainModel::conectar()->prepare(
            "UPDATE usuarios SET
                numero_documento = :numero_documento,
                nombre_usuario = :nombre_usuario, 
                apellidos_usuario = :apellidos_usuario,
                correo_usuario = :correo_usuario, 
                telefono_usuario = :telefono_usuario,
                id_rol = :id_rol, 
                contrasena_usuario = :contrasena_usuario, 
                estado = :estado
            WHERE id = :id_usuario"
        );
    
        // Vincular los parámetros con los datos proporcionados
        $consulta->bindParam(':numero_documento', $data['numero_documento']);
        $consulta->bindParam(':nombre_usuario', $data['nombre_usuario']);
        $consulta->bindParam(':apellidos_usuario', $data['apellido_usuario']);
        $consulta->bindParam(':correo_usuario', $data['correo_usuario']);
        $consulta->bindParam(':telefono_usuario', $data['telefono_usuario']);
        $consulta->bindParam(':id_rol', $data['tipo_usuario']);
        $consulta->bindParam(':contrasena_usuario', $data['password_usuario']);
        $consulta->bindParam(':estado', $data['estado_usuario']);
        $consulta->bindParam(':id_usuario', $data['id_usuario'], PDO::PARAM_INT);
    
        // Ejecutar la consulta
        $consulta->execute();
    
        return $consulta;

    }
     /****************Modelo Para actualizar contraseña de un usuario ***********************/
     protected static function Actualizar_contraseña_user($data) {
        try {
            $conexion = MainModel::conectar();
    
            // Actualizar la contraseña de todos los usuarios con el mismo número de documento
            $consulta = $conexion->prepare(
                "UPDATE usuarios SET contrasena_usuario = :password_usuario WHERE numero_documento = :numero_documento"
            );
            $consulta->bindParam(':password_usuario', $data['password_usuario']);
            $consulta->bindParam(':numero_documento', $data['numero_documento'], PDO::PARAM_INT);
            $consulta->execute();
    
            // Verificar si se afectaron filas
            if ($consulta->rowCount() > 0) {
                // Si se afectaron filas, la contraseña fue actualizada correctamente
                return  $consulta;
            } else {
                // Si no se afectaron filas, puede ser que el número de documento no exista
                return  $consulta;
            }
        } catch (PDOException $e) {
            // Registrar el error
            error_log("Error al actualizar la contraseña: " . $e->getMessage());
            return false;
        }
    }
    
  
    /*****************************************************************/

    protected static function Agregar_usuarios_facultades_modelo($data) {

        $consulta = MainModel::conectar()->prepare(
            "INSERT INTO Asignar_usuario_facultades (
                numero_documento, 
                id_facultad, 
                id_programa
            ) 
            VALUES (
                :numero_documento, 
                :id_facultad, 
                :id_programa
            )"
        );
        
        // Asignar los valores a los parámetros de la consulta
        $consulta->bindParam(':numero_documento', $data['numero_documento']);
        $consulta->bindParam(':id_facultad', $data['tipo_faculta']);
        $consulta->bindParam(':id_programa', $data['tipo_programa']);

        // Ejecutar la consulta
        $consulta->execute();
    
        return $consulta;
    }
 
    
    /****************Modelo Para eliminar un usuario ***********************/

    protected static function Eliminar_usuarios_modelo($numero_documento) {
        $consulta = MainModel::conectar()->prepare("DELETE FROM usuarios WHERE numero_documento = :numero_documento");
        
        $consulta->bindParam(':numero_documento', $numero_documento);
    
        $consulta->execute();
        
        return $consulta;
    }

        /****************Modelo Para eliminar asignacion usuarios faculta programas ***********************/

    protected static function Eliminar_asignacion_usuarios_faculta_modelo($documentoFPuser_del, $idFacultad_del, $idPrograma_del) {
        try {
            // Conexión a la base de datos
            $conexion = MainModel::conectar();
    
            // Preparar la consulta para eliminar la asignación del usuario a la facultad y programa
            $consulta = $conexion->prepare("
                DELETE FROM Asignar_usuario_facultades 
                WHERE numero_documento = :documento 
                AND id_facultad = :id_facultad 
                AND id_programa = :id_programa
            ");
    
            // Vincular los parámetros con la consulta preparada
            $consulta->bindParam(':documento', $documentoFPuser_del, PDO::PARAM_STR);
            $consulta->bindParam(':id_facultad', $idFacultad_del, PDO::PARAM_INT);
            $consulta->bindParam(':id_programa', $idPrograma_del, PDO::PARAM_INT);
    
            // Ejecutar la consulta y verificar si se eliminó correctamente
            if ($consulta->execute()) {
                return $consulta; // Retornar el resultado de la ejecución si fue exitoso
            } else {
                return false; // Retornar false si hubo algún error
            }
        } catch (Exception $e) {
            // En caso de error, capturar la excepción y retornar el mensaje de error
            return "error: " . $e->getMessage();
        }
    }
    

    protected static function CreartokenCorreoUSuarios($id) {
        // Generar un token único
        $token = bin2hex(random_bytes(16)); // Un token aleatorio de 32 caracteres

        $fecha_creacion = date("Y-m-d H:i:s");

        // Insertar en la base de datos
        $sql = "INSERT INTO recuperacion_contrasena (id_usuario, token, fecha_creacion) VALUES (:id_usuario, :token, :fecha_creacion)";
        $consulta = MainModel::conectar()->prepare($sql);
        $consulta->bindParam(':id_usuario', $id);
        $consulta->bindParam(':token', $token);
        $consulta->bindParam(':fecha_creacion', $fecha_creacion);
        $consulta->execute();

        return $consulta;
    }

    protected static function actualizar_informacion_usuario_modelo($datos) {
        // Preparar la consulta SQL para actualizar la información del usuario
        $consulta = MainModel::conectar()->prepare("
            UPDATE usuarios 
            SET nombre_usuario = :nombre, 
                apellidos_usuario = :apellidos, 
                correo_usuario = :correo, 
                contrasena_usuario = :contrasena, 
                imagen_usuario = :imagen 
            WHERE numero_documento = :numero_documento
        ");
    
        // Vincular los parámetros a la consulta
        $consulta->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $consulta->bindParam(':apellidos', $datos['apellido'], PDO::PARAM_STR);
        $consulta->bindParam(':correo', $datos['email'], PDO::PARAM_STR);
        $consulta->bindParam(':contrasena', $datos['contrasena_usuario'], PDO::PARAM_STR);
        $consulta->bindParam(':imagen', $datos['imagenes'], PDO::PARAM_STR);
        $consulta->bindParam(':numero_documento', $datos['numero_documento'], PDO::PARAM_STR);
    
        // Ejecutar la consulta y verificar si se actualizó correctamente
        if ($consulta->execute()) {
            return $consulta; // Devolver el resultado de la ejecución si fue exitosa
        } else {
            return false; // Devolver false si hubo algún error
        }
    }

    protected static function Registrar_facultad_modelo($nombre_facultad) {
        $consulta = MainModel::conectar()->prepare("INSERT INTO facultades (nombre_facultad) VALUES (:nombre_facultad)");
        
        $consulta->bindParam(':nombre_facultad', $nombre_facultad);
        
        $consulta->execute();
        
        return $consulta;
    }

    protected static function Registrar_modalidad_modelo($nombre_modalidad) {
        try {
            $conexion = MainModel::conectar();
            $consulta = $conexion->prepare("INSERT INTO modalidad_grados (nombre_modalidad) VALUES (:nombre_modalidad)");
    
            // Corregimos el parámetro sin espacio extra
            $consulta->bindParam(':nombre_modalidad', $nombre_modalidad, PDO::PARAM_STR);
    
            // Ejecutamos la consulta
            if ($consulta->execute()) {
                return $conexion->lastInsertId(); // Retornamos el ID insertado si fue exitoso
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "error: " . $e->getMessage(); // Capturar errores
        }
    }
    
    
    protected static function Eliminar_facultad_modelo($id_facultas) {
        $consulta = MainModel::conectar()->prepare("DELETE FROM facultades WHERE id_facultad = :id_facultad");
        
        $consulta->bindParam(':id_facultad', $id_facultas);
    
        $consulta->execute();
        
        return $consulta;
    }

    protected static function Actualizar_facultad_modelo($id_facultad, $nombre_facultad) {
        try {
            // Conectar a la base de datos
            $consulta = MainModel::conectar()->prepare("
                UPDATE facultades 
                SET nombre_facultad = :nombre_facultad 
                WHERE id_facultad = :id_facultad
            ");
            
            // Vincular parámetros
            $consulta->bindParam(':id_facultad', $id_facultad, PDO::PARAM_INT);
            $consulta->bindParam(':nombre_facultad', $nombre_facultad, PDO::PARAM_STR);
    
            // Ejecutar consulta
            $consulta->execute();
    
            return $consulta;
        } catch (PDOException $e) {
            return false; // Si hay un error, devuelve false
        }
    }
    

    
    protected static function Actualizar_programa_modelo($id_programa, $nombre_programa) {
        try {
            // Conectar a la base de datos
            $consulta = MainModel::conectar()->prepare("
                UPDATE programas_academicos 
                SET nombre_programa = :nombre_programa 
                WHERE id_programa = :id_programa
            ");
            
            // Vincular parámetros
            $consulta->bindParam(':id_programa', $id_programa, PDO::PARAM_INT);
            $consulta->bindParam(':nombre_programa', $nombre_programa, PDO::PARAM_STR);
    
            // Ejecutar consulta
            $consulta->execute();
    
            return $consulta;
        } catch (PDOException $e) {
            return false; // Si hay un error, devuelve false
        }
    }

    protected static function Actualizar_modalidad_modelo($id_modalidad, $nombre_modalidad) {
        try {
            // Conectar a la base de datos
            $consulta = MainModel::conectar()->prepare("
                UPDATE modalidad_grados 
                SET nombre_modalidad = :nombre_modalidad 
                WHERE id_modalidad = :id_modalidad
            ");
            
            // Vincular parámetros
            $consulta->bindParam(':id_modalidad', $id_modalidad, PDO::PARAM_INT);
            $consulta->bindParam(':nombre_modalidad', $nombre_modalidad, PDO::PARAM_STR);
    
            // Ejecutar consulta
            $consulta->execute();
    
            return $consulta;
        } catch (PDOException $e) {
            return false; // Si hay un error, devuelve false
        }
    }
    

    protected static function Eliminar_modalidad_modelo($id_modalidad) {
        try {
            $conexion = MainModel::conectar();
            $consulta = $conexion->prepare("DELETE FROM modalidad_grados WHERE id_modalidad = :id_modalidad");
    
            // Enlazar el parámetro correctamente
            $consulta->bindParam(':id_modalidad', $id_modalidad, PDO::PARAM_INT);
    
            // Ejecutar la consulta
            if ($consulta->execute()) {
                return $consulta->rowCount(); // Devuelve el número de filas eliminadas
            } else {
                return false; // Retorna false si hubo algún error
            }
        } catch (PDOException $e) {
            return "error: " . $e->getMessage(); // Capturar errores de SQL
        }
    }
    



    protected static function Registrar_programas_modelo($data) {

        $consulta = MainModel::conectar()->prepare(
            "INSERT INTO programas_academicos ( 
                id_facultad, 
                nombre_programa
            ) 
            VALUES (
                :id_facultad, 
                :nombre_programa
            )"
        );
        
        // Asignar los valores a los parámetros de la consulta
        $consulta->bindParam(':id_facultad', $data['id_facultad']);
        $consulta->bindParam(':nombre_programa', $data['nombre_programa']);

        // Ejecutar la consulta
        $consulta->execute();
    
        return $consulta;
    }

    protected static function Eliminar_programa_academico_modelo($data) {
        try {
            // Conexión a la base de datos
            $conexion = MainModel::conectar();
    
            // Preparar la consulta para eliminar el programa académico basado en id_facultad y id_programa
            $consulta = $conexion->prepare("
                DELETE FROM programas_academicos 
                WHERE id_facultad = :id_facultad 
                AND id_programa = :id_programa
            ");
    
            // Vincular los parámetros con la consulta preparada
            $consulta->bindParam(':id_facultad', $data['id_facultad'], PDO::PARAM_INT);
            $consulta->bindParam(':id_programa', $data['id_programa'], PDO::PARAM_INT);
    
            // Ejecutar la consulta y verificar si se eliminó correctamente
            if ($consulta->execute()) {
                return $consulta; // Retornar el resultado de la ejecución si fue exitoso
            } else {
                return false; // Retornar false si hubo algún error
            }
        } catch (Exception $e) {
            // En caso de error, capturar la excepción y retornar el mensaje de error
            return "error: " . $e->getMessage();
        }
    }

   
    

    protected static function Registrar_configuracion_aplicacion_modelo($data) {
        try {
            // Conexión a la base de datos
            $conexion = MainModel::conectar();
    
            // Preparar la consulta para insertar los datos
            $consulta = $conexion->prepare("
                INSERT INTO configuracion_aplicacion (
                    numero_estudiantes_proyectos, 
                    numero_jurados_proyectos, 
                    nombre_logo
                ) VALUES (
                    :numero_estudiantes, 
                    :numero_jurados_proyectos, 
                    :nombre_logo
                )
            ");
    
            // Asignar los valores a los parámetros
            $consulta->bindParam(':numero_estudiantes', $data['numero_estudiantes'], PDO::PARAM_INT);
            $consulta->bindParam(':numero_jurados_proyectos', $data['numero_jurados_proyectos'], PDO::PARAM_INT);
            $consulta->bindParam(':nombre_logo', $data['nombre_logo'], PDO::PARAM_STR);
    
            // Ejecutar la consulta y verificar si se insertó correctamente
            if ($consulta->execute()) {
                return $conexion->lastInsertId();;
            } else {
                return false; // Retorna false si hubo algún error
            }
        } catch (Exception $e) {
            // Capturar la excepción y retornar el mensaje de error en caso de fallo
            return "error: " . $e->getMessage();
        }
    }


    protected static function Actualizar_configuracion_aplicacion_modelo($data) {
        try {
            // Conexión a la base de datos
            $conexion = MainModel::conectar();
    
            // Preparar la consulta para actualizar los datos
            $consulta = $conexion->prepare("
                UPDATE configuracion_aplicacion 
                SET 
                    numero_estudiantes_proyectos = :numero_estudiantes, 
                    numero_jurados_proyectos = :numero_jurados_proyectos, 
                    nombre_logo = :nombre_logo
                WHERE consecutivo = :consecutivo
            ");
    
            // Asignar los valores a los parámetros
            $consulta->bindParam(':numero_estudiantes', $data['numero_estudiantes'], PDO::PARAM_INT);
            $consulta->bindParam(':numero_jurados_proyectos', $data['numero_jurados_proyectos'], PDO::PARAM_INT);
            $consulta->bindParam(':nombre_logo', $data['nombre_logo'], PDO::PARAM_STR);
            $consulta->bindParam(':consecutivo', $data['consecutivo'], PDO::PARAM_INT); // Se requiere el ID para actualizar
    
            // Ejecutar la consulta y verificar si se actualizó correctamente
            if ($consulta->execute()) {
                return $consulta->rowCount(); // Devuelve el número de filas afectadas
            } else {
                return false; // Retorna false si hubo algún error
            }
        } catch (Exception $e) {
            // Capturar la excepción y retornar el mensaje de error en caso de fallo
            return "error: " . $e->getMessage();
        }
    }
    
    protected static function Registrar_registros_calificados_modelo($data) {
        $consulta = MainModel::conectar()->prepare(
            "INSERT INTO registros_calificados_programas ( 
                id_programa, 
                nombre_registro, 
                fecha_creacion
            ) 
            VALUES (
                :id_programa, 
                :nombre_registro, 
                NOW()
            )"
        );
    
        // Asignar los valores a los parámetros de la consulta
        $consulta->bindParam(':id_programa', $data['id_programa']);
        $consulta->bindParam(':nombre_registro', $data['nombre_registro']);
    
        // Ejecutar la consulta
        $consulta->execute();
    
        return $consulta;
    }
    
    
    

    
    
}