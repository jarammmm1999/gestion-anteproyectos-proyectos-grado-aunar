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

   /******************************consultar informacion del asesor ************************************/

   $consultar_asesor_proyecto = "SELECT e.nombre_usuario, e.apellidos_usuario 
   FROM Asignar_asesor_anteproyecto_proyecto ep
   JOIN usuarios e ON ep.numero_documento = e.numero_documento
   WHERE ep.codigo_proyecto = '$codigo_proyecto'";

  $resultado_asesor = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_asesor_proyecto);

  if ($resultado_asesor->rowCount() > 0) {

    $data = $resultado_asesor->fetch(PDO::FETCH_ASSOC);

    $nombre_completo_asesor = $data['nombre_usuario'] . ' ' . $data['apellidos_usuario'];

  }

  /******************************consultar informacion del coordinador ************************************/

  $consultar_coordinador_proyecto = "SELECT e.nombre_usuario, e.apellidos_usuario, e.id_rol
  FROM Asignar_usuario_facultades ep
  JOIN usuarios e ON ep.numero_documento = e.numero_documento
  WHERE ep.id_programa = '$id_porgrama' AND e.id_rol = 2";

$resultado_coordinador = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_coordinador_proyecto);

if ($resultado_coordinador->rowCount() > 0) {

  $date = $resultado_coordinador->fetch(PDO::FETCH_ASSOC);

   $nombre_completo_coordinador = $date['nombre_usuario'] . ' ' . $date['apellidos_usuario'];

}



}else{
  echo "No se encontraron resultados.";
  exit();
}



$sqlLogo = "SELECT nombre_logo 
FROM configuracion_aplicacion 
LIMIT 1";
$consulta_logo = $ins_loginControlador->ejecutar_consultas_simples_two($sqlLogo);

if ($consulta_logo->rowCount() > 0) {
$resultado = $consulta_logo->fetch(PDO::FETCH_ASSOC);
$nombre_logo = $resultado['nombre_logo'];
} else {
$nombre_logo ="logo-autonoma.png";
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
      <td class="aunar-title">EVALUACIÓN METODOLÓGICA DEL PROYECTO DE GRADO</td>
      <td class="aunar-info">
        <p><span>VERSIÓN:</span> 2</p>
        <p><span>Vigencia:</span> 28-09-2021</p>
        <p><span>Página:</span> 1 de 2</p>
      </td>
    </tr>
  </table>
  <table class="aunar-table aunar-details mt-3">
    <tr><td class="aunar-bold">FECHA:</td><td><?=ucfirst($fecha_actual);?></td></tr>
    <tr><td class="aunar-bold">PROGRAMA ACADEMICO:</td><td><?=$programa_proyecto?></td></tr>
    <tr><td class="aunar-bold">TITULO DEL PROYECTO:</td><td><?=$titulo_proyecto?></td></tr>
    <tr><td class="aunar-bold">INVESTIGADOR(ESTUDIANTE):</td><td><?= implode(', ', $estudiantes_info) . "<br>";?></td></tr>
    <tr><td class="aunar-bold">ASESOR METODOLÓGICO:</td><td><?=$nombre_completo_asesor?></td></tr>
    <tr><td class="aunar-bold">ASESOR ESPECÍFICO:</td><td><?=$nombre_completo_asesor?></td></tr>
    <tr><td class="aunar-bold">EVALUADOR METODOLÓGICO:</td><td><?=$nombre_completo_coordinador?></td></tr>
    <tr><td class="aunar-bold">ROL DEL EVALUADOR:</td><td class="aunar-bold vertical">Jurado Metodológico</td></tr>
    <tr><td class="aunar-bold ">MODALIDAD:</td><td class="aunar-bold central"><?=$modalidad?></td></tr>
    <tr><td colspan="2" class="aunar-title">NOTAS</td></tr>
    <tr><td colspan="2">a) El presente formato de evaluación ha sido elaborado para arrojar un resultado de puntuación único, se solicita al evaluador no realizar ajuste de formulación o presentación al mismo.</td></tr>
    <tr><td colspan="2">b) El proyecto debe presentarse según los parámetros de la norma APA (Última versión, teniendo en cuenta que se debe justificar la alineación del texto).</td></tr>
  </table>

  <!------------------------------- extraemos los datos del resumen general --------------------------------------------->

  <?php


   $suma_valores = [];

    $sql = "SELECT resumen_general FROM asesores_metodologicos WHERE codigo_proyecto = '$codigo_proyecto'";
    $consulta_resumen = $ins_loginControlador->ejecutar_consultas_simples_two($sql);

    if ($consulta_resumen->rowCount() > 0) {
        $resumen_general = json_decode($consulta_resumen->fetch(PDO::FETCH_ASSOC)['resumen_general'], true);

        $campos = [
            'titulo', 'problema_investigacion', 'objetivos', 'justificacion', 'marco_referencia',
            'diseno_metodologico', 'finalidad_investigacion', 'referencias_bibliograficas', 'anexos', 'evaluacion_general'
        ];

        foreach ($campos as $campo) {
            ${'valor_'.$campo} = $resumen_general[$campo]['valor'] ?? '';
            ${'valor_'.$campo.'_ob'} = $resumen_general[$campo]['observacion'] ?? '';
        }

         $total_resumen_general = $resumen_general['total']['valor'] ?? '';
      
    } 
    ?>
 

  <form class=" mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
    <table class="aunar-table aunar-summary mt-3">
        <input type="hidden" name="valor_item_retroalimentacion" value="<?= $ins_loginControlador->encryption(1) ?>">
        <input type="hidden" name="codigo_Proyecto" value="<?= $ins_loginControlador->encryption($codigo_proyecto) ?>">
      <tr>
        <th colspan="2">RESUMEN GENERAL</th>
        <th>OBSERVACIONES</th>
      </tr>
      <?php
          $campos = [
              'titulo' => 'TITULO',
              'problema' => 'PROBLEMA DE INVESTIGACIÓN',
              'objetivos' => 'OBJETIVOS',
              'justificacion' => 'JUSTIFICACIÓN',
              'marco' => 'MARCO DE REFERENCIA',
              'diseno' => 'DISEÑO METODOLÓGICO',
              'finalidad' => 'FINALIDAD DE LA INVESTIGACIÓN',
              'referencias' => 'REFERENCIAS BIBLIOGRAFICAS',
              'anexos' => 'ANEXOS',
              'evaluacion' => 'EVALUACION GENERAL'
          ];

          foreach ($campos as $key => $label) {
              $valor = isset(${'valor_'.$key}) ? htmlspecialchars(${'valor_'.$key}) : '0.0';
              $observacion = isset(${'valor_'.$key.'_ob'}) ? ${'valor_'.$key.'_ob'} : '';

              echo "<tr><td>$label</td>
              <td><input type='number' step='0.01' name='{$key}_valor' id='{$key}'></td>
              <td><textarea name='{$key}_obs'>{$resumen_general[$key]['observacion']}</textarea></td></tr>";
          }

          $valor_total = isset($total_resumen_general) ? htmlspecialchars($total_resumen_general) : '0.0';
          echo "<tr><td class='aunar-bold'>TOTAL</td><td><input type='number' step='0.01' name='total_valor' id='total_suma' class='aunar-bold'></td><td></td></tr>";
          ?>
    </table>
    <div class="text-center mt-4">
        <?php
        if($resumen_general_value == 0){
            ?>
            <button type="submit">Guardar</button>
            <?php
        }else{
            ?>
            <input type="hidden" name="valor_item_retroalimentacion_upd" value="<?= $ins_loginControlador->encryption(1) ?>">
            <button type="submit">Actualizar</button>
            <?php
        }
        ?>
    </div>
  </form>
