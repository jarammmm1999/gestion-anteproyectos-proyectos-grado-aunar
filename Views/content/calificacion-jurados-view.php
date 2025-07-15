<?php

if (isset($_GET['views'])) {
    $ruta = explode(
        "/",
        $_GET['views']
    );

    $codigo_proyecto = $ruta[1];

    $documento_jurado = $ruta[2];

    $documento_jurado = $ins_loginControlador->decryption_two($documento_jurado);
}



$sqlLogo = "SELECT nombre_logo 
FROM configuracion_aplicacion 
LIMIT 1";
$consulta_logo = $ins_loginControlador->ejecutar_consultas_simples_two($sqlLogo);

if ($consulta_logo->rowCount() > 0) {
    $resultado = $consulta_logo->fetch(PDO::FETCH_ASSOC);
    $nombre_logo = $resultado['nombre_logo'];
} else {
    $nombre_logo = "logo-autonoma.png";
}

/********************consultar informacion proyecto********************* */

$consutar_codigo = "SELECT p.*, pa.nombre_programa, f.nombre_facultad,  pa.id_programa , f.id_facultad
FROM proyectos p
JOIN programas_academicos pa ON p.id_programa = pa.id_programa
JOIN facultades f ON pa.id_facultad = f.id_facultad
WHERE p.codigo_proyecto = '$codigo_proyecto'";

$resultado_consultar_codigo = $ins_loginControlador->ejecutar_consultas_simples_two($consutar_codigo);

if ($resultado_consultar_codigo->rowCount() > 0) {

    $datos = $resultado_consultar_codigo->fetch(PDO::FETCH_ASSOC);

    $titulo_proyecto = $datos['titulo_proyecto'];

    $programa_proyecto = $datos['nombre_programa'];

    $id_programa = $datos['id_programa'];

    $id_facultad = $datos['id_facultad'];

    $modalidad = $datos['modalidad'];




    /********************************consultar registro calificado ********************************** */

    $consultar_registro_calificado = "SELECT * FROM registros_calificados_programas WHERE id_programa = '$id_programa' ";

    $resultado_consultar_registro = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_registro_calificado);

    if ($resultado_consultar_registro->rowCount() > 0) {

        $dates = $resultado_consultar_registro->fetch(PDO::FETCH_ASSOC);

        $nombre_registro = $dates['nombre_registro'];
    } else {

        $nombre_registro = "No hay registro calificado, encontrado";
    }

     

    /***************************consultar modalidad************************************ */

    $consultar_modalidad = "SELECT * FROM modalidad_grados WHERE id_modalidad = '$modalidad' ";

    $resultado_consultar_modalidad = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_modalidad);

    if ($resultado_consultar_modalidad->rowCount() > 0) {

        $datas = $resultado_consultar_modalidad->fetch(PDO::FETCH_ASSOC);

        $modalidad_registrada = $datas['nombre_modalidad'];
    } else {

        $modalidad_registrada = "No modalidad registrada";
    }

    /******************************consultar informacion de los estudiantes************************************/

    $consultar_estudinates_proyecto = "SELECT e.nombre_usuario, e.apellidos_usuario 
    FROM asignar_estudiante_proyecto ep
    JOIN usuarios e ON ep.numero_documento = e.numero_documento
    WHERE ep.codigo_proyecto = '$codigo_proyecto'";

    $resultado_estudiantes = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_estudinates_proyecto);

    $estudiantes_info = [];

    if ($resultado_estudiantes->rowCount() > 0) {

        $datos_estudiantes = $resultado_estudiantes->fetchAll(PDO::FETCH_ASSOC);

        foreach ($datos_estudiantes as $estudiante) {

            $estudiantes_info[] = $estudiante['nombre_usuario'] . ' ' . $estudiante['apellidos_usuario'];
        }
    }

    /******************************consultar informacion de los jurado************************************/

    $consultar_asesores_proyecto = "SELECT 
        e.nombre_usuario, 
        e.apellidos_usuario,
        f.firma
    FROM Asignar_jurados_proyecto ep
    JOIN usuarios e ON ep.numero_documento = e.numero_documento
    LEFT JOIN firma_digital_usuarios f ON e.numero_documento = f.numero_documento
    WHERE ep.codigo_proyecto = '$codigo_proyecto'";


    $resultado_asesores = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_asesores_proyecto);

    $asesores_info = [];

    $firmas_asesores = []; 

    if ($resultado_asesores->rowCount() > 0) {

        $datos_asesores = $resultado_asesores->fetchAll(PDO::FETCH_ASSOC);

        foreach ($datos_asesores as $asesores) {

            $asesores_info[] = $asesores['nombre_usuario'] . ' ' . $asesores['apellidos_usuario'];

            $firmas_asesores[] = $asesor['firma'] ?? null;
        }
    }

    /******************************consultar informacion de los asesores************************************/
    $consultar_director_proyecto = "SELECT 
        e.nombre_usuario, 
        e.id_rol, 
        e.apellidos_usuario,
        f.firma
    FROM Asignar_asesor_anteproyecto_proyecto ep
    JOIN usuarios e ON ep.numero_documento = e.numero_documento
    LEFT JOIN firma_digital_usuarios f ON e.numero_documento = f.numero_documento
    WHERE ep.codigo_proyecto = '$codigo_proyecto' AND e.id_rol = 5";


    $resultado_directores = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_director_proyecto);

    $directores_info = [];

    $firmas_directores = []; 

    if ($resultado_directores->rowCount() > 0) {

        $datos_directores = $resultado_directores->fetchAll(PDO::FETCH_ASSOC);

        foreach ($datos_directores as $directores) {

            $directores_info[] = $directores['nombre_usuario'] . ' ' . $directores['apellidos_usuario'];

            $firmas_directores[] = $directores['firma'] ?? null;
        }
    }


    /****************************** extraer datos del coordinador************************************/

    $consultar_cordinador_proyecto = "SELECT e.nombre_usuario, e.apellidos_usuario, f.firma
     FROM Asignar_usuario_facultades ep
     JOIN usuarios e ON ep.numero_documento = e.numero_documento
     LEFT JOIN firma_digital_usuarios f ON e.numero_documento = f.numero_documento
     WHERE ep.id_facultad = '$id_facultad' 
     AND ep.id_programa = '$id_programa' 
     AND e.id_rol = '2'
    ";

    $resultado_cordinador = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_cordinador_proyecto);

    $cordinador_info = [];

    $firmas_cordinador = []; 

    if ($resultado_cordinador->rowCount() > 0) {
        $datos_cordinador = $resultado_cordinador->fetchAll(PDO::FETCH_ASSOC);

        foreach ($datos_cordinador as $cordinador) {
            $cordinador_info[] = $cordinador['nombre_usuario'] . ' ' . $cordinador['apellidos_usuario'];

            $firmas_cordinador[] = $cordinador['firma'] ?? null;
        }
    }


    /****************************** Extraer datos de los administradores ************************************/

    $consultar_administradores = "SELECT nombre_usuario, apellidos_usuario,  f.firma 
    FROM usuarios u
    LEFT JOIN firma_digital_usuarios f ON u.numero_documento = f.numero_documento
    WHERE id_rol = '1'
    ";

    $resultado_administradores = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_administradores);

    $administradores_info = [];
    
    $firmas_administradores = []; // Nuevo arreglo para las firmas

    if ($resultado_administradores->rowCount() > 0) {
        $datos_administradores = $resultado_administradores->fetchAll(PDO::FETCH_ASSOC);

        foreach ($datos_administradores as $admin) {

            $administradores_info[] = $admin['nombre_usuario'] . ' ' . $admin['apellidos_usuario'];

            $firmas_administradores[] = $admin['firma'] ?? null; //
        }
    }
} else {
}

/****************************modal si no tiene definido el tipo de usuario*****************************************/ 
// Consulta para obtener los datos del jurado
$consultar_opcion_jurado_usuario = "SELECT * FROM Asignar_jurados_proyecto WHERE numero_documento = '$documento_jurado'";

$resultado_opcion_jurado_usuario = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_opcion_jurado_usuario);

// Verificar si hay datos en la consulta
if ($resultado_opcion_jurado_usuario->rowCount() > 0) {
    $datos_jurado = $resultado_opcion_jurado_usuario->fetch(PDO::FETCH_ASSOC);

    // Obtener la opción del jurado
     $opcion_jurado = $datos_jurado['opcion_jurado'] ?? null;

    // Validar si la opción es NULL, vacío o "0"
    if ($opcion_jurado === null || trim($opcion_jurado) === "" || $opcion_jurado == "0") {
      
    ?>

    <!-- Modal a pantalla completa -->
    <div id="modal-opcion-jurado" class="modal-opcion-jurado">
        <div class="modal-content-jurado">
            <h2 class="mt-3 mb-2">Selecciona tu Rol</h2>
            <p>Antes de continuar con la evaluación, es necesario que selecciones el rol que desempeñarás en este proceso. Tu elección determinará la configuración de la evaluación y garantizará el correcto registro de la información en el sistema. Por favor, selecciona tu rol para proceder:</p>
            
            <div class="opciones-jurado">
                <div class="opcion" onclick="seleccionarJurado(1)">
                    <img src="<?=SERVERURL?>Views/assets/images/jurado-seleccion.jpg" alt="Jurado 1">
                    <p>Jurado 1</p>
                </div>
                <div class="opcion" onclick="seleccionarJurado(2)">
                <img src="<?=SERVERURL?>Views/assets/images/jurado-seleccion.jpg" alt="Jurado 2">
                    <p>Jurado 2</p>
                </div>
            </div>

            <div class="text-center">
                <p>Rol seleccionado por el usuario: <span class="badge bg-success" id="opcion_jurado_mostrar"></span></p>
            </div>
            <!-- Formulario oculto para enviar la opción seleccionada -->
            <form id="form-opcion-jurado"  class=" mt-2 mb-1 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
                <input type="hidden" name="opcion_jurado" id="opcion_jurado" value="">
                <input type="hidden" name="documento_jurado" id="opcion_jurado" value="<?= $ins_loginControlador->encryption($documento_jurado) ?>">
                <input type="hidden" name="codigo_Proyecto" value="<?= $ins_loginControlador->encryption($codigo_proyecto) ?>">
                <button type="submit" class="btn-enviar">Confirmar selección</button>
            </form>
        </div>
    </div>


    <?php        


    } else {
         "El jurado ha seleccionado la opción: " . htmlspecialchars($opcion_jurado);
    }

} else {
     "No se encontró información para el jurado con documento: $documento_jurado.";
}

