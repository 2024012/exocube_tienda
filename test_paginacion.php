<?php
// Forzar a PHP a mostrar cualquier error oculto en pantalla
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Importar tu conexión a la base de datos
require_once 'conexion.php';

// Configuramos solo 3 productos por página para que pruebes el cambio de página rápido aunque tengas pocos productos
$productos_por_pagina = 3; 
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
$offset = ($pagina_actual - 1) * $productos_por_pagina;

try {
    // Contar productos disponibles con stock en la BD
    $stmt_count = $pdo->query("SELECT COUNT(*) FROM productos WHERE stock > 0 AND disponibilidad = 1");
    $total_productos = $stmt_count->fetchColumn();
    $total_paginas = ceil($total_productos / $productos_por_pagina);

    // Consulta directa de base de datos
    $select_sql = "SELECT id_producto, nombre_prod, precio FROM productos WHERE stock > 0 AND disponibilidad = 1 ORDER BY id_producto DESC LIMIT " . (int)$productos_por_pagina . " OFFSET " . (int)$offset;
    $productos = $pdo->query($select_sql)->fetchAll();

} catch (Exception $e) {
    die("Error crítico en la consulta: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PRUEBA DE DIAGNÓSTICO DE PAGINACIÓN</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background-color: #fafafa; }
        .consola-debug { background-color: #333; color: #5af75a; padding: 20px; border-radius: 8px; font-family: monospace; line-height: 1.6; margin-bottom: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .tarjeta-producto { padding: 15px; background-color: #FFFCAF; border: 3px solid black; margin-bottom: 10px; font-size: 18px; font-weight: bold; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #FFC7C8; border: 3px solid black; text-decoration: none; color: black; font-weight: bold; margin-top: 20px; transition: transform 0.1s; }
        .btn:active { transform: translate(2px, 2px); }
    </style>
</head>
<body>
    <h2>DIAGNÓSTICO DE PAGINACIÓN DE EXO_CUBE</h2>
    
    <!-- 1. Consola de depuración para ver qué está leyendo tu servidor PHP -->
    <div class="consola-debug">
        <strong>[CONSOLA DE SERVIDOR PHP]</strong><br>
        • Total de productos leídos con stock en la BD: <?php echo $total_productos; ?><br>
        • Páginas calculadas (a 3 productos por página): <?php echo $total_paginas; ?><br>
        • Página actual leída de la URL ($_GET['p']): <?php echo $pagina_actual; ?><br>
        • Offset calculado en PHP: <?php echo $offset; ?><br>
        • Consulta ejecutada en MySQL: <span style="color: yellow;"><?php echo $select_sql; ?></span>
    </div>

    <h3>PRODUCTOS EN ESTA PÁGINA (PÁGINA <?php echo $pagina_actual; ?>):</h3>
    <div>
        <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $p): ?>
                <div class="tarjeta-producto">
                    ID: <?php echo $p['id_producto']; ?> | 
                    Nombre: <?php echo htmlspecialchars($p['nombre_prod']); ?> | 
                    Precio: S/. <?php echo number_format($p['precio'], 2); ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: red; font-weight: bold;">No se encontraron productos en esta página.</p>
        <?php endif; ?>
    </div>

    <!-- 2. Botonera dinámica -->
    <div>
        <?php if ($pagina_actual > 1): ?>
            <a href="test_paginacion.php?p=<?php echo $pagina_actual - 1; ?>" class="btn" style="margin-right: 20px;">« ANTERIOR (Pág <?php echo $pagina_actual - 1; ?>)</a>
        <?php endif; ?>
        
        <?php if ($pagina_actual < $total_paginas): ?>
            <a href="test_paginacion.php?p=<?php echo $pagina_actual + 1; ?>" class="btn">SIGUIENTE (Pág <?php echo $pagina_actual + 1; ?>) »</a>
        <?php endif; ?>
    </div>
</body>
</html>