<?php

if (isset($_SESSION['privilegio'])) {

    if ($_SESSION['privilegio'] == 1 || $_SESSION['privilegio'] == 2) {

        include_once "NavegationUser/NavAdministrador.php";
       
    }if ($_SESSION['privilegio'] == 3  ) {

        include_once "NavegationUser/NanEstudianteAnteproyecto.php";

    }if ($_SESSION['privilegio'] == 5   || $_SESSION['privilegio'] == 6) {

        include_once "NavegationUser/NavAsesores.php";

    }if ($_SESSION['privilegio'] == 4 ) {

        include_once "NavegationUser/NanEstudianteproyecto.php";
    }
}

?>