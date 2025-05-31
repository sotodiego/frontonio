window.crudRenderers = {
	EUR,
	FECHA_FORMAT,
	FECHA_FORMAT_HORA
};

let crud_adnsy;
let table = $("#crud_adnsy");
let unik = table.data("unik");
let crud = table.data("crud");
_table_.ajax = {
    url: `${AjaxUrl}${_table_.ajax.url}&crud_unik=${unik}&crud_adnsy=0&crud=${crud}`,
    data: function(d) {
        const urlParams = new URLSearchParams(window.location.search);
        d.filtros = Object.fromEntries(urlParams.entries());
    },
    dataSrc: function(json) {
		$.each(json._t, (k,v) => {
			const e = $(`.crud-totals-${k}`);
			if(v == null) v = '---';
			if(e.data("render")){
				const fnName = e.data("render");
				if (window.crudRenderers && typeof window.crudRenderers[fnName] === "function") {
					e.html(window.crudRenderers[fnName](v));
				} else {
					e.html(v);
				}
			}else{
				e.html(v);
			}
		})
        return json.aaData;
    }
};
_table_.language = datatablet_es;

jQuery(document).ready(function($){ crud_adnsy = table.DataTable(_table_); })

const load_crud = (_base) => {
	let contenido = $("<div />", {class: 'row'});
	$.each(crud_list.columns, (k,v) => {
		contenido.append(modalspElement(v.tipo, `${v.name}${v.required ? '(*)':''}`, v.icon, `_cru_${k}_`, (_base && _base[k] ? _base[k] : (v.default ?? '')), window[`adpnsy_${k}`] ?? null, v.size, {...v.attr_crud}));
	})
	return contenido;
}

const save_crud = (_element, _modal, _create) => {
	let _send = _element ?? {};
	let _file = [];
	let _required = false;
	$.each(crud_list.columns, (k,v) => {
		if(v.tipo == 9 || v.tipo == 6){
			_file.push(`_cru_${k}_`);
			if(v.required == true && !$(`#_cru_${k}_`).val()) _required = true;
		}else{
			_send[k] = $(`#_cru_${k}_`).val();
			if(v.required == true && !(_send[k]).trim()) _required = true;
		}		
	})

	if(_required) return alert("Por favor complete todo los campos con (*)");
	loading(_create ? "Creando" : "Actualizando");
	SendAjaxCrud(crud, unik, 1, {_data: JSON.stringify(_send), _create: _create ? 1 : 0 }, _file).then(rsp => {
        loading_end();
        if(rsp.r){
        	crud_adnsy.ajax.reload();
        	_modal.close();
        	$.each(rsp.list, (k,v) => {
        		window[`adpnsy_${k}`] = v;
        	})
        }else{
            alert(rsp.m);
        }                
    }).catch(error => {
        loading_end();
        alert(error);
    });
}

const img_crud = (_img) => {
	if(_img) return `<img src="${_img}" class="img-crud" />`;
	return `<i class="material-symbols-outlined img-crud">image</i>`;
}

$("#add_element").click(function(e){
	e.preventDefault();
	let modal = modalsp(
		crud_list.modal.create,
		load_crud(false),
		() => save_crud(null, modal, true),
		"Crear",
		crud_list.modal.size
	);
	modal.open();
})

$("#crud_adnsy").on('click', '.edit', function(e){
	e.preventDefault();
	loading("Cargando");
	SendAjaxCrud(crud, unik, 2, {_id: $(this).data('id') }).then(rsp => {
        loading_end();
        if(rsp.r){
	        let modal = modalsp(
				crud_list.modal.edit,
				load_crud(rsp.data),
				() => save_crud(rsp.data, modal, false),
				"Guardar",
				crud_list.modal.size
			);
			modal.open();
        }else{
            alert(rsp.m);
        }                
    }).catch(error => {
        loading_end();
        alert(error);
    });
})

$("#crud_adnsy").on('click', '.delete', function(e){
	alert_confirm(crud_list.modal.delete, () => {
		loading("Borrando");
		SendAjaxCrud(crud, unik, 3, {_id: $(this).data('id') }).then(rsp => {
	        loading_end();
	        if(rsp.r){
	        	crud_adnsy.ajax.reload();
	        }else{
	            alert(rsp.m);
	        }                
	    }).catch(error => {
	        loading_end();
	        alert(error);
	    });
	})
})

$("#crud_adnsy").on('click', '[data-crud_action]', function(e){
	loading("Enviando");
	const action = $(this).data("crud_action");
	let send = {};
	$.each($(this).data(), (k,v) => {
		if(k != 'crud_action') send[`_${k}`] = v;
	})
	SendAjaxCrud(crud, unik, action, send).then(rsp => {
        loading_end();
        if(rsp.r){
        	crud_adnsy.ajax.reload();
        }else{
            alert(rsp.m);
        }                
    }).catch(error => {
        loading_end();
        alert(error);
    });
})

if (typeof crud_sys_filter !== "undefined" && crud_sys_filter) {
	let contenido = $("<div />", {class: 'row row-auto'});
	const urlParams = new URLSearchParams(window.location.search);
	const filtros = Object.fromEntries(urlParams.entries());
	$.each(crud_sys_filter.data, (k,v) => {
		const optionsObj = window[`adpnsy_${k}`] || {};
		const options = {"": "Todos", ...optionsObj};
		if(v.filter_type == 'select')
			contenido.append(modalspElement(3, v.name, null, `_fil_${k}_`, filtros[k] ?? '', options, 1, {name: k}))
		else if(v.filter_type =='select2')
			contenido.append(modalspElement(2, v.name, null, `_fil_${k}_`, filtros[k] ?? '', options, 1, {name: k}))
		else if(v.filter_type =='date')
			contenido.append(modalspElement(4, v.name, null, `_fil_${k}_`, convertirFechaESaEN(filtros[k]) ?? '', null, 1, {name: k}))
		else if(v.filter_type == 'date_range'){
			contenido.append(modalspElement(4, "Desde " + v.name, null, `_fil_${k}_ini_`, convertirFechaESaEN(filtros[`${k}|ini`]) ?? '', null, 1, {name: `${k}|ini`}));
			contenido.append(modalspElement(4, "Hasta " + v.name, null, `_fil_${k}_end_`, convertirFechaESaEN(filtros[`${k}|end`]) ?? '', null, 1, {name: `${k}|end`}));	
		}
	})
	modalsp(
		null,
		contenido,
		() => {
			const params = {};
			// Collect all filter inputs by their name attribute
			contenido.find('input, select').each(function() {
				const name = $(this).attr('name');
				const value = $(this).val();
				if (name && value !== "" && value !== null) {
					params[name] = value;
				}
			});
			// Build query string
			const query = new URLSearchParams(params).toString();
			// Redirect to same URL with filters as GET parameters
			window.location = window.location.pathname + (query ? '?' + query : '');
		},
		crud_sys_filter.btn,
		"nomodal",
		"filters"
	).open();

}