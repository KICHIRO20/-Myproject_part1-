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
 *CheckoutPaymentModuleOfflineCCInput view
 *
 * @package PaymentModuleOfflineCC
 * @author Alexander Girin
 */
class CheckoutPaymentModuleOfflineCCInput
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
    	    'layout-file'        => "checkout-payment-module-offline-cc-input-config.ini" //; $this->LayoutFile
    	   ,'files' => array(
    	        'InputContainer'   => TEMPLATE_FILE_SIMPLE
    	    )
    	   ,'options' => array(
    	    )
    	);
    	return $format;
    }

    /**
     *  CheckoutPaymentModuleOfflineCCInput constructor
     */
    function CheckoutPaymentModuleOfflineCCInput()
    {
        global $application;

        $this->MessageResources = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");

        $this->HTML_LOCAL_TAGS_PREFIX = "Local_";
        #
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors($this->BlockTemplateName))
        {
            $this->NoView = true;
        }
        if ($application->getCurrentProtocol() == "http")
        {
            $this->NoView = "HTTPS_ERROR";
        }
    }

    /**
     *
     */
    function output()
    {
        global $application;

        $ModuleInfo = modApiFunc($this->ModuleAPIClassName, "getInfo");
        $ModuleName = $ModuleInfo['Name'];

        if ($this->NoView)
        {
            if ($this->NoView == "HTTPS_ERROR")
            {
                $msg = &$application->getInstance("MessageResources");
                return $msg->getMessage(new ActionMessage(array("HTTPS_ERROR", $ModuleName)));
            }
            return "";
        }

        global $application;
        $msg = &$application->getInstance("MessageResources");
        loadCoreFile('html_form.php');
        $html = new HtmlForm();

        $this->templateFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate($this->BlockTemplateName);
        $this->templateFiller->setTemplate($this->template);

        $ModuleInfo = modApiFunc($this->ModuleAPIClassName, "getInfo");
        $ModuleName = $ModuleInfo['Name'];



        $this->_Template_Contents = array($this->HTML_LOCAL_TAGS_PREFIX . 'FormMethodIdFieldName' => ""
                                         ,$this->HTML_LOCAL_TAGS_PREFIX . 'PaymentMethodID' => ""
                                         ,$this->HTML_LOCAL_TAGS_PREFIX . 'PaymentMethodName' => ""
                                         ,$this->HTML_LOCAL_TAGS_PREFIX . 'PaymentMethodIsChecked' => ""
                                         ,$this->HTML_LOCAL_TAGS_PREFIX . 'CreditCardInfo' => ""
                                         ,$this->HTML_LOCAL_TAGS_PREFIX . 'ErrorMessage' => ""
										 ,$this->HTML_LOCAL_TAGS_PREFIX . 'CreditCardInfoInput' => ""
                                       );

        $application->registerAttributes($this->_Template_Contents);
        $retval = $this->templateFiller->fill("InputContainer");


//        modApiFunc("Payment_Module_Offline_CC", "clearResponseErrors");
        modApiFunc("Payment_Module_Offline_CC", "saveState");
        return $retval;
    }

    function getTag($tag)
    {
        global $application;
        loadCoreFile('html_form.php');
        $html = new HtmlForm();

        $value = null;
        switch ($tag)
        {
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

            case $this->HTML_LOCAL_TAGS_PREFIX . 'CreditCardInfoInput':
                $pm_uuid = call_user_func(array($this->ModuleAPIClassName, "getUid"));
                $value = getCheckoutCreditCardInfoInput($pm_uuid);
                break;

            case $this->HTML_LOCAL_TAGS_PREFIX . 'ErrorMessage':
//        ?
                $response = array();//modApiFunc("Payment_Module_Offline_CC", "getResponse");
                $value = "<b>".(isset($response['Errors']['ErrorCode']) && $response['Errors']['ErrorCode']?
                               ($response['Errors']['ErrorCode'].":"):"")
                              .(isset($response['Errors']['ShortMessage']) && $response['Errors']['ShortMessage']?
                               (" ".$response['Errors']['ShortMessage']):"")."</b>"
                              .(isset($response['Errors']['LongMessage']) && $response['Errors']['LongMessage']?
                               (" ".$response['Errors']['LongMessage']):"");
                break;

            default:
//                $value = $this->_Template_Contents[$tag];
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

    var $ModuleAPIClassName = "Payment_Module_Offline_CC";
    var $BlockTemplateName = "CheckoutPaymentModuleOfflineCCInput";
    var $LayoutFile = "checkout-payment-module-offline-cc-input-config.ini";

    /**
     * A html tag prefix, added to the names of all Person Info local attributes.
     */
    var $HTML_LOCAL_TAGS_PREFIX;// = "Local_";
    /**#@-*/
}
?>