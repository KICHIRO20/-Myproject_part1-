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

loadModuleFile('catalog/abstract/product_class.php');

/**
 * Cart module
 *
 * @package Cart
 * @author Alexander Girin
 */
class Cart
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Module constructor.
     */
    function Cart()
    {
        $this -> CartContent = array();
        $this -> CartTotals = $this -> getInitialTotals();
        $this -> CartOrders = array();

        if (modApiFunc('Session', 'is_Set', 'CartContent'))
            $this -> CartContent = modApiFunc('Session', 'get', 'CartContent');

        if (modApiFunc('Session', 'is_Set', 'CartOrders'))
            $this -> CartOrders = modApiFunc('Session', 'get', 'CartOrders');

        $this -> buildDetailedCartContent();
    }

    /**
     * Returns initials totals for cart/order
     */
    function getInitialTotals()
    {
        return array(
            'Subtotal' => 0.0,
            'SubtotalExcludingTaxes' => 0.0,
            'SubtotalIncludedTaxesSum' => 0.0,
            'ProductsWeightNetto' => 0.0,
            'ProductsQuantity' => 0
        );
    }

    //                                         
    function loadState()
    {
    	//                              .
        //                    -                            
        $this->wasCartModifiedbyIntegrityCheck = false;
        $this->__checkCartIntegrity();
    }

    function __checkCartIntegrity()
    {
    	//                             
        //                 -                       !
        //                              ($cart_item_key, $cart_item)
        //                          false,                                      
        //                                                   
        $checkers = array(
            '__checkProductExists',
            '__checkProductVisibility',
            '__checkProductOnline',
            '__checkProductStockQuantityAttribute',
            '__checkProductHasOptions',
            '__checkProductOptionsExist',
            '__checkProductOptionsCombination',
            '__checkProductOptionsByRules',
            '__checkProductOptionsByInventory',
            '__updateProductOptionsModifiers',
        );

        foreach ($this->CartContent as $cart_item_key => $cart_item)
        {
            reset($checkers);
            for ($i=0; $i<count($checkers); $i++)
            {
                $check_method = $checkers[$i];
                if ($this->$check_method($cart_item_key, $cart_item) == false)
                {
                    unset($this->CartContent[$cart_item_key]);
                    $this->wasCartModifiedbyIntegrityCheck = true;
                    continue 2;
                }
            }

        }

        modApiFunc('Session', 'set', 'CartContent', $this -> CartContent);
        $this -> buildOrders($this->wasCartModifiedbyIntegrityCheck);
    }

    /**
     * Divides cart into orders
     * Note: currently the function is turned off...
     */
    function buildOrders($throw_event = true)
    {
        global $application;

        $this -> CartOrders = array();

        // paranoidal check...
        if (!is_array($this -> CartContent))
        {
            modApiFunc('Session', 'set', 'CartOrders', array());
            return;
        }

        $this -> CartOrders[0] = array(
            'Products' => array(),
            'Content'  => array(),
            'Totals'   => $this -> getInitialTotals(),
            'Entry'    => 0, // use this field to keep the unique identifier for each suborder
            'Lang'     => modApiFunc('MultiLang', 'getLanguage'),
            'Protocol' => $application -> getCurrentProtocol()
        );

        $this -> CartOrders[0]['Products'] = $this -> CartContent;

        // here we have cart divided...
        // calculating the order totals...
        foreach($this -> CartOrders as $k => $v)
            $this -> buildOrder($k);

        // saving the result in the session
        modApiFunc('Session', 'set', 'CartOrders', $this -> CartOrders);

        $this -> buildDetailedCartContent();
        $taxes = modApiFunc('Taxes', 'getTax');

        foreach($this -> CartOrders as $k => $v)
            $this -> buildOrderTaxes($k, $taxes);

        $this -> buildDetailedCartContent();

        // saving the result in the session
        modApiFunc('Session', 'set', 'CartOrders', $this -> CartOrders);

        if ($throw_event)
            modApiFunc('EventsManager', 'throwEvent', 'CartChanged');

        $cache = CCacheFactory::getCache('temporary');
        $cache->write( 'cart_content_hash', md5(serialize($this->CartContent)) );
    }

    function buildDetailedCartContent()
    {
        if (empty($this -> CartOrders) && !empty($this -> CartContent))
            $this -> buildOrders();

        $this -> checkOrderLanguage();
        $this -> checkOrderProtocol();

        $this -> DetailedCartContent = array();
        $this -> CartTotals = $this -> getInitialTotals();

        foreach($this -> CartOrders as $v)
        {
            foreach($v['Content'] as $vv)
                $this -> DetailedCartContent[] = $vv;

            foreach($v['Totals'] as $kk => $vv)
                $this -> CartTotals[$kk] += $vv;
        }
    }

    function checkOrderLanguage()
    {
        foreach($this -> CartOrders as $k => $v)
            if ($v['Lang'] != modApiFunc('MultiLang', 'getLanguage'))
            {
                $this -> buildOrders(false);
                return;
            }
    }

    function checkOrderProtocol()
    {
        global $application;

        foreach($this -> CartOrders as $k => $v)
            if ($v['Protocol'] != $application -> getCurrentProtocol())
            {
                $this -> buildOrders(false);
                return;
            }
    }

    function __checkProductOptionsByRules($cart_item_key, $cart_item)
    {
        //          ,                                     
        return modApiFunc("Product_Options","checkByCRules",'product', $cart_item['product_id'], $cart_item['options']);
    }

    function __checkProductOptionsByInventory($cart_item_key, $cart_item)
    {
        $obj_product = new CProductInfo($cart_item['product_id']);
        $stock_method = $obj_product->whichStockControlMethod();
        if ($stock_method == PRODUCT_OPTIONS_INVENTORY_TRACKING)
        {
            $options_settings = modApiFunc("Product_Options", "getOptionsSettingsForEntity", 'product', $cart_item['product_id']);

            //                              Inventory
            // AANIC - Allow Add the product to the cart Not in Inventory Control
            if($options_settings['AANIC']=='N')
            {
                $inv_id = modApiFunc("Product_Options", "getInventoryIDByCombination", 'product', $cart_item['product_id'], $cart_item['options']);
                if($inv_id==null) //           ,                                                   Inventory Control
                {
                    return false;
                }
            }

            //                              
            // AANIS - Allow Add the product to the cart Not In Stock
            if($options_settings['AANIS']=='N')
            {
                $inv_id = modApiFunc("Product_Options", "getInventoryIDByCombination", 'product', $cart_item['product_id'], $cart_item['options']);
                if($inv_id != null)
                {
                    //         ,        inventory               
                    $inv_info = modApiFunc('Product_Options','getInventoryInfo',$inv_id);
                    if($inv_info['quantity'] <= 0)
                    {
                        return false;
                    }
                };
            };

            //          ,                           ,            inventory tracking
            if(modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_ALLOW_BUY_MORE_THAN_STOCK) === false)
            {
                $inv_id = modApiFunc("Product_Options", "getInventoryIDByCombination", 'product', $cart_item['product_id'], $cart_item['options']);
                if($inv_id != null)
                {
                    if($options_settings['AANIS']=='Y')
                    {
                    	$inv_info = modApiFunc('Product_Options','getInventoryInfo',$inv_id);
                    }

                	if($options_settings['AANIS']=='N' && $inv_info['quantity'] < $cart_item['quantity'])
                    {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    function __checkProductStockQuantityAttribute($cart_item_key, $cart_item)
    {
        //          ,                                             
        $obj_product = new CProductInfo($cart_item['product_id']);
        $stock_method = $obj_product->whichStockControlMethod();
        if ($stock_method == PRODUCT_QUANTITY_IN_STOCK_ATTRIBUTE)
        {
            $qty_in_stock = $obj_product->getProductTagValue('QuantityInStock',PRODUCTINFO_NOT_LOCALIZED_DATA);
            $qty_already_in_cart = $cart_item['quantity'];

            //                                ,                                                      ,                 
            if($qty_in_stock != '' && $qty_in_stock <  $qty_already_in_cart &&
               modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_ALLOW_BUY_MORE_THAN_STOCK) === false )
            {
                return false;
            }

            //         ,               "min order"               -                                       .
            $min_order = $obj_product->getProductTagValue('MinQuantity',PRODUCTINFO_NOT_LOCALIZED_DATA);
            if($min_order != '' &&
               $min_order >  $qty_already_in_cart)
            {
                return false;
            }
        }
        return true;
    }

    function __checkProductExists($cart_item_key, $cart_item)
    {
        //                                           
        if (modApiFunc('Catalog', 'isCorrectProductId', $cart_item['product_id']) == false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function __checkProductVisibility($cart_item_key, $cart_item)
    {
        $prod_obj = new CProductInfo($cart_item['product_id']);
        $visibility = $prod_obj->getProductTagValue('MembershipVisibility', PRODUCTINFO_NOT_LOCALIZED_DATA);
        if($visibility=="" || $visibility=="-1")
            return true;
        $cur_customer_gr = modApiFunc('Customer_Account','getCurrentSignedCustomerGroupID');
        if(in_array($cur_customer_gr, explode('|', $visibility)))
            return true;
        return false;
    }

    function __checkProductOnline($cart_item_key, $cart_item)
    {
        $prod_obj = new CProductInfo($cart_item['product_id']);
        if ($prod_obj->haveOnlineCategory()==true && $prod_obj->getProductTagValue('Available', PRODUCTINFO_NOT_LOCALIZED_DATA)==PRODUCT_STATUS_ONLINE)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function __checkProductOptionsExist($cart_item_key, $cart_item)
    {
    	if ((is_array($cart_item['options'])) && (!empty($cart_item['options'])))
        {
        	//        id-                                      (             ,          )
            $product_options_id_list = array_keys(modApiFunc('Product_Options','__getOptionsWithValuesAsIDsArray','product', $cart_item['product_id']));

            //        id-                                        
            $cart_item_options_id_list = array_keys($cart_item['options']);

            //                                        multiselect                                .
            $data=array(
                'parent_entity' => 'product'
               ,'entity_id' => $cart_item['product_id']
               ,'options' => $cart_item['options']
	     		,'colorname' => $cart_item['colorname']

            );

            $options=modApiFunc('Product_Options','__getOptionsWithValuesAsIDsArray',$data['parent_entity'],$data['entity_id']);
            foreach ($options as $id=>$opt)
            {
            	if (($opt['type'] == "MS" && !isset($cart_item['options'][$id]))
 //           	|| ($opt['type'] == "CBSI" && !isset($cart_item['options'][$id]['cb']) && empty($cart_item['options'][$id]['val']))
            	|| ($opt['type'] == "SS" && empty($options[$id]['values'])))
            	{
            		$cart_item_options_id_list[] = $id;
            	}
            }

            //                       
            sort($product_options_id_list);
            sort($cart_item_options_id_list);

            return ($product_options_id_list === $cart_item_options_id_list);
        }
        return true;
    }

    function __checkProductHasOptions($cart_item_key, $cart_item)
    {
        if (is_array($cart_item['options']))
        {
            //          ,                         
            $product_options_array = modApiFunc('Product_Options','__getOptionsWithValuesAsIDsArray','product', $cart_item['product_id']);
            if (empty($product_options_array) and !empty($cart_item['options']))
            {
                //                     ,                                ,                                        
                return false;
            }
        }
        return true;
    }

    function __checkProductOptionsCombination($cart_item_key, $cart_item)
    {
        if (is_array($cart_item['options']))
        {
            //                                   
            $data=array(
                'parent_entity' => 'product'
               ,'entity_id' => $cart_item['product_id']
               ,'options' => $cart_item['options']
				,'colorname' => $cart_item['colorname']


            );
            list($check_result, $data) = modApiFunc("Product_Options","checkCombination",$data);
            //                                                     -                                 
            if(!empty($check_result))
            {
                return false;
            }
        }
        return true;
    }

    function __updateProductOptionsModifiers($cart_item_key, $cart_item)
    {
        if (is_array($cart_item['options']))
        {
            //                              inventory_id                        
            $this->CartContent[$cart_item_key]['modifiers'] = modApiFunc("Product_Options","getCombinationModifiers",$cart_item['options']);
            $this->CartContent[$cart_item_key]['inventory_id'] = modApiFunc("Product_Options","getInventoryIDByCombination",'product',$cart_item['product_id'],$cart_item['options']);
        }
        return true;
    }


    /**
     * Installs the specified module in the system.
     *
     * The install() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Cart::getTables() instead of $this->getTables()
     */
    function install()
    {
        global $application;

        $tables = Cart::getTables();           #the array of the Cart module tables
        $query = new DB_Table_Create($tables);
    }

    /**
     * Deinstalls the module.
     *
     * The uninstall() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Cart::getTables() instead of $this->getTables().
     *
     * @todo finish the functions on this page
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(Cart::getTables());
        global $application;
        $application->db->getDB_Result($query);
    }

    /**
     * Checks if the module was installed.
     *
     * @todo finish the functions on this page
     * @return
     */
    function isInstalled()
    {
    }

    /**
     * Gets the array of meta description of module tables.
     *
     * @todo May be add more tables
     * @return array - meta description of module tables
     */
    function getTables()
    {
        $tables = array ();
        $table_name = 'customer_basket';
        $tables[$table_name] = array();
        $tables[$table_name]['columns'] = array
        (
            'id'                => 'customer_basket.customer_busket_id'
           ,'c_id'              => 'customer_basket.customer_id'
           ,'p_id'              => 'customer_basket.product_id'
           ,'prod_quan'         => 'customer_basket.customer_busket_product_quantity'
           ,'date_added'        => 'customer_basket.customer_busket_date_added'
        );
        $tables[$table_name]['types'] = array
        (
            'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
           ,'c_id'              => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
           ,'p_id'              => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
           ,'prod_quan'         => DBQUERY_FIELD_TYPE_INT
           ,'date_added'        => DBQUERY_FIELD_TYPE_DATE
        );
        $tables[$table_name]['primary'] = array
        (
            'id'
        );
        $tables[$table_name]['indexes'] = array
        (
            'IDX_ci' => 'c_id'
           ,'IDX_pi' => 'p_id'
        );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function getCartContent($currency_id = null, $order_index = null)
    {
        if (empty($this -> DetailedCartContent) && !empty($this -> CartContent))
            $this -> buildOrders();

        if ($order_index === null)
            return $this -> convert_cart_content_currency($this -> DetailedCartContent, $currency_id);

        if (isset($this -> CartOrders[$order_index]['Content']))
            return $this -> convert_cart_content_currency($this -> CartOrders[$order_index]['Content'], $currency_id);

        return array();
    }

    function getCartContentExt()
    {
        return $this -> getCartContent();
    }

    function getCartTotals($order_index = null)
    {
        if (empty($this -> DetailedCartContent) && !empty($this -> CartContent))
            $this -> buildOrders();

        if ($order_index === null)
            return $this -> CartTotals;

        if (isset($this -> CartOrders[$order_index]['Totals']))
            return $this -> $this -> CartOrders[$order_index]['Totals'];

        return $this -> getInitialTotals();
    }

    function getCartOrders()
    {
        if (empty($this -> DetailedCartContent) && !empty($this -> CartContent))
            $this -> buildOrders();

        return $this -> CartOrders;
    }

    function getCartOrderIndexes()
    {
        if (empty($this -> DetailedCartContent) && !empty($this -> CartContent))
            $this -> buildOrders();

        $retval = array();

        foreach($this -> CartOrders as $k => $v)
            $retval[] = $k;

        return $retval;
    }

    // this function is not used yet...
    function getOrderEntry($order_index)
    {
        if (empty($this -> DetailedCartContent) && !empty($this -> CartContent))
            $this -> buildOrders();

        if (!isset($this -> CartOrders[$order_index]))
            return null;

        return $this -> CartOrders[$order_index]['Entry'];
    }

    function buildOrder($order_index)
    {
        if (!isset($this -> CartOrders[$order_index]))
            return;

        $mods_map = modApiFunc('Product_Options', 'getModsMap');
        $flip_mods_map = array_flip($mods_map);

        $retval = array();

        $Totals = $this -> getInitialTotals();

        if (!empty($this -> CartOrders[$order_index]['Products']))
        {
            $i = 0;

            // whether to treat all products in group as one product
            // regardless of it's options
            $qtydsc_discard_options = modApiFunc('Settings', 'getParamValue',
                                                 'QUANTITY_DISCOUNT',
                                                 'QUANTITY_DISCOUNT_BEHAVIOR');

            if ($qtydsc_discard_options == "YES")
            {
                $groups = array();
                foreach ($this -> CartOrders[$order_index]['Products']
                         as $key => $val)
                {
                    if (isset($groups[$val["product_id"]]))
                    {
                        $groups[$val["product_id"]] += $val["quantity"];
                    }
                    else
                    {
                        $groups[$val["product_id"]] = $val["quantity"];
                    }
                }
            }

            foreach ($this -> CartOrders[$order_index]['Products']
                     as $key => $val)
            {
                $retval[$i] = $this -> buildProduct($val, $flip_mods_map, @$groups[$val['product_id']]);
                $retval[$i]['CartID'] = $key;

                $Totals['Subtotal'] += $retval[$i]['Total'];
                $Totals['SubtotalExcludingTaxes'] += $retval[$i]['TotalExcludingTaxes'];
                $Totals['ProductsWeightNetto'] += (($retval[$i]['CartItemWeight']) * $val['quantity']);
                $Totals['ProductsQuantity'] += $val['quantity'];
                $i++;
            }
        }

        $this -> CartOrders[$order_index]['Content'] = $retval;
        $this -> CartOrders[$order_index]['Totals'] = $Totals;
    }

    function buildOrderTaxes($order_index, $ComputedTaxes = null)
    {
        if (!isset($this -> CartOrders[$order_index]))
            return;

        $mods_map = modApiFunc('Product_Options', 'getModsMap');
        $flip_mods_map = array_flip($mods_map);

        $TaxNames = modApiFunc('Taxes', 'getTaxNames');

        $retval = $this -> CartOrders[$order_index]['Content'];
        $Totals = $this -> CartOrders[$order_index]['Totals'];

        //                                              .
        if ($ComputedTaxes !== NULL &&
            !empty($this -> CartOrders[$order_index]['Products']))
        {
            $i=0;
            foreach ($this -> CartOrders[$order_index]['Products']
                     as $key => $val)
            {
                $retval[$i]['CartItemSalePriceIncludingTaxes'] = $retval[$i]['attributes']['salepriceexcludingtaxes']['value'];
                if(array_key_exists('SalePrice', $flip_mods_map))
                {
                    $options_price_including_included_taxes = $val['modifiers'][$flip_mods_map['SalePrice']];
                    $options_price_excluding_included_taxes = modApiFunc('Catalog', 'computePriceExcludingTaxes',
                                                                         $options_price_including_included_taxes,
                                                                         $retval[$i]['attributes']['TaxClass']['value']);
                    $retval[$i]['CartItemSalePriceIncludingTaxes'] += $options_price_excluding_included_taxes;
                }

                $IncludedTaxesSum = 0.0;
                //                       :
                if (isset($ComputedTaxes['products'][$key]))
                {
                    foreach($ComputedTaxes['products'][$key] as $tax_id => $tax_amount)
                    {
                        if (is_numeric($tax_id) && $tax_amount != PRICE_N_A)
                        {
                            if ($TaxNames[$tax_id]['included_into_price'] == 'true')
                            {
                                $IncludedTaxesSum += $tax_amount;
                            }
                        }
                    }
                    $retval[$i]['CartItemSalePriceIncludingTaxes'] += $IncludedTaxesSum;
                }
                if($retval[$i]['CartItemSalePriceIncludingTaxes'] < 0)
                    $retval[$i]['CartItemSalePriceIncludingTaxes'] = 0;

                $retval[$i]['TotalIncludingTaxes'] = ($retval[$i]['CartItemSalePriceIncludingTaxes']) * $val['quantity'];
                if ($retval[$i]['TotalIncludingTaxes'] < 0)
                    $retval[$i]['TotalIncludingTaxes'] = 0;

                $Totals['SubtotalIncludedTaxesSum'] += $IncludedTaxesSum * $val['quantity'];
                $i++;
            }
        }

        // additional way to compute this value
        if ($Totals['SubtotalIncludedTaxesSum'] == 0)
        {
            foreach ($retval as $key => $val)
            {
                $delta = $val['CartItemSalePriceIncludingTaxes'] - $val['CartItemSalePriceExcludingTaxes'];
                $Totals['SubtotalIncludedTaxesSum'] += $delta * $val['Quantity_In_Cart'];
            }
        }

        $this -> CartOrders[$order_index]['Content'] = $retval;
        $this -> CartOrders[$order_index]['Totals'] = $Totals;
    }

    /**
     *    .   -                                   .             -                               ,
     *              direct                                                                      
     *       ,             main store currency.
     *
     * @param unknown_type $retval
     * @param unknown_type $currency_id
     */
    function convert_cart_content_currency($retval, $currency_id)
    {
    	if ($currency_id === NULL)
    	{
    		return $retval;
    	}
    	else
    	{
    		$from = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getMainStoreCurrency"));
    		$to = modApiFunc("Localization", "getCurrencyCodeById", $currency_id);
    		if($from == $to)
    		{
    			return $retval;
    		}
    		else
    		{
	            $keys = array
	            (
	                "CartItemSalePriceExcludingTaxes"
	               ,"CartItemSalePriceIncludingTaxes"
	               ,"CartItemSalePrice"

	               ,"CartItemPerItemShippingCost"
	               ,"CartItemPerItemHandlingCost"
	               ,"TotalExcludingTaxes"
	               ,"Total"
	               ,"TotalIncludingTaxes"
	            );
	            $option_modifiers = array
	            (
	                "price"

	               ,"shipping_cost"
	               ,"handling_cost"
	            );
	            $attributes = array
	            (
	                "SalePrice"
	               ,"ListPrice"
	               ,"PerItemShippingCost"
	               ,"PerItemHandlingCost"
	               ,"salepriceincludingtaxes"
	               ,"salepriceexcludingtaxes"
	            );
	            foreach($retval as $id => $info)
	            {
                    $info = &$retval[$id];

	                foreach($keys as $key)
	                {
	                    if(isset($info[$key]))
	                    {
	                    	$info[$key] = modApiFunc("Currency_Converter", "convert", $info[$key], $from, $to);
	                    }
	                }
	                foreach($option_modifiers as $modifier_name)
	                {
	                	if(isset($info['OptionsModifiers'][$modifier_name]))
	                	{
	                		$info['OptionsModifiers'][$modifier_name] = modApiFunc("Currency_Converter", "convert", $info['OptionsModifiers'][$modifier_name], $from, $to);
	                	}
	                }
	                foreach($attributes as $attr_name)
	                {
	                	if(isset($info['attributes'][$attr_name]) &&
	                	   isset($info['attributes'][$attr_name]['value']))
	                	{
	                		$info['attributes'][$attr_name]['value'] = modApiFunc("Currency_Converter", "convert", $info['attributes'][$attr_name]['value'], $from, $to);
	                	}
	                }
	                unset($info);
	            }
                return $retval;
    		}
    	}
    }
    /**
     * Gets a cart total.
     *
     * @todo add a user
     * @return float - a cart total
     */
    function getCartSubtotal($order_index = null)
    {
        if (empty($this -> CartOrders) && !empty($this -> CartContent))
            $this -> buildOrders();

        if ($order_index === null)
            return $this -> CartTotals['Subtotal'];

        if (isset($this -> CartOrders[$order_index]))
            return $this -> CartOrders[$order_index]['Totals']['Subtotal'];

        return 0.0;
    }

    /**
     * Gets a cart total.
     *
     * @todo add a user
     * @return float - a cart total
     */
    function getCartSubtotalExcludingTaxes($order_index = null)
    {
        if (empty($this -> CartOrders) && !empty($this -> CartContent))
            $this -> buildOrders();

        if ($order_index === null)
            return $this -> CartTotals['SubtotalExcludingTaxes'];

        if (isset($this -> CartOrders[$order_index]))
            return $this -> CartOrders[$order_index]['Totals']['SubtotalExcludingTaxes'];

        return 0.0;
    }

    /**
     * Gets a cart total.
     *
     * @todo add a user
     * @return float - a cart total
     */
    function getCartSubtotalIncludedTaxesSum($order_index = null)
    {
        if (empty($this -> CartOrders) && !empty($this -> CartContent))
            $this -> buildOrders();

        if ($order_index === null)
            return $this -> CartTotals['SubtotalIncludedTaxesSum'];

        if (isset($this -> CartOrders[$order_index]))
            return $this -> CartOrders[$order_index]['Totals']['SubtotalExcludedTaxesSum'];

        return 0.0;
    }

    /**
     * Gets a total weight.
     *
     * @todo
     * @return float - weight
     */
    function getCartProductsWeightNetto($order_index = null)
    {
        if (empty($this -> CartOrders) && !empty($this -> CartContent))
            $this -> buildOrders();

        if ($order_index === null)
            return $this -> CartTotals['ProductsWeightNetto'];

        if (isset($this -> CartOrders[$order_index]))
            return $this -> CartOrders[$order_index]['Totals']['ProductsWeightNetto'];

        return 0.0;
    }

    /**
     * Gets a total products quantity in the cart.
     *
     * @todo add a user
     * @return float - total cart quantity
     */
    function getCartProductsQuantity($order_index = null)
    {
        if (empty($this -> CartOrders) && !empty($this -> CartContent))
            $this -> buildOrders();

        if ($order_index === null)
            return $this -> CartTotals['ProductsQuantity'];

        if (isset($this -> CartOrders[$order_index]))
            return $this -> CartOrders[$order_index]['Totals']['ProductsQuantity'];

        return 0;
    }

    /**
     * Sets up the cart contents.
     *
     * @todo add a user
     * @param array $cart_content cart contents array
     * @return
     */
    function setCartContent($cart_content)
    {
        $this -> CartContent = $cart_content;
        $this -> buildOrders();
    }

    /**
     * Adds a product into the cart
     *
     * @todo add a user
     * @param array $prod_data product id a set of options
     * @return
     */
    function addToCart($prod_data)
    {
        global $application;
        if (modApiFunc('Catalog','isCorrectProductId',$prod_data['entity_id']))
        {
            //                 storefront -          ,                  Offline:
            if (modApiFunc('Users', 'getZone') == "CustomerZone")
            {
                $prod = new CProductInfo($prod_data['entity_id']);

                //         ,                OutOfStock                               .
                $store_show_absent = modApiFunc("Configuration", "getValue", "store_show_absent");

                //             OutOfStock -                       .
                $qty_in_stock = $prod->getProductTagValue('QuantityInStock', PRODUCTINFO_NOT_LOCALIZED_DATA);

                $stock_method = $prod->whichStockControlMethod();

                if($prod->haveOnlineCategory() &&
                    (
                        $stock_method == PRODUCT_OPTIONS_INVENTORY_TRACKING
                        ||
                        (
                          (
                              $qty_in_stock === ""
                          )
                          ||
                          (
                              $store_show_absent != STORE_SHOW_ABSENT_SHOW_NOT_BUY &&
                              $store_show_absent != STORE_SHOW_ABSENT_NOT_SHOW_NOT_BUY
                          )
                          ||
                          (
                              $qty_in_stock > 0
                          )
                        )
                     )
                  )
                {


				if ($prod_data['colorname'] == '' )
				{
                    $cart_id=$prod_data["entity_id"]."_".modApiFunc("Product_Options","getCombinationHash",$prod_data['options']);
				}
				else
				{
					 $cart_id=$prod_data["entity_id"] . "_" . modApiFunc("Product_Options","getCombinationHash",$prod_data["options"]) . "_" . modApiFunc("ColorSwatch","getColorHash",$prod_data["colorname"]);
				}




                    $qty = $prod_data['qty'];
                    $qty = ( $qty !== NULL && is_numeric($qty) && $qty > 0) ? $qty : 1;

                    if (array_key_exists($cart_id, $this->CartContent))
                    {
                        $add_to_cart_add_not_replace = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_ADD_TO_CART_ADD_NOT_REPLACE);
                        if($add_to_cart_add_not_replace === true)
                        {
                            $this->CartContent[$cart_id]['quantity'] += $qty;
                        }
                        else
                        {
                            $this->CartContent[$cart_id]['quantity'] = $qty;
                        }
                    }
                    else
                    {
                        //                   ,  . .                           ,                        "               "
                        if (empty($this->CartContent))
                        {
                            modApiFunc('EventsManager','throwEvent', 'CartCreated');
                        }

                        $this->CartContent[$cart_id] = array(
                            'product_id' => $prod_data['entity_id']
                           ,'quantity' => $qty
                           ,'options' => $prod_data['options']

,'colorname' => $prod_data['colorname']

                           ,'modifiers' => modApiFunc("Product_Options","getCombinationModifiers",$prod_data['options'])
                           ,'inventory_id' => modApiFunc("Product_Options","getInventoryIDByCombination",'product',$prod_data['entity_id'],$prod_data['options'])
                        );
                    }

                    modApiFunc('EventsManager','throwEvent', 'ProductAddedToCart', $prod_data["entity_id"], $qty);

                    modApiFunc('Session', 'set', 'CartContent', $this->CartContent);
                    $this -> buildOrders();
                    return true;
                }
                else
                {
                    $query = new Request($application->getAppIni('SITE_URL'));
                    //     :          -           .
                    $application->redirect($query);
                }
            }
        }
        return false;
    }

    // remove Gift Certificate from DB
    function removeGCfromDB($prod_id)
    {
        $prodObj = new CProductInfo($prod_id);
        if($prodObj->getProductTagValue('typeid') == -1)
        {
            modApiFunc('Catalog','deleteProductsArray',array($prod_id));
        }
    }

    /**
     * Removes the product from the cart.
     *
     * @todo add a user
     * @param string $cart_id id of the cart element
     * @return
     */
    function removeFromCart($cart_id)
    {
        if (array_key_exists($cart_id, $this->CartContent))
        {
            if(!empty($this->CartContent[$cart_id]['options']))
            {
                foreach($this->CartContent[$cart_id]['options'] as $oinfo)
                {
                    if(is_array($oinfo) and array_key_exists('is_file',$oinfo) and ($oinfo['val'] != ''))
                    {
                        modApiFunc('Shell','removeDirectory',dirname($oinfo['val']));
                    };
                };
            };

            $prod_id = $this->CartContent[$cart_id]['product_id'];
            $stat = array( array('PRODUCT_ID'=>$prod_id, 'QUANTITY'=>$this->CartContent[$cart_id]['quantity']) );
            modApiFunc('EventsManager','throwEvent', 'ProductRemovedFromCart', $stat);

            $this->removeGCfromDB($prod_id);

            unset($this->CartContent[$cart_id]);
        }

        modApiFunc('Session', 'set', 'CartContent', $this->CartContent);
        $this -> buildOrders();
    }

    /**
     * Removes all products from the cart.
     *
     *               $del_option_files       true,                ,                              
     *                           delete all.  . .                    ,                        
     *                                   -                  .
     *
     * @todo add a user
     * @return
     */
    function removeAllFromCart($del_option_files = false, $rebuild_orders = false)
    {
        if($del_option_files)
        {
            $stat = array();
            foreach($this->CartContent as $cart_id => $cart_item)
            {
                $stat[] = array('PRODUCT_ID'=>$cart_item['product_id'], 'QUANTITY'=>$cart_item['quantity']);
                if(!empty($cart_item['options']))
                {
                    foreach($cart_item['options'] as $oinfo)
                    {
                        if(is_array($oinfo) and array_key_exists('is_file',$oinfo) and ($oinfo['val'] != ''))
                        {
                            modApiFunc('Shell','removeDirectory',dirname($oinfo['val']));
                        };
                    };
                };
            };
            if (count($stat) > 0)
                modApiFunc('EventsManager','throwEvent', 'ProductRemovedFromCart', $stat);
        };
        unset($this->CartContent);

        $this->CartContent = array();
        modApiFunc('Session', 'set', 'CartContent', $this->CartContent);

        $this -> CartOrders = array();
        modApiFunc('Session', 'set', 'CartOrders', array());

        modApiFunc("PromoCodes", "removePromoCode");

        if ($rebuild_orders)
            $this -> buildOrders();
    }

    /**
     * Updates the cart contents.
     *
     * @todo add a user
     * @param string $cart_id id of the cart element
     * @param integer $quantity_in_cart element quantity in the cart
     * @return
     */
    function updateQuantityInCart($cart_id, $quantity_in_cart)
    {
        $quantity_in_cart=intval($quantity_in_cart);
        if (array_key_exists($cart_id, $this->CartContent))
        {
            $prev_qty = $this->CartContent[$cart_id]['quantity'];
            $pid = $this->CartContent[$cart_id]['product_id'];
            if($quantity_in_cart>0)
            {
                $this->CartContent[$cart_id]['quantity'] = $quantity_in_cart;

                $data = array('PRODUCT_ID' => $pid, 'PREV_QTY'=>$prev_qty, 'NEW_QTY'=>$quantity_in_cart);
                modApiFunc('EventsManager','throwEvent', 'ProductQuantityInCartUpdated', $data);
            }
            else
            {
                unset($this->CartContent[$cart_id]);
                $stat = array(array('PRODUCT_ID'=>$pid, 'QUANTITY'=>$prev_qty));
                modApiFunc('EventsManager','throwEvent', 'ProductRemovedFromCart', $stat);
            }
        }

        modApiFunc('Session', 'set', 'CartContent', $this->CartContent);
        $this -> buildOrders();
    }

    function getCartInfo($entity, $order_index = null)
    {
        switch (_ml_strtolower($entity))
        {
            case 'shoppingcartproductsquantity':
                return $this->getCartProductsQuantity($order_index);
            case "shoppingcartsubtotal":
            {
                $display_product_price_including_taxes = modApiFunc('Settings', 'getParamValue', 'TAXES_PARAMS', "DISPLAY_PRICES_W_INCLUDED_TAXES");
                if($display_product_price_including_taxes == DB_TRUE)
		{
			/* $price = $this -> getCartSubtotalExcludingTaxes($order_index) +
			   ($this -> getCartSubtotalIncludedTaxesSum($order_index) != PRICE_N_A
			   ? $this -> getCartSubtotalIncludedTaxesSum($order_index)
			   : 0.0);*/
			$price = $this -> getCartSubtotal($order_index) ;
		}
		else
		{
                    $price = $this -> getCartSubtotalExcludingTaxes($order_index);
                }

                $price_formatted = modApiFunc("Localization", "currency_format", $price);
                return $price_formatted;
            }
            case "shoppingcartdiscountedsubtotal":
            {
                $price = modApiFunc("Checkout", "getOrderPrice", "DiscountedSubtotal", modApiFunc("Localization", "getMainStoreCurrency"), $order_index);
                $price_formatted = modApiFunc("Localization", "currency_format", $price);
                return $price_formatted;
            }
            case "shoppingcartglobaldiscount":
            {
                $price = modApiFunc("Checkout", "getOrderPrice", "SubtotalGlobalDiscount", modApiFunc("Localization", "getMainStoreCurrency"), $order_index);
                $price_formatted = modApiFunc("Localization", "currency_format", $price);
                return $price_formatted;
            }
            case "shoppingcartpromocodediscount":
            {
                $price = modApiFunc("Checkout", "getOrderPrice", "SubtotalPromoCodeDiscount", modApiFunc("Localization", "getMainStoreCurrency"), $order_index);
                $price_formatted = modApiFunc("Localization", "currency_format", $price);
                return $price_formatted;
            }
            case "shoppingcartquantitydiscount":
            {
                $price = modApiFunc("Checkout", "getOrderPrice", "QuantityDiscount", modApiFunc("Localization", "getMainStoreCurrency"), $order_index);
                $price_formatted = modApiFunc("Localization", "currency_format", $price);
                return $price_formatted;
            }
        }
    }

    //                                         $product_id.
    //   -                                -                      
    //                                                             cart_item_id.
    function getProductQuantity($product_id)
    {
        //                     .
        //                                                   ProductQuantityOptions,
        //   . .                              -                                      
        //  cart_item' ,      product_id,                                          ,
        //                       ProducInfoCZ                             .

    	//                               ,                              ,                 
    	//                                     .         ,      
    	//1)                                                                       .
    	//2)                        ,                      ,                                 .

        $res = 0;
        foreach($this->CartContent as $cart_item_info)
        {
            if($cart_item_info['product_id'] == $product_id)
            {
                $res += $cart_item_info['quantity'];
            }
        }
        return $res;
    }

    //                                 HTML        "<option></option>",
    //                           
    //  Store Settings::General Settings::AddToCart Quantity Values List
    //     $Quantity_In_Cart              , NULL     0 (                    
    //                              ),                               .
    function getProductQuantityOptions($Quantity_In_Cart, $product_id, $b_cart_view = false, $ignore_stock = false, $force_quantity = false)
    {
        global $application;

        $add_to_cart_default_quantity = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_ADD_TO_CART_DEFAULT_QUANTITY);
        $add_to_cart_max_quantity = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_ADD_TO_CART_MAX_QUANTITY);
        $add_to_cart_limit_max_quantity_by_stock = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_ADD_TO_CART_LIMIT_MAX_QUANTITY_BY_STOCK);
        $add_to_cart_add_not_replace = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_ADD_TO_CART_ADD_NOT_REPLACE);

        if($Quantity_In_Cart === NULL ||
           $Quantity_In_Cart === "" ||
           $Quantity_In_Cart === 0)
        {
            //                            .

            $default_quantity = $add_to_cart_default_quantity;
            $selected_quantity = $default_quantity;
        }
        else
        {
            //                                .

            //                                     ,                             
            //                    ,               .
            if($add_to_cart_add_not_replace === false || $b_cart_view === true
               || $force_quantity === true)
            {
                $selected_quantity = $Quantity_In_Cart;
            }
            else
            {
                $default_quantity = $add_to_cart_default_quantity;
                $selected_quantity = $default_quantity;
            }
        }

        $res = "";
        $max_quantity = $add_to_cart_max_quantity;
        $_Product_Info = &$application->getInstance('CProductInfo', $product_id);

        $qty_in_stock = $_Product_Info->getProductTagValue('QuantityInStock', PRODUCTINFO_NOT_LOCALIZED_DATA);

        if($add_to_cart_limit_max_quantity_by_stock === true && !$ignore_stock)
        {
            //                          .

            //     $qty_in_stock                              ,                    .
            //                      ,                1 -         ,                   
            //                                  OutOfStock                   
            //                                         .
            if( $_Product_Info->whichStockControlMethod() == PRODUCT_QUANTITY_IN_STOCK_ATTRIBUTE
                &&
                ($qty_in_stock !== NULL &&
                $qty_in_stock !== "" &&
                $qty_in_stock >= 1))
            {
                if($qty_in_stock < $max_quantity)
                {
                    $max_quantity = $qty_in_stock;
                }
            }
        }

        $min_order = $_Product_Info->getProductTagValue('MinQuantity',PRODUCTINFO_NOT_LOCALIZED_DATA);

        if(empty($min_order)){
            $min_order = 1;
        }

        if($min_order >= $qty_in_stock && (int)$qty_in_stock>0) {
            $min_order = $max_quantity = $qty_in_stock;
        }
        elseif($min_order >= $max_quantity){
            $max_quantity = $min_order + $max_quantity;
        }

        for($i = $min_order; $i <= $max_quantity; $i++)
        {
            $SELECTED = ($i == $selected_quantity ? 'selected="selected"' : "");
            $res .= "<option value='". $i ."' ". $SELECTED .">". $i ."</option>";
        }

        return $res;
    }

    function getUniqueProductsIDsInCart()
    {
        $res = array();

        if(!empty($this->CartContent))
        {
            foreach($this->CartContent as $cart_id => $cart_item)
            {
                $res[] = $cart_item['product_id'];
            };
        };

        $res = array_unique($res);
        return $res;
    }

    function wasCartModified()
    {
        return $this->wasCartModifiedbyIntegrityCheck;
    }

    function setCartProductShippingEntry($cartID, $entry)
    {
        if (!isset($this -> CartContent[$cartID]))
            return;

        $this -> CartContent[$cartID]['shipping_entry'] = $entry;

        modApiFunc('Session', 'set', 'CartContent', $this -> CartContent);
        $this -> buildOrders();
    }

    function processPostedProductData($data, $options_sent, $use_current_cart = true)
    {
        global $application;

        $prod_id = $data['entity_id'];

        $msgres = $application->getInstance("MessageResources", "messages");

        //                                         
        //                  ,                     multiple select
        //     :                             array_flip
        $for_for=array_keys($data['options']);
        for($i=0;$i<count($for_for);$i++)
        {
            if(is_array($data['options'][$for_for[$i]])
                and !empty($data['options'][$for_for[$i]])
                and isset($data['options'][$for_for[$i]][0])
                and is_numeric($data['options'][$for_for[$i]][0]))
            {
                $new_arr=array();
                for($j=0;$j<count($data['options'][$for_for[$i]]);$j++)
                    $new_arr[$data['options'][$for_for[$i]][$j]]='on';
                $data['options'][$for_for[$i]]=$new_arr;
            };
        }

        $is_error = false;
        $discard_by = 'none';
        $stock_discarded_by = 'none';
        $stock_discarded_by_warning = '';
        $options_settings=modApiFunc("Product_Options","getOptionsSettingsForEntity",'product',$prod_id);
        $product_has_options = (count(modApiFunc("Product_Options","getOptionsList",'product',$prod_id,USED_FOR_INV)) > 0);
        $add_to_cart_add_not_replace = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_ADD_TO_CART_ADD_NOT_REPLACE);

        $obj_product = new CProductInfo($prod_id);
        $stock_method = $obj_product->whichStockControlMethod();

        if ($stock_method == PRODUCT_QUANTITY_IN_STOCK_ATTRIBUTE)
        {
            //            Quantity In Stock Attribute.

            $qty_in_stock = $obj_product->getProductTagValue('QuantityInStock',PRODUCTINFO_NOT_LOCALIZED_DATA);
            $qty_already_in_cart = modApiFunc('Cart', 'getProductQuantity', $prod_id);

            //          $sum_qty                               ,                                         
            if($add_to_cart_add_not_replace === true && $use_current_cart)
            {
                $sum_qty = $qty_already_in_cart + $data['qty'];
            }
            else
            {
                $sum_qty = $data['qty'];
            }

            if($qty_in_stock != '' && $qty_in_stock < $sum_qty && $use_current_cart &&
               modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_ALLOW_BUY_MORE_THAN_STOCK) === false )
            {
                $is_error = true;
                $stock_discarded_by = $msgres->getMessage("ERR_NOT_ALLOWED_TO_BUY_MORE_THAN_IN_STOCK");
            }
            elseif($qty_in_stock != '' && $qty_in_stock < $sum_qty && $use_current_cart &&
                   modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_ALLOW_BUY_MORE_THAN_STOCK) === true)
            {
                $stock_discarded_by_warning = $msgres->getMessage("WARNING_NOT_ALLOWED_TO_BUY_MORE_THAN_IN_STOCK");
            }

            //         ,               "min order"               -                                       .
            $min_order = $obj_product->getProductTagValue('MinQuantity',PRODUCTINFO_NOT_LOCALIZED_DATA);
            if($min_order != '' &&
               $min_order > $sum_qty)
            {
                $is_error = true;
                $stock_discarded_by = $msgres->getMessage("ERR_NOT_ALLOWED_TO_BUY_LESS_THAN_MIN_ORDER",array($obj_product->getProductTagValue('MinQuantity')));
            }
        }
