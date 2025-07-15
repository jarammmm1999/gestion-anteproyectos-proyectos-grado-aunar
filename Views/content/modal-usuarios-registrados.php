<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Usuarios Registrados</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
    
      <?php
      $consulta_usuarios_registrados = "SELECT SQL_CALC_FOUND_ROWS 
      u.id AS ID_Usuario,
      u.numero_documento,
      u.nombre_usuario,
      u.apellidos_usuario,
      u.correo_usuario,
      u.telefono_usuario,
      ru.nombre_rol,
      u.estado,
      u.imagen_usuario,
      u.created_at
      FROM usuarios u
      INNER JOIN roles_usuarios ru ON u.id_rol = ru.id_rol
      WHERE u.id != '$id' AND u.id != '1'
      ORDER BY u.nombre_usuario ASC";

      $resultados_usuario = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_usuarios_registrados);

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
                  <th>Imagen</th>
                  <th>Documento</th>
                  <th>Nombre</th>
                  <th>Apellidos</th>
                  <th>Correo</th>
                  <th>Teléfono</th>
                  <th>Rol</th>
                  <th>Estado</th>
                  <th>Facultad</th>
                  <th>Programa</th>
              </tr>
          </thead>
          <tbody>
              <?php
              $contador = 1;
              while ($usuario = $resultados_usuario->fetch(PDO::FETCH_ASSOC)) {

                $numero_documento_f = $usuario['numero_documento'];
                
                $consulta_usuario_facultad = "SELECT 
                GROUP_CONCAT(DISTINCT f.nombre_facultad SEPARATOR ', ') AS facultades,
                GROUP_CONCAT(DISTINCT p.nombre_programa SEPARATOR ', ') AS programas
            FROM Asignar_usuario_facultades auf
            INNER JOIN facultades f ON auf.id_facultad = f.id_facultad
            LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
            WHERE auf.numero_documento = '$numero_documento_f'
            GROUP BY auf.numero_documento";
            
            $resultados_usuario_facultad = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_usuario_facultad);
            
            if ($resultados_usuario_facultad->rowCount() > 0) {
                // Se extrae la fila de resultado
                $datos = $resultados_usuario_facultad->fetch(PDO::FETCH_ASSOC);
                $facultades = $datos['facultades'];
                $programas  = $datos['programas'];
            } else {
                $facultades = '<span class="badge bg-danger">Sin asignar</span>';
                $programas  = '<span class="badge bg-danger">Sin asignar</span>';
            }
            
              

                  echo "<tr>";
                  echo "<td>{$contador}</td>"; 
                  echo "<td><img src=" . SERVERURL . "Views/assets/images/avatar/{$usuario['imagen_usuario']} alt='Imagen de usuario' width='50'></td>";
                  echo "<td>{$usuario['numero_documento']}</td>";
                  echo "<td>{$usuario['nombre_usuario']}</td>";
                  echo "<td>{$usuario['apellidos_usuario']}</td>";
                  echo "<td>{$usuario['correo_usuario']}</td>";
                  echo "<td>{$usuario['telefono_usuario']}</td>";
                  echo "<td>{$usuario['nombre_rol']}</td>";
                  echo "<td>" . ($usuario['estado'] == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>') . "</td>";
                  echo "<td>{$facultades}</td>";
                  echo "<td>{$programas}</td>";
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