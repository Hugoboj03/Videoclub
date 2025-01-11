<?php
include("conexion.php");

include('sesion.php');
include('../vista/saludo.php');


// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["modificar"])) 
{
    $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
    $texto = isset($_POST['texto']) ? $_POST['texto'] : '';
    $categoria = $_POST["categoria"];
    $id_noticia = $_POST["id_noticia"];
    
    //Compruebo si ha introducido los dos campos obligatorios
    if($titulo=="" || $texto=="")
    {
        // Mostrar el formulario de registro incompleto
        header("Location: ../vista/formulario_modificar.php?id_noticia=$id_noticia&error=Error al modificar la noticia");
    }
    //Si he recibido los dos campos obligatorios
    else
    {      
        $nombreFichero="vacio"; 
        
        //Compruebo si la noticia recibida tiene ya insertada una imagen
        $consulta = "SELECT imagen FROM noticias WHERE id=$id_noticia";
        $resultado = mysqli_query($conexion, $consulta);

        // Contar el número total de filas en el resultado de la consulta
        $total=mysqli_num_rows($resultado);

        // Obtener la primera fila como un array asociativo
        $fila = mysqli_fetch_assoc($resultado);
    
        // Verificar si hay al menos una fila y si tiene un valor en la columna 'imagen'
        if ($total>0 && !empty($fila['imagen'])) 
        {
            $nombreFichero = $fila['imagen'];

            if ($nombreFichero!="vacio" &&  isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK && $_FILES['imagen']['size'] > 0)
            {           
                $rutaFicheroDestino = '../img/' . $nombreFichero;
                // Elimino la imagen para después subir la imagen nueva
                unlink($rutaFicheroDestino);
            }           
        }

        //compruebo si recibo una nueva imagen para guardarla en la carpeta
        if($_FILES && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK &&  $_FILES['imagen']['size'] > 0)
        {
            //Recojo el nombre de la nueva imagen que se quiere subir
            $nombreFichero = $_FILES['imagen']['name'];
            $rutaFicheroDestino = '../img/' . $nombreFichero;
            $seHaSubido = move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaFicheroDestino);
        }     
       

        //inserto el registro en la base de datos
        $fecha = date("Y-m-d");

        $instruccion = "UPDATE noticias SET titulo = '$titulo', texto = '$texto', categoria_id='$categoria', imagen='$nombreFichero' WHERE id = $id_noticia";
        // Ejecución de la actualizacion del registro
        $resultado = mysqli_query($conexion, $instruccion);

        
        //Una vez insertado muestro la pagina con el resultado
        include("../vista/noticia_actualizada.php");
    }
} 

//Si no he recibido el POST
else 
{
    // Mostrar el formulario de registro "LIMPIO"
    header("Location: vista/mostrar_noticias.php");
}

?>