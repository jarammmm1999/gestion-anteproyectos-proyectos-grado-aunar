<?php

if (isset($_GET['views'])) {
    $ruta = explode(
        "/",
        $_GET['views']
    );

    $codigoproyecto = $ruta[1];

    $fecha = $ruta[2];

    $codigo = $ruta[3];

    $fecha = $ins_loginControlador->decryption_two($fecha);

    $codigo = $ins_loginControlador->decryption_two($codigo);
}

if ($_SESSION['privilegio'] != 5 &&  $_SESSION['privilegio'] != 1 &&  $_SESSION['privilegio'] != 2
 
&&  $_SESSION['privilegio'] != 3 &&  $_SESSION['privilegio'] != 4 ) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}

if ($codigoproyecto == 1) {
    $consulta_evidencias = "SELECT *
    FROM evidencia_reuniones_anteproyectos
    WHERE codigo_anteproyecto = '$codigo'
    AND DATE(fecha_creacion) = '$fecha'";

    $resultado_evidencias_anteproyecto = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_evidencias);

    if ($resultado_evidencias_anteproyecto->rowCount() == 0) {
        echo '<div class="container-alert">
            <div class="alert alert-danger" role="alert">
                <div class="text-center"> No hay datos que mostrar</div>
              </div>
        </div>';
    } else {
        ?>

        <div class="gallery-container">
            <h1 class="text-center mt-2 mb-5">Evidencias fotograficas</h1>
            <div class="gallery-grid">
                <?php 
                if ($resultado_evidencias_anteproyecto->rowCount() > 0) {
                
                foreach ($resultado_evidencias_anteproyecto->fetchAll(PDO::FETCH_ASSOC) as $raw): ?>
                    <a href="<?= SERVERURL ?>Views/document/anteproyectos/<?= $codigo ?>/evidencia/<?= $raw['imagenes'] ?>" data-lightbox="gallery" data-title="<?= $raw['imagenes'] ?>" class="gallery-item">
                        <img src="<?= SERVERURL ?>Views/document/anteproyectos/<?= $codigo ?>/evidencia/<?= $raw['imagenes'] ?>" alt="Imagen">
                        <div class="overlay"><?= $raw['imagenes'] ?></div>
                    </a>
                <?php endforeach; }?>
            </div>
            
            <?php
            if (isset($_SESSION['privilegio']) && $_SESSION['privilegio'] == 5  || $_SESSION['privilegio'] == 2 || $_SESSION['privilegio'] == 1) {

                ?>
                  <div class="text-center">
                    <form class="user-for  mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/AnteproyectoAjax.php" method="POST" data-form="delete" autocomplete="off">
                        <div class="form-actions mt-5 mb-5">
                            <input type="hidden" name="delete_evidencia_anteproyectos" value="<?= $ins_loginControlador->encryption($codigo) ?>">
                            <input type="hidden" name="fecha" value="<?= $ins_loginControlador->encryption($fecha) ?>">
                            <button type="submit"><i class="fa-solid fa-trash-can"></i> &nbsp; Eliminar evidencia fotograficas</button>
                        </div>
                    </form>
                    </div>

                <?php
            }
            
            ?>
          

        </div>

    <?php
    }
    
} else if ($codigoproyecto == 2) {
    $consulta_evidencias = "SELECT *
    FROM evidencia_reuniones_proyectos
    WHERE codigo_proyecto = '$codigo'
    AND DATE(fecha_creacion) = '$fecha'";

    $resultado_evidencias_anteproyecto = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_evidencias);

    if ($resultado_evidencias_anteproyecto->rowCount() == 0) {
        echo '<div class="container-alert">
        <div class="alert alert-danger" role="alert">
            <div class="text-center"> No hay datos que mostrar</div>
          </div>
        </div>';

    }else{
        ?>

        <div class="gallery-container">
            <h1 class="text-center mt-2 mb-5">Evidencias fotograficas</h1>
            <div class="gallery-grid">
                <?php 
                if ($resultado_evidencias_anteproyecto->rowCount() > 0) {
                
                foreach ($resultado_evidencias_anteproyecto->fetchAll(PDO::FETCH_ASSOC) as $raw): ?>
                    <a href="<?= SERVERURL ?>Views/document/proyectos/<?= $codigo ?>/evidencia/<?= $raw['imagenes'] ?>" data-lightbox="gallery" data-title="<?= $raw['imagenes'] ?>" class="gallery-item">
                        <img src="<?= SERVERURL ?>Views/document/proyectos/<?= $codigo ?>/evidencia/<?= $raw['imagenes'] ?>" alt="Imagen">
                        <div class="overlay"><?= $raw['imagenes'] ?></div>
                    </a>
                <?php endforeach; }?>
            </div>
        </div>

        <?php
        if (isset($_SESSION['privilegio']) && $_SESSION['privilegio'] == 5  || $_SESSION['privilegio'] == 2 || $_SESSION['privilegio'] == 1) {
            ?>
            <div class="text-center">
                    <form class="user-for  mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="delete" autocomplete="off">
                        <div class="form-actions mt-5 mb-5">
                            <input type="hidden" name="delete_evidencia_proyectos" value="<?= $ins_loginControlador->encryption($codigo) ?>">
                            <input type="hidden" name="fecha" value="<?= $ins_loginControlador->encryption($fecha) ?>">
                            <button type="submit"><i class="fa-solid fa-trash-can"></i> &nbsp; Eliminar evidencia fotograficas</button>
                        </div>
                    </form>
                </div>
            <?php
        }
        ?>

       

    <?php

    }
    
} else {
    // Otro contenido si no coincide
}
