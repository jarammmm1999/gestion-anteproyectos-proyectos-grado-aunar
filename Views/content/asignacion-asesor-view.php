<?php
if ($_SESSION['privilegio'] != 1 && $_SESSION['privilegio'] != 2) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
  echo $ins_loginControlador->cerrar_sesion_controlador();
  exit();
}
?>

<form class="user-form mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
  

  <div class="container-header-form">
  <h2><i class="fas fa-book"></i> Información básica usuarios, proyectos o anteproyectos</h2>
  <i class="fas fa-book" data-bs-toggle="modal" data-bs-target="#staticBackdropAnteproyecto" data-bs-toggle="tooltip" data-bs-placement="top" title="Buscar anteproyectos registrados"></i>
  <i class="fas fa-book" data-bs-toggle="modal" data-bs-target="#staticBackdroproyecto" data-bs-toggle="tooltip" data-bs-placement="top" title="Buscar proyectos registrados"></i>
  <i class="fa-solid fa-magnifying-glass" data-bs-toggle="modal" data-bs-target="#staticBackdrop" data-bs-toggle="tooltip" data-bs-placement="top" title="Buscar usuarios registrados"></i>
</div>
  <div class="form-grid two mt-5">
    <div class="form-floating">
      <input type="number" class="form-control input_border" id="codigo_proyecto_asignar"
        name="codigo_proyecto_asignar" placeholder="Codigo anteproyecto" onchange="BuscarProyecto(this.value, '<?= SERVERURL ?>')">
      <label for="codigoAnteproyecto">Código proyecto o anteproyecto</label>
    </div>
    <div class="form-floating">
      <input type="text" class="form-control input_border" id="documento_user_asignar"
        name="documento_user_asignar" placeholder="Numero de documento" onchange="BuscarUsuario(this.value, '<?= SERVERURL ?>')">
      <label for="numeroDocumento">Numero de documento profesor</label>
    </div>

    <div class="form-floating">
      <input type="text" class="form-control input_border" id="tituloProyecto" name="" placeholder="Titulo del proyecto" disabled>
      <label for="tituloProyecto">Título del proyecto</label>
    </div>

    <div class="form-floating">
      <input type="text" class="form-control input_border" id="palabrasClaves" name="" placeholder="Titulo del proyecto" disabled>
      <label for="tituloProyecto">Palabras Claves</label>
    </div>

    <div class="form-floating">
      <input type="text" class="form-control input_border" id="nombreFacultad" name="" placeholder="Titulo del proyecto" disabled>
      <label for="tituloProyecto">Facultad</label>
    </div>

    <div class="form-floating">
      <input type="text" class="form-control input_border" id="nombrePrograma" name="" placeholder="Titulo del proyecto" disabled>
      <label for="tituloProyecto">Programa</label>
    </div>
    
  </div>

  <div class="form-grid three mt-3">
    <div class="form-floating">
      <input type="text" class="form-control input_border" id="tipoProyecto" name="" placeholder="Titulo del proyecto" disabled>
      <label for="tituloProyecto">Tipo</label>
    </div>

  </div>

  <div class="form-grid two mt-3">
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

    <input type="hidden" name="numero_documento_user_logueado" value="<?= $ins_loginControlador->encryption($_SESSION['numero_documento']) ?>">
    <input type="hidden" name="tipoProyectoAnteproyecto" id="tipoProyectoAnteproyecto">
    <input type="hidden" name="idfacultaProyectoAnteproyecto" id="idfacultaProyectoAnteproyecto">
    <input type="hidden" name="idProgramaProyectoAnteproyecto" id="idProgramaProyectoAnteproyecto">

  </div>

  <div class="form-actions mt-5 mb-5">
    <button type="submit"><i class="fa-regular fa-floppy-disk"></i> &nbsp; Asignar asesor proyecto o anteproyecto</button>
  </div>
</form>

<?php include "modal-usuarios-registrados.php";  ?>
<?php include "modal-anteproyectos-registrados.php";  ?>
<?php include "modal-proyectos-registrados.php";  ?>

<script>
  function BuscarProyecto(str, ruta) {
    if (str.length == 0) {
      document.getElementById("tituloProyecto").value = "";
      document.getElementById("nombreFacultad").value = "";
      document.getElementById("nombrePrograma").value = "";
      document.getElementById("palabrasClaves").value = "";
      document.getElementById("tipoProyecto").value = "";
      document.getElementById("tipoProyectoAnteproyecto").value = "";
      document.getElementById("idfacultaProyectoAnteproyecto").value = "" ;
      document.getElementById("idProgramaProyectoAnteproyecto").value = "" ;
      return;
    }
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        try {
                // Parsear la respuesta JSON
                var response = JSON.parse(this.responseText);

                // Verificar si hay un error en la respuesta
                if (response.error) {
                  document.getElementById("tituloProyecto").value = response.error;
                  document.getElementById("nombreFacultad").value = response.error;
                  document.getElementById("nombrePrograma").value = response.error;
                  document.getElementById("palabrasClaves").value = response.error;
                  document.getElementById("tipoProyecto").value = response.error;
                  document.getElementById("tipoProyectoAnteproyecto").value = response.error;
                  document.getElementById("idfacultaProyectoAnteproyecto").value = response.error ;
                  document.getElementById("idProgramaProyectoAnteproyecto").value = response.error ;
                } else {
                    // Actualizar los campos del formulario con los datos recibidos
                    document.getElementById("tituloProyecto").value = response.Título || '';
                    document.getElementById("nombreFacultad").value = response.NombreFaculta || '';
                    document.getElementById("nombrePrograma").value = response.NombrePrograma || '';
                    document.getElementById("palabrasClaves").value = response.PalabrasClaves || '';
                    document.getElementById("tipoProyecto").value = response.tipo || '';
                    document.getElementById("tipoProyectoAnteproyecto").value = response.Codigotipo || '';
                    document.getElementById("idfacultaProyectoAnteproyecto").value = response.IdFacultad || '';
                    document.getElementById("idProgramaProyectoAnteproyecto").value = response.IdPrograma || '';
                }
            } catch (e) {
                console.error("Error al parsear la respuesta JSON:", e);
                document.getElementById("tituloProyecto").value = response.error;
                document.getElementById("nombreFacultad").value = response.error;
                document.getElementById("nombrePrograma").value = response.error;
                document.getElementById("palabrasClaves").value = response.error;
                document.getElementById("tipoProyecto").value = response.error;
                document.getElementById("tipoProyectoAnteproyecto").value = response.error;
                document.getElementById("idfacultaProyectoAnteproyecto").value = response.error ;
                document.getElementById("idProgramaProyectoAnteproyecto").value = response.error ;
            }
      }
    }

    // Obtener la ruta base de tu servidor (localhost en este caso)
    var url = ruta + "Views/content/livesearch.php?codigoProyecto=" + str;
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
  }

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