<?php

if( $id_rol_usuarios == 3){

    $consulta_proyecto_asignado = "SELECT codigo_anteproyecto FROM asignar_estudiante_anteproyecto WHERE numero_documento = :numero_documento LIMIT 1";
    $data_information_proyecto_Asignado = MainModel::conectar()->prepare($consulta_proyecto_asignado);
    $data_information_proyecto_Asignado->bindParam(':numero_documento', $numero_documento, PDO::PARAM_STR);
    $data_information_proyecto_Asignado->execute();
    $resultado = $data_information_proyecto_Asignado->fetch(PDO::FETCH_ASSOC);
    
    // Verifica si el estudiante tiene un proyecto asignado y muestra el código
    if ($resultado) {
        $codigo_proyecto = $resultado['codigo_anteproyecto'];
        $tiene_proyecto = '<span class="badge bg-success see-span"  data-bs-toggle="modal" data-bs-target="#staticBackdrop'.$numero_documento.'"
        data-bs-toggle="tooltip" data-bs-placement="right" title="Revisar"><i class="fa-regular fa-eye"></i></span> ';

        $consulta_asesor_asignado = "SELECT COUNT(*) as total FROM Asignar_asesor_anteproyecto_proyecto WHERE codigo_proyecto = :codigo_proyecto";
        $data_information_asesor_Asignado = MainModel::conectar()->prepare($consulta_asesor_asignado);
        $data_information_asesor_Asignado->bindParam(':codigo_proyecto', $codigo_proyecto, PDO::PARAM_STR);
        $data_information_asesor_Asignado->execute();
        $resultado_asesor = $data_information_asesor_Asignado->fetch(PDO::FETCH_ASSOC);
       // Badge que activa el modal (esto se mostrará en la tabla o donde lo necesites)
            $tiene_asesor = ($resultado_asesor['total'] > 0) ? 
            '<span class="badge bg-success see-span" data-bs-toggle="modal" data-bs-target="#asesorasignado'.$codigo_proyecto.'" data-bs-toggle="tooltip" data-bs-placement="right" title="Revisar"><i class="fa-regular fa-eye"></i></span>' : 
            '<span class="badge bg-danger"><i class="fa-solid fa-eye-slash"></i></span>';
        
            /*************************mostrar asesor asignado******************************* */

            $tabla .='<!-- Modal -->
            <div class="modal fade" id="asesorasignado'.$codigo_proyecto.'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Código: ' . htmlspecialchars($codigo_proyecto) .'</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">';
                // Realizamos la consulta para extraer la información del proyecto
                    // Consulta para obtener la información del profesor asignado al proyecto
                $query_profesor = "SELECT u.* 
                FROM Asignar_asesor_anteproyecto_proyecto aa
                INNER JOIN usuarios u ON aa.numero_documento = u.numero_documento
                WHERE aa.codigo_proyecto = :codigo_proyecto";
                $stmt_profesor = MainModel::conectar()->prepare($query_profesor);
                $stmt_profesor->bindParam(":codigo_proyecto", $codigo_proyecto, PDO::PARAM_STR);
                $stmt_profesor->execute();
                $resultado_profesor = $stmt_profesor->fetch(PDO::FETCH_ASSOC);


                $cedula_asesor_registrado = $resultado_profesor['numero_documento'];

                $nombre_asesor_registrado = $resultado_profesor['nombre_usuario'];

                $apellido_asesor_registrado = $resultado_profesor['apellidos_usuario'];

                $correo_asesor_registrado = $resultado_profesor['correo_usuario'];

                $telefono_asesor_registrado = $resultado_profesor['telefono_usuario'];

            

                $tabla .= '<!-- Contenedor con la tarjeta del proyecto -->
                <div id="project-container">
                  <div class="project-card">
                    <div class="project-header">
                      <div class="project-code">Documento: ' . htmlspecialchars($cedula_asesor_registrado) . '</div>
                      <h2 class="project-title">' . htmlspecialchars($nombre_asesor_registrado .' '.  $apellido_asesor_registrado) . '</h2>
                    </div>
                    <div class="project-body">
                      <p><strong>Telefono:</strong> ' . htmlspecialchars($telefono_asesor_registrado) . '</p>
                      <p><strong>Correo electronico: </strong> ' .$correo_asesor_registrado . '</p>
                      
                      '
                      ;

                $tabla .= '    </div>
                </div>
                </div>';
                        
          
        

            $tabla .='</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
            </div>';



            /*************************mostrar proyecto asignado y compañeros******************************* */
            
            $tabla .='<!-- Modal -->
            <div class="modal fade" id="staticBackdrop'.$numero_documento.'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Código: ' . htmlspecialchars($codigo_proyecto) .'</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">';
                // Realizamos la consulta para extraer la información del proyecto
                $query_proyecto = "SELECT * FROM anteproyectos WHERE codigo_anteproyecto = :codigo_proyecto";
                $stmt = MainModel::conectar()->prepare($query_proyecto);
                $stmt->bindParam(":codigo_proyecto", $codigo_proyecto, PDO::PARAM_STR);
                $stmt->execute();

                $query_asignados = "SELECT u.* 
                                    FROM usuarios u
                                    INNER JOIN asignar_estudiante_anteproyecto ae ON u.numero_documento = ae.numero_documento
                                    WHERE ae.codigo_anteproyecto = :codigo_proyecto
                                    AND u.numero_documento != :numero_documento";
                $stmt_asignados = MainModel::conectar()->prepare($query_asignados);
                $stmt_asignados->bindParam(":codigo_proyecto", $codigo_proyecto, PDO::PARAM_STR);
                $stmt_asignados->bindParam(":numero_documento", $numero_documento, PDO::PARAM_STR);
                $stmt_asignados->execute();

                $resultado_anteproyecto_stmt = $stmt->fetch(PDO::FETCH_ASSOC);

                $codigo_anteproyecto_registrado = $resultado_anteproyecto_stmt['codigo_anteproyecto'];

                $titulo_anteproyecto_registrado = $resultado_anteproyecto_stmt['titulo_anteproyecto'];

                $palabras_anteproyecto_registrado = $resultado_anteproyecto_stmt['palabras_claves'];

                $estado_anteproyecto_registrado = $resultado_anteproyecto_stmt['estado'];

                                        
                if ($estado_anteproyecto_registrado == "Aprobado") {

                    $estado_anteproyecto_registrado = '<span class="badge bg-success">Aprobado</span>'  ;

                }else if($estado_anteproyecto_registrado == "Revisión"){

                    $estado_anteproyecto_registrado = '<span class="badge bg-info">Revisión</span>'  ;
                }
                else{
                    // Extraer el estado del usuario
                
                    $estado_anteproyecto_registrado = '<span class="badge bg-danger">Cancelado</span>'  ;
                }

                $tabla .= '<!-- Contenedor con la tarjeta del proyecto -->
                <div id="project-container">
                  <div class="project-card">
                    <div class="project-header">
                      <div class="project-code">Código: ' . htmlspecialchars($codigo_anteproyecto_registrado) . '</div>
                      <h2 class="project-title">' . htmlspecialchars($titulo_anteproyecto_registrado) . '</h2>
                    </div>
                    <div class="project-body">
                      <p><strong>Palabras Claves:</strong> ' . htmlspecialchars($palabras_anteproyecto_registrado) . '</p>
                      <p><strong>estado</strong> ' .$estado_anteproyecto_registrado . '</p>
                      
                      '
                      ;
                      
                  // Sección para mostrar los compañeros asignados
                if ($stmt_asignados->rowCount() > 0) {
                    $tabla .= '<div class="assigned-users" style="margin-top:20px;">';
                    $tabla .= '<h3 style="font-size:1.4em; color:#333; margin-bottom:10px;">Compañeros Asignados:</h3>';
                    while ($rowa = $stmt_asignados->fetch(PDO::FETCH_ASSOC)) {
                        $tabla .= '<div class="assigned-user" style="padding:10px; border:1px solid #eee; margin-bottom:10px; border-radius:4px;">';
                        $tabla .= '<p><strong>Nombre:</strong> ' . htmlspecialchars($rowa['nombre_usuario']) . ' ' . htmlspecialchars($rowa['apellidos_usuario']) . '</p>';
                        $tabla .= '<p><strong>Correo:</strong> ' . htmlspecialchars($rowa['correo_usuario']) . '</p>';
                        // Agrega aquí más campos si es necesario, por ejemplo: teléfono, rol, etc.
                        $tabla .= '</div>';
                    }
                    $tabla .= '</div>';
                } else {
                    $tabla .= '<p style="color:#999; font-style:italic; margin-top:20px;">No tiene compañeros asignados.</p>';
                }

                $tabla .= '    </div>
                </div>
                </div>';
                        
          
        

            $tabla .='</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
            </div>';



    } else {
        $tiene_proyecto = '<span class="badge bg-danger"><i class="fa-solid fa-eye-slash"></i></span>';
        $tiene_asesor = '<span class="badge bg-danger"><i class="fa-solid fa-eye-slash"></i></span>';
    }

  



}else if($id_rol_usuarios == 4){
    
    $consulta_proyecto_asignado = "SELECT codigo_proyecto FROM asignar_estudiante_proyecto WHERE numero_documento = :numero_documento LIMIT 1";
    $data_information_proyecto_Asignado = MainModel::conectar()->prepare($consulta_proyecto_asignado);
    $data_information_proyecto_Asignado->bindParam(':numero_documento', $numero_documento, PDO::PARAM_STR);
    $data_information_proyecto_Asignado->execute();
    $resultado = $data_information_proyecto_Asignado->fetch(PDO::FETCH_ASSOC);
    
    // Verifica si el estudiante tiene un proyecto asignado y muestra el código
    if ($resultado) {
        $codigo_proyecto = $resultado['codigo_proyecto'];
        $tiene_proyecto = '<span class="badge bg-success see-span"  data-bs-toggle="modal" data-bs-target="#staticBackdrop'.$numero_documento.'"
        data-bs-toggle="tooltip" data-bs-placement="right" title="Revisar"><i class="fa-regular fa-eye"></i></span> ';

        $consulta_asesor_asignado = "SELECT COUNT(*) as total FROM Asignar_asesor_anteproyecto_proyecto WHERE codigo_proyecto = :codigo_proyecto";
        $data_information_asesor_Asignado = MainModel::conectar()->prepare($consulta_asesor_asignado);
        $data_information_asesor_Asignado->bindParam(':codigo_proyecto', $codigo_proyecto, PDO::PARAM_STR);
        $data_information_asesor_Asignado->execute();
        $resultado_asesor = $data_information_asesor_Asignado->fetch(PDO::FETCH_ASSOC);
       // Badge que activa el modal (esto se mostrará en la tabla o donde lo necesites)
            $tiene_asesor = ($resultado_asesor['total'] > 0) ? 
            '<span class="badge bg-success see-span" data-bs-toggle="modal" data-bs-target="#asesorasignado'.$codigo_proyecto.'" data-bs-toggle="tooltip" data-bs-placement="right" title="Revisar"><i class="fa-regular fa-eye"></i></span>' : 
            '<span class="badge bg-danger"><i class="fa-solid fa-eye-slash"></i></span>';
        
            /*************************mostrar asesor asignado******************************* */

            $tabla .='<!-- Modal -->
            <div class="modal fade" id="asesorasignado'.$codigo_proyecto.'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Código: ' . htmlspecialchars($codigo_proyecto) .'</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">';
                // Realizamos la consulta para extraer la información del proyecto
                    // Consulta para obtener la información del profesor asignado al proyecto
                $query_profesor = "SELECT u.* 
                FROM Asignar_asesor_anteproyecto_proyecto aa
                INNER JOIN usuarios u ON aa.numero_documento = u.numero_documento
                WHERE aa.codigo_proyecto = :codigo_proyecto";
                $stmt_profesor = MainModel::conectar()->prepare($query_profesor);
                $stmt_profesor->bindParam(":codigo_proyecto", $codigo_proyecto, PDO::PARAM_STR);
                $stmt_profesor->execute();
                $resultado_profesor = $stmt_profesor->fetch(PDO::FETCH_ASSOC);


                $cedula_asesor_registrado = $resultado_profesor['numero_documento'];

                $nombre_asesor_registrado = $resultado_profesor['nombre_usuario'];

                $apellido_asesor_registrado = $resultado_profesor['apellidos_usuario'];

                $correo_asesor_registrado = $resultado_profesor['correo_usuario'];

                $telefono_asesor_registrado = $resultado_profesor['telefono_usuario'];

            

                $tabla .= '<!-- Contenedor con la tarjeta del proyecto -->
                <div id="project-container">
                  <div class="project-card">
                    <div class="project-header">
                      <div class="project-code">Documento: ' . htmlspecialchars($cedula_asesor_registrado) . '</div>
                      <h2 class="project-title">' . htmlspecialchars($nombre_asesor_registrado .' '.  $apellido_asesor_registrado) . '</h2>
                    </div>
                    <div class="project-body">
                      <p><strong>Telefono:</strong> ' . htmlspecialchars($telefono_asesor_registrado) . '</p>
                      <p><strong>Correo electronico: </strong> ' .$correo_asesor_registrado . '</p>
                      
                      '
                      ;

                $tabla .= '    </div>
                </div>
                </div>';
                        
          
        

            $tabla .='</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
            </div>';



            /*************************mostrar proyecto asignado y compañeros******************************* */
            
            $tabla .='<!-- Modal -->
            <div class="modal fade" id="staticBackdrop'.$numero_documento.'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Código: ' . htmlspecialchars($codigo_proyecto) .'</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">';
                // Realizamos la consulta para extraer la información del proyecto
                $query_proyecto = "SELECT * FROM proyectos WHERE codigo_proyecto = :codigo_proyecto";
                $stmt = MainModel::conectar()->prepare($query_proyecto);
                $stmt->bindParam(":codigo_proyecto", $codigo_proyecto, PDO::PARAM_STR);
                $stmt->execute();

                $query_asignados = "SELECT u.* 
                                    FROM usuarios u
                                    INNER JOIN asignar_estudiante_proyecto ae ON u.numero_documento = ae.numero_documento
                                    WHERE ae.codigo_proyecto = :codigo_proyecto
                                    AND u.numero_documento != :numero_documento";
                $stmt_asignados = MainModel::conectar()->prepare($query_asignados);
                $stmt_asignados->bindParam(":codigo_proyecto", $codigo_proyecto, PDO::PARAM_STR);
                $stmt_asignados->bindParam(":numero_documento", $numero_documento, PDO::PARAM_STR);
                $stmt_asignados->execute();

                $resultado_anteproyecto_stmt = $stmt->fetch(PDO::FETCH_ASSOC);

                $codigo_anteproyecto_registrado = $resultado_anteproyecto_stmt['codigo_proyecto'];

                $titulo_anteproyecto_registrado = $resultado_anteproyecto_stmt['titulo_proyecto'];

                $palabras_anteproyecto_registrado = $resultado_anteproyecto_stmt['palabras_claves'];

                $estado_anteproyecto_registrado = $resultado_anteproyecto_stmt['estado'];

                                        
                if ($estado_anteproyecto_registrado == "Aprobado") {

                    $estado_anteproyecto_registrado = '<span class="badge bg-success">Aprobado</span>'  ;

                }else if($estado_anteproyecto_registrado == "Revisión"){

                    $estado_anteproyecto_registrado = '<span class="badge bg-info">Revisión</span>'  ;
                }
                else{
                    // Extraer el estado del usuario
                
                    $estado_anteproyecto_registrado = '<span class="badge bg-danger">Cancelado</span>'  ;
                }

                $tabla .= '<!-- Contenedor con la tarjeta del proyecto -->
                <div id="project-container">
                  <div class="project-card">
                    <div class="project-header">
                      <div class="project-code">Código: ' . htmlspecialchars($codigo_anteproyecto_registrado) . '</div>
                      <h2 class="project-title">' . htmlspecialchars($titulo_anteproyecto_registrado) . '</h2>
                    </div>
                    <div class="project-body">
                      <p><strong>Palabras Claves:</strong> ' . htmlspecialchars($palabras_anteproyecto_registrado) . '</p>
                      <p><strong>estado</strong> ' .$estado_anteproyecto_registrado . '</p>
                      
                      '
                      ;
                      
                  // Sección para mostrar los compañeros asignados
                if ($stmt_asignados->rowCount() > 0) {
                    $tabla .= '<div class="assigned-users" style="margin-top:20px;">';
                    $tabla .= '<h3 style="font-size:1.4em; color:#333; margin-bottom:10px;">Compañeros Asignados:</h3>';
                    while ($rowa = $stmt_asignados->fetch(PDO::FETCH_ASSOC)) {
                        $tabla .= '<div class="assigned-user" style="padding:10px; border:1px solid #eee; margin-bottom:10px; border-radius:4px;">';
                        $tabla .= '<p><strong>Nombre:</strong> ' . htmlspecialchars($rowa['nombre_usuario']) . ' ' . htmlspecialchars($rowa['apellidos_usuario']) . '</p>';
                        $tabla .= '<p><strong>Correo:</strong> ' . htmlspecialchars($rowa['correo_usuario']) . '</p>';
                        // Agrega aquí más campos si es necesario, por ejemplo: teléfono, rol, etc.
                        $tabla .= '</div>';
                    }
                    $tabla .= '</div>';
                } else {
                    $tabla .= '<p style="color:#999; font-style:italic; margin-top:20px;">No tiene compañeros asignados.</p>';
                }

                $tabla .= '    </div>
                </div>
                </div>';
                        
          
        

            $tabla .='</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
            </div>';



    } else {
        $tiene_proyecto = '<span class="badge bg-danger"><i class="fa-solid fa-eye-slash"></i></span>';
        $tiene_asesor = '<span class="badge bg-danger"><i class="fa-solid fa-eye-slash"></i></span>';
    }

   
    

} else if($id_rol_usuarios == 5){

    $query_proyectos = "SELECT * FROM Asignar_asesor_anteproyecto_proyecto WHERE numero_documento = :numero_documento";
    $stmt_proyectos = MainModel::conectar()->prepare($query_proyectos);
    $stmt_proyectos->bindParam(":numero_documento", $numero_documento, PDO::PARAM_STR);
    $stmt_proyectos->execute();
    // Verifica si el estudiante tiene un proyecto asignado

    if ($stmt_proyectos->rowCount() > 0) {

        $tiene_proyecto = '<span class="badge bg-success see-span"  data-bs-toggle="modal" data-bs-target="#proyectosasignadosasesor'.$codigo_proyecto.'"
        data-bs-toggle="tooltip" data-bs-placement="right" title="Revisar"><i class="fa-regular fa-eye"></i></span> ';

        $tabla .='<!-- Modal -->
       <div class="modal fade" id="proyectosasignadosasesor'.$codigo_proyecto.'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
       <div class="modal-dialog modal-xl modal-dialog-scrollable">
           <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" id="staticBackdropLabel">Proyectos asigandos asesor</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">';
        
           while ($asignacion = $stmt_proyectos->fetch(PDO::FETCH_ASSOC)) {

                $codigo_proyecto_asignado = $asignacion['codigo_proyecto'];

                // Primero, buscamos en la tabla de anteproyectos
                $queryAnte = "SELECT * FROM anteproyectos WHERE codigo_anteproyecto = :codigo";
                $stmtAnte = MainModel::conectar()->prepare($queryAnte);
                $stmtAnte->bindParam(":codigo", $codigo_proyecto_asignado, PDO::PARAM_STR);
                $stmtAnte->execute();

                if ($stmtAnte->rowCount() > 0) { /**** pertence a un anteproyecto */

                $tipo = "Anteproyecto";

                    // 2. Consulta: Obtener la información del anteproyecto para este código
                $query_proyecto_detalle = "SELECT * FROM anteproyectos WHERE codigo_anteproyecto = :codigo_proyecto";
                $stmt_detalle = MainModel::conectar()->prepare($query_proyecto_detalle);
                $stmt_detalle->bindParam(":codigo_proyecto", $codigo_proyecto_asignado, PDO::PARAM_STR);
                $stmt_detalle->execute();
                $proyecto_detalle = $stmt_detalle->fetch(PDO::FETCH_ASSOC);

                if ($proyecto_detalle) {
                    $codigo = $proyecto_detalle['codigo_anteproyecto'];
                    $titulo = $proyecto_detalle['titulo_anteproyecto'];
                    $palabras = $proyecto_detalle['palabras_claves'];

                    // 3. Consulta: Obtener los estudiantes asignados a este proyecto
                    $query_estudiantes = "SELECT u.* 
                                        FROM asignar_estudiante_anteproyecto ae
                                        INNER JOIN usuarios u ON ae.numero_documento = u.numero_documento
                                        WHERE ae.codigo_anteproyecto = :codigo_proyecto";
                    $stmt_estudiantes = MainModel::conectar()->prepare($query_estudiantes);
                    $stmt_estudiantes->bindParam(":codigo_proyecto", $codigo, PDO::PARAM_STR);
                    $stmt_estudiantes->execute();

                    

                $tabla .= '<!-- Contenedor con la tarjeta del proyecto -->
                <div id="project-container">
                    <div class="project-card">
                    <div class="project-header">
                        <div class="project-code">Codigo anteproyecto: ' . htmlspecialchars($codigo) . '</div>
                        <h2 class="project-title">' . htmlspecialchars($titulo ) . '</h2>
                    </div>
                    <div class="project-body">
                        <p><strong>Palabras claves:</strong> ' . htmlspecialchars($palabras) . '</p>
                         <p><strong>Tipo: </strong> ' . htmlspecialchars($tipo) . '</p>
                        ';

                        $estudiantes_html = '';
                    if ($stmt_estudiantes->rowCount() > 0) {
                        $estudiantes_html = '<h5 class="mt-3 mb-2"> <span class="badge bg-info">Nombre estudiantes</span></h5>';
                        while ($estudiante = $stmt_estudiantes->fetch(PDO::FETCH_ASSOC)) {
                            $estudiantes_html .= '<div style="border: 1px solid #eee; padding: 8px; margin-bottom: 8px; border-radius: 4px;">';
                            $estudiantes_html .= '<p><strong>Nombre:</strong> ' . htmlspecialchars($estudiante['nombre_usuario']) . ' ' . htmlspecialchars($estudiante['apellidos_usuario']) . '</p>';
                            $estudiantes_html .= '<p><strong>Correo:</strong> ' . htmlspecialchars($estudiante['correo_usuario']) . '</p>';
                            // Puedes agregar más campos si lo deseas, como teléfono, rol, etc.
                            $estudiantes_html .= '</div>';
                        }
                    } else {
                        $estudiantes_html = '<p style="font-style: italic; color: #999;">No hay estudiantes asignados.</p>';
                    }

                    $tabla .= $estudiantes_html;

                $tabla .= '    </div>
                </div>
                </div>';
                }



                } else {  /**** pertence a un proyecto */
                   
                    $tipo = "Proyecto";

                    // 2. Consulta: Obtener la información del anteproyecto para este código
                $query_proyecto_detalle = "SELECT * FROM proyectos WHERE codigo_proyecto = :codigo_proyecto";
                $stmt_detalle = MainModel::conectar()->prepare($query_proyecto_detalle);
                $stmt_detalle->bindParam(":codigo_proyecto", $codigo_proyecto_asignado, PDO::PARAM_STR);
                $stmt_detalle->execute();
                $proyecto_detalle = $stmt_detalle->fetch(PDO::FETCH_ASSOC);

                if ($proyecto_detalle) {
                    $codigo = $proyecto_detalle['codigo_proyecto'];
                    $titulo = $proyecto_detalle['titulo_proyecto'];
                    $palabras = $proyecto_detalle['palabras_claves'];

                    // 3. Consulta: Obtener los estudiantes asignados a este proyecto
                    $query_estudiantes = "SELECT u.* 
                                        FROM asignar_estudiante_proyecto ae
                                        INNER JOIN usuarios u ON ae.numero_documento = u.numero_documento
                                        WHERE ae.codigo_proyecto = :codigo_proyecto";
                    $stmt_estudiantes = MainModel::conectar()->prepare($query_estudiantes);
                    $stmt_estudiantes->bindParam(":codigo_proyecto", $codigo, PDO::PARAM_STR);
                    $stmt_estudiantes->execute();

                    

                $tabla .= '<!-- Contenedor con la tarjeta del proyecto -->
                <div id="project-container">
                    <div class="project-card">
                    <div class="project-header">
                        <div class="project-code">Codigo proyecto: ' . htmlspecialchars($codigo) . '</div>
                        <h2 class="project-title">' . htmlspecialchars($titulo ) . '</h2>
                    </div>
                    <div class="project-body">
                        <p><strong>Palabras claves:</strong> ' . htmlspecialchars($palabras) . '</p>
                         <p><strong>Tipo: </strong> ' . htmlspecialchars($tipo) . '</p>
                        ';

                        $estudiantes_html = '';
                    if ($stmt_estudiantes->rowCount() > 0) {
                        $estudiantes_html = '<h5 class="mt-3 mb-2"> <span class="badge bg-info">Nombre estudiantes</span></h5>';
                        while ($estudiante = $stmt_estudiantes->fetch(PDO::FETCH_ASSOC)) {
                            $estudiantes_html .= '<div style="border: 1px solid #eee; padding: 8px; margin-bottom: 8px; border-radius: 4px;">';
                            $estudiantes_html .= '<p><strong>Nombre:</strong> ' . htmlspecialchars($estudiante['nombre_usuario']) . ' ' . htmlspecialchars($estudiante['apellidos_usuario']) . '</p>';
                            $estudiantes_html .= '<p><strong>Correo:</strong> ' . htmlspecialchars($estudiante['correo_usuario']) . '</p>';
                            // Puedes agregar más campos si lo deseas, como teléfono, rol, etc.
                            $estudiantes_html .= '</div>';
                        }
                    } else {
                        $estudiantes_html = '<p style="font-style: italic; color: #999;">No hay estudiantes asignados.</p>';
                    }

                    $tabla .= $estudiantes_html;

                $tabla .= '    </div>
                </div>
                </div>';
                }

                }


                
           }

       $tabla .='</div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
           </div>
           </div>
       </div>
       </div>';

       $tiene_asesor = '<span class="badge  bg-danger text-dark"><i class="fa-regular fa-eye"></i></span>';

    }else{

        
    $tiene_proyecto = '<span class="badge  bg-danger text-dark"><i class="fa-regular fa-eye"></i></span>';

    $tiene_asesor = '<span class="badge  bg-danger text-dark"><i class="fa-regular fa-eye"></i></span>';

    }

        

    
}else{

    $tiene_proyecto = '<span class="badge  bg-danger text-dark"><i class="fa-regular fa-eye"></i></span>';

    $tiene_asesor = '<span class="badge  bg-danger text-dark"><i class="fa-regular fa-eye"></i></span>';

}

?>