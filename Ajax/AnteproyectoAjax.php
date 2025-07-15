<?php
$peticionAjax = true;

require_once "../Configuration/App.php";

require_once '../Controller/AnteproyectosControlador.php';

$ins_ControladorAnteproyecto = new AnteproyectoControlador();

if (isset($_POST["codigo_anteproyecto_reg"]) && isset($_POST["palabras_claves_anteproyecto_reg"])) {

    echo $ins_ControladorAnteproyecto->registrar_ideas_anteproyectos();

} else if (isset($_POST["numero_documento_user_logueado"]) && isset($_POST["codigo_anteproyecto"])

&& isset($_POST["id_documento_cargado"])) {

    echo $ins_ControladorAnteproyecto->retroalimentacion_anteproyectos();

}
else if (isset($_POST["documento_userDA"]) && isset($_POST["codigoDA"])) {

    echo $ins_ControladorAnteproyecto->eliminar_estudiante_ideas();
    
} else if (isset($_POST["codigo_idea_upd"]) && isset($_POST["palabras_claves_upd"])) {

    echo $ins_ControladorAnteproyecto->actualizar_idea_controlador();
} else if (isset($_POST["codigo_anteproyecto_regA"]) && isset($_POST["numero_documento_regA"])) {

    echo $ins_ControladorAnteproyecto->Asignar_estudiante_proyecto();
} else if (isset($_POST["numero_horas_asesorias_reg"]) && isset($_POST["numero_documento_regP"])

    && isset($_POST["numero_documento_user_logueado"])) {

    echo $ins_ControladorAnteproyecto->Asignar_horas_profesor();

} else if (isset($_POST["numero_horas_jurado_reg"]) && isset($_POST["numero_documento_regP"])

&& isset($_POST["numero_documento_user_logueado"])) {

echo $ins_ControladorAnteproyecto->Asignar_horas_jurado_profesor();

}else if (isset($_POST["codigo_anteproyecto_subir"]) && isset($_POST["numero_documento_user_logueado"])

&& isset($_FILES['archivo_user_anteproyecto']) ) { // cargar documentos usuarios

    echo $ins_ControladorAnteproyecto->cargarDocuemntosAnteproyectos();

}
else if (isset($_POST['codigo_anteproyecto_evidencia']) && isset($_POST["numero_documento_user_logueado_evidencia"]) ) { // cargar evidencias usuarios

    echo $ins_ControladorAnteproyecto->cargar_evidencias_reuniones_anteproyectos();

}

else if (isset($_POST["token"]) && isset($_POST["usuario"])) {

    require_once '../Controller/loginControlador.php';
    $ins_usuarioControlador = new LoginControlador();

    echo $ins_usuarioControlador->cerrar_sesion_usuarios_controlador();

}  else if (isset($_POST["codigo_idea_delete"]) ) {
    
    echo $ins_ControladorAnteproyecto->eliminar_idea_controlador();

}else if (isset($_POST["actualizar_estado_anteproyecto"]) ) {
    
    echo $ins_ControladorAnteproyecto->actualizar_estado_anteproyectos();
  
}else if (isset($_POST["documento_user_asesor"]) && isset($_POST["codigoProyecto"])) {

    echo $ins_ControladorAnteproyecto->eliminar_asesor_proyectos();
    
}
else if (isset($_POST["delete_evidencia_anteproyectos"]) ) {

    echo $ins_ControladorAnteproyecto->delete_evidecia_anteproyecto();
    
}else if (isset($_POST["id_retrolimentacion_editar"]) ) {

    echo $ins_ControladorAnteproyecto->actualizar_fecha_retroalimentacion();
    
}



else {
    session_start(['name' => 'Smp']);
    session_unset();
    session_destroy();
    header("location: " . SERVERURL . "login/");
    exit();
}
