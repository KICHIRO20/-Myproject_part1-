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

class test_ship extends AjaxAction
{
    function test_ship()
    {}

    function onAction()
    {
        global $application;

        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        $SessionPost = $_POST;

        if(modApiFunc('Location','getCountStatesInCountry',$SessionPost["DstCountry"])>0)
            $SessionPost["DstState"]=$SessionPost["DstState_menu_select"];
        else
            $SessionPost["DstState"]=$SessionPost["DstState_text_div"];

        if(isset($SessionPost["Cart"]))
        {
            $SessionPost["Cart"]=$this->NormalizeCart($SessionPost["Cart"]);
            $SessionPost["results"]=modApiFunc("Shipping_Tester","RunTest",$SessionPost);
        }
        else
            $SessionPost["Cart"]=array(
                    "products" => array(),
                    "subtotal" => 0,
                    "total_weight" => 0,
            );

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
    }

    function NormalizeCart($Cart)
    {
        $return=array(
            "products" => array(),
            "subtotal" => 0,
            "total_weight" => 0,
        );
        for($i=0;$i<count($Cart["products"]);$i++)
        {
            $product=$Cart["products"][$i];
            $product["qty"]=intval(trim($product["qty"]));
            $product["weight"]=floatval(trim($product["weight"]));
            $product["cost"]=floatval(str_replace(",",".",trim($product["cost"])));
            $product["ship_charge"]=floatval(str_replace(",",".",trim($product["ship_charge"])));
            $product["hand_charge"]=floatval(str_replace(",",".",trim($product["hand_charge"])));

            $return["products"][$i]=array(
                "qty" => $product["qty"]>0 ? $product["qty"] : 1,
                "weight" => $product["weight"]>0 ? $product["weight"] : 1,
                "cost" => $product["cost"]>=0 ? $product["cost"] : 0,
                "ship_charge" => $product["ship_charge"]>=0 ? $product["ship_charge"] : 0,
                "hand_charge" => $product["hand_charge"]>=0 ? $product["hand_charge"] : 0,
                "free_ship" => isset($product["free_ship"]) ? $product["free_ship"] : ''
            );
            $return["subtotal"]+=$return["products"][$i]["cost"]*$return["products"][$i]["qty"];
            $return["total_weight"]+=$return["products"][$i]["weight"]*$return["products"][$i]["qty"];
        };

        return $return;
    }
}

?>