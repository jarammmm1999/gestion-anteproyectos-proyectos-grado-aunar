<?php

$codigo_proyecto = $url_pagina[1];

$consulta = "SELECT * FROM proyectos where codigo_proyecto = :datos";

$consulta = $ins_loginControlador->consulta_information($codigo_proyecto, $consulta);

if ($consulta->rowCount() == 1) {

   // Obtener los datos de la idea
   $proyecto = $consulta->fetch(PDO::FETCH_ASSOC);

   $id_proyecto = $proyecto['id_proyecto'];

   $codigo = $proyecto['codigo_proyecto'];

   $titulo = $proyecto['titulo_proyecto'];

   $palabrasClaves = $proyecto['palabras_claves'];

   $estado_proyecto = $proyecto['estado'];


?>

            <form class="user-form mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="update" autocomplete="off">
                <h2><i class="fa-solid fa-user-pen"></i> Información proyecto</h2>

                
                <div class="form-grid two">
                    <input type="hidden" name="codigo_proyecto_upd_estado" value="<?= $ins_loginControlador->encryption($codigo) ?>" readonly>

                    <div class="form-floating">
                        <input type="text" class="form-control input_border" id="floatingNombre" name="titulo_proyecto_upd"
                            placeholder="Password" value="<?= $titulo ?>">
                        <label for="floatingNombre mb-4">Titulo Proyecto</label>
                    </div>

                    <select class="form-select" name="actualizar_estado_proyecto" aria-label="Default select example">
                            <option selected>Estado</option>
                            <option value="<?= $ins_loginControlador->encryption(1) ?>">En revisión</option>
                            <option value="<?= $ins_loginControlador->encryption(2) ?>">Aprobado</option>
                            <option value="<?= $ins_loginControlador->encryption(3) ?>">Cancelado</option>
                        </select>

                </div>


                <div class="form-actions mt-5 mb-5">
                    <button type="submit"><i class="fa-solid fa-pen"></i> &nbsp; Actualizar Proyecto</button>
                </div>
            </form>
         

 <!---------- consultar los usuarios asignados a la idea --------->

 <div class="container-fluid">
  <div class="container-table-user">
    <div class="continer-search mt-5 mb-3">
      <input type="text"  id="buscar" onkeyup="buscarTabla()" placeholder="Buscar en la tabla...">
    </div>
    <?php


$consulta_usuarios_asignados = "SELECT ae.numero_documento, u.nombre_usuario AS nombre, u.apellidos_usuario AS apellidos, u.correo_usuario AS correo
FROM asignar_estudiante_proyecto ae
INNER JOIN usuarios u ON ae.numero_documento = u.numero_documento
WHERE ae.codigo_proyecto = :codigo;
";


// Ejecutar la consulta enviando el parámetro $codigo
$sql = $ins_loginControlador->consulta_informationtwo($consulta_usuarios_asignados, [':codigo' => $codigo]);

if ($sql->rowCount() > 0) {

    $usuarios_asignados = $sql->fetchAll(PDO::FETCH_ASSOC);

   
       
    
    // Construir la tabla con Bootstrap
        echo '<div class="table-responsive mt-2 mb-5">
        <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
            <thead class="table-dark">
                <tr>
                    <th>Número de Documento</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Correo</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>';


                
            foreach ($usuarios_asignados as $usuario) {

                // Validar que el número de documento no esté vacío
                if (!empty($usuario['numero_documento'])) {
                    $encriptado = $ins_loginControlador->encryption($usuario['numero_documento']);
                } else {
                    echo "Número de documento vacío o no definido para el usuario: " . $usuario['nombre'] . "<br>";
                    continue; // Saltar a la siguiente iteración si no hay número de documento
                }
                ?>
                <tr>
                    <td><?php echo $usuario['numero_documento']; ?></td>
                    <td><?php echo $usuario['nombre']; ?></td>
                    <td><?php echo $usuario['apellidos']; ?></td>
                    <td><?php echo $usuario['correo']; ?></td>
                    <td>
                        <form class="FormulariosAjax" action="<?php echo SERVERURL; ?>Ajax/ProyectoAjax.php" method="POST" autocomplete="off" data-form="delete">
                            <input type="hidden" name="documento_userDP" value="<?php echo $encriptado; ?>">
                            <input type="hidden" name="codigoDP" value="<?php echo $ins_loginControlador->encryption($codigo) ?>">
                            <button type="submit" class="btn btn-danger">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
        <?php
            }


    // Cerrar la tabla y el contenedor
    echo '   </tbody>
        </table>
    </div>';
} else {
    // Si no se encontraron registros, mostrar el mensaje de advertencia
    echo '<div class="alert alert-warning" role="alert">
    <div class="text-center">No se encontraron usuarios asignados a la idea de proyecto</div>
    </div>';
}



?>
  </div>
</div> 

    <form class="user-form mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="update" autocomplete="off">
        <h2><i class="fa-solid fa-user-pen"></i> Información  proyecto</h2>


        <div class="form-grid two">
            <input type="hidden" name="codigo_proyecto_upd" value="<?= $ins_loginControlador->encryption($codigo) ?>" readonly>

            <div class="form-floating">
                <input type="text" class="form-control input_border" id="floatingNombre" name="titulo_proyecto_upd"
                    placeholder="Password" value="<?= $titulo ?>">
                <label for="floatingNombre mb-4">Titulo Proyecto</label>
            </div>

            <div class="form-floating">
                <input type="text" class="form-control input_border" id="floatingNombre" name="palabras_clavesP_upd"
                    placeholder="Password" value="<?= $palabrasClaves ?>">
                <label for="floatingNombre mb-4">Palabras claves</label>
            </div>

        </div>


        <div class="form-actions mt-5 mb-5">
            <button type="submit"><i class="fa-solid fa-pen"></i> &nbsp; Actualizar Proyecto</button>
        </div>
    </form>



<?php


} else {
    echo '<div class="alert alert-warning" role="alert">
            <div class="text-center">  No se encontro información de la idea de anteproyecto</div>
          </div>';
}


