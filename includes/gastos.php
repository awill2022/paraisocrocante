<?php
function registrarGasto($conn, $descripcion, $monto, $categoria) {
    $stmt = $conn->prepare("INSERT INTO gastos (descripcion, monto, categoria) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $descripcion, $monto, $categoria);
    $stmt->execute();
    $stmt->close();
}

function obtenerGastos($conn, $fecha = null, $categoria = null) {
    $query = "SELECT * FROM gastos WHERE 1=1";
    $params = [];
    $types = "";

    if ($fecha) {
        $query .= " AND DATE(fecha) = ?";
        $params[] = $fecha;
        $types .= "s";
    }
    if ($categoria) {
        $query .= " AND categoria = ?";
        $params[] = $categoria;
        $types .= "s";
    }
    $query .= " ORDER BY fecha DESC";

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $gastos = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $gastos;
}
?>