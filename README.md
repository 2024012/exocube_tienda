# exocube_tienda
# REFLEXION SOBRE LA RESPONSIVIDAD Y SUBIDA DEL SITIO WEB
#
Durante el desarrollo de la WebApp exo_cube para su publicación en el hosting, 
enfrenté dos dificultades principales al aplicar el diseño responsivo. 
Primero, los títulos de los juguetes y las tarjetas de productos relacionados se 
desalineaban y se encimaban en pantallas móviles debido a un bug de line-height: 0.05px 
y alturas relativas; esto lo solucioné aplicando Flexbox (align-items: stretch), 
unificando las cajas a un alto fijo de 260px y corrigiendo la altura de línea a 1.1 
mediante media queries. 

Segundo, los botones de paginación arrojaban un error de tipos en PHP 8 (string - int), 
lo que resolví aplicando un forzado estricto de tipo entero (int) en el servidor y 
concatenando directamente el offset en la consulta SQL. Con estas correcciones, el 
sitio web es ahora 100% responsivo, dinámico y está publicado de forma exitosa en producción.
