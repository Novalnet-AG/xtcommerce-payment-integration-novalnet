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

/**
 * xt_novalnet_extension Class
 */

class xt_novalnet_extension
{

    /**
     * Add refund panel fields
     *
     * @param integer $order_id
     * @param array   $order_data
     * @param date    $order_date
     * @return array
     */
    static function getAddRefundBox($order_id, $order_data, $order_date){
        // Create Refund panel
        $panel = new PhpExt_Form_FormPanel('addRefundForm');

        // Set Refund panel properties
        $panel->setId('addRefundForm'.$order_id)
            ->setTitle(__define('XT_NOVALNET_REFUND_PROCESS_TEXT'))
            ->setAutoWidth(true)
            ->setAutoScroll(true)
            ->setBodyStyle('padding: 10px;')
            ->setUrl("adminHandler.php?plugin=xt_novalnet_config&load_section=xt_novalnet_operations&pg=refundProcess&orders_id=".$order_id);

        // Add refund panel fields
        $refund_amount = PhpExt_Form_NumberField::createNumberField('novalnet_refund_amount', __define('XT_NOVALNET_REFUND_AMOUNT_TEXT'), '');
        $refund_amount -> setValue($order_data['amount'])
                       -> setAllowDecimals(false)
                       -> setAllowBlank(false)
                       -> setAllowNegative(false);
        $panel->addItem($refund_amount);

        // Check refund reference field
        if($order_date != '' && date('Y-m-d') != date('Y-m-d', strtotime($order_date))) {
            $panel->addItem(PhpExt_Form_TextField::createTextField('novalnet_refund_ref', __define('XT_NOVALNET_REFUND_REFERENCE_TEXT')));
        }

        $panel->addButton(self::setSubmitButton('addRefundForm'.$order_id, __define('XT_NOVALNET_CONFIRM_BUTTON_TEXT'),__define('XT_NOVALNET_REFUND_CONFIRM_TEXT')));

        return $panel;
    }


    /**
     * Add manage transaction fields
     *
     * @param integer $order_id
     * @return array
     */
    static function getAddTransBlock($order_id){
        // Create Manage transaction panel
        $panel = new PhpExt_Form_FormPanel('addTransBlock');

        // Set Manage transaction panel properties
        $panel->setId('addTransBlock'.$order_id)
            ->setTitle(__define('XT_NOVALNET_MANAGE_TRANSACTION_TEXT'))
            ->setAutoWidth(true)
            ->setBodyStyle('padding: 10px;')
            ->setUrl("adminHandler.php?plugin=xt_novalnet_config&load_section=xt_novalnet_operations&pg=manageTransactionProcess&orders_id=".$order_id);

        $combo_box = new ExtFunctions();

		// Set Manage transaction panel fields
        $combo = $combo_box->_comboBox('novalnet_transaction_status_change', __define('XT_NOVALNET_SELECT_STATUS_TEXT'), 'DropdownData.php?get=plg_xt_novalnet_transaction_code&plugin_code=xt_novalnet_config') -> setValue(__define('XT_NOVALNET_SELECT_TEXT'));
        $panel->addItem($combo);
        $panel->addButton(self::setSubmitButton('addTransBlock'.$order_id, __define('XT_NOVALNET_UPDATE_BUTTON_TEXT')));

        return $panel;
    }


