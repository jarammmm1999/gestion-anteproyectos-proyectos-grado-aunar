function buscarTabla() {
    // Obtener el valor de búsqueda y eliminar espacios extra
    let input = document.getElementById("buscar");
    let filtro = input.value.toLowerCase().trim();

    // Buscar todas las tablas en la página
    let tablas = document.getElementsByTagName("table");
    if (tablas.length === 0) return; // Si no hay tablas, salir

    // Recorrer todas las tablas (por si hay más de una dependiendo del rol)
    for (let t = 0; t < tablas.length; t++) {
        let filas = tablas[t].getElementsByTagName("tr");

        // Recorrer todas las filas de la tabla
        for (let i = 1; i < filas.length; i++) { // Ignoramos el encabezado
            let celdas = filas[i].getElementsByTagName("td");
            let coincide = false;

            // Recorrer TODAS las celdas de la fila, sin importar cuántas haya
            for (let j = 0; j < celdas.length; j++) {
                if (celdas[j]) {
                    let texto = celdas[j].textContent || celdas[j].innerText;

                    // Incluir texto de etiquetas internas como <span>, <a>, etc.
                    let elementosInternos = celdas[j].querySelectorAll("*");
                    elementosInternos.forEach(el => {
                        texto += " " + (el.textContent || el.innerText);
                    });

                    // Si la celda contiene el texto buscado, la fila se mostrará
                    if (texto.toLowerCase().indexOf(filtro) > -1) {
                        coincide = true;
                        break; // Si ya encontró coincidencia, no sigue revisando
                    }
                }
            }

            // Mostrar u ocultar la fila según el resultado de la búsqueda
            filas[i].style.display = coincide ? "" : "none";
        }
    }
}



function buscarTarjetas() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const cards = document.querySelectorAll('.card-container .card');

    cards.forEach(card => {
        const cardHeader = card.querySelector('.card-header').textContent.toLowerCase();
        if (cardHeader.includes(filter)) {
            card.style.display = ''; // Mostrar tarjeta
        } else {
            card.style.display = 'none'; // Ocultar tarjeta
        }
    });
}


function generarContrasena() {
    const longitud = 10; // Longitud mínima 8, pero podemos hacerla más segura con 10+
    const caracteresMayusculas = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const caracteresMinusculas = "abcdefghijklmnopqrstuvwxyz";
    const caracteresNumeros = "0123456789";
    const caracteresEspeciales = "._+"; // Solo estos caracteres pasan el filtro PHP
    
    let contrasena = "";

    // Garantizamos al menos un carácter de cada tipo
    contrasena += caracteresMayusculas.charAt(Math.floor(Math.random() * caracteresMayusculas.length));
    contrasena += caracteresNumeros.charAt(Math.floor(Math.random() * caracteresNumeros.length));
    contrasena += caracteresEspeciales.charAt(Math.floor(Math.random() * caracteresEspeciales.length));

    // Rellenamos la contraseña con caracteres aleatorios
    const todosCaracteres = caracteresMayusculas + caracteresMinusculas + caracteresNumeros + caracteresEspeciales;
    for (let i = contrasena.length; i < longitud; i++) {
        contrasena += todosCaracteres.charAt(Math.floor(Math.random() * todosCaracteres.length));
    }

    // Mezclar la contraseña generada para mayor seguridad
    contrasena = contrasena.split('').sort(() => 0.5 - Math.random()).join('');

    // Insertar la contraseña en el input
    document.getElementById("password").value = contrasena;
    document.getElementById("password2").value = contrasena;
    document.getElementById("MostrarContrasena").innerText = contrasena;
}

function abrirModal(url, id_usuario_logueado) {
    const modal = document.getElementById("miChatModal");

    // Asegúrate de que la animación de salida esté eliminada
    modal.classList.remove("animate__fadeOut");

    // Añadir clases de animación de entrada
    modal.classList.add("animate__animated", "animate__fadeIn");

    // Mostrar el modal
    modal.style.display = "block";

    // Cargar la lista de usuarios
    cargarUsuarios(url, id_usuario_logueado);
}

// Cerrar el modal de chat y detener la recarga de mensajes
function cerrarModal() {
    document.getElementById("miChatModal").style.display = "none";
    if (intervalId) {
        clearInterval(intervalId); // Detener la actualización automática
        intervalId = null;
    }
}

