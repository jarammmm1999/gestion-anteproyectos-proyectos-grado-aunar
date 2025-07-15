<?php

require_once "./Model/VistaModelo.php";

class VistaControlador extends VistaModelo {

    public function obtener_plantilla_controlador() {
        // Cargar la plantilla principal de la aplicación
        return require_once "./Views/plantilla.php";
    }

    public function obtener_vista_controlador() {
        // Verificar si 'views' está definido en la URL
        if (isset($_GET['views'])) {

            // Dividir la URL en segmentos utilizando "/"
            $ruta = explode("/", $_GET['views']);

            // Obtener la vista solicitada usando el modelo
            $respuesta = VistaModelo::obtener_vista_modelo($ruta[0]);

            // Validar si la vista existe
            if ($respuesta) {
                return $respuesta;
            } else {
                // Redirigir a una vista de error si no existe
                return "./Views/404.php";
            }
        } else {
            // Si no hay 'views' en la URL, cargar la vista por defecto
            return "login";
        }
    }
}
?>
