<?php
require_once 'conexion.php';
session_start();

// Capturar el ID de la boleta generado en el paso anterior (por defecto 2 para pruebas)
$id_pedido = isset($_GET['id_pedido']) ? (int)$_GET['id_pedido'] : 2;

try {
    // Consultar los datos de la boleta desde la base de datos
    $stmt_ped = $pdo->prepare("SELECT * FROM pedidos WHERE id_pedido = ?");
    $stmt_ped->execute([$id_pedido]);
    $pedido = $stmt_ped->fetch();

    if (!$pedido) {
        die("<br><br><center><h3>No se encontró el registro de pedido correspondiente.</h3><a href='inicio.php'>Volver al Inicio</a></center>");
    }

    // Determinar lógicamente qué bloque de pago activar según lo seleccionado en el carrito
    $metodo_actual = $pedido['metodo_pago'];
    $activo_1 = ($metodo_actual == 'Pago Directo' || $metodo_actual == 'Directo') ? true : false;
    $activo_2 = ($metodo_actual == 'Yape/Plin') ? true : false;
    $activo_3 = ($metodo_actual == 'Tarjeta') ? true : false;

} catch (Exception $e) {
    die("Error al conectar con la base de datos.");
}

// Procesar el envío de la captura del pago (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirmar_pago'])) {
    $ruta_comprobante = null;

    // Procesar la subida física de la captura de pantalla si se adjuntó
    if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] == UPLOAD_ERR_OK) {
        $directorio_subida = 'uploads_pagos/';
        if (!is_dir($directorio_subida)) {
            mkdir($directorio_subida, 0755, true);
        }

        $nombre_archivo = 'pago_' . $id_pedido . '_' . time() . '_' . basename($_FILES['comprobante']['name']);
        $ruta_completa = $directorio_subida . $nombre_archivo;

        if (move_uploaded_file($_FILES['comprobante']['tmp_name'], $ruta_completa)) {
            $ruta_comprobante = $ruta_completa;
        }
    }

    try {
        
        // Actualizar el pedido asociando el comprobante de pago
        $stmt_update = $pdo->prepare("UPDATE pedidos SET comprobante_pago = ? WHERE id_pedido = ?");
        $stmt_update->execute([$ruta_comprobante, $id_pedido]);

        // REDIRECCIÓN CORREGIDA: Enviar a la página de procesamiento automatizado
        echo "<script>
                alert('¡Comprobante subido! Iniciando validación del pago...');
                window.location = 'procesando_pago.php?id_pedido=" . $id_pedido . "';
              </script>";
        exit;

    } catch (Exception $e) {
        die("Error crítico al actualizar el comprobante en la base de datos.");
    }
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>MÉTODO DE PAGO</title>
   <!-- Reutilizamos la hoja de estilos de tu formulario para heredar perfectamente el diseño retro de bloques -->
   <link href="estilos/metodo_pago.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
   <script>
       function abrirExplorador() {
           document.getElementById('comprobante_input').click();
       }

       function mostrarNombreArchivo(input) {
           if (input.files && input.files[0]) {
               let nombre = input.files[0].name;
               document.getElementById('nombre_archivo_temp').innerText = "Listo: " + nombre;
           }
       }
   </script>
