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
 * Module "Shipping Cost Calculator"
 *
 * @package ShippingCostCalculator
 * @author Egor V. Derevyankin, Ravil Garafutdinov
 */

class Shipping_Cost_Calculator
{

//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    function Shipping_Cost_Calculator()
    {
        $this->ShippingInfo=array();
        $this->Cart=array();
        $this->PaymentModuleInfo=array();
        $this->fictitious_subtotal=0;
        $this->fictitious_weight=0;
        $this->fictitious_items_count=0;
        $this->per_item_shipping_sum=0;
        $this->per_order_shipping_fee=0;
        $this->per_item_handling_sum=0;
        $this->per_order_handling_fee=0;
        $this->is_debug=false;
        $this->is_full_free_shipping=false;
        $this->is_shipping_not_needed=false;
    }

    function getInfo()
    {
    }

    /**
     * @return structure of the tables of this module
     */
    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        };

        $tables = array ();

        $settings = 'scc_settings';
        $tables[$settings] = array();
        $tables[$settings]['columns'] = array
            (
                'id'                => $settings.'.'.$settings.'_id'
               ,'key'               => $settings.'.'.$settings.'_key'
               ,'value'             => $settings.'.'.$settings.'_value'
            );
        $tables[$settings]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'key'               => DBQUERY_FIELD_TYPE_CHAR50
               ,'value'             => DBQUERY_FIELD_TYPE_CHAR50
            );
        $tables[$settings]['primary'] = array
            (
                'id'
            );

        $fsr = 'scc_fs_rules';
        $tables[$fsr] = array();
        $tables[$fsr]['columns'] = array
            (
                'id'                => $fsr.'.'.$fsr.'_id'
               ,'rule_name'         => $fsr.'.'.$fsr.'_rule_name'
               ,'min_subtotal'      => $fsr.'.'.$fsr.'_min_subtotal'
               ,'cats'              => $fsr.'.'.$fsr.'_cats'
               ,'prods'             => $fsr.'.'.$fsr.'_prods'
               ,'dirty_cart'        => $fsr.'.'.$fsr.'_dirty_cart'
            );
        $tables[$fsr]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'rule_name'         => DBQUERY_FIELD_TYPE_CHAR255
               ,'min_subtotal'      => DBQUERY_FIELD_TYPE_FLOAT
               ,'cats'              => DBQUERY_FIELD_TYPE_LONGTEXT
               ,'prods'             => DBQUERY_FIELD_TYPE_LONGTEXT
               ,'dirty_cart'        => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 1'
            );
        $tables[$fsr]['primary'] = array
            (
                'id'
            );

        global $application;
        return $application->addTablePrefix($tables);

    }

    /**
     * Module installator.
     */
    function install()
    {
        _use(dirname(__FILE__)."/includes/install.inc");
    }

    /**
     * Uninstalls the module.
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(Shipping_Cost_Calculator::getTables());
        global $application;
        $application->db->getDB_Result($query);
    }

    /**
     * Gets settings.
     *
     * @return array of the settings
     */
    function getSettings()
    {
        global $application;


        if(!isset($this->settings))
        {
            $tables = $this->getTables();
            $columns=$tables['scc_settings']['columns'];

            $query = new DB_Select();
            $query->addSelectTable('scc_settings');
            $query->addSelectField($columns['key']);
            $query->addSelectField($columns['value']);
            $result = $application->db->getDB_Result($query);
            $settings=array();
            foreach($result as $k => $v)
                $settings[$v['scc_settings_key']]=$v['scc_settings_value'];

	        $this->settings = $settings;
        }
        return $this->settings;
    }

    /**
     * Sets settings.
     *
     * @param $settings array of the setings
     */
    function setSettings($settings)
    {
        global $application;
        $tables = $this->getTables();
        $columns=$tables['scc_settings']['columns'];

        foreach($settings as $k => $v)
        {
            $query = new DB_Update('scc_settings');
            $query->addUpdateValue($columns['value'],$v);
            $query->WhereValue($columns['key'], DB_EQ, $k);
            $application->db->getDB_Result($query);
        };

    }

    function setShippingInfo($s_info)
    {
        $this->ShippingInfo=$s_info;
    }


    function setCart($s_cart)
    {
        if(md5(serialize($s_cart)) !== md5(serialize($this->Cart)))
        {
            $this->Cart=$s_cart;
            $this->processCart();
        }
    }

    function setPaymentModuleInfo($pm_info)
    {
        $this->PaymentModuleInfo = $pm_info;
    }

    function setDebugFlag($d_flag)
    {
        $this->is_debug=$d_flag;
    }

    function calculateShippingCost()
    {
        $this->preparePromoCodeAndFSR();
        $testTC = $this->calculateTotalShippingCost(1.0);

        if($this->is_shipping_not_needed)
        {
            return array("Shipping_Not_Needed" =>array(
                    "methods" => array(
                        "0" => array(
                            "id" => modApiFunc("Checkout","getNotNeedShippingMethodID"),
                            "shipping_cost" => $this->calculateTotalShippingCost(0.0)
                        )
                    )
                ));
        }

        $sm_list = modApiFunc("Checkout", "getInstalledAndActiveModulesListData", "shipping");

        #
        # exlude all_inactive_module
        #

        if(count($sm_list)==1)
        {
            $sm = array_pop(array_values($sm_list));
            if($sm->name == modApiFunc("Checkout", "getAllInactiveModuleClassAPIName", "shipping"))
                return array(modApiFunc("Checkout", "getAllInactiveModuleClassAPIName", "shipping") =>array(
                        "methods" => array(
                            "0" => array(
                                "id" => modApiFunc(modApiFunc("Checkout", "getAllInactiveModuleClassAPIName", "shipping"), "getSingleAvailableMethodId"),
                                "shipping_cost" => $this->calculateTotalShippingCost(0.0)
                            )
                        )
                    ));
        }
        else if (is_array($sm_list) && count($sm_list) != 0) # remove all_inactive_module from the list
        {
            foreach ($sm_list as $i=>$sm)
            {
            	if ($sm->name == modApiFunc("Checkout", "getAllInactiveModuleClassAPIName", "shipping"))
        	    {
        	    	unset($sm_list[$i]);
        	    }
            }
        }

        $SelectedModules = modApiFunc("Checkout", "getSelectedModules", "shipping");
        $items = array();
        $new_selected_module_sort_order = 0;
        foreach ($sm_list as $sm_item)
        {
            $name = _ml_strtolower($sm_item->name);
            $smInfo = modApiFunc($name, "getInfo");

            //If sort id is not defined then assign the highest possible sort id to this item: 0, -1, -2 ...
            $sort_id = empty($SelectedModules[$smInfo["GlobalUniqueShippingModuleID"]]["sort_order"]) ? $new_selected_module_sort_order++ : $SelectedModules[$smInfo["GlobalUniqueShippingModuleID"]]["sort_order"];
            $items[$sort_id] = $sm_item;
        }

        //Sort items by sort_id.
        ksort($items, SORT_NUMERIC);
        $sm_list = $items;

        $results = array();

    // if we need to add a new free shipping method to the list
        if (($testTC['FreeShippingApplied'] === true && $this->settings['FS_MODE'] == FS_MODE_ADD)
            || $this->is_full_free_shipping === true)
        {
            $fsm["Shipping_Module_Free_Shipping"]['methods'] = modApiFunc('Shipping_Module_Free_Shipping', "getShippingMethods");
            $fsm["Shipping_Module_Free_Shipping"]['methods'][0]['shipping_cost'] = $this->calculateTotalShippingCost(0.0);
            $fsm["Shipping_Module_Free_Shipping"]['methods'][0]['method_name']   = $this->settings['FS_METHOD_LABEL_VALUE'];

            $fsm["Shipping_Module_Free_Shipping"]['methods'][0]['shipping_cost']['PerItemShippingCostSum'] = 0;
            $fsm["Shipping_Module_Free_Shipping"]['methods'][0]['shipping_cost']['PerOrderShippingFee'] = 0;
            $fsm["Shipping_Module_Free_Shipping"]['methods'][0]['shipping_cost']['TotalShippingCharge'] = 0;

            $fsm["Shipping_Module_Free_Shipping"]['methods'][0]['shipping_cost']['TotalShippingAndHandlingCost'] = $fsm["Shipping_Module_Free_Shipping"]['methods'][0]['shipping_cost']['TotalHandlingCharge'];
        }

        foreach($sm_list as $sm)
        {
            modApiFunc($sm->name,"setDebugFlag",$this->is_debug);
            $results[$sm->name]["methods"]=modApiFunc($sm->name,"getShippingMethods","AVAILABLE",true);

            if($this->is_debug)
                $results[$sm->name]["debug_info"]=modApiFunc($sm->name,"getDebugInfo");
            if(count($results[$sm->name]["methods"]))
            {
                foreach($results[$sm->name]["methods"] as $mk => $minf)
                    $results[$sm->name]["methods"][$mk]["shipping_cost"]=$this->calculateTotalShippingCost($minf["cost"]);
            }
            else
            {
                if(!$this->is_debug)
                    unset($results[$sm->name]);
            }
        };

        if (($testTC['FreeShippingApplied'] === true && $this->settings['FS_MODE'] == FS_MODE_ADD)
            || $this->is_full_free_shipping === true)
        {
            if ($this->settings['FS_PLACING'] == FS_PLACING_TOP)
            {
                $results = array_merge($fsm, $results);
            }
            else
            {
                $results = array_merge($results, $fsm);
            }
        }

        return $results;

    }

    function getCalculatedMethod($API_name,$method_id)
    {
        if($API_name == 'Shipping_Not_Needed')
        {
            $method = array(
                'shipping_cost' => $this->calculateTotalShippingCost(0.0)
            );
        }
        else
        {
            $method=modApiFunc($API_name,"getShippingMethodInfo",$method_id,true);
            $method["shipping_cost"]=$this->calculateTotalShippingCost($method["cost"]);
        };
        return $method;
    }

    function getShippingInfo()
    {
        return $this->ShippingInfo;
    }

    function getPackWeight()
    {
        return $this->fictitious_weight;
    }

    function getCartSubtotal()
    {
        return $this->fictitious_subtotal;
    }

    function getItemsCount()
    {
        return $this->fictitious_items_count;
    }

    /**
     * The parameter $customer_cart is an array, which returns the method
     * Cart::getCartContent.
     */
    function formatCart($customer_cart)
    {
        $formatted_cart=array("products"=>array(),'subtotal'=>0);

        /*
         * It needs to know, that any product attribute can be hidden
         * in the product type. That's why each product attribute should
         * be checked, before using its value.
         * I.e. if the attribute is hidden, then attribute value can't be used.
         * Moreover, if the attribute is public, then the attribute value
         * still should be checked, if it is null, because the attribute value
         * can be null.
         */

        for($i=0;$i<count($customer_cart);$i++)
        {
            $product=array();
            $product["qty"]=$customer_cart[$i]["Quantity_In_Cart"];

            $_cycle = array("weight"=>"Weight",
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

            $product["options_modifiers"]=$customer_cart[$i]["OptionsModifiers"];

            $formatted_cart["products"][]=$product;
            $formatted_cart["subtotal"] += $product["cost"] * $product["qty"];
        }

        return $formatted_cart;
    }

    function isShippingNotNeeded()
    {
        return $this->is_shipping_not_needed;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    function processCart()
    {
        if(count($this->Cart["products"])>0)
        {
            $fs_flag=true;
            $nns_flag=true;
        }
        else
        {
            $fs_flag=false;
            $nns_flag=false;
        };
        $this->fictitious_subtotal=0;
        $this->fictitious_weight=0;
        $this->fictitious_items_count=0;
        $this->per_item_shipping_sum=0;
        $this->per_order_shipping_fee=0;
        $this->per_item_handling_sum=0;
        $this->per_order_handling_fee=0;

        for($i=0;$i<count($this->Cart["products"]);$i++)
        {
            $product=$this->Cart["products"][$i];

            if(!isset($product["need_ship"]) or ($product["need_ship"]!="NO" and $product["need_ship"]!="2"))
                $nns_flag=false;
            else
                continue;

           if(!isset($product["free_ship"])  or $product["free_ship"]==""
               or $product["free_ship"]=="NO" or $product["free_ship"]=="2")
            {
                $fs_flag=false;
            }
                $this->per_item_shipping_sum+=($product["ship_charge"]*$product["qty"]);
                $this->fictitious_weight+=($product["weight"]*$product["qty"]);
                $this->fictitious_subtotal+=($product["cost"]*$product["qty"]);
                $this->fictitious_items_count+=$product["qty"];

                //Options start
                if(isset($product["options_modifiers"]["price"]))
                    $this->fictitious_subtotal+=($product["options_modifiers"]["price"]*$product["qty"]);
                if(isset($product["options_modifiers"]["weight"]))
                    $this->fictitious_weight+=($product["options_modifiers"]["weight"]*$product["qty"]);
                if(isset($product["options_modifiers"]["shipping_cost"]))
                    $this->per_item_shipping_sum+=($product["options_modifiers"]["shipping_cost"]*$product["qty"]);
                //Options end

            $this->per_item_handling_sum+=($product["hand_charge"]*$product["qty"]);
            //Options start
            if(isset($product["options_modifiers"]["handling_cost"]))
                $this->per_item_handling_sum+=($product["options_modifiers"]["handling_cost"]*$product["qty"]);
            //Options end
        }
        $this->is_full_free_shipping=$fs_flag;
        $this->is_shipping_not_needed=$nns_flag;

        $settings=$this->getSettings();

        if($settings["PO_SC_TYPE"]=="A")
            $this->per_order_shipping_fee=$settings["PO_SC"];
        elseif($settings["PO_SC_TYPE"]=="P")
            $this->per_order_shipping_fee=round(($settings["PO_SC"]/100*$this->fictitious_subtotal),2);

        $this->per_order_handling_fee=$settings["PO_HC"];

        if($this->fictitious_subtotal<0)
            $this->fictitious_subtotal=0;
        if($this->fictitious_weight<0)
            $this->fictitious_weight=0;
        if($this->per_item_shipping_sum<0)
            $this->per_item_shipping_sum=0;
        if($this->per_item_handling_sum<0)
            $this->per_item_handling_sum=0;

    }

    /**
     * Converts the value of the monetary sum to be used _out_ ASC.
     * If the price equals PRICE_N_A, then it is changed to 0.0.
     */
    function export_PRICE_N_A($price)
    {
        return ($price == PRICE_N_A) ? 0.0 : $price;
    }

    function calculateTotalShippingCost($delivery_cost)
    {
        if ($this->pc_info == NULL)
            $this->preparePromoCodeAndFSR();

        $pc_info = $this->pc_info;
        $fs_rule_decision = $this->fs_rule_decision;

        //                          general shipping settings
        $settings = $this->settings;
        $return = array(
            // settings
            "FreeHandlingForOrdersOver" => is_numeric($settings["FH_OO"]) ? $settings["FH_OO"] : PRICE_N_A,
            "FreeShippingForOrdersOver" => is_numeric($settings["FS_OO"]) ? $settings["FS_OO"] : PRICE_N_A,
            "MinimumShippingCost"       => $settings["MIN_SC"],

            // shipping
            'PerItemShippingCostSum'=> $this->per_item_shipping_sum,
            'PerOrderShippingFee'   => $this->per_order_shipping_fee,
            "ShippingMethodCost"    => $delivery_cost,
            "TotalShippingCharge"   => ($this->per_item_shipping_sum+$this->per_order_shipping_fee+$delivery_cost),
            "FreeShippingApplied"   => false,

            // handling
            'PerItemHandlingCostSum'=> $this->per_item_handling_sum,
            'PerOrderHandlingFee'   => $this->per_order_handling_fee,
            "TotalHandlingCharge"   => ($this->per_item_handling_sum+$this->per_order_handling_fee),

            //Payment
            'PerOrderPaymentModuleShippingFee' => empty($this->PaymentModuleInfo) ? PRICE_N_A : $this->PaymentModuleInfo['PerOrderPaymentModuleShippingFee'],

            // overall
            'TotalShippingAndHandlingCost' => 0 // see below
        );

        // adjust minimal shipping cost
        if ($return["TotalShippingCharge"] < $settings["MIN_SC"])
            $return["TotalShippingCharge"] = $settings["MIN_SC"];

        $isFreeShippingApplied = false;
        // if coupon does not forbid free shipping
        if ($pc_info['free_shipping'] != PROMO_CODE_FORBIDS_FREE_SHIPPING)
        {
            // check if coupon grants free shipping
            if ($pc_info['free_shipping'] == PROMO_CODE_GRANTS_FREE_SHIPPING)
            {
                $return["FreeShippingApplied"] = true;
                $isFreeShippingApplied = true;
            }

            // check if general shippong settings grant free shipping
            if(is_numeric($settings["FS_OO"]) && ($this->fictitious_subtotal >= $settings["FS_OO"]))
            {
                $return["FreeShippingApplied"] = true;
                $isFreeShippingApplied = true;
            }

            // check if products do not need shipping internally
            if($this->is_full_free_shipping)
            {
                $return["FreeShippingApplied"] = true;
                $isFreeShippingApplied = true;
            }

            // check free shipping rules
            if ($fs_rule_decision === true)
            {
                $return["FreeShippingApplied"] = true;
                $isFreeShippingApplied = true;
            }
        }

        if ($isFreeShippingApplied === true)
        {
            // check if we need to zero all shipping costs due to free shipping
            if ($this->settings['FS_MODE'] == FS_MODE_ZERO)
            {
                $return["TotalShippingCharge"] = 0;
            }
        }

        // if coupon does not forbid free handling
        if ($pc_info['free_handling'] != PROMO_CODE_FORBIDS_FREE_HANDLING)
        {
            // coupon grants free handling
            if ($pc_info['free_handling'] == PROMO_CODE_GRANTS_FREE_HANDLING)
                $return["TotalHandlingCharge"] = 0;

            // if general shippong settings grant free handling
            if(is_numeric($settings["FH_OO"]) && ($this->fictitious_subtotal >= $settings["FH_OO"]))
                $return["TotalHandlingCharge"] = 0;
        }

        if ($this->fictitious_subtotal > $return['FreeShippingForOrdersOver'] && $return['FreeShippingForOrdersOver'] != PRICE_N_A && $this->settings['FS_MODE'] == FS_MODE_ZERO)
        {
            $return['TotalShippingCharge'] = 0;
        }
        if ($this->fictitious_subtotal > $return['FreeHandlingForOrdersOver'] && $return['FreeHandlingForOrdersOver'] != PRICE_N_A)
        {
            $return['TotalHandlingCharge'] = 0;
        }

        //                       FS_OO                                  PerOrderPaymentModuleShippingFee.                            ,
        //                                                                    -                  .
        $return['TotalShippingAndHandlingCost'] = $return['TotalShippingCharge'] +
                                                  $return['TotalHandlingCharge'] +
                                                  $this->export_PRICE_N_A($return['PerOrderPaymentModuleShippingFee']);

        if($this->is_shipping_not_needed)
            $return = array_map(array(&$this,"__set_to_zero"),$return);

        return $return;
    }

    function preparePromoCodeAndFSR()
    {
        global $application;
        $promo_codes = $application->getInstance('PromoCodes');
        // promocode/coupon, applied to the order
        $pc_info = false;
        $promo_code_id = $promo_codes->getPromoCodeId();
        $is_promocode_set = $promo_codes->isPromoCodeIdSet();

        // not valid id || not set || info not available -> coupon, good bye
        if ($promo_code_id && $is_promocode_set)
        {
            $pc_info = $promo_codes->getPromoCodeInfo($promo_code_id);
        }

        // get product IDs and their categories' IDs
        $coupon_cart = array();
        $order_cart = modApiFunc("Cart","getCartContentExt");
        foreach ($order_cart as $product)
        {
            $coupon_cart[] = array(
              'id' => $product["ID"],
              'cat' => $product['CategoryID'],
              'total' => $product['TotalExcludingTaxes']
            );
        }

        // check if coupon is applicable
        if ($pc_info)
        {
            // coupon is not applicable due to not meeting coupon conditions
            if(false == $promo_codes->isPromoCodeApplicable($this->fictitious_subtotal, $promo_code_id, $coupon_cart))
                $pc_info = false;
        }

        //                                                                                                         ID
        //
        if (!$pc_info)
        {
            $pc_info = array(
                     "free_shipping" => PROMO_CODE_NO_ATTENTION_TO_FREE_SHIPPING
                    ,"free_handling" => PROMO_CODE_NO_ATTENTION_TO_FREE_HANDLING
                );
        }

        $applied_rule = '';
        $fs_rule_decision = $this->checkIfFsRulesApply($this->fictitious_subtotal, $coupon_cart, $applied_rule);

        $this->settings         = $this->getSettings();
        $this->pc_info          = $pc_info;
        $this->fs_rule_decision = $fs_rule_decision;

        return;
    }

    function __set_to_zero($a)
    {
        return 0;
    }

    function addFsRuleInfo($params)
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['scc_fs_rules']['columns'];

        $query = new DB_Insert('scc_fs_rules');

        $query->addInsertValue($params['FsRuleName'], $tr['rule_name']);
        $query->addInsertValue($params['FsRuleMinSubtotal'], $tr['min_subtotal']);
        $query->addInsertValue($params['FsRuleStrictCart'], $tr['dirty_cart']);
        $query->addInsertValue('1', $tr['cats']);

        $result = $application->db->getDB_Result($query);
        return $application->db->DB_Insert_Id();
    }

    function updateFsRuleInfo($params)
    {
        if (!isset($params['FsRule_id']))
            return false;

        global $application;
        $tables = $this->getTables();
        $tr = $tables['scc_fs_rules']['columns'];

        $query = new DB_Update('scc_fs_rules');

        $query->addUpdateValue($tr['rule_name'], $params['FsRuleName']);
        $query->addUpdateValue($tr['min_subtotal'], $params['FsRuleMinSubtotal']);
        $query->addUpdateValue($tr['dirty_cart'], $params['FsRuleStrictCart']);

        $query->WhereValue($tr['id'], DB_EQ, $params['FsRule_id']);

        $result = $application->db->getDB_Result($query);
        return $application->db->DB_Insert_Id();
    }

    function updateFsRuleArea($data)
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['scc_fs_rules']['columns'];

        $query = new DB_Update('scc_fs_rules');

        $query->addUpdateValue($tr['prods'],   $data["prods"]);
        $query->addUpdateValue($tr['cats'], $data["cats"]);

        $query->WhereValue($tr['id'], DB_EQ, $data['fsr_id']);
        $application->db->getDB_Result($query);
    }

    function deleteFsRuleByIdsArray($id_array)
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['scc_fs_rules']['columns'];

        $query = new DB_Delete('scc_fs_rules');
        $query->WhereField( $tr['id'], DB_IN, "('".implode("', '", $id_array)."') ");
        $application->db->getDB_Result($query);
    }

    function checkIfFsRuleIsUnique($rule_name, $fsr_id)
    {
        $rlt = execQuery('SELECT_SCC_FS_RULE_BY_NAME', array('name' => $rule_name));

        // zero such names
        if (count($rlt) === 0)
            return true;

        // too a lot of such names
        if (count($rlt) > 1)
            return false;

        // such name is present, but it is not our id,
        // so it is not a valid update
        if ($rlt[0]['id'] != $fsr_id)
            return false;

        return true;
    }

    function getFsRuleInfo($fsr_id)
    {
        $rlt = execQuery('SELECT_SCC_FS_RULE_BY_ID', array('id' => $fsr_id));

        if (empty($rlt))
            return false;

        return $rlt[0];
    }

    function getCatsProductsAffected($id)
    {
        $params = array('id' => $id);
        $res = execQuery('SCC_FS_RULE_SELECT_PRODUCTS_AFFECTED', $params);
        if (!$res)
        {
            return array('prods' => array(), 'cats' => array());
        }
        $res = $res[0];

        if ($res['prods'] && !empty($res['prods']))
            $res['prods'] = explode('|', $res['prods']);
        else
            $res['prods'] = array();

        if ($res['cats'] && !empty($res['cats']))
            $res['cats']  = explode('|', $res['cats']);
        else
            $res['cats'] = array();

        return $res;
    }

    function isFsRuleEffectiveAreaNotEmpty($id)
    {
        $area = $this->getCatsProductsAffected($id);
        if (empty($area['prods']) && empty($area['cats']))
            return false;

        return true;
    }

    /**
     * function checks if any of the existing free shipping rules
     * apply to the current cart
     *
     * @param int $subtotal
     * @param obj[] $order_cart
     * @return bool
     */
    function checkIfFsRulesApply($subtotal, $order_cart, &$finally_applied_rule)
    {
        $fs_rules = execQuery('SELECT_SCC_FS_RULES', array());
        if (count($fs_rules) < 1)
            return false;

      // check all rules
      // break if at least one applies to the current cart
        foreach ($fs_rules as $rule)
        {
            if ($rule['min_subtotal'] > $subtotal)
            {
                // min subtotal is not fulfilled, rule not applicable
                continue;
            }

          // register affected area for the current rule
            $cats = explode('|', $rule['cats']);
            if (empty($rule['cats']))
                $cats = array();

            $prods = explode('|', $rule['prods']);
            if (empty($rule['prods']))
                $prods = array();

            $this->clearAffected();
            foreach ($prods as $pid)
            {
                $this->registerAffectedProduct($pid);
            }
            foreach ($cats as $cid)
            {
                $this->registerAffectedCategory($cid);
            }

          // check all products one by one, trying to find linebreaker
            $rule_applicable = false;
            foreach ($order_cart as $product)
            {
                if ($this->isAffectedProduct($product['id'], $product['cat']))
                {
                    if ($rule['dirty_cart'] === SCC_DIRTY_CART)
                    {
                        // at least one affected product, enough for meeee
                        $rule_applicable = 'yes';
                        break;
                    }
                }
                else // not affected
                {
                    if ($rule['dirty_cart'] === SCC_STRICT_CART)
                    {
                        // at least one outside product, tooo baaad for youuu
                        $rule_applicable = 'no';
                        break;
                    }
                }
            } // foreach ($order_cart as $product)

          // check intact flag
            if ($rule_applicable === false)
            {
                if ($rule['dirty_cart'] === SCC_DIRTY_CART)
                {
                    // no affected products found, uaaaaaa! [crying]
                    $rule_applicable = 'no';
                }
                else // if ($rule['dirty_cart'] === SCC_STRICT_CART)
                {
                    // no dirty, filthy outsiders, nice-nice
                    $rule_applicable = 'yes';
                }
            } // if ($rule_applicable === false)

            if ($rule_applicable === 'yes')
            {
                $finally_applied_rule = $rule;
                return true;
            }
        } // foreach ($fs_rules as $rule)

      // cannot find an applicable rule, you are pitiful
        $finally_applied_rule = false;
        return false;
    }

    /**
     * returns categories' parent paths
     *
     */
    function dictGetCategoryFullPath($cid)
    {
        if (isset($this->cat_array[$cid]))
            return $this->cat_array[$cid];

        $path = modApiFunc('Catalog', 'getCategoryFullPath', $cid);

        $current_path = array();
        foreach ($path as $cat)
        {
            $current_path[] = $cat;
            if (!isset($this->cat_array[$cat['id']]))
               $this->cat_array[$cat['id']] = $current_path;
        }

        if (isset($this->cat_array[$cid]))
            return $this->cat_array[$cid];

        return array();
    }

    function registerAffectedCategory($cid)
    {
        $this->affected_cats[$cid] = true;
    }

    function registerAffectedProduct($pid)
    {
        $this->affected_prods[$pid] = true;
    }

    function clearAffected()
    {
        $this->affected_cats = array();
        $this->affected_prods = array();
    }

    /**
     * gets category id to be checked
     * returns whether the category belongs to the coupons effective area
     *
     * @param int $cid
     * @return bool
     */
    function isAffectedCategory($cid)
    {
        $path = $this->dictGetCategoryFullPath($cid);
        foreach ($path as $node)
        {
            if (isset($this->affected_cats[$node['id']]))
                return true;
        }
        return false;
    }

    /**
     * gets a product id to be checked
     * returns whether the product belongs to the coupons effective area products
     * do not checks products' category or parent categories
     *
     * @param int $pid
     * @param int $cid
     * @return bool
     */
    function isAffectedProduct($pid, $cid)
    {
        if (isset($this->affected_prods[$pid]))
            return true;

        return $this->isAffectedCategory($cid);
    }

    function getCustomerChoice()
    {
        if (modApiFunc('Session', 'is_Set', 'ShippingCalculatorChoice'))
        {
            $value = modApiFunc('Session', 'get', 'ShippingCalculatorChoice');
            $value = explode('_', $value);

            $rlt = array('module' => '', 'method' => '');
            if (isset($value[0]))
                $rlt['module'] = $value[0];
            if (isset($value[1]))
                $rlt['method'] = $value[1];

            return $rlt;
        }
        return false;
    }

    function setCustomerChoice($choice)
    {
        modApiFunc('Session', 'set', 'ShippingCalculatorChoice', $choice);
    }

    function clearCustomerChoice()
    {
        modApiFunc('Session', 'un_Set', 'ShippingCalculatorChoice');
    }

    var $affected_cats;
    var $affected_prods;

    var $settings;
    var $pc_info;
    var $fs_rule_decision;

    var $ShippingInfo;
    var $Cart;
    var $fictitious_weight;
    var $fictitious_subtotal;
    var $fictitious_items_count;
    var $per_item_shipping_sum;
    var $per_order_shipping_fee;
    var $per_item_handling_sum;
    var $per_order_handling_fee;
    var $is_debug;
    var $is_full_free_shipping;
    var $is_shipping_not_needed;
};

?>