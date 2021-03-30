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

include_once _SRV_WEBROOT ._SRV_WEB_PLUGINS.'xt_novalnet_config/classes/class.novalnet.php';

/**
 * xt_novalnet_paypal Class
 */
class xt_novalnet_paypal
{
    /**
	 * Code for the gateway.
	 *
	 * @var string
	 */
	public $code         = 'xt_novalnet_paypal';
	
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
	public $external       = true;
	
	/**
	 * Gateway shows payment form on the checkout.
	 *
	 * @var bool
	 */
	public $post_form    = true;

	/**
	 * Settings of the gateway template.
	 *
	 * @var array
	 */
	public $data         = array();
	
	/**
	 * Paygate URL.
	 *
	 * @var string
	 */
	public $TARGET_URL   = 'https://payport.novalnet.de/paypal_payport';
	
	/**
	 * Constructor
	 *
	 */
    function xt_novalnet_paypal()
    {
        // Assign basic details to template
        $this->data = array_merge($this->data, Novalnet::get_basic_template_details($this->code));

        // Assign One click process configuration
        Novalnet::get_shopping_type_details($this->data, $this->code);

        // Assign values for post process
        if (!empty($_REQUEST['tid']) && $_REQUEST['novalnet_payment'] == $this->code) {
            $this->external = true;
        }

        if(empty($_SESSION['xt_novalnet_paypal_data']['xt_novalnet_paypal_one_click_process'])) {
			$this->post_form = true;
		} else if( !empty($request['page_action']) && $request['page_action'] === 'confirmation' && !empty($request['page']) && $request['page'] == 'checkout' ){
			$this->data['payment_desc'] = XT_NOVALNET_PAYPAL_REFERENCE_TRANSACTION_DESCRIPTION_TEXT;
		}
    }
    
    /**
     * Form additional parameters
     *
     * @param array $xt_novalnet_config
     * @param array $parameters
     */
    function additional_parameters($xt_novalnet_config, &$parameters) {
		
		$process_type = constant(strtoupper($this->code).'_SHOPPING_TYPE');
		// Check whether the Zero amount booking is enabled or not for set the amount to 0
		$xt_novalnet_config->zero_booking_param( $parameters, $this->code, $process_type);

		// Assign the one-click shopping parameter
		$xt_novalnet_config->form_one_click_params($parameters, $this->code, $process_type);

		// Assign the on-hold parameter
		$xt_novalnet_config->onhold_param($parameters, $this->code);

		if(!empty($parameters['payment_ref'])) {
			$xt_novalnet_config->proceed_payment($parameters, $this->code);
		} else {

			// Set the redirecting parameters
			Novalnet::redirect_parameters($parameters, $this->code);
			$_SESSION[$this->code]['redirect_parameters'] = $parameters;
			$_SESSION[$this->code]['redirect_url'] = $this->TARGET_URL;
		}
	}
	
	/**
     * Form the payment params and send to server for lower versions
     *
     * @return none
     */
    function pspRedirect()
    {
		global $xtLink;
		return ($xtLink->_link(array('page'=>'checkout', 'paction'=>'novalnet_redirect')));
    }

    /**
     * Complete the order after response from novalnet
     *
     * @return none
     */
    function pspSuccess()
    {
        // Unset payment parameters SESSION
		if(!empty($_SESSION[$this->code]['redirect_parameters'])) {
			unset($_SESSION[$this->code]['redirect_parameters']);
		}

		// Check the hash value from server
		Novalnet::check_hash_process($_REQUEST);

		// Decode the values from server
		Novalnet::decode_response_data($_REQUEST);

		// Complete novalnet order
		Novalnet::complete_novalnet_order($this->code, $_REQUEST);
		return true;
    }
}
