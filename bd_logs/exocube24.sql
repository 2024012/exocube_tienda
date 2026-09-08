USE exocube_db;

-- 1. Crear tabla de gastos si no existe
CREATE TABLE IF NOT EXISTS gastos (
    id_gasto INT AUTO_INCREMENT,
    nombre_gasto VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL, -- "Logistics", "Marketing", "Inventory", "Operations"
    fecha DATE NOT NULL,
    monto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    metodo_pago VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_gasto)
) ENGINE=InnoDB;

-- 2. Insertar los gastos por defecto de exo_cube basados en tu maquetación
INSERT INTO gastos (nombre_gasto, categoria, fecha, monto, metodo_pago) VALUES 
('Suplementos de almacen', 'Logistica', '2026-06-12', 450.00, 'Credit Card'),
('Paquete de mercancia XL-2345FE3', 'Inventario', '2026-06-05', 2500.00, 'Transfer'),
('Paquete de mercancia LF-45RF345', 'Inventario', '2026-06-02', 1031.00, 'Direct Cash');