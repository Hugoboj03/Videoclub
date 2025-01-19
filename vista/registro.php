<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../estilos/estilo2.css">
    <title>Registro de Usuario</title>
</head>

<body>

    <header>
        Bienvenido, al Sistema de Gestión de Noticias
    </header>

    <h1>Registro de Usuario</h1>

    <?php
    // Verificar si hay un mensaje de error
    if (isset($_GET['error'])) {
        echo '<p style="color: red;">' . $_GET['error'] . '</p>';
    }
    ?>

    <form action="../modelo/nuevoUsuario.php" method="post">
        <table>
            <tr>
                <td><label for="nombre">Nombre:</label></td>
                <td><input type="text" name="nombre" required></td>
            </tr>
            <tr>
                <td><label for="usuario">Usuario:</label></td>
                <td><input type="text" name="usuario" required></td>
            </tr>
            <tr>
                <td><label for="password">Contraseña:</label></td>
                <td><input type="password" name="password" required></td>
            </tr>
            <tr>
                <td><label for="email">Email:</label></td>
                <td><input type="email" name="email" required></td>
            </tr>

        </table>

        <input type="submit" value="Registrar">
    </form>
    <br><a href="login.php">- Volver - </a>
</body>

</html>