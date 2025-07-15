<?php

require_once "MainModel.php";

Class AnteproyectoModelo extends MainModel {

    // Modelo para agregar las ideas de los anteproyectos

    protected static function agregar_anteproyecto_modelo($datos) {
        try {
            // Preparamos la consulta de inserción
            $consulta = MainModel::conectar()->prepare(
                "INSERT INTO anteproyectos (
                    codigo_anteproyecto, titulo_anteproyecto, palabras_claves,id_facultad, id_programa, fecha_creacion, modalidad
                ) VALUES (
                    :codigo_anteproyecto, :titulo_anteproyecto, :palabras_claves,:id_facultad, :id_programa, NOW(), :modalidad
                )"
            );
    
            // Asignamos los valores de los parámetros
            $consulta->bindParam(':codigo_anteproyecto', $datos['codigo_anteproyecto'], PDO::PARAM_STR);
            $consulta->bindParam(':titulo_anteproyecto', $datos['titulo_anteproyecto'], PDO::PARAM_STR);
            $consulta->bindParam(':palabras_claves', $datos['palabras_claves_anteproyecto'], PDO::PARAM_STR);
            $consulta->bindParam(':id_facultad', $datos['id_facultad'], PDO::PARAM_INT);
            $consulta->bindParam(':id_programa', $datos['id_programa'], PDO::PARAM_INT);
            $consulta->bindParam(':modalidad', $datos['modalidad'], PDO::PARAM_INT);
    
            // Ejecutamos la consulta
            if ($consulta->execute()) {
                return $consulta; // Devolvemos la consulta en caso de éxito
            } else {
                return false; // Devolvemos false en caso de error al ejecutar
            }
        } catch (PDOException $e) {
            // Si ocurre un error, lo registramos y devolvemos false
            error_log("Error al agregar anteproyecto: " . $e->getMessage());
            echo "Error: " . $e->getMessage(); // Imprimir error para verificar
            return false;
        }
    }
    
    
    protected static function asignar_estudiante_proyecto_modelo($datos) {
        // Preparar la consulta SQL para insertar los datos en la tabla asignar_estudiante_anteproyecto
        $consulta = MainModel::conectar()->prepare("
            INSERT INTO asignar_estudiante_anteproyecto (codigo_anteproyecto, numero_documento, fecha_creacion)
            VALUES (:codigo_anteproyecto, :numero_documento, NOW())
        ");
    
        // Vincular los parámetros a la consulta
        $consulta->bindParam(':codigo_anteproyecto', $datos['codigo_anteproyecto'], PDO::PARAM_STR);
        $consulta->bindParam(':numero_documento', $datos['numero_documento'], PDO::PARAM_STR);
    
        // Ejecutar la consulta y verificar si se insertó correctamente
        if($consulta->execute()) {
            return $consulta; // Devolver el resultado de la ejecución si fue exitosa
        } else {
            return false; // Devolver false si hubo algún error
        }
    }

    protected static function asignar_horas_profesor_modelos($datos) {
        // Preparar la consulta SQL para insertar los datos en la tabla asignar_estudiante_anteproyecto
        $consulta = MainModel::conectar()->prepare("
            INSERT INTO asignar_horas_profesor (numero_hora, numero_documento, fecha_creacion)
            VALUES (:numero_horas, :numero_documento, NOW())
        ");
    
        // Vincular los parámetros a la consulta
        $consulta->bindParam(':numero_documento', $datos['numero_documento'], PDO::PARAM_STR);
        $consulta->bindParam(':numero_horas', $datos['numero_horas'], PDO::PARAM_STR);
    
        // Ejecutar la consulta y verificar si se insertó correctamente
        if($consulta->execute()) {
            return $consulta; // Devolver el resultado de la ejecución si fue exitosa
        } else {
            return false; // Devolver false si hubo algún error
        }
    }

    protected static function asignar_horas_jurados_profesor_modelos($datos) {
        // Preparar la consulta SQL para insertar los datos en la tabla asignar_estudiante_anteproyecto
        $consulta = MainModel::conectar()->prepare("
            INSERT INTO asignar_horas_jurado_profesor (numero_hora, numero_documento, fecha_creacion)
            VALUES (:numero_horas, :numero_documento, NOW())
        ");
    
        // Vincular los parámetros a la consulta
        $consulta->bindParam(':numero_documento', $datos['numero_documento'], PDO::PARAM_STR);
        $consulta->bindParam(':numero_horas', $datos['numero_horas'], PDO::PARAM_STR);
    
        // Ejecutar la consulta y verificar si se insertó correctamente
        if($consulta->execute()) {
            return $consulta; // Devolver el resultado de la ejecución si fue exitosa
        } else {
            return false; // Devolver false si hubo algún error
        }
    }
    protected static function eliminar_estudiante_ideas_modelos($codigo_anteproyecto, $numero_documento) {
        // Preparar la consulta SQL para eliminar el registro de la tabla asignar_estudiante_anteproyecto
        $consulta = MainModel::conectar()->prepare("
            DELETE FROM asignar_estudiante_anteproyecto 
            WHERE codigo_anteproyecto = :codigo_anteproyecto 
            AND numero_documento = :numero_documento
        ");
    
        // Vincular los parámetros a la consulta
        $consulta->bindParam(':codigo_anteproyecto', $codigo_anteproyecto, PDO::PARAM_STR);
        $consulta->bindParam(':numero_documento', $numero_documento, PDO::PARAM_STR);
    
        // Ejecutar la consulta y verificar si se eliminó correctamente
        if ($consulta->execute()) {
            return $consulta; // Devolver el resultado de la ejecución si fue exitosa
        } else {
            return false; // Devolver false si hubo algún error
        }
    }

    protected static function actualizar_idea_modelos($datos) {
        // Preparar la consulta SQL para actualizar el título y las palabras claves
        $consulta = MainModel::conectar()->prepare("
            UPDATE anteproyectos 
            SET titulo_anteproyecto = :titulo_idea, 
                palabras_claves = :palabras_claves 
            WHERE codigo_anteproyecto = :codigo_anteproyecto
        ");
    
        // Vincular los parámetros a la consulta
        $consulta->bindParam(':titulo_idea', $datos['titulo_idea'], PDO::PARAM_STR);
        $consulta->bindParam(':palabras_claves', $datos['palabras_claves'], PDO::PARAM_STR);
        $consulta->bindParam(':codigo_anteproyecto', $datos['codigo_anteproyecto'], PDO::PARAM_STR);
    
        // Ejecutar la consulta y verificar si se actualizó correctamente
        if ($consulta->execute()) {
            return $consulta; // Devolver el resultado de la ejecución si fue exitosa
        } else {
            return false; // Devolver false si hubo algún error
        }
    }

    protected static function eliminar_idea_modelos($codigo) {
        try {
            // Conectar a la base de datos
            $conexion = MainModel::conectar();
    
            // Iniciar la transacción
            $conexion->beginTransaction();
    
            // 1. Eliminar registros de la tabla `asignar_estudiante_anteproyecto` que correspondan al código del anteproyecto
            $consultaAsignacion = $conexion->prepare("
                DELETE FROM asignar_estudiante_anteproyecto 
                WHERE codigo_anteproyecto = :codigo_anteproyecto
            ");
            $consultaAsignacion->bindParam(':codigo_anteproyecto', $codigo, PDO::PARAM_STR);
    
            // Ejecutar la eliminación en la tabla de asignaciones
            $consultaAsignacion->execute();
    
            // 2. Eliminar el registro de la tabla `anteproyectos`
            $consultaAnteproyecto = $conexion->prepare("
                DELETE FROM anteproyectos 
                WHERE codigo_anteproyecto = :codigo_anteproyecto
            ");
            $consultaAnteproyecto->bindParam(':codigo_anteproyecto', $codigo, PDO::PARAM_STR);
    
            // Ejecutar la eliminación en la tabla de anteproyectos
            if ($consultaAnteproyecto->execute()) {
                // Confirmar la transacción si ambas eliminaciones son exitosas
                $conexion->commit();
                return $consultaAnteproyecto; // Retornar la consulta si todo se ejecutó correctamente
            } else {
                // Si la eliminación en la tabla `anteproyectos` falla, revertir la transacción
                $conexion->rollBack();
                return false;
            }
        } catch (Exception $e) {
            // Capturar cualquier excepción y revertir la transacción
            $conexion->rollBack();
            return "error: " . $e->getMessage();
        }
    }
    
    protected static function cargar_documento_anteproyecto_modelo($datos) {
        // Preparar la consulta SQL para insertar el documento en la tabla
        $consulta = MainModel::conectar()->prepare("
            INSERT INTO cargar_documento_anteproyectos (codigo_anteproyecto, numero_documento, documento,nombre_archivo_word, estado, fecha_creacion)
            VALUES (:codigo_anteproyecto, :numero_documento, :documento, :nombre_archivo_word, :estado, :fecha_creacion)
        ");
    
        // Vincular los parámetros a la consulta
        $consulta->bindParam(':codigo_anteproyecto', $datos['codigo_anteproyecto'], PDO::PARAM_STR);
        $consulta->bindParam(':numero_documento', $datos['numero_documento'], PDO::PARAM_STR);
        $consulta->bindParam(':documento', $datos['nombre_archivo'], PDO::PARAM_STR);
        $consulta->bindParam(':nombre_archivo_word', $datos['nombre_archivo_word'], PDO::PARAM_STR);
        $consulta->bindParam(':estado', $datos['estado'], PDO::PARAM_STR);
        $consulta->bindParam(':fecha_creacion', $datos['fecha_creacion'], PDO::PARAM_STR);
    
        // Ejecutar la consulta y verificar si se insertó correctamente
        if ($consulta->execute()) {
            return $consulta; // Devolver el resultado de la ejecución si fue exitosa
        } else {
            return false; // Devolver false si hubo algún error
        }
    }


    protected static function cargar_evidencia_reunion_modelo($datos) {
        // Preparar la consulta SQL para insertar la evidencia en la tabla
        $consulta = MainModel::conectar()->prepare("
            INSERT INTO evidencia_reuniones_anteproyectos (codigo_anteproyecto, numero_documento, imagenes, fecha_creacion)
            VALUES (:codigo_anteproyecto, :numero_documento, :imagenes, :fecha_creacion)
        ");
    
        // Vincular los parámetros a la consulta
        $consulta->bindParam(':codigo_anteproyecto', $datos['codigo_anteproyecto'], PDO::PARAM_STR);
        $consulta->bindParam(':numero_documento', $datos['numero_documento'], PDO::PARAM_STR);
        $consulta->bindParam(':imagenes', $datos['imagenes'], PDO::PARAM_STR);
        $consulta->bindParam(':fecha_creacion', $datos['fecha_creacion'], PDO::PARAM_STR);
    
        // Ejecutar la consulta y verificar si se insertó correctamente
        if ($consulta->execute()) {
            return $consulta; // Devolver el resultado de la ejecución si fue exitosa
        } else {
            return false; // Devolver false si hubo algún error
        }
    }
    
    
    protected static function guardar_retroalimentacion_anteproyecto_modelo($datos) {
        try {
            $conexion = MainModel::conectar();
    
            // Primero actualizar el estado del documento
            $consulta_actualizar_estado = $conexion->prepare("
                UPDATE cargar_documento_anteproyectos 
                SET estado = :estadodocumento 
                WHERE id = :id
            ");
    
            $consulta_actualizar_estado->bindParam(':estadodocumento', $datos['estado_revision'], PDO::PARAM_STR);
            $consulta_actualizar_estado->bindParam(':id', $datos['id_documento_cargado'], PDO::PARAM_INT);
    
            if ($consulta_actualizar_estado->execute()) {
                // Luego insertar la retroalimentación en la tabla retroalimentacion_anteproyecto
                $consulta_insertar_retroalimentacion = $conexion->prepare("
                    INSERT INTO retroalimentacion_anteproyecto 
                    (
                        id, 
                        numero_documento, 
                        codigo_anteproyecto, 
                        observacion_general, 
                        estado, 
                        documento, 
                        fecha_creacion, 
                        fecha_entrega_avances
                    ) 
                    VALUES 
                    (
                        :id_documento_cargado, 
                        :numero_documento, 
                        :codigo_anteproyecto, 
                        :observacion_general, 
                        :estado, 
                        :documento, 
                        :fecha_creacion, 
                        :fecha_entrega_avances
                    )
                ");
    
                // Vincular parámetros
                $consulta_insertar_retroalimentacion->bindParam(':id_documento_cargado', $datos['id_documento_cargado'], PDO::PARAM_INT);
                $consulta_insertar_retroalimentacion->bindParam(':numero_documento', $datos['numero_documento'], PDO::PARAM_STR);
                $consulta_insertar_retroalimentacion->bindParam(':codigo_anteproyecto', $datos['codigo_anteproyecto'], PDO::PARAM_STR);
                $consulta_insertar_retroalimentacion->bindParam(':observacion_general', $datos['observacion_general'], PDO::PARAM_STR);
                $consulta_insertar_retroalimentacion->bindParam(':estado', $datos['estado'], PDO::PARAM_STR);
                $consulta_insertar_retroalimentacion->bindParam(':documento', $datos['nombre_archivo_word'], PDO::PARAM_STR);
                $consulta_insertar_retroalimentacion->bindParam(':fecha_entrega_avances', $datos['fecha_entrega'], PDO::PARAM_STR);
    
                // Fecha actual en formato correcto
                $fecha_actual = date("Y-m-d H:i:s");
                $consulta_insertar_retroalimentacion->bindParam(':fecha_creacion', $fecha_actual, PDO::PARAM_STR);
    
                // Ejecutar y verificar
                if ($consulta_insertar_retroalimentacion->execute()) {
                    return true;
                }
            }
        } catch (PDOException $e) {
            error_log("Error en la inserción de retroalimentación: " . $e->getMessage());
        }
    
        return false;
    }
    
    
    

    protected static function eliminar_asesor_proyecto_modelo($codigo_proyecto, $numero_documento) {
        // Preparar la consulta SQL para eliminar el registro de la tabla asignar_estudiante_anteproyecto
        $consulta = MainModel::conectar()->prepare("
            DELETE FROM Asignar_asesor_anteproyecto_proyecto 
            WHERE codigo_proyecto = :codigo_proyecto 
            AND numero_documento = :numero_documento
        ");
    
        // Vincular los parámetros a la consulta
        $consulta->bindParam(':codigo_proyecto', $codigo_proyecto, PDO::PARAM_STR);
        $consulta->bindParam(':numero_documento', $numero_documento, PDO::PARAM_STR);
    
        // Ejecutar la consulta y verificar si se eliminó correctamente
        if ($consulta->execute()) {
            return $consulta; // Devolver el resultado de la ejecución si fue exitosa
        } else {
            return false; // Devolver false si hubo algún error
        }
    }
    
    
    
        
}