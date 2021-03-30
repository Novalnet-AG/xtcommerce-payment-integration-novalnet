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


global $xtLink, $page, $page_data;
if($page->page_name == 'cart') {
echo " <a style='float:right;margin-right: 15px;' class='btn btn-success' href=".$xtLink->_link(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL')).">
                        ".BUTTON_CHECKOUT."
                    </a>";
				}
?>
