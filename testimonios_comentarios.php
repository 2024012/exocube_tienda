<?php
require_once 'conexion.php';
session_start();

// 1. Procesar envío de un nuevo comentario (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enviar_comentario'])) {
    // Sanitizar entradas
    $nombre = strtoupper(trim($_POST['nombre'])); // Guardar nombre en MAYÚSCULAS
    $comentario = trim($_POST['comentario']);
    $calificacion = (int)$_POST['calificacion'];
    
    // Validar campos obligatorios
    if (!empty($nombre) && !empty($comentario) && $calificacion >= 1 && $calificacion <= 5) {
        try {
            $stmt_ins = $pdo->prepare("INSERT INTO testimonios (nombre_cliente, comentario, calificacion) VALUES (?, ?, ?)");
            $stmt_ins->execute([$nombre, $comentario, $calificacion]);
            
            // Alerta de éxito y recarga limpia de página (Evita duplicados)
            echo "<script>
                    alert('¡Gracias por tu comentario! Ha sido registrado con éxito.');
                    window.location = 'testimonios_comentarios.php';
                  </script>";
            exit;
        } catch (Exception $e) {
            die("Error al guardar comentario.");
        }
    } else {
        echo "<script>alert('Por favor, complete todos los campos obligatorios antes de enviar.');</script>";
    }
}

// 2. Configurar Paginación (Máximo 9 comentarios por página)
$comentarios_por_pagina = 9;
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
$offset = (int)(($pagina_actual - 1) * $comentarios_por_pagina);

