<?php

if (isset($_GET['views'])) {
    $ruta = explode(
        "/",
        $_GET['views']
    );

     $codigo = $ruta[1];
}

if ($_SESSION['privilegio'] != 5 &&  $_SESSION['privilegio'] != 1 &&  $_SESSION['privilegio'] != 2 &&  $_SESSION['privilegio'] != 6) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}


$consulta_estado_proyecto = "SELECT * 
    FROM proyectos
    WHERE codigo_proyecto = '$codigo'";

$resultado_estado_proyecto = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_estado_proyecto);

if ($resultado_estado_proyecto->rowCount() > 0) {
    // Extraer los datos de la primera fila (ya que el código es único, suponemos que solo hay un resultado)
    $datos_proyecto = $resultado_estado_proyecto->fetch(PDO::FETCH_ASSOC);

    $estado_proyecto_estudiante = $datos_proyecto['estado'];

}


?>
        <div class="container-mostrar-mensaje-aprovado">
                <?php
                if (isset($estado_proyecto_estudiante)) {

                    if ($estado_proyecto_estudiante == 'Aprobado') {
                ?>
                        <div class="mensaje-container">
                            <h1>🎉 ¡Felicidades! 🎉</h1>
                            <p>El proyecto ya fue aprobado</p>
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
                            <p class="canceled-message">Lo sentimos, el proyecto ha sido cancelado.</p>
                        </div>

                <?php
                    }
                }else{
                    echo 'no se esta recibiendo';
                }
                ?>
        
        </div>
<?php

$consulta_documentos = "SELECT * 
    FROM cargar_documento_proyectos
    WHERE codigo_proyecto = '$codigo'";

$resultado_documentos = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_documentos);


if($resultado_documentos->rowCount() > 0){

    ?>
        <div class="card-container mt-5 mb-5">

        <a href="<?= SERVERURL ?>evidencias-reuniones/2/<?=$codigo ?>" class="card">
                <div class="card-header">Evidencia reuniones</div>
                <div class="card-content">
                    <img src="<?= SERVERURL ?>/Views/assets/images/investigacion.png" alt="Consulta de ideas">
                </div>
        </a>


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
    <a href="<?= SERVERURL ?>ver-documentos-proyectos-asesor/<?= $row['codigo_proyecto']; ?>/<?= $ins_loginControlador->encryption($row['id']) ?>" class="card">
            <div class="card-header"><?= $row['fecha_creacion']; ?> &nbsp; <button type="button" class="btn btn-<?= $color?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $estado; ?> ">
            <i class="fa-solid fa-eye"></i>
            </button></div>
            <div class="card-content">
                <img src="<?= SERVERURL ?>/Views/assets/images/anteproyectos.png" alt="Consulta de ideas">
            </div>
        </a>
    <?php endforeach; }else{
    echo ' <div class="alert alert-danger alertas-ms " role="alert">
    <div class="text-center">No hay documentos registrados </div>
    </div>';
    $codido_idea = false;
    ?>
    </div>
    <?php
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