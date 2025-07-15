<?php

if ($_SESSION['privilegio'] != 1 && $_SESSION['privilegio'] != 2) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
  echo $ins_loginControlador->cerrar_sesion_controlador();
  exit();
}
?>

<div class="container-fluid">
  <div class="container-table-user">
    <div class="continer-search mt-5 mb-3">
      <input type="text"  id="buscar" onkeyup="buscarTabla()" placeholder="Buscar en la tabla...">
    </div>
    <?php
    require_once "./Controller/UsuarioControlador.php";
    $ins_usuarioControlador = new UsuarioControlador();
    echo $ins_usuarioControlador->paginar_usuarios_controlador($url_pagina[1], 10, $_SESSION['privilegio'], $_SESSION['id_usuario'], $url_pagina[0], "",$_SESSION['numero_documento']);
    ?>
  </div>
</div>

<script>

function mostrarFacultadesUsuario(ruta,numeroDocumento) {

    // Crear la solicitud XMLHttpRequest
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            try {
                // Parsear la respuesta JSON
                var data = JSON.parse(this.responseText);

                // Verificar si hay un error
                if (data.error) {
                    document.getElementById('facultades-content').innerHTML = '<p>' + data.error + '</p>';
                } else {
                    // Construir la tabla para mostrar las facultades y programas
                    var contenido = '<h5 class="text-center mt-3 mb-3">Facultades Asignadas</h5><table class="table table-striped table-bordered dt-responsive nowrap w-100" id="tabla_usuarios""><thead><tr><th>Facultad</th><th>Programa</th></tr></thead><tbody>';
                    data.forEach(function(facultad) {
                        contenido += '<tr><td>' + facultad.nombre_facultad + '</td><td>' + facultad.nombre_programa + '</td></tr>';
                    });
                    contenido += '</tbody></table>';
                    
                    // Insertar la tabla en el modal
                    document.getElementById('facultades-content').innerHTML = contenido;
                }
            } catch (e) {
                console.error("Error al procesar la respuesta del servidor", e);
            }
        }
    };

    // Definir la URL de la petición con el número de documento como parámetro
    var url = ruta + "Views/content/livesearch.php?documento_usersid=" + numeroDocumento;
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

function toggleProject() {
      var container = document.getElementById("project-container");
      if (container.style.display === "none" || container.style.display === "") {
        container.style.display = "block";
      } else {
        container.style.display = "none";
      }
   }

   



</script>