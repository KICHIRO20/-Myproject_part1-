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
class SELECT_PERSON_INFO_TYPE_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Checkout::getTables();
        $columns = $tables['person_info_types']['columns'];

        $this->addSelectField($columns['id'], 'id');
        $this->addSelectField($columns['active'], 'active');
        $this->addSelectField($columns['tag'], 'tag');
    }
}

class SELECT_VALIDATED_DATA_ALL_VISIBLE_LABELS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Checkout::getTables();
        $p = $tables['person_info_variants_to_attributes']['columns'];

        $this->addSelectField($p['variant_id']);
        $this->addSelectField($p['attribute_id']);

        $this->setMultiLangAlias('_ml_name', 'person_info_variants_to_attributes', $p['name'], $p['id'], 'Checkout');
        $this->addSelectField($this->getMultiLangAlias('_ml_name'), 'attribute_visible_name');

        $this->setMultiLangAlias('_ml_descr', 'person_info_variants_to_attributes', $p['descr'], $p['id'], 'Checkout');
        $this->addSelectField($this->getMultiLangAlias('_ml_descr'), 'attribute_description');
    }
}

class SELECT_VALIDATED_DATA_VISIBLE_LABELS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Checkout::getTables();
        $p = $tables['person_info_variants_to_attributes']['columns'];

        $this->setMultiLangAlias('_ml_name', 'person_info_variants_to_attributes', $p['name'], $p['id'], 'Checkout');
        $this->addSelectField($this->getMultiLangAlias('_ml_name'), 'attribute_visible_name');

        $this->setMultiLangAlias('_ml_descr', 'person_info_variants_to_attributes', $p['descr'], $p['id'], 'Checkout');
        $this->addSelectField($this->getMultiLangAlias('_ml_descr'), 'attribute_description');

        $this->WhereValue($p['variant_id'], DB_EQ, $params['vid']);
        $this->WhereAnd();
        $this->WhereValue($p['attribute_id'], DB_EQ, $params['aid']);
    }
}

class SELECT_VALIDATED_DATA_STRUCTURE extends DB_Select
{
    function initQuery($params)
    {
        $person_info_variant_tag = $params['person_info_variant_tag'];
        $prerequisite_name = $params['prerequisite_name'];
        // $params['PrerequisitesValidationResults'] -

        $tables = Checkout::getTables();
        $piv  = $tables['person_info_variants']['columns'];
        $pivta = $tables['person_info_variants_to_attributes']['columns'];
        $pa  = $tables['person_attributes']['columns'];
        $pit = $tables['person_info_types']['columns'];

        $this->addSelectField($pa['id'], 'id');
        $this->addSelectField($pa['tag'], 'view_tag');
        $this->addSelectField($pa['pattern_id'], 'pattern_id');
        $this->addSelectField($pa['input_type_id'], 'input_type_id');
        $this->addSelectField($pa['input_validation_func_name'], 'input_validation_func_name');
        $this->addSelectField($pa['min'], 'min');
        $this->addSelectField($pa['max'], 'max');
        $this->addSelectField($pa['size'], 'size');

        $this->addSelectField($pivta['required'], 'attribute_required');
        $this->addLeftJoin('person_info_variants_to_attributes', $pivta['variant_id'], DB_EQ, $piv['id']);
        $this->addLeftJoin('person_attributes', $pa['id'], DB_EQ, $pivta['attribute_id']);

        $this->setMultiLangAlias('_ml_name', 'person_info_variants_to_attributes', $pivta['name'], $pivta['id'], 'Checkout');
        $this->addSelectField($this->getMultiLangAlias('_ml_name'), 'attribute_visible_name');

        $this->setMultiLangAlias('_ml_descr', 'person_info_variants_to_attributes', $pivta['descr'], $pivta['id'], 'Checkout');
        $this->addSelectField($this->getMultiLangAlias('_ml_descr'), 'attribute_description');

        if(!empty($person_info_variant_tag))
        {
           /* The ID of the required variant was passed as a function parameter,
              e.g. by the payment module request: "Paypal Pro billing info"
            */
            $this->WhereValue($piv['tag'], DB_EQ, $person_info_variant_tag);
            $this->WhereAnd();
            $this->WhereValue($pit['tag'], DB_EQ, $prerequisite_name);
            $this->addLeftJoin('person_info_types', $pit['id'], DB_EQ, $piv['type_id']);
        }
        else
        if(isset($params['PrerequisitesValidationResults']))
        {
            $this->WhereValue($piv['tag'], DB_EQ, $params['PrerequisitesValidationResults']);
            $this->WhereAnd();
            $this->WhereValue($pit['tag'], DB_EQ, $prerequisite_name);
            $this->addLeftJoin('person_info_types', $pit['id'], DB_EQ, $piv['type_id']);
        }
        else
        {
            //At the first initialization. The variant is by default.
            $this->WhereValue($piv['tag'], DB_EQ, "default");
            $this->WhereAnd();
            $this->WhereValue($pit['tag'], DB_EQ, $prerequisite_name);
            $this->addLeftJoin('person_info_types', $pit['id'], DB_EQ, $piv['type_id']);
        }
        $this->WhereAnd();
        $this->WhereValue($pivta['visible'], DB_EQ, 1);
        $this->SelectOrder($pivta['sort']);
    }
}

class SELECT_PREREQUISITES_VALIDATION_RESULTS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Checkout::getTables();
        $pit  = $tables['person_info_types']['columns'];
        $piv  = $tables['person_info_variants']['columns'];
        /* a tag, which is selected by default */
        $variant_tag  = "default";

        $this->addSelectField($piv['id'], 'variant_id');
        $this->addSelectField($piv['tag'], 'variant_tag');
        $this->addSelectField($piv['visible_name'], 'visible_name');
        $this->addSelectField($piv['tag'], 'variant_tagvisible_name');
        $this->addSelectField($pit['tag'], 'view_tag');
        $this->addSelectField($pit['id'], 'type_id');
        $this->Where($piv['type_id'], DB_EQ, $pit['id']);
        $this->WhereAnd();
        $this->Where($piv['tag'], DB_EQ, '"'. $variant_tag . '"');
    }
}

