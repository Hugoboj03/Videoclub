<?php
$consulta = "SELECT nombre FROM categoria WHERE id=$categoria";
$resultado = mysqli_query($conexion, $consulta);
while ($busco = mysqli_fetch_assoc($resultado)) 
{
   $nom_categoria= $busco['nombre'];
}
mysqli_close($conexion);
?>

<h1>Resultado de la inserción de nueva noticia</h1>
La noticia ha sido recibida correctamente:
<ul>
<li>Título: <?=$titulo?></li>
<li>Texto: <?=$texto?></li>
<li>Categoría: <?=$nom_categoria?></li>
<li>Fecha: <?=date ("d-m-Y")?></li>

<?php
if ($nombreFichero != "") 
{   ?>
    <li>Imagen: <a target='_blank' href="img/<?=$nombreFichero?>"> <?=$nombreFichero?> </a>    <?php
} 
else 
{?>
    <li>Imagen: (no hay) </li>   <?php
}   ?>
</ul>

<br>
[ <a href='../vista/insertar_noticias.php'>Volver</a> ]
