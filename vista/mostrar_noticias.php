<head>
   <title>Consulta de noticias</title>
   <link rel="stylesheet" type="text/css" href="../estilos/estilo.css">
<?php
// Incluir bibliotecas de funciones
include("../modelo/fecha.php");
include("../modelo/conexion.php"); 
include('../modelo/sesion.php');
include('saludo.php');
?>
<h1>Consulta de noticias</h1>

<?PHP
//muestro el menu desplegable para filtrar las noticias
include("../vista/mostrar_select.php");

   if($id_categoria!="todas")
   {

      $instruccion = "SELECT noticias.id,titulo, texto, categoria.nombre, fecha, imagen FROM noticias 
      INNER JOIN categoria ON  noticias.categoria_id = categoria.id WHERE categoria.id=$id_categoria ORDER BY fecha DESC";
   }
   else
   {
      
      $instruccion = "SELECT noticias.id,titulo, texto, categoria.nombre, fecha, imagen FROM noticias 
      INNER JOIN categoria ON  noticias.categoria_id = categoria.id ORDER BY fecha DESC";
   }


   $consulta = mysqli_query($conexion, $instruccion);

   if($consulta == FALSE)
   {
      echo "Error en la ejecución de la consulta.<br />";
   }
   else
   {
      // Mostrar resultados de la consulta
      $nfilas = mysqli_num_rows($consulta);
      if ($nfilas > 0) 
      {  ?>
         <table>
         <tr>
             <th>Título</th>
             <th>Texto</th>
             <th>Categoría</th>
             <th>Fecha</th>
             <th>Imagen</th>

             <?php   
             if($tipo_usuario=="Administrador" || $tipo_usuario=="Moderador")
             {      
               echo" <th>Actualizar</th>";
             }?>

         </tr>
         <?php
         while ($resultado = mysqli_fetch_assoc($consulta)) 
         {  ?>
            <tr>
            <td><?php echo $resultado['titulo']; ?></td>
            <td><?php echo $resultado['texto']; ?></td>
            <td><?php echo $resultado['nombre']; ?></td>
            <td><?php echo date2string($resultado['fecha']); ?></td> <?php

            if ($resultado['imagen'] != "") 
            {  ?>
               <td><a target='_blank' href='../img/<?= $resultado['imagen'] ?>'><img src='../img/ico-fichero.gif' alt='Imagen asociada'></a></td><?php
            } 
            else 
            {  ?>
                <td>&nbsp;</td><?php
            }
            
            if($tipo_usuario=="Administrador" || $tipo_usuario=="Moderador")
            {
            ?>
            <td>
               <form action="formulario_modificar.php" method="post">
                <input type="hidden" name="id_noticia" value="<?php echo $resultado['id']; ?>">
                <input type="submit" value="Modificar">
               </form>
            </td><?php
            }?>

            </tr><?php
         }?>
         </table><?php
      } 
      else 
      {
         echo "No hay noticias disponibles";
      }
   }
   // Cerrar conexión
   mysqli_close($conexion);

?>
<br>
<P>[ <A HREF='../index.php'>Volver al Index</A> ]</P>

