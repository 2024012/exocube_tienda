<?php
require_once 'conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirmar_compra'])) {
    
    if (empty($_SESSION['carrito'])) {
        die("<script>alert('Tu carrito está vacío.'); window.location='carrito_compras.php';</script>");
    }
    // Capturar datos del formulario de envío y pago
    $cliente_nombre = trim($_POST['cliente_nombre']); // CORRECCIÓN: Captura el nombre real
    $cliente_correo = trim($_POST['cliente_correo']);
    $cliente_celular = trim($_POST['cliente_celular']); 
    $direccion_entrega = isset($_POST['direccion_entrega']) ? trim($_POST['direccion_entrega']) : '';
    
    // Capturar datos del formulario de envío y pago
    $envoltura = isset($_POST['envoltura']) ? 1 : 0;
    $recojo_tienda = isset($_POST['recojo_tienda']) ? 1 : 0;
    $destino = $_POST['destino'];
    $metodo_pago = $_POST['metodo_pago'];
    
    // Configuración del costo de entrega según la casilla marcada
    $costo_envio = $recojo_tienda ? 0.00 : 10.00;
    $tipo_entrega = $recojo_tienda ? "Recojo en Tienda" : "Delivery - " . $destino;

    // CORRECCIÓN: Calcular el costo de la envoltura (S/. 5.00 si está marcada, S/. 0.00 si no)
    $costo_envoltura = $envoltura ? 5.00 : 0.00;

    try {
        // Iniciar transacción SQL para asegurar consistencia de datos
        $pdo->beginTransaction();

        // 1. Traer el precio, stock y descuento de los productos en el carrito
        $total_productos = 0.00;
        $placeholders = implode(',', array_fill(0, count($_SESSION['carrito']), '?'));
        
        $stmt_precios = $pdo->prepare("SELECT id_producto, precio, stock, descuento FROM productos WHERE id_producto IN ($placeholders)");
        $stmt_precios->execute(array_keys($_SESSION['carrito']));
        $productos_db = $stmt_precios->fetchAll(PDO::FETCH_UNIQUE);

        // Validar stock de almacén y calcular totales aplicando los descuentos correspondientes
        foreach ($_SESSION['carrito'] as $id_p => $cant) {
            if ($cant > $productos_db[$id_p]['stock']) {
                throw new Exception("Disculpe, el stock del producto ID " . $id_p . " es insuficiente para completar la cantidad solicitada.");
            }
            
            // Lógica de cálculo dinámico de precio con descuento
            $precio_unitario = $productos_db[$id_p]['precio'];
            if ($productos_db[$id_p]['descuento'] > 0) {
                // Aplicar el porcentaje de descuento al precio original
                $precio_unitario = $precio_unitario * (1 - ($productos_db[$id_p]['descuento'] / 100));
            }
            
            $total_productos += $precio_unitario * $cant;
        }

        // CORRECCIÓN: Costo total de la boleta sumando: Productos + Envió + Envoltura de regalo
        $total_compra = $total_productos + $costo_envio + $costo_envoltura;

       // 2. Insertar cabecera de la boleta
       $stmt_pedido = $pdo->prepare("
       INSERT INTO pedidos (envoltura, tipo_entrega, costo_envio, metodo_pago, total_compra, cliente_correo, cliente_celular, direccion_entrega, cliente_nombre) 
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
       ");
       $stmt_pedido->execute([$envoltura, $tipo_entrega, $costo_envio, $metodo_pago, $total_compra, $cliente_correo, $cliente_celular, $direccion_entrega, $cliente_nombre]);
       $id_pedido_nuevo = $pdo->lastInsertId();

        // 3. Insertar detalles con precios descontados y restar existencias físicas en el inventario
        $stmt_detalle = $pdo->prepare("
            INSERT INTO detalle_pedidos (id_pedido, id_producto, cantidad, subtotal) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt_update_stock = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");

        foreach ($_SESSION['carrito'] as $id_p => $cant) {
            // Lógica de cálculo dinámico para el precio unitario del detalle
            $precio_final_item = $productos_db[$id_p]['precio'];
            if ($productos_db[$id_p]['descuento'] > 0) {
                $precio_final_item = $precio_final_item * (1 - ($productos_db[$id_p]['descuento'] / 100));
            }
            
            $subtotal = $precio_final_item * $cant;

            // Guardar desglose de boleta
            $stmt_detalle->execute([$id_pedido_nuevo, $id_p, $cant, $subtotal]);

            // Descontar cantidad vendida del stock físico
            $stmt_update_stock->execute([$cant, $id_p]);
        }

        // Confirmar transacción en MySQL si todo se ejecutó sin errores
       // Confirmar transacción en MySQL si todo se ejecutó sin errores
       $pdo->commit();

       // Limpiar el carrito de compras tras el éxito de la transacción
       unset($_SESSION['carrito']);

       // REDIRECCIÓN CORRECTA: Enviar a la página de Método de Pago pasando el ID de la boleta recién creada
       header("Location: metodo_pago.php?id_pedido=" . $id_pedido_nuevo);
       exit;

   } catch (Exception $e) {
        // En caso de error, deshacer todas las modificaciones en la Base de Datos
        $pdo->rollBack();
        die("<script>alert('Error al procesar la compra: " . $e->getMessage() . "'); window.location='carrito_compras.php';</script>");
    }
} else {
    header("Location: carrito_compras.php");
    exit;
}
?>