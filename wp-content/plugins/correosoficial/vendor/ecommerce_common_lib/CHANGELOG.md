# Correos Ecommerce

### **[1.9.1]** - 18/02/25

##### Mejoras
- Se añade compatibilidad con el módulo de logística

### **[1.9.0]** - 14/01/25

##### Mejoras
- Copia del teléfono del destinatario a <NumeroSMS> según formato y destino
- Traslado del campo "empresa" al pre-registro: en Correos, al bloque Destinatario; en CEX, concatenado con el nombre del destinatario
- Comprobación de que la extensión Soap está instalada al añadir las credenciales de Correos

### **[1.8.6]** - 22/10/2024

##### Mejoras
-    Se añaden indices para las tablas del módulo que disminuyen la tiempos de carga en las consultas

##### Correcciones
-    Se mejora el tratamiento de los carácteres especiales durante el preregistro. Reportado en INC000052947920 y INC000052965826

### **[1.8.5]** - 02/09/2024

##### Mejoras
-    Se añade control a la hora de añadir un código de usuario ya existente en la base de datos

### **[1.8.4]** - 15/07/2024

##### Correcciones
-   Corregido limpieza de números de teléfono durante el preregistro. Reportado en INC000052856195

### **[1.8.3]** - 02/07/2024

##### Correcciones
-   Se corrige conflicto con clase utils. Reportado en INC000052829728
-   Corregido comprobacion y uso de código AT para envíos Portugal-Portugal. Reportado en INC000052809081
-   Corregido comprobacion y envio de numero de SMS cuando es preregistrado un envio. INC000052827356
-   Eliminada referencia en listaBultos a la hora de hacer un preregistro con producto CEX

### **[1.8.2]** - 10/06/2024

##### Mejoras
-   Se incluyen nuevas funciones para filtrar números de teléfono y códigos postales

### **[1.8.1]** - 17/05/2024

##### Correcciones
-   Corregido uso y guardado de logotipo personalizado para pedidos CEX
-   Corregido problema al guardar remitente y tratar de ejecutar analitica

### **[1.8.0]** - 15/04/2024

##### Mejoras
-   Se añade compatibilidad con HPOS y checkout por bloques
-   Se añade la licencia de distribución GPL
-   Se añaden dimensiones por defecto para paq ligero y cityPaq
-   Se añaden los paises China y Zimbabue a paq light

##### Correcciones
-   Corregido error en el CRON, según algunos detallables no se identificaba en el servicio localizador. Reportado en INC000052711979 y INC000052725928
-   Corregido error en pedido, no aparecía el histórico en algunas configuraciones de PHP. Reportado en INC000052721053
-   Corregido error en WooCommerce, en algunos clientes no se recogía todos los estados de pedido. Reportado en INC000052661317
-   Corregido error con caracteres portugueses. Reportado en INC000052693141
-   Corregido error en el que no actualizaba el estado del envío tras preregistrarlo. Reportado en INC000052715136
-   Corregido error con las medidas de los bultos de CEX. Reportado en INC000052715139 y INC000052715236

### **[1.7.0]** - 08/03/2024

##### Mejoras
-   Se añade funcionalidad multicliente

##### Correcciones
-   Corregido un error que impredía instalar el módulo. Reportado en INC000052646287

### **[1.6.1]** - 08/02/2024

##### Correcciones
-   Corregido problema que impedía la correcta instanciación de la clase Smarty. Reportado en INC000052592084
-   Corregido problema que ocurría al intentar descargar los registros desde la ventana de ajustes. Reportado en INC000052616476
-   Corregido un error que impedía la actualización del cron. Reportado en INC000052609766

### **[1.5.12]** - 05/01/2023

##### Correcciones
-   Corregido problema de compatibilidad con woocommerce en el apartado de las reglas de costes a partir de la version 8.4.0. Reportado en INC000052574734

### **[1.5.1]** - 20/12/2023

##### Correcciones
-   Corregido error de duplicación a la hora de hacer impresión etiquetas de envío. Reportado en INC000052552378
-   Resuelto problema de duplicidad de pedidos que se presentaba desde la versión 1.4.2.0. Reportado en INC000052552378
Este problema se manifestaba al pre-registrar un pedido desde el menú de gestión masiva de envíos

### **[1.5.0]** - 22/11/2023

