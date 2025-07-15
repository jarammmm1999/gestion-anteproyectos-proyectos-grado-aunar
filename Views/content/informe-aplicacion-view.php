

<?php?>

<div class="container-search-information">
        <a href="<?=SERVERURL?>Views/content/exportar_excel_hojas.php" class="btn btn-success" target="_blank">
            📥 Descargar informe Excel
        </a>
</div>

<?php
/****************************************************************************** */
$consulta_usuarios_registrados  = "SELECT COUNT(*) AS total_usuarios FROM usuarios";
$resultado_usuarios_registrados = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_usuarios_registrados);
?>
<div class="dashboard-wrapper">
    <?php
    if ($resultado_usuarios_registrados->rowCount() > 0) {
        $row_usuarios_registrados = $resultado_usuarios_registrados->fetch(PDO::FETCH_ASSOC);
        $colorAleatorio = generarColorAleatorio();
    ?>
        <div class="info-card two animate__animated animate__fadeIn">
            <div class="container-imagen-informe" >
            <img src="<?= SERVERURL ?>/Views/assets/images/seguidores.png" >
            </div>
            <h3><?= $row_usuarios_registrados['total_usuarios'] ?></h3>
            <p>Usuarios registrados</p>
        </div>
    <?php
    }

    /************************* Total de anteproyectos registrados****************************************/
    $consulta_anteproyectos_registrados = "SELECT COUNT(*) AS total_anteproyectos FROM anteproyectos";
    $resultado_anteproyectos_registrados = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_anteproyectos_registrados);

    if ($resultado_anteproyectos_registrados->rowCount() > 0) {
        $total_anteproyectos = $resultado_anteproyectos_registrados->fetch(PDO::FETCH_ASSOC)['total_anteproyectos'];
        $colorAleatorio = generarColorAleatorio();
        ?>
        <div class="info-card two animate__animated animate__fadeIn">
            <div class="container-imagen-informe" >
            <img src="<?= SERVERURL ?>/Views/assets/images/idea.png" >
            </div>
            <h3><?= $total_anteproyectos ?></h3>
            <p>Total de anteproyectos registrados</p>
        </div>
    <?php
    
    }

     /************************* Total de anteproyectos en revisión****************************************/
     $consulta_anteproyectos_revision = "SELECT COUNT(*) AS total_en_revision FROM anteproyectos WHERE estado = 'Revisión'";
     $resultado_anteproyectos_revision = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_anteproyectos_revision);
     
     if ($resultado_anteproyectos_revision->rowCount() > 0) {
         $total_en_revision = $resultado_anteproyectos_revision->fetch(PDO::FETCH_ASSOC)['total_en_revision'];
         $colorAleatorio = generarColorAleatorio();
         ?>
         <div class="info-card two animate__animated animate__fadeIn">
             <div class="container-imagen-informe" >
             <img src="<?= SERVERURL ?>/Views/assets/images/revision.png" >
             </div>
             <h3><?= $total_en_revision ?></h3>
             <p>Total de anteproyectos en revisión</p>
         </div>
        <?php
     }
     
      /************************* Total de anteproyectos aprobados****************************************/

        $consulta_proyectos_aprobados = "SELECT COUNT(*) AS total_aprobados FROM anteproyectos WHERE estado = 'Aprobado'";
        $resultado_proyectos_aprobados = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_proyectos_aprobados);

        if ($resultado_proyectos_aprobados->rowCount() > 0) {
            $total_aprobados = $resultado_proyectos_aprobados->fetch(PDO::FETCH_ASSOC)['total_aprobados'];
            $colorAleatorio = generarColorAleatorio();
            ?>
            <div class="info-card two animate__animated animate__fadeIn">
                <div class="container-imagen-informe">
                <img src="<?= SERVERURL ?>/Views/assets/images/aprobacion.png" >
                </div>
                <h3><?= $total_aprobados ?></h3>
                <p>Total de anteproyectos aprobados</p>
            </div>
            <?php
        }

         /************************* Total de anteproyectos cancelados****************************************/

        $consulta_proyectos_cancelados = "SELECT COUNT(*) AS total_cancelados FROM proyectos WHERE estado = 'Cancelado'";
        $resultado_proyectos_cancelados = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_proyectos_cancelados);

        if ($resultado_proyectos_cancelados->rowCount() > 0) {
            $total_cancelados = $resultado_proyectos_cancelados->fetch(PDO::FETCH_ASSOC)['total_cancelados'];
            $colorAleatorio = generarColorAleatorio();
            ?>
            <div class="info-card two animate__animated animate__fadeIn">
                <div class="container-imagen-informe" >
                <img src="<?= SERVERURL ?>/Views/assets/images/rechazar.png" >
                </div>
                <h3><?= $total_cancelados ?></h3>
                <p>Total de anteproyectos cancelados</p>
            </div>
            <?php
        }

        /************************* Total de proyectos registrados****************************************/

        $consulta_proyectos_registrados = "SELECT COUNT(*) AS total_proyectos FROM proyectos";
        $resultado_proyectos_registrados = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_proyectos_registrados);

        if ($resultado_proyectos_registrados->rowCount() > 0) {
            $total_proyectos = $resultado_proyectos_registrados->fetch(PDO::FETCH_ASSOC)['total_proyectos'];
            $colorAleatorio = generarColorAleatorio();
            ?>
            <div class="info-card two animate__animated animate__fadeIn">
                <div class="container-imagen-informe" >
                <img src="<?= SERVERURL ?>/Views/assets/images/proyecto.png" >
                </div>
                <h3><?= $total_proyectos ?></h3>
                <p>Total de proyectos registrados</p>
            </div>
            <?php
        }

        /************************* Total de proyectos revisión****************************************/
        $consulta_proyectos_revision = "SELECT COUNT(*) AS total_en_revision FROM proyectos WHERE estado = 'Revisión'";
        $resultado_proyectos_revision = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_proyectos_revision);
        
        if ($resultado_proyectos_revision->rowCount() > 0) {
            $total_en_revision = $resultado_proyectos_revision->fetch(PDO::FETCH_ASSOC)['total_en_revision'];
            $colorAleatorio = generarColorAleatorio();
            ?>
            <div class="info-card two animate__animated animate__fadeIn">
                <div class="container-imagen-informe" >
                <img src="<?= SERVERURL ?>/Views/assets/images/revisionproyecto.png" >
                </div>
                <h3><?= $total_en_revision ?></h3>
                <p>Total de proyectos en revisión</p>
            </div>
            <?php
        }

         /************************* Total de proyectos aprobados****************************************/
         $consulta_proyectos_aprobados = "SELECT COUNT(*) AS total_aprobados FROM proyectos WHERE estado = 'Aprobado'";
         $resultado_proyectos_aprobados = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_proyectos_aprobados);
         
         if ($resultado_proyectos_aprobados->rowCount() > 0) {
             $total_aprobados = $resultado_proyectos_aprobados->fetch(PDO::FETCH_ASSOC)['total_aprobados'];
             $colorAleatorio = generarColorAleatorio();
            ?>
            <div class="info-card two animate__animated animate__fadeIn">
                <div class="container-imagen-informe" >
                <img src="<?= SERVERURL ?>/Views/assets/images/aprobado.png" >
                </div>
                <h3><?= $total_aprobados ?></h3>
                <p>Total de proyectos aprobados</p>
            </div>
            <?php
         }
         
         /************************* Total de proyectos cancelados****************************************/
        $consulta_proyectos_cancelados = "SELECT COUNT(*) AS total_cancelados FROM proyectos WHERE estado = 'Cancelado'";
        $resultado_proyectos_cancelados = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_proyectos_cancelados);

        if ($resultado_proyectos_cancelados->rowCount() > 0) {
            $total_cancelados = $resultado_proyectos_cancelados->fetch(PDO::FETCH_ASSOC)['total_cancelados'];
            $colorAleatorio = generarColorAleatorio();
            ?>
            <div class="info-card two animate__animated animate__fadeIn">
                <div class="container-imagen-informe" >
                <img src="<?= SERVERURL ?>/Views/assets/images/cancelado.png" >
                </div>
                <h3><?= $total_cancelados ?></h3>
                <p>Total de proyectos cancelados</p>
            </div>
            <?php
        }

         /************************* Total de proyectos Calificados jurados****************************************/
         $consulta_proyectos_cancelados = "SELECT COUNT(*) AS total_calificados FROM evaluaciones_proyectos ";
         $resultado_proyectos_cancelados = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_proyectos_cancelados);
 
         if ($resultado_proyectos_cancelados->rowCount() > 0) {
             $total_calificados = $resultado_proyectos_cancelados->fetch(PDO::FETCH_ASSOC)['total_calificados'];
             $colorAleatorio = generarColorAleatorio();
             ?>
             <div class="info-card two animate__animated animate__fadeIn">
                 <div class="container-imagen-informe" >
                 <img src="<?= SERVERURL ?>/Views/assets/images/jurado.png" >
                 </div>
                 <h3><?= $total_calificados ?></h3>
                 <p>Total de proyectos calificados jurados</p>
             </div>
             <?php
         }


    ?>

    <?php
    
  

    // Generar un color aleatorio en formato hexadecimal
    function generarColorAleatorio()
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    $consulta = "SELECT 
                f.id_facultad, 
                f.nombre_facultad,
                p.id_programa, 
                p.nombre_programa,
                (SELECT COUNT(DISTINCT auf.numero_documento) 
                 FROM Asignar_usuario_facultades auf 
                 WHERE auf.id_programa = p.id_programa) AS total_usuarios
            FROM facultades f
            LEFT JOIN programas_academicos p ON f.id_facultad = p.id_facultad
            ORDER BY f.nombre_facultad, p.nombre_programa";

    $resultado = $ins_loginControlador->ejecutar_consultas_simples_two($consulta);
    $datos = $resultado->fetchAll(PDO::FETCH_ASSOC);
       
     // Organizar datos en un array estructurado para evitar múltiples consultas
    $facultades = [];
    
    foreach ($datos as $row) {
         $id_facultad = $row['id_facultad'];
    
        if (!isset($facultades[$id_facultad])) {
            $facultades[$id_facultad] = [
                "nombre_facultad" => $row['nombre_facultad'],
                "programas" => []
            ];
        }
    
        if (!empty($row['id_programa'])) { // Evita programas nulos en facultades sin programas
            $facultades[$id_facultad]['programas'][] = [
                "id_programa" => $row['id_programa'],
                "nombre_programa" => $row['nombre_programa'],
                "total_usuarios" => $row['total_usuarios']
            ];
        }
    }
    
    ?>
    <!-- Mostrar las facultades y programas en HTML -->
        <?php foreach ($facultades as $id_facultad => $info): ?>
            <?php $colorAleatorio = generarColorAleatorio(); ?>
            <div class="info-card animate__animated animate__fadeIn">
                <div class="container-imagen-informe" style="background-color: <?= htmlspecialchars($colorAleatorio) ?>;">
                    <i class="fa-solid fa-university icono-informes"></i>
                </div>
                <h3><?= htmlspecialchars($info['nombre_facultad']) ?></h3>
                <ul>
                    <?php if (!empty($info['programas'])): ?>
                        <?php foreach ($info['programas'] as $programa): ?>
                            <li>
                                <?= htmlspecialchars($programa['nombre_programa']) ?>
                                <span>(<?= htmlspecialchars($programa['total_usuarios']) ?> usuarios)</span>
                                <i class="fa-solid fa-eye ver-usuarios-programas" onclick="mostrarDetallesUsuarios('<?= SERVERURL ?>','<?= $programa['id_programa'] ?>')"></i>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No hay programas registrados</li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endforeach; ?>
            
    
    <?php

