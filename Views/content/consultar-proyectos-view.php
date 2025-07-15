
<div class="container-fluid">
    <div class="container-table-user">
        <div class="continer-search mt-5 mb-3">
            <input type="text" id="buscar" onkeyup="buscarTabla()" placeholder="Buscar en la tabla...">
        </div>
        <?php
        require_once "./Controller/UsuarioControlador.php";
        $ins_usuarioControlador = new UsuarioControlador();
        echo $ins_usuarioControlador->paginar_proyectos_controlador($url_pagina[1], 100, $_SESSION['privilegio'],  $url_pagina[0],$_SESSION['numero_documento']);
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
                                <td>${usuario.numero_documento}</td>
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
        var url = ruta + "Views/content/livesearch.php?codigo_proyecto=" + codigoAnteproyecto;
        xmlhttp.open("GET", url, true);
        xmlhttp.send();

    }
</script>