<!------------------------------------------obervaciones titulo------------------------------------------------------------>

<?php
$sql1 = "SELECT resumen_titulos FROM asesores_metodologicos WHERE codigo_proyecto = '$codigo_proyecto'";
$consulta_resumen_titulo = $ins_loginControlador->ejecutar_consultas_simples_two($sql1);

if ($consulta_resumen_titulo->rowCount() > 0) {
    $resultado_titulo = $consulta_resumen_titulo->fetch(PDO::FETCH_ASSOC);
    $resumen_titulos = json_decode($resultado_titulo['resumen_titulos'], true);

    $items_titulo = [];
    $items = range(1, 3);

    foreach ($items as $i) {
        $items_titulo["item_$i"] = [
            "valor" => $resumen_titulos["item_$i"]["valor"] ?? '',
            "observacion" => $resumen_titulos["item_$i"]["observacion"] ?? '',
            "valores_items" => $resumen_titulos["item_$i"]["valores_items"] ?? 0.0
        ];
    }

    $suma_valores_titulos = array_reduce($items_titulo, function ($carry, $item) {
        return $carry + $item['valores_items'];
    }, 0.0) * 0.091;

    $suma_valores['titulo'] = $suma_valores_titulos ?? 0.0;

}
?>
  <form class=" mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
  <input type="hidden" name="valor_item_retroalimentacion" value="<?= $ins_loginControlador->encryption(2) ?>">
  <input type="hidden" name="codigo_Proyecto" value="<?= $ins_loginControlador->encryption($codigo_proyecto) ?>">
  <table class="aunar-table aunar-summary mt-3"> 
      <tr>
        <th colspan="4" class="text-center central">TÍTULO: es el más breve resumen del proyecto </th>
      </tr>
      <tr>
        <th  class="text-center valores"><?=$suma_valores_titulos?> </th>
        <th  class="text-center central">Aspectos </th>
        <th  class="text-center central">Calificación </th>
        <th  class="text-center central">OBSERVACION </th>
      </tr>
 
      <?php
      $items = [
          1 => "Responde a tres o más de los siguientes interrogantes: ¿qué?, ¿sobre qué?, ¿dónde?, ¿cómo?, ¿cuándo?",
          2 => "Proporciona una idea global completa de la investigación a realizar",
          3 => "Es claro y conciso"
      ];

      foreach ($items as $i => $descripcion) {
          $valor = ${"valores_titulo_$i"};
          $item_titulo = ${"item_titulo_$i"};
          $observacion = ${"observacio_titulo_$i"};

          echo "<tr><td>{$items_titulo['item_'.$i]['valores_items']}</td>
                <td>$descripcion</td>
                <td><select class=\"form-select\" name=\"opcion_item_$i\">
                    <option selected>" . (isset($items_titulo['item_'.$i]['valor']) ? htmlspecialchars($items_titulo['item_'.$i]['valor']) : 'Seleccione una opción') . "</option>
                    <option value=\"" . $ins_loginControlador->encryption(1) . "\">No aplica</option>
                    <option value=\"" . $ins_loginControlador->encryption(2) . "\">Cumple</option>
                    <option value=\"" . $ins_loginControlador->encryption(3) . "\">Cumple Parcialmente</option>
                    <option value=\"" . $ins_loginControlador->encryption(4) . "\">No cumple</option>
                </select></td>
                <td><textarea name=\"obs_problema_$i\">{$items_titulo['item_'.$i]['observacion']}</textarea></td></tr>";
      }
      ?>
      
  </table>
    <div class="text-center mt-4">
        <button type="submit">Guardar</button>
    </div>

  </form>

  <!------------------------------------------obervaciones problemas------------------------------------------------------------>

  <?php
// Optimización de la extracción de datos desde la base de datos

$sql2 = "SELECT resumen_problemas FROM asesores_metodologicos WHERE codigo_proyecto = '$codigo_proyecto'";
$consulta_resumen_problema = $ins_loginControlador->ejecutar_consultas_simples_two($sql2);

