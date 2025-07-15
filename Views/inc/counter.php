<script>
    function iniciarCuentaRegresiva(fechaLimite, elementoId, mostrarContador) {
        let elemento = document.getElementById(elementoId);

        // Si mostrarContador no es 1, mostrar todo en cero
        if (mostrarContador !== 1) {
            elemento.innerHTML = "📅 0d 0h 0m 0s";
            return;
        }

        let fechaObjetivo = new Date(fechaLimite).getTime();

        let intervalo = setInterval(function () {
            let ahora = new Date().getTime();
            let tiempoRestante = fechaObjetivo - ahora;

            if (tiempoRestante <= 0) {
                elemento.innerHTML = "⏳ ¡Tiempo finalizado!";
                elemento.classList.add("poco-tiempo");
                clearInterval(intervalo);
                return;
            }

            let dias = Math.floor(tiempoRestante / (1000 * 60 * 60 * 24));
            let horas = Math.floor((tiempoRestante % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutos = Math.floor((tiempoRestante % (1000 * 60 * 60)) / (1000 * 60));
            let segundos = Math.floor((tiempoRestante % (1000 * 60)) / 1000);

            elemento.innerHTML = `📅 ${dias}d ${horas}h ${minutos}m ${segundos}s`;

            // Si quedan menos de 24 horas, cambia el color a rojo con animación
            if (dias === 0 && horas < 24) {
                elemento.classList.add("poco-tiempo");
            }
        }, 1000);
    }

    document.addEventListener("DOMContentLoaded", function () {
        let fechaSubida = "<?=$fecha_subida?>"; // Fecha de subida
        let fechaLimite = "<?=$fecha_limite?>"; // Fecha límite (+7 días)
        let mostrarContador = <?= $mostrarContador ?>; // 1 para mostrar, otro valor para ocultar

        iniciarCuentaRegresiva(fechaLimite, "contador", mostrarContador);
    });
</script>
