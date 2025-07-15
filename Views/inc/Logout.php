<script>
    let boton_salir = document.querySelector('.btn-exit-system');
    boton_salir.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '<?= $_SESSION['nombre_usuario'] . ' ' . $_SESSION['apellido_usuario'] ?>',
            text: "Estás a punto de cerrar la sesión",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, salir!'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = '<?= SERVERURL ?>Ajax/LoginAjax.php';
                let token = '<?= $ins_loginControlador->encryption($_SESSION['token_usuario']) ?>';
                let usuario = '<?= $ins_loginControlador->encryption($_SESSION['nombre_usuario']) ?>';
                let idUsuario = '<?= $ins_loginControlador->encryption($_SESSION['id_usuario']) ?>';

                let datos = new FormData();
                datos.append('token', token);
                datos.append('usuario', usuario);
                datos.append('idusuario', idUsuario);
            
                fetch(url, {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.json())
                .then(respuesta => {
                    return alert_ajax(respuesta)
                });

            }
        });
    });
</script>