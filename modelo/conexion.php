<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "videoclub";

// Crear conexión
$conexion = mysqli_connect($servername, $username, $password, $dbname);

// Verificar la conexión
if (!$conexion) 
{
    $error_message = "No se puede conectar con el servidor: " . mysqli_connect_error();
    echo $error_message;
}
?>