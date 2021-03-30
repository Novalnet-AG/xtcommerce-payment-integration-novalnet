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

if(isset($_REQUEST['nn_aff_id']) && ctype_digit($_REQUEST['nn_aff_id'])) {
    $_SESSION['nn_aff_id'] = trim($_REQUEST['nn_aff_id']);
}
