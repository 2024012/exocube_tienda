<?php
require_once 'conexion.php';
session_start();

$mensaje_exito = "";

// Procesar el envío de las consultas dinámicas (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enviar_consulta'])) {
    $correo = trim($_POST['correo']);

    try {
        // Iniciar transacción SQL para asegurar consistencia
        $pdo->beginTransaction();

        // 1. Insertar Cabecera (tabla: consultas_envios)
        $stmt_cabecera = $pdo->prepare("INSERT INTO consultas_envios (correo_feedback) VALUES (?)");
        $stmt_cabecera->execute([$correo]);
        $id_envio_nuevo = $pdo->lastInsertId();

        // 2. Procesar y guardar cada Consulta con su respectiva imagen de forma masiva
        if (isset($_POST['txt_c']) && is_array($_POST['txt_c'])) {
            $stmt_detalle = $pdo->prepare("
                INSERT INTO consultas_detalles (id_envio, consulta_texto, datos_extra, archivo_adjunto) 
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($_POST['txt_c'] as $key => $texto_consulta) {
                $texto_limpio = trim($texto_consulta);
                
                // Procesar únicamente las consultas escritas y marcadas en su respectivo checkbox
                if (!empty($texto_limpio) && isset($_POST['act_c'][$key])) {
                    $extra_limpio = isset($_POST['extra_c'][$key]) ? trim($_POST['extra_c'][$key]) : null;
                    $ruta_archivo = null;

                    // Procesar la subida física del archivo específico de esta consulta
                    if (isset($_FILES['captura_c']['error'][$key]) && $_FILES['captura_c']['error'][$key] == UPLOAD_ERR_OK) {
                        $directorio_subida = 'uploads_consultas/';
                        if (!is_dir($directorio_subida)) {
                            mkdir($directorio_subida, 0755, true);
                        }

                        $nombre_archivo = time() . '_c' . $key . '_' . basename($_FILES['captura_c']['name'][$key]);
                        $ruta_completa = $directorio_subida . $nombre_archivo;

                        if (move_uploaded_file($_FILES['captura_c']['tmp_name'][$key], $ruta_completa)) {
                            $ruta_archivo = $ruta_completa;
                        }
                    }

                    // Insertar en la Base de Datos asociando la ruta del archivo específico
                    $stmt_detalle->execute([$id_envio_nuevo, $texto_limpio, $extra_limpio, $ruta_archivo]);
                }
            }
        }

        $pdo->commit();
        $mensaje_exito = "¡Consultas Enviadas con Éxito! Se responderá en un plazo de 15 días.";

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error crítico al guardar la consulta en el servidor.");
    }
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>FORMULARIO DE CONSULTA</title>
   <link href="estilos/formulario_pedido.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
   
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

      // Función auxiliar para auto-expandir los textarea dinámicamente al escribir
      function autoExpandir(textarea) {
          textarea.style.height = 'auto'; // Resetea la altura para recalcular
          textarea.style.height = textarea.scrollHeight + 'px'; // Aplica la nueva altura basada en el scroll
      }
   </script>
</head>
<body>
   <?php if (!empty($mensaje_exito)): ?>
       <script>
           alert("<?php echo $mensaje_exito; ?>");
           window.location = "atencion_cliente.php"; 
       </script>
   <?php endif; ?>

   
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br>
   
   <div class="faq">  
      <p>FORMULARIO DE CONSULTA</p>
      <p>Y PEDIDO</p>
   </div>
   <br><br>

   <form action="formulario_pedido.php" method="POST" enctype="multipart/form-data" class="form-container">
      
      <div id="contenedor-consultas">
         <!-- Primera Consulta con Textareas Auto-expandibles al escribir -->
         <section class="consult-block">
            <div class="block-header">
               <span>CONSULTA 01</span>
               <div class="square-box">
                  <input type="checkbox" name="act_c[0]" value="1" checked style="width:25px; height:25px; cursor:pointer;">
               </div>
            </div>
            <div class="block-content">
               <!-- Reemplazados inputs por textareas con el disparador autoExpandir -->
               <textarea name="txt_c[0]" placeholder="Describir consulta" class="field desc" required oninput="autoExpandir(this)"></textarea>
               <textarea name="extra_c[0]" placeholder="Datos extra" class="field extra" oninput="autoExpandir(this)"></textarea>
            </div>
            <div class="block-header2">
                <span class="span2">Agregar captura o archivo</span>
                <div class="plus-box" onclick="abrirExploradorFila(this)">+</div>
                <input type="file" name="captura_c[0]" style="display:none;" onchange="mostrarNombreArchivoFila(this)" accept="image/*">
            </div>
            <div class="image-content">
                <span class="file-feedback-fila">No se ha seleccionado ningún archivo</span>
            </div>
         </section>
         <br>
      </div>

      <!-- BOTÓN INTERACTIVO: Agrege su consulta extra [+] -->
      <table class="tabla-producto">
         <tr class="fila-verde">
           <td class="col-etiqueta" style="cursor: pointer;" onclick="agregarBloqueConsulta()">Agregue otra consulta</td>
           <td class="col-controles" colspan="2" style="cursor: pointer;" onclick="agregarBloqueConsulta()">
             <div class="control-cantidad">
               <button class="btn-mas" type="button">+</button>
             </div>
           </td>
         </tr>
      </table>
      <br>

      <!-- INPUT CORREO FEEDBACK -->
      <div class="feedback-input">
         <input type="email" name="correo" required placeholder="Escriba el correo para feedback ..." style="width:100%; border:none; background:transparent; font-family:'Alfaqix'; font-size:30px; font-weight:bold; color:rgb(16, 4, 27); outline:none;">
      </div>
      <br>

      <!-- BOTÓN ACCION FINAL -->
      <div class="submit-section">
         <button type="submit" name="enviar_consulta" class="submit-btn">
            <p>ENVIAR CONSULTA</p><br>
            <p style="font-size:20px; font-family:'Alfaqix'; font-weight:normal; color:#444;">Se responderá en un plazo de 15 días</p>
         </button>
      </div>
   </form>
   <br><br><br><br>

       <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
       <?php include 'footer.php'; ?>
   
   <!-- Script auxiliar para control del DOM y agregar bloques de textareas -->
   <script>
     let contadorConsultas = 1; // Inicia con 1 consulta cargada por defecto
       // JavaScript basado en nodos relativos para abrir el explorador de archivos específico
       function abrirExploradorFila(boton) {
           let input = boton.nextElementSibling; 
           input.click();
       }

       function mostrarNombreArchivoFila(input) {
           if (input.files && input.files[0]) {
               let nombre = input.files[0].name;
               let bloqueHeader = input.parentElement;
               let bloqueImageContent = bloqueHeader.nextElementSibling;
               let spanFeedback = bloqueImageContent.querySelector('.file-feedback-fila');
               spanFeedback.innerText = "Archivo listo: " + nombre;
           }
       }

       // Función de JS para agregar nuevos bloques de consulta de forma ilimitada
       function agregarBloqueConsulta() {
           contadorConsultas++;
           let contenedor = document.getElementById('contenedor-consultas');

           let nuevoBloque = document.createElement('section');
           nuevoBloque.className = 'consult-block';
           // Se inyecta la estructura con los dos elementos textarea auto-expandibles
           nuevoBloque.innerHTML = `
                <div class="block-header">
                    <span>CONSULTA ` + String(contadorConsultas).padStart(2, '0') + `</span>
                    <div class="square-box">
                        <input type="checkbox" name="act_c[` + (contadorConsultas - 1) + `]" value="1" checked style="width:25px; height:25px; cursor:pointer;">
                    </div>
                </div>
                <div class="block-content">
                    <textarea name="txt_c[` + (contadorConsultas - 1) + `]" placeholder="Describir consulta" class="field desc" required oninput="autoExpandir(this)"></textarea>
                    <textarea name="extra_c[` + (contadorConsultas - 1) + `]" placeholder="Datos extra" class="field extra" oninput="autoExpandir(this)"></textarea>
                </div>
                <div class="block-header2">
                    <span class="span2">Agregar captura o archivo</span>
                    <div class="plus-box" onclick="abrirExploradorFila(this)" style="cursor:pointer; font-weight:bold; font-size:24px;">+</div>
                    <input type="file" name="captura_c[` + (contadorConsultas - 1) + `]" style="display:none;" onchange="mostrarNombreArchivoFila(this)" accept="image/*">
                </div>
                <div class="image-content">
                    <span class="file-feedback-fila">No se ha seleccionado ningún archivo</span>
                </div>
           `;
           
           contenedor.appendChild(nuevoBloque);
           contenedor.appendChild(document.createElement('br'));
       }
   </script>
</body>
</html>