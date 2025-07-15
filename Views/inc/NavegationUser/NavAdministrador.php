<?php

if (isset($_GET['views'])) {
    $ruta = explode(
        "/",
        $_GET['views']
    );
}

if ($_SESSION['privilegio'] != 1) {
    $consultar_firma_directores = "SELECT firma 
FROM firma_digital_usuarios 
WHERE numero_documento = '$documento_user_logueado'";

    $resultado_firma_digital = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_firma_directores);

    if ($resultado_firma_digital->rowCount() == 0) {

        if (isset($ruta[0]) && $ruta[0] != "configuration-user") {

?>

            <!-- Modal -->
            <div class="modal-firma" id="modalFirma">
                <div class="modal-firma-contenido">
                    <h2>⚠️ Registro de Firma Digital <?=$documento_user_logueado?> </h2>
                    <p>
                        ⚠️ Antes de continuar, es obligatorio registrar su firma digital.
                        La firma digital es un requisito esencial para garantizar la autenticidad y seguridad de las modificaciones realizadas en la aplicación.
                        <br><br>
                        Sin su firma digital registrada, no podrá proceder con la edición, eliminación o aprobación de documentos dentro del sistema.
                        <br><br>
                        Haga clic en el botón de abajo para configurar su firma digital y evitar interrupciones en su flujo de trabajo.
                    </p>
                    <a href="<?= SERVERURL ?>configuration-user/<?= $ins_loginControlador->encryption($_SESSION['privilegio']) ?>/<?= $ins_loginControlador->encryption($_SESSION['numero_documento']) ?>"><button class="modal-firma-boton">Registrar firma digital</button></a>

                </div>
            </div>

    <?php
        }
    }
}


if ($_SESSION['privilegio'] != 1 && $_SESSION['privilegio'] != 2) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}

