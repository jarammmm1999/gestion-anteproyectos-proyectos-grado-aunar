<?php

class VistaModelo {

    protected static function obtener_vista_modelo($vista) {

        // Lista de páginas permitidas
        $listaPaginas = ["home","registrar-usuarios","user-list","user-update","registro-anteproyectos",
        "asignar-estudiantes-anteproyecto","asignar-horas-profesores","consultar-ideas","ideas-update",
        "registro-proyectos", "consultar-proyectos","proyecto-update","asignar-estudiantes-proyectos",
        "asignacion-asesor","asignar-usuarios-faculta","consultar-horas-asesores","como-redactar-anteproyecto",
        "asignar-jurados", "asignar-horas-jurados","cargar-docuemento-user","anteproyectos-asignados-asesor",
        "entregas-anteproyectos","ver-documentos-anteproyectos-asesor","proyectos-asignados-asesor",
        "consultar-retroalimentaciones","evidencias-reuniones","ver-evidencia","entregas-proyectos",
        "ver-documentos-proyectos-asesor","configuration-user","proyectos-asignados-jurados","calificar-proyectos",
        "informe-aplicacion", "retroalimentacion-anteproyectos","asesor-metodologico","calificacion-jurados","acta-proyectos",
         "consultar-jurados-asignados-proyectos","fecha-sustentacion"];

        // Verificar si la vista solicitada está en la lista de páginas permitidas
        if (in_array($vista, $listaPaginas)) {

            // Verificar si el archivo de la vista existe en el directorio correspondiente
            if (is_file("./Views/content/" . $vista . "-view.php")) {
                
                $contenido = "./Views/content/" . $vista . "-view.php";
            } else {
                // Si el archivo no existe, devolver 404
                $contenido = "404";
            }

        } elseif ($vista == "login" || $vista == "index") {
            // Manejo especial para vistas de login o index
            $contenido = "login";

        } elseif ($vista == "forgot-password"){
            // Si la vista solicitada no está en la lista y no es login, devolver 404
            $contenido = "forgot-password";

        }elseif ($vista == "restore-password"){
            $contenido = "restore-password";

        }elseif ($vista == "consultar-ideas-registradas"){
            $contenido = "consultar-ideas-registradas";
        }
        else{
            // Si la vista solicitada no está en la lista y no es login, devolver 404
            $contenido = "404";
        }

        return $contenido;
    }
}
?>
