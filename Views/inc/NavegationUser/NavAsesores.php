<?php

if (isset($_GET['views'])) {
    $ruta = explode(
        "/",
        $_GET['views']
    );
}

if ($_SESSION['privilegio'] != 5 && $_SESSION['privilegio'] != 6) { 
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}

if($_SESSION['privilegio'] != 6){

    $consultar_firma_directores = "SELECT firma 
FROM firma_digital_usuarios 
WHERE numero_documento = '$documento_user_logueado'";

$resultado_firma_digital = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_firma_directores);

if ($resultado_firma_digital->rowCount() == 0) {

    if(isset($ruta[0]) && $ruta[0] != "configuration-user"){

        ?>

        <!-- Modal -->
        <div class="modal-firma" id="modalFirma">
            <div class="modal-firma-contenido">
                <h2 class="mb-3">⚠️ Registro de Firma Digital <?=$documento_user_logueado?></h2>
                <p>
                    ⚠️ Antes de continuar, es obligatorio registrar su firma digital.  
                    La firma digital es un requisito esencial para garantizar la autenticidad y seguridad de las modificaciones realizadas en la aplicación.  
                    <br><br>  
                    Sin su firma digital registrada, no podrá proceder con la edición, eliminación o aprobación de documentos dentro del sistema.  
                    <br><br>  
                    Haga clic en el botón de abajo para configurar su firma digital y evitar interrupciones en su flujo de trabajo.
                </p>
                <a href="<?=SERVERURL?>configuration-user/<?= $ins_loginControlador->encryption($_SESSION['privilegio']) ?>/<?= $ins_loginControlador->encryption($_SESSION['numero_documento']) ?>"><button class="modal-firma-boton" >Registrar firma digital</button></a>
                
            </div>
        </div>
    
    <?php
    }
  
}


}




