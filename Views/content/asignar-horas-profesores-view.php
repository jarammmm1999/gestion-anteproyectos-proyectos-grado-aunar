<?php
if ($_SESSION['privilegio'] != 1 && $_SESSION['privilegio'] != 2) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
  echo $ins_loginControlador->cerrar_sesion_controlador();
  exit();
}
?>

<form class="user-form mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/AnteproyectoAjax.php" method="POST" data-form="save" autocomplete="off">

  <div class="container-header-form">
  <h2><i class="fas fa-book"></i> Información básica profesores</h2>
  <i class="fa-solid fa-magnifying-glass" data-bs-toggle="modal" data-bs-target="#staticBackdrop" data-bs-toggle="tooltip" data-bs-placement="top" title="Buscar usuarios registrados"></i>
</div>

  <div class="form-grid two mt-5">


    <div class="form-floating">
      <input type="number" class="form-control input_border" id="numero_horas_asesorias_reg"
        name="numero_horas_asesorias_reg" placeholder="Codigo anteproyecto">
      <label for="codigoAnteproyecto">Horas asesorias</label>
    </div>
    <div class="form-floating">
      <input type="text" class="form-control input_border" id="numero_documento_regP"
        name="numero_documento_regP" placeholder="Numero de documento" onchange="BuscarUsuario(this.value, '<?= SERVERURL ?>')">
      <label for="numeroDocumento">Numero de documento profesor</label>
    </div>

    <div class="form-floating">
      <input type="text" class="form-control input_border" id="nombreasesor" placeholder="Titulo del proyecto" disabled>
      <label for="tituloProyecto">Nombre y apellidos</label>
    </div>

    <div class="form-floating">
      <input type="text" class="form-control input_border" id="telefono" placeholder="Nombre estudiante" disabled>
      <label for="nombreEstudiante">Telefono</label>
    </div>

    <div class="form-floating">
      <input type="text" class="form-control input_border" id="correoasesor" placeholder="Titulo del proyecto" disabled>
      <label for="tituloProyecto">Correo</label>
    </div>

    <div class="form-floating">
      <input type="text" class="form-control input_border" id="rol_usuario" placeholder="Nombre estudiante" disabled>
      <label for="nombreEstudiante">Tipo de usuario</label>
    </div>

    <div class="form-floating">
      <input type="text" class="form-control input_border" id="faculta_usuario" placeholder="Nombre estudiante" disabled>
      <label for="nombreEstudiante">Facultad profesor</label>
    </div>

    <div class="form-floating">
      <input type="text" class="form-control input_border" id="programa_usuario" placeholder="Nombre estudiante" disabled>
      <label for="nombreEstudiante">Programa profesor</label>
    </div>
   
  </div>


  <input type="hidden" name="numero_documento_user_logueado" value="<?= $ins_loginControlador->encryption($_SESSION['numero_documento']) ?>">

  <div class="form-actions mt-5 mb-5">
    <button type="submit"><i class="fa-regular fa-floppy-disk"></i> &nbsp; Registrar</button>
  </div>
</form>

<?php include "modal-usuarios-registrados.php";  ?>

<script>
 function BuscarUsuario(str, ruta) {
    if (str.length == 0) {
      // Limpiar los campos si no hay entrada
      document.getElementById("nombreasesor").value = "";
      document.getElementById("telefono").value = "";
      document.getElementById("correoasesor").value = "";
      document.getElementById("rol_usuario").value = "";
      document.getElementById("faculta_usuario").value = "";
      document.getElementById("programa_usuario").value = "";
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
            document.getElementById("nombreasesor").value = data.error;
            document.getElementById("telefono").value = data.error;
            document.getElementById("correoasesor").value = data.error;
            document.getElementById("rol_usuario").value = data.error;
            document.getElementById("faculta_usuario").value = data.error;
            document.getElementById("programa_usuario").value = data.error;
          } else {
            // Asignar los valores a los campos correspondientes
            document.getElementById("nombreasesor").value = data.nombre + " " + data.apellidos;
            document.getElementById("telefono").value = data.telefono;
            document.getElementById("correoasesor").value = data.correo;
            document.getElementById("rol_usuario").value = data.rol;
            document.getElementById("faculta_usuario").value = data.facultad;
            document.getElementById("programa_usuario").value = data.programa;
          }
        } catch (e) {
          document.getElementById("nombreasesor").value = data.error;
          document.getElementById("telefono").value = data.error;
          document.getElementById("correoasesor").value = data.error;
          document.getElementById("rol_usuario").value = data.error;
          document.getElementById("faculta_usuario").value = data.error;
          document.getElementById("programa_usuario").value = data.error;
        }
      }
    };

    // Hacer la solicitud GET al servidor
    var url = ruta + "Views/content/livesearch.php?documentoasesor=" + str;
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
  }
</script>