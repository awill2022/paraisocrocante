<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes Gráficos - Paraíso Crocante</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
</head>
<body>
    <?php
    $page_title = 'Reportes Gráficos';

    ?>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="registrar-venta.php">Registrar Venta</a></li>
            <li><a href="cierre-diario.php">Cierre Diario</a></li>
            <li><a href="registrar-gasto.php">Registrar Gasto</a></li>
            <li><a href="ver-reportes.php">Ver Reportes</a></li>
            <li><a href="reportes-graficos.php">Reportes Gráficos</a></li>
        </ul>
    </nav>
    <div class="container">
        <h2>Reportes Gráficos</h2>
        <div class="filters">
            <input type="date" id="fechaInicio" name="fechaInicio" value="<?php echo isset($_GET['fechaInicio']) ? htmlspecialchars($_GET['fechaInicio']) : date('Y-m-d', strtotime('-7 days')); ?>">
            <input type="date" id="fechaFin" name="fechaFin" value="<?php echo isset($_GET['fechaFin']) ? htmlspecialchars($_GET['fechaFin']) : date('Y-m-d'); ?>">
            <button onclick="filtrar()">Filtrar</button>
        </div>

        <?php
        require '../config/db.php';

        // Obtener fechas de los filtros (por defecto: últimos 7 días)
        $fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : date('Y-m-d', strtotime('-7 days'));
        $fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : date('Y-m-d');

        // Validar fechas
        if (strtotime($fechaInicio) > strtotime($fechaFin)) {
            echo "<p>Error: La fecha de inicio no puede ser mayor que la fecha de fin.</p>";
        } else {
            // Consulta para ventas por método de pago
            $queryVentasMetodo = "SELECT metodo_pago, SUM(total) as total
                                 FROM ventas
                                 WHERE DATE(fecha) BETWEEN ? AND ?
                                 GROUP BY metodo_pago";
            $stmtVentasMetodo = $conn->prepare($queryVentasMetodo);
            $stmtVentasMetodo->bind_param("ss", $fechaInicio, $fechaFin);
            $stmtVentasMetodo->execute();
            $resultVentasMetodo = $stmtVentasMetodo->get_result();
            $ventasMetodo = $resultVentasMetodo->fetch_all(MYSQLI_ASSOC);

            // Preparar datos para el gráfico de pastel
            $metodos = [];
            $totalesMetodo = [];
            foreach ($ventasMetodo as $venta) {
                $metodos[] = $venta['metodo_pago'];
                $totalesMetodo[] = $venta['total'];
            }

            // Consulta para ventas y gastos por día
            $queryVentasGastos = "SELECT DATE(v.fecha) as fecha,
                                        SUM(v.total) as total_ventas,
                                        COALESCE(SUM(g.monto), 0) as total_gastos
                                 FROM ventas v
                                 LEFT JOIN gastos g ON DATE(v.fecha) = DATE(g.fecha)
                                 WHERE DATE(v.fecha) BETWEEN ? AND ?
                                 GROUP BY DATE(v.fecha)
                                 UNION
                                 SELECT DATE(g.fecha) as fecha,
                                        COALESCE(SUM(v.total), 0) as total_ventas,
                                        SUM(g.monto) as total_gastos
                                 FROM gastos g
                                 LEFT JOIN ventas v ON DATE(g.fecha) = DATE(v.fecha)
                                 WHERE DATE(g.fecha) BETWEEN ? AND ?
                                 GROUP BY DATE(g.fecha)";
            $stmtVentasGastos = $conn->prepare($queryVentasGastos);
            $stmtVentasGastos->bind_param("ssss", $fechaInicio, $fechaFin, $fechaInicio, $fechaFin);
            $stmtVentasGastos->execute();
            $resultVentasGastos = $stmtVentasGastos->get_result();
            $ventasGastos = [];
            while ($row = $resultVentasGastos->fetch_assoc()) {
                $fecha = $row['fecha'];
                if (!isset($ventasGastos[$fecha])) {
                    $ventasGastos[$fecha] = ['total_ventas' => 0, 'total_gastos' => 0];
                }
                $ventasGastos[$fecha]['total_ventas'] += $row['total_ventas'];
                $ventasGastos[$fecha]['total_gastos'] += $row['total_gastos'];
            }

            // Preparar datos para el gráfico de barras
            ksort($ventasGastos); // Ordenar por fecha
            $fechas = [];
            $ventasDiarias = [];
            $gastosDiarios = [];
            $gananciasDiarias = [];
            foreach ($ventasGastos as $fecha => $data) {
                $fechas[] = $fecha;
                $ventasDiarias[] = $data['total_ventas'];
                $gastosDiarios[] = $data['total_gastos'];
                $gananciasDiarias[] = $data['total_ventas'] - $data['total_gastos'];
            }
        ?>
            <!-- Gráfico de pastel: Ventas por método de pago -->
            <h3>Ventas por Método de Pago</h3>
            <canvas id="ventasMetodoChart" style="max-height: 300px;"></canvas>

            <!-- Gráfico de barras: Ventas, Gastos y Ganancias por Día -->
            <h3>Ventas, Gastos y Ganancias por Día</h3>
            <canvas id="ventasGastosChart" style="max-height: 400px;"></canvas>

            <script>
                // Gráfico de pastel
                const ctxMetodo = document.getElementById('ventasMetodoChart').getContext('2d');
                new Chart(ctxMetodo, {
                    type: 'pie',
                    data: {
                        labels: <?php echo json_encode($metodos); ?>,
                        datasets: [{
                            data: <?php echo json_encode($totalesMetodo); ?>,
                            backgroundColor: ['#FF6F61', '#4A2C2A', '#FFD700'],
                            borderColor: '#fff',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'Distribución de Ventas por Método de Pago' }
                        }
                    }
                });

                // Gráfico de barras
                const ctxVentasGastos = document.getElementById('ventasGastosChart').getContext('2d');
                new Chart(ctxVentasGastos, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($fechas); ?>,
                        datasets: [
                            {
                                label: 'Ventas ($)',
                                data: <?php echo json_encode($ventasDiarias); ?>,
                                backgroundColor: '#FF6F61',
                                borderColor: '#FF6F61',
                                borderWidth: 1
                            },
                            {
                                label: 'Gastos ($)',
                                data: <?php echo json_encode($gastosDiarios); ?>,
                                backgroundColor: '#4A2C2A',
                                borderColor: '#4A2C2A',
                                borderWidth: 1
                            },
                            {
                                label: 'Ganancias ($)',
                                data: <?php echo json_encode($gananciasDiarias); ?>,
                                backgroundColor: '#FFD700',
                                borderColor: '#FFD700',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true, title: { display: true, text: 'Monto ($)' } },
                            x: { title: { display: true, text: 'Fecha' } }
                        },
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'Ventas, Gastos y Ganancias por Día' }
                        }
                    }
                });

                function filtrar() {
                    const fechaInicio = document.getElementById('fechaInicio').value;
                    const fechaFin = document.getElementById('fechaFin').value;
                    if (fechaInicio && fechaFin) {
                        window.location.href = `reportes-graficos.php?fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`;
                    }
                }
            </script>
        <?php
            $stmtVentasMetodo->close();
            $stmtVentasGastos->close();
            $conn->close();
        }
        ?>
    </div>
    <div class="footer">
        <p>© 2025 Paraíso Crocante - Todos los derechos reservados</p>
    </div>
</body>
</html>