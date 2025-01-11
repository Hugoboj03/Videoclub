<br><br><br><br><br><br><br><br>
<?php

//Si no tiene ROL asignado terminamos la ejecución del programa
if($tipo_usuario!="Administrador")
{
    echo"No tienes acceso";
    exit();
}
else
{
    include("modelo/conexion.php"); 

    $instruccion = "SELECT COUNT(*) AS cantidad_nulos FROM usuarios WHERE tipo_usuario IS NULL";
    $consulta = mysqli_query($conexion, $instruccion);
    
    if ($consulta) 
    {
        $fila = mysqli_fetch_assoc($consulta);
        $cantidad_nulos = $fila['cantidad_nulos'];
    
        echo "Nuevos usuarios sin asignar un Rol: ";
        echo "<a href='vista/muestraUsuarioSinRol.php'>$cantidad_nulos</a>";
    } 
    else 
    {
        echo "Error en la consulta: " . mysqli_error($conexion);
    }
}


?>