<?php
if ($_SESSION['privilegio'] != 4) {
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}


if (isset($codido_proyecto_estudiante)) {

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
            $codigo_anteproyecto_estudiante = $row['codigo_anteproyecto'];
        endforeach;
    }
}

?>

<div class="container-mostrar-mensaje-aprovado">
    <?php
    if (isset($codigo_anteproyecto_estudiante)) {

        if ($estado_idea_estudiante == 'Aprobado') {
    ?>
            <div class="mensaje-container">
                <h1>🎉 ¡Felicidades! 🎉</h1>
                <p>Tu anteproyecto ha sido aprobado, un gran paso hacia el éxito. 🌟</p>
                <p>Es momento de continuar con la siguiente etapa y seguir avanzando con determinación para culminar este proyecto. ¡Tienes todo para lograrlo!</p>
                <p>Recuerda que cada esfuerzo te acerca más a tu meta. 🚀 ¡Sigue adelante!</p>
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
            </div>

    <?php
        }
    }
    ?>
</div>

<?php

if(isset( $codigo_anteproyecto_estudiante)){

    echo ' <div class="alert alert-info alertas-ms " role="alert">
    <div class="text-center">Retroalimientaciones pasadas </div>
    </div>';
       
    $consulta_documentos = "SELECT * 
    FROM cargar_documento_anteproyectos
    WHERE codigo_anteproyecto = '$codigo_anteproyecto_estudiante'";

    $resultado_documentos = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_documentos);
    if($resultado_documentos->rowCount() > 0){

        ?>
            <div class="card-container mt-5 mb-5">
        <?php
                            
        foreach ($resultado_documentos->fetchAll(PDO::FETCH_ASSOC) as $row): 
        
            if($row['estado'] ==1){
                $estado = "Pendiente por revisar";
                $color = "danger";
            }else {
                $estado = "Revisado";
                $color = "success";
            }
        ?>
        <a href="<?= SERVERURL ?>ver-documentos-anteproyectos-asesor/<?= $row['codigo_anteproyecto']; ?>/<?= $ins_loginControlador->encryption($row['id']) ?>" class="card">
                <div class="card-header"><?= $row['fecha_creacion']; ?> &nbsp; <button type="button" class="btn btn-<?= $color?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $estado; ?> ">
                <i class="fa-solid fa-eye"></i>
                </button></div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/anteproyectos.png" alt="Consulta de ideas">
                </div>
            </a>
        <?php endforeach; }else{
        echo ' <div class="alert alert-danger alertas-ms " role="alert">
        <div class="text-center">No hay retroalimentaciones registradas para el anteproyecto </div>
        </div>';
        $codido_idea = false;
        ?>
        </div>
        <?php

    }
    
       
}else{
    echo ' <div class="alert alert-danger alertas-ms " role="alert">
    <div class="text-center">No hay retroalimentaciones que mostrar </div>
    </div>';
}














/*******************************Script********************************/


if (isset($estado_idea_estudiante)) {
    // Ejemplo de condicional PHP
    if ($estado_idea_estudiante == "Cancelado") {
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
    } else if ($estado_idea_estudiante == "Aprobado") {
        echo '
    <!-- Partículas animadas -->
    <script>
        function crearConfetti() {
            const alturaMaxima = document.documentElement.scrollHeight;

            for (let i = 0; i < 100; i++) {
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
