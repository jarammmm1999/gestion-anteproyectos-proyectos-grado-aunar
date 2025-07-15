<?php

if ($_SESSION['privilegio'] != 1 && $_SESSION['privilegio'] != 2) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
  echo $ins_loginControlador->cerrar_sesion_controlador();
  exit();
}
$privilegio = $_SESSION['privilegio'];
$numero_documento_user = $_SESSION['numero_documento'];
?>

<div class="button-container text-center">
        <button type="button" class="btna btn-success btn-1" onclick="mostrarContenedor('contenedor1')">
            Asignar facultades y programas usuarios manualmente &nbsp; <i class="fa-solid fa-users"></i>
        </button>
        <button type="button" class="btna btn-2" onclick="mostrarContenedor('contenedor2')">
            Asignar desde archivo &nbsp; <i class="fa-solid fa-file-excel"></i>
        </button>
    </div>

<div id="contenedor1" class="contenedor activo">
  <form class="user-form mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/UsuarioAjax.php" method="POST" data-form="save" autocomplete="off">
  
      <div class="container-header-form">
      <h2><i class="fas fa-user"></i> Información básica usuarios</h2>
      <i class="fa-solid fa-magnifying-glass" data-bs-toggle="modal" data-bs-target="#staticBackdrop"></i>
      </div>

        <div class="form-grid">


          <div class="form-floating">
            <input type="number" class="form-control " id="floatingDocumento" name="documento_usuario_regASG" placeholder="Numero de documento" onchange="BuscarUsuario(this.value, '<?= SERVERURL ?>')">
            <label for="floatingDocumento">Numero de documento</label>
          </div>

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


        <div class="form-grid two mt-4">
          <div class="form-floating">
            <select class="form-select input_border" id="floatingSelect" name="tipo_faculta_reg" aria-label="Floating label select example">
              <option selected></option>
            </select>
            <label for="floatingSelect">Facultades</label>
          </div>

          <div class="form-floating">
            <select class="form-select input_border" id="floatingSelectprograma" name="tipo_programa_reg" aria-label="Floating label select example">
              <option selected></option>
            </select>
            <label for="floatingSelect">Programa</label>
          </div>

        </div>

        <div class="form-actions mt-5 mb-5">
          <button type="submit"><i class="fa-regular fa-floppy-disk"></i> &nbsp; Asignar usuario facultad programa</button>
        </div>
  </form>
</div>

