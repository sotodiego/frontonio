<?php
/**
 * Admin View: Settings
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="yith-plugin-fw  yit-admin-panel-container">
	<form method="post" id="plugin-fw-wc" action="" enctype="multipart/form-data">
		<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br/></div>

		<?php do_action( 'woocommerce_settings_email' ); ?>

		<p class="submit">
			<?php if ( ! isset( $GLOBALS['hide_save_button'] ) ) : ?>
				<input name="save" class="button-primary" type="submit"
					value="<?php esc_html_e( 'Save changes', 'yith-woocommerce-waiting-list' ); ?>"/>
			<?php endif; ?>
			<input type="hidden" name="subtab" id="last_tab"/>
			<?php wp_nonce_field( 'woocommerce-settings' ); ?>
		</p>
	</form>
</div>