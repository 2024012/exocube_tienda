<?php
require_once 'conexion.php';
session_start();

// 1. Configurar Paginación (Máximo 5 preguntas por página)
$preguntas_por_pagina = 5;
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
$offset = ($pagina_actual - 1) * $preguntas_por_pagina;

try {
    // Contar total de preguntas registradas
    $stmt_count = $pdo->query("SELECT COUNT(*) FROM preguntas_frecuentes");
    $total_preguntas = $stmt_count->fetchColumn();

    // Consultar las preguntas paginadas de forma correlativa
    $stmt_select = $pdo->prepare("SELECT * FROM preguntas_frecuentes ORDER BY id_faq ASC LIMIT ? OFFSET ?");
    $stmt_select->bindValue(1, $preguntas_por_pagina, PDO::PARAM_INT);
    $stmt_select->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt_select->execute();
    $preguntas = $stmt_select->fetchAll();

    // Calcular páginas totales
    $total_paginas = ceil($total_preguntas / $preguntas_por_pagina);

} catch (Exception $e) {
    die("Error al procesar las preguntas frecuentes.");
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>PREGUNTAS FRECUENTES</title>
   <!-- Vinculamos la nueva hoja de estilos optimizada -->
   <link href="estilos/preguntas_frecuentes.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.28">
</head>
<body>
   
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br>
   
   <div class="faq">  
      <p>PREGUNTAS FRECUENTES (FAQ)</p>
   </div>
   <br><br>
   
   <?php 
   if (!empty($preguntas)):
       $indice_global = $offset + 1; // Para llevar la numeración continua entre páginas
       
       foreach ($preguntas as $faq):
           // Alternar dinámicamente entre tus clases: table01, table02 y table03
           $id_clase = ($indice_global - 1) % 3;
           if ($id_clase == 0) $clase_tabla = "table01";
           elseif ($id_clase == 1) $clase_tabla = "table02";
           else $clase_tabla = "table03";
   ?>
           <table class="<?php echo $clase_tabla; ?>">
              <tr>
                <td class="cabecera01">
                  <!-- Título dinámico numerado de forma correlativa -->
                  <h2>PREGUNTA FRECUENTE <?php echo str_pad($indice_global, 2, "0", STR_PAD_LEFT); ?></h2>
                </td>
              </tr>
              <!-- Nueva Fila: Enunciado de la pregunta de la Base de Datos -->
              <tr>
                <td class="celda-pregunta">
                   <?php echo htmlspecialchars($faq['pregunta']); ?>
                </td>
              </tr>
              <!-- Fila: Respuesta dinámica de la Base de Datos -->
              <tr>
                <td class="cuerpo01">
                  <?php echo nl2br(htmlspecialchars($faq['respuesta'])); ?>
                </td>
              </tr>
           </table>
           <br>
   <?php 
           $indice_global++;
       endforeach;
   else:
       echo "<center><p style='font-family:Alfaqix; font-size:24px; color:#666;'>No hay preguntas disponibles por el momento.</p></center><br><br>";
   endif; 
   ?>

   <!-- Sistema Dinámico de Paginación -->
   <?php if ($total_paginas > 1): ?>
       <div class="paginacion-contenedor">
          <?php if ($pagina_actual > 1): ?>
              <a href="preguntas_frecuentes.php?p=<?php echo $pagina_actual - 1; ?>" class="btn-paginacion" style="margin-right: 20px;">ANTERIOR</a>
          <?php endif; ?>
          
          <?php if ($pagina_actual < $total_paginas): ?>
              <a href="preguntas_frecuentes.php?p=<?php echo $pagina_actual + 1; ?>" class="btn-paginacion">SIGUIENTE</a>
          <?php endif; ?>
       </div>
   <?php endif; ?>

   <br><br>
    <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
    <?php include 'footer.php'; ?>
</body>
</html>