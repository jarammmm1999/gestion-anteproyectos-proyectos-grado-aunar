const mensajesSpinner = [
    "Procesando la información...",
    "Esto puede tardar un poco, por favor espera...",
    "Estamos validando tus datos...",
    "Casi listo, por favor ten paciencia...",
    "Preparando todo para ti...",
    "Realizando comprobaciones finales...",
    "Por favor, no cierres esta ventana...",
    "Estamos trabajando en ello...",
    "Un momento más, estamos casi allí...",
    "Tus datos están siendo revisados...",
    "Optimización en progreso, gracias por tu paciencia...",
    "Asegurándonos de que todo esté correcto...",
    "Tu solicitud está siendo procesada...",
    "Dale un segundo, estamos terminando...",
    "Revisando cada detalle, no tardaremos mucho...",
    "Analizando la información con precisión...",
    "Cargando los datos necesarios, paciencia...",
    "Verificando credenciales, por favor espera...",
    "Estamos configurando todo para ti...",
    "Sincronizando con el sistema, esto tomará unos segundos...",
    "Optimizando los recursos para una mejor experiencia...",
    "Comprobando la integridad de los datos...",
    "Procesando en segundo plano, seguimos avanzando...",
    "Estamos afinando los últimos detalles...",
    "Verificación en curso, no tardaremos mucho...",
    "Tus datos están siendo organizados...",
    "Revisando permisos y accesos...",
    "Casi terminamos, solo un poco más...",
    "Ultimando detalles para garantizar la mejor precisión...",
    "Finalizando el proceso, gracias por tu paciencia..."
];


let mensajeIndex = 0;
let intervaloMensajes;

// Función para mostrar el spinner y cambiar el texto dinámicamente
function mostrarSpinner() {
    const spinnerContainer = document.getElementById("spinner-container");
    const spinnerText = document.getElementById("spinner-text");

    if (spinnerContainer && spinnerText) {
        spinnerContainer.style.visibility = "visible";
        spinnerText.textContent = mensajesSpinner[mensajeIndex]; // Mostrar el primer mensaje

        // Cambiar el texto cada 3 segundos
        intervaloMensajes = setInterval(() => {
            mensajeIndex = (mensajeIndex + 1) % mensajesSpinner.length; // Incrementar y reiniciar si es necesario
            spinnerText.textContent = mensajesSpinner[mensajeIndex];
        }, 4000); // Cambia el mensaje cada 3 segundos
    }
}

// Función para ocultar el spinner
function ocultarSpinner() {
    const spinnerContainer = document.getElementById("spinner-container");

    if (spinnerContainer) {
        spinnerContainer.style.visibility = "hidden";
        clearInterval(intervaloMensajes); // Detener el cambio de mensajes
    }
}

function formulario_ajax_login(e) {
    e.preventDefault();
    let data = new FormData(this);
    let method = this.getAttribute("method");
    let action = this.getAttribute("action");

    mostrarSpinner();

    fetch(action, {
        method: method,
        body: data,
        headers: new Headers(),
        mode: 'cors',
        cache: 'no-cache'
    })
        .then((respuesta) => respuesta.json())
        .then((respuesta) => {
            ocultarSpinner();


            if (respuesta.Alerta === "mostrar_modal") {
                mostrarModalRoles(respuesta.Perfiles, respuesta.url );
            } else if (respuesta.Alerta === "redireccionar") {
                window.location.href = respuesta.URL;
            } else {
                alert_ajax(respuesta);
            }
        })
        .catch((error) => {
            ocultarSpinner();
            console.error("Error en la solicitud:", error);
        });
}

function mostrarModalRoles(perfiles, url) {
    const container = document.getElementById("roles-container");
    container.innerHTML = ""; // Limpia el contenido previo del contenedor

    perfiles.forEach((perfil) => {
        const card = document.createElement("div");
        card.className = "card-role";
        card.innerHTML = `
            <img src="${url}/Views/assets/images/avatar.png" alt="${perfil.nombre_rol}" />
            <h4>${perfil.nombre_rol}</h4>
            <form class="FormulariosAjaxLogin" method="POST" action="${url}Ajax/LoginAjax.php">
                <input type="hidden" name="DocumentoUserLog" value="${document.querySelector('[name=DocumentoUserLog]').value}">
                <input type="hidden" name="passwordUserLog" value="${document.querySelector('[name=passwordUserLog]').value}">
                <input type="hidden" name="rolSeleccionado" value="${perfil.id_rol}">
                <button type="submit" class="btn-seleccionar">Seleccionar</button>
            </form>
        `;
        container.appendChild(card);
    });

    // Asegurar que solo se agrega un listener para evitar múltiples eventos
    document.querySelectorAll(".FormulariosAjaxLogin").forEach((form) => {
         console.log("📌 Acción del formulario en Hostinger:", form.getAttribute("action"));
        form.addEventListener("submit", function (e) {
            e.preventDefault(); // Evitar recarga de la página
            cerrarModalSeleccion(); // Cerrar el modal antes de hacer el fetch

            let data = new FormData(this);
            let action = this.getAttribute("action");
            let method = this.getAttribute("method");

            fetch(action, {
                method: method,
                body: data,
                headers: new Headers(),
                mode: "cors",
                cache: "no-cache",
            })
                .then((respuesta) => respuesta.json())
                .then((respuesta) => {
              

                    if (respuesta.Alerta === "redireccionar") {
                        window.location.href = respuesta.URL;
                    } else {
                        alert_ajax(respuesta); // Mostrar alertas correctamente
                    }
                })
                .catch((error) => {
                    console.error("Error en la solicitud:", error);
                   
                });
        });
    });

    // Muestra el modal
    document.getElementById("modal-seleccion-perfil").classList.add("visible");
}

