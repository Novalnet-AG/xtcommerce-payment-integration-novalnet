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
if($_REQUEST['action'] == 'shipping' && !empty($_REQUEST['selected_shipping'])) {
	$checkout = new checkout();
	$checkout->_setShipping($_REQUEST['selected_shipping']);
	$xtLink->_redirect($xtLink->_link(array('page'=>'checkout','paction'=>'payment','conn'=>'SSL')));

}

?>
