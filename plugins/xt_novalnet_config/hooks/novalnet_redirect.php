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
global $page,$page_data;

$selected_payment = $_SESSION['selected_payment'];
if($page->page_name == 'checkout' && $page->page_action == 'novalnet_redirect' && strpos($selected_payment, 'novalnet') !== false) {
		      $template = new Template();
		      $tpl_data = array('param'=> $_SESSION[$selected_payment]['redirect_parameters'], 'redirect_url' => $_SESSION[$selected_payment]['redirect_url']);
		      $tpl = 'xt_novalnet_redirect.html';
      $template->getTemplatePath($tpl, 'xt_novalnet_config', '', 'plugin');
      echo ($template->getTemplate('', $tpl, $tpl_data));
}

if($page->page_name == 'cart') {
echo '<style>
	 @media (min-width: 992px) {
#cart .col-md-6 {
width: 100%;

 }
}
	</style>';
}
?>
