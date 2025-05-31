# Correos Ecommerce para Woocommerce


### **[1.9.1]** - 11/03/25

##### Mejoras
-    Se añade compatibilidad con el módulo de logística
-    Ahora se usa WP-Cron, para las tareas programadas

##### Correcciones
-    Corregido error al tener una versión de WooCommerce anterior a la 8.5.3. Reportado en INC000053104225
-    Corregido descripción aduanera no se copiaba correctamente en el bulto 2. Reportado en INC000053125717
-    Corregido enlace de seguimiento del envío dentro del email venía mal formateado. Reportado en INC000053135993

### **[1.9.0]** - 14/01/25

##### Mejoras
-    Copia del teléfono del destinatario a <NumeroSMS> según formato y destino
-    Eliminación del bloque para agregar datos de contrato, ahora visible solo al pulsar "Nuevo contrato" o "Editar"
-    Traslado del campo "empresa" al pre-registro: en Correos, al bloque Destinatario; en CEX, concatenado con el nombre del destinatario
-    Comprobación de que la extensión Soap está instalada al añadir las credenciales de Correos

### **[1.8.6]** - 22/10/2024

##### Mejoras
-   Se realiza un cambio a las consultas a la hora de cargar las tablas en la pestaña de utilidades para mejorar el rendimiento

### **[1.8.5]** - 02/09/2024

##### Mejoras
-    Se añade animación "en proceso" cuando se apretan los botones de imprimir hasta que se termina de descargar el documento pdf
-    Se añade control a la hora de añadir un código de usuario ya existente en la base de datos
-    Se añade botón para eliminar el archivo a subir a la base de datos antes de guardar los ajustes de usuario
-    Se añade botón para copiar al portapapeles los datos guardados en el buscador de Oficina/CityPaq dentro de un pedido
-    Se añade confirmación para cancelación de pedidos de Oficina/CityPaq dentro de un pedido

##### Correcciones
-   Corregido mensaje de error en el apartado devoluciones del pedido no aparece
-   Corregido checkbox desaparecia al cancelar un envio dentro de un pedido
-   Corregido mensaje de error proveniente de webservice no aparece cuando se cancelaba un pedido
-   Corregido Nif Personalizado era requerido cuando el checkbox de "añadir campo NIF en el checkout" estaba desmarcado
-   Corregido subida de logo personalizado distinto al guardado en la base de datos
-   Corregido búsqueda y generación de envío mediante Oficina/CityPaq desde dentro de un pedido
-   Corregido error provocado al intentar cargar pedidos reembolsados en gestion masiva. Reportado en INC000052919275

### **[1.8.4]** - 15/07/2024

##### Correcciones
-   Corregido redondeo en el campo peso del manifiesto. Reportado en INC000052858711
-   Corregido pérdida de configuración en las reglas de coste cuando se utiliza una clase de envio personalizada. Reportado en INC000052854671
-   Corregido redondeo erróneo del campo "valor neto" dentro de la documentación aduanera. Reportado en INC000052857881
-   Corregido error al cargar la tabla "Resumen pedidos" en utilidades. Reportado en INC000052843328

### **[1.8.3]** - 02/07/2024

##### Mejoras
-   Mejora la obtención del Manifiesto en Utilidades -> Resumen de Pedidos (Filtrado, Formato y Datatable)
-   Se cambia el orden de los estados de CRON en Ajustes

##### Correcciones
-   Corregido comprobacion y uso de código AT para envíos Portugal-Portugal. Reportado en INC000052809081
-   Corregido error al escribir un numero de telefono en el destinario con espacios
-   Corregido ajustes por defecto en seguimiento de estado del pedido al instalar por primera vez el módulo. Reportado en INC000052827113
-   Corregido error de conexión a la base de datos producido al tener un mapeado diferente a utf8
-   Corregido aparicion de nuevos productos internacionales en zonas. Reportado en INC000052834489
-   Corregido enlace duplicado en el email de pedido completado. Reportado en INC000052821316 

### **[1.8.2]** - 10/06/2024

##### Mejoras
-   Se mejoran los filtros de código postal para encontrar CityPaqs

