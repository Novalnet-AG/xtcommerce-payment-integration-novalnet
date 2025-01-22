/*
 * Novalnet Global Configuration Script
 * author    Novalnet AG
 * copyright 2019 Novalnet 
 * license   https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * link      https://www.novalnet.de
*/

$(document).ready(
    function() {
    if($(":input[name*='XT_NOVALNET_INVOICE_ENABLE_GUARANTEE_PAYMENT']") != undefined) {
        var name = $(":input[name*='XT_NOVALNET_INVOICE_ENABLE_GUARANTEE_PAYMENT']").attr('name');
        if($('#'+name+'_title').attr('class') == undefined) {
            $(":input[name*='XT_NOVALNET_INVOICE_ENABLE_GUARANTEE_PAYMENT']").closest('.x-form-item').before("<div class='x-form-item' id="+name+"_title tabindex='-1'>"+$('#xt_novalnet_invoice_guarantee_payment_title').val()+ $('#xt_novalnet_invoice_guarantee_payment_hint').val() +"</div>");
        }
    }

    if($(":input[name*='XT_NOVALNET_SEPA_ENABLE_GUARANTEE_PAYMENT']") != undefined) {
        var name = $(":input[name*='XT_NOVALNET_SEPA_ENABLE_GUARANTEE_PAYMENT']").attr('name');
        if($('#'+name+'_title').attr('class') == undefined) {
            $(":input[name*='XT_NOVALNET_SEPA_ENABLE_GUARANTEE_PAYMENT']").closest('.x-form-item').before("<div class='x-form-item' tabindex='-1' id="+name+"_title>"+$('#xt_novalnet_sepa_guarantee_payment_title').val()+ $('#xt_novalnet_sepa_guarantee_payment_hint').val() +"</div>");
        }
    }
    if($(":input[name*='XT_NOVALNET_PAYPAL_SHOPPING_TYPE']") != undefined) {
        var name = $(":input[name*='XT_NOVALNET_PAYPAL_SHOPPING_TYPE']").attr('name');
        if($('#'+name+'_hint').attr('class') == undefined) {
            $(":input[name*='XT_NOVALNET_PAYPAL_ORDER_STATUS_NEW']").closest('.x-form-item').after("<div class='x-form-item' tabindex='-1' id="+name+"_hint>"+$('#xt_novalnet_paypal_reference_transaction_hint').val() +"</div>");
        }
    }
    
    if($(":input[name*='XT_NOVALNET_INVOICE_ENABLE_GUARANTEE_PAYMENT']") != undefined) {
        if($(":input[name*='XT_NOVALNET_INVOICE_ENABLE_GUARANTEE_PAYMENT']:checked").length == 0) {
            $(":input[name*='XT_NOVALNET_INVOICE_GUARANTEE_ORDER_STATUS'], :input[name*='XT_NOVALNET_INVOICE_GUARANTEE_PAYMENT_MIN_AMOUNT'], :input[name*='XT_NOVALNET_INVOICE_ENABLE_FORCE_NON_GUARANTEE_PAYMENT']").closest('.x-form-item ').css("display", "none");
        }
        $(":input[name*='XT_NOVALNET_INVOICE_ENABLE_GUARANTEE_PAYMENT']").on('click',function() {
            if($(":input[name*='XT_NOVALNET_INVOICE_ENABLE_GUARANTEE_PAYMENT']:checked").length > 0) {
                $(":input[name*='XT_NOVALNET_INVOICE_GUARANTEE_ORDER_STATUS'], :input[name*='XT_NOVALNET_INVOICE_GUARANTEE_PAYMENT_MIN_AMOUNT'], :input[name*='XT_NOVALNET_INVOICE_ENABLE_FORCE_NON_GUARANTEE_PAYMENT']").closest('.x-form-item ').css("display", "block");
            } else {
                $(":input[name*='XT_NOVALNET_INVOICE_GUARANTEE_ORDER_STATUS'], :input[name*='XT_NOVALNET_INVOICE_GUARANTEE_PAYMENT_MIN_AMOUNT'], :input[name*='XT_NOVALNET_INVOICE_ENABLE_FORCE_NON_GUARANTEE_PAYMENT']").closest('.x-form-item ').css("display", "none");
            }
        });
    }
    
    if($(":input[name*='XT_NOVALNET_SEPA_ENABLE_GUARANTEE_PAYMENT']") != undefined) {
        if($(":input[name*='XT_NOVALNET_SEPA_ENABLE_GUARANTEE_PAYMENT']:checked").length == 0) {
            $(":input[name*='XT_NOVALNET_SEPA_GUARANTEE_ORDER_STATUS'], :input[name*='XT_NOVALNET_SEPA_GUARANTEE_PAYMENT_MIN_AMOUNT'], :input[name*='XT_NOVALNET_SEPA_ENABLE_FORCE_NON_GUARANTEE_PAYMENT']").closest('.x-form-item ').css("display", "none");
        }
        $(":input[name*='XT_NOVALNET_SEPA_ENABLE_GUARANTEE_PAYMENT']").on('click',function() {
            if($(":input[name*='XT_NOVALNET_SEPA_ENABLE_GUARANTEE_PAYMENT']:checked").length > 0) {
                $(":input[name*='XT_NOVALNET_SEPA_GUARANTEE_ORDER_STATUS'], :input[name*='XT_NOVALNET_SEPA_GUARANTEE_PAYMENT_MIN_AMOUNT'], :input[name*='XT_NOVALNET_SEPA_ENABLE_FORCE_NON_GUARANTEE_PAYMENT']").closest('.x-form-item ').css("display", "block");
            } else {
                $(":input[name*='XT_NOVALNET_SEPA_GUARANTEE_ORDER_STATUS'], :input[name*='XT_NOVALNET_SEPA_GUARANTEE_PAYMENT_MIN_AMOUNT'], :input[name*='XT_NOVALNET_SEPA_ENABLE_FORCE_NON_GUARANTEE_PAYMENT']").closest('.x-form-item ').css("display", "none");
            }
        });
    }
});
