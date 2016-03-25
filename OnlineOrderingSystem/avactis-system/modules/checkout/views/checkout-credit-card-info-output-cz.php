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
 * Checkout CreditCard-Info-Output view
 *
 * @package Checkout
 * @author Vadim Lyalikov
 */
class CheckoutCreditCardInfoOutput extends Checkout_PersonInfo_OutputCZ_Base
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     *                                                     .
     */
    function getTemplateFormat()
    {
    	$format = array(
    	    'layout-file'        => 'credit-card-info-output-config.ini'
    	   ,'files' => array(
    	        'OutputItem'               => TEMPLATE_FILE_SIMPLE
    	    )
    	   ,'options' => array(
    	    )
    	);
    	return $format;
    }

    /**
     *  CheckoutCreditCardInfoOutput constructor
     */
    function CheckoutCreditCardInfoOutput()
    {
        /**
         *                                          View                    .
         *                                    ,              ,
         *         output (                "       "
         *                           output()).                   output
         *        ,                                   - asc_ctor()
         */
    }

    function asc_ctor($parameter)
    {
      loadCoreFile('UUIDUtils.php');
      $payment_module_id = $parameter;

      //                    javaScript            .
      $parameter = UUIDUtils::convert("minuses_and_capitals", "js", $parameter);

      //    checkout prerequisite' ,                           Person Info (e.g. CustomerInfo)
      $this->CHECKOUT_PREREQUISITE_NAME = Checkout::getAdditionalPrerequisiteName("creditCardInfo", $payment_module_id);

      //         store      ,                          checkout prerequisite' .
      $this->CHECKOUT_STORE_BLOCK_NAME = "credit-card-info-output" . $parameter;

      //        html      ,                                   Person Info.
      $this->HTML_TAGS_PREFIX = "CreditCardInfo" . $parameter;

      //        html      ,                                   Person Info.
      //     .                  Container         .
      $this->HTML_TAGS_PREFIX_WITHOUT_MODULE_UID = "CreditCardInfo";

      //                 .                          . (e.g. CheckoutCustomerInfoOutput)
      $this->BLOCK_TAG_NAME = "CheckoutCreditCardInfoOutput" . $parameter;

      //                                                      ,
      //                                                       :
      $this->{get_parent_class(__CLASS__)}();
    }

    function output()
    {
        $parameters = func_get_args();
        $this->asc_ctor($parameters[0]);

        //
        modApiFunc("Checkout", "decrypt_prerequisite_with_checkout_cz_blowfish_key", $this->CHECKOUT_PREREQUISITE_NAME);

        $value = parent::output();

        //
        modApiFunc("Checkout", "encrypt_prerequisite_with_checkout_cz_blowfish_key", $this->CHECKOUT_PREREQUISITE_NAME);

        return $value;
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