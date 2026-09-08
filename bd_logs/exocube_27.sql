USE exocube_db;

-- =========================================================================
-- PASO 1: LIMPIEZA DE LOS DATOS CON ERRORES
-- Eliminamos primero de la tabla hija por la restriccion de llave foranea,
-- y luego de la tabla padre para los IDs del 1 al 8.
-- =========================================================================
DELETE FROM consultas_detalles WHERE id_envio BETWEEN 1 AND 8;
DELETE FROM consultas_envios WHERE id_envio BETWEEN 1 AND 8;


-- =========================================================================
-- PASO 2: RE-INSERCION DE CABECERAS DE ENVIOS (Limpio)
-- =========================================================================
INSERT INTO consultas_envios (id_envio, correo_feedback, fecha_registro) VALUES 
(1, 'carlos.ticona@gmail.com', NOW() - INTERVAL 5 DAY),
(2, 'ana.mendoza@outlook.com', NOW() - INTERVAL 4 DAY),
(3, 'jorge.vargas@hotmail.com', NOW() - INTERVAL 3 DAY),
(4, 'patricia_loza@gmail.com', NOW() - INTERVAL 2 DAY),
(5, 'luis.mamani@gmail.com', NOW() - INTERVAL 1 DAY),
(6, 'sofia.romero@gmail.com', NOW() - INTERVAL 10 HOUR),
(7, 'diego.gamer@gmail.com', NOW() - INTERVAL 5 HOUR),
(8, 'mario.paredes@hotmail.com', NOW() - INTERVAL 20 MINUTE);


-- =========================================================================
-- PASO 3: RE-INSERCION DE DETALLES DE CONSULTAS (Limpio)
-- =========================================================================
INSERT INTO consultas_detalles (id_envio, consulta_texto, datos_extra, archivo_adjunto) VALUES 

-- Envio 1 (1 Consulta)
(1, 'HOLA, TIENEN STOCK DISPONIBLE DEL SET DE LEGOS STAR WARS PARA RECOJO HOY EN LA SEDE DE TACNA CENTRO?', 'URGENTE - REGALO DE CUMPLEANOS', NULL),

-- Envio 2 (2 Consultas)
(2, 'EL FRASCO DE BURBUJAS YA VIENE CON LA SOLUCION JABONOSA O SE VENDE POR SEPARADO?', 'FRASCO DE BURBUJAS - PACK DE 3', NULL),
(2, 'SI COMPRO UN MINIMO DE 12 UNIDADES DE BURBUJAS ME APLICAN ALGUN DESCUENTO POR MAYOR?', 'COTIZACION AL POR MAYOR', NULL),

-- Envio 3 (1 Consulta)
(3, 'REALIZAN ENVIOS A DOMICILIO AL CONO NORTE DE TACNA LOS DIAS DOMINGOS POR LA TARDE?', 'DIRECCION AV. COLLASUYO', NULL),

-- Envio 4 (1 Consulta)
(4, 'LA MUNECA BRATZ ES TOTALMENTE ORIGINAL Y VIENE CON LA CAJA EN PERFECTO ESTADO? ES PARA COLECCION.', 'MUNECA DE COLECCION', NULL),

-- Envio 5 (2 Consultas)
(5, 'EL KIT ARDUINO UNO INCLUYE EL CABLE USB DE CONEXION A LA COMPUTADORA O SE VENDE APARTE?', 'PROYECTO ESCOLAR DE ROBOTICA', NULL),
(5, 'TIENEN DISPONIBILIDAD DE 5 SERVOMOTORES COMPATIBLES PARA LLEVAR JUNTO AL ARDUINO?', 'CANTIDAD: 5 UNIDADES', NULL),

-- Envio 6 (1 Consulta)
(6, 'TIENEN EL SERVICIO DE ENVOLTURA PARA REGALO CON TARJETA DE FELICITACION PERSONALIZADA?', 'REGALO SORPRESA DAMA', NULL),

-- Envio 7 (1 Consulta)
(7, 'LOS AUDIFONOS GAMER CUENTAN CON CONEXION CON JACK DE 3.5MM O ES EXCLUSIVO POR ENTRADA USB?', 'COMPATIBILIDAD CON PS5 / PC', NULL),

-- Envio 8 (1 Consulta)
(8, 'CUALES SON LAS MEDIDAS EXACTAS DE ALTO Y ANCHO DEL BALDE DE CUBOS DIDACTICOS?', 'DIMENSIONES DE LA CAJA', NULL);