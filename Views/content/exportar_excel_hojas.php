<?php

require_once "../../vendor/SimpleXLSXGen.php";

$peticionAjax = true;

require_once "../../Model/Consultas.php";

$ins_MainModelo = new Consultas();


/***** exportar información de usuario *****/
$consulta_usuarios_registrados = "SELECT 
    u.numero_documento, 
    u.nombre_usuario, 
    u.apellidos_usuario, 
    u.correo_usuario, 
    u.telefono_usuario, 
    r.nombre_rol,
    u.created_at, 
    COALESCE(GROUP_CONCAT(DISTINCT f.nombre_facultad SEPARATOR ', '), 'Sin asignar') AS facultades_asignadas,
    COALESCE(GROUP_CONCAT(DISTINCT p.nombre_programa SEPARATOR ', '), 'Sin asignar') AS programas_asignados
FROM usuarios u
INNER JOIN roles_usuarios r ON u.id_rol = r.id_rol
LEFT JOIN Asignar_usuario_facultades auf ON u.numero_documento = auf.numero_documento
LEFT JOIN facultades f ON auf.id_facultad = f.id_facultad
LEFT JOIN programas_academicos p ON auf.id_programa = p.id_programa
GROUP BY u.numero_documento;
";

$resultado_usuarios_registrados = $ins_MainModelo->ejecutar_consultas_simples_two($consulta_usuarios_registrados);

// Encabezados
$usuarios = [
    ['Documento', 'Nombre', 'Apellidos', 'Correo', 'Teléfono', 'Rol', 'Facultades', 'Programas' , 'Fecha creación']
];
;

// Recorrer resultados y agregarlos al arreglo
while ($row = $resultado_usuarios_registrados->fetch(PDO::FETCH_ASSOC)) {
    $fecha = new DateTime($row['created_at']);
    $fecha_usuario = $fecha->format('Y-m-d'); // o 'd/m/Y' si lo prefieres
    $usuarios[] = [
        $row['numero_documento'],
        $row['nombre_usuario'],
        $row['apellidos_usuario'],
        $row['correo_usuario'],
        $row['telefono_usuario'],
        $row['nombre_rol'],
        $row['facultades_asignadas'],
        $row['programas_asignados'],
        $fecha_usuario
    ];
}

/*************************************************************************/

$consulta_anteproyectos = "SELECT 
    ap.codigo_anteproyecto,
    ap.titulo_anteproyecto,
    ap.palabras_claves,
    mo.nombre_modalidad,
    ap.estado,
    ap.fecha_creacion,
    COALESCE(f.nombre_facultad, 'Sin asignar') AS facultad,
    COALESCE(p.nombre_programa, 'Sin asignar') AS programa,
    COALESCE(GROUP_CONCAT(DISTINCT CONCAT(e.nombre_usuario, ' ', e.apellidos_usuario) SEPARATOR ', '), 'Sin asignar') AS estudiantes,
    COALESCE(GROUP_CONCAT(DISTINCT CONCAT(a.nombre_usuario, ' ', a.apellidos_usuario) SEPARATOR ', '), 'Sin asignar') AS asesores
FROM anteproyectos ap
LEFT JOIN modalidad_grados mo ON ap.modalidad = mo.id_modalidad
LEFT JOIN programas_academicos p ON ap.id_programa = p.id_programa
LEFT JOIN facultades f ON p.id_facultad = f.id_facultad
LEFT JOIN asignar_estudiante_anteproyecto aeap ON ap.codigo_anteproyecto = aeap.codigo_anteproyecto
LEFT JOIN usuarios e ON aeap.numero_documento = e.numero_documento
LEFT JOIN Asignar_asesor_anteproyecto_proyecto asap ON asap.codigo_proyecto = ap.codigo_anteproyecto
LEFT JOIN usuarios a ON asap.numero_documento = a.numero_documento
GROUP BY ap.codigo_anteproyecto;

"; // Pega aquí la consulta completa

$resultado_anteproyectos = $ins_MainModelo->ejecutar_consultas_simples_two($consulta_anteproyectos);

// Encabezado
$anteproyectos = [
    ['Código', 'Título', 'Palabras claves', 'Modalidad', 'Estado', 'Facultad', 'Programa', 'Estudiantes', 'Directores','Fecha creación']
];

// Llenar datos
while ($row = $resultado_anteproyectos->fetch(PDO::FETCH_ASSOC)) {
    $fecha = new DateTime($row['fecha_creacion']);
    $fecha_sola = $fecha->format('Y-m-d'); // o 'd/m/Y' si lo prefieres

    $anteproyectos[] = [
        $row['codigo_anteproyecto'],
        $row['titulo_anteproyecto'],
        $row['palabras_claves'],
        $row['nombre_modalidad'],
        $row['estado'],
        $row['facultad'],
        $row['programa'],
        $row['estudiantes'],
        $row['asesores'],
        $fecha_sola
    ];
    
}

