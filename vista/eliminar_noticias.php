<head>
   <title>Eliminar noticias</title>
   <link rel="stylesheet" type="text/css" href="../estilos/estilo.css">
</head>


<?php
// Incluir bibliotecas de funciones
include("../modelo/fecha.php");
include("../modelo/conexion.php");
include('../modelo/sesion.php');
include('saludo.php');
?>

<h1>Eliminar noticias</h1>

<?php
$consulta = mysqli_query($conexion, "SELECT titulo,texto,categoria.nombre,categoria_id,fecha,noticias.id,imagen 
FROM noticias INNER JOIN categoria
ON categoria.id=noticias.categoria_id ORDER BY fecha DESC");
$nfilas = mysqli_num_rows($consulta);

if ($nfilas > 0) 
{   ?>
    <form action='../modelo/funcionesDelete.php' method='post'>
    <table>
        <tr>
            <th>Título</th>
            <th>Texto</th>
            <th>Categoría</th>
            <th>Fecha</th>
            <th>Imagen</th>
            <th>Borrar</th>
        </tr>
    <?php
    while ($resultado = mysqli_fetch_assoc($consulta)) 
    {   ?>
        <tr>
        <td><?php echo $resultado['titulo']; ?></td>
        <td><?php echo $resultado['texto']; ?></td>
        <td><?php echo $resultado['nombre']; ?></td>
        <td><?php echo date2string($resultado['fecha']); ?></td>

        <?php
        if ($resultado['imagen'] != "") 
        {   ?>
            <td><a target='_blank' href='../img/<?= $resultado['imagen'] ?>'><img  src='../img/ico-fichero.gif' alt='Imagen asociada'></a></td><?php
        } 
        else 
        {   ?>
           <td>&nbsp;</td><?php
        }   ?>

        <td><input type='checkbox' name='borrar[]' value='<?php echo $resultado['id']; ?>'></td>
        </tr><?php
    }   ?>
    </table>

   <br>

    <input type='submit' name='eliminar' value='Eliminar noticias marcadas'>
    </form> <?php
} 
else 
{
    echo "No hay noticias disponibles";
}

mysqli_close($conexion);
?>
<br>
<a href="../index.php">Volver al Index</a> 


