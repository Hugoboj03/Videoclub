<?php

include("../modelo/conexion.php");
include("../modelo/sesion.php");
include("saludo.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>

<body>

    <h1>Gestionar Clientes</h1>

    <p><a href='../index.php'>Volver al Index</a></p>

    <table border="1">
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Usuario</th>
        </tr>
        <?php

        $query = "SELECT nombre, email, usuario FROM usuarios;";
        $stmt_select = $conexion->prepare($query);
        $stmt_select->execute();
        $stmt_select->bind_result($nombre, $mail, $usuario);



        while ($stmt_select->fetch()) {

            echo "<tr>";
            echo "<td>$nombre</td>";
            echo "<td>$mail</td>";
            echo "<td>$usuario</td>";
            echo "</tr>";

        }

        ?>

    </table>



</body>

</html>