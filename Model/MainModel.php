<?php

if($peticionAjax){
    
    require_once "../Configuration/Server.php";

    require_once "../Configuration/App.php";

}else{
    // Incluir el archivo de configuraciones del servidor
    require_once "./Configuration/Server.php";

    require_once "./Configuration/App.php";
}

class MainModel {
    // Función para conectarse a la base de datos
    protected static function conectar(){
        $conexion = new PDO('mysql:host='.SERVER.';dbname='.DB.'',USER, PASS,[
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false
    ]);
        $conexion->exec("SET CHARACTER SET utf8mb4 ");
        return $conexion;
    }

    Protected static function ejecutar_consultas_simples($consulta){
        $sql = self::conectar()->prepare($consulta);
        $sql->execute();
        return $sql;
    }

    public static function ejecutar_consultas_simples_two_ajax($consulta){
        $sql = self::conectar()->prepare($consulta);
        $sql->execute();
        return $sql;
    }


    public  function ejecutar_consultas_simples_two($consulta){
        $sql = self::conectar()->prepare($consulta);
        $sql->execute();
        return $sql;
    }

    public  function encryption($string){
        $output=FALSE;
        $key=hash('sha256', SECRET_KEY);
        $iv=substr(hash('sha256', SECRET_IV), 0, 16);
        $output=openssl_encrypt($string, METHOD, $key, 0, $iv);
        $output=base64_encode($output);
        return $output;
    }


    protected static function decryption($string){
        $key=hash('sha256', SECRET_KEY);
        $iv=substr(hash('sha256', SECRET_IV), 0, 16);
        $output=openssl_decrypt(base64_decode($string), METHOD, $key, 0, $iv);
        return $output;
    }

        public  function decryption_two($string){
        $key=hash('sha256', SECRET_KEY);
        $iv=substr(hash('sha256', SECRET_IV), 0, 16);
        $output=openssl_decrypt(base64_decode($string), METHOD, $key, 0, $iv);
        return $output;
    }
    
    protected static function ocultar_contrasena($contrasena) {
        $longitud = strlen($contrasena); // Longitud de la contraseña
        if ($longitud > 3) {
            $ocultos = str_repeat('*', $longitud - 3); // Generar los asteriscos
            $visibles = substr($contrasena, -3); // Obtener los últimos 3 caracteres
            return $ocultos . $visibles; // Combinar los asteriscos con los últimos caracteres
        }
        return $contrasena; // Si la contraseña es muy corta, no la modifica
    }

    protected static function  generar_codigo_aleatorios($letra,$longitud,$numero){
        for($i=1;$i<$longitud;$i++){
            $aleatorio=rand(0,9);
            $letra.=$aleatorio;
        }
        return $letra."-".$numero;
    }


    protected static function obtenerDireccionIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            // Convertir ::1 a 127.0.0.1
            return $_SERVER['REMOTE_ADDR'] === '::1' ? '127.0.0.1' : $_SERVER['REMOTE_ADDR'];
        }
    }
    

    protected static function obtenerNavegador() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
        if (strpos($userAgent, 'Edge') !== false) {
            return 'Microsoft Edge';
        } elseif (strpos($userAgent, 'Edg') !== false) {
            return 'Microsoft Edge (Chromium)';
        } elseif (strpos($userAgent, 'Chrome') !== false) {
            return 'Google Chrome';
        } elseif (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Mozilla Firefox';
        } elseif (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR') !== false) {
            return 'Opera';
        } elseif (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
            return 'Internet Explorer';
        } else {
            return 'Navegador desconocido';
        }
    }
    

    protected static function limpiar_cadenas($cadena) {
        // Eliminar espacios en blanco al inicio y al final
        $cadena = trim($cadena);
        // Convertir caracteres especiales a entidades HTML para prevenir XSS
        $cadena = htmlspecialchars($cadena, ENT_QUOTES, 'UTF-8');
        // Eliminar posibles inyecciones SQL, pero se recomienda utilizar prepared statements
        $cadena = preg_replace('/\b(SELECT|DELETE|INSERT|UPDATE|DROP|SHOW|TRUNCATE)\b/i', '', $cadena);
        // Eliminar cualquier rastro de PHP o scripts
        $cadena = preg_replace('/<\?php.*?\?>/i', '', $cadena);  // Eliminar cualquier rastro de PHP
        $cadena = preg_replace('/<script.*?<\/script>/i', '', $cadena);  // Eliminar cualquier script
        // Eliminar posibles caracteres peligrosos adicionales
        $cadena = str_replace(array("'", "\"", "--", ";", ">", "<", "[", "]", "^", "==", "::"), '', $cadena);
        // Eliminar barras invertidas
        $cadena = stripslashes($cadena);
    
        return $cadena;
    }
    
    protected static function verificar_datos($filtro,$cadena){
        if(preg_match("/^".$filtro."$/",$cadena)){
            return false;
        }else{
            return true;
        }
    }

    protected static function verificar_fecha($fecha){
        $valores=explode('-',$fecha);
        if(count($valores)==3 && checkdate($valores[1],$valores[2],$valores[3])){
            return false;
        }else{
            return true;
        }
    }
	

    /************************************* paginar de tablas *************************** */
    protected static function paginador_tablas($pagina, $Npaginas, $url, $botones)
    {
        $tabla = '<nav aria-label="Page navigation example"><ul class="pagination justify-content-center">';

        if ($pagina == 1) {
            $tabla .= '<li class="page-item disabled"><a class="page-link"><i class="fas fa-angle-double-left"></i></a></li>';
        } else {
            $tabla .= '
            <li class="page-item"><a class="page-link" href="' . $url . '1/"><i class="fas fa-angle-double-left"></i></a></li>
            <li class="page-item"><a class="page-link" href="' . $url . ($pagina - 1) . '/">Anterior</a></li>
            ';
        }

        $ci = 0;
        for ($i = $pagina; $i <= $Npaginas; $i++) {
            if ($ci >= $botones) {
                break;
            }

            if ($pagina == $i) {
                $tabla .= '<li class="page-item"><a class="page-link active" href="' . $url . $i . '/">' . $i . '</a></li>';
            } else {
                $tabla .= '<li class="page-item"><a class="page-link" href="' . $url . $i . '/">' . $i . '</a></li>';
            }

            $ci++;
        }


        if ($pagina == $Npaginas) {
            $tabla .= '<li class="page-item disabled"><a class="page-link"><i class="fas fa-angle-double-right"></i></a></li>';
        } else {
            $tabla .= '
            <li class="page-item"><a class="page-link" href="' . $url . ($pagina + 1) . '/">Siguiente</a></li>
            <li class="page-item"><a class="page-link" href="' . $url . $Npaginas . '/"><i class="fas fa-angle-double-right"></i></a></li>
            ';
        }



        $tabla .= '</ul></nav>';
        return $tabla;
    }
    

    

}

