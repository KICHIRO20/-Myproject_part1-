<?php
/***********************************************************************
| Avactis (TM) Shopping Cart software developed by HBWSL.
| http://www.avactis.com
| -----------------------------------------------------------------------
| All source codes & content (c) Copyright 2004-2010, HBWSL.
| unless specifically noted otherwise.
| =============================================
| This source code is released under the Avactis License Agreement.
| The latest version of this license can be found here:
| http://www.avactis.com/license.php
|
| By using this software, you acknowledge having read this license agreement
| and agree to be bound thereby.
|
 ***********************************************************************/
?><?php

loadModuleFile('checkout/abstract/person_info_input_cz.php');

/**
 * Checkout Shipping-Info-Input view.
 *
 * @package Checkout
 * @author Vadim Lyalikov
 */
class CheckoutShippingInfoInput extends Checkout_PersonInfo_InputCZ_Base
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Gets a template format for the given view.
     */
    function getTemplateFormat()
    {
    	$format = array(
    	    'layout-file'        => 'shipping-info-input-config.ini'
    	   ,'files' => array(
                 'InputContainer'   => TEMPLATE_FILE_SIMPLE
                ,'InputSelect'   => TEMPLATE_FILE_SIMPLE
                ,'InputSelectError' => TEMPLATE_FILE_SIMPLE
                ,'InputSelectRequired' => TEMPLATE_FILE_SIMPLE
                ,'InputSelectRequiredError' => TEMPLATE_FILE_SIMPLE

                ,'InputText' => TEMPLATE_FILE_SIMPLE
                ,'InputTextError' => TEMPLATE_FILE_SIMPLE
                ,'InputTextRequired' => TEMPLATE_FILE_SIMPLE
                ,'InputTextRequiredError' => TEMPLATE_FILE_SIMPLE

                ,'Textarea' => TEMPLATE_FILE_SIMPLE
                ,'TextareaError' => TEMPLATE_FILE_SIMPLE
                ,'TextareaRequired' => TEMPLATE_FILE_SIMPLE
                ,'TextareaRequiredError' => TEMPLATE_FILE_SIMPLE
    	    )
    	   ,'options' => array(
    	    )
    	);
    	return $format;
    }

    /**
     *  CheckoutShippingInfoInput constructor.
     */
    function CheckoutShippingInfoInput()
    {
      //The checkout prerequisite name, appropriate to the current Person Info (e.g. CustomerInfo).
      $this->CHECKOUT_PREREQUISITE_NAME = "shippingInfo";

      //The store block name, appropriate to the given checkout prerequisite.
      $this->CHECKOUT_STORE_BLOCK_NAME = "shipping-info-input";

      //A html tag prefix, appropriate to the attributes of the given Person Info.
      $this->HTML_TAGS_PREFIX = "ShippingInfo";

      //The block tag name. It matches the class name. (e.g. CheckoutCustomerInfoInput)
      $this->BLOCK_TAG_NAME = "CheckoutShippingInfoInput";

      //The parent consructor is called at the end of the method, because to work
      // correctly, it is necessary to set up values of some variables:
      $this->{get_parent_class(__CLASS__)}();
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */
    /**#@-*/
}
?>