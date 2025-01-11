<?php
include("../modelo/conexion.php");
include('../modelo/sesion.php');
include('saludo.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir datos del formulario
    $titulo = $_POST['nombre'];
    $genero_id = $_POST['genero'];
    $anio = $_POST['anio'];


    // Insertar película en la base de datos con estado predeterminado 1
    $consulta = "INSERT INTO peliculas (titulo, genero_id, anio, estado_id) VALUES ('$titulo', $genero_id, $anio, 1)";
    if (mysqli_query($conexion, $consulta)) {
        echo "<p class='success'>La película se ha insertado correctamente.</p>";
    } else {
        echo "<p class='error'>Error al insertar la película: " . mysqli_error($conexion) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar Película</title>
    <link rel="stylesheet" type="text/css" href="../estilos/estilo.css">
</head>

<body>

    <h1>Insertar Película</h1>


    <form action="insertar_pelicula.php" method="post">

        <label for="titulo">Título:</label><br>
        <input type="text" name="nombre" required><br><br>


        <label>Género:</label><br>
        <select name="genero" required>
            <option value="1">Acción</option>
            <option value="4">Ciencia Ficción</option>
            <option value="3">Drama</option>
            <option value="2">Comedia</option>
            <option value="5">Horror</option>
        </select><br><br>


        <label for="anio">Año:</label><br>
        <input type="number" name="anio" required><br><br>


        <input type="submit" value="Insertar Película">
    </form>

    <br>
    <p><a href="../index.php">Volver al Index</a></p>

</body>

</html>