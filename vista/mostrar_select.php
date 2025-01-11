<?php
// Obtener el estado seleccionado (por defecto, "todos")
if (isset($_GET['estado_id'])) {
    $estado_id = $_GET['estado_id'];
} else {
    $estado_id = 'todos';
}

echo "<form action='gestionarPeliculas.php' method='get'>";

// Realizar la consulta para obtener los estados
$consulta = "SELECT id, nombre FROM estadospeliculas";
$resultado = mysqli_query($conexion, $consulta);

// Crear el desplegable
echo "<select name='estado_id'>";

// Agregar la opción "Todos" al desplegable
echo "<option value='todos'>Todos</option>";

// Recorrer los resultados de la consulta y agregar las opciones al desplegable
while ($fila = mysqli_fetch_assoc($resultado)) {
    echo "<option value='$fila[id]'";

    // Si el estado es el seleccionado, marcarlo como seleccionado
    if ($fila['id'] == $estado_id) {
        echo " selected";
    }

    echo ">$fila[nombre]</option>";
}

// Cerrar el desplegable
echo "</select>";



// Agregar el botón para enviar el formulario
echo "<input type='submit' value='Filtrar' />";

echo "</form>";
?>
