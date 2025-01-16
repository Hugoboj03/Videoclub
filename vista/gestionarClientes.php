<?php

include("../modelo/conexion.php");
include("../modelo/sesion.php");
include("saludo.php");

$query = "SELECT usuarios.id, usuarios.nombre, usuarios.email, usuarios.usuario,
                   (SELECT COUNT(*) 
                    FROM historial 
                    WHERE historial.usuario_id = usuarios.id 
                      AND historial.tipo_accion_id = 1) AS total_alquiladas
            FROM usuarios;";
        $stmt_select = $conexion->prepare($query);
        $stmt_select->execute();
        $stmt_select->bind_result($id, $nombre, $mail, $usuario, $total_alquiladas);


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
            <th>Total Alquiladas</th>
            <th>Acciones</th>
        </tr>
        <?php

        



        while ($stmt_select->fetch()) {

            echo "<tr>";
            echo "<td>$nombre</td>";
            echo "<td>$mail</td>";
            echo "<td>$usuario</td>";
            echo "<td>$total_alquiladas</td>";
            echo "<td><a href='fichaCliente.php?id=$id'>Gestionar</a></td>";
            echo "</tr>";

        }

        ?>

    </table>



</body>

</html>