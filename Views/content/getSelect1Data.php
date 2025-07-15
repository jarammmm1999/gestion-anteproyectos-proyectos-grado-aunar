<?php

$peticionAjax = true;
// Incluir el archivo que contiene la clase MainModel
require_once "../../Model/Consultas.php"; 

$ins_MainModelo = new Consultas();
// Recibir el valor que se pasa a la función

$consulta = $ins_MainModelo->ejecutar_consultas_simples_two("SELECT * FROM facultades");

// Comprobar si hay resultados
if ($consulta->rowCount() > 0) {
    $facultades = [];
    while ($resultado = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $facultades[] = $resultado;
    }
    echo json_encode($facultades); // Enviar como JSON
} else {
    echo json_encode([]); // Si no hay datos, enviar un array vacío
}