class SELECT_PERSON_VARIANT_INFO extends DB_Select
{
    function initQuery($params)
    {
        $variant_id = $params['variant_id'];

        $tables = Checkout::getTables();
        $piv = $tables['person_info_variants']['columns'];
        $pit = $tables['person_info_types']['columns'];

        $this->addSelectField($piv['tag'], 'variant_tag');
        $this->addSelectField($piv['visible_name'], 'visible_name');
        $this->addSelectField($pit['tag'], 'variant_view_tag');
        $this->addSelectField($pit['id'], 'type_id');
        $this->addSelectField($pit['visible_name'], 'type_visible_name');
        $this->Where($piv['id'], DB_EQ, $variant_id);
        $this->WhereAnd();
        $this->Where($piv['type_id'], DB_EQ, $pit['id']);
    }
}

class SELECT_ACTIVE_PM_SM_MODULES extends DB_Select
{
    function initQuery($params)
    {
        $modulesType = $params['modulesType'];

        $tables = Checkout::getTables();
        $columns = $tables['checkout_pm_sm_settings']['columns'];

        $this->addSelectField($columns["module_id"], "module_id");
        $this->addSelectField($columns["module_class_name"], "module_class_name");
        $this->addSelectField($columns["status_active_value_id"], "status_active_value_id");
        $this->addSelectField($columns["status_selected_value_id"], "status_selected_value_id");
        $this->addSelectField($columns["sort_order"], "sort_order");
        $this->WhereValue($columns["status_active_value_id"], DB_EQ, 1);
        $this->WhereAnd();
        $this->WhereValue($columns["module_group"], DB_LIKE, "%".$modulesType);
    }
}

class SELECT_SELECTED_PM_SM_MODULES extends DB_Select
{
    function initQuery($params)
    {
        $modulesType = $params['modulesType'];

        $tables = Checkout::getTables();
        $columns = $tables['checkout_pm_sm_settings']['columns'];

        $this->addSelectField($columns["module_id"], "module_id");
        $this->addSelectField($columns["module_class_name"], "module_class_name");
        $this->addSelectField($columns["status_active_value_id"], "status_active_value_id");
        $this->addSelectField($columns["status_selected_value_id"], "status_selected_value_id");
        $this->addSelectField($columns["sort_order"], "sort_order");
        $this->WhereValue($columns["status_selected_value_id"], DB_EQ, 1);
        $this->WhereAnd();
        $this->WhereValue($columns["module_group"], DB_LIKE, "%".$modulesType);
    }
}

class SELECT_PM_SM_ACCEPTED_CURRENCIES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Checkout::getTables();
        $columns = $tables['checkout_pm_sm_accepted_currencies']['columns'];

        $this->addSelectField($columns["module_id"], "module_id");
        $this->addSelectField($columns["currency_code"], "currency_code");
        $this->addSelectField($columns["currency_status"], "currency_status");
    }
}

class SELECT_PM_SM_CURRENCY_ACCEPTANCE_RULES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Checkout::getTables();
        $columns = $tables['checkout_pm_sm_currency_acceptance_rules']['columns'];

        $this->addSelectField($columns["module_id"], "module_id");
        $this->addSelectField($columns["rule_name"], "rule_name");
        $this->addSelectField($columns["rule_selected"], "rule_selected");
    }
}

class SELECT_PM_SM_REQUIRED_CURRENCIES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Checkout::getTables();
        $settings = $tables['checkout_pm_sm_settings']['columns'];
        $currencies = $tables['checkout_pm_sm_accepted_currencies']['columns'];

        $this->addSelectField($settings["module_id"], "module_id");
        $this->addSelectField($settings["module_class_name"], "module_class_name");
        $this->addSelectField($currencies["currency_code"], "currency_code");

        $this->WhereValue($settings["status_active_value_id"], DB_EQ, 1);
        $this->WhereAnd();
        $this->Where($settings["module_id"], DB_EQ, $currencies['module_id']);
        $this->WhereAnd();
        $this->WhereValue($currencies["currency_status"], DB_NEQ, 'NOT_ACCEPTED');
    }
}

class SELECT_NUMBER_OR_ORDERS_BY_ID extends DB_Select
{
    function initQuery($params)
    {
        $order_id = $params['order_id'];

        $tables = Checkout::getTables();
        $o = $tables['orders']['columns'];

        $this->addSelectField($this->fCount($o['id']), 'count_id');
        $this->WhereValue($o['id'], DB_EQ, $order_id);
    }
}

class SELECT_ORDER_STATUS_LIST extends DB_Select
{
    function initQuery($params)
    {
        // @ sort order
        $s_id = $params['s_id'];

        $tables = Checkout::getTables();
        $s = $tables['order_status']['columns'];

        $this->addSelectField($s['id'], 'id');
        $this->addSelectField($s['name'], 'name');
        $this->addSelectField($s['descr'], 'descr');
        if ($s_id)
        {
            $this->WhereValue($s['id'], DB_EQ, $s_id);
        }
    }
}

class SELECT_ORDER_PAYMENT_STATUS_LIST extends DB_Select
{
    function initQuery($params)
    {
        // @ sort order
        $ps_id = $params['ps_id'];

        $tables = Checkout::getTables();
        $ps = $tables['order_payment_status']['columns'];

        $this->addSelectField($ps['id'], 'id');
        $this->addSelectField($ps['name'], 'name');
        $this->addSelectField($ps['descr'], 'descr');
        if ($ps_id)
        {
            $this->WhereValue($ps['id'], DB_EQ, $ps_id);
        }
    }
}

