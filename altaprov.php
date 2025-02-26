<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta provisional</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <form id="addForm" action="add.php" method="POST">
        <div class="form-outline mb-4">
            <label class="form-label" for="name">Nombre</label>
            <input type="text" id="name" name="username" class="form-control" placeholder="Ingresa el nombre del usuario" required />
        </div>
        <div class="form-outline mb-4">
            <label class="form-label" for="pass">Contraseña</label>
            <input type="password" id="pass" name="password" class="form-control" required />
        </div>
        <div class="form-outline mb-4">
            <label class="form-label" for="passconf">Confirma la contraseña</label>
            <input type="password" id="passconf" name="passconfirmation" class="form-control" required />
        </div>
        <div class="form-outline mb-4">
            <label class="form-label" for="user_type">Tipo de usuario</label>
            <select id="user_type" name="user_type" class="form-control" required>
                <option value="1">Admin</option>
                <option value="2">Usuario estándar</option>
            </select>
        </div>
        <div class="text-center pt-1 mb-5 pb-1">
            <button id="loginButton" class="btn btn-primary btn-block fa-lg btn-warning mb-3" type="submit">
                Registrar
            </button>
        </div>
    </form>

    <script>
    $(document).ready(function() {
        $("#addForm").submit(function(event) {
            event.preventDefault();
            $("#loginButton").prop("disabled", true).text("Registrando...");

            $.ajax({
                url: "add.php",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    console.log("Server response:", response); // Depuración

                    if (response.trim() === "success") {
                        alert("Usuario registrado correctamente.");
                        window.location.href = "dashboard.php";
                    } else if (response.trim() === "error_password_mismatch") {
                        alert("Las contraseñas no coinciden.");
                    } else if (response.trim() === "error_user_exists") {
                        alert("El usuario ya existe.");
                    } else {
                        alert("Error al registrar usuario. Respuesta: " + response);
                    }
                    $("#loginButton").prop("disabled", false).text("Registrar");
                },
                error: function(xhr, status, error) {
                    console.error("Error en AJAX:", error);
                    alert("Error en la conexión con el servidor.");
                    $("#loginButton").prop("disabled", false).text("Registrar");
                }
            });
        });
    });
    </script>
</body>
</html>
