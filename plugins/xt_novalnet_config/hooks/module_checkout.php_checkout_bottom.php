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
global $tpl;
$tmp_tpl = _SRV_WEBROOT._SRV_WEB_TEMPLATES.'xt_responsive/xtPro/'._SRV_WEB_CORE.'pages/checkout.html';
if(file_exists($tmp_tpl)){
	$tpl = $tmp_tpl;
}
else {
	$tpl = _SRV_WEBROOT.'plugins/xt_novalnet_config/templates/novalnet_checkout.html';
}

?>
