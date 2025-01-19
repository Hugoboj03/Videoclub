<?php
// Incluir bibliotecas de funciones y sesiones
include("../modelo/conexion.php");
include("../modelo/sesion.php");
include("saludo.php");

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Películas</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>

<body>

    <h1>Gestionar Películas</h1>

    <p><a href='../index.php'>Volver al Index</a></p>
    <?php
    // Mostrar el enlace "Añadir Película" solo si el usuario es un empleado
    if ($tipo_usuario == 1) { // 1 = Empleado
        echo "<p><a href='insertar_pelicula.php'>Añadir Película</a></p>";
    }
    ?>

    <?php
    // Obtener el estado seleccionado (por defecto "todos") y el término de búsqueda
    $estado_id = isset($_GET['estado_id']) ? $_GET['estado_id'] : 'todos';
    $buscar_texto = isset($_GET['nombre']) ? $_GET['nombre'] : '';

    // Crear el formulario para filtrar por estado y búsqueda de texto
    echo "<form action='gestionarPeliculas.php' method='get'>";
    echo "<label for='estado_id'>Filtrar por Estado:</label>";

    // Realizar la consulta para obtener los estados disponibles
    $consulta_estado = "SELECT id, nombre FROM estadospeliculas";
    $resultado_estado = mysqli_query($conexion, $consulta_estado);

    // Crear el desplegable de estados
    echo "<select name='estado_id'>";

    // Agregar la opción "Todos"
    echo "<option value='todos'>Todos</option>";

    // Recorrer los resultados y agregar las opciones de estado
    while ($fila_estado = mysqli_fetch_assoc($resultado_estado)) {
        echo "<option value='$fila_estado[id]'";
        if ($fila_estado['id'] == $estado_id)
            echo " selected"; // Seleccionar el estado previamente elegido
        echo ">$fila_estado[nombre]</option>";
    }

    // Cerrar el desplegable y agregar el botón
    echo "</select>";

    // Campo de búsqueda por nombre (título)
    echo "<label for='nombre'>Buscar por nombre (título):</label>";
    echo "<input type='text' name='nombre' value='$buscar_texto' />";

    // Agregar el botón de filtro
    echo "<input type='submit' value='Filtrar' />";
    echo "</form>";

    // Consulta de películas con el filtro de estado y búsqueda por texto
    $instruccion = "
    SELECT peliculas.id, peliculas.titulo, generos.nombre AS genero, peliculas.anio, estadospeliculas.nombre AS estado 
    FROM peliculas
    INNER JOIN generos ON peliculas.genero_id = generos.id
    INNER JOIN estadospeliculas ON peliculas.estado_id = estadospeliculas.id";

    // Condición de filtro por estado
    if ($estado_id != 'todos') {
        $instruccion .= " WHERE peliculas.estado_id = $estado_id";
    }

    // Condición de búsqueda por texto
    if (!empty($buscar_texto)) {
        $instruccion .= ($estado_id != 'todos' ? " AND" : " WHERE") . " peliculas.titulo LIKE '%$buscar_texto%'";
    }

    $instruccion .= " ORDER BY peliculas.titulo ASC"; // Ordenar por título

    $consulta = mysqli_query($conexion, $instruccion);

    if ($consulta == FALSE) {
        echo "Error en la ejecución de la consulta.<br />";
    } else {
        // Mostrar resultados de la consulta
        $nfilas = mysqli_num_rows($consulta);
        if ($nfilas > 0) {
    ?>
            <table>
                <tr>
                    <th>Título</th>
                    <th>Género</th>
                    <th>Año</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                <?php
                while ($resultado = mysqli_fetch_assoc($consulta)) {
                ?>
                    <tr>
                        <td><?php echo $resultado['titulo']; ?></td>
                        <td><?php echo $resultado['genero']; ?></td>
                        <td><?php echo $resultado['anio']; ?></td>

                        <td><?php echo $resultado['estado']; ?></td>


                        <td>
                            <?php

                            if ($tipo_usuario == 2 && $resultado['estado'] != 'Disponible') {
                                echo "";
                            } else {
                                echo "<a href='gestionarPelicula.php?id_pelicula=" . $resultado['id'] . "'>Gestionar</a>";
                            }

                            ?>

                        </td>
                    </tr>
                <?php
                }
                ?>
            </table>
    <?php
        } else {
            echo "No hay películas disponibles.";
        }
    }

    // Cerrar conexión
    mysqli_close($conexion);
    ?>

    <br>

</body>

</html>