// Función para cerrar el modal antes de enviar el formulario
function cerrarModalSeleccion() {
    document.getElementById("modal-seleccion-perfil").classList.remove("visible");
}



function cerrarModalPerfiles() {
    const modal = document.getElementById("modal-seleccion-perfil");
    if (modal) {
        modal.classList.remove("visible");
    }
}


// Asignar evento de envío a los formularios de inicio de sesión
const formulario_ajax_loginOne = document.querySelectorAll(".FormulariosAjaxLogin");
formulario_ajax_loginOne.forEach((form) => {
    form.addEventListener("submit", formulario_ajax_login);
});


// Función para manejar otros formularios
function enviar_formularios_ajax(e) {
    e.preventDefault();
    let data = new FormData(this);
    let method = this.getAttribute("method");
    let action = this.getAttribute("action");
    let tipo = this.getAttribute("data-form");

    let texto_alerta = tipo === "save" ? "Los datos quedarán guardados en el sistema" :
                      tipo === "delete" ? "Los datos serán eliminados completamente del sistema" :
                      tipo === "update" ? "Los datos serán actualizados" :
                      "¿Quieres realizar la operación solicitada?";

    Swal.fire({
        title: '¿Estás seguro?',
        text: texto_alerta,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#034873",
        cancelButtonColor: "#d33",
        confirmButtonText: "Aceptar"
    }).then((result) => {
        if (result.isConfirmed) {
            mostrarSpinner();

            fetch(action, {
                method: method,
                body: data,
                headers: new Headers(),
                mode: 'cors',
                cache: 'no-cache'
            })
            .then(respuesta => respuesta.json())
            .then(respuesta => {
                ocultarSpinner();
                alert_ajax(respuesta);
            })
            .catch(error => {
                ocultarSpinner();
                console.error("Error en la solicitud:", error);
            });
        }
    });
}

// Asignar evento de envío a los demás formularios
const formularios_ajax = document.querySelectorAll(".FormulariosAjax");
formularios_ajax.forEach(form => {
    form.addEventListener("submit", enviar_formularios_ajax);
});



// Función para mostrar alertas
function alert_ajax(alerta) {
    if (alerta.Alerta === "simple") {
        Swal.fire({
            title: alerta.Titulo,
            text: alerta.Texto,
            icon: alerta.Tipo,
            confirmButtonText: 'Aceptar'
        });
    } else if (alerta.Alerta === "Recargar") {
        Swal.fire({
            title: alerta.Titulo,
            text: alerta.Texto,
            icon: alerta.Tipo,
            confirmButtonText: "Aceptar"
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
        });
    } else if (alerta.Alerta === "limpiar") {
        Swal.fire({
            title: alerta.Titulo,
            text: alerta.Texto,
            icon: alerta.Tipo,
            confirmButtonText: "Aceptar"
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector(".FormulariosAjax").reset();
            }
        });
    } else if (alerta.Alerta === "redireccionar") {
        window.location.href = alerta.URL;
    } else if (alerta.Alerta === "errores") {
        let delay = 0; // Tiempo inicial
        let cancelAlerts = false; // Variable para cancelar alertas futuras
    
        alerta.Errores.forEach((errorItem, index) => {
            const timeoutId = setTimeout(() => {
                if (cancelAlerts) return; // Si se pasa el mouse sobre una alerta, no se ejecutan más
    
                Swal.fire({
                    toast: true,
                    position: "top-end",
                    icon: errorItem.icono || alerta.Tipo, // Usa el ícono individual si está, o el general
                    title: errorItem.mensaje || errorItem, // Usa el mensaje si es objeto, o directamente el string
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener("mouseenter", () => {
                            cancelAlerts = true;
                        });
                    }
                });
            }, delay);
    
            delay += 3500;
        });
    }
    
    
    
    
    
    
}