// Función para cargar los usuarios y añadir el buscador
function cargarUsuarios(url, id_usuario_logueado) {
    fetch(url + `Views/content/cargar_usuarios.php?emisor_id=${id_usuario_logueado}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById("usuariosContainer");
            container.innerHTML = '';

            // Crear el contenedor para el buscador
            const buscadorContainer = document.createElement('div');
            buscadorContainer.id = 'miBuscadorContainer';
            
            // Crear el input de búsqueda
            const buscadorInput = document.createElement('input');
            buscadorInput.id = 'miBuscadorInput';
            buscadorInput.placeholder = 'Buscar usuario...';
            buscadorInput.addEventListener('input', function() {
                filtrarUsuarios(data, buscadorInput.value);
            });

            // Añadir el input de búsqueda al contenedor
            buscadorContainer.appendChild(buscadorInput);
            container.appendChild(buscadorContainer);  // Añadir el contenedor al contenedor de usuarios

            // Crear y agregar los usuarios
            data.forEach(usuario => {
                const item = document.createElement("div");
                item.className = "mi-usuarios-item";

                // Mostrar la imagen del usuario
                const img = document.createElement("img");
                img.src = url + "/Views/assets/images/avatar/" + usuario.imagen;
                img.className = "mi-usuario-imagen"; // Añadir una clase para estilos personalizados
                item.appendChild(img);

                // Mostrar el nombre del usuario
                const nombreSpan = document.createElement("span");
                nombreSpan.textContent = usuario.nombre;
                item.appendChild(nombreSpan);

                // Crear un círculo para mostrar la cantidad de mensajes no leídos
                if (usuario.mensajes_no_leidos > 0) {
                    const badgeNoLeido = document.createElement("span");
                    badgeNoLeido.className = "mi-mensaje-no-leido";
                    badgeNoLeido.textContent = usuario.mensajes_no_leidos;
                    item.appendChild(badgeNoLeido);
                }

                // Al hacer clic en un usuario, abrir la conversación y resaltar el usuario seleccionado
                item.onclick = () => {
                    abrirConversacion(url, id_usuario_logueado, usuario.id, usuario.nombre);
                    resaltarUsuarioSeleccionado(item);
                };

                container.appendChild(item);
            });
        })
        .catch(error => console.error('Error al cargar usuarios:', error));
}

// Función para filtrar los usuarios en función del texto ingresado en el input de búsqueda
function filtrarUsuarios(usuarios, texto) {
    const container = document.getElementById("usuariosContainer");
    container.innerHTML = '';

    const buscadorContainer = document.createElement('div');
    buscadorContainer.id = 'miBuscadorContainer';
    const buscadorInput = document.createElement('input');
    buscadorInput.id = 'miBuscadorInput';
    buscadorInput.placeholder = 'Buscar usuario...';
    buscadorInput.value = texto;
    buscadorInput.addEventListener('input', function() {
        filtrarUsuarios(usuarios, buscadorInput.value);
    });
    buscadorContainer.appendChild(buscadorInput);
    container.appendChild(buscadorContainer);

    // Filtrar usuarios según el texto del buscador
    const usuariosFiltrados = usuarios.filter(usuario => 
        usuario.nombre.toLowerCase().includes(texto.toLowerCase())
    );

    // Crear y agregar los usuarios filtrados
    usuariosFiltrados.forEach(usuario => {
        const item = document.createElement("div");
        item.className = "mi-usuarios-item";

        // Mostrar la imagen del usuario
        const img = document.createElement("img");
        img.src = url + "/Views/assets/images/avatar/" + usuario.imagen;
        img.className = "mi-usuario-imagen";
        item.appendChild(img);

        // Mostrar el nombre del usuario
        const nombreSpan = document.createElement("span");
        nombreSpan.textContent = usuario.nombre;
        item.appendChild(nombreSpan);

        // Crear un círculo para mostrar la cantidad de mensajes no leídos
        if (usuario.mensajes_no_leidos > 0) {
            const badgeNoLeido = document.createElement("span");
            badgeNoLeido.className = "mi-mensaje-no-leido";
            badgeNoLeido.textContent = usuario.mensajes_no_leidos;
            item.appendChild(badgeNoLeido);
        }

        item.onclick = () => {
            abrirConversacion(url, id_usuario_logueado, usuario.id, usuario.nombre);
            resaltarUsuarioSeleccionado(item);
        };

        container.appendChild(item);
    });
}


// Función para resaltar el usuario seleccionado
function resaltarUsuarioSeleccionado(elementoSeleccionado) {
    // Remover la clase 'seleccionado' de todos los usuarios
    const usuarios = document.querySelectorAll(".mi-usuarios-item");
    usuarios.forEach(usuario => usuario.classList.remove("seleccionado"));

    // Agregar la clase 'seleccionado' al usuario actual
    elementoSeleccionado.classList.add("seleccionado");
}

let selectedUserId = null; // ID del usuario receptor actual
let firstLoad = true; // Indicador para saber si es la primera carga
let displayedMessageIds = new Set(); // Conjunto de IDs de mensajes ya mostrados para evitar duplicación
let displayedDates = new Set(); // Conjunto de fechas ya mostradas para evitar duplicación de fechas

function abrirConversacion(url, id_usuario_logueado, idUsuario, nombreUsuario) {
    selectedUserId = idUsuario;
    currentChatUserId = idUsuario; // Asignar el ID del usuario con el que se está chateando actualmente
    document.getElementById("miChatHeader").innerText = `Conversación con ${nombreUsuario}`;
    firstLoad = true; // Reiniciar para que cargue el historial completo
    displayedDates.clear(); // Reiniciar el conjunto de fechas mostradas

    // Limpiar el contenedor de mensajes y reiniciar el conjunto de IDs
    const container = document.getElementById("miMensajesContainer");
    container.innerHTML = ''; // Limpiar los mensajes antiguos del contenedor
    displayedMessageIds.clear(); // Reiniciar el conjunto de IDs de mensajes

    // Llamar al script PHP para actualizar el estado de los mensajes a leídos
    fetch(`${url}Views/content/marcar_mensajes_leidos.php?id_usuario_logueado=${id_usuario_logueado}&id_usuario_conversacion=${idUsuario}`)
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error("Error al marcar mensajes como leídos:", data.error);
        }
    })
    .catch(error => console.error("Error al actualizar estado de mensajes:", error));

    // Cargar los mensajes de la conversación actual
    cargarMensajes(url, id_usuario_logueado, idUsuario);
}

function cargarMensajes(url, id_usuario_logueado, usuarioId) {
    if (usuarioId !== currentChatUserId) {
        return; // Detener si se ha cambiado de usuario
    }

    fetch(url + `Views/content/cargar_mensajes.php?emisor_id=${id_usuario_logueado}&receptor_id=${usuarioId}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById("miMensajesContainer");

            if (firstLoad) {
                container.innerHTML = ''; // Limpiar el contenedor solo en la primera carga
                firstLoad = false;
            }

            const isNearBottom = container.scrollTop + container.clientHeight >= container.scrollHeight - 10;

            if (!data.error) {
                data.forEach(mensaje => {
                    // Extraer la fecha del mensaje y la hora de envío
                    const opcionesFecha = { day: 'numeric', month: 'long', year: 'numeric' };
                    const messageDate = new Date(mensaje.fecha_envio).toLocaleDateString('es-ES', opcionesFecha);
                    const messageTime = new Date(mensaje.fecha_envio).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                    // Solo agregar la fecha si no ha sido mostrada antes
                    if (!displayedDates.has(messageDate)) {
                        const dateDiv = document.createElement("div");
                        dateDiv.className = "mi-mensaje-fecha";
                        dateDiv.textContent = messageDate;
                        container.appendChild(dateDiv);
                        displayedDates.add(messageDate);
                    }

                    if (!displayedMessageIds.has(mensaje.id)) {
                        const msgDiv = document.createElement("div");
                        msgDiv.id = `msg-${mensaje.id}`;
                        msgDiv.className = "mi-mensaje " + (mensaje.id_emisor == id_usuario_logueado ? "emisor" : "receptor");

                        // Crear el contenido del mensaje
                        const msgText = document.createElement("p");
                        msgText.textContent = mensaje.mensaje;
                        msgText.className = "mensaje-texto";
                        msgDiv.appendChild(msgText);

                        // Crear el elemento para la hora
                        const timeSpan = document.createElement("span");
                        timeSpan.className = "mensaje-hora";
                        timeSpan.textContent = messageTime;
                        msgDiv.appendChild(timeSpan);

                        container.appendChild(msgDiv);
                        displayedMessageIds.add(mensaje.id);
                    }
                });
            }

            if (isNearBottom) {
                container.scrollTop = container.scrollHeight;
            }

            setTimeout(() => cargarMensajes(url, id_usuario_logueado, usuarioId), 1000);
        })
        .catch(error => {
            console.error('Error al cargar mensajes:', error);
            const container = document.getElementById("miMensajesContainer");
            container.innerHTML = '';
            const errorMsg = document.createElement("div");
            errorMsg.className = "alert alert-danger text-center";
            errorMsg.textContent = "Error al cargar mensajes: " + error.message;
            container.appendChild(errorMsg);
            setTimeout(() => cargarMensajes(url, id_usuario_logueado, usuarioId), 1000);
        });
}

