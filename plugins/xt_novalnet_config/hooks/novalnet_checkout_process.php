<?php
/*
 ###################################################
 #             Novalnet Payment file
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 # @author    Novalnet AG
 # @copyright 2019 Novalnet 
 # @license   https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 # @link      https://www.novalnet.de
 ###################################################
*/
 
defined('_VALID_CALL') or die('Direct Access is not allowed.');

include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_novalnet_config/classes/class.xt_novalnet_config.php';

if( strpos($_SESSION['selected_payment'], 'novalnet') !== false && is_data($_SESSION[$_SESSION['selected_payment'].'_data']) ) {

	$code = $_SESSION['selected_payment'];
	
	// Create instance
	$xt_novalnet_config = new xt_novalnet_config();
	
	
	if(!empty($_SESSION['novalnet'][$code]['proceed_pin_call'])) {
		$xt_novalnet_config->send_pin_confirmation($_SESSION[$code.'_data']);
	} else {
		// Form all common required parameters
		$parameters           = $xt_novalnet_config->form_payment_params($code);
		
		$payment_class = new $code();
		// Form additional parameters.
		$payment_class->additional_parameters($xt_novalnet_config, $parameters); 
	}
}
