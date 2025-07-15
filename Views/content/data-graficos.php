<?php
// Inicializar arreglo para los datos
$data = [
    'labels' => [
        'Usuarios registrados',
        'Anteproyectos registrados',
        'Anteproyectos en revisión',
        'Anteproyectos aprobados',
        'Anteproyectos cancelados',
        'Proyectos registrados',
        'Proyectos cancelados',
        'Proyectos aprobados',
        'Proyectos en revisión'
    ],
    'values' => [] // Aquí se almacenarán los totales de las consultas
];

// Consultas para cada dato
$queries = [
    "SELECT COUNT(*) AS total FROM usuarios",                                      // Total usuarios registrados
    "SELECT COUNT(*) AS total FROM anteproyectos",                                // Total anteproyectos registrados
    "SELECT COUNT(*) AS total FROM anteproyectos WHERE estado = 'Revisión'",      // Total anteproyectos en revisión
    "SELECT COUNT(*) AS total FROM anteproyectos WHERE estado = 'Aprobado'",      // Total anteproyectos aprobados
    "SELECT COUNT(*) AS total FROM anteproyectos WHERE estado = 'Cancelado'",     // Total anteproyectos cancelados
    "SELECT COUNT(*) AS total FROM proyectos",                                    // Total proyectos registrados
    "SELECT COUNT(*) AS total FROM proyectos WHERE estado = 'Cancelado'",         // Total proyectos cancelados
    "SELECT COUNT(*) AS total FROM proyectos WHERE estado = 'Aprobado'",          // Total proyectos aprobados
    "SELECT COUNT(*) AS total FROM proyectos WHERE estado = 'Revisión'"           // Total proyectos en revisión
];

// Ejecutar cada consulta y almacenar los resultados
foreach ($queries as $query) {
    $result = $ins_loginControlador->ejecutar_consultas_simples_two($query);
    $data['values'][] = $result->fetch(PDO::FETCH_ASSOC)['total'];
}

// Convertir los datos a JSON
echo "<script>const chartData = " . json_encode($data) . ";</script>";
?>
