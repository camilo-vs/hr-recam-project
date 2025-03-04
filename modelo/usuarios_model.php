<?php

class usuarios_model
{
    private $db;

    public function __construct()
    {
        $this->db = Conectar::conexion();
        mysqli_set_charset($this->db, 'utf8');
    }

    public function consultarUsuarios()
    {
        mysqli_select_db($this->db, "hr_system");
        $sql = "SELECT * FROM users";
        $result = mysqli_query($this->db, $sql);

        if (!$result) {
            return json_encode(['error' => true, 'msg' => mysqli_error($this->db)]);
        }

        $registros = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $registros[] = $row;
        }

        return json_encode(['error' => false, 'usuarios' => $registros]);
    }

    public function crearUsuario($nombre, $apellido, $telefono, $email)
    {
        mysqli_select_db($this->db, "hr_system");
        $sql = "INSERT INTO users (name, lastname, phone, email) VALUES ('$nombre', '$apellido', '$telefono', '$email')";
        $result = mysqli_query($this->db, $sql);

        if (!$result) {
            return json_encode(['error' => true, 'msg' => mysqli_error($this->db)]);
        }
        return json_encode(['error' => false, 'msg' => 'Usuario agregado con éxito']);
    }

    public function actualizarUsuario($id, $nombre, $apellido, $telefono, $email)
    {
        mysqli_select_db($this->db, "hr_system");
        $sql = "UPDATE users SET name='$nombre', lastname='$apellido', phone='$telefono', email='$email' WHERE id=$id";
        $result = mysqli_query($this->db, $sql);

        if (!$result) {
            return json_encode(['error' => true, 'msg' => mysqli_error($this->db)]);
        }
        return json_encode(['error' => false, 'msg' => 'Usuario actualizado con éxito']);
    }

    public function eliminarUsuario($id)
    {
        mysqli_select_db($this->db, "hr_system");
        $sql = "DELETE FROM users WHERE id=$id";
        $result = mysqli_query($this->db, $sql);

        if (!$result) {
            return json_encode(['error' => true, 'msg' => mysqli_error($this->db)]);
        }
        return json_encode(['error' => false, 'msg' => 'Usuario eliminado con éxito']);
    }
}
