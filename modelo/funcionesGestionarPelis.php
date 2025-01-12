<?php
function buscarCliente($conexion, $criterio) {
    $buscar_por = is_numeric($criterio) ? "id" : "nombre";
    $consulta_cliente = "SELECT * FROM usuarios WHERE $buscar_por = ?";
    $stmt = $conexion->prepare($consulta_cliente);
    $stmt->bind_param("s", $criterio);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function actualizarEstadoPelicula($conexion, $pelicula_id, $nuevo_estado_id) {
    $consulta_estado = "UPDATE peliculas SET estado_id = ? WHERE id = ?";
    $stmt = $conexion->prepare($consulta_estado);
    $stmt->bind_param("ii", $nuevo_estado_id, $pelicula_id);
    return $stmt->execute();
}

function registrarOperacion($conexion, $usuario_id, $pelicula_id) {
    $fecha_operacion = date("Y-m-d H:i:s"); // Fecha y hora actual
    $consulta_operacion = "INSERT INTO operaciones (usuario_id, pelicula_id, fecha_operacion) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($consulta_operacion);
    $stmt->bind_param("iis", $usuario_id, $pelicula_id, $fecha_operacion);
    return $stmt->execute();
}

function registrarHistorial($conexion, $usuario_id, $pelicula_id, $codigo_operacion, $fecha_devolucion) {
    $tipo_accion_id = 1; // 1 para alquiler
    $fecha_accion = date("Y-m-d H:i:s"); // Fecha y hora actual
    $estado_devolucion_id = NULL; // No aplica aún en el alquiler
    
    $consulta_historial = "INSERT INTO historial (usuario_id, pelicula_id, tipo_accion_id, fecha_accion, estado_devolucion_id, codigo_operacion, fecha_prevista_devolucion) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($consulta_historial);
    $stmt->bind_param("iiissii", $usuario_id, $pelicula_id, $tipo_accion_id, $fecha_accion, $estado_devolucion_id, $codigo_operacion, $fecha_devolucion);
    return $stmt->execute();
}

?>