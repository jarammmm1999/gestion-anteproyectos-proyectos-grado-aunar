
$consulta_existente = MainModel::ejecutar_consultas_simples(
    "SELECT codigo_proyecto FROM asesores_metodologicos WHERE codigo_proyecto = '$codigo'"
);

if ($consulta_existente->rowCount() > 0) {

}else{

        // Si no existe, realizar la inserción
        $insertar_registro_datos_proyecto = MainModel::ejecutar_consultas_simples(
            "INSERT INTO asesores_metodologicos (
                codigo_proyecto, resumen_general, resumen_titulos, 
                resumen_problemas, resumen_objetivo, resumen_justificacion, 
                resumen_marcos, resumen_diseño, resumen_finalidad
            ) VALUES (
                '$codigo', '{}', '{}', '{}', '{}', '{}', '{}', '{}', '{}')"
        );

        if ($insertar_registro_datos_proyecto->rowCount() > 0) {
        
            $insertar_registro_datos_proyecto2 = MainModel::ejecutar_consultas_simples(
                "INSERT INTO asesores_metodologicos_two (
                    codigo_proyecto, resumen_referencia, resumen_anexo,resumen_postulacion
                ) VALUES (
                    '$codigo', '{}', '{}', '{}')"
            );

            if ($insertar_registro_datos_proyecto2->rowCount() > 0) {
    

            }else{
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado",
                    "Texto" => "No se pudo guardar la informacion en la tabla asesores_metodologicos2",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }



        }else{

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo guardar la informacion en la tabla asesores_metodologicos",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }



}

