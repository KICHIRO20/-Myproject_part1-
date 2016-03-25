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
 * Taxes Module, TaxCalculator View.
 *
 * @package Taxes
 * @author Alexander Girin
 */
class TaxCalculator
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * A constructor.
     */
    function TaxCalculator()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');
        $this->classes = modApiFunc('Taxes', 'getProductTaxClasses');
        $this->class_qty = sizeof($this->classes);
//        modApiFunc("Taxes", "getTax", true);

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->initFormData();
        }
    }

    function copyFormData()
    {
        // eliminate copying on construction
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState = $SessionPost["ViewState"];

        //Remove some data, that should not be sent to action one more time, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }
        $this->POST  =
            array(
                "price"                 => $SessionPost["price"]
               ,"qty"                   => $SessionPost["qty"]
               ,"shipping_cost"         => $SessionPost["shipping_cost"]
               ,"tax_class"             => $SessionPost["tax_class"]
               ,"ShippingCost"          => $SessionPost["ShippingCost"]
               ,"ShippingMethod"        => $SessionPost["ShippingMethod"]
//               ,"ListPrice"             => $SessionPost["ListPrice"]
//               ,"ProductTaxClassId"     => $SessionPost["ProductTaxClassId"]
               ,"ShippingCountryId"     => $SessionPost["ShippingCountryId"]
               ,"ShippingStateId"       => $SessionPost["ShippingStateId"]
               ,"BillingCountryId"      => $SessionPost["BillingCountryId"]
               ,"BillingStateId"        => $SessionPost["BillingStateId"]
//               ,"CustomerCountryId"     => $SessionPost["CustomerCountryId"]
//               ,"CustomerStateId"       => $SessionPost["CustomerStateId"]
            );
    }

    function initFormData()
    {
        $this->ViewState =
            array(
                "ShowResults" => "false"
               ,"TraceInfo"=> ($_SERVER["QUERY_STRING"] == "TraceInfo") ? "true":"false"
                 );
        $price = array();
        $qty = array();
        $shipping_cost = array();
        $tax_class = array();

        for ($i=1; $i<=$this->class_qty; $i++)
        {
            $price[$i] = 10 + 10*$i;
            $qty[$i] = 1 + $i;
            $shipping_cost[$i] = 4 + $i;
            $tax_class[$i] = $this->classes[$i-1]['id'];
        }
        $this->POST  =
            array(
                "price"                 => $price
               ,"qty"                   => $qty
               ,"shipping_cost"         => $shipping_cost
               ,"tax_class"             => $tax_class
               ,"ShippingCost"          => "10"
               ,"ShippingMethod"        => null
//               ,"ListPrice"             => ""
//               ,"ProductTaxClassId"     => "0"
               ,"ShippingCountryId"     => "0"
               ,"ShippingStateId"       => "-1"
               ,"BillingCountryId"      => "0"
               ,"BillingStateId"        => "-1"
//               ,"CustomerCountryId"     => "0"
//               ,"CustomerStateId"       => "-1"
            );
    }

    function outputViewState()
    {
        $retval = "";
        foreach ($this->ViewState as $key => $value)
        {
            $retval .= "<input type=\"hidden\" name=\"ViewState[" .$key . "]\" value=\"" . $value . "\">";
        }
        return $retval;
    }

    function formAction()
    {
        global $application;
        $request = new Request();
        $request->setView  ('TaxCalculator');
        $request->setAction('TaxCalculateAction');
        return $request->getURL();
    }

    function outputCountriesList($address)
    {
        switch($address)
        {
            case 'Shipping':
                $CountryId = $this->POST["ShippingCountryId"];
                break;
            case 'Billing':
                $CountryId = $this->POST["BillingCountryId"];
                break;
            case 'Customer':
                $CountryId = $this->POST["CustomerCountryId"];
                break;
        }
        $retval = "";
        $countriesList = modApiFunc("Location", "getCountries");
        if (!$CountryId)
        {
            $countriesList[0] = $this->MessageResources->getMessage('SELECT_COUNTRY_LABEL');
        }
        ksort($countriesList);
        foreach ($countriesList as $id => $country)
        {
            $retval.= "<option value=\"".$id."\" ".(($id == $CountryId)? "SELECTED":"").">".$country."</option>";
        }
        return $retval;
    }

    function outputStatesList($address)
    {
        switch($address)
        {
            case 'Default':
            {
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
                $CountryId = $country_id;
                $StateId   = $state;
                break;
            }
            case 'Shipping':
                $CountryId = $this->POST["ShippingCountryId"];
                $StateId   = $this->POST["ShippingStateId"];
                break;
            case 'Billing':
                $CountryId = $this->POST["BillingCountryId"];
                $StateId   = $this->POST["BillingStateId"];
                break;
            case 'Customer':
                $CountryId = $this->POST["CustomerCountryId"];
                $StateId   = $this->POST["CustomerStateId"];
                break;
        }
        $retval = "";
        $states = modApiFunc("Location", "getStates", $CountryId);
        if ($StateId<0)
        {
            $states[-1] = $this->MessageResources->getMessage('SELECT_STATE_LABEL');
        }/*
        if ($StateId == 0)
        {
            $states[0] = $this->MessageResources->getMessage('STATE_ALL_LABEL');
        }*/
        ksort($states);
        foreach ($states as $stateId => $stateName)
        {
            $retval.= "<option value=\"".$stateId."\" ".(($stateId == $StateId)? "SELECTED":"").">".$stateName."</option>";
        }

        return $retval;
    }

    function outputShippingModules()
    {
        $retval = "";
        $ShippingModulesList = modApiFunc("Taxes", "getShippingModulesList");

        if ($this->POST["ShippingMethod"] === null)
        {
            $retval.= "<option value=\"\">".$this->MessageResources->getMessage('SELECT_SHIPPING_METHOD_LABEL')."</option>";
        }
        $retval.= "<option value=\"0\" ".(("0" === $this->POST["ShippingMethod"]) ? "SELECTED":"").">".$this->MessageResources->getMessage('NO_SHIPPING_METHOD_LABEL')."</option>";
        foreach ($ShippingModulesList as $module_id => $module_info)
        {
            $retval.= "<option value=\"".$module_id."\" ".(($module_id == $this->POST["ShippingMethod"])? "SELECTED":"").">".prepareHTMLDisplay($module_info["Name"])."</option>";
        }
        return $retval;
    }

    function outputProductTaxClassesList($selected_class = 0)
    {
        $retval = "";
        foreach ($this->classes as $classInfo)
        {
            $retval.= "<option value=\"".$classInfo["id"]."\" ".(($classInfo["id"] == $selected_class/*$this->POST["ProductTaxClassId"]*/)? "SELECTED":"").">".prepareHTMLDisplay($classInfo["value"])."</option>";
        }
        return $retval;
    }

    function outputTaxDisplay()
    {
        $retval = "";
        if (isset($this->TaxAmounts))
        {
            foreach ($this->TaxAmounts["TaxSubtotalAmountView"] as $TaxSubtotalAmountView)
            {
                $retval.= $TaxSubtotalAmountView['view']." ".modApiFunc("Localization", "format", $TaxSubtotalAmountView['value'], "currency")."<br>";
            }
        }
        return $retval;
    }

    function outputProductList()
    {
        global $application;

        $retval = "";

        $i = 1;
        foreach ($this->classes as $classInfo)
        {

            $this->_Template_Contents = array(
                                              'I' => $i
                                             ,'price' => $this->POST['price'][$i]
                                             ,'qty' => $this->POST['qty'][$i]
                                             ,'shipping_cost' => $this->POST['shipping_cost'][$i]
                                             ,'tax_class' => $this->POST['tax_class'][$i]
                                             ,'TaxClassList' => $this->outputProductTaxClassesList($this->POST['tax_class'][$i])
                                             ,'Format'  => modApiFunc("Localization", "format_settings_for_js", "currency")
                                             );
            $application->registerAttributes($this->_Template_Contents);
            $retval.= modApiFunc('TmplFiller', 'fill', "taxes/calculator/","item.tpl.html", array());
            $i++;
        }
        return $retval;
    }

    /**
     *
     */
    function output()
    {
        global $application;

        if($this->ViewState["ShowResults"] == "true")
        {
            if ($this->ViewState["TraceInfo"] == "true")
            {
                $this->TaxAmounts = modApiFunc("Taxes", "getTax", false, true, true);
            }
            else
            {
                $this->TaxAmounts = modApiFunc("Taxes", "getTax", false, true);
            }

            //Fill in the first table trace info
            $table1 = "";
            $table1_total = 0;
            $total_product_prices = array();
            for ($i=1; $i<=$this->class_qty; $i++)
            {
                $total_product_price = $this->POST["price"][$i]*$this->POST["qty"][$i];
                $total_product_prices[$i] = $total_product_price;
                $table1_total+= $total_product_price;
                $this->_Template_Contents = array(
                                                  'ProductName' => $this->MessageResources->getMessage('TAX_CALCULATOR_VIRTUAL_PRODUCT_NAME').$i
                                                 ,'ProductSalePrice' => modApiFunc("Localization", "format", $this->POST["price"][$i], "currency")
                                                 ,'ProductQty' => $this->POST["qty"][$i]
                                                 ,'TotalSalesPrice' => modApiFunc("Localization", "format", $total_product_price, "currency")
                                                 );
                $application->registerAttributes($this->_Template_Contents);
                $table1.= modApiFunc('TmplFiller', 'fill', "taxes/calculator/","table1_item.tpl.html", array());
            }

            //Fill in the second table trace info
            $table2 = "";
            $table2_total = 0;
            $total_per_item_shipping_costs = array();
            for ($i=1; $i<=$this->class_qty; $i++)
            {
                $total_per_item_shipping_cost = $this->POST["shipping_cost"][$i]*$this->POST["qty"][$i];
                $table2_total+= $total_per_item_shipping_cost;
                $total_per_item_shipping_costs[$i] = $total_per_item_shipping_cost;
                $this->_Template_Contents = array(
                                                  'ProductName' => $this->MessageResources->getMessage('TAX_CALCULATOR_VIRTUAL_PRODUCT_NAME').$i
                                                 ,'ProductShippingCost' => modApiFunc("Localization", "format", $this->POST["shipping_cost"][$i], "currency")
                                                 ,'ProductQty' => $this->POST["qty"][$i]
                                                 ,'TotalPerItemShippingCost' => modApiFunc("Localization", "format", $total_per_item_shipping_cost, "currency")
                                                 );
                $application->registerAttributes($this->_Template_Contents);
                $table2.= modApiFunc('TmplFiller', 'fill', "taxes/calculator/","table2_item.tpl.html", array());
            }

            //Fill in the third table trace info
            $table3 = "";
            $table3_total_price_sale = $table1_total;
            $table3_total_price_share = 0;
            $product_price_shares = array();
            for ($i=1; $i<=$this->class_qty; $i++)
            {
                $product_price_share = $total_product_prices[$i]/$table3_total_price_sale;
                $table3_total_price_share+= $product_price_share;
                $product_price_shares[$i] = $product_price_share;
                $this->_Template_Contents = array(
                                                  'ProductName' => $this->MessageResources->getMessage('TAX_CALCULATOR_VIRTUAL_PRODUCT_NAME').$i
                                                 ,'TotalSalPrice' => modApiFunc("Localization", "format", $total_product_prices[$i], "currency")
                                                 ,'ProductPriceShare' => modApiFunc("Localization", "format", $product_price_share, "number")."&nbsp;(".modApiFunc("Localization", "format", $total_product_prices[$i], "currency")."/".modApiFunc("Localization", "format", $table3_total_price_sale, "currency").")"
                                                 );
                $application->registerAttributes($this->_Template_Contents);
                $table3.= modApiFunc('TmplFiller', 'fill', "taxes/calculator/","table3_item.tpl.html", array());
            }

            //Fill in the fourth table trace info
            $table4 = "";
            $table4_total = 0;
            $order_level_shipping_costs = array();
            for ($i=1; $i<=$this->class_qty; $i++)
            {
                $order_level_shipping_cost = $this->POST["ShippingCost"]*$total_product_prices[$i]/$table3_total_price_sale;
                $table4_total+= $order_level_shipping_cost;
                $order_level_shipping_costs[$i] = $order_level_shipping_cost;
                $this->_Template_Contents = array(
                                                  'ProductName' => $this->MessageResources->getMessage('TAX_CALCULATOR_VIRTUAL_PRODUCT_NAME').$i
                                                 ,'ProductPriceShare' => modApiFunc("Localization", "format", $product_price_shares[$i], "number")."&nbsp;(".modApiFunc("Localization", "format", $total_product_prices[$i], "currency")."/".modApiFunc("Localization", "format", $table3_total_price_sale, "currency").")"
                                                 ,'ShippingMinusPerItemSum' => modApiFunc("Localization", "format", $order_level_shipping_cost, "currency")
                                                 );
                $application->registerAttributes($this->_Template_Contents);
                $table4.= modApiFunc('TmplFiller', 'fill', "taxes/calculator/","table4_item.tpl.html", array());
            }

            //Fill in the fifth table trace info
            $table5 = "";
            $table5_total_per_item_shipping_cost = $table2_total;
            $table5_total_order_level_shipping_cost = $table4_total;
            $table5_total_shipping_cost = 0;
            $total_shipping_costs = array();
            for ($i=1; $i<=$this->class_qty; $i++)
            {
                $total_shipping_cost = $total_per_item_shipping_costs[$i] + $order_level_shipping_costs[$i];
                $table5_total_shipping_cost+= $total_shipping_cost;
                $total_shipping_costs[$i] = $total_shipping_cost;
                $this->_Template_Contents = array(
                                                  'ProductName' => $this->MessageResources->getMessage('TAX_CALCULATOR_VIRTUAL_PRODUCT_NAME').$i
                                                 ,'TotalPerItemShippingCost' => modApiFunc("Localization", "format", $total_per_item_shipping_costs[$i], "currency")
                                                 ,'ShippingMinusPerItemSum' => modApiFunc("Localization", "format", $order_level_shipping_costs[$i], "currency")
                                                 ,'TotalShippingCost' => modApiFunc("Localization", "format", $total_shipping_cost, "currency")
                                                 );
                $application->registerAttributes($this->_Template_Contents);
                $table5.= modApiFunc('TmplFiller', 'fill', "taxes/calculator/","table5_item.tpl.html", array());
            }

            //Fill in the sixth table trace info
            $table6 = "";
            $table6_total_sales_price = $table1_total;
            $table6_total_shipping_cost = $table5_total_shipping_cost;
            for ($i=1; $i<=$this->class_qty; $i++)
            {
                $tax_class = modApiFunc("Taxes", "getTaxClassInfo", $this->POST["tax_class"][$i]);
                $tax_class = $tax_class["name"];
                $this->_Template_Contents = array(
                                                  'ProductName' => $this->MessageResources->getMessage('TAX_CALCULATOR_VIRTUAL_PRODUCT_NAME').$i
                                                 ,'TaxClass' => prepareHTMLDisplay($tax_class)
                                                 ,'SalePrice' => modApiFunc("Localization", "format", $total_product_prices[$i], "currency")
                                                 ,'ShippingCost' => modApiFunc("Localization", "format", $total_shipping_costs[$i], "currency")
                                                 );
                $application->registerAttributes($this->_Template_Contents);
                $table6.= modApiFunc('TmplFiller', 'fill', "taxes/calculator/","table6_item.tpl.html", array());
            }

            $products = "";
            $product_tax_totals = array();
            for ($i=1; $i<=$this->class_qty; $i++)
            {
                $items = "";
                $product_tax_total = 0;
                if (isset($this->TaxAmounts['products'][$i]))
                {
                    foreach ($this->TaxAmounts['products'][$i] as $tax_rate_id => $tax_amount)
                    {
                        if (is_array($tax_amount))
                        {
                            continue;
                        }
                        if ($tax_amount == PRICE_N_A)
                        {
                            continue;
                        }
                        $tax_total = $tax_amount*$this->POST["qty"][$i];
                        $product_tax_total+= $tax_total;
                        if ($tax_total == 0)
                        {
                            continue;
                        }
                        $tr_id = null;
                        if (isset($this->TaxAmounts['products'][$i]['tax_rate_id'][$tax_rate_id]))
                        {
                            $tr_id = $this->TaxAmounts['products'][$i]['tax_rate_id'][$tax_rate_id];
                            $tr_info = modApiFunc("Taxes", "getTaxRateInfo", $tr_id);
                            $tax_info = modApiFunc("Taxes", "getTaxNameInfo", $tr_info["TaxNameId"]);
                            $address_used = $this->MessageResources->getMessage(sprintf("TAX_ADDRESS_NAME_%03d",$tax_info["AddressId"]));
                        }

                        $taxFormula = ($tr_id)? modApiFunc("Taxes", "getTaxFormula", $tr_id):"";
                        $replace = array();
                        foreach (modApiFunc("Taxes", "getTaxNamesList") as $taxNameInfo)
                        {
                            if (isset($this->TaxAmounts['products'][$i][$taxNameInfo['Id']]))
                            {
                                $replace['{t_'.$taxNameInfo['Id'].'}'] = modApiFunc("Localization", "format", ($this->TaxAmounts['products'][$i][$taxNameInfo['Id']]*$this->POST["qty"][$i]), "currency");
                            }
                        }
                        $replace['{p_1}'] = modApiFunc("Localization", "format", $total_product_prices[$i], "currency");
                        $replace['{p_2}'] = ($this->TaxAmounts["tax_on_shipping"])? modApiFunc("Localization", "format", $total_shipping_costs[$i], "currency"):modApiFunc("Localization", "format", 0, "currency");
                        preg_match_all("/([^_][0-9]+[\.]*[0-9]+)/", $taxFormula['Formula'], $numbers);
                        for ($j=0; $j<sizeof($numbers[0]); $j++)
                        {
                            $replace[$numbers[0][$j]] = modApiFunc("Localization", "num_format", $numbers[0][$j]);
                        }
                        $taxFormula = modApiFunc("Localization", "format", $tax_total, "currency")." = ".$taxFormula['Rate']."% * (".strtr($taxFormula['Formula'], $replace).")";
                        $this->_Template_Contents = array(
                                                          'TaxFormula' => ($tr_id)? modApiFunc("Taxes", "getTaxFormulaViewFull", $tr_id)."<br>".$taxFormula:""
                                                         ,'AddressUsed' => isset($address_used)? $address_used:""
                                                         ,'TaxCost'    => modApiFunc("Localization", "format", $tax_total, "currency")
                                                         );
                        $application->registerAttributes($this->_Template_Contents);
                        $items.= modApiFunc('TmplFiller', 'fill', "taxes/calculator/","product_item.tpl.html", array());
                    }
                }
                if ($product_tax_total == 0)
                {
                    $this->_Template_Contents = array(
                                                      'TaxCost'    => modApiFunc("Localization", "format", 0, "currency")
                                                     );
                    $application->registerAttributes($this->_Template_Contents);
                    $items = modApiFunc('TmplFiller', 'fill', "taxes/calculator/","product_item_no_tax.tpl.html", array());
                    $product_tax_total = 0;
                }
                $product_tax_totals[] = modApiFunc("Localization", "format", $product_tax_total, "currency");
                $this->_Template_Contents = array(
                                                  'ProductName' => $this->MessageResources->getMessage('TAX_CALCULATOR_VIRTUAL_PRODUCT_NAME').$i
                                                 ,'CurrencySign' => modApiFunc("Localization", "getCurrencySign")
                                                 ,'Items' => $items
                                                 ,'ProductTaxTotal' => modApiFunc("Localization", "format", $product_tax_total, "currency")
                                                 );
                $application->registerAttributes($this->_Template_Contents);
                $products.= modApiFunc('TmplFiller', 'fill', "taxes/calculator/","product_container.tpl.html", array());
            }

            $this->_Template_Contents = array(
                                              'CurrencySign' => modApiFunc("Localization", "getCurrencySign")
                                             ,'Table1' => $table1
                                             ,'Table1_total' => modApiFunc("Localization", "format", $table1_total, "currency")
                                             ,'Table2' => $table2
                                             ,'Table2_total' => modApiFunc("Localization", "format", $table2_total, "currency")
                                             ,'Table3' => $table3
                                             ,'Table3_total_price_sale' => modApiFunc("Localization", "format", $table3_total_price_sale, "currency")
                                             ,'Table3_total_price_share' => modApiFunc("Localization", "format", $table3_total_price_share, "number")
                                             ,'Table4' => $table4
                                             ,'Table4_total' => modApiFunc("Localization", "format", $table4_total, "currency")
                                             ,'Table5' => $table5
                                             ,'Table5_total_per_item_shipping_cost' => modApiFunc("Localization", "format", $table5_total_per_item_shipping_cost, "currency")
                                             ,'Table5_total_order_level_shipping_cost' => modApiFunc("Localization", "format", $table5_total_order_level_shipping_cost, "currency")
                                             ,'Table5_total_shipping_cost' => modApiFunc("Localization", "format", $table5_total_shipping_cost, "currency")
                                             ,'Table6' => $table6
                                             ,'Table6_total_sales_price' => modApiFunc("Localization", "format", $table6_total_sales_price, "currency")
                                             ,'Table6_total_shipping_cost' => modApiFunc("Localization", "format", $table6_total_shipping_cost, "currency")
                                             ,'NoShippingMethod' => ($this->POST['ShippingMethod'] == 0)? $this->MessageResources->getMessage('TAX_CALCULATOR_STEP_001_WRN'):""
                                             ,'TaxToShipping' => ($this->TaxAmounts["tax_on_shipping"])? "":$this->MessageResources->getMessage('TAX_CALCULATOR_STEP_002_WRN')
                                             ,'Tax_per_Products' => $products
                                             ,'Total' => modApiFunc("Localization", "format", $this->TaxAmounts['TaxTotalAmount'], "currency")."&nbsp;(".implode(" + ", $product_tax_totals).")"
                                             );

            $application->registerAttributes($this->_Template_Contents);
            $calculation_results = modApiFunc('TmplFiller', 'fill', "taxes/calculator/","result.tpl.html", array());


            $traceInfo = "";
            if ($this->ViewState["TraceInfo"] == "true")
            {
                $this->_Template_Contents = modApiFunc("Taxes", "getTraceInfo");
                foreach ($this->_Template_Contents["TaxCalculationOrder"] as $taxId)
//                foreach ($this->_Template_Contents["TaxRatesListStage3"] as $taxId=>$TaxRatesListStage)
                {
                    $TaxRatesListStage = $this->_Template_Contents["TaxRatesListStage3"][$taxId];
                    $this->_Template_Contents["TaxRatesListStage3_1"] = $TaxRatesListStage;
                    $this->_Template_Contents["TaxName3_1"] = isset($this->_Template_Contents["TaxName3"][$taxId])? $this->MessageResources->getMessage(new ActionMessage(array('TAX_CALCULATOR_TRACE_INFO_007', $this->_Template_Contents["TaxName3"][$taxId]))):"";
                    $this->_Template_Contents["TaxRatesListStage3_2"] = isset($this->_Template_Contents["TaxRatesListStage4"][$taxId])? $this->_Template_Contents["TaxRatesListStage4"][$taxId]:"";
                    $this->_Template_Contents["Address3_2"] = isset($this->_Template_Contents["Address4"][$taxId])? $this->MessageResources->getMessage(new ActionMessage(array('TAX_CALCULATOR_TRACE_INFO_008', $this->_Template_Contents["Address4"][$taxId], $this->_Template_Contents["TaxName3"][$taxId]))):"";
                    if (isset($this->_Template_Contents["TaxRatesListStage5"][$taxId]))
                    {
                        $taxByProducts = "";
                        foreach ($this->_Template_Contents["TaxRatesListStage5"][$taxId] as $i=>$TaxInfo)
                        {
                            $this->_Template_Contents["ProductTaxClass"] = prepareHTMLDisplay($TaxInfo["ProductTaxClass"]);
                            $this->_Template_Contents["TaxRatesListStage"] = $TaxInfo["TaxRatesList"];
                            $this->_Template_Contents["ProductName"] = "Product ".$TaxInfo["ProdInfo"]["ID"];
                            $this->_Template_Contents["ProductTaxCalculation"] = $this->MessageResources->getMessage(new ActionMessage(array('TAX_CALCULATOR_TRACE_INFO_011', prepareHTMLDisplay($this->_Template_Contents["TaxName3"][$taxId]), $this->_Template_Contents["ProductName"])));
                            $this->_Template_Contents["ProductSalePrice"] = modApiFunc("Localization", "format", $TaxInfo["ProdInfo"]["attributes"]["SalePrice"]["value"], "currency");
                            $this->_Template_Contents["ProductShippingCost"] = modApiFunc("Localization", "format", $total_shipping_costs[$TaxInfo["ProdInfo"]["ID"]]/$TaxInfo["ProdInfo"]["Quantity_In_Cart"], "currency");
                            $this->_Template_Contents["ProductQuantity"] = $TaxInfo["ProdInfo"]["Quantity_In_Cart"];
                            $this->_Template_Contents["TotalSalePrice"] = modApiFunc("Localization", "format", $total_product_prices[$TaxInfo["ProdInfo"]["ID"]], "currency");
                            $this->_Template_Contents["TotalShippingCost"] = modApiFunc("Localization", "format", $total_shipping_costs[$TaxInfo["ProdInfo"]["ID"]], "currency");
                            $this->_Template_Contents["ProductTaxCalculationByTaxClass"] = $this->MessageResources->getMessage(new ActionMessage(array('TAX_CALCULATOR_TRACE_INFO_009', prepareHTMLDisplay($this->_Template_Contents["TaxName3"][$taxId]))));
                            $this->_Template_Contents["TaxMessage"] = isset($this->_Template_Contents["Message"][$taxId][$i])? $this->_Template_Contents["Message"][$taxId][$i]:"";
                            $application->registerAttributes($this->_Template_Contents);
                            $taxByProducts.= modApiFunc('TmplFiller', 'fill', "taxes/calculator/","tax_rates_list_by_product.tpl.html", array());
                        }
                    }
                    $this->_Template_Contents["TaxByProducts"] = $taxByProducts;
                    $application->registerAttributes($this->_Template_Contents);
                    $traceInfo.= modApiFunc('TmplFiller', 'fill', "taxes/calculator/","tax_rates_list.tpl.html", array());
                }
                $this->_Template_Contents["TaxRatesListStage3"] = $traceInfo;
                $this->_Template_Contents["Total"] = modApiFunc("Localization", "format", $this->TaxAmounts['TaxTotalAmount'], "currency");
                $application->registerAttributes($this->_Template_Contents);
                $traceInfo = modApiFunc('TmplFiller', 'fill', "taxes/calculator/","trace_info.tpl.html", array());
            }
        }
        else
        {
            $calculation_results = "";
            $traceInfo = "";
        }

        $this->_Template_Contents = array(
                                          'FormAction'            => $this->formAction()
                                         ,'HiddenArrayViewState'  => $this->outputViewState()
                                         ,'Items'                 => $this->outputProductList()
                                         ,'ShippingCost'          => $this->POST["ShippingCost"]
                                         ,"ShippingMethod"        => $this->outputShippingModules()
//                                         ,'ListPrice'             => $this->POST["ListPrice"]
                                         ,'ShippingCountriesList' => $this->outputCountriesList("Shipping")
                                         ,'ShippingStatesList'    => $this->outputStatesList("Shipping")
                                         ,'BillingCountriesList'  => $this->outputCountriesList("Billing")
                                         ,'BillingStatesList'     => $this->outputStatesList("Billing")
//                                         ,'CustomerCountriesList' => $this->outputCountriesList("Customer")
//                                         ,'CustomerStatesList'    => $this->outputStatesList("Customer")
                                         ,'CountriesStatesArrays' => modApiFunc("Location", "getJavascriptCountriesStatesArrays")
                                         ,'ProductTaxClassesList' => $this->outputProductTaxClassesList()
                                         ,'Format'  => modApiFunc("Localization", "format_settings_for_js", "currency")
                                         ,'CurrencySign' => modApiFunc("Localization", "getCurrencySign")
                                         ,'TaxDisplay' => $this->outputTaxDisplay()
                                         ,'CalculationResults'    => $calculation_results
                                         ,'TraceInfo' => $traceInfo
                                         ,'N'            => $this->class_qty
                                         );
        $application->registerAttributes($this->_Template_Contents);
        $output = modApiFunc('TmplFiller', 'fill', './../../js/','validate.msgs.js.tpl', array("CURRENCY" => addslashes($this->MessageResources->getMessage( new ActionMessage(array('CURRENCY_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 12.35, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.00, "currency"))))),
                                                         "INTEGER" => addslashes($this->MessageResources->getMessage('ITEM_FIELD'))
                                                         ));
        return $output.modApiFunc('TmplFiller', 'fill', "taxes/calculator/","container.tpl.html", array());
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        if (array_key_exists($tag, $this->_Template_Contents))
        {
            $value = $this->_Template_Contents[$tag];
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


    /**#@-*/

}
?>