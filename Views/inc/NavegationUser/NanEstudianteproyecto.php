<?php

if (isset($_GET['views'])) {
    $ruta = explode(
        "/",
        $_GET['views']
    );
}

if ($_SESSION['privilegio'] != 4 ) { 
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}

if (isset($ruta[0]) && $ruta[0] == "consultar-proyectos") {

    ?>
        <div class="navbar-container">
        <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
        <div class="link-container">
            <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
                <ul class="link-list">
                <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?= SERVERURL ?>consultar-retroalimentaciones/"><i class="fas fa-comments"></i> Consultar Retroalimentaciones</a></li>
                <li><a href="<?= SERVERURL ?>cargar-docuemento-user/"> <i class="fas fa-file-upload"></i>Enviar Docuemento</a></li>
                <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a class="active" href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-lightbulb"></i> Consulta de proyectos</a></li>
                <li><a href="<?= SERVERURL ?>retroalimentacion-anteproyectos/"><i class="fa-solid fa-magnifying-glass"></i> Consulta retroalimentaciones anteproyecto</a></li> 
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
                    <li><a href="<?= SERVERURL ?>consultar-retroalimentaciones/"><i class="fas fa-comments"></i> Consultar Retroalimentaciones</a></li>
                    <li><a href="<?= SERVERURL ?>cargar-docuemento-user/"><i class="fas fa-file-upload"></i> Enviar Docuemento</a></li>
                    <li><a class="active" href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-lightbulb"></i> Consulta de proyectos</a></li>
                    <li><a href="<?= SERVERURL ?>retroalimentacion-anteproyectos/"><i class="fa-solid fa-magnifying-glass"></i> Consulta retroalimentaciones anteproyecto</a></li> 
                </ul>
            </div>
        </div>
        
        
        <?php
        
    } else if (isset($ruta[0]) && $ruta[0] == "cargar-docuemento-user") {

        ?>
            <div class="navbar-container">
            <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
            <div class="link-container">
                <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
                    <ul class="link-list">
                    <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-retroalimentaciones/"><i class="fas fa-comments"></i> Consultar Retroalimentaciones</a></li>
                    <li><a class="active" href="<?= SERVERURL ?>cargar-docuemento-user/"><i class="fas fa-file-upload"></i> Enviar Docuemento</a></li>
                    <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-lightbulb"></i> Consulta de proyectos</a></li>
                    <li><a href="<?= SERVERURL ?>retroalimentacion-anteproyectos/"><i class="fa-solid fa-magnifying-glass"></i> Consulta retroalimentaciones anteproyecto</a></li> 
                </ul>
            </div>
        </div>
        
        
        <?php
        
    }  else if (isset($ruta[0]) && $ruta[0] == "consultar-retroalimentaciones" || isset($ruta[0]) && $ruta[0] == "ver-documentos-anteproyectos-asesor" ) {

        ?>
            <div class="navbar-container">
            <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
            <div class="link-container">
                <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
                    <ul class="link-list">
                    <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                    <li><a class="active" href="<?= SERVERURL ?>consultar-retroalimentaciones/"><i class="fas fa-comments"></i> Consultar Retroalimentaciones</a></li>
                    <li><a href="<?= SERVERURL ?>cargar-docuemento-user/"><i class="fas fa-file-upload"></i> Enviar Docuemento</a></li>
                    <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-lightbulb"></i> Consulta de proyectos</a></li>
                    <li><a href="<?= SERVERURL ?>retroalimentacion-anteproyectos/"><i class="fa-solid fa-magnifying-glass"></i> Consulta retroalimentaciones anteproyecto</a></li> 
                </ul>
            </div>
        </div>
        
        
        <?php
        
    } else if (isset($ruta[0]) && $ruta[0] == "retroalimentacion-anteproyectos") {

        ?>
            <div class="navbar-container">
            <button class="menu-toggle-user" aria-label="Abrir menú">Desplegar menú</button>
            <div class="link-container">
                <button class="menu-close-user" aria-label="Cerrar menú">✕</button>
                    <ul class="link-list">
                    <li><a href="<?= SERVERURL ?>home/"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-retroalimentaciones/"><i class="fas fa-comments"></i> Consultar Retroalimentaciones</a></li>
                    <li><a href="<?= SERVERURL ?>cargar-docuemento-user/"><i class="fas fa-file-upload"></i> Enviar Docuemento</a></li>
                    <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                    <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-lightbulb"></i> Consulta de proyectos</a></li>
                    <li><a class="active" href="<?= SERVERURL ?>retroalimentacion-anteproyectos/"><i class="fa-solid fa-magnifying-glass"></i> Consulta retroalimentaciones anteproyecto</a></li> 
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
                <li><a href="<?= SERVERURL ?>consultar-retroalimentaciones/"><i class="fas fa-comments"></i> Consultar Retroalimentaciones</a></li>
                <li><a href="<?= SERVERURL ?>cargar-docuemento-user/"><i class="fas fa-file-upload"></i> Enviar Documento</a></li>
                <li><a href="<?= SERVERURL ?>como-redactar-anteproyecto"><i class="fas fa-pencil-alt"></i> Cómo redactar su anteproyecto</a></li>
                <li><a href="<?= SERVERURL ?>consultar-proyectos/"><i class="fas fa-lightbulb"></i> Consulta de proyectos</a></li>  
                <li><a href="<?= SERVERURL ?>retroalimentacion-anteproyectos/"><i class="fa-solid fa-magnifying-glass"></i> Consulta retroalimentaciones anteproyecto</a></li>          
            </ul>
        </div>
    </div>

<?php
}

?>