class SELECT_BASE_ORDER_INFO extends DB_Select
{
    function initQuery($params)
    {
        $order_id = $params['order_id'];

        $tables = Checkout::getTables();
        $o = $tables['orders']['columns'];
        $s = $tables['order_status']['columns'];
        $ps = $tables['order_payment_status']['columns'];

        $this->addSelectTable('order_status');
        $this->addSelectTable('order_payment_status');
        $this->addSelectField($o['id'], 'id');
        $this->addSelectField($o['date'], 'date');
        $this->addSelectField($o['status_id'], 'status_id');
        $this->addSelectField($o['person_id'], 'person_id');
        $this->addSelectField($o['payment_status_id'], 'payment_status_id');
        $this->addSelectField($o['payment_method'], 'payment_method');
        $this->addSelectField($o['payment_module_id'], 'payment_module_id');
        $this->addSelectField($o['payment_method_detail'], 'payment_method_detail');
        $this->addSelectField($o['payment_processor_order_id'], 'payment_processor_order_id');
        $this->addSelectField($o['shipping_method'], 'shipping_method');
        $this->addSelectField($o['track_id'], 'track_id');
        $this->addSelectField($o['affiliate_id'], 'affiliate_id');
        $this->addSelectField($o['included_tax'], 'included_tax');
        $this->addSelectField($o['new_type'], 'new_type');
        # order status info
        $this->addSelectField($s['name'], 'status');
        $this->addLeftJoin('order_status', $o['status_id'], DB_EQ, $s['id']);
        # order payment status info
        $this->addSelectField($ps['name'], 'payment_status');
        $this->addLeftJoin('order_payment_status', $o['payment_status_id'], DB_EQ, $ps['id']);
        # select only one order
        $this->Where($o['id'], DB_EQ, $order_id);
    }
}

class SELECT_ORDER_PRICES extends DB_Select
{
    function initQuery($params)
    {
        $order_id = $params['order_id'];
		$currency_code = $params['currency_code'];

        $tables = Checkout::getTables();
        $op = $tables['order_prices']['columns'];

        $this->addSelectField($op['order_total']                     , 'order_total');
        $this->addSelectField($op['order_subtotal']                  , 'order_subtotal');
        $this->addSelectField($op['order_total_to_pay']              , 'order_total_to_pay');
        $this->addSelectField($op['order_total_paid_by_gc']          , 'order_total_paid_by_gc');
        $this->addSelectField($op['minimum_shipping_cost']           , 'minimum_shipping_cost');
        $this->addSelectField($op['free_shipping_for_orders_over']   , 'free_shipping_for_orders_over');
        $this->addSelectField($op['free_handling_for_orders_over']   , 'free_handling_for_orders_over');
        $this->addSelectField($op['per_item_shipping_cost_sum']      , 'per_item_shipping_cost_sum');
        $this->addSelectField($op['per_order_shipping_fee']          , 'per_order_shipping_fee');
        $this->addSelectField($op['shipping_method_cost']            , 'shipping_method_cost');
        $this->addSelectField($op['total_shipping_charge']           , 'total_shipping_charge');
        $this->addSelectField($op['per_item_handling_cost_sum']      , 'per_item_handling_cost_sum');
        $this->addSelectField($op['per_order_handling_fee']          , 'per_order_handling_fee');
        $this->addSelectField($op['total_handling_charge']           , 'total_handling_charge');
        $this->addSelectField($op['total_shipping_and_handling_cost'], 'total_shipping_and_handling_cost');
        $this->addSelectField($op['order_tax_total']                 , 'order_tax_total');
        $this->addSelectField($op['subtotal_global_discount']        , 'subtotal_global_discount');
        $this->addSelectField($op['subtotal_promo_code_discount']    , 'subtotal_promo_code_discount');
        $this->addSelectField($op['quantity_discount']               , 'quantity_discount');
        $this->addSelectField($op['discounted_subtotal']             , 'discounted_subtotal');
        $this->addSelectField($op['order_not_included_tax_total']    , 'order_not_included_tax_total');
        $this->Where($op['order_id'], DB_EQ, $order_id);
        $this->WhereAnd();
        $this->WhereValue($op['currency_code'], DB_EQ, $currency_code);
    }
}

class SELECT_ORDER_TAXES extends DB_Select
{
    function initQuery($params)
    {
        $order_id = $params['order_id'];
        $currency_code = $params['currency_code'];

        $tables = Checkout::getTables();
        $otx = $tables['order_taxes']['columns'];

        $this->addSelectField($otx['id'], 'id');
        $this->addSelectField($otx['type'], 'type');
        $this->addSelectField($otx['value'], 'value');
        $this->addSelectField($otx['is_included'], "is_included");
        $this->Where($otx['order_id'], DB_EQ, $order_id);
        $this->WhereAnd();
        $this->WhereValue($otx['currency_code'], DB_EQ, $currency_code);
    }
}

class SELECT_ORDER_TAX_DISPLAY_OPTIONS extends DB_Select
{
    function initQuery($params)
    {
        $order_id = $params['order_id'];
        $currency_code = $params['currency_code'];

        $tables = Checkout::getTables();
        $otx = $tables['order_tax_display_options']['columns'];

        $this->addSelectField($otx['id'], 'id');
        $this->addSelectField($otx['name'], 'name');
        $this->addSelectField($otx['value'], 'value');
        $this->addSelectField($otx['formula'], "formula");
//        $this->addSelectField($otx['currency_code'], "currency_code");
        $this->Where($otx['order_id'], DB_EQ, $order_id);
        $this->WhereAnd();
        $this->WhereValue($otx['currency_code'], DB_EQ, $currency_code);
    }
}

