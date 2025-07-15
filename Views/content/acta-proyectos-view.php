<?php

if (isset($_GET['views'])) {
  $ruta = explode(
      "/",
      $_GET['views']
  );

  $codigo_proyecto = $ruta[1];
 
}


setlocale(LC_TIME, 'es_ES.UTF-8'); // Configurar el locale en español
$fecha_actual = strftime('%A %d de %B de %Y');


$consutar_codigo = "SELECT p.*, pa.nombre_programa, f.nombre_facultad,  pa.id_programa
FROM proyectos p
JOIN programas_academicos pa ON p.id_programa = pa.id_programa
JOIN facultades f ON pa.id_facultad = f.id_facultad
WHERE p.codigo_proyecto = '$codigo_proyecto'";

$resultado_consultar_codigo = $ins_loginControlador->ejecutar_consultas_simples_two($consutar_codigo);

if ($resultado_consultar_codigo->rowCount() > 0) {

    $datos = $resultado_consultar_codigo->fetch(PDO::FETCH_ASSOC);

  $titulo_proyecto = $datos['titulo_proyecto'];

  $programa_proyecto = $datos['nombre_programa'];

  $id_porgrama = $datos['id_programa'];
  
  $modalidad = $datos['modalidad'];

  if($modalidad == 1){
    $modalidad = 'TRABAJO DE GRADO';
  }else if($modalidad == 2){
    $modalidad = 'PASANTIA';
  }else if($modalidad == 3){ 
    $modalidad = 'CURSO DE INVESTIGACIÓN PREGRADUAL CIP';
  }else{
    $modalidad = "no hay datos que mostrar";
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

  /******************************consultar informacion de los asesores************************************/

  $consultar_asesores_proyecto = "SELECT e.nombre_usuario, e.apellidos_usuario 
    FROM Asignar_asesor_anteproyecto_proyecto ep
    JOIN usuarios e ON ep.numero_documento = e.numero_documento
    WHERE ep.codigo_proyecto = '$codigo_proyecto'";

    $resultado_asesores = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_asesores_proyecto);
    
    $asesores_info = [];

    if ($resultado_asesores->rowCount() > 0) {

    $datos_asesores = $resultado_asesores->fetchAll(PDO::FETCH_ASSOC);

    foreach ($datos_asesores as $asesores) {

        $asesores_info[] = $asesores['nombre_usuario'] . ' ' . $asesores['apellidos_usuario'];

    }

    }


    /******************************consultar informacion de los jurados************************************/

  $consultar_jurados_proyecto = "SELECT e.nombre_usuario, e.apellidos_usuario, e.numero_documento
  FROM Asignar_jurados_proyecto ep
  JOIN usuarios e ON ep.numero_documento = e.numero_documento
  WHERE ep.codigo_proyecto = '$codigo_proyecto'";

  $resultado_jurados = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_jurados_proyecto);
  
  $jurados_info = [];

  $documentos_jurados_info = [];

  if ($resultado_jurados->rowCount() > 0) {

  $datos_jurados = $resultado_jurados->fetchAll(PDO::FETCH_ASSOC);

  foreach ($datos_jurados as $jurados) {

      $jurados_info[] = $jurados['nombre_usuario'] . ' ' . $jurados['apellidos_usuario'];

      $documentos_jurados_info  [] = $jurados['numero_documento'];

  }



  }

  /******************************************jurado metodologico*************************************************** */
  
  $consultar_jurados_metodologico_proyecto = "SELECT u.nombre_usuario, u.apellidos_usuario 
  FROM usuarios u
  JOIN Asignar_usuario_facultades p ON u.numero_documento = p.numero_documento
  JOIN programas_academicos pa ON pa.id_programa = p.id_programa
  WHERE p.id_programa = '$id_porgrama'
  AND u.id_rol = 2;";

  $consulta = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_jurados_metodologico_proyecto);

  $jurados_metodologico_info = [];

  if ($consulta->rowCount() > 0) {
      $datos_jurados_metodologico = $consulta->fetchAll(PDO::FETCH_ASSOC);

      foreach ($datos_jurados_metodologico as $jurado_metodologico) {
          $jurados_metodologico_info[] = $jurado_metodologico['nombre_usuario'] . ' ' . $jurado_metodologico['apellidos_usuario'];
      }
    
      
  }

  /*******************************************extraer calificaicones ******************************************* */

$sql = "SELECT numero_documento, resumen_parte1, resumen_parte2 FROM calificaciones_jurados WHERE codigo_proyecto = '$codigo_proyecto'";
$consulta = $ins_loginControlador->ejecutar_consultas_simples_two($sql);

$usuarios_resumen = [];