// Enviar mensaje al servidor sin agregarlo manualmente a la interfaz
function enviarMensaje(url, idEmisor) {
    const mensajeInput = document.getElementById("miMensajeInput");
    const mensaje = mensajeInput.value;

    if (mensaje.trim() && selectedUserId) {
        const emisorId = idEmisor; // ID del usuario logueado
        
        fetch(url + "Views/content/enviar_mensaje.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ emisor_id: emisorId, receptor_id: selectedUserId, mensaje: mensaje })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Limpiar el campo de entrada sin agregar el mensaje manualmente
                mensajeInput.value = "";
                // No es necesario agregar el mensaje aquí, ya que cargarMensajes lo actualizará en el próximo ciclo
            } else {
                console.error('Error al enviar el mensaje:', data.error);
            }
        })
        .catch(error => console.error('Error al enviar el mensaje:', error));
    }
}


document.addEventListener("DOMContentLoaded", function() {
    const dragArea = document.querySelector(".drag-area");
    const fileInput = document.querySelector("#archivo");
    const fileDisplay = document.querySelector(".file-display");
    const iconMap = {
        'pdf': 'fa-file-pdf',
        'doc': 'fa-file-word',
        'docx': 'fa-file-word',
        'png': 'fa-file-image',
        'jpg': 'fa-file-image',
        'jpeg': 'fa-file-image',
        'txt': 'fa-file-alt',
        'default': 'fa-file' // Icono predeterminado para otros tipos de archivo
    };

    // Mostrar los archivos cuando el usuario selecciona varios
    fileInput.addEventListener("change", function() {
        if (this.files.length) {
            displayFileInfo(this.files);
        }
    });

    // Evitar el comportamiento predeterminado al arrastrar archivos sobre la página
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dragArea.addEventListener(eventName, preventDefaults, false);
    });

    // Agregar clase al área cuando se arrastra el archivo sobre ella
    ['dragenter', 'dragover'].forEach(eventName => {
        dragArea.classList.add('highlight');
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dragArea.classList.remove('highlight');
    });

    // Función para evitar comportamientos predeterminados
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Mostrar nombres y iconos de cada archivo seleccionado
    function displayFileInfo(files) {
        fileDisplay.innerHTML = ''; // Limpiar el área de visualización

        Array.from(files).forEach(file => {
            const fileType = file.name.split('.').pop().toLowerCase();
            const iconClass = iconMap[fileType] || iconMap['default'];

            const fileInfo = `
                <div class="file-info">
                    <i class="fas ${iconClass}"></i>
                    <span>${file.name}</span>
                </div>
            `;
            fileDisplay.innerHTML += fileInfo; // Agregar cada archivo al contenedor
        });
    }

    // Al soltar archivos en el área, se agregan al input y se muestra la información
    dragArea.addEventListener("drop", (e) => {
        const files = e.dataTransfer.files;
        fileInput.files = files;  // Actualiza el input con los archivos
        if (files.length) {
            displayFileInfo(files);
        }
    });
});


