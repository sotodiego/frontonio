
jQuery(document).ready( function($) {

	//Genarales funciones
	$(".select2").select2();

	//variables generales
	let aje_noc = $("#aje_noc").val();
	window.unik = aje_noc;

	if($("#list_facturas_admin").length){
		//General
		const action = "facturas_admin";
		//Listar
		_table_.ajax.url = `${AjaxUrl}${_table_.ajax.url}&${action}=0&unik=${aje_noc}`;
		_table_.language = datatablet_es;
		window.list_facturas_admin = $("#list_facturas_admin").DataTable(_table_);

	}
});



jQuery(".sp_seleccionar_estado").on("click", function(){
    let estado = jQuery(this).data("estado");
    let estado_slug = jQuery(this).data("estado_slug");

    if(estado && estado_slug){
        const newParams = {
            estado: estado_slug,
        };

        const baseUrl = window.location.origin + window.location.pathname;

        const searchParams = new URLSearchParams(newParams);
        const newQueryString = searchParams.toString();

        const newUrl = `${baseUrl}?${newQueryString}`;

        window.history.replaceState(null, '', newUrl);

        filtrar_pedido_estado(estado);
    }
})


jQuery(".sp_limpiar_filtro").on("click", function(){
    const baseUrl = window.location.origin + window.location.pathname;
    window.history.replaceState(null, '', baseUrl);

    filtrar_pedido_estado("");
})

function filtrar_pedido_estado(estado){
    jQuery("#list_facturas_admin_filter").find("input.form-control").val(estado).trigger("input");
};