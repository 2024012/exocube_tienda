<?php
require_once 'conexion.php';
session_start();
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>DOCUMENTO EN MANTENIMIENTO</title>
   <!-- Vinculamos tus hojas de estilos para heredar la tipografía y texturas animadas -->
   <link href="estilos/producto03.css" rel="stylesheet">
   <link href="estilos/formulario_pedido.css" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
</head>
<body>
   <!-- Incluimos tu cabecera dinámica unificada -->
   <?php include 'header.php'; ?>
   
   <br><br><br><br><br><br>
   
   <!-- Título lúdico con tus estilos originales de exo_cube -->
   <div class="somos">  
      <p>DOCUMENTO EN MANTENIMIENTO</p>

   </div>

   <!-- Caja de texto explicativa con tu animación original de lluvia diagonal -->
   <div class="experiencia_texto" style="margin: 0 auto; width: 65%; padding: 30px; box-sizing: border-box;">  
      <p style="margin: 0; font-size: 24px; line-height: 1.5; font-family: 'Alfaqix'; font-weight: bold; color: rgb(16, 4, 27);">
         Estimado cliente, esta política o documento legal se encuentra temporalmente inhabilitado debido a que nuestro equipo administrativo está realizando actualizaciones en las normativas del portal de **exo_cube**. Por favor, vuelva a intentarlo más tarde o contáctenos directamente.
      </p>
   </div>
   <br><br><br>
  
   <!-- Botón interactivo de retorno: Tabla rosa con tus animaciones originales -->
   <table class="tabla-producto" style="width: 60%; margin: 0 auto; cursor: pointer;" onclick="window.location='inicio.php';">
      <tr class="fila-rosada">
        <td class="col-etiqueta" style="text-align: center !important; font-size: 22px; padding: 20px; font-weight: bold; cursor: pointer;">
           REGRESAR A INICIO
        </td>
        <td class="col-controles" style="width: 40%; cursor: pointer;padding:0 0 0 12px;">
          <div class="control-cantidad" style="width: 120%; display: flex; align-items: center; justify-content: center;">
             <span style="font-size: 32px; color: black; -webkit-text-stroke: 0px; align-items:center;text-align:center;justify-content:center;">🏠</span>
          </div>
        </td>
      </tr>
   </table>
   <br><br><br><br>
   
   <!-- Incluimos tu footer dinámico rotatorio automático -->
   <?php include 'footer.php'; ?>
</body>
</html>