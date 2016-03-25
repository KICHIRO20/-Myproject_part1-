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
 * Module "Shipping Compositor"
 *
 * @package ShippingCompositor
 * @author Ravil Garafutdinov
 */

class ShippingCompositor
{
    function ShippingCompositor()
    {
        return;
    }


    /**
     * The parameter $customer_cart is an array, which returns the method
     * Cart::getCartContent.
     */
    function formatCart($customer_cart)
    {
        $formatted_cart = array(
                 "products" => array()
                ,"subtotal" => 0
                ,"total_weight" => 0
            );

        for($i=0; $i < count($customer_cart); $i++)
        {
            $product = array();
            $product["qty"] = $customer_cart[$i]["Quantity_In_Cart"];
            $product["SKU"] = $customer_cart[$i]["SKU"];
            $product["Name"] = $customer_cart[$i]["Name"];

            $_cycle = array(
                            "weight"=>"Weight",
                            "cost"=>"SalePrice",
                            "ship_charge"=>"PerItemShippingCost",
                            "hand_charge"=>"PerItemHandlingCost",
                            "free_ship"=>"FreeShipping",
                            "need_ship"=>"NeedShipping");

            foreach($_cycle as $_key=>$_attribute)
            {
                if (isset($customer_cart[$i]["attributes"][$_attribute])
                    && $customer_cart[$i]["attributes"][$_attribute]["visible"] == TRUE
                    && !empty($customer_cart[$i]["attributes"][$_attribute]["value"]))
                {
                    $product[$_key] = $customer_cart[$i]["attributes"][$_attribute]["value"];
                }
                else
                {
                    switch($_key)
                    {
                        case 'free_ship': $product[$_key] = 'NO'; break;
                        case 'need_ship': $product[$_key] = 'YES'; break;
                        default: $product[$_key] = 0.0; break;
                    };
                }
            }

//            $product["options_modifiers"] = $customer_cart[$i]["OptionsModifiers"];
            $product["cost"]   += $customer_cart[$i]["OptionsModifiers"]["price"];
            $product["weight"] += $customer_cart[$i]["OptionsModifiers"]["weight"];

            $product["pg_subtotal"] = $product["cost"] * $product["qty"];
            $product["pg_weight"] = $product["weight"] * $product["qty"];

            $formatted_cart["products"][]=$product;
            $formatted_cart["subtotal"] += $product["pg_subtotal"];
            $formatted_cart["total_weight"] += $product["pg_weight"];
        }

        return $formatted_cart;
    }

    function split_cart($order_full_cart, $threshold)
    {
        $this->_reset_msg();
        $this->threshold = $threshold;
        $this->cart = $this->formatCart($order_full_cart);
        $this->_add_msg("Cart untouched:<br />" . prepareArrayDisplay($this->cart));

        // splitting is not needed
        if ($this->cart["total_weight"] <= $threshold)
        {
            $this->_add_msg("Splitting is not needed.<br />");
            return array(0 => $this->cart);
        }

        $n=0;
        $this->parcel = array();

        // split large groups - groups, which' weight is larger than the threshold
        $this->_split_large_groups();

        // the better way
            // count minimum parcels number

            // loop groups
            // if there are empty parcels left,
            //     find the heaviest group, try to settle it inside, not splitting it
            // else
            //     find the group with heaviest items, try to settle it, splitting if needed
            //
            // if there are no empty parcels left, make a new parcel
            // and place the group there

        // but a simplified version is used now
        // we do not care about item weights or group weights
        // - just foreach() and go on

        foreach ($this->cart["products"] as $key => $value)
        {
            if (!$this->_settle_group_into_parcels($key))
                if (!$this->_settle_splitted_group_into_parcels($key))
                {
                    $this->_add_msg("$key wants to be settled as the last new.<br />");
                    $this->_add_parcel($key, $value["qty"], -1);
                }
        }

        $this->_add_msg("<br />Cart leftovers:<br />" . prepareArrayDisplay($this->cart));

        $this->parcel["total_weight"] = $this->parcel_weight;
        $this->parcel["subtotal"]     = $this->parcel_subtotal;
        $this->_add_msg("<br />Parcels:<br />" . prepareArrayDisplay($this->parcel));
        $this->parcel["msg"] = $this->msg;
        return $this->parcel;
    }

    function _split_large_groups()
    {
        $threshold = $this->threshold;

        foreach ($this->cart["products"] as $key => $value)
        {
            if ($value["weight"] > $threshold)
            {
                // item is too heavy
                // splitting the group into separate packets
                // @ generate warning
                $num = $value["qty"];
                for ($i=0; $i < $num; $i++)
                {
                    $this->_add_msg("$key wants to be settled as new with overweight warning.<br />");
                    $this->_add_parcel($key, 1, -1, true);
                }
            }
            else if ($value["weight"] == $threshold)
            {
                // the item fits one packet exactly
                // splitting the group into separate packets
                $num = $value["qty"];
                for ($i=0; $i < $num; $i++)
                {
                    $this->_add_msg("$key wants to be settled as new an feats neatly.<br />");
                    $this->_add_parcel($key, 1, -1);
                }
            }
            else if ($value["pg_weight"] > $threshold)
            {
                //                         ,           =
                //                 ,
                $rate = floor($threshold / $value["weight"]);
                $num = floor($value["qty"] / $rate);
                for ($i=0; $i < $num; $i++)
                {
                    $this->_add_msg("$key wants to be settled as new with rate $rate.<br />");
                    $this->_add_parcel($key, $rate, -1);
                }
            }
        }
    }

