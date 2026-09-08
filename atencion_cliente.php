<?php
// Iniciar sesión para mantener la consistencia del carrito de compras en la cabecera
session_start();
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
   <meta charset="utf-8">
   <title>ATENCIÓN AL CLIENTE</title>
   <!-- Vinculamos tu hoja de estilos oficial de atención al cliente -->
   <link href="estilos/atencion_cliente.css" rel="stylesheet">
   <!-- 1. Le dice al celular: "Ajusta el ancho de la página al ancho de tu pantalla" -->
   <meta name="viewport" content="width=device-width, initial-scale=0.30">
</head>
<body>
   
   <!-- Incluye la cabecera dinámica autogestionable de exo_cube -->
   <?php include 'header.php'; ?>
   <br><br><br><br><br><br><br><br><br><br>
   
   <div class="atencion_c">  
      <p>ATENCIÓN AL CLIENTE</p>
   </div>
   <br><br><br>
   
   <!-- Enlaces interactivos unificados a tus páginas dinámicas PHP correspondientes -->
   <div class="at01">  
      <p>PREGUNTAS FRECUENTES (FAQ)</p>
      <a href="preguntas_frecuentes.php" class="enlace-faq" target="_blank"></a>
   </div>
   <br><br><br><br>
   
   <div class="at02">  
      <p>CONTÁCTENOS</p>
      <a href="contactenos.php" class="enlace-contacto" target="_blank"></a>
   </div>
   <br><br><br><br>

   <div class="at03">  
      <p>MAPA DE UBICACIÓN</p>
      <a href="mapa_ubicacion.php" class="enlace-mapa_ubicacion" target="_blank"></a>
   </div>
   <br><br><br><br>

   <div class="at04">  
      <p>POLÍTICAS DE PRIVACIDAD</p>
      <a href="politicas_privacidad.php" class="enlace-p_privacidad" target="_blank"></a>
   </div>
   <br><br><br><br>

   <div class="at05">  
      <p>TÉRMINOS DE USO</p>
      <a href="terminos_uso.php" class="enlace-terminos_u" target="_blank"></a>
   </div>
   <br><br><br><br>

   <div class="at06">  
      <p>POLÍTICAS DE DEVOLUCION</p>
      <a href="politicas_devolucion.php" class="enlace-p_devolucion" target="_blank"></a>
   </div>
   <br><br><br><br>

   <div class="at07">  
      <p>TESTIMONIOS Y COMENTARIOS</p>
      <a href="testimonios_comentarios.php" class="enlace-testimonio_c" target="_blank"></a>
   </div>
   <br><br><br><br>

   <div class="at08">  
      <p>FORMULARIO DE CONSULTA Y PEDIDO</p>
      <a href="formulario_pedido.php" class="enlace-formulario_cp" target="_blank"></a>
   </div>
   <br><br><br><br>

   <div class="at09">  
      <p>POLÍTICA DE COOKIES</p>
      <a href="politicas_cookies.php" class="enlace-p_cookies" target="_blank"></a>
   </div>
   <br><br><br><br>
      <!-- Reemplazo del Footer Estático por el Footer Dinámico unificado -->
      <?php include 'footer.php'; ?>
</body>
</html>