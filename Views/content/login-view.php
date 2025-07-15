<?php

$sqlLogo = "SELECT nombre_logo 
            FROM configuracion_aplicacion 
            LIMIT 1";
$consulta_logo = $ins_loginControlador->ejecutar_consultas_simples_two($sqlLogo);

if ($consulta_logo->rowCount() > 0) {
    $resultado = $consulta_logo->fetch(PDO::FETCH_ASSOC);
    $nombre_logo = $resultado['nombre_logo'];
} else {
    $nombre_logo ="logo-autonoma.png";
}

?>

<div class="container-main-login">
    <div class="container-section image">
        <?php
            
            $sql_imagenes_portadas ="SELECT nombre_imagenes FROM imagenes_portada WHERE estado = 'A'";

            $consulta_imagenes_portadas = $ins_loginControlador->ejecutar_consultas_simples_two($sql_imagenes_portadas);

          

            ?>

                <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    
                    <?php
                     if ($consulta_imagenes_portadas->rowCount() > 0) {
                        $index = 0;
                        while ($fila = $consulta_imagenes_portadas->fetch(PDO::FETCH_ASSOC)) {
                            $imagenes = json_decode($fila['nombre_imagenes'], true);
                            foreach ($imagenes as $imagen) {
                                ?>
                                <button type="button" data-bs-target="#carouselExampleIndicators" 
                                        data-bs-slide-to="<?= $index ?>" 
                                        class="<?= ($index === 0) ? 'active' : '' ?>" 
                                        aria-label="Slide <?= $index + 1 ?>"></button>
                                <?php
                                 $index++;
                            }
                        }
                     }else{
                        echo '<button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-label="Slide 1"></button>';
                     }
                     $consulta_imagenes_portadas->execute();
                    ?>
                </div>
                <div class="carousel-inner">
                    
                    <?php
                     if ($consulta_imagenes_portadas->rowCount() > 0) {
                        $primeraImagen = true; // Bandera para la primera imagen

                        while ($fila = $consulta_imagenes_portadas->fetch(PDO::FETCH_ASSOC)) {
                            $imagenes = json_decode($fila['nombre_imagenes'], true);
                        
                            foreach ($imagenes as $imagen) {
                                // Agregar "active" solo a la primera imagen
                                $claseActive = $primeraImagen ? 'active' : '';
                                echo "<div class='carousel-item $claseActive'>";
                                echo "<img src='" . SERVERURL . "Views/assets/images/ImagenesPortada/$imagen' alt='Imagen de portada' class='d-block w-100'>";
                                echo "</div>";
                        
                                $primeraImagen = false; // Después de la primera imagen, se desactiva la clase "active"
                            }
                        }
                    } else {
                        echo "<div class='carousel-item active'>";
                        echo '<img src="' . SERVERURL . 'Views/assets/images/img-login.jpg" alt="imagen login">';
                        echo "</div>";
                    }
                    ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                </div>

            <?php

           
           


        
        ?>
    </div>
    <div class="container-section login">
        <div class="container-image mb-5">
            <img src="<?= SERVERURL ?>/Views/assets/images/<?=$nombre_logo?>" alt="imagen login">
        </div>
        <div class="container mt-3 container-form">
            <h3 class="text-center mb-3 title-login "><?= strtoupper('Gestion Integral de Proyectos de Grado') ?></h3>
            
           
            <form class ="FormulariosAjaxLogin" action="<?=SERVERURL?>Ajax/LoginAjax.php" method="POST" class="mt-5" autocomplete="off">
                <div class="mb-3 mt-3">
                    <input type="number" class="form-control input-text" id="DocumentoUserLog" name="DocumentoUserLog" placeholder="Usuario" >
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control input-text" id="passwordUserLog" name="passwordUserLog" placeholder="Contraseña" >
                </div>
            
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-send">Ingresar</button>
                </div>
                <div class="text-center mt-4">
                    <a href="<?=SERVERURL?>consultar-ideas-registradas/" target="_blank" class="forgot-password" rel="noopener noreferrer">Consultar proyectos registrados</a>
                </div>
                <div class="text-center mt-4">
                    <a href="<?=SERVERURL?>forgot-password/" target="_blank" class="forgot-password" rel="noopener noreferrer">¿Olvidaste tu contraseña?</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modal-seleccion-perfil" class="modal-seleccion-perfil">
    <div class="modal-seleccion-contenido">
        <h3>Seleccione el rol con el que desea iniciar sesión:</h3>
        <div id="roles-container" class="roles-container">
            <!-- Aquí se cargarán las tarjetas dinámicamente -->
        </div>
        <button class="btn-cerrar" onclick="cerrarModalPerfiles()">Cerrar</button>
    </div>
</div>
