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
 * Checkout Shipping Module Settings view.
 *
 * @package Checkout
 * @author Vadim Lyalikov
 */

class CheckoutShippingModuleFreeVersion
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
    function CheckoutShippingModuleFreeVersion()
    {
        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("CheckoutShippingModuleSettings"))
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

        $ViewClassName = modApiFunc("Checkout", "getCurrentShippingModuleSettingsViewName");

        $this->_Current_Payment_Module = array("Content" => $ViewClassName());
        $application->registerAttributes($this->_Current_Payment_Module);
        $value = modApiFunc('TmplFiller', 'fill', "checkout/shipping_module_settings/","container.tpl.html",array());

        // Ticket #1895
        // output is required for proper window functioning,
        // but it's value is that pesky white line below the buttons
        // it is always empty and we don't nned it
        return "";//$value;
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case "Content":
                $value = $this->_Current_Payment_Module["Content"];
                break;
    	    default:
    	        return modApiFunc(modApiFunc("Checkout", "getCurrentShippingModuleSettingsViewName"), "getTag", $tag);
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