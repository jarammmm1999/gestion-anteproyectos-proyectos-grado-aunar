<?php
require_once('../../vendor/tecnickcom/tcpdf/tcpdf.php');

$peticionAjax = true;
require_once "../../Model/Consultas.php";

$ins_MainModelo = new Consultas();

// Extender la clase TCPDF para personalizar los encabezados y pies de página
class CustomTCPDF extends TCPDF {

    public function Header() {
        $this->SetY(10); // Posición desde la parte superior
        $this->SetFont('helvetica', '', 10);

        // HTML del encabezado
        $headerHTML = '
        <table border="1" cellpadding="4" style="margin-bootom: 20px;">
            <tr>
                <td rowspan="3" style="width: 30%; text-align: center;">
                    <img src="' . SERVERURL . '/Views/assets/images/logo-autonoma.jpg" width="100">
                </td>
                <td style="width: 40%; text-align: center; font-weight: bold;">
                    SISTEMA DE GESTIÓN DE LA CALIDAD
                </td>
                <td style="width: 30%; font-size: 10px;">
                    <span style="color: red; font-weight: bold;">Código: FR-DA-GDE-0026</span><br>

                </td>
            </tr>
            <tr>
                <td style="text-align: center; font-weight: bold;">
                 INFORME GENERAL GESTIÓN DE ANTEPROYECTO Y PROYECTOS DE GRADOS 
                </td>
                <td style="font-size: 10px;">
                    <strong>Fecha:</strong> ' . date('d/m/Y') . '
                </td>
            </tr>
            <tr>
                <td style="text-align: center; font-weight: bold;">Extensión Villavicencio</td>
                <td style="font-size: 10px;">
                    <strong>Página:</strong> ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages() . '
                </td>
            </tr>
        </table>';

        $this->writeHTML($headerHTML, true, false, false, false, '');
    }
    // Personalizar el pie de página
    public function Footer() {
        $this->SetY(-30);
        $this->SetFont('helvetica', 'I', 10);
        $footer = '
            <div style="text-align: center; color: #555; ">
                <p>Universidad Autónoma de Nariño | Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages().'</p>
            </div>
        ';
        $this->writeHTML($footer, true, false, true, false, '');
    }
}

// Crear una nueva instancia del PDF
$pdf = new CustomTCPDF();

// Configuración del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Universidad Autónoma de Nariño');
$pdf->SetTitle('Reporte Académico');
$pdf->SetSubject('Informe de Estudiantes');
$pdf->SetKeywords(', universidad, informe');


// Configurar márgenes para mayor espaciado entre header, contenido y footer
$pdf->SetMargins(15, 40, 15);
$pdf->SetAutoPageBreak(TRUE, 30);

// **PRIMERA PÁGINA: PRESENTACIÓN DEL REPORTE**
$pdf->AddPage('P', array(300, 300));

$bootstrapCSS = '
<style>
    /* Fuente general */
    body {
        font-family: "Arial", sans-serif;
    }

    /* Contenedor principal */
    .container {
        width: 100%;
        text-align: center;
        padding: 20px;
    }

    /* Logo centrado */
    .contenedor_imagen_pdf {
        text-align: center;
        margin-bottom: 10px;
    }

    .contenedor_imagen_pdf img {
        width: 200px; /* Ajusta el tamaño del logo */
        height: auto;
    }


    /* Subtítulo */
    .subtitle {
        font-size: 14px;
        color: #555;
        font-style: italic;
        margin-top: 3px;
    }

    /* Contenido justificado */
    .content {
        font-size: 12px;
        text-align: justify;
        line-height: 1.5;
    }

    /* Fecha alineada a la derecha */
    .fecha {
        text-align: right;
        font-size: 10px;
        margin-top: 10px;
        font-weight: bold;
        color: #333;
    }
