




document.addEventListener('DOMContentLoaded', () => {
    // Inicializar valores por defecto en los campos visibles
    document.getElementById('valor_total_a').innerText = "0.0";
    document.getElementById('valor_total_b').innerText = "0.0";

    // Variables para almacenar datos
    const datosEvaluacion = {};
    const registrosExtras = {};
    let acumuladoPrincipal = 0;
    let acumuladoSecundario = 0;

    function recalcularTotales() {
        acumuladoPrincipal = Object.values(datosEvaluacion).reduce((suma, num) => suma + num, 0);
        acumuladoSecundario = Object.values(registrosExtras).reduce((suma, num) => suma + num, 0);

        // Verificar existencia de los elementos antes de modificarlos
        if (document.getElementById('campo_oculto_a')) {
            document.getElementById('campo_oculto_a').value = acumuladoPrincipal.toFixed(1);
        }
        if (document.getElementById('campo_oculto_b')) {
            document.getElementById('campo_oculto_b').value = acumuladoSecundario.toFixed(1);
        }
        if (document.getElementById('valor_total_a')) {
            document.getElementById('valor_total_a').innerText = acumuladoPrincipal.toFixed(1);
        }
        if (document.getElementById('valor_total_b')) {
            document.getElementById('valor_total_b').innerText = acumuladoSecundario.toFixed(1);
        }

        sumarValores();
       
    }

    function cargarValoresIniciales() {
        document.querySelectorAll('.seleccion-opcion:checked').forEach((radio) => {
            const valorSeleccionado = radio.value;
            const idItem = parseInt(radio.getAttribute('data-registro'));

            // Eliminar valores anteriores antes de asignar nuevos
            datosEvaluacion[idItem] = 0.0;
            registrosExtras[idItem] = 0.0;

            if (valorSeleccionado === '2') {
                if (idItem >= 1 && idItem <= 6) {
                    datosEvaluacion[idItem] = 0.5;
                    registrosExtras[idItem] = 0.1667;
                } else if (idItem >= 7 && idItem <= 11) {
                    datosEvaluacion[idItem] = 0.5;
                    registrosExtras[idItem] = 0.1667;
                } else if (idItem >= 12 && idItem <= 15) {
                    datosEvaluacion[idItem] = 0.5;
                    registrosExtras[idItem] = 0.16;
                }
            } else if (valorSeleccionado === '1') {
                if (idItem >= 1 && idItem <= 6) {
                    datosEvaluacion[idItem] = 1.0;
                    registrosExtras[idItem] = 0.3333;
                } else if (idItem >= 7 && idItem <= 11) {
                    datosEvaluacion[idItem] = 1.0;
                    registrosExtras[idItem] = 0.34;
                } else if (idItem >= 12 && idItem <= 15) {
                    datosEvaluacion[idItem] = 1.0;
                    registrosExtras[idItem] = 0.3333;
                }
            }
        });

        recalcularTotales();
    }


    // Obtener los botones tipo radio de la nueva clase
    const opcionesSeleccion = document.querySelectorAll('.seleccion-opcion');

    if (opcionesSeleccion.length === 0) {
        console.error("⚠️ No se encontraron elementos con la clase 'seleccion-opcion'.");
        return;
    }

    cargarValoresIniciales();

    opcionesSeleccion.forEach((opcion) => {
        opcion.addEventListener('change', (event) => {
            const eleccionUsuario = event.target.value;
            const identificador = parseInt(event.target.getAttribute('data-registro'));

            if (eleccionUsuario === '2') {
                if (identificador >= 1 && identificador <= 6) {
                    datosEvaluacion[identificador] = 0.5;
                    registrosExtras[identificador] = 0.1667;
                } else if (identificador >= 7 && identificador <= 11) {
                    datosEvaluacion[identificador] = 0.5;
                    registrosExtras[identificador] = 0.1667;
                }
                else if (identificador >= 11 && identificador <= 15) {
                    datosEvaluacion[identificador] = 0.5;
                    registrosExtras[identificador] = 0.16;
                } else {
                    datosEvaluacion[identificador] = 0.0;
                    registrosExtras[identificador] = 0.0;
                }
            } else if (eleccionUsuario === '1') {
                if (identificador >= 1 && identificador <= 6) {
                    datosEvaluacion[identificador] = 1.0;
                    registrosExtras[identificador] = 0.3333;
                } else if (identificador >= 7 && identificador <= 11) {
                    datosEvaluacion[identificador] = 1.0;
                    registrosExtras[identificador] = 0.34;
                } else if (identificador >= 11 && identificador <= 15) {
                    datosEvaluacion[identificador] = 1.0;
                    registrosExtras[identificador] = 0.3333;
                }
                else {
                    datosEvaluacion[identificador] = 0.0;
                    registrosExtras[identificador] = 0.0;
                }
            } else {
                switch (eleccionUsuario) {
                    case '3':
                        datosEvaluacion[identificador] = 0.0;
                        registrosExtras[identificador] = 0.0;
                        break;
                    default:
                        datosEvaluacion[identificador] = 0.0;
                        registrosExtras[identificador] = 0.0;
                }
            }

            recalcularTotales();
        });
    });

    // **Forzar evento 'change' en todos los radios marcados al inicio**
    document.querySelectorAll('.seleccion-opcion:checked').forEach((radio) => {
        radio.dispatchEvent(new Event('change'));
    });


});
