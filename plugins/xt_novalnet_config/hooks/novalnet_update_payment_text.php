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

if(!empty($_REQUEST['load_section']) && $_REQUEST['load_section'] == 'payment' && $_REQUEST['gridHandle'] == 'paymentgridForm') {
    echo '<script type="text/javascript" src="../plugins/xt_novalnet_config/javascript/xt_novalnet_add_description.js">
    </script>
    <input type="hidden" id="xt_novalnet_invoice_guarantee_payment_title" value="'. XT_NOVALNET_INVOICE_PAYMENT_GUARANTEE_TITLE .'">
    <input type="hidden" id="xt_novalnet_invoice_guarantee_payment_hint" value="'. XT_NOVALNET_INVOICE_PAYMENT_GUARANTEE_HINT .'">
    <input type="hidden" id="xt_novalnet_sepa_guarantee_payment_title" value="'. XT_NOVALNET_SEPA_PAYMENT_GUARANTEE_TITLE .'">
    <input type="hidden" id="xt_novalnet_sepa_guarantee_payment_hint" value="'. XT_NOVALNET_SEPA_PAYMENT_GUARANTEE_HINT .'">
    <input type="hidden" id="xt_novalnet_paypal_reference_transaction_hint" value="'. XT_NOVALNET_PAYPAL_REFERENCE_TRANSACTION_HINT .'">
    ';
}
