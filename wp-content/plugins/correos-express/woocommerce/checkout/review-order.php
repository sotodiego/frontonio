<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php 
$cexFront=new CorreosExpress();
$cexFront->CEX_styles_front(); ?>
<?php $cexFront->CEX_scripts_contador();?>

<table class="shop_table woocommerce-checkout-review-order-table">
	<thead>
		<tr>
			<th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-total"><?php _e( 'Total', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			do_action( 'woocommerce_review_order_before_cart_contents' );

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					?>
					<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
						<td class="product-name">
							<?php echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;'; ?>
							<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf( '&times; %s', $cart_item['quantity'] ) . '</strong>', $cart_item, $cart_item_key ); ?>
							<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
						</td>
						<td class="product-total">
							<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
						</td>
					</tr>
					<?php
				}
			}

			do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</tbody>
	<tfoot>

		<tr class="cart-subtotal">
			<th><?php _e( 'Subtotal', 'woocommerce' ); ?></th>
			<td><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo esc_html( $fee->name ); ?></th>
				<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
					<tr class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
						<th><?php echo esc_html( $tax->label ); ?></th>
						<td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
					<td><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<tr class="order-total">
			<th><?php _e( 'Total', 'woocommerce' ); ?></th>
			<td><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</tfoot>
</table>

