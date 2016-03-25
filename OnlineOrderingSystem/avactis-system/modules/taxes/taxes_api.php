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

define('TAX_CLASS_ID_ANY', 0);
define('TAX_CLASS_ID_NOT_TAXABLE', 1);
define('TAX_FORMULA_ID_UNKNOWN', -1);

/**
 * Taxes module.
 *
 * @package Taxes
 * @author Alexander Girin
 */
class TaxesBase
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * News  constructor.
     */
    function Taxes()
    {
        $this->TaxDebug = null;
        $this->TraceInfo = "";
    }

    /**
     * Sets up data for tax debugging. The data structure is similar to one
     * of cart contents and to Checkout data.
     */
    function setTaxDebug($products, $OrderLevelShippingCost, $ShippingMethod, $OrderLevelDiscount, $addresses)
    {
        $ProductsList = array();
        $i = 1;
        $CartSubtotal = 0;
        $membership = modApiFunc('Customer_Account','getCurrentSignedCustomerGroupID');

        foreach ($products as $productInfo)
        {
            $price = (isset($productInfo["CartItemSalePriceExcludingTaxes"]) && $productInfo["CartItemSalePriceExcludingTaxes"] != null) ? $productInfo["CartItemSalePriceExcludingTaxes"] : $productInfo["CartItemSalePrice"];
            #12.01 $qd = modApiFunc("Quantity_Discounts", "getQuantityDiscount", $productInfo["ID"], $productInfo["Quantity_In_Cart"], $productInfo["CartItemSalePrice"]);
            $qd = modApiFunc("Quantity_Discounts", "getQuantityDiscount", $productInfo["ID"], $productInfo["Quantity_In_Cart"], $price, $membership);
            if($qd === 'FIXED_PRICE'){
                 $productInfo["CartItemSalePrice"] = modApiFunc('Quantity_Discounts', 'getFixedPrice', $productInfo["ID"], $productInfo["Quantity_In_Cart"], $productInfo["CartItemSalePrice"], $membership);

                 $qd = 0.0;
            }
            $qd = (($qd === PRICE_N_A) ? 0.0 : $qd);
            $CartSubtotal += $productInfo["Quantity_In_Cart"]*$productInfo["CartItemSalePrice"];
            $ProductsList[] = array(
                                    "CartID" => $i,
                                    "Quantity_In_Cart" => $productInfo["Quantity_In_Cart"],
                                    "attributes" => array(
                                                          "SalePrice"     => array(
                                                                             "value"=>$productInfo["CartItemSalePrice"]
                                                                                  ),
                                                          "CartItemSalePriceExcludingTaxes" => array(
                                                                             "value"=>$productInfo["CartItemSalePriceExcludingTaxes"]
                                                                                  ),
                                                          "PerItemShippingCost" => array(
                                                                             "value"=>$productInfo["ShippingPrice"]
                                                                                  ),
                                                          "PerItemHandlingCost" => array(
                                                                                  //: which version?
                                                                             "value"=>0 //$productInfo["HandlingPrice"] //NOT SUPPORTED in this version
                                                                                  ),
                                                          "TaxClass"      => array(
                                                                             "value"=>$productInfo["TaxClass"]
                                                                                  ),
                                                         ),
                                    "CartItemSalePrice" => $productInfo["CartItemSalePrice"],
                                    "CartItemSalePriceExcludingTaxes" => $productInfo["CartItemSalePriceExcludingTaxes"],
                                                                                  //:               $productInfo["CartItemSalePrice"]     setTaxDebug        .
                                                                                  //  CartItemSalePriceExcludingTaxes                              NULL (
                                                                                  //                               ),         $productInfo["CartItemSalePrice"]
                                                                                  //                                                  $info['attributes']['SalePrice']['value'].
                                                                                  //                    ,                         QuantityDiscount.
                                    "PerItemDiscount" =>  (1.0 * $qd)/ (1.0 * $productInfo["Quantity_In_Cart"])

                                   );
            $i++;
        }
        $this->TaxDebug = array(
                               "ProductsList" => $ProductsList,
                               "CartSubtotal" => $CartSubtotal,
                               "OrderLevelShippingCost" => $OrderLevelShippingCost,
                               "OrderLevelDiscount" => $OrderLevelDiscount,
                               "ShippingMethod" => $ShippingMethod,
                               "AddressesList"=> array(
                                                       "0" => array(//DefaultAddress
                                                                                  "CountryId" => $addresses["Default"]["CountryId"],
                                                                                  "StateId"   => $addresses["Default"]["StateId"]
                                                                                 ),
                                                       "1" => array(//ShippingAddress
                                                                                  "CountryId" => $addresses["Shipping"]["CountryId"],
                                                                                  "StateId"   => $addresses["Shipping"]["StateId"]
                                                                                 ),
                                                       "2" => array(//BillingAddress
                                                                                  "CountryId" => $addresses["Billing"]["CountryId"],
                                                                                  "StateId"   => $addresses["Billing"]["StateId"]
                                                                                 ),
                                                       TAXES_STORE_OWNER_ADDRESS_ID => array(//StoreOwnerAddress
                                                                                  "CountryId" => $addresses["StoreOwner"]["CountryId"],
                                                                                  "StateId"   => $addresses["StoreOwner"]["StateId"]
                                                                                 )

/*
                                                                                 ,
                                                        => array(//CustomerAddress
                                                                                  "CountryId" => $addresses["Customer"]["CountryId"],
                                                                                  "StateId"   => $addresses["Customer"]["StateId"]
                                                                                 )*/
                                                      )
                             );
    }

    function unsetTaxDebug()
    {
        $this->TaxDebug = null;
    }


    /**
     * Restores the session module state.
     */
    function loadState()
    {
        if(modApiFunc('Session', 'is_Set', 'TaxClassId'))
        {
            $this->TaxClassId = modApiFunc('Session', 'get', 'TaxClassId');
        }
        else
        {
            $this->TaxClassId = 0;
        }

        if(modApiFunc('Session', 'is_Set', 'editableTaxName'))
        {
            $this->editableTaxName = modApiFunc('Session', 'get', 'editableTaxName');
        }
        else
        {
            $this->editableTaxName = 0;
        }

        if(modApiFunc('Session', 'is_Set', 'editableTaxDisplayOption'))
        {
            $this->editableTaxDisplayOption = modApiFunc('Session', 'get', 'editableTaxDisplayOption');
        }
        else
        {
            $this->editableTaxDisplayOption = 0;
        }

        if(modApiFunc('Session', 'is_Set', 'editableTaxClass'))
        {
            $this->editableTaxClass = modApiFunc('Session', 'get', 'editableTaxClass');
        }
        else
        {
            $this->editableTaxClass = 0;
        }

        if(modApiFunc('Session', 'is_Set', 'editableTaxRate'))
        {
            $this->editableTaxRate = modApiFunc('Session', 'get', 'editableTaxRate');
        }
        else
        {
            $this->editableTaxRate = 0;
        }

        if(modApiFunc('Session', 'is_Set', 'CountryId'))
        {
            $this->country_id = modApiFunc('Session', 'get', 'CountryId');
        }
        else
        {
            $this->country_id = 0;
        }
        if (modApiFunc('Session', 'is_set', 'TaxDebug'))
        {
            $this->TaxDebug = modApiFunc('Session', 'get', 'TaxDebug');
            modApiFunc('Session', 'un_set', 'TaxDebug');
        }
        else
        {
            $this->TaxDebug = null;
        }

        if(modApiFunc('Session', 'is_Set', 'ErrorData'))
        {
            $this->ErrorData = modApiFunc('Session', 'get', 'ErrorData');
        }
        else
        {
            $this->ErrorData = array();
        }
    }

    /**
     * Saves the module state.
     */
    function saveState()
    {
        modApiFunc('Session', 'set', 'editableTaxName', $this->editableTaxName);
        modApiFunc('Session', 'set', 'editableTaxDisplayOption', $this->editableTaxDisplayOption);
        modApiFunc('Session', 'set', 'editableTaxRate', $this->editableTaxRate);
        modApiFunc('Session', 'set', 'editableTaxClass', $this->editableTaxClass);
        modApiFunc('Session', 'set', 'CountryId', $this->country_id);
        modApiFunc('Session', 'set', 'TaxClassId', $this->TaxClassId);
        if ($this->TaxDebug)
        {
            modApiFunc('Session', 'set', 'TaxDebug', $this->TaxDebug);
        }
        else
        {
            if (modApiFunc('Session', 'is_set', 'TaxDebug'))
            {
                modApiFunc('Session', 'un_set', 'TaxDebug');
            }
        }
        modApiFunc('Session', 'set', 'ErrorData', $this->ErrorData);
    }

    /**
     * Installs the specified module in the system.
     *
     * The install() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * News::getTables() instead of $this->getTables()
     */
    function install()
    {
        include_once dirname(__FILE__).'/install/install.php';
    }

    /**
     * Installs the specified module in the system.
     *
     * The uninstall() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * News::getTables() instead of $this->getTables()
     */
    function uninstall()
    {
        global $application;
        $query = new DB_Table_Delete(Taxes::getTables());
        $application->db->getDB_Result($query);
    }

    /**
     * Gets the array of meta description of module tables.
     *
     * The array structure of meta description of the table:
     * <code>
     *      $tables = array ();
     *      $table_name = 'table_name';
     *      $tables[$table_name] = array();
     *      $tables[$table_name]['columns'] = array
     *      (
     *          'fn1'               => 'table_name.field_name_1'
     *         ,'fn2'               => 'table_name.field_name_2'
     *         ,'fn3'               => 'table_name.field_name_3'
     *         ,'fn4'               => 'table_name.field_name_4'
     *      );
     *      $tables[$table_name]['types'] = array
     *      (
     *          'fn1'               => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
     *         ,'fn2'               => DBQUERY_FIELD_TYPE_INT .' NOT NULL'
     *         ,'fn3'               => DBQUERY_FIELD_TYPE_CHAR255
     *         ,'fn4'               => DBQUERY_FIELD_TYPE_TEXT
     *      );
     *      $tables[$table_name]['primary'] = array
     *      (
     *          'fn1'       # several key fields may be used - 'fn1', 'fn2'
     *      );
     *      $tables[$table_name]['indexes'] = array
     *      (
     *          'index_name1' => 'fn2'      # several fields can be used in one index- 'fn2, fn3'
     *         ,'index_name2' => 'fn3'
     *      );
     * </code>
     *
     * @return array - the meta description of module tables
     */
    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $prod_tax_classes = 'product_tax_classes';
        $tables[$prod_tax_classes] = array();
        $tables[$prod_tax_classes]['columns'] = array
            (
                'id'                => $prod_tax_classes.'.product_tax_class_id'
               ,'name'              => $prod_tax_classes.'.product_tax_class_name'
               ,'descr'             => $prod_tax_classes.'.product_tax_class_descr'
               ,'type'              => $prod_tax_classes.'.product_tax_class_type'
            );
        $tables[$prod_tax_classes]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255
               ,'descr'             => DBQUERY_FIELD_TYPE_LONGTEXT
               ,'type'              => DBQUERY_FIELD_TYPE_CHAR10
            );
        $tables[$prod_tax_classes]['primary'] = array
            (
                'id'
            );

        $tax_names = 'tax_names';
        $tables[$tax_names] = array();
        $tables[$tax_names]['columns'] = array
            (
                'id'                  => $tax_names.'.tax_name_id'
               ,'included_into_price' => $tax_names.'.included_into_price'
               ,'ta_id'               => $tax_names.'.tax_address_id'
               ,'name'                => $tax_names.'.tax_name'
               ,'needs_address'       => $tax_names.'.tax_needs_address'
            );
        $tables[$tax_names]['types'] = array
            (
                'id'                  => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'included_into_price' => DBQUERY_FIELD_BOOLEAN_DEFAULT_FALSE
               ,'ta_id'               => DBQUERY_FIELD_TYPE_INT
               ,'name'                => DBQUERY_FIELD_TYPE_CHAR255
               ,'needs_address'       => DBQUERY_FIELD_BOOLEAN_DEFAULT_TRUE
            );
        $tables[$tax_names]['primary'] = array
            (
                'id'
            );
        $tables[$tax_names]['indexes'] = array
            (
                'IDX_tai' => 'ta_id'
            );

        $tax_addresses = 'tax_addresses';
        $tables[$tax_addresses] = array();
        $tables[$tax_addresses]['columns'] = array
            (
                'id'                => $tax_addresses.'.tax_address_id'
               ,'pit_id'            => $tax_addresses.'.person_info_type_id'
               ,'name'              => $tax_addresses.'.tax_address_name'
            );
        $tables[$tax_addresses]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'pit_id'            => DBQUERY_FIELD_TYPE_INT
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR50
            );
        $tables[$tax_addresses]['primary'] = array
            (
                'id'
            );
        $tables[$tax_addresses]['indexes'] = array
            (
                'IDX_piti' => 'pit_id'
            );

        $tax_display = 'tax_display';
        $tables[$tax_display] = array();
        $tables[$tax_display]['columns'] = array
            (
                'id'                => $tax_display.'.tax_display_id'
               ,'tdo_id'            => $tax_display.'.tax_display_option_id'
               ,'formula'           => $tax_display.'.tax_display_formula'
               ,'view'              => $tax_display.'.tax_display_view'
            );
        $tables[$tax_display]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'tdo_id'            => DBQUERY_FIELD_TYPE_INT
               ,'formula'           => DBQUERY_FIELD_TYPE_CHAR255
               ,'view'              => DBQUERY_FIELD_TYPE_CHAR255
            );
        $tables[$tax_display]['primary'] = array
            (
                'id'
            );
        $tables[$tax_display]['indexes'] = array
            (
                'IDX_tdoi' => 'tdo_id'
            );

        $tax_display_options = 'tax_display_options';
        $tables[$tax_display_options] = array();
        $tables[$tax_display_options]['columns'] = array
            (
                'id'                => $tax_display_options.'.tax_display_option_id'
               ,'name'              => $tax_display_options.'.tax_display_option_name'
            );
        $tables[$tax_display_options]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR50
            );
        $tables[$tax_display_options]['primary'] = array
            (
                'id'
            );

        $tax_rates = 'tax_rates';
        $tables[$tax_rates] = array();
        $tables[$tax_rates]['columns'] = array
            (
                'id'                => $tax_rates.'.tax_rate_id'
               ,'c_id'              => $tax_rates.'.country_id'
               ,'s_id'              => $tax_rates.'.state_id'
               ,'ptc_id'            => $tax_rates.'.product_tax_class_id'
               ,'tn_id'             => $tax_rates.'.tax_name_id'
               ,'rate'              => $tax_rates.'.tax_rate'
               ,'formula'           => $tax_rates.'.tax_formula'
               ,'applicable'        => $tax_rates.'.tax_applicable'
               ,'rates_set'         => $tax_rates.'.rates_set'
            );
        $tables[$tax_rates]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'c_id'              => DBQUERY_FIELD_TYPE_INT
               ,'s_id'              => DBQUERY_FIELD_TYPE_INT
               ,'ptc_id'            => DBQUERY_FIELD_TYPE_INT
               ,'tn_id'             => DBQUERY_FIELD_TYPE_INT
               ,'rate'              => DBQUERY_FIELD_TYPE_FLOAT
               ,'formula'           => DBQUERY_FIELD_TYPE_CHAR255
               ,'applicable'        => DBQUERY_FIELD_TYPE_CHAR5
               ,'rates_set'         => DBQUERY_FIELD_TYPE_INT
            );
        $tables[$tax_rates]['primary'] = array
            (
                'id'
            );
        $tables[$tax_rates]['indexes'] = array
            (
                'IDX_ci'   => 'c_id'
               ,'IDX_si'   => 's_id'
               ,'IDX_ptci' => 'ptc_id'
               ,'IDX_tni'  => 'tn_id'
            );

        $tax_costs = 'tax_costs';
        $tables[$tax_costs] = array();
        $tables[$tax_costs]['columns'] = array
            (
                'id'                => $tax_costs.'.tax_cost_id'
               ,'name'              => $tax_costs.'.tax_cost_name'
            );
        $tables[$tax_costs]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR50
            );
        $tables[$tax_costs]['primary'] = array
            (
                'id'
            );

        $tax_settings = 'tax_settings';
        $tables[$tax_settings] = array();
        $tables[$tax_settings]['columns'] = array
            (
                'id'                => $tax_settings.'.tax_setting_id'
               ,'key'               => $tax_settings.'.tax_setting_key'
               ,'val'               => $tax_settings.'.tax_setting_value'
            );
        $tables[$tax_settings]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'key'               => DBQUERY_FIELD_TYPE_CHAR50
               ,'val'               => DBQUERY_FIELD_TYPE_TEXT
            );
        $tables[$tax_settings]['primary'] = array
            (
                'id'
            );

        global $application;
        return $application->addTablePrefix($tables);
    }

    /**
     * Gets a Product Tax Classes list.
     *
     * :  wouldn't it be better to make NOT_TAXABLE one of
     * Taxes class data and store it somewhere in the table Settings?
     *
     * @param
     * @return
     */
    function getProductTaxClasses($with_nottaxable = true)
    {
       return execQuery('SELECT_PRODUCT_TAX_CLASSES', array('with_nottaxable' => $with_nottaxable));
    }


    /**
     * Gets a taxes names list.
     *
     * @param
     * @return
     */
    function getTaxNamesList()
    {
        static $taxes;
        if (! isset($taxes)) {
            $taxes = execQuery('SELECT_TAX_NAMES_LIST',array());
        }
        return $taxes;
    }


    /**
     * Gets a taxes names list indexed by TaxId.
     *
     * @param
     * @return
     */
    function getTaxNames()
    {
        $TaxNamesRow = modApiFunc("Taxes", "getTaxNamesList");
        $TaxNames = array();
        foreach($TaxNamesRow as $tax)
        {
            $TaxNames[$tax['Id']] = $tax;
        }
        return $TaxNames;
    }

    /**
     * Gets a Display Options list.
     *
     * @param
     * @return
     */
    function getTaxDisplayOptionsList()
    {
        global $tax_options;
        if (! isset($tax_options)) {
            $tax_options = execQuery('SELECT_TAX_DISPLAY_OPTIONS_LIST', array());
        }
        return $tax_options;
    }

    function getTaxSetting($key)
    {
        if(!isset($this->settings))
        {
        	$this->settings = array();
        }
        if(isset($this->settings[$key]))
        {
        	return $this->settings[$key];
        }
        else
        {
	        $result = execQuery('SELECT_TAX_SETTING', array('key'=>$key));
	        if (sizeof($result))
	        {
	        	$this->settings[$key] = $result[0]['val'];
	        }
	        else
	        {
                $this->settings[$key] = null;
            }
            return $this->settings[$key];
        }
    }


    /**
     *                   $this->TaxDebug:
     * $this->TaxDebug = array(
     *                          "ProductsList" => array(
     *                                                  "0" => array(
     *                                                               "ID" => 1,
     *                                                               "Quantity_In_Cart" => 1,
     *                                                               "attributes" => array(
     *                                                                                     "SalePrice"     => array(
     *                                                                                                        "value"=>$CartItemSalePrice
     *                                                                                                             ),
     *                                                                                     "ShippingPrice" => array(
     *                                                                                                        "value"=>$ShippingPrice
     *                                                                                                             ),
     *                                                                                     "TaxClass"      => array(
     *                                                                                                        "value"=>$ProductTaxClass
     *                                                                                                             ),
     *                                                                                    ),
    *                                                                "CartItemSalePrice" => $CartItemSalePrice
     *                                                              )
     *                                                 ),
     *                          "AddressesList"=> array(
     *                                                  "1" => array(//ShippingAddress
     *                                                                             "CountryId" => $sa_c_id,
     *                                                                             "StateId"   => $sa_s_id
     *                                                                            ),
     *                                                  "2" => array(//CustomerAddress
     *                                                                             "CountryId" => $ca_c_id,
     *                                                                             "StateId"   => $ca_s_id
     *                                                                            ),
     *                                                  "3" => array(//BillingAddress
     *                                                                             "CountryId" => $ba_c_id,
     *                                                                             "StateId"   => $ba_s_id
     *                                                                            )
     *                                                 )
     *                        );
     */
    function getTax($included_only = false, $debug = false, $trace = false, $symbolic = false, $use_default_address_for_not_included_taxes = false)
    {
        static $__cache__ = array();
        global $application;
        $cache = CCacheFactory::getCache('temporary');
        $cart_content_hash = $cache->read('cart_content_hash');
        $__cache_key__ = md5( $cart_content_hash . serialize($included_only) . serialize($debug) . serialize($trace) .
                              serialize($symbolic) . serialize($use_default_address_for_not_included_taxes) );
        if (isset($__cache__[$__cache_key__]))
        {
            // disabled due to http://projects.simbirsoft.com/issues/1391
            //return $__cache__[$__cache_key__];
        }

        //$fake_parameter_this_tax_debug = $this -> TaxDebug;
        $currency_id = modApiFunc("Localization", "getMainStoreCurrency");
    	$TaxNames = modApiFunc("Taxes", "getTaxNames");
        if ($debug)
        {
            $ProductsList  = $this->TaxDebug["ProductsList"];
            $CartSubtotal = $this->TaxDebug["CartSubtotal"];
            $OrderLevelShippingCost = $this->TaxDebug["OrderLevelShippingCost"];
            $TotalShippingCost = $OrderLevelShippingCost;

            $OrderLevelDiscount = $this->TaxDebug["OrderLevelDiscount"];
            $TotalDiscount = $OrderLevelDiscount;
            foreach ($this->TaxDebug["ProductsList"] as $productInfo)
            {
                $TotalShippingCost+= $productInfo["Quantity_In_Cart"]*($productInfo["attributes"]["PerItemShippingCost"]["value"] +
                                                                       $productInfo["attributes"]["PerItemHandlingCost"]["value"]);
                $TotalDiscount+= $productInfo["Quantity_In_Cart"]*($productInfo["PerItemDiscount"]);
            }
            $ShippingMethod = $this->TaxDebug["ShippingMethod"];
            $AddressesList = $this->TaxDebug["AddressesList"];
        }
        else
        {
            $ProductsList = modApiFunc("Cart", "getCartContentExt");

            #12.01 $CartSubtotal = modApiFunc("Cart", "getCartSubtotal");
            $CartSubtotal = modApiFunc("Cart", "getCartSubtotalExcludingTaxes");

            $ShippingMethod = modApiFunc("Checkout", "getChosenShippingModuleIdCZ");

            $TotalShippingCost = modApiFunc("Checkout", "getOrderPrice", "TotalShippingAndHandlingCost", $currency_id);
            $OrderLevelShippingCost = $TotalShippingCost -
                                      modApiFunc("Checkout", "getOrderPrice", "PerItemShippingCostSum", $currency_id) -
                                      modApiFunc("Checkout", "getOrderPrice", "PerItemHandlingCostSum", $currency_id);
            if ($OrderLevelShippingCost < 0)
            {
            	$OrderLevelShippingCost = 0;
            }

            $OrderLevelDiscount = COMPUTATION_POSTPONED;
            $TotalDiscount = COMPUTATION_POSTPONED;


            $ShippingInfo = modApiFunc("Checkout", "getPrerequisiteValidationResults", "shippingInfo");
            $BillingInfo = modApiFunc("Checkout", "getPrerequisiteValidationResults", "billingInfo");
            $StoreOwnerInfo = modApiFunc("Checkout", "getPrerequisiteValidationResults", "storeOwnerInfo");

            //                          Checkout::storeOwnerInfo                isMet
            //                -               storeOwnerInfo          -                            ,
            //                      ?
            $store_owner_country = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_COUNTRY);
            $store_owner_state = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_STATE);
            $b_store_owner_address_correct = is_numeric($store_owner_country) && is_numeric($store_owner_state);
            $AddressesList = array(
                                   "0" => (!$b_store_owner_address_correct) ? null :
                                          array(//DefaultAddress (dad) - e.g. Store Owner Address
                                                "CountryId" => $store_owner_country,
                                                "StateId"   => $store_owner_state
                                               ),
                                   "1" => ($ShippingInfo["isMet"] == false)?
                                          ((!$b_store_owner_address_correct || !$use_default_address_for_not_included_taxes) ? null :
                                          array(//DefaultAddress (dad) - e.g. Store Owner Address
                                                "CountryId" => $store_owner_country,
                                                "StateId"   => $store_owner_state
                                               )):
                                          array(//ShippingAddress
                                                "CountryId" => isset($ShippingInfo["validatedData"]["Country"]["value"])? $ShippingInfo["validatedData"]["Country"]["value"]:0,
                                                "StateId"   => empty($ShippingInfo["validatedData"]["Statemenu"]["value"]) ? STATE_ID_ALL : $ShippingInfo["validatedData"]["Statemenu"]["value"]
                                               ),
                                   "2" => ($BillingInfo["isMet"] == false)?
                                          ((!$b_store_owner_address_correct || !$use_default_address_for_not_included_taxes) ? null :
                                          array(//DefaultAddress (dad) - e.g. Store Owner Address
                                                "CountryId" => $store_owner_country,
                                                "StateId"   => $store_owner_state
                                               )):
                                          array(//BillingAddress
                                                "CountryId" => isset($BillingInfo["validatedData"]["Country"]["value"])? $BillingInfo["validatedData"]["Country"]["value"]:0,
                                                "StateId"   => empty($BillingInfo["validatedData"]["Statemenu"]["value"]) ? STATE_ID_ALL : $BillingInfo["validatedData"]["Statemenu"]["value"]
                                               ),

                                   TAXES_STORE_OWNER_ADDRESS_ID . "" => ($StoreOwnerInfo["isMet"] == false)?
                                          ((!$b_store_owner_address_correct || !$use_default_address_for_not_included_taxes) ? null :
                                          array(//DefaultAddress (dad) - e.g. Store Owner Address
                                                "CountryId" => $store_owner_country,
                                                "StateId"   => $store_owner_state
                                               )):
                                          array(//StoreOwnerAddress
                                                "CountryId" => isset($StoreOwnerInfo["validatedData"]["Country"]["value"])? $StoreOwnerInfo["validatedData"]["Country"]["value"]:0,
                                                "StateId"   => empty($StoreOwnerInfo["validatedData"]["Statemenu"]["value"]) ? STATE_ID_ALL : $StoreOwnerInfo["validatedData"]["Statemenu"]["value"]
                                               )
                                  );
        }

        if (sizeof($ProductsList)>0)
        {
            #Gets the Product Tax Class array
            $ProductTaxClassArray = array();
            #A products( of each type) array
            $productsQuantity = array();
            foreach ($ProductsList as $ProductInfo)
            {
                if(!in_array($ProductInfo["attributes"]["TaxClass"]["value"], $ProductTaxClassArray))
                {
                    $ProductTaxClassArray[] = ($ProductInfo["attributes"]["TaxClass"]["value"])? $ProductInfo["attributes"]["TaxClass"]["value"]:1;
                }
                $productsQuantity[$ProductInfo["CartID"]] = $ProductInfo["Quantity_In_Cart"];
            }/*
            if(!in_array(1, $ProductTaxClassArray))
            {
                $ProductTaxClassArray[] = 1;
            }*/
            #Gets an Address array
            $AddressArray = array("CountryId" => array(), "StateId" => array());
            foreach ($AddressesList as $AddressInfo)
            {
                if ($AddressInfo)
                {
                    if(!in_array($AddressInfo["CountryId"], $AddressArray["CountryId"]))
                    {
                        $AddressArray["CountryId"][] = $AddressInfo["CountryId"];
                    }
                    if(!in_array($AddressInfo["StateId"], $AddressArray["StateId"]))
                    {
                        $AddressArray["StateId"][] = $AddressInfo["StateId"];
                    }
                }
            }
            if(!in_array((int)0, $AddressArray["StateId"]))
            {
                $AddressArray["StateId"][] = (int)0;
            }
            if(sizeof($AddressArray["CountryId"]) == 0)
            {
                $AddressArray["CountryId"][] = (int)0;
            }
            #Gets a list of applicable tax rates
            $ApplicableTaxes = array();
            $TaxRatesList = $this->getApplicableTaxRates($ProductTaxClassArray, $AddressArray);
//            if ($trace)
//            {
//
//                $TraceInfo = array(
//                                    "ProductList" => $ProductsList
//                                   ,"ProductTaxClassArray" => $ProductTaxClassArray
//                                   ,"AddressesList"         => $AddressesList
//                                   ,"TaxRatesList"         => $TaxRatesList
//                                  );
//                $this->addTraceInfo("1", $TraceInfo);
//            }

            $Taxes = $this->getTaxes($included_only);

            foreach ($TaxRatesList as $TaxRateInfo)
            {
                if(isset($Taxes[$TaxRateInfo['TaxNameId']]))
                {
                    $Taxes[$TaxRateInfo['TaxNameId']]["applicable"] = true;
                }
            }
            if($included_only === true)
            {
                //                    :
                $TaxRatesList_new = array();
                foreach ($TaxRatesList as $TaxRateInfo)
                {
                    if(isset($Taxes[$TaxRateInfo['TaxNameId']]))
                    {
                        $TaxRatesList_new[] = $TaxRateInfo;
                    }
                }
                $TaxRatesList = $TaxRatesList_new;
            }
//            if ($trace)
//            {
//                $TraceInfo = array(
//                                    "TaxesArray" => $Taxes
//                                  );
//                $this->addTraceInfo("2", $TraceInfo);
//            }
    /*
                          $TaxAmounts
        $TaxAmounts = array(
                            "products" => array(
                                                "ProdId1" = array(
                                                                 "TaxId1" => $amount1,
                                                                 "TaxId2" => $amount2,
                                                                 ...
                                                                 ),
                                                "ProdId2" = array(
                                                                 "TaxId1" => $amount3,
                                                                 "TaxId2" => $amount4,
                                                                 ...
                                                                 ),
                                               ),
                            "TaxSubtotalAmount" = array(
                                                       "TaxId1" => $amount1 + $amount3 + ...,
                                                       "TaxId2" => $amount2 + $amount4 + ...,
                                                        ...
                                                       ),
                            "TaxSubtotalAmountView" => array(), -                     Tax Display Options
                            "TaxTotalAmount" => sum(TaxSubtotalAmount)
                           );
    */

            $thisTaxAmounts = array(
                                        "products" => array(),
                                        "tax_on_shipping" => true,
                                        "TaxSubtotalAmount" => array(),
                                        "TaxSubtotalAmountView" => array(),
                                        "TaxTotalAmount" => (int)PRICE_N_A,//0
                                        "IncludedTaxTotalAmount" => (int)PRICE_N_A//0
                                     );
            #Calculate the tax
            foreach ($Taxes as $TaxId => $TaxInfo)
            {
                $thisTaxAmounts["TaxSubtotalAmount"][$TaxId] = (int)PRICE_N_A;//0;
                {
                    $retval = $this->calculateTax($thisTaxAmounts, $Taxes, $TaxId, $TaxRatesList, $AddressesList, $ProductsList, $CartSubtotal, $OrderLevelShippingCost, $TotalShippingCost, $ShippingMethod, $OrderLevelDiscount, $TotalDiscount, $currency_id, $debug, $trace, $symbolic, NULL);//$flag);

                    if ($retval == "fatal")
                    {
                        return $thisTaxAmounts;
                    }
                }
            }
            $CartIDs = array();
            foreach($ProductsList as $info)
            {
                $CartIDs[] = "".$info['CartID'];
            }

            foreach ($thisTaxAmounts["products"] as $prodId => $taxAmountInfo)
            {
                //PATCH: :             .        calculateTax                   $this->TaxAmounts["products"]
                //          ,               $ProductsList,        .                          .
                if(!in_array("".$prodId, $CartIDs))
                {
                    continue;
                }

//print prepareArrayDisplay($prodId);
//print prepareArrayDisplay($taxAmountInfo);
//$i = 1;
//print $i.' '.prepareArrayDisplay($thisTaxAmounts["TaxTotalAmount"]);
                foreach ($taxAmountInfo as $taxId => $taxAmount)
                {
//$i++;
                    if (is_array($taxAmount))
                    {
                        continue;
                    }
                    if ($taxAmount == PRICE_N_A)
                    {
    //                    $this->TaxAmounts["TaxTotalAmount"] = (int)PRICE_N_A;
                        continue;
                    }

                    if ($thisTaxAmounts["TaxSubtotalAmount"][$taxId] == PRICE_N_A)
                    {
                        $thisTaxAmounts["TaxSubtotalAmount"][$taxId] = 0;
                    }
                    if ($thisTaxAmounts["IncludedTaxTotalAmount"] == PRICE_N_A)
                    {
                        $thisTaxAmounts["IncludedTaxTotalAmount"] = 0;
                    }
                    if ($thisTaxAmounts["TaxTotalAmount"] == PRICE_N_A)
                    {
                        $thisTaxAmounts["TaxTotalAmount"] = 0;
                    }
                    if(!isset($productsQuantity[$prodId]))
                    {
                    }
                    $thisTaxAmounts["TaxSubtotalAmount"][$taxId] += modApiFunc("Localization", "roundMonetaryUnit", $productsQuantity[$prodId]*$taxAmount);

                    if($TaxNames[$taxId]["included_into_price"] == "true")
                    {
                        $thisTaxAmounts["IncludedTaxTotalAmount"] += modApiFunc("Localization", "roundMonetaryUnit", $productsQuantity[$prodId]*$taxAmount);
                    }
                    $thisTaxAmounts["TaxTotalAmount"] += modApiFunc("Localization", "roundMonetaryUnit", $productsQuantity[$prodId]*$taxAmount);
//print $i.' '.prepareArrayDisplay($thisTaxAmounts["TaxTotalAmount"]);
                }
//print prepareArrayDisplay($thisTaxAmounts["TaxTotalAmount"]);
            }
        }
        else
        {
            $Taxes = $this->getTaxes();
            $TaxSubtotalAmount = array();
            foreach ($Taxes as $TaxId => $TaxInfo)
            {
                $TaxSubtotalAmount[$TaxId] = (int)PRICE_N_A;
            }
            $thisTaxAmounts = array(
                                        "products" => array(),
                                        "tax_on_shipping" => true,
                                        "TaxSubtotalAmount" => $TaxSubtotalAmount,
                                        "TaxSubtotalAmountView" => array(),
                                        "TaxTotalAmount" => (int)PRICE_N_A//0
                                     );
        }

        $replace = array();
        foreach ($thisTaxAmounts["TaxSubtotalAmount"] as $taxId => $taxAmount)
        {
            $replace["{".$taxId."}"] = $taxAmount;
        }

        $DisplayOptions = $this->getTaxDisplayOptionsList();
        //                      included       ,
        //            .
        if($included_only === true)
        {
            $DisplayOptions_new = array();
            foreach($DisplayOptions as $tdo)
            {
                if(isset($Taxes[$tdo['Id']]))
                {
                    $DisplayOptions_new[] = $tdo;
                }
            }
            $DisplayOptions = $DisplayOptions_new;
        }

        foreach ($DisplayOptions as $DisplayOptionInfo)
        {
            if ($DisplayOptionInfo['tdoId'] != 3)
            {
                $_replace = $replace;
                if ($DisplayOptionInfo['tdoId'] == 1)
                {
                    foreach ($replace as $taxIdTag => $TaxAmount)
                    {
                        if (!(_ml_strpos($DisplayOptionInfo['Formula'], $taxIdTag) === false) && $TaxAmount != PRICE_N_A)
                        {
                            foreach ($_replace as $taxIdTag => $TaxAmount)
                            {
                                if ($TaxAmount == PRICE_N_A)
                                {
                                    $_replace[$taxIdTag] = 0;
                                }
                            }
                            break;
                        }
                    }
                }

                $is_included = false;
                $this_one_is_included = false;
                foreach ($thisTaxAmounts["TaxSubtotalAmount"] as $taxId => $taxAmount)
                {
                    if (!(_ml_strpos($DisplayOptionInfo['Formula'], "{".$taxId."}") === false))
                    {
                        $this_one_is_included = $Taxes[$taxId]["included_into_price"];
                    }
                }

                $DisplayOptionInfo['Formula'] = strtr($DisplayOptionInfo['Formula'], $_replace);
                if (_ml_strpos($DisplayOptionInfo['Formula'], sprintf("%d", PRICE_N_A)) === false)
                {
                    $TaxAmount = eval("return ".$DisplayOptionInfo['Formula'].";");
                }
                else
                {
                    $TaxAmount = (int)PRICE_N_A;
                }
                if (!($TaxAmount==PRICE_N_A && $DisplayOptionInfo['tdoId'] == 2))
                {
                    $thisTaxAmounts["TaxSubtotalAmountView"][] = array
                            (
                                "id" => $DisplayOptionInfo["Id"]
                               ,'view' => prepareHTMLDisplay($DisplayOptionInfo['View'])
                               ,'value' => $TaxAmount
                               ,"is_included" => $this_one_is_included
                            );
                }
            }
        }

        $__cache__[$__cache_key__] = $thisTaxAmounts;

        return $thisTaxAmounts;
    }

    /**
     *
     *
     * @param
     * @return
     */
    function excludeTaxRates($TaxRatesList, $condition)
    {
        $result = array();
        foreach ($TaxRatesList as $TaxRateInfo)
        {
            switch ($condition["entity"])
            {
                case "Tax":
                    if ($TaxRateInfo["TaxNameId"] == $condition["TaxNameId"])
                    {
                        $result[] = $TaxRateInfo;
                    }
                    break;
                case "TaxClass":
                    if ($TaxRateInfo["ProductTaxClassId"] == $condition["ProductTaxClassId"])
                    {
                        $result[] = $TaxRateInfo;
                    }
                    break;
            }
        }
        if ($condition["entity"] == "TaxClass" && sizeof($result) == 0)
        {
            foreach ($TaxRatesList as $TaxRateInfo)
            {
                if ($TaxRateInfo["ProductTaxClassId"] == 1)
                {
                    $result[] = $TaxRateInfo;
                }
            }
        }
        if ($condition["entity"] == "Address")
        {
            foreach ($TaxRatesList as $TaxRateInfo)
            {
                if ($TaxRateInfo["StateId"] == $condition["StateId"] && $TaxRateInfo["CountryId"] == $condition["CountryId"])
                {
                    $result[] = $TaxRateInfo;
                }
            }
            //                      ALL_STATES
            $all_states_present = false;
            foreach($result as $info)
            {
            	if($info["CountryId"] == $condition["CountryId"] &&
            	   $info["StateId"] == 0)
            	{
            		$all_states_present = true;
            		break;
            	}
            }
            if (!$all_states_present)
            {
                foreach ($TaxRatesList as $TaxRateInfo)
                {
                    if ($TaxRateInfo["CountryId"] == $condition["CountryId"] && $TaxRateInfo["StateId"] == 0)
                    {
                        $result[] = $TaxRateInfo;
                    }
                }
            }
        }
        //                                    ,       ALL_STATES.           ALL_STATES,               .
        if ($condition["entity"] == "State")
        {
            foreach ($TaxRatesList as $TaxRateInfo)
            {
                if ($TaxRateInfo["StateId"] == $condition["StateId"])
                {
                    $result[] = $TaxRateInfo;
                }
                //                  rate         StateId,               ALL_STATES
                if(empty($result))
                {
                    foreach ($TaxRatesList as $TaxRateInfo)
                    {
                        if ($TaxRateInfo["StateId"] == 0)
                        {
                            $result[] = $TaxRateInfo;
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     *          ,                     (                  )                                      .
     *                                                ,                                            .
     *                      - ?                              $1   $10,       $1000
     *                            .                                   .
     *            true,                                       , false -                .
     *                 true,              $factors                                    k   b,          :
     * k * $variable_name + b === $formula
     *
     *                                                                PRICE_N_A.           ,
     *                  , ), (, +, -, *, $variable_name.
     */
    function isTaxFormulaLinear($formula, $tax_rate, $variable_name, &$factors)
    {
        //$factors = array("k" => NULL, "b" => NULL);
        $x1 = 1.0;
        $_formula = strtr($formula, array($variable_name => $x1));
        $y1 = eval("return ".$_formula.";");

        $x2 = 10.0;
        $_formula = strtr($formula, array($variable_name => $x2));
        $y2 = eval("return ".$_formula.";");

        $x3 = 1000.0;
        $_formula = strtr($formula, array($variable_name => $x3));
        $y3 = eval("return ".$_formula.";");

        $y3_linear = ($x3-$x1)*(($y2-$y1)/($x2-$x1)) + $y1;

        $one_cent = 0.01;
        $tax_rate = $tax_rate/100.0;
        if(abs($y3_linear-$y3) < $one_cent)
        {
            $factors["k"] = $tax_rate * ($y2-$y1)/($x2-$x1);
            $factors["b"] = $tax_rate * ($y1 - $x1 * (($y2-$y1)/($x2-$x1)));
            //       ,
            return true;
        }
        else
        {
            //                      .
            //                                                 _ _                    .
            return false;
        };
    }

    /**
     *                                              .
     *                     ,                          .
     *
     *        .  . .                                    :
     *     _ _         =     _   _        + k *     _   _        + b,
     *     k   b -      .
     *
     *     _   _        = (    _ _         - b) / (1 + k)
     */
    function getPriceExcludingTaxes($price_including_taxes, $factors)
    {
        return (($price_including_taxes - $factors['b']) / (1.0 + $factors['k']));
    }

    /**
     *               $symbolic = true,                                   ,                {p_1} -
     *               .                                   _   _       ,
     *     _ _        .      ,      $symbolic = false,                                      -      ,
     *       ,                _   _       .
     * @param
     * @return
     */
    function calculateTax(&$thisTaxAmounts, &$Taxes, $TaxId, $TaxRatesList, $AddressesList, $ProductsList, $CartSubtotal, $OrderLevelShippingCost, $TotalShippingCost, $ShippingMethod, $OrderLevelDiscount, $TotalDiscount, $currency_id, $debug, $trace, $symbolic, $md5 = NULL)
    {
        global $application;
        $MessageResources = &$application->getInstance('MessageResources');

        $retval = "";
        if ($Taxes[$TaxId]["applicable"])
        {
            if ($Taxes[$TaxId]["status"] == "in_progress")
            {
                $Taxes[$TaxId]["status"] = "na";
//                if ($trace)
//                {
//                    $TaxInfo = $this->getTaxNameInfo($TaxId);
//                    $TraceInfo = array(
//                                       "TaxId" => $TaxId,
//                                       "Message"      => $MessageResources->getMessage( new ActionMessage(array('TAX_CALCULATOR_WRN_009', $TaxInfo["Name"])))
//                                      );
//                    $this->addTraceInfo("6", $TraceInfo);
//                }
                return "fatal";
            }
            if ($Taxes[$TaxId]["status"] == "calculated")
            {
                return;
            }
            $Taxes[$TaxId]["status"] = "in_progress";
            $_TaxRatesList = $this->excludeTaxRates($TaxRatesList, array("entity" => "Tax", "TaxNameId" => $TaxId));
//            if ($trace)
//            {
//                $TaxInfo = $this->getTaxNameInfo($TaxId);
//                $TraceInfo = array(
//                                   "TaxId" => $TaxId
//                                  ,"TaxName"      => $TaxInfo["Name"]
//                                  ,"Address"      => $MessageResources->getMessage(sprintf("TAX_ADDRESS_NAME_%03d", $TaxInfo["AddressId"]))
//                                  ,"TaxRatesList" => $_TaxRatesList
//                                  );
//                $this->addTraceInfo("3", $TraceInfo);
//            }

            //                   ,          "                 ",
            //       .                                        .                          (
            //             Store Owner Address)                            (
            //  country_id, state_id),    "                ""
            //                         .                                      "
            //                    ",                                                 .
            //                                      .         ,            Checkout CZ.
            //                                                 .                       ,
            //            .

            //                        ,     "                "
            //                                 .                     .         ,                      .

            if($Taxes[$TaxId]["needs_address"] == DB_TRUE &&
               $AddressesList[$Taxes[$TaxId]["address"]] === NULL)
            {
                //                        default address       -                .
                if($Taxes[$TaxId]["included_into_price"] === "true" &&
                   $AddressesList[0] !== NULL)
                {
                    $__TaxRatesList = $this->excludeTaxRates($_TaxRatesList,
                                                             array
                                                             (
                                                                 "entity" => "Address",
                                                                 "CountryId" => $AddressesList[0]["CountryId"],
                                                                 "StateId" => $AddressesList[0]["StateId"],
                                                             )
                                                            );
                }
                else
                {
                    //"                "           .                                                  ,
                    //              "                                                            "
                    //           .
                    //                                .
                    $__TaxRatesList = $this->excludeTaxRates($_TaxRatesList,
                                                             array
                                                             (
                                                                 "entity" => "Address",
                                                                 "CountryId" => NULL,
                                                                 "StateId" => NULL,
                                                             )
                                                            );
                }
            }
            else if($Taxes[$TaxId]["needs_address"] == DB_TRUE)
            {
                //                            .

                //                                        ,     "                  "
                //                                    .
                //                        default address       -                .
                if($Taxes[$TaxId]["included_into_price"] === "true")
                {
                    if($AddressesList[0] !== NULL)
                    {
                        $__TaxRatesList = $this->excludeTaxRates($_TaxRatesList,
                                                                 array
                                                                 (
                                                                     "entity" => "Address",
                                                                     "CountryId" => $AddressesList[0]["CountryId"],
                                                                     "StateId" => $AddressesList[0]["StateId"],
                                                                 )
                                                                );
                    }
                    else
                    {
                        //                         .
                        //                       "                ."
                        //                             PRICE_N_A.
                        $__TaxRatesList = $this->excludeTaxRates($_TaxRatesList,
                                                                 array
                                                                 (
                                                                     "entity" => "Address",
                                                                     "CountryId" => NULL,
                                                                     "StateId" => NULL,
                                                                 )
                                                                );
                    }
                }
                else
                {
                    //      "            ".                                           .
                    $__TaxRatesList = $this->excludeTaxRates($_TaxRatesList,
                                                             array
                                                             (
                                                                 "entity" => "Address",
                                                                 "CountryId" => $AddressesList[$Taxes[$TaxId]["address"]]["CountryId"],
                                                                 "StateId" => $AddressesList[$Taxes[$TaxId]["address"]]["StateId"],
                                                             )
                                                            );
                }
            }
            else
            //$Taxes[$TaxId]["needs_address"] == DB_FALSE
            {
                $__TaxRatesList = $_TaxRatesList;
                //                                                .                                   .
            }

//            if ($trace)
//            {
//                global $application;
//                $MessageResources = &$application->getInstance('MessageResources');
//                $TraceInfo = array(
//                                   "TaxId" => $TaxId
//                                  ,"Address"      => $MessageResources->getMessage(sprintf('TAX_ADDRESS_NAME_%03d', $Taxes[$TaxId]["address"]))
//                                  ,"CountryId" => $this->TaxDebug["AddressesList"][$Taxes[$TaxId]["address"]]["CountryId"]
//                                  ,"StateId" => $this->TaxDebug["AddressesList"][$Taxes[$TaxId]["address"]]["StateId"]
//                                  ,"TaxRatesList" => $__TaxRatesList
//                                  );
//                $this->addTraceInfo("4", $TraceInfo);
//            }

            foreach ($ProductsList as $ProductInfo)
            {
                $thisTaxAmounts["products"][$ProductInfo["CartID"]][$TaxId] = (int)PRICE_N_A;//0;
                /*
                if ($ProductInfo["attributes"]["TaxClass"]["value"] == TAX_CLASS_ID_NOT_TAXABLE)
                {
                    continue;
                }
                */
                $___TaxRatesList = $this->excludeTaxRates($__TaxRatesList, array("entity" => "TaxClass",
                                                               "ProductTaxClassId" => $ProductInfo["attributes"]["TaxClass"]["value"]));
//                if ($trace)
//                {
//                    $ProdTaxClass = $this->getProductTaxClassInfo($ProductInfo["attributes"]["TaxClass"]["value"]);
//                    $TraceInfo = array(
//                                       "TaxId" => $TaxId
//                                      ,"ProdInfo" => $ProductInfo
//                                      ,"ProductTaxClass"      => $ProdTaxClass['value']
//                                      ,"TaxRatesList" => $___TaxRatesList
//                                      );
//                    $this->addTraceInfo("5", $TraceInfo);
//                }
                if (sizeof($___TaxRatesList) == 0)
                {
                    $this->TaxAmounts["products"][$ProductInfo["ID"]][$TaxId] = 0;
                    $Taxes[$TaxId]["status"] = "na";
//                    if ($trace)
//                    {
//                        $TaxInfo = $this->getTaxNameInfo($TaxId);
//                        $TraceInfo = array(
//                                           "TaxId" => $TaxId,
//                                           "Message"      => $MessageResources->getMessage( new ActionMessage(array('TAX_CALCULATOR_WRN_013', prepareHTMLDisplay($TaxInfo["Name"]), "Product ".$ProductInfo["CartID"])))
//                                          );
//                        $this->addTraceInfo("6", $TraceInfo);
//                    }
                    continue;
                }

                //                  ALL_STATES                NEW_YORK,              .
                //    NEW_YORK -         .
                if($Taxes[$TaxId]["needs_address"] == DB_TRUE &&
                   isset($AddressesList[$Taxes[$TaxId]["address"]]) &&
                   isset($AddressesList[$Taxes[$TaxId]["address"]]["StateId"]) &&
                   $AddressesList[$Taxes[$TaxId]["address"]]["StateId"] != NULL &&
                   $AddressesList[$Taxes[$TaxId]["address"]]["StateId"] != 0 &&
                   is_numeric($AddressesList[$Taxes[$TaxId]["address"]]["StateId"])
                )
                {
                    $___TaxRatesList = $this->excludeTaxRates($___TaxRatesList, array("entity" => "State",
                                                                   "StateId" => $AddressesList[$Taxes[$TaxId]["address"]]["StateId"]));
                }

                if (sizeof($___TaxRatesList) > 1)
                {
                    $this->TaxAmounts["products"][$ProductInfo["ID"]][$TaxId] = 0;
                    $Taxes[$TaxId]["status"] = "na";
//                    if ($trace)
//                    {
//                        $TaxInfo = $this->getTaxNameInfo($TaxId);
//                        $TraceInfo = array(
//                                           "TaxId" => $TaxId,
//                                           "Message"      => $MessageResources->getMessage( new ActionMessage(array('TAX_CALCULATOR_WRN_010', prepareHTMLDisplay($TaxInfo["Name"]))))
//                                          );
//                        $this->addTraceInfo("6", $TraceInfo);
//                    }
                    continue;
                }
                if ($___TaxRatesList[0]["Applicable"] == "false")
                {
                    continue;
                }
                //                                                        .
                $___TaxRatesList[0]["Formula"] = strtr($___TaxRatesList[0]["Formula"], array('{p_1}' => '<p_1>'));

                if (!$selected_sm = $this->getTaxSetting('SELECTED_SHIPPING_MODULES_LIST'))
                {
                    $selected_sm = "a:0:{}";
                }
                $selected_sm = unserialize($selected_sm);
                if (!in_array($ShippingMethod, $selected_sm))
                {
                    if ($TotalShippingCost == PRICE_N_A)
                    {
                        $___TaxRatesList[0]["Formula"] = strtr($___TaxRatesList[0]["Formula"], array('{p_2}' => PRICE_N_A));
                    }
                    elseif($TotalShippingCost == 0)
                    {
                        $___TaxRatesList[0]["Formula"] = strtr($___TaxRatesList[0]["Formula"], array('{p_2}' => 0));
                    }
                    else
                    {
                    	//                                 ,    PerOrderShippingCost
                    	//                              .
                    	//                                    0,  . .                                0,
                    	//               PerOrdershippingCost                                            .
                    	$cart_quantity = modApiFunc("Cart", "getCartProductsQuantity");
                    	if($CartSubtotal == 0.0)
                    	{
                            if ($cart_quantity == 0)
                                $order_level_shipping_cost_share = $OrderLevelShippingCost;
                            else
                    	       $order_level_shipping_cost_share = $OrderLevelShippingCost*($ProductInfo["Quantity_In_Cart"] / $cart_quantity);
                    	}
                    	else
                    	{
                            $price = (isset($ProductInfo["CartItemSalePriceExcludingTaxes"])) ? $ProductInfo["CartItemSalePriceExcludingTaxes"] : $ProductInfo["CartItemSalePrice"];
                            $order_level_shipping_cost_share = $OrderLevelShippingCost * $price / $CartSubtotal;
                    	}
                        $shippingCostModifier = isset($ProductInfo["OptionsModifiers"]["shipping_cost"]) ? $ProductInfo["OptionsModifiers"]["shipping_cost"] : 0;
                        $___TaxRatesList[0]["Formula"] = strtr($___TaxRatesList[0]["Formula"], array('{p_2}' => (($ProductInfo["attributes"]["PerItemShippingCost"]["value"])? $ProductInfo["attributes"]["PerItemShippingCost"]["value"]:0) + (($ProductInfo["attributes"]["PerItemHandlingCost"]["value"])? $ProductInfo["attributes"]["PerItemHandlingCost"]["value"]:0) + $order_level_shipping_cost_share + $shippingCostModifier));
                    }
                }
                else
                {
                    $thisTaxAmounts["tax_on_shipping"] = false;
                    $___TaxRatesList[0]["Formula"] = strtr($___TaxRatesList[0]["Formula"], array('{p_2}' => 0));
                }

                //                    Discount:
                if(_ml_strpos($___TaxRatesList[0]["Formula"], '{p_3}') !== FALSE)
                {
                    if($TotalDiscount === COMPUTATION_POSTPONED)
                    {
                        $TotalDiscount = modApiFunc("Checkout", "getOrderPrice", "DiscountsSum", $currency_id);
                    }
                    if($OrderLevelDiscount === COMPUTATION_POSTPONED)
                    {
                        $OrderLevelDiscount = $TotalDiscount -
                                  modApiFunc("Checkout", "getOrderPrice", "QuantityDiscount", $currency_id);
                    }

                    if ($TotalDiscount == PRICE_N_A ||
                        $TotalDiscount == 0)
                    {
                        $___TaxRatesList[0]["Formula"] = strtr($___TaxRatesList[0]["Formula"], array('{p_3}' => 0));
                    }
                    else
                    {
                        //                                 ,    PerOrderDiscount
                        //                              .
                        //                                    0,  . .                                0,
                        //               PerOrderDiscount                                            .
                        $cart_quantity = modApiFunc("Cart", "getCartProductsQuantity");
                        if($CartSubtotal == 0.0)
                        {
                            if ($cart_quantity == 0)
                                $order_level_discount_share = $OrderLevelDiscount;
                            else
                                $order_level_discount_share = $OrderLevelDiscount*($ProductInfo["Quantity_In_Cart"] / $cart_quantity);
                        }
                        else
                        {
                            #12.01 $order_level_discount_share = $OrderLevelDiscount*$ProductInfo["CartItemSalePrice"] / $CartSubtotal;
                            $price = (isset($ProductInfo["CartItemSalePriceExcludingTaxes"])) ? $ProductInfo["CartItemSalePriceExcludingTaxes"] : $ProductInfo["CartItemSalePrice"];
                            $order_level_discount_share = $OrderLevelDiscount * $price / $CartSubtotal;
                        }
                        $___TaxRatesList[0]["Formula"] = strtr($___TaxRatesList[0]["Formula"], array('{p_3}' => (($ProductInfo["PerItemDiscount"])? $ProductInfo["PerItemDiscount"]:0) + $order_level_discount_share));
                    }
                }

                if (!isset($thisTaxAmounts["products"][$ProductInfo["CartID"]]["tax_rate_id"]))
                {
                    $thisTaxAmounts["products"][$ProductInfo["CartID"]]["tax_rate_id"] = array();
                }
//                if ($trace)
//                {
//                    $AdditionalMessage = "";
//                }
                while (!(_ml_strpos($___TaxRatesList[0]["Formula"], '{t_') === false))
                {
                    $pos = _ml_strpos($___TaxRatesList[0]["Formula"], '{t_');
                    $_taxId = _ml_substr($___TaxRatesList[0]["Formula"], $pos+3, _ml_strpos($___TaxRatesList[0]["Formula"], '}')-($pos+3));
                    if (!isset($thisTaxAmounts["products"][$ProductInfo["CartID"]][$_taxId]))
                    {
                        $thisTaxAmounts["products"][$ProductInfo["CartID"]][$_taxId] = 0;
                    }
                    if (!isset($thisTaxAmounts["products"][$ProductInfo["CartID"]]["tax_rate_id"][$_taxId]))
                    {
                        $thisTaxAmounts["products"][$ProductInfo["CartID"]]["tax_rate_id"][$_taxId] = 0;
                    }
                    $retval = $this->calculateTax($thisTaxAmounts, $Taxes, $_taxId, $TaxRatesList, $AddressesList, $ProductsList, $CartSubtotal, $OrderLevelShippingCost, $TotalShippingCost, $ShippingMethod, $OrderLevelDiscount, $TotalDiscount, $currency_id, $debug, $trace, $symbolic);
                    if ($retval == "fatal")
                    {
                        return $retval;
                    }
                    if ($thisTaxAmounts["products"][$ProductInfo["CartID"]][$_taxId] == PRICE_N_A)
                    {
                        $___TaxRatesList[0]["Formula"] = strtr($___TaxRatesList[0]["Formula"], array("{t_".$_taxId."}" => PRICE_N_A));
                    }
                    else
                    {
                        if(is_array($thisTaxAmounts["products"][$ProductInfo["CartID"]][$_taxId]))
                        {
                            $factors = $thisTaxAmounts["products"][$ProductInfo["CartID"]][$_taxId];
                            //                        .                                                 .
                            $___TaxRatesList[0]["Formula"] = strtr($___TaxRatesList[0]["Formula"], array("{t_".$_taxId."}" => $factors['k'] . " * <p_1> + " . $factors['b']));
                        }
                        else
                        {
                            $___TaxRatesList[0]["Formula"] = strtr($___TaxRatesList[0]["Formula"], array("{t_".$_taxId."}" => $thisTaxAmounts["products"][$ProductInfo["CartID"]][$_taxId]));
                        }
                    }
//                    if ($trace)
//                    {
//                        $TaxInfo = $this->getTaxNameInfo($_taxId);
//                        $AdditionalMessage.= $MessageResources->getMessage(new ActionMessage(array('TAX_CALCULATOR_WRN_012', prepareHTMLDisplay($TaxInfo["Name"]), modApiFunc("Localization", "format", $thisTaxAmounts["products"][$ProductInfo["CartID"]][$_taxId], "currency"), "Product ".$ProductInfo["CartID"])))."<br>";
//                    }
                }
                if (_ml_strpos($___TaxRatesList[0]["Formula"], sprintf("%d", PRICE_N_A)) === false)
                {
                    if($symbolic === true)
                    {
                        //                                                  {p_1}.                               -
                        //                           ,                                _   _       ,        _ _        .
                        $factors = array
                        (
                            "k" => NULL,
                            "b" => NULL,
                        );
                        $b_is_linear = $this->isTaxFormulaLinear($___TaxRatesList[0]["Formula"], $___TaxRatesList[0]["Rate"], "<p_1>", $factors);
                        if($b_is_linear === false)
                        {
                            $thisTaxAmounts["products"][$ProductInfo["CartID"]][$TaxId] = TAX_NOT_LINEAR;
                            //: report error.   CZ                                   .
                        }
                        else
                        {
                            $thisTaxAmounts["products"][$ProductInfo["CartID"]][$TaxId] = $factors;//SYMBOLIC_TAX_VALUE;
                        }
                    }
                    else
                    {
                        loadCoreFile('aal.class.php');
                        //print prepareArrayDisplay($___TaxRatesList);
                        //print prepareArrayDisplay(debug_backtrace());
                        $sid = $___TaxRatesList[0]["rates_set"];
                        if ($sid != 0)
                        {
                            if ($Taxes[$TaxId]["address"] == 1)
                            {
                                $shippingInfo = new ArrayAccessLayer(modApiFunc("Checkout", "getPrerequisiteValidationResults", "shippingInfo"));
                                $shippingInfo->setAccessMask("validatedData", AAL_CUSTOM_PARAM, "value");
                                $zip = $shippingInfo->getByMask('Postcode');
                                $rate = modApiFunc("TaxRateByZip", "getTaxRateByZip", $sid, $zip);
                            }
                            else if ($Taxes[$TaxId]["address"] == 2)
                            {
                                $billingInfo = new ArrayAccessLayer(modApiFunc("Checkout", "getPrerequisiteValidationResults", "billingInfo"));
                                $billingInfo->setAccessMask("validatedData", AAL_CUSTOM_PARAM, "value");
                                $zip = $billingInfo->getByMask('Postcode');
                                $rate = modApiFunc("TaxRateByZip", "getTaxRateByZip", $sid, $zip);
                            }
                        }
                        else
                        {
                            $rate = $___TaxRatesList[0]["Rate"];
                        }
                        $___TaxRatesList[0]["Formula"] = strtr($___TaxRatesList[0]["Formula"], array('<p_1>' => $ProductInfo["CartItemSalePriceExcludingTaxes"]));
                        $thisTaxAmounts["products"][$ProductInfo["CartID"]][$TaxId] = $rate * eval("return ".$___TaxRatesList[0]["Formula"].";")/100.00;
                    }
                }
                else
                {
                    $thisTaxAmounts["products"][$ProductInfo["CartID"]][$TaxId] = (int)PRICE_N_A;
                }
                $thisTaxAmounts["products"][$ProductInfo["CartID"]]["tax_rate_id"][$TaxId] = $___TaxRatesList[0]["Id"];

//                if ($trace)
//                {
//                    $TaxInfo = $this->getTaxNameInfo($TaxId);
//                    $TraceInfo = array(
//                                       "TaxId" => $TaxId,
//                                       "Message" => $AdditionalMessage.$MessageResources->getMessage(new ActionMessage(array('TAX_CALCULATOR_WRN_011', prepareHTMLDisplay($TaxInfo["Name"]), modApiFunc("Localization", "format", $thisTaxAmounts["products"][$ProductInfo["CartID"]][$TaxId], "currency"), $ProductInfo["Quantity_In_Cart"], modApiFunc("Localization", "format", ($thisTaxAmounts["products"][$ProductInfo["CartID"]][$TaxId]*$ProductInfo["Quantity_In_Cart"]), "currency"), "Product ".$ProductInfo["CartID"])))
//                                      );
//                    $this->addTraceInfo("6", $TraceInfo);
//                }
            }
//            if ($trace)
//            {
//                $TraceInfo = array("TaxId" => $TaxId);
//                $this->addTraceInfo("7", $TraceInfo);
//            }
            $Taxes[$TaxId]["status"] = "calculated";
        }
        return $retval;
    }

    /**
     *
     *
     * @param
     * @return
     */
    function getApplicableTaxRates($ProductTaxClassArray, $AddressArray)
    {
        $params = array('ProductTaxClassArray' => $ProductTaxClassArray,
                        'AddressArray' => $AddressArray);
        return execQuery('SELECT_APPLICABLE_TAX_RATES', $params);
    }

    /**
     *
     *
     * @param
     * @return
     */
    function getTaxes($b_included_only = false)
    {
        static $taxes;
        if (! isset($taxes)) {
            $taxes = array(array(), array());
            $result = execQuery('SELECT_TAX_LIST', array());
            foreach ($result as $tax)
            {
                $t = array(
                    "included_into_price" => $tax["included_into_price"],
                    "needs_address" => $tax["needs_address"],
                    "name"          => $tax["name"],
                    "address"       => $tax["addressId"],
                    "applicable"    => false,
                    "status"        => "not_calculated",
                    "value"         => (int)PRICE_N_A//"0"
                );
                $taxes[0][ $tax["id"] ] = $t;
                if ($b_included_only === true && $tax["included_into_price"] == DB_TRUE)
                {
                    $taxes[1][ $tax["id"] ] = $t;
                }
            }
        }
        $b_included_only = $b_included_only ? 1 : 0;
        return $taxes[$b_included_only];
    }

    /**
     * Gets information about Tax Classes.
     */
    function getProductTaxClassInfo($ptc_id)
    {
        global$application;
        $MessageResources = &$application->getInstance('MessageResources');

        $tables = $this->getTables();
        $ptc = $tables['product_tax_classes']['columns'];

        $query = new DB_Select();
        $query->addSelectField($ptc['id'],    'id');
        $query->addSelectField($ptc['name'],  'value');
        $query->WhereValue($ptc['id'], DB_EQ, $ptc_id);
        $result = $application->db->getDB_Result($query);
        return (sizeof($result) == 1)? $result[0]:array('id' => '0', 'value' => $MessageResources->getMessage("PRODUCT_TAX_CLASS_ANY_LABEL"));
    }

    function getConst($name)
    {
    	return constant($name);
    }
    /**#@-*/


    /**
     * checks if zip set is used in tax rates
     *
     * @param int $sid
     */
    function checkIfSetIsUsed($sid)
    {
        $result = execQuery("SELECT_TAXES_WHICH_USE_SET", array("sid" => $sid));

        if (count($result) > 0)
            return true;

        return false;
    }

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */
    var $TaxNamesList;
    var $CostList;

    /**
     * Error data, e.g. to be passed for mapping from Action to View.
     */
     var $ErrorData;
     //E.g. "contradictory formula" id, or "cyclic formula" cycle info.

    /**#@-*/
}

global $zone;
if($zone == 'AdminZone')
{
    loadModuleFile('taxes/includes/taxes_api_az.php');
    eval("class Taxes extends Taxes_AZ{};");
}
else
{
	eval("class Taxes extends TaxesBase{};");
}
?>