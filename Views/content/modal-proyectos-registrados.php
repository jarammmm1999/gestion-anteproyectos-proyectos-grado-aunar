<!-- Modal -->
<div class="modal fade" id="staticBackdroproyecto" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Proyectos Registrados</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
    
      <?php
      $consulta_proyecto_registrados = "SELECT SQL_CALC_FOUND_ROWS 
        a.codigo_proyecto,
        a.titulo_proyecto,
        a.palabras_claves,
        a.estado,
        p.nombre_programa,
        f.nombre_facultad,
        CASE 
            WHEN aa.codigo_proyecto IS NOT NULL THEN 'Sí'
            ELSE 'No'
        END AS tiene_asesor
    FROM proyectos a
    INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
    INNER JOIN facultades f ON p.id_facultad = f.id_facultad
    LEFT JOIN Asignar_asesor_anteproyecto_proyecto aa ON a.codigo_proyecto = aa.codigo_proyecto;";

      $resultados_proyecto = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_proyecto_registrados);

      ?>
        <!-- Campo de búsqueda -->
        <div class="mb-3">
            <input type="text" id="buscador" class="form-control" placeholder="Buscar usuario...">
        </div>

      <div class="table-responsive">
      <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
          <thead class="table-dark">
              <tr>
                  <th>ID</th>
                  <th>Codigo</th>
                  <th>Titulo</th>
                  <th>Palabras Claves</th>
                  <th>Esatdo</th>
                  <th>Facultad</th>
                  <th>Programa</th>
                  <th>Tiene Director</th>
                  <th>Tiene Estudiantes</th>
                  
              </tr>
          </thead>
          <tbody>
              <?php
              $contador = 1;
              while ($proyecto = $resultados_proyecto->fetch(PDO::FETCH_ASSOC)) {
                $estados_proyectos = $proyecto['estado'];

                $estados_asesor_proyectos = $proyecto['tiene_asesor'];

                if ($estados_proyectos == "Aprobado") {

                    $estados_proyectos = '<span class="badge bg-success">Aprobado</span>'  ;

                }else if($estados_proyectos == "Revisión"){

                    $estados_proyectos = '<span class="badge bg-info">Revisión</span>'  ;
                }
                else{
                    // Extraer el estado del usuario
                   
                    $estados_proyectos = '<span class="badge bg-danger">Cancelado</span>'  ;
                }


                if ($estados_asesor_proyectos == "No") {

                  $estados_asesor_proyectos = '<span class="badge bg-danger">'. $estados_asesor_proyectos.'</span>'  ;

                }else if ($estados_asesor_proyectos =="Sí"){
                  $estados_asesor_proyectos = '<span class="badge bg-success">'. $estados_asesor_proyectos.'</span>'  ;
                }


                $codigo_proyecto_e = $proyecto['codigo_proyecto'];

                $consulta_estudiantes_asignados = "SELECT COUNT(*) as total 
                    FROM asignar_estudiante_proyecto 
                    WHERE codigo_proyecto = '$codigo_proyecto_e'";

                $stmt_estudiantes = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_estudiantes_asignados);
                $result_estudiantes = $stmt_estudiantes->fetch(PDO::FETCH_ASSOC);
                $tiene_estudiantes = ($result_estudiantes['total'] > 0) ?  '<span class="badge bg-success">Tiene estudiantes asignados</span>': '<span class="badge bg-danger">No tiene estudiantes asignados</span>';


                  echo "<tr>";
                  echo "<td>{$contador}</td>";
                  echo "<td>{$proyecto['codigo_proyecto']}</td>";
                  echo "<td>{$proyecto['titulo_proyecto']}</td>";
                  echo "<td>{$proyecto['palabras_claves']}</td>";
                  echo "<td>{$estados_proyectos}</td>";
                  echo "<td>{$proyecto['nombre_facultad']}</td>";
                  echo "<td>{$proyecto['nombre_programa']}</td>";
                  echo "<td>{$estados_asesor_proyectos}</td>";
                  echo "<td>{$tiene_estudiantes}</td>";
                  echo "</tr>";
                $contador++; 
              }
              ?>
          </tbody>
      </table>
        </div>

      <?php

      ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
    
document.getElementById("buscador").addEventListener("keyup", function () {
            let input = this.value.toLowerCase();
            let filas = document.querySelectorAll("#tabla_usuarios tbody tr");

            filas.forEach(fila => {
                let textoFila = fila.textContent.toLowerCase();
                fila.style.display = textoFila.includes(input) ? "" : "none";
            });
        });
</script>