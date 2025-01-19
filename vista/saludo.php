<?php
$nombreUsuario = $_SESSION['nombre'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Sistema de Gestión de Peliculas</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #3498db;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            font-size: 14px;
        }

        h1 {
            font-size: 16pt;
            font-weight: bold;
            color: #0066CC;
            text-align: center;
        }
    </style>
</head>

<body>
    <header>
        <div>Sistema de Gestión de Noticias</div>

        <div>Bienvenido, <?php echo $nombreUsuario; ?>

            <a href="../modelo/cerrarSesionProceso.php">
                <img src="../img/salir.png" width="20" alt="Cerrar sesión">
            </a>
        </div>
    </header>