/********************************************************************************/
?>
 <div class="container-fluid">
 <div class="container-table-user">
    <?php
    if(isset($codigo)){
        $consulta_asesor = "SELECT 
        ae.codigo_proyecto, 
        ae.titulo_proyecto, 
        ae.palabras_claves, 
        ua.nombre_usuario AS nombre_asesor,  
        ua.apellidos_usuario AS apellidos_asesor,
        ua.correo_usuario,
        ua.id_rol,
        ua.numero_documento  
        FROM proyectos ae
        LEFT JOIN Asignar_asesor_anteproyecto_proyecto ap ON ae.codigo_proyecto = ap.codigo_proyecto
        LEFT JOIN usuarios ua ON ap.numero_documento = ua.numero_documento
        WHERE ae.codigo_proyecto = '$codigo'
        GROUP BY ae.codigo_proyecto, ae.titulo_proyecto, ae.palabras_claves, ua.nombre_usuario, ua.apellidos_usuario;
        ";
    
        $resultado_consulta_asesor = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_asesor);

        if ($resultado_consulta_asesor->rowCount() > 0) {

            $datos_asesor = $resultado_consulta_asesor->fetchAll(PDO::FETCH_ASSOC);

    

            if (!empty($usuario['numero_documento']) && $usuario['numero_documento'] !== null) {
                 // Construir la tabla con Bootstrap
             echo '<div class="table-responsive mt-2 mb-5">
              <div class="text-center mt-2 mb-5">
             <h4>Información Directores</h4>
             </div>
             <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
                 <thead class="table-dark">
                     <tr>
                         <th>Documento</th>
                         <th>Nombre</th>
                         <th>Apellidos</th>
                         <th>Correo</th>
                         <th>Tipo</th>
                         <th>Editar</th>
                         <th>Eliminar</th>
                     </tr>
                 </thead>
                 <tbody>';
 
                 foreach ($datos_asesor as $usuario) {

                    if($usuario['id_rol'] == 5){
                        $tipo = '<span class="badge bg-success">Director</span>';
                    }else{
                        $tipo = '<span class="badge bg-warning text-dark">Director Externo</span>';
                    }

                ?>
                    <tr>
                        <td><?php echo !empty($usuario['numero_documento']) ? $usuario['numero_documento'] : 'Sin asignar'; ?></td>
                        <td><?php echo !empty($usuario['nombre_asesor']) ? $usuario['nombre_asesor'] : 'Sin asignar'; ?></td>
                        <td><?php echo !empty($usuario['apellidos_asesor']) ? $usuario['apellidos_asesor'] : 'Sin asignar'; ?></td>
                        <td><?php echo !empty($usuario['correo_usuario']) ? $usuario['correo_usuario'] : 'Sin asignar'; ?></td>
                        <td><?= $tipo; ?></td>
                        <?php
                        if(!empty($usuario['numero_documento'])){
                            if($usuario['id_rol'] == 5){
                                ?>
                                    <td> 
                                        <button type="submit" class="btn btn-info"  data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </td>
                                <?php
                            }else{
                            
                                ?>
                                <td> 
                                    <button type="submit" class="btn btn-warning"  >
                                    <i class="fa-solid fa-lock"></i>
                                    </button>
                                </td>
                            <?php

                            }
            
                        ?>
                        <td>
                            <form class="FormulariosAjax" action="<?php echo SERVERURL; ?>Ajax/AnteproyectoAjax.php" method="POST" autocomplete="off" data-form="delete">
                                <input type="hidden" name="documento_user_asesor" value="<?php echo $ins_loginControlador->encryption($usuario['numero_documento']) ?>">
                                <input type="hidden" name="codigoProyecto" value="<?php echo $ins_loginControlador->encryption($codigo) ?>">
                                <button type="submit" class="btn btn-danger">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <?php } else {?>

                         <td><?php echo !empty($usuario['correo_usuario']) ? $usuario['correo_usuario'] : 'Sin asignar'; ?></td>
                         <td><?php echo !empty($usuario['correo_usuario']) ? $usuario['correo_usuario'] : 'Sin asignar'; ?></td>
                    <?php }?>

                    <?php
                    
                    if($usuario['id_rol'] == 5){
                        ?>
                    <div class="collapse" id="collapseExample">
                    <div class="card card-body  actualizar_asesor_anteproyecto">

                      <form class="user-for  mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/UsuarioAjax.php" method="POST" data-form="save" autocomplete="off">
                        <h3 class="mt-2 mb-5"><i class="fas fa-user"></i> Información básica usuario</h3>

                        <div class="form-grid">


                            <div class="form-floating">
                            <input type="number" class="form-control " id="floatingDocumento" name="documento_asesor_actualizar" placeholder="Numero de documento" onchange="BuscarUsuario(this.value, '<?= SERVERURL ?>')">
                            <label for="floatingDocumento">Numero de documento</label>
                            </div>
                            <input type="hidden" name="codigo_proyecto" value=" <?= $ins_loginControlador->encryption(2)?>" readonly>
                            <input type="hidden" name="codigo_idea_actualizar_asesor" value="<?= $ins_loginControlador->encryption($codigo) ?>" readonly>

                            <div class="form-floating">
                            <input type="text" class="form-control input_border" id="nombreusuario" placeholder="Password" disabled>
                            <label for="floatingNombre mb-4">Nombre de usuario</label>
                            </div>

                            <div class="form-floating">
                            <input type="text" class="form-control input_border" id="apellidousuario"
                                placeholder="Password" disabled>
                            <label for="floatingNombre mb-4">Apellidos de usuario</label>
                            </div>

                            <div class="form-floating">
                            <input type="email" class="form-control input_border" id="correo"
                                placeholder="Password" disabled>
                            <label for="floatingNombre mb-4">Correo de usuario</label>
                            </div>

                            <div class="form-floating">
                            <input type="text" class="form-control input_border" id="telefono"
                                placeholder="Password" disabled>
                            <label for="floatingNombre mb-4">Telefono de usuario</label>
                            </div>

                            <div class="form-floating">
                            <input type="text" class="form-control input_border" id="rol"
                                placeholder="Password" disabled>
                            <label for="floatingNombre mb-4">Rol usuario</label>
                            </div>


                        </div>
                        <div class="form-actions mt-5 mb-5">
                            <button type="submit"><i class="fa-regular fa-floppy-disk"></i> &nbsp; Actualizar director</button>
                        </div>
                        </form>
                    </div>
                    </div>
                    <?php
                    }
                    
                    ?>
                <?php

                }
 
 
             echo '</tbody>
                 </table>
             </div>';
            }else{
                echo '<div class="alert alert-danger" role="alert">
                <div class="text-center"> El anteproyecto no tiene director asignado.</div>
              </div>';
            }

            

        }else{
            echo '<div class="alert alert-warning" role="alert">
            <div class="text-center"> El anteproyecto no tiene director asignado.</div>
          </div>';
        }
        
        
    }else{
        echo '<div class="alert alert-warning" role="alert">
        <div class="text-center"> El anteproyecto no tiene director asignado.</div>
      </div>';
    }
    ?>
 </div>
 </div>

 

 <script>
  function BuscarUsuario(str, ruta) {
    if (str.length == 0) {
      // Limpiar los campos si no hay entrada
      document.getElementById("nombreusuario").value = "";
      document.getElementById("apellidousuario").value = "";
      document.getElementById("telefono").value = "";
      document.getElementById("correo").value = "";
      document.getElementById("rol").value = "";
      document.getElementById('floatingSelect').value = "";
    
      return;
    }

    // Crear la instancia de XMLHttpRequest
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        try {
          // Parsear la respuesta JSON
          var data = JSON.parse(this.responseText);
          // Verificar si hay un error en la respuesta
          if (data.error) {
            document.getElementById("nombreusuario").value = data.error;
            document.getElementById("apellidousuario").value = data.error;
            document.getElementById("telefono").value = data.error;
            document.getElementById("correo").value = data.error;
            document.getElementById("rol").value = data.error;
          
          } else {
            // Asignar los valores a los campos correspondientes
            document.getElementById("nombreusuario").value = data.nombre;
            document.getElementById("apellidousuario").value = data.apellidos;
            document.getElementById("telefono").value = data.telefono;
            document.getElementById("correo").value = data.correo;
            document.getElementById("rol").value = data.rol;

          }
        } catch (e) {
         
          document.getElementById("nombreusuario").value = data.error;
          document.getElementById("apellidousuario").value = data.error;
          document.getElementById("telefono").value = data.error;
          document.getElementById("correo").value = data.error;
          document.getElementById("rol").value = data.error;
       
        }
      }
    };

    // Hacer la solicitud GET al servidor
    var url = ruta + "Views/content/livesearch.php?documentousers=" + str;
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
  }
</script>
<?php





