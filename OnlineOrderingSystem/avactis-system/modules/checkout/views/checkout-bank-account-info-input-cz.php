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
 * Checkout BankAccount-Info-Input view
 *
 * @package Checkout
 * @author Vadim Lyalikov
 */

class CheckoutBankAccountInfoInput extends Checkout_PersonInfo_InputCZ_Base
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
    	    'layout-file'        => 'bank-account-info-input-config.ini'
    	   ,'files' => array(
                 'InputSelect'   => TEMPLATE_FILE_SIMPLE
                ,'InputSelectError' => TEMPLATE_FILE_SIMPLE
                ,'InputSelectRequired' => TEMPLATE_FILE_SIMPLE
                ,'InputSelectRequiredError' => TEMPLATE_FILE_SIMPLE

                ,'InputText' => TEMPLATE_FILE_SIMPLE
                ,'InputTextError' => TEMPLATE_FILE_SIMPLE
                ,'InputTextRequired' => TEMPLATE_FILE_SIMPLE
                ,'InputTextRequiredError' => TEMPLATE_FILE_SIMPLE
    	    )
    	   ,'options' => array(
    	    )
    	);
    	return $format;
    }


    /**
     *  CheckoutBankAccountInput constructor
     */
    function CheckoutBankAccountInfoInput()
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
      $this->CHECKOUT_PREREQUISITE_NAME = Checkout::getAdditionalPrerequisiteName("bankAccountInfo", $payment_module_id);

      //         store      ,                          checkout prerequisite' .
      $this->CHECKOUT_STORE_BLOCK_NAME = "bank-account-info-input" . $parameter;

      //        html      ,                                   Person Info.
      $this->HTML_TAGS_PREFIX = "BankAccountInfo" . $parameter;

      //        html      ,                                   Person Info.
      //     .                  Container         .
      $this->HTML_TAGS_PREFIX_WITHOUT_MODULE_UID = "BankAccountInfo";

      //                 .                          . (e.g. CheckoutCustomerInfoInput)
      $this->BLOCK_TAG_NAME = "CheckoutBankAccountInfoInput" . $parameter;

      //                                                      ,
      //                                                       :
      $this->{get_parent_class(__CLASS__)}();
    }
    /**#@-*/

    function output()
    {
        $parameters = func_get_args();
        $this->asc_ctor($parameters[0]);

        $value = parent::output();

        return $value;
    }
//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */
    /**#@-*/
}
?>