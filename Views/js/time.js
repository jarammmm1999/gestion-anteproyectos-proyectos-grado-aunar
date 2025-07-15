function sumarValores() {
    let valor1 = parseFloat(document.getElementById("input-total-acumulado").value) || 0;
    let valor2 = parseFloat(document.getElementById("campo_oculto_a").value) || 0;

    let sumaTotalFinal = valor1 + valor2;

    document.getElementById('resultado_evaluacion_jurado').innerText = sumaTotalFinal.toFixed(1);
}

document.addEventListener('DOMContentLoaded', () => {
    // Variables para almacenar valores
    const valoresAsignados = {};
    const otrosValoresAsignadosItem = {};

    function actualizarSumaTotal() {
        let sumaTotal = Object.values(valoresAsignados).reduce((acc, valor) => acc + valor, 0);
        let SumaItems = Object.values(otrosValoresAsignadosItem).reduce((acc, valor) => acc + valor, 0);

        document.getElementById('input-total-acumulado').value = sumaTotal.toFixed(1);
        document.getElementById('input-total-acumulado2').value = SumaItems.toFixed(1);
        document.getElementById('porcentaje_asignado1').innerText = sumaTotal.toFixed(1);
        document.getElementById('porcentaje_asignado2').innerText = SumaItems.toFixed(1);

        sumarValores();
    }

    // **Función para detectar opciones ya seleccionadas y actualizar valores**
    function cargarValoresIniciales() {
        document.querySelectorAll('.radio-opcion:checked').forEach((radio) => {
            const opcionSeleccionada = radio.value;
            const itemId = parseInt(radio.getAttribute('data-item'));

            if (opcionSeleccionada === '2') {
                if (itemId >= 1 && itemId <= 5) {
                    valoresAsignados[itemId] = 1.5;
                    otrosValoresAsignadosItem[itemId] = 0.12;
                } else if (itemId >= 6 && itemId <= 10) {
                    valoresAsignados[itemId] = 4.5;
                    otrosValoresAsignadosItem[itemId] = 0.38;
                }
            } else if (opcionSeleccionada === '1') {
                if (itemId >= 1 && itemId <= 5) {
                    valoresAsignados[itemId] = 3.0;
                    otrosValoresAsignadosItem[itemId] = 0.26;
                } else if (itemId >= 6 && itemId <= 10) {
                    valoresAsignados[itemId] = 9.0;
                    otrosValoresAsignadosItem[itemId] = 0.76;
                }
            }

        });

        actualizarSumaTotal(); // **Llamamos a la función para actualizar la suma desde el inicio**
    }

    // **Ejecutamos la función para cargar valores al iniciar**
    cargarValoresIniciales();

    // **Escuchar eventos de cambio en los radios**
    document.querySelectorAll('.radio-opcion').forEach((radio) => {
        radio.addEventListener('change', (event) => {
            const opcionSeleccionada = event.target.value;
            const itemId = parseInt(event.target.getAttribute('data-item'));

            valoresAsignados[itemId] = 0.0;
            otrosValoresAsignadosItem[itemId] = 0.0;

            if (opcionSeleccionada === '2') {
                if (itemId >= 1 && itemId <= 5) {
                    valoresAsignados[itemId] = 1.5;
                    otrosValoresAsignadosItem[itemId] = 0.12;
                } else if (itemId >= 6 && itemId <= 10) {
                    valoresAsignados[itemId] = 4.5;
                    otrosValoresAsignadosItem[itemId] = 0.38;
                }
            } else if (opcionSeleccionada === '1') {
                if (itemId >= 1 && itemId <= 5) {
                    valoresAsignados[itemId] = 3.0;
                    otrosValoresAsignadosItem[itemId] = 0.26;
                } else if (itemId >= 6 && itemId <= 10) {
                    valoresAsignados[itemId] = 9.0;
                    otrosValoresAsignadosItem[itemId] = 0.76;
                }
            }

            actualizarSumaTotal();
        });
    });
});
