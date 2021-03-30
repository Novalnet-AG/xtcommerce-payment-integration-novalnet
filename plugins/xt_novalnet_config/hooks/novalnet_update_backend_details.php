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
/**
 * Get the client ip address
 *
 * @return ip address
 */

global $store_handler;

$stores = $store_handler->getStores();
$js = '';

foreach ($stores as $store)
{
	$store_id = $store['id'];
	$js .= " 
(function($){

	novalnet_parent = {
		
		get_details : function () {
			var params = {
						activation_key : 'conf_XT_NOVALNET_ACTIVATION_KEY_shop_". $store_id ."',
						admin_link : '". XT_NOVALNET_ADMIN_PORTAL_LINK_TITLE ."',
						logo_link : '". XT_NOVALNET_LOGO_MANAGEMENT_TITLE ."',
						payment_logo_key : 'conf_XT_NOVALNET_PAYMENT_LOGO_ENABLE_shop_". $store_id ."',
						status_link : '". XT_NOVALNET_ORDER_STATUS_MANAGEMENT_TITLE ."',
						status_key : 'conf_XT_NOVALNET_ONHOLD_COMPLETE_STATUS_shop_". $store_id ."',
						callback_management_link : '". XT_NOVALNET_MERCHANT_SCRIPT_MANAGEMENT_TITLE ."',
						callback_management_key : 'conf_XT_NOVALNET_CALLBACK_TESTMODE_shop_". $store_id ."',
						select_text : '". XT_NOVALNET_SELECT_TEXT ."',
						vendor_id : 'conf_XT_NOVALNET_VENDOR_ID_shop_".$store_id."',
						auth_code : 'conf_XT_NOVALNET_AUTH_CODE_shop_".$store_id."',
						product_id : 'conf_XT_NOVALNET_PRODUCT_ID_shop_".$store_id."',
						access_key : 'conf_XT_NOVALNET_PAYMENT_ACCESS_KEY_shop_".$store_id."',
						tariff_id : 'conf_XT_NOVALNET_TARIFF_ID_shop_". $store_id ."',
				}
			return params;
		}
		
	};

	$( document ).ready(function () {
		var s = document.createElement('script');
		s.type = 'text/javascript';
		s.src = '../plugins/xt_novalnet_config/javascript/xt_novalnet_admin.js';
		document.getElementsByTagName('head')[0].appendChild(s);
	});

})(jQuery);";

}
$params['rowActionsJavascript'] .= $js;
