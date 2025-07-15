<?php
if ($_SESSION['privilegio'] != 3 && $_SESSION['privilegio'] != 4) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}



if ($_SESSION['privilegio'] == 3) {

 

    /****************** consultar ultima fecha de calificacion de la retroalimentación*********************** */

    $consultar_fecha_ultima_entrega = "SELECT * 
        FROM retroalimentacion_anteproyecto
        WHERE codigo_anteproyecto = '$codido_idea_estudiante'
        ORDER BY id DESC 
        LIMIT 1";

    $resultado_ultima_entrega = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_fecha_ultima_entrega);

    if ($resultado_ultima_entrega->rowCount() > 0) {
        $documento = $resultado_ultima_entrega->fetch(PDO::FETCH_ASSOC);

        // Extraer datos
        $id = $documento['id'];
        $codigo_anteproyecto = $documento['codigo_anteproyecto'];
    
        $estado = $documento['estado'];
        $fecha_subida = isset($documento['fecha_creacion']) ? $documento['fecha_creacion'] : 'No disponible'; // Si existe esta columna
        
        $fecha_limite = isset($documento['fecha_entrega_avances']) ? $documento['fecha_entrega_avances'] : 'No disponible'; 

        $mostrarContador = $estado;

        if($mostrarContador != 1){
            $texto = "Le recordamos que ya está habilitado el plazo para la carga de su avance del proyecto. Es fundamental que suba los documentos requeridos dentro del tiempo establecido para garantizar el adecuado seguimiento y retroalimentación por parte del asesor.";

        }else{
                 
        $texto .= "<p>Le informamos que ya puede enviar el siguiente  avance de su proyecto para su evaluación y calificación por parte de su asesor. 📄✍️</p>";
        $texto .= "<p>Le recomendamos asegurarse de que toda la información esté completa y correctamente presentada para facilitar el proceso de revisión. Aproveche esta oportunidad para demostrar el progreso de su trabajo. 🚀📚</p>";
        $texto .= "<p> ⏳Tiempo restante para la siguinete entrega del avance</p>";
        }

    
        
    } else {

        $consultar_ultima_entrega = "SELECT * 
        FROM cargar_documento_anteproyectos
        WHERE codigo_anteproyecto = '$codido_idea_estudiante'
        ORDER BY id DESC 
        LIMIT 1";

        $resultado_ultima_entrega_estudiantes = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_ultima_entrega);

        if ($resultado_ultima_entrega_estudiantes->rowCount() > 0) {

            $documento_estudiantes = $resultado_ultima_entrega_estudiantes->fetch(PDO::FETCH_ASSOC);

            $estado_cargar_documento = $documento_estudiantes['estado'];

            if($estado_cargar_documento == 1 ){

                $texto = "📢 **Estimado estudiante,** puedes realizar actualizaciones de tu anteproyecto ✍️ antes de que sea calificado por tu asesor. ⏳ ¡Aprovecha esta oportunidad para mejorar tu trabajo! 🚀";

                $mostrarContador = 2;
            }
            
        }else{

            $texto .= "<p>Le informamos que ya puede enviar el siguiente  avance de su proyecto para su evaluación y calificación por parte de su asesor. 📄✍️</p>";
            $texto .= "<p>Le recomendamos asegurarse de que toda la información esté completa y correctamente presentada para facilitar el proceso de revisión. Aproveche esta oportunidad para demostrar el progreso de su trabajo. 🚀📚</p>";

            $texto .= "<p> ⏳ El contador se restablecerá según la fecha que el asesor indique para la subida de la siguiente etapa de su anteproyecto. 📅✨</p>";

            $texto .= "<p>Permanezca atento a las indicaciones.</p>";

            $mostrarContador = 2;

        }
        

    }

        
    ?>

    <div class="container-tiempo-regresivo">
        <div class="contador-container">
                <h2>🚀 Tiempo Restante: <?=$titulo_proyecto?></h2>
                <p><?=$texto?></p>
                <div id="contador" class="contador">Calculando...</div>
            </div>
       </div>

    <form class="user-form mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/AnteproyectoAjax.php" method="POST" enctype="multipart/form-data" data-form="save" autocomplete="off">
        <h2><i class="fa-solid fa-upload"></i> Subir archivo</h2>
        <div class="form-grid two mt-3 mb-3">
            <?php

            $consulta_anteprotecto_estudiante = "SELECT 
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

            $resultado_consulta_anteproyecto = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_anteprotecto_estudiante);

            if ($resultado_consulta_anteproyecto->rowCount() > 0) {

                foreach ($resultado_consulta_anteproyecto->fetchAll(PDO::FETCH_ASSOC) as $row):

                    $codido_idea_estudiante = $row['codigo_anteproyecto'];

                    $estado_idea_estudiante = $row['estado'];
            ?>

                    <div class="form-floating">
                        <input type="text" class="form-control input_border" id="telefono" value="<?= $row['titulo_anteproyecto']; ?>" disabled>
                        <label for="nombreEstudiante">Título</label>
                    </div>

                    <div class="form-floating">
                        <input type="text" class="form-control input_border" id="correoasesor" value="<?= $row['palabras_claves']; ?>" disabled>
                        <label for="tituloProyecto">Palabras claves</label>
                    </div>

                    <input type="hidden" name="numero_documento_user_logueado" value="<?= $ins_loginControlador->encryption($_SESSION['numero_documento']) ?>">
                    <?php
                      if (isset($codido_idea_estudiante) && !empty($codido_idea_estudiante)) {
                        $consulta_ultima_carga = "SELECT * 
                        FROM cargar_documento_anteproyectos 
                        WHERE codigo_anteproyecto = '$codido_idea_estudiante'
                        ORDER BY id DESC 
                        LIMIT 1";
                
                        $resultado_consulta_ultima_carga = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_ultima_carga);

                        if ($resultado_consulta_ultima_carga->rowCount() > 0) {

                        }else{
                            ?>
                            <input type="hidden" name="identificador_carga_documento" value="<?= $ins_loginControlador->encryption(2) ?>">
                            <?php
                        }
                      }
                    ?>
                    <input type="hidden" name="codigo_anteproyecto_subir" value="<?= $ins_loginControlador->encryption($codido_idea_estudiante) ?>">

                <?php endforeach;


            } else {
                ?>
                <div class="form-floating">
                    <input type="text" class="form-control input_border" id="telefono" value="No tiene proyecto asignado" disabled>
                    <label for="nombreEstudiante">Título</label>
                </div>

                <div class="form-floating">
                    <input type="text" class="form-control input_border" id="correoasesor" value="No tiene proyecto asignado" disabled>
                    <label for="tituloProyecto">Palabras claves</label>
                </div>


                <input type="hidden" name="numero_documento_user_logueado" value="<?= $ins_loginControlador->encryption($_SESSION['numero_documento']) ?>">
                <input type="hidden" name="codigo_anteproyecto_subir" value="<?= $ins_loginControlador->encryption(0) ?>">

            <?php

            } ?>
        </div>
        <?php
        if(isset($estado_idea_estudiante)){
            if ($estado_idea_estudiante !== 'Cancelado') {
                ?>
                    <!-- Campo para adjuntar archivo -->
                    <div class="mb-3">
                        <label for="archivo" class="form-label">Archivo adjunto</label>
                        <div class="drag-area">
                            <label for="archivo" class="upload-label">
                                <input type="file" id="archivo" name="archivo_user_anteproyecto[]" multiple hidden>
                                <div class="file-display">
                                    <p>Arrastra y suelta el archivo aquí, o haz clic para seleccionarlo</p>
                                </div>
                            </label>
                        </div>
                    </div>
                <?php
                }
        }
    

        ?>

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
                    <p class="canceled-message">Comunicate con tu asesor, para mas información, o directamente con el coordinador de la facultad</p>
                    <a href="<?= SERVERURL ?>home"> <button type="button" class="retry-button">Volver</button></a>
                </div>

            <?php
            } else {

                
                /******************* verifricar si hay un documento subido por los estudiantes****************************** */

                if (isset($codido_idea_estudiante) && !empty($codido_idea_estudiante)) {
                    
                    $consulta_ultima_carga = "SELECT * 
                    FROM cargar_documento_anteproyectos 
                    WHERE codigo_anteproyecto = '$codido_idea_estudiante'
                    ORDER BY id DESC 
                    LIMIT 1";
            
                    $resultado_consulta_ultima_carga = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_ultima_carga);
            
                    // Verificamos si hay resultados
                      // Verificamos si hay resultados
                      if ($resultado_consulta_ultima_carga->rowCount() > 0) {
                        $datos_documento = $resultado_consulta_ultima_carga->fetch(PDO::FETCH_ASSOC);
                        
                        $estado_revision_documento = $datos_documento['estado'];

                        if($estado_revision_documento == 1){

                            $textoboton = " Actualizar archivos enviados";
                            ?>
                              <input type="hidden" name="identificador_carga_documento" value="<?= $ins_loginControlador->encryption(1) ?>">
                            <?php

                        }else if($estado_revision_documento == 2){

                             $textoboton = " Enviar archivos ";

                             ?>
                              <input type="hidden" name="identificador_carga_documento" value="<?= $ins_loginControlador->encryption(2) ?>">
                            <?php
                        }

                    } else {
                        // No hay documentos cargados
                       $textoboton = "Enviar archivos";

                            ?>
                              <input type="hidden" name="identificador_carga_documento" value="<?= $ins_loginControlador->encryption(2) ?>">
                            <?php
                    }
            
              
                    
                }

            ?>
                <!-- Botón para enviar -->
                <div class="form-actions mt-3">
                    <button id="btnEnviarDocumentos" type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> &nbsp; <?=$textoboton?></button>
                </div>
        <?php
            }
        }

        ?>

    </form>