if ($consulta_resumen_problema->rowCount() > 0) {
    $resultado_problema = $consulta_resumen_problema->fetch(PDO::FETCH_ASSOC);
    $resumen_problema = json_decode($resultado_problema['resumen_problemas'], true);

    $items = range(1, 6);
    $items_problema = [];

    foreach ($items as $i) {
        $items_problema["item_$i"] = [
            "valor" => $resumen_problema["item_$i"]["valor"] ?? '',
            "observacion" => $resumen_problema["item_$i"]["observacion"] ?? '',
            "valores_items" => $resumen_problema["item_$i"]["valores_items"] ?? 0.0
        ];
    }

    $suma_valores_problema = array_reduce($items_problema, function ($carry, $item) {
        return $carry + $item['valores_items'];
    }, 0.0) * 0.091;

    
    $suma_valores['problema'] = $suma_valores_problema ?? 0.0;

    // Ahora puedes acceder a $items_problema['item_1']['valor'], etc., sin repetir código

}
?>
  <form class=" mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
  <input type="hidden" name="valor_item_retroalimentacion" value="<?= $ins_loginControlador->encryption(3) ?>">
  <input type="hidden" name="codigo_Proyecto" value="<?= $ins_loginControlador->encryption($codigo_proyecto) ?>">
  <table class="aunar-table aunar-summary mt-3"> 
      <tr>
        <th colspan="4" class="text-center central">PROBLEMA DE INVESTIGACIÓN: responde a la pregunta ¿qué se va a investigar? </th>
      </tr>
      <tr>
        <th  class="text-center valores"><?=$suma_valores_problema?> </th>
        <th  class="text-center central">Aspectos </th>
        <th  class="text-center central">Calificación </th>
        <th  class="text-center central">OBSERVACION </th>
      </tr>
 
      <?php
      $items = [
          1 => "Describe los sintomas o problemas identificados por el investigador",
          2 => "Identifica las causas que generan los sintomas o problemas definidos.",
          3 => "Evidencia la Probabilidad de ocurrencia de las consecuencias, si el problema persiste.",
          4 => "Identifica las alternativas de control al pronostico.",
          5 => "Se presenta en forma de pregunta",
          6 => "Esta relacionada con la pertinencia del problema."
      ];

      foreach ($items as $i => $descripcion) {
          $valor = ${"valores_titulo_$i"};
          $item_titulo = ${"item_titulo_$i"};
          $observacion = ${"observacio_titulo_$i"};

          echo "<tr><td>{$items_problema['item_'.$i]['valores_items']}</td>
                <td>$descripcion</td>
                <td><select class=\"form-select\" name=\"opcion_item_$i\">
                    <option selected>" . (isset($items_problema['item_'.$i]['valor']) ? htmlspecialchars($items_problema['item_'.$i]['valor']) : 'Seleccione una opción') . "</option>
                    <option value=\"" . $ins_loginControlador->encryption(1) . "\">No aplica</option>
                    <option value=\"" . $ins_loginControlador->encryption(2) . "\">Cumple</option>
                    <option value=\"" . $ins_loginControlador->encryption(3) . "\">Cumple Parcialmente</option>
                    <option value=\"" . $ins_loginControlador->encryption(4) . "\">No cumple</option>
                </select></td>
               <td><textarea name=\"obs_problema_$i\">{$items_problema['item_'.$i]['observacion']}</textarea></td></tr>";
      }
      ?>
      
      
  </table>
    <div class="text-center mt-4">
        <button type="submit">Guardar</button>
    </div>


  </form>

  <!------------------------------------------obervaciones objetivos------------------------------------------------------------>

  <?php
// Optimización de la extracción de datos desde la base de datos

$sql3 = "SELECT resumen_objetivo FROM asesores_metodologicos WHERE codigo_proyecto = '$codigo_proyecto'";
$consulta_resumen_objetivo = $ins_loginControlador->ejecutar_consultas_simples_two($sql3);

if ($consulta_resumen_objetivo->rowCount() > 0) {
    $resultado_objetivo = $consulta_resumen_objetivo->fetch(PDO::FETCH_ASSOC);
    $resumen_objetivo = json_decode($resultado_objetivo['resumen_objetivo'], true);

    $items = range(1, 7);
    $items_objetivo = [];

    foreach ($items as $i) {
        $items_objetivo["item_$i"] = [
            "valor" => $resumen_objetivo["item_$i"]["valor"] ?? '',
            "observacion" => $resumen_objetivo["item_$i"]["observacion"] ?? '',
            "valores_items" => $resumen_objetivo["item_$i"]["valores_items"] ?? 0.0
        ];
    }

    $suma_valores_objetivo = array_reduce($items_objetivo, function ($carry, $item) {
        return $carry + $item['valores_items'];
    }, 0.0) * 0.091;

    $suma_valores['objetivos'] = $suma_valores_objetivo ?? 0.0;

    // Ahora puedes acceder a $items_problema['item_1']['valor'], etc., sin repetir código

}
?>

  <form class=" mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
  <input type="hidden" name="valor_item_retroalimentacion" value="<?= $ins_loginControlador->encryption(4) ?>">
  <input type="hidden" name="codigo_Proyecto" value="<?= $ins_loginControlador->encryption($codigo_proyecto) ?>">
  <table class="aunar-table aunar-summary mt-3"> 
      <tr>
        <th colspan="4" class="text-center central">OBJETIVOS: responden a la pregunta ¿qué se propone lograr con la investigación? </th>
      </tr>
      <tr>
        <th  class="text-center valores"><?=$suma_valores_objetivo?> </th>
        <th  class="text-center central">Aspectos </th>
        <th  class="text-center central">Calificación </th>
        <th  class="text-center central">OBSERVACION </th>
      </tr>
 
      <?php
      $items = [
          1 => "Guardan coherencia con el problema planteado (¿al cumplir estos objetivos se transforma el estado actual del problema y se logra llegar al estado deseado?)",
          2 => "Factibles de alcanzar en el horizonte de tiempo trazado para la investigación",
          3 => "Indican en forma clara y directa las acciones a realizar",
          4 => "Cumple parametros de escritura (Verbo en infinitivo, en tercera persona)",
          5 => "Es medible, verificable, específico, cuantificable, definido en el tiempo y define el alcance con respecto a la competencia del investigador.",
          6 => "Esta relacionado con la formulacion del problema.",
          7 => "Los objetivos especificos señalan los resultados o metas parciales que facilitan el logro del objetivo general.",
      ];

      foreach ($items as $i => $descripcion) {
          echo "<tr><td>{$items_objetivo['item_'.$i]['valores_items']}</td>
                <td>$descripcion</td>
                <td><select class=\"form-select\" name=\"opcion_item_$i\">
                    <option selected>" . (isset($items_objetivo['item_'.$i]['valor']) ? htmlspecialchars($items_objetivo['item_'.$i]['valor']) : 'Seleccione una opción') . "</option>
                    <option value=\"" . $ins_loginControlador->encryption(1) . "\">No aplica</option>
                    <option value=\"" . $ins_loginControlador->encryption(2) . "\">Cumple</option>
                    <option value=\"" . $ins_loginControlador->encryption(3) . "\">Cumple Parcialmente</option>
                    <option value=\"" . $ins_loginControlador->encryption(4) . "\">No cumple</option>
                </select></td>
               <td><textarea name=\"obs_objetivo_$i\">{$items_objetivo['item_'.$i]['observacion']}</textarea></td></tr>";
      }
      ?>
      
      
  </table>
    <div class="text-center mt-4">
        <button type="submit">Guardar</button>
    </div>

  </form>

  <!------------------------------------------obervaciones justificacion------------------------------------------------------------>

  
  <?php
