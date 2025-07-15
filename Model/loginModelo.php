<?php

require_once "MainModel.php";

Class LoginModelo extends MainModel {

    /** Modelo para iniaciar session */

    protected static function iniciar_session_modelo($datos) {
        $consulta = MainModel::conectar()->prepare("
            SELECT u.id, 
                   u.numero_documento, 
                   u.nombre_usuario, 
                   u.apellidos_usuario, 
                   u.correo_usuario, 
                   u.id_rol,
                   u.estado,
                   u.estado_conexion,
                   r.nombre_rol 
            FROM usuarios u
            INNER JOIN roles_usuarios r ON u.id_rol = r.id_rol
            WHERE u.numero_documento = :numero_documento 
              AND u.contrasena_usuario = :contrasena_usuario 
        ");
        $consulta->bindParam(':numero_documento', $datos['numero_documento']);
        $consulta->bindParam(':contrasena_usuario', $datos['contrasena_usuario']);
        $consulta->execute();
    
        return $consulta; // Retorna todos los roles asociados al usuario
    }
    


}