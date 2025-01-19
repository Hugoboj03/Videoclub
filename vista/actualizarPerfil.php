<?php

include("../modelo/conexion.php");
include("../modelo/sesion.php");

// Obtener el nombre del usuario actual desde la sesión
$nombreUsuario = $_SESSION['nombre'];

// Obtener datos actuales del usuario desde la base de datos
$query_usuario = "SELECT id, email, contrasena FROM usuarios WHERE nombre = ?";
$stmt_usuario = $conexion->prepare($query_usuario);
$stmt_usuario->bind_param("s", $nombreUsuario);
$stmt_usuario->execute();
$resultado_usuario = $stmt_usuario->get_result();

if ($resultado_usuario->num_rows === 0) {
    echo "<p>Error: Usuario no encontrado.</p>";
    exit;
}

$usuario = $resultado_usuario->fetch_assoc();
$usuario_id = $usuario['id'];
$email_actual = $usuario['email'];
$password_actual = $usuario['contrasena'];

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_email = $_POST['email'];
    $password_actual_ingresada = $_POST['password_actual'];
    $nueva_password = $_POST['nueva_password'];
    $confirmar_password = $_POST['confirmar_password'];

    // Validar campos
    if (empty($nuevo_email) || empty($password_actual_ingresada)) {
        echo "<p>Error: Todos los campos son obligatorios.</p>";
    } elseif (!password_verify($password_actual_ingresada, $password_actual)) {
        echo "<p>Error: La contraseña actual no es correcta.</p>";
    } elseif (!empty($nueva_password) && $nueva_password !== $confirmar_password) {
        echo "<p>Error: Las contraseñas nuevas no coinciden.</p>";
    } else {
        // Actualizar email
        $query_update_email = "UPDATE usuarios SET email = ? WHERE id = ?";
        $stmt_update_email = $conexion->prepare($query_update_email);
        $stmt_update_email->bind_param("si", $nuevo_email, $usuario_id);

        if ($stmt_update_email->execute()) {
            echo "<p>El correo electrónico se ha actualizado correctamente.</p>";
        } else {
            echo "<p>Error al actualizar el correo electrónico.</p>";
        }

        // Actualizar contraseña si se ingresó una nueva
        if (!empty($nueva_password)) {
            $hashed_password = password_hash($nueva_password, PASSWORD_DEFAULT);
            $query_update_password = "UPDATE usuarios SET contrasena = ? WHERE id = ?";
            $stmt_update_password = $conexion->prepare($query_update_password);
            $stmt_update_password->bind_param("si", $hashed_password, $usuario_id);

            if ($stmt_update_password->execute()) {
                echo "<p>La contraseña se ha actualizado correctamente.</p>";
            } else {
                echo "<p>Error al actualizar la contraseña.</p>";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>

<body>
    <h1>Editar Perfil</h1>
    <p><a href="../index.php">Volver al Index</a></p>

    <form method="post" action="">
        <table>
            <tr>
                <th>Nombre:</th>
                <td><?php echo htmlspecialchars($nombreUsuario); ?></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><input type="email" name="email" value="<?php echo htmlspecialchars($email_actual); ?>" required></td>
            </tr>
            <tr>
                <th>Contraseña Actual:</th>
                <td><input type="password" name="password_actual" required></td>
            </tr>
            <tr>
                <th>Nueva Contraseña:</th>
                <td><input type="password" name="nueva_password"></td>
            </tr>
            <tr>
                <th>Confirmar Nueva Contraseña:</th>
                <td><input type="password" name="confirmar_password"></td>
            </tr>
        </table>
        <input type="submit" value="Guardar Cambios">
    </form>
</body>

</html>