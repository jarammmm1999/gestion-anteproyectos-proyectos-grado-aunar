<?php

require_once "MainModel.php";

Class ProyectoModelo extends MainModel {
 
        
    protected static function agregar_proyecto_modelo($datos) {
        try {
            // Preparamos la consulta de inserción
            $consulta = MainModel::conectar()->prepare(
                "INSERT INTO proyectos (
                    codigo_proyecto, titulo_proyecto, palabras_claves, id_facultad, id_programa, fecha_creacion, modalidad
                ) VALUES (
                    :codigo_proyecto, :titulo_proyecto, :palabras_claves,:id_facultad,:id_programa, NOW(), :modalidad
                )"
            );
    
            // Asignamos los valores de los parámetros
            $consulta->bindParam(':codigo_proyecto', $datos['codigo_proyecto'], PDO::PARAM_STR);
            $consulta->bindParam(':titulo_proyecto', $datos['titulo_proyecto'], PDO::PARAM_STR);
            $consulta->bindParam(':palabras_claves', $datos['palabras_claves_proyecto'], PDO::PARAM_STR);
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
            error_log("Error al agregar proyecto: " . $e->getMessage());
            echo "Error: " . $e->getMessage(); // Imprimir error para verificar
            return false;
        }
    }

    protected static function asignar_estudiante_proyecto_modelo($datos) {
        // Preparar la consulta SQL para insertar los datos en la tabla asignar_estudiante_anteproyecto
        $consulta = MainModel::conectar()->prepare("
            INSERT INTO asignar_estudiante_proyecto (codigo_proyecto, numero_documento, fecha_creacion)
            VALUES (:codigo_proyecto, :numero_documento, NOW())
        ");
    
        // Vincular los parámetros a la consulta
        $consulta->bindParam(':codigo_proyecto', $datos['codigo_proyecto'], PDO::PARAM_STR);
        $consulta->bindParam(':numero_documento', $datos['numero_documento'], PDO::PARAM_STR);
    
        // Ejecutar la consulta y verificar si se insertó correctamente
        if($consulta->execute()) {
            return $consulta; // Devolver el resultado de la ejecución si fue exitosa
        } else {
            return false; // Devolver false si hubo algún error
        }
    }

    protected static function actualizar_proyecto_modelos($datos) {
        // Preparar la consulta SQL para actualizar el título y las palabras claves
        $consulta = MainModel::conectar()->prepare("
            UPDATE proyectos 
            SET titulo_proyecto = :titulo_proyecto, 
                palabras_claves = :palabras_claves 
            WHERE codigo_proyecto = :codigo_proyecto
        ");
    
        // Vincular los parámetros a la consulta
        $consulta->bindParam(':titulo_proyecto', $datos['titulo_proyecto'], PDO::PARAM_STR);
        $consulta->bindParam(':palabras_claves', $datos['palabras_claves'], PDO::PARAM_STR);
        $consulta->bindParam(':codigo_proyecto', $datos['codigo_proyecto'], PDO::PARAM_STR);
    
        // Ejecutar la consulta y verificar si se actualizó correctamente
        if ($consulta->execute()) {
            return $consulta; // Devolver el resultado de la ejecución si fue exitosa
        } else {
            return false; // Devolver false si hubo algún error
        }
    }

    protected static function eliminar_estudiante_proyecto_modelos($codigo_proyecto, $numero_documento) {
        // Preparar la consulta SQL para eliminar el registro de la tabla asignar_estudiante_anteproyecto
        $consulta = MainModel::conectar()->prepare("
            DELETE FROM asignar_estudiante_proyecto 
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

    protected static function eliminar_proyecto_modelos($codigo) {
        try {
            // Conectar a la base de datos
            $conexion = MainModel::conectar();
    
            // Iniciar la transacción
            $conexion->beginTransaction();
    
            // 1. Eliminar registros de la tabla `asignar_estudiante_anteproyecto` que correspondan al código del anteproyecto
            $consultaAsignacion = $conexion->prepare("
                DELETE FROM asignar_estudiante_proyecto 
                WHERE codigo_proyecto = :codigo_proyecto
            ");
            $consultaAsignacion->bindParam(':codigo_proyecto', $codigo, PDO::PARAM_STR);
    
            // Ejecutar la eliminación en la tabla de asignaciones
            $consultaAsignacion->execute();
    
            // 2. Eliminar el registro de la tabla `anteproyectos`
            $consultaAnteproyecto = $conexion->prepare("
                DELETE FROM proyectos 
                WHERE codigo_proyecto = :codigo_proyecto
            ");
            $consultaAnteproyecto->bindParam(':codigo_proyecto', $codigo, PDO::PARAM_STR);
    
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

    protected static function Asignar_asesor_anteproyecto_proyecto_modelos($datos) {
        try {
            // Conectar a la base de datos y comenzar la transacción
            $conexion = MainModel::conectar();
            $conexion->beginTransaction();
    
                $consulta = $conexion->prepare("
                    INSERT INTO Asignar_asesor_anteproyecto_proyecto (codigo_proyecto, numero_documento) 
                    VALUES (:codigo_proyecto, :numero_documento)
                ");
    
                $consulta->bindParam(':codigo_proyecto', $datos['codigo_proyecto'], PDO::PARAM_STR);
                $consulta->bindParam(':numero_documento', $datos['numero_documento'], PDO::PARAM_STR);
    
                // Ejecutar la consulta de inserción
                if ($consulta->execute()) {
                    // Confirmar la transacción y devolver la consulta de inserción
                    $conexion->commit();
                    return $consulta;
                } else {
                    // Si la inserción falla, revertir la transacción
                    $conexion->rollBack();
                    return false;
                }
           
        } catch (PDOException $e) {
            // Manejar cualquier error y revertir la transacción
            $conexion->rollBack();
            return false;
        }
    }

    protected static function Asignar_asesor_externo_anteproyecto_proyecto_modelos($datos) {
        try {
            // Conectar a la base de datos y comenzar la transacción
            $conexion = MainModel::conectar();
            $conexion->beginTransaction();

            $fecha_creacion = date("Y-m-d H:i:s");
    
                // Si la actualización de horas fue exitosa, proceder a insertar en `Asignar_asesor_anteproyecto_proyecto`
                $consulta = $conexion->prepare("
                    INSERT INTO Asignar_asesor_anteproyecto_proyecto (codigo_proyecto, numero_documento,fecha_creacion) 
                    VALUES (:codigo_proyecto, :numero_documento, :fecha_creacion)
                ");
    
                $consulta->bindParam(':codigo_proyecto', $datos['codigo_proyecto'], PDO::PARAM_STR);
                $consulta->bindParam(':numero_documento', $datos['numero_documento'], PDO::PARAM_STR);
                $consulta->bindParam(':fecha_creacion', $fecha_creacion);
    
                // Ejecutar la consulta de inserción
                if ($consulta->execute()) {
                    // Confirmar la transacción y devolver la consulta de inserción
                    $conexion->commit();
                    return $consulta;
                } else {
                    // Si la inserción falla, revertir la transacción
                    $conexion->rollBack();
                    return false;
                }
            
        } catch (PDOException $e) {
            // Manejar cualquier error y revertir la transacción
            $conexion->rollBack();
            return false;
        }
    }


    protected static function Actualizar_horas_profesor_modelos($datos) {
        // Preparar la consulta SQL para actualizar el título y las palabras claves
        $consulta = MainModel::conectar()->prepare("
             UPDATE asignar_horas_profesor 
                SET numero_hora = :numero_hora
                WHERE numero_documento = :numero_documento
        ");
    
    
            // Vincular los parámetros a la consulta
            $consulta->bindParam(':numero_hora', $datos['numero_hora'], PDO::PARAM_INT);
            $consulta->bindParam(':numero_documento', $datos['numero_documento'], PDO::PARAM_STR);

            // Ejecutar la consulta y verificar si se actualizó correctamente
        if ($consulta->execute()) {
            return $consulta; // Devolver el resultado de la ejecución si fue exitosa
        } else {
            return false; // Devolver false si hubo algún error
        }
    }

    protected static function Actualizar_horas_jurados_profesor_modelos($datos) {
        // Preparar la consulta SQL para actualizar el título y las palabras claves
        $consulta = MainModel::conectar()->prepare("
             UPDATE asignar_horas_jurado_profesor 
                SET numero_hora = :numero_hora
                WHERE numero_documento = :numero_documento
        ");
    
    
            // Vincular los parámetros a la consulta
            $consulta->bindParam(':numero_hora', $datos['numero_hora'], PDO::PARAM_INT);
            $consulta->bindParam(':numero_documento', $datos['numero_documento'], PDO::PARAM_STR);

            // Ejecutar la consulta y verificar si se actualizó correctamente
        if ($consulta->execute()) {
            return $consulta; // Devolver el resultado de la ejecución si fue exitosa
        } else {
            return false; // Devolver false si hubo algún error
        }
    }

    protected static function Eliminar_horas_profesor_modelos($numero_documento) {
        // Iniciar una transacción para asegurar que ambas eliminaciones se realicen correctamente
        $conexion = MainModel::conectar();
        $conexion->beginTransaction();
    
        try {
            // Eliminar de la tabla asignar_horas_profesor
            $consulta_profesor = $conexion->prepare("
                DELETE FROM asignar_horas_profesor 
                WHERE numero_documento = :numero_documento
            ");
            $consulta_profesor->bindParam(':numero_documento', $numero_documento, PDO::PARAM_STR);
            $consulta_profesor->execute();
    
            // Eliminar de la tabla asignar_horas_jurado_profesor
            $consulta_jurado = $conexion->prepare("
                DELETE FROM asignar_horas_jurado_profesor 
                WHERE numero_documento = :numero_documento
            ");
            $consulta_jurado->bindParam(':numero_documento', $numero_documento, PDO::PARAM_STR);
            $consulta_jurado->execute();
    
            // Si ambas consultas se ejecutan correctamente, confirmar la transacción
            $conexion->commit();
            return true; // Devuelve true si todo fue exitoso
        } catch (Exception $e) {
            // En caso de error, deshacer la transacción
            $conexion->rollBack();
            return false; // Devuelve false si hubo algún error
        }
    }
    
    protected static function asignar_jurado_proyecto($datos) {

        try {
            // Conectar a la base de datos y comenzar la transacción
            $conexion = MainModel::conectar();
            $conexion->beginTransaction();
    
                // Si la actualización de horas fue exitosa, proceder a insertar en `Asignar_asesor_anteproyecto_proyecto`
                $consulta = $conexion->prepare("
                    INSERT INTO Asignar_jurados_proyecto (codigo_proyecto, numero_documento) 
                    VALUES (:codigo_proyecto, :numero_documento)
                ");
    
                $consulta->bindParam(':codigo_proyecto', $datos['codigo_proyecto'], PDO::PARAM_STR);
                $consulta->bindParam(':numero_documento', $datos['numero_documento'], PDO::PARAM_STR);
    
                // Ejecutar la consulta de inserción
                if ($consulta->execute()) {
                    // Confirmar la transacción y devolver la consulta de inserción
                    $conexion->commit();
                    return $consulta;
                } else {
                    // Si la inserción falla, revertir la transacción
                    $conexion->rollBack();
                    return false;
                }
            
        } catch (PDOException $e) {
            // Manejar cualquier error y revertir la transacción
            $conexion->rollBack();
            return false;
        }

    }

    protected static function cargar_documento_proyecto_modelo($datos) {
        // Preparar la consulta SQL para insertar el documento en la tabla
        $consulta = MainModel::conectar()->prepare("
            INSERT INTO cargar_documento_proyectos (codigo_proyecto, numero_documento, documento,nombre_archivo_word, estado, fecha_creacion)
            VALUES (:codigo_proyecto, :numero_documento, :documento, :nombre_archivo_word, :estado, :fecha_creacion)
        ");
    
        // Vincular los parámetros a la consulta
        $consulta->bindParam(':codigo_proyecto', $datos['codigo_proyecto'], PDO::PARAM_STR);
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


    protected static function guardar_retroalimentacion_proyecto_modelo($datos) {
        // Primero actualizar el estado del documento
        $consulta_actualizar_estado = MainModel::conectar()->prepare("
            UPDATE cargar_documento_proyectos 
            SET estado = :estadodocumento 
            WHERE id = :id
        ");
    
        // Vincular los parámetros a la consulta de actualización del estado
        $consulta_actualizar_estado->bindParam(':estadodocumento', $datos['estado'], PDO::PARAM_STR);
        $consulta_actualizar_estado->bindParam(':id', $datos['id_documento_cargado'], PDO::PARAM_INT);
    
        // Ejecutar la actualización del estado
        if ($consulta_actualizar_estado->execute()) {
            // Luego insertar la retroalimentación en la tabla retroalimentacion_proyecto
            $consulta_insertar_retroalimentacion = MainModel::conectar()->prepare("
                INSERT INTO retroalimentacion_proyecto 
                (
                    id, 
                    numero_documento, 
                    codigo_proyecto, 
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
                    :codigo_proyecto, 
                    :observacion_general, 
                    :estado, 
                    :documento, 
                    :fecha_creacion,
                    :fecha_entrega_avances
                )
            ");
    
            // Vincular los parámetros a la consulta de inserción de retroalimentación
            $consulta_insertar_retroalimentacion->bindParam(':id_documento_cargado', $datos['id_documento_cargado'], PDO::PARAM_INT);
            $consulta_insertar_retroalimentacion->bindParam(':numero_documento', $datos['numero_documento'], PDO::PARAM_STR);
            $consulta_insertar_retroalimentacion->bindParam(':codigo_proyecto', $datos['codigo_anteproyecto'], PDO::PARAM_STR);
            $consulta_insertar_retroalimentacion->bindParam(':observacion_general', $datos['observacion_general'], PDO::PARAM_STR);
            $consulta_insertar_retroalimentacion->bindParam(':estado', $datos['estado_revision'], PDO::PARAM_STR);
            $consulta_insertar_retroalimentacion->bindParam(':documento', $datos['nombre_archivo_word'], PDO::PARAM_STR);
            $consulta_insertar_retroalimentacion->bindParam(':fecha_entrega_avances', $datos['fecha_entrega'], PDO::PARAM_STR);
            $fecha_actual = date("Y-m-d H:i:s");
            $consulta_insertar_retroalimentacion->bindParam(':fecha_creacion', $fecha_actual);
    
            // Ejecutar la consulta de inserción y verificar si se insertó correctamente
            if ($consulta_insertar_retroalimentacion->execute()) {
                return $consulta_insertar_retroalimentacion; // Devolver el resultado de la ejecución si fue exitosa
            } else {
                return false; // Devolver false si hubo algún error
            }
        } else {
            return false; // Devolver false si hubo un error al actualizar el estado
        }
    }
    
    
    
    protected static function cargar_evidencia_reunion_modelo($datos) {
        // Preparar la consulta SQL para insertar la evidencia en la tabla
        $consulta = MainModel::conectar()->prepare("
            INSERT INTO evidencia_reuniones_proyectos (codigo_proyecto, numero_documento, imagenes, fecha_creacion)
            VALUES (:codigo_proyecto, :numero_documento, :imagenes, :fecha_creacion)
        ");
    
        // Vincular los parámetros a la consulta
        $consulta->bindParam(':codigo_proyecto', $datos['codigo_proyecto'], PDO::PARAM_STR);
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


   
    

}