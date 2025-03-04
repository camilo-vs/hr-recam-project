<?php

class Conectar{
    public static function conexion(){
		
		$servidor = 'localhost';
		$usuario = 'root';
		$contraseña = '';
        $conexion = new mysqli($servidor, $usuario, $contraseña);
        return $conexion;
    }
}
