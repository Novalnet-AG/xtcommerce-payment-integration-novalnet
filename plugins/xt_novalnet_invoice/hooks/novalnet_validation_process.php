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


if ($_REQUEST['selected_payment'] == 'xt_novalnet_invoice') {

	$code = 'xt_novalnet_invoice';

	// Assign posted values in SESSION
	$_SESSION['xt_novalnet_invoice_data'] = $_REQUEST;
	$_SESSION['novalnet_selected_payment'] = $_REQUEST['selected_payment'];

	// Include required file
	include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_novalnet_config/classes/class.novalnet.php';

	if(!empty($_SESSION['novalnet']['xt_novalnet_invoice']['proceed_pin_call'])) {
		if (!empty($_SESSION['xt_novalnet_invoice_data']['xt_novalnet_invoice_new_pin'])) {

			// Process Forgot PIN request
			include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_novalnet_config/classes/class.xt_novalnet_config.php';
			$xt_novalnet_config = new xt_novalnet_config();
			$xt_novalnet_config->send_forget_pin_request($code);

		} elseif (empty($_SESSION['xt_novalnet_invoice_data']['xt_novalnet_invoice_pin'])) {
			Novalnet::checkout_redirect_process( XT_NOVALNET_PAYMENT_ENTER_PIN_TEXT );
		} elseif (!Novalnet::validate_pin($_SESSION['xt_novalnet_invoice_data']['xt_novalnet_invoice_pin'])) {
			Novalnet::checkout_redirect_process(XT_NOVALNET_PAYMENT_INVALID_PIN_TEXT);
		}
	} else {

		// Check and validate fraud check data
		Novalnet::validate_fraud_data($code);

		// Check and validate guarantee payment
		$error = Novalnet::validate_guarantee_process($code);
		if($error != '') {
			 Novalnet::checkout_redirect_process($error);
		}
	}

}
