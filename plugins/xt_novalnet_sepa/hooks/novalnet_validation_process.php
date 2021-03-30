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

if ($_REQUEST['selected_payment'] == 'xt_novalnet_sepa') {

	$code = 'xt_novalnet_sepa';

	$_SESSION['xt_novalnet_sepa_data'] = $_REQUEST;
	$_SESSION['novalnet_selected_payment'] = $_REQUEST['selected_payment'];

	// Include required file
	include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_novalnet_config/classes/class.novalnet.php';

    if(!empty($_SESSION['novalnet']['xt_novalnet_sepa']['proceed_pin_call'])) {


		if (!empty($_SESSION['xt_novalnet_sepa_data']['xt_novalnet_sepa_new_pin'])) {

			// Include required file
			include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_novalnet_config/classes/class.xt_novalnet_config.php';
			$xt_novalnet_config = new xt_novalnet_config();
			$xt_novalnet_config->send_forget_pin_request($code);

		} elseif(empty($_SESSION['xt_novalnet_sepa_data']['xt_novalnet_sepa_pin'])) {

			Novalnet::checkout_redirect_process(XT_NOVALNET_PAYMENT_ENTER_PIN_TEXT);
		} elseif (!Novalnet::validate_pin($_SESSION['xt_novalnet_sepa_data']['xt_novalnet_sepa_pin'])) {
			Novalnet::checkout_redirect_process(XT_NOVALNET_PAYMENT_INVALID_PIN_TEXT);
		}
	} else {

		// Validate Payment form values
		if(empty($_SESSION['xt_novalnet_sepa_data']['xt_novalnet_sepa_one_click_process'])) {
			if( empty($_SESSION['xt_novalnet_sepa_data']['novalnet_sepa_account_holder']) || empty($_SESSION['xt_novalnet_sepa_data']['novalnet_sepa_iban']) || Novalnet::validate_alpha_numeric($_SESSION['xt_novalnet_sepa_data']['novalnet_sepa_iban'])) {
				Novalnet::checkout_redirect_process(XT_NOVALNET_SEPA_INVALID_ACCOUNT_DETAILS_ERROR_TEXT);
			}

			// Check and validate fraud check data
			Novalnet::validate_fraud_data($code);
		} else{

			if(isset($_SESSION['xt_novalnet_sepa_data']['xt_novalnet_sepa_one_click_user_dob']) && isset($_SESSION['xt_novalnet_sepa_data']['xt_novalnet_sepa_one_click_user_dob'])) {
				$_SESSION['xt_novalnet_sepa_data']['xt_novalnet_sepa_user_dob'] = $_SESSION['xt_novalnet_sepa_data']['xt_novalnet_sepa_one_click_user_dob'];
			}
		}

		// Check and validate guarantee payment
		$error = Novalnet::validate_guarantee_process($code);
		if($error != '') {
			 Novalnet::checkout_redirect_process($error);
		}
	}
}

