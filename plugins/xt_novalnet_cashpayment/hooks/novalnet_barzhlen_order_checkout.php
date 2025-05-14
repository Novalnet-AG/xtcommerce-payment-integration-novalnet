<?php
/**
 * Novalnet payment extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Novalnet End User License Agreement
 * that is bundled with this package in the file freeware_license_agreement.txt
 *
 * DISCLAIMER
 *
 * If you wish to customize Novalnet payment extension for your needs, please contact technic@novalnet.de for more information.
 *
 * @category   Novalnet
 * @package    Novalnet_Payment
 * @copyright  Copyright (c) Novalnet AG
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
 
defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $success_order,$db;

// Initiate order object.
if ($success_order) {
   
    // Check for Novalnet payment
    if(strpos($success_order->order_data['payment_code'], 'novalnet') !== false) {
		if($success_order->order_data['payment_code'] == 'xt_novalnet_cashpayment') {
			$novalnet_info = $db->execute("SELECT test_mode,payment_details FROM ".DB_PREFIX."_novalnet_transaction_detail WHERE order_no= $success_order->oID");
			$payment_details = (array) json_decode($novalnet_info->fields['payment_details']);
			$barzhlen_url = "https://cdn.barzahlen.de/js/v2/checkout.js";
			if ($novalnet_info->fields['test_mode'] == 1) {
				$barzhlen_url = "https://cdn.barzahlen.de/js/v2/checkout-sandbox.js";
			}
		// Create a template
				$template = new Template();

				// Assign template file
				$tpl      = 'xt_novalnet_cashpayment_checkout_success.html';
				$template->getTemplatePath(
					$tpl,
					'xt_novalnet_cashpayment',
					'',
					'plugin'
				);
				$tp_data = array(
					'txt_cp_checkout_token' => $payment_details['cp_checkout_token'],
					'txt_cp_checkout_url' => $barzhlen_url,
					'txt_cp_checkout_button' => XT_NOVALNET_CASHPAYMENT_CHECKOUT_PAGE_BUTTON_NAME,
				);
				echo $template->getTemplate(
					'novalnet_cashpayment_checkout_success',
					$tpl,
					$tp_data
				);
		}		
	}
}