</head>
<body>
  
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br>
   
   <div class="faq">  
      <p>MÉTODO DE PAGO</p>
   </div>
   <br><br>

   <form action="metodo_pago.php?id_pedido=<?php echo $id_pedido; ?>" method="POST" enctype="multipart/form-data" class="form-container" target="_blank">
      
      <!-- MÉTODO 1: PAGO DIRECTO -->
      <section class="consult-block" style="<?php echo !$activo_1 ? 'opacity: 0.6;' : ''; ?>">
         <div class="block-header">
            <span >METODO DE PAGO 01 (PAGO DIRECTO)</span>
            <div class="square-box">
                <?php echo $activo_1 ? '✓' : ''; ?>
            </div>
         </div>
         <div class="block-content">
            <div class="field desc">
                <?php echo $activo_1 ? 'ACÉRQUESE A NUESTRO LOCAL FÍSICO PARA REALIZAR EL PAGO EN EFECTIVO AL RECOGER SU COMPRA.' : 'Opción no seleccionada en el carrito.'; ?>
            </div>
            <div class="field extra">No aplica comprobante</div>
         </div>
      </section>
      <br>

      <!-- MÉTODO 2: BILLETERA DIGITAL (YAPE/PLIN) -->
      <section class="consult-block" style="<?php echo !$activo_2 ? 'opacity: 0.6;' : ''; ?>">
         <div class="block-header">
            <span>METODO DE PAGO 02 (BILLETERA DIGITAL)</span>
            <div class="square-box">
                <?php echo $activo_2 ? '✓' : ''; ?>
            </div>
         </div>
         <div class="block-content">
            <div class="field desc">
                <?php echo $activo_2 ? 'REALICE SU PAGO AL YAPE/PLIN: 966085432 (EXOCUBE S.A.) Y ADJUNTE LA CAPTURA DE PAGO AL COSTADO.' : 'Opción no seleccionada en el carrito.'; ?>
            </div>
            
            <?php if ($activo_2): ?>
                <!-- Campo interactivo de subida para el comprobante en el bloque activo -->
                <div class="field extra">
                    <span id="span3">Adjunte captura de Yape/Plin</span>
                    <div class="plus-box" onclick="abrirExplorador()" style="cursor:pointer; font-weight:bold; font-size:20px; width:30px; height:30px;">+</div>
                    <input type="file" name="comprobante" id="comprobante_input" style="display:none;" onchange="mostrarNombreArchivo(this)" accept="image/*">
                </div>
            <?php else: ?>
                <div class="field extra" style="font-size:20px;">Bloque inactivo</div>
            <?php endif; ?>
         </div>
      </section>
      <br>

      <!-- MÉTODO 3: TARJETA -->
      <section class="consult-block" style="<?php echo !$activo_3 ? 'opacity: 0.6;' : ''; ?>">
         <div class="block-header">
            <span>METODO DE PAGO 03 (TARJETA)</span>
            <div class="square-box">
                <?php echo $activo_3 ? '✓' : ''; ?>
            </div>
         </div>
         <div class="block-content">
            <div class="field desc">
                <?php echo $activo_3 ? 'PROCESANDO PAGO SEGURO CON TARJETA DE CRÉDITO/DÉBITO. ADJUNTE CAPTURA DE LA OPERACIÓN EXITOSA.' : 'Opción no seleccionada en el carrito.'; ?>
            </div>
            
            <?php if ($activo_3): ?>
                <div class="field extra">
                    <span id="span3">Adjunte comprobante de tarjeta</span>
                    <div class="plus-box" onclick="abrirExplorador()" style="cursor:pointer; font-weight:bold; font-size:20px; width:30px; height:30px;">+</div>
                    <input type="file" name="comprobante" id="comprobante_input" style="display:none;" onchange="mostrarNombreArchivo(this)" accept="image/*">
                </div>
            <?php else: ?>
                <div class="field extra">Bloque inactivo</div>
            <?php endif; ?>
         </div>
      </section>
      <br>

      <!-- Campo de validación oculta para el envío -->
      <input type="hidden" name="confirmar_pago" value="1">

      <!-- Botón de Envío Final -->
      <div class="submit-section">
         <button type="submit" class="submit-btn">
            <p>CONFIRMAR PAGO</p><br>
            <p style="font-size:20px; font-family:'Alfaqix'; font-weight:normal; color:#444;">No hay devoluciones</p>
         </button>
      </div>
   </form>
   <br><br><br><br>

    <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
    <?php include 'footer.php'; ?>
</body>
</html>