<script type="text/javascript">

	var comprobanteEntregaOficina = false;
	//coger de base de datos los transportistas que tienen entrega en oficina

	function recuperarentregaoficina(){
		(jQuery).ajax({
			type: "POST",
			url: '<?php echo admin_url('admin-ajax.php');?>',
			data:
			{
				'action'					:'cex_sacar_transportistas_oficina',
				'nonce'						:'<?php echo wp_create_nonce( 'cex-nonce-user' ); ?>',

			},
			success: function(msg){
				var transportistas = JSON.parse(msg);				
				mostrar(transportistas);	
			},
			error: function(msg){
			}
		});
	}
	
	document.addEventListener('mouseover', function (e) {

		var textoNoSelec = '<?php esc_html_e('* No has seleccionado ninguna oficina', 'cex_pluggin');?>';
		if ((jQuery)('#nombre_oficina').val()=='' && !(jQuery)('#oficina').hasClass('d-none')) {			
			(jQuery)('#place_order').prop('disabled', true);
			(jQuery)('#place_order').attr('title', '<?php esc_html_e('Necesitas seleccionar una oficina para completar el pedido', 'cex_pluggin');?>');
			(jQuery)('#place_order').attr('data-toggle', 'tooltip');
			if((jQuery)('#CEX.avisoOficina').length == 0)
				(jQuery)("#place_order").after("<div id='CEX' class='avisoOficina'><span id='spanOficina'>"+textoNoSelec +"</span></div>");    

		} else {
			(jQuery)('#place_order').prop('disabled', false);
			(jQuery)('#place_order').removeAttr('title');
			(jQuery)('#place_order').removeAttr('data-toggle');
			(jQuery)('#CEX.avisoOficina').remove();
		}
	}, false);

	function mostrar(transportistas){
		comprobanteEntregaOficina = false;
		var comprobante = (jQuery)("input[name='shipping_method[0]']:checked").length;
		var transportistaSelecionado = '';		
		if (comprobante == 1) {
			transportistaSelecionado = (jQuery)("input[name='shipping_method[0]']:checked").val();
			//recorrer y comprobar
			//if(transportistaSelecionado.lenght>0){
			for (var i = 0; i < transportistas.length; i++) {	
				if (transportistaSelecionado.localeCompare(transportistas[i].id_bc) == 0){
					comprobanteEntregaOficina = true;
					(jQuery)('#oficina').removeClass("d-none");
					break;
				}
			}
			//}
			//Si bandera, mostramos			
			if (comprobanteEntregaOficina ) {
				if((jQuery)('#nombre_oficina').val() ===""){
					(jQuery)('#place_order').prop('disabled', true);
				}else
				{
					(jQuery)('#place_order').prop('disabled', false);
			
				}
			}else{
				(jQuery)('#oficina').addClass("d-none");

			}
		}
	}

	recuperarentregaoficina(); 

	(jQuery)( document ).ready(function() {
		if((jQuery)('#buscador_ofi').length == 0){
			//(jQuery)('.woocommerce').not('.widget').prepend(ContenedorOficinas);
			var height=(jQuery)('woocommerce').height();
			(jQuery)('.woocommerce').not('.widget').prepend(ContenedorOficinas);
			(jQuery)('#buscador_ofi').height(height);
		}
		if ((jQuery)("#shipping_method").length!=0) {
				(jQuery)('#shipping_method').append(buscador_oficinas);
		} else {
			(jQuery)("#shipping_method_0").parent().append(buscador_oficinas);
		}

		var options2 = {
			'maxCharacterSize': 69,
			'originalStyle': 'originalTextareaInfo',
			'warningStyle' : 'warningTextareaInfo',
			'warningNumber': 10,
			'displayFormat': '#input caracteres | #left caracteres restantes | #words palabras'
		};
		if((jQuery)('.charleft.originalTextareaInfo').length==0){			
			(jQuery)('#order_comments').textareaCount(options2);
		}else{			
			var cont=1;
			(jQuery)('.charleft.originalTextareaInfo').each(function(element){
				if(cont>1)
					element.remove();				
				cont++;
			});
		}
	});

	var buscador_oficinas = "<li id='CEX'><div id='oficina' class='d-none form-group mt-5'>"+
			"<label class='d-block mt-2'><strong><?php _e('Oficina para la entrega', 'cex_pluggin')?></strong></label>"+
			"<button name='buscador_oficina' id='buscador_oficina' class='CEX-btn CEX-button-yellow' onclick='mostrarBuscador(event);'><?php _e('Buscar oficina', 'cex_pluggin')?></button>"+
			"<label class='d-block mt-2'><strong><?php _e('Oficina seleccionada', 'cex_pluggin')?></strong></label>"+
			"<input class='form-control rounded' type='text' id='nombre_oficina' disabled>"+
		"</div></li>";

	var ContenedorOficinas = "<div id='CEX' class='none'><div id='CEX-loading' class='cexmodal d-none'></div>"+
							"<div id='buscador_ofiCabecera' class='d-none mb-2'><h3 class='my-auto CEX-text-blue w-75 d-inline-block'><strong><?php esc_html_e('Buscador de Oficinas', 'cex_pluggin');?></strong></h3><img src='<?php echo esc_url(plugins_url().'/correos-express/views/img/logo-correosexpress-nuevo.png');?>' class='img-fluid w-25 d-inline-block'></div>"+
							"<div id='buscador_ofi' class='CEX-card-front CEX-text-white p-0 d-none CEX-mi-border'>"+
							"<div class='cexmodal-content my-auto mx-auto w-100'>"+
							"<span class='cexclose d-inline-block' onclick='cerrarModal();'>&times;</span>"+							
							"<div class='row d-flex mb-3'><div class='col-6 col-md-6 col-lg-6 form-group d-inline-block w-50 pr-3'>"+
							"<label for='codigo_postal_ofi' class='d-block'><?php esc_html_e('C&oacute;digo Postal', 'cex_pluggin');?></label>"+
                    		"<input type='number' class='form-control d-block rounded w-75' name='codigo_postal_ofi' id='codigo_postal_ofi'></div>"+           
							"<div class='col-6 col-md-6 col-lg-6 form-group d-inline-block w-50'>"+
							"<label for='poblacion_ofi' class='d-block'><?php esc_html_e('Poblaci&oacute;n', 'cex_pluggin');?></label>"+
							"<input type='text' class='form-control d-block rounded w-75' name='poblacion_ofi' id='poblacion_ofi'>"+						
							"</div></div>"+
							"<div class='row'>"+
							"<div class='col-12 col-md-12 col-lg-12'>"+
							"<button class='CEX-btn CEX-button-yellow' name='buscar_oficina' value='' onclick='buscarOficina(event);'>"+
							"<?php esc_html_e('Buscar oficinas', 'cex_pluggin');?>"+
							"</button></div></div>"+							
							"<div id='tab_oficinas' class='d-none mt-3'>"+							
							"<div class='table-responsive CEX-overflow-y-hidden CEX-text-blue'>"+							
							"<table id='tabla_oficinas'>"+
							"</table>"+							
							"</div>"+
							"</div>"+
						"</div>"+
					"</div></div>";
	

	function buscarOficina(event){
		event.preventDefault();
		var getUrl = window.location;
		(jQuery)('#CEX-loading').removeClass("d-none");
		(jQuery).ajax({
			type: "POST",
			url: '<?php echo admin_url('admin-ajax.php');?>',
			data:
			{
				'action'					:'procesar_curl_oficina_rest',
				'cod_postal'				:(jQuery)('#codigo_postal_ofi').val(),
				'poblacion'					:(jQuery)('#poblacion_ofi').val(),
				'nonce'						:'<?php echo wp_create_nonce( 'cex-nonce-user' ); ?>',

			},
			success: function(msg){
				pintarOficinasModal(msg);
				(jQuery)('#tab_oficinas').removeClass("d-none");
				(jQuery)('#CEX-loading').addClass("d-none");
			},
			error: function(msg){
				(jQuery)('#CEX-loading').addClass("d-none");
			}
		});
	}

	function pintarOficinasModal(msg){
		var oficinas = JSON.parse(msg);
		var oficinas = JSON.parse(msg);
		var tabla = '';
		tabla += '<thead><tr>';
		tabla += '<th><?php esc_html_e("Cod Oficina", "cex_pluggin");?></th>';
		tabla += '<th><?php esc_html_e("CP", "cex_pluggin");?></th>';
		tabla += '<th><?php esc_html_e("Direcci&oacute;n", "cex_pluggin");?></th>';
		tabla += '<th><?php esc_html_e("Nombre", "cex_pluggin");?></th>';
		tabla += '<th><?php esc_html_e("Poblaci&oacute;n", "cex_pluggin");?></th>';
		tabla += '<th></th>';
		tabla += '</tr></thead>';
		tabla += '<tbody>';   
		for (i = 0; i<oficinas.length; i++) {
			var concatenado = "'" + oficinas[i].codigoOficina + "#!#" +
            oficinas[i].direccionOficina + "#!#" +
            oficinas[i].nombreOficina + "#!#" +
            oficinas[i].codigoPostalOficina + "#!#" +
            oficinas[i].poblacionOficina + "'";
			tabla += '<tr>';
			tabla += '<td>' + oficinas[i].codigoOficina + '</td>';
			tabla += '<td>' + oficinas[i].codigoPostalOficina + '</td>';
			tabla += '<td>' + oficinas[i].direccionOficina + '</td>';
			tabla += '<td>' + oficinas[i].nombreOficina + '</td>';
			tabla += '<td>' + oficinas[i].poblacionOficina + '</td>';
			tabla += '<td><button type="button" class="CEX-btn CEX-button-success" onclick="setCodigoOficina(' + concatenado +
				',event);"><?php esc_html_e("Seleccionar", "cex_pluggin");?></button>';
			tabla += '</tr>'
		}
		tabla += '</tbody>';			
		(jQuery)('#tabla_oficinas').html(tabla);
		(jQuery)('#tab_oficinas').removeClass("d-none");		
	}

	function setCodigoOficina(concatenado, event){
		event.preventDefault();
		var split= concatenado.split("#!#");
		var nombre_oficina= split[1]+' '+split[2];
		(jQuery)('#codigo_oficina').val(concatenado);
		(jQuery)('#nombre_oficina').val(nombre_oficina);
		cerrarModal();
	}
	function mostrarBuscador(event){
		event.preventDefault();
		//document.getElementsByName('checkout')[0].style.display='none';
		(jQuery)('.woocommerce-billing-fields').hide();
		(jQuery)('.woocommerce-shipping-fields').hide();
		(jQuery)('.woocommerce-additional-fields__field-wrapper').hide();
		(jQuery)('#order_review').hide();
		(jQuery)('#order_review_heading').hide();
		(jQuery)('#buscador_ofi').removeClass('d-none');
		(jQuery)('#buscador_ofiCabecera').removeClass('d-none');		
		(jQuery)('#buscador_ofiCabecera').addClass('d-flex');		
		(jQuery)('.checkout').hide();
		(jQuery)('.cart-checkout-nav').hide();
		(jQuery)('.woocommerce-info').hide();
		(jQuery)('html,body').animate({
        	scrollTop: (jQuery)("#CEX").offset().top-200
    	}, 'slow');
	}
	function cerrarModal() {
		(jQuery)('#buscador_ofi').addClass('d-none');
		(jQuery)('#buscador_ofiCabecera').addClass('d-none');
		(jQuery)('#buscador_ofiCabecera').removeClass('d-flex');
		(jQuery)('.woocommerce-billing-fields').show();
		(jQuery)('.woocommerce-shipping-fields').show();
		(jQuery)('.woocommerce-additional-fields__field-wrapper').show();
		(jQuery)('#order_review').show();
		(jQuery)('#order_review_heading').show();
		(jQuery)('.checkout').show();
		(jQuery)('.cart-checkout-nav').show();
		(jQuery)('.woocommerce-info').show();

		//document.getElementsByName('checkout')[0].style.display='block';
	}
	window.onclick = function(event) {
		if (event.target == (jQuery)('#buscador_ofi')) {
			(jQuery)('#buscador_ofi').removeClass('d-none');
			(jQuery)('#buscador_ofiCabecera').removeClass('d-none');
			(jQuery)('#buscador_ofiCabecera').addClass('d-flex');
		}

	}	
</script>