?>


</div>

 <?php include "data-graficos.php";  ?>

 <?php include "data-graficos-facultades.php";  ?>

<!-- Nuevo Modal para mostrar detalles de los estudiantes -->
<div id="detallesUsuariosModal" class="custom-modal">
    <div class="custom-modal-content">
        <span class="custom-close" onclick="cerrarDetallesModal()">&times;</span>
        <h3>Detalles de los Usuarios</h3>
        <div id="detallesUsuarios"></div>
    </div>
</div>


<div id="charts-container">
        <!-- Gráfico de barras -->
        <div class="chart-box">
            <canvas id="barChart"></canvas>
        </div>

        <!-- Gráfico de líneas -->
        <div class="chart-box">
            <canvas id="lineChart"></canvas>
        </div>
</div>
<div id="facultades-charts" ></div>


<!-- Botón flotante 
 <div class="fab-container">
        <button class="fab-button" onclick="toggleMenuinforme()">+</button>
        <div class="fab-menu">
            <button onclick="downloadPDF('<?=SERVERURL?>')">
                <span>PDF</span>
                📄
            </button>
            <button onclick="downloadExcel()">
                <span>Excel</span>
                📊
            </button>
        </div>
    </div>-->

<script>

console.log(window.jspdf); // Esto debería mostrar el objeto jsPDF en la consola
        