document.addEventListener("DOMContentLoaded", function() {

    const firmaInput = document.getElementById("firma-input");
    const firmaDropzone = document.getElementById("firma-dropzone");
    const firmaPreview = document.getElementById("firma-preview");

    // Evento para cuando el usuario hace clic en la zona
    firmaDropzone.addEventListener("click", () => {
        firmaInput.click();
    });

    // Evento cuando se selecciona un archivo
    firmaInput.addEventListener("change", function () {
        mostrarVistaPrevia(this.files[0]);
    });

    // Evento para arrastrar y soltar
    firmaDropzone.addEventListener("dragover", (e) => {
        e.preventDefault();
        firmaDropzone.style.backgroundColor = "#d1e7ff";
    });

    firmaDropzone.addEventListener("dragleave", () => {
        firmaDropzone.style.backgroundColor = "#f8f9fa";
    });

    firmaDropzone.addEventListener("drop", (e) => {
        e.preventDefault();
        firmaDropzone.style.backgroundColor = "#f8f9fa";

        let archivo = e.dataTransfer.files[0];
        firmaInput.files = e.dataTransfer.files; // Asignar el archivo al input
        mostrarVistaPrevia(archivo);
    });

    // Función para mostrar la vista previa
    function mostrarVistaPrevia(archivo) {
        if (archivo && archivo.type.startsWith("image/")) {
            const reader = new FileReader();
            reader.onload = function (e) {
                firmaPreview.src = e.target.result;
                firmaPreview.style.display = "block";
            };
            reader.readAsDataURL(archivo);
        }
    }

});


