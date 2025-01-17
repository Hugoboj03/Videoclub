<?php
include("../modelo/conexion.php");
include("../modelo/sesion.php");
include("../modelo/funcionesGestionarPelis.php");

// Obtener el ID del cliente desde los parámetros GET
$cliente_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$cliente_id) {
    echo "<p>Error: No se proporcionó un ID de cliente válido.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_devolucion'])) {
    // Obtener los datos del formulario
    $codigo_operacion = intval($_POST['codigo_operacion']);
    $pelicula_id = intval($_POST['pelicula_id']);
    $fecha_devolucion = $_POST['fecha_devolucion'];

    if (empty($codigo_operacion) || empty($pelicula_id) || empty($fecha_devolucion)) {
        echo "<p class='error'>Error: Todos los campos son obligatorios.</p>";
    } else {
        // 1. Actualizar el estado de la película a "Disponible" (estado_id = 1)
        $consulta_estado = "UPDATE peliculas SET estado_id = 1 WHERE id = ?";
        $stmt_estado = $conexion->prepare($consulta_estado);
        $stmt_estado->bind_param("i", $pelicula_id);

        if ($stmt_estado->execute()) {
            // 2. Registrar la operación
            $usuario_id = $cliente_id; // El ID del cliente actual
            if (registrarOperacion($conexion, $usuario_id, $pelicula_id)) {
                // 3. Determinar si la devolución es tardía o a tiempo
                $estado_devolucion_id = (strtotime($fecha_devolucion) > strtotime($_POST['fecha_prevista_devolucion'])) ? 2 : 1;

                // Registrar en el historial
                if (registrarHistorial(
                    $conexion,
                    $usuario_id,
                    $pelicula_id,
                    $codigo_operacion,
                    $fecha_devolucion,
                    3, // Tipo de acción: Devolución
                    $estado_devolucion_id
                )) {
                    echo "<p class='success'>Devolución registrada correctamente.</p>";
                } else {
                    echo "<p class='error'>Error al registrar el historial de devolución.</p>";
                }
            } else {
                echo "<p class='error'>Error al registrar la operación.</p>";
            }
        } else {
            echo "<p class='error'>Error al actualizar el estado de la película.</p>";
        }
    }
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
                                peliculas.id AS pelicula_id, 
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
                <th>Acciones</th>
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
                            echo "Pendiente";
                        }


                        ?></td>
                    <td><?php

                        if ($pelicula['tipo_accion'] == 1) {
                            echo "Alquiler";
                        } else {
                            echo "";
                        }
                        ?></td>
                    <td>
                        <form action="fichaCliente.php?id=<?php echo $cliente_id; ?>" method="post">
                            <input type="hidden" name="codigo_operacion" value="<?php echo $pelicula['codigo_operacion']; ?>">
                            <input type="hidden" name="pelicula_id" value="<?php echo $pelicula['pelicula_id']; ?>">
                            <input type="hidden" name="fecha_prevista_devolucion" value="<?php echo $pelicula['fecha_prevista_devolucion']; ?>">
                            <label>Fecha Devolución:</label>
                            <input type="date" name="fecha_devolucion" required><br>
                            <input type="submit" name="confirmar_devolucion" value="Confirmar Devolución">
                        </form>
                    </td>

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