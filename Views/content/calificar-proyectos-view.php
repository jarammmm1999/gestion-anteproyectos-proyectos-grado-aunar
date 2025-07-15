<?php

if ($_SESSION['privilegio'] != 5 && $_SESSION['privilegio'] != 1 && $_SESSION['privilegio'] != 2 && $_SESSION['privilegio'] != 4) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}

if (isset($_GET['views'])) {
    $ruta = explode(
        "/",
        $_GET['views']
    );

    $codigo = $ruta[1];

   
   
}



?>

<div class="card-container mt-5 mb-5">

   <?php
   
   if($_SESSION['privilegio'] == 1){
    ?>
     <a href="<?= SERVERURL ?>asignar-jurados/<?=$codigo?>" class="card">
        <div class="card-header">Asignar jurado proyecto </div>
        <div class="card-content">
        <img src="<?= SERVERURL ?>/Views/assets/images/jurado.png">
        </div>
    </a>

    <?php
   }
   
   ?>

    <a href="<?= SERVERURL ?>calificacion-jurados/<?=$codigo?>/<?=$ins_loginControlador->encryption($_SESSION['numero_documento'])?>" class="card">
        <div class="card-header">Calificación jurados </div>
        <div class="card-content">
            <img src="<?= SERVERURL ?>/Views/assets/images/asesor-metodologico.png" >
        </div>
    </a>

  

</div>

