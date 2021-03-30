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
if(!empty($_REQUEST['selected_payment']) && strpos($_REQUEST['selected_payment'], 'novalnet') !== false && !in_array($_REQUEST['selected_payment'], array('xt_novalnet_cc', 'xt_novalnet_sepa', 'xt_novalnet_invoice'))) {
	$_SESSION[$_REQUEST['selected_payment'].'_data'] = $_REQUEST;
}
