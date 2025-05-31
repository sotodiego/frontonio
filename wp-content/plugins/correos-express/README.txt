=== CorreosExpress - Shipping Management - Tags ===
Contributors: correosexpress
Requires at least: 4.6
Tested up to: 5.7
Stable tag: 5.5
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Transporte y Logística

== Descripcion ==

Las mejores Soluciones de Transporte Urgente con garantía en la recogida/entrega de tus paquetes que ayudarán a tu empresa a implementar una tienda online eficiente. ¡Con Correos Express tu negocio online no tiene límites!

Además con este módulo gestionarás el envío de los productos a tus clientes de forma muy fácil e intuitiva, podrás generar etiquetas de forma individual o masiva, solicitar recogidas en el momento que lo grabas o hacerlo más adelante y hacer el seguimiento de los envíos desde tu tienda.

== Installation ==

1. Install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Correos Express screen to configure the plugin

== Frequently Asked Questions ==

= Is compatible with all versions of woocommerce? =

No, above 3.3.5

== Changelog ==
=2.6.2=
Validate Configuration forms
Add support panel Configuration
Add check optional log
Fix logo image 

=2.6.1=
fix update url productoWS
exclude trash orders

=2.6.0=
fix update
new migrations for products
upgrade action hook
fix img url tcpdf
fix translations

=2.5.10=
fix update

=2.5.9=
new migrations

=2.5.8=
Fix updateBBDD

=2.5.7=
Fix URL APIRESTOF
Fix Order interactive help
Fix default/unit weight

=2.5.6=
Fix Cron
Fix WS conexion

=2.5.5=
Fix grabacionMasiva
Fix ManualInteractivo

=2.5.4=
Fix cron
Fix changestatus


=2.5.3=
Update Masive Utillities
Update Cron 
Add public log

=2.5.1=
FIX SQL
FIX Masive Utillities
Add borrar recogidas
New functions pickup
New migrations

=2.5.0=
WS REST.
Add client language
Fix cron SQL
Add Migración REST y migración 35
Fix Utilidades masivas
Update Template_history & template_order

= 2.4.15 =
IntroJS - Fix manual configuracion.
Fix utilidades - Etiqueta por defecto.
Cambio url validacion credenciales.
Update ajustes template.

= 2.4.14 =
* Fix default weight
* Fix user validation
* Fix cron
* Fix pick up time
* Translate update
* Credentials REST validation
* Fix Office pick up
* AJAX spaces deleted
* Fix Form urls

= 2.4.13 =
* Fix encript account configuration
* Fix cron

= 2.4.12 =
* Fix conflict modules

= 2.4.11 =
* Fix security tokens

= 2.4.10 =
* Fix security tokens
* Add select product - massive utilities

= 2.4.9 =
* Fix grabacion de recogida

= 2.4.8 =
* Fix cron
* Fix Select default deliver
* Move cron_log file to folder /log
* Fix SOAP
* Fix massive record

= 2.4.7 =
* Fix tcpdf_cex
* Fix cron 

= 2.4.6 =
* Fix consulta intervalo del cron

= 2.4.5 =
* Add input range para cambiar la periocidad del cron

= 2.4.4 =
* Añadir botón validar credenciales web service
* Fix intervalo del cron

= 2.4.3 =
* Añadir enlace Quicksupport Teamviewer.
* Fix contrarrembolso.
* Verificacion Cod Cliente.
* Añadir funcionalidad validar credenciales.
* Actualizar logos.
* Internacionalizar nuevos textos.


= 2.4.2 =
* Fix entrega en oficina

= 2.4.1 =
* Fix estadisticas de WooCommerce
* Add entrega en sabado para las etiquetas
* Update etiquetas entrega en sabado

= 2.0.17 =
* Fix cambio nombre libreria PDF

= 2.0.16 =
* Fix update MXPS_REFETIQUETAS

= 2.0.15 =
* Actualizacion de traducciones
* Fix textos
* Fix mensaje formulario de inicio

= 2.0.14 =
* Fix id_order tabla errores grabacion masiva

= 2.0.13 =
* Fix comentar sanear_string
* Fix incluir en ajustes la zona "Ubicaciones no cubiertas por tus otras zonas"
* Fix compatibilidad plugin "WooCommerce Sequential Order Numbers"

= 2.0.12 =
* Fix ruta logo modal oficinas front
* Fix eliminación código obsoleto

