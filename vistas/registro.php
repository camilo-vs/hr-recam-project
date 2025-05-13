<script>
  $(document).ready(function() {
    // Capturar el evento de envío del formulario de registro
    $('#formRegistro').submit(function(e) {
      e.preventDefault(); // Evita que el formulario se envíe de la manera tradicional

      // Obtener los datos del formulario
      var nombre = $('#nombre').val();
      var contraseña = $('#contraseña').val();

      // Validar que todos los campos estén llenos
      if (nombre === "" || contraseña === "") {
        Swal.fire({
          icon: 'error',
          title: 'Campos Vacíos',
          text: 'Por favor, llena todos los campos.'
        });
        return;
      }
      var passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
      if (!passwordRegex.test(contraseña)) {
        Swal.fire({
          icon: 'info',
          title: 'Contraseña Insegura',
          text: 'La contraseña debe tener al menos 8 caracteres, una letra, un número y un carácter especial.'
        });
        return;
      }
      // Mostrar loading (pantalla de carga)
      const loadingSwal = Swal.fire({
        title: 'Registrando...',
        text: 'Por favor espera...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading(); // Mostrar el ícono de carga
        }
      });

      // Realizar la petición AJAX
      $.ajax({
        url: 'index.php?c=registro&m=registro', // Cambia esta URL por la ruta correcta en tu servidor
        type: 'POST',
        data: {
          nombre: nombre,
          contraseña: contraseña
        },
        success: function(response) {
          var respuesta = JSON.parse(response);
          // Dependiendo de la respuesta, puedes redirigir o mostrar un error
          if (respuesta.error == false) {
            Swal.fire({
              icon: 'success',
              title: 'Registro Exitoso',
              text: '¡Bienvenido! Ahora puedes iniciar sesión.',
            }).then(function() {
              // Redirigir al usuario a la página de login
              window.location.href = 'index.php?c=paginas&m=login'; // Cambia por la ruta a tu página de login
            });
          } else {
            // Mostrar mensaje de error si el registro falla
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: respuesta.msg
            });
          }
        },
        error: function(xhr, status, error) {
          console.error('Error en la solicitud AJAX: ' + error);
          Swal.fire({
            icon: 'error',
            title: 'Error al procesar solicitud',
            text: 'Hubo un error al procesar tu solicitud.'
          });
        },
        complete: function() {
          // Cerrar la alerta de loading cuando termina la solicitud
          loadingSwal.close();
        }
      });
    });
  });
</script>

<body>
  <!-- Contenido -->
  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- Tarjeta de Registro -->
        <div class="card">
          <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center">
              <a href="#" class="app-brand-link gap-2">
                <img src="assets/img/logos/logo.png" width="100%">
              </a>
            </div>
            <!-- /Logo -->
            <h4 class="mb-2">Registro</h4>
            <p class="mb-4">¡Ingresa la información solicitada!</p>

            <form id="formRegistro" class="mb-3">
              <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input
                  type="text"
                  class="form-control"
                  id="nombre"
                  name="nombre"
                  placeholder="Introduce tu nombre"
                  autofocus
                  required />
              </div>
              <div class="mb-3 form-password-toggle">
                <label class="form-label" for="contraseña">Contraseña</label>
                <div class="input-group input-group-merge">
                  <input
                    type="password"
                    id="contraseña"
                    class="form-control"
                    name="contraseña"
                    placeholder="********"
                    aria-describedby="password"
                    required />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>
              <button type="submit" class="btn btn-primary d-grid w-100">Registrarse</button>
            </form>

            <p class="text-center">
              <span>¿Ya tienes una cuenta?</span>
              <a href="index.php?c=paginas&m=home">
                <span>Iniciar sesión</span>
              </a>
            </p>
          </div>
        </div>
        <!-- Tarjeta de Registro -->
      </div>
    </div>
  </div>

  <!-- JS Principal -->
  <script src="assets/vendor/libs/jquery/jquery.js"></script>
  <script src="assets/vendor/libs/popper/popper.js"></script>
  <script src="assets/vendor/js/bootstrap.js"></script>
  <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="assets/vendor/js/menu.js"></script>
  <script src="assets/js/main.js"></script>
  <!-- Incluir SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>



</body>

</html>