class SELECT_ORDER_BILLING_SHIPPING_INFO extends DB_Select
{
    function initQuery($params)
    {
        $order_id = $params['order_id'];
        $b_encrypted = $params['b_encrypted'];

        $tables = Checkout::getTables();
        $opd = $tables['order_person_data']['columns'];
        $pit = $tables['person_info_types']['columns'];
        $piv = $tables['person_info_variants']['columns'];
        $pa  = $tables['person_attributes']['columns'];

        $this->addSelectField($opd['id'], 'id');
        $this->addSelectField($opd['variant_id'], 'person_info_variant_id');
        $this->addSelectField($opd['attribute_id'], 'person_attribute_id');
        $this->addSelectField($opd['name'], 'name');
        $this->addSelectField($opd['value'], 'value');
        $this->addSelectField($opd['desc'], 'descr');
        $this->addSelectField($opd['b_encrypted'], 'b_encrypted');
        $this->addSelectField($opd['encrypted_secret_key'], 'encrypted_secret_key');
        $this->addSelectField($opd['rsa_public_key_asc_format'], 'rsa_public_key_asc_format');
        # info type information
        $this->addSelectField($pit['id'], 'pit_id');
        $this->addSelectField($pit['tag'], 'pit_tag');
        $this->addSelectField($pit['name'], 'pit_name');
        $this->addSelectField($pit['descr'], 'pit_descr');
        $this->addLeftJoin('person_info_variants', $opd['variant_id'], DB_EQ, $piv['id']);
        # attribute info for the info type
        $this->addSelectField($pa['tag'], 'pa_tag');
        $this->addSelectField($pa['input_type_id'], 'pa_input_type_id');
        $this->addLeftJoin('person_attributes', $opd['attribute_id'], DB_EQ, $pa['id']);
        //Variant type is the second key in the variant table person info
        $this->addSelectField($piv['tag'], 'piv_tag');

        $this->Where($pit['id'], DB_EQ, $piv['type_id']);
        $this->WhereAnd();
        # for one order only
        $this->Where($opd['order_id'], DB_EQ, $order_id);
        $this->WhereAnd();
        # either only encrypted order info, or only not decrypted info
        $this->Where($opd['b_encrypted'], DB_EQ, $b_encrypted === true ? "1" : "0");
    }
}

class SELECT_ORDER_PRODUCTS extends DB_Select
{
    function initQuery($params)
    {
        $order_id = $params['order_id'];

        $tables = Checkout::getTables();
        $opr = $tables['order_product']['columns'];

        $this->addSelectField($opr['id'], 'id');
        $this->addSelectField($opr['name'], 'name');
        $this->addSelectField($opr['type'], 'type');
        $this->addSelectField($opr['qty'], 'qty');
        $this->addSelectField($opr['store_id'], 'store_id');
        $this->addSelectField($opr['inventory_id'], 'inventory_id');
        $this->Where($opr['order_id'], DB_EQ, $order_id);
    }
}

class SELECT_ORDER_PRODUCT_ATTRIBUTES extends DB_Select
{
    function initQuery($params)
    {
        $order_product_id = $params['order_product_id'];
		$currency_code = $params['currency_code'];


        $tables = Checkout::getTables();
        $opta = $tables['order_product_to_attributes']['columns'];

        $catalog_tables =  modApiStaticFunc('Catalog','getTables'); //Catalog::getTables();
        $a = $catalog_tables['attributes']['columns'];

        $this->addSelectField($opta['value'], 'value');
        # real attribute info
        $this->addSelectField($a['view_tag'], 'a_tag');
        $this->addSelectField($a['name'], 'a_name');
        $this->addLeftJoin('attributes', $opta['attribute_id'], DB_EQ, $a['id']);
        $this->Where($opta['product_id'], DB_EQ, $order_product_id);
		$this->WhereAnd();
        $this->WhereValue($opta['currency_code'], DB_EQ, $currency_code);
    }
}

class SELECT_ORDER_PRODUCT_OPTIONS extends DB_Select
{
    function initQuery($params)
    {
        $order_product_id = $params['order_product_id'];
		$currency_code = $params['currency_code'];

        $tables = Checkout::getTables();
        $opot = $tables['order_product_options']['columns'];

        $this->addSelectField($opot['product_option_id']);
        $this->addSelectField($opot['option_name']);
        $this->addSelectField($opot['option_value']);
        $this->addSelectField($opot['is_file']);
        $this->WhereValue($opot['order_product_id'], DB_EQ, $order_product_id );
    }
}

class SELECT_ORDER_PRODUCT_CUSTOM_ATTRIBUTES extends DB_Select
{
    function initQuery($params)
    {
        $order_product_id = $params['order_product_id'];

        $tables = Checkout::getTables();
        $opca = $tables['order_product_custom_attributes']['columns'];

        $this->addSelectField($opca['tag'], 'tag');
        $this->addSelectField($opca['name'], 'name');
        $this->addSelectField($opca['value'], 'value');
        $this->WhereValue($opca['product_id'], DB_EQ, $order_product_id);
    }
}

class SELECT_ORDER_NOTES extends DB_Select
{
    function initQuery($params)
    {
        $order_id = $params['order_id'];

        $tables = Checkout::getTables();
        $on = $tables['order_notes']['columns'];

        $this->addSelectField($on['type'], 'type');
        $this->addSelectField($on['date'], 'date');
        $this->addSelectField($on['content'], 'content');
        $this->Where($on['order_id'], DB_EQ, $order_id);
        $this->SelectOrder($on['date'], 'DESC');
        $this->SelectOrder($on['microtime'], 'DESC');
    }
}

