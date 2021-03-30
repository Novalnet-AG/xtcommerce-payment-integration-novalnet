/*
 * Novalnet Credit Card Script
 * author    Novalnet AG
 * copyright 2019 Novalnet 
 * license   https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * link      https://www.novalnet.de
*/

function load_novalnet_payment_iframe() {
	// Form style object.
    var styleObject = {
        labelStyle: jQuery('#nn_standard_label').val(),
        inputStyle: jQuery('#nn_standard_input').val(),
        styleText:  jQuery('#nn_standard_css').val(),
    };
	var nn_card_holder_label_lang = $('#nn_card_holder_text').val();
	var nn_card_holder_input_lang = $('#nn_card_holder_input').val();
	// Credit card Number language
	var nn_card_no_label_lang = $('#nn_card_no_text').val();
	var nn_card_no_input_lang = $('#nn_card_no_input').val();
	// Credit card Expiry date language
	var nn_card_exp_label_lang = $('#nn_card_exp_date_text').val();
	var nn_card_exp_input_lang = $('#nn_card_exp_date_input').val();
	// Credit card CVC language
	var nn_card_cvc_label_lang  = $('#nn_card_cvc_text').val();
	var nn_card_cvc_input_lang  = $('#nn_card_cvc_input').val();
	// CVC hint language
	var nn_card_cvc_hint_lang  = $('#nn_card_cvc_hint_text').val();
	var nn_card_error_text  = $('#nn_card_error_text').val();
        
     var textObj   = {
        card_holder: {
            labelText: nn_card_holder_label_lang,
            inputText: nn_card_holder_input_lang,
        },
        card_number: {
            labelText: nn_card_no_label_lang,
            inputText: nn_card_no_input_lang,
        },
        expiry_date: {
            labelText: nn_card_exp_label_lang,
            inputText: nn_card_exp_input_lang,
        },
        cvc: {
            labelText: nn_card_cvc_label_lang,
            inputText: nn_card_cvc_input_lang,
        },
        cvcHintText: nn_card_cvc_hint_lang,
        errorText: nn_card_error_text,
    };

	var iframe= jQuery('#xt_novalnet_cc_iframe')[0].contentWindow? jQuery('#xt_novalnet_cc_iframe')[0].contentWindow : jQuery('#xt_novalnet_cc_iframe')[0].contentDocument.defaultView;
	var requestObject = {
		callBack: 'createElements',
		customStyle: styleObject,
		customText : textObj
	};
	iframe.postMessage(JSON.stringify(requestObject), 'https://secure.novalnet.de');
	iframe.postMessage(JSON.stringify({callBack: 'getHeight'}), 'https://secure.novalnet.de');
}


jQuery(document).ready(function() {
	// Initiate the process for Creditcard iframe.
	jQuery('#xt_novalnet_cc_iframe').closest('form').submit(
			function(evt) {
				var novalnet_selected_payment = (jQuery("input[name='selected_payment']").attr('type') == 'hidden') ? jQuery("input[name='selected_payment']").val() : jQuery("input[name='selected_payment']:checked").val();

				if(novalnet_selected_payment == 'xt_novalnet_cc' && jQuery('#cc_pan_hash').val() == '' && jQuery('#xt_novalnet_cc_server_error_message').val() == '' && (jQuery('#nn_given_card_div').css('display') == undefined || jQuery('#nn_given_card_div').css('display') != 'block') ) {
					gethash();
					evt.preventDefault();
					evt.stopImmediatePropagation();
				}
            }
    );

    if (window.addEventListener) {
        // addEventListener works for all major browsers
        window.addEventListener('message', function(e) {
            addEvent(e);
        }, false);
    } else {
        // attachEvent works for IE8
        window.attachEvent('onmessage', function(e) {
            addEvent(e);
        });
    }
    // Function to handle Event Listener
    function addEvent(e) {
		if (e.origin === 'https://secure.novalnet.de') {
			var data = JSON.parse(e.data);
            if (data['callBack'] !== undefined && data['callBack'] == 'getHash') {
                if (data['error_message'] != undefined) {
                    jQuery('#xt_novalnet_cc_server_error_message').val(data['error_message']);
                    jQuery('#xt_novalnet_cc_iframe').closest('form').submit();
                } else {
                    jQuery('#cc_pan_hash').val(data['hash']);
                    jQuery('#nn_unique_id').val(data['unique_id']);
                    jQuery('#xt_novalnet_cc_server_error_message').val('');
                    jQuery('#xt_novalnet_cc_iframe').closest('form').submit();
                }
            } else if (data['callBack'] == 'getHeight') {
				console.log(data['contentHeight']);
                jQuery('#xt_novalnet_cc_iframe').height(data['contentHeight']);
            }
        }
    }
    // Function to retrieve hash from iframe
    function gethash() {
        var iframe= jQuery('#xt_novalnet_cc_iframe')[0].contentWindow ? jQuery('#xt_novalnet_cc_iframe')[0].contentWindow : jQuery('#xt_novalnet_cc_iframe')[0].contentDocument.defaultView;
        iframe.postMessage(JSON.stringify({callBack: 'getHash'}), 'https://secure.novalnet.de');
    }
});
