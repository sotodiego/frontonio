jQuery(document).ready(function($){

	if($(".exito-login").length){
		$(".exito-login").click(function(e){
			$(this).fadeOut();
		});
	}

	$(".login-card .showpass").click(function(e) {
		e.preventDefault();
		if($("#password").attr("type") == 'password'){
			$("#password").attr("type", 'text');
			$(this).text('visibility_off');
		}else{
			$("#password").attr("type", 'password');
			$(this).text('visibility');	
		}
	})

	$("#menu_toggle_sp").click(function(e){
		e.preventDefault();
		$("body").toggleClass("sp-toogle");
	})
})