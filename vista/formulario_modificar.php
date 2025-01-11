<?php
include("../modelo/conexion.php");
include('../modelo/sesion.php');
include('saludo.php');

//inicializo el error a vacio
$error="";

//Si recibo la variable "id_noticia" con el metodo GET de funcionesModificar.php es pq ha habido un error al modificar el formulario:
if(isset($_GET["id_noticia"]) && isset($_GET["error"])) 
{
    $id_noticia=$_GET['id_noticia'];
    $error=$_GET['error'];
}

//Recibo la variable "id_noticia" con el metodo POST de mostrar_noticias.php:
elseif(isset($_POST['id_noticia']))
{
    $id_noticia=$_POST['id_noticia'];
}

//Sino se recibe por ningun método:
else
{
    $id_noticia="";
}


if($id_noticia!="")
{
    // Realizar la consulta para obtener los datos de la noticia
    $instruccion = "SELECT * FROM noticias WHERE id = $id_noticia";
    $consulta_modificar = mysqli_query($conexion, $instruccion);

    //mysqli_fetch_assoc devuelve una única fila de resultados como una "matriz asociativa" -> "$datos_noticia"
    //o NULL si no hay más filas en el conjunto de resultados.
    $datos_noticia = mysqli_fetch_assoc($consulta_modificar);
    ?>

    <h1>Modificación de noticia</h1>

    <form class="borde" action="../modelo/funcionesModificar.php" method="POST" enctype="multipart/form-data">

        <p><label>Título: *</label>
            <input type="text" name="titulo" size="50" maxlength="50" value="<?php echo $datos_noticia['titulo']; ?>">
        </p>

        <p><label>Texto: *</label>
            <textarea cols="45" rows="5" name="texto"><?php echo $datos_noticia['texto']; ?></textarea>
        </p>

        <p><label>Categoría:</label>
        <?php
        $consulta = "SELECT id, nombre FROM categoria";
        $resultado = mysqli_query($conexion, $consulta);
        
        // Crear el desplegable
        echo "<select name='categoria'>";      
        // Recorrer el conjunto de resultados
        while ($fila = mysqli_fetch_assoc($resultado)) 
        {
            $selected = ($datos_noticia['categoria'] == $fila['id']) ? 'selected' : '';
            echo "<option value='$fila[id]' $selected>$fila[nombre]</option>";
        }
        echo "<select/>";
        ?>
        </p>

        <p><label>Imagen actual:</label>
            <?php
            if ($datos_noticia['imagen'] != "") 
            {
                echo "<img src='../img/$datos_noticia[imagen]' alt='Imagen actual' width='300'>";
            } 
            else 
            {
                echo "No hay imagen asociada";
            }
            ?>
        </p>

        <p><label>Nueva imagen:</label>
            <input type="file" name="imagen">
        </p>
            <!-- Campo oculto para enviar el ID de la noticia -->
            <input type="hidden" name="id_noticia" value="<?php echo $id_noticia; ?>">

        <p><input type="submit" name="modificar" value="Guardar cambios"></p>
    </form>
    <p>NOTA: los datos marcados con (*) deben ser rellenados obligatoriamente</p>
    <br><?php

    //Muestro el error en el caso de que se haya producido al modificar la noticia
    echo"<span style='color: red;'>$error</span>";
}
else
{
    echo"No se encuentra la noticia";
}?>

<P>[ <a href='mostrar_noticias.php'>Volver al Listado de noticias</a> ] [ <a href='../index.php'>Volver al Index</a> ]</P>
