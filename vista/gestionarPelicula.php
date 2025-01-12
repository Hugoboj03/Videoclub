<?php
// Incluir bibliotecas de funciones y sesiones
include("../modelo/conexion.php");
include("../modelo/sesion.php");
include("../modelo/funcionesGestionarPelis.php");
include("saludo.php");

// Verificar el tipo de usuario
$tipo_usuario = $_SESSION['tipo_usuario']; // 1 = Trabajador, 2 = Cliente

// Verificar que se haya proporcionado un ID válido de película
$id_pelicula = isset($_GET['id_pelicula']) ? intval($_GET['id_pelicula']) : null;

// Inicializar variables
$cliente_encontrado = false;
$cliente = null;

// Obtener el estado de la película
$consulta_estado = "SELECT estado_id FROM peliculas WHERE id = ?";
$stmt = $conexion->prepare($consulta_estado);
$stmt->bind_param("i", $id_pelicula);
$stmt->execute();
$resultado_estado = $stmt->get_result();
$pelicula = $resultado_estado->fetch_assoc();

// Verificar si se encontró la película
if ($pelicula) {
    $estado_pelicula = $pelicula['estado_id'];
} else {
    echo "<p class='error'>Película no encontrada.</p>";
    exit;
}

// Procesar lógica según formulario enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['buscar_cliente'])) {
        // Buscar cliente (ya se hace en otra clase)
        $criterio = $_POST['criterio'];
        $cliente = buscarCliente($conexion, $criterio);
        $cliente_encontrado = !empty($cliente);

        if (!$cliente_encontrado) {
            echo "<p class='error'>Cliente no encontrado. Intente de nuevo.</p>";
        }
    } elseif (isset($_POST['alquilar'])) {
        // Alquilar película
        $cliente_id = $_POST['cliente_id']; // El id del cliente que alquila la película
        $pelicula_id = $_POST['pelicula_id']; // El id de la película que se quiere alquilar
        $fecha_devolucion = $_POST['fecha_devolucion'];
        echo "Fecha de devolución: " . $fecha_devolucion;

        // Verificar si la película está disponible
        $consulta_estado = "SELECT estado_id FROM peliculas WHERE id = ?";
        $stmt = $conexion->prepare($consulta_estado);
        $stmt->bind_param("i", $pelicula_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $pelicula = $resultado->fetch_assoc();


        // Cambiar el estado de la película a "Alquilada" (estado_id 3)
        if (actualizarEstadoPelicula($conexion, $pelicula_id, 3)) {  // 3 es el estado "Alquilada"
            // Registrar la operación de alquiler en la tabla Operaciones
            if (registrarOperacion($conexion, $cliente_id, $pelicula_id)) {
                // Registrar el historial de la acción
                $codigo_operacion = $conexion->insert_id;  // El id de la operación insertada
                if (registrarHistorial($conexion, $cliente_id, $pelicula_id, $codigo_operacion, $fecha_devolucion)) {
                    echo "<p>Película alquilada correctamente.</p>";
                } else {
                    echo "<p>Hubo un error al registrar el historial de la acción.</p>";
                }
            } else {
                echo "<p>Hubo un error al registrar la operación de alquiler.</p>";
            }
        } else {
            echo "<p>Hubo un error al actualizar el estado de la película.</p>";
        }
    } elseif (isset($_POST['no_disponible'])) {
        // Marcar película como no disponible (sin insertar datos en la base de datos por ahora)
        echo "<p class='success'>La película ha sido marcada como no disponible (sin cambios en base de datos).</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Película</title>
    <link rel="stylesheet" type="text/css" href="../estilos/estilo.css">
</head>

<body>
    <h1>Gestionar Película</h1>
    <p><a href='gestionarPeliculas.php'>Volver a la lista de películas</a></p>

    <?php if ($tipo_usuario == 1): // Vista para trabajadores 
    ?>
        <!-- Mostrar diferentes formularios según el estado de la película -->

        <?php if ($estado_pelicula == 1): // Disponible 
        ?>
            <!-- Formulario si la película está disponible -->
            <form action="gestionarPelicula.php?id_pelicula=<?php echo $id_pelicula; ?>" method="post">
                <label for="criterio">Buscar Cliente (ID o Nombre):</label><br>
                <input type="text" name="criterio" required><br><br>
                <input type="submit" name="buscar_cliente" value="Buscar Cliente">
            </form>

            <?php if ($cliente_encontrado): ?>
                <h2>Cliente Encontrado</h2>
                <p>Nombre: <?php echo $cliente['nombre']; ?></p>
                <p>ID: <?php echo $cliente['id']; ?></p>

                <!-- Formulario para alquilar o marcar como no disponible -->
                <form action="gestionarPelicula.php?id_pelicula=<?php echo $id_pelicula; ?>" method="post">
                    <input type="hidden" name="cliente_id" value="<?php echo $cliente['id']; ?>">
                    <input type="hidden" name="pelicula_id" value="<?php echo $id_pelicula; ?>">

                    <label for="fecha_alquiler">Fecha de Alquiler:</label><br>
                    <input type="date" name="fecha_alquiler" required><br><br>

                    <label for="fecha_devolucion">Fecha de Devolución:</label><br>
                    <input type="date" name="fecha_devolucion" required><br><br>

                    <input type="submit" name="alquilar" value="Alquilar">
                    <input type="submit" name="no_disponible" value="No Disponible">
                </form>
            <?php endif; ?>

        <?php elseif ($estado_pelicula == 2): // Reservada 
        ?>
            <!-- Si la película está reservada -->
            <p>La película está reservada y no puede ser alquilada en este momento.</p>

        <?php elseif ($estado_pelicula == 3): // Alquilada 
        ?>
            <!-- Si la película está alquilada -->
            <p>La película ya está alquilada y no puede ser alquilada nuevamente.</p>

        <?php else: ?>
            <p>Estado desconocido de la película.</p>
        <?php endif; ?>

    <?php elseif ($tipo_usuario == 2): // Vista para clientes 
    ?>
        <p>No tienes permisos para gestionar películas.</p>
    <?php else: ?>
        <p>Error: Tipo de usuario no reconocido.</p>
    <?php endif; ?>
</body>

</html>