<?php

if (isset($_GET['id_categoria'])) {$id_categoria = $_GET['id_categoria'];} 
else {$id_categoria = 'todas';}


echo "<form action='mostrar_noticias.php' method='get'>";

//////////////////////////////////////////////////////////////////////////////////////////////////
// Realizar la consulta

$consulta = "SELECT id, nombre FROM categoria";
$resultado = mysqli_query($conexion, $consulta);

// Crear el desplegable
echo "<select name='id_categoria'>";

// Agregar la opción "Todas" al desplegable
echo "<option value='todas'>Todas</option>";

// Recorrer el conjunto de resultados
while ($fila = mysqli_fetch_assoc($resultado)) 
{


    echo "<option value='$fila[id]'";
    if ($fila['id'] == $id_categoria) echo " selected";
    echo ">$fila[nombre]</option>";


}
// Cerrar el desplegables
echo "</select>";

// Agregar el botón
echo "<input type='submit' value='Enviar' name='enviar' />";?>

</form>