</style>
';
// Contenido de la primera página con el nuevo diseño
$htmlFirstPage = $bootstrapCSS . '
<div class="container">

   <div class="content">
    <p>Este informe tiene como propósito proporcionar un análisis detallado y estructurado sobre el funcionamiento y el impacto de la aplicación de gestión y seguimiento de anteproyectos y proyectos de grado en la Universidad Autónoma de Nariño. A través de una recopilación precisa de datos, se busca ofrecer una visión integral del uso de la plataforma, destacando su contribución a la optimización de los procesos académicos y administrativos relacionados con la gestión de trabajos de grado.</p>

    <p>El documento está diseñado como una herramienta clave para la toma de decisiones estratégicas, brindando información detallada sobre los usuarios registrados, los proyectos y anteproyectos gestionados, así como las tendencias y métricas relevantes que permiten evaluar el desempeño y la efectividad del sistema. Se presentan datos organizados de manera clara y estructurada, facilitando el acceso a estadísticas sobre el uso de la plataforma, la participación de docentes y estudiantes, y la evolución de los proyectos a lo largo del tiempo.</p>

    <p>Además, el informe permite identificar fortalezas y áreas de mejora en la implementación y el uso de la aplicación, promoviendo la adopción de estrategias innovadoras que optimicen la gestión académica y faciliten el seguimiento continuo de cada etapa del desarrollo de los anteproyectos y proyectos de grado. La información presentada servirá para evaluar el impacto del sistema en la agilización de trámites, la transparencia en los procesos y la eficiencia en la supervisión de los trabajos académicos.</p>

    <p>A lo largo del documento, los lectores encontrarán un análisis detallado sobre la evolución de la plataforma, incluyendo el crecimiento en el número de usuarios activos, la distribución de proyectos por facultades y programas, y los logros alcanzados desde su implementación. También se incluyen estadísticas clave, indicadores de desempeño, y comparaciones con sistemas tradicionales de gestión, permitiendo contextualizar los avances logrados y los desafíos pendientes en la mejora continua del sistema.</p>

    <p>Finalmente, este informe busca proporcionar una base sólida para futuras actualizaciones y mejoras de la aplicación, garantizando su alineación con las necesidades académicas y administrativas de la universidad. Con ello, se pretende fortalecer la calidad educativa, optimizar la organización institucional y facilitar el acceso a información relevante para docentes, estudiantes y directivos, fomentando una cultura de innovación y excelencia en la gestión académica.</p>
</div>

    <p class="fecha">Fecha: ' . date('d/m/Y') . '</p>
</div>
';


// Escribir HTML en la primera página
$pdf->writeHTML($htmlFirstPage, true, false, true, false, '');

// **AGREGAR PÁGINA**
$pdf->AddPage('P', array(300, 300));

// **Configurar márgenes y paginación automática**
$pdf->SetMargins(15, 40, 15);
$pdf->SetAutoPageBreak(TRUE, 30);

// Conexión y ejecución de consultas
$estadisticas = [
    "Usuarios Registrados" => "SELECT COUNT(*) AS total FROM usuarios",
    "Anteproyectos Registrados" => "SELECT COUNT(*) AS total FROM anteproyectos",
    "Anteproyectos en Revisión" => "SELECT COUNT(*) AS total FROM anteproyectos WHERE estado = 'Revisión'",
    "Anteproyectos Aprobados" => "SELECT COUNT(*) AS total FROM proyectos WHERE estado = 'Aprobado'",
    "Anteproyectos Cancelados" => "SELECT COUNT(*) AS total FROM proyectos WHERE estado = 'Cancelado'",
    "Proyectos Registrados" => "SELECT COUNT(*) AS total FROM proyectos",
    "Proyectos en Revisión" => "SELECT COUNT(*) AS total FROM proyectos WHERE estado = 'Revisión'",
    "Proyectos Aprobados" => "SELECT COUNT(*) AS total FROM proyectos WHERE estado = 'Aprobado'",
    "Proyectos Cancelados" => "SELECT COUNT(*) AS total FROM proyectos WHERE estado = 'Cancelado'",
    "Proyectos Calificados por Jurados" => "SELECT COUNT(*) AS total FROM proyectos WHERE estado = 'Calificado'"
];

