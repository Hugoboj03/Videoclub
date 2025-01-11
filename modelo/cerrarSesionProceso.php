<?php
// Inicia la sesión
session_start();

// Destruye la sesión, eliminando todas las variables de sesión
session_destroy();

// Redirige a la página de inicio de sesión u otra página de tu elección
header("Location: ../vista/login.php");
exit();
?>