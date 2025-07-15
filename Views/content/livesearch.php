<?php

$peticionAjax = true;
// Incluir el archivo que contiene la clase MainModel
require_once "../../Model/Consultas.php"; 

$ins_MainModelo = new Consultas();
// Recibir el valor que se pasa a la función

if (isset($_GET['q'])) {

    $q = Consultas::limpiar_cadenas($_REQUEST["q"]);

    $consulta = Consultas::ejecutar_consultas_simples_two_ajax(
        "SELECT titulo_anteproyecto FROM anteproyectos WHERE codigo_anteproyecto = :codigo", 
        [':codigo' => $q]  // Pasar el valor del código como un array de parámetros
    );
    
    if ($consulta->rowCount() > 0) {
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        echo $resultado['titulo_anteproyecto'];
    } else {
        echo "No se encontró el proyecto";
    }
    

}else if (isset($_GET['codigoProyecto'])) {

    $q = Consultas::limpiar_cadenas($_REQUEST["codigoProyecto"]);

    // Verificar si el código está en ambas tablas
    $consulta_existe_en_ambas = Consultas::ejecutar_consultas_simples_two_ajax(
        "SELECT 
            (SELECT COUNT(*) FROM anteproyectos WHERE codigo_anteproyecto = :codigo) AS existe_en_anteproyecto,
            (SELECT COUNT(*) FROM proyectos WHERE codigo_proyecto = :codigo) AS existe_en_proyecto",
        [':codigo' => $q]
    );
    
    $verificacion = $consulta_existe_en_ambas->fetch(PDO::FETCH_ASSOC);
    $existe_en_anteproyecto = $verificacion['existe_en_anteproyecto'];
    $existe_en_proyecto = $verificacion['existe_en_proyecto'];
    
    // Si el código está en ambas tablas, se prioriza "Proyecto"
    if ($existe_en_proyecto > 0) {
        // Consultar el proyecto
        $consulta_proyecto = Consultas::ejecutar_consultas_simples_two_ajax(
            "SELECT 
                p.*,  
                f.nombre_facultad,
                pr.nombre_programa
            FROM proyectos p
            INNER JOIN facultades f ON p.id_facultad = f.id_facultad
            INNER JOIN programas_academicos pr ON p.id_programa = pr.id_programa
            WHERE p.codigo_proyecto = :codigo", 
            [':codigo' => $q]
        );
    
        if ($consulta_proyecto->rowCount() > 0) {
            $resultado = $consulta_proyecto->fetch(PDO::FETCH_ASSOC);
            echo json_encode([
                'Título' => $resultado['titulo_proyecto'],
                'PalabrasClaves' => $resultado['palabras_claves'],
                'NombreFaculta' => $resultado['nombre_facultad'],
                'NombrePrograma' => $resultado['nombre_programa'],
                'tipo' => 'Proyecto',
                'Codigotipo' => Consultas::encryption(2),
                'IdFacultad' => Consultas::encryption($resultado['id_facultad']),
                'IdPrograma' => Consultas::encryption($resultado['id_programa'])
            ]);
            exit();
        }
    } elseif ($existe_en_anteproyecto > 0) {
        // Consultar el anteproyecto
        $consulta_anteproyecto = Consultas::ejecutar_consultas_simples_two_ajax(
            "SELECT 
                a.*,
                f.nombre_facultad,
                p.nombre_programa
            FROM anteproyectos a
            INNER JOIN facultades f ON a.id_facultad = f.id_facultad
            INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
            WHERE a.codigo_anteproyecto = :codigo", 
            [':codigo' => $q]
        );
    
        if ($consulta_anteproyecto->rowCount() > 0) {
            $resultado = $consulta_anteproyecto->fetch(PDO::FETCH_ASSOC);
            echo json_encode([
                'Título' => $resultado['titulo_anteproyecto'],
                'PalabrasClaves' => $resultado['palabras_claves'],
                'NombreFaculta' => $resultado['nombre_facultad'],
                'NombrePrograma' => $resultado['nombre_programa'],
                'tipo' => 'Anteproyecto',
                'Codigotipo' => Consultas::encryption(1),
                'IdFacultad' => Consultas::encryption($resultado['id_facultad']),
                'IdPrograma' => Consultas::encryption($resultado['id_programa'])
            ]);
            exit();
        }
    }
    
    // Si no está en ninguna tabla, mostrar mensaje de error
    echo json_encode(['error' => 'No se encontró información del código']);
    exit();
    

}