<?php
} else if ($_SESSION['privilegio'] == 4) {


    /****************** consultar ultima fecha de calificacion de la retroalimentación*********************** */

    $consultar_fecha_ultima_entrega = "SELECT * 
        FROM retroalimentacion_proyecto
        WHERE codigo_proyecto = '$codido_proyecto_estudiante'
        ORDER BY id DESC 
        LIMIT 1";

    $resultado_ultima_entrega = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_fecha_ultima_entrega);

    if ($resultado_ultima_entrega->rowCount() > 0) {
        $documento = $resultado_ultima_entrega->fetch(PDO::FETCH_ASSOC);

        // Extraer datos
        $id = $documento['id'];
        $codigo_anteproyecto = $documento['codigo_proyecto'];
    
        $estado = $documento['estado'];
        $fecha_subida = isset($documento['fecha_creacion']) ? $documento['fecha_creacion'] : 'No disponible'; // Si existe esta columna
        
        $fecha_limite = isset($documento['fecha_entrega_avances']) ? $documento['fecha_entrega_avances'] : 'No disponible'; 

        $mostrarContador = $estado;

        if($mostrarContador != 1){
            $texto = "Le recordamos que ya está habilitado el plazo para la carga de su avance del proyecto. Es fundamental que suba los documentos requeridos dentro del tiempo establecido para garantizar el adecuado seguimiento y retroalimentación por parte del asesor.";

        }else{
                 
        $texto .= "<p>Le informamos que ya puede enviar el siguiente  avance de su proyecto para su evaluación y calificación por parte de su asesor. 📄✍️</p>";
        $texto .= "<p>Le recomendamos asegurarse de que toda la información esté completa y correctamente presentada para facilitar el proceso de revisión. Aproveche esta oportunidad para demostrar el progreso de su trabajo. 🚀📚</p>";
        $texto .= "<p> ⏳Tiempo restante para la siguinete entrega del avance</p>";
        }

    
        
    } else {

        $consultar_ultima_entrega = "SELECT * 
        FROM cargar_documento_proyectos
        WHERE codigo_proyecto = '$codido_proyecto_estudiante'
        ORDER BY id DESC 
        LIMIT 1";

        $resultado_ultima_entrega_estudiantes = $ins_loginControlador->ejecutar_consultas_simples_two($consultar_ultima_entrega);

        if ($resultado_ultima_entrega_estudiantes->rowCount() > 0) {

            $documento_estudiantes = $resultado_ultima_entrega_estudiantes->fetch(PDO::FETCH_ASSOC);

            $estado_cargar_documento = $documento_estudiantes['estado'];

            if($estado_cargar_documento == 1 ){

                $texto = "📢 **Estimado estudiante,** puedes realizar actualizaciones de tu anteproyecto ✍️ antes de que sea calificado por tu asesor. ⏳ ¡Aprovecha esta oportunidad para mejorar tu trabajo! 🚀";

                $mostrarContador = 2;
            }
            
        }else{

            
        $texto .= "<p>Le informamos que ya puede enviar el siguiente  avance de su proyecto para su evaluación y calificación por parte de su asesor. 📄✍️</p>";
        $texto .= "<p>Le recomendamos asegurarse de que toda la información esté completa y correctamente presentada para facilitar el proceso de revisión. Aproveche esta oportunidad para demostrar el progreso de su trabajo. 🚀📚</p>";

        $texto .= "<p> ⏳ El contador se restablecerá según la fecha que el asesor indique para la subida de la siguiente etapa de su anteproyecto. 📅✨</p>";

        $texto .= "<p>Permanezca atento a las indicaciones.</p>";

        $mostrarContador = 2;

        }
        

    }

        
    ?>

    <div class="container-tiempo-regresivo">
        <div class="contador-container">
                <h2>🚀 Tiempo Restante: <?=$titulo_proyecto?></h2>
                <p><?=$texto?></p>
                <div id="contador" class="contador">Calculando...</div>
            </div>
    </div>

    <form class="user-form mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" enctype="multipart/form-data" data-form="save" autocomplete="off">
        <h2><i class="fa-solid fa-upload"></i> Subir archivo</h2>
        <div class="form-grid two mt-3 mb-3">
            <?php

            $consulta_anteprotecto_estudiante = "SELECT 
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

            $resultado_consulta_anteproyecto = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_anteprotecto_estudiante);

            if ($resultado_consulta_anteproyecto->rowCount() > 0) {

                foreach ($resultado_consulta_anteproyecto->fetchAll(PDO::FETCH_ASSOC) as $row):

                    $codido_proyecto_estudiante = $row['codigo_proyecto'];

                    $estado_proyecto_estudiante = $row['estado'];
            ?>

                    <div class="form-floating">
                        <input type="text" class="form-control input_border" id="telefono" value="<?= $row['titulo_proyecto']; ?>" disabled>
                        <label for="nombreEstudiante">Título</label>
                    </div>

                    <div class="form-floating">
                        <input type="text" class="form-control input_border" id="correoasesor" value="<?= $row['palabras_claves']; ?>" disabled>
                        <label for="tituloProyecto">Palabras claves</label>
                    </div>

                    
                    <input type="hidden" name="numero_documento_user_logueado" value="<?= $ins_loginControlador->encryption($_SESSION['numero_documento']) ?>">
                   
                    <input type="hidden" name="codigo_anteproyecto_subir" value="<?= $ins_loginControlador->encryption($codido_proyecto_estudiante) ?>">

                <?php endforeach;
            } else {
                ?>
                <div class="form-floating">
                    <input type="text" class="form-control input_border" id="telefono" value="No tiene proyecto asignado" disabled>
                    <label for="nombreEstudiante">Título</label>
                </div>

                <div class="form-floating">
                    <input type="text" class="form-control input_border" id="correoasesor" value="No tiene proyecto asignado" disabled>
                    <label for="tituloProyecto">Palabras claves</label>
                </div>


                <input type="hidden" name="numero_documento_user_logueado" value="<?= $ins_loginControlador->encryption($_SESSION['numero_documento']) ?>">

                <input type="hidden" name="codigo_anteproyecto_subir" value="<?= $ins_loginControlador->encryption(0) ?>">

            <?php

            } ?>
        </div>
        <!-- Campo para adjuntar archivo -->
        <div class="mb-3">
            <label for="archivo" class="form-label">Archivo adjunto</label>
            <div class="drag-area">
                <label for="archivo" class="upload-label">
                    <input type="file" id="archivo" name="archivo_user_anteproyecto[]" multiple hidden>
                    <div class="file-display">
                        <p>Arrastra y suelta el archivo aquí, o haz clic para seleccionarlo</p>
                    </div>
                </label>
            </div>
        </div>

        
        <?php


        if (isset($codido_proyecto_estudiante)) {

            if ($estado_proyecto_estudiante == 'Aprobado') {
        ?>
                <div class="mensaje-container">
                    <h1>🎉 ¡Felicidades! 🎉</h1>
                    <p>Tu proyecto ya fue aprobado, ¡felicitaciones!</p>
                </div>
            <?php
            } else if ($estado_proyecto_estudiante == 'Cancelado') {
            ?>
                <div class="canceled-container">
                    <div class="icon-container">
                        <div class="circle"></div>
                        <div class="cross"></div>
                    </div>
                    <h1 class="canceled-title">Anteproyecto Cancelado</h1>
                    <p class="canceled-message">Lo sentimos, tu proyecto ha sido cancelado. No te desanimes, sigue intentándolo.</p>
                    <p class="canceled-message">Comunicate con tu asesor, para mas información, o directamente con el coordinador de la facultad</p>
                    <a href="<?= SERVERURL ?>home"> <button type="button" class="retry-button">Volver</button></a>
                </div>

            <?php
            } else {

                 /******************* verifricar si hay un documento subido por los estudiantes****************************** */

                 if (isset($codido_proyecto_estudiante) && !empty($codido_proyecto_estudiante)) {
                    
                    $consulta_ultima_carga = "SELECT * 
                    FROM cargar_documento_proyectos 
                    WHERE codigo_proyecto = '$codido_proyecto_estudiante'
                    ORDER BY id DESC 
                    LIMIT 1";
            
                    $resultado_consulta_ultima_carga = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_ultima_carga);
            
                    // Verificamos si hay resultados
                    if ($resultado_consulta_ultima_carga->rowCount() > 0) {
                        $datos_documento = $resultado_consulta_ultima_carga->fetch(PDO::FETCH_ASSOC);
                        
                        $estado_revision_documento = $datos_documento['estado'];

                        if($estado_revision_documento == 1){

                            $textoboton = " Actualizar archivos enviados";
                            ?>
                              <input type="hidden" name="identificador_carga_documento" value="<?= $ins_loginControlador->encryption(1) ?>">
                            <?php

                        }else if($estado_revision_documento == 2){

                             $textoboton = " Enviar archivos ";

                             ?>
                              <input type="hidden" name="identificador_carga_documento" value="<?= $ins_loginControlador->encryption(2) ?>">
                            <?php
                        }

                    } else {
                        // No hay documentos cargados
                       $textoboton = "Enviar archivos";

                            ?>
                              <input type="hidden" name="identificador_carga_documento" value="<?= $ins_loginControlador->encryption(2) ?>">
                            <?php
                    }
            
              
                    
                }


            ?>
                <!-- Botón para enviar -->
                <div class="form-actions mt-3">
                    <button type="submit"  id="btnEnviarDocumentos" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> <?= $textoboton?></button>
                </div>
        <?php
            }
        }

        ?>

    </form>
