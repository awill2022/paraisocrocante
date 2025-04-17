<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre Diario - Paraíso Crocante</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <h1>Cierre Diario</h1>
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
            <h2>Registrar Cierre Diario</h2>
            <form method="POST" action="">
                <input type="date" name="fecha" required value="<?php echo date('Y-m-d'); ?>">
                <input type="number" name="total_efectivo" placeholder="Total Efectivo ($)" step="0.01" min="0" required>
                <input type="number" name="total_transferencia" placeholder="Total Transferencia ($)" step="0.01" min="0" required>
                <input type="number" name="total_deuna" placeholder="Total DeUna ($)" step="0.01" min="0" required>
                <textarea name="notas" placeholder="Notas (opcional)" rows="4"></textarea>
                <button type="submit">Registrar Cierre</button>
            </form>
        </div>

        <div class="filters">
            <input type="date" id="filtroFecha" name="filtroFecha" value="<?php echo isset($_GET['fecha']) ? htmlspecialchars($_GET['fecha']) : ''; ?>">
            <button onclick="filtrar()">Filtrar</button>
        </div>

        <?php
        require '../config/db.php';
        require '../includes/ventas.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $total_efectivo = $_POST['total_efectivo'];
            $total_transferencia = $_POST['total_transferencia'];
            $total_deuna = $_POST['total_deuna'];
            $fecha = $_POST['fecha'];
            $notas = $_POST['notas'];
            try {
                registrarCierreDiario($conn, $total_efectivo, $total_transferencia, $total_deuna, $fecha, $notas);
                echo "<p>Cierre diario registrado exitosamente.</p>";
            } catch (mysqli_sql_exception $e) {
                echo "<p>Error: Ya existe un cierre para esta fecha.</p>";
            }
        }

        $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;
        $cierres = obtenerCierresDiarios($conn, $fecha);

        if (count($cierres) > 0) {
            echo "<table>
                    <tr>
                        <th>Fecha</th>
                        <th>Efectivo</th>
                        <th>Transferencia</th>
                        <th>DeUna</th>
                        <th>Total</th>
                        <th>Notas</th>
                    </tr>";
            foreach ($cierres as $cierre) {
                $total = $cierre['total_efectivo'] + $cierre['total_transferencia'] + $cierre['total_deuna'];
                echo "<tr>
                        <td>" . $cierre['fecha'] . "</td>
                        <td>$" . number_format($cierre['total_efectivo'], 2) . "</td>
                        <td>$" . number_format($cierre['total_transferencia'], 2) . "</td>
                        <td>$" . number_format($cierre['total_deuna'], 2) . "</td>
                        <td>$" . number_format($total, 2) . "</td>
                        <td>" . htmlspecialchars($cierre['notas'] ?? '') . "</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No hay cierres diarios registrados.</p>";
        }

        $conn->close();
        ?>
    </div>
    <div class="footer">
        <p>© 2025 Paraíso Crocante - Todos los derechos reservados</p>
    </div>

    <script>
        function filtrar() {
            const fecha = document.getElementById('filtroFecha').value;
            window.location.href = `cierre-diario.php?fecha=${fecha}`;
        }
    </script>
</body>
</html>