##### Correcciones
-   Corregido error a la hora de filtrar u ordenar los campos en las tablas de utilidades. Reportado en INC000052517709 y INC000052662183 
-   Corregido error al imprimir etiquetas con formato "Papel etiquetas (Solo CEX)"
-   Desaparece mensaje "touching.. touching.." por defecto. Reportado en INC000052807916
-   Corregido error al intentar generar recogida de una devolucion junto la impresión de etiqueta. Reportado en INC000052785687
-   Se corrige limpieza incorrecta de teléfonos
-   Se corrige error al parsear json que venía con código HTML, debido a errores previos de programación en PHP
-   Se corrige malfuncionamiento de función de transformación de centímetros a metros
-   Se utiliza un único endPoint para las recogidas de las devoluciones CEX. Reportado en INC000052793910

### **[1.8.1]** - 17/05/2024

##### Mejoras
-   Configuración de Channable en ZONAS y TRANSPORTISTAS
-   Se añade multicliente a ctrlvers

##### Correcciones
-   Corregido carga de bloques en navegadores Firefox
-   Corregido guardado de NIF en modo compatibilidad post/HPOS
-   Corregido guardado oficina/citypaqs
-   Se revisan los estados en los que es posible cancelar un preregistro
-   Corregido uso y guardado de logotipo personalizado para pedidos CEX
-   Corregido guardado de reglas de coste al utilizar la "," como separador decimal. Reportado en INC000052777527
-   Corregido carga de pedidos en el menu de utilidades cuando hay pedidos corruptos. Reportado en INC000052749703
-   Corregido carga detalles de los city/office paq en el checkout. Reportado en INC000052776416
-   Corregido error al cambiar el estado de un pedido manualmente. Reportdado en INC000052758993

### **[1.8.0]** - 15/04/2024

##### Mejoras
-   Se añade compatibilidad con HPOS y checkout por bloques
-   Se añade la licencia de distribución GPL
-   Se añaden dimensiones por defecto para paq ligero y cityPaq

##### Correcciones
-   Corregido error en el CRON, según algunos detallables no se identificaba en el servicio localizador. Reportado en INC000052711979 y INC000052725928
-   Corregido error en pedido, no aparecía el histórico en algunas configuraciones de PHP. Reportado en INC000052721053
-   Corregido error en WooCommerce, en algunos clientes no se recogía todos los estados de pedido. Reportado en INC000052661317
-   Corregido error con caracteres portugueses. Reportado en INC000052693141
-   Corregido error en medidas de alto/largo/ancho de CEX. Reportado en INC000052715139
-   Corregido error en el que no actualizaba el estado del envío tras preregistrarlo. Reportado en INC000052715136

### **[1.7.1]** 

##### Correcciones
-   Corregido problema de no informa el nº de seguimiento en el email de completado si al preregistrar un pedido pasa al estado Completado sin haber cambiado de página.
    Reportado en INC000052703581

### **[1.7.0]**  - 12/03/2024

##### Mejoras
-   Se añade funcionalidad multicliente

##### Correcciones
-   Corregido problema de devoluciones. Reportado en INC000052646278
-   Corregido prolema de guardado de campo de nif personalizado en Ajustes->Configuración de usuario. Reportado en INC000052657481
-   Corregido envios que no aparecen en la pestaña de resumen de pedidos desde utilidades. Reportado en INC000052634733

### **[1.6.1]**  - 22/01/2024

##### Correcciones
-   Corregido problema de error procesando usuario sin registrar. El comprador tenía que hacer click tres veces (según Theme). Reportado en INC000052587434
-   Corregido problema de fichero de bloqueo. Reportado en INC000052598993
-   Corregido problema de no detección de carácter 'Ú'
-   Corregido problema que impedía la correcta instanciación de la clase Smarty. Reportado en INC000052592084
-   Corregido problema que ocurría al intentar descargar los registros desde la ventana de ajustes. Reportado en INC000052616476
-   Corregido el estado del checkbox "checked" o "unchecked" al tratarse de un pedido CEX/Correos. Reportado en INC000052614185
-   Corregido un error que se producía al tener productos sin clases al calcular las reglas de costo. Reportado en INC000052602177

### **[1.6.0]**  - 09/01/2024

##### Mejoras
-   Se implementa control de versiones y sistemas de avisos

