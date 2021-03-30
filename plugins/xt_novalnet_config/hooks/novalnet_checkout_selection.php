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

global $xtPlugin;
if(in_array($page->page_action, array('shipping', 'payment', 'confirmation')))
{
            unset($_SESSION['last_order_id']);
} 
if (!isset($xtPlugin->active_modules['xt_novalnet_config']) || !ctype_digit(XT_NOVALNET_VENDOR_ID) || !ctype_digit(XT_NOVALNET_PRODUCT_ID) || XT_NOVALNET_TARIFF_ID == 'none' || trim(XT_NOVALNET_AUTH_CODE) == '' || trim(XT_NOVALNET_PAYMENT_ACCESS_KEY) == '' || !function_exists('base64_decode') || !function_exists('base64_encode') || !function_exists('hash') || !function_exists('curl_init') || !function_exists('openssl_encrypt') || !function_exists('openssl_decrypt')) {
    foreach($payment_data as $key => $value) {
        if(strpos($key, 'xt_novalnet') !== false) {
            unset($payment_data[$key]);
        } else if (strpos($payment_data[$key]['payment'], 'xt_novalnet') !== false ) {
            unset($payment_data[$key]['payment']);
        }
    }
}

// Get the selected payment.
$selected_payment = (!empty($_SESSION['novalnet_selected_payment']) ? $_SESSION['novalnet_selected_payment'] :  !empty($_SESSION['selected_payment']) ? $_SESSION['selected_payment'] : (!empty($_SESSION['payment']) ? $_SESSION['payment'] : ''));

// Hide the Invoice/Direct Debit SEPA payments when the user failed to enter correct pin
if (!empty($selected_payment) && !empty($_SESSION[$selected_payment. '_max_hidden_time']) && (time() < $_SESSION[$selected_payment. '_max_hidden_time'])) {
    if(isset($payment_data[$selected_payment])) {
        unset($payment_data[$selected_payment]);
    } else {
		if(isset($payment_data)) {
			foreach($payment_data as $key => $value) {
				if (strpos($payment_data[$key]['payment'], $selected_payment) !== false ) {
					unset($payment_data[$key]['payment']);
				}
			}
		}
    }
} else {
    unset($_SESSION[$selected_payment . '_max_hidden_time'], $_SESSION['nn_payment_lock']);
}
