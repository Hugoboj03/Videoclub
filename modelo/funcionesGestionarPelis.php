<?php
function buscarCliente($conexion, $criterio) {
    $consulta = "SELECT id, nombre FROM usuarios WHERE id = ? OR nombre LIKE ?";
    $stmt = $conexion->prepare($consulta);
    $criterio_busqueda = "%$criterio%";
    $stmt->bind_param("is", $criterio, $criterio_busqueda);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
}

function actualizarEstadoPelicula($conexion, $pelicula_id, $nuevo_estado_id) {
    $consulta_estado = "UPDATE peliculas SET estado_id = ? WHERE id = ?";
    $stmt = $conexion->prepare($consulta_estado);
    $stmt->bind_param("ii", $nuevo_estado_id, $pelicula_id);
    return $stmt->execute();
}

function registrarOperacion($conexion, $usuario_id, $pelicula_id) {
    $consulta_usuario = "SELECT id FROM usuarios WHERE id = ?";
    $stmt_usuario = $conexion->prepare($consulta_usuario);
    $stmt_usuario->bind_param("i", $usuario_id);
    $stmt_usuario->execute();
    $resultado_usuario = $stmt_usuario->get_result();

    if ($resultado_usuario->num_rows === 0) {
        echo "<p>Error: El usuario con ID $usuario_id no existe.</p>";
        return false; // Detener si el usuario no existe
    }

    // Verificar que los valores no estén vacíos
    if (empty($usuario_id) || empty($pelicula_id)) {
        echo "<p>Error: ID de usuario o película no válido.</p>";
        return false;
    }

    // Registrar operación
    $fecha_operacion = date("Y-m-d H:i:s"); // Fecha y hora actual
    $consulta_operacion = "INSERT INTO operaciones (usuario_id, pelicula_id, fecha_operacion) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($consulta_operacion);
    $stmt->bind_param("iis", $usuario_id, $pelicula_id, $fecha_operacion);

    if ($stmt->execute()) {
        return true;
    } else {
        echo "<p>Error al registrar operación: " . $stmt->error . "</p>";
        return false;
    }
}

function registrarHistorial($conexion, $usuario_id, $pelicula_id, $codigo_operacion, $fecha_devolucion, $tipo_accion_id, $estado_devolucion_id) {
    $tipo_accion_id = 1; // 1 para alquiler
    $estado_devolucion_id = 1; //1 para no devuelta
    $fecha_accion = date("Y-m-d H:i:s"); // Fecha y hora actual

    
    
    $estado_devolucion_id = NULL; // No aplica aún en el alquiler

    
    
    $consulta_historial = "INSERT INTO historial (usuario_id, pelicula_id, tipo_accion_id, fecha_accion, fecha_prevista_devolucion ,estado_devolucion_id, codigo_operacion) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($consulta_historial);
    $stmt->bind_param("iiissii", $usuario_id, $pelicula_id, $tipo_accion_id, $fecha_accion, $fecha_devolucion, $estado_devolucion_id, $codigo_operacion, );
    return $stmt->execute();
}



?>