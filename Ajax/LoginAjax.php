<?php
    $peticionAjax = true;

    require_once "../Configuration/App.php";

    if(isset($_POST["DocumentoUserLog"])){

     require_once '../Controller/loginControlador.php';
     $ins_usuarioControlador = new LoginControlador();

     if(isset($_POST["DocumentoUserLog"]) && isset($_POST["passwordUserLog"])){
        echo $ins_usuarioControlador->iniciar_sesion_controlador(); 
     }
        
    }else if(isset($_POST["token"]) && isset($_POST["usuario"])){

        require_once '../Controller/loginControlador.php';
        $ins_usuarioControlador = new LoginControlador();

        echo $ins_usuarioControlador->cerrar_sesion_usuarios_controlador(); 


    }else{
        session_start(['name' => 'Smp']);
        session_unset();
        session_destroy();
        header("location: ".SERVERURL."login/");
        exit();
    }