// Optimización de la extracción de datos desde la base de datos

$sql4 = "SELECT resumen_justificacion FROM asesores_metodologicos WHERE codigo_proyecto = '$codigo_proyecto'";
$consulta_resumen_justificacion = $ins_loginControlador->ejecutar_consultas_simples_two($sql4);

if ($consulta_resumen_justificacion->rowCount() > 0) {
    $resultado_justificacion = $consulta_resumen_justificacion->fetch(PDO::FETCH_ASSOC);
    $resumen_justificacion = json_decode($resultado_justificacion['resumen_justificacion'], true);

    $items = range(1, 3);
    $items_justificacion = [];

    foreach ($items as $i) {
        $items_justificacion["item_$i"] = [
            "valor" => $resumen_justificacion["item_$i"]["valor"] ?? '',
            "observacion" => $resumen_justificacion["item_$i"]["observacion"] ?? '',
            "valores_items" => $resumen_justificacion["item_$i"]["valores_items"] ?? 0.0
        ];
    }

    $suma_valores_justificacion = array_reduce($items_justificacion, function ($carry, $item) {
        return $carry + $item['valores_items'];
    }, 0.0) * 0.091;

    $suma_valores['justificacion'] = $suma_valores_justificacion ?? 0.0;

    // Ahora puedes acceder a $items_problema['item_1']['valor'], etc., sin repetir código

}
?>



  <form class=" mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
  <input type="hidden" name="valor_item_retroalimentacion" value="<?= $ins_loginControlador->encryption(5) ?>">
  <input type="hidden" name="codigo_Proyecto" value="<?= $ins_loginControlador->encryption($codigo_proyecto) ?>">
  <table class="aunar-table aunar-summary mt-3"> 
      <tr>
        <th colspan="4" class="text-center central">JUSTIFICACIÓN: responde a la pregunta ¿por qué hay que hacer la investigación? </th>
      </tr>
      <tr>
        <th  class="text-center valores"><?=$suma_valores_justificacion?> </th>
        <th  class="text-center central">Aspectos </th>
        <th  class="text-center central">Calificación </th>
        <th  class="text-center central">OBSERVACION </th>
      </tr>
 
      <?php
      $items = [
          1 => "Indica el grado de pertinencia de la investigación",
          2 => "Presenta el grado de novedad que tiene la investigación, o su valor teórico y científico-técnico; así como la relevancia para la(s) disciplina(s) que confluyen en el proyecto",
          3 => "Presenta las motivaciones (teoricas, metodologica o practica) que llevan al desarrollo del proyecto." 
      ];

      foreach ($items as $i => $descripcion) {
          echo "<tr><td>{$items_justificacion['item_'.$i]['valores_items']}</td>
                <td>$descripcion</td>
                <td><select class=\"form-select\" name=\"opcion_item_$i\">
                    <option selected>" . (isset($items_justificacion['item_'.$i]['valor']) ? htmlspecialchars($items_justificacion['item_'.$i]['valor']) : 'Seleccione una opción') . "</option>
                    <option value=\"" . $ins_loginControlador->encryption(1) . "\">No aplica</option>
                    <option value=\"" . $ins_loginControlador->encryption(2) . "\">Cumple</option>
                    <option value=\"" . $ins_loginControlador->encryption(3) . "\">Cumple Parcialmente</option>
                    <option value=\"" . $ins_loginControlador->encryption(4) . "\">No cumple</option>
                </select></td>
               <td><textarea name=\"obs_justificacion_$i\">{$items_justificacion['item_'.$i]['observacion']}</textarea></td></tr>";
      }
      ?>
      
      
  </table>
    <div class="text-center mt-4">
        <button type="submit">Guardar</button>
    </div>

  </form>


  <!------------------------------------------obervaciones marcos------------------------------------------------------------>

  <?php
// Optimización de la extracción de datos desde la base de datos

$sql5 = "SELECT resumen_marcos FROM asesores_metodologicos WHERE codigo_proyecto = '$codigo_proyecto'";
$consulta_resumen_marcos = $ins_loginControlador->ejecutar_consultas_simples_two($sql5);