document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.querySelector('.menu-toggle');
    const navbarRight = document.querySelector('.navbar-right');

    // Manejador de clic para el botón de menú hamburguesa
    menuToggle.addEventListener('click', function () {
        navbarRight.classList.toggle('active');
    });

    // Manejadores de clic para alternar menús desplegables
    const notificationIcon = document.querySelector('.notification-icon');
    const messageIcon = document.querySelector('.message-icon');
    const userMenu = document.querySelector('.user-down');

    notificationIcon.addEventListener('click', function (e) {
        e.stopPropagation(); // Evita que el clic cierre otros menús
        toggleDropdownMenu('.notification-menu');
    });

    messageIcon.addEventListener('click', function (e) {
        e.stopPropagation(); // Evita que el clic cierre otros menús
        toggleDropdownMenu('.message-menu');
    });

    userMenu.addEventListener('click', function (e) {
        e.stopPropagation(); // Evita que el clic cierre otros menús
        toggleDropdownMenu('.dropdown-menu');
    });

    // Función para alternar menús desplegables
    function toggleDropdownMenu(menuSelector) {
        const menus = document.querySelectorAll('.notification-menu, .message-menu, .dropdown-menu');
        menus.forEach(menu => {
            if (menu.matches(menuSelector)) {
                menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
            } else {
                menu.style.display = 'none'; // Cierra otros menús abiertos
            }
        });
    }

    // Cerrar menús desplegables al hacer clic fuera
    document.addEventListener('click', function () {
        const menus = document.querySelectorAll('.notification-menu, .message-menu, .dropdown-menu');
        menus.forEach(menu => menu.style.display = 'none');
    });
});


document.querySelector('.menu-toggle-user-visited').addEventListener('click', function() {
    document.querySelector('.container-main-pages').classList.toggle('menu-collapsed');
});

document.querySelector('.menu-close-user').addEventListener('click', function() {
    document.querySelector('.container-main-pages').classList.toggle('menu-collapsed');
});


var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})



const menuToggle = document.querySelector('.menu-toggle-user');
const menuClose = document.querySelector('.menu-close-user');
const linkContainer = document.querySelector('.link-container');

// Mostrar el menú al hacer clic en el botón hamburguesa
menuToggle.addEventListener('click', function() {
    linkContainer.classList.add('show');
});

// Cerrar el menú al hacer clic en la "X"
menuClose.addEventListener('click', function() {
    linkContainer.classList.remove('show');
});



// genera automaticamente un numero aleatorio para los anteproyectos y proyectos de grado
document.getElementById("buscarAnteproyecto").addEventListener("click", function() {
    // Generar un número aleatorio de 6 dígitos
    let codigoAleatorio = Math.floor(100000 + Math.random() * 900000);
    
    // Asignar el número generado al input
    document.getElementById("floatingDocumento").value = codigoAleatorio;
});


function filtrarUsuarios() {
    const input = document.getElementById('miBuscadorInput');
    const filter = input.value.toLowerCase();
    const usuarios = document.querySelectorAll('#usuariosContainer .mi-usuarios-item');

    usuarios.forEach(usuario => {
        const texto = usuario.textContent || usuario.innerText;
        if (texto.toLowerCase().indexOf(filter) > -1) {
            usuario.style.display = ""; // Muestra el usuario
        } else {
            usuario.style.display = "none"; // Oculta el usuario
        }
    });
}


document.getElementById('miMensajeInput').addEventListener('keydown', function(event) {
    // Verificar si la tecla presionada es "Enter"
    if (event.key === 'Enter') {
        event.preventDefault(); // Evitar el salto de línea
        document.getElementById('enviarBtn').click(); // Simular clic en el botón de enviar
    }
});


/*****************************pdf *********************************** */

async function downloadPDF(url) {
    // Crear un enlace temporal para descargar el PDF
    const link = document.createElement('a');
    link.href = url + 'Views/content/generar_pdf.php'; // Ruta al archivo PHP
    link.target = '_blank'; // Abrir en una nueva pestaña (opcional)
    link.click();
}