<?php


}

if(isset($estado_idea_estudiante)){
    // Ejemplo de condicional PHP
if ($estado_idea_estudiante == "Cancelado") {
    echo '
  <script>
    // Lágrimas dinámicas en toda la página
    window.onload = function() {
        const body = document.body; // Seleccionamos todo el body
        for (let i = 0; i < 20; i++) { // Aumentamos el número de lágrimas
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
} else if ($estado_idea_estudiante == "Aprobado") {
    echo '
    <!-- Partículas animadas -->
    <script>
        function crearConfetti() {
            const alturaMaxima = document.documentElement.scrollHeight;

            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement("div");
                confetti.classList.add("confetti");
                document.body.appendChild(confetti);
                confetti.style.left = Math.random() * 100 + "vw";
                confetti.style.animationDuration = Math.random() * 3 + 2 + "s"; // Duración aleatoria
                confetti.style.animationDelay = Math.random() * 4 + "s";
                confetti.style.backgroundColor = getRandomColor();

                // Configurar animación dinámica
                confetti.style.animationName = `fall-${i}`;

                const style = document.createElement("style");
                style.innerHTML = `
                    @keyframes fall-${i} {
                        0% {
                            transform: translateY(-10px) rotate(0);
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

?>

<script>
    function iniciarCuentaRegresiva(fechaLimite, elementoId, mostrarContador) {
        let elemento = document.getElementById(elementoId);
        let botonEnviar = document.getElementById("btnEnviarDocumentos"); // Referencia al botón

        if (mostrarContador == 2) {
            elemento.innerHTML = "📅 0d 0h 0m 0s";
            return;

        }

        let fechaObjetivo = new Date(fechaLimite.replace("T", " ") + ":00").getTime();

        let intervalo = setInterval(function () {
            let ahora = new Date().getTime();
            let tiempoRestante = fechaObjetivo - ahora;

            if (tiempoRestante <= 0) {
                elemento.innerHTML = "⏳ ¡Tiempo finalizado!";
                elemento.classList.add("poco-tiempo");

                if (botonEnviar) {
                    botonEnviar.style.display = "none"; // Oculta el botón cuando se acabe el tiempo
                }

                clearInterval(intervalo);
                return;
            }

            let dias = Math.floor(tiempoRestante / (1000 * 60 * 60 * 24));
            let horas = Math.floor((tiempoRestante % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutos = Math.floor((tiempoRestante % (1000 * 60 * 60)) / (1000 * 60));
            let segundos = Math.floor((tiempoRestante % (1000 * 60)) / 1000);

            elemento.innerHTML = `📅 ${dias}d ${horas}h ${minutos}m ${segundos}s`;

            if (dias === 0 && horas < 24) {
                elemento.classList.add("poco-tiempo");
            }
        }, 1000);
    }

    document.addEventListener("DOMContentLoaded", function () {
        let fechaSubida = "<?=$fecha_subida?>"; 
        let fechaLimite = "<?=$fecha_limite?>"; 
        let mostrarContador = <?= $mostrarContador ?>; 

        iniciarCuentaRegresiva(fechaLimite, "contador", mostrarContador);
    });
</script>