if ($consulta_resumen_marcos->rowCount() > 0) {
    $resultado_marcos = $consulta_resumen_marcos->fetch(PDO::FETCH_ASSOC);
    $resumen_marcos = json_decode($resultado_marcos['resumen_marcos'], true);

    $items = range(1, 8);
    $items_marcos = [];

    foreach ($items as $i) {
        $items_marcos["item_$i"] = [
            "valor" => $resumen_marcos["item_$i"]["valor"] ?? '',
            "observacion" => $resumen_marcos["item_$i"]["observacion"] ?? '',
            "valores_items" => $resumen_marcos["item_$i"]["valores_items"] ?? 0.0
        ];
    }

    $suma_valores_marcos = array_reduce($items_marcos, function ($carry, $item) {
        return $carry + $item['valores_items'];
    }, 0.0) * 0.091;

    $suma_valores['marco'] = $suma_valores_marcos ?? 0.0;
    // Ahora puedes acceder a $items_problema['item_1']['valor'], etc., sin repetir código

}
?>

  <form class=" mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
  <input type="hidden" name="valor_item_retroalimentacion" value="<?= $ins_loginControlador->encryption(6) ?>">
  <input type="hidden" name="codigo_Proyecto" value="<?= $ins_loginControlador->encryption($codigo_proyecto) ?>">
  <table class="aunar-table aunar-summary mt-3"> 
      <tr>
        <th colspan="4" class="text-center central">MARCO DE REFERENCIA (ANTECEDENTES, MARCO TEÓRICO, MARCO CONCEPTUAL, MARCO GEOGRÁFICO, MARCO LEGAL) </th>
      </tr>
      <tr>
        <th  class="text-center valores"><?=$suma_valores_marcos?> </th>
        <th  class="text-center central">Aspectos </th>
        <th  class="text-center central">Calificación </th>
        <th  class="text-center central">OBSERVACION </th>
      </tr>
 
      <?php
      $items = [
          1 => "Los Antecedentes, presentan una revisión detallada de la literatura especializada.",
          2 => "El Marco Teórico, representa un cuerpo unitario, articulado, coherente y consistente con el problema a abordar",
          3 => "El Marco Teórico, emplea diversidad de fuentes de información",
          4 => "El Marco Conceptual, guarda relación directa con el proyecto desarrollado", 
          5 => "El Marco Geografico, delimita la zona geográfica a nivel departamental, regional y local donde se realizará el proyecto", 
          6 => "El Marco Legal, relaciona las leyes, decretos, resoluciones y normas que se relacionan con el objetodel proyecto", 
          7 => "Su presetacion eviencia el grado de profundidad tematica que soporta la investigacion. ", 
          8 => "Facilita la identificación de terminos especificos a ser utilizados en el desarrollo de la investigación.", 
      ];

      foreach ($items as $i => $descripcion) {
          echo "<tr><td>{$items_marcos['item_'.$i]['valores_items']}</td>
                <td>$descripcion</td>
                <td><select class=\"form-select\" name=\"opcion_item_$i\">
                    <option selected>" . (isset($items_marcos['item_'.$i]['valor']) ? htmlspecialchars($items_marcos['item_'.$i]['valor']) : 'Seleccione una opción') . "</option>
                    <option value=\"" . $ins_loginControlador->encryption(1) . "\">No aplica</option>
                    <option value=\"" . $ins_loginControlador->encryption(2) . "\">Cumple</option>
                    <option value=\"" . $ins_loginControlador->encryption(3) . "\">Cumple Parcialmente</option>
                    <option value=\"" . $ins_loginControlador->encryption(4) . "\">No cumple</option>
                </select></td>
               <td><textarea name=\"obs_marcos_$i\">{$items_marcos['item_'.$i]['observacion']}</textarea></td></tr>";
      }
      ?>
      
      
  </table>
    <div class="text-center mt-4">
        <button type="submit">Guardar</button>
    </div>

  </form>

  <!------------------------------------------obervaciones diseño------------------------------------------------------------>

  <?php
// Optimización de la extracción de datos desde la base de datos

$sql6 = "SELECT resumen_diseño FROM asesores_metodologicos WHERE codigo_proyecto = '$codigo_proyecto'";
$consulta_resumen_diseño = $ins_loginControlador->ejecutar_consultas_simples_two($sql6);

if ($consulta_resumen_diseño->rowCount() > 0) {
    $resultado_diseño = $consulta_resumen_diseño->fetch(PDO::FETCH_ASSOC);
    $resumen_diseño = json_decode($resultado_diseño['resumen_diseño'], true);

    $items = range(1, 7);
    $items_diseño = [];

    foreach ($items as $i) {
        $items_diseño["item_$i"] = [
            "valor" => $resumen_diseño["item_$i"]["valor"] ?? '',
            "observacion" => $resumen_diseño["item_$i"]["observacion"] ?? '',
            "valores_items" => $resumen_diseño["item_$i"]["valores_items"] ?? 0.0
        ];
    }

    $suma_valores_diseño = array_reduce($items_diseño, function ($carry, $item) {
        return $carry + $item['valores_items'];
    }, 0.0) * 0.091;

    $suma_valores['diseno'] = $suma_valores_diseño ?? 0.0;
    // Ahora puedes acceder a $items_problema['item_1']['valor'], etc., sin repetir código

}
?>

  <form class=" mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
  <input type="hidden" name="valor_item_retroalimentacion" value="<?= $ins_loginControlador->encryption(7) ?>">
  <input type="hidden" name="codigo_Proyecto" value="<?= $ins_loginControlador->encryption($codigo_proyecto) ?>">
  <table class="aunar-table aunar-summary mt-3"> 
      <tr>
        <th colspan="4" class="text-center central">DISEÑO METODOLÓGICO: responde a la pregunta ¿cómo se realizará la investigación? </th>
      </tr>
      <tr>
        <th  class="text-center valores"><?=$suma_valores_diseño?> </th>
        <th  class="text-center central">Aspectos </th>
        <th  class="text-center central">Calificación </th>
        <th  class="text-center central">OBSERVACION </th>
      </tr>
 
      <?php
      $items = [
          1 => "El tipo de investigación está acorde con los objetivos que se pretenden alcanzar.",
          2 => "Contempla las estrategias, procedimientos, actividades y medios requeridos para cumplir los objetivos propuestos y dar respuesta al problema planteado",
          3 => "Las hipotesis estan formuladas adecuadamente",
          4 => "La investigación esta ajustada a las lineas de investigacion del programa.", 
          5 => "Describe el tipo de investigacion a desarrollarse.", 
          6 => "El tamaño de la muestra esta determinada de manera estadistica.", 
          7 => "Describe el desarrollo de actividades y/o porcentaje de mejoramiento alcanzado."
      ];

      foreach ($items as $i => $descripcion) {
          echo "<tr><td>{$items_diseño['item_'.$i]['valores_items']}</td>
                <td>$descripcion</td>
                <td><select class=\"form-select\" name=\"opcion_item_$i\">
                    <option selected>" . (isset($items_diseño['item_'.$i]['valor']) ? htmlspecialchars($items_diseño['item_'.$i]['valor']) : 'Seleccione una opción') . "</option>
                    <option value=\"" . $ins_loginControlador->encryption(1) . "\">No aplica</option>
                    <option value=\"" . $ins_loginControlador->encryption(2) . "\">Cumple</option>
                    <option value=\"" . $ins_loginControlador->encryption(3) . "\">Cumple Parcialmente</option>
                    <option value=\"" . $ins_loginControlador->encryption(4) . "\">No cumple</option>
                </select></td>
               <td><textarea name=\"obs_diseno_$i\">{$items_diseño['item_'.$i]['observacion']}</textarea></td></tr>";
      }
      ?>
      
      
  </table>
    <div class="text-center mt-4">
        <button type="submit">Guardar</button>
    </div>

  </form>

  <!------------------------------------------obervaciones finalidad------------------------------------------------------------>

  <?php
