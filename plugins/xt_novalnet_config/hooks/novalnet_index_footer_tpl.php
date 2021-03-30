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

if(isset($_GET['page_action']) && $_GET['page_action'] == 'pay' && strpos($_SESSION['selected_payment'], 'novalnet') !== false) { ?>
    <script>
        jQuery('#content input[type=submit]').click(function ()
        {
            setTimeout(function(){
                jQuery('#content input[type=submit]').attr(
                    'disabled', 'disabled'
                ).css(
                    'opacity', '0.2'
                );
            }, 1000);
        });
    </script>
<?php }