    function _add_parcel($id, $qty_asked, $pid, $warning = false)
    {
        if (!isset($this->cart["products"][$id]))
            return false;

        if ($this->cart["products"][$id]["qty"] < $qty_asked)
        {
            $qty_asked = $this->cart["products"][$id]["qty"];
        }

        $product = $this->cart["products"][$id];
        $product["qty"]         = $qty_asked;
        $product["pg_subtotal"] = $product["cost"] * $product["qty"];
        $product["pg_weight"]   = $product["weight"] * $product["qty"];

        if ($pid == -1)
        {
            // add to a new parcel
            $temp = array();
            $temp["products"][0] = $product;
            ($warning === true) ? $temp["warning"] = true : "";
            $temp["total_weight"] = $product["pg_weight"];
            $temp["subtotal"] = $product["pg_subtotal"];
            $this->parcel[] = $temp;
        }
        else
        {
            // add to the existing parcel
            $this->parcel[$pid]["products"][] = $product;
            $this->parcel[$pid]["total_weight"] += $product["pg_weight"];
            $this->parcel[$pid]["subtotal"] += $product["pg_subtotal"];
        }

        // clean cart
        if ($this->cart["products"][$id]["qty"] == $qty_asked)
        {
            unset($this->cart["products"][$id]);
        }
        else
        {
            $this->cart["products"][$id]["qty"] -= $qty_asked;
            $this->cart["products"][$id]["pg_weight"] =
                $this->cart["products"][$id]["qty"]
                *
                $this->cart["products"][$id]["weight"];
        }
        $this->_recalc_cart();
        $this->_recalc_parcels();
    }

    function _settle_group_into_parcels($gid)
    {
        $this->_add_msg("$gid wants to be settled inside.<br />");
        foreach ($this->parcel as $key => $value)
        {
        	$space = $this->threshold - $this->parcel[$key]["total_weight"];
        	if ($space > 0
        	   && $this->cart["products"][$gid]["pg_weight"] <= $space)
        	{
                $this->_add_msg("&nbsp;&nbsp;&nbsp;&nbsp;$gid settles at $key with all qty {$this->cart["products"][$gid]["qty"]}.<br />");
        	    $this->_add_parcel($gid, $this->cart["products"][$gid]["qty"], $key);
        	    return true;
        	}
        }
        $this->_add_msg("&nbsp;&nbsp;&nbsp;&nbsp;...and is unsuccesful.<br />");
        return false;
    }

    function _settle_splitted_group_into_parcels($gid)
    {
        $this->_add_msg("$gid wants to be splitted inside.<br />");
        foreach ($this->parcel as $key => $value)
        {
            $space = $this->threshold - $this->parcel[$key]["total_weight"];
            $rate = floor($space / $this->cart["products"][$gid]["weight"]);
            if ($space > 0 && $rate > 0)
            {
                $this->_add_msg("&nbsp;&nbsp;&nbsp;&nbsp;$gid splits in $key with rate $rate, space $space, weight {$this->cart["products"][$gid]["weight"]}.<br />");
                $this->_add_parcel($gid, $rate, $key);
                if (!isset($this->cart["products"][$gid])
                    || $this->cart["products"][$gid]["qty"] <= 0)
                {
                    $this->_add_msg("&nbsp;&nbsp;&nbsp;&nbsp;... and ceased.<br />");
                    return true;
                }
            }
        }

        $this->_add_msg("&nbsp;&nbsp;&nbsp;&nbsp;...and is unsuccesful.<br />");
        return false;
    }

    function _recalc_cart()
    {
        $cost = 0;
        $weight = 0;

        foreach ($this->cart["products"] as $key => $value)
        {
            $cost += $value["pg_subtotal"];
            $weight += $value["pg_weight"];
        }

        $this->cart["subtotal"] = $cost;
        $this->cart["total_weight"] = $weight;
    }

    function _recalc_parcels()
    {
        $cost = 0;
        $weight = 0;

        foreach ($this->parcel as $key => $value)
        {
            $cost   += $this->parcel[$key]["subtotal"];
            $weight += $this->parcel[$key]["total_weight"];
        }

        $this->parcel_subtotal = $cost;
        $this->parcel_weight = $weight;
    }

    function _reset_msg()
    {
        $this->msg = "";
    }

    function _add_msg($add)
    {
        $this->msg .= $add;
    }

    var $cart;
    var $parcel;
    var $threshold;
    var $parcel_weight;
    var $parcel_subtotal;

    var $msg;
};