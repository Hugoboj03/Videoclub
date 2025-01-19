<?php

include("../modelo/conexion.php");
include("../modelo/sesion.php");
include("saludo.php");

// Obtener el nombre de usuario de la sesión
$nombreUsuario = $_SESSION['nombre'];

// Obtener el ID del usuario actual desde la base de datos
$query_usuario = "SELECT id FROM usuarios WHERE nombre = ?";
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

// Consultar las películas reservadas del usuario actual
$query_reservadas = "
    SELECT 
        peliculas.id AS pelicula_id, 
        peliculas.titulo, 
        historial.fecha_accion AS fecha_reserva 
    FROM historial
    INNER JOIN peliculas ON historial.pelicula_id = peliculas.id
    WHERE historial.usuario_id = ? 
    AND historial.tipo_accion_id = 2;"; // 2 = Reserva
$stmt_reservadas = $conexion->prepare($query_reservadas);
$stmt_reservadas->bind_param("i", $usuario_id);
$stmt_reservadas->execute();
$resultado_reservadas = $stmt_reservadas->get_result();

// Consultar las películas alquiladas del usuario actual
$query_alquiladas = "
    SELECT 
        peliculas.id AS pelicula_id, 
        peliculas.titulo, 
        historial.fecha_accion AS fecha_alquiler, 
        historial.fecha_prevista_devolucion AS fecha_devolucion
    FROM historial
    INNER JOIN peliculas ON historial.pelicula_id = peliculas.id
    WHERE historial.usuario_id = ? 
    AND historial.tipo_accion_id = 1;"; // 1 = Alquiler
$stmt_alquiladas = $conexion->prepare($query_alquiladas);
$stmt_alquiladas->bind_param("i", $usuario_id);
$stmt_alquiladas->execute();
$resultado_alquiladas = $stmt_alquiladas->get_result();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Clientes</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>

<body>

    <h1>Historial de Películas de <?php echo $nombreUsuario; ?></h1>
    <p><a href="../index.php">Volver al Index</a></p>

    <!-- Tabla de Películas Reservadas -->
    <h2>Películas Reservadas</h2>
    <?php if ($resultado_reservadas->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Título</th>
                <th>Fecha de Reserva</th>
            </tr>
            <?php while ($fila = $resultado_reservadas->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $fila['titulo']; ?></td>
                    <td><?php echo $fila['fecha_reserva']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No tienes películas reservadas actualmente.</p>
    <?php endif; ?>

    <!-- Tabla de Películas Alquiladas -->
    <h2>Películas Alquiladas</h2>
    <?php if ($resultado_alquiladas->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Título</th>
                <th>Fecha de Alquiler</th>
                <th>Fecha Prevista de Devolución</th>
            </tr>
            <?php while ($fila = $resultado_alquiladas->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $fila['titulo']; ?></td>
                    <td><?php echo $fila['fecha_alquiler']; ?></td>
                    <td><?php echo $fila['fecha_devolucion']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No tienes películas alquiladas actualmente.</p>
    <?php endif; ?>

</body>

</html>