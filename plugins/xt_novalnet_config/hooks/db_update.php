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

global $db, $store_handler;
$db->Execute("ALTER TABLE ".DB_PREFIX."_novalnet_transaction_detail
	ADD due_date date COMMENT 'Transaction Due Date in response',
	ADD booked enum('0','1') DEFAULT '1' COMMENT 'Transaction booked',
	ADD payment_params text COMMENT 'Payment params used for zero amount booking',
	ADD payment_ref enum('0','1') DEFAULT '0' COMMENT 'payment reference for the transaction',
	ADD payment_details text COMMENT 'Payment details',
	DROP COLUMN status,
	DROP COLUMN active,
	DROP COLUMN amount,
	DROP COLUMN additional_note,
	DROP COLUMN callback_status,
	DROP COLUMN account_holder,
	DROP COLUMN process_key
");

$plugin_id = $db->GetOne('SELECT plugin_id FROM ' . TABLE_PLUGIN_PRODUCTS . ' where code ="xt_novalnet_config"');

$stores = $store_handler->getStores();
foreach ($stores as $store)
{
	$store_id = $store['id'];

	// Get store details
	$record = $db->Execute("SELECT shop_ssl, shop_ssl_domain FROM ".TABLE_MANDANT_CONFIG." where shop_id =?", array($store_id));

	// Check for SSL
	$shop_url = $record->fields['shop_ssl'] != 0 ? $record->fields['shop_ssl_domain'] : '';
	// Insert Dynamic Notfication URL
	$db->AutoExecute(TABLE_PLUGIN_CONFIGURATION, array(
		'config_key'   => 'XT_NOVALNET_CALLBACK_URL',
		'config_value' => 'https://'.$shop_url.'/index.php?page=callback&page_action=xt_novalnet_config',
		'plugin_id'    => $plugin_id,
		'type'         => 'textfield',
		'shop_id'      => $store_id,
		'sort_order'   => '25',
	));
}

echo '<script>
    $.ajax({
        type: "POST",
        url: "adminHandler.php?load_section=plugin_installed&pg=overview&parentNode=node_plugin_installed&m_ids='.$plugin_id.'&multiFlag_setStatus=true",
        dataType: "json",
        async: false,
        success: function(data) {
			return true;
        }
    });
</script>';