if (isset($ruta[0]) && $ruta[0] == "consultar-ideas" ) {

    ?>
        <div class="navbar-container">
        <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
        <div class="link-container">
            <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
                <ul class="link-list">
                <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?= SERVERURL ?>anteproyectos-asignados-asesor/"><i class="fas fa-comments"></i> Anteproyectos Asignados</a></li>
                <li><a href="<?= SERVERURL ?>proyectos-asignados-asesor/"> <i class="fas fa-file-upload"></i>Proyectos Asignados</a></li>
                <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a class="active" href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-lightbulb"></i> Consulta de proyectos</a></li>
                <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-gavel"></i> Proyectos asignados jurado</a></li>
            </ul>
        </div>
    </div>
    
    
    <?php
    
    } else if (isset($ruta[0]) && $ruta[0] == "como-redactar-anteproyecto") {

        ?>
            <div class="navbar-container">
            <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
            <div class="link-container">
                <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
                    <ul class="link-list">
                    <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?= SERVERURL ?>anteproyectos-asignados-asesor/"><i class="fas fa-comments"></i> Anteproyectos Asignados</a></li>
                    <li><a href="<?= SERVERURL ?>proyectos-asignados-asesor/"><i class="fas fa-file-upload"></i> Proyectos Asignados</a></li>
                    <li><a class="active" href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-lightbulb"></i> Consulta de proyectos</a></li>
                    <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-gavel"></i> Proyectos asignados jurado</a></li>

                </ul>
            </div>
        </div>
        
        
        <?php
        
    } else if (isset($ruta[0]) && $ruta[0] == "proyectos-asignados-asesor") {

        ?>
            <div class="navbar-container">
            <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
            <div class="link-container">
                <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
                    <ul class="link-list">
                    <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?= SERVERURL ?>anteproyectos-asignados-asesor/"><i class="fas fa-comments"></i> Anteproyectos Asignados</a></li>
                    <li><a class="active" href="<?= SERVERURL ?>proyectos-asignados-asesor/"><i class="fas fa-file-upload"></i> Proyectos Asignados</a></li>
                    <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-lightbulb"></i> Consulta de proyectos</a></li>
                    <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-gavel"></i> Proyectos asignados jurado</a></li>

                </ul>
            </div>
        </div>
        
        
        <?php
        
    } else if (isset($ruta[0]) && $ruta[0] == "anteproyectos-asignados-asesor") {

        ?>
            <div class="navbar-container">
            <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
            <div class="link-container">
                <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
                    <ul class="link-list">
                    <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                    <li><a  class="active" href="<?= SERVERURL ?>anteproyectos-asignados-asesor/"><i class="fas fa-comments"></i> Anteproyectos Asignados</a></li>
                    <li><a href="<?= SERVERURL ?>proyectos-asignados-asesor/"><i class="fas fa-file-upload"></i> Proyectos Asignados</a></li>
                    <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-lightbulb"></i> Consulta de proyectos</a></li>
                    <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-gavel"></i> Proyectos asignados jurado</a></li>

                </ul>
            </div>
        </div>
        
        
        <?php
        
    } else if (isset($ruta[0]) && $ruta[0] == "consultar-proyectos") {

        ?>
            <div class="navbar-container">
            <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
            <div class="link-container">
                <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
                    <ul class="link-list">
                    <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?= SERVERURL ?>anteproyectos-asignados-asesor/"><i class="fas fa-comments"></i> Anteproyectos Asignados</a></li>
                    <li><a href="<?= SERVERURL ?>proyectos-asignados-asesor/"><i class="fas fa-file-upload"></i> Proyectos Asignados</a></li>
                    <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                    <li><a class="active" href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-lightbulb"></i> Consulta de proyectos</a></li>
                    <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-gavel"></i> Proyectos asignados jurado</a></li>

                </ul>
            </div>
        </div>
        
        
        <?php
        
    } else if (isset($ruta[0]) && $ruta[0] == "consultar-horas-asesores") {

        ?>
            <div class="navbar-container">
            <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
            <div class="link-container">
                <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
                    <ul class="link-list">
                    <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?= SERVERURL ?>anteproyectos-asignados-asesor/"><i class="fas fa-comments"></i> Anteproyectos Asignados</a></li>
                    <li><a href="<?= SERVERURL ?>proyectos-asignados-asesor/"><i class="fas fa-file-upload"></i> Proyectos Asignados</a></li>
                    <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-lightbulb"></i> Consulta de proyectos</a></li>
                    <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-gavel"></i> Proyectos asignados jurado</a></li>
                    <li><a class="active" href="<?= SERVERURL ?>consultar-horas-asesores/"><i class="fas fa-clock"></i> Consultar horas director</a></li>
                </ul>
            </div>
        </div>
        
        
        <?php
        
    }  else if (isset($ruta[0]) && $ruta[0] == "proyectos-asignados-jurados" || $ruta[0] == "calificar-proyectos") {

        ?>
            <div class="navbar-container">
            <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
            <div class="link-container">
                <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
                    <ul class="link-list">
                    <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?= SERVERURL ?>anteproyectos-asignados-asesor/"><i class="fas fa-comments"></i> Anteproyectos Asignados</a></li>
                    <li><a href="<?= SERVERURL ?>proyectos-asignados-asesor/"><i class="fas fa-file-upload"></i> Proyectos Asignados</a></li>
                    <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-lightbulb"></i> Consulta de proyectos</a></li>
                    <li><a  class="active" href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-gavel"></i> Proyectos asignados jurado</a></li>

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
            <li><a href="<?= SERVERURL ?>anteproyectos-asignados-asesor/"><i class="fas fa-comments"></i> Anteproyectos Asignados</a></li>
            <li><a href="<?= SERVERURL ?>proyectos-asignados-asesor/"><i class="fas fa-file-upload"></i> Proyectos Asignados</a></li>
            <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
            <li><a href="<?= SERVERURL ?>consultar-ideas/"><i class="fas fa-lightbulb"></i> Consulta de ideas</a></li>  
            <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-lightbulb"></i> Consulta de proyectos</a></li>
            <li><a href="<?= SERVERURL ?>proyectos-asignados-jurados/"><i class="fa-solid fa-gavel"></i> Proyectos asignados jurado</a></li>         
        </ul>
    </div>
</div>

<?php
}

?>