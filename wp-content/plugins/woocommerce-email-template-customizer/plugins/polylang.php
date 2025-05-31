<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class VIWEC_Plugins_Polylang extends VIWEC_Multi_Languages {
	public function __construct() {
		if ( ! class_exists('Polylang') ) {
			return;
		}
		parent::__construct();
	}

	/**
	 * @param $product WC_Product
	 *
	 */
	public function get_product_language($product, $language){
		if ($language){
			$product_id = $product->get_id();
			$language_object_id = pll_get_post( $product_id, $language );
			if ($language_object_id != $product_id){
				$language_object = wc_get_product($language_object_id);
			}
			if (!empty($language_object)){
				$product = $language_object;
			}
		}
		return $product;
	}
	/**
	 * @param $order WC_Order
	 *
	 * @return string
	 */
	public function get_order_language($order){
		$order_id = $order->get_id();
		if (isset($this->cache['order_language'][$order_id])){
			return $this->cache['order_language'][$order_id];
		}
		if (!isset($this->cache['order_language'])){
			$this->cache['order_language'] =[];
		}
		$this->cache['order_language'][$order_id] =  pll_get_post_language( $order_id );
		return $this->cache['order_language'][$order_id];
	}
	public function get_current_language() {
		if ( isset( $this->cache['current_language'] ) ) {
			return $this->cache['current_language'];
		}
		$current_language = pll_current_language( 'slug' );
		if ($current_language === $this->get_default_language()){
			$current_language ='';
		}
		$this->cache['current_language'] = $current_language;
		return $this->cache['current_language'];
	}
	public function get_default_language() {
		if ( isset( $this->cache['default_language']) ) {
			return $this->cache['default_language'];
		}
		$this->cache['default_language'] = pll_default_language( 'slug' );
		return $this->cache['default_language'];
	}
	public function get_languages_data() {
		if ( isset( $this->cache['languages_data'] ) ) {
			return $this->cache['languages_data'];
		}
		$languages_data =[];
		$languages = pll_languages_list(array( 'fields' => '' ) );
		foreach ($languages as $language){
			if (!is_array($language)){
				continue;
			}
			$tmp = new PLL_Language($language);
			if (!isset($tmp->slug)){
				continue;
			}
			$languages_data[$tmp->slug]=[
				'code' => $tmp->slug,
				'translated_name' => $tmp->name ??$tmp->slug,
				'url' => $tmp->search_url ??'' ,
				'country_flag_url' => !empty($tmp->custom_flag_url) ? $tmp->custom_flag_url : ($tmp->flag_url??''),
			];
		}
		$this->cache['languages_data'] = $languages_data;
		return $this->cache['languages_data'];
	}
}