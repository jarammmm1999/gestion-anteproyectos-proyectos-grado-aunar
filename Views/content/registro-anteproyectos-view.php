<?php
if ($_SESSION['privilegio'] != 1 && $_SESSION['privilegio'] != 2) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}

$privilegio = $_SESSION['privilegio'];
$numero_documento_user = $_SESSION['numero_documento'];
?>

<form class="user-form mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/AnteproyectoAjax.php" method="POST" data-form="save" autocomplete="off">
    <h2><i class="fa-solid fa-book"></i> Información básica anteproyectos </h2>

    <div class="form-grid  mt-5">

        <div class="input-group">
            <div class="form-floating">
                <input type="number" class="form-control input_border" id="floatingDocumento" name="codigo_anteproyecto_reg" placeholder="Numero de documento" readonly>
                <label for="floatingDocumento">Codigo Anteproyecto</label>
            </div>
            <button class="btn " type="button" id="buscarAnteproyecto">Generar</button>
        </div>

        <div class="form-floating">
            <input type="text" class="form-control input_border" id="floatingNombre" name="titulo_anteproyecto_reg" placeholder="Password">
            <label for="floatingNombre mb-4">Título de anteproyecto</label>
        </div>

        <div class="form-floating">
            <input type="text" class="form-control input_border" id="floatingNombre" name="palabras_claves_anteproyecto_reg" placeholder="Password">
            <label for="floatingNombre mb-4">Palabras Claves</label>
        </div>
        

    </div>

    <div class="form-grid three mt-4">

    <div class="form-floating">
            <select class="form-select input_border" id="floatingSelect2" name="tipo_modalidad_reg" aria-label="Floating label select example">
                <option selected></option>
                <?php
                $sql_modalidad = "SELECT * FROM modalidad_grados";
                $consulta_modalidad = $ins_loginControlador->ejecutar_consultas_simples_two($sql_modalidad);

                if ($consulta_modalidad->rowCount() > 0) { // Verifica si hay resultados antes de recorrerlos
                    while ($modalidad = $consulta_modalidad->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . $ins_loginControlador->encryption($modalidad['id_modalidad']) . '">' . $modalidad['nombre_modalidad'] . '</option>';
                    }
                } 
                ?>
            </select>
            <label for="floatingSelect">Modalidad del proyecto</label>
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
        <button type="submit"><i class="fa-regular fa-floppy-disk"></i> &nbsp; Registrar anteproyecto</button>
    </div>
</form>


<script>

    document.addEventListener("DOMContentLoaded", function() {
        cargarFacultades('<?=SERVERURL?>', 'dato', '<?=$privilegio?>','<?=$numero_documento_user?> ');
    });

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
