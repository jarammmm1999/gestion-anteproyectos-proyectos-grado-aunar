<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Tiempo máximo de inactividad en milisegundos
    const tiempoInactividad = 3000000; 
    const tiempoAdvertencia = 10000; // 10 segundos antes del cierre

    let temporizadorInactividad;
    let intervaloActualizarSegundos; // Variable para manejar el intervalo de actualización

    // Función para cerrar la sesión automáticamente
    function cerrarSesion() {
        fetch('<?= SERVERURL ?>Ajax/CerrarSesionAjax.php', {
            method: 'POST'
        }).then(response => {
            if (response.ok) {
                // Redirigir al login
                window.location.href = '<?= SERVERURL ?>login/';
            }
        }).catch(error => {
            console.error('Error al cerrar la sesión:', error);
        });
    }

    // Función para mostrar el mensaje de advertencia
    function mostrarAdvertencia() {
        let segundosRestantes = tiempoAdvertencia / 1000;

        Swal.fire({
            title: 'Inactividad detectada',
            html: `Su sesión se cerrará automáticamente en <b>${segundosRestantes}</b> segundos.<br> ¿Desea continuar en la aplicación?`,
            icon: 'warning',
            timer: tiempoAdvertencia,
            timerProgressBar: true,
            showCancelButton: true, // Botón de cancelar
            confirmButtonText: 'Continuar',
            cancelButtonText: 'Cerrar sesión',
            allowOutsideClick: false, // No cerrar al hacer clic fuera de la alerta
            willOpen: () => {
                // Actualizar el texto de los segundos restantes
                intervaloActualizarSegundos = setInterval(() => {
                    segundosRestantes--;
                    Swal.getHtmlContainer().querySelector('b').textContent = segundosRestantes;
                }, 1000);
            },
            didClose: () => {
                clearInterval(intervaloActualizarSegundos); // Detener el intervalo cuando la alerta se cierre
            },
        }).then((result) => {
            if (result.isConfirmed) {
                // Si el usuario decide continuar, reiniciar los temporizadores
                reiniciarTemporizador();
            } else if (result.dismiss === Swal.DismissReason.timer || result.dismiss === Swal.DismissReason.cancel) {
                // Si el usuario cancela o el tiempo se agota, cerrar la sesión
                cerrarSesion();
            }
        }).catch(() => {
            // Si algo falla o el tiempo se agota, cerrar la sesión
            cerrarSesion();
        });
    }

    // Función para reiniciar el temporizador de inactividad
    function reiniciarTemporizador() {
        clearTimeout(temporizadorInactividad);
        clearInterval(intervaloActualizarSegundos); // Detener cualquier intervalo previo

        // Configurar el temporizador para mostrar advertencia
        temporizadorInactividad = setTimeout(() => {
            mostrarAdvertencia();
        }, tiempoInactividad - tiempoAdvertencia);
    }

    // Escuchar eventos de actividad del usuario
    document.addEventListener('mousemove', reiniciarTemporizador);
    document.addEventListener('keydown', reiniciarTemporizador);
    document.addEventListener('click', reiniciarTemporizador);

    // Iniciar el temporizador al cargar la página
    reiniciarTemporizador();
</script>
