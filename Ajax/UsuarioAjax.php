<?php
$peticionAjax = true;

require_once "../Configuration/App.php";

require_once '../Controller/UsuarioControlador.php';
 $ins_usuarioControlador = new UsuarioControlador();

if (isset($_POST["documento_usuario_reg"])  && isset($_POST["password_usuario_reg"])) {
      echo $ins_usuarioControlador->agregar_usuario_controlador();
} else  if (isset($_POST["jsonData"])) {
   echo $ins_usuarioControlador->agregar_usuario_archivo_controlador();
}
else  if (isset($_POST["DatosArchivosFacultad"])) {
   echo $ins_usuarioControlador->agregar_usuario_facultad_archivo_controlador();
}

else  if (isset($_POST["idUsuario_del"])) {
   echo $ins_usuarioControlador->eliminar_usuarios_controlador();
}else  if (isset($_POST["documento_usuario_upd"]) && isset($_POST["telefono_usuario_upd"])) {
   echo $ins_usuarioControlador->editar_usuarios_controlador();
}else  if (isset($_POST["correoresetpassword"]) ) {
   echo $ins_usuarioControlador->recuperar_contrasena_controlador();
} else  if (isset($_POST["IdPassword"]) && isset($_POST["RestorPassword"]) ) {
   echo $ins_usuarioControlador->actulalizar_contrasena_usuario();
} else  if (isset($_POST["documento_usuario_regASG"])) {
   echo $ins_usuarioControlador->agregar_usuarios_facultades_controlador();
}else  if (isset($_POST["idFacultad_del"]) && isset($_POST["idPrograma_del"]) && isset($_POST["documentoFPuser_del"])) {
   echo $ins_usuarioControlador->eliminar_asignacion_usuarios_faculta_controlador();
}else if(isset($_FILES['imagen_user']) && isset($_POST["email_usuario_reg"])){
   echo $ins_usuarioControlador->Actualizar_informacion_user();
}else if(isset($_POST["configuration_name_facultad"])){
   echo $ins_usuarioControlador->registrar_facultada_controlador();
}else if(isset($_POST["configuration_name_modalidad"])){
   echo $ins_usuarioControlador->registrar_modalidad_controlador();
}
else if(isset($_POST["configuration_id_facultad"])){
   echo $ins_usuarioControlador->eliminar_facultad_controlador();
}
else if(isset($_POST["configuration_id_facultad_upd"])){
   echo $ins_usuarioControlador->actualizar_facultad_controlador();
}
else if(isset($_POST["configuration_id_programa_upd"])){
   echo $ins_usuarioControlador->actualizar_programas_controlador();
}
else if(isset($_POST["modalidad_nombre_actualizado"])){
   echo $ins_usuarioControlador->actualizar_modaidad_controlador();
}
else if(isset($_POST["configuration_id_modalidad"])){
   echo $ins_usuarioControlador->eliminar_modalidad_controlador();
}
else if(isset($_POST["configuration_name_programa"])){
   echo $ins_usuarioControlador->registrar_programas_controlador();
}else if(isset($_POST["id_facultad_configuration_delete"]) && isset($_POST["id_programa_configuration_delete"])){
   echo $ins_usuarioControlador->eliminar_programas_controlador();
}else if(isset($_POST["configuration_numero_estudiantes_proyectos"])){
   echo $ins_usuarioControlador->actualizar_numero_estudiantes_proyectos_controlador();
}else if(isset($_POST["codigo_idea_actualizar_asesor"])){
   echo $ins_usuarioControlador->actualizar_asesor_anteproyecto_proyecto();
}else if(isset($_FILES['imagenes_portadas']) ){
   echo $ins_usuarioControlador->cargar_imagenes_portadas();
}
else if(isset($_POST['codigo_id_portada_delete']) ){
   echo $ins_usuarioControlador->eliminar_imagenes_portada();
}
else if(isset($_POST['codigo_id_portada_update']) ){
   echo $ins_usuarioControlador->actualizar_estado_imagenes_portada();

}else if(isset($_FILES['firma_digital']) ){
   echo $ins_usuarioControlador->cargar_firmas_usuarios();
}else if(isset($_FILES['firma_digital_upd']) ){
   echo $ins_usuarioControlador->editar_firmas_usuarios();
}else if(isset($_POST['registro_calificado_programa']) ){
   echo $ins_usuarioControlador->registrar_registros_calificados_controlador();
}else if(isset($_POST['id_programa_registro_calificado']) ){
   echo $ins_usuarioControlador->eliminar_registro_calificado_controlador();
}
else if(isset($_POST['id_programa_registro_calificado_upd']) ){
   echo $ins_usuarioControlador->actualizar_registro_calificado_controlador();
}


else {
   session_start(['name' => 'Smp']);
   session_unset();
   session_destroy();
   header("location: " . SERVERURL . "login/");
   exit();
}
