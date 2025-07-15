<?php

require_once __DIR__ . '/../Configuration/Server.php';

require_once __DIR__ . '/../Configuration/App.php';


    class Consultas {

        public static function ejecutar_consultas_simples_two_ajax($consulta, $parametros = []) {
            try {
                // Establecer la conexión
                $conexion = new PDO('mysql:host='.SERVER.';dbname='.DB.'',USER, PASS);
                $conexion->exec("SET CHARACTER SET utf8");
                
                // Preparar la consulta
                $sql = $conexion->prepare($consulta);
                
                // Vincular los parámetros si existen
                foreach ($parametros as $clave => $valor) {
                    $sql->bindValue($clave, $valor);
                }
                
                // Ejecutar la consulta
                $sql->execute();
                return $sql;
                
            } catch (PDOException $e) {
                // Manejar errores de conexión o consulta
                die("Error en la consulta: " . $e->getMessage());
            }
        }

        public  function ejecutar_consultas_simples_two($consulta){

            try {
                // Establecer la conexión
                $conexion = new PDO('mysql:host='.SERVER.';dbname='.DB.'',USER, PASS);
                $conexion->exec("SET CHARACTER SET utf8");

                 // Preparar la consulta
                 $sql = $conexion->prepare($consulta);

                 // Ejecutar la consulta
                $sql->execute();
                return $sql;
                
            } catch (PDOException $e) {
                // Manejar errores de conexión o consulta
                die("Error en la consulta: " . $e->getMessage());
            }
            
        }

        public static function limpiar_cadenas($cadena) {
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

        public static function encryption($string){
            $output=FALSE;
            $key=hash('sha256', SECRET_KEY);
            $iv=substr(hash('sha256', SECRET_IV), 0, 16);
            $output=openssl_encrypt($string, METHOD, $key, 0, $iv);
            $output=base64_encode($output);
            return $output;
        }

        public static function decryption($string){
            $key=hash('sha256', SECRET_KEY);
            $iv=substr(hash('sha256', SECRET_IV), 0, 16);
            $output=openssl_decrypt(base64_decode($string), METHOD, $key, 0, $iv);
            return $output;
        }
    
    }


