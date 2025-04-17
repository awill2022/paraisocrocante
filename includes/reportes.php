<?php
function obtenerReporte($conn, $fecha_inicio = null, $fecha_fin = null) {
    $query = "SELECT 
                (SELECT SUM(total) FROM ventas WHERE 1=1";
    $params = [];
    $types = "";

    if ($fecha_inicio && $fecha_fin) {
        $query .= " AND fecha BETWEEN ? AND ?";
        $params[] = $fecha_inicio . " 00:00:00";
        $params[] = $fecha_fin . " 23:59:59";
        $types .= "ss";
    }
    $query .= ") as total_ventas_productos,
                (SELECT SUM(total_efectivo + total_transferencia + total_deuna) FROM cierres_diarios WHERE 1=1";
    if ($fecha_inicio && $fecha_fin) {
        $query .= " AND fecha BETWEEN ? AND ?";
        $params[] = $fecha_inicio;
        $params[] = $fecha_fin;
        $types .= "ss";
    }
    $query .= ") as total_ventas_cierres,
                (SELECT SUM(monto) FROM gastos WHERE 1=1";
    if ($fecha_inicio && $fecha_fin) {
        $query .= " AND fecha BETWEEN ? AND ?";
        $params[] = $fecha_inicio . " 00:00:00";
        $params[] = $fecha_fin . " 23:59:59";
        $types .= "ss";
    }
    $query .= ") as total_gastos";

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $reporte = $result->fetch_assoc();
    $stmt->close();

    $reporte['total_ventas'] = ($reporte['total_ventas_productos'] ?? 0) + ($reporte['total_ventas_cierres'] ?? 0);
    $reporte['ganancia'] = $reporte['total_ventas'] - ($reporte['total_gastos'] ?? 0);
    return $reporte;
}
?>