= 2.0.11 =
* Fix utilidades selector "Todos"
* Fix eliminar gijgo(datepicker), página detalle pedido
* Fix texto incorrecto, columna tabla utilidades
* Fix peso calculado en kilos en el PDF resumen

= 2.0.10 =
* Fix etiquetas MultiChrono

= 2.0.9 =
* Fix permisos usuario woocommerce (Gestor tienda)
* Fix obtener el producto mapeado en las órdenes

= 2.0.8 =
* Fix peticion SOAP

= 2.0.7 =
* Fix utilizar unidad de medida de peso configurada en la tienda
* Fix caracteres especiales
* Fix cex_deleteSavedShip etiquetas fallidas
* Fix errores pedidos en Utilidades

= 2.0.6 =
* Fix grabar recogida

= 2.0.5 =
* Fix compatibilidad wafs

= 2.0.4 =
* Fix retornar_mapeo_transportistas
* Fix cron 

= 2.0.3 =
* Fix class_correosexpress.php - sanitize checkbox(Recogida grabar envio)

= 2.0.2 =
* Fix table template_order.php
* Fix etiquetas

= 2.0.1 =
* Fix reload Datatables
* Fix pais remitente por defecto

= 2.0 =
* Implementadas librerías DataTables
* Implementado bootstrap 4 para el módulo
* Incluidas la libreria de Datatables.
* Modificación de maquetación y diseño.
* Añadida funcionalidad para la migración de datos de versiones anteriores del módulo

= 1.1.23 =
* Añadida funcionalidad de sacar el peso del pedido
* Fix migraciones

= 1.1.22 =
* Añadida plantilla para idiomas no soportados en el módulo
* Corregidas las grabaciones con código postal de Portugal para que se grabe con los datos correctos
* Añadida la posibilidad de desactivar el cambio de estado tras la grabación del envío al igual que está en Prestashop
* Corrección de varias consultas para que retornen los datos correctos para el formulario de la orden
* Añadida funcionalidad para imprimir las etiquetas en la posición deseada al igual que la que está incluida en Prestashop
* Corrección en las utilidades masivas de un error al grabar masivamente pedidos con entrega en oficina
* Corrección en la lógica del softdelete para evitar que de errores de duplicidad al intentar grabar un pedido con una referencia marcada como borrado
* Actualizar la tabla tras la grabación de los pedidos (Utilidades masivas)
* Internacionalización de textos en las utilidades masivas

= 1.1.21 =
* Fix parametros de generar etiquetas

= 1.1.20 =
* Fix sacar transportista
* Añadida compatibilidad para mas caracteres raros
* Añadida funcionalidad para quitar los remitentes de las etiquetas
* Añadida columna pais destinatario al pdf del resumen

= 1.1.19 =
* Modificación en las grabaciones masivas para bloquear la seleccion de los pedidos que ya tengan un numero de referencia
* Añadido numero de referencia y numero de envio en la tabla de grabacion masiva de pedidos para los pedidos que ya esten grabados

= 1.1.18 =
* Modificado el cron para permitir elegir el estado al que cambia el pedido.
* Arreglos en rutas de archivos

= 1.1.17 =
* Añadida compatibilidad con woocommerce 3.5
* Cambios en la consulta al servicio de busqueda de oficinas

= 1.1.16 =
* Compatibilidad de la entrega en oficinas con mas temas de woocommerce

= 1.1.15b =
* Modificada la forma de guardar los precios en base de datos para evitar errores de lectura
* Arreglos en las utilidades masivas que provocaban errores

= 1.1.15 =
* Eliminada la columna precio de las tablas de las utilidades masivas

= 1.1.14 =
* Añadida opcion para seleccionar todos los pedidos de la lista en las utilidades masivas
* Añadido soporte de retrocompatibilidad adicional para versiones antiguas

= 1.1.13b =
* Mejorada la retrocompatibilidad para versiones de woocommerce 3.3.X

= 1.1.13 =
* Añadido limitacion a los campos de observaciones para adaptarlos al maximo permitido por los webservice
* Añadida correccion a las creaciones de las tablas para evitar el error #1071
* Añadida soporte para el caracter ' dentro de las direcciones para evitar errores en las grabaciones
* Modificadas la forma de obtener los transportistas para para hacer compatible el plugin con los plugin de WC-APG

= 1.1.12 =
* Añadida traduccion al catalan al plugin
* Correccion de errores de traducccion