<div id="contenedor2" class="contenedor">

  
      <!-- Tabla Bootstrap -->
      <form id="fileUploadForm" class="user-form mb-2 FormulariosAjax" action="<?= SERVERURL ?>Ajax/UsuarioAjax.php" method="POST" data-form="save" autocomplete="off">
        
        
        <!-- Mensaje -->

        <div class="container-fluid">
    <div class="card shadow-lg contenedor-body-mensaje text-dark">
        <div class="card-header bg-primary text-white header-card-mensaje">
            <h3>Requisitos para la asignar facultades a usuarios desde excel</h3>
        </div>
        <div class="card-body ">
            <div class="alert text-left  p-4">
                <p>
                    <strong style="color: #d9534f;">IMPORTANTE:</strong> 
                    Para garantizar un correcto procesamiento de los datos, el archivo que cargue debe ser exclusivamente un 
                    <strong style="color: #034873;">archivo Excel (.xlsx o .xls)</strong> con el siguiente formato:
                </p>
                <p>
                    El archivo debe contener las siguientes <strong style="color: #034873">6 columnas obligatorias</strong>: 
                    <strong style="color: #034873">Número de documento, Nombre de usuario, Apellidos de usuario, Correo de usuario, Teléfono de usuario y Tipo de usuario</strong> 
                    Cualquier archivo que no cumpla con este formato será rechazado.
                </p>

                <h5 class=" mt-3 mb-4" style="color: #034873"><i class="fa-solid fa-users"></i> Usuarios permitidos y sus roles:</h5>
                <ul class="list-group text-left  mb-4">
                    <li class="list-group-item"><span class="fw-bold">1 →</span> <strong style="color: #d9534f;">Administrador</strong></li>
                    <li class="list-group-item"><span class="fw-bold">2 →</span> Coordinador</li>
                    <li class="list-group-item"><span class="fw-bold">3 →</span> Estudiante Anteproyecto</li>
                    <li class="list-group-item"><span class="fw-bold">4 →</span> Estudiante Proyecto</li>
                    <li class="list-group-item"><span class="fw-bold">5 →</span> Asesor</li>
                    <li class="list-group-item"><span class="fw-bold">6 →</span> Asesor Externo</li>
                </ul>

                <p >
                    Además, el sistema generará automáticamente  una casilla para poder seleccionar una <strong style="color: #d9534f;">facultad y un programa</strong> para cada usuario registrado en el archivo cargado.
                </p>
                <p >
                   los usuarios con rol de administrador no se le asignará una <strong style="color: #d9534f;">facultad y un programa</strong>.
                </p>
            </div>
            
            <div class="table-container">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Número de documento</th>
                            <th>Nombre de usuario</th>
                            <th>Apellidos de usuario</th>
                            <th>Correo de usuario</th>
                            <th>Teléfono de usuario</th>
                            <th>Tipo de usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2001</td>
                            <td>Andrea</td>
                            <td>Muñoz</td>
                            <td>andrea@gmail.com</td>
                            <td>3101234567</td>
                            <td>1</td>
                        </tr>
                        <tr>
                            <td>2002</td>
                            <td>Fernando</td>
                            <td>García</td>
                            <td>fernando@yahoo.com</td>
                            <td>3156789012</td>
                            <td>3</td>
                        </tr>
                        <tr>
                            <td>2003</td>
                            <td>Laura</td>
                            <td>Pérez</td>
                            <td>laura@hotmail.com</td>
                            <td>3205678901</td>
                            <td>4</td>
                        </tr>
                        <tr>
                            <td>2004</td>
                            <td>David</td>
                            <td>Rodríguez</td>
                            <td>david@outlook.com</td>
                            <td>3056781234</td>
                            <td>3</td>
                        </tr>
                        <tr>
                            <td>2005</td>
                            <td>Camila</td>
                            <td>Fernández</td>
                            <td>camila@gmail.com</td>
                            <td>3193456789</td>
                            <td>3</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-3"><strong>Nota:</strong> Si algún usuario ya está registrado en la base de datos con el mismo correo o número de documento, no será agregado nuevamente.</p>
        </div>
    </div>
</div>


      <div class="upload-container">
        <div class="drop-area" id="drop-area2">
            <p class="text-center">Arrastra y suelta tu archivo aquí o haz clic para seleccionar</p>
            <input type="file" id="fileInput2" class="hidden-input" accept=".xls,.xlsx">
            <div class="file-info" id="file-info" style="display: none;">
                <img src="https://cdn-icons-png.flaticon.com/512/732/732220.png" alt="Excel Icon">
                <span class="text-center" id="file-name"></span>
            </div>
        </div>
    </div>


      
      <div class="container table-container mt-4">
            <div class="alert alert-primary text-center" role="alert">
                Datos del archivo cargado
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered " id="tabla_usuarios">
                    <thead class="thead-dark">
                        <tr>
                            <th>Numero de documento</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Tipo de usuario</th>
                            <th>Facultad</th>
                            <th>Programa</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="table-body2" class="tabla_facultad_select">
                        <!-- Filas generadas dinámicamente -->
                    </tbody>
                </table>
                <input type="hidden" name="DatosArchivosFacultad" id="hiddenData2">
                <div class="form-actions mt-5 mb-5">
                    <button type="submit"><i class="fa-regular fa-floppy-disk"></i> &nbsp;Asignar usuarios a las facultades</button>
                </div>
            </div>
        </div>
    </form>


</div>




<?php include "modal-usuarios-registrados.php";  ?>


