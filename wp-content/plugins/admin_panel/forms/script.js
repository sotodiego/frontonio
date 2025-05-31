const form = {
    SendAjax:(accion,data={},files=[])=>{var form_file=$("<form />",{enctype:"multipart/form-data"});var formData=new FormData(form_file[0]);formData.append("action",'adpn_form');formData.append('adpn_form',accion);formData.append('form',form_ajax.id);if(form.isObject(data))$.each(data,(k,v)=>{formData.append(k,v);});if(Array.isArray(files))$.each(files,(k,v)=>{let element=$(`#${v}`)[0];if(element&&element.files&&element.files[0])formData.append(v,element.files[0],element.files[0].name);});return new Promise((resolve,reject)=>{$.ajax({type:"POST",url:form_ajax.url,dataType:'json',cache:false,contentType:false,processData:false,data:formData,success:function(respuesta){resolve(respuesta);},error:function(error){if(form.isObject(error.responseJSON)&&"m" in error.responseJSON){reject(error.responseJSON.m);}else{console.error(error);reject("Sistema no disponible. Por favor, inténtelo más tarde.");}}});});},
    alert:(text, ico = false)=>{if(!ico){var img = '<i class="aspnfor_alert_img material-symbols-outlined">warning</i>';}else{var img = '<i class="aspnfor_alert_img material-symbols-outlined">check_circle</i>';}var el = $("<div />",{class: 'aspnfor_alert'});el.append($("<div />",{ class: 'aspnfor_alert_cont' }).append( $("<a />",{href:"#", class: 'aspnfor_alert_close', text: "x"}).on("click", function(e) { e.preventDefault(); el.removeClass("show"); setTimeout(function(){ el.remove() }, 600); }) ).append( img  ).append( $("<p />",{class: 'aspnfor_alert_text', html: text}) ));$("body").append(el);setTimeout(function(){ el.addClass("show"); }, 100);},
    alert_confirm:(text, fun)=>{var img = '<i class="aspnfor_alert_img material-symbols-outlined">warning</i>';var el = $("<div />",{class: 'aspnfor_alert'});el.append($("<div />",{ class: 'aspnfor_alert_cont' }).append($("<a />",{href:"#", class: 'aspnfor_alert_close', text: "x"}).on("click", function(e) { e.preventDefault(); el.removeClass("show"); setTimeout(function(){ el.remove() }, 600); })).append( img ).append( $("<p />",{class: 'aspnfor_alert_text', html: text}) ).append( $("<div />",{class: 'aspnfor_alert_btns'}).append($("<a />",{href:"#", class: 'aspnfor_alert_btn aspnfor_alert_accept', text: "Si"}).on("click", function(e) { fun(); e.preventDefault(); el.removeClass("show"); setTimeout(function(){ el.remove() }, 600); })).append($("<a />",{href:"#", class: 'aspnfor_alert_btn aspnfor_alert_cancel', text: "No"}).on("click", function(e) { fun; e.preventDefault(); el.removeClass("show"); setTimeout(function(){ el.remove() }, 600); })) ));$("body").append(el);setTimeout(function(){ el.addClass("show"); }, 100);},
    alert_options:(text, funs)=>{let el = $("<div />",{class: 'aspnfor_alert'}).append($("<div />",{ class: 'aspnfor_alert_cont' }).append($("<a />",{href:"#", class: 'aspnfor_alert_close', text: "x"}).on("click", function(e) { e.preventDefault(); el.removeClass("show"); setTimeout(function(){ el.remove() }, 600); })).append( $("<p />",{class: 'aspnfor_alert_text p-0', html: text}) ).append( $("<div />",{class: 'aspnfor_alert_btns ml-0'}) ));let bts = el.find('.aspnfor_alert_btns');$.each(funs, (nam, fun) => {bts.append($("<a />",{href:"#", class: 'aspnfor_alert_btn aspnfor_alert_multiple', text: nam}).on("click", function(e) { fun(); e.preventDefault(); el.removeClass("show"); setTimeout(function(){ el.remove() }, 600); }))});$("body").append(el);setTimeout(function(){ el.addClass("show"); }, 100);return;},
    loading:(text)=>{var el = $("#aspnfor_loading");if(!el.length){el = $('<div class="aspnfor_modal" id="aspnfor_loading"><div class="aspnfor_modal_load"><p class="aspnfor_modal_load_title"><span id="load-msg">Cargando</span><span>.</span><span>.</span><span>.</span></p></div></div>')};el.find("#load-msg").text(text);$("body").append(el);el.fadeIn();},
    loading_end:(time = 0)=>{var el = $("#aspnfor_loading");if(el.length > 0){setTimeout(function(){el.fadeOut();setTimeout(function() { el.remove();}, 500);}, time);}},
    isEmail: (email) => { var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/; return regex.test(email); },
    isObject: (obj) => (typeof obj === 'object' && obj !== null),
    lel: (_e, _c)  => $(`#_${_e}_${_c}_`).val(),
    generarCalendarioProximosDias: (dias = 7, diasSemana = [1, 1, 1, 1, 1, 1, 1], fechasNoPermitidas = [], next = false) => { const nDay = next ? 2 : 1; const calendario = {}, hoy = new Date(); hoy.setDate(hoy.getDate() + nDay); let conteoDiasValidos = 0; for (let i = 0; i < dias; i++) { const fecha = new Date(hoy); fecha.setDate(hoy.getDate() + i); const mes = fecha.getMonth() + 1, dia = fecha.getDate(), diaSemana = fecha.getDay(), anio = fecha.getFullYear(); const esFechaNoPermitida = fechasNoPermitidas.some(f => f.dia == dia && f.mes == mes); if (diasSemana[diaSemana] == 1 && !esFechaNoPermitida) { calendario[anio] = calendario[anio] || {}; calendario[anio][mes] = calendario[anio][mes] || []; calendario[anio][mes].push(dia); conteoDiasValidos++; } else conteoDiasValidos = 0; if (conteoDiasValidos === dias) break; } return calendario; },
    generarCalendarioHTML: (calendarioDiasActivos) => {
        const _days = ["L", "M", "X", "J", "V", "S", "D"];
        const hoy = new Date();
        let mesInicial = hoy.getMonth() + 1;
        let anioInicial = hoy.getFullYear();
        const calendario = $('<div />', {class: 'adpnsy-calendar'});

        while (!(calendarioDiasActivos[anioInicial] && calendarioDiasActivos[anioInicial][mesInicial])) {
            mesInicial++;
            if (mesInicial > 12) {
            mesInicial = 1;
            anioInicial++;
            }
        }

        for (let anio in calendarioDiasActivos) {
            for (let mes in calendarioDiasActivos[anio]) {
            if (anio < anioInicial || (anio == anioInicial && mes < mesInicial)) continue;

            const tabla = $('<table />').append(`<caption>${mes}/${anio}</caption>`);
            tabla.append(`<tr>${["L","M","X","J","V","S","D"].map(d => `<th>${d}</th>`).join('')}</tr>`);

            // Calcular el primer día del mes y el último día del mes
            let primerDia = (new Date(anio, mes - 1, 1).getDay() + 6) % 7; // Ajustar para que inicie en lunes
            let ultimoDiaMes = new Date(anio, mes, 0).getDate();
            let diaIndice = 1 - primerDia;

            // Generar las filas del calendario
            for (let filaIndex = 0; diaIndice <= ultimoDiaMes; filaIndex++) {
                const fila = $('<tr />');

                for (let diaSemana = 0; diaSemana < 7; diaSemana++, diaIndice++) {
                const esDiaValido = diaIndice > 0 && diaIndice <= ultimoDiaMes;
                const claseActivo = esDiaValido && calendarioDiasActivos[anio][mes].includes(diaIndice) ? 'activo' : '';
                const fechaData = new Date(anio, mes - 1, diaIndice);
                const celda = esDiaValido
                    ? $('<td />', { class: claseActivo }).text(diaIndice).data('date', fechaData)
                    : $('<td />');

                fila.append(celda);
                }

                tabla.append(fila);
            }

            calendario.append(tabla);
            }
        }

        return calendario;
    },
    data: {},
    formContainer: null,
    stepsContainer: null,
    stepsData: {},
    currentSel: null,
    init:()=>{
        if(!form_ajax?.data) return;
        form.formContainer = $(`#adpnsy_form_${form_ajax.id}`);
        if(!form.formContainer.length) return;
        form.content('ini');
    },
    complete_data:()=>{
        const data = form_ajax.data[form.currentSel];
        let _newData = {};
        let _required = [];
        switch(data.type) {
            case 'form':
                data.elements?.forEach(field => {
                    _newData[field.name] = form.lel("apf", field.name);
                    if(field.required && !_newData[field.name]){
                        _required.push(field.label ?? field.placeholder);
                    }
                });
                break;
            default:
                return false;
        }
        if(_required.length){
            form.alert("<p>Los campos:</p>" + _required.join("<br>") + "<p>Son obligatorios</p>");
            return false;    
        }
        form.data[data.id] = _newData;
        return _newData;
    },
    content:(sel)=>{
        const data = form_ajax.data[sel];
        form.currentSel = sel;
        let html = $(`<div id="form_${data.id}" class="adpnsy-form-container"></div>`);
        if("step" in data){
            let steps = form_ajax.data[sel].step ?? null;
            if(!form.isObject(steps)) steps = form_ajax.data[steps].step ?? null;
            if(steps){
                if(!form.stepsContainer) {
                    form.stepsContainer = $("<div/>", {
                        class: "adpnsy-form-steps"
                    }).append($("<div/>", {
                        class: "adpnsy-form-steps-loc"
                    }));
                    form.formContainer.prepend(form.stepsContainer);
                }
                // Clean up stale step data
                Object.keys(form.stepsData).forEach(key => {
                    if(!(key in steps)) {
                        form.stepsData[key].remove();
                        delete form.stepsData[key];
                    }
                });

                const totalSteps = Object.keys(steps).length;

                if(totalSteps === 1) {
                    Object.entries(steps).forEach(([key, step]) => {
                        if(!(key in form.stepsData)){
                            form.stepsData[key] = $("<div/>", {
                                class: `adpnsy-form-step`,
                                title: step.title
                            }).append($("<i/>", {
                                class: "material-symbols-outlined",
                                text: step.ico
                            })).append($("<span/>", {
                                text: step.title
                            }));
                            form.stepsContainer.append(form.stepsData[key]);
                        }
                        const stepElement = form.stepsData[key];
                        stepElement.removeClass('done');
                        stepElement.addClass('current');
                        stepElement.css('left', `50%`); 
                    });
                    form.stepsContainer.find(".adpnsy-form-steps-loc").css('width', `100%`);
                }else{
                    const stepWidth = 100 / (totalSteps - 1);
                    let currentStep = 0;

                    Object.entries(steps).forEach(([key, step]) => {
                        const isCurrentStep = key === sel;
                        const stepIndex = Object.keys(steps).indexOf(key);
                        
                        if(isCurrentStep) {
                            currentStep = stepIndex;
                        }

                        if(!(key in form.stepsData)){
                            form.stepsData[key] = $("<div/>", {
                                class: `adpnsy-form-step`,
                                title: step.title
                            }).append($("<i/>", {
                                class: "material-symbols-outlined",
                                text: step.ico
                            })).append($("<span/>", {
                                text: step.title
                            }));
                            form.stepsContainer.append(form.stepsData[key]);
                        }
                        const stepElement = form.stepsData[key];
                        stepElement.removeClass('current done');
                        if(isCurrentStep) stepElement.addClass('current');
                        if(currentStep == 0 &&!isCurrentStep) stepElement.addClass('done');
                        stepElement.css('left', `${stepIndex * stepWidth}%`);                        
                        stepElement.on('click', () => {
                            const currentIndex = Object.keys(form.stepsData).indexOf(form.currentSel);
                            const clickedIndex = Object.keys(form.stepsData).indexOf(key);
                            if(key != form.currentSel && clickedIndex <= currentIndex) {
                                form.content(key);
                            }
                        });
                    });
                    form.stepsContainer.find(".adpnsy-form-steps-loc").css('width', `${(currentStep) * stepWidth}%`);
                }
            }
            form.stepsContainer.show();
        }else if(form.stepsContainer){
            form.stepsContainer.hide();
        }

        switch(data.type) {
            case 'btns':
                const btns = $(`<div class="adpnsy-form-btns">`); 
                data.elements?.forEach(field => {
                    const el = $(`<div class="adpnsy-form-btn"></div>`);
                    if('ico' in field && field.ico){
                        el.append(`<i class="material-symbols-outlined">${field.ico}</i>`);
                    }else if('img' in field && field.img){
                        el.append(`<img src="${form_ajax.base}${field.img}" alt="${field.label}">`);
                    }
                    el.append(`<span>${field.label}</span>`);
                    if(field.actionType == 'js'){
                        el.on('click', () => {
                            form.data[data.id] = field.actionData;
                            if(field.action){
                                window[field.action](form);
                            }else{
                                form.alert("No se ha definido una acción correcta para este botón.");
                            }
                        });
                    }else{
                        el.on('click', () => {
                            form.data[data.id] = field.actionData;
                            if(field.action && field.action in form_ajax.data){
                                form.content(field.action);
                            }else{
                                form.alert("No se ha definido una acción correcta para este botón.");
                            }
                            
                        })
                    }
                    btns.append(el);
                });
                html.append(btns);
                break;
            case 'checkboxs':
                const values = form.data[data.id] ?? {};
                const checkboxs = $(`<div class="adpnsy-form-checkboxs">`);
                if(!(data.id in form.data)) form.data[data.id] = {};
                data.elements?.forEach(field => {
                    const _el = $(`<label class="adpnsy-form-checkbox"></label>`);
                    form.data[data.id][field.name] = values && values[field.name] == true;
                    _el.append($("<input/>", {
                        type: 'checkbox',
                        id: `_apf_${field.name}_`,
                        name: field.name,
                        checked: values && values[field.name] == true
                    }));
                    const el = $(`<span class="adpnsy-form-checkbox-content"></span>`);
                    if('ico' in field && field.ico){
                        el.append(`<i class="material-symbols-outlined">${field.ico}</i>`);
                    }else if('img' in field && field.img){
                        el.append(`<img src="${form_ajax.base}${field.img}" alt="${field.label}">`);
                    }
                    _el.find('input').on('change', function(e){
                        form.data[data.id][field.name] = $(this).is(':checked');
                    });
                    el.append(`<span>${field.label}</span>`);
                    _el.append(el);
                    checkboxs.append(_el);
                });
                html.append(checkboxs);
                if('send' in data){
                    const sendBox = $(`<button class="adpnsy-form-checkbox-send">${data.send.text ?? 'Enviar'}</button>`);
                    sendBox.on('click', () => {
                        if('action' in data.send && data.send.action == 'js'){
                            if(data.send.actionData in window){
                                window[data.send.actionData](form);
                            }else{
                                form.alert("No se ha definido una acción correcta para este botón.");
                            }
                        }else if('actionData' in data.send){
                            form.content(data.send.actionData);
                        }else{
                            form.alert("No se ha definido una acción correcta para este botón.");
                        }
                    });
                    html.append(sendBox);
                }
                break;
            case 'multicheckboxs':
                if(!(data.id in form.data)) form.data[data.id] = {};
                const Pcheckboxs = $(`<div class="adpnsy-form-multicheckboxs">`);
                data.elements?.forEach(fields => {
                    const checkboxs = $(`<div class="adpnsy-form-checkboxs">`);
                    checkboxs.append(`<h4>${fields.title}</h4>`);
                    if(!(fields.id in form.data[data.id])) form.data[data.id][fields.id] = {}
                    const values = form.data[data.id][fields.id] ?? {};
                    fields.elements?.forEach(field => {
                        const _el = $(`<label class="adpnsy-form-checkbox"></label>`);
                        form.data[data.id][fields.id][field.name] = values && values[field.name] == true;
                        _el.append($("<input/>", {
                            type: 'checkbox',
                            id: `_apf_${field.name}_`,
                            name: field.name,
                            checked: values && values[field.name] == true
                        }));
                        const el = $(`<span class="adpnsy-form-checkbox-content"></span>`);
                        if('ico' in field && field.ico){
                            el.append(`<i class="material-symbols-outlined">${field.ico}</i>`);
                        }else if('img' in field && field.img){
                            el.append(`<img src="${form_ajax.base}${field.img}" alt="${field.label}">`);
                        }
                        _el.find('input').on('change', function(e){
                            form.data[data.id][fields.id][field.name] = $(this).is(':checked');
                        });
                        el.append(`<span>${field.label}</span>`);
                        _el.append(el);
                        checkboxs.append(_el);
                    }) 
                    Pcheckboxs.append(checkboxs);
                });
                html.append(Pcheckboxs);
                if('send' in data){
                    const sendBox = $(`<button class="adpnsy-form-checkbox-send">${data.send.text ?? 'Enviar'}</button>`);
                    sendBox.on('click', () => {
                        if('action' in data.send && data.send.action == 'js'){
                            if(data.send.actionData in window){
                                window[data.send.actionData](form);
                            }else{
                                form.alert("No se ha definido una acción correcta para este botón.");
                            }
                        }else if('actionData' in data.send){
                            form.content(data.send.actionData);
                        }else{
                            form.alert("No se ha definido una acción correcta para este botón.");
                        }
                    });
                    html.append(sendBox);
                }
                break;
            
            case 'form':
                const Nform = $(`<form id="form_${data.id}" class="adpnsy-form-standar">`);
                data.elements?.forEach(field => {
                    Nform.append(form.renderField(field, form.data[data.id]));
                });
                html.append(Nform);
                break;
            case 'calendar':
                const days = form.generarCalendarioProximosDias(data.calendar.maximo?? 7, data.calendar.semana?? [1, 1, 1, 1, 1, 1, 1], data.calendar.diasCerrados?? [], true);
                const calendario_box = $(`<div class="adpnsy-calendario-cont">
					<div class="adpnsy-calendario-rotar">
						<span class="menos">
							<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#fffff">
								<path d="M560-240 320-480l240-240 56 56-184 184 184 184-56 56Z"/>
							</svg>
						</span>
						<span class="mas">
							<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#fffff">
								<path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
							</svg>
						</span>
					</div>
				</div>`);
                const calendario = form.generarCalendarioHTML(days);
                
                calendario.find('td.activo').on('click', function() {
                    if('action' in data.send && data.send.action == 'js'){
                        if(data.send.actionData in window){
                            const selectedDate = $(this).data('date');
                            const formattedDate = selectedDate.getFullYear() + '-' + 
                                String(selectedDate.getMonth() + 1).padStart(2, '0') + '-' + 
                                String(selectedDate.getDate()).padStart(2, '0');
                            form.data[data.id] = formattedDate;
                            window[data.send.actionData](form);
                        }else{
                            form.alert("No se ha definido una acción correcta para este botón.");
                        }
                    }else if('actionData' in data.send){
                        form.content(data.send.actionData);
                    }else{
                        form.alert("No se ha definido una acción correcta para este botón.");
                    }
                })

                calendario_box.find(".menos").on("click", function(e){
                    e.preventDefault();
                    const tablaVisible = $('.adpnsy-calendar').find('table:visible');
                    const tablaPrev = tablaVisible.prev();
                    if(tablaPrev.length){
                        tablaPrev.show();
                        tablaVisible.hide();
                        return;
                    }
                    form.alert("No hay meses anteriores disponibles");
                })
            
                calendario_box.find(".mas").on("click", function(e){
                    e.preventDefault();
                    const tablaVisible = $('.adpnsy-calendar').find('table:visible');
                    const tablaNext = tablaVisible.next();
                    if(tablaNext.length){
                        tablaNext.show();
                        tablaVisible.hide();
                        return;
                    }
                    form.alert("No hay meses siguientes disponibles");
                })
                calendario_box.append(calendario);
                html.append(calendario_box);
                break;
            case 'free':
                html.append(window[data.funcion](form));
                break;
            default:
                html.append('<p>Tipo de formulario no válido.</p>');
                break;
        }

        if(form.formContainer.find(".adpnsy-form-container").length){
            form.formContainer.children('h3').text(data.title ?? '');
            form.formContainer.find(".adpnsy-form-container").replaceWith(html);
        }else{
            form.formContainer.find(".adpnsy-form-load").remove();
            form.formContainer.append(`<h3>${data.title ?? ''}</h3>`);
            form.formContainer.append(html);
        }
    },
    renderField:(field, values)=>{
        const formGroup = $("<div/>", {
            class: `form-group col${field.col ?? '1'}`
        });

        if(field.label) {
            formGroup.append($("<label/>", {
                for: field.id,
                text: field.label + (field.required? '*' : ''),
            }));
        }
        
        switch(field.type) {
            case 'text':
            case 'email':
            case 'password':
            case 'number':
            case 'date':
            case 'time':
                formGroup.append($("<input/>", {
                    type: field.type,
                    id: `_apf_${field.name}_`,
                    name: field.name,
                    required: field.required,
                    placeholder: field.placeholder ? field.placeholder + (!field.label && field.required? '*' : '') : '',
                    min: field.min,
                    max: field.max,
                    value : values? values[field.name] : ''
                }));
                break;
            case 'btn':
                const element = $("<button/>", {
                    type: 'button',
                    id: `_apf_${field.name}_`,
                    name: field.name,
                    class: 'adpnsy-form-btn-field',
                    disabled: field.disabled,
                    text: field.text
                });
                element.on('click', () => {
                    if(field.action && field.action in window){
                        window[field.action](form);  
                    }else{
                        form.alert("No se ha definido una acción correcta para este botón.");
                    }
                })
                formGroup.append(element);
                break;
            case 'textarea':
                formGroup.append($("<textarea/>", {
                    id: `_apf_${field.name}_`,
                    name: field.name,
                    required: field.required,
                    placeholder: field.placeholder ? field.placeholder + (!field.label && field.required? '*' : '') : '',
                    text: values ? values[field.name] : ''
                }));
                break;
            case 'select':
                const select = $("<select/>", {
                    id: `_apf_${field.name}_`,
                    name: field.name,
                    required: field.required,
                    value: values ? values[field.name] : ''
                }).append($("<option/>", {
                    value: "",
                    text: (field.placeholder ?? "Seleccione") + (field.required? '*' : ''),
                }));
                
                field.options?.forEach(opt => {
                    select.append($("<option/>", {
                        value: opt.value,
                        text: opt.label,
                        selected: values && values[field.name] === opt.value
                    }));
                });
                
                formGroup.append(select);
                break;
            case 'file':
                const fileInput = $("<input/>", {
                    type: 'file',
                    id: `_apf_${field.name}_`,
                    name: field.name,
                    required: field.required,
                    accept: field.accept
                });
                                
                formGroup.append(fileInput);
                break;
            default:
                formGroup.append($("<span/>", {
                    text: 'Tipo de campo no válido.'
                }));
        }
        
        if(field.help) {
            formGroup.append($("<small/>", {
                class: 'help-text',
                text: field.help
            }));
        }
        
        return formGroup;
    }
};
let $ = jQuery;
$(document).ready(form.init);
