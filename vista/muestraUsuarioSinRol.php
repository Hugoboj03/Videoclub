<head>
   <title>Usuarios sin ROL</title>
   <link rel="stylesheet" type="text/css" href="../estilos/estilo.css">
</head>


<?php
// Incluir bibliotecas de funciones
include("../modelo/conexion.php"); 
include('../modelo/sesion.php');
include('saludo.php');

// Obtener los datos del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST" ) 
{
    $id_usuario = $_POST["id_usuario"];
    $tipo_usuario = $_POST["tipo_usuario"];
  

    $instruccion = "UPDATE usuarios SET tipo_usuario = '$tipo_usuario' WHERE id = $id_usuario";
    // Ejecución de la actualizacion del registro
    $resultado = mysqli_query($conexion, $instruccion);

}

echo"<h1>Asignación de ROLES de usuario</h1>";

$instruccion = "SELECT * FROM usuarios WHERE tipo_usuario is NULL";
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
         <table align="center">
         <tr>
             <th>Nombre</th>
             <th>Usuario</th>
             <th>Mail</th>
             <th>Tipo Usuario</th>
             <th>Asignar ROL</th>         
         </tr>

         <?php
         while ($resultado = mysqli_fetch_assoc($consulta)) 
         {  ?>
            <tr>
            <td><?php echo $resultado['nombre']; ?></td>
            <td><?php echo $resultado['usuario']; ?></td> 
            <td><?php echo $resultado['email']; ?></td> 
            
            <form action="muestraUsuarioSinRol.php" method="post">
            <td>
                <select name="tipo_usuario">      
                <option value="Administrador">Administrador</option>';         
                <option value="Consultor">Consultor</option>          
                <option value="Moderador">Moderador</option>
                </select>
            </td>

            <td>
            <input type="hidden" name="id_usuario" value="<?php echo $resultado['id']; ?>">
            <input type="submit" value="Modificar">
            </form></td><?php

         }
        }
    }   ?>
            </tr>
         </table>
    <br>
<P>[ <A HREF='../index.php'>Volver al Index</A> ]</P>