$colorswatchvalue = modApiFunc('ColorSwatch','getColorSwatchInfo',$prod_id);
if($colorswatchvalue != '')
{
$datacolor = $data['colorname'];
 if($datacolor  == '')
{
$is_error = true;
$discard_by = 'CLR_SWTCH_ERR_MSG';
}
}

        //                             

        //                                  
        if (!$is_error)
        {
            if($options_sent!="yes")
            {
                if($options_settings['AAWD']=='Y')
                {
                    $data['options']=modApiFunc("Product_Options","getDefaultCombinationForEntity",'product',$prod_id);
                    $check_result=array();
                }
                else
                {
                    $check_result=array('WRN_ONS');
                }
            }
            else
            {
                list($check_result,$data)=modApiFunc("Product_Options","checkCombination",$data);
            }
            #_print($check_result);die;
            //                                                    
            if(!empty($check_result))
            {
                $is_error=true;
                $discard_by = 'WRN_INVALID_OPTIONS';
                foreach($data['options'] as $oinf)
                {
                    if(is_array($oinf) and array_key_exists('is_file',$oinf) and $oinf['val'] != '')
                    {
                        modApiFunc('Shell','removeDirectory',dirname($oinf['val']));
                    }
                }
            }
            else
            {
                $is_error = !modApiFunc("Product_Options","checkByCRules",'product',$prod_id,$data['options']);
                if($is_error)
                    $discard_by = 'WRN_CI_CR';
            }

            //check if the file option can be uploaded (by allowed extensions) See Admin->FILE UPLOAD SETTINGS in admin area
            if (!$is_error)
            {
                $settings = modApiFunc("Settings","getParamListByGroup","FILE_UPLOAD_SETTINGS","SETTINGS_WITH_DESCRIPTION");
                $allowed = false;
                foreach($data['options'] as $oinf)
                {
                    if(is_array($oinf) && array_key_exists('is_file',$oinf) && $oinf['val'] != '')
                    {
                        $file_info = pathinfo($oinf['val']);

                        foreach ($settings as $i=>$s)
                        {
                            if ($s['param_current_value'] == "YES") //upload is enabled
                            {
                                $exts = preg_replace("/, /","|",$s['description']);
                                $exts = preg_replace("/[.]/","\.",$exts);
                                $res = preg_match( "/$exts/", strtolower( $file_info['basename'] ), $m );

                                if ($res)
                                {
                                    // file extension is alowed
                                    $allowed = true;
                                    break;
                                }
                                else
                                {
                                    // not allowed extension
                                    $allowed = false;
                                }
                            }
                        }

                        if (!$allowed)
                        {
                            $is_error = true;
                            $discard_by = 'WRN_CI_EXT';
                        }
                    }
                }
            }

            //                             Inventory
            // AANIC - Allow Add the product to the cart Not in Inventory Control
            if (!$is_error && $options_settings['AANIC']=='N' && $stock_method == PRODUCT_OPTIONS_INVENTORY_TRACKING)
            {
                $inv_id = modApiFunc("Product_Options","getInventoryIDByCombination",'product',$prod_id,$data['options']);
                if($inv_id==null) //           ,                                                   Inventory Control
                {
                    $is_error=true;
                    $discard_by = 'WRN_CI_INV';
                }
            }

            //                              
            // AANIS - Allow Add the product to the cart Not In Stock
            if(!$is_error && $options_settings['AANIS']=='N' && $stock_method == PRODUCT_OPTIONS_INVENTORY_TRACKING)
            {
                $inv_id = modApiFunc("Product_Options","getInventoryIDByCombination",'product',$prod_id,$data['options']);
                if($inv_id != null)
                {
                    //         ,        inventory               
                    $inv_info = modApiFunc('Product_Options','getInventoryInfo',$inv_id);
                    if($inv_info['quantity'] <= 0 && $use_current_cart)
                    {
                        $is_error = true;
                        $discard_by = 'WRN_CI_INV';
                    }
                }
            }

            //          ,                           ,            inventory tracking
            if (!$is_error && $stock_method == PRODUCT_OPTIONS_INVENTORY_TRACKING && $use_current_cart
                && modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_ALLOW_BUY_MORE_THAN_STOCK) === false)
            {
                $inv_id = modApiFunc("Product_Options","getInventoryIDByCombination",'product',$prod_id,$data['options']);
                if($inv_id != null)
                {
                    //          ,                                                               
                    $cart_id = $prod_id."_".modApiFunc("Product_Options","getCombinationHash",$data['options']);
                    $obj_cart = &$application->getInstance('Cart');
                    if (isset($obj_cart->CartContent[$cart_id]))
                    {
                        $qty_already_in_cart = $obj_cart->CartContent[$cart_id]['quantity'];
                    }
                    else
                    {
                        $qty_already_in_cart = 0;
                    }

                    //                             
                    if($add_to_cart_add_not_replace === true)
                    {
                        $sum_qty = $qty_already_in_cart + $data['qty'];
                    }
                    else
                    {
                        $sum_qty = $data['qty'];
                    }

                    if ($options_settings['AANIS']=='Y')
                    {
                    	$inv_info = modApiFunc('Product_Options','getInventoryInfo',$inv_id);
                    }

                    if($options_settings['AANIS']=='N' && $inv_info['quantity']<$sum_qty)
                    {
                        $is_error = true;
                        $stock_discarded_by = $msgres->getMessage("ERR_NOT_ALLOWED_TO_BUY_MORE_THAN_IN_STOCK");
                    }
                }
            }
        }

        return array(
            'data' => $data,
            'is_error' => $is_error,
            'discard_by' => $discard_by,
            'stock_discarded_by' => $stock_discarded_by,
            'stock_discarded_by_warning' => $stock_discarded_by_warning
        );
    }

    function buildProduct($prod_data, $flip_mods_map, $total_quantity)
    {
        $retval = modApiFunc('Catalog', 'getProductInfo', $prod_data['product_id'], true, $prod_data['quantity']);
	//Removing unnecessary info like descs from going into session
	unset($retval['ShortDescription'], $retval['DetailedDescription'], $retval['MetaDescription']);
	unset($retval['attributes']['ShortDescription'], $retval['attributes']['DetailedDescription'], $retval['attributes']['MetaDescription']);

        $retval['Quantity_In_Cart'] = $prod_data['quantity'];
        $retval['Options'] = $prod_data['options'];
		$retval['Colorname'] = $prod_data['colorname'];

        $retval['OptionsModifiers'] = $prod_data['modifiers'];
        $retval['InventoryID'] = $prod_data['inventory_id'];
        $retval['shipping_entry'] = @$prod_data['shipping_entry']; // not actually used

        $retval['CartItemSalePriceExcludingTaxes'] = $retval['attributes']['salepriceexcludingtaxes']['value'];

        if (array_key_exists('SalePrice', $flip_mods_map))
        {
            $options_price_including_included_taxes = $prod_data['modifiers'][$flip_mods_map['SalePrice']];
            $options_price_excluding_included_taxes = modApiFunc('Catalog', 'computePriceExcludingTaxes',
                                                                 $options_price_including_included_taxes,
                                                                 $retval['attributes']['TaxClass']['value']);
            $retval['CartItemSalePriceExcludingTaxes'] += $options_price_excluding_included_taxes;
        }
        if ($retval['CartItemSalePriceExcludingTaxes'] < 0)
            $retval['CartItemSalePriceExcludingTaxes'] = 0;

        $retval['CartItemSalePriceIncludingTaxes'] = $retval['attributes']['salepriceincludingtaxes']['value'];
        if (array_key_exists('SalePrice', $flip_mods_map))
            $retval['CartItemSalePriceIncludingTaxes'] += $prod_data['modifiers'][$flip_mods_map['SalePrice']];
        if ($retval['CartItemSalePriceIncludingTaxes'] < 0)
            $retval['CartItemSalePriceIncludingTaxes'] = 0;

        //          SalePrice,                               "          "       .
        //     TaxClass             -        ,                     .
        //                         SalePrice                              .       
        //                .
        $display_product_price_including_taxes = modApiFunc('Settings', 'getParamValue',
                                                            'TAXES_PARAMS',
                                                            'DISPLAY_PRICES_W_INCLUDED_TAXES');
        if ($display_product_price_including_taxes == DB_TRUE)
            $retval['CartItemSalePrice'] = $retval['CartItemSalePriceIncludingTaxes'];
        else
            $retval['CartItemSalePrice'] = $retval['CartItemSalePriceExcludingTaxes'];

        $retval['CartItemWeight'] = isset($retval['attributes']['Weight']['value'])
                                          ? $retval['attributes']['Weight']['value']
                                          : 0.0;
        if (array_key_exists('Weight', $flip_mods_map))
            $retval['CartItemWeight'] += $prod_data['modifiers'][$flip_mods_map['Weight']];
        if ($retval['CartItemWeight']<0)
            $retval['CartItemWeight']=0;

        $retval['CartItemPerItemShippingCost'] = isset($retval['attributes']['PerItemShippingCost']['value'])
                                                       ? $retval['attributes']['PerItemShippingCost']['value']
                                                       : 0.0;
        if (array_key_exists('PerItemShippingCost',$flip_mods_map))
            $retval['CartItemPerItemShippingCost'] += $prod_data['modifiers'][$flip_mods_map['PerItemShippingCost']];
        if ($retval['CartItemPerItemShippingCost'] < 0)
            $retval['CartItemPerItemShippingCost'] = 0;

        $retval['CartItemPerItemHandlingCost'] = isset($retval['attributes']['PerItemHandlingCost']['value'])
                                                       ? $retval['attributes']['PerItemHandlingCost']['value']
                                                       : 0.0;
        if (array_key_exists('PerItemHandlingCost', $flip_mods_map))
            $retval['CartItemPerItemHandlingCost'] += $prod_data['modifiers'][$flip_mods_map['PerItemHandlingCost']];
        if ($retval['CartItemPerItemHandlingCost'] < 0)
            $retval['CartItemPerItemHandlingCost'] = 0;

        $price = (isset($retval['CartItemSalePriceExcludingTaxes']))
                     ? $retval['CartItemSalePriceExcludingTaxes']
                     : $retval['CartItemSalePrice'];

        // check if we have to use total_quantity
        $qtydsc_discard_options = modApiFunc('Settings', 'getParamValue',
                                             'QUANTITY_DISCOUNT',
                                             'QUANTITY_DISCOUNT_BEHAVIOR');
        $qty = $prod_data['quantity'];
        if ($qtydsc_discard_options == "YES")
            $qty = $total_quantity;

        $membership = modApiFunc('Customer_Account','getCurrentSignedCustomerGroupID');
        $qd = modApiFunc('Quantity_Discounts', 'getQuantityDiscount', $prod_data['product_id'], $qty, $price, $membership);
        if($qd !== 'FIXED_PRICE')
        {
            $qd = (($qd === PRICE_N_A) ? 0.0 : $qd);
            $retval['PerItemDiscount'] = (1.0 * $qd) / (1.0 * $qty);
        }

        $retval['TotalIncludingTaxes'] = 0;

        $from_code = modApiFunc('Localization','getCurrencyCodeById',modApiFunc('Localization','getLocalMainCurrency'));
        $to_code = modApiFunc('Localization','getCurrencyCodeById',modApiFunc('Localization','getLocalDisplayCurrency'));
        if($from_code != $to_code)
        {
            $cisp = number_format(modApiFunc("Currency_Converter", "convert", $retval['CartItemSalePrice'], $from_code, $to_code), 2, '.', '');
            $retval['Total'] = modApiFunc("Currency_Converter", "convert", $cisp * $prod_data['quantity'], $to_code, $from_code);
            $cispet = number_format(modApiFunc("Currency_Converter", "convert", $retval['CartItemSalePriceExcludingTaxes'], $from_code, $to_code), 2, '.', '');
            $retval['TotalExcludingTaxes'] = modApiFunc("Currency_Converter", "convert", $cispet * $prod_data['quantity'], $to_code, $from_code);
        }
        else
        {
            $retval['TotalExcludingTaxes'] = ($retval['CartItemSalePriceExcludingTaxes']) * $prod_data['quantity'];
            $retval['Total'] = ($retval['CartItemSalePrice']) * $prod_data['quantity'];
        }
        if ($retval['TotalExcludingTaxes'] < 0) $retval['TotalExcludingTaxes'] = 0;
        if ($retval['Total'] < 0) $retval['Total'] = 0;

        return $retval;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /*
     * Cart Content (stored in session)
     */
    var $CartContent;

    /*
     * Detailed Cart Content
     */
    var $DetailedCartContent;

    /*
     * Cart orders
     */
    var $CartOrders;

    /*
     * Cart totals
     */
    var $CartTotals;

    // this is obvious. this variable shows if any products in the cart were deleted by failed integrity check.
    var $wasCartModifiedbyIntegrityCheck;

    /**#@-*/

}
?>