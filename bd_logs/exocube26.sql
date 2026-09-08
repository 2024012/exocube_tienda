USE exocube_db;

-- =========================================================================
-- PASO 1: INSERCIÓN DE 8 CABECERAS DE ENVÍOS (consultas_envios)
-- Distribuidas en el tiempo para poblar la bandeja de entrada del admin
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
-- PASO 2: INSERCIÓN DE EXACTAMENTE 10 DETALLES DE CONSULTAS (consultas_detalles)
-- Enlazados con sus respectivas cabeceras mediante la llave foránea 'id_envio'
-- =========================================================================
INSERT INTO consultas_detalles (id_envio, consulta_texto, datos_extra, archivo_adjunto) VALUES 

-- Envió 1 (1 Consulta)
(1, 'HOLA, ¿TIENEN STOCK DISPONIBLE DEL SET DE LEGOS STAR WARS PARA RECOJO HOY EN LA SEDE DE TACNA CENTRO?', 'URGENTE - REGALO DE CUMPLEAÑOS', NULL),

-- Envió 2 (2 Consultas - Pruebas del 1:N)
(2, '¿EL FRASCO DE BURBUJAS YA VIENE CON LA SOLUCIÓN JABONOSA O SE VENDE POR SEPARADO?', 'FRASCO DE BURBUJAS - PACK DE 3', NULL),
(2, '¿SI COMPRO UN MÍNIMO DE 12 UNIDADES DE BURBUJAS ME APLICAN ALGÚN DESCUENTO POR MAYOR?', 'COTIZACIÓN AL POR MAYOR', NULL),

-- Envió 3 (1 Consulta)
(3, '¿REALIZAN ENVÍOS A DOMICILIO AL CONO NORTE DE TACNA LOS DÍAS DOMINGOS POR LA TARDE?', 'DIRECCIÓN AV. COLLASUYO', NULL),

-- Envió 4 (1 Consulta)
(4, '¿LA MUÑECA BRATZ ES TOTALMENTE ORIGINAL Y VIENE CON LA CAJA EN PERFECTO ESTADO? ES PARA COLECCIÓN.', 'MUÑECA DE COLECCIÓN', NULL),

-- Envió 5 (2 Consultas - Pruebas del 1:N)
(5, '¿EL KIT ARDUINO UNO INCLUYE EL CABLE USB DE CONEXIÓN A LA COMPUTADORA O SE VENDE APARTE?', 'PROYECTO ESCOLAR DE ROBÓTICA', NULL),
(5, '¿TIENEN DISPONIBILIDAD DE 5 SERVOMOTORES COMPATIBLES PARA LLEVAR JUNTO AL ARDUINO?', 'CANTIDAD: 5 UNIDADES', NULL),

-- Envió 6 (1 Consulta)
(6, '¿TIENEN EL SERVICIO DE ENVOLTURA PARA REGALO CON TARJETA DE FELICITACIÓN PERSONALIZADA?', 'REGALO SORPRESA DAMA', NULL),

-- Envió 7 (1 Consulta)
(7, '¿LOS AUDÍFONOS GAMER CUENTAN CON CONEXIÓN CON JACK DE 3.5MM O ES EXCLUSIVO POR ENTRADA USB?', 'COMPATIBILIDAD CON PS5 / PC', NULL),

-- Envió 8 (1 Consulta)
(8, '¿CUÁLES SON LAS MEDIDAS EXACTAS DE ALTO Y ANCHO DEL BALDE DE CUBOS DIDÁCTICOS?', 'DIMENSIONES DE LA CAJA', NULL);