class SELECT_PERSON_INFO_VARIANT_ID extends DB_Select
{
    function initQuery($params)
    {
        $person_info_variant_tag = $params['person_info_variant_tag'];
        $person_info_type_tag = $params['person_info_type_tag'];

        $tables = Checkout::getTables();
        $piv = $tables['person_info_variants']['columns'];
        $pit = $tables['person_info_types']['columns'];

        $this->addSelectField($piv['id'], 'id');
        $this->WhereValue($piv['tag'], DB_EQ, $person_info_variant_tag);
        $this->WhereAnd();
        $this->WhereValue($pit['tag'], DB_EQ, $person_info_type_tag);
        $this->addLeftJoin('person_info_types', $pit['id'], DB_EQ, $piv['type_id']);
    }
}

class SELECT_CUSTOMER_ATTRIBUTE_VALUE extends DB_Select
{
    function initQuery($params)
    {
        $person_info_type_tag = $params['person_info_type_tag'];
        $person_info_variant_tag = $params['person_info_variant_tag'];
        $person_attribute = $params['person_attribute'];
        $order_id = $params['order_id'];

        $tables = Checkout::getTables();
        $opd = $tables['order_person_data']['columns'];
        $pa  = $tables['person_attributes']['columns'];
        $piv = $tables['person_info_variants']['columns'];
        $pit = $tables['person_info_types']['columns'];

        $this->addSelectField($opd["value"], "value");
        $this->addLeftJoin('person_attributes', $opd['attribute_id'], DB_EQ, $pa['id']);
        $this->addLeftJoin('person_info_variants', $opd['variant_id'], DB_EQ, $piv['id']);
        $this->addLeftJoin('person_info_types', $piv['type_id'], DB_EQ, $pit['id']);
        $this->WhereValue($pit['tag'], DB_EQ, $person_info_type_tag);
        $this->WhereAND();
        $this->WhereValue($piv['tag'], DB_EQ, $person_info_variant_tag);
        $this->WhereAND();
        $this->WhereValue($pa['tag'], DB_EQ, $person_attribute);
        $this->WhereAND();
        $this->WhereValue($opd['order_id'], DB_EQ, $order_id);
    }
}

class SELECT_ORDER_COUNT_BY_STATUS_ID extends DB_Select
{
    function initQuery($params)
    {
        $status_id = $params['status_id'];

        $tables = Checkout::getTables();
        $o = $tables['orders']['columns'];

        $this->addSelectField("count(".$o['id'].")", 'count');
        if ($status_id != 0)
        {
            $this->Where($o['status_id'], DB_EQ, $status_id);
        }
    }
}


class SELECT_ORDER_CURRENCY_LIST_BY_ORDER_ID extends DB_Select
{
    function initQuery($params)
    {
        $id = $params['order_id'];

        $tables = Checkout::getTables();
        $op = $tables['order_prices']['columns'];

        $this->addSelectField($op["currency_code"], "currency_code");
        $this->addSelectField($op["currency_type"], "currency_type");
        $this->WhereValue($op['order_id'], DB_EQ, $id);
    }
}

class SELECT_INPUT_TYPE_DATA extends DB_Select
{
    function initQuery($params)
    {
        $id = $params['it_id'];

        $tables = Catalog::getTables();
        $it = $tables['input_types']['columns'];
        $itv = $tables['input_type_values']['columns'];

        foreach($it as $k => $v)
            $this->addSelectField($v);
        foreach($itv as $k => $v)
            if ($k != 'value')
            {
                $this->addSelectField($v);
            }
            else
            {
                $this->setMultiLangAlias('_ml_value', 'input_type_values', $itv['value'], $itv['id'], 'Catalog');
                $this->addSelectValue($this->getMultiLangAlias('_ml_value'), 'input_type_value');
            }
        $this->WhereValue($it['id'], DB_EQ, $id);
        $this->WhereAND();
        $this->WhereField($itv['it_id'], DB_EQ, $it['id']);
        $this->SelectOrder($itv['id'], 'ASC');
    }
}


//class SELECT_ORDER_COUNT_BY_STATUS_ID extends DB_Select
//{
//    function initQuery($params)
//    {
//        $tables = Checkout::getTables();
//    }
//}



class INSERT_ORDER_PERSON_ATTRIBUTE extends DB_Insert
{
    function INSERT_ORDER_PERSON_ATTRIBUTE()
    {
        parent::DB_Insert('order_person_data');
    }

    function initQuery($params)
    {
        $order_id                   = $params['order_id'];
        $person_info_variant_id     = $params['person_info_variant_id'];
        $attribute_id               = $params['attribute_id'];
        $attribute_visible_name     = $params['attribute_visible_name'];
        $attribute_value            = $params['attribute_value'];
        $attribute_description      = $params['attribute_description'];
        $b_encrypted                = $params['b_encrypted'];
        $encrypted_secret_key       = $params['encrypted_secret_key'];
        $rsa_public_key_asc_format  = $params['rsa_public_key_asc_format'];

        $tables = Checkout::getTables();
        $ptiv = $tables["person_to_info_variants"]['columns'];
        $opd = $tables["order_person_data"]['columns'];

        //  add to the table order_person_data
        $this->addInsertValue($order_id,                    $opd["order_id"]);
        // what is the id (becomes a type_id in the DB)? Into the prerequisite
        $this->addInsertValue($person_info_variant_id,      $opd["variant_id"]);
        $this->addInsertValue($attribute_id,                $opd["attribute_id"]);
        $this->addInsertValue($attribute_visible_name,      $opd["name"]);
        $this->addInsertValue($attribute_value,             $opd["value"]);
        $this->addInsertValue($attribute_description,       $opd["desc"]);
        $this->addInsertValue($b_encrypted,                 $opd["b_encrypted"]);
        $this->addInsertValue($encrypted_secret_key,        $opd["encrypted_secret_key"]);
        $this->addInsertValue($rsa_public_key_asc_format,   $opd["rsa_public_key_asc_format"]);
    }
}