##### Mejoras
-   Se obtienen las etiquetas a traves de servicio web PS2C en lugar de las tablas

##### Correcciones
-   Se cambia pdo por wpdb para reducir el nº máx de conexiones de usuario (max_user_connections). Reportado en INC000052492688, INC000052495240, INC000052490221
-   INC000052512699 Cuando hay un remitente es el remitente por defecto
-   Se corrige warning al no encontrar ficheros de secret y tener mal puesta la contraseña. Reportado en INC000052520481

### **[1.3.5.0]** - 30/10/2023

### Mejoras
-   Nueva columna que muestra los articulos en cada envío de la pestaña utilidades
-   Permite filtrar envios según el nombre del artículo.

### **[1.3.4.0]** - 16/10/2023

### Mejoras
-   Adaptación de código al Market Place de Woocoomerce
-   Se añade nuevo producto para recogida en oficina para CEX. Reportado en ID00013280 PR00015515

#### Correcciones
-   Se ha ajustado el mínimo de carácteres necesarios de 8 a 7 para clientes antiguos. Reportado en INC000052404988

### **[1.3.3.0]** - 11/09/2023

##### Mejoras
-   Se informa el NIF opcional/obligatorio/personalizado en el proceso de compra
-   Se informa el código postal en el buscador en el proceso de checkout cuando se elige transportista Oficina o CityPaq
-   Apellido opcional en pedido y utlilidades
-   Nuevos nombres de campos en Datos de cliente. Descripción dentro de cada campo
- 	Tooltips en datos de de clientes en Ajustes
-   Validaciones en Datos de clientes en Ajustes y mensajes de error

##### Correcciones
-   Se añade carácteres necesarios para interpretar los datos del formulario del Destinatario en la pestaña de envío. Reportado en INC000052393819
-   Se corrige un error que afectaba a la carga de la pestaña de la Doc. Aduanera. Reportado en INC000052402350

---

### **[1.3.2.0]** - 10/08/2023

##### Mejoras
-   Se cambia dispatcher por admin-ajax para evitar bloqueos de plugins de seguridad
-   Transportistas Oficina / Citypaq en el checkout. Cargar con búsqueda realizada
-   Apellidos opcionales en el formulario Destinatario para pre-registrar
-   Nuevos nombres de campos en Datos de cliente. Descripción dentro de cada campo
-   Tooltips en datos de cliente en Ajustes
-   Validaciones en Datos de cliente en Ajustes y mensajes de error
-   Se informa el código postal en el buscador en el proceso de checkout cuando se elige transportista Oficina o CityPaq

##### Correcciones
-   Corregido problema con contraseña caracteres alemanes. Reportado en WO0000050425057

### **[1.3.1.1]** - 01/08/2023

##### Correcciones
- Corregido problema con contraseña en Prestashop con contrabarra. Reportado en INC000052321224
- Corregido error en procesar Paq Light Internacional.
- Corregido error al preregistrar ePaq24

---

### **[1.3.1.0]** - 11/07/2023

##### Mejoras
-   Mejoras PR00015515, inclusión de impresión de etiquetas formato 3/A4

##### Correcciones
-   Corregido error en búsqueda por columnas en los datatables de utilidades. Reportado en INC000052305084
-   Corregido bug del selector de Remitente en bloque Datos Remitente de Pedido ya que se estaba preregistrando con los datos del Remitente por defecto y no con los del Remitente elegido en el selector. Reportado en INC000052284500
-   Corregido error que se producía al recuperar el código iso del país al usar rangos de códigos postales en las regiones. Reportado en INC000052243461

---

### **[1.3.0.6]** - 22/06/2023

##### Correcciones
-   Corregido problema que se producía al recuperar la dirección de oficina/city desde la vista de pedido ya que el hash del carrito de compra causaba problemas con la relación de pedido y dirección de oficina/city elegidas
-   Corregido problema de caracteres en la dirección del destinatario a la hora de preregistrar. Reportado en INC000052272137

---

### **[1.3.0.5]** - 06/06/2023
##### Mejoras
-   Quitada validación en campo IBAN en la vista de Configuración de usuario y Pedido
-   El peso por defecto (Kg) en Configuración de usuario ahora admite valores con saltos de 100 gramos
-   Quitada obligatoriedad del campo CÓDIGO AT
-   Identificación canal pre-registro para envíos desde Pedido y Utilidades (bulto y multibulto) y devoluciones desde Pedido

