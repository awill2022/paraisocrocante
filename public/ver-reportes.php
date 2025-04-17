<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Reportes - Paraíso Crocante</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <h1>Reportes Financieros</h1>
    </header>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="registrar-venta.php">Registrar Venta</a></li>
            <li><a href="cierre-diario.php">Cierre Diario</a></li>
            <li><a href="registrar-gasto.php">Registrar Gasto</a></li>
            <li><a href="ver-reportes.php">Ver Reportes</a></li>
        </ul>
    </nav>
    <div class="container">
        <div class="form-section">
            <h2>Filtrar Reporte</h2>
            <form method="GET" action="">
                <input type="date" name="fecha_inicio" value="<?php echo isset($_GET['fecha_inicio']) ? htmlspecialchars($_GET['fecha_inicio']) : ''; ?>" required>
                <input type="date" name="fecha_fin" value="<?php echo isset($_GET['fecha_fin']) ? htmlspecialchars($_GET['fecha_fin']) : ''; ?>" required>
                <button type="submit">Filtrar</button>
            </form>
        </div>

        <?php
        require '../config/db.php';
        require '../includes/reportes.php';

        $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
        $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;
        $reporte = obtenerReporte($conn, $fecha_inicio, $fecha_fin);

        echo "<h3>Resumen Financiero</h3>";
        echo "<p><strong>Ventas por Producto:</strong> $" . number_format($reporte['total_ventas_productos'] ?? 0, 2) . "</p>";
        echo "<p><strong>Ventas por Cierre Diario:</strong> $" . number_format($reporte['total_ventas_cierres'] ?? 0, 2) . "</p>";
        echo "<p><strong>Total Ventas:</strong> $" . number_format($reporte['total_ventas'] ?? 0, 2) . "</p>";
        echo "<p><strong>Total Gastos:</strong> $" . number_format($reporte['total_gastos'] ?? 0, 2) . "</p>";
        echo "<p><strong>Ganancia Neta:</strong> $" . number_format($reporte['ganancia'] ?? 0, 2) . "</p>";

        $conn->close();
        ?>
    </div>
    <div class="footer">
        <p>© 2025 Paraíso Crocante - Todos los derechos reservados</p>
    </div>
</body>
</html>