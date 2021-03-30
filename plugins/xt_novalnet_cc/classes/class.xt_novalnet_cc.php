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
 * xt_novalnet_cc Class
 */

class xt_novalnet_cc
{
    /**
	 * Code for the gateway.
	 *
	 * @var string
	 */
	public $code         = 'xt_novalnet_cc';
	
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
	 * Gateway shows payment form on the checkout.
	 *
	 * @var bool
	 */
	public $post_form    = false;

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
	public $TARGET_URL   = 'https://payport.novalnet.de/pci_payport';
	
	/**
	 * Constructor
	 *
	 */
    function xt_novalnet_cc()
    {
		// Assign basic details to template
        $this->data = array_merge($this->data, Novalnet::get_basic_template_details($this->code));

        // Assign Credit card configuration
        $this->data['amex_logo']         = XT_NOVALNET_CC_AMEX_CARD_ENABLE;
        $this->data['maestro_logo']      = XT_NOVALNET_CC_MAESTRO_CARD_ENABLE;

        // Assign Iframe CSS style configuartions
        $this->data['nn_standard_label'] = XT_NOVALNET_CC_STANDARD_STYLE_LABEL_CONFIGURATION;
        $this->data['nn_standard_input'] = XT_NOVALNET_CC_STANDARD_STYLE_INPUT_CONFIGURATION;
        $this->data['nn_standard_css']   = XT_NOVALNET_CC_STANDARD_STYLE_CSS_CONFIGURATION;
        $this->data['nn_card_details_error_text'] = XT_NOVALNET_CC_IFRAME_CARD_DETAILS_ERROR_TEXT;

        // Assign One click process configuration
        Novalnet::get_shopping_type_details($this->data, $this->code);

		// Javascript path
        $this->data['js_path'] = _SRV_WEB_PLUGINS.'xt_novalnet_cc/javascript/xt_novalnet_cc.js';

		$this->data['iframe_src'] = '';

        // Form iframe API key
        if(defined('XT_NOVALNET_ACTIVATION_KEY') && trim(XT_NOVALNET_ACTIVATION_KEY) != '') {

			$selected_language = !empty($_SESSION['selected_language']) ? strtolower($_SESSION['selected_language']) : 'en';
			$vendor_details             = Novalnet::get_vendor_details();
			$api_params = array(
				'vendor'    => $vendor_details['vendor'],
				'product'   => $vendor_details['product'],
				'server_ip' => $_SERVER['SERVER_ADDR'],
				'lang'      => $selected_language,
			);
			$iframe_param = defined('XT_NOVALNET_ACTIVATION_KEY') ? base64_encode( http_build_query( $api_params ) ) : '';

			// Iframe URL
			$this->data['iframe_src'] = 'https://secure.novalnet.de/cc?api='. $iframe_param . '&ln=' . $selected_language;
		}

		// 3D secure actions.
		$this->data['is_redirect'] = (boolean) (XT_NOVALNET_CC_ENABLE_CC3D == 'true' || XT_NOVALNET_CC_ENABLE_CC3D_FORCE == 'true') ;
		if($this->data['is_redirect']) {
			$this->data['one_click_process_enabled'] = false;
			$this->data['given_details_style']  = 'display:none';
			$this->data['new_details_style']    = 'display:block';
			$this->external = true;

			$this->data['payment_desc'] = XT_NOVALNET_REDIRECTION_MESSAGE_TEXT;
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
		// Assign the on-hold parameter
		$xt_novalnet_config->onhold_param( $parameters, $this->code );
		
		// Check whether the Zero amount booking is enabled or not for set the amount to 0
		$xt_novalnet_config->zero_booking_param( $parameters, $this->code, $process_type );

		
		if (empty($this->data['is_redirect'])) {
			// Assign the one-click shopping parameters
			$xt_novalnet_config->form_one_click_params($parameters, $this->code, $process_type);
		}
		// Add payment form parameters
		if(empty($parameters['payment_ref'])) {
			$parameters['nn_it']     = 'iframe';
			$parameters['pan_hash']  =  $_SESSION['xt_novalnet_cc_data']['cc_pan_hash'];
			$parameters['unique_id'] =  $_SESSION['xt_novalnet_cc_data']['nn_unique_id'];
		}
		
		// Check/ Assign 3d-secure parameters
		if (! empty($this->data['is_redirect'])) {
			if(XT_NOVALNET_CC_ENABLE_CC3D == 'true'){
				$parameters['cc_3d'] = 1;
			}
			// Set the redirecting parameters
			Novalnet::redirect_parameters($parameters, $this->code);
			$_SESSION[$this->code]['redirect_parameters'] = $parameters;
			$_SESSION[$this->code]['redirect_url'] = $this->TARGET_URL;
		} else {
			// Process the payment
			$xt_novalnet_config->proceed_payment($parameters, $this->code);
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
     * @return boolean
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
