let $ = jQuery;
window.adpnsy = {
  quill: {}
}
const alert = function(text, ico = false){if(!ico){var img = '<i class="spuser_alert_img material-symbols-outlined">warning</i>';}else{var img = '<i class="spuser_alert_img material-symbols-outlined">check_circle</i>';}var el = $("<div />",{class: 'spuser_alert'});el.append($("<div />",{ class: 'spuser_alert_cont' }).append( $("<a />",{href:"#", class: 'spuser_alert_close', text: "x"}).on("click", function(e) { e.preventDefault(); el.removeClass("show"); setTimeout(function(){ el.remove() }, 600); }) ).append( img  ).append( $("<p />",{class: 'spuser_alert_text', html: text}) ));$("body").append(el);setTimeout(function(){ el.addClass("show"); }, 100);return;}
const alert_confirm = function(text, fun){var img = '<i class="spuser_alert_img material-symbols-outlined">warning</i>';var el = $("<div />",{class: 'spuser_alert'});el.append($("<div />",{ class: 'spuser_alert_cont' }).append($("<a />",{href:"#", class: 'spuser_alert_close', text: "x"}).on("click", function(e) { e.preventDefault(); el.removeClass("show"); setTimeout(function(){ el.remove() }, 600); })).append( img ).append( $("<p />",{class: 'spuser_alert_text', html: text}) ).append( $("<div />",{class: 'spuser_alert_btns'}).append($("<a />",{href:"#", class: 'spuser_alert_btn spuser_alert_accept', text: "Si"}).on("click", function(e) { fun(); e.preventDefault(); el.removeClass("show"); setTimeout(function(){ el.remove() }, 600); })).append($("<a />",{href:"#", class: 'spuser_alert_btn spuser_alert_cancel', text: "No"}).on("click", function(e) { fun; e.preventDefault(); el.removeClass("show"); setTimeout(function(){ el.remove() }, 600); })) ));$("body").append(el);setTimeout(function(){ el.addClass("show"); }, 100);return;}
const loading = function(text){var el = $("#spuser_loading");if(!el.length){el = $('<div class="spuser_modal" id="spuser_loading"><div class="spuser_modal_load"><p class="spuser_modal_load_title"><span id="load-msg">Cargando</span><span>.</span><span>.</span><span>.</span></p></div></div>')};el.find("#load-msg").text(text);$("body").append(el);el.fadeIn();}
const loading_end = function(time = 0){var el = $("#spuser_loading");if(el.length > 0){setTimeout(function(){el.fadeOut();setTimeout(function() { el.remove();}, 500);}, time);}}
const datatablet_es = {"processing":"Procesando...","lengthMenu":"Mostrar _MENU_ registros","zeroRecords":"No se encontraron resultados","emptyTable":"Ningún dato disponible en esta tabla","infoEmpty":"Mostrando registros del 0 al 0 de un total de 0 registros","infoFiltered":"(filtrado de un total de _MAX_ registros)","search":"Buscar:","infoThousands":",","loadingRecords":"Cargando...","paginate":{"first":"Primero","last":"Último","next":"Siguiente","previous":"Anterior"},"aria":{"sortAscending":": Activar para ordenar la columna de manera ascendente","sortDescending":": Activar para ordenar la columna de manera descendente"},"buttons":{"copy":"Copiar","colvis":"Visibilidad","collection":"Colección","colvisRestore":"Restaurar visibilidad","copyKeys":"Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br /> <br /> Para cancelar, haga clic en este mensaje o presione escape.","copySuccess":{"1":"Copiada 1 fila al portapapeles","_":"Copiadas %ds fila al portapapeles"},"copyTitle":"Copiar al portapapeles","csv":"CSV","excel":"Excel","pageLength":{"-1":"Mostrar todas las filas","_":"Mostrar %d filas"},"pdf":"PDF","print":"Imprimir","renameState":"Cambiar nombre","updateState":"Actualizar","createState":"Crear Estado","removeAllStates":"Remover Estados","removeState":"Remover","savedStates":"Estados Guardados","stateRestore":"Estado %d"},"autoFill":{"cancel":"Cancelar","fill":"Rellene todas las celdas con <i>%d</i>","fillHorizontal":"Rellenar celdas horizontalmente","fillVertical":"Rellenar celdas verticalmentemente"},"decimal":",","searchBuilder":{"add":"Añadir condición","button":{"0":"Constructor de búsqueda","_":"Constructor de búsqueda (%d)"},"clearAll":"Borrar todo","condition":"Condición","conditions":{"date":{"after":"Despues","before":"Antes","between":"Entre","empty":"Vacío","equals":"Igual a","notBetween":"No entre","notEmpty":"No Vacio","not":"Diferente de"},"number":{"between":"Entre","empty":"Vacio","equals":"Igual a","gt":"Mayor a","gte":"Mayor o igual a","lt":"Menor que","lte":"Menor o igual que","notBetween":"No entre","notEmpty":"No vacío","not":"Diferente de"},"string":{"contains":"Contiene","empty":"Vacío","endsWith":"Termina en","equals":"Igual a","notEmpty":"No Vacio","startsWith":"Empieza con","not":"Diferente de","notContains":"No Contiene","notStarts":"No empieza con","notEnds":"No termina con"},"array":{"not":"Diferente de","equals":"Igual","empty":"Vacío","contains":"Contiene","notEmpty":"No Vacío","without":"Sin"}},"data":"Data","deleteTitle":"Eliminar regla de filtrado","leftTitle":"Criterios anulados","logicAnd":"Y","logicOr":"O","rightTitle":"Criterios de sangría","title":{"0":"Constructor de búsqueda","_":"Constructor de búsqueda (%d)"},"value":"Valor"},"searchPanes":{"clearMessage":"Borrar todo","collapse":{"0":"Paneles de búsqueda","_":"Paneles de búsqueda (%d)"},"count":"{total}","countFiltered":"{shown} ({total})","emptyPanes":"Sin paneles de búsqueda","loadMessage":"Cargando paneles de búsqueda","title":"Filtros Activos - %d","showMessage":"Mostrar Todo","collapseMessage":"Colapsar Todo"},"select":{"cells":{"1":"1 celda seleccionada","_":"%d celdas seleccionadas"},"columns":{"1":"1 columna seleccionada","_":"%d columnas seleccionadas"},"rows":{"1":"1 fila seleccionada","_":"%d filas seleccionadas"}},"thousands":".","datetime":{"previous":"Anterior","next":"Proximo","hours":"Horas","minutes":"Minutos","seconds":"Segundos","unknown":"-","amPm":["AM","PM"],"months":{"0":"Enero","1":"Febrero","2":"Marzo","3":"Abril","4":"Mayo","5":"Junio","6":"Julio","7":"Agosto","8":"Septiembre","9":"Octubre","10":"Noviembre","11":"Diciembre"},"weekdays":["Dom","Lun","Mar","Mie","Jue","Vie","Sab"]},"editor":{"close":"Cerrar","create":{"button":"Nuevo","title":"Crear Nuevo Registro","submit":"Crear"},"edit":{"button":"Editar","title":"Editar Registro","submit":"Actualizar"},"remove":{"button":"Eliminar","title":"Eliminar Registro","submit":"Eliminar","confirm":{"1":"¿Está seguro que desea eliminar 1 fila?","_":"¿Está seguro que desea eliminar %d filas?"}},"error":{"system":"Ha ocurrido un error en el sistema (<a target=\"\\\" rel=\"\\ nofollow\" href=\"\\\">Más información&lt;\\/a&gt;).</a>"},"multi":{"title":"Múltiples Valores","info":"Los elementos seleccionados contienen diferentes valores para este registro. Para editar y establecer todos los elementos de este registro con el mismo valor, hacer click o tap aquí, de lo contrario conservarán sus valores individuales.","restore":"Deshacer Cambios","noMulti":"Este registro puede ser editado individualmente, pero no como parte de un grupo."}},"info":"Mostrando _START_ a _END_ de _TOTAL_ registros","stateRestore":{"creationModal":{"button":"Crear","name":"Nombre:","order":"Clasificación","paging":"Paginación","search":"Busqueda","select":"Seleccionar","columns":{"search":"Búsqueda de Columna","visible":"Visibilidad de Columna"},"title":"Crear Nuevo Estado","toggleLabel":"Incluir:"},"emptyError":"El nombre no puede estar vacio","removeConfirm":"¿Seguro que quiere eliminar este %s?","removeError":"Error al eliminar el registro","removeJoiner":"y","removeSubmit":"Eliminar","renameButton":"Cambiar Nombre","renameLabel":"Nuevo nombre para %s","duplicateError":"Ya existe un Estado con este nombre.","emptyStates":"No hay Estados guardados","removeTitle":"Remover Estado","renameTitle":"Cambiar Nombre Estado"}};
const getCookie = (cname) => {var name = cname + "=";var decodedCookie = decodeURIComponent(document.cookie);var ca = decodedCookie.split(';');for(var i = 0; i <ca.length; i++) {var c = ca[i];while (c.charAt(0) == ' ') {c = c.substring(1);}if (c.indexOf(name) == 0) {return c.substring(name.length, c.length);}}return "";}
const urlBase64ToUint8Array = function(base64String) {const padding = '='.repeat((4 - (base64String.length % 4)) % 4);const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');const rawData = window.atob(base64);const outputArray = new Uint8Array(rawData.length);for (let i = 0; i < rawData.length; ++i) {outputArray[i] = rawData.charCodeAt(i);}return outputArray;}
const alert_options = function(text, funs){let el = $("<div />",{class: 'spuser_alert'}).append($("<div />",{ class: 'spuser_alert_cont' }).append($("<a />",{href:"#", class: 'spuser_alert_close', text: "x"}).on("click", function(e) { e.preventDefault(); el.removeClass("show"); setTimeout(function(){ el.remove() }, 600); })).append( $("<p />",{class: 'spuser_alert_text p-0', html: text}) ).append( $("<div />",{class: 'spuser_alert_btns ml-0'}) ));let bts = el.find('.spuser_alert_btns');$.each(funs, (nam, fun) => {bts.append($("<a />",{href:"#", class: 'spuser_alert_btn spuser_alert_multiple', text: nam}).on("click", function(e) { fun(); e.preventDefault(); el.removeClass("show"); setTimeout(function(){ el.remove() }, 600); }))});$("body").append(el);setTimeout(function(){ el.addClass("show"); }, 100);return;}
const isEmail = (email) => { var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/; return regex.test(email); };
const isObject = (obj) => (typeof obj === 'object' && obj !== null);
const lel = (_e, _c)  => $(`#_${_e}_${_c}_`).val();
const leq = (_e, _c)  => window.adpnsy.quill[`_${_e}_${_c}_`].getSemanticHTML();
const nbd = (num) => num.toString().padStart(2, '0');
const fillField = (selector, value) => $(selector).val(value).trigger('change').hasClass('validate') && $(selector).siblings('label').toggleClass('active', !!value);
const compararObjetos = (obj1, obj2) => Object.entries(obj1).every(([k, v]) => (Array.isArray(obj2[k]) ? obj2[k].join(",") : obj2[k]) == v);
const EUR = v => isNaN(v = parseFloat(v)) ? "0,00 €" : v.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + " €";
const FECHA_FORMAT = d => isNaN(d = new Date(d)) ? "Fecha inválida" : d.toLocaleDateString('es-ES');
const FECHA_FORMAT_HORA = d => isNaN(d = new Date(d)) ? "Fecha inválida" : d.toLocaleDateString('es-ES') + " " + d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
const convertirFechaESaEN = fecha => {if (typeof fecha === "string" && /^\d{2}\/\d{2}\/\d{4}$/.test(fecha)) { const [dia, mes, anio] = fecha.split("/"); return `${anio}-${mes.padStart(2, "0")}-${dia.padStart(2, "0")}`;} return fecha;}

const registe_psh = function(){
  if (!('safari' in window ) ) {
    if (!('serviceWorker' in navigator)) {console.warn('Service workers are not supported by this browser');return;}
    if (!('PushManager' in window)) {console.warn('Push notifications are not supported by this browser');return;}
    if (!('showNotification' in ServiceWorkerRegistration.prototype)) {console.warn('Notifications are not supported by this browser');return;}
    if (Notification.permission === 'denied') {console.warn('Notifications are denied by the user');return;}
    Notification.requestPermission();
  }
}


const modalsp = (titulo, contenido, action, action_name = "Guardar", tipo = "", id = "", extras = {}) => {
  let modal = null;
	let tablas = {};
  if(!id) id = 'modalsp' + Math.random().toString(36).substring(2, 8);
  if(tipo == 'nomodal'){
    modal = $(`<div class="row"><div class="col s12"><div class="container"><section class="section"><div class="card"><div class="card-content"><div class="adpn_basic_botton"><button class="btn adpn_action">${action_name}</button></div></div></div></section></div></div></div>`);
    modal.find(".card-content").prepend(contenido);
    modal.find(".adpn_action").click(function(e){e.preventDefault();if(action() == true){window.location.reload();}});
    $(`#${id}`).append(modal);
  }else{
    modal = $(`<div class="adpn_modal" id="${id}"><div class="adpn_modal_content${tipo}"><div class="adpn_modal_header degradado"><h3>${titulo}</h3><a href="#" class="adpn_modal_close adp_modal_hide"><i class="material-symbols-outlined">close</i></a></div><div class="adp_modal_body"></div><div class="adpn_modal_botton"><button class="btn adpn_action">${action_name}</button><button class="btn adp_modal_hide">Cancelar</button></div></div></div>`);
    modal.find(".adp_modal_body").append(contenido);
    modal.find(".adp_modal_hide").click(function(e){e.preventDefault();modal.fadeOut(500, function(ex){ modal.remove()});});
    modal.find(".adpn_action").click(function(e){e.preventDefault();if(action() == true){modal.fadeOut(500, function(ex){ modal.remove()});}});
    $("body").append(modal);
  }

  if('tablas' in extras){
    $.each(extras.tablas, (k,v) => {
      tablas[k] = $(`#${k}`).DataTable(v);
    });
  }
  if(action == null) modal.find(".adpn_action").hide();
  if(action == null) modal.fadeIn();
	return {
    e: modal,
    open: () => {
      let dtp = {...datepicker};
      let tmp = {...timepicker};
      dtp.container = modal;
      tmp.container = `#${id}`;
      if(modal.find(".adpnsy-datepicker").length) modal.find(".adpnsy-datepicker").datepicker(dtp);
      if(modal.find(".adpnsy-timepicker").length) modal.find(".adpnsy-timepicker").timepicker(tmp);
      if(modal.find(".adpnsy-dropify").length) modal.find(".adpnsy-dropify").dropify(dropify);
      if(modal.find(".adpnsy-select2").length){
        modal.find(".adpnsy-select2").select2({ dropdownParent: modal, language: {
          noResults: function() {
            return "Sin resultados";        
          },
          searching: function() {
            return "Buscando...";
          }
        } });
      }
      if(modal.find(".adpnsy-textarea").length) modal.find(".adpnsy-textarea").each(function(k,v){ M.textareaAutoResize($(v)); });
      if(modal.find(".adpnsy-quill").length){
        modal.find(".adpnsy-quill").each((k,v) => {
          const q = $(v);
          const id = q.attr('id');
          const options = ('quill' in extras) ? extras.quill(id, q) : {
            modules: {
              toolbar: {
                container: [
                  [{ header: [1, 2, 3, 4, 5, 6, false] }],
                  ['bold', 'italic', 'underline'],
                  [{ list: 'ordered'}, { list: 'bullet' }],
                  [{ 'align': [] }],
                  ['blockquote', 'code-block'],
                  ['link'],
                  ['html']
                ],
                handlers: {
                  'html': () => toggleHtmlView(id)
                }
              }
            },
            placeholder: q.attr('placeholder'),
            theme: 'snow'
          };
          $(`#${id}_html`).on('input', function(){
            const html = $(this).val();
            const quill = window.adpnsy.quill[id];
            quill.root.innerHTML = html;
          })
          window.adpnsy.quill[id] = new Quill(`#${id}`, options);
        })
      }
      if(modal.find(".adpnsy-files").length) $.each(modal.find(".adpnsy-files"), function(k,v){
        const input = $(v).find(".adpnsy-file");
        const input_text = $(v).find(".adpnsy-file-text");

        input.on('change', function(event) {
          const el = event.target;
          if (el.files && el.files[0]) {
            const fileName = el.files[0].name;
            input_text.val(fileName);
          } else {
            input_text.val(input.data('filename'));
          }
        });
      })
      modal.fadeIn();
    },
    close: () => modal.fadeOut(500, function(ex){ modal.remove()}),
    tablas: (id) => tablas[id]
  }
}

const modalspElement = (type, title, icon, id, value, options = {}, col = 3, attr = {}) => {
  let active = value || value === 0 ? 'active' : '';
  let element = "";
  let cols = "s" + (12/col);
  let atts = isObject(attr) ? Object.keys(attr).map(key => `${key}="${attr[key]}"`).join(" ") : "";
  if (typeof value === 'string' && type != 11) value = value.replace(/"/g, '&quot;');
  
  //Texto
  if(type == 1){ 
    element = `<input ${atts} id="${id}" type="text" value="${value}">`;
  }

  //Select2
  if(type == 2){ 
    active = 'active';
    element = `<select id="${id}" ${atts} class="browser-default adpnsy-select2">`;
    if(options){
      $.each(options, function(k,v){
        if(isObject(v) && 'child' in v){
          element += `<option ${value==k?'selected':''} value="${k}">${v.value}</option>`;
          element += buildOptions(v.child, 1, value);
        }else if(Array.isArray(options)){
          element += `<option ${value==k?'selected':''} value="${k}">${v}</option>`;
        }else{
          if(typeof v === 'string'){
            element += `<option ${value==k?'selected':''} value="${k}">${v}</option>`;
          }else if(Array.isArray(v)){
            if(v.length){
              element += `<optgroup label="${k}">`;
              $.each(v, function(k2,v2){
                element += `<option ${value==v2?'selected':''}>${v2}</option>`;
              })
              element += `</optgroup>`;
            }
          }else{
            if(Object.keys(v).length){
              element += `<optgroup label="${k}">`;
              $.each(v, function(k2,v2){
                element += `<option ${value==k2?'selected':''} value="${k2}">${v2}</option>`;
              })
              element += `</optgroup>`;
            }
          }
        }
      })
    }else{
      element += `<option disabled value="0">Sin opciones</option>`;
    }
    element += "</select>";
    if('button' in attr){
      element += `<button class="btn btn_action posfix">${attr.button.text}</button>`;
    }
  }

  //Select default
  if(type == 3){ 
    active = 'active';
    element = `<select class="browser-default" id="${id}" ${atts}>`;
    if(Array.isArray(options)){
      $.each(options, (k,v)=>{
        element += `<option ${value==v?'selected':''} value="${v}">${v}</option>`;
      })
    }else{
      $.each(options, (k,v)=>{
        element += `<option ${value==k?'selected':''} value="${k}">${v}</option>`;
      })
    }
    element += `</select>`;
  }

  //date
  if(type == 4){ 
  	active = 'active';
    element = `<input type='text' id='${id}' value="${value}"  ${atts} class="adpnsy-datepicker" readonly='true' placeholder='Click para seleccionar fecha'>`;
  }

  //number
  if(type == 5){ 
    element = `<input id="${id}" type="number" value="${value}" ${atts}>`;
  }

  //Imagen dropyfy
  if(type == 6){ 
    return `<div class="col ${cols}" id="${id}_cont"><label class="dropify-label" for="${id}">${title}</label><input type="file" class="adpnsy-dropify" id="${id}" data-default-file="${value}" accept="image/*" /></div>`;
  }

  //hidden
  if(type == 7){ 
    return `<input type='hidden' id='${id}' value="${value}">`;
  }

  //Select2 Multiple
  if(type == 8){ 
    active = 'active';
    if(!Array.isArray(value)) value = [value];
    element = `<select id="${id}" multiple="multiple" class="browser-default adpnsy-select2">`;

    if(options){
      $.each(options, function(k,v){
        if(isObject(v) && 'child' in v){
          element += `<option ${value.includes(k)?'selected':''} value="${k}">${v.value}</option>`;
          element += buildOptions(v.child, 1, value);
        }else if(Array.isArray(options)){
          element += `<option ${value.includes(k)?'selected':''} value="${k}">${v}</option>`;
        }else{
          if(typeof v === 'string'){
            element += `<option ${value.includes(k)?'selected':''} value="${k}">${v}</option>`;
          }else if(Array.isArray(v)){
            if(v.length){
              element += `<optgroup label="${k}">`;
              $.each(v, function(k2,v2){
                element += `<option ${value.includes(v2)?'selected':''}>${v2}</option>`;
              })
              element += `</optgroup>`;
            }
          }else{
            if(Object.keys(v).length){
              element += `<optgroup label="${k}">`;
              $.each(v, function(k2,v2){
                element += `<option ${value.includes(k2)?'selected':''} value="${k2}">${v2}</option>`;
              })
              element += `</optgroup>`;
            }
          }
        }
      })
    }else{
      element += '<option disabled value="0">Sin opciones</option>';
    }
    element += "</select>";

    if('button' in attr){
      element += `<button class="btn btn_action posfix">${attr.button.text}</button>`;
    }
  }

  //file
  if(type == 9){
    active = 'active';
    const partes = value.split('/');
    const nombreArchivo = value && partes.length ? partes[partes.length - 1] : 'Ninguno';
    if(value) element = `<div class="posfix"><a href='${value}' target='_blank' title='Veure ${nombreArchivo}' class="material-symbols-outlined">visibility</a><a href='#' title='Esborrar' onclick="deleteFile('${id}')" class="material-symbols-outlined">delete</a></div>`;
    element += `<input id="${id}_name" class='adpnsy-file-text' type="text" value="${nombreArchivo}" />`;
    element += `<input data-filename='${nombreArchivo}' class='adpnsy-file' id="${id}" type="file" ${atts} />`;
  }

  //textarea
  if(type == 10){ 
    element = `<textarea id="${id}" class="adpnsy-textarea materialize-textarea" ${atts}>${value}</textarea>`;
  }

  //textareaformat
  if(type == 11){
    return `<div class="col ${cols}" id="${id}_cont"><div class='adpnsy-quill' placeholder="${title}" id="${id}">${value}</div><textarea class="adpnsy-quill-html" id="${id}_html"></textarea></div>`;
  }

  //datatablet
  if(type == 12){
    //genaral
    const contenedor = $("<div />", {class: `col ${cols}`, id: `${id}_cont`});
    const table = $("<table />", {id: id, class: 'responsive-table adpnsy_tabla'});

    //Head
    const thead = $("<thead><tr></tr></thead>");
    $.each(options, (k,v) => thead.find('tr').append(`<th>${v}</th>`));
    table.append(thead.prop('outerHTML'));

    //Body
    const tbody = $("<tbody></tbody>");
    $.each(value, (k,v) => {
      const tr = $("<tr></tr>");
      $.each(v, (kv,vv) => tr.append(`<th>${vv}</th>`));
      tbody.append(tr);
    })
    table.append(tbody.prop('outerHTML'));
    
    //enviar
    contenedor.append(table);
    return contenedor;
  }

  //Multiples Imagenes
  if(type == 13){ 
    const elm = $(`<div class="col ${cols}" id="${id}_cont"><label class="dropify-label" for="${id}">${title}</label><div class="adpnsy-dropify-cont"></div><button class="btn btn_action width-100 mt-1"><i class="material-symbols-outlined">add</i></button></div>`);

    $.each(value, (k,v) => elm.find(".adpnsy-dropify-cont").append(`<div class="adpnsy-dropify-multi-img" id="${id}__img_${k}" onclick="deleteGallery('${k}', '${id}__img_${k}')"><img src="${v}" /></div>`));
    if(typeof options === 'function') elm.find(".adpnsy-dropify-multi-img").click(function(e){options(this);})
    elm.find('.btn_action').click(function(e){
      e.preventDefault();
      const newkey = Math.random().toString(36).substring(2, 8);
      const nelem = $(`<input type="file" class="${id}_class" id="${id}_${newkey}" data-default-file="" accept="image/*" />`);
      elm.find(".adpnsy-dropify-cont").append(nelem);
      nelem.dropify(dropify);
    });
    return elm;
  }

  //date
  if(type == 14){ 
    active = 'active';
    element = `<input type='text' id='${id}' value="${value}" class="adpnsy-timepicker" readonly='true' placeholder='Click para seleccionar la hora'>`;
  }
  
  //return
  const new_element = $(`<div class="input-field ${(type == 2 || type == 8) ? (icon ? 'select2_noinp' : '') : ''} ${(type == 9) ? 'adpnsy-files' : ''} col ${cols}" id="${id}_cont">${icon ? '<i class="material-symbols-outlined prefix">'+icon+'</i>' : ''}${element} ${type == 7 ? '' : `<label for="${id}" class="${active}">${title}</label>`}</div>`);
  if('button' in attr){
    new_element.find('.btn_action').click(function(e){
      e.preventDefault();
      attr.button.action();
    })
  }
  return new_element;
}

const toggleHtmlView = (id) => {
  const isHtmlView = $(`#${id}_html`).is(':visible');
  if (isHtmlView) {
    $(`#${id}_html`).hide();
    $(`#${id}`).show();
  } else {
    const html = window.adpnsy.quill[id].getSemanticHTML();
    $(`#${id}_html`).val(html);
    $(`#${id}_html`).show();
    $(`#${id}_html`).height($(`#${id} .ql-editor`).height());    
    $(`#${id}`).hide();
  }
}

const buildOptions = (tree, level = 1, value = "") => {
  let options = '';
  const prefix = '-- '.repeat(level);
  $.each(tree, (k,v) => {
    if(Array.isArray(value)){
      options += `<option ${value.includes(k)?'selected':''} value="${k}">${prefix}${v.value}</option>`;
    }else{
      options += `<option ${value==k?'selected':''} value="${k}">${prefix}${v.value}</option>`;
    }
    options += buildOptions(v.child, level + 1, value);
  });
  return options;
}

const removeKeyFromTree = (tree, keyToDelete) => {
  const newTree = {};
  $.each(tree, (k,v) => {
    if(k != keyToDelete){
      newTree[k] = { ...tree[k] };
      newTree[k].child = removeKeyFromTree(tree[k].child, keyToDelete);
    } 
  })
  return newTree;
}

const dropify = {
  height: 100,
  allowedFileExtensions: 'png jpeg jpg gif',
  maxFileSize: '5M',
  showRemove: false,
  messages: {
    'default': 'Arrastra y suelta un archivo aquí o haz clic aquí',
    'replace': 'Arrastra y suelta o haz clic para reemplazar',
    'remove': 'Eliminar',
    'error': 'Vaya, ocurrió algo mal.'
  },
  error: {
    'fileSize': 'El tamaño del archivo es demasiado grande (máximo {{ value }}).',
    'fileExtension': 'El formato del archivo no está permitido (solo {{ value }}).'
  }
}


const datepicker = {
    autoClose: true,
    format: "dd/mm/yyyy",
    setDefaultDate: true,
    i18n: {
      'months': [
        'Enero',
        'Febrero',
        'Marzo',
        'Abril',
        'Mayo',
        'Junio',
        'Julio',
        'Agosto',
        'Septiembre',
        'Octubre',
        'Noviembre',
        'Diciembre'
      ],
      'monthsShort': [
        'Ene',
        'Feb',
        'Mar',
        'Abr',
        'May',
        'Jun',
        'Jul',
        'Ago',
        'Sep',
        'Oct',
        'Nov',
        'Dic'
      ],
      'weekdays': [
        'Domingo',
        'Lunes',
        'Martes',
        'Miércoles',
        'Jueves',
        'Viernes',
        'Sábado'
      ],
      'weekdaysShort': [
        'Dom', 
        'Lun', 
        'Mar', 
        'Mié', 
        'Jue', 
        'Vie', 
        'Sáb'
      ],
      'weekdaysAbbrev': [
        'D','L','M','M','J','V','S'
      ]
    }
}

const timepicker = {
    twelveHour: false,
    vibrate: true,
    autoClose: true,
    i18n: {
      cancel: 'Cancelar',
      clear: 'Limpiar',
      done: 'Aceptar',
      hours: 'Horas',
      minutes: 'Minutos',
      am: 'AM',
      pm: 'PM'
    },
    format: 'HH:mm'
  }

const SendAjax = (metodo, accion, data = {}, files = []) => {
  var form = $("<form />",{enctype: "multipart/form-data"});
  var formData = new FormData(form[0]); 
  formData.append("action", 'admin_panel');
  formData.append(metodo, accion);
  formData.append('unik', window.unik);
  if(isObject(data)) $.each(data, (k,v) => {formData.append(k, v);});
  if(Array.isArray(files)) $.each(files, (k,v) => { let element = $(`#${v}`)[0]; if(element && element.files && element.files[0]) formData.append(v, element.files[0], element.files[0].name);});
  return new Promise((resolve, reject) => {
    $.ajax({
      type : "POST",
      url : AjaxUrl,
      dataType: 'json',
      cache: false,
      contentType: false,
      processData: false,
      data : formData,
      success: function(respuesta) {
        resolve(respuesta);
      },
      error: function(error) {
        if(isObject(error.responseJSON) && "m" in error.responseJSON){
          reject(error.responseJSON.m);
        }else{
          console.error(error);
          reject("Sistema no disponible. Por favor, inténtelo más tarde.");
        }
      }
    });
  });
}

const SendAjaxCrud = (crud, unik, accion, data = {}, files = []) => {
  var form = $("<form />",{enctype: "multipart/form-data"});
  var formData = new FormData(form[0]); 
  formData.append("action", 'adpn_crud');
  formData.append('crud_adnsy', accion);
  formData.append('crud_unik', unik);
  formData.append('crud', crud);
  if(isObject(data)) $.each(data, (k,v) => {formData.append(k, v);});
  if(Array.isArray(files)) $.each(files, (k,v) => { let element = $(`#${v}`)[0]; if(element && element.files && element.files[0]) formData.append(v, element.files[0], element.files[0].name);});
  return new Promise((resolve, reject) => {
    $.ajax({
      type : "POST",
      url : AjaxUrl,
      dataType: 'json',
      cache: false,
      contentType: false,
      processData: false,
      data : formData,
      success: function(respuesta) {
        resolve(respuesta);
      },
      error: function(error) {
        if(isObject(error.responseJSON) && "m" in error.responseJSON){
          reject(error.responseJSON.m);
        }else{
          console.error(error);
          reject("Sistema no disponible. Por favor, inténtelo más tarde.");
        }
      }
    });
  });
}

const GetAjaxData = (url, metodo, accion, unik, data = {}) => {
  var form = $("<form />",{enctype: "multipart/form-data"});
  var formData = new FormData(form[0]); 
  formData.append("action", 'lflmi');
  formData.append(metodo, accion);
  formData.append('unik',unik);
  if(isObject(data)) $.each(data, (k,v) => {formData.append(k, v);});
  return new Promise((resolve, reject) => {
    $.ajax({
      type : "GET",
      url : url,
      dataType: 'json',
      cache: false,
      contentType: false,
      processData: false,
      data : formData,
      success: function(respuesta) {
        resolve(respuesta);
      },
      error: function(error) {
        if(isObject(error.responseJSON) && "m" in error.responseJSON){
          reject(error.responseJSON.m);
        }else{
          console.error(error);
          reject("Sistema no disponible. Por favor, inténtelo más tarde.");
        }
      }
    });
  });
}

const SendAjaxMedia = (metodo, accion, file, data) => {

  // Crear el contenedor de carga
  const loadingBox = $('<div />', { class: 'adpnsy-loading-box' });

  // Agregar la imagen de carga
  const loadingImg = $('<img />').appendTo(loadingBox);

  // Agregar el texto de "Cargando"
  const loadingText = $('<p />').text('Subiendo...').appendTo(loadingBox);

  // Crear la barra de progreso
  const progressBar = $('<div />', { class: 'adpnsy-progress-bar' }).appendTo(loadingBox);
  const progressFill = $('<div />').appendTo(progressBar);

  let formData = new FormData();
  formData.append('file', file, file.name);
  formData.append('action', 'admin_panel');
  formData.append(metodo, accion);
  formData.append('unik', window.unik);
  if(data) $.each(data, (k,v) => formData.append(k, v));

  // Verificar si es video
  let isVideo = file.type.startsWith("video/");

  // Si es un video, extraer el fotograma
  if (isVideo) {
    let videoElement = document.createElement('video');
    videoElement.src = URL.createObjectURL(file);

    videoElement.onloadeddata = () => {
      let canvas = document.createElement('canvas');
      let ctx = canvas.getContext('2d');
      videoElement.currentTime = 1;  // El fotograma se toma después de 1 segundo.

      videoElement.onseeked = () => {
        canvas.width = videoElement.videoWidth;
        canvas.height = videoElement.videoHeight;
        ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
        
        canvas.toBlob((blob) => {
            formData.append('image_frame', blob, 'frame.jpg');  // Añadir el fotograma como archivo binario.

            // Actualizar la imagen de carga
            let imageURL = URL.createObjectURL(blob);
            loadingImg.attr('src', imageURL);

            // Subir el archivo junto con el fotograma
            uploadFile(formData, progressFill, loadingText, loadingImg, loadingBox);
        }, 'image/jpeg');
      };
    };
  } else {
    // Si no es un video, proceder directamente con la subida
    loadingImg.attr('src', URL.createObjectURL(file));
    uploadFile(formData, progressFill, loadingText, loadingImg, loadingBox);
  }

  return loadingBox;
};

const uploadFile = (formData, progressFill, loadingText, loadingImg, loadingBox) => {
  // Crear una instancia de XMLHttpRequest para poder realizar un seguimiento del progreso
  let xhr = new XMLHttpRequest();
  xhr.open("POST", AjaxUrl, true);

  // Evento para la actualización del progreso
  xhr.upload.onprogress = (event) => {
    if (event.lengthComputable) {
      let percent = (event.loaded / event.total) * 100;
      let opacityLoad = (event.loaded / event.total);
      progressFill.css({ width: `${percent}%` });
      loadingImg.css({ opacity: opacityLoad });
    }
  };

  // Configurar la respuesta para el éxito o el error
  xhr.onload = function () {
    if (xhr.status === 200) {
      // Subida exitosa
      loadingImg.css({ opacity: 1 });
      progressFill.css({ width: '100%' });
      try {
        const response = JSON.parse(xhr.responseText);
        if (response.r) {
          loadingImg.attr('src', response.url);
          loadingText.remove();
          progressFill.parent().remove();
          const actions = $('<div />', { class: 'adpnsy-actions-img' }).appendTo(loadingBox);
          if(formData.has('image_frame')) actions.append(`<span class='ver' data-tipo="2" data-src="${response.url_media}"><i class='material-symbols-outlined' title="Ver">visibility</i>Ver</span>`);
          else actions.append(`<span class='ver' data-tipo="1" data-src="${response.url}"><i class='material-symbols-outlined' title="Ver">visibility</i>Ver</span>`);
          actions.append(`<span class='delete' data-id="${response.id}"><i class='material-symbols-outlined' title="Borrar">delete</i>Borrar</span>`);
          actions.append(`<span class='private' data-id="${response.id}"><i class='material-symbols-outlined' title="Privada">lock</i>Privada</span>`);
          if(!formData.has('image_frame')) actions.append(`<span class='default' data-url-convert="" data-url-original="${response.url}" data-id="${response.id}"><i class='material-symbols-outlined' title="Difuminar">blur_linear</i>Difuminar</span>`);
          loadingBox.addClass('complete');
          loadingBox.data('id', response.id);
          if(!formData.has('image_frame')) loadingBox.addClass('tipo_0');
        } else {
          loadingText.text('Error de carga!');
          loadingBox.addClass('faild');
          alert(response.m);
        }
      } catch (error) {
        loadingText.text('Error de carga!');
        loadingBox.addClass('faild');
      }
    } else {
      // Subida fallida
      loadingImg.css({ opacity: 1 });
      loadingText.text('Error de carga!');
      loadingBox.addClass('faild');
      console.log("Error: " + xhr.statusText);
    }
  };

  // Si ocurre algún error en la solicitud
  xhr.onerror = function () {
    loadingBox.addClass('faild');
    loadingText.text('Error de carga!');
    console.log("Error: " + xhr.statusText);
  };

  // Enviar la solicitud con los datos
  xhr.send(formData);
};
