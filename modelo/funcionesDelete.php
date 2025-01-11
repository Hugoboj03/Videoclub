<?php
include("conexion.php");
include("fecha.php");
include('sesion.php');
include('../vista/saludo.php');


//Compruebo que se ha pulsado el boton de eliminar del formulario
if (isset($_POST['eliminar'])) 
{
    //Compruebo que se ha seleccionado noticias para eliminar
    if (isset($_POST['borrar']) && !empty($_POST['borrar'])) 
    {
        //Cuento el numero de noticias a borrar
        $nfilas = count($_POST['borrar']);

       // var_dump($_POST['borrar']);

        for ($i = 0; $i < $nfilas; $i++) 
        {
            //recojo el id que pertenece a la noticia en la tabla noticias
            $idNoticia = $_POST['borrar'][$i];
            
            $consulta = mysqli_query($conexion, "SELECT titulo,imagen,texto,categoria.nombre,fecha 
            FROM noticias INNER JOIN categoria
            ON categoria.id=noticias.categoria_id WHERE noticias.id = $idNoticia");

            $resultado = mysqli_fetch_assoc($consulta); ?>

            <p>Noticia eliminada:</p>
            <ul>
                <li>Título: <?= $resultado['titulo'] ?></li>
                <li>Texto: <?= $resultado['texto'] ?></li>
                <li>Categoría: <?= $resultado['nombre'] ?></li>
                <li>Fecha: <?= date2string($resultado['fecha']) ?></li>
           <?php

           if ($resultado['imagen'] != "") 
           { ?>
               <li>Imagen: <?= $resultado['imagen'] ?></li> <?php
           } 
           else 
           { ?>
               <li>Imagen: (no hay)</li>
               <?php
           } ?>
           </ul>    <?php

           mysqli_query($conexion, "DELETE FROM noticias WHERE id = $idNoticia");

            if ($resultado['imagen'] != "") 
            {
                $nombreFichero = "../img/" . $resultado['imagen'];

                //La función unlink($nombreFichero) en PHP se utiliza para eliminar (borrar) un archivo del sistema de archivos
                unlink($nombreFichero);
            }
        }
        echo "<p>Número total de noticias eliminadas: $nfilas</p>";    
    }
    else
    {
        echo"No ha seleccionado ninguna noticia para eliminar";
    }
}


echo "<P>[ <A HREF='../vista/eliminar_noticias.php'>Volver</A> ]</P>\n";

?>
