<?php
include("conexion.php"); 
include('../modelo/sesion.php');
include('../vista/saludo.php');

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["insertar"])) 
{
    $titulo = $_POST["titulo"];
    $texto = $_POST["texto"];
    $categoria = $_POST["categoria"];

    //Compruebo si ha introducido los dos campos obligatorios
    if($titulo=="" || $texto=="")
    {
        // Mostrar el formulario de registro incompleto
        header("Location: ../vista/inserta_error.php?titulo=$titulo&texto=$texto&categoria=$categoria");
    }
    //Si he recibido los dos campos obligatorios
    else
    {
       
        //compruebo si hay imagen para guardarla en la carpeta
        if($_FILES && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK &&  $_FILES['imagen']['size'] > 0)
        {
            $nombreFichero = $_FILES['imagen']['name'];
            $rutaFicheroDestino = '../img/' . $nombreFichero;
            $seHaSubido = move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaFicheroDestino);
        }     
        else//no hay imagen para subir
        {
            $nombreFichero="";
        }
       
        //inserto el registro en la base de datos
        $fecha = date("Y-m-d");
        $instruccion = "insert into noticias (titulo, texto, categoria_id, fecha, imagen) values ('$titulo', '$texto', '$categoria', '$fecha', '$nombreFichero')";
        $consulta = mysqli_query($conexion, $instruccion) or die("Fallo en la consulta");
        
        //Una vez insertado muestro la pagina con el resultado
        include("../vista/noticia_insertada.php");
    }
} 

//Si no he recibido el POST
else 
{
    // Mostrar el formulario de registro "LIMPIO"
    header("Location: vista/insertar_noticias.php");
}

?>