// Array para almacenar resultados
$resultados = [];

foreach ($estadisticas as $titulo => $consulta) {
    $resultado = $ins_MainModelo->ejecutar_consultas_simples_two($consulta);
    $resultados[$titulo] = ($resultado->rowCount() > 0) ? $resultado->fetch(PDO::FETCH_ASSOC)['total'] : 0;
}

// **ESTILOS PARA LA TABLA**
$tableStyles = '
<style>
    table { width: 100%; border-collapse: collapse; }
    th {
        background-color: #034873;
        color: white;
        font-size: 9px;
        font-weight: bold;
        padding: 10px;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: 1px solid #000;
        height: 30px;
        line-height: 25px; /* Ajusta la altura del texto dentro de th */
    }
    td {
        border: 1px solid #ccc;
        padding: 8px;
        font-size: 9px;
        text-align: center;
        color: #333;
        font-weight: normal;
        line-height: 18px; /* Ajusta la altura del texto dentro de td */
        height: 25px;
        line-height: 25px;
    }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .content {
        font-size: 12px;
        text-align: justify;
        line-height: 1.5;
    }
    .content h2{text-align: center;}
</style>
';

// **ENCABEZADO DE LA TABLA**
$htmlTable = '
<div class="content">
    <h2>Resumen General del Sistema de Gestión de Anteproyectos y Proyectos</h2>
    <p>En este informe se presentan datos actualizados sobre el número de usuarios registrados, la cantidad de anteproyectos y proyectos en sus distintas fases, así como el estado actual de los trabajos de grado. Esta información es fundamental para evaluar el impacto de la plataforma en la mejora de la gestión académica y administrativa.</p>

    <p>A continuación, se muestra un desglose detallado de la actividad registrada en la plataforma:</p>
</div>


<table border="1">
    <thead>
        <tr>
            <th>Categoría</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>';

// **CONSTRUIR TABLA CON DATOS**
foreach ($resultados as $categoria => $total) {
    $htmlTable .= '<tr>
        <td>' . htmlspecialchars($categoria) . '</td>
        <td>' . htmlspecialchars($total) . '</td>
    </tr>';
}

// Cerrar la tabla
$htmlTable .= '</tbody></table>';

// **ESCRIBIR LA TABLA EN EL PDF**
$pdf->writeHTML($tableStyles . $htmlTable, true, false, true, false, '');



// **AGREGAR PÁGINA**
$pdf->AddPage('P', array(300, 300));

// **CONFIGURACIÓN DEL PDF**
$pdf->SetMargins(15, 30, 15); // Aumentar margen superior para dar más espacio entre el header y el contenido
$pdf->SetAutoPageBreak(TRUE, 30);

// **SALTO DE LÍNEA PARA SEPARAR EL ENCABEZADO DE LA TABLA**
$pdf->Ln(12); // Añadir 15px de espacio antes de la tabla


// **FUNCIÓN PARA VERIFICAR EL SALTO DE PÁGINA Y AÑADIR EL ENCABEZADO**
function CheckPageBreak2($pdf, $tableHeader, $tableStyles) {
    if ($pdf->GetY() > 260) { // Si está cerca del final de la página
        $pdf->AddPage();
        $pdf->Ln(15); // Agregar espacio en la nueva página
        $pdf->writeHTML($tableStyles . $tableHeader, true, false, true, false, '');
    }
}

// **CONSULTA A LA BASE DE DATOS**
$consulta_usuarios_registrados = "SELECT 
    u.numero_documento, u.nombre_usuario, u.apellidos_usuario, u.correo_usuario, u.telefono_usuario, 
    r.nombre_rol  
