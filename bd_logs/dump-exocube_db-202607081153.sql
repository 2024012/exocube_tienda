-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: exocube_db
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_cat` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Aire Libre y Deportes','Juguetes para utilizar en exteriores, parques y jardines.'),(2,'Didácticos y Construcción','Juguetes orientados al desarrollo cognitivo y motriz.'),(3,'TECNOLOGÍA Y GADGETS','Componentes electrónicos, audífonos, mouses y accesorios tecnológicos.'),(4,'REGALOS Y NOVEDADES','Termos, tazas simples, organizadores de oficina y regalos de uso diario.');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consultas_detalles`
--

DROP TABLE IF EXISTS `consultas_detalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consultas_detalles` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_envio` int(11) NOT NULL,
  `consulta_texto` text NOT NULL,
  `datos_extra` varchar(255) DEFAULT NULL,
  `archivo_adjunto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `fk_envio_consultas_detalles` (`id_envio`),
  CONSTRAINT `fk_envio_consultas_detalles` FOREIGN KEY (`id_envio`) REFERENCES `consultas_envios` (`id_envio`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consultas_detalles`
--

LOCK TABLES `consultas_detalles` WRITE;
/*!40000 ALTER TABLE `consultas_detalles` DISABLE KEYS */;
/*!40000 ALTER TABLE `consultas_detalles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consultas_envios`
--

DROP TABLE IF EXISTS `consultas_envios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consultas_envios` (
  `id_envio` int(11) NOT NULL AUTO_INCREMENT,
  `correo_feedback` varchar(150) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_envio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consultas_envios`
--

LOCK TABLES `consultas_envios` WRITE;
/*!40000 ALTER TABLE `consultas_envios` DISABLE KEYS */;
/*!40000 ALTER TABLE `consultas_envios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_pedidos`
--

DROP TABLE IF EXISTS `detalle_pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_pedidos` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_detalle`),
  KEY `fk_detalle_pedido` (`id_pedido`),
  KEY `fk_detalle_producto` (`id_producto`),
  CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE,
  CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pedidos`
--

LOCK TABLES `detalle_pedidos` WRITE;
/*!40000 ALTER TABLE `detalle_pedidos` DISABLE KEYS */;
INSERT INTO `detalle_pedidos` VALUES (1,1,2,18,180.00),(2,2,11,1,17.00),(3,2,2,3,30.00),(4,2,17,4,50.40),(5,3,11,4,68.00),(6,3,14,2,170.00),(7,3,17,5,63.00),(8,4,17,3,37.80),(9,4,4,3,36.00),(10,4,10,2,88.00),(11,4,12,3,40.50),(12,5,12,4,54.00),(13,5,9,3,135.00),(14,6,9,3,135.00),(15,6,12,3,40.50),(16,7,1,3,75.00),(17,7,17,4,50.40),(18,7,12,3,40.50),(19,7,20,2,120.00),(20,8,20,3,180.00),(21,8,2,4,40.00),(22,8,17,1,12.60),(23,8,11,1,17.00),(24,8,12,3,40.50),(25,9,12,3,40.50),(26,9,17,2,25.20),(27,9,7,2,44.00),(28,10,3,4,119.00),(29,10,8,2,30.00),(30,11,19,3,60.00),(31,11,18,2,50.00),(32,11,20,1,60.00),(33,11,11,5,85.00);
/*!40000 ALTER TABLE `detalle_pedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enlaces_contacto`
--

DROP TABLE IF EXISTS `enlaces_contacto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enlaces_contacto` (
  `id_contacto` int(11) NOT NULL AUTO_INCREMENT,
  `plataforma` varchar(50) NOT NULL,
  `enlace` varchar(255) NOT NULL,
  `etiqueta` varchar(100) NOT NULL,
  `icono_url` varchar(100) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_contacto`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enlaces_contacto`
--

LOCK TABLES `enlaces_contacto` WRITE;
/*!40000 ALTER TABLE `enlaces_contacto` DISABLE KEYS */;
INSERT INTO `enlaces_contacto` VALUES (1,'WhatsApp','https://wa.me/51966085432','966085432','img/whatsapp.png',1),(2,'Instagram','https://instagram.com/exo_cube','exo_cube','img/social.png',1),(3,'Correo','mailto:ex1cube@gmail.com','ex1cube@gmail.com','img/correo-electronico.png',1),(4,'Facebook','https://facebook.com/exo_cube','exo_cube','img/facebook.png',1),(5,'WhatsApp','https://wa.me/51955123456','955123456','img/whatsapp.png',1);
/*!40000 ALTER TABLE `enlaces_contacto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gastos`
--

DROP TABLE IF EXISTS `gastos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gastos` (
  `id_gasto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_gasto` varchar(100) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `monto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `metodo_pago` varchar(50) NOT NULL,
  PRIMARY KEY (`id_gasto`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gastos`
--

LOCK TABLES `gastos` WRITE;
/*!40000 ALTER TABLE `gastos` DISABLE KEYS */;
INSERT INTO `gastos` VALUES (1,'Suplementos de almacen','Logistica','2026-06-12',450.00,'Credit Card'),(2,'Paquete de mercancia XL-2345FE3','Inventario','2026-06-05',2500.00,'Transfer'),(3,'Paquete de mercancia LF-45RF345','Inventario','2026-06-02',1031.00,'Direct Cash');
/*!40000 ALTER TABLE `gastos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `informacion_tienda`
--

DROP TABLE IF EXISTS `informacion_tienda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `informacion_tienda` (
  `id_info` int(11) NOT NULL AUTO_INCREMENT,
  `quienes_somos` text NOT NULL,
  `experiencia` text NOT NULL,
  PRIMARY KEY (`id_info`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `informacion_tienda`
--

LOCK TABLES `informacion_tienda` WRITE;
/*!40000 ALTER TABLE `informacion_tienda` DISABLE KEYS */;
INSERT INTO `informacion_tienda` VALUES (1,'Somos una tienda minorista de regalos y juguetes con trato directo al público, operando fielmente de forma física durante más de 5 años en la ciudad de Tacna.','Nos enfocamos en ofrecer juguetes y regalos diversos traídos de importadoras para el público en general, padres de familia y personas buscando regalos.');
/*!40000 ALTER TABLE `informacion_tienda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locales_tienda`
--

DROP TABLE IF EXISTS `locales_tienda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `locales_tienda` (
  `id_local` int(11) NOT NULL AUTO_INCREMENT,
  `id_info` int(11) NOT NULL,
  `nombre_local` varchar(100) NOT NULL,
  `direccion` text NOT NULL,
  `google_maps_iframe` text DEFAULT NULL,
  `horario_atencion` varchar(100) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_local`),
  KEY `id_info` (`id_info`),
  CONSTRAINT `locales_tienda_ibfk_1` FOREIGN KEY (`id_info`) REFERENCES `informacion_tienda` (`id_info`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locales_tienda`
--

LOCK TABLES `locales_tienda` WRITE;
/*!40000 ALTER TABLE `locales_tienda` DISABLE KEYS */;
INSERT INTO `locales_tienda` VALUES (1,1,'SUCURSAL TACNA CENTRO','Calle San Martín Nro. 123 (Frente a la Plaza de Armas), Tacna - Perú.','<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3791.956740612662!2d-70.2529946851128!3d-18.01334498770744!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9136c968f9aef10f%3A0x7a30cf7f0fc6c8e!2sPlaza%20de%20Armas%20de%20Tacna!5e0!3m2!1ses-419!2spe!4v1680000000000!5m2!1ses-419!2spe\" width=\"100%\" height=\"250\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>','LUNES A SABADO 9:00 A 18:00',1),(2,1,'SUCURSAL CONO SUR','Av. Municipal Nro. 456 (A media cuadra del Óvalo de la Cultura), Nuevo Tacna.','<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3791.245265487854!2d-70.2312456!3d-18.0315482!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9136cf988888888f%3A0x8888888888888888!2sAsoc.+De+Vivienda+Villa+El+Salvador!5e0!3m2!1ses-419!2spe!4v1680000000000!5m2!1ses-419!2spe\" width=\"100%\" height=\"250\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>','LUNES A SABADO 10:00 A 19:00',1);
/*!40000 ALTER TABLE `locales_tienda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_pedido` datetime NOT NULL DEFAULT current_timestamp(),
  `cliente_correo` varchar(100) DEFAULT NULL,
  `cliente_celular` varchar(15) DEFAULT NULL,
  `cliente_nombre` varchar(100) DEFAULT NULL,
  `envoltura` tinyint(1) NOT NULL DEFAULT 0,
  `tipo_entrega` varchar(50) NOT NULL,
  `direccion_entrega` varchar(255) DEFAULT NULL,
  `costo_envio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `metodo_pago` varchar(50) NOT NULL,
  `total_compra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `comprobante_pago` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_pedido`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES (1,'2026-06-24 18:20:48',NULL,NULL,NULL,1,'Delivery - Tacna Centro',NULL,10.00,'Yape/Plin',190.00,NULL),(2,'2026-06-25 21:37:20',NULL,NULL,NULL,0,'Delivery - Tacna Centro',NULL,10.00,'Yape/Plin',97.40,NULL),(3,'2026-06-25 21:43:21',NULL,NULL,NULL,1,'Recojo en Tienda',NULL,0.00,'Transferencia',301.00,NULL),(4,'2026-06-25 22:06:24',NULL,NULL,NULL,1,'Delivery - Cono Sur',NULL,10.00,'Yape/Plin',212.30,NULL),(5,'2026-06-25 22:12:58',NULL,NULL,NULL,1,'Recojo en Tienda',NULL,0.00,'Yape/Plin',189.00,NULL),(6,'2026-06-25 22:14:29',NULL,NULL,NULL,0,'Delivery - Tacna Centro',NULL,10.00,'Yape/Plin',185.50,NULL),(7,'2026-06-25 22:39:22',NULL,NULL,NULL,1,'Delivery - Cono Sur',NULL,10.00,'Transferencia',300.90,NULL),(8,'2026-06-25 22:41:41',NULL,NULL,NULL,1,'Recojo en Tienda',NULL,0.00,'Tarjeta',295.10,NULL),(9,'2026-06-25 22:42:39',NULL,NULL,NULL,0,'Recojo en Tienda',NULL,0.00,'Yape/Plin',109.70,NULL),(10,'2026-06-26 08:59:53',NULL,NULL,NULL,0,'Delivery - Tacna Centro',NULL,10.00,'Yape/Plin',159.00,NULL),(11,'2026-06-28 22:19:15',NULL,NULL,NULL,0,'Recojo en Tienda',NULL,0.00,'Yape/Plin',255.00,NULL);
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `politicas_portal`
--

DROP TABLE IF EXISTS `politicas_portal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `politicas_portal` (
  `id_politica` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_politica` varchar(100) NOT NULL,
  `contenido` longtext NOT NULL,
  `fecha_actualizacion` date NOT NULL,
  PRIMARY KEY (`id_politica`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `politicas_portal`
--

LOCK TABLES `politicas_portal` WRITE;
/*!40000 ALTER TABLE `politicas_portal` DISABLE KEYS */;
INSERT INTO `politicas_portal` VALUES (1,'Términos y Condiciones de Uso del Portal Web','Bienvenido al sitio web de EXO_CUBE (en adelante, \"el Sitio Web\"). <br><br> El acceso y uso de este portal, así como la compra de productos, implica la aceptación total de los presentes Términos y Condiciones. Si no está de acuerdo con ellos, le rogamos que no utilice el sitio.<br>\r\n    <ol>\r\n       <li>OBJETO <br><br>\r\nEl Sitio Web tiene como objeto la comercialización minorista de juguetes y regalos (en adelante, \"los Productos\"). <br><br> La tienda se reserva el derecho de modificar, en cualquier momento, la presentación, configuración del sitio y los productos ofrecidos.</li><br>\r\n              <li>USO DEL SITIO Y REGISTRO <br>\r\n<ul><br>\r\n              <li>Edad mínima: Al utilizar este sitio, usted declara que es mayor de edad en su país o estado de residencia.\r\n\r\n</li><br>\r\n<li>Cuenta de Usuario: Para realizar compras, el usuario podrá registrarse. Es responsabilidad del usuario mantener la confidencialidad de su contraseña y cuenta.</li><br>\r\n<li>Uso prohibido: Queda prohibido el uso del sitio para fines ilícitos, envío de virus, o cualquier actividad que afecte la integridad de la plataforma. </li>\r\n              \r\n</ul></li><br>\r\n<li>CONDICIONES DE VENTA Y PRECIOS <br>\r\n<ul><br>\r\n   <li>Precios: Todos los precios están expresados en Soles (S/.) e incluyen los impuestos aplicables de ley (IGV), a menos que se indique lo contrario.</li><br>\r\n   <li>Modificaciones: Nos reservamos el derecho de modificar los precios en cualquier momento sin previo aviso. Sin embargo, se aplicará el precio vigente al momento de confirmar el pedido.</li><br>\r\n   <li>Disponibilidad: Todos los pedidos están sujetos a disponibilidad de stock. Si un producto no estuviera disponible tras la compra, se informará al cliente y se procederá al reembolso total o cambio del producto.</li><br>\r\n\r\n</ul></li>\r\n              <li>PROCESO DE PAGO <br><br>\r\n              El pago se realizará a través de las plataformas seguras integradas en el sitio, como Yape, Plin o transferencia bancaria virtual. También, se acepta el pago en efectivo directamente en la tienda física al seleccionar recojo. <br><br> La orden se procesará una vez que hayamos recibido la confirmación de pago de la transacción.\r\n              <br>\r\n<br></li>\r\n\r\n              <li>ENVÍOS Y ENTREGAS <br><br>\r\n            \r\n              <ul>\r\n                <li>Zonas de cobertura: Realizamos envíos locales en toda la ciudad de Tacna. </li><br>\r\n                <li>Plazos: Los tiempos de entrega son estimaciones y pueden variar por causas ajenas a la tienda (logística del courier).</li><br>\r\n                <li>Responsabilidad: La tienda no se hace responsable de retrasos causados por direcciones incorrectas proporcionadas por el usuario.</li><br>\r\n              </ul>\r\n</li>\r\n<li>POLÍTICA DE CAMBIOS Y DEVOLUCIONES<br>\r\n<ul><br>\r\n  <li>Plazo: El cliente dispone de 1 día hábil desde la recepción del producto para solicitar un cambio en la tienda física de exo_cube.</li><br>\r\n  <li>Condiciones: Los juguetes deben estar en perfecto estado, con sus empaques originales, etiquetas y sin señales de haber sido abiertos o usados. No se aceptan devoluciones de dinero una vez emitido el comprobante de pago.</li><br>\r\n  <li>Costos: Los costos de traslado para cambios corren por cuenta del cliente, salvo fallas de fábrica demostradas con pruebas gráficas al vendedor.</li>\r\n</ul><br>\r\nVea con más detalles, en nuestra pagina de Políticas de devoluciones. <br>\r\n<br>\r\n</li>\r\n\r\n<li>PROPIEDAD INTELECTUAL <br><br>\r\n   Todo el contenido de este sitio (textos, fotografías de productos, logotipos, gráficos, iconos, animaciones) es propiedad exclusiva de EXO_CUBE o de sus proveedores y está protegido por las leyes de propiedad intelectual internacionales y nacionales. Queda prohibida su reproducción o uso sin autorización previa por escrito. <br><br></li>\r\n\r\n<li>DESCARGO DE RESPONSABILIDAD (FOTOGRAFÍAS) <br><br>\r\n   Hemos hecho todo lo posible para mostrar con la mayor precisión posible los colores y texturas de nuestros productos. Sin embargo, no podemos garantizar que la pantalla de su dispositivo móvil o monitor muestre los colores de manera exacta al producto físico.\r\n<br></li><br>\r\n\r\n<li>PROTECCIÓN DE DATOS<br><br>\r\n   El uso de sus datos personales se rige por nuestra Política de Privacidad, la cual cumple estrictamente con la Ley de Protección de Datos Personales vigente. <br><br>\r\n</li>\r\n\r\n<li>MODIFICACIONES DE LOS TÉRMINOS<br><br>\r\n   Nos reservamos el derecho de actualizar o modificar estos Términos y Condiciones en cualquier momento sin previo aviso. El uso continuado del sitio tras dichos cambios constituye la aceptación de los nuevos términos de uso. <br><br></li>\r\n\r\n<li>LEY APLICABLE Y JURISDICCIÓN<br><br>\r\n   Estos términos se rigen por las leyes actuales de la legislación peruana. Cualquier controversia derivada del uso del sitio web o de la compra de productos será sometida a los tribunales competentes de la ciudad de Tacna. <br></li>\r\n</ol>','2026-05-25'),(2,'Políticas de Privacidad del Portal Web','En EXO_CUBE (en adelante, \"la Tienda\"), la privacidad de nuestros clientes es una prioridad. <br><br>Esta Política de Privacidad describe cómo recopilamos, utilizamos, compartimos y protegemos su información personal cuando visita nuestro sitio web y realiza una compra. <br>\r\n    <ol>\r\n              <li>DATOS QUE RECOPILAMOS <br><br>\r\nRecopilamos información necesaria para brindarle una experiencia de compra segura y eficiente: <br> <br>\r\n<ul>\r\n              <li>Información de contacto: Nombre, apellido, dirección de correo electrónico, número de teléfono y dirección de envío/facturación. </li><br>\r\n              <li>Información de pago: Los detalles de su tarjeta de crédito o débito son procesados directamente por nuestros proveedores de pasarela de pago de forma externa. Nosotros no almacenamos los datos completos de su tarjeta en nuestros servidores. </li><br>\r\n              <li>Datos de navegación: Dirección IP, tipo de navegador, páginas visitadas y tiempo de permanencia (a través de cookies). </li><br>\r\n              <li>Preferencias de compra: Historial de pedidos y productos guardados en el carrito de compras. <br></li>\r\n</ul></li><br>\r\n              <li>FINALIDAD DEL TRATAMIENTO DE LOS DATOS <br><br>\r\n              Utilizamos sus datos personales para:\r\n              <ul><br>\r\n                <li>Gestionar y procesar sus pedidos de juguetes y regalos.</li><br>\r\n                <li>Realizar el envío de los productos a la dirección indicada.</li><br>\r\n                <li>Procesar devoluciones y gestionar el servicio de atención al cliente.</li><br>\r\n                <li>Enviar comunicaciones comerciales, promociones y boletines (newsletters), siempre que usted haya dado su consentimiento previo.</li><br>\r\n                <li>Mejorar nuestro sitio web y personalizar su experiencia de usuario.</li><br>\r\n                <li>Prevenir fraudes y garantizar la seguridad de la plataforma. </li> \r\n              </ul>\r\n<br></li>\r\n\r\n              <li>LEGITIMACIÓN <br><br>\r\n              Tratamos sus datos bajo las siguientes bases legales: <br> <br>\r\n              <ul>\r\n                <li>Ejecución de un contrato: Necesitamos sus datos para enviarle los juguetes que compró.</li> <br>\r\n                <li>Consentimiento: Para el envío de publicidad y uso de cookies no esenciales.</li> <br>\r\n                <li>Obligación legal: Para cumplir con normativas fiscales y contables (facturación). </li> <br>\r\n              </ul>\r\n</li>\r\n<li>¿CON QUIÉN COMPARTIMOS SUS DATOS? <br><br>\r\nNo vendemos ni alquilamos su información personal. Solo compartimos datos con terceros necesarios para la operación de la tienda: \r\n<ul><br>\r\n  <li>Empresas de Logística: Para la entrega y delivery de pedidos en Tacna.</li><br>\r\n  <li>Pasarelas de Pago: Para procesar las transacciones de forma segura.</li><br>\r\n<li>Servicios de Alojamiento y TI: Para el mantenimiento técnico del sitio web.</li><br>\r\n<li>Autoridades Legales: Solo cuando sea requerido por ley o para proteger nuestros derechos. </li>\r\n</ul>\r\n<br>\r\n</li>\r\n\r\n<li>PLAZO DE CONSERVACIÓN DE DATOS <br><br>\r\nMantendremos sus datos personales mientras sean necesarios para la finalidad por la que fueron recabados, mientras exista una relación comercial o hasta que usted solicite su supresión, siempre que no exista una obligación legal de conservarlos (por ejemplo, registros contables durante 5 a 10 años). <br><br></li>\r\n\r\n<li>DERECHOS DEL USUARIO (DERECHOS ARCO) <br><br>\r\nUsted tiene derecho a:\r\n<ul><br>\r\n<li>Acceder a sus datos personales que poseemos.</li><br>\r\n<li>Rectificar datos inexactos o incompletos.</li><br>\r\n<li>Cancelar o Suprimir sus datos cuando ya no sean necesarios.</li><br>\r\n<li>Oponerse al tratamiento de sus datos para fines específicos (como publicidad).</li><br>\r\n<li>Revocar el consentimiento otorgado en cualquier momento.</li>\r\n\r\n</ul><br>\r\nPara ejercer estos derechos, envíe un correo electrónico a ex1cube@gmail.com con el asunto \"Protección de Datos\".\r\n<br></li><br>\r\n\r\n<li>SEGURIDAD DE LOS DATOS <br><br>\r\nImplementamos medidas de seguridad técnicas y organizativas, como el protocolo SSL (Secure Socket Layer), para proteger la transmisión de sus datos y asegurar que su información personal esté a salvo de accesos no autorizados. <br><br>\r\n</li>\r\n\r\n<li>COOKIES <br><br>\r\nEste sitio utiliza \"cookies\" para mejorar la navegación. Puede configurar su navegador para rechazar todas las cookies o para que le avise cuando se envíe una. <br><br></li>\r\n\r\n<li>CAMBIOS EN LA POLÍTICA DE PRIVACIDAD <br><br>\r\nNos reservamos el derecho de modificar esta Política de Privacidad en cualquier momento. Cualquier cambio será publicado en esta página con la fecha de actualización correspondiente. <br></li>\r\n</ol>','2026-05-25'),(3,'Políticas de Devolución y Cambios','En EXO_CUBE, nuestra prioridad es que quedes totalmente satisfecho con tu compra. Si por alguna razón no estás conforme con tu pedido, te ofrecemos soluciones sencillas para cambios y devoluciones.<br>\r\n    <ol>\r\n       <li>PLAZO PARA LA DEVOLUCIÓN<br><br>\r\n          Tienes un plazo de 1 día calendario a partir de la fecha de entrega de tu pedido para solicitar un cambio. Pasado este tiempo, lamentablemente no podremos procesar tu solicitud.</li><br>\r\n       <li>CONDICIONES DE LOS JUGUETES Y REGALOS<br><br>\r\n          Para que una devolución o cambio sea aceptado, el producto debe cumplir con los siguientes requisitos sin excepción:\r\n          <ul><br>\r\n<li>Debe estar en su estado original: sin usar, sin lavar, sin abrir y sin daños en el empaque o caja de fábrica.</li><br>\r\n<li>Debe conservar todas las etiquetas originales (internas y externas) pegadas al producto.</li><br>\r\n<li>No debe presentar signos de haber sido manipulado de forma indebida o alterado en sus piezas.</li><br>\r\n<li>Debe enviarse en su empaque original de fábrica.</li> </ul></li><br>\r\n\r\n<li>PRODUCTOS NO SUJETOS A CAMBIOS NI DEVOLUCIONES<br><br>\r\n   Por razones de higiene y seguridad, no aceptamos cambios ni devoluciones de los siguientes artículos (a menos que presenten un defecto de fábrica comprobado):\r\n<ul><br>\r\n   <li>Productos personalizados.</li><br>\r\n   <li>Artículos marcados como \"Venta Final\" o en la sección de \"Outlet/Liquidación\".</li><br>\r\n</ul></li>\r\n\r\n                <li>PROCESO DE DEVOLUCIÓN Y CAMBIO<br><br>\r\n                  Para iniciar tu proceso, sigue estos pasos:             \r\n               <ul><br>\r\n                     <li>Envía un correo electrónico a ex1cube@gmail.com con el asunto \"Devolución - Pedido #[Número de pedido]\".</li><br>\r\n                     <li>Adjunta fotos del producto (si es por defecto de fábrica) y el motivo del cambio.</li><br>\r\n                     <li>Nuestro equipo te responderá en un plazo de 4 horas con las instrucciones.</li><br>\r\n                     <li>En caso de no haber respuesta rápida por el medio digital, apersonése a la tienda física con las evidencias y el producto antes de que se venza el plazo.</li><br>\r\n                  </ul>\r\n</li>\r\n\r\n                <li>COSTOS DE ENVÍO<br><br>\r\n                  <ul>\r\n                     <li>Por cambio por gusto: El cliente asumirá los costos de traslado o envío hacia nuestro almacén.</li><br>\r\n                     <li>Por error de la tienda o defecto de fábrica comprobado, EXO_CUBE hará el 100% de cambio de producto en tienda física sin cargos adicionales.</li><br>\r\n                     <li>Nota: No se reembolsarán los gastos de envío del pedido inicial.</li><br>\r\n                  </ul> </li>\r\n\r\n<li>REEMBOLSOS<br><br>\r\n   Una vez recibamos tu paquete en tienda física y verifiquemos que los juguetes están en perfecto estado de fábrica: \r\n<ul><br>\r\n   <li>No se realizan reembolsos en efectivo ni devoluciones de dinero en cuentas bancarias.</li><br>\r\n   <li>En su lugar, emitiremos un Vale de Tienda o Tarjeta Regalo por el valor de tu compra para que lo uses en otro juguete cuando quieras, sin fecha de vencimiento.</li><br>\r\n</ul></li>\r\n\r\n<li>CAMBIOS EN GENERAL<br><br>\r\n   Si el juguete que elegiste no es de tu agrado, puedes solicitar un cambio. Los cambios están sujetos a la disponibilidad de inventario en tienda física. <br><br> En caso de que el juguete ya no esté disponible, podrás elegir otra prenda/juguete del mismo valor.</li>\r\n<br>\r\n   <li>GARANTÍA POR DEFECTOS<br><br>\r\n      Todos nuestros productos tienen una garantía de 3 días calendario por defectos de fábrica (piezas rotas o fallas electrónicas de origen). <br><br> No cubre daños por mal uso o caídas.</li>\r\n\r\n</ol>','2026-05-25'),(4,'Políticas de Cookies del Portal Web','El sitio web de EXO_CUBE (en adelante, el \"Sitio Web\") utiliza cookies propias y de terceros para mejorar la experiencia del usuario, analizar el tráfico y ofrecer publicidad personalizada basada en sus hábitos de navegación.<br>\r\n    <ol>\r\n       <li>¿QUÉ SON LAS COOKIES?<br><br>\r\n          Las cookies son pequeños archivos de texto que se descargan y almacenan en su dispositivo (ordenador, smartphone o tablet) al acceder a determinadas páginas web. <br><br>  Permiten a un sitio web, entre otras cosas, almacenar y recuperar información sobre los hábitos de navegación de un usuario y, dependiendo de la información que contengan, pueden utilizarse para reconocer al usuario.</li><br>\r\n            <li>TIPOS DE COOKIES QUE UTILIZAMOS<br><br>\r\n              En nuestra tienda de juguetes y regalos utilizamos los siguientes tipos de cookies:\r\n<ul><br>\r\n<li>Cookies Técnicas (Necesarias): Son esenciales para que el Sitio Web funcione correctamente. Permiten funciones básicas como la navegación por las páginas, el acceso a áreas seguras, y el funcionamiento del carrito de compras. Sin estas cookies, el sitio no puede funcionar adecuadamente.</li><br>\r\n<li>Cookies de Personalización: Permiten que el sitio recuerde información que cambia la forma en que aparece la página, como su idioma preferido o la región en la que se encuentra.</li><br>\r\n<li>Cookies de Análisis (Estadísticas): Nos ayudan a comprender cómo interactúan los visitantes con el Sitio Web (qué páginas visitan más, cuánto tiempo permanecen) mediante la recopilación y notificación de información de forma anónima. Normalmente utilizamos servicios como Google Analytics.</li><br>\r\n<li>Cookies de Marketing y Publicidad: Se utilizan para rastrear a los visitantes a través de las webs. La intención es mostrar anuncios que sean relevantes y atractivos para el usuario individual (por ejemplo, mostrarte un juguete que viste previamente en nuestra tienda mientras navegas en Facebook o Instagram). Utilizamos herramientas como el Píxel de Facebook (Meta).</li> </ul></li><br>\r\n\r\n<li>COOKIES DE TERCEROS<br><br>\r\n   En algunos casos, utilizamos cookies proporcionadas por terceros de confianza. Las más comunes en nuestra tienda son:\r\n<ul><br>\r\n   <li>Google Analytics: Para medir cómo usas el sitio y cómo podemos mejorar tu experiencia.</li><br>\r\n   <li>Redes Sociales (Facebook, Instagram): Para que puedas compartir contenido y para que podamos dirigirte publicidad basada en tus gustos.</li><br>\r\n   <li>Pasarelas de Pago (Yape/Plin, etc.): Pueden usar cookies para garantizar la seguridad de las transacciones financieras.</li><br>\r\n</ul></li>\r\n\r\n            <li>¿CÓMO DESACTIVAR O ELIMINAR LAS COOKIES?<br><br>\r\n              Usted puede permitir, bloquear o eliminar las cookies instaladas en su equipo mediante la configuración de las opciones del navegador instalado en su ordenador:\r\n              <ul><br>\r\n                 <li>Chrome: Configuración -> Privacidad y seguridad -> Cookies y otros datos de sitios.</li><br>\r\n                 <li>Firefox: Ajustes -> Privacidad y Seguridad -> Cookies y datos del sitio.</li><br>\r\n                 <li>Safari: Preferencias -> Privacidad -> Bloquear todas las cookies.</li><br>\r\n                 <li>Edge: Configuración -> Cookies y permisos del sitio.</li><br>\r\n              </ul>\r\n              Tenga en cuenta que, si opta por bloquear las cookies, es posible que algunas partes del Sitio Web no funcionen correctamente o que no pueda acceder a determinadas funciones (como mantener productos en el carrito).\r\n<br><br></li>\r\n\r\n            <li>CONSENTIMIENTO<br><br>\r\n              Al navegar por nuestro Sitio Web por primera vez, aparecerá un aviso (banner) de cookies que le permitirá aceptar, rechazar o configurar el uso de cookies. Usted puede cambiar su elección en cualquier momento. </li><br>\r\n\r\n<li>ACTUALIZACIONES DE LA POLÍTICA<br><br>\r\n   Es posible que actualicemos la Política de Cookies de nuestro Sitio Web en función de nuevas exigencias legislativas, reglamentarias, o con el objeto de adaptar dicha política a las instrucciones dictadas por las autoridades de protección de datos.\r\n<br>\r\n</ol>','2026-05-25');
/*!40000 ALTER TABLE `politicas_portal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preguntas_frecuentes`
--

DROP TABLE IF EXISTS `preguntas_frecuentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `preguntas_frecuentes` (
  `id_faq` int(11) NOT NULL AUTO_INCREMENT,
  `pregunta` text NOT NULL,
  `respuesta` text NOT NULL,
  PRIMARY KEY (`id_faq`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preguntas_frecuentes`
--

LOCK TABLES `preguntas_frecuentes` WRITE;
/*!40000 ALTER TABLE `preguntas_frecuentes` DISABLE KEYS */;
INSERT INTO `preguntas_frecuentes` VALUES (1,'¿CUÁLES SON LOS MÉTODOS DE PAGO ACEPTADOS?','Aceptamos transferencias bancarias, tarjetas de crédito/débito y billeteras digitales como Yape y Plin para procesar tus pagos de forma rápida y segura.'),(2,'¿OFRECEN ENVOLTURAS ESPECIALES PARA REGALO?','¡Sí, por supuesto! Al confirmar tu compra en la boleta de pago, puedes marcar la casilla \"Agregar envoltura de regalo\" por un costo adicional de S/. 5.00.'),(3,'¿HACEN ENVÍOS A DOMICILIO O TIENEN RECOJO EN TIENDA?','Ofrecemos delivery a domicilio en todo Tacna (Centro, Cono Sur, Cono Norte) y también contamos con recojo gratuito en nuestro local físico.'),(4,'¿CÓMO SÉ SI UN JUGUETE TIENE STOCK DISPONIBLE?','La disponibilidad se muestra en tiempo real en la ficha de cada producto y en el catálogo. Si un juguete está agotado, se marcará como no disponible.'),(5,'¿CUÁLES SON SUS HORARIOS DE ATENCIÓN EN LA TIENDA FÍSICA?','Nuestra tienda física en la ciudad de Tacna atiende fielmente de lunes a sábado desde las 9:00 AM hasta las 18:00 PM.');
/*!40000 ALTER TABLE `preguntas_frecuentes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_prod` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `informacion_prod` text DEFAULT NULL,
  `imagen_frontal` varchar(255) DEFAULT NULL,
  `imagen_lateral` varchar(255) DEFAULT NULL,
  `imagen_trasera` varchar(255) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descuento` int(11) NOT NULL DEFAULT 0,
  `stock` int(11) NOT NULL DEFAULT 0,
  `disponibilidad` tinyint(1) NOT NULL DEFAULT 1,
  `id_categoria` int(11) NOT NULL,
  `fecha_ingreso` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_producto`),
  KEY `fk_productos_categorias` (`id_categoria`),
  CONSTRAINT `fk_productos_categorias` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,'PELOTA DE FÚTBOL','Pelota deportiva de alta resistencia, ideal para juegos al aire libre.','Pelota nro 5, material de cuero sintético PVC, peso oficial, cocida a mano. Ideal para entrenamiento y recreación en superficies de césped o losa deportiva.','product_images/52.jpg','product_images/52.jpg','product_images/52.jpg',25.00,0,12,1,1,'2026-06-05 18:28:05'),(2,'FRASCO DE BURBUJAS','Frascos con solución jabonosa de divertidos colores para hacer burbujas gigantes.','Pack por 3 unidades de colores variados. Contiene líquido jabonoso no tóxico de alta densidad y un soplador plástico de alta durabilidad integrado en la tapa.','product_images/57.png','product_images/57.png','product_images/57.png',10.00,0,15,1,1,'2026-06-05 18:28:05'),(3,'BALDE DE CUBOS','Balde clásico con bloques coloridos para construcción de diversas formas.','Balde con 50 bloques de construcción de diversos tamaños y colores primarios. Desarrolla la motricidad fina y la creatividad en niños de 2 a 5 años. Plástico ABS libre de BPA.','product_images/51.jpg','product_images/51.jpg','product_images/51.jpg',35.00,15,16,1,2,'2026-06-05 18:28:05'),(4,'PELOTITAS DE GOMA','Pelotitas de goma de alta resistencia, ideales para entretenimiento exterior.','Pack de pelotitas saltarinas de goma maciza. Colores variados de gran visibilidad, diseño ergonómico y material no tóxico.','product_images/85.jpg','product_images/85.jpg','product_images/85.jpg',12.00,0,47,1,1,'2026-06-05 18:28:05'),(5,'VENTILADOR PORTATIL','Práctico mini ventilador recargable vía USB, ideal para escritorio o viajes.','Ventilador portátil con batería recargable integrada. Posee 3 niveles de velocidad silenciosos y base de soporte desmontable.','product_images/78.png','product_images/78.png','product_images/78.png',15.00,0,30,1,3,'2026-06-05 18:28:05'),(6,'PISTOLA DE AGUA','Pistola de agua con tanque de recarga rápida para juegos en exteriores.','Fabricado en plástico ABS resistente a impactos. Cuenta con un sistema de bombeo de presión manual y un alcance de chorro de hasta 5 metros.','product_images/55.jpg','product_images/55.jpg','product_images/55.jpg',18.00,0,25,1,1,'2026-06-05 18:28:05'),(7,'SET DE CARRITOS','Paquete de carritos de metal coleccionables a escala a fricción.','Set que incluye 5 vehículos metálicos de carreras de alta resistencia. Pinturas libres de plomo, ruedas de libre desplazamiento para juego directo.','product_images/54.jpg','product_images/54.jpg','product_images/54.jpg',22.00,0,13,1,1,'2026-06-25 18:28:05'),(8,'IMANES DE NEODIMIO','Potentes imanes de neodimio para experimentos y manualidades.','Set de imanes de alta potencia magnética. Formato de cubos pequeños. Ideal para proyectos escolares, didácticos y modelado estructural.','product_images/10.jpg','product_images/10.jpg','product_images/10.jpg',15.00,0,38,1,2,'2026-06-25 18:28:05'),(9,'MUÑECA BRATZ','Muñeca coleccionable con accesorios de moda de gran nivel de detalle.','Muñeca articulada de colección con sets de ropa intercambiables y accesorios temáticos de estilo urbano.','product_images/53.jpg','product_images/53.jpg','product_images/53.jpg',45.00,0,4,1,1,'2026-06-25 18:28:05'),(10,'KIT DE HERRAMIENTAS LEGOS','Kit didáctico de herramientas compatibles con bloques de construcción estándar.','Set de herramientas de ensamblado para bloques de construcción Lego. Ayuda a separar piezas difíciles y a facilitar el diseño de maquetas complejas.','product_images/4.jpg','product_images/4.jpg','product_images/4.jpg',55.00,20,10,1,2,'2026-06-25 18:28:05'),(11,'LINTERNA PORTATIL MEDIANA','Linterna de mano metálica con enfoque regulable y alta potencia de iluminación.','Cuerpo de aleación de aluminio de alta resistencia. Foco LED de gran alcance con tres modos de luz: Alta, Baja y Estroboscópica (S.O.S).','product_images/5.jpg','product_images/5.jpg','product_images/5.jpg',20.00,15,7,1,3,'2026-06-25 18:28:05'),(12,'PUNTERO LASER','Puntero láser recargable de largo alcance, ideal para exposiciones o guías.','Puntero con haz de luz visible a grandes distancias. Cuerpo metálico con llave de seguridad de encendido. Incluye cargador y batería.','product_images/6.jpg','product_images/6.jpg','product_images/6.jpg',15.00,10,6,1,3,'2026-06-25 18:28:05'),(13,'SERVOMOTOR','Servomotor de precisión para robótica, electrónica y modelado.','Servomotor de precisión compatible con Arduino y microcontroladores. Ideal para proyectos escolares y diseño de prototipos robóticos básicos.','product_images/8.jpg','product_images/8.jpg','product_images/8.jpg',12.00,25,35,1,3,'2026-06-25 18:28:05'),(14,'SET DE LEGOS STAR WARS','Set de bloques de construcción coleccionables inspirados en Star Wars.','Kit de construcción de naves de combate de la saga Star Wars. Incluye minifiguras oficiales y guía de armado paso a paso. 120 piezas.','product_images/12.jpg','product_images/12.jpg','product_images/12.jpg',85.00,0,6,1,2,'2026-06-25 18:28:05'),(15,'ARDUINO UNO','Placa de desarrollo microcontroladora estándar para proyectos de electrónica.','Placa Arduino Uno R3 original o compatible de alta durabilidad. Perfecta para estudiantes y desarrollo de prototipos electrónicos autónomos.','product_images/13.jpg','product_images/13.jpg','product_images/13.jpg',45.00,0,25,1,3,'2026-06-25 18:28:05'),(16,'TERMO TRANSPARENTE GRANDE','Termo de policarbonato resistente a impactos con capacidad de 1 Litro.','Termo para líquidos fríos con escala de medición horaria integrada. Tapa hermética antiderrames con soplador y correa de mano.','product_images/20.jpg','product_images/20.jpg','product_images/20.jpg',30.00,0,15,1,4,'2026-06-25 18:28:05'),(17,'SET DE TAZAS SIMPLE','Set de tazas de cerámica minimalistas para el hogar o la oficina.','Par de tazas de cerámica blanca de alta temperatura con asas ergonómicas. Aptas para microondas y lavavajillas.','product_images/21.jpg','product_images/21.jpg','product_images/21.jpg',18.00,30,1,1,4,'2026-06-25 18:28:05'),(18,'SET DE CONDIMENTEROS','Organizadores de vidrio con tapas dosificadoras metálicas para cocina.','Set de 4 frascos de vidrio grueso para especias con soporte metálico giratorio para mantener la cocina ordenada.','product_images/22.jpg','product_images/22.jpg','product_images/22.jpg',25.00,0,10,1,4,'2026-06-25 18:28:05'),(19,'CAJA DE LAPICEROS VIKINGO (12 UNIDADES)','Caja de bolígrafos de gel con tinta negra de flujo continuo.','Lapiceros ergonómicos de punta fina de 0.5 mm con grip de goma antideslizante para escritura prolongada de forma cómoda.','product_images/23.jpg','product_images/23.jpg','product_images/23.jpg',20.00,0,27,1,4,'2026-06-25 18:28:05'),(20,'AUDIFONOS GAMER','Audífonos circumaurales con micrófono omnidireccional y luces LED.','Diadema acolchada regulable con almohadillas viscoelásticas. Conexión Jack 3.5mm compatible con consolas y PC. Aislante de ruido exterior.','product_images/31.jpg','product_images/31.jpg','product_images/31.jpg',60.00,0,4,1,3,'2026-06-25 18:28:05'),(21,'MOUSE LOGITECH M90 BLUETOOTH','Mouse óptico inalámbrico de alta precisión y diseño ergonómico.','Mouse con conexión Bluetooth estable de largo alcance y sensor de seguimiento óptico de 1000 DPI para navegación fluida.','product_images/33.jpg','product_images/33.jpg','product_images/33.jpg',38.00,0,15,1,3,'2026-06-25 18:28:05');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonios`
--

DROP TABLE IF EXISTS `testimonios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `testimonios` (
  `id_testimonio` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_cliente` varchar(100) NOT NULL,
  `comentario` text NOT NULL,
  `calificacion` int(11) NOT NULL DEFAULT 5,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_testimonio`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonios`
--

LOCK TABLES `testimonios` WRITE;
/*!40000 ALTER TABLE `testimonios` DISABLE KEYS */;
INSERT INTO `testimonios` VALUES (1,'MARÍA ELENA VALDIVIA','¡ME ENCANTA ESTA TIENDA! COMPRÉ LA MUÑECA BRATZ PARA EL CUMPLEAÑOS DE MI HIJA Y LLEGÓ CON UNA ENVOLTURA HERMOSA. EXCELENTE ATENCIÓN EN TACNA.',5,'2026-06-26 19:00:09'),(2,'JUAN CARLOS FLORES','LOS IMANES DE NEODIMIO SON SÚPER POTENTES. PERFECTOS PARA EL PROYECTO DE CIENCIAS DE MI HIJO. EL ENVÍO LLEGÓ SÚPER RÁPIDO.',5,'2026-06-26 19:00:09'),(3,'GABRIELA CONDORI','EXCELENTE TRATO EN LA TIENDA FÍSICA. SELECCIONÉ RECOJO EN TIENDA DE LA PELOTA DE FÚTBOL Y YA LA TENÍAN LISTA Y EMPACADA CUANDO LLEGUÉ.',5,'2026-06-26 19:00:09'),(4,'ROBERTO QUISPE','COMPRÉ LOS AUDÍFONOS GAMER PARA MI SOBRINO. TIENEN UN EXCELENTE SONIDO Y LAS LUCES LED LE ENCANTARON. MUY RECOMENDADO.',4,'2026-06-26 19:00:09'),(5,'LUCÍA APERESTEGUI','EL FRASCO DE BURBUJAS ES DE BUENA CALIDAD. EL JABÓN HACE BURBUJAS GIGANTES Y ES MUY DIVERTIDO. COMPRARÉ MÁS.',5,'2026-06-26 19:00:09'),(6,'CARLOS MIRANDA','BUENOS PRECIOS Y GRAN VARIEDAD DE JUGUETES IMPORTADOS. ME HUBIERA GUSTADO ENCONTRAR MÁS LEGOS DE STAR WARS EN STOCK, PERO EL QUE COMPRÉ ESTÁ GENIAL.',4,'2026-06-26 19:00:09'),(7,'PATRICIA MAMANI','EL VENTILADOR PORTÁTIL ES SÚPER ÚTIL PARA EL ESCRITORIO. TIENE TRES VELOCIDADES SILENCIOSAS Y ES RECARGABLE.',5,'2026-06-26 19:00:09'),(8,'RICARDO JORGE','COMPRÉ EL ARDUINO UNO PARA MIS CLASES DE ELECTRÓNICA. ESTÁ TOTALMENTE ORIGINAL Y FUNCIONA SIN PROBLEMAS. VOLVERÉ A COMPRAR.',5,'2026-06-26 19:00:09'),(9,'STEFANY CASAS','LA ATENCIÓN POR WHATSAPP ES MUY AMABLE. ME ASESORARON SOBRE QUÉ REGALO ERA ADECUADO PARA UN NIÑO DE 6 AÑOS. EXCELENTES.',5,'2026-06-26 19:00:09'),(10,'FERNANDO VILLANUEVA','EL BALDE DE CUBOS DIDÁCTICOS ES MUY ENTRETENIDO PARA MI BEBÉ. LA CAJA ES RESISTENTE Y SIRVE PARA GUARDARLOS.',4,'2026-06-26 19:00:09'),(11,'VANESSA CALIZAYA','EL TERMO TRANSPARENTE ES GRANDE Y SÚPER ÚTIL. NO DERRAMA NADA DE AGUA Y TIENE UNA ESCALA PARA CONTROLAR LO QUE TOMO.',5,'2026-06-26 19:00:09'),(12,'ALEJANDRO PINTO','COMPRÉ EL SET DE CARRITOS METÁLICOS. SON MUY RESISTENTES A LOS GOLPES DE MI HIJO MENOR. BUENA CALIDAD DE METAL.',5,'2026-06-26 19:00:09'),(13,'MÓNICA SUÁREZ','LA CAJA DE LAPICEROS VIKINGO TIENE UNA ESCRITURA SÚPER SUAVE. PERFECTA PARA MIS APUNTES. VOLVERÉ A COMPRAR MÁS PRODUCTOS.',4,'2026-06-26 19:00:09'),(14,'CHRISTIAN ORTIZ','EL SERVOMOTOR ES COMPATIBLE CON ARDUINO. LLEGÓ SÚPER RÁPIDO A MI CASA. MUY BUEN TRATO.',5,'2026-06-26 19:00:09'),(15,'ELIZABETH RAMOS','LAS PELOTITAS DE GOMA SON MUY DIVERTIDAS. REBOTAN SÚPER ALTO. BUENA ATENCIÓN EN LA TIENDA.',5,'2026-06-26 19:00:09'),(16,'HÉCTOR COAQUIRA','EL PUNTERO LÁSER TIENE UN HAZ DE LUZ MUY POTENTE. COMPLETO CON SU CARGADOR DE BATERÍA. BUENA EXPERIENCIA DE COMPRA.',4,'2026-06-26 19:00:09'),(17,'KARINA ROSAS','COMPRÉ EL KIT DE HERRAMIENTAS LEGO. ES UN POCO COMPLICADO DE USAR AL PRINCIPIO, PERO AYUDA MUCHO A DESARMAR LAS PIEZAS.',4,'2026-06-26 19:00:09'),(18,'ÁNGEL GÓMEZ','EXCELENTE VARIEDAD DE REGALOS MINORISTAS EN TACNA. SE VALORA MUCHO QUE SE PUEDA PAGAR POR YAPE.',5,'2026-06-26 19:00:09');
/*!40000 ALTER TABLE `testimonios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `rol` varchar(30) NOT NULL DEFAULT 'Admin',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'admin','$2y$10$6m4Lg76f4P7R3KxX3BA2Ye.V.h9/oK6Z6m4Lg76f4P7R3KxX3BA2Y','Administrador General','Administrador');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'exocube_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-08 11:53:41