##### Correcciones
-   Se prefija variable de prestashop a co_ps_base_uri ya que había colisión de nombres con otro módulo. Reportado en INC000052231342
-   Se escapan caracteres de contraseña antes de enviar el webservice. Solución a INC000052249162
-   Corregida descarga de etiqueta tras preregistrar un envío desde Utilidades - Gestión Masiva. Corrige INC000052257645


---

### **[1.3.0.4]** - 15/05/2023

##### Mejoras
-   Ejecución del CRON se hace mediante ajax
-   Se configura un log de errores del CRON
-   Se evita reintentos si el CRON no ha ido correctamente
##### Correcciones
- Ampliado juego de caracteres en regex para impedir fallo en preregistro.

---

### **[1.3.0.3]** - 18/04/2023

##### Correcciones
-   Cambios en los nombres de los estados de pedido de Correos y Cex ya que al ser superiores a 20 caracteres algunos daban fallos.
-   Corregido problema que se producía al preregistrar en algunos casos. Solución a INC000052153207

---
### **[1.3.0.2]** - 24/03/2023
##### Correcciones
-   Limitado a 1 el número de registros devueltos en la consulta para recuperar los pedidos en Gestión Masiva de Envíos
-   Se cambia a curl la llamada al localizador de Correos 
-   Se graba el pedido al llamar el cron en Utils por diferencias de versiones de Woocommerce
-   Se filtra en Utilidades los pedidos eliminados por diferencias de verisones de Woocommerce
-   Fix: Cambio de estados mediante el CRON. Se corrige error relacionado con el cambio de estados en pedido para pedidos quq ya se encontraban en estado Entregado en Correos. Corrige INC000052096069

---

### **[1.3.0.1]** - 08/02/2023
##### Correcciones
-   Timeout a las llamadas ajax para borrar documentos de aduanas una vez descargados, ya que se estaban eliminando antes de que el navegador los descargara, soluciona INC000052022061
-   Controlamos si no está definida la api de google en configuración para impedir el error de google is not defined en el checkout. Corrige INC000052095063

---
### **[1.3.0.0]** - 18/01/2023
##### Mejoras
-   Compatibilidad con versión PHP8.0
---

### **[1.2.0.7]**

##### Correcciones
-   Se corrige el problema de la Excepción 15500 $order->id_address_delivery está vacía si se han borrado pedidos de la base de datos

---

### **[1.2.0.6]** - 20/02/2023

##### Correcciones

-   Actualizado regex de contraseñas

---

### **[1.2.0.5]** - 08/02/2023

##### Mejoras

-   Mejorada gestión en descarga de etiquetas

##### Correcciones

-   Se corrige problema en Zonas y Transportistas que eliminaba producto cuando se asignaba a un transportista existente. Problema reportado en INC000052026425

---

### **[1.2.0.4]**
##### Correcciones
- Solucionado problema al obtener la zona de envío, al haber zonas con mezcla de rangos de cp y zonas con provincias
- Se corrige problema con puerto por defecto de la base de datos
- Timeout a las llamadas ajax para borrar docuemntos de aduanas una vez descargados, ya que se estaban eliminando antes de que el navegador los descargara, soluciona INC000052022061

---
### **[1.2.0.3]** - 09/01/2023
##### Corrección
- Se corrige error al guardar productos cuando la tienda tenía configuradas las zonas de envío con códigos postales
- Se corrige error al guardar remitente
- Se reduce tamaño de los logos de los carriers, ya que se veían muy grandes en resumen de pedido(checkout)
- Controlamos si en checkout se informa teléfono o teléfono móvil para posteriormente mostrarlo destinatario dentro de pedido

---
### **[1.2.0.2]** - 02/01/2023

##### Mejoras

    - Se recoge el campo dirección complementaria
    - Arreglo de Mensaje de advertencia al comprador en Ajustes->Tramitación Aduanera de Envíos
    - Mensaje de advertencia al comprador en checkout envíos internacionales
    - Corregida la hora de envío de CEX en histórico de Pedidos
    - Dirección complementaria se duplicaba en Utilidades
    - Se eliminan espacios antes y después de los campos en Ajustes->Remitentes

##### Corrección

    - Se soluciona el error this is incompatible with sql_mode='only_full_group_by' al prerregistrar en Utilidades.
    - Prefijado estilos CSS en checkout

