<?php
/**
 * Plugin Name: VNPAY-INSTALLMENT
 * Description: VNPAY INSTALLMENT for Woocommerce
 * Version: 1.0.1
 * Author: VNPAY
 * Author URI: https://vnpay.vn/
 * License: DTT
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use vnpayInst\InstGateway\international\vnpayInstGateway;
use vnpayInst\InstGateway\vnpayInternationalResponse;
use vnpayInst\Traits\Pages;

require 'vendor/autoload.php';

/**
 */
class vnpayInst
{
	use vnpayInst\Traits\Pages;

	
	protected $shortcodes = array();

	
	protected $responses;

	public function __construct()
	{
		$this->constants();
		add_action('init', array($this, 'renderPages'));
		add_action('plugins_loaded', array($this, 'vnpayInstInit'));
		add_filter('woocommerce_locate_template', array($this, 'vnpayInstWoocommerceTemplates'), 10, 3);
		$this->loadModule();
		$this->responseListener();
	}

	
	public function constants()
	{
		$consts = array(
			'URL' => plugins_url('', __FILE__),
			'IMAGE' => plugins_url('/images', __FILE__)
		);

		foreach ($consts as $key => $value) {
			define($key, $value);
		}
	}

	
	public function vnpayInstInit()
	{
		add_filter('woocommerce_payment_gateways', array($this, 'addPaymentMethod'));
	}

	
	public function addPaymentMethod($methods)
	{
		$methods[] = 'vnpayInst\InstGateway\vnpayInstGateway';
		return $methods;
	}


	public function loadModule()
	{
		//$this->shortcodes[] = new vnpayInst\Shortcodes\Thankyou;
	}

	public function responseListener()
	{
		if (isset($_GET['type'])) {
			switch ($_GET['type']) {
				case 'international':
					$this->responses[] = new vnpayInternationalResponse;
					break;
				
			}
		}
	}

	
	public function renderPages()
	{
		$checkRenderPage = (!get_option('vnpayInst_settings')) ? false : get_option('vnpayInst_settings');
		if ($checkRenderPage != false) return;
		if (!empty($this->pages)) {
			foreach ($this->pages as $slug => $args) {
				$page = new vnpayInst\Page($args);
			}
			update_option('vnpayInst_settings', true);
		}
	}

	public function vnpayInstWoocommerceTemplates($template, $template_name, $template_path)
	{
		global $woocommerce;

		$_template = $template;

		if (!$template_path) $template_path = $woocommerce->template_url;

		$plugin_path  = __DIR__ . '/woocommerce/';

		$template = locate_template(
			array(
			  $template_path . $template_name,
			  $template_name
			)
		);

		if (!$template && file_exists( $plugin_path . $template_name))

		$template = $plugin_path . $template_name;

		if (!$template) $template = $_template;

		return $template;
	}
}

new vnpayInst;