// Optimización de la extracción de datos desde la base de datos

$sql7 = "SELECT resumen_finalidad FROM asesores_metodologicos WHERE codigo_proyecto = '$codigo_proyecto'";
$consulta_resumen_finalidad = $ins_loginControlador->ejecutar_consultas_simples_two($sql7);

if ($consulta_resumen_finalidad->rowCount() > 0) {
    $resultado_finalidad = $consulta_resumen_finalidad->fetch(PDO::FETCH_ASSOC);
    $resumen_finalidad = json_decode($resultado_finalidad['resumen_finalidad'], true);

    $items = range(1, 6);
    $items_finalidad = [];

    foreach ($items as $i) {
        $items_finalidad["item_$i"] = [
            "valor" => $resumen_finalidad["item_$i"]["valor"] ?? '',
            "observacion" => $resumen_finalidad["item_$i"]["observacion"] ?? '',
            "valores_items" => $resumen_finalidad["item_$i"]["valores_items"] ?? 0.0
        ];
    }

    $suma_valores_finalidad = array_reduce($items_finalidad, function ($carry, $item) {
        return $carry + $item['valores_items'];
    }, 0.0) * 0.091;

    $suma_valores['finalidad'] = $suma_valores_finalidad ?? 0.0;
    // Ahora puedes acceder a $items_problema['item_1']['valor'], etc., sin repetir código

}
?>

  <form class=" mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
  <input type="hidden" name="valor_item_retroalimentacion" value="<?= $ins_loginControlador->encryption(8) ?>">
  <input type="hidden" name="codigo_Proyecto" value="<?= $ins_loginControlador->encryption($codigo_proyecto) ?>">
  <table class="aunar-table aunar-summary mt-3"> 
      <tr>
        <th colspan="4" class="text-center central">FINALIDAD DE LA INVESTIGACIÓN </th>
      </tr>
      <tr>
        <th  class="text-center valores"><?=$suma_valores_finalidad?> </th>
        <th  class="text-center central">Aspectos </th>
        <th  class="text-center central">Calificación </th>
        <th  class="text-center central">OBSERVACION </th>
      </tr>
 
      <?php
      $items = [
          1 => "El proyecto presentado, permite la explicación concreta de la problemática a resolver.",
          2 => "El proyecto presentado, puede o facilita mostrar resultados.",
          3 => "El proyecto presentado, ayuda a mejorar los sistemas y procedimientos.",
          4 => "El proyecto presentado, ayuda a resolver el problema.", 
          5 => "El proyecto presentado, permite dar respuesta o solucion a un problema.", 
          6 => "Muestran claramente hasta que punto se cumplieron los objetivos planteados y o hipotesis."
      ];

      foreach ($items as $i => $descripcion) {
          echo "<tr><td>{$items_finalidad['item_'.$i]['valores_items']}</td>
                <td>$descripcion</td>
                <td><select class=\"form-select\" name=\"opcion_item_$i\">
                    <option selected>" . (isset($items_finalidad['item_'.$i]['valor']) ? htmlspecialchars($items_finalidad['item_'.$i]['valor']) : 'Seleccione una opción') . "</option>
                    <option value=\"" . $ins_loginControlador->encryption(1) . "\">No aplica</option>
                    <option value=\"" . $ins_loginControlador->encryption(2) . "\">Cumple</option>
                    <option value=\"" . $ins_loginControlador->encryption(3) . "\">Cumple Parcialmente</option>
                    <option value=\"" . $ins_loginControlador->encryption(4) . "\">No cumple</option>
                </select></td>
               <td><textarea name=\"obs_finalidad_$i\">{$items_finalidad['item_'.$i]['observacion']}</textarea></td></tr>";
      }
      ?>
      
      
  </table>
    <div class="text-center mt-4">
        <button type="submit">Guardar</button>
    </div>

  </form>


  <!------------------------------------------obervaciones referencias------------------------------------------------------------>

  <?php
// Optimización de la extracción de datos desde la base de datos

$sql8 = "SELECT resumen_referencia FROM asesores_metodologicos_two WHERE codigo_proyecto = '$codigo_proyecto'";
$consulta_resumen_referencia = $ins_loginControlador->ejecutar_consultas_simples_two($sql8);

