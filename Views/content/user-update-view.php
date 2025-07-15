<?php

if ($_SESSION['privilegio'] != 1 && $_SESSION['privilegio'] != 2) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}
// Obtener el número de documento y ejecutar la consulta
$numero_documento_user = $url_pagina[1];
$consulta = $ins_loginControlador->consulta_information_user($numero_documento_user);
// Verificar si hay resultados
if ($consulta->rowCount() == 1) {
    // Obtener los datos del usuario
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    $numero_documento_user_update = $usuario['numero_documento'];

    if ($usuario['id_rol'] == 1) {
        $tipo_usuario = 'Administrador';
    } else if ($usuario['id_rol'] == 2) {
        $tipo_usuario = 'Coordinador';
    } else if ($usuario['id_rol'] == 3) {
        $tipo_usuario = 'Estudiante Anteproyecto';
    } else if ($usuario['id_rol'] == 4) {
        $tipo_usuario = 'Estudiante Proyecto';
    } else {
        $tipo_usuario = 'Asesor - Profesor';
    }


    if ($usuario['estado'] == 1) {
        $estado = 'Activo';
    } else {
        $estado = 'Bloqueado';
    }


?>



    <form class="user-form mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/UsuarioAjax.php" method="POST" data-form="update" autocomplete="off">
        <h2><i class="fa-solid fa-user-pen"></i> Información básica usuarios</h2>

        <div class="form-grid">
            <input type="hidden" class="form-control input_border" id="floatingDocumento" name="id_usuario_upd"
                placeholder="id usuario" value="<?= $ins_loginControlador->encryption($usuario['id']) ?>" readonly>

            <div class="form-floating">
                <input type="number" class="form-control input_border" id="floatingDocumento" name="documento_usuario_upd"
                    placeholder="Numero de documento" value="<?= $usuario['numero_documento'] ?>">
                <label for="floatingDocumento">Numero de documento</label>
            </div>

            <div class="form-floating">
                <input type="text" class="form-control input_border" id="floatingNombre" name="nombre_usuario_upd"
                    placeholder="Password" value="<?= $usuario['nombre_usuario'] ?>">
                <label for="floatingNombre mb-4">Nombre de usuario</label>
            </div>

            <div class="form-floating">
                <input type="text" class="form-control input_border" id="floatingNombre"
                    name="apellido_usuario_upd"
                    placeholder="Password" value="<?= $usuario['apellidos_usuario'] ?>">
                <label for="floatingNombre mb-4">Apellidos de usuario</label>
            </div>

            <div class="form-floating">
                <input type="email" class="form-control input_border" id="floatingNombre"
                    name="correo_usuario_upd"
                    placeholder="Password" value="<?= $usuario['correo_usuario'] ?>">
                <label for="floatingNombre mb-4">Correo de usuario</label>
            </div>

            <div class="form-floating">
                <input type="text" class="form-control input_border" id="floatingNombre" name="telefono_usuario_upd"
                    placeholder="Password" value="<?= $usuario['telefono_usuario'] ?>">
                <label for="floatingNombre mb-4">Telefono de usuario</label>
            </div>

            <div class="form-floating">
                <input type="text" class="form-control input_border" id="floatingNombre"
                    placeholder="Password" value="<?= $tipo_usuario ?>" disabled>
                <label for="floatingNombre mb-4">Tipo de usuario</label>
            </div>

            <div class="form-floating">
                <select class="form-select input_border" id="floatingSelect" name="tipo_usuario_upd" aria-label="Floating label select example">
                    <option selected></option>
                    <?php
                    $sql = "SELECT * from roles_usuarios";
                    $consulta_roles = $ins_loginControlador->ejecutar_consultas_simples_two($sql);
                    while ($roles = $consulta_roles->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . $ins_loginControlador->encryption($roles['id_rol']) . '">' . $roles['nombre_rol'] . '</option>';
                    }
                    ?>
                </select>
                <label for="floatingSelect">Cambiar tipo de usuario</label>
            </div>

            <div class="form-floating">
                <input type="text" class="form-control input_border" id="floatingNombre"
                    placeholder="Password" value="<?= $estado ?>" disabled>
                <label for="floatingNombre mb-4">Estado usuario</label>
            </div>

            <div class="form-floating">
                <select class="form-select input_border" id="floatingSelect" name="estado_usuario_upd" aria-label="Floating label select example">
                    <option selected></option>
                    <option value="<?= $ins_loginControlador->encryption('1') ?>">Activo</option>
                    <option value="<?= $ins_loginControlador->encryption('2') ?>">Bloqueado</option>

                </select>
                <label for="floatingSelect">Cambiar estado del usuario</label>
            </div>


        </div>

        <div class="form-grid two mt-3">

            <div class="form-floating">
                <input type="password" class="form-control input_border" id="floatingNombre" name="password_usuario_upd"
                    placeholder="Password">
                <label for="floatingNombre mb-4">Contraseña de usuario</label>
            </div>

            <div class="form-floating">
                <input type="password" class="form-control input_border" id="floatingNombre" name="confirm_password_usuario_upd"
                    placeholder="Password">
                <label for="floatingNombre mb-4">Confirmar contraseña</label>
            </div>

        </div>


        <div class="form-actions mt-5 mb-5">
            <button type="submit"><i class="fa-solid fa-user-pen"></i> &nbsp; Actualizar usuario</button>
        </div>
    </form>


    <?php
    $consulta = "SELECT auf.numero_documento, f.nombre_facultad, p.nombre_programa, f.id_facultad, p.id_programa
FROM Asignar_usuario_facultades auf
INNER JOIN facultades f ON auf.id_facultad = f.id_facultad
LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
WHERE auf.numero_documento = '$numero_documento_user_update'";

    $consulta_facultad_programa = $ins_loginControlador->ejecutar_consultas_simples_two($consulta);

    if ($consulta_facultad_programa->rowCount() > 0) {
    ?>
        <div class="container-fluid">
            <div class="container-table-user">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Facultad</th>
                                <th>Programa</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Contador para el ID de cada fila
                            $contador = 1;

                            // Recorrer cada fila de la consulta y mostrarla en la tabla
                            while ($fila = $consulta_facultad_programa->fetch(PDO::FETCH_ASSOC)) {
                                echo '<tr>';
                                echo '<td>' . $contador++ . '</td>'; // Incrementar el contador para cada fila
                                echo '<td>' . $fila['nombre_facultad'] . '</td>';
                                echo '<td>' . ($fila['nombre_programa'] ? $fila['nombre_programa'] : 'Sin Programa Asignado') . '</td>';
                                // Botón de Eliminar con formulario y campo oculto con el ID de programa
                                echo '<td><form class="FormulariosAjax" action="' . SERVERURL . 'Ajax/UsuarioAjax.php" method="POST" data-form="delete" autocomplete="off">
                                <input type="hidden" name="idFacultad_del" value="' . $ins_loginControlador->encryption($fila['id_facultad']) . '">
                                <input type="hidden" name="idPrograma_del" value="' . $ins_loginControlador->encryption($fila['id_programa']) . '">
                                <input type="hidden" name="documentoFPuser_del" value="' . $ins_loginControlador->encryption($numero_documento_user_update) . '">
                                <button type="submit" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
                                </form></td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
    } else {
    ?>
        <div class="container-fluid see-mensagge">
            <div class="alert alert-warning" role="alert">
                <div class="text-center">
                    No se encontraron facultades y programas asociados al usuario
                </div>
            </div>
        </div>

<?php

    }
} else {
    ?>
        <div class="container-fluid see-mensagge">
            <div class="alert alert-warning" role="alert">
                <div class="text-center">
                No se encontró información para este usuario.
                </div>
            </div>
        </div>

<?php
    
 
}
