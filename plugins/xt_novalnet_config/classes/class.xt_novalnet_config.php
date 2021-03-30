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
 * xt_novalnet_config Class
 */

class xt_novalnet_config
{
    /**
     * Forming payment parameters
     *
     * @param string $selected_payment
     * @return array
     */
    public function form_payment_params($selected_payment)
    {
        return Novalnet::form_payment_params($selected_payment);
    }


    /**
     * Proceed the payment process
     *
     * @param array  $parameters
     * @param string $selected_payment
     */
    public function proceed_payment($parameters, $selected_payment)
    {
        // Post request to server
        $response_data = Novalnet::perform_curlrequest($parameters);
        parse_str($response_data, $response);
        if ($response['status'] == '100') {

            if(empty($_SESSION[$selected_payment]['guarantee_payment']) &&  empty($_SESSION[$selected_payment . '_data'][$selected_payment . '_one_click_process']) && !empty($_SESSION[$selected_payment]['fraud_module_enabled'])) {

                // Fraud check prevention call
                $_SESSION['novalnet'][$selected_payment]['proceed_pin_call'] = true;

                // Assign response in SESSION
                $_SESSION['response_data'] = $response;

                Novalnet::checkout_redirect_process(constant('XT_NOVALNET_'.$_SESSION[$selected_payment]['fraud_module_enabled'].'_PIN_NOTIFY_TEXT'));
            }
        }

        // Complete the order
        Novalnet::complete_novalnet_order($selected_payment, $response);
    }


    /**
     * Send the pin for confirmation
     *
     * @param array $data
     */
    public function send_forget_pin_request($data)
    {
        $config = Novalnet::get_vendor_details();

        Novalnet::validate_fraud_amount();

        $request_parameters = array(
        'vendor_id'       => $config['vendor'],
        'vendor_authcode' => $config['auth_code'],
        'request_type'    => 'TRANSMIT_PIN_AGAIN',
        'tid'             => $_SESSION['response_data']['tid'],
        'lang'            => !empty($_SESSION['selected_language']) ? $_SESSION['selected_language'] : 'EN',
       );

        // Sending forgot PIN request to server
        $xml_response = Novalnet::perform_xml_request($request_parameters);
        if ($xml_response['status'] == '0529008') {
			unset($_SESSION[$data], $_SESSION['novalnet'], $_SESSION['response_data']);
		}
        $message      = Novalnet::set_response_message($xml_response);
        Novalnet::checkout_redirect_process($message);
    }

    /**
     * Send PIN call
     *
     * @param array $data
     */
    public function send_pin_confirmation($data)
    {
		// Validate fraud modules input fields for second call and new pin generation
		Novalnet::validate_fraud_amount();

        $config = Novalnet::get_vendor_details();
        $request_parameters = array(
        'vendor_id'       => $config['vendor'],
        'vendor_authcode' => $config['auth_code'],
        'request_type'    => 'PIN_STATUS',
        'tid'             => $_SESSION['response_data']['tid'],
        'lang'            => !empty($_SESSION['selected_language']) ? $_SESSION['selected_language'] : 'EN',
        'pin'             => trim($data[$data['selected_payment'] . '_pin']),
       );

        // Sending the transaction PIN to server
        $xml_response = Novalnet::perform_xml_request($request_parameters);
        $message      = Novalnet::set_response_message($xml_response);

        if (isset($xml_response['status']) && $xml_response['status'] == '100') {

			// Get the new TID status
			$_SESSION['response_data']['tid_status'] = $xml_response['tid_status'];

            // Form a comments after response from server
            Novalnet::complete_novalnet_order($data['selected_payment'], $_SESSION['response_data']);
        } else if ($xml_response['status'] == '0529006') {

            // Hide payment notification
            $_SESSION[$data['selected_payment']. '_max_hidden_time'] = time() + ( 30 * 60 );
            Novalnet::checkout_redirect_process($message);
        } else {
			Novalnet::checkout_redirect_process($message);
		}
    }


    /**
     * Assign on-hold to payment parameter
     *
     * @param array $parameters
     */
    public function onhold_param(&$parameters,$payment_name)
    {
		$onhold_type = trim(constant(strtoupper($payment_name).'_ENABLE_AUTHORIZATION'));
		
        // Check the Minimum transaction limit for authorization 
        $onhold_limit_amount = trim(constant(strtoupper($payment_name).'_ONHOLD_LIMIT_AMOUNT'));
      
        if ($onhold_type == 'AUTHORIZE' && ($parameters['amount'] >= $onhold_limit_amount || empty($onhold_limit_amount))) {
            $parameters['on_hold'] = '1';
        }
    }


    /* Check for zero amount booking
     *
     * @param array $parameters
     * @param string $zero_booking
     */
    public function zero_booking_param(&$parameters, $code, $zero_booking)
    {
        // Check the zero amount booking
        if ($zero_booking == 'ZEROAMOUNT' && $_SESSION['novalnet']['tariff_type'] == '2') {

            // Assign payment parameters in SESSION
            $_SESSION[$code]['payment_params'] = $parameters;
            $parameters['amount'] = 0;
            $parameters['create_payment_ref'] = 1;
        }
    }

    /**
     * Validate amount after pin generation
     *
     * @return boolean
     */
    public function validate_fraud_amount() {

        if (isset($_SESSION['order_amount']) && $_SESSION['order_amount'] != $_SESSION['cart']->total['plain'] * 100) {
			Novalnet::unset_novalnet_session('novalnet');
            Novalnet::checkout_redirect_process(XT_NOVALNET_AMOUNT_CHANGE_ERROR_TEXT);
        }
        return true;
    }

    /**
     * Form fraud module parameters
     *
     * @param array $parameters
     * @param string $code
     */
    public function form_fraud_module_params(&$parameters, $code)
    {
		if(empty($_SESSION[$code]['guarantee_payment']) && !empty($_SESSION[$code]['fraud_module_enabled'])) {
			if ($_SESSION[$code]['fraud_module_enabled'] == 'CALLBACK') {
				$parameters['tel']             = $_SESSION[$code . '_data'][$code . '_fraud_data'];
				$parameters['pin_by_callback'] = '1';
			} else {
				$parameters['mobile']     = $_SESSION[$code . '_data'][$code . '_fraud_data'];
				$parameters['pin_by_sms'] = '1';
			}
		}
	}

	/**
     * Form Guarantee payment parameters
     *
     * @param array $parameters
     * @param string $code
     */
    public function form_guarantee_payment_params(&$parameters, $code)
    {
		if(!empty($_SESSION[$code]['process_guarantee'])) {
			$payment_details = Novalnet::get_payment_key();
			$payment_code = $code . '_guarantee';
			$parameters['payment_type'] = $payment_details[$payment_code]['payment_type'];
			$parameters['key'] = $_SESSION[$code]['key'] = $payment_details[$payment_code]['key'];
			if(!empty($_SESSION[$code]['customer_company'])) {
				$parameters['birth_date']   = date('Y-m-d', strtotime($_SESSION[$code . '_data'][$code . '_user_dob']));
			}
		}
	}

	/**
     * Form One click process parameters
     *
     * @param reference array $parameters
     * @param string $code
     * @param string $shopping_type
     */
    public function form_one_click_params(&$parameters, $code, $shopping_type)
    {
		if ($shopping_type == 'ONECLICK') {
			if(!empty($_SESSION[$code . '_data'][$code . '_one_click_process'])) {
				$parameters['payment_ref'] = $_SESSION[$code]['masked_tid'];
			} else {
				$parameters['create_payment_ref'] = '1';
			}
		}
	}
}
