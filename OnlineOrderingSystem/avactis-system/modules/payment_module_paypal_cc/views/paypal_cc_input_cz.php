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

/**
 * CheckoutPaymentModulePaypalCCInput view.
 *
 * @package PaymentModulePaypalCC
 * @author Vadim Lyalikov
 */
class CheckoutPaymentModulePaypalCCInput
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
    	    'layout-file'        => "checkout-payment-module-paypal-cc-input-config.ini" //; $this->LayoutFile
    	   ,'files' => array(
    	        'InputContainer'               => TEMPLATE_FILE_SIMPLE
    	    )
    	   ,'options' => array(
    	    )
    	);
    	return $format;
    }

    /**
     *  CheckoutPaymentModulePaypalCCInput constructor.
     */
    function CheckoutPaymentModulePaypalCCInput()
    {
        global $application;
        $this->HTML_LOCAL_TAGS_PREFIX = "Local_";
        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors($this->BlockTemplateName))
        {
            $this->NoView = true;
        }
    }

    /**
     *
     */
    function output()
    {
        global $application;

//        $application->registerAttributes(array('PaymentHTMLModuleDescription' => $this->getTag("PaymentHTMLModuleDescription"),
//                                               'InputField' => $this->getTag("InputField")));

        $application->registerAttributes(array($this->HTML_LOCAL_TAGS_PREFIX . 'FormMethodIdFieldName' => "",
                                               $this->HTML_LOCAL_TAGS_PREFIX . 'PaymentMethodID' => "",
                                               $this->HTML_LOCAL_TAGS_PREFIX . 'PaymentMethodName' => "",
                                               $this->HTML_LOCAL_TAGS_PREFIX . 'PaymentMethodIsChecked' => ""));


        $this->templateFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate($this->BlockTemplateName);
        $this->templateFiller->setTemplate($this->template);

        $retval = $this->templateFiller->fill("InputContainer");
        return $retval;
    }

    function getTag($tag)
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-paypal-messages", "AdminZone");
        global $application;
        $value = null;
        switch ($tag)
        {
//            case $this->HTML_LOCAL_TAGS_PREFIX .'PaymentHTMLModuleDescription':
//                $value = $obj->getMessage('MODULE_PAYMENT_PAYPAL_PAYPAL_IPN_HTML_DESCRIPTION_CZ');
//                break;

            case $this->HTML_LOCAL_TAGS_PREFIX .'FormMethodIdFieldName':
                $value = "paymentModule[method_code]";
                break;

            case $this->HTML_LOCAL_TAGS_PREFIX . 'PaymentMethodID':
                $ModuleInfo = modApiFunc($this->ModuleAPIClassName, "getInfo");
                $value = $ModuleInfo['GlobalUniquePaymentModuleID'];
                break;

            case $this->HTML_LOCAL_TAGS_PREFIX . 'PaymentMethodName':
                $ModuleInfo = modApiFunc($this->ModuleAPIClassName, "getInfo");
                $value = $ModuleInfo['Name'];
                break;

            case $this->HTML_LOCAL_TAGS_PREFIX . 'PaymentMethodIsChecked':
                $ModuleInfo = modApiFunc($this->ModuleAPIClassName, "getInfo");
                $pm_uuid = $ModuleInfo['GlobalUniquePaymentModuleID'];
                $checked = modApiFunc("Checkout", "isPaymentModuleChecked", $pm_uuid);
                $value = ($checked === true ? 'checked="checked"' : "");
                break;

            default:
        		break;
        }
        return $value;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $ModuleAPIClassName = "Payment_Module_Paypal_CC";
    var $BlockTemplateName = "CheckoutPaymentModulePaypalCCInput";
    var $LayoutFile = "checkout-payment-module-paypal-cc-input-config.ini";

    /**
     * A html tag prefix, added to the names of all Person Info local attributes.
     */
    var $HTML_LOCAL_TAGS_PREFIX;// = "Local_";
    /**#@-*/
}
?>