class INSERT_PERSON_INFO_VARIANTS extends DB_Insert
{
    function INSERT_PERSON_INFO_VARIANTS()
    {
        parent::DB_Insert('person_info_variants');
    }

    function initQuery($params)
    {
        $tables = Checkout::getTables();
        $columns = $tables['person_info_variants']['columns'];

        $this->addInsertValue($params['person_info_type_id'],                 $columns['type_id']);
        $this->addInsertValue($params['person_info_variant_tag'],             $columns['tag']);
        $this->addInsertValue($params['person_info_variant_name'],            $columns['name']);
        $this->addInsertValue($params['person_info_variant_description'],     $columns['descr']);
        $this->addInsertValue($params['person_info_variant_visible_name'],    $columns['visible_name']);
    }
}

class INSERT_PERSON_INFO_VARIANTS_ATTRIBUTES extends DB_Insert
{
    function INSERT_PERSON_INFO_VARIANTS_ATTRIBUTES()
    {
        parent::DB_Insert('person_info_variants_to_attributes');
    }

    function initQuery($params)
    {
        $inserted_id = $params['inserted_id'];
        $attribute = $params['attribute'];

        $tables = Checkout::getTables();
        $columns = $tables['person_info_variants_to_attributes']['columns'];

        $this->addInsertValue($inserted_id, $columns['variant_id']);
        $this->addInsertValue($attribute['person_attribute_id'], $columns['attribute_id']);
        $this->addMultiLangInsertValue($attribute['person_attribute_visible_name'], $columns['name'], $columns['id'], 'Checkout');
        $this->addMultiLangInsertValue($attribute['person_attribute_description'], $columns['descr'], $columns['id'], 'Checkout');
        $this->addInsertValue($attribute['person_attribute_unremovable'], $columns['unremovable']);
        $this->addInsertValue($attribute['person_attribute_visible'], $columns['visible']);
        $this->addInsertValue($attribute['person_attribute_required'], $columns['required']);
        $this->addInsertValue($attribute['person_attribute_sort_order'], $columns['sort']);
        $this->addInsertValue('', $columns['field_params']);
    }
}

class INSERT_ORDER_PRODUCTS extends DB_Insert
{
    function INSERT_ORDER_PRODUCTS()
    {
        parent::DB_Insert('order_product');
    }

    function initQuery($params)
    {
        $order_id           = $params['order_id'];
        $Quantity_In_Cart   = $params['Quantity_In_Cart'];
        $ProductName        = $params['ProductName'];
        $ProductTypeID      = $params['ProductTypeID'];
        $ProductID          = $params['ProductID'];
        $inventory_id       = $params['inventory_id'];

        $tables = Checkout::getTables();
        $columns = $tables['order_product']['columns'];

        $this->addInsertValue($order_id,        $columns['order_id']);
        $this->addInsertValue($Quantity_In_Cart, $columns['qty']);
        $this->addInsertValue($ProductName,     $columns['name']);
        $this->addInsertValue($ProductTypeID,   $columns['type']);
        $this->addInsertValue($ProductID,       $columns['store_id']);
        if($inventory_id !== NULL)
        {
            $this->addInsertValue($inventory_id,$columns['inventory_id']);
        }
    }
}

class INSERT_ORDER_PRODUCT_TO_ATTRIBUTES extends DB_Insert
{
    function INSERT_ORDER_PRODUCT_TO_ATTRIBUTES()
    {
        parent::DB_Insert('order_product_to_attributes');
    }

    function initQuery($params)
    {
        $attribute_id = $params['attribute_id'];
        $attribute_value = $params['attribute_value'];
        $inserted_order_product_id = $params['inserted_order_product_id'];
		$currency_code = $params['currency_code'];
		$currency_type = $params['currency_type'];

        $tables = Checkout::getTables();
        $columns = $tables['order_product_to_attributes']['columns'];

		$this->addInsertValue($currency_code,				$columns['currency_code']);
		$this->addInsertValue($currency_type,			$columns['currency_type']);
		$this->addInsertValue($attribute_id,                $columns['attribute_id']);
        $this->addInsertValue($inserted_order_product_id,   $columns['product_id']);
        $this->addInsertValue($attribute_value,             $columns['value']);
    }
}

class INSERT_ORDER_PRODUCT_CUSTOM_ATTRIBUTES extends DB_Insert
{
    function INSERT_ORDER_PRODUCT_CUSTOM_ATTRIBUTES()
    {
        parent::DB_Insert('order_product_custom_attributes');
    }

    function initQuery($params)
    {
        $inserted_order_product_id  = $params['inserted_order_product_id'];
        $attr_view_tag              = $params['attr_view_tag'];
        $attr_view_name             = $params['attr_view_vame'];
        $attr_view_value            = $params['attr_view_value'];

        $tables = Checkout::getTables();
        $table = 'order_product_custom_attributes';
        $columns = $tables['order_product_custom_attributes']['columns'];

        $this->addInsertValue($inserted_order_product_id, $columns['product_id']);
        $this->addInsertValue($attr_view_tag,       $columns['tag']);
        $this->addInsertValue($attr_view_name,      $columns['name']);
        $this->addInsertValue($attr_view_value,     $columns['value']);
    }
}

class INSERT_ORDER_PRODUCT_OPTIONS extends DB_Insert
{
    function INSERT_ORDER_PRODUCT_OPTIONS()
    {
        parent::DB_Insert('order_product_options');
    }

