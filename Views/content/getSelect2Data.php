<?php

$peticionAjax = true;
// Incluir el archivo que contiene la clase MainModel
require_once "../../Model/Consultas.php"; 

$ins_MainModelo = new Consultas();
// Recibir el valor que se pasa a la función

if (isset($_GET['id'])) {

    $id = Consultas::limpiar_cadenas($_REQUEST["id"]);

    $consulta = $ins_MainModelo->ejecutar_consultas_simples_two("SELECT * FROM programas_academicos where id_facultad = $id ");

    // Comprobar si hay resultados
    if ($consulta->rowCount() > 0) {
        $programas = [];
        while ($resultado = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $programas[] = $resultado;
        }
        echo json_encode($programas); // Enviar como JSON
    } else {
        echo json_encode([]); // Si no hay datos, enviar un array vacío
    }
    

}