/******************************extraer fecha vigencia  ************************************** */

$consultar_fecha_vigencia= "SELECT fecha FROM evaluaciones_proyectos WHERE codigo_proyecto = '$codigo_proyecto'";

$resultado_fecha_vigencia = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_fecha_vigencia);

if ($resultado_fecha_vigencia->rowCount() > 0) {
    $fecha_data = $resultado_fecha_vigencia->fetch(PDO::FETCH_ASSOC);
    $fecha_vigencia = $fecha_data['fecha']; // o el nombre exacto de la columna que contiene la fecha
} else {
  
    $fecha_vigencia = 'Sin definir';
}


?>
<div class="tabla-asesor-metodologico">

<div class="btn-container">
       <div class="text-center">
            <button class="btn-toggle btn-formulario" onclick="toggleSeccion('formulario')">RUBIRCA DE EVALUACIÓN</button>
            <button class="btn-toggle btn-acta" onclick="toggleSeccion('acta')">ACTA PROYECTO</button>
       </div>
    </div>

    <div id="formulario" class="contenedor mostrar">
       
        <table class="container-table-evaluacion">
            <tr>
                <td class="logo-evaluacion" rowspan="3">
                    <img src="<?= SERVERURL ?>Views/assets/images/<?= $nombre_logo ?>" alt="Logo AUNAR">
                </td>
                <td class="header-evaluacion" colspan="1">MACROPROCESO MISIONAL DESARROLLO ACADÉMICO</td>
                <td class="content-evaluacion" colspan="3">Código: <?=$nombre_registro?></td>
            </tr>
            <tr>
                <td class="header-evaluacion" colspan="1">EVALUACIÓN TRABAJOS MODALIDAD DE GRADO CURSO DE INVESTIGACIÓN PREGRADO CIP Y PASANTÍA EMPRESARIAL</td>
                <td class="content-evaluacion " colspan="3">VERSIÓN: 1</td>
            </tr>
            <tr>
                <td class="header-evaluacion" colspan="1">DOCUMENTO CONTROLADO</td>
                <td class="content-evaluacion" colspan="3">Vigencia: <br> <?= $fecha_vigencia?></td>
            </tr>
            <?php
            $colspan1 = 1;
            $colspan2 = 3;

            $meses = [
                "January" => "enero", "February" => "febrero", "March" => "marzo", "April" => "abril",
                "May" => "mayo", "June" => "junio", "July" => "julio", "August" => "agosto",
                "September" => "septiembre", "October" => "octubre", "November" => "noviembre", "December" => "diciembre"
            ];
            
            $fecha_actual = new DateTime();
            $mes = $meses[$fecha_actual->format('F')]; // Traducir el mes al español
            $fecha_formateada = $fecha_actual->format('d') . ' de ' . $mes . ' de ' . $fecha_actual->format('Y');

            ?>
            <!--*********************************************************************************++-->
            <tr>
                <td class="content-evaluacion" colspan="<?= $colspan1 ?>">FECHA:</td>
                <td colspan="<?= $colspan2 ?>" class="text-center"><?=$fecha_formateada?></td>
            </tr>
            <tr>
                <td class="content-evaluacion" colspan="<?= $colspan1 ?>">PROGRAMA ACADÉMICO:</td>
                <td colspan="<?= $colspan2 ?>" class="text-center"><?= $programa_proyecto ?></td>
            </tr>
            <tr>
                <td class="content-evaluacion" colspan="<?= $colspan1 ?>">OPCIÓN DE GRADO:</td>
                <td colspan="<?= $colspan2 ?>" class="text-center"><span class="badge bg-success"><?= $modalidad_registrada ?></span></td>
            </tr>
            <tr>
                <td class="content-evaluacion" colspan="<?= $colspan1 ?>">TÍTULO:</td>
                <td colspan="<?= $colspan2 ?>" class="text-center"><?= $titulo_proyecto ?></td>
            </tr>
            <?php
            if (!empty($estudiantes_info)) {
                $contador = 1; // Para numerar cada estudiante dinámicamente

                foreach ($estudiantes_info as $estudiante) {
                    echo "<tr>";
                    echo "<td class='content-evaluacion' colspan='$colspan1'>ESTUDIANTE $contador: </td>";
                    echo "<td  class='text-center' colspan='$colspan2'>$estudiante</td>";
                    echo "</tr>";

                    $contador++; // Incrementa el número del estudiante
                }
            } else {
                echo "<tr><td colspan='" . ($colspan1 + $colspan2) . "'>No hay estudiantes asignados.</td></tr>";
            }

            ?>

           

            <?php

            if (!empty($directores_info)) {
                $contador = 1; // Para numerar cada asesor dinámicamente

                foreach ($directores_info as $director) {
                    echo "<tr>";
                    echo "<td class='content-evaluacion' colspan='$colspan1'>DIRECTOR $contador:</td>";
                    echo "<td   class='text-center' colspan='$colspan2'>$director</td>";
                    echo "</tr>";

                    $contador++; // Incrementa el número del asesor
                }
            } else {
                echo "<tr><td colspan='" . ($colspan1 + $colspan2) . "'>No hay directores asignados.</td></tr>";
            }


            if (!empty($asesores_info)) {
                $contador = 1; // Para numerar cada asesor dinámicamente

                foreach ($asesores_info as $asesor) {
                    echo "<tr>";
                    echo "<td class='content-evaluacion' colspan='$colspan1'>JURADO $contador:</td>";
                    echo "<td   class='text-center' colspan='$colspan2'>$asesor</td>";
                    echo "</tr>";

                    $contador++; // Incrementa el número del asesor
                }
            } else {
                echo "<tr><td colspan='" . ($colspan1 + $colspan2) . "'>No hay asesores asignados.</td></tr>";
            }


            if (!empty($cordinador_info)) {
                $contador = 1; // Para numerar cada coordinador dinámicamente

                foreach ($cordinador_info as $cordinador) {
                    echo "<tr>";
                    echo "<td class='content-evaluacion' colspan='$colspan1'>PRESIDENTE $contador: </td>";
                    echo "<td  class='text-center' colspan='$colspan2'>$cordinador</td>";
                    echo "</tr>";

                    $contador++; // Incrementa el número del coordinador
                }
            } else {
                echo "<tr><td colspan='" . ($colspan1 + $colspan2) . "'>No hay coordinadores asignados.</td></tr>";
            }


            if (!empty($administradores_info)) {
                $contador = 1; // Para numerar cada administrador dinámicamente

                foreach ($administradores_info as $admin) {
                    echo "<tr>";
                    echo "<td class='content-evaluacion' colspan='$colspan1'>VEEDOR $contador: </td>";
                    echo "<td  class='text-center' colspan='$colspan2'> $admin</td>";
                    echo "</tr>";

                    $contador++; // Incrementa el número del administrador
                }
            } else {
                echo "<tr><td colspan='" . ($colspan1 + $colspan2) . "'>No hay veedor registrados.</td></tr>";
            }


            ?>

        </table>

        <!---------------------------------extraser datos----------------------------------------------------->

        <?php

        $datos_evaluacion_jurados = "SELECT evaluacion_jurado1, evaluacion_jurado2 FROM evaluaciones_proyectos WHERE codigo_proyecto = '$codigo_proyecto'";

        $resultado_evaluacion_jurados = $ins_loginControlador->ejecutar_consultas_simples_two($datos_evaluacion_jurados);

        if ($resultado_evaluacion_jurados->rowCount() > 0) {
            $datos_proyecto = $resultado_evaluacion_jurados->fetch(PDO::FETCH_ASSOC);

            // Decodificar los JSON en arrays
            $evaluacion_jurado1 = json_decode($datos_proyecto['evaluacion_jurado1'], true);
            $evaluacion_jurado2 = json_decode($datos_proyecto['evaluacion_jurado2'], true);

            // Inicializar arreglos por categoría
            $titulo = [];
            $problema = [];
            $justificacion = [];
            $objetivos = [];
            $marco = [];
            $diseno = [];
            $resultados = [];
            $referencias = [];
            $anexos = [];
            $sustentacion = [];

            // Función para organizar los datos en los arreglos correspondientes
            function organizarDatos($evaluacion, &$categoriaArray, $categoria) {
                if (!empty($evaluacion[$categoria])) {
                    foreach ($evaluacion[$categoria] as $id => $datos) {
                        $categoriaArray[$id] = [
                            "calificacion" => $datos['calificacion'],
                            "observacion" => $datos['observacion']
                        ];
                    }
                }
            }

            // Llenar los arreglos con los datos de cada categoría
            organizarDatos($evaluacion_jurado1, $titulo, "titulo");
            organizarDatos($evaluacion_jurado1, $problema, "problema");
            organizarDatos($evaluacion_jurado1, $justificacion, "justificacion");
            organizarDatos($evaluacion_jurado1, $objetivos, "objetivos");
            organizarDatos($evaluacion_jurado1, $marco, "marco");
            organizarDatos($evaluacion_jurado1, $diseno, "diseno");
            organizarDatos($evaluacion_jurado1, $resultados, "resultados");
            organizarDatos($evaluacion_jurado1, $referencias, "referencias");
            organizarDatos($evaluacion_jurado1, $anexos, "anexos");
            organizarDatos($evaluacion_jurado1, $sustentacion, "sustentacion");

            // Si necesitas lo mismo para el jurado 2, puedes duplicar esta sección con un sufijo diferente, por ejemplo:
            $titulo_j2 = [];
            $problema_j2 = [];
            $justificacion_j2 = [];
            $objetivos_j2 = [];
            $marco_j2 = [];
            $diseno_j2 = [];
            $resultados_j2 = [];
            $referencias_j2 = [];
            $anexos_j2 = [];
            $sustentacion_j2 = [];

            organizarDatos($evaluacion_jurado2, $titulo_j2, "titulo");
            organizarDatos($evaluacion_jurado2, $problema_j2, "problema");
            organizarDatos($evaluacion_jurado2, $justificacion_j2, "justificacion");
            organizarDatos($evaluacion_jurado2, $objetivos_j2, "objetivos");
            organizarDatos($evaluacion_jurado2, $marco_j2, "marco");
            organizarDatos($evaluacion_jurado2, $diseno_j2, "diseno");
            organizarDatos($evaluacion_jurado2, $resultados_j2, "resultados");
            organizarDatos($evaluacion_jurado2, $referencias_j2, "referencias");
            organizarDatos($evaluacion_jurado2, $anexos_j2, "anexos");
            organizarDatos($evaluacion_jurado2, $sustentacion_j2, "sustentacion");



        }

        
        ?>
     
        <form class=" mt-2 mb-1 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">

         <input type="hidden" name="codigo_Proyecto" value="<?= $ins_loginControlador->encryption($codigo_proyecto) ?>">


            <?php

          $usuario_juez = $opcion_jurado; // Cambiar a 2 si el usuario es jurado 2

            ?>
            <input type="hidden" name="identificador_jurado_evaluador" value="<?= $ins_loginControlador->encryption($usuario_juez) ?>">
            <table class="container-table-evaluacion">
                <tr class="table-titulo">
                    <td colspan="5" class="text-center">TÍTULO: es el más breve resumen del proyecto</td>
                </tr>
                <tr class="table-aspectos">
                    <td>Aspectos</td>
                    <td>JURADO 1</td>
                    <td>OBSERVACIÓN</td>
                    <td>JURADO 2</td>
                    <td>OBSERVACIÓN</td>
                </tr>
                <?php
                $aspectos_titulo = [
                    "1" => "Responde a tres o más de los siguientes interrogantes: <span style='color:red;'>¿qué?</span>, ¿sobre qué?, ¿dónde?, ¿cómo?, ¿cuándo?",
                    "2" => "Proporciona una idea global completa de la investigación a realizar",
                    "3" => "Es claro y conciso"
                ];

                   
                if($modalidad == 2 ||  $modalidad == 3){

                    $opciones = [0, 30, 40, 50, 60, 70, 80, 90, 94];

                }else if ( $modalidad == 1){

                    $opciones = [0, 30, 40, 50, 60, 70, 80, 90, 100];

                }else{
                    $opciones = [0, 30, 40, 50, 60, 70, 80, 90, 94];
                }

                foreach ($aspectos_titulo as $id => $aspecto) {
                    echo "<tr>";
                    echo "<td>$aspecto</td>";
                
                    // Obtener valores guardados del jurado 1
                    $calificacion_j1 = isset($titulo[$id]['calificacion']) ? $titulo[$id]['calificacion'] : "";
                    $observacion_j1 = isset($titulo[$id]['observacion']) ? $titulo[$id]['observacion'] : "";
                
                    // Obtener valores guardados del jurado 2
                    $calificacion_j2 = isset($titulo_j2[$id]['calificacion']) ? $titulo_j2[$id]['calificacion'] : "";
                    $observacion_j2 = isset($titulo_j2[$id]['observacion']) ? $titulo_j2[$id]['observacion'] : "";
                
                    // Control de edición dinámico según el usuario
                    $class_jurado1 = ($usuario_juez == 2) ? "class='disabled-field' disabled" : "class='active-field'";
                    $class_jurado2 = ($usuario_juez == 1) ? "class='disabled-field' disabled" : "class='active-field'";
                
                    // Campo de calificación y observación para el jurado 1
                    echo "<td><select name='item_titulo_$id' $class_jurado1>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j1) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_titulo_$id' $class_jurado1>$observacion_j1</textarea></td>";
                
                    // Campo de calificación y observación para el jurado 2
                    echo "<td><select name='item_titulo_$id' $class_jurado2>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j2) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_titulo_$id' $class_jurado2>$observacion_j2</textarea></td>";
                
                    echo "</tr>";
                }
                
                
                ?>
            </table>

            <table class="container-table-evaluacion"> <!--problema-->
                <tr class="table-titulo">
                    <td colspan="5" class="text-center">PROBLEMA DE INVESTIGACIÓN: responde a la pregunta ¿qué se va a investigar?</td>
                </tr>
                <tr class="table-aspectos">
                    <td>Aspectos</td>
                    <td>JURADO 1</td>
                    <td>OBSERVACIÓN</td>
                    <td>JURADO 2</td>
                    <td>OBSERVACIÓN</td>
                </tr>
                <?php
                $aspectos_problema = [
                    "1" => "Describe los síntomas o problemas identificados por el investigador",
                    "2" => "Identifica las causas que generan los síntomas o problemas definidos.",
                    "3" => "Evidencia la probabilidad de ocurrencia de las consecuencias, si el problema persiste",
                    "4" => "Se evidencian los antecedentes que dan contexto a la problemática identificada",
                    "5" => "Identifica las alternativas de control al pronóstico.",
                    "6" => "Se presenta en forma de pregunta",
                    "7" => "Está relacionada con la pertinencia del problema."
                ];


                foreach ($aspectos_problema as $id => $aspecto) {
                    echo "<tr>";
                    echo "<td>$aspecto</td>";
                
                    // Obtener valores guardados del jurado 1
                    $calificacion_j1 = isset($problema[$id]['calificacion']) ? $problema[$id]['calificacion'] : "";
                    $observacion_j1 = isset($problema[$id]['observacion']) ? $problema[$id]['observacion'] : "";
                
                    // Obtener valores guardados del jurado 2
                    $calificacion_j2 = isset($problema_j2[$id]['calificacion']) ? $problema_j2[$id]['calificacion'] : "";
                    $observacion_j2 = isset($problema_j2[$id]['observacion']) ? $problema_j2[$id]['observacion'] : "";
                
                    // Control de edición dinámico según el usuario
                    $class_jurado1 = ($usuario_juez == 2) ? "class='disabled-field' disabled" : "class='active-field'";
                    $class_jurado2 = ($usuario_juez == 1) ? "class='disabled-field' disabled" : "class='active-field'";
                
                    // Campo de calificación y observación para el jurado 1
                    echo "<td><select name='item_problema_$id' $class_jurado1>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j1) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_problema_$id' $class_jurado1>$observacion_j1</textarea></td>";
                
                    // Campo de calificación y observación para el jurado 2
                    echo "<td><select name='item_problema_$id' $class_jurado2>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j2) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_problema_$id' $class_jurado2>$observacion_j2</textarea></td>";
                
                    echo "</tr>";
                }
                
                ?>
            </table>

            <table class="container-table-evaluacion"> <!--justificacion-->
                <tr class="table-titulo">
                    <td colspan="5" class="text-center">JUSTIFICACIÓN: responde a la pregunta ¿por qué hay que hacer la investigación?</td>
                </tr>
                <tr class="table-aspectos">
                    <td>Aspectos</td>
                    <td>JURADO 1</td>
                    <td>OBSERVACIÓN</td>
                    <td>JURADO 2</td>
                    <td>OBSERVACIÓN</td>
                </tr>
                <?php
                $aspectos_justificacion = [
                    "1" => "Indica el grado de pertinencia de la investigación",
                    "2" => "Presenta el grado de novedad que tiene la investigación, o su valor teórico y científico-técnico; así como la relevancia para la(s) disciplina(s) que confluyen en el proyecto",
                    "3" => "Presenta las motivaciones (teóricas, metodológicas o prácticas) que llevan al desarrollo del proyecto."
                ];


                foreach ($aspectos_justificacion as $id => $aspecto) {
                    echo "<tr>";
                    echo "<td>$aspecto</td>";
                
                    // Obtener valores guardados del jurado 1
                    $calificacion_j1 = isset($justificacion[$id]['calificacion']) ? $justificacion[$id]['calificacion'] : "";
                    $observacion_j1 = isset($justificacion[$id]['observacion']) ? $justificacion[$id]['observacion'] : "";
                
                    // Obtener valores guardados del jurado 2
                    $calificacion_j2 = isset($justificacion_j2[$id]['calificacion']) ? $justificacion_j2[$id]['calificacion'] : "";
                    $observacion_j2 = isset($justificacion_j2[$id]['observacion']) ? $justificacion_j2[$id]['observacion'] : "";
                
                    // Control de edición dinámico según el usuario
                    $class_jurado1 = ($usuario_juez == 2) ? "class='disabled-field' disabled" : "class='active-field'";
                    $class_jurado2 = ($usuario_juez == 1) ? "class='disabled-field' disabled" : "class='active-field'";
                
                    // Campo de calificación y observación para el jurado 1
                    echo "<td><select name='item_justificacion_$id' $class_jurado1>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j1) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_justificacion_$id' $class_jurado1>$observacion_j1</textarea></td>";
                
                    // Campo de calificación y observación para el jurado 2
                    echo "<td><select name='item_justificacion_$id' $class_jurado2>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j2) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_justificacion_$id' $class_jurado2>$observacion_j2</textarea></td>";
                
                    echo "</tr>";
                }
                
                ?>
            </table>

            <table class="container-table-evaluacion"> <!--objetivos-->
                <tr class="table-titulo">
                    <td colspan="5" class="text-center">OBJETIVOS: responden a la pregunta ¿qué se propone lograr con la investigación?</td>
                </tr>
                <tr class="table-aspectos">
                    <td>Aspectos</td>
                    <td>JURADO 1</td>
                    <td>OBSERVACIÓN</td>
                    <td>JURADO 2</td>
                    <td>OBSERVACIÓN</td>
                </tr>
                <?php

                if($modalidad == 2 || $modalidad == 2){

                    $aspectos_objetivos = [
                        "1" => "Guardan coherencia con el problema planteado (¿al cumplir estos objetivos se transforma el estado actual del problema y se logra llegar al estado deseado?)",
                        "2" => "Factibles de alcanzar en el horizonte de tiempo trazado para la investigación",
                        "3" => "Indican en forma clara y directa las acciones a realizar",
                        "4" => "Cumple parámetros de escritura (Verbo en infinitivo, en tercera persona)",
                        "5" => "Es medible, verificable, específico, cuantificable, definido en el tiempo y define el alcance con respecto a la competencia del investigador.",
                        "6" => "Está relacionado con la formulación del problema.",
                        "7" => "Los objetivos específicos señalan los resultados o metas parciales que facilitan el logro del objetivo general."
                    ];

                }else if ($modalidad ==1){
                    $aspectos_objetivos = [
                        "1" => "Guardan coherencia con el problema planteado (¿al cumplir estos objetivos se transforma el estado actual del problema y se logra llegar al estado deseado?)",
                        "2" => "Factibles de alcanzar en el horizonte de tiempo trazado para la investigación",
                        "3" => "Indican en forma clara y directa las acciones a realizar",
                        "4" => "Los objetivos contribuyen a la comprobación de la hipotesis",
                        "5" => "Cumple parámetros de escritura (Verbo en infinitivo, en tercera persona)",
                        "6" => "Es medible, verificable, específico, cuantificable, definido en el tiempo y define el alcance con respecto a la competencia del investigador.",
                        "7" => "Está relacionado con la formulación del problema.",
                        "8" => "Los objetivos específicos señalan los resultados o metas parciales que facilitan el logro del objetivo general."
                    ];
                }


                


                foreach ($aspectos_objetivos as $id => $aspecto) {
                    echo "<tr>";
                    echo "<td>$aspecto</td>";
                
                    // Obtener valores guardados del jurado 1
                    $calificacion_j1 = isset($objetivos[$id]['calificacion']) ? $objetivos[$id]['calificacion'] : "";
                    $observacion_j1 = isset($objetivos[$id]['observacion']) ? $objetivos[$id]['observacion'] : "";
                
                    // Obtener valores guardados del jurado 2
                    $calificacion_j2 = isset($objetivos_j2[$id]['calificacion']) ? $objetivos_j2[$id]['calificacion'] : "";
                    $observacion_j2 = isset($objetivos_j2[$id]['observacion']) ? $objetivos_j2[$id]['observacion'] : "";
                
                    // Control de edición dinámico según el usuario
                    $class_jurado1 = ($usuario_juez == 2) ? "class='disabled-field' disabled" : "class='active-field'";
                    $class_jurado2 = ($usuario_juez == 1) ? "class='disabled-field' disabled" : "class='active-field'";
                
                    // Campo de calificación y observación para el jurado 1
                    echo "<td><select name='item_objetivos_$id' $class_jurado1>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j1) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_objetivos_$id' $class_jurado1>$observacion_j1</textarea></td>";
                
                    // Campo de calificación y observación para el jurado 2
                    echo "<td><select name='item_objetivos_$id' $class_jurado2>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j2) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_objetivos_$id' $class_jurado2>$observacion_j2</textarea></td>";
                
                    echo "</tr>";
                }
                
                ?>
            </table>


            <table class="container-table-evaluacion"> <!--MARCO DE REFERENCIA (ANTECEDENTES, MARCO TEÓRICO, MARCO CONCEPTUAL, MARCO GEOGRÁFICO, MARCO LEGAL)-->
                <tr class="table-titulo">
                    <td colspan="5" class="text-center">MARCO DE REFERENCIA (ANTECEDENTES, MARCO TEÓRICO, MARCO CONCEPTUAL, MARCO GEOGRÁFICO, MARCO LEGAL)</td>
                </tr>
                <tr class="table-aspectos">
                    <td>Aspectos</td>
                    <td>JURADO 1</td>
                    <td>OBSERVACIÓN</td>
                    <td>JURADO 2</td>
                    <td>OBSERVACIÓN</td>
                </tr>
                <?php
                $aspectos_marco_referencia = [
                    "1" => "Los Antecedentes, presentan una revisión detallada de la literatura especializada.",
                    "2" => "El Marco teórico permite delimitar el área de la investigación, a partir de teorías que dan respuesta al problema formulado.",
                    "3" => "El Marco teórico expresa proposiciones teóricas, generales, postulados y marcos de referencia que permiten la formulación de hipótesis, operacionalización de variables y procedimientos a seguir.",
                    "4" => "El Marco Teórico, emplea diversidad de fuentes de información.",
                    "5" => "El Marco Conceptual, guarda relación directa con el proyecto desarrollado.",
                    "6" => "El Marco Geográfico, delimita la zona geográfica a nivel departamental, regional y local donde se realizará el proyecto.",
                    "7" => "El Marco Legal, relaciona las leyes, decretos, resoluciones y normas que se relacionan con el objeto del proyecto.",
                    "8" => "Su presentación evidencia el grado de profundidad temática que soporta la investigación.",
                    "9" => "Facilita la identificación de términos específicos a ser utilizados en el desarrollo de la investigación."
                ];


                foreach ($aspectos_marco_referencia as $id => $aspecto) {
                    echo "<tr>";
                    echo "<td>$aspecto</td>";
                
                    // Obtener valores guardados del jurado 1
                    $calificacion_j1 = isset($marco[$id]['calificacion']) ? $marco[$id]['calificacion'] : "";
                    $observacion_j1 = isset($marco[$id]['observacion']) ? $marco[$id]['observacion'] : "";
                
                    // Obtener valores guardados del jurado 2
                    $calificacion_j2 = isset($marco_j2[$id]['calificacion']) ? $marco_j2[$id]['calificacion'] : "";
                    $observacion_j2 = isset($marco_j2[$id]['observacion']) ? $marco_j2[$id]['observacion'] : "";
                
                    // Control de edición dinámico según el usuario
                    $class_jurado1 = ($usuario_juez == 2) ? "class='disabled-field' disabled" : "class='active-field'";
                    $class_jurado2 = ($usuario_juez == 1) ? "class='disabled-field' disabled" : "class='active-field'";
                
                    // Campo de calificación y observación para el jurado 1
                    echo "<td><select name='item_marco_$id' $class_jurado1>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j1) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_marco_$id' $class_jurado1>$observacion_j1</textarea></td>";
                
                    // Campo de calificación y observación para el jurado 2
                    echo "<td><select name='item_marco_$id' $class_jurado2>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j2) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_marco_$id' $class_jurado2>$observacion_j2</textarea></td>";
                
                    echo "</tr>";
                }
                
                ?>
            </table>

            <table class="container-table-evaluacion"> <!--DISEÑO METODOLÓGICO,-->
                <tr class="table-titulo">
                    <td colspan="5">DISEÑO METODOLÓGICO: responde a la pregunta ¿cómo se realizará la investigación?</td>
                </tr>
                <tr class="table-aspectos">
                    <td>Aspectos</td>
                    <td>JURADO 1</td>
                    <td>OBSERVACIÓN</td>
                    <td>JURADO 2</td>
                    <td>OBSERVACIÓN</td>
                </tr>
                <?php

                if($modalidad == 2 || $modalidad == 2){

                    $aspectos_diseno_metodologico = [
                        "1" => "El tipo de investigación está acorde con los objetivos que se pretenden alcanzar.",
                        "2" => "Contempla las estrategias, procedimientos, actividades y medios requeridos para cumplir los objetivos propuestos y dar respuesta al problema planteado.",
                        "3" => "La investigación está ajustada a las líneas de investigación del programa.",
                        "4" => "Describe el tipo de investigación a desarrollarse.",
                        "5" => "La población objeto de estudio es la adecuada de acuerdo al objetivo de la investigación.",
                        "6" => "Las técnicas de recolección de información son acordes con los objetivos que se pretenden alcanzar.",
                        "7" => "El tamaño de la muestra está determinado de manera estadística."
                    ];

                }else if($modalidad == 1){

                    $aspectos_diseno_metodologico = [
                        "1" =>  "El tipo de investigación está acorde con los objetivos que se pretenden alcanzar.",
                        "2" =>  "Contempla las estrategias, procedimientos, actividades y medios requeridos para cumplir los objetivos propuestos y dar respuesta al problema planteado.",
                        "3" => "Las hipótesis y sus variables están formuladas adecuadamente.",
                        "4" => "La investigación está ajustada a las líneas de investigación del programa.",
                        "5" => "Describe el tipo de investigación a desarrollarse.",
                        "6" =>  "La población objeto de estudio es la adecuada de acuerdo al objetivo de la investigación.",
                        "7" => "Las técnicas de recolección de información son acordes con los objetivos que se pretenden alcanzar.",
                        "8" => "El tamaño de la muestra está determinada de manera estadística."
                    ];

                }

               



                foreach ($aspectos_diseno_metodologico as $id => $aspecto) {
                    echo "<tr>";
                    echo "<td>$aspecto</td>";
                
                    // Obtener valores guardados del jurado 1
                    $calificacion_j1 = isset($diseno[$id]['calificacion']) ? $diseno[$id]['calificacion'] : "";
                    $observacion_j1 = isset($diseno[$id]['observacion']) ? $diseno[$id]['observacion'] : "";
                
                    // Obtener valores guardados del jurado 2
                    $calificacion_j2 = isset($diseno_j2[$id]['calificacion']) ? $diseno_j2[$id]['calificacion'] : "";
                    $observacion_j2 = isset($diseno_j2[$id]['observacion']) ? $diseno_j2[$id]['observacion'] : "";
                
                    // Control de edición dinámico según el usuario
                    $class_jurado1 = ($usuario_juez == 2) ? "class='disabled-field' disabled" : "class='active-field'";
                    $class_jurado2 = ($usuario_juez == 1) ? "class='disabled-field' disabled" : "class='active-field'";
                
                    // Campo de calificación y observación para el jurado 1
                    echo "<td><select name='item_diseno_$id' $class_jurado1>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j1) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_diseno_$id' $class_jurado1>$observacion_j1</textarea></td>";
                
                    // Campo de calificación y observación para el jurado 2
                    echo "<td><select name='item_diseno_$id' $class_jurado2>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j2) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_diseno_$id' $class_jurado2>$observacion_j2</textarea></td>";
                
                    echo "</tr>";
                }
                
                ?>
            </table>

            <table class="container-table-evaluacion"> <!--INFORME DE RESULTADOS,-->
                <tr class="table-titulo">
                    <td colspan="5">INFORME DE RESULTADOS</td>
                </tr>
                <tr class="table-aspectos">
                    <td>Aspectos</td>
                    <td>JURADO 1</td>
                    <td>OBSERVACIÓN</td>
                    <td>JURADO 2</td>
                    <td>OBSERVACIÓN</td>
                </tr>
                <?php
                $aspectos_informe_resultados = [
                    "1" => "Los resultados permiten identificar claramente el desarrollo de los objetivos específicos.",
                    "2" => "Los resultados presentan de manera clara, precisa y conveniente el desarrollo de los objetivos específicos propuestos.",
                    "3" => "Los resultados dan respuesta a la problemática planteada en la investigación.",
                    "4" => "Los instrumentos para la recolección de la información son adecuados y pertinentes para el desarrollo de los objetivos planteados.",
                    "5" => "La prueba de hipótesis se realiza mediante procesos estadísticos o teóricos derivados de los resultados de la investigación.",
                    "6" => "Las conclusiones presentan de forma objetiva, lógica, coherente y ordenada los resultados de la investigación."
                ];


                foreach ($aspectos_informe_resultados as $id => $aspecto) {
                    echo "<tr>";
                    echo "<td>$aspecto</td>";
                
                    // Obtener valores guardados del jurado 1
                    $calificacion_j1 = isset($resultados[$id]['calificacion']) ? $resultados[$id]['calificacion'] : "";
                    $observacion_j1 = isset($resultados[$id]['observacion']) ? $resultados[$id]['observacion'] : "";
                
                    // Obtener valores guardados del jurado 2
                    $calificacion_j2 = isset($resultados_j2[$id]['calificacion']) ? $resultados_j2[$id]['calificacion'] : "";
                    $observacion_j2 = isset($resultados_j2[$id]['observacion']) ? $resultados_j2[$id]['observacion'] : "";
                
                    // Control de edición dinámico según el usuario
                    $class_jurado1 = ($usuario_juez == 2) ? "class='disabled-field' disabled" : "class='active-field'";
                    $class_jurado2 = ($usuario_juez == 1) ? "class='disabled-field' disabled" : "class='active-field'";
                
                    // Campo de calificación y observación para el jurado 1
                    echo "<td><select name='item_resultados_$id' $class_jurado1>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j1) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_resultados_$id' $class_jurado1>$observacion_j1</textarea></td>";
                
                    // Campo de calificación y observación para el jurado 2
                    echo "<td><select name='item_resultados_$id' $class_jurado2>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j2) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_resultados_$id' $class_jurado2>$observacion_j2</textarea></td>";
                
                    echo "</tr>";
                }
                
                ?>
            </table>

            <table class="container-table-evaluacion"> <!--REFERENCIAS-->
                <tr class="table-titulo">
                    <td colspan="5">REFERENCIAS BIBLIOGRÁFICAS: responde a la pregunta ¿qué documentación se consultó para la formulación del proyecto?</td>
                </tr>
                <tr class="table-aspectos">
                    <td>Aspectos</td>
                    <td>JURADO 1</td>
                    <td>OBSERVACIÓN</td>
                    <td>JURADO 2</td>
                    <td>OBSERVACIÓN</td>
                </tr>
                <?php
                $aspectos_referencias = [
                    "1" => "La revisión de literatura mencionada es suficiente.",
                    "2" => "Hay literatura consultada en un segundo idioma y de los últimos 5 años.",
                    "3" => "Todas las fuentes de información referenciadas están en la 'literatura citada'.",
                    "4" => "Las referencias se citan de acuerdo con la norma APA última versión.",
                    "5" => "La literatura usada cumple con el número mínimo (mínimo 20 referencias bibliográficas).",
                    "6" => "Se está referenciando investigaciones y/o artículos de revista en donde los autores pertenezcan a la Corporación Universitaria Autónoma de Nariño.",
                    "7" => "Se aplica debidamente la norma respectiva para referenciar (APA)."
                ];


                foreach ($aspectos_referencias as $id => $aspecto) {
                    echo "<tr>";
                    echo "<td>$aspecto</td>";
                
                    // Obtener valores guardados del jurado 1
                    $calificacion_j1 = isset($referencias[$id]['calificacion']) ? $referencias[$id]['calificacion'] : "";
                    $observacion_j1 = isset($referencias[$id]['observacion']) ? $referencias[$id]['observacion'] : "";
                
                    // Obtener valores guardados del jurado 2
                    $calificacion_j2 = isset($referencias_j2[$id]['calificacion']) ? $referencias_j2[$id]['calificacion'] : "";
                    $observacion_j2 = isset($referencias_j2[$id]['observacion']) ? $referencias_j2[$id]['observacion'] : "";
                
                    // Control de edición dinámico según el usuario
                    $class_jurado1 = ($usuario_juez == 2) ? "class='disabled-field' disabled" : "class='active-field'";
                    $class_jurado2 = ($usuario_juez == 1) ? "class='disabled-field' disabled" : "class='active-field'";
                
                    // Campo de calificación y observación para el jurado 1
                    echo "<td><select name='item_referencias_$id' $class_jurado1>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j1) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_referencias_$id' $class_jurado1>$observacion_j1</textarea></td>";
                
                    // Campo de calificación y observación para el jurado 2
                    echo "<td><select name='item_referencias_$id' $class_jurado2>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j2) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_referencias_$id' $class_jurado2>$observacion_j2</textarea></td>";
                
                    echo "</tr>";
                }
                
                ?>
            </table>


            <table class="container-table-evaluacion"> <!--ANEXOS-->
                <tr class="table-titulo">
                    <td colspan="5">ANEXOS: corresponden a la información que soporta o complementa el proyecto formulado</td>
                </tr>
                <tr class="table-aspectos">
                    <td>Aspectos</td>
                    <td>JURADO 1</td>
                    <td>OBSERVACIÓN</td>
                    <td>JURADO 2</td>
                    <td>OBSERVACIÓN</td>
                </tr>
                <?php
                $aspectos_anexos = [
                    "1" => "Se encuentran debidamente identificados con letras o números según corresponda.",
                    "2" => "Indican la fuente respectiva."
                ];

                foreach ($aspectos_anexos as $id => $aspecto) {
                    echo "<tr>";
                    echo "<td>$aspecto</td>";
                
                    // Obtener valores guardados del jurado 1
                    $calificacion_j1 = isset($anexos[$id]['calificacion']) ? $anexos[$id]['calificacion'] : "";
                    $observacion_j1 = isset($anexos[$id]['observacion']) ? $anexos[$id]['observacion'] : "";
                
                    // Obtener valores guardados del jurado 2
                    $calificacion_j2 = isset($anexos_j2[$id]['calificacion']) ? $anexos_j2[$id]['calificacion'] : "";
                    $observacion_j2 = isset($anexos_j2[$id]['observacion']) ? $anexos_j2[$id]['observacion'] : "";
                
                    // Control de edición dinámico según el usuario
                    $class_jurado1 = ($usuario_juez == 2) ? "class='disabled-field' disabled" : "class='active-field'";
                    $class_jurado2 = ($usuario_juez == 1) ? "class='disabled-field' disabled" : "class='active-field'";
                
                    // Campo de calificación y observación para el jurado 1
                    echo "<td><select name='item_anexos_$id' $class_jurado1>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j1) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_anexos_$id' $class_jurado1>$observacion_j1</textarea></td>";
                
                    // Campo de calificación y observación para el jurado 2
                    echo "<td><select name='item_anexos_$id' $class_jurado2>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j2) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_anexos_$id' $class_jurado2>$observacion_j2</textarea></td>";
                
                    echo "</tr>";
                }
                
                ?>
            </table>


            <table class="container-table-evaluacion"> <!--SUSTENTACIÓN-->
                <tr class="table-titulo">
                    <td colspan="5">SUSTENTACIÓN</td>
                </tr>
                <tr class="table-aspectos">
                    <td>Aspectos</td>
                    <td>JURADO 1</td>
                    <td>OBSERVACIÓN</td>
                    <td>JURADO 2</td>
                    <td>OBSERVACIÓN</td>
                </tr>
                <?php
                $aspectos_sustentacion = [
                    "1" => "El personal está preparado para desarrollar la actividad.",
                    "2" => "Su presentación personal es adecuada.",
                    "3" => "Asume el rol asignado para la presentación.",
                    "4" => "Desarrolla las ayudas educativas requeridas.",
                    "5" => "Usa de manera adecuada las ayudas educativas.",
                    "6" => "Da inicio según lo requerido (saludo, presentación individual, presentación del tema, de objetivos y duración de la actividad).",
                    "7" => "Expresa los contenidos con claridad y de manera concreta.",
                    "8" => "Transmite la información en secuencia lógica y ordenada.",
                    "9" => "Mantiene la dirección de la temática sin perder el objetivo.",
                    "10" => "Usa un tono de voz adecuado.",
                    "11" => "Responde de manera acertada y asertiva a las inquietudes del auditorio.",
                    "12" => "Fomenta relación de empatía con el jurado.",
                    "13" => "Mantiene el control del auditorio (realiza desplazamientos en la locación, que fomenten la participación positiva del auditorio).",
                    "14" => "Da cierre a la actividad emitiendo, conclusiones, apreciaciones o resumen de los aspectos más relevantes de la actividad.",
                    "15" => "La actividad se realiza en el horario establecido."
                ];


                foreach ($aspectos_sustentacion as $id => $aspecto) {
                    echo "<tr>";
                    echo "<td>$aspecto</td>";
                
                    // Obtener valores guardados del jurado 1
                    $calificacion_j1 = isset($sustentacion[$id]['calificacion']) ? $sustentacion[$id]['calificacion'] : "";
                    $observacion_j1 = isset($sustentacion[$id]['observacion']) ? $sustentacion[$id]['observacion'] : "";
                
                    // Obtener valores guardados del jurado 2
                    $calificacion_j2 = isset($sustentacion_j2[$id]['calificacion']) ? $sustentacion_j2[$id]['calificacion'] : "";
                    $observacion_j2 = isset($sustentacion_j2[$id]['observacion']) ? $sustentacion_j2[$id]['observacion'] : "";
                
                    // Control de edición dinámico según el usuario
                    $class_jurado1 = ($usuario_juez == 2) ? "class='disabled-field' disabled" : "class='active-field'";
                    $class_jurado2 = ($usuario_juez == 1) ? "class='disabled-field' disabled" : "class='active-field'";
                
                    // Campo de calificación y observación para el jurado 1
                    echo "<td><select name='item_sustentacion_$id' $class_jurado1>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j1) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_sustentacion_$id' $class_jurado1>$observacion_j1</textarea></td>";
                
                    // Campo de calificación y observación para el jurado 2
                    echo "<td><select name='item_sustentacion_$id' $class_jurado2>";
                    foreach ($opciones as $opcion) {
                        $selected = ($opcion == $calificacion_j2) ? "selected" : "";
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    echo "</select></td>";
                    echo "<td><textarea name='observacion_sustentacion_$id' $class_jurado2>$observacion_j2</textarea></td>";
                
                    echo "</tr>";
                }
                
                ?>
            </table>


            <?php if (isset($_SESSION['privilegio']) && $_SESSION['privilegio'] == 5): ?>
                <?php
                // Asegúrate de que $fecha_vigencia tenga formato 'Y-m-d'
                $fecha_actual = date('Y-m-d');

                if ($fecha_vigencia !== "") {
                    if ($fecha_actual === $fecha_vigencia) {
                        ?>
                        <div class="text-center mt-4 mb-3">
                            <button class="boton-flotante-formulariosss" type="submit">Enviar datos</button>
                        </div>
                        <?php
                    } else {

                        echo ' <div class="alert alert-info alertas-ms " role="alert">
                        <div class="text-center">🚫 El botón se habilitará el día: <strong>' . $fecha_vigencia . '</strong> </div>
                        </div>';
                      
                    }
                } else {
                    
                    echo ' <div class="alert alert-info alertas-ms " role="alert">
                    <div class="text-center">⚠️ Aún no se ha programado la fecha de sustentación. </div>
                    </div>';
                  
                }
                ?>

               
            <?php endif; ?>


        </form>
    </div>

    <div id="acta" class="contenedor">
    <button onclick="descargarPDF()">Descargar sección en PDF</button>
        <table class="container-table-evaluacion">
            <tr>
                <td class="logo-evaluacion" rowspan="3">
                    <img src="<?= SERVERURL ?>Views/assets/images/<?= $nombre_logo ?>" alt="Logo AUNAR">
                </td>
                <td class="header-evaluacion" colspan="1">MACROPROCESO MISIONAL DESARROLLO ACADÉMICO</td>
                <td class="content-evaluacion" colspan="3">Código: FR-DA-GDE-0059</td>
            </tr>
            <tr>
                <td class="header-evaluacion" colspan="1">EVALUACIÓN TRABAJOS MODALIDAD DE GRADO CURSO DE INVESTIGACIÓN PREGRADO CIP Y PASANTÍA EMPRESARIAL</td>
                <td class="content-evaluacion " colspan="3">VERSIÓN: 1</td>
            </tr>
            <tr>
                <td class="header-evaluacion" colspan="1">DOCUMENTO CONTROLADO</td>
                <td class="content-evaluacion" colspan="3">Vigencia: <br><?= $fecha_vigencia?></td>
            </tr>
        
        </table>

        <p class="acta-title">
            Establecidos y cumplidos los requerimientos de la Corporación Universitaria Autónoma de Nariño (Acuerdo 20), se hace el reporte del ACTA DE CALIFICACION (Cualitativa y Cuantitativa) Y APROBACION de la modalidad de grado, establecida como parte del cumplimiento de requisitos al optar a titulación de acuerdo al registro de inscripción y matrícula de estudiantes al Programa de Educación Superior en la institución.
        </p>
    
        <table class="acta-table">
            <tr>
                <td class="text-center content-evaluacion">PROGRAMA:</td>
                <td><?= $programa_proyecto ?></td>
                <td class="text-center content-evaluacion">REGISTRO CALIFICADO:</td>
                <td><?=$nombre_registro?></td>
                <td class="text-center content-evaluacion">FECHA:</td>
                <td><?=$fecha_formateada?></td>
            </tr>
            <tr>
            <?php

           $consulta_resumen = "SELECT resumen_general FROM evaluaciones_proyectos WHERE codigo_proyecto = '$codigo_proyecto'";

           $resultado_consulta_resumen = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_resumen);
           
           if ($resultado_consulta_resumen->rowCount() > 0) {
               $fila_resumen = $resultado_consulta_resumen->fetch(PDO::FETCH_ASSOC);
               $resumen_general = $fila_resumen['resumen_general'];
               $borde = "border: 2px solid rgb(89, 255, 0); ";
           
           } else {
              $resumen_general = 0;
              $borde = "border: 2px solid rgb(255, 34, 0); ";
           }
           
           
            
            ?>
            <form class=" mt-2 mb-1 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
                <td class="text-center content-evaluacion">ACTA CALIFICACIÓN N°:</td>
                <td colspan="5"><input type="text" name="number_acta" style="<?=$borde?>" value ="<?=$resumen_general?>" class="campo_actas text-center">
                    <input type="hidden" name="codigo_Proyecto" value="<?= $ins_loginControlador->encryption($codigo_proyecto) ?>">
                </td>
                <?php 
                if($_SESSION['privilegio'] == 1){
                    ?>
                     <button type="submit">Registrar Acta calificacion</button>
                    <?php
                }
                ?>
               
            </form>
            </tr>
            <tr>
                <td class="text-center content-evaluacion">TÍTULO DEL PROYECTO:</td>
                <td colspan="5"><?= $titulo_proyecto ?></td>
            </tr>
            <?php
            if (!empty($estudiantes_info)) {
                $contador = 1; // Para numerar cada estudiante dinámicamente

                foreach ($estudiantes_info as $estudiante) {
                    echo "<tr>";
                    echo "<td class='content-evaluacion' colspan='$colspan1'>ESTUDIANTE $contador: </td>";
                    echo "<td  class='text-center' colspan='6'>$estudiante</td>";
                    echo "</tr>";
                   
                    

                    $contador++; // Incrementa el número del estudiante
                }
            } else {
                echo "<tr><td colspan='" . ($colspan1 + $colspan2) . "'>No hay estudiantes asignados.</td></tr>";
            }

            
            if (!empty($directores_info)) {
                $contador = 1; // Para numerar cada asesor dinámicamente

                foreach ($directores_info as $director) {
                    echo "<tr>";
                    echo "<td class='content-evaluacion' colspan='$colspan1'>DIRECTOR $contador:</td>";
                    echo "<td   class='text-center' colspan='5'>$director</td>";
                    echo "</tr>";

                    $contador++; // Incrementa el número del asesor
                }
            } else {
                echo "<tr><td colspan='" . ($colspan1 + $colspan2) . "'>No hay directores asignados.</td></tr>";
            }

            ?>
           
            <tr>
                <td colspan="6" class="acta-header content-evaluacion">EVALUADORES DEL PROYECTO DE GRADO</td>
            </tr>
            <?php
            if (!empty($asesores_info)) {
                $contador = 1; // Para numerar cada asesor dinámicamente

                foreach ($asesores_info as $asesor) {
                    echo "<tr>";
                    echo "<td class='content-evaluacion' colspan='$colspan1'>JURADO $contador:</td>";
                    echo "<td   class='text-center' colspan='6'>$asesor</td>";
                    echo "</tr>";

                    $contador++; // Incrementa el número del asesor
                }
            } else {
                echo "<tr><td colspan='" . ($colspan1 + $colspan2) . "'>No hay asesores asignados.</td></tr>";
            }

            
            if (!empty($administradores_info)) {
                $contador = 1; // Para numerar cada administrador dinámicamente

                foreach ($administradores_info as $admin) {
                    echo "<tr>";
                    echo "<td class='content-evaluacion' colspan='$colspan1'>VEEDOR $contador: </td>";
                    echo "<td  class='text-center' colspan='6'> $admin</td>";
                    echo "</tr>";

                    $contador++; // Incrementa el número del administrador
                }
            } else {
                echo "<tr><td colspan='" . ($colspan1 + $colspan2) . "'>No hay veedor registrados.</td></tr>";
            }

            if (!empty($cordinador_info)) {
                $contador = 1; // Para numerar cada coordinador dinámicamente

                foreach ($cordinador_info as $cordinador) {
                    echo "<tr>";
                    echo "<td class='content-evaluacion' colspan='$colspan1'>PRESIDENTE $contador: </td>";
                    echo "<td  class='text-center' colspan='6'>$cordinador</td>";
                    echo "</tr>";

                    $contador++; // Incrementa el número del coordinador
                }
            } else {
                echo "<tr><td colspan='" . ($colspan1 + $colspan2) . "'>No hay coordinadores asignados.</td></tr>";
            }

            ?>
            
        </table>

        <table class="acta-table">
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">VARIABLES E INDICADORES</th>
                <th rowspan="2">NÚMERO DE CRITERIOS VALORADOS</th>
                <th rowspan="2">PESO CRITERIO</th>
                <th colspan="2">CALIFICACIÓN</th>
            </tr>
            <tr>
                <th>JURADO 1</th>
                <th>JURADO 2</th>
            </tr>

            <?php
            // Arreglos individuales para cada tipo de datos
            $variables = [
                "TÍTULO: es el más breve resumen del proyecto",
                "PROBLEMA DE INVESTIGACIÓN: responde a la pregunta ¿qué se va a investigar?",
                "JUSTIFICACIÓN: responde a la pregunta ¿por qué hay que hacer la investigación?",
                "OBJETIVOS: responden a la pregunta ¿qué se propone lograr con la investigación?",
                "MARCO DE REFERENCIA (ANTECEDENTES, MARCO TEÓRICO, MARCO CONCEPTUAL, MARCO GEOGRÁFICO, MARCO LEGAL)",
                "DISEÑO METODOLÓGICO: responde a la pregunta ¿cómo se realizará la investigación?",
                "INFORME DE RESULTADOS",
                "REFERENCIAS BIBLIOGRÁFICAS: responde a la pregunta ¿qué documentación se consultó para la formulación del proyecto?",
                "ANEXOS: corresponden a la información que soporta o complementa el proyecto formulado",
                "SUSTENTACIÓN"
            ];


                
            if($modalidad == 2 ||  $modalidad == 3){

                $peso_criterio = [5, 5, 5, 5, 10, 5, 10, 10, 5, 34];

            }else if ( $modalidad == 1){

                $peso_criterio = [5, 5, 5, 5, 10, 5, 10, 10, 5, 40];

            }else{
                $peso_criterio = [5, 5, 5, 5, 10, 5, 10, 10, 5, 34];
            }

         
           
            /******************jurado 1*************************** */

            $criterios_valorados = [
                isset($aspectos_titulo) ? count($aspectos_titulo) : 0,
                isset($aspectos_problema) ? count($aspectos_problema) : 0,
                isset($aspectos_justificacion) ? count($aspectos_justificacion) : 0,
                isset($aspectos_objetivos) ? count($aspectos_objetivos) : 0,
                isset($aspectos_marco_referencia) ? count($aspectos_marco_referencia) : 0,
                isset($aspectos_diseno_metodologico) ? count($aspectos_diseno_metodologico) : 0,
                isset($aspectos_informe_resultados) ? count($aspectos_informe_resultados) : 0,
                isset($aspectos_referencias) ? count($aspectos_referencias) : 0,
                isset($aspectos_anexos) ? count($aspectos_anexos) : 0,
                isset($aspectos_sustentacion) ? count($aspectos_sustentacion) : 0
            ];
            

            // Función para sumar las calificaciones de cada categoría
            function sumarCalificaciones($categoriaArray) {
                $suma = 0;
                foreach ($categoriaArray as $id => $datos) {
                    $suma += $datos['calificacion']; // Sumar calificación
                }
                return $suma;
            }

            // Sumar calificaciones de cada categoría
            $sumaTitulo = sumarCalificaciones($titulo);
            $sumaProblema = sumarCalificaciones($problema);
            $sumaJustificacion = sumarCalificaciones($justificacion);
            $sumaObjetivos = sumarCalificaciones($objetivos);
            $sumaMarco = sumarCalificaciones($marco);
            $sumaDiseno = sumarCalificaciones($diseno);
            $sumaResultados = sumarCalificaciones($resultados);
            $sumaReferencias = sumarCalificaciones($referencias);
            $sumaAnexos = sumarCalificaciones($anexos);
            $sumaSustentacion = sumarCalificaciones($sustentacion);

          
            // Crear un array con todas las sumas
        $sumas_calificaciones = [
            $sumaTitulo, $sumaProblema, $sumaJustificacion, $sumaObjetivos,
            $sumaMarco, $sumaDiseno, $sumaResultados, $sumaReferencias,
            $sumaAnexos, $sumaSustentacion
        ];

        $valores_jurado1 = [];

        if($modalidad == 2 || $modalidad == 3){

            // Calcular los resultados aplicando la fórmula
            for ($i = 0; $i < count($sumas_calificaciones); $i++) {
                $numerador = $sumas_calificaciones[$i] * $peso_criterio[$i];
                $denominador = $criterios_valorados[$i] * 94;
                $resultado = ($denominador != 0) ? $numerador / $denominador : 0;
                $valores_jurado1[] = number_format($resultado, 1, '.', ''); // Redondeo con dos decimales
            }

        }else if($modalidad == 1){

            for ($i = 0; $i < count($sumas_calificaciones); $i++) {
                $numerador = $sumas_calificaciones[$i] * $peso_criterio[$i];
                
                
                if($i == 0){
                    $denominador = 300;
                }else  if($i == 1){
                    $denominador = 700;
                }else  if($i == 2){
                    $denominador = 300;
                }else  if($i == 3){
                    $denominador = 800;
                }else  if($i == 4){
                    $denominador = 900;
                }else  if($i == 5){
                    $denominador = 800;
                }else  if($i == 6){
                    $denominador = 600;
                }else  if($i == 7){
                    $denominador = 700;
                }else  if($i == 8){
                    $denominador = 200;
                }else  if($i == 9){
                    $denominador = 1500;
                }
                
               
                
                
                $resultado = ($denominador != 0) ? $numerador / $denominador : 0;
                $valores_jurado1[] = number_format($resultado, 1, '.', ''); // Redondeo con dos decimales
            }

        }

       

         /******************jurado 2*************************** */

        // Función para sumar las calificaciones del segundo jurado
            function sumarCalificacionesJurado2($categoriaArray) {
                $suma = 0;
                foreach ($categoriaArray as $id => $datos) {
                    $suma += $datos['calificacion']; // Sumar calificación del segundo jurado
                }
                return $suma;
            }

            // Sumar calificaciones de cada categoría para el segundo jurado
            $sumaTituloJ2 = sumarCalificacionesJurado2($titulo_j2);
            $sumaProblemaJ2 = sumarCalificacionesJurado2($problema_j2);
            $sumaJustificacionJ2 = sumarCalificacionesJurado2($justificacion_j2);
            $sumaObjetivosJ2 = sumarCalificacionesJurado2($objetivos_j2);
            $sumaMarcoJ2 = sumarCalificacionesJurado2($marco_j2);
            $sumaDisenoJ2 = sumarCalificacionesJurado2($diseno_j2);
            $sumaResultadosJ2 = sumarCalificacionesJurado2($resultados_j2);
            $sumaReferenciasJ2 = sumarCalificacionesJurado2($referencias_j2);
            $sumaAnexosJ2 = sumarCalificacionesJurado2($anexos_j2);
            $sumaSustentacionJ2 = sumarCalificacionesJurado2($sustentacion_j2);

            // Crear un array con todas las sumas del jurado 2
            $sumas_calificaciones_j2 = [
                $sumaTituloJ2, $sumaProblemaJ2, $sumaJustificacionJ2, $sumaObjetivosJ2,
                $sumaMarcoJ2, $sumaDisenoJ2, $sumaResultadosJ2, $sumaReferenciasJ2,
                $sumaAnexosJ2, $sumaSustentacionJ2
            ];

            // Calcular los resultados aplicando la fórmula para el jurado 2
            $valores_jurado2 = [];
            if($modalidad == 2 || $modalidad == 3){
            
                for ($i = 0; $i < count($sumas_calificaciones_j2); $i++) {
                    $numerador = $sumas_calificaciones_j2[$i] * $peso_criterio[$i];
                    $denominador = $criterios_valorados[$i] * 94;
                    $resultado = ($denominador != 0) ? $numerador / $denominador : 0;
                    $valores_jurado2[] = number_format($resultado, 1, '.', ''); // Redondeo con un decimal
                }

            }else if($modalidad == 1){

                for ($i = 0; $i < count($sumas_calificaciones_j2); $i++) {
                    $numerador = $sumas_calificaciones_j2[$i] * $peso_criterio[$i];
                    
                    
                    if($i == 0){
                        $denominador = 300;
                    }else  if($i == 1){
                        $denominador = 700;
                    }else  if($i == 2){
                        $denominador = 300;
                    }else  if($i == 3){
                        $denominador = 800;
                    }else  if($i == 4){
                        $denominador = 900;
                    }else  if($i == 5){
                        $denominador = 800;
                    }else  if($i == 6){
                        $denominador = 600;
                    }else  if($i == 7){
                        $denominador = 700;
                    }else  if($i == 8){
                        $denominador = 200;
                    }else  if($i == 9){
                        $denominador = 1500;
                    }
                    
                    $resultado = ($denominador != 0) ? $numerador / $denominador : 0;
                    $valores_jurado2[] = number_format($resultado, 1, '.', ''); // Redondeo con dos decimales
                }
    
            }
           

            

            $total_criterios = 0;
            $total_peso = 0;
            $total_jurado1 = 0;
            $total_jurado2 = 0;

            for ($i = 0; $i < count($variables); $i++) {
                echo "<tr>";
                echo "<td>" . ($i + 1) . "</td>"; // Número de fila
                echo "<td>{$variables[$i]}</td>"; // Nombre de la variable
                echo "<td>" . (isset($criterios_valorados[$i]) ? $criterios_valorados[$i] : 0) . "</td>"; // Cantidad de criterios
                echo "<td>" . (isset($peso_criterio[$i]) ? $peso_criterio[$i] : 0) . "</td>"; // Peso del criterio
                echo "<td>" . (isset($valores_jurado1[$i]) ? $valores_jurado1[$i] : 0) . "</td>"; // Valores del jurado 1
                echo "<td>" . (isset($valores_jurado2[$i]) ? $valores_jurado2[$i] : 0) . "</td>"; // Valores del jurado 2
                echo "</tr>";
            
                // Acumular totales
                $total_criterios += isset($criterios_valorados[$i]) ? $criterios_valorados[$i] : 0;
                $total_peso += isset($peso_criterio[$i]) ? $peso_criterio[$i] : 0;
                $total_jurado1 += isset($valores_jurado1[$i]) ? floatval($valores_jurado1[$i]) : 0.0;
                $total_jurado2 += isset($valores_jurado2[$i]) ? floatval($valores_jurado2[$i]) : 0.0;
                // Formatear los totales con un decimal
                $total_jurado1 = number_format($total_jurado1, 1, '.', '');
                $total_jurado2 = number_format($total_jurado2, 1, '.', '');
                
            }

            // Calcular la media de los totales
            $promedio_totales = ($total_jurado1 + $total_jurado2) / 2;

            // Redondear el resultado a 0 decimales como en la fórmula de Excel
            $promedio_redondeado = round($promedio_totales, 0);
            

            echo "<tr class='bold'>";
            echo "<td colspan='2'>PONDERACIÓN</td>";
            echo "<td>{$total_criterios}</td>";
            echo "<td>{$total_peso}</td>";
            echo "<td>{$total_jurado1}</td>";
            echo "<td>{$total_jurado2}</td>";
            echo "</tr>";

            
            echo "<tr class='bold'>";
            echo "<td colspan='5'>CALIFICACION FINAL</td>";
            echo "<td olspan='4' class='content-evaluacion'>{$promedio_redondeado}</td>";
    
           
            echo "</tr>";
            ?>

        </table>

        <?php

        if (($total_jurado1 !== null && $total_jurado1 > 0) || ($total_jurado2 !== null && $total_jurado2 > 0)) {
            $actualizar_valores = "UPDATE evaluaciones_proyectos 
            SET calificacion_jurado1 = '$total_jurado1', 
                calificacion_jurado2 = '$total_jurado2' 
            WHERE codigo_proyecto = '$codigo_proyecto'";
        
            $ins_loginControlador->ejecutar_consultas_simples_two($actualizar_valores);
        }

            

    

        if($modalidad == 2 || $modalidad == 2){

         // Determinar la clase CSS según el promedio
        $clase_aprobado = ($promedio_redondeado >= 70) ? "aprobado-activo" : "aprobado";
        $clase_reprobado = ($promedio_redondeado < 70) ? "reprobado-activo" : "reprobado";
        ?>
            <table class="tabla-evaluacion mt-5 mb-5">
                <tr>
                    <td class="<?= $clase_reprobado ?>" colspan="2"><b>REPROBADO</b></td>
                    <td class="<?= $clase_aprobado ?>" colspan="2"><b>APROBADO</b></td>
                </tr>
                <tr>
                    <td colspan="2">MENOS DE 69 PUNTOS</td>
                    <td colspan="2">DE 70 A 94 PUNTOS</td>
                </tr>
            </table>
        <?php

        }else if($modalidad ==1){
        // Determinar la clase CSS según el promedio
        if ($promedio_redondeado < 70) {
            $clase_resultado_reprobado = "reprobado"; // Menos de 69 puntos
        } elseif ($promedio_redondeado >= 70 && $promedio_redondeado <= 94) {
            $clase_resultado_aprobado = "aprobado"; // De 70 a 94 puntos
        } elseif ($promedio_redondeado >= 95 && $promedio_redondeado <= 99) {
            $clase_resultado_meritorio = "sobresaliente"; // De 95 a 99 puntos
        } else {
            $clase_resultado_laureado = "perfecto"; // 100 puntos
        }

        ?>
        <table class="tabla-evaluacion mt-5 mb-5">
            <tr>
                <td class="<?= $clase_resultado_reprobado ?>" colspan="2"><b>REPROBADO</b></td>
                <td class="<?= $clase_resultado_aprobado ?>" colspan="2"><b>APROBADO</b></td>
                <td class="<?= $clase_resultado_meritorio ?>" colspan="2"><b>MERITORIO</b></td>
                <td class="<?= $clase_resultado_laureado ?>" colspan="2"><b>LAUREADO</b></td>
            </tr>
            <tr>
                <td colspan="2">MENOS DE 69 PUNTOS</td>
                <td colspan="2">DE 70 A 94 PUNTOS</td>
                <td colspan="2">DE 95 A 99 PUNTOS</td>
                <td colspan="2">100 PUNTOS</td>
            </tr>
        </table>
     <?php


        }


        ?>


        <?php
            $consultar_firmas_jurados = "SELECT 
            ep.numero_documento,
            u.nombre_usuario,
            u.apellidos_usuario,
            f.firma,
            ep.opcion_jurado
        FROM Asignar_jurados_proyecto ep
        JOIN usuarios u ON ep.numero_documento = u.numero_documento
        LEFT JOIN firma_digital_usuarios f ON ep.numero_documento = f.numero_documento
        WHERE ep.codigo_proyecto = '$codigo_proyecto'";

        $resultado_firmas_jurados = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_firmas_jurados);

        $firmas_jurados = [];
        $nombres_jurados = []; // Nuevo arreglo para almacenar nombres

        if ($resultado_firmas_jurados->rowCount() > 0) {
        $datos_firmas = $resultado_firmas_jurados->fetchAll(PDO::FETCH_ASSOC);
        foreach ($datos_firmas as $firma) {
            $nombre_completo = $firma['nombre_usuario'] . " " . $firma['apellidos_usuario'];
            
            // Guardamos la firma y el nombre según la opción del jurado (1 o 2)
            if ($firma['opcion_jurado'] == 1) {
                $firmas_jurados['jurado1'] = $firma['firma'];
                $nombres_jurados['jurado1'] = $nombre_completo;
            } elseif ($firma['opcion_jurado'] == 2) {
                $firmas_jurados['jurado2'] = $firma['firma'];
                $nombres_jurados['jurado2'] = $nombre_completo;
            }
        }
        }


    
        ?>

       
       
        <table class="tabla-firmas">
            <tr>
                <td> 
                    <div class="firma">
                        <div class="espacio-firma">
                        <?php if (!empty($firmas_cordinador[0])): ?>
                            <img src="<?= SERVERURL ?>Views/assets/images/FirmasUsuarios/<?= $firmas_cordinador[0] ?>" 
                                alt="Firma Coordinador" style="width: 150px;">
                        <?php else: ?>
                            <p>No hay firma registrada</p>
                        <?php endif; ?>
                        </div>
                        <p><b>FIRMA DEL PRESIDENTE</b></p>
                    </div>
                </td>
                <td>
                    <div class="firma">
                        <div class="espacio-firma">
                        <img src="<?=SERVERURL?>Views/assets/images/FirmasUsuarios/<?=$firmas_jurados['jurado2'] ?>" 
                        alt="Firma Jurado 1" style="width: 150px;">
                        </div>
                        <p ><b>FIRMA JURADO 2 : <?=$nombres_jurados['jurado2']?></b></p>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="firma">
                        <div class="espacio-firma">
                        <img src="<?= SERVERURL ?>Views/assets/images/FirmasUsuarios/<?=$firmas_jurados['jurado1'] ?>" 
                        alt="Firma Jurado 1" style="width: 150px;">
                        </div>
                        <p ><b>FIRMA JURADO 1: <?=$nombres_jurados['jurado1']?></b></p>
                    </div>
                </td>
                <td>
                    <div class="firma">
                        <div class="espacio-firma">
                        <?php if (!empty($firmas_administradores[0])): ?>
                            <img src="<?= SERVERURL ?>Views/assets/images/FirmasUsuarios/<?=$firmas_administradores[0] ?>" 
                                alt="Firma Coordinador" style="width: 150px;">
                        <?php else: ?>
                            <p>No hay firma registrada</p>
                        <?php endif; ?>
                        </div>
                        <p><b>FIRMA VEEDOR</b></p>
                    </div>
                </td>
            </tr>
        </table>




    </div>

    <!--*********************************************************************************++-->

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        function toggleSeccion(seccion) {
            const formulario = document.getElementById("formulario");
            const acta = document.getElementById("acta");

            if (seccion === "formulario") {
                formulario.classList.add("mostrar");
                acta.classList.remove("mostrar");
            } else {
                acta.classList.add("mostrar");
                formulario.classList.remove("mostrar");
            }
        }

        // Asignar eventos manualmente en lugar de `onclick`
        document.querySelector(".btn-formulario").addEventListener("click", () => toggleSeccion('formulario'));
        document.querySelector(".btn-acta").addEventListener("click", () => toggleSeccion('acta'));
    });

    function descargarPDF() {
     
        const elemento = document.getElementById('acta');
        

        const opciones = {
            margin: [10, 10, 10, 10], // Márgenes en mm
            filename: 'acta-calificacion-<?=$codigo_proyecto?>.pdf',
            image: { type: 'jpeg', quality: 1 },
            html2canvas: {
            scale: 3,       // Calidad nítida
            useCORS: true,
            scrollX: 0,
            scrollY: -window.scrollY
            },
            jsPDF: {
            orientation: 'portrait',
            unit: 'mm',
            format: [310, 700]   // Hoja estándar horizontal
            },
            pagebreak: {
            mode: ['avoid-all', 'css', 'legacy'],
            before: '#nueva-pagina', // puedes crear elementos que marquen quiebres si lo deseas
            }
        };

        html2pdf().set(opciones).from(elemento).save();
    }

</script>