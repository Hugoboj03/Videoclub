<?php
//include("../modelo/conexion.php");

$consulta = "SELECT nombre FROM categoria WHERE id=$categoria";
$resultado = mysqli_query($conexion, $consulta);
while ($busco = mysqli_fetch_assoc($resultado)) 
{
   $nom_categoria= $busco['nombre'];
}
mysqli_close($conexion);?>

<h1>Gestión de noticias</h1>
<h2>Resultado de la actualización de la noticia</h2>
La noticia ha sido actualizada correctamente:
<ul>
<li>Título: <?=$titulo?></li>
<li>Texto: <?=$texto?></li>
<li>Categoría: <?=$nom_categoria?></li>
<li>Fecha: <?=date ("d-m-Y")?></li>

<?php
if ($nombreFichero != "") 
{   ?>
    <li>Imagen: <a target='_blank' href="../img/<?=$nombreFichero?>"> <?=$nombreFichero?> </a>    <?php
} 
else 
{?>
    <li>Imagen: (no hay) </li>   <?php
}   ?>
</ul>

<br>
[ <a href='../vista/mostrar_noticias.php'>Volver</a> ]
