<?php
if ($_SESSION['privilegio'] != 1 && $_SESSION['privilegio'] != 2 && $_SESSION['privilegio'] != 5 && $_SESSION['privilegio'] != 6) { // Validamos que solo puedan ingresar los de privilegio 1, administradores
  echo $ins_loginControlador->cerrar_sesion_controlador();
  exit();
}
?>

<div class="container-fluid">
  <div class="container-table-user">
    <div class="continer-search mt-5 mb-3">
      <input type="text"  id="buscar" onkeyup="buscarTabla()" placeholder="Buscar en la tabla...">
    </div>
    <?php
    require_once "./Controller/UsuarioControlador.php";
    $ins_usuarioControlador = new UsuarioControlador();
    echo $ins_usuarioControlador->consultar_horas_asesorias_usuario($url_pagina[1], 10, $_SESSION['privilegio'], $url_pagina[0],$_SESSION['numero_documento']);
    ?>
  </div>
</div>