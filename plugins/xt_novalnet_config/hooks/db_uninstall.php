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

global $db;
$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text LIKE '%Novalnet%'");
$db->Execute("DELETE FROM ".TABLE_PLUGIN_CONFIGURATION." WHERE config_key LIKE '%NOVALNET%'");