else  if (isset($_GET['documento'])) {

    $documento = Consultas::limpiar_cadenas($_REQUEST["documento"]);
    $consulta = Consultas::ejecutar_consultas_simples_two_ajax(
        "SELECT nombre_usuario, apellidos_usuario FROM usuarios WHERE numero_documento = :documento", 
        [':documento' => $documento]  // Pasar el valor del documento como un array de parámetros
    );
    
    if ($consulta->rowCount() > 0) {
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        echo $resultado['nombre_usuario'] . " " . $resultado['apellidos_usuario'];
    } else {
        echo "No se encontró el usuario";
    }

}else  if (isset($_GET['documentoasesor'])) {


    $documento = Consultas::limpiar_cadenas($_REQUEST["documentoasesor"]);

    $consulta = Consultas::ejecutar_consultas_simples_two_ajax(
        "SELECT 
            u.nombre_usuario, 
            u.apellidos_usuario, 
            u.telefono_usuario, 
            u.correo_usuario, 
            r.nombre_rol, 
            f.nombre_facultad, 
            GROUP_CONCAT(DISTINCT p.nombre_programa ORDER BY p.nombre_programa SEPARATOR ', ') AS programas
        FROM usuarios u
        INNER JOIN roles_usuarios r ON u.id_rol = r.id_rol
        LEFT JOIN Asignar_usuario_facultades auf ON u.numero_documento = auf.numero_documento
        LEFT JOIN facultades f ON auf.id_facultad = f.id_facultad
        LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
        WHERE u.numero_documento = :documento
        GROUP BY u.nombre_usuario, u.apellidos_usuario, u.telefono_usuario, u.correo_usuario, r.nombre_rol, f.nombre_facultad",
        [':documento' => $documento]
    );
    
    if ($consulta->rowCount() > 0) {
        // Obtener el resultado de la consulta
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    
        // Preparar los datos en formato JSON
        echo json_encode([
            'nombre' => $resultado['nombre_usuario'],
            'apellidos' => $resultado['apellidos_usuario'],
            'telefono' => $resultado['telefono_usuario'],
            'correo' => $resultado['correo_usuario'],
            'programa' => $resultado['programas'] ?? 'No asignado',
            'facultad' => $resultado['nombre_facultad'] ?? 'No asignado',
            'rol' => $resultado['nombre_rol']
        ]);
    } else {
        // Si no se encuentra el usuario, devolver un mensaje de error en JSON
        echo json_encode(['error' => 'No se encontró el usuario']);
    }
    

    
    

}else  if (isset($_GET['codigo_anteproyecto'])) {

    $codigoAnteproyecto = Consultas::limpiar_cadenas($_REQUEST["codigo_anteproyecto"]);
    $consulta = "SELECT ae.codigo_anteproyecto, ae.numero_documento, u.nombre_usuario AS nombre, u.apellidos_usuario AS apellidos, u.correo_usuario AS correo
    FROM asignar_estudiante_anteproyecto ae
    INNER JOIN usuarios u ON ae.numero_documento = u.numero_documento
    WHERE ae.codigo_anteproyecto = :codigo_anteproyecto";

    // Ejecutar la consulta utilizando parámetros seguros
    $sql = Consultas::ejecutar_consultas_simples_two_ajax(
        $consulta,
        [':codigo_anteproyecto' => $codigoAnteproyecto]
    );

    if ($sql->rowCount() > 0) {
        // Obtener todos los usuarios como un array asociativo
        $usuarios = $sql->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($usuarios); // Retornar todos los usuarios en formato JSON
    } else {
        echo json_encode(['error' => 'No se encontró ningún usuario registrado para este anteproyecto.']);
    }

}else  if (isset($_GET['codigo_proyecto'])) {

    $codigoProyecto = Consultas::limpiar_cadenas($_REQUEST["codigo_proyecto"]);

    $consulta = "SELECT ae.codigo_proyecto, ae.numero_documento, u.nombre_usuario AS nombre, u.apellidos_usuario AS apellidos, u.correo_usuario AS correo
    FROM asignar_estudiante_proyecto ae
    INNER JOIN usuarios u ON ae.numero_documento = u.numero_documento
    WHERE ae.codigo_proyecto = :codigo_proyecto";

    // Ejecutar la consulta utilizando parámetros seguros
    $sql = Consultas::ejecutar_consultas_simples_two_ajax(
        $consulta,
        [':codigo_proyecto' => $codigoProyecto]
    );

    if ($sql->rowCount() > 0) {
        // Obtener todos los usuarios como un array asociativo
        $usuarios = $sql->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($usuarios); // Retornar todos los usuarios en formato JSON
    } else {
        echo json_encode(['error' => 'No se encontró ningún usuario registrado para este anteproyecto.']);
    }

}else  if (isset($_GET['documentousers'])) {

    $numero_documento = Consultas::limpiar_cadenas($_REQUEST["documentousers"]);

    $consulta = Consultas::ejecutar_consultas_simples_two_ajax(
        "SELECT * FROM usuarios WHERE numero_documento = :documento",
        [':documento' => $numero_documento]
    );
    if ($consulta->rowCount() > 0) {

        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        $id_rol = $resultado['id_rol'];

        $sql2 = Consultas::ejecutar_consultas_simples_two_ajax(
            "SELECT nombre_rol FROM roles_usuarios
             WHERE id_rol = :id_rol",
            [':id_rol' => $id_rol]);

            if ($sql2->rowCount() > 0) {
                $rol = $sql2->fetch(PDO::FETCH_ASSOC);
                
                $rol_usuario = $rol['nombre_rol'];
                
            } else {
                $rol_usuario =  "No se encontró el rol del usuario ";
                 
            }

        echo json_encode([
            'nombre' => $resultado['nombre_usuario'],
            'apellidos' => $resultado['apellidos_usuario'],
            'telefono' => $resultado['telefono_usuario'],
            'correo' => $resultado['correo_usuario'],
            'id_rol' => (int) $resultado['id_rol'],
            'rol' => $rol_usuario
            
        ]);
    
    }else {

         // Si no se encuentra el usuario, devolver un mensaje de error en JSON
         echo json_encode(['error' => 'No se encontró el usuario']);
    }



}else  if (isset($_GET['coordinador']) && isset($_GET['privilegio']) &&isset($_GET['documentoUSER'])) {

    $dato = Consultas::limpiar_cadenas($_REQUEST["coordinador"]);

    $privilegio = Consultas::limpiar_cadenas($_REQUEST["privilegio"]);

    $documentoUSER = Consultas::limpiar_cadenas($_REQUEST["documentoUSER"]);

    if($privilegio == 1){

        $sql_faculta = "SELECT * FROM facultades";
        $consulta_faculta = Consultas::ejecutar_consultas_simples_two_ajax($sql_faculta);
        if ($consulta_faculta->rowCount() > 0) {
            $faculta_array = array();
            while ($faculta = $consulta_faculta->fetch(PDO::FETCH_ASSOC)) {
                $faculta_array[] = array(
                    'id_faculta' => Consultas::encryption($faculta['id_facultad']),
                    'nombre_faculta' => $faculta['nombre_facultad']
                );
            }

            echo json_encode($faculta_array);
        } else {

            echo json_encode(['error' => 'No se encontraron facultades en el sistema.']);
        }

    }else if ($privilegio == 2){

        $consulta = "SELECT 
                auf.numero_documento,
                f.nombre_facultad, 
                MIN(p.nombre_programa) AS nombre_programa,  -- Selecciona un programa de la facultad
                f.id_facultad, 
                MIN(p.id_programa) AS id_programa           -- Selecciona un id_programa de la facultad
            FROM Asignar_usuario_facultades auf
            INNER JOIN facultades f ON auf.id_facultad = f.id_facultad
            LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
            WHERE auf.numero_documento = :numero_documento
            GROUP BY 
                auf.numero_documento, 
                f.nombre_facultad, 
                f.id_facultad";

        // Ejecutar la consulta utilizando parámetros seguros
        $consulta_faculta = Consultas::ejecutar_consultas_simples_two_ajax(
            $consulta,
            [':numero_documento' => $documentoUSER]
        );

        if ($consulta_faculta->rowCount() > 0) {

            $faculta_array = array();
            while ($faculta = $consulta_faculta->fetch(PDO::FETCH_ASSOC)) {
                $faculta_array[] = array(
                    'id_faculta' => Consultas::encryption($faculta['id_facultad']),
                    'nombre_faculta' => $faculta['nombre_facultad']
                );
            }

            echo json_encode($faculta_array);


        }else {

            echo json_encode(['error' => 'No se encontraron facultades en el sistema.']);
        }


    }


    


}else  if (isset($_GET['programa'])) {

    $programa = Consultas::limpiar_cadenas($_REQUEST["programa"]);

    $programa = Consultas::decryption($programa);

    $sql_faculta = "SELECT * FROM programas_academicos WHERE id_facultad = '$programa'";

    $consulta_programas = Consultas::ejecutar_consultas_simples_two_ajax($sql_faculta);
    if ($consulta_programas->rowCount() > 0) {
        $programa_array = array();
        while ($programa = $consulta_programas->fetch(PDO::FETCH_ASSOC)) {
            $programa_array[] = array(
                'id_faculta' => Consultas::encryption($programa['id_programa']),
                'nombre_programa' => $programa['nombre_programa']
            );
        }

        echo json_encode($programa_array);
    } else {

        echo json_encode(['error' => 'No se encontraron programas en el sistema.']);
    }

}else  if (isset($_GET['documento_usersid'])) {

    $numero_documento = Consultas::limpiar_cadenas($_GET['documento_usersid']);

                    $consulta = "SELECT 
                    auf.numero_documento,
                    f.nombre_facultad, 
                    GROUP_CONCAT(p.nombre_programa SEPARATOR ', ') AS nombre_programa,  -- Agrupa todos los programas en una sola fila separados por comas
                    f.id_facultad
                FROM Asignar_usuario_facultades auf
                INNER JOIN facultades f ON auf.id_facultad = f.id_facultad
                LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
                WHERE auf.numero_documento =:numero_documento
                GROUP BY 
                    auf.numero_documento, 
                    f.nombre_facultad, 
                    f.id_facultad;";

    // Ejecutar la consulta utilizando parámetros seguros
    $sql = Consultas::ejecutar_consultas_simples_two_ajax(
        $consulta,[':numero_documento' => $numero_documento]
    );

    if ($sql->rowCount() > 0) {
        // Obtener todos los usuarios como un array asociativo
        $facultades  = $sql->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($facultades ); // Retornar todos los usuarios en formato JSON
    } else {
        echo json_encode(['error' => 'No se encontraron facultades asignadas para este usuario.']);
    }

}






?>
