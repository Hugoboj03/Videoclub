<?php
include("../modelo/conexion.php"); 
include('../modelo/sesion.php');
include('saludo.php');
?>

<h1>Inserción de nueva noticia</h1>

<form class="borde" action="../modelo/funcionesInsert.php" name="inserta" method="POST" enctype="multipart/form-data">
    <p><label>Título: *</label>
        <input type="text" name="titulo" size="50" maxlength="50" >
    </p>

    <p><label>Texto: *</label>
        <textarea cols="45" rows="5" name="texto"></textarea>
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
  
      echo "<option value='$fila[id]'>$fila[nombre]</option>";
  }
  // Cerrar el desplegables
  echo "</select>"; ?>
    </p>

    <p><label>Imagen:</label>
        <input type="file" name="imagen">
    </p>

    <p><input type="submit" name="insertar" value="Insertar noticia"></p>
</form>
<p>NOTA: los datos marcados con (*) deben ser rellenados obligatoriamente</p>
<br>

<P>[ <A HREF='../index.php'>Volver al Index</A> ]</P>
