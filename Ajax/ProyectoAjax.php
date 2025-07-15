<?php
$peticionAjax = true;

require_once "../Configuration/App.php";

require_once '../Controller/ProyectoControlador.php';

$ins_ControladorProyecto = new ProyectoControlador();

if (isset($_POST["codigo_proyecto_reg"]) && isset($_POST["palabras_claves_proyecto_reg"])) {

    echo $ins_ControladorProyecto->registrar_proyectos_controlador();
    
} else if (isset($_POST["codigo_proyecto_regAsig"]) && isset($_POST["numero_documento_regP"])) {

    echo $ins_ControladorProyecto->Asignar_estudiante_proyecto();

} else if (isset($_POST["titulo_proyecto_upd"]) && isset($_POST["codigo_proyecto_upd"])) {

    echo $ins_ControladorProyecto->actualizar_proyecto_controlador();

}else if (isset($_POST["documento_userDP"]) && isset($_POST["codigoDP"])) {

    echo $ins_ControladorProyecto->eliminar_estudiante_proyecto();

} else if (isset($_POST["codigo_proyecto_delete"]) ) {

    echo $ins_ControladorProyecto->eliminar_proyecto_controlador();

}else if (isset($_POST["codigo_proyecto_asignar"]) && isset($_POST["documento_user_asignar"])

&& isset($_POST["numero_documento_user_logueado"]) && isset($_POST["tipoProyectoAnteproyecto"])

&& isset($_POST["idProgramaProyectoAnteproyecto"])) {

    echo $ins_ControladorProyecto->asignar_asesor_proyecto_controlador();

}else if (isset($_POST["numero_documento_sum_user"]) ) {

    echo $ins_ControladorProyecto->sumar_horas_profesores_controlador();
}else if (isset($_POST["numero_documento_res_user"]) ) {

    echo $ins_ControladorProyecto->restar_horas_profesores_controlador();
    
}else if (isset($_POST["delete_horas_asesor"]) ) {

    echo $ins_ControladorProyecto->eliminar_horas_profesores_controlador();
    
}else if (isset($_POST["numero_documento_sum_user_jurado"]) ) {

    echo $ins_ControladorProyecto->sumar_horas_jurado_profesores_controlador();
    
}else if (isset($_POST["numero_documento_res_user_jurado"]) ) {

    echo $ins_ControladorProyecto->restar_horas_jurado_profesores_controlador();
    
}else if (isset($_POST["codigo_anteproyecto_subir"]) && isset($_POST["numero_documento_user_logueado"])

&& isset($_FILES['archivo_user_anteproyecto']) ) { // cargar documentos usuarios

    echo $ins_ControladorProyecto->cargarDocuemntosProyectos();

} else if (isset($_POST["numero_documento_user_logueado"]) && isset($_POST["codigo_anteproyecto"])

&& isset($_POST["id_documento_cargado"])) {

    echo $ins_ControladorProyecto->retroalimentacion_proyectos();

}else if (isset($_POST['codigo_anteproyecto_evidencia']) && isset($_POST["numero_documento_user_logueado_evidencia"]) ) { // cargar evidencias usuarios

    echo $ins_ControladorProyecto->cargar_evidencias_reuniones_proyectos();

}else if (isset($_POST["actualizar_estado_proyecto"]) ) {
    
    echo $ins_ControladorProyecto->actualizar_estado_proyectos();
  
}

else if (isset($_POST["codigo_proyecto_asignar_jurado"]) && isset($_POST["documento_user_asignar_jurado"])

&& isset($_POST["numero_documento_user_logueado"]) && isset($_POST["tipoProyectoAnteproyecto"])

&& isset($_POST["idProgramaProyectoAnteproyecto"])) {

    echo $ins_ControladorProyecto->asignar_jurado_proyecto_controlador();

}
else if (isset($_POST["delete_evidencia_proyectos"]) ) {

    echo $ins_ControladorProyecto->delete_evidecia_proyecto();
    
}else if (isset($_POST["id_retrolimentacion_editar_proyecto"]) ) {

    echo $ins_ControladorProyecto->actualizar_fecha_retroalimentacion();
    
}else if (isset($_POST["identificador_jurado_evaluador"]) ) {

    echo $ins_ControladorProyecto->calificacion_rubrica_evaluacion();
    
}
else if (isset($_POST["opcion_jurado"]) ) {

    echo $ins_ControladorProyecto->actualizar_opcion_jurados();
    
}else if (isset($_POST["number_acta"]) ) {

    echo $ins_ControladorProyecto->registrar_acta_calificacion_proyectos();
    
}else if (isset($_POST["nueva_fecha_sustentacion"]) ) {

    echo $ins_ControladorProyecto->actualizar_fecha_sustentacion();
    
}


else {
    session_start(['name' => 'Smp']);
    session_unset();
    session_destroy();
    header("location: " . SERVERURL . "login/");
    exit();
}