if ($consulta_resumen_referencia->rowCount() > 0) {
    $resultado_referencia = $consulta_resumen_referencia->fetch(PDO::FETCH_ASSOC);
    $resumen_referencia = json_decode($resultado_referencia['resumen_referencia'], true);

    $items = range(1, 6);
    $items_referencia = [];

    foreach ($items as $i) {
        $items_referencia["item_$i"] = [
            "valor" => $resumen_referencia["item_$i"]["valor"] ?? '',
            "observacion" => $resumen_referencia["item_$i"]["observacion"] ?? '',
            "valores_items" => $resumen_referencia["item_$i"]["valores_items"] ?? 0.0
        ];
    }

    $suma_valores_referencia = array_reduce($items_referencia, function ($carry, $item) {
        return $carry + $item['valores_items'];
    }, 0.0) * 0.091;

    $suma_valores['referencias'] = $suma_valores_referencia ?? 0.0;

    // Ahora puedes acceder a $items_problema['item_1']['valor'], etc., sin repetir código

}
?>

  <form class=" mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
  <input type="hidden" name="valor_item_retroalimentacion" value="<?= $ins_loginControlador->encryption(9) ?>">
  <input type="hidden" name="codigo_Proyecto" value="<?= $ins_loginControlador->encryption($codigo_proyecto) ?>">
  <table class="aunar-table aunar-summary mt-3"> 
      <tr>
        <th colspan="4" class="text-center central">REFERENCIAS BIBLIOGRAFICAS: responde a la pregunta ¿qué documentación se consultó para la formulación del proyecto? </th>
      </tr>
      <tr>
        <th  class="text-center valores"><?=$suma_valores_referencia?> </th>
        <th  class="text-center central">Aspectos </th>
        <th  class="text-center central">Calificación </th>
        <th  class="text-center central">OBSERVACION </th>
      </tr>
 
      <?php
      $items = [
          1 => "Se referencia la documentación citada al interior del texto, como aquella que ha sido consultada pero no necesariamente citada",
          2 => "Se aplica debidamente la norma respectiva para referenciar (APA) "
      ];

      foreach ($items as $i => $descripcion) {
          echo "<tr><td>{$items_referencia['item_'.$i]['valores_items']}</td>
                <td>$descripcion</td>
                <td><select class=\"form-select\" name=\"opcion_item_$i\">
                    <option selected>" . (isset($items_referencia['item_'.$i]['valor']) ? htmlspecialchars($items_referencia['item_'.$i]['valor']) : 'Seleccione una opción') . "</option>
                    <option value=\"" . $ins_loginControlador->encryption(1) . "\">No aplica</option>
                    <option value=\"" . $ins_loginControlador->encryption(2) . "\">Cumple</option>
                    <option value=\"" . $ins_loginControlador->encryption(3) . "\">Cumple Parcialmente</option>
                    <option value=\"" . $ins_loginControlador->encryption(4) . "\">No cumple</option>
                </select></td>
               <td><textarea name=\"obs_referencia_$i\">{$items_referencia['item_'.$i]['observacion']}</textarea></td></tr>";
      }
      ?>
      
      
  </table>
    <div class="text-center mt-4">
        <button type="submit">Guardar</button>
    </div>

  </form>

    <!------------------------------------------obervaciones anexo------------------------------------------------------------>

    
  <?php
// Optimización de la extracción de datos desde la base de datos

$sql9 = "SELECT resumen_anexo FROM asesores_metodologicos_two WHERE codigo_proyecto = '$codigo_proyecto'";
$consulta_resumen_anexo = $ins_loginControlador->ejecutar_consultas_simples_two($sql9);

if ($consulta_resumen_anexo->rowCount() > 0) {
    $resultado_anexo = $consulta_resumen_anexo->fetch(PDO::FETCH_ASSOC);
    $resumen_anexo = json_decode($resultado_anexo['resumen_anexo'], true);

    $items = range(1, 6);
    $items_anexo = [];

    foreach ($items as $i) {
        $items_anexo["item_$i"] = [
            "valor" => $resumen_anexo["item_$i"]["valor"] ?? '',
            "observacion" => $resumen_anexo["item_$i"]["observacion"] ?? '',
            "valores_items" => $resumen_anexo["item_$i"]["valores_items"] ?? 0.0
        ];
    }

    $suma_valores_anexo = array_reduce($items_anexo, function ($carry, $item) {
        return $carry + $item['valores_items'];
    }, 0.0) * 0.091;

    $suma_valores['anexos'] = $suma_valores_anexo ?? 0.0;

    // Ahora puedes acceder a $items_problema['item_1']['valor'], etc., sin repetir código

}
?>
    
  <form class=" mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
  <input type="hidden" name="valor_item_retroalimentacion" value="<?= $ins_loginControlador->encryption(10) ?>">
  <input type="hidden" name="codigo_Proyecto" value="<?= $ins_loginControlador->encryption($codigo_proyecto) ?>">
  <table class="aunar-table aunar-summary mt-3"> 
      <tr>
        <th colspan="4" class="text-center central">ANEXOS: corresponden a la información que soporta o complementa el proyecto formulado</th>
      </tr>
      <tr>
        <th  class="text-center valores"><?=$suma_valores_anexo?> </th>
        <th  class="text-center central">Aspectos </th>
        <th  class="text-center central">Calificación </th>
        <th  class="text-center central">OBSERVACION </th>
      </tr>
 
      <?php
      $items = [
          1 => "Se encuentran debidamente identificados con letras o números según corresponda. ",
          2 => "Indican la fuente respectiva  "
      ];

      foreach ($items as $i => $descripcion) {
          echo "<tr><td>{$items_anexo['item_'.$i]['valores_items']}</td>
                <td>$descripcion</td>
                <td><select class=\"form-select\" name=\"opcion_item_$i\">
                    <option selected>" . (isset($items_anexo['item_'.$i]['valor']) ? htmlspecialchars($items_anexo['item_'.$i]['valor']) : 'Seleccione una opción') . "</option>
                    <option value=\"" . $ins_loginControlador->encryption(1) . "\">No aplica</option>
                    <option value=\"" . $ins_loginControlador->encryption(2) . "\">Cumple</option>
                    <option value=\"" . $ins_loginControlador->encryption(3) . "\">Cumple Parcialmente</option>
                    <option value=\"" . $ins_loginControlador->encryption(4) . "\">No cumple</option>
                </select></td>
               <td><textarea name=\"obs_anexo_$i\">{$items_anexo['item_'.$i]['observacion']}</textarea></td></tr>";
      }
      ?>
      
      
  </table>
    <div class="text-center mt-4">
        <button type="submit">Guardar</button>
    </div>

  </form>


    <!------------------------------------------obervaciones postulacion------------------------------------------------------------>
    <?php
$sql10 = "SELECT resumen_postulacion  FROM asesores_metodologicos_two WHERE codigo_proyecto = '$codigo_proyecto'";
$consulta_resumen_postulacion = $ins_loginControlador->ejecutar_consultas_simples_two($sql10);