= 1.1.11 =
* Añadida función para eliminar los ceros por la izquierda en caso de que sean pedidos internacionales
* Añadida compatibilidad con otras nomenclaturas de transportistas
* Correccion de la longitud de las direcciones y los campos de observacion para que no excedan el numero de caracteres maximos
* Modificado el cuadrante de la etiqueta que incluye los datos del remitente para que ajuste el tamaño de la fuente en funcion de la cantidad de caracteres
* Agregada funcionalidad para copiar los datos del remitente o los del destinatario en los campos de destinatario
* Agregada funcion de soft delete

= 1.1.10 =
* Añadida la traduccion al inlges del plugin
* Añadidas funciones para realizar las grabciones correctamente cuando el pedido se realiza contrarembolso
* Añadida comprobacion para marcar correctamente cuando un pedido es contrarembolso en la administracion de la orden

= 1.1.9 =
* Correccion de la llamada ajax que se encargaba de la entrega en oficina en el front para que llame a la url correcta
* Modificacion de las ventanas modales de los ajustes para evitar problemas de compatibilidad
* Eliminadas dos librerias del archivo helpers que provocaba mensajes de error en el front

= 1.1.8 =
* Correción en la asignación del codigo de oficina cuando empezaba por 0
* Agregada comprobacion para la entrega en oficina para mostrar buscador de oficinas al cargar la pagina

= 1.1.7 =
* Añadimos la función para retornar todos los paises no solo España y Portugal

= 1.1.7 =
* Añadimos la función para retornar todos los paises no solo España y Portugal 

= 1.1.6 =
* Modificada función para obtener los nombres de los transportistas en las utilidades masivas
* Modificado el cron para permitir elegir si se quiere actualizar el estado de la orden o no 
* Modificacion en el formulario de la orden para que recargue la pagina al completar la grabacion para mostrar los datos actualizados

= 1.1.5 =
* Añadida compatibilidad con la version 3.4.3 de woocommerce

= 1.1.4b =
* Añadida función para detectar los prefijos de las tablas de la tienda al realizar el borrado del historial.

= 1.1.4 =
* Introducción de 2 nuevos métodos de envío ( 54 Entrega + Recogida Multichrono y 55 Entrega + recogida + Manip Multichrono).
* Generación de etiquetas para estos métodos con sus correspondientes etiquetas de retorno.
* Retocar estilos de la etiqueta mediofolio.
* Nueva función que solo permite el almacenamiento en la tabla cex_history de las últimas 500 inserciones (se genera una inserción por cada envío), evitando así la masificación de datos

= 1.1.3 =
* Añadida funcion para cambiar el estado de los pedidos cuando se graban
* Añadidos mas estados del pedido a la restricción de ejecucion del cron

= 1.1.2 =
* Eliminacion de funciones en las plantillas que provocaban que el plugin no funcionase en algunas páginas
* Restructuración de los archivos para cumplir los estandares

= 1.1.1 =
* Añadida compatibilidad con Firefox
* Modificacion de etiquetas termicas para encuadrar los productos en una celda autoajustable
* Cambio en la funcionalidad del cron para funcionar sin necesidad de estado de inicio

= 1.0.16c =
* Correccion en las peticiones de webservice para enviar correctamente el valor de contrarembolso

= 1.0.16b =
* Cambio en la funcionalidad de la version 1.0.16 (se cargan los bultos por defecto y se permite al usuario modificar los bultos manualmente)

= 1.0.16 =
* Grabacion masiva : numero de bultos para las etiquetas es igual al numero de articulos que tiene la orden.
* Esta funcionalidad solo afecta a la grabcion masiva desde la orden individual se pueden imprimir el numero de bultos que se necesiten.

= 1.0.15b =
* Correccion de estilos que afectaban a los textos de la admnistracion de la orden (.label)

= 1.0.15 =
* Corrección de estilos
* Soporte para codigos postales menores de 5 digitos

= 1.0.14b =
* Correccion en la grabacion masiva para asignar correctamente el valor del "contacto destinatario" cuando el campo empresa se deja en blanco

= 1.0.14 =
* Corrección de error en el rango de fechas a la hora de buscar pedidos
* Corrección de la opción de editar la orden en caso de error para que redirija correctamente

= 1.0.13b =
* Correccion de asignación incorrecta de datos en las grabaciones masivas

= 1.0.13 =
* Agregada funcion para compatibilizar los numeros de telefono con prefijos
* Correccion en la grabación masiva para que busque en el rango de fechas correctamente

