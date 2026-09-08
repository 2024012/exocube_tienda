<?php
// 1. Incluir la conexión a la base de datos
require_once 'conexion.php';
session_start();

// 2. Capturar el ID del producto por la URL (por defecto 2)
$id_producto = isset($_GET['id']) ? (int)$_GET['id'] : 2;

// Procesar adición al carrito y redirección limpia para evitar reenvío de formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion_carrito']) && $_POST['accion_carrito'] == 'agregar') {
    $id_p = (int)$_POST['id_producto'];
    $cant = (int)$_POST['cantidad'];
    
    if ($cant > 0) {
        $_SESSION['carrito'][$id_p] = $cant;
    }
    
    header("Location: producto.php?id=" . $id_p);
    exit;
}

try {
    // 3. Consulta con INNER JOIN para traer el nombre de la categoría del producto
    $stmt = $pdo->prepare("
        SELECT p.*, c.nombre_cat 
        FROM productos p 
        INNER JOIN categorias c ON p.id_categoria = c.id_categoria 
        WHERE p.id_producto = ?
    ");
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch();

    if (!$producto) {
        die("<br><br><center><h3>El producto solicitado no está disponible.</h3><a href='catalogo.html'>Volver al Catálogo</a></center>");
    }

    // 4. Consultar 3 productos relacionados de la misma categoría
    $stmt_rel = $pdo->prepare("SELECT * FROM productos WHERE id_categoria = ? AND id_producto != ? LIMIT 3");
    $stmt_rel->execute([$producto['id_categoria'], $id_producto]);
    $productos_relacionados = $stmt_rel->fetchAll();

    $ya_seleccionado = isset($_SESSION['carrito'][$id_producto]);

    // Calcular precio de oferta si tiene descuento
    $tiene_descuento = ($producto['descuento'] > 0);
    $precio_final = $producto['precio'];
    if ($tiene_descuento) {
        $precio_final = $producto['precio'] * (1 - ($producto['descuento'] / 100));
      }
} catch (Exception $e) {
    die("Error al procesar la información del producto.");
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title><?php echo htmlspecialchars($producto['nombre_prod']); ?></title>
   <!-- Vinculamos tu hoja de estilos oficial producto03.css -->
   <link href="estilos/producto03.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">

   <script>
      function incrementar() {
         let cantSpan = document.getElementById('cant_mostrar');
         let cantInput = document.getElementById('cant_input');
         let stockMax = <?php echo $producto['stock']; ?>;
         let currentVal = parseInt(cantSpan.innerText);
         
         if (currentVal < stockMax) {
            cantSpan.innerText = currentVal + 1;
            cantInput.value = currentVal + 1;
         } else {
            alert("No hay más stock disponible de este producto.");
         }
      }

      function decrementar() {
         let cantSpan = document.getElementById('cant_mostrar');
         let cantInput = document.getElementById('cant_input');
         let currentVal = parseInt(cantSpan.innerText);
         
         if (currentVal > 1) {
            cantSpan.innerText = currentVal - 1;
            cantInput.value = currentVal - 1;
         }
      }
   </script>
</head>
<body>
   
<!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
<?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br>
   
   <div class="somos">  
      <p><?php echo htmlspecialchars($producto['nombre_prod']); ?></p>
   </div>
   <br>
   
   <div class="populares_producto">  
      <div class="p04"><br>
         <img src="<?php echo htmlspecialchars($producto['imagen_frontal'] ?? 'product_images/57.png'); ?>" alt="Toma frontal" class="p004">
         <p>Toma frontal</p>
      </div>
      <div class="p05"><br>
         <img src="<?php echo htmlspecialchars($producto['imagen_lateral'] ?? 'product_images/57.png'); ?>" alt="Toma lateral" class="p005">
         <p>Toma lateral</p>
      </div>
      <div class="p06"><br>
         <img src="<?php echo htmlspecialchars($producto['imagen_trasera'] ?? 'product_images/57.png'); ?>" alt="Toma trasera" class="p006">
         <p>Toma trasera</p>
      </div>
   </div>
   <br><br>
   
   <div class="experiencia">  
      <p>DESCRIPCION DEL PRODUCTO</p>
   </div><br>
   <div class="experiencia_texto">  
      <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
   </div>
   <br><br>
   <div class="ubicamos">  
      <p>DETALLES Y PRECIO DEL PRODUCTO</p>
   </div>
   <br>

   <form action="producto.php?id=<?php echo $producto['id_producto']; ?>" method="POST">
      <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
      <input type="hidden" name="cantidad" id="cant_input" value="1">

      <table class="tabla-producto">
         <tr class="fila-gris">
           <td class="col-etiqueta">Precio (por unidad)</td>
           <td class="col-valor" style="text-align: center; padding-left: 15px; line-height: 0.5;  ">
               <?php if ($tiene_descuento): ?>
                   <!-- Muestra el precio anterior tachado, el de oferta y la chapa de descuento -->
                   <span class="precio-antes">S/. <?php echo number_format($producto['precio'], 2); ?></span>
                   <span class="badge-descuento">-<?php echo $producto['descuento']; ?>%</span><br><br><br>
                   <span class="precio-oferta">S/. <?php echo number_format($precio_final, 2); ?></span>
                   
               <?php else: ?>
                   S/. <?php echo number_format($producto['precio'], 2); ?>
               <?php endif; ?>
           </td>
           <td class="col-icono">
               <button type="submit" name="accion_carrito" value="agregar" style="background: none; border: none; padding: 0; cursor: pointer;">
                   <div class="cuadrado-interno" style="display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: bold; color: #333;">
                       <?php echo $ya_seleccionado ? '✓' : ''; ?>
                   </div>
               </button>
               
           </td>
         </tr>
         <tr class="fila-rosada">
           <td class="col-etiqueta">Cantidad a comprar</td>
           <td class="col-controles" colspan="2">
             <div class="control-cantidad">
               <button class="btn-menos" type="button" onclick="decrementar()">—</button>
               <span class="num-cantidad" id="cant_mostrar">1</span>
               <button class="btn-mas" type="button" onclick="incrementar()">+</button>
             </div>
           </td>
         </tr>
         
      </table>
      <br><br>
      <table class="tabla-producto">
         <tr class="fila-gris">
           <td class="col-etiqueta">Categoría</td>
           <td class="col-valor" colspan="2" style="font-weight: bold; text-align: center; padding-left: 15px;">
               <?php echo htmlspecialchars($producto['nombre_cat']); ?>
           </td>
         </tr>
         
      </table>
      <br><br>
      <table class="tabla-producto">
        
         <tr class="fila-gris">
           <td class="col-etiqueta">Disponibilidad</td>
           <td class="col-valor" colspan="2" style="text-align: center; padding-left: 15px; color: <?php echo $producto['disponibilidad'] == 1 ? 'green' : 'red'; ?>; font-weight: bold;">
               <?php echo $producto['disponibilidad'] == 1 ? 'Disponible (En Stock)' : 'Agotado'; ?>
           </td>
         </tr>
         <tr class="fila-rosada">
           <td class="col-etiqueta">Stock Disponible</td>
           <td class="col-valor" colspan="2" style="text-align: center; padding-left: 15px;">
               <?php echo htmlspecialchars($producto['stock']); ?> unidades
           </td>
         </tr>
         
      </table>
   </form>
   <br><br>

   <div class="informacion">  
      <p>INFORMACION DEL PRODUCTO</p>
   </div>
   <br>
   <div class="informacion_texto">  
      <p><?php echo nl2br(htmlspecialchars($producto['informacion_prod'])); ?></p>
   </div>
   <br><br>

    <!-- CORRECCIÓN: El botón de PDF ahora es un enlace dinámico que abre e imprime la Ficha Técnica en PDF -->
    <a href="imprimir_ficha_pdf.php?id_producto=<?php echo $producto['id_producto']; ?>" target="_blank" style="text-decoration: none; display: block; width: fit-content; margin: 0 auto;" title="Descargar Ficha Técnica en PDF">
      <div class="boton-pdf">
         <div class="texto-pdf">Archivo en PDF</div>
         <img src="img/archivo-pdf.png" alt="Icono PDF" class="icono-pdf">
      </div>
   </a>
   <br><br>

   <div class="populares">  
      <p>PRODUCTOS RELACIONADOS</p>
   </div>
   <br>
   
   <div class="novedades_producto">  
      <?php 
      if (count($productos_relacionados) > 0): 
         $i = 1;
         foreach ($productos_relacionados as $rel): 
            $clase_div = "p0" . $i;
            $clase_img = "p00" . $i;
            $clase_p = "p000" . $i;
            $img_rel = !empty($rel['imagen_frontal']) ? $rel['imagen_frontal'] : 'product_images/57.png';
      ?>
            <div class="<?php echo $clase_div; ?>"><br>
               <img src="<?php echo htmlspecialchars($img_rel); ?>" alt="Producto relacionado" class="<?php echo $clase_img; ?>">
               <p class="<?php echo $clase_p; ?>"><?php echo htmlspecialchars($rel['nombre_prod']); ?></p>
               <a href="producto.php?id=<?php echo $rel['id_producto']; ?>" class="enlace-producto04" target="_blank"></a>
            </div>
      <?php 
            $i++;
         endforeach; 
      else:
         echo "<p style='text-align:center; width:100%; color:#666;'>No hay más productos relacionados en esta categoría.</p>";
      endif; 
      ?>
   </div>
   <br><br><br>

       <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
       <?php include 'footer.php'; ?>
</body>
</html>