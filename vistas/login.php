<script>
  $(document).ready(function() {
    // Capturar el evento de envío del formulario
    $('#formAuthentication').submit(function(e) {
      e.preventDefault(); // Evita que el formulario se envíe de la manera tradicional

      // Obtener los datos del formulario
      var usuario = $('#usuario').val();
      var password = $('#password').val();

      // Mostrar el loading
      const loadingSwal = Swal.fire({
        title: 'Iniciando sesión...',
        text: 'Por favor espera...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading(); // Mostrar el ícono de carga
        }
      });

      // Realizar la petición AJAX
      $.ajax({
        url: 'index.php?c=login&m=logear',
        type: 'POST',
        data: {
          usuario: usuario,
          password: password
        },
        success: function(response) {
          var respuesta = JSON.parse(response);
          // Dependiendo de la respuesta, puedes redirigir o mostrar un error
          if (respuesta.error == false) {
            localStorage.setItem('usuario', JSON.stringify(respuesta.registros));
            // Redirigir al usuario a la página principal
            Swal.fire({
              icon: 'success',
              title: 'Bienvenido',
              text: '¡Inicio de sesión exitoso!',
            }).then(function() {
              window.location.href = 'index.php?c=paginas&m=inicio'; // Redirigir a la página principal
            });
          } else {
            // Mostrar mensaje de error si el login es incorrecto
            Swal.fire({
              icon: 'error',
              title: 'Error de Inicio de sesión',
              text: 'Usuario o contraseña incorrectos',
            });
          }
        },
        error: function(xhr, status, error) {
          console.error('Error en la solicitud AJAX: ' + error);
          Swal.fire({
            icon: 'error',
            title: 'Error al procesar solicitud',
            text: 'Hubo un error al procesar tu solicitud.',
          });
        },
        complete: function() {
          // Cerrar el loading después de que la solicitud termine
          loadingSwal.close();
        }
      });
    });
  });
</script>

<body>
  <!-- Content -->

  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- Register -->
        <div class="card">
          <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center">
              <a href="#" class="app-brand-link gap-2">
                <img src="assets/img/logos/recam_logo_black.png" width="100%">
              </a>
            </div>
            <form id="formAuthentication" class="mb-3">
              <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input
                  type="text"
                  class="form-control"
                  id="usuario"
                  name="usuario"
                  placeholder="Ingresa tu usuario"
                  required
                  autofocus />
              </div>
              <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                  <label class="form-label" for="password">Contraseña</label>
                </div>
                <div class="input-group input-group-merge">
                  <input
                    type="password"
                    id="password"
                    class="form-control"
                    name="password"
                    placeholder="********"
                    aria-describedby="password"
                    required />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>
              <div class="mb-3">
                <button class="btn btn-primary d-grid w-100" type="submit">Ingresar</button>
              </div>
            </form>


            <p class="text-center">
              <span>No recuerdas la contraseña?</span>
              <a href="auth-register-basic.html">
                <span>Solicitar Cambio</span>
              </a>
            </p>
          </div>
        </div>
        <!-- /Register -->
      </div>
    </div>
  </div>

  <!-- / Content -->

  <!-- Core JS -->
  <!-- build:js assets/vendor/js/core.js -->
  <script src="assets/vendor/libs/jquery/jquery.js"></script>
  <script src="assets/vendor/libs/popper/popper.js"></script>
  <script src="assets/vendor/js/bootstrap.js"></script>
  <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="assets/vendor/js/menu.js"></script>
  <script src="assets/js/main.js"></script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

</body>

</html>