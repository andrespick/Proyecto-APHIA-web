<?php if (isset($_GET['error'])): ?>
  <p class="error">
    <?php
      switch ($_GET['error']) {
        case 'campos_vacios': echo "Por favor completa todos los campos."; break;
        case 'email_invalido': echo "El correo electrónico no es válido."; break;
        case 'credenciales_invalidas': echo "Correo o contraseña incorrectos."; break;
        case 'categoria_invalida': echo "Tu categoría de usuario no tiene acceso."; break;
      }
    ?>
  </p>
<?php endif; ?>
