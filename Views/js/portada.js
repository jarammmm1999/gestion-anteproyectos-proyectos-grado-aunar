document.addEventListener("DOMContentLoaded", function() {
    const modal = document.querySelector(".modal-cargar-imagenes-portadas");
    const btnAbrir = document.querySelector(".btn-abrir-cargar-imagenes-portadas");
    const btnCerrar = document.querySelector(".cerrar-modal-cargar-imagenes-portadas");
    const dropzone = document.getElementById("dropzone");
    const inputImagenes = document.getElementById("input-imagenes");
    const vistaPrevia = document.getElementById("vista-previa");
    let archivos = [];

    // Abrir modal
    btnAbrir.addEventListener("click", () => modal.style.display = "flex");

    // Cerrar modal
    btnCerrar.addEventListener("click", () => modal.style.display = "none");

    // Manejo del área de dropzone
    dropzone.addEventListener("click", () => inputImagenes.click());

    dropzone.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropzone.style.background = "#f0f8ff";
    });

    dropzone.addEventListener("dragleave", () => dropzone.style.background = "white");

    dropzone.addEventListener("drop", (e) => {
        e.preventDefault();
        dropzone.style.background = "white";
        handleFiles(e.dataTransfer.files);
    });

    inputImagenes.addEventListener("change", () => handleFiles(inputImagenes.files));

    function handleFiles(files) {
        vistaPrevia.innerHTML = ""; // Limpiar la vista previa
        archivos = []; // Reiniciar el array

        for (let file of files) {
            if (file.type.startsWith("image/")) {
                archivos.push(file);
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement("div");
                    div.classList.add("card-imagen-portada");
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Imagen">
                        <button class="btn-eliminar-imagen">&times;</button>
                    `;
                    div.querySelector(".btn-eliminar-imagen").addEventListener("click", () => {
                        archivos = archivos.filter(a => a !== file);
                        div.remove();
                    });
                    vistaPrevia.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        }
    }
});










 function mostrarDetallesUsuarios(url, id_programa) {

        fetch(url + `Views/content/obtener_usuarios_por_programa.php?id_programa=${id_programa}`)
            .then(response => response.json())
            .then(data => {
                const detallesUsuarios = document.getElementById('detallesUsuarios');
                detallesUsuarios.innerHTML = '';

                if (data.length > 0) {
                    data.forEach(usuario => {
                        const usuarioDiv = document.createElement('div');
                        usuarioDiv.classList.add('usuario');
                        usuarioDiv.innerHTML = `
                        <p><strong>Imagen:</strong> <img src="${url}/Views/assets/images/avatar/${usuario.imagen_usuario}" alt="Imagen de ${usuario.nombre_usuario}" width="50"></p>
                        <p><strong>Nombre:</strong> ${usuario.nombre_usuario} ${usuario.apellidos_usuario}</p>
                        <p><strong>Correo:</strong> ${usuario.correo_usuario}</p>
                        <p><strong>Telefono:</strong> ${usuario.telefono_usuario}</p>
                         <p><strong>Rol usuario:</strong> ${usuario.nombre_rol}</p>
        `;
                        detallesUsuarios.appendChild(usuarioDiv);
                    });
                } else {
                    detallesUsuarios.innerHTML = '<p>No hay usuarios asignados.</p>';
                }


                document.getElementById('detallesUsuariosModal').style.display = 'flex';
            })
            .catch(error => console.error('Error:', error));
    }

    function cerrarDetallesModal() {
        document.getElementById('detallesUsuariosModal').style.display = 'none';
    }
