
<?php
if ($_SESSION['privilegio'] != 1 && $_SESSION['privilegio'] != 4) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
  echo $ins_loginControlador->cerrar_sesion_controlador();
  exit();
}


if (isset($_GET['views'])) {
  $ruta = explode(
      "/",
      $_GET['views']
  );

  $codigo = $ruta[1];
 
}
?>

<div class="contenedor-main-see-jurados-proyectos">
<?php

if(isset($codigo)){


    $verificar_jurado_proyecto = "SELECT 
      ajp.codigo_proyecto,
      ajp.numero_documento,
      u.nombre_usuario,
      u.apellidos_usuario,
      u.correo_usuario,
      u.telefono_usuario,
      ajp.opcion_jurado,
      ajp.fecha_creacion
  FROM Asignar_jurados_proyecto ajp
  INNER JOIN usuarios u ON ajp.numero_documento = u.numero_documento
  WHERE ajp.codigo_proyecto = '$codigo'";

  $resultado_verificar_jurado_proyecto = $ins_loginControlador->ejecutar_consultas_simples_two($verificar_jurado_proyecto);

  if ($resultado_verificar_jurado_proyecto->rowCount() > 0) {

      echo '<div class="alert alert-info text-center" role="alert">
      El proyecto  tiene jurados asignados.
    </div>';

     /******************************extraer fecha vigencia  ************************************** */

      $consultar_fecha_vigencia= "SELECT * FROM evaluaciones_proyectos WHERE codigo_proyecto = '$codigo'";

      $resultado_fecha_vigencia = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_fecha_vigencia);

      if ($resultado_fecha_vigencia->rowCount() > 0) {
          $fecha_data = $resultado_fecha_vigencia->fetch(PDO::FETCH_ASSOC);
          $fecha_vigencia = $fecha_data['fecha']; // o el nombre exacto de la columna que contiene la fecha

          $total_jurado1 = $fecha_data['calificacion_jurado1'];

          $total_jurado2 = $fecha_data['calificacion_jurado2'];


          if($fecha_vigencia != ""){
            echo ' <div class="alert alert-success " role="alert">
            <div class="text-center"> la fecha de sustentación es el dia: ' . $fecha_vigencia . '  </div>
          </div>';
          }

          if (($total_jurado1 !== null && $total_jurado1 > 0) || ($total_jurado2 !== null && $total_jurado2 > 0)) {
           
            $promedio_totales = ($total_jurado1 + $total_jurado2) / 2;

            $promedio_redondeado = round($promedio_totales, 0);

            ?>
            <div class="card-resultado-evaluacion">
              <div class="contenido-resultado">
                  <h3 class="text-center">📘 Resultado de la Evaluación Final</h3>

                  <p><strong>🧑 Jurado 1:</strong> <?= $total_jurado1 ?> puntos</p>
                  <p><strong>🧑 Jurado 2:</strong> <?= $total_jurado2 ?> puntos</p>

                  <div class="linea-separadora"></div>

                  <p><strong>📊 Promedio:</strong> <?= $promedio_redondeado ?> puntos</p>

                  <div class="resultado-final 
                      <?php 
                          if ($promedio_redondeado < 70) {
                              echo 'texto-reprobado';
                          } elseif ($promedio_redondeado <= 94) {
                              echo 'texto-aprobado';
                          } elseif ($promedio_redondeado <= 99) {
                              echo 'texto-sobresaliente';
                          } else {
                              echo 'texto-laureado';
                          }
                      ?>">
                      <?php 
                          if ($promedio_redondeado < 70) {
                              echo "❌ Proyecto Reprobado";
                          } elseif ($promedio_redondeado <= 94) {
                              echo "✅ Proyecto Aprobado";
                          } elseif ($promedio_redondeado <= 99) {
                              echo "🌟 Proyecto Sobresaliente";
                          } else {
                              echo "🏅 Proyecto Laureado (Perfecto)";
                          }
                      ?>
                  </div>
              </div>
          </div>



            <?php

              
          }
        
        }
          
      } else {
        
          echo ' <div class="alert alert-success alertas-ms " role="alert">
          <div class="text-center"> No se ha establecido una fecha para la sustentación   </div>
        </div>';
      }


   

    echo '<div class="jurado-container">';
      while ($fila = $resultado_verificar_jurado_proyecto->fetch(PDO::FETCH_ASSOC)) {
        // Verificamos si tiene imagen o usamos una por defecto
        $ruta_imagen = !empty($fila['imagen_usuario']) 
        ? SERVERURL . 'Views/assets/images/avatar/' . $fila['imagen_usuario'] 
        : SERVERURL . 'Views/assets/images/avatar/AvatarNone.png';

        $rol_jurado = '';
        switch ($fila['opcion_jurado']) {
            case 1:
                $rol_jurado = 'Jurado principal';
                break;
            case 2:
                $rol_jurado = 'Jurado secundario';
                break;
            default:
                $rol_jurado = 'Sin definir rol';
                break;
        }
    
        echo '
        <div class="jurado-box">
          <div class="jurado-img">
            <img src="' . $ruta_imagen . '" alt="Foto del jurado">
          </div>
          <div class="jurado-info">
            <h5>' . $fila['nombre_usuario'] . ' ' . $fila['apellidos_usuario'] . '</h5>
            <p><strong>Correo:</strong><br>' . $fila['correo_usuario'] . '</p>
            <p><strong>Teléfono:</strong><br>' . $fila['telefono_usuario'] . '</p>
            <p><strong>Rol como jurado:</strong> 
              <span class="jurado-badge">' . $rol_jurado . '</span>
            </p>
            <p class="fecha-asignacion">Asignado el: ' . $fila['fecha_creacion'] . '</p>
          </div>
        </div>
        ';
    }
    echo '</div>';
  
  } else {
      echo '<div class="alert alert-danger text-center" role="alert">
           El proyecto no tiene jurados asignados.
          </div>';

  }
  


?>
</div>
