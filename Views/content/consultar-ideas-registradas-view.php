<div class="title-head">
    <h1>Gestión de anteproyectos y proyectos de grados - Autónoma de nariño</h1>
</div>
<div class="content-text-information">
    <h3 class="title-section-users mt-3 mb-3"> 
        <span><i class="fa-solid fa-folder-open"></i></span> Repositorio de Proyectos de Grado en Postulación
    </h3>
    <p>📚 Bienvenido al repositorio institucional de proyectos de grado en fase de postulación. Aquí encontrarás una recopilación organizada de las propuestas académicas presentadas por estudiantes que aspiran a su titulación. Este módulo te permite acceder a información general como el código del proyecto, título, palabras clave, facultad y programa académico, promoviendo así la innovación y evitando la repetición de ideas. 💡🎓</p>

    <p>✅ Los documentos completos solo estarán disponibles para aquellos proyectos que **han sido aprobados oficialmente** y que se encuentran en **etapa de sustentación**. Estos documentos pueden ser consultados por otros estudiantes como **referencia o guía estructural**, sirviendo de apoyo para el desarrollo de futuras propuestas académicas. 📄📘</p>

    <p>🔐 Ten presente que los proyectos aún no aprobados o que están en fases previas al aval institucional permanecerán restringidos, salvaguardando la integridad y confidencialidad del proceso académico. 🛡️📑</p>
</div>



<div class="container-fluid">
  <div class="container-table-user">
    <div class="continer-search mt-5 mb-3">
      <input type="text"  id="buscar" onkeyup="buscarTabla()" placeholder="Buscar en la tabla...">
    </div>
    <?php
    require_once "./Controller/UsuarioControlador.php";
    $ins_usuarioControlador = new UsuarioControlador();
    echo $ins_usuarioControlador->pagina_ideas_registradas_controlador($url_pagina[1], 10, $url_pagina[0]);
    ?>
  </div>
</div>

<script>
    function mostrarUsuariosProyecto(codigoAnteproyecto, tituloAnteproyecto, ruta) {
        // Actualizar el título del modal con el título del anteproyecto
        document.getElementById("modal-titulo").textContent = " " + tituloAnteproyecto;

        // Limpiar la tabla de usuarios antes de agregar nuevos datos
        document.getElementById("tabla-usuarios-registrados").innerHTML = "";

        // Crear la instancia de XMLHttpRequest
        var xmlhttp = new XMLHttpRequest();

        // Definir el evento onreadystatechange
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                try {
                    // Parsear la respuesta JSON
                    var usuarios = JSON.parse(this.responseText);

                    // Verificar si la respuesta es un array y si tiene longitud mayor a 0
                    if (Array.isArray(usuarios) && usuarios.length > 0) {
                        let filas = "";
                        // Recorrer cada usuario y agregarlo a la tabla
                        usuarios.forEach(usuario => {
                            filas += `<tr>
                                <td>${usuario.nombre}</td>
                                <td>${usuario.apellidos}</td>
                                <td>${usuario.correo}</td>
                            </tr>`;
                        });
                        // Insertar las filas en la tabla del modal
                        document.getElementById("tabla-usuarios-registrados").innerHTML = filas;
                    } else {
                        // Mostrar un mensaje si no hay usuarios asignados
                        document.getElementById("tabla-usuarios-registrados").innerHTML = '<tr><td colspan="4"> <div class=" text-center">No hay usuarios asignados a este proyecto.</div> </td></tr>';
                    }

                } catch (e) {
                    // Si hay un error en el parsing o en la estructura de datos, mostrar un mensaje de error
                    document.getElementById("tabla-usuarios-registrados").innerHTML = '<tr><td colspan="4">Error al procesar la respuesta del servidor.</td></tr>';
                }
            }
        };


        // Hacer la solicitud GET al servidor
        var url = ruta + "Views/content/livesearch.php?codigo_anteproyecto=" + codigoAnteproyecto;
        xmlhttp.open("GET", url, true);
        xmlhttp.send();

    }
</script>