##### Correcciones
-   Corregido problema de pérdida de historial en pedidos. Reportado en INC000052577832

### **[1.5.12]** - 05/01/2024

##### Correcciones
- Corregido problema de compatibilidad con woocommerce en el apartado de las reglas de costes a partir de la version 8.4.0. Reportado en INC000052574734

### **[1.5.11]** - 29/12/2023

##### Correcciones
- No imprimía. Conflicto de espacio de nombres en plugins que usaban la misma librería setasign/Fpdi. Reportado en INC000052552378
- No informaba el codigo postal en Oficina/CityPaq en checkout en ciertos temas. Reportado en INC000052544817

### **[1.5.1]** - 20/12/2023

##### Correcciones
- Corregido error de duplicación a la hora de hacer impresión etiquetas de envío. Reportado en INC000052552378
- Resuelto problema de duplicidad de pedidos que se presentaba desde la versión 1.4.2.0. Reportado en INC000052552378
Este problema se manifestaba al pre-registrar un pedido desde el menú de gestión masiva de envíos.

### **[1.5.0]** - 22/11/2023

##### Mejoras
-   Se obtienen las etiquetas a través de servicio web PS2C en lugar de las tablas

##### Correcciones
-   Se cambia pdo por wpdb para reducir el nº máx de conexiones de usuario (max_user_connections). Reportado en INC000052492688, INC000052495240, INC000052490221 
-   INC000052512699 Cuando hay un remitente es el remitente por defecto 

### **[1.4.30]** - 21/11/2023

##### Mejoras

-   Se añade enlace de seguimiento de Correos y CEX en email de pedido completado
-   Se añade bloque en la sección de edición de pedidos del backoffice para poder editar seguimientos

### **[1.4.2.0]** - 15/11/2023

##### Mejoras

-   Se permite la generación de recogidas, directamente desde la página del pedido mediante check
-   Se elimina la opción en CEX de usar el endpoint de grabación de recogidas, unicamente se podrán hacer mediante el check de generar recogida al crear un preregistro
-   En la pestaña de Recogidas, unicamente aparecerán las de Correos
-   Se elimina la posibilidad de envios con Paq International Light a paises no incluidos en este servicio, además se incluyen a los existentes, Estados Unidos y Kazajistán

##### Correcciones

-   Se impide que se pueda generar un preregistro con el el prefijo telefónico de España en las siguientes versiones, +34 y 0034

##### Correcciones

-   INC000052467958 No genera correctamente el coste de envío. Se corrige que la clase de envío pueda tener caracteres como tildes un otros
-   INC000052481973 No generaba envío con Paq Light Internacional cuando la zona Ubicaciones no cubiertas por tus otras zonas
-   INC000052485872 Error en carrito de woocommerce Se aplica parche directamente. Se enviaban cabeceras en carrito
 
### **[1.4.1.0]** - 30/10/2023

##### Mejoras

-   Nueva columna que muestra los articulos en cada envío de la pestaña utilidades
-   Permite filtrar envios según el nombre del artículo

### **[1.4.0.0]** - 10/10/2023

##### Mejoras

-   Adaptación de código al Market Place de Woocoomerce
-   Mejoras ID00013280 PR00015515, Se añade nuevo producto para recogida en oficina para CEX 

##### Correcciones

-   Se corrige las Clases de Envío en Woocommerce, se admiten palabras separadas por espacio en el nombre de la clase. Reportado en INC000052427256
-   Se ha ajustado el mínimo de carácteres necesarios de 8 a 7 para clientes antiguos. Reportado en INC000052404988

### **[1.3.4.0]** - 11/09/2023

##### Mejoras

-   Se informa el NIF opcional/obligatorio/personalizado en el proceso de compra
-   Se informa el código postal en el buscador en el proceso de checkout cuando se elige transportista Oficina o CityPaq
-   Apellido opcional en pedido y utlilidades
-   Nuevos nombres de campos en Datos de cliente. Descripción dentro de cada campo
- 	Tooltips en datos de de clientes en Ajustes
-   Validaciones en Datos de clientes en Ajustes y mensajes de error

##### Correcciones