    function initQuery($params)
    {
        $inserted_order_product_id  = $params['inserted_order_product_id'];
        $oname                      = $params['oname'];
        $oval                       = $params['oval'];
//        $modifiers                  = $params['modifiers'];
        $option_data                = $params['option_data'];
//		$currency_code				= $params['currency_code'];
//		$currency_type			= $params['currency_type'];

        $tables = Checkout::getTables();
        $columns = $tables['order_product_options']['columns'];

        $this->addInsertValue($inserted_order_product_id,   $columns['order_product_id']);
        $this->addInsertValue($oname,                       $columns['option_name']);
        $this->addInsertValue($oval,                        $columns['option_value']);
//        $this->addInsertValue(serialize($modifiers),        $columns['option_modifiers']);
//        $this->addInsertValue($currency_code,				$columns['currency_code']);
//        $this->addInsertValue($currency_type,			$columns['currency_type']);

        if(is_array($option_data) and
           array_key_exists('is_file',$option_data) and
           $option_data['is_file'] == true)
        {
            $this->addInsertValue('Y',$columns['is_file']);
        }

    }
}


class UPDATE_ORDER_PRODUCT_ATTRIBUTES_BY_MODIFIERS extends DB_Update
{
    function UPDATE_ORDER_PRODUCT_ATTRIBUTES_BY_MODIFIERS()
    {
        parent::DB_Update('order_product_to_attributes');
    }

    function initQuery($params)
    {
        $inv_inf_sku = $params['inv_inf_sku'];
        $inserted_order_product_id = $params['inserted_order_product_id'];

        $tables = Checkout::getTables();
        $columns = $tables['order_product_to_attributes']['columns'];

        $this->addUpdateValue($columns['value'], $inv_inf_sku);
        $this->WhereValue($columns['attribute_id'], DB_EQ, $params['attr_info']['id']);
        $this->WhereAND();
        $this->WhereValue($columns['product_id'], DB_EQ, $inserted_order_product_id);
    }
}

class UPDATE_ORDER_PAYMENT_PROCESSOR_ORDER_ID extends DB_Update
{
    function UPDATE_ORDER_PAYMENT_PROCESSOR_ORDER_ID()
    {
        parent::DB_Update('orders');
    }

    function initQuery($params)
    {
        $new_payment_processor_order_id = $params['new_payment_processor_order_id'];
        $order_id = $params['order_id'];

        $tables = Checkout::getTables();
        $o = $tables['orders']['columns'];

        $this->addUpdateValue($o['payment_processor_order_id'], $new_payment_processor_order_id);
        $this->WhereValue($o["id"], DB_EQ, $order_id);
    }
}


class INSERT_ORDER_DATA extends DB_Insert
{
    function INSERT_ORDER_DATA()
    {
        parent::DB_Insert('orders');
    }

    function initQuery($params)
    {
        $tables = Checkout::getTables();
        $columns = $tables['orders']['columns'];

        $this->addInsertExpression($this->fNow(),                    $columns['date']);
        $this->addInsertValue($params['status_id'],                  $columns['status_id']);
        $this->addInsertValue($params['payment_status_id'],          $columns['payment_status_id']);
        $this->addInsertValue($params['payment_method'],             $columns['payment_method']);
        $this->addInsertValue($params['payment_module_id'],          $columns['payment_module_id']);
        $this->addInsertValue($params['payment_method_detail'],      $columns['payment_method_detail']);
        $this->addInsertValue($params['payment_processor_order_id'], $columns['payment_processor_order_id']);
        $this->addInsertValue($params['shipping_method'],            $columns['shipping_method']);
        $this->addInsertValue($params['track_id'],                   $columns['track_id']);
        $this->addInsertValue($params['person_id'],                  $columns['person_id']);
        $this->addInsertValue($params['affiliate_id'],               $columns['affiliate_id']);
        $this->addInsertValue($params['included_tax'],               $columns['included_tax']);
    }
}

class INSERT_ORDER_PRICES extends DB_Insert
{
    function INSERT_ORDER_PRICES()
    {
        parent::DB_Insert('order_prices');
    }

    function initQuery($params)
    {
        $order_id = $params['order_id'];
        $order_prices = $params['order_prices'];
		$currency_code = $params['currency_code'];
		$currency_type = $params['currency_type'];

        $tables = Checkout::getTables();
        $columns = $tables['order_prices']['columns'];

        $this->addInsertValue($order_id, $columns['order_id']);
        $this->addInsertValue($currency_code                                   , $columns['currency_code']);
        $this->addInsertValue($currency_type                                   , $columns['currency_type']);
        $this->addInsertValue($order_prices['OrderTotalToPay']                 , $columns['order_total_to_pay']);
        $this->addInsertValue($order_prices['OrderTotalPrepaidByGC']           , $columns['order_total_paid_by_gc']);
        $this->addInsertValue($order_prices['OrderTotal']                      , $columns['order_total']);
        $this->addInsertValue($order_prices['OrderSubtotal']                   , $columns['order_subtotal']);
        $this->addInsertValue($order_prices['MinimumShippingCost']             , $columns['minimum_shipping_cost']);
        $this->addInsertValue($order_prices['FreeShippingForOrdersOver']       , $columns['free_shipping_for_orders_over']);
        $this->addInsertValue($order_prices['FreeHandlingForOrdersOver']       , $columns['free_handling_for_orders_over']);
        $this->addInsertValue($order_prices['PerItemShippingCostSum']          , $columns['per_item_shipping_cost_sum']);
        $this->addInsertValue($order_prices['PerOrderShippingFee']             , $columns['per_order_shipping_fee']);
        $this->addInsertValue($order_prices['ShippingMethodCost']              , $columns['shipping_method_cost']);
        $this->addInsertValue($order_prices['TotalShippingCharge']             , $columns['total_shipping_charge']);
        $this->addInsertValue($order_prices['PerItemHandlingCostSum']          , $columns['per_item_handling_cost_sum']);
        $this->addInsertValue($order_prices['PerOrderHandlingFee']             , $columns['per_order_handling_fee']);
        $this->addInsertValue($order_prices['TotalHandlingCharge']             , $columns['total_handling_charge']);
        $this->addInsertValue($order_prices['TotalShippingAndHandlingCost']    , $columns['total_shipping_and_handling_cost']);
        $this->addInsertValue($order_prices['OrderTaxTotal']                   , $columns['order_tax_total']);
        $this->addInsertValue($order_prices['SubtotalGlobalDiscount']          , $columns['subtotal_global_discount']);
        $this->addInsertValue($order_prices['SubtotalPromoCodeDiscount']       , $columns['subtotal_promo_code_discount']);
        $this->addInsertValue($order_prices['QuantityDiscount']                , $columns['quantity_discount']);
        $this->addInsertValue($order_prices['DiscountedSubtotal']              , $columns['discounted_subtotal']);
        $this->addInsertValue($order_prices['OrderNotIncludedTaxTotal']        , $columns['order_not_included_tax_total']);
        $this->addInsertValue($order_prices['PerOrderPaymentModuleShippingFee'], $columns['per_order_payment_module_shipping_fee']);
    }
}

