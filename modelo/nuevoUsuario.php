<?php
// Incluir el archivo de conexión
include("conexion.php");

// Función para generar un hash de contraseña
function hash_password($password) 
{
    return password_hash($password, PASSWORD_DEFAULT);
}

// Obtener los datos del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST" ) 
{
    $nombre = $_POST["nombre"];
    $usu = $_POST["usuario"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    

    // Verificar si los campos no están en blanco
    if (empty($nombre) || empty($usu) || empty($password) || empty($email)) 
    {
        // Redireccionar a la página del formulario con un mensaje de error
        header("Location: ../vista/registro.php?error=Todos los campos son obligatorios");
        exit(); // Asegura que el script se detenga después de redirigir
    }


    // Verificar si el nombre de usuario ya existe
    $sql_verificar = "SELECT * FROM usuarios WHERE usuario = '$usu'";
    $resultado_verificar = mysqli_query($conexion, $sql_verificar);

    if (mysqli_num_rows($resultado_verificar) > 0) {
        // Redireccionar a la página del formulario con un mensaje de error
        header("Location: ../vista/registro.php?error=El nombre de usuario ya existe. Por favor, elige otro.");
        exit();
    }

    // Generar el hash de la contraseña
    $hashed_password = hash_password($password);

    // Escapar los datos del usuario 
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $usu = mysqli_real_escape_string($conexion, $usu);
    $hashed_password = mysqli_real_escape_string($conexion, $hashed_password);
    $email = mysqli_real_escape_string($conexion, $email);
    

    // Construir la consulta SQL con los datos escapados
    $sql = "INSERT INTO usuarios(nombre, usuario, contrasena, rol_id, email) 
    VALUES ('$nombre', '$usu', '$hashed_password', 2, '$email')";

    // Ejecutar la consulta utilizando mysqli_query
    $resultado = mysqli_query($conexion, $sql);

    // Verificar si hubo algún error al ejecutar la consulta
    if ($resultado === false) {
        die("Error al ejecutar la consulta: " . mysqli_error($conexion));
    }

    echo "El usuario $usu ha sido introducido en el sistema con la contraseña $password.";
    echo '<br><a href="../vista/login.php">Volver a la página login</a>';
}
else
{
    // Redireccionar a la página del formulario con un mensaje de error
    header("Location: ../vista/registro.php?error=No se ha podido introducir el nuevo usuario");
    exit(); // Asegura que el script se detenga después de redirigir
}
// Cerrar la conexión
mysqli_close($conexion);
?>