-   Se corrige conflicto entre themes y script que ocultaba los avisos del web service
-   Se corrige conflicto con el theme Appilo y nuestro plugin. Reportado en INC000052374847
-   Se añade carácteres necesarios para interpretar los datos del formulario del Destinatario en la pestaña de envío. Reportado en INC000052393819

---

### **[1.3.3.0]** - 10/08/2023

##### Mejoras

-   Se cambia dispatcher por admin-ajax para evitar bloqueos de plugins de seguridad

##### Correcciones

-   Se disminuye el nº máx de conexiones simultáneas a la base de datos. Reportado en INC000052347955

---

### **[1.3.2.1]** - 01/08/2023

##### Mejoras

-   Se cambian los literales para imprimir etiquetas a Papel 4 etiquetas y Papel 3 etiquetas (Solo CEX)

##### Correcciones

-   Corregido error en instalación. No soporte tablas MyiSAm. Reportado en INC000052INC000052303910
-   Corregido error en procesar Paq Light Internacional
-   Corregido error al preregistrar ePaq24
-   Actualizados estados de pedido antiguos por estados nuevos de Correos ya que algunos pedidos con estos estados antiguos no se mostraban en la parrilla de pedidos

---

### **[1.3.2.0]** - 18/07/2023

##### Mejoras

-   Mejoras PR00015515, inclusión de impresión de etiquetas formato 3/A4

##### Correcciones

-   Se corrige el guardado de transportistas en Woocommerce->Ajustes->Métodos de Envío con rango de códigos postales numéricos y alfanuméricos, reportado en INC000052243461

---

### **[1.3.1.2]** - 03/07/2023

##### Correcciones

-   Eliminación de notices a la hora de recuperar estados cuando estos no vienen informados en las devoluciones
-   Corregido bug de estilos que afectaba a la vista principal de la tienda. Reportado en INC000052289791
-   Corregido error que se producía al recuperar el código iso del país al usar rangos de códigos postales en las regiones. Reportado en INC000052243461
-   Corregido problema que se producía al consultar la tabla correos_oficial_requests mediante el campo id_order. Reportado en INC000052297349

---

### **[1.3.1.1]** - 22/06/2023

##### Correcciones

-   Corregido problema que se producía al recuperar la dirección de oficina/city desde la vista de pedido ya que el hash del carrito de compra causaba problemas con la relación de pedido y dirección de oficina/city elegidas. Corrige INC000052259850
-   Corregido problema de caracteres en la dirección del destinatario a la hora de preregistrar. Reportado en INC000052272137

---

### **[1.3.1.0]** - 06/06/2023

##### Mejoras

-   Quitada validación en campo IBAN en la vista de Configuración de usuario y Pedido
-   El peso por defecto (Kg) en Configuración de usuario ahora admite valores con saltos de 100 gramos
-   Quitada obligatoriedad del campo CÓDIGO AT
-   Identificación canal pre-registro para envíos desde Pedido y Utilidades (bulto y multibulto) y devoluciones desde Pedido

##### Correcciones

-   Se escapan caracteres de contraseña antes de enviar el webservice. Solución a INC000052249162
-   Corregido problema al guardar en el pedido la dirección de oficina/city elegidos en checkout

---

### **[1.3.0.6]** - 22/05/2023

##### Mejoras

-   Ampliado juego de caracteres en regex para impedir fallo en preregistro.
-   Ejecución del CRON se hace mediante ajax
-   Se configura un log de errores del CRON
-   Se evita reintentos si el CRON no ha ido correctamente
-   Añadida configuración de Smarty para templates compilados
-   Error que se producía cuando en Woocommerce teníamos marcada la opción "Ocultar los gastos de envío hasta que se introduzca una dirección" haciendo que no se cargaran los estilos ni los ficheros js.
-   Corrección en cambio de estados de pedido de forma automatizada que causaba error con los estados propios de WC.
-   Ampliado número de estados para la automatización de cambios de estados en pedido añadiendo 'wc-cancelled', 'wc-refunded', 'wc-failed'

##### Correcciones

-   Error al cargar los assets de los transportistas de tipo Oficina/CityPaq en el checkout(CarrierExtraContent)

---

### **[1.3.0.5]** - 18/04/2023

##### Correcciones