if (isset($ruta[0]) && $ruta[0] == "registrar-usuarios") {

    ?>
    <div class="navbar-container">
        <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
        <div class="link-container">
            <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
            <ul class="link-list">
                <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                <li><a class="active" href="<?= SERVERURL ?>registrar-usuarios/"><i class="fas fa-user-plus"></i> Registro de usuarios</a></li>
                <li><a href="<?= SERVERURL ?>user-list/"><i class="fas fa-users"></i> Consultar usuarios</a></li>
                <li><a href="<?= SERVERURL ?>asignar-usuarios-faculta/"><i class="fas fa-users"></i> Asignar usuarios a facultad</a></li>
                <li><a href="<?= SERVERURL ?>registro-anteproyectos/"><i class="fas fa-folder-open"></i> Registro de anteproyectos</a></li>
                <li><a href="<?= SERVERURL ?>registro-proyectos/"><i class="fas fa-folder-plus"></i> Registro de proyectos</a></li>
                <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-file-alt"></i> Consultar Proyectos</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-anteproyecto/"><i class="fas fa-user-graduate"></i> Asignar estudiante a anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-proyectos/"><i class="fas fa-user-graduate"></i> Asignar estudiante a proyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignacion-asesor/"><i class="fas fa-chalkboard-teacher"></i> Asignación de director</a></li>
                <li><a href="<?= SERVERURL ?>asignar-jurados/"><i class="fas fa-gavel"></i> Asignar jurados proyectos</a></li>
                <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>informe-aplicacion/"><i class="fas fa-chart-bar"></i> Informe general aplicación</a></li>
                <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-magnifying-glass"></i>  Consulta de proyectos calificados</a></li>
            </ul>
        </div>
    </div>


<?php

} else  if (isset($ruta[0]) && $ruta[0] == "user-list" || $ruta[0] == "user-update") {
?>

    <div class="navbar-container">
        <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
        <div class="link-container">
            <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
            <ul class="link-list">
                <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?= SERVERURL ?>registrar-usuarios/"><i class="fas fa-user-plus"></i> Registro de usuarios</a></li>
                <li><a class="active" href="<?= SERVERURL ?>user-list/"><i class="fas fa-users"></i> Consultar usuarios</a></li>
                <li><a href="<?= SERVERURL ?>asignar-usuarios-faculta/"><i class="fas fa-users"></i> Asignar usuarios a facultad</a></li>
                <li><a href="<?= SERVERURL ?>registro-anteproyectos/"><i class="fas fa-folder-open"></i> Registro de anteproyectos</a></li>
                <li><a href="<?= SERVERURL ?>registro-proyectos/"><i class="fas fa-folder-plus"></i> Registro de proyectos</a></li>
                <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-file-alt"></i> Consultar Proyectos</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-anteproyecto/"><i class="fas fa-user-graduate"></i> Asignar estudiante a anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-proyectos/"><i class="fas fa-user-graduate"></i> Asignar estudiante a proyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignacion-asesor/"><i class="fas fa-chalkboard-teacher"></i> Asignación de director</a></li>
                <li><a href="<?= SERVERURL ?>asignar-jurados/"><i class="fas fa-gavel"></i> Asignar jurados proyectos</a></li>
                <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>informe-aplicacion/"><i class="fas fa-chart-bar"></i> Informe general aplicación</a></li>
                <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-magnifying-glass"></i>  Consulta de proyectos calificados</a></li>
            </ul>
        </div>
    </div>

<?php
} else  if (isset($ruta[0]) && $ruta[0] == "registro-anteproyectos") {

?>

    <div class="navbar-container">
        <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
        <div class="link-container">
            <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
            <ul class="link-list">
                <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?= SERVERURL ?>registrar-usuarios/"><i class="fas fa-user-plus"></i> Registro de usuarios</a></li>
                <li><a href="<?= SERVERURL ?>user-list/"><i class="fas fa-users"></i> Consultar usuarios</a></li>
                <li><a href="<?= SERVERURL ?>asignar-usuarios-faculta/"><i class="fas fa-users"></i> Asignar usuarios a facultad</a></li>
                <li><a class="active" href="<?= SERVERURL ?>registro-anteproyectos/"><i class="fas fa-folder-open"></i> Registro de anteproyectos</a></li>
                <li><a href="<?= SERVERURL ?>registro-proyectos/"><i class="fas fa-folder-plus"></i> Registro de proyectos</a></li>
                <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-file-alt"></i> Consultar Proyectos</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-anteproyecto/"><i class="fas fa-user-graduate"></i> Asignar estudiante a anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-proyectos/"><i class="fas fa-user-graduate"></i> Asignar estudiante a proyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignacion-asesor/"><i class="fas fa-chalkboard-teacher"></i> Asignación de director</a></li>
                <li><a href="<?= SERVERURL ?>asignar-jurados/"><i class="fas fa-gavel"></i> Asignar jurados proyectos</a></li>
                <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>informe-aplicacion/"><i class="fas fa-chart-bar"></i> Informe general aplicación</a></li>
                <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-magnifying-glass"></i>  Consulta de proyectos calificados</a></li>
            </ul>
        </div>
    </div>

<?php


} else  if (isset($ruta[0]) && $ruta[0] == "asignar-estudiantes-anteproyecto") {

?>

    <div class="navbar-container">
        <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
        <div class="link-container">
            <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
            <ul class="link-list">
                <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?= SERVERURL ?>registrar-usuarios/"><i class="fas fa-user-plus"></i> Registro de usuarios</a></li>
                <li><a href="<?= SERVERURL ?>user-list/"><i class="fas fa-users"></i> Consultar usuarios</a></li>
                <li><a href="<?= SERVERURL ?>asignar-usuarios-faculta/"><i class="fas fa-users"></i> Asignar usuarios a facultad</a></li>
                <li><a href="<?= SERVERURL ?>registro-anteproyectos/"><i class="fas fa-folder-open"></i> Registro de anteproyectos</a></li>
                <li><a href="<?= SERVERURL ?>registro-proyectos/"><i class="fas fa-folder-plus"></i> Registro de proyectos</a></li>
                <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-file-alt"></i> Consultar Proyectos</a></li>
                <li><a class="active" href="<?= SERVERURL ?>asignar-estudiantes-anteproyecto/"><i class="fas fa-user-graduate"></i> Asignar estudiante a anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-proyectos/"><i class="fas fa-user-graduate"></i> Asignar estudiante a proyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignacion-asesor/"><i class="fas fa-chalkboard-teacher"></i> Asignación de director</a></li>
                <li><a href="<?= SERVERURL ?>asignar-jurados/"><i class="fas fa-gavel"></i> Asignar jurados proyectos</a></li>
                <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>informe-aplicacion/"><i class="fas fa-chart-bar"></i> Informe general aplicación</a></li>
                <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-magnifying-glass"></i>  Consulta de proyectos calificados</a></li>
            </ul>
        </div>
    </div>


<?php

} else  if (isset($ruta[0]) && $ruta[0] == "asignar-estudiantes-proyectos") {

?>
    <div class="navbar-container">
        <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
        <div class="link-container">
            <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
            <ul class="link-list">
                <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?= SERVERURL ?>registrar-usuarios/"><i class="fas fa-user-plus"></i> Registro de usuarios</a></li>
                <li><a href="<?= SERVERURL ?>user-list/"><i class="fas fa-users"></i> Consultar usuarios</a></li>
                <li><a href="<?= SERVERURL ?>asignar-usuarios-faculta/"><i class="fas fa-users"></i> Asignar usuarios a facultad</a></li>
                <li><a href="<?= SERVERURL ?>registro-anteproyectos/"><i class="fas fa-folder-open"></i> Registro de anteproyectos</a></li>
                <li><a href="<?= SERVERURL ?>registro-proyectos/"><i class="fas fa-folder-plus"></i> Registro de proyectos</a></li>
                <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-file-alt"></i> Consultar Proyectos</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-anteproyecto/"><i class="fas fa-user-graduate"></i> Asignar estudiante a anteproyecto</a></li>
                <li><a class="active" href="<?= SERVERURL ?>asignar-estudiantes-proyectos/"><i class="fas fa-user-graduate"></i> Asignar estudiante a proyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignacion-asesor/"><i class="fas fa-chalkboard-teacher"></i> Asignación de director</a></li>
                <li><a href="<?= SERVERURL ?>asignar-jurados/"><i class="fas fa-gavel"></i> Asignar jurados proyectos</a></li>
                <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>informe-aplicacion/"><i class="fas fa-chart-bar"></i> Informe general aplicación</a></li>
                <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-magnifying-glass"></i>  Consulta de proyectos calificados</a></li>
            </ul>
        </div>
    </div>

<?php

} else  if (isset($ruta[0]) && $ruta[0] == "consultar-ideas" || $ruta[0] == "ideas-update") {

?>

    <div class="navbar-container">
        <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
        <div class="link-container">
            <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
            <ul class="link-list">
                <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?= SERVERURL ?>registrar-usuarios/"><i class="fas fa-user-plus"></i> Registro de usuarios</a></li>
                <li><a href="<?= SERVERURL ?>user-list/"><i class="fas fa-users"></i> Consultar usuarios</a></li>
                <li><a href="<?= SERVERURL ?>asignar-usuarios-faculta/"><i class="fas fa-users"></i> Asignar usuarios a facultad</a></li>
                <li><a href="<?= SERVERURL ?>registro-anteproyectos/"><i class="fas fa-folder-open"></i> Registro de anteproyectos</a></li>
                <li><a href="<?= SERVERURL ?>registro-proyectos/"><i class="fas fa-folder-plus"></i> Registro de proyectos</a></li>
                <li><a class="active" href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-file-alt"></i> Consultar Proyectos</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-anteproyecto/"><i class="fas fa-user-graduate"></i> Asignar estudiante a anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-proyectos/"><i class="fas fa-user-graduate"></i> Asignar estudiante a proyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignacion-asesor/"><i class="fas fa-chalkboard-teacher"></i> Asignación de director</a></li>
                <li><a href="<?= SERVERURL ?>asignar-jurados/"><i class="fas fa-gavel"></i> Asignar jurados proyectos</a></li>
                <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>informe-aplicacion/"><i class="fas fa-chart-bar"></i> Informe general aplicación</a></li>
                <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-magnifying-glass"></i>  Consulta de proyectos calificados</a></li>
            </ul>
        </div>
    </div>



<?php

} else  if (isset($ruta[0]) && $ruta[0] == "registro-proyectos") {

?>

    <div class="navbar-container">
        <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
        <div class="link-container">
            <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
            <ul class="link-list">
                <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?= SERVERURL ?>registrar-usuarios/"><i class="fas fa-user-plus"></i> Registro de usuarios</a></li>
                <li><a href="<?= SERVERURL ?>user-list/"><i class="fas fa-users"></i> Consultar usuarios</a></li>
                <li><a href="<?= SERVERURL ?>asignar-usuarios-faculta/"><i class="fas fa-users"></i> Asignar usuarios a facultad</a></li>
                <li><a href="<?= SERVERURL ?>registro-anteproyectos/"><i class="fas fa-folder-open"></i> Registro de anteproyectos</a></li>
                <li><a class="active" href="<?= SERVERURL ?>registro-proyectos/"><i class="fas fa-folder-plus"></i> Registro de proyectos</a></li>
                <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-file-alt"></i> Consultar Proyectos</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-anteproyecto/"><i class="fas fa-user-graduate"></i> Asignar estudiante a anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-proyectos/"><i class="fas fa-user-graduate"></i> Asignar estudiante a proyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignacion-asesor/"><i class="fas fa-chalkboard-teacher"></i> Asignación de director</a></li>
                <li><a href="<?= SERVERURL ?>asignar-jurados/"><i class="fas fa-gavel"></i> Asignar jurados proyectos</a></li>
                <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>informe-aplicacion/"><i class="fas fa-chart-bar"></i> Informe general aplicación</a></li>
                <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-magnifying-glass"></i>  Consulta de proyectos calificados</a></li>
            </ul>
        </div>
    </div>


<?php

} else  if (isset($ruta[0]) && $ruta[0] == "consultar-proyectos" || $ruta[0] == "proyecto-update") {

?>

    <div class="navbar-container">
        <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
        <div class="link-container">
            <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
            <ul class="link-list">
                <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?= SERVERURL ?>registrar-usuarios/"><i class="fas fa-user-plus"></i> Registro de usuarios</a></li>
                <li><a href="<?= SERVERURL ?>user-list/"><i class="fas fa-users"></i> Consultar usuarios</a></li>
                <li><a href="<?= SERVERURL ?>asignar-usuarios-faculta/"><i class="fas fa-users"></i> Asignar usuarios a facultad</a></li>
                <li><a href="<?= SERVERURL ?>registro-anteproyectos/"><i class="fas fa-folder-open"></i> Registro de anteproyectos</a></li>
                <li><a href="<?= SERVERURL ?>registro-proyectos/"><i class="fas fa-folder-plus"></i> Registro de proyectos</a></li>
                <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                <li><a class="active" href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-file-alt"></i> Consultar Proyectos</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-anteproyecto/"><i class="fas fa-user-graduate"></i> Asignar estudiante a anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-proyectos/"><i class="fas fa-user-graduate"></i> Asignar estudiante a proyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignacion-asesor/"><i class="fas fa-chalkboard-teacher"></i> Asignación de director</a></li>
                <li><a href="<?= SERVERURL ?>asignar-jurados/"><i class="fas fa-gavel"></i> Asignar jurados proyectos</a></li>
                <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>informe-aplicacion/"><i class="fas fa-chart-bar"></i> Informe general aplicación</a></li>
                <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-magnifying-glass"></i>  Consulta de proyectos calificados</a></li>
            </ul>
        </div>
    </div>

<?php

} else  if (isset($ruta[0]) && $ruta[0] == "asignacion-asesor") {

?>
    <div class="navbar-container">
        <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
        <div class="link-container">
            <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
            <ul class="link-list">
                <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?= SERVERURL ?>registrar-usuarios/"><i class="fas fa-user-plus"></i> Registro de usuarios</a></li>
                <li><a href="<?= SERVERURL ?>user-list/"><i class="fas fa-users"></i> Consultar usuarios</a></li>
                <li><a href="<?= SERVERURL ?>asignar-usuarios-faculta/"><i class="fas fa-users"></i> Asignar usuarios a facultad</a></li>
                <li><a href="<?= SERVERURL ?>registro-anteproyectos/"><i class="fas fa-folder-open"></i> Registro de anteproyectos</a></li>
                <li><a href="<?= SERVERURL ?>registro-proyectos/"><i class="fas fa-folder-plus"></i> Registro de proyectos</a></li>
                <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-file-alt"></i> Consultar Proyectos</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-anteproyecto/"><i class="fas fa-user-graduate"></i> Asignar estudiante a anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-proyectos/"><i class="fas fa-user-graduate"></i> Asignar estudiante a proyecto</a></li>
                <li><a class="active" href="<?= SERVERURL ?>asignacion-asesor/"><i class="fas fa-chalkboard-teacher"></i> Asignación de director</a></li>
                <li><a href="<?= SERVERURL ?>asignar-jurados/"><i class="fas fa-gavel"></i> Asignar jurados proyectos</a></li>
                <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>informe-aplicacion/"><i class="fas fa-chart-bar"></i> Informe general aplicación</a></li>
                <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-magnifying-glass"></i>  Consulta de proyectos calificados</a></li>
            </ul>
        </div>
    </div>

<?php

} else  if (isset($ruta[0]) && $ruta[0] == "asignar-usuarios-faculta") {

?>

    <div class="navbar-container">
        <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
        <div class="link-container">
            <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
            <ul class="link-list">
                <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?= SERVERURL ?>registrar-usuarios/"><i class="fas fa-user-plus"></i> Registro de usuarios</a></li>
                <li><a href="<?= SERVERURL ?>user-list/"><i class="fas fa-users"></i> Consultar usuarios</a></li>
                <li><a class="active" href="<?= SERVERURL ?>asignar-usuarios-faculta/"><i class="fas fa-users"></i> Asignar usuarios a facultad</a></li>
                <li><a href="<?= SERVERURL ?>registro-anteproyectos/"><i class="fas fa-folder-open"></i> Registro de anteproyectos</a></li>
                <li><a href="<?= SERVERURL ?>registro-proyectos/"><i class="fas fa-folder-plus"></i> Registro de proyectos</a></li>
                <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-file-alt"></i> Consultar Proyectos</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-anteproyecto/"><i class="fas fa-user-graduate"></i> Asignar estudiante a anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-proyectos/"><i class="fas fa-user-graduate"></i> Asignar estudiante a proyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignacion-asesor/"><i class="fas fa-chalkboard-teacher"></i> Asignación de director</a></li>
                <li><a href="<?= SERVERURL ?>asignar-jurados/"><i class="fas fa-gavel"></i> Asignar jurados proyectos</a></li>
                <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>informe-aplicacion/"><i class="fas fa-chart-bar"></i> Informe general aplicación</a></li>
                <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-magnifying-glass"></i>  Consulta de proyectos calificados</a></li>
            </ul>
        </div>
    </div>

<?php

} else  if (isset($ruta[0]) && $ruta[0] == "como-redactar-anteproyecto") {

?>

    <div class="navbar-container">
        <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
        <div class="link-container">
            <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
            <ul class="link-list">
                <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?= SERVERURL ?>registrar-usuarios/"><i class="fas fa-user-plus"></i> Registro de usuarios</a></li>
                <li><a href="<?= SERVERURL ?>user-list/"><i class="fas fa-users"></i> Consultar usuarios</a></li>
                <li><a href="<?= SERVERURL ?>asignar-usuarios-faculta/"><i class="fas fa-users"></i> Asignar usuarios a facultad</a></li>
                <li><a href="<?= SERVERURL ?>registro-anteproyectos/"><i class="fas fa-folder-open"></i> Registro de anteproyectos</a></li>
                <li><a href="<?= SERVERURL ?>registro-proyectos/"><i class="fas fa-folder-plus"></i> Registro de proyectos</a></li>
                <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-file-alt"></i> Consultar Proyectos</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-anteproyecto/"><i class="fas fa-user-graduate"></i> Asignar estudiante a anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-proyectos/"><i class="fas fa-user-graduate"></i> Asignar estudiante a proyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignacion-asesor/"><i class="fas fa-chalkboard-teacher"></i> Asignación de director</a></li>
                <li><a href="<?= SERVERURL ?>asignar-jurados/"><i class="fas fa-gavel"></i> Asignar jurados proyectos</a></li>
                <li><a class="active" href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>informe-aplicacion/"><i class="fas fa-chart-bar"></i> Informe general aplicación</a></li>
                <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-magnifying-glass"></i>  Consulta de proyectos calificados</a></li>
            </ul>
        </div>
    </div>

<?php

} else  if (isset($ruta[0]) && $ruta[0] == "asignar-jurados") {

?>
    <div class="navbar-container">
        <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
        <div class="link-container">
            <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
            <ul class="link-list">
                <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?= SERVERURL ?>registrar-usuarios/"><i class="fas fa-user-plus"></i> Registro de usuarios</a></li>
                <li><a href="<?= SERVERURL ?>user-list/"><i class="fas fa-users"></i> Consultar usuarios</a></li>
                <li><a href="<?= SERVERURL ?>asignar-usuarios-faculta/"><i class="fas fa-users"></i> Asignar usuarios a facultad</a></li>
                <li><a href="<?= SERVERURL ?>registro-anteproyectos/"><i class="fas fa-folder-open"></i> Registro de anteproyectos</a></li>
                <li><a href="<?= SERVERURL ?>registro-proyectos/"><i class="fas fa-folder-plus"></i> Registro de proyectos</a></li>
                <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-file-alt"></i> Consultar Proyectos</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-anteproyecto/"><i class="fas fa-user-graduate"></i> Asignar estudiante a anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-proyectos/"><i class="fas fa-user-graduate"></i> Asignar estudiante a proyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignacion-asesor/"><i class="fas fa-chalkboard-teacher"></i> Asignación de director</a></li>
                <li><a class="active" href="<?= SERVERURL ?>asignar-jurados/"><i class="fas fa-gavel"></i> Asignar jurados proyectos</a></li>
                <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>informe-aplicacion/"><i class="fas fa-chart-bar"></i> Informe general aplicación</a></li>
                <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-magnifying-glass"></i>  Consulta de proyectos calificados</a></li>
            </ul>
        </div>
    </div>

<?php

} else  if (isset($ruta[0]) && $ruta[0] == "informe-aplicacion") {

?>
    <div class="navbar-container">
        <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
        <div class="link-container">
            <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
            <ul class="link-list">
                <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?= SERVERURL ?>registrar-usuarios/"><i class="fas fa-user-plus"></i> Registro de usuarios</a></li>
                <li><a href="<?= SERVERURL ?>user-list/"><i class="fas fa-users"></i> Consultar usuarios</a></li>
                <li><a href="<?= SERVERURL ?>asignar-usuarios-faculta/"><i class="fas fa-users"></i> Asignar usuarios a facultad</a></li>
                <li><a href="<?= SERVERURL ?>registro-anteproyectos/"><i class="fas fa-folder-open"></i> Registro de anteproyectos</a></li>
                <li><a href="<?= SERVERURL ?>registro-proyectos/"><i class="fas fa-folder-plus"></i> Registro de proyectos</a></li>
                <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-file-alt"></i> Consultar Proyectos</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-anteproyecto/"><i class="fas fa-user-graduate"></i> Asignar estudiante a anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-proyectos/"><i class="fas fa-user-graduate"></i> Asignar estudiante a proyecto</a></li>
                <li><a href="<?= SERVERURL ?>asignacion-asesor/"><i class="fas fa-chalkboard-teacher"></i> Asignación de director</a></li>
                <li><a href="<?= SERVERURL ?>asignar-jurados/"><i class="fas fa-gavel"></i> Asignar jurados proyectos</a></li>
                <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a class="active" href="<?= SERVERURL ?>informe-aplicacion/"><i class="fas fa-chart-bar"></i> Informe general aplicación</a></li>
                <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-magnifying-glass"></i>  Consulta de proyectos calificados</a></li>
            </ul>
        </div>
    </div>


<?php

} else  if (isset($ruta[0]) && $ruta[0] == "proyectos-asignados-jurados") {

    ?>
        <div class="navbar-container">
            <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
            <div class="link-container">
                <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
                <ul class="link-list">
                    <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?= SERVERURL ?>registrar-usuarios/"><i class="fas fa-user-plus"></i> Registro de usuarios</a></li>
                    <li><a href="<?= SERVERURL ?>user-list/"><i class="fas fa-users"></i> Consultar usuarios</a></li>
                    <li><a href="<?= SERVERURL ?>asignar-usuarios-faculta/"><i class="fas fa-users"></i> Asignar usuarios a facultad</a></li>
                    <li><a href="<?= SERVERURL ?>registro-anteproyectos/"><i class="fas fa-folder-open"></i> Registro de anteproyectos</a></li>
                    <li><a href="<?= SERVERURL ?>registro-proyectos/"><i class="fas fa-folder-plus"></i> Registro de proyectos</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-file-alt"></i> Consultar Proyectos</a></li>
                    <li><a href="<?= SERVERURL ?>asignar-estudiantes-anteproyecto/"><i class="fas fa-user-graduate"></i> Asignar estudiante a anteproyecto</a></li>
                    <li><a href="<?= SERVERURL ?>asignar-estudiantes-proyectos/"><i class="fas fa-user-graduate"></i> Asignar estudiante a proyecto</a></li>
                    <li><a href="<?= SERVERURL ?>asignacion-asesor/"><i class="fas fa-chalkboard-teacher"></i> Asignación de director</a></li>
                    <li><a href="<?= SERVERURL ?>asignar-jurados/"><i class="fas fa-gavel"></i> Asignar jurados proyectos</a></li>
                    <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                    <li><a href="<?= SERVERURL ?>informe-aplicacion/"><i class="fas fa-chart-bar"></i> Informe general aplicación</a></li>
                    <li><a class="active" href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-magnifying-glass"></i>  Consulta de proyectos calificados</a></li>
                </ul>
            </div>
        </div>
    
    
    <?php
    
    }