try {
    // Contar total de comentarios registrados
    $stmt_count = $pdo->query("SELECT COUNT(*) FROM testimonios");
    $total_comentarios = $stmt_count->fetchColumn();

    // Calcular páginas totales
    $total_paginas = ceil($total_comentarios / $comentarios_por_pagina);

    // CORRECCIÓN DEFINITIVA: Concatenamos directamente los enteros de LIMIT y OFFSET en la cadena SQL.
    // Esto evita que PDO envíe estos valores como texto, solucionando el bug de que siempre cargara la página 1 en XAMPP.
    $select_sql = "
        SELECT * FROM testimonios 
        ORDER BY id_testimonio DESC 
        LIMIT " . (int)$comentarios_por_pagina . " OFFSET " . (int)$offset;

    // Al no tener marcadores de entrada del usuario en el WHERE, podemos consultar directamente de forma segura
    $comentarios = $pdo->query($select_sql)->fetchAll();

} catch (Exception $e) {
    die("Error al cargar la lista de testimonios: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>TESTIMONIOS Y COMENTARIOS</title>
   <!-- Vinculamos la nueva hoja de estilos optimizada con burbujas de diálogo y formulario -->
   <link href="estilos/testimonios_comentarios.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
   
   <style>
      /* =========================================================================
         CORRECCIÓN UX CRÍTICA: Elevar los botones de paginación para que los
         enlaces transparentes e invisibles de las fotos de arriba no los tapen.
         ========================================================================= */
      .paginacion-contenedor {
          position: relative !important;
          z-index: 9999 !important; /* Capa súper elevada */
          display: flex !important;
          justify-content: center !important;
          margin: 40px auto !important;
      }
      .btn-paginacion {
          position: relative !important;
          z-index: 10000 !important; /* Capa por encima de todo */
          cursor: pointer !important;
          pointer-events: auto !important; /* Asegura la acción del clic */
      }
   </style>

   <!-- Script para mostrar y ocultar de forma elegante el formulario mediante el botón [+] -->
   <script>
      function toggleFormulario() {
         let form = document.getElementById('nuevo-comentario-form');
         if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
         } else {
            form.style.display = 'none';
         }
      }
   </script>
</head>
<body>
   
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br>
   
   <div class="faq">  
      <p>TESTIMONIOS Y COMENTARIOS</p>
   </div>
   <br><br>
   
   <?php 
   if (!empty($comentarios)):
       $indice_global = $offset + 1;
       
       foreach ($comentarios as $row):
           // Alternar dinámicamente entre tus clases: table01, table02 y table03
           $id_clase = ($indice_global - 1) % 3;
           if ($id_clase == 0) $clase_tabla = "table01";
           elseif ($id_clase == 1) $clase_tabla = "table02";
           else $clase_tabla = "table03";
           
           // Dibujar caracteres nativos de estrellas doradas y grises según la calificación de la BD
           $estrellas_doradas = str_repeat('★', $row['calificacion']);
           $estrellas_grises = str_repeat('☆', 5 - $row['calificacion']);
   ?>
           <table class="<?php echo $clase_tabla; ?>">
              <tr>
                <td class="cabecera01">
                  <!-- Muestra el nombre y las estrellas correspondientes -->
                  <h2>
                     <span><?php echo htmlspecialchars($row['nombre_cliente']); ?></span>
                     <span class="estrellas-valoracion"><?php echo $estrellas_doradas . $estrellas_grises; ?></span>
                  </h2>
                </td>
              </tr>
              <tr>
                <td class="cuerpo01">
                  "<?php echo htmlspecialchars($row['comentario']); ?>"
                </td>
              </tr>
           </table><br>
   <?php 
           $indice_global++;
       endforeach;
   else:
       echo "<center><p style='font-family:Alfaqix; font-size:24px; color:#666;'>No hay comentarios publicados todavía. ¡Sé el primero en comentar!</p></center><br><br>";
   endif; 
   ?>

   <!-- BOTÓN INTERACTIVO: Rosa con borde y animación. Llama a Javascript para mostrar el formulario -->
   <table class="tabla-producto">
      <tr class="fila-verde">
        <td class="col-etiqueta" style="cursor: pointer;" onclick="toggleFormulario()">Agregue su comentario</td>
        <td class="col-controles" colspan="2" style="cursor: pointer;" onclick="toggleFormulario()">
          <div class="control-cantidad">
            <button class="btn-mas" type="button">+</button>
          </div>
        </td>
      </tr>
   </table>
   

   <!-- FORMULARIO OCULTO PARA AGREGAR NUEVO COMENTARIO (Se habilita al pulsar [+]) -->
   <div id="nuevo-comentario-form" class="form-comentario-contenedor">
      <form action="testimonios_comentarios.php" method="POST">
         <label for="nombre">TU NOMBRE Y APELLIDO (OBLIGATORIO):</label>
         <input type="text" name="nombre" id="nombre" required placeholder="Escriba su nombre aquí...">
         
         <label for="comentario">TU COMENTARIO / TESTIMONIO (OBLIGATORIO):</label>
         <textarea name="comentario" id="comentario" required placeholder="Cuéntanos qué te parecieron nuestros juguetes o la atención de la tienda..."></textarea>
         
         <label for="calificacion">TU VALORACIÓN EN ESTRELLAS:</label>
         <select name="calificacion" id="calificacion" required>
            <option value="5">★★★★★ (5 Estrellas - Excelente)</option>
            <option value="4">★★★★☆ (4 Estrellas - Muy Bueno)</option>
            <option value="3">★★★☆☆ (3 Estrellas - Regular)</option>
            <option value="2">★★☆☆☆ (2 Estrellas - Malo)</option>
            <option value="1">★☆☆☆☆ (1 Estrella - Muy Malo)</option>
         </select>
         
         <button type="submit" name="enviar_comentario" class="btn-enviar-comentario">ENVIAR COMENTARIO</button>
      </form>
   </div>
<br><br>
   <!-- Sistema Dinámico de Paginación Corregido -->
   <?php if ((int)$total_paginas > 1): ?>
       <div class="paginacion-contenedor">
          <?php if ((int)$pagina_actual > 1): ?>
              <!-- CORRECCIÓN: Forzamos tipo de dato entero en la resta -->
              <a href="testimonios_comentarios.php?p=<?php echo (int)$pagina_actual - 1; ?>" class="btn-paginacion" style="margin-right: 20px;">ANTERIOR</a>
          <?php endif; ?>
          
          <?php if ((int)$pagina_actual < (int)$total_paginas): ?>
              <!-- CORRECCIÓN: Forzamos tipo de dato entero en la suma -->
              <a href="testimonios_comentarios.php?p=<?php echo (int)$pagina_actual + 1; ?>" class="btn-paginacion">SIGUIENTE</a>
          <?php endif; ?>
       </div>
   <?php endif; ?>
<br><br><br>

       <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
       <?php include 'footer.php'; ?>
</body>
</html>