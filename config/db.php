<?php
// Conectar al servidor MySQL sin especificar una base de datos
$conn = new mysqli('localhost', 'root', '');

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    die("Error de conexión al servidor MySQL: " . $conn->connect_error);
}

// Crear la base de datos si no existe
$conn->query("CREATE DATABASE IF NOT EXISTS paraiso_crocante");

// Seleccionar la base de datos
$conn->select_db('paraiso_crocante');

// Verificar si la selección fue exitosa
if ($conn->error) {
    die("Error al seleccionar la base de datos: " . $conn->error);
}

// Crear tabla de ventas (por producto)
$conn->query("
    CREATE TABLE IF NOT EXISTS ventas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        producto VARCHAR(100) NOT NULL,
        cantidad INT NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        metodo_pago ENUM('Efectivo','Transferencia','DeUna') NOT NULL,
        fecha DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");

// Crear tabla de cierres diarios
$conn->query("
    CREATE TABLE IF NOT EXISTS cierres_diarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        total_efectivo DECIMAL(10,2) NOT NULL DEFAULT 0,
        total_transferencia DECIMAL(10,2) NOT NULL DEFAULT 0,
        total_deuna DECIMAL(10,2) NOT NULL DEFAULT 0,
        fecha DATE NOT NULL,
        notas TEXT,
        UNIQUE(fecha)
    )
");

// Crear tabla de gastos
$conn->query("
    CREATE TABLE IF NOT EXISTS gastos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        descripcion VARCHAR(255) NOT NULL,
        monto DECIMAL(10,2) NOT NULL,
        categoria ENUM('Insumos','Fresas','Desechables','Transporte','Personal','Otros') NOT NULL,
        fecha DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");

return $conn;
?>