<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Venta - Paraíso Crocante</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <h1>Registrar Venta</h1>
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
            <h2>Nueva Venta</h2>
            <form method="POST" action="">
                <select name="producto" required>
                    <option value="">Selecciona un producto</option>
                    <option value="Helado">Helado</option>
                    <option value="Fresas con Crema">Fresas con Crema</option>
                    <option value="Waffle">Waffle</option>
                    <option value="Crepe">Crepe</option>
                </select>
                <input type="number" name="cantidad" placeholder="Cantidad" min="1" required>
                <input type="number" name="total" placeholder="Total ($)" step="0.01" min="0" required>
                <select name="metodo_pago" required>
                    <option value="">Método de pago</option>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Transferencia">Transferencia</option>
                    <option value="DeUna">DeUna</option>
                </select>
                <button type="submit">Registrar</button>
            </form>
        </div>

        <div class="filters">
            <input type="date" id="filtroFecha" name="filtroFecha" value="<?php echo isset($_GET['fecha']) ? htmlspecialchars($_GET['fecha']) : ''; ?>">
            <select id="filtroMetodoPago" name="filtroMetodoPago">
                <option value="">Método de pago</option>
                <option value="Efectivo" <?php echo isset($_GET['metodo_pago']) && $_GET['metodo_pago'] === 'Efectivo' ? 'selected' : ''; ?>>Efectivo</option>
                <option value="Transferencia" <?php echo isset($_GET['metodo_pago']) && $_GET['metodo_pago'] === 'Transferencia' ? 'selected' : ''; ?>>Transferencia</option>
                <option value="DeUna" <?php echo isset($_GET['metodo_pago']) && $_GET['metodo_pago'] === 'DeUna' ? 'selected' : ''; ?>>DeUna</option>
            </select>
            <button onclick="filtrar()">Filtrar</button>
        </div>

        <?php
        require '../config/db.php';
        require '../includes/ventas.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $producto = $_POST['producto'];
            $cantidad = $_POST['cantidad'];
            $total = $_POST['total'];
            $metodo_pago = $_POST['metodo_pago'];
            registrarVenta($conn, $producto, $cantidad, $total, $metodo_pago);
            echo "<p>Venta registrada exitosamente.</p>";
        }

        $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;
        $metodo_pago = isset($_GET['metodo_pago']) ? $_GET['metodo_pago'] : null;
        $ventas = obtenerVentas($conn, $fecha, $metodo_pago);

        if (count($ventas) > 0) {
            echo "<table>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th>Método de Pago</th>
                    </tr>";
            foreach ($ventas as $venta) {
                echo "<tr>
                        <td>" . $venta['fecha'] . "</td>
                        <td>" . htmlspecialchars($venta['producto']) . "</td>
                        <td>" . $venta['cantidad'] . "</td>
                        <td>$" . number_format($venta['total'], 2) . "</td>
                        <td>" . $venta['metodo_pago'] . "</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No hay ventas registradas.</p>";
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
            const metodoPago = document.getElementById('filtroMetodoPago').value;
            const params = new URLSearchParams();
            if (fecha) params.append('fecha', fecha);
            if (metodoPago) params.append('metodo_pago', metodoPago);
            window.location.href = `registrar-venta.php?${params.toString()}`;
        }
    </script>
</body>
</html>