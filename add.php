<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    var_dump($_POST);

    if (!isset($_POST['username'], $_POST['password'], $_POST['passconfirmation'], $_POST['user_type'])) {
        echo "error_missing_fields";
        exit();
    }

    $name = trim($_POST['username']);
    $password = $_POST['password'];
    $passconfirmation = $_POST['passconfirmation'];
    $user_type = intval($_POST['user_type']); // Asegurar que es un número entero

    if ($password !== $passconfirmation) {
        echo "error_password_mismatch";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $checkUser = $conn->prepare("SELECT id FROM users WHERE name = ?");
    $checkUser->bind_param("s", $name);
    $checkUser->execute();
    $checkUser->store_result();

    if ($checkUser->num_rows > 0) {
        echo "error_user_exists"; 
        exit();
    }

    $query = "INSERT INTO users (name, active, password, user_type_id, created_by, updated_by) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    $active = 1;
    $created_by = 1;
    $updated_by = 1;

    $stmt->bind_param("sissii", $name, $active, $hashed_password, $user_type, $created_by, $updated_by);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error_db: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
