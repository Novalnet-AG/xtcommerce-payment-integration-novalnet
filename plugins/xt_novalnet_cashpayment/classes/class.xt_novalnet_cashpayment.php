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

include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_novalnet_config/classes/class.novalnet.php';

/**
 * xt_novalnet_cashpayment Class.
 */

class xt_novalnet_cashpayment
{
	/**
	 * Code for the gateway.
	 *
	 * @var string
	 */
	public $code         = 'xt_novalnet_cashpayment';
	
	/**
	 * Gateway shows Sub-Payments inside the Payment on the checkout.
	 *
	 * @var bool
	 */
	public $subpayments  = false;
	
	/**
	 * Gateway shows default iframe on the checkout.
	 *
	 * @var bool
	 */
	public $iframe       = false;
	
	/**
	 * Assign value for gateway post process
	 *
	 * @var bool
	 */
	public $external       = false;

	/**
	 * Settings of the gateway template.
	 *
	 * @var array
	 */
	public $data         = array();
    
	/**
	 * Constructor
	 *
	 */
    function xt_novalnet_cashpayment()
    {
        // Assign basic details to template.
        $this->data = array_merge($this->data, Novalnet::get_basic_template_details($this->code));
    }
    
    /**
     * Form additional parameters
     *
     * @param array $xt_novalnet_config
     * @param array $parameters
     */
    function additional_parameters($xt_novalnet_config, &$parameters) {
    	$barzahlen_due_date = intval(trim(XT_NOVALNET_CASHPAYMENT_CASH_DUE_DATE));

		// Get invoice due date
		if ($barzahlen_due_date != '') {
			   $parameters['cashpayment_due_date'] = date('Y-m-d', strtotime('+' . $barzahlen_due_date . 'days'));
		}

		// Process the payment.
		$xt_novalnet_config->proceed_payment($parameters, $this->code);
	}
}
