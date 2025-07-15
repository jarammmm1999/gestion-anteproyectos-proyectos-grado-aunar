<?php

if ($_SESSION['privilegio'] != 5 && $_SESSION['privilegio'] != 1 && $_SESSION['privilegio'] != 2 && $_SESSION['privilegio'] != 6) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
    echo $ins_loginControlador->cerrar_sesion_controlador();
    exit();
}

?>

<div class="contenedor-fecha-sustentaciones">

<?php

$consulta_proyectos_jurado_Asignado = "SELECT 
    aa.codigo_proyecto, 
    aa.titulo_proyecto, 
    aa.palabras_claves, 
    aa.fecha_creacion,
    f.nombre_facultad,
    p.nombre_programa,
    IFNULL(GROUP_CONCAT(CONCAT(u.nombre_usuario, ' ', u.apellidos_usuario) SEPARATOR ', '), 'Sin estudiantes asignados') AS estudiantes_asignados,
    ep.fecha AS fecha_sustentacion
FROM proyectos aa
INNER JOIN facultades f ON aa.id_facultad = f.id_facultad
INNER JOIN programas_academicos p ON aa.id_programa = p.id_programa
LEFT JOIN asignar_estudiante_proyecto ae ON aa.codigo_proyecto = ae.codigo_proyecto
LEFT JOIN usuarios u ON ae.numero_documento = u.numero_documento
LEFT JOIN evaluaciones_proyectos ep ON aa.codigo_proyecto = ep.codigo_proyecto
WHERE aa.estado = 'Aprobado'
GROUP BY aa.codigo_proyecto, aa.titulo_proyecto, aa.palabras_claves, aa.fecha_creacion, f.nombre_facultad, p.nombre_programa, ep.fecha";


$resultado_proyectos_jurados = $ins_loginControlador->ejecutar_consultas_simples_two($consulta_proyectos_jurado_Asignado);

if ($resultado_proyectos_jurados->rowCount() > 0) {
    ?>

    <div class="table-responsive">
        <table class="table table-striped table-bordered dt-responsive nowrap" id="tabla_usuarios">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Título</th>
                    <th>Palabras clave</th>
                    <th>Estudiantes</th>
                    <th>Facultad</th>
                    <th>Programa</th>
                    <th>Fecha actual de sustentación</th>
                    <th>Seleccionar nueva fecha</th>
                    <th>Guardar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $contador = 1;
                while ($fila = $resultado_proyectos_jurados->fetch(PDO::FETCH_ASSOC)) {

                    if($fila['fecha_sustentacion'] == ""){
                        $fecha = '<span class="badge bg-danger">Sin definir</span>';
                    }else{
                        $fecha = '<span class="badge bg-success">'.$fila['fecha_sustentacion'].'</span>';
                        
                    }
                    ?>
                    <tr>
                        <td><?= $contador++ ?></td>
                        <td><?= $fila['codigo_proyecto'] ?></td>
                        <td><?= $fila['titulo_proyecto'] ?></td>
                        <td><?= $fila['palabras_claves'] ?></td>
                        <td><?= $fila['estudiantes_asignados'] ?></td>
                        <td><?= $fila['nombre_facultad'] ?></td>
                        <td><?= $fila['nombre_programa'] ?></td>

                        <?php
                        

                        
                        ?>


                        <td>
                            <?=$fecha?>
                        </td>
                        <td>
                            <form class="user-form mt-5 mb-5 FormulariosAjax" action="<?= SERVERURL ?>Ajax/ProyectoAjax.php" method="POST" data-form="save" autocomplete="off">
                                <input type="hidden" name="codigo_proyecto" value="<?= $ins_loginControlador->encryption($fila['codigo_proyecto']) ?>">
                                <input type="date" name="nueva_fecha_sustentacion" required>
                        </td>
                        <td>
                                <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php
}
 else {
    echo "<p>No se encontraron proyectos aprobados.</p>";
}
?>

</div>