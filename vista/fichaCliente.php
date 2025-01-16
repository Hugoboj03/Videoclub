<?php
include("../modelo/conexion.php");
include("../modelo/sesion.php");

// Obtener el ID del cliente desde los parámetros GET
$cliente_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$cliente_id) {
    echo "<p>Error: No se proporcionó un ID de cliente válido.</p>";
    exit;
}

// Consultar información del cliente
$consulta_cliente = "SELECT nombre, email FROM usuarios WHERE id = ?";
$stmt = $conexion->prepare($consulta_cliente);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$resultado_cliente = $stmt->get_result();
$cliente = $resultado_cliente->fetch_assoc();

if (!$cliente) {
    echo "<p>Error: Cliente no encontrado.</p>";
    exit;
}

// Consultar películas pendientes de devolución (alquiladas actualmente)
$consulta_pendientes = "SELECT peliculas.titulo,
                                historial.codigo_operacion, 
                               historial.fecha_accion AS fecha_alquiler, 
                               historial.fecha_prevista_devolucion,
                               historial.estado_devolucion_id AS estado_devolucion,
                               historial.tipo_accion_id AS tipo_accion    
                        FROM historial 
                        INNER JOIN peliculas ON historial.pelicula_id = peliculas.id 
                        WHERE historial.usuario_id = ? 
                        AND historial.tipo_accion_id = 1 
                        AND peliculas.estado_id = 3"; // Estado 3 = Alquilada
$stmt_pendientes = $conexion->prepare($consulta_pendientes);
$stmt_pendientes->bind_param("i", $cliente_id);
$stmt_pendientes->execute();
$resultado_pendientes = $stmt_pendientes->get_result();

// Consultar el total de películas alquiladas por el cliente
$consulta_total = "SELECT COUNT(*) AS total_alquiladas 
                   FROM historial 
                   WHERE usuario_id = ? 
                   AND tipo_accion_id = 1";
$stmt_total = $conexion->prepare($consulta_total);
$stmt_total->bind_param("i", $cliente_id);
$stmt_total->execute();
$resultado_total = $stmt_total->get_result();
$total_alquiladas = $resultado_total->fetch_assoc()['total_alquiladas'];

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ficha Cliente</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>

<body>
    <h1>Ficha del Cliente</h1>
    <p><a href="gestionarPeliculas.php">Volver a la lista de películas</a></p>

    <table>
        <tr>

            <th>Nombre:</th>
            <td><?php echo $cliente['nombre']; ?></td>

        </tr>
        <tr>

            <th>Email:</th>
            <td><?php echo $cliente['email']; ?></td>

        </tr>
        <tr>
            <th>Pendientes de Devolver:</th>
            <!--Ovtenemos el numero de peliculas pendientes con num rows-->
            <td><?php echo $resultado_pendientes->num_rows; ?></td>
        </tr>
        <tr>
            <th>Total de Películas Alquiladas:</th>
            <td><?php echo $total_alquiladas; ?></td>
        </tr>
    </table>


    <h2>Películas Pendientes de Devolución</h2>
    <?php if ($resultado_pendientes->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Título</th>
                <th>Código de Operación</th>
                <th>Fecha de Alquiler</th>
                <th>Fecha Prevista de Devolución</th>
                <th>Estado de Devolución</th>
                <th>Tipo de Accion</th>
            </tr>
            <?php while ($pelicula = $resultado_pendientes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $pelicula['titulo']; ?></td>
                    <td><?php echo $pelicula['codigo_operacion']; ?></td>
                    <td><?php echo $pelicula['fecha_alquiler']; ?></td>
                    <td><?php echo $pelicula['fecha_prevista_devolucion']; ?></td>
                    <td><?php

                        if ($pelicula['estado_devolucion'] == 1) {
                            echo "Pendiente";
                        } else {
                            echo "";
                        }


                        ?></td>
                    <td><?php

                        if ($pelicula['tipo_accion'] == 1) {
                            echo "Alquiler";
                        } else {
                            echo "";
                        }
                        ?></td>

                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No tiene películas pendientes de devolución.</p>
    <?php endif; ?>

    <h2>Total de Películas Alquiladas</h2>
    <p><?php echo $total_alquiladas; ?></p>
</body>

</html>