= 1.0.12 =
* Corrección en las notificaciones para mostrar los mensajes correctamente
* Modificada la funcion de actualizar la orden para no modificar los datos de facturacion

= 1.0.11b =
* Correccion de estilos(problemas con los botones)

= 1.0.11 =
* Correccion fallo en grabaciones masivas de pedidos
* Correccion de los estilos del plugin para evitar afectar a otras partes de la tienda
* Añadidos tokens para securizar el plugin

= 1.0.10b =
* Correccion merge (error en las migraciones)

= 1.0.10 =
* Correccion de la visibilidad de las utilidades con respuestas vacias
* Adecuar las funciones de desactivacion,instalacion y desinstalacion del plugin para cumplir los estandares

= 1.0.9 =
* Guardar direccion concatenada en direccion1 y dejar en blanco direccion2

= 1.0.8 =
* Cambio en la funcionalidad del menu utilidades cuando no recibe datos

= 1.0.7 =
* Arreglado problema en las migraciones y el seeding
* Reescritura de los estilos del modulo
* Correccion metodo cex_guardar_savedships (cambio en el orden de los parametros)

= 1.0.6 =
* Concatenado de direccion de envio en formulario de la orden.

= 1.0.5 =
* Eliminada función de comprobar diferencia de horas para manejarlo a traves de la respuesta del WS
* Se anula el guardado de datos en BBDD cuando el código de respuesta sea igual a 0

= 1.0.4 =
* Corrección de una consulta que le faltaba el prefijo de tabla
* Fechas por defecto en todos los input de utilidades
* Checkbox desactivado cuando hayan pasado mas de 7 dias desde su grabación en reimpresión de etiquetas

= 1.0.3 =
* Crear funcion para ejecutar las migraciones.
* Crear funcion de registro de migraciones.
* Cambiar  prefijos de base de datos por funcion.

= 1.0.2 =
* Se dejan en el poblado los entornos de produccion.
* Se quitan mas ficheros de backup

= 1.0.1 =
* Primera version liberada.
* Mejora de los templates ( las utilidades pasan a ser un tabbed panel)
* Creacion del manual 
* Se dejan archivos de depuracion
* Se dejan comentarios en el codigo.
* Generacion de etiquetas al vuelo.
* Añadir el email a los remitentes
* Mejora en la plantilla de utilidades ( se añaden tablas para el mostrado de datos)
* Quitar logs ( console.log, file_put_content..... )
* Quitar archivos de pruebas o internos.
* Manual solamente en PDF
* Quitar mensajes de error.
* Quitar archivos de Backup.

== Upgrade Notice ==

= 1.1.23 =
Update available

= 1.1.22 =
Update available

= 1.1.21 =
Update available

= 1.1.20 =
Update available

= 1.1.19 =
Update available

= 1.1.18 =
Update available

= 1.1.17 =
Update available

= 1.1.16 =
Update available

= 1.1.15b =
Update available

= 1.1.15 =
Update available

= 1.1.14 =
Update available

= 1.1.13b =
Update available

= 1.1.13 =
Update available

= 1.1.12 =
Update available

= 1.1.11 =
Update available

= 1.1.10 =
Update available

= 1.1.9 =
Update available

= 1.1.8 =
Update available

= 1.1.7 =
Update available

= 1.1.6 =
Update available

= 1.1.5 =
Update available

= 1.1.4b =
Update available

= 1.1.4 =
Update available

= 1.1.3 =
Update available

= 1.1.2 =
Update available

= 1.1.1 =
Update available

= 1.0.16c =
Update available

= 1.0.16b =
Update available

= 1.0.16 =
Update available

= 1.0.15b =
Update available

= 1.0.15 =
Update available

= 1.0.14b =
Update available

= 1.0.14 =
Update available

= 1.0.13b =
Update available

= 1.0.13 =
Update available

= 1.0.12 =
Update available

= 1.0.11b =
Update available

= 1.0.11 =
Update available

= 1.0.10b =
Update available

= 1.0.10 =
Update available

= 1.0.9 =
Update available

= 1.0.8 =
Update available

= 1.0.7 =
Update available

= 1.0.6 =
Update available

= 1.0.5 =
Update available

= 1.0.4 =
Update available

= 1.0.3 =
Update available

= 1.0.2 =
Update available

= 1.0.1 =
Update available
