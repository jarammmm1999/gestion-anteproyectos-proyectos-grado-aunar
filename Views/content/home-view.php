<?php


if (isset($_SESSION['privilegio'])) {
?>
    <div class="search-container mb-4">
        <input type="text" id="searchInput" class="search-input" placeholder="Buscar..." onkeyup="buscarTarjetas()">
    </div>


    <?php
    if ($_SESSION['privilegio'] == 1 || $_SESSION['privilegio'] == 2) {
    ?>

        <div class="card-container mt-5 mb-5">
            <a href="<?= SERVERURL ?>registrar-usuarios/" class="card">
                <div class="card-header">Registro de usuarios</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/registrar usuario.png" alt="Registro de usuarios">
                </div>
                <div class="card-footer">
                   <span>Paso <b>1</b></span>
                </div>
            </a>
            <a href="<?= SERVERURL ?>user-list/" class="card">
                <div class="card-header">Consulta de usuarios</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/buscar - usuarios.png" alt="Consulta de usuarios">
                </div>
                <div class="card-footer">
                   <span>Paso <b>2</b></span>
                </div>
            </a>

            <a href="<?= SERVERURL ?>asignar-usuarios-faculta/" class="card">
                <div class="card-header">A. usuarios a facultad</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>Views/assets/images/asignar.png" alt="Reportes">
                </div>
                <div class="card-footer">
                   <span>Paso <b>3</b></span>
                </div>
            </a>

            <a href="<?= SERVERURL ?>registro-anteproyectos/" class="card">
                <div class="card-header">Registro de anteproyectos</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/idea.png" alt="Registro de proyectos">
                </div>
                <div class="card-footer">
                   <span>Paso <b>4</b></span>
                </div>
            </a>

            <a href="<?= SERVERURL ?>registro-proyectos/" class="card">
                <div class="card-header">Registro de proyectos </div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/registrar proyecto.png" alt="Registro de proyectos">
                </div>
                <div class="card-footer">
                   <span>Paso <b>5</b></span>
                </div>
            </a>

            <a href="<?= SERVERURL ?>consultar-ideas/" class="card">
                <div class="card-header">Consulta de ideas</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/consultar anteproyectos.png" alt="Consulta de ideas">
                </div>
                <div class="card-footer">
                   <span>Paso <b>6</b></span>
                </div>
            </a>

            <a href="<?= SERVERURL ?>consultar-proyectos/" class="card">
                <div class="card-header">Consultar proyectos</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/consultar proyecto.png" alt="Consultar Proyectos">
                </div>
                <div class="card-footer">
                   <span>Paso <b>7</b></span>
                </div>
            </a>


            <a href="<?= SERVERURL ?>asignar-estudiantes-anteproyecto/" class="card">
                <div class="card-header">Asignar usuarios a anteproyectos</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>Views/assets/images/asignado.png" alt="Reportes">
                </div>
                <div class="card-footer">
                   <span>Paso <b>8</b></span>
                </div>
            </a>

            <a href="<?= SERVERURL ?>asignar-estudiantes-proyectos/" class="card">
                <div class="card-header">Asignar usuarios a proyectos</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>Views/assets/images/asignado.png" alt="Reportes">
                </div>
                <div class="card-footer">
                   <span>Paso <b>9</b></span>
                </div>
            </a>


            <a href="<?= SERVERURL ?>asignacion-asesor/" class="card">
                <div class="card-header">Asignar director</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/Asiganar Asesor.png" alt="Asignar asesor">
                </div>
                <div class="card-footer">
                   <span>Paso <b>12</b></span>
                </div>
            </a>

            <a href="<?= SERVERURL ?>asignar-jurados/" class="card">
                <div class="card-header">Asignar jurado proyecto</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/jurado.png" alt="Asignar horas asesoría">
                </div>
                <div class="card-footer">
                   <span>Paso <b>13</b></span>
                </div>
            </a>


          
            <a href="<?= SERVERURL ?>como-redactar-anteproyecto" class="card">
                <div class="card-header">C. redactar su anteproyecto</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/redactar anteproyecto.png" alt="Como redactar su anteproyecto">
                </div>
                <div class="card-footer">
                   <span>Paso <b>15</b></span>
                </div>
            </a>
            
            <a href="<?= SERVERURL ?>informe-aplicacion/" class="card">
                <div class="card-header">Informe </div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/resporte.png" alt="Informes">
                </div>
                <div class="card-footer">
                   <span>Paso <b>16</b></span>
                </div>
            </a>

            <a href="<?= SERVERURL ?>proyectos-asignados-jurados/" class="card">
                <div class="card-header">Proyectos a calificados</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/jurado-one.png" alt="Asignar Director">
                </div>
                <div class="card-footer">
                   <span>Paso <b>17</b></span>
                </div>
            </a>
           

        
        </div>



        <?php
    } else if ($_SESSION['privilegio'] == 3 || $_SESSION['privilegio'] == 4) {

        /******************************************************************/

        if ($_SESSION['privilegio'] == 3) {

            $consulta_anteprotecto_estado = "SELECT 
                    ae.numero_documento,
                    ae.codigo_anteproyecto,  
                    a.titulo_anteproyecto,
                    a.estado, 
                    a.palabras_claves, 
                    a.fecha_creacion, 
                    f.nombre_facultad, 
                    p.nombre_programa 
                FROM asignar_estudiante_anteproyecto ae
                INNER JOIN anteproyectos a ON ae.codigo_anteproyecto = a.codigo_anteproyecto
                INNER JOIN facultades f ON a.id_facultad = f.id_facultad
                INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
                WHERE ae.numero_documento = '$documento_user_logueado'";

            $resultado_consulta_estado_anteproyecto = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_anteprotecto_estado);

            if ($resultado_consulta_estado_anteproyecto->rowCount() > 0) {
                foreach ($resultado_consulta_estado_anteproyecto->fetchAll(PDO::FETCH_ASSOC) as $row):
                    $estado_idea_estudiante = $row['estado'];
                endforeach;
            }

        ?>

            <div class="container-mostrar-mensaje-aprovado">
                <?php
                if (isset($codido_idea_estudiante)) {

                    if ($estado_idea_estudiante == 'Aprobado') {
                ?>
                        <div class="mensaje-container">
                            <h1>🎉 ¡Felicidades! 🎉</h1>
                            <p>Tu anteproyecto ya fue aprobado, ¡felicitaciones!</p>
                        </div>
                    <?php
                    } else if ($estado_idea_estudiante == 'Cancelado') {
                    ?>
                        <div class="canceled-container">
                            <div class="icon-container">
                                <div class="circle"></div>
                                <div class="cross"></div>
                            </div>
                            <h1 class="canceled-title">Anteproyecto Cancelado</h1>
                            <p class="canceled-message">Lo sentimos, tu proyecto ha sido cancelado. No te desanimes, sigue intentándolo.</p>
                            <p class="canceled-message">Comunicate con tu Director, para mas información, o directamente con el coordinador de la facultad</p>
                        </div>

                <?php
                    }
                }
                ?>
            </div>

        <?php
        } else if($_SESSION['privilegio'] == 4){
            $consulta_anteprotecto_estado = "SELECT 
            ae.numero_documento,
            ae.codigo_proyecto,  
            a.titulo_proyecto,
            a.estado, 
            a.palabras_claves, 
            a.fecha_creacion, 
            f.nombre_facultad, 
            p.nombre_programa 
        FROM asignar_estudiante_proyecto ae
        INNER JOIN proyectos a ON ae.codigo_proyecto = a.codigo_proyecto
        INNER JOIN facultades f ON a.id_facultad = f.id_facultad
        INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
        WHERE ae.numero_documento = '$documento_user_logueado'";

    $resultado_consulta_estado_anteproyecto = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_anteprotecto_estado);

    if ($resultado_consulta_estado_anteproyecto->rowCount() > 0) {
        foreach ($resultado_consulta_estado_anteproyecto->fetchAll(PDO::FETCH_ASSOC) as $row):
            $estado_proyecto_estudiante = $row['estado'];
        endforeach;
    }

        ?>

            <div class="container-mostrar-mensaje-aprovado">
                <?php
                if (isset($estado_proyecto_estudiante)) {

                    if ($estado_proyecto_estudiante == 'Aprobado') {
                ?>
                        <div class="mensaje-container">
                            <h1>🎉 ¡Felicidades! 🎉</h1>
                            <p>Tu Proyecto ya fue aprobado, ¡felicitaciones!</p>
                        </div>
                    <?php
                    } else if ($estado_proyecto_estudiante == 'Cancelado') {
                    ?>
                        <div class="canceled-container">
                            <div class="icon-container">
                                <div class="circle"></div>
                                <div class="cross"></div>
                            </div>
                            <h1 class="canceled-title">Proyecto Cancelado</h1>
                            <p class="canceled-message">Lo sentimos, tu proyecto ha sido cancelado. No te desanimes, sigue intentándolo.</p>
                            <p class="canceled-message">Comunicate con tu Director, para mas información, o directamente con el coordinador de la facultad</p>
                        </div>

                <?php
                    }
                }
                ?>
            </div>

        <?php




        }

        ?>
        <div class="card-container mt-5 mb-5">

            <a href="<?= SERVERURL ?>cargar-docuemento-user/" class="card">
                <div class="card-header">Enviar Documentos</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/subir documento.png" alt="Consulta de ideas">
                </div>
            </a>
            <a href="<?= SERVERURL ?>consultar-retroalimentaciones/" class="card">
                <div class="card-header">Consulta de obervaciones</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/supervision.png" alt="Consulta de ideas">
                </div>
            </a>

            <?php

            if ($_SESSION['privilegio'] == 3) {
            ?>
                <a href="<?= SERVERURL ?>consultar-ideas/" class="card">
                    <div class="card-header">Consulta de ideas</div>
                    <div class="card-content">
                        <img src="<?= SERVERURL ?>/Views/assets/images/consultar anteproyectos.png" alt="Consulta de ideas">
                    </div>
                </a>
            <?php

            } else if ($_SESSION['privilegio'] == 4) {
            ?>
                <a href="<?= SERVERURL ?>consultar-proyectos/" class="card">
                    <div class="card-header">Consulta de proyectos</div>
                    <div class="card-content">
                        <img src="<?= SERVERURL ?>/Views/assets/images/consultar anteproyectos.png" alt="Consulta de ideas">
                    </div>
                </a>

                <a href="<?= SERVERURL ?>retroalimentacion-anteproyectos/" class="card">
                    <div class="card-header">Retroalimentacion Anteproyecto</div>
                    <div class="card-content">
                        <img src="<?= SERVERURL ?>/Views/assets/images/buena-resena.png" alt="Consulta de ideas">
                    </div>
                </a>

                <a href="<?= SERVERURL ?>consultar-jurados-asignados-proyectos/<?=$codido_proyecto_estudiante?>" class="card">
                    <div class="card-header">Consultar Jurados Asignados</div>
                    <div class="card-content">
                        <img src="<?= SERVERURL ?>/Views/assets/images/jurado.png" >
                    </div>
                </a>
                
                

            <?php
            }

            ?>



            <a href="<?= SERVERURL ?>como-redactar-anteproyecto" class="card">
                <div class="card-header">C. redactar su anteproyecto</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/redactar anteproyecto.png" alt="Como redactar su anteproyecto">
                </div>
            </a>

           

           

            

        </div>

    <?php

    /*******************************Script********************************/ 

    if(isset($estado_idea_estudiante)){
        // Ejemplo de condicional PHP
    if ($estado_idea_estudiante == "Cancelado"  ) {
        echo '
      <script>
        // Lágrimas dinámicas en toda la página
        window.onload = function() {
            const body = document.body; // Seleccionamos todo el body
            for (let i = 0; i < 90; i++) { // Aumentamos el número de lágrimas
                let tear = document.createElement("div");
                tear.classList.add("tear");
    
                // Posición aleatoria en la página
                tear.style.left = Math.random() * 100 + "vw"; 
                tear.style.top = Math.random() * -100 + "vh"; // Comienzan desde fuera de la pantalla
                
                // Configuramos la duración aleatoria
                tear.style.animationDuration = Math.random() * 3 + 2 + "s"; 
    
                body.appendChild(tear); // Añadimos las lágrimas al body
            }
        };
    </script>
    
        ';
    } else if ($estado_idea_estudiante == "Aprobado" ) {
        echo '
        <!-- Partículas animadas -->
        <script>
            function crearConfetti() {
                const alturaMaxima = document.documentElement.scrollHeight;
    
                for (let i = 0; i < 80; i++) {
                    const confetti = document.createElement("div");
                    confetti.classList.add("confetti");
                    document.body.appendChild(confetti);
                    confetti.style.left = Math.random() * 95 + "vw";
                    confetti.style.animationDuration = Math.random() * 3 + 2 + "s"; // Duración aleatoria
                    confetti.style.animationDelay = Math.random() * 4 + "s";
                    confetti.style.backgroundColor = getRandomColor();
    
                    // Configurar animación dinámica
                    confetti.style.animationName = `fall-${i}`;
    
                    const style = document.createElement("style");
                    style.innerHTML = `
                        @keyframes fall-${i} {
                            0% {
                                transform: translateY(-1px) rotate(0);
                            }
                            100% {
                                transform: translateY(${alturaMaxima}px) rotate(360deg);
                            }
                        }
                    `;
                    document.head.appendChild(style);
                }
            }
    
            function getRandomColor() {
                const colors = ["#ff0000", "#ff9900", "#ffee00", "#00ff00", "#00ccff", "#9900ff"];
    
                return colors[Math.floor(Math.random() * colors.length)];
            }
    
            crearConfetti();
        </script>
        ';
    }
    
    }


    if(isset($estado_proyecto_estudiante)){
        // Ejemplo de condicional PHP
    if ($estado_proyecto_estudiante == "Cancelado"  ) {
        echo '
      <script>
        // Lágrimas dinámicas en toda la página
        window.onload = function() {
            const body = document.body; // Seleccionamos todo el body
            for (let i = 0; i < 90; i++) { // Aumentamos el número de lágrimas
                let tear = document.createElement("div");
                tear.classList.add("tear");
    
                // Posición aleatoria en la página
                tear.style.left = Math.random() * 100 + "vw"; 
                tear.style.top = Math.random() * -100 + "vh"; // Comienzan desde fuera de la pantalla
                
                // Configuramos la duración aleatoria
                tear.style.animationDuration = Math.random() * 3 + 2 + "s"; 
    
                body.appendChild(tear); // Añadimos las lágrimas al body
            }
        };
    </script>
    
        ';
    } else if ($estado_proyecto_estudiante == "Aprobado" ) {
        echo '
        <!-- Partículas animadas -->
        <script>
            function crearConfetti() {
                const alturaMaxima = document.documentElement.scrollHeight;
    
                for (let i = 0; i < 80; i++) {
                    const confetti = document.createElement("div");
                    confetti.classList.add("confetti");
                    document.body.appendChild(confetti);
                    confetti.style.left = Math.random() * 95 + "vw";
                    confetti.style.animationDuration = Math.random() * 3 + 2 + "s"; // Duración aleatoria
                    confetti.style.animationDelay = Math.random() * 4 + "s";
                    confetti.style.backgroundColor = getRandomColor();
    
                    // Configurar animación dinámica
                    confetti.style.animationName = `fall-${i}`;
    
                    const style = document.createElement("style");
                    style.innerHTML = `
                        @keyframes fall-${i} {
                            0% {
                                transform: translateY(-1px) rotate(0);
                            }
                            100% {
                                transform: translateY(${alturaMaxima}px) rotate(360deg);
                            }
                        }
                    `;
                    document.head.appendChild(style);
                }
            }
    
            function getRandomColor() {
                const colors = ["#ff0000", "#ff9900", "#ffee00", "#00ff00", "#00ccff", "#9900ff"];
    
                return colors[Math.floor(Math.random() * colors.length)];
            }
    
            crearConfetti();
        </script>
        ';
    }
    
    }




    } else if ($_SESSION['privilegio'] == 5  || $_SESSION['privilegio'] == 6) {
    ?>
        <div class="card-container mt-5 mb-5">

            <a href="<?= SERVERURL ?>anteproyectos-asignados-asesor/" class="card">
                <div class="card-header">Anteproyectos Asignados</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/aglutinante.png" alt="Consulta de ideas">
                </div>
            </a>
            <a href="<?= SERVERURL ?>proyectos-asignados-asesor/" class="card">
                <div class="card-header">Proyectos Asignados</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/carpetas.png" alt="Consulta de ideas">
                </div>
            </a>

            <a href="<?= SERVERURL ?>consultar-ideas/" class="card">
                <div class="card-header">Consulta de ideas</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/consultar anteproyectos.png" alt="Consulta de ideas">
                </div>
            </a>

            <a href="<?= SERVERURL ?>consultar-proyectos/" class="card">
                <div class="card-header">Consulta de proyectos</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/consultar anteproyectos.png" alt="Consulta de ideas">
                </div>
            </a>


            <a href="<?= SERVERURL ?>proyectos-asignados-jurados/" class="card">
                <div class="card-header">Proyectos asignados jurado</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/jurado-one.png" alt="Asignar Director">
                </div>
            </a>

            <a href="<?= SERVERURL ?>como-redactar-anteproyecto" class="card">
                <div class="card-header">C. redactar su anteproyecto</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/redactar anteproyecto.png" alt="Como redactar su anteproyecto">
                </div>
            </a>


        </div>

<?php
    }
}

?>