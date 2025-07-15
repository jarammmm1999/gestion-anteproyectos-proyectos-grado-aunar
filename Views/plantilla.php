<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= COMPANY ?></title>
    <link rel="shortcut icon" href="<?= SERVERURL ?>/Views/assets/images/logo-ico.png" type="image/x-icon">
    <script src="https://kit.fontawesome.com/9b2b8e0f24.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <?php include "./Views/inc/Css.php" ?> <!-- Estilos CSS -->
</head>

<body>

    <?php
    $peticionAjax = false;
    require_once "./Controller/VistaControlador.php";
    $IVC = new VistaControlador();
    $vistasPaginas = $IVC->obtener_vista_controlador();

    // Verifica si $vistasPaginas contiene "login" o "404" y carga la vista correspondiente
    if (
        $vistasPaginas == "login" || $vistasPaginas == "404" || $vistasPaginas == "forgot-password"
        || $vistasPaginas == "restore-password"  || $vistasPaginas == "consultar-ideas-registradas"
    ) {
        // Si la página es login o 404, carga la vista correspondiente
      
        require_once "./Controller/loginControlador.php";

        $ins_loginControlador = new LoginControlador();

        if (isset($_GET['views'])) {
            $ruta = explode(
                "/",
                $_GET['views']
            );
            $url_pagina = explode("/", $_GET['views']);
        }

       


        $rutaVista = "./Views/content/" . $vistasPaginas . "-view.php";

        // Comprueba si el archivo existe antes de incluirlo
        if (file_exists($rutaVista)) {
            ?>

            <!-- Spinner que aparecerá mientras se valida -->
            <div id="spinner-container" style="visibility: hidden;">
                         <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                             <span class="visually-hidden">Loading...</span>
                         </div>
                         <p class="mt-2 tex-validacion" id="spinner-text">Validando Información ....</p>
                     </div>
     
             <?php
             
            require_once $rutaVista;
        } else {
            echo "La vista no se encuentra disponible.";
        }
    } else {


        session_start(['name' => 'Smp']); // iniciamos la session de los usuarios

        $url_pagina = explode("/", $_GET['views']);

        require_once "./Controller/loginControlador.php";

        $ins_loginControlador = new LoginControlador();  // Crear una nueva instancia del controlador de login

        // Verificar si alguna de las variables de sesión importantes no está definida
        // Esto comprueba que el usuario esté correctamente autenticado y tenga los datos de sesión necesarios

        if (
            !isset($_SESSION['token_usuario']) || !isset($_SESSION['nombre_usuario'])

            || !isset($_SESSION['privilegio']) || !isset($_SESSION['id_usuario'])
        ) {

            // Si alguna de las variables de sesión no está definida (lo que indica que no se ha iniciado sesión correctamente),
            // llamar al método 'cerrar_sesion_controlador()' del controlador de login para cerrar la sesión del usuario

            echo $ins_loginControlador->cerrar_sesion_controlador();
            exit();
        } else {



        ?>
            <div class="content-main-aplication">

                <!-- Spinner que aparecerá mientras se valida -->
                <div id="spinner-container" style="visibility: hidden;">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 tex-validacion" id="spinner-text">Validando Información ....</p>
                </div>
            </div>
            <!---------------------------------------boton flotante------------------------------------------>
            <button id="btn-flotante" class="boton-flotante">
                <i class="fa-solid fa-up-long"></i> 
            </button>
            <?php
            include "./Views/inc/NavBar.php";  // Incluye el navbar
            ?>
            <div class="container-main-pages">
                <?php
                include "./Views/inc/NavegationUser.php";  // Incluye navegación de usuario
                // Carga la vista principal
                if (file_exists($vistasPaginas)) {
                    ?>
                    <div class="container-pages-user-viseted">
                     <div class="container-bar">
                         <i class="fa-solid fa-bars menu-toggle-user-visited"></i>
                     </div>
                        <?php 
                        include "./Views/inc/text-page.php";  // Incluye texto adicional
                        include $vistasPaginas;
                        //include "./Views/inc/footer.php";
                        ?>
                    </div>
                <?php
                } else {
                    echo "La vista no se encuentra disponible.";
                }
                ?>
            </div>


    <?php


        }

        include "./Views/inc/Logout.php";
        include "./Views/inc/inactividad-usuarios.php";
       
    }
    ?>

    <!-- Siempre incluir los scripts aquí, sin importar la vista -->
    <?php include "./Views/inc/Script.php" ?> <!-- Incluye los scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>



</body>

</html>