FROM usuarios u
INNER JOIN roles_usuarios r ON u.id_rol = r.id_rol";

$resultado_usuarios_registrados = $ins_MainModelo->ejecutar_consultas_simples_two($consulta_usuarios_registrados);

// **ESTILOS Y ENCABEZADO DE LA TABLA**
$tableStyles = '
<style>
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th {
        background-color: #034873;
        color: white;
        font-size: 9px;
        font-weight: bold;
        padding: 10px;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: 1px solid #000;
        height: 30px;
        line-height: 25px; /* Ajusta la altura del texto dentro de th */
    }
    td {
        border: 1px solid #ccc;
        padding: 8px;
        font-size: 9px;
        text-align: center;
        color: #333;
        font-weight: normal;
        line-height: 18px; /* Ajusta la altura del texto dentro de td */
        height: 25px;
        line-height: 20px;
    }
    .content {
        font-size: 12px;
        text-align: justify;
        line-height: 1.5;
    }
    tr:nth-child(even) { background-color: #f2f2f2; }
     .content h2{text-align: center;}
</style>
';

$tableHeader = '<table border="1">
    <thead>
        <tr>
            <th style="width: 20%;">Documento</th>
            <th style="width: 25%;">Nombre</th>
            <th style="width: 30%;">Correo</th>
            <th style="width: 15%;">Teléfono</th>
            <th style="width: 10%;">Rol</th>
        </tr>
    </thead>
    <tbody>';

$htmlTable = $tableHeader;

// **CONSTRUIR TABLA CON DATOS**
if ($resultado_usuarios_registrados->rowCount() > 0) {
    $usuarios = $resultado_usuarios_registrados->fetchAll(PDO::FETCH_ASSOC);

    foreach ($usuarios as $usuario) {
        // **VERIFICAR SI HAY ESPACIO SUFICIENTE PARA UNA FILA**
        CheckPageBreak2($pdf, $tableHeader, $tableStyles);

        $htmlTable .= '<tr>
            <td style="width: 20%;">' . htmlspecialchars($usuario['numero_documento']) . '</td>
            <td style="width: 25%;">' . htmlspecialchars($usuario['nombre_usuario'] . ' ' . $usuario['apellidos_usuario']) . '</td>
            <td style="width: 30%;">' . htmlspecialchars($usuario['correo_usuario']) . '</td>
            <td style="width: 15%;">' . htmlspecialchars($usuario['telefono_usuario']) . '</td>
            <td style="width: 10%;">' . htmlspecialchars($usuario['nombre_rol']) . '</td>
        </tr>';
    }
} else {
    $htmlTable .= '<tr><td colspan="5" style="text-align: center;">No se encontraron registros.</td></tr>';
}

$htmlTable .= '</tbody></table>';

// **ESCRIBIR TABLA EN EL PDF**
$pdf->writeHTML($tableStyles . $htmlTable, true, false, true, false, '');


// **FUNCIÓN PARA VERIFICAR ESPACIO ANTES DE AGREGAR UNA FILA**
function checkPageBreak($pdf, $alturaFila) {
    $margenInferior = 40; // Espacio mínimo que debe quedar
    $paginaAltura = 350; // Altura total de la página
    $posicionActual = $pdf->GetY(); // Posición actual en la página

    // Si la fila no cabe en la página, se agrega una nueva antes de escribir la fila
    if (($posicionActual + $alturaFila + $margenInferior) > $paginaAltura) {
        $pdf->AddPage();
        $pdf->Ln(10); // Espacio antes de continuar
    }
}

// **AGREGAR PÁGINA PRINCIPAL**
$pdf->AddPage('P', array(300, 300));
$pdf->SetMargins(15, 50, 15);
$pdf->SetAutoPageBreak(TRUE, 40);
$pdf->Ln(30);

// **ESTILOS PARA TABLA**
$anteproyectosStyles = '
<style>
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th {
        background-color: #034873;
        color: white;
        font-size: 9px;
        font-weight: bold;
        padding: 10px;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: 1px solid #000;
        height: 30px;
        line-height: 25px; /* Ajusta la altura del texto dentro de th */
    }
    td {
        border: 1px solid #ccc;
        padding: 8px;
        font-size: 9px;
        text-align: center;
        color: #333;
        font-weight: normal;
        line-height: 18px; /* Ajusta la altura del texto dentro de td */
        height: 25px;
        line-height: 20px;
    }
    .content {
        font-size: 12px;
        text-align: justify;
        line-height: 1.5;
    }
    tr:nth-child(even) { background-color: #f2f2f2; }
     .content h2{text-align: center;}
</style>
';

// **ENCABEZADO DE LA TABLA**
$htmlAnteproyectos = '
<div class="content">
    <h2>Registro de Anteproyectos</h2>
    <p>Cada anteproyecto registrado contiene información clave, como su código identificador, título, palabras clave que resumen su enfoque, estado actual dentro del proceso de evaluación, el programa académico al que pertenece y la facultad responsable. Estos datos permiten un seguimiento preciso y organizado del avance de cada propuesta, facilitando la supervisión y gestión de los trabajos de grado.</p>

</div>


<table border="1">
    <thead>
        <tr>
            <th style="width: 12%;">Código</th>
            <th style="width: 28%;">Título</th>
            <th style="width: 20%;">Palabras Claves</th>
            <th style="width: 10%;">Estado</th>
            <th style="width: 15%;">Programa</th>
            <th style="width: 15%;">Facultad</th>
        </tr>
    </thead>
    <tbody>';

// **CONSULTA DE ANTEPROYECTOS**
$consulta_anteproyectos = "SELECT 
    a.codigo_anteproyecto,
    a.titulo_anteproyecto,
    a.palabras_claves,
    a.estado,
    p.nombre_programa,
    f.nombre_facultad
FROM anteproyectos a
INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
INNER JOIN facultades f ON p.id_facultad = f.id_facultad";

$resultado_anteproyectos = $ins_MainModelo->ejecutar_consultas_simples_two($consulta_anteproyectos);

// **VERIFICAR SI HAY DATOS**
if ($resultado_anteproyectos->rowCount() > 0) {
    $anteproyectos = $resultado_anteproyectos->fetchAll(PDO::FETCH_ASSOC);

    foreach ($anteproyectos as $anteproyecto) {
        $alturaFila = 20; // Estimación de la altura de cada fila

        // **VERIFICAR SI HAY ESPACIO ANTES DE ESCRIBIR UNA FILA**
        checkPageBreak($pdf, $alturaFila);

        // **CONSTRUIR FILA**
        $htmlAnteproyectos .= '<tr>
            <td style="width: 12%;">' . htmlspecialchars($anteproyecto['codigo_anteproyecto']) . '</td>
            <td style="width: 28%;">' . htmlspecialchars($anteproyecto['titulo_anteproyecto']) . '</td>
            <td style="width: 20%;">' . htmlspecialchars($anteproyecto['palabras_claves']) . '</td>
            <td style="width: 10%;">' . htmlspecialchars($anteproyecto['estado']) . '</td>
            <td style="width: 15%;">' . htmlspecialchars($anteproyecto['nombre_programa']) . '</td>
            <td style="width: 15%;">' . htmlspecialchars($anteproyecto['nombre_facultad']) . '</td>
        </tr>';
    }
} else {
    $htmlAnteproyectos .= '<tr><td colspan="6" style="text-align: center;">No se encontraron anteproyectos.</td></tr>';
}

// **CERRAR TABLA**
$htmlAnteproyectos .= '</tbody></table>';

// **IMPRIMIR EN PDF**
$pdf->writeHTML($anteproyectosStyles . $htmlAnteproyectos, true, false, true, false, '');


// **CONSULTA DE PROYECTOS**
$consulta_proyectos = "SELECT 
    a.codigo_proyecto,
    a.titulo_proyecto,
    a.palabras_claves,
    p.nombre_programa,
    f.nombre_facultad
FROM proyectos a
INNER JOIN programas_academicos p ON a.id_programa = p.id_programa
INNER JOIN facultades f ON p.id_facultad = f.id_facultad";

$resultado_proyectos = $ins_MainModelo->ejecutar_consultas_simples_two($consulta_proyectos);

// **ESTILOS PARA TABLA DE PROYECTOS**
$proyectosStyles = '
<style>
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th {
        background-color: #034873;
        color: white;
        font-size: 9px;
        font-weight: bold;
        padding: 10px;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: 1px solid #000;
        height: 30px;
        line-height: 25px; /* Ajusta la altura del texto dentro de th */
    }
    td {
        border: 1px solid #ccc;
        padding: 8px;
        font-size: 9px;
        text-align: center;
        color: #333;
        font-weight: normal;
        line-height: 18px; /* Ajusta la altura del texto dentro de td */
        height: 25px;
        line-height: 20px;
    }
    .content {
        font-size: 12px;
        text-align: justify;
        line-height: 1.5;
    }
    tr:nth-child(even) { background-color: #f2f2f2; }
     .content h2{text-align: center;}
</style>
';

// **ENCABEZADO DE LA TABLA DE PROYECTOS**
$htmlProyectos = '
<div class="content">
    <h2>Registro de proyectos</h2>
    <p>Cada proyecto registrado contiene información clave, como su código identificador, título, palabras clave que resumen su enfoque, estado actual dentro del proceso de evaluación, el programa académico al que pertenece y la facultad responsable. Estos datos permiten un seguimiento preciso y organizado del avance de cada propuesta, facilitando la supervisión y gestión de los trabajos de grado.</p>

</div>
<table border="1">
    <thead>
        <tr>
            <th style="width: 15%;">Código</th>
            <th style="width: 30%;">Título</th>
            <th style="width: 25%;">Palabras Claves</th>
            <th style="width: 15%;">Programa</th>
            <th style="width: 15%;">Facultad</th>
        </tr>
    </thead>
    <tbody>';

// **CONSTRUIR FILAS DE PROYECTOS**
if ($resultado_proyectos->rowCount() > 0) {
    $proyectos = $resultado_proyectos->fetchAll(PDO::FETCH_ASSOC);

    foreach ($proyectos as $proyecto) {
        // Verificar si hay espacio suficiente antes de agregar una nueva fila
        if ($pdf->GetY() > 260) {
            $pdf->AddPage();
            $pdf->writeHTML($proyectosStyles . $htmlProyectos, true, false, true, false, '');
        }

        $htmlProyectos .= '<tr>
            <td style="width: 15%;">' . htmlspecialchars($proyecto['codigo_proyecto']) . '</td>
            <td style="width: 30%;">' . htmlspecialchars($proyecto['titulo_proyecto']) . '</td>
            <td style="width: 25%;">' . htmlspecialchars($proyecto['palabras_claves']) . '</td>
            <td style="width: 15%;">' . htmlspecialchars($proyecto['nombre_programa']) . '</td>
            <td style="width: 15%;">' . htmlspecialchars($proyecto['nombre_facultad']) . '</td>
        </tr>';
    }
} else {
    $htmlProyectos .= '<tr><td colspan="5" style="text-align: center;">No se encontraron proyectos.</td></tr>';
}

$htmlProyectos .= '</tbody></table>';

// **ESCRIBIR TABLA DE PROYECTOS EN EL PDF**
$pdf->writeHTML($proyectosStyles . $htmlProyectos, true, false, true, false, '');














ob_end_clean();
// **SALIDA DEL PDF**
$pdf->Output('informe_estudiantes.pdf', 'I');


?>
