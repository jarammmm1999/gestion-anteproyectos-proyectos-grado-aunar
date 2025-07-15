<?php


if (isset($_SESSION['privilegio']) && $_SESSION['privilegio'] == 1 || $_SESSION['privilegio'] == 2) {

    if (isset($ruta[0]) && $ruta[0] == "home") {

?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-house"></i></span>Bienvenido</h3>
            <p>Hola, <b> <?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b> Bienvenido al Módulo de <?= $rol ?>. Este entorno ha sido diseñado para proporcionarte acceso integral a las herramientas y funcionalidades necesarias para optimizar la gestión de procesos y recursos de manera eficiente y estructurada. A través de este panel centralizado, podrás administrar usuarios, supervisar el progreso de proyectos, evaluar informes detallados y ejecutar múltiples operaciones con el máximo control y precisión. </p>
        </div>

    <?php
    } else if (isset($ruta[0]) && $ruta[0] == "registrar-usuarios") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-user-plus"></i></span> Registro de usuarios</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al Módulo de Gestión de Usuarios. En este espacio podrás registrar de manera sencilla y eficiente a los usuarios que formarán parte de la aplicación, asegurando que toda la información se capture con precisión y se mantenga organizada.</p>
        </div>


    <?php


    } else if (isset($ruta[0]) && $ruta[0] == "fecha-sustentacion") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3">
                <span><i class="fa-solid fa-calendar-check"></i></span> Asignación de Fecha de Sustentación buenas tardes Camila
            </h3>
            <p>
                Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>.
                Bienvenido al módulo de asignación de fechas de sustentación.
                En este espacio podrás definir la fecha en la que los proyectos previamente aprobados deberán ser sustentados por los estudiantes.
                Esta funcionalidad permite organizar y planificar eficientemente el calendario académico, asegurando una adecuada gestión del proceso de evaluación final.
            </p>
        </div>



    <?php


    } else  if (isset($ruta[0]) && $ruta[0] == "evidencias-reuniones") {
    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-upload"></i></span> Cargar evidencias </h3>
            <p>Hola, <b> <?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b> Bienvenido al módulo de evidencias de reuniones de los proyectos. En este espacio podrás ver todas las imágenes como evidencia de las reuniones de asesoría realizadas entre el profesor y los estudiantes para sus anteproyectos o proyectos de grado, y estan organizadas por fechas para un mayor seguimiento.

            </p>
        </div>

    <?php
    } else  if (isset($ruta[0]) && $ruta[0] == "ver-evidencia") {
    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-eye"></i></span> Observar evidencias </h3>
            <p>Hola, <b> <?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b> Bienvenido al módulo de visualización de evidencias de asesorías. En este espacio podrás consultar las imágenes cargadas como evidencia de las reuniones de asesoría realizadas para los anteproyectos o proyectos de grado. Aquí encontrarás un registro visual de cada sesión, permitiendo revisar los avances, acuerdos y aspectos clave documentados en el proceso de orientación académica..

            </p>
        </div>

    <?php
    } else if (isset($ruta[0]) && $ruta[0] == "ver-documentos-anteproyectos-asesor") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-magnifying-glass"></i></span> Consulta Retroalimentaciones</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de consulta de retroalimentaciones para estudiantes. En este espacio podrás visualizar los documentos del anteproyecto y consultar todas las retroalimentaciones proporcionadas por los directores. Aquí encontrarás observaciones previas y recomendaciones para guiarte en el proceso de mejora de tu trabajo, asegurando que cada versión refleje el progreso y los ajustes necesarios. </p>
        </div>
    <?php

    } else  if (isset($ruta[0]) && $ruta[0] == "entregas-proyectos") {
    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-eye"></i></span>Versiones documentos enviados por los estudiantes </h3>
            <p>Hola, <b> <?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b> Bienvenido al módulo de versiones de los proyectos asignados. En este espacio podrás consultar todas las versiones de un proyecto, desde sus primeras entregas hasta la versión final. Cada versión estará organizada de manera cronológica, permitiendo un fácil acceso para revisión y comparación. Este módulo te asegura que todo el historial del desarrollo del proyecto esté disponible para un seguimiento detallado y exhaustivo. </p>
        </div>

    <?php
    } else  if (isset($ruta[0]) && $ruta[0] == "entregas-anteproyectos") {
    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-eye"></i></span>Versiones documentos enviados por los estudiantes </h3>
            <p>Hola, <b> <?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b> Bienvenido al módulo de versiones de anteproyectos asignados. En este espacio podrás consultar todas las versiones de un anteproyecto, desde sus primeras entregas hasta la versión final. Cada versión estará organizada de manera cronológica, permitiendo un fácil acceso para revisión y comparación. Este módulo te asegura que todo el historial del desarrollo del anteproyecto esté disponible para un seguimiento detallado y exhaustivo. </p>
        </div>

    <?php
    } else if (isset($ruta[0]) && $ruta[0] == "configuration-user") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-gear"></i></span> Configuración usuarios</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de configuración de usuario. En este espacio podrás actualizar tu información personal, gestionar tus datos de contacto y ajustar tus preferencias según tus necesidades. Este módulo te permite mantener toda tu información al día, asegurando una experiencia personalizada y adaptada a tus requerimientos dentro de la plataforma.</p>
        </div>


    <?php


    } else if (isset($ruta[0]) && $ruta[0] == "user-list") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-magnifying-glass"></i></span> Consulta de usuarios</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al Módulo de Consulta de Usuarios. En este espacio podrás consultar, actualizar y eliminar los registros de usuarios que forman parte de la aplicación. Contarás con todas las herramientas necesarias para gestionar de manera eficiente la información, garantizando un control detallado y preciso de cada perfil.</p>
        </div>


    <?php


    } else if (isset($ruta[0]) && $ruta[0] == "user-update") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-user-pen"></i></span> Actualización de usuarios</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al Módulo de Actualización de Información de Usuarios. En esta sección podrás modificar y actualizar de manera sencilla los datos de los usuarios que forman parte de la aplicación, garantizando que la información se mantenga siempre actualizada y precisa.</p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "ideas-update") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-user-pen"></i></span> Actualización de ideas</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de actualización y eliminación de ideas de. En esta sección podrás actualizar y eliminar la información de las ideas de anteproyectos ya registradas, asegurando una gestión eficiente y un control preciso de cada propuesta.</p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "proyecto-update") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-user-pen"></i></span> Actualización de proyectos</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de actualización y eliminación de proyectos de. En esta sección podrás actualizar y eliminar la información de los de proyectos ya registrados, asegurando una gestión eficiente y un control preciso de cada uno.</p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "registro-anteproyectos") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fas fa-book"></i></span> Registro de ideas de anteproyectos</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de registro de ideas de anteproyectos. En este módulo podrás ingresar y registrar nuevas ideas de anteproyectos de manera organizada, asegurando que cada propuesta se documente correctamente para facilitar su posterior consulta y seguimiento. </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "registro-proyectos") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fas fa-book"></i></span> Registro de proyectos</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de registro de proyecto. En este módulo podrás ingresar y registrar nuevos proyectos de manera organizada, asegurando que cada proyecto se documente correctamente para facilitar su posterior consulta y seguimiento. </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "asignar-estudiantes-anteproyecto") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fas fa-book"></i></span> Asignar estudiantes anteproyectos </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de asignación de estudiantes a anteproyectos. En este espacio podrás registrar la información básica necesaria para asignar de manera precisa a los estudiantes a sus respectivos anteproyectos, garantizando una gestión organizada y eficiente. </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "asignar-estudiantes-proyectos") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fas fa-book"></i></span> Asignar estudiantes proyectos </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de asignación de estudiantes a los proyectos de grados. En este espacio podrás registrar la información básica necesaria para asignar de manera precisa a los estudiantes a sus respectivos proyectos, garantizando una gestión organizada y eficiente. </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "asignar-horas-profesores") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fas fa-book"></i></span> Asignar horas asesorias profesores </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de asignación de horas de asesoría para profesores.. En este espacio podrás registrar la información básica necesaria para programar y asignar las horas de asesoría a cada profesor, garantizando una distribución eficiente y organizada. </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "consultar-ideas") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fas fa-book"></i></span> Consulta de ideas de anteproyectos </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de Consulta de Ideas de Anteproyectos. Desde este módulo, tendrás la posibilidad de consultar, registrar, actualizar y eliminar la información básica de las ideas de anteproyectos, facilitando su administración y correcta asignación dentro del sistema. </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "consultar-proyectos") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fas fa-book"></i></span> Consulta de proyectos </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de Consulta de proyectos. Desde este módulo, tendrás la posibilidad de consultar, registrar, actualizar y eliminar la información básica de los proyectos, facilitando su administración y correcta asignación dentro del sistema. </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "asignar-usuarios-faculta") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-users"></i></span> Asignar usuarios a facultades y programas</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de asignación de usuarios a facultades y programas . En este espacio podrás asociar de manera sencilla y eficiente a los usuarios con sus respectivas facultades y programas académicos, asegurando que cada perfil esté correctamente vinculado al entorno institucional correspondiente para facilitar la gestión y el seguimiento de cada miembro. </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "asignacion-asesor") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3">
                <span><i class="fa-solid fa-users"></i></span> Asignar director a proyectos y anteproyectos.
            </h3>
            <p>
                Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de asignación de directores a proyectos y anteproyectos. En esta sección podrás designar de manera eficiente a los directores responsables de brindar acompañamiento académico a cada iniciativa estudiantil. Este proceso garantiza un seguimiento adecuado, fortalece la calidad de los proyectos y optimiza la orientación en cada etapa de desarrollo.
            </p>
            <p>
                Además, este módulo permite la incorporación y asignación de directores externos, lo cual enriquece el proceso formativo al integrar experiencias y conocimientos provenientes del entorno profesional. De esta manera, se fomenta una visión más amplia y práctica en la ejecución de los proyectos.
            </p>
        </div>

    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "consultar-horas-asesores") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-users"></i></span> Consultar horas asesores</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de consulta y gestión de horas de asesores . En este espacio podrás consultar las horas asignadas a los asesores, así como editarlas o eliminarlas según sea necesario, asegurando una administración eficiente y flexible de sus horarios. </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "asignar-jurados") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-users"></i></span> Asignar jurados</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de asignación de jurados a proyectos. En esta sección podrás designar de manera precisa a los jurados encargados de evaluar los diferentes proyectos. Este módulo te ofrece las herramientas necesarias para asignar a los evaluadores de acuerdo con su área de especialización, garantizando una revisión objetiva y completa de cada trabajo académico. Asegúrate de que cada proyecto cuente con el jurado adecuado para asegurar un proceso de evaluación riguroso y transparente. </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "asignar-horas-jurados") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-users"></i></span> Asignar horas jurados</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de asignación de horas a jurados. Este espacio te permite asignar de manera eficiente las horas correspondientes a los jurados encargados de evaluar los proyectos. Podrás distribuir el tiempo de evaluación según la carga de trabajo de cada jurado, garantizando un proceso de revisión justo y equilibrado. Asegúrate de que cada jurado tenga las horas adecuadas asignadas para facilitar una evaluación detallada y cuidadosa de los proyectos. </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "informe-aplicacion") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-file-lines"></i></span> Sección de Informes</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido a la sección de informes. Aquí podrás acceder a toda la información de la aplicación y generar reportes detallados para un mejor análisis y seguimiento. Este módulo te proporciona herramientas avanzadas para visualizar datos clave y exportar reportes que te ayudarán a tomar decisiones informadas y a optimizar la gestión de la aplicación. ¡Explora y aprovecha al máximo las funcionalidades de esta sección!</p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "proyectos-asignados-jurados") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fas fa-book"></i></span> Consulta de proyectos calificados </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de proyectos calificados por los jurados. En esta sección podrás consultar los proyectos que están pendientes por calificar o que ya han sido calificados. Aquí tendrás acceso a los detalles de cada proyecto, facilitando el seguimiento, la revisión y la consulta de evaluaciones previas. </p>

        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "calificar-proyectos") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fas fa-book"></i></span> Consulta de proyectos asignado jurado </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de calificación de proyectos. En este espacio podrás evaluar y asignar calificaciones a los proyectos presentados, asegurando que cada propuesta reciba una valoración justa y detallada. Aquí tendrás acceso a todas las herramientas necesarias para registrar tus observaciones, puntuaciones y comentarios, contribuyendo así al desarrollo académico de cada estudiante. Tu opinión como evaluador es fundamental para el éxito de este proceso.</p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "asesor-metodologico") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3">
                <span><i class="fa-solid fa-star"></i></span> Ítems de Evaluación del Proyecto
            </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>.
                A continuación, se presentan los criterios fundamentales que serán considerados en la evaluación del proyecto.
                Cada ítem refleja aspectos clave en la calidad, innovación y desarrollo del trabajo, asegurando una valoración objetiva y detallada.
                Agradecemos su criterio profesional en este proceso de evaluación.
            </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "acta-proyectos") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3">
                <span><i class="fa-solid fa-file-alt"></i></span> Acta del Proyecto de Grado
            </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>.
                A continuación, se presenta el acta oficial del proyecto de grado, un documento fundamental que certifica el desarrollo, evaluación y seguimiento del trabajo realizado.
                En este acta se registran aspectos clave como la aprobación, observaciones y decisiones tomadas durante el proceso.
                Es importante revisar detalladamente la información contenida, ya que forma parte integral del proceso académico.
            </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "calificacion-jurados") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3">
                <span><i class="fa-solid fa-star"></i></span> Ítems de Evaluación del Proyecto
            </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>.
                A continuación, se presentan los criterios fundamentales que serán considerados en la evaluación del proyecto.
                Cada ítem refleja aspectos clave en la calidad, innovación y desarrollo del trabajo, asegurando una valoración objetiva y detallada.
                Agradecemos su criterio profesional en este proceso de evaluación.
            </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "ver-documentos-proyectos-asesor") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3">
                <span><i class="fa-solid fa-folder-open"></i></span> Documentos enviados por los estudiantes
            </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>.
                Bienvenido al módulo de gestión de documentos. 📂✨ En esta sección podrás visualizar todos los documentos enviados por los estudiantes y las retroalimentacione hechas por los directores encargados o directores externos asignados, para su respectiva revisión y evaluación.

                Aquí encontrarás las entregas organizadas de manera clara, lo que te permitirá acceder a cada versión con facilidad y realizar un seguimiento detallado del progreso del proyecto. Este módulo facilita la supervisión de cada etapa del proceso académico, asegurando un control efectivo y una retroalimentación precisa.

            </p>
        </div>
    <?php


    }
}
if (isset($_SESSION['privilegio']) && $_SESSION['privilegio'] == 3 || $_SESSION['privilegio'] == 4) {

    if (isset($ruta[0]) && $ruta[0] == "home") {
    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-house"></i></span>Bienvenido</h3>
            <p>Hola, <b> <?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b> Bienvenido al Módulo de <?= $rol ?>. Este entorno ha sido diseñado para proporcionarte acceso integral a las herramientas y funcionalidades necesarias para optimizar la gestión de procesos y recursos de manera eficiente y estructurada. A través de este panel centralizado, podrás subir docuementos, consultar ideas, consultar observaciones y ejecutar múltiples operaciones con el máximo control y precisión. </p>
        </div>

    <?php
    } else if (isset($ruta[0]) && $ruta[0] == "consultar-retroalimentaciones") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-magnifying-glass"></i></span> Consulta Retroalimentaciones</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de consulta de retroalimentaciones para estudiantes. En este espacio podrás acceder a todas las retroalimentaciones recibidas sobre tu anteproyecto. Aquí encontrarás las observaciones, sugerencias y recomendaciones proporcionadas por los directores y jurados, lo que te permitirá mejorar y ajustar tu trabajo de acuerdo con los comentarios realizados a lo largo del proceso. </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "consultar-jurados-asignados-proyectos") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3">
                <span><i class="fa-solid fa-magnifying-glass"></i></span> Información de Jurados y Sustentación
            </h3>
            <p>
                Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de consulta de jurados asignados. En este espacio podrás conocer quiénes han sido designados como jurados para evaluar tu proyecto de grado, así como la fecha programada para tu sustentación. Esta información es clave para que te prepares adecuadamente y tengas en cuenta los tiempos establecidos en el proceso académico.
            </p>
        </div>

    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "ver-documentos-anteproyectos-asesor") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-magnifying-glass"></i></span> Consulta Retroalimentaciones</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de consulta de retroalimentaciones para estudiantes. En este espacio podrás visualizar los documentos del anteproyecto y consultar todas las retroalimentaciones proporcionadas por los directores. Aquí encontrarás observaciones previas y recomendaciones para guiarte en el proceso de mejora de tu trabajo, asegurando que cada versión refleje el progreso y los ajustes necesarios. </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "consultar-ideas") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fas fa-book"></i></span> Consulta de ideas de anteproyectos </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de Consulta de Ideas de Anteproyectos. En este módulo, podrás únicamente consultar la información básica de las ideas de anteproyectos, facilitando su visualización y seguimiento dentro del sistema. No tienes permisos para registrar, editar o eliminar los datos.</p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "configuration-user") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-gear"></i></span> Configuración usuarios</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de configuración de usuario. En este espacio podrás actualizar tu información personal, gestionar tus datos de contacto y ajustar tus preferencias según tus necesidades. Este módulo te permite mantener toda tu información al día, asegurando una experiencia personalizada y adaptada a tus requerimientos dentro de la plataforma.</p>
        </div>


    <?php


    } else if (isset($ruta[0]) && $ruta[0] == "cargar-docuemento-user") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-upload"></i></span> Cargar documentos anteproyectos o Proyectos </h3>
            <?php

            if ($_SESSION['privilegio'] == 3) {
            ?>
                <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de carga de documentos de anteproyectos. En este espacio, los estudiantes podrán cargar sus documentos relacionados con sus proyectos o anteproyectos. Desde aquí, podrás subir informes, avances, presentaciones y cualquier otro material necesario para la revisión y seguimiento académico. Asegúrate de que todos los archivos estén correctamente organizados y actualizados para facilitar su evaluación por parte de los directores.</p>
                <h4>Instrucciones para la Subida de documentos en PDF y Word</h4>
                <p>Para facilitar la revisión y retroalimentación de tu anteproyecto, es necesario que subas dos versiones de tu documento:</p>
            <?php
            } else if ($_SESSION['privilegio'] == 4) {
            ?>
                <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de carga de documentos de proyectos. En este espacio, los estudiantes podrán cargar sus documentos relacionados con sus proyectos o anteproyectos. Desde aquí, podrás subir informes, avances, presentaciones y cualquier otro material necesario para la revisión y seguimiento académico. Asegúrate de que todos los archivos estén correctamente organizados y actualizados para facilitar su evaluación por parte de los directores.</p>
                <h4>Instrucciones para la Subida de documentos en PDF y Word</h4>
                <p>Para facilitar la revisión y retroalimentación de tu proyecto, es necesario que subas dos versiones de tu documento:</p>
            <?php
            }

            ?>

            <ul>
                <li><b>Versión en PDF:</b> Este archivo permitirá que el documento se visualice directamente en la aplicación, asegurando un formato consistente y fácil de leer.</li>
                <li class="mt-2"><b>Versión en Word:</b> Esta versión editable será útil para que el profesor pueda realizar comentarios, sugerencias o ediciones directamente en el texto, facilitando un proceso de retroalimentación detallado.</li>
            </ul>

        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "calificacion-jurados") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3">
                <span><i class="fa-solid fa-star"></i></span> Ítems de Evaluación del Proyecto
            </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>.
                A continuación, se presentan los criterios fundamentales que serán considerados en la evaluación del proyecto.
                Cada ítem refleja aspectos clave en la calidad, innovación y desarrollo del trabajo, asegurando una valoración objetiva y detallada.
                Agradecemos su criterio profesional en este proceso de evaluación.
            </p>
        </div>
    <?php

    }
}
if (isset($_SESSION['privilegio']) && $_SESSION['privilegio'] == 5 || $_SESSION['privilegio'] == 6) {

    if (isset($ruta[0]) && $ruta[0] == "home") {
    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-house"></i></span>Bienvenido</h3>
            <p>Hola, <b> <?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b> Bienvenido al Módulo de <?= $rol ?>. Este entorno ha sido diseñado para proporcionarte acceso integral a las herramientas y funcionalidades necesarias para optimizar la gestión de procesos y recursos de manera eficiente y estructurada. A través de este panel centralizado, podrás realizar retroalimentaciones a los diferentes anteproyectos o proyectos de grados, consultar ideas, consultar observaciones y ejecutar múltiples operaciones con el máximo control y precisión. </p>
        </div>

    <?php
    } else  if (isset($ruta[0]) && $ruta[0] == "anteproyectos-asignados-asesor") {
    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-magnifying-glass"></i></span>Consulta anteproyectos asignados </h3>
            <p>Hola, <b> <?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b> Bienvenido al Módulo de consulta de anteproyectos asignados. En este espacio podrás acceder a la lista completa de los anteproyectos que han sido asignados. Aquí encontrarás información detallada sobre cada anteproyecto, incluyendo los estudiantes responsables, el estado actual de cada propuesta. Este módulo te permitirá realizar un seguimiento eficiente del progreso y la asignación de cada anteproyecto. </p>
        </div>

    <?php
    } else  if (isset($ruta[0]) && $ruta[0] == "proyectos-asignados-asesor") {
    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-magnifying-glass"></i></span>Consulta proyectos asignados </h3>
            <p>Hola, <b> <?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b> Bienvenido al Módulo de consulta de proyectos asignados. En este espacio podrás acceder a la lista completa de los proyectos que han sido asignados. Aquí encontrarás información detallada sobre cada proyecto, incluyendo los estudiantes responsables, el estado actual de cada proyecto. Este módulo te permitirá realizar un seguimiento eficiente del progreso y la asignación de cada proyecto. </p>
        </div>

    <?php
    } else  if (isset($ruta[0]) && $ruta[0] == "entregas-anteproyectos") {
    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-eye"></i></span>Versiones documentos enviados por los estudiantes </h3>
            <p>Hola, <b> <?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b> Bienvenido al módulo de versiones de anteproyectos asignados. En este espacio podrás consultar todas las versiones de un anteproyecto, desde sus primeras entregas hasta la versión final. Cada versión estará organizada de manera cronológica, permitiendo un fácil acceso para revisión y comparación. Este módulo te asegura que todo el historial del desarrollo del anteproyecto esté disponible para un seguimiento detallado y exhaustivo. </p>
        </div>

    <?php
    } else  if (isset($ruta[0]) && $ruta[0] == "entregas-proyectos") {
    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-eye"></i></span>Versiones documentos enviados por los estudiantes </h3>
            <p>Hola, <b> <?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b> Bienvenido al módulo de versiones de los proyectos asignados. En este espacio podrás consultar todas las versiones de un proyecto, desde sus primeras entregas hasta la versión final. Cada versión estará organizada de manera cronológica, permitiendo un fácil acceso para revisión y comparación. Este módulo te asegura que todo el historial del desarrollo del proyecto esté disponible para un seguimiento detallado y exhaustivo. </p>
        </div>

    <?php
    } else  if (isset($ruta[0]) && $ruta[0] == "ver-documentos-anteproyectos-asesor") {
    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-magnifying-glass"></i></span>Ver documentos documentos enviados por estudiantes </h3>
            <p>Hola, <b> <?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b> Bienvenido al módulo de visualización de documentos. En este espacio podrás consultar los documentos del anteproyecto y realizar retroalimentaciones para mejorar su desarrollo. Además, tendrás acceso a todas las retroalimentaciones previas realizadas sobre ese anteproyecto, permitiendo un seguimiento detallado de las observaciones y mejoras sugeridas a lo largo del proceso.

            </p>
        </div>

    <?php
    } else  if (isset($ruta[0]) && $ruta[0] == "evidencias-reuniones") {
    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-upload"></i></span> Cargar evidencias </h3>
            <p>Hola, <b> <?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b> Bienvenido al módulo de carga de evidencias de reuniones de asesorías. En este espacio podrás cargar imágenes como evidencia de las reuniones de asesoría realizadas entre el profesor y los estudiantes para sus anteproyectos o proyectos de grado. Asegúrate de que las imágenes capturen los aspectos más importantes de cada sesión, permitiendo así un seguimiento visual de los avances y acuerdos logrados en el proceso de asesoría.

            </p>
        </div>

    <?php
    } else  if (isset($ruta[0]) && $ruta[0] == "ver-evidencia") {
    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-eye"></i></span> Observar evidencias </h3>
            <p>Hola, <b> <?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b> Bienvenido al módulo de visualización de evidencias de asesorías. En este espacio podrás consultar las imágenes cargadas como evidencia de las reuniones de asesoría realizadas para los anteproyectos o proyectos de grado. Aquí encontrarás un registro visual de cada sesión, permitiendo revisar los avances, acuerdos y aspectos clave documentados en el proceso de orientación académica..

            </p>
        </div>

    <?php
    } else if (isset($ruta[0]) && $ruta[0] == "configuration-user") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-gear"></i></span> Configuración usuarios</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de configuración de usuario. En este espacio podrás actualizar tu información personal, gestionar tus datos de contacto y ajustar tus preferencias según tus necesidades. Este módulo te permite mantener toda tu información al día, asegurando una experiencia personalizada y adaptada a tus requerimientos dentro de la plataforma.</p>
        </div>


    <?php


    } else if (isset($ruta[0]) && $ruta[0] == "consultar-ideas") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fas fa-book"></i></span> Consulta de ideas de anteproyectos </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de Consulta de Ideas de Anteproyectos. Desde este módulo, tendrás la posibilidad de consultar la información básica de las ideas de anteproyectos registrada dentro del sistema. </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "consultar-proyectos") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fas fa-book"></i></span> Consulta de proyectos </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de Consulta de proyectos. Desde este módulo, tendrás la posibilidad de consultar la información básica de los proyectos registrada dentro del sistema. </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "proyectos-asignados-jurados") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fas fa-book"></i></span> Consulta de proyectos asignado jurado </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de proyectos asignados como jurado. En esta sección podrás consultar todos los proyectos que se te han asignado para evaluación en calidad de jurado. Aquí tendrás acceso a los detalles de cada proyecto, facilitando el seguimiento, la revisión y la preparación para brindar retroalimentación adecuada y objetiva. Además, tendrás la opción de calificar cada uno de los proyectos el día de la presentación, asegurando un proceso de evaluación justo y completo. ¡Prepárate para contribuir con tu experiencia al éxito de estas iniciativas académicas!</p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "calificar-proyectos") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fas fa-book"></i></span> Consulta de proyectos asignado jurado </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de calificación de proyectos. En este espacio podrás evaluar y asignar calificaciones a los proyectos presentados, asegurando que cada propuesta reciba una valoración justa y detallada. Aquí tendrás acceso a todas las herramientas necesarias para registrar tus observaciones, puntuaciones y comentarios, contribuyendo así al desarrollo académico de cada estudiante. Tu opinión como evaluador es fundamental para el éxito de este proceso.</p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "consultar-horas-asesores") {


    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3"> <span><i class="fa-solid fa-users"></i></span> Consultar horas asesores</h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>. Bienvenido al módulo de consulta y gestión de horas de asesores . En este espacio podrás consultar las horas asignadas a los asesores, asegurando una administración eficiente y flexible de sus horarios. </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "calificacion-jurados") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3">
                <span><i class="fa-solid fa-star"></i></span> Ítems de Evaluación del Proyecto
            </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>.
                A continuación, se presentan los criterios fundamentales que serán considerados en la evaluación del proyecto.
                Cada ítem refleja aspectos clave en la calidad, innovación y desarrollo del trabajo, asegurando una valoración objetiva y detallada.
                Agradecemos su criterio profesional en este proceso de evaluación.
            </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "asesor-metodologico") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3">
                <span><i class="fa-solid fa-star"></i></span> Ítems de Evaluación del Proyecto
            </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>.
                A continuación, se presentan los criterios fundamentales que serán considerados en la evaluación del proyecto.
                Cada ítem refleja aspectos clave en la calidad, innovación y desarrollo del trabajo, asegurando una valoración objetiva y detallada.
                Agradecemos su criterio profesional en este proceso de evaluación.
            </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "acta-proyectos") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3">
                <span><i class="fa-solid fa-file-alt"></i></span> Acta del Proyecto de Grado
            </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>.
                A continuación, se presenta el acta oficial del proyecto de grado, un documento fundamental que certifica el desarrollo, evaluación y seguimiento del trabajo realizado.
                En este acta se registran aspectos clave como la aprobación, observaciones y decisiones tomadas durante el proceso.
                Es importante revisar detalladamente la información contenida, ya que forma parte integral del proceso académico.
            </p>
        </div>
    <?php

    } else if (isset($ruta[0]) && $ruta[0] == "ver-documentos-proyectos-asesor") {

    ?>
        <div class="content-text-information">
            <h3 class="title-section-users mt-3 mb-3">
                <span><i class="fa-solid fa-folder-open"></i></span> Documentos enviados por los estudiantes
            </h3>
            <p>Hola, <b><?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?></b>.
                Bienvenido al módulo de gestión de documentos. 📂✨ En esta sección podrás visualizar todos los documentos enviados por los estudiantes para su respectiva revisión y evaluación.

                Aquí encontrarás las entregas organizadas de manera clara, lo que te permitirá acceder a cada versión con facilidad y realizar un seguimiento detallado del progreso del proyecto. Este módulo facilita la supervisión de cada etapa del proceso académico, asegurando un control efectivo y una retroalimentación precisa.

                🔍 **Explora los documentos y brinda el acompañamiento necesario para la mejora continua de cada proyecto.**
            </p>
        </div>
<?php


    }
}



?>