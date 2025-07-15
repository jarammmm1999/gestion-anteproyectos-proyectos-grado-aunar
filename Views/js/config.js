const url = "http://localhost/proyectofinaljhon.shop/"; // cambiar a la URL de tu servidor

function seleccionarJurado(opcion) {
    document.getElementById("opcion_jurado").value = opcion;
    document.getElementById("opcion_jurado_mostrar").innerHTML = opcion;
}


document.addEventListener("DOMContentLoaded", function () {
    let btnFlotante = document.getElementById("btn-flotante");

    // Mostrar u ocultar el botón según el scroll
    window.addEventListener("scroll", function () {
        if (window.scrollY > 200) {
            btnFlotante.classList.remove("ocultar");
            btnFlotante.classList.add("mostrar");
        } else {
            btnFlotante.classList.remove("mostrar");
            btnFlotante.classList.add("ocultar");
        }
    });

    // Efecto de scroll suave al hacer clic
    btnFlotante.addEventListener("click", function () {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });

        // Ocultar el botón al llegar arriba
        setTimeout(() => {
            btnFlotante.classList.remove("mostrar");
            btnFlotante.classList.add("ocultar");
        }, 600); // Tiempo suficiente para que termine el scroll
    });
});


function mostrarContenedor(id) {
    let contenedores = document.querySelectorAll('.contenedor');
    contenedores.forEach(cont => cont.classList.remove('activo'));

    document.getElementById(id).classList.add('activo');
}

function generarContrasenaarchivo() {
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

    return  contrasena;
}

