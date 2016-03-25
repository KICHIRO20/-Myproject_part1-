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
 * Checkout Payment Module Settings view.
 *
 * @package Checkout
 * @author Vadim Lyalikov
 */

class CheckoutPaymentModuleFreeVersion
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     *  CheckoutPaymentModulesList constructor.
     */
    function CheckoutPaymentModuleFreeVersion()
    {
        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("CheckoutPaymentModuleSettings"))
        {
            $this->NoView = true;
        }
    }

    /**
     * Otputs the view.
     *
     * @todo $request->setView  ( '' ) - define the view name
     */
    function output()
    {
        global $application;

        $ViewClassName = modApiFunc("Checkout", "getCurrentPaymentModuleSettingsViewName");
        $ViewClassName = 'get'.$ViewClassName;
        $this->_Current_Payment_Module = array("MODULE_NAME" => 'none_at_all');

        $application->registerAttributes($this->_Current_Payment_Module);
//        $value = modApiFunc('TmplFiller', 'fill', "checkout/payment_module_settings/","container.tpl.html",array());
        return prepareArrayDisplay($this);//$value;
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case "MODULE_NAME":
                $value = $this->_Current_Payment_Module["MODULE_NAME"];
                break;
    	    default:
    	        return modApiFunc(modApiFunc("Checkout", "getCurrentPaymentModuleSettingsViewName"), "getTag", $tag);
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

    var $_Current_Payment_Module;
    /**#@-*/
}
?>