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
 * module "Shipping Tester"
 *
 * @package ShippingTester
 * @author Egor V. Derevyankin
 */

class Shipping_Tester
{
    function Shipping_Tester()
    {}

    function getInfo()
    {}

    function install()
    {}

    function uninstall()
    {}

    /**
     * Runs the test of the shipping modules.
     *
     * @param $SessionPost array with the destination address and package weight
     * @return array with the results
     */
    function RunTest($SessionPost)
    {

        $ShippingInfo=array(
            "isMet" => true,
            "validatedData" => array (
                "Firstname" => array (
                    "value" => "Shipping"
                ),
                "Lastname" => array (
                    "value" => "Tester"
                ),
                "Country" => array (
                    "value" => $SessionPost["DstCountry"]
                ),
                "Statemenu" => array (
                    "value" => $SessionPost["DstState"]
                ),
                "City" => array (
                    "value" => $SessionPost["DstCity"]
                ),
                "Postcode" => array (
                    "value" => $SessionPost["DstZip"]
                )
            ),
        );

        modApiFunc("Shipping_Cost_Calculator","setShippingInfo",$ShippingInfo);
        modApiFunc("Shipping_Cost_Calculator","setCart",$SessionPost["Cart"]);
        modApiFunc("Shipping_Cost_Calculator","setDebugFlag",true);

        $results=modApiFunc("Shipping_Cost_Calculator","calculateShippingCost");

        return gzdeflate(serialize($results));
    }

}

?>