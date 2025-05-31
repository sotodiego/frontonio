jQuery(document).ready(function($){
	$("#imagen_registro").dropify(dropify);

	$("#sp_1").click(function(e){
		e.preventDefault();
		let nombre = ($("#user_name").val()).trim();
		let apellido = ($("#user_lastname").val()).trim();
		if(!nombre || !apellido) return alert("Por favor complete todo los campos para continuar");
		$(this).parent(".row.margin").addClass("hide");
		$(this).parent(".row.margin").next().removeClass("hide");
	})

	$("#sp_2, #sp_4").click(function(e){
		e.preventDefault();
		$(this).parents(".row.margin").addClass("hide");
		$(this).parents(".row.margin").prev().removeClass("hide");
	})

	$("#sp_3").click(function(e){
		e.preventDefault();
		$(this).parents(".row.margin").addClass("hide");
		$(this).parents(".row.margin").next().removeClass("hide");
	})

	$("#politicas").click(function(e){
		e.preventDefault();
		$("#politica_modal").fadeIn();
	})

	$(".adp_modal_politicas").click(function(e){
		e.preventDefault();
		$("#aceptar").prop("checked", true);
		$(this).parents(".adpn_modal").fadeOut();
	})

	$(".adpn_modal_close, .adp_modal_cancel").click(function(e){
		e.preventDefault();
		$(this).parents(".adpn_modal").fadeOut();
	});

	$("#sp_5").click(function(e){
		e.preventDefault();
		let p1 = ($("#user_password").val()).trim();
		let p2 = ($("#user_password_2").val()).trim();
		let error = [];
		if(!p1 || !p2) return alert("Por favor complete todo los campos para continuar");
		if(p1 !== p2) return alert("Las contraseñas no son iguales");

		if(!(p1.length >= 8)) error.push("al menos 8 dígitos");
	    if(!(/[0-9]/.test(p1))) error.push("al menos 1 numero");
	    if(!(/[a-z]/.test(p1))) error.push("al menos 1 letra minúscula");
	    if(!(/[A-Z]/.test(p1))) error.push("al menos 1 letra mayúscula");
	    if(!(/[!@#$%^&*(),.?":{}|<>]/.test(p1))) error.push('al menos 1 carácter especial como !@#$%^&*(),.?":{}|<>');
	    if(error.length) return alert("Contraseña no segura debe tener:<br>" + error.join("<br>"));

	    if(!$("#aceptar").is(":checked")) return alert("Por favor acepte nuestras políticas para continuar");

	    loading("Registrando");
	    let data = {
	    	_code: ($("#user_code").val()).trim(),
	    	_nombre: ($("#user_name").val()).trim(),
	    	_apellido: ($("#user_lastname").val()).trim(),
	    	_telefono: ($("#user_telefono").val()).trim(),
	    	_pass: ($("#user_password").val()).trim()
	    }
	    SendAjax('user_access', 0, data, ['imagen_registro']).then(rsp => {
	    	window.location.href = rsp.login;
	    }).catch(error => {
	    	loading_end();
	    	alert(error);
	    })
	})
})