const barCtx = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Totales',
                    data: chartData.values,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(199, 199, 199, 0.2)',
                        'rgba(83, 102, 255, 0.2)',
                        'rgba(144, 238, 144, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)',
                        'rgba(83, 102, 255, 1)',
                        'rgba(144, 238, 144, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: false, // Desactivar redimensionamiento automático
                maintainAspectRatio: false, // Para asegurar que los gráficos mantengan la proporción
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const lineCtx = document.getElementById('lineChart').getContext('2d');
        const lineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Tendencia',
                    data: chartData.values,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: false, // Desactivar redimensionamiento automático
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });



        function generarColoresAleatorios(cantidad) {
            const colores = [];
            for (let i = 0; i < cantidad; i++) {
                const color = `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.6)`;
                colores.push(color);
            }
            return colores;
        }

        // Crear gráficos dinámicos para cada facultad
        if (typeof chartDataFacultades !== 'undefined' && chartDataFacultades.facultades) {
            console.log('chartDataFacultades:', chartDataFacultades);

            chartDataFacultades.facultades.forEach((facultad, index) => {
                // Crear colores dinámicos para cada programa dentro de la facultad
                const colores = generarColoresAleatorios(chartDataFacultades.programas[index].length);

                // Crear un contenedor para cada gráfico
                const container = document.createElement('div');
                container.classList.add('chart-box');
                container.innerHTML = `
                    <h4 class="text-center">${facultad}</h4>
                    <canvas id="facultad-chart-${index}"></canvas>
                `;
                document.getElementById('facultades-charts').appendChild(container);

                // Crear el gráfico de barras
                const ctx = document.getElementById(`facultad-chart-${index}`).getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartDataFacultades.programas[index], // Programas de la facultad
                        datasets: [{
                            label: 'Total de usuarios',
                            data: chartDataFacultades.usuarios[index], // Totales de usuarios por programa
                            backgroundColor: colores, // Colores dinámicos
                            borderColor: colores.map(color => color.replace('0.6', '1')), // Bordes más opacos
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        } else {
            console.error('chartDataFacultades no está definido o no contiene datos.');
        }


function toggleMenuinforme() {
    const menu = document.querySelector('.fab-menu');
    menu.classList.toggle('active');
}



/*****************************pdf *********************************** */

async function downloadPDF(url) {
    // Crear un enlace temporal para descargar el PDF
    const link = document.createElement('a');
    link.href = url + 'Views/content/generar_pdf.php'; // Ruta al archivo PHP
    link.target = '_blank'; // Abrir en una nueva pestaña (opcional)
    link.click();
}





</script>
