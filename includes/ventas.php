<?php
function registrarVenta($conn, $producto, $cantidad, $total, $metodo_pago) {
    $stmt = $conn->prepare("INSERT INTO ventas (producto, cantidad, total, metodo_pago) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sids", $producto, $cantidad, $total, $metodo_pago);
    $stmt->execute();
    $stmt->close();
}

function registrarCierreDiario($conn, $total_efectivo, $total_transferencia, $total_deuna, $fecha, $notas) {
    $stmt = $conn->prepare("INSERT INTO cierres_diarios (total_efectivo, total_transferencia, total_deuna, fecha, notas) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("dddss", $total_efectivo, $total_transferencia, $total_deuna, $fecha, $notas);
    $stmt->execute();
    $stmt->close();
}

function obtenerVentas($conn, $fecha = null, $metodo_pago = null) {
    $query = "SELECT * FROM ventas WHERE 1=1";
    $params = [];
    $types = "";

    if ($fecha) {
        $query .= " AND DATE(fecha) = ?";
        $params[] = $fecha;
        $types .= "s";
    }
    if ($metodo_pago) {
        $query .= " AND metodo_pago = ?";
        $params[] = $metodo_pago;
        $types .= "s";
    }
    $query .= " ORDER BY fecha DESC";

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $ventas = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $ventas;
}

function obtenerCierresDiarios($conn, $fecha = null) {
    $query = "SELECT * FROM cierres_diarios WHERE 1=1";
    $params = [];
    $types = "";

    if ($fecha) {
        $query .= " AND fecha = ?";
        $params[] = $fecha;
        $types .= "s";
    }
    $query .= " ORDER BY fecha DESC";

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $cierres = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $cierres;
}
?>