document.addEventListener("DOMContentLoaded", function () {
    const dropArea = document.getElementById("drop-area");
    const fileInput = document.getElementById("fileInput");
    const tableBody = document.getElementById("table-body");
    const hiddenInput = document.getElementById("hiddenData");
    const form = document.getElementById("fileUploadForm");

    let dataToSend = []; // Array para almacenar los datos

    // Detecta arrastre sobre el área
    dropArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropArea.classList.add("drag-over");
    });

    dropArea.addEventListener("dragleave", () => {
        dropArea.classList.remove("drag-over");
    });

    // Maneja el drop de archivos
    dropArea.addEventListener("drop", (e) => {
        e.preventDefault();
        dropArea.classList.remove("drag-over");

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            processFile(files[0]);
        }
    });

    // Activa el input oculto al hacer clic en el área
    dropArea.addEventListener("click", () => fileInput.click());

    // Detecta la selección de archivo
    fileInput.addEventListener("change", function () {
        if (fileInput.files.length > 0) {
            processFile(fileInput.files[0]);
        }
    });

    // Procesa el archivo y extrae los datos
    function processFile(file) {
        const reader = new FileReader();
        reader.readAsBinaryString(file);

        reader.onload = function (e) {
            const data = e.target.result;
            const workbook = XLSX.read(data, { type: "binary" });
            const sheet = workbook.Sheets[workbook.SheetNames[0]];
            const rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

            tableBody.innerHTML = ""; // Limpia la tabla
            dataToSend = []; // Reinicia el array

            rows.slice(1).forEach((row, index) => {
                if (row.length > 0) {

                    let tipoUsuario = row[5] || "";
                    let mensaje = "";

                    // Validación para mostrar un mensaje según el tipo de usuario
                    if (tipoUsuario == "1") {
                        mensaje = "Administrador";
                    } else if (tipoUsuario == "2") {
                        mensaje = "Coordinador";
                    } else  if (tipoUsuario == "3"){
                        mensaje = "Estudiante Anteproyecto";
                    }else  if (tipoUsuario == "4"){
                        mensaje = "Estudiante Proyecto";
                    }else  if (tipoUsuario == "5"){
                        mensaje = "Director";
                    }else  if (tipoUsuario == "6"){
                        mensaje = "Director Externo";
                    }

                    const rowData = {
                        numero_documento: row[0] || "",
                        nombre: row[1] || "",
                        apellidos: row[2] || "",
                        correo: row[3] || "",
                        telefono: row[4] || "",
                        tipo_usuario: row[5] || "",
                        contrasena: generarContrasenaarchivo() || "",
                    };

                    dataToSend.push(rowData); // Guarda en el array

                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${rowData.numero_documento}</td>
                        <td>${rowData.nombre}</td>
                        <td>${rowData.apellidos}</td>
                        <td>${rowData.correo}</td>
                        <td>${rowData.telefono}</td>
                        <td>${mensaje}</td>
                        <td>${rowData.contrasena}</td>
                        <td><button type="button" class="btn  btn-sm remove-row"><i class="fa-solid fa-trash"></i></button></td>
                    `;

                    tableBody.appendChild(tr);
                }
            });

            // Actualiza el input oculto
            hiddenInput.value = JSON.stringify(dataToSend);
        };
    }

    // Elimina una fila al hacer clic en el botón "Quitar"
    tableBody.addEventListener("click", function (e) {
        let btn = e.target.closest(".remove-row"); // Asegura que el clic viene del botón
        if (btn) {
            const row = btn.closest("tr"); // Encuentra la fila más cercana
            const index = Array.from(tableBody.children).indexOf(row);

            if (index > -1) {
                dataToSend.splice(index, 1); // Elimina del array
                hiddenInput.value = JSON.stringify(dataToSend); // Actualiza el input oculto
                row.remove(); // Elimina de la tabla
            }
        }
    });


    // Antes de enviar, asegura que los datos están en el input oculto
    form.addEventListener("submit", function () {
        hiddenInput.value = JSON.stringify(dataToSend);
    });
});



document.addEventListener("DOMContentLoaded", function () {
    const dropArea = document.getElementById("drop-area2");
    const fileInput = document.getElementById("fileInput2");
    const tableBody = document.getElementById("table-body2");
    const hiddenInput = document.getElementById("hiddenData2");
    const form = document.getElementById("fileUploadForm");
    const select1 = document.getElementById("select1");
    const select2 = document.getElementById("select2");


    let dataToSend = []; // Array para almacenar los datos

    // Detecta arrastre sobre el área
    dropArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropArea.classList.add("drag-over");
    });

    dropArea.addEventListener("dragleave", () => {
        dropArea.classList.remove("drag-over");
    });

    // Maneja el drop de archivos
    dropArea.addEventListener("drop", (e) => {
        e.preventDefault();
        dropArea.classList.remove("drag-over");

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            processFile(files[0]);
        }
    });

    // Activa el input oculto al hacer clic en el área
    dropArea.addEventListener("click", () => fileInput.click());

    async function fetchSelectData(url) {
        try {
            let response = await fetch(url);
            let data = await response.json();
            
            if (!Array.isArray(data)) {
                console.error("El JSON recibido no es un array:", data);
                return [];
            }
    
            return data;
        } catch (error) {
            console.error("Error al obtener datos:", error);
            return [];
        }
    }

    // Función para crear un select con opciones dinámicas
    function createSelect(options, name, textKey,idkey, onChangeCallback) {
        let select = document.createElement("select");
        select.name = name;
        select.innerHTML = '<option value="">Seleccione una opción</option>';

        options.forEach(item => {
            let option = document.createElement("option");
            option.value = item[idkey];
            option.textContent = item[textKey]; // Usa la clave dinámica
            select.appendChild(option);
        });

        if (onChangeCallback) {
            select.addEventListener("change", onChangeCallback);
        }

        return select;
    }

    function processFile(file, select1Data) {
        const reader = new FileReader();
        reader.readAsBinaryString(file);
    
        reader.onload = async function (e) {
            const data = e.target.result;
            const workbook = XLSX.read(data, { type: "binary" });
            const sheet = workbook.Sheets[workbook.SheetNames[0]];
            const rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });
    
            tableBody.innerHTML = ""; // Limpia la tabla
            dataToSend = []; // Reinicia el array
    
            for (let i = 1; i < rows.length; i++) {
                let row = rows[i];
                if (row.length > 0) {

                    let tipoUsuario = row[5] || "";
                    let mensaje = "";

                    // Validación para mostrar un mensaje según el tipo de usuario
                    if (tipoUsuario == "1") {
                        mensaje = "Administrador";
                    } else if (tipoUsuario == "2") {
                        mensaje = "Coordinador";
                    } else  if (tipoUsuario == "3"){
                        mensaje = "Estudiante Anteproyecto";
                    }else  if (tipoUsuario == "4"){
                        mensaje = "Estudiante Proyecto";
                    }else  if (tipoUsuario == "5"){
                        mensaje = "Director";
                    }else  if (tipoUsuario == "6"){
                        mensaje = "Director Externo";
                    }


                    let rowData = {
                        numero_documento: row[0] || "",
                        nombre: row[1] || "",
                        apellidos: row[2] || "",
                        correo: row[3] || "",
                        telefono: row[4] || "",
                        rol: mensaje || "",
                        tipo_usuario: row[5] || "",
                        select1Value: "",
                        select2Value: ""
                    };
    
                    dataToSend.push(rowData); // Guarda en el array
    
                    let tr = document.createElement("tr");
    
                    // Crear celdas de datos (sin incluir select1Value y select2Value)
                    Object.values(rowData).slice(0, -3).forEach(value => { 
                        let td = document.createElement("td");
                        td.textContent = value;
                        tr.appendChild(td);
                    });
                    // Crear contenedores para selects
                    let select1Td = document.createElement("td");
                    let select2Td = document.createElement("td");
    
                    // Crear Select1
                    let select1 = createSelect(select1Data, "select1", "nombre_facultad","id_facultad", async function () {
                        let selectedId = select1.value;
                        rowData.select1Value = select1.value; // Guarda valor en rowData
                        hiddenInput.value = JSON.stringify(dataToSend);
    
                        if (selectedId) {
                            let select2Data = await fetchSelectData(url +`Views/content/getSelect2Data.php?id=${selectedId}`);
                            console.log("Datos recibidos para Select2:", select2Data);
                            let newSelect2 = createSelect(select2Data, "select2", "nombre_programa","id_programa");
    
                            // Reemplazar select2 en su celda
                            select2Td.innerHTML = "";
                            select2Td.appendChild(newSelect2);
    
                            // Evento para actualizar select2Value cuando cambie
                            newSelect2.addEventListener("change", function () {
                                rowData.select2Value = newSelect2.value;
                                hiddenInput.value = JSON.stringify(dataToSend);
                            });

                            newSelect2.classList.add("custom-select");
                        }
                    });

                    select1.classList.add("custom-select"); // Agregar clase al Select1
        

                    select1Td.appendChild(select1);
                    tr.appendChild(select1Td);
                    tr.appendChild(select2Td);
                    tableBody.appendChild(tr);


                     // Agregar botón de eliminación con Font Awesome
                        let deleteTd = document.createElement("td");
                        let deleteBtn = document.createElement("button");
                        deleteBtn.innerHTML = '<i class="fa-solid fa-trash"></i>'; // Ícono de basura
                        deleteBtn.style.cursor = "pointer";
                        deleteBtn.style.border = "none";
                        deleteBtn.style.background = "transparent";
                        deleteBtn.style.color = "red"; // Color del icono
                        deleteBtn.style.fontSize = "18px";
  
                      deleteBtn.addEventListener("click", function () {
                          tr.remove(); // Elimina la fila de la tabla
                          dataToSend = dataToSend.filter(item => item !== rowData); // Elimina del array
                      });
  
                      deleteTd.appendChild(deleteBtn);
                      tr.appendChild(deleteTd);
      

                    hiddenInput.value = JSON.stringify(dataToSend);
                }
            }
        };
    }
    
    

    // Cargar los datos del primer select al iniciar
    async function init() {
        let select1Data = await fetchSelectData(url +`Views/content/getSelect1Data.php`);
        fileInput.addEventListener("change", function (event) {
            let file = event.target.files[0];
            if (file) {
                processFile(file, select1Data);
            }
        });
    }

    init();
   

    form.addEventListener("submit", function (event) {
        if (dataToSend.length === 0) {
            event.preventDefault(); // Detiene el envío si dataToSend está vacío
            console.log("El arreglo está vacío en el primer intento. Intenta nuevamente.");
            return;
        }
        
        hiddenInput.value = JSON.stringify(dataToSend);
    });

    
});

