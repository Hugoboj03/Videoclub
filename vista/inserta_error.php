<?php

include('../modelo/sesion.php');
include('saludo.php');

$titulo = $_GET["titulo"];
$texto = $_GET["texto"];
$categoria = $_GET["categoria"];
?>

<h1>Inserción de nueva noticia</h1>

<form class="borde" action="../modelo/funcionesInsert.php" name="inserta" method="POST" enctype="multipart/form-data">

   <p><label>Título: *</label>
      <input type="text" name="titulo" size="50" maxlength="50" value="<?= $titulo ?>">
      <?php if ($titulo == "") { ?> <span style='color: red;'>¡Debe introducir el título de la noticia!</span><?php } ?>

   <p><label>Texto: *</label>
      <textarea cols="45" rows="5" name="texto"><?= $texto ?></textarea>
      <?php
      if ($texto == "") { ?> <span style='color: red;'>¡Debe introducir el texto de la noticia!</span><?php }
                                                                                             ?>

   <p><label>Categoría:</label>
      <select name="categoria">
         <option value="pro" <?php if ($categoria == 'pro') echo 'selected'; ?>>promociones</option>
         <option value="ofe" <?php if ($categoria == 'ofe') echo 'selected'; ?>>ofertas</option>
         <option value="cos" <?php if ($categoria == 'cos') echo 'selected'; ?>>costas</option>
      </select>
   </p>

   <p><label>Imagen:</label>
      <input type="hidden" name="MAX_FILE_SIZE" value="102400">
      <input type="file" size="44" name="imagen">
   </p>

   <p><input type="submit" name="insertar" value="Insertar noticia"></p>
</form>

<p>NOTA: los datos marcados con (*) deben ser rellenados obligatoriamente</p>

<P>[ <A HREF='../index.php'>Volver al Index</A> ]</P>