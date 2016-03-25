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
 * CheckoutPaymentModuleAllInactiveOutput view
 *
 * @package PaymentModuleAllInactive
 * @author Vadim Lyalikov
 */
class CheckoutPaymentModuleGCOutput
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
    	    'layout-file'        => "checkout-payment-module-gc-output-config.ini" //; $this->LayoutFile
    	   ,'files' => array(
    	        'OutputContainer'               => TEMPLATE_FILE_SIMPLE
    	    )
    	   ,'options' => array(
    	    )
    	);
    	return $format;
    }

    /**
     *  CheckoutPaymentModuleAllInactiveOutput constructor
     */
    function CheckoutPaymentModuleGCOutput()
    {
        global $application;

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
//                                               'OutputField' => $this->getTag("OutputField")));

        $application->registerAttributes(array());


        $this->templateFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate($this->BlockTemplateName);
        $this->templateFiller->setTemplate($this->template);

        $application->registerAttributes(array("Local_PaymentMethodName" => ""));

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
            {
                $ModuleInfo = modApiFunc($this->ModuleAPIClassName, "getInfo");
                $value = $ModuleInfo['Name'];
                break;
            }
//            case 'PaymentHTMLModuleDescription':
//                $ModuleInfo = modApiFunc($this->ModuleAPIClassName, "getInfo");
//                $value = "<b>" . $ModuleInfo['Description'] . "</b>";
//                break;

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

    var $ModuleAPIClassName = "Payment_Module_Gc";
    var $BlockTemplateName = "CheckoutPaymentModuleGCOutput";
    var $LayoutFile = "checkout-payment-module-gc-output-config.ini";
    /**#@-*/

}
?>