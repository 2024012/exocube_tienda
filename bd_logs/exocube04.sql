USE exocube_db;

-- 1. Crear la tabla de pedidos finalizados
CREATE TABLE IF NOT EXISTS pedidos (
    id_pedido INT AUTO_INCREMENT,
    fecha_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    envoltura TINYINT(1) NOT NULL DEFAULT 0, -- 1 = Con envoltura, 0 = Normal
    tipo_entrega VARCHAR(50) NOT NULL,        -- "Delivery" o "Recojo en Tienda"
    costo_envio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    metodo_pago VARCHAR(50) NOT NULL,
    total_compra DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (id_pedido)
) ENGINE=InnoDB;

-- 2. Crear la tabla de desglose o detalle de los pedidos
CREATE TABLE IF NOT EXISTS detalle_pedidos (
    id_detalle INT AUTO_INCREMENT,
    id_pedido INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (id_detalle),
    CONSTRAINT fk_detalle_pedido 
        FOREIGN KEY (id_pedido) 
        REFERENCES pedidos (id_pedido)
        ON DELETE CASCADE,
    CONSTRAINT fk_detalle_producto 
        FOREIGN KEY (id_producto) 
        REFERENCES productos (id_producto)
) ENGINE=InnoDB;