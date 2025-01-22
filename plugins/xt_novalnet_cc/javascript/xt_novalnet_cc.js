function load_novalnet_payment_iframe() {
    var client_key = jQuery("#nn_client_key").val();

    NovalnetUtility.setClientKey(client_key);
    var configurationObject = {

        // You can handle the process here, when specific events occur.
        callback: {

            // Called once the pan_hash (temp. token) created successfully.
            on_success: function (data) {
                document.getElementById('cc_pan_hash').value = data['hash'];
                document.getElementById('nn_unique_id').value = data['unique_id'];
                document.getElementById('nn_cc_do_redirect').value = data['do_redirect'];
                $('#xt_novalnet_cc_iframe').closest('form').submit();
                return true;
            },
            on_error: function (data) {
                if (undefined !== data['error_message']) {
                    jQuery('#xt_novalnet_cc_server_error_message').val(data['error_message']);
                    return false;
                }
            },
            on_show_overlay: function (data) {
                document.getElementById('xt_novalnet_cc_iframe').classList.add("novalnet-challenge-window-overlay");
            },
            on_hide_overlay: function (data) {
                document.getElementById('xt_novalnet_cc_iframe').classList.remove("novalnet-challenge-window-overlay");
            }
        },
        iframe: {
            id: "xt_novalnet_cc_iframe",
            inline: $('#nn_cc_inline_form').val(),
            style: {
                container: jQuery('#nn_standard_css').val(),
                input: jQuery('#nn_standard_input').val(),
                label: jQuery('#nn_standard_label').val(),
            },
            text: {
                error: jQuery('#nn_card_details_error_text').val(),
                card_holder: {
                    label: $('#nn_card_holder_text').val(),
                    place_holder: $('#nn_card_holder_input').val(),
                    error: "Please enter the valid card holder name"
                },
                card_number: {
                    label: $('#nn_card_no_text').val(),
                    place_holder: $('#nn_card_no_input').val(),
                    error: "Please enter the valid card number"
                },
                expiry_date: {
                    label: $('#nn_card_exp_date_text').val(),
                    error: "Please enter the valid expiry month / year in the given format"
                },
                cvc: {
                    label: $('#nn_card_cvc_text').val(),
                    place_holder: $('#nn_card_cvc_input').val(),
                    error: "Please enter the valid CVC/CVV/CID"
                }
            }
        },
        customer: {
            first_name: jQuery("#nn_first_name").val(),
            last_name: jQuery("#nn_last_name").val(),
            email: jQuery("#nn_email").val(),
            billing: {
                street: jQuery("#nn_billing_street").val(),
                city: jQuery("#nn_billing_city").val(),
                zip: jQuery("#nn_billing_zip").val(),
                country_code: jQuery("#nn_billing_country_code").val()
            },
            shipping: {
                same_as_billing: jQuery("#nn_same_as_billing").val(),
                first_name: jQuery("#nn_shipping_first_name").val(),
                last_name: jQuery("#nn_shipping_last_name").val(),
                email: jQuery("#nn_shipping_enail").val(),
                street: jQuery("#nn_shipping_street").val(),
                city: jQuery("#nn_shipping_city").val(),
                zip: jQuery("#nn_shipping_zip").val(),
                country_code: jQuery("#nn_shipping_country_code").val(),
            },
        },
        transaction: {
            amount: jQuery("#transaction_amount").val(),
            currency: jQuery("#shop_currency").val(),
            test_mode: jQuery("#nn_test_mode").val()
        },
        custom: {
            lang: jQuery('#shop_lang').val(),
        }
    };

    // Create the Credit Card form
    NovalnetUtility.createCreditCardForm(configurationObject);
}

function loadJQueryAndExecute(callback) {
    if (typeof jQuery !== 'undefined') {
        // If jQuery is already defined
        $(document).ready(callback);
    } else {
        const script = document.createElement('script');
        script.src = 'plugins/xt_novalnet_cc/javascript/xt_novalnet_jquery.js'; // Link to the jQuery CDN
        script.type = 'text/javascript';
        script.onload = function () {
            $(document).ready(callback); // Run the callback once jQuery is loaded and the DOM is ready
        };
        document.head.appendChild(script);
    }
}


loadJQueryAndExecute(function () {
	
	$('#nn_new_card_link').click(function(event){
        $('#nn_given_card_div').hide();
        $('#nn_new_card_div').show();
        $('#xt_novalnet_cc_one_click_process').val('');
        load_novalnet_payment_iframe();
        event.stopImmediatePropagation();

    });

    // Display one click new card fields.
    $('#nn_given_card_link').click(function(event){
        $('#nn_new_card_div').hide();
        $('#nn_given_card_div').show();
        $('#xt_novalnet_cc_one_click_process').val('1');
        event.stopImmediatePropagation();
    });
    $("#xt_novalnet_cc_iframe").closest('li').click(function() {
        load_novalnet_payment_iframe();
    });
    

    load_novalnet_payment_iframe();
    // Initiate the process for Creditcard iframe.
    jQuery('#xt_novalnet_cc_iframe').closest('form').submit(
        function (evt) {
            var novalnet_selected_payment = (jQuery("input[name='selected_payment']").attr('type') == 'hidden') ? jQuery("input[name='selected_payment']").val() : jQuery("input[name='selected_payment']:checked").val();

            if (novalnet_selected_payment == 'xt_novalnet_cc' && jQuery('#cc_pan_hash').val() == '' && jQuery('#xt_novalnet_cc_server_error_message').val() == '' && (jQuery('#nn_given_card_div').css('display') == undefined || jQuery('#nn_given_card_div').css('display') != 'block')) {
                NovalnetUtility.getPanHash();
                evt.preventDefault();
                evt.stopImmediatePropagation();

            }
        }
    );

});  
