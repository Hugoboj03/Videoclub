<?php
include('modelo/sesion.php');

// Verificar si el usuario tiene sesión activa
if (!isset($_SESSION['nombre']) || !isset($_SESSION['tipo_usuario'])) {
    header("Location: vista/login.php?error=Debes iniciar sesión");
    exit();
}

// Variables de la sesión
$nombre = $_SESSION['nombre'];
$tipo_usuario = $_SESSION['tipo_usuario']; // 1 = Trabajador, 2 = Cliente

// Mostrar mensaje si el rol no está definido
if (empty($tipo_usuario)) {
    echo "Debe esperar a que un administrador le dé acceso al sistema.";
    exit();
}

// Mostrar saludo
include('vista/saludo.php');

// Panel según el rol del usuario
echo "<h1>Bienvenido, $nombre</h1>";

if ($tipo_usuario == 1) { // Trabajador
    echo "<h2>Panel de control: Trabajador</h2>";
    echo '<ul>
        <li><a href="vista/gestionarPeliculas.php">Gestionar Películas</a></li>
        <li><a href="vista/gestionarClientes.php">Visualizar Clientes</a></li>
    </ul>';
} elseif ($tipo_usuario == 2) { // Cliente
    echo "<h2>Panel de control: Cliente</h2>";
    echo '<ul>
        <li><a href="vista/gestionarPeliculas.php">Listado de Películas</a></li>
        <li><a href="vista/historial.php">Historial</a></li>
        <li><a href="vista/actualizarPerfil.php">Actualizar Perfil</a></li>
    </ul>';
}

// Botón de cierre de sesión
echo '<a href="modelo/cerrarSesionProceso.php">Cerrar sesión</a>';
?>