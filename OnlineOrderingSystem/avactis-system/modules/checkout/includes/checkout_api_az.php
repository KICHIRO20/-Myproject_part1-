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
class Checkout_AZ extends CheckoutBase
{
    function getPaymentModulesGroupsInfo()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');

        return array("Offline"            => array("group_id"   => "Offline",
                                                   "short_name" => $obj->getMessage('CHECKOUT_PAYMENT_GROUP_OFFLINE_SHORT_NAME'))
                    ,"OnlineCC"           => array("group_id"   => "OnlineCC",
                                                   "short_name" => $obj->getMessage('CHECKOUT_PAYMENT_GROUP_ONLINE_CC_SHORT_NAME'))
                    ,"OnlineECheck"       => array("group_id"   => "OnlineECheck",
                                                   "short_name" => $obj->getMessage('CHECKOUT_PAYMENT_GROUP_ONLINE_ECHECK_SHORT_NAME'))
//                    ,"OnlinePaymentSystem"=> array("group_id"   => "OnlinePaymentSystem",
//                                                   "short_name" => $obj->getMessage('CHECKOUT_PAYMENT_GROUP_ONLINE_PAYMENT_SYSTEM_SHORT_NAME'))
                    );
    }

    function getShippingModulesGroupsInfo()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        return array("Offline"            => array("group_id"   => "Offline",
                                                   "short_name" => $obj->getMessage('CHECKOUT_SHIPPING_GROUP_OFFLINE_SHORT_NAME'))
                     );
    }

    function getPaymentModuleGroup($payment_module_data)
    {
        //// INCORRECT method to get group name!
        $groups_array = explode(',', $payment_module_data->groups);
        $payment_group = array_key_exists(1, $groups_array) ? $groups_array[1] : "";
        return $payment_group;
    }

    function getShippingModuleGroup($shipping_module_data)
    {
        //// INCORRECT method to get group name!
        $groups_array = explode(',', $shipping_module_data->groups);
        $shipping_group = array_key_exists(1, $groups_array) ? $groups_array[1] : "";
        return $shipping_group;
    }

    ##########
    ##########

    function insertPersonInfoVariant($person_info_type_id
                                    ,$person_info_variant_tag
                                    ,$person_info_variant_name
                                    ,$person_info_variant_description
                                    ,$person_info_variant_visible_name
                                    ,$variant_to_attributes)
    {
        global $application;
        /*
         $variant_to_attributes - the array of the array type
         array
         (
            array
            (
                "person_attribute_id"
                "person_attribute_visible_name"
                "person_attribute_description"
                "person_attribute_visible"
                "person_attribute_required"
                "person_attribute_sort_order"
            )
            array
            (
                "person_attribute_id"
                "person_attribute_visible_name"
                "person_attribute_description"
                "person_attribute_visible"
                "person_attribute_required"
                "person_attribute_sort_order"
            )
         )
        */
        //@ Add a validation of all input data.

        $params = array('person_info_type_id'=>$person_info_type_id,
                        'person_info_variant_name'=>$person_info_variant_name,
                        'person_info_variant_description'=>$person_info_variant_description,
                        'person_info_variant_tag'=>$person_info_variant_tag,
                        'person_info_variant_visible_name'=>$person_info_variant_visible_name);
        execQuery('INSERT_PERSON_INFO_VARIANTS',$params);
        $inserted_id = $application->db->DB_Insert_Id();

        $params = array('inserted_id'=>$inserted_id);
        foreach($variant_to_attributes as $attribute)
        {
            $params['attribute'] = $attribute;
            execQuery('INSERT_PERSON_INFO_VARIANTS_ATTRIBUTES', $params);
        }

        return $inserted_id;
    }

    ##########
    ##########


    ##########
    ##########

    /**
     * Returns currently selected Order ID.
     */
    function getCurrentOrderID()
    {
        return $this->currentOrderID;
    }

    function getCurrentOrderCurrencyID()
    {
        return $this->currentOrderCurrencyID;
    }

    /**
     * Sets currently selected Customer ID.
     */
    function setCurrentCustomerID($customer_id)
    {
        $this->currentCustomerID = $customer_id;
    }

    /**
     * Returns currently selected Customer ID.
     */
    function getCurrentCustomerID()
    {
        return $this->currentCustomerID;
    }

    ##########
    ##########
    /**
     * Returns an order list.
     */
    function getOrderList()
    {
        global $application;

        $tables = $this->getTables();

        $o = $tables['orders']['columns'];

        $query = new DB_Select();
        $query->addSelectField($o['id'], 'id');
        # use a filter
        $filter = $this->order_search_filter;

        if ($filter['search_by'] == 'id' && $filter['order_id'] != null)
        {
            $query->Where($o['id'], DB_EQ, $filter['order_id']);
        }
        elseif ($filter['search_by'] == 'date')
        {
            $from_date = $filter['from_year'] . "-" . $filter['from_month'] . "-" . $filter['from_day'] . ' 00:00:00';
            $to_date = $filter['to_year'] . "-" . $filter['to_month'] . "-" . $filter['to_day'] . ' 23:59:59';
            $query->Where('DATE_ADD(' . $o['date'] . ', ' . modApiFunc('Localization', 'getSQLInterval') . ')', DB_GTE, "'".$from_date."'");
            $query->WhereAND();
            $query->Where('DATE_ADD(' . $o['date'] . ', ' . modApiFunc('Localization', 'getSQLInterval') . ')', DB_LTE, "'".$to_date."'");
            if (!empty($filter['status_id']))
            {
                $query->WhereAND();
                $query->Where($o['status_id'], DB_EQ, $filter['status_id']);
            }
            if (!empty($filter['payment_status_id']))
            {
                $query->WhereAND();
                $query->Where($o['payment_status_id'], DB_EQ, $filter['payment_status_id']);
            }
            if (isset($filter['order_statuses']) && is_array($filter['order_statuses']))
            {
        	    $query->WhereAND();
        	    foreach ($filter['order_statuses'] as $id => $v)
        	    {
        	    	$ord_stat[] = $id;
        	    }
            	$query->Where($o['status_id'], DB_IN, "(".implode(", ",$ord_stat).")");
            }
            if (isset($filter['payment_statuses']) && is_array($filter['payment_statuses']))
            {
                $query->WhereAND();
                foreach ($filter['payment_statuses'] as $id => $v)
        	    {
        	    	$pay_stat[] = $id;
        	    }
            	$query->Where($o['payment_status_id'], DB_IN, "(".implode(", ",$pay_stat).")");
            }
            if (isset($filter['affiliate_id']) && !empty($filter['affiliate_id']))
            {
                $query->WhereAND();
                $query->WhereValue($o['affiliate_id'], DB_LIKE, "%".$filter['affiliate_id']."%");
            }
        }
        elseif ($filter['search_by'] == 'status' && $filter['filter_status_id'] != 0)
        {
            $query->Where($o['status_id'], DB_EQ, $filter['filter_status_id']);
        }

        $query->SelectOrder($o['date'], 'DESC');
        $query->SelectOrder($o['id'], 'DESC');
        $query = modApiFunc('paginator', 'setQuery', $query);

        $result = $application->db->getDB_Result($query);

        $orders = array();
        for ($i=0; $i<sizeof($result); $i++)
        {
//            $order = $this->getOrderInfo($result[$i]['id']);
//            array_push($orders, $order);
            array_push($orders, $result[$i]['id']);
        }
        return $orders;
    }

    /**
     * Calculates order count with the specified status.
     */
    function getOrderCount($status_id)
    {
        if ($status_id === null)
        {
            return 0;
        }

        $result = execQuery('SELECT_ORDER_COUNT_BY_STATUS_ID', array('status_id'=>$status_id));
        return $result[0]['count'];
    }

    /**
     * Returns filter array for orders.
     */
    function getOrderSearchFilter()
    {
        //this code was added due to new layout of order search page at the admin backoffice
        //in the new version status_id parameter should be added to order_statuses array
        //so that appropriate checkbox should be toggled.
        //11.08.2008 (melkor)
        /*if (isset($this->order_search_filter['filter_status_id']) && !isset($this->order_search_filter['order_statuses']) && !isset($this->order_search_filter['payment_statuses']))
        {
        	$this->order_search_filter['order_statuses'] = array($this->order_search_filter['filter_status_id'] => "on");
        }*/
    	return $this->order_search_filter;
    }

    /**
     * Saves filter array for orders.
     */
    function setOrderSearchFilter($array)
    {
        $this->order_search_filter = $array;
    }

    /**
     * Returns filter array for customers.
     */
    function getCustomerSearchFilter()
    {
        return $this->customer_search_filter;
    }

    /**
     * Saves filter array for customers.
     */
    function setCustomerSearchFilter($array)
    {
        $this->customer_search_filter = $array;
    }

    function getCurrentPaymentModuleSettingsViewName()
    {
        return $this->CurrentPaymentModuleSettingsViewName;
    }

    function getCurrentPaymentShippingModuleSettingsUID()
    {
        return $this->CurrentPaymentShippingModuleSettingsUID;
    }

    ##########
    ##########
    function setCurrentPaymentModuleSettingsViewName($view_class_name)
    {
        $this->CurrentPaymentModuleSettingsViewName = $view_class_name;
    }

    function setCurrentPaymentShippingModuleSettingsUID($uid)
    {
        $this->CurrentPaymentShippingModuleSettingsUID = $uid;
    }

    function getCurrentShippingModuleSettingsViewName()
    {
        return $this->CurrentShippingModuleSettingsViewName;
    }

    function setCurrentShippingModuleSettingsViewName($view_class_name)
    {
        $this->CurrentShippingModuleSettingsViewName = $view_class_name;
    }

    ##########
    ##########
    /**
     * Returns complete person info with the specified id, which is not used
     * (Nov.2006). It wasn't tested with the person info variants.
     */
    function getPersonInfo($person_id)
    {
        global $application;
        $tables = $this->getTables();

        $pit = $tables['person_info_types']['columns'];
        $piv = $tables['person_info_variants']['columns'];
        $ptiv = $tables['person_to_info_variants']['columns'];
        $pd = $tables['persons_data']['columns'];

        # get a list of variants, associated with the person
        $query = new DB_Select();
        #get data on person info type variant
        $query->addSelectField($piv['tag'], 'piv_tag');
        $query->addLeftJoin('person_info_variants', $ptiv['variant_id'], DB_EQ, $piv['id']);
        $query->addSelectField($pit['id'], 'type_id');
        $query->addLeftJoin('person_info_types', $pit['id'], DB_EQ, $piv['type_id']);
        $query->addSelectField($ptiv['variant_id'], 'variant_id');
        $query->Where($ptiv['person_id'], DB_EQ, $person_id);
        $result = $application->db->getDB_Result($query);

        # get all data associated with that person
        $query = new DB_Select();
        $query->addSelectField($pd['value'], 'value');
        $query->addSelectField($pd['variant_id'], 'variant_id');
        $query->addSelectField($pd['attribute_id'], 'attribute_id');
        $query->Where($pd['person_id'], DB_EQ, $person_id);
        $data_result = $application->db->getDB_Result($query);
        # structure for the following usage
        $person_data = array();
        foreach ($data_result as $data)
        {
            if (!array_key_exists($data['variant_id'], $person_data))
            {
                $person_data[$data['variant_id']] = array();
            }
            $person_data[$data['variant_id']][$data['attribute_id']] = $data['value'];
        }

        $variants = array();
        for ($i=0; $i<sizeof($result); $i++)
        {
            $variant = $this->getPersonVariantInfo($result[$i]['variant_id']);
            # real attribute values for this type
            if (array_key_exists($variant['id'], $person_data))
            {
                $variant_value = &$person_data[$variant['id']];

                # save real values
                foreach ($variant['attr'] as $attr)
                {
                    if (!array_key_exists($attr['id'], $variant_value)) continue;
                    $variant['attr'][$attr['tag']]['value'] = $variant_value[$attr['id']];
                }
            }
            $variants[$variant['type_tag']] = $variant;
        }

        return $variants;
    }


    ##########
    ##########

    /**
     * Returns a list of Person Info variants.
     *
     * @author Oleg Vlasenko
     * @return array the array of variant ids and their visible prices
     */
    function getPersonInfoVariantList()
    {
        global $application;
        $tables = $this->getTables();
        $columns = $tables['person_info_variants']['columns'];
        $query = new DB_Select();
        $query->addSelectField($columns['id'], 'id');
        $query->addSelectField($columns['type_id'], 'type_id');
        $query->addSelectField($columns['visible_name'], 'visible_name');

        $result = $application->db->getDB_Result($query);

        $s = $tables['person_info_types']['columns'];

        $array = array();
        for ($i = 0; $i < sizeof($result); $i++)
        {
            $variant_id = $result[$i]['id'];
            $person_info_type_id = $result[$i]['type_id'];
            $visible_name = $this->getClearVariantName($result[$i]['visible_name']);
            $array[$variant_id] = array(
                'variant_id'    => $variant_id
               ,'visible_name'  => $visible_name
               ,'type_id'       => $person_info_type_id
            );
        }

        return $array;
    }

    //                person info type   On    Off       Off    On.
    function flipPersonInfoTypeStatus($person_info_type_id)
    {
        global $application;
        $tables = $this->getTables();
        $columns = $tables['person_info_types']['columns'];
        $query = new DB_Update('person_info_types');
        $query->addUpdateExpression($columns['active'], "IF(".$columns['active']." = '".DB_TRUE."','".DB_FALSE."','".DB_TRUE."')");
        $query->WhereValue($columns['id'], DB_EQ, $person_info_type_id);
        $application->db->getDB_Result($query);
    }

    ##########
    ##########

    /**
     * Returns a "clear" visible name for the Person Info variant.
     *
     * @author Oleg Vlasenko
     * @param string $variant_name - vsible variant name. For default variants
     *               a visible name begins with substring "[default]"
     * @return string a visible name without substring "[default]"
     */
    function getClearVariantName($variant_name)
    {
        $pos = _ml_strrpos($variant_name, "]");
        if ($pos != false)
        {
            $tail = _ml_substr($variant_name, $pos + 1);
            return $tail;
        }
        else
        {
            return $variant_name;
        }
    }


    /**
     * Returns a visible name for the Person Info variant.
     *
     * @author Oleg Vlasenko
     * @param integer $variant_id - the Id of the attribute variant.
     * @return string a visible name
     */
    function getVariantNameById($variant_id)
    {
        global $application;
        $tables = $this->getTables();
        $columns = $tables['person_info_variants']['columns'];
        $query = new DB_Select();
        $query->addSelectField($columns['visible_name'], 'visible_name');
        $query->WhereValue($columns['id'], DB_EQ, $variant_id);

        $result = $application->db->getDB_Result($query);
        $visible_name = $this->getClearVariantName($result[0]['visible_name']);

        return $visible_name;
    }


    /**
     * Returns a list of ids for the Person Info variant, sorted in order
     * fields sort.
     *
     * @author Oleg Vlasenko
     * @param integer $variant_id - the Id of the attribute variant.
     * @param bool $custom - if true only custom attributes will be returned
     * @return array the array of attribute ids
     */
    function getPersonInfoAttributeIdList($variant_id, $custom=STANDARD_ATTRIBUTES_ONLY)
    {
        global $application;
        $tables = $this->getTables();
        $s = $tables['person_info_variants_to_attributes']['columns'];
        $a = $tables['person_attributes']['columns'];

        $query = new DB_Select();
        $query->addSelectField($s['attribute_id'], 'attribute_id');
        $query->WhereValue($s['variant_id'], DB_EQ, $variant_id);
        if ($custom == CUSTOM_ATTRIBUTES_ONLY)
        {
            $query->WhereAND();
            $query->WhereValue($a['is_custom'], DB_EQ, 1);
            $query->WhereAND();
            $query->WhereField($a['id'], DB_EQ, $s['attribute_id']);
        }
        else if ($custom == STANDARD_ATTRIBUTES_ONLY)
        {
            $query->WhereAND();
            $query->WhereValue($a['is_custom'], DB_EQ, 0);
            $query->WhereAND();
            $query->WhereField($a['id'], DB_EQ, $s['attribute_id']);
        }
        else if ($custom == ALL_ATTRIBUTES)
        {

        }
        $query->SelectOrder($s['sort']);

        $result = $application->db->getDB_Result($query);

        $ids = array();
        for ($i = 0; $i < sizeof($result); $i++)
        {
            $ids[] = $result[$i]['attribute_id'];
        }

        return $ids;
    }


    /**
     * Returns a list of fields for the Person Info attribute.
     *
     * @author Oleg Vlasenko
     * @param integer $variant_id - the Id of the attribute variant.
     * @param integer $attribute_id - the attribute Id .
     * @return array the array of values of attribute fields.
     */
    function getPersonInfoFieldsList($variant_id, $attribute_id)
    {
        global $application;
        $tables = $this->getTables();
        $s = $tables['person_info_variants_to_attributes']['columns'];

        $query = new DB_Select();
        $query->addSelectField($s['variant_id'], 'variant_id');
        $query->addSelectField($s['attribute_id'], 'attribute_id');

        $query->setMultiLangAlias('_ml_name', 'person_info_variants_to_attributes', $s['name'], $s['id'], 'Checkout');
        $query->addSelectField($query->getMultiLangAlias('_ml_name'), 'name');

        $query->setMultiLangAlias('_ml_descr', 'person_info_variants_to_attributes', $s['descr'], $s['id'], 'Checkout');
        $query->addSelectField($query->getMultiLangAlias('_ml_descr'), 'descr');

        $query->addSelectField($s['unremovable'], 'unremovable');
        $query->addSelectField($s['visible'], 'visible');
        $query->addSelectField($s['required'], 'required');

        $query->WhereValue($s['variant_id'], DB_EQ, $variant_id);
        $query->WhereAnd();
        $query->WhereValue($s['attribute_id'], DB_EQ, $attribute_id);

        $result = $application->db->getDB_Result($query);

        if ($result != null)
        {

            $array = array(
                    'variant_id'    => $result[0]['variant_id']
                   ,'attribute_id'  => $result[0]['attribute_id']
                   ,'name'          => $result[0]['name']
                   ,'descr'         => $result[0]['descr']
                   ,'unremovable'   => $result[0]['unremovable']
                   ,'visible'       => $result[0]['visible']
                   ,'required'      => $result[0]['required']
                );

            return $array;
        }
        else
        {
            return null;
        }
    }

    /**
     * Returnts the full person attribute record (person_attributes and person_info_variants_to_attributes)
     *
     * @author Andrei V. Zhuravlev
     * @param $variantId integer variant id
     * @param $attributeId integer attribute id
     */
    function getPersonAttributeData($variantId, $attributeId)
    {
        global $application;
        $tables = $this->getTables();
        $piva = $tables['person_info_variants_to_attributes']['columns'];
        $pa = $tables['person_attributes']['columns'];

        $query = new DB_Select();

        foreach($piva as $k => $v)
            if ($k != 'name' && $k != 'descr')
                $query->addSelectField($v);

        $query->setMultiLangAlias('_ml_name', 'person_info_variants_to_attributes', $piva['name'], $piva['id'], 'Checkout');
        $query->addSelectField($query->getMultiLangAlias('_ml_name'), 'person_attribute_visible_name');

        $query->setMultiLangAlias('_ml_descr', 'person_info_variants_to_attributes', $piva['descr'], $piva['id'], 'Checkout');
        $query->addSelectField($query->getMultiLangAlias('_ml_descr'), 'person_attribute_description');

        foreach($pa as $v)
            $query->addSelectField($v);

        $query->WhereField($piva['attribute_id'], DB_EQ, $pa['id']);
        $query->WhereAnd();
        $query->WhereValue($piva['variant_id'], DB_EQ, $variantId);
        $query->WhereAnd();
        $query->WhereValue($piva['attribute_id'], DB_EQ, $attributeId);

        $result = $application->db->getDB_Result($query);
        return $result;
    }

    /**
     * Returnts the full person attribute record (person_attributes and person_info_variants_to_attributes)
     *
     * @author Andrei V. Zhuravlev
     * @param $variant_id integer variant id
     */
    function getPersonCustomAttributes($variant_id)
    {
        global $application;
        $tables = $this->getTables();
        $s = $tables['person_info_variants_to_attributes']['columns'];
        $a = $tables['person_attributes']['columns'];

        $query = new DB_Select();

        foreach($s as $k => $v)
            if ($k != 'name' && $k != 'descr')
                $query->addSelectField($v);

        $query->setMultiLangAlias('_ml_name', 'person_info_variants_to_attributes', $s['name'], $s['id'], 'Checkout');
        $query->addSelectField($query->getMultiLangAlias('_ml_name'), 'person_attribute_visible_name');

        $query->setMultiLangAlias('_ml_descr', 'person_info_variants_to_attributes', $s['descr'], $s['id'], 'Checkout');
        $query->addSelectField($query->getMultiLangAlias('_ml_descr'), 'person_attribute_description');

        foreach($a as $v)
            $query->addSelectField($v);

        $query->WhereValue($s['variant_id'], DB_EQ, $variant_id);
        /*if ($custom == CUSTOM_ATTRIBUTES_ONLY)
        {*/
            $query->WhereAND();
            $query->WhereValue($a['is_custom'], DB_EQ, 1);
            $query->WhereAND();
            $query->WhereField($a['id'], DB_EQ, $s['attribute_id']);
        //}
        /*else if ($custom == STANDARD_ATTRIBUTES_ONLY)
        {
            $query->WhereAND();
            $query->WhereValue($a['is_custom'], DB_EQ, 0);
            $query->WhereAND();
            $query->WhereField($a['id'], DB_EQ, $s['attribute_id']);
        }
        else if ($custom == ALL_ATTRIBUTES)
        {

        }*/
        $query->SelectOrder($s['sort']);

        $result = $application->db->getDB_Result($query);

        return $result;
    }


    function _addPersonAttribute($field_name, $input_type_id, $min_value, $max_value, $size)
    {
        global $application;
        $tables = $this->getTables();

        $pa = $tables['person_attributes']['columns'];

        $i = new DB_Insert('person_attributes');
        $i->addInsertValue($field_name,$pa['tag']);
        $i->addInsertValue(3,$pa['pattern_id']); //: all new attributes will be consiredered as simple strings
        $i->addInsertValue($input_type_id,$pa['input_type_id']);
        $i->addInsertValue("is_valid_no_validation",$pa['input_validation_func_name']); //: validation will not be applied to custom fields
        $i->addInsertValue($min_value,$pa['min']);
        $i->addInsertValue($max_value,$pa['max']);
        $i->addInsertValue($size,$pa['size']);
        $i->addInsertValue("1",$pa['is_custom']);

        $result['new_person_attribute'] = $application->db->getDB_Result($i);
        $result['attr_id'] = $application->db->DB_Insert_Id();

        return $result;
    }

    function _addPersonInfoData ($variant_id, $attr_id, $field_vis_name, $field_desc, $is_visible, $is_required, $sort_order, $field_type, $field_params)
    {
        global $application;
        $tables = $this->getTables();

        $piva = $tables['person_info_variants_to_attributes']['columns'];

        $i = new DB_Insert('person_info_variants_to_attributes');
        $i->addInsertValue($variant_id,$piva['variant_id']);
        $i->addInsertValue($attr_id,$piva['attribute_id']);
        $i->addMultiLangInsertValue($field_vis_name,$piva['name'],$piva['id'],'Checkout');
        $i->addMultiLangInsertValue($field_desc,$piva['descr'],$piva['id'],'Checkout');
        $i->addInsertValue(0,$piva['unremovable']);
        $i->addInsertValue($is_visible,$piva['visible']);
        $i->addInsertValue($is_required,$piva['required']);
        $i->addInsertValue($sort_order,$piva['sort']);
        $i->addInsertValue($field_type,$piva['field_type']);
        if ($field_params != null)
            $i->addInsertValue($field_params,$piva['field_params']);

        $result = $application->db->getDB_Result($i);

        return $result;
    }

    function _addInputType($type_name, $type_values=array(), $it_id = '')
    {
        global $application;

        $result = array();

        // magic numbers: 9 is the maximal pre-defined input type
        if ($it_id > 0 && $it_id <= 9)
            return;

        // removing empty values
        if (!is_array($type_values))
            $type_values = array($type_values);
        foreach($type_values as $k => $v)
            if (!trim($v))
                unset($type_values[$k]);

        $tables = modApiFunc('Catalog','getTables');
        $it = $tables['input_types']['columns'];
        $itv = $tables['input_type_values']['columns'];

        $i = new DB_Replace('input_types');
        if ($it_id)
            $i->addReplaceValue($it_id, $it['ut_id']);
        $i->addReplaceValue($type_name, $it['name']);
        $result['new_input_type'] = $application->db->getDB_Result($i);

        if ($it_id)
           $result['insert_type_id'] = $it_id;
        else
            $result['insert_type_id'] = $application->db->DB_Insert_Id();

        //adding values for new type
        $id = $result['insert_type_id'];
        if (is_array($type_values) && !empty($type_values))
        {
            $saved_values = array();
            if ($it_id)
            {
                $query = new DB_Select();
                $query->addSelectField($itv['id'], 'id');
                $query->WhereValue($itv['it_id'], DB_EQ, $id);
                $query->SelectOrder($itv['id'], 'ASC');
                $saved_values = $application->db->getDB_Result($query);
                if (!$saved_values)
                    $saved_values = array();
                else
                    foreach($saved_values as $k => $v)
                        $saved_values[$k] = $v['id'];
            }

            $index = 1;
            foreach ($type_values as $i => $value)
            {
                $old_id = array_shift($saved_values);
                if ($old_id)
                {
                    $upd = new DB_Update('input_type_values');
                    $upd->addMultiLangUpdateValue($itv['value'], $value,
                                                  $itv['id'], $old_id,
                                                  'Catalog');
                    $upd->WhereValue($itv['id'], DB_EQ, $old_id);
                    $application->db->getDB_Result($upd);
                }
                else
                {
                    $ins = new DB_Insert('input_type_values');
                    $ins->addInsertValue($id, $itv['it_id']);
                    $ins->addMultiLangInsertValue($value, $itv['value'],
                                                  $itv['id'], 'Catalog');
                    $application->db->getDB_Result($ins);
                }

                $result['new_input_values'][] = $value;
            }

            if (!empty($saved_values))
            {
                $d1 = new DB_Delete("input_type_values");
                $d1->deleteMultiLangField($itv['value'],
                                          $itv['id'], 'Catalog');
                $d1->WhereValue($itv['id'], DB_IN, '(\'' . implode('\', \'', $saved_values) . '\')');
                $application->db->getDB_Result($d1);
            }
        }
        elseif ($it_id)
        {
            $d1 = new DB_Delete("input_type_values");
            $d1->deleteMultiLangField($itv['value'],
                                      $itv['id'], 'Catalog');
            $d1->WhereValue($itv['it_id'], DB_EQ, $it_id);
            $application->db->getDB_Result($d1);
        }

        return $result;
    }

    function _updatePersonAttribute($attr_id, $input_type_id, $min_value, $max_value, $size)
    {
        global $application;
        $tables = $this->getTables();

        $pa = $tables['person_attributes']['columns'];

        $u = new DB_Update('person_attributes');
        #$u->addUpdateValue($pa['tag'], $field_name);
        #$u->addUpdateValue($pa['pattern_id'], 3); //: all new attributes will be consiredered as simple strings
        $u->addUpdateValue($pa['input_type_id'], $input_type_id);
        #$u->addUpdateValue($pa['input_validation_func_name'], "is_valid_no_validation");
        $u->addUpdateValue($pa['min'], $min_value);
        $u->addUpdateValue($pa['max'], $max_value);
        $u->addUpdateValue($pa['size'], $size);
        #$u->addUpdateValue($pa['is_custom'], "1");
        $u->WhereValue($pa['id'], DB_EQ, $attr_id);

        $result['upd_person_attribute'] = $application->db->getDB_Result($u);

        return $result;
    }

    function _updatePersonInfoData ($variant_id, $attr_id, $field_vis_name, $field_desc, $is_visible, $is_required, $field_type, $field_params)
    {
        global $application;
        $tables = $this->getTables();

        $piva = $tables['person_info_variants_to_attributes']['columns'];

        $up = new DB_Update('person_info_variants_to_attributes');
        $up->addMultiLangUpdateValue($piva['name'], $field_vis_name, $piva['id'], '', 'Checkout');
        $up->addMultiLangUpdateValue($piva['descr'], $field_desc, $piva['id'], '', 'Checkout');
        $up->addUpdateValue($piva['visible'], $is_visible);
        $up->addUpdateValue($piva['required'], $is_required);
        #$up->addUpdateValue($piva['sort'], $sort_order);
        $up->addUpdateValue($piva['field_type'], $field_type);
        if ($field_params != null)
            $up->addUpdateValue($piva['field_params'], $field_params);

        $up->WhereValue($piva['variant_id'], DB_EQ, $variant_id);
        $up->WhereAND();
        $up->WhereValue($piva['attribute_id'], DB_EQ, $attr_id);

        #$application->db->PrepareUpdateQuery($up);
        $result = $application->db->getDB_Result($up);

        return $result;
    }

    function updateCustomField($variantId, $attributeId, $field_visible_name, $field_desc, $is_visible, $is_required, $field_type, $values=null, $data=null)
    {
        global $application;
        $tables = $this->getTables();
        $piva = $tables['person_info_variants_to_attributes']['columns'];
        $pa = $tables['person_attributes']['columns'];

        $params = array();
        if (!empty($data))
        {
            $params = unserialize($data);
        }

        switch ($field_type)
        {
            case 'CUSTOM_FIELD_TYPE_TEXT':
                $size=20;
                if (!empty($params))
                {
                    $size=$params['size'];
                    $maxlength=$params['maxlength'];
                }
                $result = $this->_updatePersonAttribute($attributeId, 1, 0, $maxlength, $size);

                $r = $this->_updatePersonInfoData($variantId, $attributeId, $field_visible_name, $field_desc, $is_visible, $is_required, $field_type, $data);

            break;
            case 'CUSTOM_FIELD_TYPE_CHECKBOX':
                //:         !!!!                                                     .
                /*$result = $this->_updatePersonAttribute($attributeId, 1, 0, 128, 0);
                $this->_updatePersonInfoData($variantId, $attributeId, $field_visible_name, $field_desc, $is_visible, $is_required, $sort_order, $field_type, $data);*/
            break;
            case 'CUSTOM_FIELD_TYPE_TEXTAREA':

                $result = $this->_updatePersonAttribute($attributeId, 2, 0, 128, 0);
                $this->_updatePersonInfoData($variantId, $attributeId, $field_visible_name, $field_desc, $is_visible, $is_required, $field_type, $data);
            break;
            case 'CUSTOM_FIELD_TYPE_SELECT':
                if (is_array($values) && !empty($values))
                {
                    $data  = $this->getPersonAttributeData($variantId, $attributeId);

                    $r = $this->_addInputType("SELECT", $values, $data[0]['input_type_id']);
                }
                else
                {
                    //: show error
                }

                $it_id = $r['insert_type_id'];
                $result = $this->_updatePersonAttribute($attributeId, $it_id, 0, 128, 0);
                $this->_updatePersonInfoData($variantId, $attributeId, $field_visible_name, $field_desc, $is_visible, $is_required, $field_type, "");
            break;
        }
    }

    function addCustomField($variantId, $field_name, $field_visible_name, $field_desc, $is_visible, $is_required, $field_type, $values=null, $data=null, $sort_order=null)
    {
        global $application;
        $tables = $this->getTables();
        $piva = $tables['person_info_variants_to_attributes']['columns'];
        $pa = $tables['person_attributes']['columns'];

        $params = array();
        if (!empty($data))
        {
            $params = unserialize($data);
        }
        $sort_order=100;
        switch ($field_type)
        {
            case 'CUSTOM_FIELD_TYPE_TEXT':
                $size=20;
                if (!empty($params))
                {
                    $size=$params['size'];
                    $maxlength=$params['maxlength'];
                }
                $sort_order=100;
                $result = $this->_addPersonAttribute($field_name, 1, 0, $maxlength, $size);

                $this->_addPersonInfoData($variantId, $result['attr_id'], $field_visible_name, $field_desc, $is_visible, $is_required, $sort_order, $field_type, $data);
            break;
            case 'CUSTOM_FIELD_TYPE_CHECKBOX':
                $sort_order=100;
                //:         !!!!                                                     .
                /*$result = $this->_addPersonAttribute($field_name, 1, 0, 128, 0);
                $this->_addPersonInfoData($variantId, $result['attr_id'], $field_visible_name, $field_desc, $is_visible, $is_required, $sort_order, $field_type, $data);*/
            break;
            case 'CUSTOM_FIELD_TYPE_TEXTAREA':
                $sort_order=100;
                $result = $this->_addPersonAttribute($field_name, 2, 0, 128, 0);
                $this->_addPersonInfoData($variantId, $result['attr_id'], $field_visible_name, $field_desc, $is_visible, $is_required, $sort_order, $field_type, $data);
            break;
            case 'CUSTOM_FIELD_TYPE_SELECT':
                if (is_array($values) && !empty($values))
                {
                    $r = $this->_addInputType("SELECT", $values);
                }
                else
                {
                    //: show error
                }

                $sort_order=100;


                $it_id = $r['insert_type_id'];
                $result = $this->_addPersonAttribute($field_name, $it_id, 0, 128, 0);
                $this->_addPersonInfoData($variantId, $result['attr_id'], $field_visible_name, $field_desc, $is_visible, $is_required, $sort_order, $field_type, $data);
            break;
        }

        // adding the new field as infotag to order notifications
        // Note: for cc and bank account info the new infotags are NOT created
        if ($variantId == '2')
        {
            // Billing Info: adding the new field to the order notifications
            modApiFunc('Notifications', 'addCustomInfoTag',
                       'Billing' . $field_name);
        }
        elseif ($variantId == '3')
        {
            // Shipping Info: adding the new field to the order notifications
            modApiFunc('Notifications', 'addCustomInfoTag',
                       'Shipping' . $field_name);
        }

        return $result['attr_id'];
    }

    function removeCustomField($variantId, $attributeId)
    {
        global $application;

        $tables = $this->getTables();

        $piva = $tables['person_info_variants_to_attributes']['columns'];
        $pa = $tables['person_attributes']['columns'];

        $s = $this->getPersonAttributeData($variantId, $attributeId);

        // removing the field from notification infotags
        // Note: only for Billing and Shipping info
        if ($variantId == '2')
            modApiFunc('Notifications', 'removeCustomInfoTag',
                       'Billing' . $s[0]['person_attribute_view_tag']);
        elseif ($variantId == '3')
            modApiFunc('Notifications', 'removeCustomInfoTag',
                       'Shipping' . $s[0]['person_attribute_view_tag']);

        $d = new DB_Delete("person_attributes");
        $d->WhereValue($pa['id'], DB_EQ, $attributeId);
        $application->db->PrepareSQL($d);
        $application->db->DB_Exec();

        $d2 = new DB_Delete("person_info_variants_to_attributes");
        $d2->deleteMultiLangField($piva['name'], $piva['id'], 'Checkout');
        $d2->deleteMultiLangField($piva['descr'], $piva['id'], 'Checkout');
        $d2->WhereValue($piva['variant_id'], DB_EQ, $variantId);
        $d2->WhereAND();
        $d2->WhereValue($piva['attribute_id'], DB_EQ, $attributeId);
        $application->db->PrepareSQL($d2);
        $application->db->DB_Exec();

        if ($s[0]['input_type_id'] > 9) //: magic numbers!
        {
            $tables = modApiFunc('Catalog','getTables');
            $it = $tables['input_types']['columns'];
            $itv = $tables['input_type_values']['columns'];

            $d3 = new DB_Delete("input_types");
            $d3->WhereValue($it['id'], DB_EQ, $s[0]['input_type_id']);
            $application->db->PrepareSQL($d3);
            $application->db->DB_Exec();

            $d4 = new DB_Delete("input_type_values");
            $d4->deleteMultiLangField($itv['value'],
                                      $itv['id'], 'Catalog');
            $d4->WhereValue($itv['it_id'], DB_EQ, $s[0]['input_type_id']);

            $application->db->getDB_Result($d4);
        }
    }

    /**
     * Saves a list of fields for the Person Info attribute. If it saves fields
     * for the Country attribute and it is invisible, then the State attribute,
     * associated with it, should be invisible too.
     *
     * @author Oleg Vlasenko
     * @param integer $fields - the array of new values of the fields
     * @return void
     */
    function setPersonInfoFieldList($fields)
    {
        global $application;
        $tables = $this->getTables();
        $columns = $tables['person_info_variants_to_attributes']['columns'];

        $variant_id = $fields['variant_id'];
        $attribute_id = $fields['attribute_id'];

        $fields_original = $this->getPersonInfoFieldsList($variant_id, $attribute_id);
        $fields['unremovable'] = $fields_original['unremovable'];
        if ($fields_original['unremovable'] != 0)
        {
        	$fields['visible'] = '1';
            $fields['required'] = '1';
        }
        if ($fields_original['variant_id'] == 4) // Credit Card Info
        {
            $fields['unremovable'] = '1';
            $fields['required'] = '0';
        }
        $query = new DB_Update('person_info_variants_to_attributes');
        if ($fields['name'] != null)
        {
            $query->addMultiLangUpdateValue($columns['name'], $fields['name'], $columns['id'], '', 'Checkout');
        }
        if ($fields['descr'] != null)
        {
            $query->addMultiLangUpdateValue($columns['descr'], $fields['descr'], $columns['id'], '', 'Checkout');
        }
        $query->addUpdateValue($columns['unremovable'], $fields['unremovable']);
        $query->addUpdateValue($columns['visible'], $fields['visible']);
        $query->addUpdateValue($columns['required'], $fields['required']);
        $query->WhereValue($columns['variant_id'], DB_EQ, $variant_id);
        $query->WhereAnd();
        $query->WhereValue($columns['attribute_id'], DB_EQ, $attribute_id);
        $application->db->getDB_Result($query);

        // Correct the State field visibility if the Country field vsibility is changed
        if ($attribute_id == 9) // attrubute = Country
        {
            $country_visible = $fields['visible'];
            $states_attribute_id = 7;

            // get the State attribute state
            $query = new DB_Select();

            $query->addSelectField($columns['visible'], 'visible');
            $query->WhereValue($columns['variant_id'], DB_EQ, $variant_id);
            $query->WhereAnd();
            $query->WhereValue($columns['attribute_id'], DB_EQ, $states_attribute_id); // States

            $result = $application->db->getDB_Result($query);

            $state_visible = $result[0]['visible'];

            // If State is visible and Country is invisible, then hide State
            if (!$country_visible && $state_visible)
            {
                $query = new DB_Update('person_info_variants_to_attributes');
                $query->addUpdateValue($columns['visible'], 0);
                $query->addUpdateValue($columns['required'], 0);
                $query->WhereValue($columns['variant_id'], DB_EQ, $variant_id);
                $query->WhereAnd();
                $query->WhereValue($columns['attribute_id'], DB_EQ, $states_attribute_id);
                $application->db->getDB_Result($query);
            }
        }

        modApiFunc('EventsManager','throwEvent','CheckoutPersonInfoFieldUpdated',$fields);
    }


    /**
     * Saves the attribute sort in the variant to the DB.
     *
     * @author Oleg Vlasenko
     * @param array $attrSortOrderArray the array of attribute ids, the sequence
     *              of which is defined by sort_order of attributes.
     * @param integer $variantId the Id of the attribute variant.
     * @return void
     */
    function setAttributesSortOrder($attrSortOrderArray, $variantId)
    {
        global $application;

        $i = 1;
        foreach ($attrSortOrderArray as $attrId)
        {
            $tables = $this->getTables();
            $columns = $tables['person_info_variants_to_attributes']['columns'];
            $query = new DB_Update('person_info_variants_to_attributes');
            $query->addUpdateValue($columns['sort'], $i);
            $query->WhereValue($columns['variant_id'], DB_EQ, $variantId);
            $query->WhereAnd();
            $query->WhereValue($columns['attribute_id'], DB_EQ, $attrId);
            $application->db->getDB_Result($query);
            $i++;
        }

        modApiFunc('EventsManager','throwEvent','CheckoutAttributesSortOrderUpdated',$attrSortOrderArray, $variantId);
    }

    ##########
    ##########

    /**
     * Updates order info.
     */
    function updateOrder($data)
    {
        global $application;
        $history = array();

        if (!is_array($data))
        {
            return;
        }
        $tables = $this->getTables();

        $o = $tables['orders']['columns'];
        $on = $tables['order_notes']['columns'];
        $opd = $tables['order_person_data']['columns'];

        $statusChanged = array("order_status" => array(), "payment_status" => array());
        foreach ($data as $order)
        {
            $order_id = $order['order_id'];
            $status_id = $order['status_id'];
            $payment_status_id = $order['payment_status_id'];
            $track_id = $order['track_id'];
            $comment = $order['comment'];
            $processor_order_id = $order['processor_order_id'];
            $payment_method = $order['payment_method'];
            $shipping_method = $order['shipping_method'];
            $customer_info = $order['customer_info'];
            $billing_info = $order['billing_info'];
            $shipping_info = $order['shipping_info'];
            $bank_account_info = $order['bank_account_info'];
            $credit_card_info = $order['credit_card_info'];

            $tax_exemption = $order["tax_exemption"];
            $product_names = $order["product_names"];
            $product_prices = $order["product_prices"];
            $product_qty = $order["product_qty"];
            $product_options = $order["product_options"];
            $taxes = $order["taxes"];
            $totals = $order["order_totals"];

            # advanced order editing flag
            $adv_order_edit = false;

            if (!$this->isCorrectOrderId($order_id)) continue;

            # get current order info
            if (isset($order["order_totals"]))
                $order_view_currency = $this->getOrderInfo($order_id, $totals["order_currency"]);

            $order = $this->getOrderInfo($order_id, modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $order_id));

            # check if it is necessary to update status
            $update_status_id = $status_id != $order['StatusId'];
            $update_payment_status_id = $payment_status_id != $order['PaymentStatusId'];
            $update_track_id = $track_id != null && $track_id != $order['TrackId'];
            $update_processor_order_id = $processor_order_id != null && $processor_order_id != $order['PaymentProcessorOrderId'];
            $update_payment_method = $payment_method != null && $payment_method != $order['PaymentMethod'];
            $update_shipping_method = $shipping_method != null && $shipping_method != $order['ShippingMethod'];

            if($update_status_id)
            {
                $statusChanged["order_status"][$order_id] = array("old_status" => $order['StatusId'], "new_status" => $status_id);
            }
            if($update_payment_status_id)
            {
                $statusChanged["payment_status"][$order_id] = array("old_status" => $order['PaymentStatusId'], "new_status" => $payment_status_id);
            }

            if($update_status_id || $update_payment_status_id || $update_track_id || $update_processor_order_id || $update_payment_method || $update_shipping_method)
            {
                $db_update = new DB_Update('orders');
                $db_update->addUpdateValue($o['status_id'], $status_id);
                $db_update->addUpdateValue($o['payment_status_id'], $payment_status_id);
                if ($update_track_id)
                {
                    $db_update->addUpdateValue($o['track_id'], $track_id);
                }
                if ($update_processor_order_id)
                {
                    $db_update->addUpdateValue($o['payment_processor_order_id'], $processor_order_id);
                }
                if ($update_payment_method)
                {
                    $db_update->addUpdateValue($o['payment_method'], $payment_method);
                }
                if ($update_shipping_method)
                {
                    $db_update->addUpdateValue($o['shipping_method'], $shipping_method);
                }
                $db_update->WhereValue($o['id'], DB_EQ, $order_id);
                $application->db->PrepareSQL($db_update);
                $application->db->DB_Exec();
            }

            #  check if it is necessary to update Customer Info
            $update_customer_info = false;
            if (is_array($customer_info))
            {
                foreach ($order['Customer']['attr'] as $tag => $attr)
                {
                    if (array_key_exists($tag, $customer_info) && trim($attr['value']) != trim($customer_info[$tag]))
                    {
                        $update_customer_info = true;
                        $db_update = new DB_Update('order_person_data');
                        $db_update->addUpdateValue($opd['value'], $customer_info[$tag]);
                        $db_update->WhereValue($opd['id'], DB_EQ, $attr['id']);
                        $application->db->PrepareSQL($db_update);
                        $application->db->DB_Exec();
                    }
                }
            }

            #  check if it is necessary to update Billing Info
            $update_billing_info = false;
            if (is_array($billing_info))
            {
                foreach ($order['Billing']['attr'] as $tag => $attr)
                {
                    if (array_key_exists($tag, $billing_info) && trim($attr['value']) != trim($billing_info[$tag]))
                    {
                        $update_billing_info = true;
                        $db_update = new DB_Update('order_person_data');
                        $db_update->addUpdateValue($opd['value'], $billing_info[$tag]);
                        $db_update->WhereValue($opd['id'], DB_EQ, $attr['id']);
                        $application->db->PrepareSQL($db_update);
                        $application->db->DB_Exec();
                    }
                }
            }

            #      check if it is necessary to update Shipping Info
            $update_shipping_info = false;
            if (is_array($shipping_info))
            {
                foreach ($order['Shipping']['attr'] as $tag => $attr)
                {
                    if (array_key_exists($tag, $shipping_info) && trim($attr['value']) != trim($shipping_info[$tag]))
                    {
                        $update_shipping_info = true;
                        $db_update = new DB_Update('order_person_data');
                        $db_update->addUpdateValue($opd['value'], $shipping_info[$tag]);
                        $db_update->WhereValue($opd['id'], DB_EQ, $attr['id']);
                        $application->db->PrepareSQL($db_update);
                        $application->db->DB_Exec();
                    }
                }
            }

            #      check if it is necessary to update Bank Account Info
            $update_bank_account_info = false;
            if (is_array($bank_account_info))
            {
                foreach ($order['BankAccount']['attr'] as $tag => $attr)
                {
                    if (array_key_exists($tag, $bank_account_info) && trim($attr['value']) != trim($bank_account_info[$tag]))
                    {
                        $update_bank_account_info = true;
                        $db_update = new DB_Update('order_person_data');
                        $db_update->addUpdateValue($opd['value'], $bank_account_info[$tag]);
                        $db_update->WhereValue($opd['id'], DB_EQ, $attr['id']);
                        $application->db->PrepareSQL($db_update);
                        $application->db->DB_Exec();
                    }
                }
            }

            #      check if it is necessary to update Credit Card Info
            $update_credit_card_info = false;
            if (is_array($credit_card_info))
            {
                //Credit card info is ALWAYS encrypted.
                //Prepare the encrypting keys.

                $symmetric_secret_key = modApiFunc("Crypto", "blowfish_gen_blowfish_key");
                $rsa_public_key = modApiFunc("Payment_Module_Offline_CC", "getRSAPublicKeyInCryptRSAFormat");
                $rsa_public_key_asc_format = modApiFunc("Payment_Module_Offline_CC", "getRSAPublicKeyInASCFormat");

                $rsa_obj = new Crypt_RSA;
                $encrypted_symmetric_secret_key = $rsa_obj->encrypt($symmetric_secret_key, $rsa_public_key);
                $person_info_variant_id = $order['CreditCard']['person_info_variant_id'];
                foreach ($order['CreditCard']['attr'] as $tag => $attr)
                {
                    if (array_key_exists($tag, $credit_card_info))// && trim($attr['value']) != trim($credit_card_info[$tag]))
                    //Reencrypt the whole block rather than by attributes. This is the fast way.
                    {
                        $update_credit_card_info = true;
                        //Not encrypted obfuscated value
                        $b_encrypted = false;
                        $db_update = new DB_Update('order_person_data');
                        $db_update->addUpdateValue($opd['value'], $this->get_public_view_of_secured_data($credit_card_info[$tag], $attr['person_attribute_id']));
                        $db_update->addUpdateValue($opd['encrypted_secret_key'], $encrypted_symmetric_secret_key);
                        $db_update->addUpdateValue($opd['rsa_public_key_asc_format'], $rsa_public_key_asc_format);
                        $db_update->WhereValue($opd['order_id'], DB_EQ, $order_id);
                        $db_update->WhereAND();
                        $db_update->WhereValue($opd['variant_id'], DB_EQ, $person_info_variant_id);
                        $db_update->WhereAND();
                        $db_update->WhereValue($opd['attribute_id'], DB_EQ, $attr['person_attribute_id']);
                        $db_update->WhereAND();
                        $db_update->WhereValue($opd['b_encrypted'], DB_EQ, "0");
                        $application->db->PrepareSQL($db_update);
                        $application->db->DB_Exec();

                        //  encrypted not obfuscated value
                        $b_encrypted = true;
                        $db_update = new DB_Update('order_person_data');
                        $db_update->addUpdateValue($opd['value'], base64_encode($this->encryptOrderPersonAttribute($credit_card_info[$tag], $symmetric_secret_key)));
                        $db_update->addUpdateValue($opd['encrypted_secret_key'], $encrypted_symmetric_secret_key);
                        $db_update->addUpdateValue($opd['rsa_public_key_asc_format'], $rsa_public_key_asc_format);
                        $db_update->WhereValue($opd['order_id'], DB_EQ, $order_id);
                        $db_update->WhereAND();
                        $db_update->WhereValue($opd['variant_id'], DB_EQ, $person_info_variant_id);
                        $db_update->WhereAND();
                        $db_update->WhereValue($opd['attribute_id'], DB_EQ, $attr['person_attribute_id']);
                        $db_update->WhereAND();
                        $db_update->WhereValue($opd['b_encrypted'], DB_EQ, "1");
                        $application->db->PrepareSQL($db_update);
                        $application->db->DB_Exec();
                    }
                }
            }

            // @

            // update product options
            if (is_array($product_options))
            {
                $cols = $tables["order_product_options"]['columns'];
                foreach ($product_options as $key => $value)
                {
                    $db_update = new DB_Update('order_product_options');
                    $db_update->addUpdateValue($cols['option_value'], $value);
                    $db_update->WhereValue($cols["product_option_id"], DB_EQ, $key);
                    $application->db->PrepareSQL($db_update);
                    $application->db->DB_Exec();

                    foreach ($order_view_currency["Products"] as $pid => $product)
                    {
                        foreach ($product["options"] as $oid => $option)
                        {
                            if ($option["product_option_id"] == $key
                                && $option["option_value"] != $value)
                            {
                                $history[] = new ActionMessage(array('ORDERS_HISTORY_PRODUCT_OPTION_UPDATED', $option["option_value"], $value));
                                $adv_order_edit = true;
                            }
                        }
                    }
                }
            }

            // update order product names
            if (is_array($product_names))
            {
                $cols = $tables["order_product"]['columns'];
                foreach ($product_names as $key => $value)
                {
                    $db_update = new DB_Update("order_product");
                    $db_update->addUpdateValue($cols["name"], $value);
                    $db_update->WhereValue($cols["id"], DB_EQ, $key);
                    $application->db->PrepareSQL($db_update);
                    $application->db->DB_Exec();

                    foreach ($order_view_currency["Products"] as $pid => $product)
                    {
                        if ($product["id"] == $key
                            && $product["name"] != $value)
                        {
                            $history[] = new ActionMessage(array('ORDERS_HISTORY_PRODUCT_NAME_UPDATED', $product["name"], $value));
                            $adv_order_edit = true;
                        }
                    }
                }
            }

            // update prices
            $arePricesUpdated = false;
            if ($totals["arePricesEdited"] == "true")
            {
                $order_view_currency["taxExemption"] = modApiFunc("TaxExempts", "getOrderFullTaxExempts", intval($order_id), false);

                $totals["taxes"] = $taxes;
                $totals["products"]["names"] = $product_names;
                $totals["products"]["prices"] = $product_prices;
                $totals["products"]["qty"] = $product_qty;

                // get order's currencies
                $currencies = $this->getOrderCurrencyList($order_id);

                // get editor's currency
                $edited_currency = modApiFunc("Localization", "getCurrencyCodeById", $totals["order_currency"]);
                $history[] = new ActionMessage(array('ORDERS_HISTORY_PRICES_UPDATED', $edited_currency));

                // find currency rates
                foreach ($currencies as $cur)
                {
                    $current_prices[$cur["currency_code"]] = execQuery('SELECT_ORDER_PRICES', array('order_id'=>$order_id, 'currency_code'=>$cur["currency_code"]));
                }

                $rates = array();
                foreach ($currencies as $cur)
                {
                    if ($cur["currency_code"] === $edited_currency)
                        $rates[$cur["currency_code"]] = 1;
                    else
                        $rates[$cur["currency_code"]] = $current_prices[$cur["currency_code"]][0]["order_total"] / $current_prices[$edited_currency][0]["order_total"];
                }

                // subtotal
                $totals["subtotal"] = 0;
                foreach ($product_prices as $prod_id => $prod)
                {
                    $totals["subtotal"] += $prod * $product_qty[$prod_id];
                }

                // discounted subtotal
                $totals["discounted_subtotal"] =
                    $totals["subtotal"]
                    - $totals["global_discount"]
                    - $totals["promocode_discount"]
                    - $totals["qty_discount"];

                // total, taxes
                $totals["tax_total"] = 0;
                $totals["tax_total_wo_included"] = 0;
                $totals["total"] = $totals["discounted_subtotal"] + $totals["shipping_handling"];

                if (is_array($taxes))
                foreach ($taxes as $key => $value)
                {
                    if ($value["value"] == PRICE_N_A)
                        continue;

                    if ($order_view_currency["DisplayIncludedTax"] == true)
                    {
                        if ($value["is_included"] == "1")
                        {
                            if ($tax_exemption == "true")
                            {
                                $totals["total"] -= $value["value"];
                            }
                        }
                        else if ($value["is_included"] == "0")
                        {
                            if ($tax_exemption == "false")
                            {
                                $totals["total"] += $value["value"];
                            }
                            $totals["tax_total_wo_included"] += $value["value"];
                        }
                    }
                    else //if ($order_view_currency["DisplayIncludedTax"] == false)
                    {
                        if ($tax_exemption == "false")
                        {
                            $totals["total"] += $value["value"];
                        }
                        $totals["tax_total_wo_included"] += $value["value"];
                    }
                    $totals["tax_total"] += $value["value"];
                }

                // update tax exemption mark
                if ($tax_exemption != null)
                {
                    $tax_tables = modApiFunc("TaxExempts", "getTables");
                    $col = $tax_tables['order_full_tax_exempts']['columns'];
                    $db_update = new DB_Update('order_full_tax_exempts');
                    $db_update->addUpdateValue($col['exempt_status'], $tax_exemption);
                    $db_update->WhereValue($col['order_id'], DB_EQ, intval($order_id));
                    $application->db->PrepareSQL($db_update);
                    $application->db->DB_Exec();

                    if (is_array($order_view_currency["taxExemption"])
                        && isset($order_view_currency["taxExemption"][0]["exempt_status"])
                        && $order_view_currency["taxExemption"][0]["order_id"] == intval($order_id)
                        && $order_view_currency["taxExemption"][0]["exempt_status"] != $tax_exemption)
                    {
                        $old_value = ($order_view_currency["taxExemption"][0]["exempt_status"] == "true") ? "Yes" : "No";
                        $new_value = ($tax_exemption == "true") ? "Yes" : "No";
                        $history[] = new ActionMessage(array('ORDERS_HISTORY_TAX_EXEMPTION_UPDATED', $old_value, $new_value));
                        $arePricesUpdated = true;
                        $adv_order_edit = true;
                    }
                }

                // update order product quantities
                if (is_array($product_qty))
                {
                    $cols = $tables["order_product"]['columns'];
                    foreach ($product_qty as $key => $value)
                    {
                        $db_update = new DB_Update("order_product");
                        $db_update->addUpdateValue($cols["qty"], $value);
                        $db_update->WhereValue($cols["id"], DB_EQ, $key);
                        $application->db->PrepareSQL($db_update);
                        $application->db->DB_Exec();

                        foreach ($order_view_currency["Products"] as $pid => $product)
                        {
                            if ($product["id"] == $key
                                && $product["qty"] != $value)
                            {
                                $history[] = new ActionMessage(array('ORDERS_HISTORY_PRODUCT_QUANTITY_UPDATED', $product["qty"], $value));
                                $arePricesUpdated = true;
                                $adv_order_edit = true;
                            }
                        }
                    }
                }

                // update order prices
                $cols = $tables["order_prices"]['columns'];
                foreach ($rates as $code => $rate)
                {
                    $db_update = new DB_Update("order_prices");

                    $db_update->addUpdateValue($cols["order_total"],                    $totals["total"] * $rate);
                    $db_update->addUpdateValue($cols["order_subtotal"],                 $totals["subtotal"] * $rate);
                    $db_update->addUpdateValue($cols["total_shipping_and_handling_cost"], $totals["shipping_handling"] * $rate);
                    $db_update->addUpdateValue($cols["order_tax_total"],                $totals["tax_total"] * $rate);
                    $db_update->addUpdateValue($cols["subtotal_global_discount"],       $totals["global_discount"] * $rate);
                    $db_update->addUpdateValue($cols["subtotal_promo_code_discount"],   $totals["promocode_discount"] * $rate);
                    $db_update->addUpdateValue($cols["quantity_discount"],              $totals["qty_discount"] * $rate);
                    $db_update->addUpdateValue($cols["discounted_subtotal"],            $totals["discounted_subtotal"] * $rate);
                    $db_update->addUpdateValue($cols["order_not_included_tax_total"],   $totals["tax_total_wo_included"] * $rate);

                    $db_update->WhereValue($cols["order_id"], DB_EQ, intval($order_id));
                    $db_update->WhereAND();
                    $db_update->WhereValue($cols["currency_code"], DB_EQ, $code);

                    $application->db->PrepareSQL($db_update);
                    $application->db->DB_Exec();
                }

                if ($order_view_currency["Price"]["SubtotalGlobalDiscount"] != $totals["global_discount"])
                {
                    $history[] = new ActionMessage(array('ORDERS_HISTORY_GLOBAL_DISCOUNT_UPDATED', $order_view_currency["Price"]["SubtotalGlobalDiscount"], $totals["global_discount"]));
                    $arePricesUpdated = true;
                }
                if ($order_view_currency["Price"]["SubtotalPromoCodeDiscount"] != $totals["promocode_discount"])
                {
                    $history[] = new ActionMessage(array('ORDERS_HISTORY_PROMOCODE_DISCOUNT_UPDATED', $order_view_currency["Price"]["SubtotalPromoCodeDiscount"], $totals["promocode_discount"]));
                    $arePricesUpdated = true;
                }
                if ($order_view_currency["Price"]["QuantityDiscount"] != $totals["qty_discount"])
                {
                    $history[] = new ActionMessage(array('ORDERS_HISTORY_QUANTITY_DISCOUNT_UPDATED', $order_view_currency["Price"]["QuantityDiscount"], $totals["qty_discount"]));
                    $arePricesUpdated = true;
                }
                if ($order_view_currency["Price"]["TotalShippingAndHandlingCost"] != $totals["shipping_handling"])
                {
                    $history[] = new ActionMessage(array('ORDERS_HISTORY_SHIPPING_HANDLING_UPDATED', $order_view_currency["Price"]["TotalShippingAndHandlingCost"], $totals["shipping_handling"]));
                    $arePricesUpdated = true;
                }

                // update order product prices
                if (is_array($product_prices))
                {
                    $cols = $tables["order_product_to_attributes"]['columns'];
                    foreach ($product_prices as $key => $value)
                    {
                        foreach ($order_view_currency["Products"] as $pid => $product)
                        {
                            if ($product["id"] == $key
                                && $product["SalePrice"] != $value)
                            {
                                $history[] = new ActionMessage(array('ORDERS_HISTORY_PRODUCT_SALEPRICE_UPDATED', $product["SalePrice"], $value));
                                $arePricesUpdated = true;
                            }
                        }

                        foreach ($rates as $code => $rate)
                        {
                            $db_update = new DB_Update("order_product_to_attributes");

                            $db_update->addUpdateValue($cols["value"], $value * $rate);

                            $db_update->WhereValue($cols["attribute_id"], DB_EQ, "1");
                            $db_update->WhereAND();
                            $db_update->WhereValue($cols["currency_code"], DB_EQ, $code);
                            $db_update->WhereAND();
                            $db_update->WhereValue($cols["product_id"], DB_EQ, $key);

                            $application->db->PrepareSQL($db_update);
                            $application->db->DB_Exec();
                        }
                    }
                }

                if (is_array($taxes))
                {
                    // update order taxes
                    $current_taxes = execQuery('SELECT_ORDER_TAXES', array('order_id'=>$order_id, 'currency_code'=>$edited_currency));
                    $cols = $tables["order_taxes"]['columns'];

                    foreach ($current_taxes as $tax)
                    {
                        if ($order_view_currency["Price"]["taxes"][$tax["id"]]["value"] != $taxes[$tax["id"]]["value"])
                        {
                            $history[] = new ActionMessage(array('ORDERS_HISTORY_TAX_UPDATED', $order_view_currency["Price"]["taxes"][$tax["id"]]["name"], $order_view_currency["Price"]["taxes"][$tax["id"]]["value"], $taxes[$tax["id"]]["value"]));
                            $arePricesUpdated = true;
                        }

                        foreach ($rates as $code => $rate)
                        {
                            $db_update = new DB_Update("order_taxes");

                            $db_update->addUpdateValue($cols["value"], $taxes[$tax["id"]]["value"] * $rate);

                            $db_update->WhereValue($cols["order_id"], DB_EQ, intval($order_id));
                            $db_update->WhereAND();
                            $db_update->WhereValue($cols["currency_code"], DB_EQ, $code);
                            $db_update->WhereAND();
                            $db_update->WhereValue($cols["type"], DB_EQ, $tax["type"]);

                            $application->db->PrepareSQL($db_update);
                            $application->db->DB_Exec();
                        }
                    }

                    // update order tax display options
                    $current_tdo = execQuery('SELECT_ORDER_TAX_DISPLAY_OPTIONS', array('order_id'=>$order_id, 'currency_code'=>$edited_currency));
                    $cols = $tables["order_tax_display_options"]['columns'];

                    foreach ($current_tdo as $tdo)
                    {
                        $new_amount = 0;
                        $formula = explode(',', $tdo["formula"]);
                        foreach ($formula as $sign)
                        {
                            if ($taxes[$sign]["value"] != PRICE_N_A)
                                $new_amount += $taxes[$sign]["value"];
                        }

                        // @ a strange error here: (float(6.39) != float(6.39)) === true
                        if (floatval($new_amount) != floatval($order_view_currency["Price"]["tax_dops"][$tdo["id"]]["value"]))
                        {
                            $history[] = new ActionMessage(array('ORDERS_HISTORY_TDO_UPDATED', $tdo["name"], $order_view_currency["Price"]["tax_dops"][$tdo["id"]]["value"], $new_amount));
                            $arePricesUpdated = true;
                        }

                        foreach ($rates as $code => $rate)
                        {
                            $db_update = new DB_Update("order_tax_display_options");

                            $db_update->addUpdateValue($cols["value"], $new_amount * $rate);


                            $db_update->WhereValue($cols["name"], DB_EQ, $tdo["name"]);
                            $db_update->WhereAND();
                            $db_update->WhereValue($cols["order_id"], DB_EQ, intval($order_id));
                            $db_update->WhereAND();
                            $db_update->WhereValue($cols["currency_code"], DB_EQ, $code);

                            $application->db->PrepareSQL($db_update);
                            $application->db->DB_Exec();
                        }
                    }
                }

                // update odrers "was_manually_edited" flag
                $cols = $tables["orders"]['columns'];
                $db_update = new DB_Update('orders');
                $db_update->addUpdateValue($cols['edited'], 1);
                $db_update->WhereValue($cols["id"], DB_EQ, intval($order_id));
                $application->db->PrepareSQL($db_update);
                $application->db->DB_Exec();

            } // if ($totals["arePricesEdited"] == "true")

            # add comment, if it is defined
            if (!empty($comment))
            {
                $query = new DB_Insert('order_notes');
                $query->addInsertValue($order_id, $on['order_id']);
                $query->addInsertValue(date('Y-m-d H:i:s', time()/*getServerTime()*/), $on['date']);
                $query->addInsertValue($comment, $on['content']);
                $query->addInsertValue('comment', $on['type']);
                $application->db->getDB_Result($query);
            }

            # generate records for the order history
            $status_list = $this->getOrderStatusList();
            $payment_status_list = $this->getOrderPaymentStatusList();
            if ($update_status_id)
            {
                $history[] = new ActionMessage(array('ORDERS_HISTORY_STATUS_UPDATED', $status_list[$order['StatusId']]['name'], $status_list[$status_id]['name']));
            }
            if ($update_payment_status_id)
            {
                $history[] = new ActionMessage(array('ORDERS_HISTORY_PAYMENT_STATUS_UPDATED', $payment_status_list[$order['PaymentStatusId']]['name'], $payment_status_list[$payment_status_id]['name']));
            }
            if ($update_track_id)
            {
                $history[] = new ActionMessage(array('ORDERS_HISTORY_TRACKING_NUMBER_UPDATED', $order['TrackId'], $track_id));
            }
            if ($update_processor_order_id)
            {
                $history[] = new ActionMessage(array('ORDERS_HISTORY_PAYMENT_PROCESSOR_ORDER_ID_UPDATED', $order['PaymentProcessorOrderId'], $processor_order_id));
            }
            if ($update_payment_method)
            {
                $history[] = new ActionMessage(array('ORDERS_HISTORY_PAYMENT_METHOD_UPDATED', $order['PaymentMethod'], $payment_method));
            }
            if ($update_shipping_method)
            {
                $history[] = new ActionMessage(array('ORDERS_HISTORY_SHIPPING_METHOD_UPDATED', $order['ShippingMethod'], $shipping_method));
            }
            if ($update_customer_info)
            {
                $history[] = new ActionMessage(array('ORDERS_HISTORY_CUSTOMER_INFO_UPDATED'));
            }
            if ($update_billing_info)
            {
                $history[] = new ActionMessage(array('ORDERS_HISTORY_BILLING_INFO_UPDATED'));
            }
            if ($update_shipping_info)
            {
                $history[] = new ActionMessage(array('ORDERS_HISTORY_SHIPPING_INFO_UPDATED'));
            }
            if ($update_bank_account_info)
            {
                $history[] = new ActionMessage(array('ORDERS_HISTORY_BANK_ACCOUNT_INFO_UPDATED'));
            }
            if ($update_credit_card_info)
            {
                $history[] = new ActionMessage(array('ORDERS_HISTORY_CREDIT_CARD_INFO_UPDATED'));
            }

            if ($arePricesUpdated === true)
            {
                foreach ($rates as $code => $rate)
                {
                    $history[] = new ActionMessage(array('ORDERS_HISTORY_TOTAL_UPDATED', $code));
                }
            }
            else if ($totals["arePricesEdited"] == "true")
            {
                $history[] = new ActionMessage(array('ORDERS_HISTORY_PRICES_INTACT'));
            }

            $messageResources = &$application->getInstance('MessageResources');
            $history_text = "";
            foreach ($history as $record)
            {
                $history_text .= $messageResources->getMessage($record) . "\n";
            }

            if(!empty($history_text))
            {
                $this->addOrderHistory($order_id, $history_text);
            }
        }

        if (!empty($statusChanged['order_status']) or !empty($statusChanged['payment_status']))
        {
            modApiFunc('EventsManager','throwEvent','OrdersWereUpdated',$statusChanged);
        }

        if (!isset($arePricesUpdated)) $arePricesUpdated = false;
        if ($adv_order_edit == true || $arePricesUpdated == true)
        {
        	$event_data = (int)$order_id;
        	modApiFunc('EventsManager','throwEvent','OrderDataEdited',$event_data);
        }

        modApiFunc('EventsManager','throwEvent','OrderStatusUpdated',$statusChanged);

        return $statusChanged;
    }

    /**
     * Copies the part of the updateOrder($data) functionality. It is created
     * only, because updateOrder is too hungus for the small update.
     */
    function updateOrderPersonDataAttribute($order_id, $person_info_variant_id, $person_attribute_id, $b_encrypted, $encrypted_symmetric_secret_key, $rsa_public_key_asc_format, $new_value)
    {
        global $application;
        $tables = $this->getTables();

        $opd = $tables['order_person_data']['columns'];
        $db_update = new DB_Update('order_person_data');
        $db_update->addUpdateValue($opd['value'], $new_value);
        $db_update->addUpdateValue($opd['encrypted_secret_key'], $encrypted_symmetric_secret_key);
        $db_update->addUpdateValue($opd['rsa_public_key_asc_format'], $rsa_public_key_asc_format);
        $db_update->WhereValue($opd['order_id'], DB_EQ, $order_id);
        $db_update->WhereAND();
        $db_update->WhereValue($opd['attribute_id'], DB_EQ, $person_attribute_id);
        $db_update->WhereAND();
        $db_update->WhereValue($opd['variant_id'], DB_EQ, $person_info_variant_id);
        $db_update->WhereAND();
        $db_update->WhereValue($opd['b_encrypted'], DB_EQ, $b_encrypted ? "1" : "0");
        $application->db->PrepareSQL($db_update);
        $application->db->DB_Exec();
    }
    /**
     *
     *
     * @author Alexandr Girin
     * @param
     * @return
     */
    function setOrdersIDForDelete($ordersId)
    {
        $this->ordersId = $ordersId;
    }

    /**
     *
     *
     * @author Alexandr Girin
     * @param
     * @return
     */
    function getOrdersIDForDelete()
    {
        return $this->ordersId;
    }

    /**
     *
     *
     * @author Alexandr Girin
     * @param
     * @return
     */
    function setDeleteOrdersFlag($value)
    {
        $this->DeleteOrdersFlag = $value;
    }

    /**
     *
     *
     * @author Alexandr Girin
     * @param
     * @return
     */
    function getDeleteOrdersFlag()
    {
        return $this->DeleteOrdersFlag;
    }

    /**
     *
     *
     * @author Alexandr Girin
     * @param
     * @return
     */
    function DeleteOrders($ordersId)
    {
        modApiFunc('EventsManager','throwEvent','OrdersWillBeDeleted',$ordersId);

        global $application;

        $tables = $this->getTables();
        $on = $tables['order_notes']['columns'];
        $opd = $tables['order_person_data']['columns'];
        $opr = $tables['order_prices']['columns'];
        $otx = $tables['order_taxes']['columns'];
        $otdo = $tables['order_tax_display_options']['columns'];
        $op = $tables['order_product']['columns'];
        $opca = $tables['order_product_custom_attributes']['columns'];
        $opta = $tables['order_product_to_attributes']['columns'];
        $opot = $tables['order_product_options']['columns'];
        $o = $tables['orders']['columns'];

        $DB_IN_string = "('".implode("', '", $ordersId)."')";

        $query = new DB_Select();
        $query->addSelectField($op['id'], 'id');
        $query->WhereField($op['order_id'], DB_IN, $DB_IN_string);
        $order_products_id = $application->db->getDB_Result($query);
        foreach ($order_products_id as $key => $order_product_id)
        {
            $order_products_id[$key] = $order_product_id['id'];
        }

        $query = new DB_Delete('order_notes');
        $query->WhereField($on['order_id'], DB_IN, $DB_IN_string);
        $application->db->getDB_Result($query);

        $query = new DB_Delete('order_person_data');
        $query->WhereField($opd['order_id'], DB_IN, $DB_IN_string);
        $application->db->getDB_Result($query);

        $query = new DB_Delete('order_prices');
        $query->WhereField($opr['order_id'], DB_IN, $DB_IN_string);
        $application->db->getDB_Result($query);

        $query = new DB_Delete('order_taxes');
        $query->WhereField($otx['order_id'], DB_IN, $DB_IN_string);
        $application->db->getDB_Result($query);

        $query = new DB_Delete('order_tax_display_options');
        $query->WhereField($otdo['order_id'], DB_IN, $DB_IN_string);
        $application->db->getDB_Result($query);

        $query = new DB_Delete('order_product');
        $query->WhereField($op['order_id'], DB_IN, $DB_IN_string);
        $application->db->getDB_Result($query);

        $query = new DB_Delete('order_product_custom_attributes');
        $query->WhereField($opca['product_id'], DB_IN, "('".implode("', '", $order_products_id)."')");
        $application->db->getDB_Result($query);

        $query = new DB_Delete('order_product_to_attributes');
        $query->WhereField($opta['product_id'], DB_IN, "('".implode("', '", $order_products_id)."')");
        $application->db->getDB_Result($query);

        $query = new DB_Select();
        $query->addSelectField($opot['option_value'], 'option_value');
        $query->WhereValue($opot['is_file'], DB_EQ, 'Y');
        $query->WhereAND();
        $query->Where($opot['order_product_id'], DB_IN, "('".implode("', '", $order_products_id)."')");
        $__res = $application->db->getDB_Result($query);
        if(count($__res) > 0)
        {
            foreach($__res as $oinfo)
                if($oinfo['option_value'] != '')
                    modApiFunc('Shell','removeDirectory',dirname($oinfo['option_value']));
        };

        $query = new DB_Delete('order_product_options');
        $query->WhereField($opot['order_product_id'], DB_IN, "('".implode("', '", $order_products_id)."')");
        $application->db->getDB_Result($query);

        modApiFunc("PromoCodes", "DeleteOrders", $ordersId);
        modApiFunc("TaxExempts", "DeleteOrders", $ordersId);
        modApiFunc('GiftCertificateApi', 'DeleteOrders', $ordersId);

        $query = new DB_Delete('orders');
        $query->WhereField($o['id'], DB_IN, $DB_IN_string);
        $application->db->getDB_Result($query);
    }

    /**
     * The former log_decrypted_credit_card_info_review.
     * The order, that includes Credit Card Info. For now, log is included to
     * the order info. No special log exists for the credit card now.
     */
    function log_decrypted_credit_card_info_review($order_id)
    {
        /**
         * Get info about the current viewing customer.
         */

        /**
         * IP address.
         * It is taken from users_api.php
         */
        $ip = $_SERVER['REMOTE_ADDR'];

        //Name
        $admin_info = modApiFunc("Users", "getUserInfo", modApiFunc("Users", "getCurrentUserID"));
        $admin_full_name = $admin_info['firstname'] . " " .
                           $admin_info['lastname'];

        //Time, Date
        $date_time = modApiFunc("Localization", "timestamp_date_format", time())
              . " " .modApiFunc("Localization", "timestamp_time_format", time());

        global $application;
        $MessageResources = &$application->getInstance('MessageResources',"payment-module-offline-messages", "AdminZone");

        $order_history_msg = $MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_LOG_MSG_001') . " " . $admin_full_name . "\n"
                           . $date_time . "\n"
                           . $MessageResources->getMessage('MODULE_PAYMENT_OFFLINE_CC_MSG_LOG_MSG_002') . " " . $ip;

        modApiFunc("Checkout", "addOrderHistory", $order_id, $order_history_msg);
    }

    /**
     * The order, that includes Credit Card Info. For now, log is included to
     * the order info. No special log exists for the credit card now.
     */
    function log_person_info_removal($order_id, $person_info_variant_id)
    {
        /**
         * Get info about the current deleting customer.
         */

        /**
         * IP address.
         * It is taken from users_api.php
         */
        $ip = $_SERVER['REMOTE_ADDR'];

        //Name
        $admin_info = modApiFunc("Users", "getUserInfo", modApiFunc("Users", "getCurrentUserID"));
        $admin_full_name = $admin_info['firstname'] . " " .
                           $admin_info['lastname'];

        //Time, Date
        $date_time = modApiFunc("Localization", "timestamp_date_format", time())
              . " " .modApiFunc("Localization", "timestamp_time_format", time());

        global $application;
        $MessageResources = &$application->getInstance('MessageResources');

        //Get Person Info name
        $person_variant_info = $this->getPersonVariantInfo($person_info_variant_id);
        $order_history_msg = $person_variant_info["type_visible_name"]
                           . " "
                           . $MessageResources->getMessage('MODULE_CHECKOUT_MSG_LOG_MSG_003') . " " . $admin_full_name . "\n"
                           . $date_time . "\n"
                           . $MessageResources->getMessage('MODULE_CHECKOUT_MSG_LOG_MSG_002') . " " . $ip;

        modApiFunc("Checkout", "addOrderHistory", $order_id, $order_history_msg);
    }

   /**
    * The former Action 'PurgeCVVFromStoredCreditCardInfo'.
    *
    * Note! This function shouldn't output anything and shouldn't
    * call exit(), because it is called in the special Action.
    *
    * It repalces the value CVV for this credit card in the database
    * with the string "**purged**".
    * Te following values come from the form:
    *     order_id
    *     person_info_variant_id (just in case, if it is
    *         addmissible to save more than one credit card).
    * The following values are defined by them automatically:
    *     person_attribute_id (for CVV is 14)
    *     encrypted_secret_key
    *
    * The encrypted_secret_key couldn't be used at all, just to
    * generate a new blowfish key and to encrypt it.
    * But then the decryption would take much time (twice as much),
    * as it would have to call RSA decryption for two blowfish keys
    * instead of one.
    */
    function PurgeCVVFromStoredCreditCardInfo($order_id, $person_info_variant_id, $rsa_private_key)
    {
        global $application;
        //The code is not correct. Clear the value CVV.
        //It would be better to search the id by the tag in the table person_attributes.
        $perston_attribute_id = 14;

        //Gets from the base encrypted_secret_key
        //You have to get the whole order for it.
        $order_info = modApiFunc('Checkout', 'getOrderInfo', $order_id, modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $order_id), true);
        //Encrypted data should be less in volume, than not encrypted ones.
        foreach($order_info as $key => $info_block)
        {
            if(is_array($info_block))
            {
                if(isset($info_block['person_info_variant_id']))
                {
                    //Person Info
                    if($info_block['person_info_variant_id'] == $person_info_variant_id)
                    {
                        //Creditcard info
                        foreach($info_block['attr'] as $attr)
                        {
                            if($attr['person_attribute_id'] == $perston_attribute_id)
                            {
                                //CVV
                                //The required encrypted blowfish key:
                                $encrypted_secret_key = $attr['encrypted_secret_key'];
                                $old_rsa_public_key_asc_format = $attr['rsa_public_key_asc_format'];

                                /*
                                 If the loaded Private key and the Public key stored
                                 in the database for this attribute don't match - output an
                                 error message. Do not rewrite anything in the dtatabase.
                                 */
                                $old_rsa_public_key_cryptrsa_format = modApiFunc("Crypto", "convert_rsa_public_key_from_asc_into_cryptrsa_format", $old_rsa_public_key_asc_format);
                                if(modApiFunc("Crypto","rsa_do_public_key_match_private_key", $old_rsa_public_key_cryptrsa_format, $rsa_private_key) === true)
                                {
                                    //Decrypt it and rewrite CVV with the message "**purged**":
                                    $rsa_obj = new Crypt_RSA;
                                    $decrypted_secret_key = $rsa_obj->decrypt($encrypted_secret_key, $rsa_private_key);

                                    $MessageResources = &$application->getInstance('MessageResources');
                                    $cvv_purged__msg = $MessageResources->getMessage("CHECKOUT_ORDER_INFO_CVV_PURGED_MSG");
                                    $new_value = $cvv_purged__msg;
                                    $new_value_encrypted =  base64_encode(modApiFunc("Crypto", "blowfish_encrypt", $new_value, $decrypted_secret_key));

                                    $rsa_public_key_asc_format = $old_rsa_public_key_asc_format;
                                    //Save both obfuscated and encrypted value to the database
                                    modApiFunc('Checkout', 'updateOrderPersonDataAttribute', $order_id, $person_info_variant_id, $perston_attribute_id, false, $encrypted_secret_key, $rsa_public_key_asc_format, $new_value);

                                    modApiFunc('Checkout', 'updateOrderPersonDataAttribute', $order_id, $person_info_variant_id, $perston_attribute_id, true,  $encrypted_secret_key, $rsa_public_key_asc_format, $new_value_encrypted);
                                }
                                else
                                {
                                    //              :                                  .
                                    //Output an error: the keys don't match.
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Deletes data of specified variant_info from the order.
     */
    function removePersonInfoOrderData($order_id, $person_info_variant_id)
    {
        global $application;
        $tables = $this->getTables();
        $opd = $tables['order_person_data']['columns'];
        $query = new DB_Delete("order_person_data");
        $query->WhereValue($opd["order_id"], DB_EQ, (int)$order_id);
        $query->WhereAnd();
        $query->WhereValue($opd["variant_id"], DB_EQ, (int)$person_info_variant_id);
        $application->db->getDB_Result($query);
    }

    ##########
    ##########

    //                       Auto_Increment      order_id
    // orders.
    function setNextOrderId($new_id)
    {
        global $application;

        $query = "ALTER TABLE " . $application->db->table_prefix . "orders AUTO_INCREMENT = " . $new_id;
        $result = $application->db->DB_Query($query);
    }
}
?>