/************************************************************************** */

$consulta_proyectos = "SELECT 
    ap.codigo_proyecto,
    ap.titulo_proyecto,
    ap.palabras_claves,
    mo.nombre_modalidad,
    ap.estado,
    ap.fecha_creacion,
    COALESCE(f.nombre_facultad, 'Sin asignar') AS facultad,
    COALESCE(p.nombre_programa, 'Sin asignar') AS programa,
    COALESCE(GROUP_CONCAT(DISTINCT CONCAT(e.nombre_usuario, ' ', e.apellidos_usuario) SEPARATOR ', '), 'Sin asignar') AS estudiantes,
    COALESCE(GROUP_CONCAT(DISTINCT CONCAT(a.nombre_usuario, ' ', a.apellidos_usuario) SEPARATOR ', '), 'Sin asignar') AS asesores,
    COALESCE(GROUP_CONCAT(DISTINCT CONCAT(j.nombre_usuario, ' ', j.apellidos_usuario) SEPARATOR ', '), 'Sin jurado') AS jurados,
    ep.calificacion_jurado1,
    ep.calificacion_jurado2
FROM proyectos ap
LEFT JOIN modalidad_grados mo ON ap.modalidad = mo.id_modalidad
LEFT JOIN programas_academicos p ON ap.id_programa = p.id_programa
LEFT JOIN facultades f ON p.id_facultad = f.id_facultad
LEFT JOIN asignar_estudiante_proyecto aeap ON ap.codigo_proyecto = aeap.codigo_proyecto
LEFT JOIN usuarios e ON aeap.numero_documento = e.numero_documento
LEFT JOIN Asignar_asesor_anteproyecto_proyecto asap ON asap.codigo_proyecto = ap.codigo_proyecto
LEFT JOIN usuarios a ON asap.numero_documento = a.numero_documento
LEFT JOIN Asignar_jurados_proyecto ajp ON ajp.codigo_proyecto = ap.codigo_proyecto
LEFT JOIN usuarios j ON ajp.numero_documento = j.numero_documento
LEFT JOIN evaluaciones_proyectos ep ON ep.codigo_proyecto = ap.codigo_proyecto
GROUP BY ap.codigo_proyecto;



";

$resultado_proyectos = $ins_MainModelo->ejecutar_consultas_simples_two($consulta_proyectos);

$proyectos = [
    ['Código', 'Título', 'Palabras claves', 'Modalidad', 'Estado', 'Facultad', 'Programa', 'Estudiantes', 'Directores', 'Jurados', 'Calificación Jurado 1', ' Calificación Jurado 2', ' Estado sustentación', 'Fecha creación']
];


while ($row = $resultado_proyectos->fetch(PDO::FETCH_ASSOC)) {
    $fecha = new DateTime($row['fecha_creacion']);
    $fecha_sola = $fecha->format('Y-m-d');

    $cal_jurado1 = (isset($row['calificacion_jurado1']) && $row['calificacion_jurado1'] > 0) 
    ? $row['calificacion_jurado1'] 
    : 'Sin calificar';

$cal_jurado2 = (isset($row['calificacion_jurado2']) && $row['calificacion_jurado2'] > 0) 
    ? $row['calificacion_jurado2'] 
    : 'Sin calificar';

    $promedio_totales = ($row['calificacion_jurado1'] + $row['calificacion_jurado2']) / 2;

    $promedio_redondeado = round($promedio_totales, 0);

    if ($promedio_redondeado < 70) {
        $texto = "❌ Proyecto Reprobado";
    } elseif ($promedio_redondeado <= 94) {
        $texto = "✅ Proyecto Aprobado";
    } elseif ($promedio_redondeado <= 99) {
        $texto ="🌟 Proyecto Sobresaliente";
    } else {
        $texto = "🏅 Proyecto Laureado (Perfecto)";
    }

$proyectos[] = [
    $row['codigo_proyecto'],
    $row['titulo_proyecto'],
    $row['palabras_claves'],
    $row['nombre_modalidad'],
    $row['estado'],
    $row['facultad'],
    $row['programa'],
    $row['estudiantes'],
    $row['asesores'],
    $row['jurados'],
    $cal_jurado1,
    $cal_jurado2,
    $texto,
    $fecha_sola
];

}


// Obtener la fecha actual en formato YYYY-MM-DD
$fecha_actual = date('Y-m-d');

// Crear archivo Excel con varias hojas
$xlsx = SimpleXLSXGen::make()
    ->addSheet($usuarios, 'Usuarios')
    ->addSheet($anteproyectos, 'Anteproyectos')
    ->addSheet($proyectos, 'Proyectos');

// Descargar con fecha en el nombre
$nombre_archivo = "informe_completo_{$fecha_actual}.xlsx";
$xlsx->downloadAs($nombre_archivo);
exit;
?>
