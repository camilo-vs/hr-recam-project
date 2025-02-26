<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>

</head>
<body>
<section class="h-200" style="background-color: #eee;">
  <div class="container py-5 h-200">
    <div class="row d-flex justify-content-center align-items-center h-200">
      <div class="col-xl-10">
        <div class="card rounded-3 text-light bg-black">
          <div class="row g-0">
            <div class="col-lg-6">
              <div class="card-body p-md-5 mx-md-4">

                <div class="text-center">
                  <img src="img/recam.png" style="width: 300px;" alt="logo">
                  <h4 class="mb-3 pb-1">Inicio de sesión</h4>
                  <p id="errorMessage" class="text-danger"></p>
                </div>

                <form id="loginForm" action="login.php" method="POST">
                    <div class="form-outline mb-4">
                        <label class="form-label" for="id">Username</label>
                        <input type="number" id="id" name="id" class="form-control" placeholder="Ingresa tu ID" required />
                    </div>

                    <div class="form-outline mb-4">
                        <label class="form-label" for="pass">Password</label>
                        <input type="password" id="pass" name="password" class="form-control" required />
                    </div>

                    <div class="text-center pt-1 mb-5 pb-1">
                        <button id="loginButton" class="btn btn-primary btn-block fa-lg btn-warning mb-3" type="submit">
                            Acceder
                        </button>
                    </div>
                </form>

              </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center bg-image">
              <div class="text-white px-3 py-4 p-md-5 mx-md-4">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $("#loginForm").submit(function(event) {
        event.preventDefault();

        $("#errorMessage").text("");
        $("#loginButton").prop("disabled", true).text("Accediendo...");

        $.ajax({
            url: "login.php",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                console.log(response); // Agregado para depuración

                if (response.trim() === "success") {
                    window.location.href = "dashboard.php";
                } else if (response.trim() === "error1") {
                    $("#errorMessage").text("ID de usuario o contraseña incorrectos.");
                    $("#loginButton").prop("disabled", false).text("Acceder");
                } else if (response.trim() === "error2") {
                    $("#errorMessage").text("Usuario no encontrado.");
                    $("#loginButton").prop("disabled", false).text("Acceder");
                } else {
                    $("#errorMessage").text("Error desconocido. Inténtalo de nuevo.");
                    $("#loginButton").prop("disabled", false).text("Acceder");
                }
            },
            error: function() {
                $("#errorMessage").text("Error en la solicitud. Inténtalo de nuevo.");
                $("#loginButton").prop("disabled", false).text("Acceder");
            }
        });
    });
});
</script>

</html>