    /**
     * Add amount update fields
     *
     * @param integer $order_id
     * @param array   $order_data
     * @return array
     */
    static function getAddAmountUpdate($order_id, $order_data){
        // Create Amount update/ Due date change panel
        $panel = new PhpExt_Form_FormPanel('addAmountUpdate');

		$title = ($order_data['payment_id'] == '27') ? __define('XT_NOVALNET_AMOUNT_DUEDATE_UPDATE_TEXT') : (($order_data['payment_id'] == '59') ? __define('XT_NOVALNET_CASHPAYMENT_AMOUNT_DUEDATE_UPDATE_TEXT') : __define('XT_NOVALNET_AMOUNT_UPDATE_TEXT'));
        // Set Amount update/ Due date change panel properties
        $panel->setId('addAmountUpdate'.$order_id)
            ->setTitle($title)
            ->setAutoWidth(true)
            ->setBodyStyle('padding: 10px;overflow-y:scroll;')
            ->setUrl("adminHandler.php?plugin=xt_novalnet_config&load_section=xt_novalnet_operations&pg=amountUpdateProcess&orders_id=".$order_id);

        // Set Amount update/ Due date change panel fields
        $update_amount = PhpExt_Form_NumberField::createNumberField('novalnet_amount_update', __define('XT_NOVALNET_AMOUNT_UPDATE_FIELD_TEXT'), '');
        $update_amount-> setValue($order_data['amount'])
                       -> setAllowDecimals(false)
                       -> setAllowBlank(false)
                       -> setAllowNegative(false);
        $panel->addItem($update_amount);

        // Add Due date field
        if(in_array($order_data['payment_id'], array('27','59'))) {

			if (empty($order_data['due_date'])) {
				global $db;
				// Get due date for lower version
				$table = DB_PREFIX . '_novalnet_preinvoice_transaction_detail';
				$result = $db->Execute("SHOW TABLES LIKE '".$table."'");

				if ($result->RecordCount()) {
					$due_date = $db->GetOne("SELECT due_date FROM $table WHERE order_no='". $order_id ."'");
				}
			} else {
				$due_date = $order_data['due_date'];
			}
			$due_date_title = ($order_data['payment_id'] == '27') ? __define('XT_NOVALNET_DUE_DATE_FIELD_TEXT') : __define('XT_NOVALNET_CASHPAYMENT_DUE_DATE_FIELD_TEXT');

			$date_field = PhpExt_Form_DateField::createDateField('novalnet_duedate', $due_date_title);
			$date_field->setValue($due_date);
            $panel->addItem($date_field);
        }
		$confirm_text = ($order_data['payment_id'] == '37') ? __define('XT_NOVALNET_AMOUNT_UPDATE_SEPA_CONFIRM_TEXT') :(($order_data['payment_id'] == '59') ? __define('XT_NOVALNET_AMOUNT_UPDATE_BARZAHLEN_CONFIRM_TEXT') :__define('XT_NOVALNET_AMOUNT_UPDATE_INV_PRE_CONFIRM_TEXT'));
        $panel->addButton(self::setSubmitButton('addAmountUpdate'.$order_id, __define('XT_NOVALNET_UPDATE_BUTTON_TEXT'),$confirm_text));

        return $panel;
    }
    

    /**
     * Add zero amount booking fields
     *
     * @param integer $order_id
     * @param array   $order_data
     * @return array
     */
    static function getAddZeroAmountBook($order_id, $order_data){
        //Create Zero zmount booking panel
        $panel = new PhpExt_Form_FormPanel('addZeroAmountBook');

        //Set Zero amount booking panel properties
        $panel->setId('addZeroAmountBook'.$order_id)
            ->setTitle(__define('XT_NOVALNET_AMOUNT_BOOK_PROCESS'))
            ->setAutoWidth(true)
            ->setBodyStyle('padding: 10px;')
            ->setUrl("adminHandler.php?plugin=xt_novalnet_config&load_section=xt_novalnet_operations&pg=amountBookProcess&orders_id=".$order_id);

		//Set transaction amount booking field
        $booking_amount = PhpExt_Form_NumberField::createNumberField('novalnet_booking_amount', __define('XT_NOVALNET_BOOK_AMOUNT_TEXT'), '');
        $booking_amount -> setValue($order_data['order_amount'])
                        -> setAllowDecimals(false)
                        -> setAllowBlank(false)
                        -> setAllowNegative(false);
        $panel->addItem($booking_amount);

        $panel->addButton(self::setSubmitButton('addZeroAmountBook'.$order_id, __define('XT_NOVALNET_BOOK_BUTTON_TEXT'),__define('XT_NOVALNET_ZERO_AMOUNT_BOOK_CONFIRM_TEXT')));

        return $panel;
    }


    /**
     * Create submit button
     *
     * @param string $form
     * @param string $field_name
     * @return array
     */
	static function setSubmitButton($form, $field_name,$confirm_text= ''){
        // Create submit button and action
        $submit_button = PhpExt_Button::createTextButton(
            $field_name,
            new PhpExt_Handler(
                PhpExt_Javascript::stm(
					"var on_hold_update =  '" . $confirm_text . "'
					if('".$form."'.search('addTransBlock') > -1){
						var on_hold_update = document.getElementById('novalnet_transaction_status_change').value == 100 ? '".XT_NOVALNET_ON_HOLD_CAPTURE_CONFIRM_TEXT."' : '".XT_NOVALNET_ON_HOLD_CANCEL_CONFIRM_TEXT."';
					}
					Ext.Msg.confirm('" . TEXT_CONFIRM . "',on_hold_update,function(btn){ if(btn == 'yes') novalnet_extenstion_process(); })
					function novalnet_extenstion_process()
                    {
                     Ext.getCmp('".$form."').getForm().submit({
				     waitMsg:'".__define('TEXT_LOADING')."',
				     success: function(form, action) {
						var response = action.result;
						contentTabs.getActiveTab().getUpdater().refresh();
						Ext.MessageBox.alert('".__define('TEXT_ALERT')."', response.message);
				     },
					 failure: function(form, action) {
						var response = action.result;
						Ext.Msg.alert('".__define('TEXT_FAILURE')."', response.status)
						Ext.Msg.alert('".__define('TEXT_FAILURE')."', response.message);
					 }
				   })
				  }"
               )
           )
       );
        // Set submit button type
        $submit_button->setType(PhpExt_Button::BUTTON_TYPE_SUBMIT);

        return $submit_button;
    }
}
