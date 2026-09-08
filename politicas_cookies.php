<?php
require_once 'conexion.php';
session_start();

// ... (Al inicio del archivo terminos_uso.php) ...
try {
  // CORRECCIÓN: Filtramos la consulta exigiendo de forma obligatoria que esté activo = 1 (ENCENDIDO)
  $stmt_pol = $pdo->prepare("
        SELECT * FROM politicas_portal 
        WHERE nombre_politica = 'Políticas de Cookies del Portal Web' 
        AND activo = 1 
        LIMIT 1
    ");
    
  // ... (En la parte superior de tus archivos de políticas legales) ...
  $stmt_pol->execute();
  $politica = $stmt_pol->fetch();

  // CORRECCIÓN: Si la política está apagada o no existe, redirigimos estéticamente al aviso personalizado
  if (!$politica) {
      header("Location: politica_inactiva.php");
      exit; // Detener ejecución para realizar el redireccionamiento seguro
  }

  $fecha_formateada = date("d/m/Y", strtotime($politica['fecha_actualizacion']));
} catch (Exception $e) {
    die("Error de conexión al cargar las políticas de cookies.");
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <!-- Título oficial exacto de la pestaña -->
   <title>POLÍTICAS DE COOKIES</title>
   <!-- Vinculamos la hoja de estilos de cookies corregida sin el bug de line-height -->
   <link href="estilos/politicas_cookies.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
</head>
<body>
   
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br>
   
   <!-- Título principal exacto de la página -->
   <div class="faq">  
      <p>POLÍTICAS DE COOKIES</p>
   </div>
   <br><br>
   
   <table class="table01">
      <tr>
        <td class="cabecera01">
          <!-- Fecha de actualización dinámica de la base de datos -->
          <h2>ÚLTIMA ACTUALIZACIÓN: <?php echo $fecha_formateada; ?></h2>
        </td>
      </tr>
      <tr>
        <td class="cuerpo01">
          <!-- Impresión del cuerpo del texto en HTML directo desde la base de datos -->
          <?php echo $politica['contenido']; ?>
        </td>
      </tr>
   </table>
   <br>
   <br><br><br>
   
       <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
       <?php include 'footer.php'; ?>
</body>
</html>