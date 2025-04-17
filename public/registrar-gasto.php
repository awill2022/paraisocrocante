<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Gasto - Paraíso Crocante</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <h1>Registrar Gasto</h1>
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
            <h2>Nuevo Gasto</h2>
            <form method="POST" action="">
                <input type="text" name="descripcion" placeholder="Descripción" required>
                <input type="number" name="monto" placeholder="Monto ($)" step="0.01" min="0" required>
                <select name="categoria" required>
                    <option value="">Selecciona una categoría</option>
                    <option value="Insumos">Fresas</option>
                    <option value="Insumos">Desechables</option>
                    <option value="Servicios">Servicios</option>
                    <option value="Transporte">Transporte</option>
                    <option value="Personal">Personal</option>
                    <option value="Otros">Otros</option>
                </select>
                <button type="submit">Registrar</button>
            </form>
        </div>

        <div class="filters">
            <input type="date" id="filtroFecha" name="filtroFecha" value="<?php echo isset($_GET['fecha']) ? htmlspecialchars($_GET['fecha']) : ''; ?>">
            <select id="filtroCategoria" name="filtroCategoria">
                <option value="">Categoría</option>
                <option value="Insumos" <?php echo isset($_GET['categoria']) && $_GET['categoria'] === 'Insumos' ? 'selected' : ''; ?>>Insumos</option>
                <option value="Servicios" <?php echo isset($_GET['categoria']) && $_GET['categoria'] === 'Servicios' ? 'selected' : ''; ?>>Servicios</option>
                <option value="Transporte" <?php echo isset($_GET['categoria']) && $_GET['categoria'] === 'Transporte' ? 'selected' : ''; ?>>Transporte</option>
                <option value="Personal" <?php echo isset($_GET['categoria']) && $_GET['categoria'] === 'Personal' ? 'selected' : ''; ?>>Personal</option>
                <option value="Otros" <?php echo isset($_GET['categoria']) && $_GET['categoria'] === 'Otros' ? 'selected' : ''; ?>>Otros</option>
            </select>
            <button onclick="filtrar()">Filtrar</button>
        </div>

        <?php
        require '../config/db.php';
        require '../includes/gastos.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $descripcion = $_POST['descripcion'];
            $monto = $_POST['monto'];
            $categoria = $_POST['categoria'];
            registrarGasto($conn, $descripcion, $monto, $categoria);
            echo "<p>Gasto registrado exitosamente.</p>";
        }

        $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;
        $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;
        $gastos = obtenerGastos($conn, $fecha, $categoria);

        if (count($gastos) > 0) {
            echo "<table>
                    <tr>
                        <th>Fecha</th>
                        <th>Descripción</th>
                        <th>Monto</th>
                        <th>Categoría</th>
                    </tr>";
            foreach ($gastos as $gasto) {
                echo "<tr>
                        <td>" . $gasto['fecha'] . "</td>
                        <td>" . htmlspecialchars($gasto['descripcion']) . "</td>
                        <td>$" . number_format($gasto['monto'], 2) . "</td>
                        <td>" . $gasto['categoria'] . "</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No hay gastos registrados.</p>";
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
            const categoria = document.getElementById('filtroCategoria').value;
            const params = new URLSearchParams();
            if (fecha) params.append('fecha', fecha);
            if (categoria) params.append('categoria', categoria);
            window.location.href = `registrar-gasto.php?${params.toString()}`;
        }
    </script>
</body>
</html>