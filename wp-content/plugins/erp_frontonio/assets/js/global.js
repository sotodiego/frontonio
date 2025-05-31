function formatear_fecha_yyyy_mm_dd_to_dd_mm_yyyy(fecha) {
	const fechaObj = new Date(fecha);
	
	const dia = String(fechaObj.getUTCDate()).padStart(2, '0'); 
	const mes = String(fechaObj.getUTCMonth() + 1).padStart(2, '0'); 
	const anio = fechaObj.getUTCFullYear();
	
	return `${dia}/${mes}/${anio}`;
}

function render_fecha_renovacion(data, type, row) {
    if(data == "0000-00-00") return '---';

    var fechaRenovacion = new Date(data);
    var hoy = new Date();

    var diffTime = fechaRenovacion.getTime() - hoy.getTime();
    var diffDays = diffTime / (1000 * 60 * 60 * 24);

    var claseFecha = "";

    if (row.estado === "dado_de_baja" || row.estado === "inactivo_dado_de_baja") {
         claseFecha = "color_gris";
    } else {
         if (diffDays > 30) {
             claseFecha = "color_verde"; 
         } else if (diffDays <= 30 && diffDays >= 15) {
             claseFecha = "color_amarillo"; 
         } else if (diffDays < 15) {
             claseFecha = "color_rojo"; 
         }
    }
    
    var fechaFormateada = formatear_fecha_yyyy_mm_dd_to_dd_mm_yyyy(data);
    
    return '<div class="color_td ' + claseFecha + '">' + fechaFormateada + '</div>';
}

function render_estado(data, type, row){
	let estados = {
		"inactivo_pendiente_pago": "Pendiente del primer pago",
		"activo_primer_pago_terminal": "Activo Hiberus/Terminal (primer año)",
		"activo_primer_pago": "Activo (primer año)",
		"activo_pendiente_domiciliacion": "Activo (pendiente de domiciliar)",
		"activo_domiciliado": "Activo (domiciliado)",
		"inactivo_dado_de_baja": "Dado de baja"
	}

    return '<div class="color_td color_' + data + '">' + estados[data] + '</div>';
}

function render_estado_perfil(data, type, row){
	let estados = {
		"0": "Ficha sin completar",
		"1": "Ficha completada",
	}

    return '<div class="color_td color_estado_perfil_' + data + '">' + estados[data] + '</div>';
}


function render_precio(data) {
    const numero = parseFloat(data);
    const formateado = numero.toLocaleString('es-ES', { 
        minimumFractionDigits: 2, 
        maximumFractionDigits: 2 
    });
    return formateado + '€';
}

function validar_email(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validar_telefono(telefono) {
    telefono = telefono.trim();
    if (!telefono.startsWith('+')) {
        telefono = '+34' + telefono;
    }
    const regexTelefono = /^\+34\d{9}$/;
    if (regexTelefono.test(telefono)) {
        return telefono;
    } else {
        return false;
    }
}