if ($consulta->rowCount() > 0) {
    $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    foreach ($datos as $fila) {
        // Decodificar los JSON de cada usuario
        $resumen_parte1 = json_decode($fila['resumen_parte1'], true);
        $resumen_parte2 = json_decode($fila['resumen_parte2'], true);

        // Extraer los valores requeridos, asegurando que existan en el JSON
        $usuarios_resumen[] = [
            "numero_documento" => $fila['numero_documento'],
            "total_valor_asignado_parte1" => $resumen_parte1['total_valor_asignado'] ?? 0.0,
            "total_item_evaluar_parte1" => $resumen_parte1['total_item_evaluar'] ?? 0.0,
            "total_valor_asignado_parte2" => $resumen_parte2['total_valor_asignado'] ?? 0.0,
            "total_item_evaluar_parte2" => $resumen_parte2['total_item_evaluar'] ?? 0.0
        ];
    }
}

  /*******************************************extraer calificaicones general ******************************************* */

  $sql1 = "SELECT resumen_general FROM asesores_metodologicos WHERE codigo_proyecto = '$codigo_proyecto'";

  $consulta2 = $ins_loginControlador->ejecutar_consultas_simples_two($sql1);
  
  $resumen_general = [];
  
  if ($consulta2->rowCount() > 0) {
      $fila = $consulta2->fetch(PDO::FETCH_ASSOC);
      $resumen_decodificado = json_decode($fila['resumen_general'], true); // Decodificar JSON correctamente
  
      // Verificar si el JSON contiene 'total' antes de acceder a él
      $valor_resumen_jurado_metodologico = isset($resumen_decodificado['total']['valor']) ? $resumen_decodificado['total']['valor'] : [];

      $valor_resumen_jurado_metodologico2 = (float)  $valor_resumen_jurado_metodologico / 2;
  }
  

}

?>

<div class="tabla-asesor-metodologico">

<table class="aunar-table">
    <tr>
      <td rowspan="2" class="aunar-logo">
        <img src="<?= SERVERURL ?>Views/assets/images/<?=$nombre_logo?>" alt="Logo AUNAR">
      </td>
      <td class="aunar-title">SISTEMA DE GESTION DE LA CALIDAD</td>
      <td class="aunar-info"><span class="aunar-red">Código:</span> FR-DA-GDE-0028</td>
    </tr>
    <tr>
      <td class="aunar-title"> ACTA DE CALIFICACION Y APROBACION DE PROYECTO DE GRADO</td>
      <td class="aunar-info">
        <p><span>VERSIÓN:</span> 2</p>
        <p><span>Vigencia:</span> 28-09-2021</p>
        <p><span>Página:</span> 1 de 1</p>
      </td>
    </tr>
  </table>

  

<table class="tabla-proyecto">
    <tr>
        <td colspan="6" class="titulo">DOCUMENTO CONTROLADO</td>
    </tr>
   
    <tr>
        <td colspan="6" class="descripcion text-justify">Establecidos y cumplidos los requerimientos de la Corporación Universitaria Autónoma de Nariño (Capitulo XVI del Reglamento estudiantil por Acuerdo N° 53/2018), se hace el reporte del ACTA DE CALIFICACION (Cualitativa y Cuantitativa) Y APROBACION de proyecto de grado, establecida como parte del cumplimiento de requisitos al optar a titulación de acuero al registro de inscripción y matricula de estudiantes al Programa de Educación Superior en la institución.</td>
    </tr>
</table>