class INSERT_ORDER_TAXES extends DB_Insert
{
    function INSERT_ORDER_TAXES()
    {
        parent::DB_Insert('order_taxes');
    }

    function initQuery($params)
    {
        $order_id = $params['order_id'];
        $taxes_type = $params['taxes_type'];
        $taxes_value = $params['taxes_value'];
        $currency_code = $params['currency_code'];
        $currency_type = $params['currency_type'];
        $is_included = $params["is_included"];

        $tables = Checkout::getTables();
        $columns_taxes = $tables['order_taxes']['columns'];

        $this->addInsertValue($currency_code,       $columns_taxes['currency_code']);
        $this->addInsertValue($currency_type,       $columns_taxes['currency_type']);
        $this->addInsertValue($order_id,            $columns_taxes['order_id']);
        $this->addInsertValue($taxes_type,          $columns_taxes['type']);
        $this->addInsertValue($taxes_value,         $columns_taxes['value']);
        $this->addInsertValue($is_included,         $columns_taxes['is_included']);
    }
}

class INSERT_ORDER_TAX_DISPLAY_OPTIONS extends DB_Insert
{
    function INSERT_ORDER_TAX_DISPLAY_OPTIONS()
    {
        return parent::DB_Insert('order_tax_display_options');
    }

    function initQuery($params)
    {
        $order_id =         $params['order_id'];
        $value =            $params['value'];
        $visible_name =     $params['name'];
        $currency_code =    $params['currency_code'];
        $currency_type =    $params['currency_type'];
        $formula =          $params["formula"];

        $tables = Checkout::getTables();
        $columns_taxes = $tables['order_tax_display_options']['columns'];

        $this->addInsertValue($currency_code,       $columns_taxes['currency_code']);
        $this->addInsertValue($currency_type,       $columns_taxes['currency_type']);
        $this->addInsertValue($order_id,            $columns_taxes['order_id']);
        $this->addInsertValue($visible_name,        $columns_taxes['name']);
        $this->addInsertValue($value,               $columns_taxes['value']);
        $this->addInsertValue($formula,             $columns_taxes['formula']);
    }
}

class SELECT_PM_SM_SETTINGS extends DB_Select
{
    function initQuery($params)
    {
        $tables = modApiStaticFunc($params['ApiClassName'], "getTables");
        $s = $tables[$params['settings_table_name']]['columns'];
        $this->addSelectField($s['key'], 'set_key');
        if (in_array($params['ApiClassName'],
            array('Shipping_Module_Flat_Shipping_Rates',
                  'Shipping_Module_Flat_Shipping_Rates2',
                  'Shipping_Module_Flat_Shipping_Rates3')))
        {
            $this -> setMultiLangAlias('_value',
                                       $params['settings_table_name'],
                                       $s['value'], $s['id'],
                                       $params['ApiClassName']);
            $this -> addSelectField($this -> getMultiLangAlias('_value'),
                                    'set_value');
        }
        else
        {
            $this->addSelectField($s['value'], 'set_value');
        }
    }
}

class SELECT_PM_SM_SETTINGS2 extends DB_Select
{
    function initQuery($params)
    {
        $tables = modApiStaticFunc($params['ApiClassName'], "getTables");
        $s = $tables[$params['settings_table_name']]['columns'];
        $this->addSelectTable($params['settings_table_name']);
        $this->addSelectField('*');
    }
}

class SELECT_BASE_ORDERS_INFO extends DB_Select
{
    function initQuery($params)
    {
        $tables = Checkout::getTables();
        $o_table = $tables['orders']['columns'];
        $op_table = $tables['order_prices']['columns'];

        $this->addSelectField($o_table['id'],'order_id');
        $this->addSelectField($o_table['date'],'order_date');
        $this->addSelectField($o_table['payment_status_id'],'payment_status_id');
        $this->addSelectField($o_table['status_id'],'status_id');
        $this->addSelectField($o_table['person_id'],'person_id');
        $this->addSelectField($op_table['currency_code'],'currency_code');
        $this->addSelectField($op_table['currency_type'],'currency_type');
        $this->addSelectField($op_table['order_total'],'order_total');
        $this->addSelectField($op_table['order_tax_total'],'order_tax_total');
        $this->addInnerJoin('orders', $o_table['id'], DB_EQ, $op_table['order_id']);
        $this->Where($o_table['id'], DB_IN, "(".implode(",",$params['order_ids']).")");
    }
}

?>