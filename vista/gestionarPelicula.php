<?php
// Incluir bibliotecas de funciones y sesiones
include("../modelo/conexion.php");
include("../modelo/sesion.php");
include("../modelo/funcionesGestionarPelis.php");
include("saludo.php");

$nombreUsuario = $_SESSION['nombre'];

$mostrarReserva = true;

// Conprobar el tipo de usuario
$tipo_usuario = $_SESSION['tipo_usuario'];

// Coprobar que se haya proporcionado un ID válido de película
$id_pelicula = isset($_GET['id_pelicula']) ? intval($_GET['id_pelicula']) : null;

// Variables
$cliente_encontrado = false;
$cliente = null;

// Obtener el estado de la película
$consulta_estado = "SELECT estado_id FROM peliculas WHERE id = ?";
$stmt = $conexion->prepare($consulta_estado);
$stmt->bind_param("i", $id_pelicula);
$stmt->execute();
$resultado_estado = $stmt->get_result();
$pelicula = $resultado_estado->fetch_assoc();

// Comprobar si se encontró la película
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
    } elseif (isset($_POST['cambiar_disponibilidad'])) {

        $pelicula_id = $id_pelicula; // Obtenemos el ID de la película de la URL
        $consulta_estado_actual = "SELECT estado_id FROM peliculas WHERE id = ?";
        $stmt = $conexion->prepare($consulta_estado_actual);
        $stmt->bind_param("i", $pelicula_id);
        $stmt->execute();
        $resultado_estado = $stmt->get_result();
        $pelicula = $resultado_estado->fetch_assoc();

        if ($pelicula) {
            $estado_actual = $pelicula['estado_id'];

            /**
             * Comprobar nuevo estado
             * 1 Disponible
             * 4 No Disponible
             * @var mixed
             */
            $nuevo_estado = ($estado_actual == 1) ? 4 : 1;

            // Actualizar el estado en la base de datos
            $actualizar_estado = "UPDATE peliculas SET estado_id = ? WHERE id = ?";
            $stmt = $conexion->prepare($actualizar_estado);
            $stmt->bind_param("ii", $nuevo_estado, $pelicula_id);

            if ($stmt->execute()) {
                $mensaje = ($nuevo_estado == 1)
                    ? "La película ahora está disponible."
                    : "La película ahora está no disponible.";
                echo "<p class='success'>$mensaje</p>";
            } else {
                echo "<p class='error'>Error al actualizar el estado de la película.</p>";
            }
        } else {
            echo "<p class='error'>Película no encontrada.</p>";
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
                if (registrarHistorial($conexion, $cliente_id, $pelicula_id, $codigo_operacion, $fecha_devolucion, 1, 1)) {
                    echo "<p>Película alquilada correctamente.</p>";
                } else {
                    echo "Algo salio mal al registrar el historial de la acción.</p>";
                }
            } else {
                echo "<p>Algo salio mal al registrar la operación de alquiler.</p>";
            }
        } else {
            echo "<p>Algo salio mal actualizar el estado de la película.</p>";
        }
    } elseif (isset($_POST['reservar'])) {

        $cliente = buscarCliente($conexion, $nombreUsuario);

        echo "hola";

        // Verificar si la película está disponible
        $consulta_estado = "SELECT estado_id FROM peliculas WHERE id = ?";
        $stmt = $conexion->prepare($consulta_estado);
        $stmt->bind_param("i", $pelicula_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $pelicula = $resultado->fetch_assoc();

        //$cliente_id = $_POST['cliente_id'];
        $pelicula_id = $_POST['pelicula_id'];
        $fecha_reserva = date('Y-m-d');



        // Cambiar el estado de la película a "Reservada" (estado_id 2)
        if (actualizarEstadoPelicula($conexion, $pelicula_id, 2)) {  // 2 es el estado "Reservada"
            // 1. Registrar la operación en la tabla Operaciones
            if (registrarOperacion($conexion, $cliente["id"], $pelicula_id)) {
                // 2. Registrar el historial con tipo_accion_id = 2
                $codigo_operacion = $conexion->insert_id;
                if (registrarHistorial($conexion, $cliente["id"], $pelicula_id, $codigo_operacion, null, 2, 1)) {
                    $mostrarReserva = false;
                    echo "<p>Película reservada correctamente.</p>";
                } else {
                    echo "<p>Hubo un error al registrar el historial de la acción.</p>";
                }
            } else {
                echo "<p>Hubo un error al registrar la operación de reserva.</p>";
            }
        } else {
            echo "<p>Hubo un error al actualizar el estado de la película.</p>";
        }
    } elseif (isset($_POST['no_disponible'])) {

        echo "<p class='success'>La pelicula no esta disponible.</p>";
    } elseif (isset($_POST['alccli'])) {

        //$cliente_id = $_POST['cliente_id']; // El id del cliente que alquila la película
        $pelicula_id = $_POST['pelicula_id'];
        //echo $cliente_id;
        echo $pelicula_id;
        $fecha_alquiler = isset($_POST['fecha_alquiler']) ? $_POST['fecha_alquiler'] : null;
        $fecha_devolucion = isset($_POST['fecha_devolucion']) ? $_POST['fecha_devolucion'] : null;

        $nombreUsuario = $_SESSION['nombre'];
        $cliente = buscarCliente($conexion, $nombreUsuario);
        $cliente_id = $cliente['id'];
        echo $cliente_id;

        if ($cliente_id && $pelicula_id && $fecha_alquiler && $fecha_devolucion) {
            // Cambiar el estado de la película a "Alquilada" (estado_id = 3)
            if (actualizarEstadoPelicula($conexion, $pelicula_id, 3)) {

                //echo "1";
                // Registrar la operación en la tabla de operaciones
                if (registrarOperacion($conexion, $cliente_id, $pelicula_id)) {
                    //echo "2";
                    // Obtener el ID de la operación registrada
                    $codigo_operacion = $conexion->insert_id;

                    // Registrar el historial de la acción
                    if (registrarHistorial($conexion, $cliente_id, $pelicula_id, $codigo_operacion, $fecha_devolucion, 1, 1)) {
                        echo "<p>Película alquilada correctamente.</p>";
                    } else {
                        echo "<p>Error al registrar el historial de la acción.</p>";
                    }
                } else {
                    echo "<p>Error al registrar la operación de alquiler.</p>";
                }
            } else {
                echo "<p>Error al actualizar el estado de la película.</p>";
            }
        } else {
            echo "<p>Por favor, complete todos los campos del formulario.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Película</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>

<body>
    <h1>Gestionar Película</h1>
    <p><a href='gestionarPeliculas.php'>Volver a la lista de películas</a></p>

    <!--Segun el tipo de usuario y estado de la pelicula se mostrara un formulario distinto en el html-->

    <?php if ($tipo_usuario == 1):
    ?>


        <?php if ($estado_pelicula == 1): // Disponible 
        ?>

            <form action="gestionarPelicula.php?id_pelicula=<?php echo $id_pelicula; ?>" method="post">
                <label for="criterio">Buscar Cliente (ID o Nombre):</label><br>
                <input type="text" name="criterio" required><br><br>
                <input type="submit" name="buscar_cliente" value="Buscar Cliente">

            </form>

            <form action="gestionarPelicula.php?id_pelicula=<?php echo $id_pelicula; ?>" method="post">
                <input type="submit" name="cambiar_disponibilidad" value="Cambiar Disponibilidad">
            </form>

            <?php if ($cliente_encontrado): ?>
                <h2>Cliente Encontrado</h2>
                <p>Nombre: <?php echo $cliente['nombre']; ?></p>
                <p>ID: <?php echo $cliente['id']; ?></p>

                <!-- Formulario para alquilar -->
                <form action="gestionarPelicula.php?id_pelicula=<?php echo $id_pelicula; ?>" method="post">
                    <input type="hidden" name="cliente_id" value="<?php echo $cliente['id']; ?>">
                    <input type="hidden" name="pelicula_id" value="<?php echo $id_pelicula; ?>">

                    <label for="fecha_alquiler">Fecha de Alquiler:</label><br>
                    <input type="date" name="fecha_alquiler" required><br><br>

                    <label for="fecha_devolucion">Fecha de Devolución:</label><br>
                    <input type="date" name="fecha_devolucion" required><br><br>

                    <input type="submit" name="alquilar" value="Alquilar">

                </form>
            <?php endif; ?>

        <?php elseif ($estado_pelicula == 2): // Reservada 
        ?>

            <form action="gestionarPelicula.php?id_pelicula=<?php echo $id_pelicula; ?>" method="post">

                <input type="hidden" name="pelicula_id" value="<?php echo $id_pelicula; ?>">
                <input type="date" name="fecha_alquiler" required><br>
                <input type="date" name="fecha_devolucion" required><br>
                <input type="submit" name="alccli" value="Alquilar Pelicula al Cliente">
            </form>



        <?php elseif ($estado_pelicula == 3): // Alquilada 
        ?>
            <!-- Si la película está alquilada -->
            <?php
            // Consulta para obtener el cliente que tiene alquilada la película
            $consulta_cliente = "SELECT usuarios.id AS usuario_id, usuarios.nombre AS nombre_cliente 
                     FROM historial 
                     INNER JOIN usuarios ON historial.usuario_id = usuarios.id 
                     WHERE historial.pelicula_id = ? 
                     AND historial.tipo_accion_id = 1 
                     ORDER BY historial.fecha_accion DESC LIMIT 1";

            $stmt = $conexion->prepare($consulta_cliente);
            $stmt->bind_param("i", $id_pelicula);
            $stmt->execute();
            $resultado_cliente = $stmt->get_result();
            $cliente_alquiler = $resultado_cliente->fetch_assoc();
            ?>
            <p>La película ya está alquilada por <a
                    href="fichaCliente.php?id=<?php echo $cliente_alquiler['usuario_id']; ?>"><?php echo $cliente_alquiler['nombre_cliente'] ?></a>
            </p>

        <?php elseif ($estado_pelicula == 4): // No Disponible 
        ?>
            <!-- Nuevo caso: Película no disponible -->
            <p>La película está marcada como no disponible. Pulse el boton para cambiar su disponibilidad</p>

            <form action="gestionarPelicula.php?id_pelicula=<?php echo $id_pelicula; ?>" method="post">
                <input type="submit" name="cambiar_disponibilidad" value="Cambiar Disponibilidad">
            </form>




        <?php else: ?>
            <p>Estado desconocido de la película.</p>
        <?php endif; ?>

    <?php elseif ($tipo_usuario == 2): // Vista para clientes 
    ?>


        <?php if ($mostrarReserva == false): ?>

            <p>Su pelicula fue reservada</p>


        <?php else: ?>

            <form action="gestionarPelicula.php?id_pelicula=<?php echo $id_pelicula; ?>" method="post">

                <input type="hidden" name="pelicula_id" value="<?php echo $id_pelicula; ?>">


                <input type="submit" name="reservar" value="Reservar">
            </form>

        <?php endif; ?>



    <?php else: ?>
        <p>Error: Tipo de usuario no reconocido.</p>
    <?php endif; ?>
</body>

</html>