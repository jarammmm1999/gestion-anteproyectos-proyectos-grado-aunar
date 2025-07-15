<?php

if ($_SESSION['privilegio'] != 1 && $_SESSION['privilegio'] != 2) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}
?>

<div class="button-container text-center">
        <button type="button" class="btna btn-success btn-1" onclick="mostrarContenedor('contenedor1')">
            cargar usuarios manualmente &nbsp; <i class="fa-solid fa-users"></i>
        </button>
        <button type="button" class="btna btn-2" onclick="mostrarContenedor('contenedor2')">
            cargar desde archivo &nbsp; <i class="fa-solid fa-file-excel"></i>
        </button>
    </div>


<div id="contenedor1" class="contenedor activo">

    <form class="user-form mt-2 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/UsuarioAjax.php" method="POST" data-form="save" autocomplete="off">
        <h2><i class="fa-solid fa-user-plus"></i> Información básica usuarios  </h2>

        <div class="form-grid">

            <div class="form-floating">
                <input type="number" class="form-control input_border" id="floatingDocumento" name="documento_usuario_reg" placeholder="Numero de documento">
                <label for="floatingDocumento">Numero de documento</label>
            </div>

            <div class="form-floating">
                <input type="text" class="form-control input_border" id="floatingNombre" name="nombre_usuario_reg" placeholder="Password">
                <label for="floatingNombre mb-4">Nombre de usuario</label>
            </div>

            <div class="form-floating">
                <input type="text" class="form-control input_border" id="floatingApellido" name="apellido_usuario_reg"
                    placeholder="Password">
                <label for="floatingApellido mb-4">Apellidos de usuario</label>
            </div>

            <div class="form-floating">
                <input type="email" class="form-control input_border" id="floatingCorreo" name="correo_usuarrio_reg"
                    placeholder="Password">
                <label for="floatingCorreo mb-4">Correo de usuario</label>
            </div>

            <div class="form-floating">
                <input type="text" class="form-control input_border" id="floatingpassword" name="tefelefono_usuario_reg"
                    placeholder="Password">
                <label for="floatingpassword mb-4">Telefono de usuario</label>
            </div>

            <div class="form-floating">
                <select class="form-select input_border" id="floatingSelect" name="tipo_usuario_reg" aria-label="Floating label select example">
                    <option selected></option>
                    <?php
                    $sql = "SELECT * from roles_usuarios";
                    $consulta_roles = $ins_loginControlador->ejecutar_consultas_simples_two($sql);
                    while ($roles = $consulta_roles->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . $ins_loginControlador->encryption($roles['id_rol']) . '">' . $roles['nombre_rol'] . '</option>';
                    }
                    ?>

                </select>
                <label for="floatingSelect">Tipo de usuario</label>
            </div>
        
        </div>

        <div class="form-grid two mt-3">

            <div class="form-floating">
                <input type="password" id="password" class="form-control input_border" id="floatingNombre" name="password_usuario_reg"
                    placeholder="Password">
                <label for="floatingNombre mb-4">Contraseña de usuario</label>
            </div>

            <div class="form-floating">
                <input type="password" id="password2" class="form-control input_border" id="floatingNombre" name="confirm-password_usuario_reg"
                    placeholder="Password">
                <label for="floatingNombre mb-4">Confirmar contraseña</label>
            </div>

        </div>

        <div class="text-center mt-5">
            <span>Contraseña generada: <b id="MostrarContrasena" class="badge bg-success"></b> </span>
        </div>


        <div class="form-actions mt-5 mb-5">
            <button type="button" onclick="generarContrasena()"><i class="fa-solid fa-key"></i> &nbsp;Generar Contraseña</button>
            <button type="submit"><i class="fa-regular fa-floppy-disk"></i> &nbsp; Registrar usuario</button>
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
            <h3>Requisitos para la carga de usuarios en excel</h3>
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
                    <li class="list-group-item"><span class="fw-bold">1 →</span> Administrador</li>
                    <li class="list-group-item"><span class="fw-bold">2 →</span> Coordinador</li>
                    <li class="list-group-item"><span class="fw-bold">3 →</span> Estudiante Anteproyecto</li>
                    <li class="list-group-item"><span class="fw-bold">4 →</span> Estudiante Proyecto</li>
                    <li class="list-group-item"><span class="fw-bold">5 →</span> Asesor</li>
                    <li class="list-group-item"><span class="fw-bold">6 →</span> Asesor Externo</li>
                </ul>

                <p >
                    Además, el sistema generará automáticamente una <strong style="color: #d9534f;">contraseña aleatoria</strong> para cada usuario registrado en el archivo cargado.
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
        <div class="drop-area" id="drop-area">
            <p class="text-center">Arrastra y suelta tu archivo aquí o haz clic para seleccionar</p>
            <input type="file" id="fileInput" class="hidden-input" accept=".xls,.xlsx">
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
                            <th>Contraseña</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <!-- Filas generadas dinámicamente -->
                    </tbody>
                </table>
                <input type="hidden" name="jsonData" id="hiddenData">
                <div class="form-actions mt-5 mb-5">
                    <button type="submit"><i class="fa-regular fa-floppy-disk"></i> &nbsp; Registrar usuario</button>
                </div>
            </div>
        </div>
    </form>


</div>
