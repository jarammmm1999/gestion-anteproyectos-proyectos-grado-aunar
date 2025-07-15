
        <div class="container-main-login">
            <div class="container-section image">
                <img src="<?= SERVERURL ?>/Views/assets/images/Forgot password-cuate.png" alt="imagen login">
            </div>
            <div class="container-section login">
                <div class="container-image mb-5">
                    <img src="<?= SERVERURL ?>/Views/assets/images/logo-autonoma.png" alt="imagen login">
                </div>
                <div class="container mt-3 container-form">
                    <h3 class="text-center mb-5 title-login two"><?= strtoupper('Recuperar contrasena') ?></h3>
                    
                    <!-- Formulario para recuperar contraseña -->
                    <form class="FormulariosAjaxLogin" action="<?= SERVERURL ?>Ajax/UsuarioAjax.php" method="POST" autocomplete="off">
                        <div class="mb-5 mt-3">
                            <input type="email" class="form-control input-text" id="correoresetpassword" name="correoresetpassword" placeholder="Correo de usuario" >
                        </div>
                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-send">Recuperar Contraseña</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
       