else {
    /** si no existe ninguna sección establecida mostrara el menu normal */

?>
    <div class="navbar-container">
        <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
        <div class="link-container">
            <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
            <ul class="link-list">
                <li><a class="active" href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?= SERVERURL ?>registrar-usuarios/"><i class="fas fa-user-plus"></i> Registro de usuarios</a></li>
                <li><a href="<?= SERVERURL ?>user-list/"><i class="fas fa-users"></i> Consultar usuarios</a></li>
                <li><a href="<?= SERVERURL ?>asignar-usuarios-faculta/"><i class="fas fa-users"></i> Asignar usuarios a facultad</a></li>
                <li><a href="<?= SERVERURL ?>registro-anteproyectos/"><i class="fas fa-folder-open"></i> Registro de anteproyectos</a></li>
                <li><a href="<?= SERVERURL ?>registro-proyectos/"><i class="fas fa-folder-plus"></i> Registro de proyectos</a></li>
                <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-file-alt"></i> Consultar Proyectos</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-anteproyecto/"><i class="fas fa-user-graduate"></i> Asignar usuario a anteproyectos</a></li>
                <li><a href="<?= SERVERURL ?>asignar-estudiantes-proyectos/"><i class="fas fa-user-graduate"></i> Asignar usuario a proyectos</a></li>
                <li><a href="<?= SERVERURL ?>asignacion-asesor/"><i class="fas fa-chalkboard-teacher"></i> Asignación de director</a></li>
                <li><a href="<?= SERVERURL ?>asignar-jurados/"><i class="fas fa-gavel"></i> Asignar jurados</a></li>
                <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>informe-aplicacion/"><i class="fas fa-chart-bar"></i> Informe general aplicación</a></li>
                <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-magnifying-glass"></i>  Consulta de proyectos calificados</a></li>
            </ul>
        </div>
    </div>


<?php
}

?>