<?php
require_once 'conexion.php';
session_start();

// Capturar el ID del producto que se desea imprimir
$id_producto = isset($_GET['id_producto']) ? (int)$_GET['id_producto'] : 2;

try {
    // Consultar el juguete uniendo relacionalmente con su categoría para traer el nombre dinámico
    $stmt = $pdo->prepare("
        SELECT p.*, c.nombre_cat 
        FROM productos p 
        INNER JOIN categorias c ON p.id_categoria = c.id_categoria 
        WHERE p.id_producto = ?
    ");
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch();

    if (!$producto) {
        die("Error: No se encontró el registro del juguete seleccionado en el servidor.");
    }

} catch (Exception $e) {
    die("Error al procesar la ficha técnica.");
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <!-- Nombre oficial sugerido para el archivo PDF al descargar -->
   <title>Ficha_Tecnica_<?php echo htmlspecialchars(str_replace(' ', '_', $producto['nombre_prod'])); ?></title>
   <!-- Reutilizamos tus clases originales para los bordes y grillas -->
   <link href="estilos/producto01.css" rel="stylesheet">
   
   <style>
      body {
         background-color: #ffffff; /* Fondo blanco ideal para impresión de papel o PDF */
         padding: 50px;
         font-family: 'Alfaqix', sans-serif;
         color: #333333;
      }
      
      .ficha-cabecera {
         text-align: center;
         border-bottom: 4px solid #000000;
         padding-bottom: 15px;
         margin-bottom: 30px;
      }
      
      .ficha-cabecera h1 {
         font-family: 'SuperBeatpop', sans-serif;
         font-size: 42px;
         color: #ff1e00;
         margin: 0;
         -webkit-text-stroke: 1.5px #f39c12;
      }
      
      /* Contenedor centrado para la foto del juguete */
      .ficha-visual {
         display: flex;
         justify-content: center;
         margin-bottom: 35px;
      }
      
      .ficha-visual img {
         max-height: 240px;
         width: auto;
         border: 4px solid #000000;
         padding: 10px;
         background-color: #ffffff;
         box-shadow: 4px 4px 0px rgba(0,0,0,0.1);
      }
      
      /* Forzamos que las tablas ocupen el 100% de la hoja de impresión */
      .tabla-producto {
         width: 100% !important;
         max-width: 100% !important;
         margin-bottom: 35px !important;
         border: 4px solid #000000 !important;
      }
      
      .tabla-producto td {
         border: 3px solid #000000 !important;
         font-size: 22px !important;
      }
      
      .fila-gris {
         background-color: #f5f5f5 !important;
      }
      
      .fila-rosada {
         background-color: #fff0f1 !important;
      }
      
      /* Contenedor descriptivo de la ficha técnica */
      .ficha-detalle-caja {
         border: 3px solid #000000;
         padding: 25px;
         background-color: #fafafa;
         font-size: 20px;
         line-height: 1.6;
         text-align: justify;
         font-weight: bold;
         color: #222222;
      }
      
      /* Ocultar elementos interactivos en la versión impresa final */
      @media print {
         .no-imprimir {
            display: none !important;
         }
      }
   </style>
   <meta name="viewport" content="width=device-width, initial-scale=0.32">
</head>
<!-- El atributo onload="window.print()" activa automáticamente el diálogo de guardado PDF al abrir la pestaña -->
<body onload="window.print();">

   <!-- Cabecera de la Ficha Técnica -->
   <div class="ficha-cabecera">
      <h1>FICHA TÉCNICA OFICIAL</h1>
      <h3 style="font-size: 22px; font-weight: bold; margin-top: 10px; text-transform: uppercase; color: #111;">EXOCUBE TIENDA S.A.</h3>
      <p style="font-size: 14px; color: #666; margin-top: 5px; font-weight: bold;">Catálogo de Juguetes y Regalos Minoristas - Tacna</p>
   </div>

   <!-- Foto del Producto -->
   <div class="ficha-visual">
      <img src="<?php echo htmlspecialchars($producto['imagen_frontal'] ?? 'product_images/57.png'); ?>" alt="Fotografía del juguete">
   </div>

   <!-- Especificaciones de Tabla de solo lectura -->
   <table class="tabla-producto">
      <tr class="fila-gris">
        <td class="col-etiqueta" style="width: 50%;">Nombre del Producto</td>
        <td class="col-valor" style="width: 50%; font-weight: bold; text-transform: uppercase;"><?php echo htmlspecialchars($producto['nombre_prod']); ?></td>
      </tr>
      <tr class="fila-rosada">
         <td class="col-etiqueta">Categoría Registrada</td>
         <td class="col-valor" style="font-weight: bold;"><?php echo htmlspecialchars($producto['nombre_cat']); ?></td>
      </tr>
      <tr class="fila-gris">
        <td class="col-etiqueta">Precio Minorista Sugerido</td>
        <td class="col-valor" style="font-weight: bold; color: #ff1e00;">S/. <?php echo number_format($producto['precio'], 2); ?></td>
      </tr>
      <tr class="fila-rosada">
        <td class="col-etiqueta">Disponibilidad en Tienda</td>
        <td class="col-valor" style="font-weight: bold; color: <?php echo $producto['disponibilidad'] == 1 ? 'green' : 'red'; ?>;">
            <?php echo $producto['disponibilidad'] == 1 ? 'En Stock (Disponible)' : 'Agotado (Sin Stock)'; ?>
        </td>
      </tr>
   </table>

   <h3 style="font-size: 22px; font-weight: bold; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 15px; text-transform: uppercase; color: #111;">Especificaciones Técnicas e Información</h3>
   
   <!-- Detalle técnico dinámico -->
   <div class="ficha-detalle-caja">
      <?php echo nl2br(htmlspecialchars($producto['informacion_prod'])); ?>
   </div>

   <br><br><br>
   
   <!-- Botón flotante para cerrar pestaña (No se imprimirá en el papel/PDF) -->
   <div style="text-align: center;" class="no-imprimir">
       <button onclick="window.close();" style="font-family: inherit; font-size: 16px; padding: 12px 35px; cursor: pointer; border: 3px solid black; background-color: #f2f2f2; font-weight: bold; box-shadow: 3px 3px 0px #000;">Cerrar Ficha Técnica</button>
   </div>
   
</body>
</html>