if ($consulta_resumen_postulacion->rowCount() > 0) {
    $resultado_postulacion = $consulta_resumen_postulacion->fetch(PDO::FETCH_ASSOC);
    $resumen_postulacion = json_decode($resultado_postulacion['resumen_postulacion'], true);

    $items = range(1, 3);
    $items_postulacion = [];

    $valor_item_1 = (float) 0.0;
    $valor_item_2 = (float) 0.0;

    foreach ($items as $i) {
        $valor = $resumen_postulacion["item_$i"]["valor"] ?? '';
        $valores_items = $resumen_postulacion["item_$i"]["valores_items"] ?? 0.0;

        if ($i == 1 && $valor === 'Si') {
            $valor_item_1 = (float) 0.987;
        } elseif ($i == 2 && $valor === 'Si') {
            $valor_item_2 = (float) 1.087;
        }

        $items_postulacion["item_$i"] = [
            "valor" => $valor,
            "observacion" => $resumen_postulacion["item_$i"]["observacion"] ?? '',
            "valores_items" => $valores_items
        ];
    }

    $suma_valores_postulacion = $valor_item_1 + $valor_item_2;

    $suma_valores['evaluacion'] = $suma_valores_postulacion ?? 0.0;
}
?>
    
  <form class=" mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
  <input type="hidden" name="valor_item_retroalimentacion" value="<?= $ins_loginControlador->encryption(11) ?>">
  <input type="hidden" name="codigo_Proyecto" value="<?= $ins_loginControlador->encryption($codigo_proyecto) ?>">
  <table class="aunar-table aunar-summary mt-3"> 
      <tr>
        <th colspan="4" class="text-center central">POSTULACIÓN: Elija una sola obción</th>
      </tr>
      <tr>
        <th  class="text-center valores"><?=$suma_valores_postulacion?> </th>
        <th  class="text-center central">Aspectos </th>
        <th  class="text-center central">Calificación </th>
        <th  class="text-center central">OBSERVACION </th>
      </tr>
 
      <?php
      $items = [
          1 => "Considera que el proyecto de investigación cumple con las condiciones para ser propuesto como trabajo meritorio (tiene amplias repercusiones científicas, sociales, culturales, técnicas o tecnológicas.) ",
          2 => "Considera que el proyecto de investigación cumple con las condiciones para ser propuesto como trabajo Laureado (hace un aporte significativo a su respectiva área del conocimiento.) ",
          3 => "Considera que el proyecto de investigación no presenta suficientes condiciones para ser propuesto como trabajo meritorio o Laureado.  "
      ];

      foreach ($items as $i => $descripcion) {
        
        if($i==1 || $i == 2){
          $valor_item = $items_postulacion['item_'.$i]['valores_items'];
        }else{
          $valor_item = $suma_valores_postulacion;
        }

          echo "<tr><td>{$valor_item}</td>
                <td>$descripcion</td>
                <td><select class=\"form-select\" name=\"opcion_item_$i\">
                    <option selected>" . (isset($items_postulacion['item_'.$i]['valor']) ? htmlspecialchars($items_postulacion['item_'.$i]['valor']) : 'Seleccione una opción') . "</option>
                    <option value=\"" . $ins_loginControlador->encryption(1) . "\">Si</option>
                    <option value=\"" . $ins_loginControlador->encryption(2) . "\">No</option>
                </select></td>
               <td><textarea name=\"obs_postulacion_$i\">{$items_postulacion['item_'.$i]['observacion']}</textarea></td></tr>";
      }
      ?>
      
      
  </table>
    <div class="text-center mt-4">
        <button type="submit">Guardar</button>
    </div>

  </form>


    <div class="container text-center mt-5">
      <!-- Tabla Puntajes -->
      
      <?php

            $numero_documento_user = $_SESSION['numero_documento'];

            $consulta_firma_usuarios = "SELECT * FROM firma_digital_usuarios WHERE numero_documento = '$numero_documento_user'";
            $consulta_exec_resultado = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_firma_usuarios);
            
            // Verificar si se obtuvieron resultados
            if ($consulta_exec_resultado && $consulta_exec_resultado->rowCount() > 0) {
                $resultado_usuario_firma = $consulta_exec_resultado->fetch(PDO::FETCH_ASSOC);
                $firma_usuario = $resultado_usuario_firma['firma'];
              
                  $mostrar_imagen = '<div class="mostrar_firma_registrada two">
                              <img src="'. SERVERURL.'Views/assets/images/FimasUsuarios/'.$firma_usuario.'" alt="firma_usuarios">
                            </div>';
          
            } else {
                
                $mostrar_imagen = '';


            }
            
              
              ?>

      <!-- Firma -->
      <div class="row justify-content-center mt-5">
          <div class="col-md-8">
              <?=$mostrar_imagen?>
              <hr>
              <p class="text-left"><strong>FIRMA JURADO :</strong></p>
            
              <div class="signature-line"></div>
          </div>
      </div>
    </div>


</div>



<script>


// Función para asignar valores a inputs desde un arreglo
function asignarValoresInputs(ids, valores) {
    let sumaTotal = 0;
    for (let i = 0; i < ids.length; i++) {
        let input = document.getElementById(ids[i]);
        if (input) {
            let valor = (valores[i] !== undefined && valores[i] !== null) ? parseFloat(valores[i]).toFixed(1) : '0.0';
            input.value = valor;
            sumaTotal += parseFloat(valor);
        }
    }
    // Mostrar la suma total en un elemento con id especifico
    let totalElement = document.getElementById('total_suma');
    if (totalElement) {
        totalElement.value = sumaTotal.toFixed(1);
    }
}

// Pasar directamente el arreglo PHP $suma_valores
const suma_valores = <?= json_encode($suma_valores); ?>;
const idsInputs = ['titulo', 'problema', 'objetivos', 'justificacion', 'marco', 'diseno', 'finalidad', 'referencias', 'anexos', 'evaluacion'];

// Asignar valores a los inputs
asignarValoresInputs(idsInputs, Object.values(suma_valores));


  document.addEventListener('input', function (e) {
    if (e.target.tagName.toLowerCase() === 'textarea') {
      e.target.style.height = 'auto';
      e.target.style.height = (e.target.scrollHeight) + 'px';
    }
  }, false);
</script>