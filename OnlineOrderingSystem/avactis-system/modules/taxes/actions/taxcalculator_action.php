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
 * @package Taxes
 * @author Alexander Girin
 */
class TaxCalculateAction extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * TaxCalculateAction constructor.
     */
    function TaxCalculateAction()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;
        $SessionPost = $_POST;
        $SessionPost["ViewState"]["ShowResults"] = "true";
        $request = $application->getInstance('Request');

        $products = array();
        $prod_prices = $request->getValueByKey("price");
        $prod_qtys = $request->getValueByKey("qty");
        $prod_shipping_costs = $request->getValueByKey("shipping_cost");
        $prod_tax_classes = $request->getValueByKey("tax_class");
        for ($i=1; $i<=sizeof($prod_prices); $i++)
        {
            //              _   _                  .
            $price_including_taxes = modApiFunc("Localization", "FormatStrToFloat", $prod_prices[$i], "currency");
            $price_excluding_taxes = modApiFunc("Catalog", "computePriceExcludingTaxes", $price_including_taxes, $prod_tax_classes[$i], true); //force to work in AZ
            $price_excluding_taxes = number_format($price_excluding_taxes, 2, '.', ',');
            $SessionPost["price"][$i] = $price_excluding_taxes;
            $products[] = array("CartItemSalePrice" => $price_excluding_taxes,
                                "CartItemSalePriceExcludingTaxes" => $price_excluding_taxes,
                                "Quantity_In_Cart" => $prod_qtys[$i],
                                "ShippingPrice" => modApiFunc("Localization", "FormatStrToFloat", $prod_shipping_costs[$i], "currency"),
                                "TaxClass" => $prod_tax_classes[$i]
                                );
        }

        $country_id = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_COUNTRY);
        $state = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_STATE);
        //            ,                                       ,         ,    ProductInfo,
        //                      .
        //                             -                                       .
        //                          $price_including_taxes
        if(!is_numeric($country_id) ||
           ($country_id < 1) ||
           !is_numeric($state) ||
           ($state < 1))
        {
            //                :
            _fatal(array( "CODE" => "CORE_057"), __CLASS__, __FUNCTION__);
        }

        modApiFunc('Taxes', 'setTaxDebug', $products,
                                           modApiFunc("Localization", "FormatStrToFloat", $request->getValueByKey("ShippingCost"), "currency"),
                                           $request->getValueByKey("ShippingMethod"),
                                           PRICE_N_A,//: ask user to input OrderLevelDiscount
                                           array(
                                                 "Default"  => array(
                                                                     "CountryId" => $country_id,
                                                                     "StateId" => $state
                                                                    ),
                                                 "Shipping" => array(
                                                                     "CountryId" => $request->getValueByKey("ShippingCountryId"),
                                                                     "StateId" => $request->getValueByKey("ShippingStateId")
                                                                    ),
                                                 "Billing"  => array(
                                                                     "CountryId" => $request->getValueByKey("BillingCountryId"),
                                                                     "StateId" => $request->getValueByKey("BillingStateId")
                                                                    )
/*                                                                    ,
                                                 "Customer" => array(
                                                                     "CountryId" => $request->getValueByKey("CustomerCountryId"),
                                                                     "StateId" => $request->getValueByKey("CustomerStateId")
                                                                    )*/
                                                ));
        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
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