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
 * CheckoutPaymentModuleOfflineCCOutput view
 *
 * @package PaymentModuleOfflineCC
 * @author Alexander Girin
 */
class CheckoutPaymentModuleOfflineCCOutput
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
    	    'layout-file'        => "checkout-payment-module-offline-cc-output-config.ini" //; $this->LayoutFile
    	   ,'files' => array(
    	        'OutputContainer'               => TEMPLATE_FILE_SIMPLE
    	    )
    	   ,'options' => array(
    	    )
    	);
    	return $format;
    }

    /**
     *  CheckoutPaymentModuleOfflineCCOutput constructor
     */
    function CheckoutPaymentModuleOfflineCCOutput()
    {
        global $application;
//        $this->MessageResources = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");

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
        if ($this->NoView)
        {
            return "";
        }

        global $application;
        $msg = &$application->getInstance("MessageResources");

        $this->templateFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate($this->BlockTemplateName);
        $this->templateFiller->setTemplate($this->template);

        $ModuleInfo = modApiFunc($this->ModuleAPIClassName, "getInfo");
        $ModuleName = $ModuleInfo['Name'];

        $this->_Template_Contents = array(
                                          "Local_PaymentMethodName" => ""
                                         ,'Local_CreditCardInfoOutput' => ""
                                         );
        $application->registerAttributes($this->_Template_Contents);
        $retval = $this->templateFiller->fill("OutputContainer");
        return $retval;
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {

            case 'Local_PaymentMethodName':
                $ModuleInfo = modApiFunc($this->ModuleAPIClassName, "getInfo");
                $value = $ModuleInfo['Name'];
                break;

            case 'Local_CreditCardInfoOutput':
                $moduleSettings = modApiFunc($this->ModuleAPIClassName, "getSettings");
                if ($application->getCurrentProtocol() != "http")
                {
                    $pm_uuid = call_user_func(array($this->ModuleAPIClassName, "getUid"));
                    $value = getCheckoutCreditCardInfoOutput($pm_uuid);
                }
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
    var $BlockTemplateName = "CheckoutPaymentModuleOfflineCCOutput";
    var $LayoutFile = "checkout-payment-module-offline-cc-output-config.ini";
    /**#@-*/

}
?>