<div class="container-acta">
        <table class="tabla-info">
            <tr>
                <td class="bold gray">PROGRAMA: </td>
                <td><?=$programa_proyecto?></td>
                <td class="bold gray">REGISTRO CALIFICADO: 104600</td>
                <td class="bold">FECHA: <?=$fecha_actual?></td>
            </tr>
            <tr>
                <td class="bold gray">ACTA CALIFICACIÓN N°:</td>
                <td colspan="3">102018-2023-08-01</td>
            
            </tr>

            <tr>
                <td class="bold gray">OPCIÓN DE GRADO</td>
                <td colspan="3"><?=$modalidad?></td>
            </tr>
            
            <tr>
                <td class="bold gray">TIPO DE PROYECTO</td>
                <td colspan="3">Linea Tecnológica</td>
            </tr>

           
            <tr>
                <td class="bold gray">TÍTULO DEL PROYECTO:</td>
                <td colspan="5"><?=$titulo_proyecto?></td>
            </tr>
        </table>
        
        <table class="tabla-info">
            <tr>
                <td class="bold gray">ESTUDIANTE:</td>
                <td><?= $estudiantes_info[0]?></td>
                <td class="bold gray">ESTUDIANTE 2:</td>
                <td><?= $estudiantes_info[1]?></td>
            </tr>
            <tr>
                <td class="bold gray">ASESOR 1:</td>
                <td><?=$asesores_info[0];?></td>
                <td class="bold gray">ASESOR 2:</td>
                <td><?=$asesores_info[0];?></td>
            </tr>
        </table>

        <table class="tabla-info">
            <tr>
                <td class="bold gray">JURADO 1:</td>
                <td><?=$jurados_info[0];?></td>
                <td class="bold gray">JURADO 2:</td>
                <td><?=$jurados_info[1];?></td>
                
            </tr>
            <tr>
               
                <td class="bold gray">JURADO 3:</td>
                <td><?= $jurados_metodologico_info[0],  $jurados_metodologico_info[1]?></td>
                <td colspan="5">Metodológico</td>
            </tr>
        </table>
        
        <div class="bold mt-2 mb-2">Coloque en la casilla el valor cuantitativo dado por los jurados especificos (Jurado 1 al Jurado 2) y metodológico (Jurado 3), para la evaluación del proyecto de grado.</div>
        <table class="tabla-info">
        <tr>
                <th colspan="8" class="center">CALIFICACIÓN</th>
            </tr>
            <tr>
                <th>No.</th>
                <th>VARIABLES E INDICADORES</th>
                <th>VALOR ASIGNADO (%)</th>
                <th>JURADO 1</th>
                <th>JURADO 2</th>
                <th>JURADO 3</th>
                <th>JURADO 4</th>
                <th>JURADO 5</th>
            </tr>

            <tr>
                <td class="gray"></td>
                <td class="gray"></td>
                <td class="center gray">100</td>
                <td class="center gray"><?php echo $usuarios_resumen[0]['total_valor_asignado_parte1'] + $usuarios_resumen[0]['total_valor_asignado_parte2']; ?></td>
                <td class="center gray"><?php echo $usuarios_resumen[1]['total_valor_asignado_parte1'] + $usuarios_resumen[1]['total_valor_asignado_parte2']; ?></td>
                <td class="center gray"><?=$valor_resumen_jurado_metodologico2?></td>
                <td class="center gray"></td>
                <td class="center gray"></td>
            </tr>

            <tr>
                <td class="gray">1</td>
                <td class="gray">CONTENIDO DEL PROYECTO</td>
                <td class="center ">60</td>
                <td class="center "><?php echo $usuarios_resumen[0]['total_valor_asignado_parte1']; ?></td>
                <td class="center "><?php echo $usuarios_resumen[1]['total_valor_asignado_parte1']; ?></td>
                <td class="center gray"></td>
                <td class="center gray"></td>
                <td class="center gray"></td>
            </tr>
            <tr>
                <td class="gray">2</td>
                <td class="gray">DESARROLLO DE LA ACTIVIDAD</td>
                <td class="center">15</td>
                <td class="center"><?php echo $usuarios_resumen[0]['total_valor_asignado_parte2']; ?></td>
                <td class="center"><?php echo $usuarios_resumen[1]['total_valor_asignado_parte2']; ?></td>
                <td class="center gray"></td>
                <td class="center"></td>
                <td class="center"></td>
            </tr>
            <tr>
                <td class="gray">3</td>
                <td class="gray">METODOLOGÍA</td>
                <td class="center">25</td>
                <td class="center gray"></td>
                <td class="center gray"></td>
                <td class="center"><?=$valor_resumen_jurado_metodologico2?></td>
                <td class="center"></td>
                <td class="center"></td>
            </tr>
            <tr>
                <td class="gray" colspan="2"></td>
                <td class="gray">CALIFICACIÓN</td>
                <td class="center"><?=$suma = (float) ($usuarios_resumen[0]['total_valor_asignado_parte1'] + $usuarios_resumen[0]['total_valor_asignado_parte2'])*5/75 ?></td>
                <td class="center"><?=$suma = (float) ($usuarios_resumen[1]['total_valor_asignado_parte1'] + $usuarios_resumen[1]['total_valor_asignado_parte2'])*5/75 ?></td>
                <td class="center"><?=$valor_resumen_jurado_metodologico?></td>
                <td class="center"></td>
                <td class="center"></td>
            </tr>
        </table>
        
        <table class="tabla-info">
            <tr>
                <td class="bold center">PORCENTAJE MÁXIMO</td>
                <td class="center">100%</td>
                <td class="bold center">PORCENTAJE MÍNIMO REQUERIDO</td>
                <td class="center">60%</td>
                <td class="bold center">PORCENTAJE PROMEDIO ALCANZADO</td>
                <td class="center">89.0</td>
                <td class="bold center">CALIFICACIÓN OBTENIDA</td>
                <td class="center">4.5</td>
            </tr>
        </table>

        <table class="tabla-info">
            <tr>
                <td class="bold">CONCEPTO:</td>
                <td>REPROBADO (0-59)</td>
                <td class="center bold">APROBADO CON RECOMENDACIONES</td>
                <td class="center">X</td>
                <td class="bold">APROBADO (60-89)</td>
                <td></td>
                <td class="bold">MERITORIO (90-99)</td>
                <td></td>
            </tr>
            <tr>
                <td class="bold" colspan="2">PROPUESTO PARA:</td>
                <td colspan="2">MERITORIO (90-99)</td>
                <td></td>
                <td class="bold" colspan="2">LAUREADO (100)</td>
                <td></td>
               
            </tr>
        </table>

        <div class="center bold">FIRMA COORDINADOR / LÍDER DE INVESTIGACIÓN EXTENSIÓN</div>
    </div>


</div>