<script>
  function BuscarUsuario(str, ruta) {
      limpiarProgramas();
      limpiarFaculta()
    if (str.length == 0) {
      // Limpiar los campos si no hay entrada
      document.getElementById("nombreusuario").value = "";
      document.getElementById("apellidousuario").value = "";
      document.getElementById("telefono").value = "";
      document.getElementById("correo").value = "";
      document.getElementById("rol").value = "";
      document.getElementById('floatingSelect').value = "";
      limpiarProgramas();
      limpiarFaculta()
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
            limpiarFaculta();
            limpiarProgramas();
          } else {
            // Asignar los valores a los campos correspondientes
            document.getElementById("nombreusuario").value = data.nombre;
            document.getElementById("apellidousuario").value = data.apellidos;
            document.getElementById("telefono").value = data.telefono;
            document.getElementById("correo").value = data.correo;
            document.getElementById("rol").value = data.rol;

            if (data.id_rol) {
             limpiarFaculta();
            limpiarProgramas();
              cargarFacultades(ruta, data.id_rol,'<?=$privilegio?>','<?=$numero_documento_user?>');
            }

          }
        } catch (e) {
          limpiarFaculta();
          document.getElementById("nombreusuario").value = data.error;
          document.getElementById("apellidousuario").value = data.error;
          document.getElementById("telefono").value = data.error;
          document.getElementById("correo").value = data.error;
          document.getElementById("rol").value = data.error;
          limpiarFaculta();
          limpiarProgramas();
        }
      }
    };

    // Hacer la solicitud GET al servidor
    var url = ruta + "Views/content/livesearch.php?documentousers=" + str;
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
  }

  function cargarFacultades(ruta, dato, privilegio, documento) {
        var select = document.getElementById('floatingSelect');

        // Crear la solicitud XMLHttpRequest
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Parsear la respuesta JSON
                try {
                    var faculta = JSON.parse(xhr.responseText);

                    // Validar que el array faculta sea válido
                    if (Array.isArray(faculta)) {
                        var defaultOption = document.createElement('option');
                        defaultOption.value = ''; // Un valor vacío para que no se pueda seleccionar
                        defaultOption.text = 'Selecciona'; // El texto que se verá
                        defaultOption.selected = true; // Seleccionar esta opción por defecto
                        select.appendChild(defaultOption);
                        faculta.forEach(function(faculta) {
                            var option = document.createElement('option');
                            option.value = faculta.id_faculta;
                            option.text = faculta.nombre_faculta;
                            select.appendChild(option);
                        });

                        // Capturar el valor seleccionado cada vez que cambie la selección
                        select.addEventListener('change', function() {
                            var selectedValue = select.value; // Capturar el valor seleccionado (id_faculta)
                            var selectedText = select.options[select.selectedIndex].text; // Capturar el texto seleccionado (nombre_faculta)
                            limpiarProgramas();
                            cargarProgramas(ruta, selectedValue);
                        });
                    } else {
                        console.error("Error: La respuesta del servidor no es un array válido.");
                    }
                } catch (e) {
                    console.error("Error al parsear la respuesta del servidor en cargarFacultades:", e);
                }
            }
        };

        var url = ruta + "Views/content/livesearch.php?coordinador=" + dato + "&privilegio=" + privilegio + "&documentoUSER=" + documento;
        xhr.open('GET', url, true);
        xhr.send();
    }

  function cargarProgramas(ruta, dato) {
    var select = document.getElementById('floatingSelectprograma');

    // Crear la solicitud XMLHttpRequest
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        // Parsear la respuesta JSON
        try {
          var faculta = JSON.parse(xhr.responseText);

          // Validar que el array faculta sea válido
          if (Array.isArray(faculta)) {
            faculta.forEach(function(faculta) {
              var option = document.createElement('option');
              option.value = faculta.id_faculta;
              option.text = faculta.nombre_programa;
              select.appendChild(option);
              
            });
          } else {
            console.error("Error: La respuesta del servidor no es un array válido.");
          }
        } catch (e) {
          console.error("Error al parsear la respuesta del servidor en cargarRolesUsuario:", e);
         
        }
      }
    };

    var url = ruta + "Views/content/livesearch.php?programa=" + dato;
    xhr.open('GET', url, true);
    xhr.send();
  }

  // Función para limpiar la lista de programas
function limpiarProgramas() {
    var listaProgramas = document.getElementById('floatingSelectprograma'); // Asegúrate de tener este elemento en tu HTML
    while (listaProgramas.firstChild) {
        listaProgramas.removeChild(listaProgramas.firstChild); // Eliminar todos los elementos hijos uno por uno
    }
}

function limpiarFaculta() {
    var listaProgramas = document.getElementById('floatingSelect'); // Asegúrate de tener este elemento en tu HTML
    while (listaProgramas.firstChild) {
        listaProgramas.removeChild(listaProgramas.firstChild); // Eliminar todos los elementos hijos uno por uno
    }
}


</script>