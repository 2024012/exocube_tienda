<?php
require_once 'conexion.php';
session_start();
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>PÁGINA NO ENCONTRADA - ERROR 404</title>
   <!-- Vinculamos tus hojas de estilos para heredar los títulos gigantes y los botones rosas -->
   <link href="estilos/producto03.css" rel="stylesheet">
   <link href="estilos/formulario_pedido.css" rel="stylesheet">
</head>
<body>
   <!-- Incluimos tu cabecera dinámica unificada -->
   <?php include 'header.php'; ?>
   
   <br><br><br><br><br>
   
   <!-- Título gigante lúdico con tus estilos originales de exo_cube -->
   <div class="somos">  
      <p>ERROR 404</p>
      <p style="font-size: 60px; margin-top: 10px;">PÁGINA NO ENCONTRADA</p>
   </div>
   

   <!-- Caja de texto explicativa gris con tu animación original de lluvia diagonal -->
   <div class="experiencia_texto" style="margin: 0 auto; width: 65%; padding: 30px; box-sizing: border-box;">  
      <p style="margin: 0; font-size: 24px; line-height: 1.5; font-family: 'Alfaqix'; font-weight: bold; color: rgb(16, 4, 27);">
         ¡Ups! El enlace o archivo que has ingresado en el navegador no existe, ha sido movido de lugar o no está disponible actualmente en la tienda de **exo_cube**.
      </p>
   </div>
   <br><br><br>
  
   <!-- Botón interactivo de retorno: Tabla rosa con tus animaciones originales de textura en movimiento -->
   <table class="tabla-producto" style="width: 60%; margin: 0 auto; cursor: pointer;" onclick="window.location='inicio.php';">
      <tr class="fila-rosada">
        <td class="col-etiqueta" style="text-align: center !important; font-size: 22px; padding: 20px; font-weight: bold; cursor: pointer;">
           REGRESAR A INICIO
        </td>
        <td class="col-controles" style="width: 40%; cursor: pointer;padding:0 0 0 12px;">
          <div class="control-cantidad" style="width: 120%; display: flex; align-items: center; justify-content: center;">
             <!-- Dibujamos un ícono de casita en texto plano -->
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