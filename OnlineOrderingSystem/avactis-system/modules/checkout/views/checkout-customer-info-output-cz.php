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

loadModuleFile('checkout/abstract/person_info_output_cz.php');

/**
 * Checkout Customer-Info-Output view.
 *
 * @package Checkout
 * @author Vadim Lyalikov
 */
class CheckoutCustomerInfoOutput extends Checkout_PersonInfo_OutputCZ_Base
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
//    function getTemplateFormat()
//    {
//    	$format = array(
//    	    'layout-file'        => 'customer-info-output-config.ini',
//    	    'files' => array(
//    	    )
//    	   ,'options' => array(
//    	    )
//    	);
//    	return $format;
//    }

    /**
     *  CheckoutCustomerInfoOutput constructor.
     */
    function CheckoutCustomerInfoOutput()
    {
      //The checkout prerequisite name, appropriate to the current Person Info (e.g. CustomerInfo).
      $this->CHECKOUT_PREREQUISITE_NAME = "customerInfo";

      //The store block name, appropriate to the given checkout prerequisite.
      $this->CHECKOUT_STORE_BLOCK_NAME = "customer-info-output";

      //A html tag prefix, appropriate to the attributes of the given Person Info.
      $this->HTML_TAGS_PREFIX = "CustomerInfo";

      //The block tag name. It matches the class name. (e.g. CheckoutCustomerInfoInput)
      $this->BLOCK_TAG_NAME = "CheckoutCustomerInfoOutput";

      //The parent consructor is called at the end of the method, because to work
      // correctly, it is necessary to set up values of some variables:
      $this->{get_parent_class(__CLASS__)}();
    }

    function output()
    {
        return '';
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