---
### **[1.2.0.1]** - 19/12/2022
##### Mejoras
- Arreglos en etiquetas de devolución de Correos
- Se envía nº de pedido en el campo Referencia en la devolución
- Se recoge el campo dirección complementaria
---
### **[1.2.0.0]** - 14/12/2022  
##### Mejoras
- Se permiten hasta cinco descripciones aduaneras por envío en el pedido
- Se modifica el bloque Remitentes en pedido simplificándolo
- Se cambian nuevos logos
- Añadida funcionalidad del CRON en Configuración de Usuario
- Se añade la descripción del módulo, plataforma y sus versiones en el campo "refCliente" de CEX
- En el campo "Dirección" en "Pedido" se concatena la dirección complementaria y se envía al preregistro
- Se añade el número de pedido delante de la referencia del pedido en el campo "Referencia" de "Pedido" y en el preregistro, en el campo "ReferenciaCliente"
- Redefinido bloque devoluciones



---
### **[1.1.0.3]** - 13/12/2022
##### Corrección
- Corregido error que impedía que se generara correctamente la etiqueta cuando se trataba de un pedido a contra reembolso.
- Implementado método getSubTotal() para obtener el valor neto del pedido necesario para envíos con aduanas.
- Tratadas por igual las rutas del módulo en tiendas instaladas en raiz y subdirectorios.
- Añadido tiempo prudencial de 5 segundos para eliminar las etiquetas del directorio temporal tras generarlas. Esto generaba error ya que se eliminaban antes de descargarse.
---
### **[1.1.0.2]** - 07/12/2022
##### Mejoras
- Se permiten hasta cinco descripciones aduaneras por envío en el pedido
- Se modifica el bloque Remitentes en pedido simplificándolo
- Se cambian nuevos logos
- Añadida funcionalidad del CRON en Configuración de Usuario
- Se añade la descripción del módulo, plataforma y sus versiones en el campo "refCliente" de CEX
- En el campo "Dirección" en "Pedido" se concatena la dirección complementaria y se envía al preregistro
- Se añade el número de pedido delante de la referencia del pedido en el campo "Referencia" de "Pedido" y en el preregistro, en el campo "ReferenciaCliente"
- Redefinido bloque devoluciones

##### Corrección
- Se eliminan productos Islas Documentación y Paquetería Óptica de  Correos Express cuando la zona es Portugal 
---
### **[1.1.0.1]** - 10/11/2022
##### Mejoras
- Se cambian los valores que quedaban de prefijo "wp" para que coja el prefijo que haya puesto el cliente para su BBDD
---
### **[1.1.0.0]** - 28/10/2022
##### Mejoras
- Cambio de nombre de los productos (Mayusculas-Minúsculas)
- Cambios en el campo "referenciaCliente3"
- Hipervínculo a pedido desde utilidades
- Mostrar y ocultar campos en las tablas de "Utilidades"
- Bloque devoluciones siempre visible - ya no depende del estado del envío. Cambio de lógica con selector de producto de 
devolución: PaqRetorno / Paq24
- Incluidas nuevas descripciones aduaneras
- Modificación bloque "Datos de remitente" en "Pedido" (Enlace para editar remitente desde pedido)
---
### **[1.0.0.1]** - 21-10-2022
- Cuota fija en Ajustes de transportista
- Manifiesto no lista los códigos largos (los de envío)
- Enlace de seguimiento en la cuenta de comprador
- Anular recogida dentro del pedido
- Paq Estándar Oficina Elegida - Está mal la modalidad entrega
- Envíos multibulto. Peso del bloque aduana no llega al xml
- Mensaje al usuario cuando el webservice no responda
- Textos en Ajustes del transportista
- Comportamiento países y transportistas disponibles
- Pre-registro desde Utilidades no toma valor del pedido
- ImporteSeguro no es integer en el xml
- NIF Destinatario no va al xml de pre-registro
- Dirección duplicada en etiquetas
- Solo se obtiene cn23 del bulto 1 / Botones DCAF DDP
- Envíos internacionales tienen una letra rara en Reimpresión etiquetas
- Precio y peso no vuelcan al bloque Datos Aduana del pedido
- DCAF/DDP una sola vez
---
### **[1.0.0.0]** - 15-09-2022
- Entrega inicial de Release