-   Cambios en los nombres de los estados de pedido de Correos y Cex ya que al ser superiores a 20 caracteres algunos daban fallos.
-   Corregido problema que se producía al preregistrar en algunos casos. Solución a INC000052153207
-   Cambiado literal de advertencia de error en el campo teléfono del estinatario en Pedido
-   Cambiado literal de advertencia para el campo IBAN en Configuración de Usuario
-   Movida carga de assets a la función correosoficialOrderMetaBox para evitar la carga de assets fuera de la edición de pedido ya que esto causaba conflicto con el bootstrap de otros módulos. Reportada en INC000052161795
-   Se divide en dos partes la consulta request (office, citypaq) de checkout ya que cuando la tienda tenía muchos registros en la tabla post_meta se ralentizaba la tienda. Reportado en INC000052164036

---

### **[1.3.0.4]** - 24/03/2023

##### Correcciones

-   Corregido error que hacía que las reglas de coste no funcionaran correctamente cuando había más de un artículo en el carrito, ya que no se estaba sumando el total de esos artículos, sino solamente el último. Soluciona INC000052111121
-   Corregido peso total en reglas de coste cuando este era distinto de entero. Corrige INC000052124489
-   Se cambia a curl la llamada al localizador de Correos
-   Se graba el pedido al llamar el cron en Utils por diferencias de versiones de Woocommerce
-   Se filtra en Utilidades los pedidos eliminados por diferencias de verisones de Woocommerce
-   Fix: Cambio de estados mediante el CRON. Se corrige error relacionado con el cambio de estados en pedido para pedidos que ya se encontraban en estado Entregado en Correos. Corrige INC000052096069

---

### **[1.3.0.3]** - 07/03/2023

##### Correcciones

-   Corregido fallo de operación con tipos de datos incompatibles cost (null + string) en clase CorreosOficialAddShippingMethod reportado en INC000052062264
-   Controlados pedidos eliminados de la tienda para así no tenerlos en cuenta en la ejecución del CRON
-   Se corrige el problema de la Excepción 15500 $order->id_address_delivery está vacía si se han borrado pedidos de la base de datos
-   Controlamos si no está definida la api de google en configuración para impedir el error de google is not defined en el checkout. Corrige INC000052095063
-   Corregida posición(de horizontal a vertical) de etiqueta térmica para los productos Paq Estándar Internacional y Paq Premium Internacional. Corrige INC000052096090

---

### **[1.3.0.2]** - 20/02/2023

##### Mejoras

-   Las reglas de coste por precio se aplicarán después de cupones de descuento
-   La clasificación de regla de coste “Todos los productos” pasa a llamarse “Pedido con varias clases”

##### Correcciones

-   Mostramos todos los productos de Correos en selector para reregistrar desde pedido
-   Aumentada longitud de fila `sender_city` de la tabla senders
-   Cambiado nombre de plugin a Correos Eccommerce
-   Traducción de información de coste en checkout cuando el coste del envío es gratis

---

### **[1.3.0.1]** - 08/02/2023

##### Mejoras

-   Mejorada gestión en descarga de etiquetas

##### Correcciones

-   Fix problema al obtener el país del pedido en Datos destinatario, la forma de obtenerlo podía fallar en algunas tiendas como en la que se reporta en INC000052014777
-   Fix problema con tailing comma tras usar método createButtons al generar el formulario de las reglas de coste
-   Incluído código postal 01000 en la tabla de postcodes y no se generaba un error al filtrar por códigos postal

---

### **[1.3.0.0]** - 18/01/2023

##### Mejoras

-   Compatibilidad con versión PHP8.0

---

### **[1.2.0.4]**

##### Correcciones

-   Solucionado problema al obtener la zona de envío, al haber zonas con mezcla de rangos de cp y zonas con provincias
-   Se corrige problema con puerto por defecto de la base de datos

---

### **[1.2.0.3]** - 10/01/2023

##### Correcciones

-   Se corrige error al guardar productos cuando la tienda tenía configuradas las zonas de envío con códigos postales
-   Se corrige error al guardar remitente
-   Se corrige excepción de la tabla ws_status

---

### **[1.2.0.2]** - 02/01/2023

##### Mejoras

