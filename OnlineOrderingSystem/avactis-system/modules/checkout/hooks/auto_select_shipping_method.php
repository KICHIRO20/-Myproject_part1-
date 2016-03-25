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
 *
 * @package Checkout
 * @author Vadim Lyalikov
 */
class AutoSelectShippingMethod
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * StepIDIsNotSet constructor.
     */
    function AutoSelectShippingMethod()
    {
    }

    /**
     *
     */
    function onHook()
    {
        global $zone;
        if ($zone == 'CustomerZone')
        {
            //Check if shippingInfo has already been outputted
            $ShippingInfo = modApiFunc("Checkout", "getPrerequisiteValidationResults", "shippingInfo");
            if($ShippingInfo["isMet"]!=false)
            {
                //check if shipping method hasn't been selected yet
                $shippingModuleAndMethod = modApiFunc("Checkout", "getPrerequisiteValidationResults", "shippingModuleAndMethod");
                if($shippingModuleAndMethod["isMet"] == false)
                {
                    $the_only_available_shipping_method_info = modApiFunc("Checkout", "getTheOnlyAvailableAndComputableShippingMethodId");
                    if($the_only_available_shipping_method_info === false)
                    {
                        return;
                    }
                    else
                    {
                        //Auto-select method
                        modApiFunc("Checkout", "setChosenShippingMethod",
                                   $the_only_available_shipping_method_info["module_id"]
                                  ,$the_only_available_shipping_method_info["method_id"]);
                        //: check if this method will be ticked off while outputting.
                    }
                }
            }
        }
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