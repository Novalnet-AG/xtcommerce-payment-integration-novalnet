/*
 * Novalnet Global Configuration Script
 * author    Novalnet AG
 * copyright 2019 Novalnet 
 * license   https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * link      https://www.novalnet.de
*/

/* Process product activation key */
(function($){
	novalnet_config = {
		
		process: function () {
			
			var novalnet_data = novalnet_parent.get_details();
			$('[name='+novalnet_data.activation_key+']').closest('.x-form-item').before('<div class=x-form-item tabindex=-1>'+novalnet_data.admin_link+'</div>');
			
			if($('[name='+novalnet_data.activation_key+']') != undefined ) {
				if($('[name='+novalnet_data.payment_logo_key+']') != undefined ) {
						$('[name='+novalnet_data.payment_logo_key+']').closest('.x-form-item').before('<div class=x-form-item tabindex=-1>'+novalnet_data.logo_link+'</div>');
				}

				if($('[name='+novalnet_data.status_key+']') != undefined ) {
					$('[name='+novalnet_data.status_key+']').closest('.x-form-item').before('<div class=x-form-item tabindex=-1>'+novalnet_data.status_link+'</div>');
				}

				if($('[name='+novalnet_data.callback_management_key+']') != undefined ) {
					$('[name='+novalnet_data.callback_management_key+']').closest('.x-form-item').before('<div class=x-form-item tabindex=-1>'+novalnet_data.callback_management_link+'</div>');
				}
				
				$('[name='+novalnet_data.vendor_id+']').prop('readonly', true);
				$('[name='+novalnet_data.auth_code+']').prop('readonly', true);
				$('[name='+novalnet_data.product_id+']').prop('readonly', true);
				$('[name='+novalnet_data.access_key+']').prop('readonly', true);
				
				var saved_tariff_id = $('[name='+novalnet_data.tariff_id+']').val();
				novalnet_config.novalnet_refill(saved_tariff_id, novalnet_data.store_id);
				
				$(document).on('change','[name='+novalnet_data.activation_key+']',function(){
					var saved_tariff_id = $('#'+novalnet_data.store_id+'').val();
					novalnet_config.novalnet_refill(saved_tariff_id);
				});
			}
		},
		
		/* Process to fill the vendor details */	
		novalnet_refill : function (saved_tariff_id) 
		{
			var novalnet_data = novalnet_parent.get_details();
				hash = $('[name='+novalnet_data.activation_key+']'),
				conn = new Ext.data.Connection();
			
			if(hash != undefined && $.trim(hash.val()) != '') {
				conn.request({
					url: 'adminHandler.php',
					method:'POST',
					params: {
						plugin: 'xt_novalnet_config',
						load_section: 'xt_novalnet_operations',
						pg: 'autoConfigHash',
						hash: hash.val()
					},
				
					success: function(responseObject){
						var response = Ext.decode(responseObject.responseText);
						if (response.error != undefined){
							Ext.MessageBox.alert('Error', response.error);
							novalnet_config.novalnet_null_basic_params();
							return false;
						} else if (response.status != undefined && response.status == 100) {
							novalnet_config.novalnet_config_hash_response( response, saved_tariff_id );
						} else if (response.status_desc != undefined){
							Ext.MessageBox.alert('Error', response.status_desc);
							novalnet_config.novalnet_null_basic_params();
							return false;
						}
					},
					failure: function(responseObject){
						var error = Ext.decode(responseObject.responseText);
						var title = 'Error';
						var msg = 'No Details available';
						Ext.MessageBox.alert(title,msg);
					}
				});
			} else {
				novalnet_config.novalnet_null_basic_params();
			}
		},
		
		novalnet_null_basic_params : function ()
		{
			var novalnet_data = novalnet_parent.get_details();
			$('[name='+novalnet_data.vendor_id+']').val('');
			$('[name='+novalnet_data.auth_code+']').val('');
			$('[name='+novalnet_data.product_id+']').val('');
			$('[name='+novalnet_data.access_key+']').val('');
			$('[name='+novalnet_data.tariff_id+']').find('option').remove();
			$('[name='+novalnet_data.tariff_id+']').replaceWith('<select id='+novalnet_data.tariff_id+' name='+novalnet_data.tariff_id+'><option>'+novalnet_data.select_text+'</option></select>');
		},

		novalnet_config_hash_response : function ( data, saved_tariff_id )
		{
			var novalnet_data = novalnet_parent.get_details();
			var tariff = data.tariff;
			if(tariff != undefined){
				$('[name='+novalnet_data.tariff_id+']').replaceWith('<select id='+novalnet_data.tariff_id+' name='+novalnet_data.tariff_id+'><option>'+novalnet_data.select_text+'</option></select>');
				
				 $.each(tariff, function( index, value ) {
					var tariff_val = value.type + '-' + index;
					$('[name='+novalnet_data.tariff_id+']').append($('<option>', {
						 value: $.trim(tariff_val),
						 text: $.trim(value.name)
					}));
					
					// Assign tariff id.
					if (saved_tariff_id != undefined && saved_tariff_id == tariff_val) {
						 $('[name='+novalnet_data.tariff_id+']').val(tariff_val);
					}
				});
			} 
			
			// Assign vendor details.
			if(data.vendor != undefined) {
				$('[name='+novalnet_data.vendor_id+']').val(data.vendor);
			}
			if(data.auth_code != undefined) {
				$('[name='+novalnet_data.auth_code+']').val(data.auth_code);
			}
			if(data.product != undefined) {
				$('[name='+novalnet_data.product_id+']').val(data.product);
			}
			if(data.access_key != undefined) {
				$('[name='+novalnet_data.access_key+']').val(data.access_key);
			}
		}
		
	};

	$( document ).ready(function () {
		novalnet_config.process();
	});

})(jQuery);