-   Se recoge el campo dirección complementaria
-   Arreglo de Mensaje de advertencia al comprador en Ajustes->Tramitación Aduanera de Envíos
-   Mensaje de advertencia al comprador en checkout envíos internacionales
-   Corregida la hora de envío de CEX en histórico de Pedidos
-   Dirección complementaria se duplicaba en Utilidades
-   Se eliminan espacios antes y después de los campos en Ajustes->Remitentes

##### Correcciones

-   Se corrige error al guardar productos cuando la tienda tenía configuradas las zonas de envío con códigos postales
-   Se soluciona el error this is incompatible with sql_mode=only_full_group_by al prerregistrar en Utilidades.
-   Prefijado estilos CSS en checkout

---

### **[1.2.0.1]** - 19/12/2022

##### Mejoras

-   Arreglos en etiquetas de devolución de Correos
-   Se envía nº de pedido en el campo Referencia en la devolución
-   Se recoge el campo dirección complementaria

---

### **[1.2.0.0]**

##### Mejoras

-   Se permiten hasta cinco descripciones aduaneras por envío en el pedido
-   Se modifica el bloque Remitentes en pedido simplificándolo
-   Se cambian nuevos logos
-   Añadida funcionalidad del CRON en Configuración de Usuario
-   Se añade la descripción del módulo, plataforma y sus versiones en el campo "refCliente" de CEX
-   En el campo "Dirección" en "Pedido" se concatena la dirección complementaria y se envía al preregistro
-   Se añade el número de pedido delante de la referencia del pedido en el campo "Referencia" de "Pedido" y en el preregistro, en el campo "ReferenciaCliente"
-   Redefinido bloque devoluciones

---

### **[1.1.0.3]**

##### Corrección

-   Corregido error que impedía que se generara correctamente la etiqueta cuando se trataba de un pedido a contra reembolso.
-   Implementado método getSubTotal() para obtener el valor neto del pedido necesario para envíos con aduanas.
-   Añadido tiempo prudencial de 5 segundos para eliminar las etiquetas del directorio temporal tras generarlas. Esto generaba error ya que se eliminaban antes de descargarse.

---

### **[1.1.0.2]** - 07/12/2022

##### Mejoras

-   Cambiada forma de tratar las rutas en tiendas instaladas en subdirectorios. Ahora se asume que todas las tiendas están instaladas en raíz

---

### **[1.1.0.1]** - 10/11/2022

##### Mejoras

-   Se cambian los valores que quedaban de prefijo "wp" para que coja el prefijo que haya puesto el cliente para su BBDD

---

### **[1.1.0.0]** - 25/10/2022

##### Mejoras

-   Cambio de nombre de los productos (Mayusculas-Minúsculas)
-   Cambios en el campo "referenciaCliente3"
-   Hipervínculo a pedido desde utilidades
-   Mostrar y ocultar campos en las tablas de "Utilidades"
-   Bloque devoluciones siempre visible - ya no depende del estado del envío. Cambio de lógica con selector de producto de
    devolución: PaqRetorno / Paq24
-   Incluidas nuevas descripciones aduaneras
-   Modificación bloque "Datos de remitente" en "Pedido" (Enlace para editar remitente desde pedido)

---

### **[1.0.0.1]** - 21-10-2022

-   Cuota fija en Ajustes de transportista
-   Manifiesto no lista los códigos largos (los de envío)
-   Enlace de seguimiento en la cuenta de comprador
-   Anular recogida dentro del pedido
-   Paq Estándar Oficina Elegida - Está mal la modalidad entrega
-   Envíos multibulto. Peso del bloque aduana no llega al xml
-   Mensaje al usuario cuando el webservice no responda
-   Textos en Ajustes del transportista
-   Comportamiento países y transportistas disponibles
-   Pre-registro desde Utilidades no toma valor del pedido
-   ImporteSeguro no es integer en el xml
-   NIF Destinatario no va al xml de pre-registro
-   Dirección duplicada en etiquetas
-   Solo se obtiene cn23 del bulto 1 / Botones DCAF DDP
-   Envíos internacionales tienen una letra rara en Reimpresión etiquetas
-   Precio y peso no vuelcan al bloque Datos Aduana del pedido
-   DCAF/DDP una sola vez

---

### **[1.0.0.0]** - 15-09-2022

-   Entrega inicial de Release
