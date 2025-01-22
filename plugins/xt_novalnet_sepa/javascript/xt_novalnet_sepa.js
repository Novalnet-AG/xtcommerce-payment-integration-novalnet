/*
 * Novalnet Direct Debit SEPA Script
 * author    Novalnet AG
 * copyright 2019 Novalnet 
 * license   https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * link      https://www.novalnet.de
*/
 
/* Initiate Direct Debit SEPA process */
(function($){

	novalnet_sepa = {
		
		process : function() {
			$("#novalnet-about-mandate").hide();
			$( '#payment-submit' ).on( 'click', function( event ) {
				
				if( ! $( '#xt_novalnet_sepa_one_click_process' ).val() && novalnet_sepa.check_payment( 'xt_novalnet_sepa' )) {
					if( ! novalnet_sepa.perform_sepa_iban_validation( event ) ) {
						event.stopImmediatePropagation();
						alert(jQuery('#sepa_invalid_account_error').val());
						return false;
					}
				}	
			} );
			
		},
		
		/* Initiate Direct Debit SEPA iban process */
		perform_sepa_iban_validation : function () {
			var account_holder         = $.trim( $( '#novalnet_sepa_account_holder' ).val() ),
			iban                   = $.trim( $( '#novalnet_sepa_iban' ).val() );
			$( '#novalnet_sepa_iban' ).val( iban );
			if ( '' === account_holder || '' === iban ) {
				return false;
			} else {
				return true;
			}
		},
		
		/* Toggle SEPA mandate */
		sepa_mandate_toggle_process : function () {
			$("#novalnet-about-mandate").toggle();
		},
		
		/* Check Selected Payment */
		check_payment : function ( payment ) {
			return payment === $( 'input[name=selected_payment]:checked' ).val();
		},


	};

	$( document ).ready(function () {
		novalnet_sepa.process();
	});
	

})(jQuery);
