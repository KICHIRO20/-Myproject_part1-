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

loadModuleFile('currency_converter/currency_converter_api.php');

class SELECT_CURRENT_GROUP_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Account::getTables();
        $t_ca_customers = $tables['ca_customers']['columns'];

        $this->addSelectField($t_ca_customers['group_id'], 'group_id');
        $this->WhereValue($t_ca_customers['customer_account'], DB_EQ, $params['customer_account']);
    }
}

class SELECT_CUSTOMER_ACCOUNT_GROUPS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Account::getTables();
        $t_ca_customer_groups = $tables['ca_customer_groups']['columns'];

        $this->addSelectField($t_ca_customer_groups['group_id'], 'group_id');
        $this->addSelectField($t_ca_customer_groups['group_name'], 'group_name');
        $this->SelectOrder('group_name', 'ASC');
    }
}

class DELETE_CUSTOMER_GROUP extends DB_Delete
{
    function DELETE_CUSTOMER_GROUP()
    {
        parent :: DB_Delete('ca_customer_groups');
    }

    function initQuery($params)
    {
        $tables = Customer_Account :: getTables();
        $t_ca_customer_groups = $tables['ca_customer_groups']['columns'];

        $this -> WhereValue($t_ca_customer_groups['group_id'], DB_EQ, $params['group_id']);
    }
}

class INSERT_CUSTOMER_GROUP extends DB_Insert
{
    function INSERT_CUSTOMER_GROUP()
    {
        parent::DB_Insert('ca_customer_groups');
    }

    function initQuery($params)
    {
        $tables = Customer_Account::getTables();
        $columns = $tables['ca_customer_groups']['columns'];

        $this->addInsertValue($params['group_name'], $columns['group_name']);
    }
}

class UPDATE_CUSTOMER_GROUP_ID extends DB_Update
{
    function UPDATE_CUSTOMER_GROUP_ID()
    {
        parent :: DB_Update('ca_customer_groups');
    }

    function initQuery($params)
    {
        $tables = Customer_Account::getTables();
        $table = $tables['ca_customer_groups']['columns'];

        $this -> AddUpdateValue($table['group_id'], $params['group_id']);
        $this -> WhereValue($table['group_name'], DB_EQ, $params['group_name']);
    }
}

class UPDATE_CUSTOMER_ACCOUNT_GROUP extends DB_Update
{
    function UPDATE_CUSTOMER_ACCOUNT_GROUP()
    {
        parent :: DB_Update('ca_customers');
    }

    function initQuery($params)
    {
        $tables = Customer_Account::getTables();
        $table = $tables['ca_customers']['columns'];

        $this -> AddUpdateValue($table['group_id'], $params['group_id']);
        $this -> WhereValue($table['customer_account'], DB_EQ, $params['customer_account']);
    }
}

class UPDATE_CUSTOMER_ACCOUNT_GROUP_TO_DEFAULT extends DB_Update
{
    function UPDATE_CUSTOMER_ACCOUNT_GROUP_TO_DEFAULT()
    {
        parent :: DB_Update('ca_customers');
    }

    function initQuery($params)
    {
        $tables = Customer_Account::getTables();
        $table = $tables['ca_customers']['columns'];

        $this -> AddUpdateValue($table['group_id'], 1);
        $this -> WhereValue($table['group_id'], DB_EQ, $params['group_id']);
    }
}

class SELECT_CUSTOMER_ACCOUNT_SETTINGS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Account::getTables();
        $t_ca_settings = $tables['ca_settings']['columns'];

        $this->addSelectField($t_ca_settings['setting_key'], 'setting_key');
        $this->addSelectField($t_ca_settings['setting_value'], 'setting_value');
    }
}

class SELECT_CUSTOMER_ACCOUNT_NAME_BY_CUSTOMER_ID extends DB_Select
{
    function initQuery($params)
    {
        $customer_id = $params['customer_id'];

        $tables = Customer_Account::getTables();
        $customers_table = $tables['ca_customers']['columns'];

        $this->addSelectTable('ca_customers');
        $this->addSelectField($customers_table['customer_account'], 'customer_account');
        $this->WhereValue($customers_table['customer_id'], DB_EQ, $customer_id);
    }
}

class SELECT_CUSTOMER_ACCOUNT_FULL_INFO extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Account::getTables();
        $capig  = $tables['ca_person_info_groups']['columns'];
        $caatg  = $tables['ca_attrs_to_groups']['columns'];
        $capia  = $tables['ca_person_info_attrs']['columns'];
        $capid  = $tables['ca_person_info_data']['columns'];

        $this->addSelectField($capig['group_id'], 'group_id');
        $this->addSelectField($capig['group_name'], 'group_name');
        $this->addSelectField($capig['lang_code'], 'capig_lang_code');
        $this->addSelectField($capig['sort_order'], 'sort_order');

        $this->addSelectField($caatg['attr_id'], 'attr_id');
        $this->addSelectField($caatg['is_visible'], 'is_visible');
        $this->addSelectField($caatg['is_required'], 'is_required');

        $this->addSelectField($capia['lang_code'], 'capia_lang_code');
        $this->addSelectField($capia['attr_name'], 'attr_name');

        $this->addSelectField($capid['data_value'], 'data_value');

        $this->addSelectTable('ca_person_info_groups');
        $this->addLeftJoin('ca_attrs_to_groups', $capig['group_id'], DB_EQ, $caatg['group_id']);
        $this->addInnerJoin('ca_person_info_attrs', $caatg['attr_id'], DB_EQ, $capia['attr_id']);
        $this->addLeftJoin('ca_person_info_data', $caatg['ag_id'], DB_EQ, $capid['ag_id'] . ' AND '. $capid['customer_id'] . ' = ' . $params['customer_id'] . ' ');

        $this->setMultiLangAlias('_ml_name', 'ca_attrs_to_groups', $caatg['visible_name'], $caatg['ag_id'], 'Customer_Account');
        $this->addSelectField($this->getMultiLangAlias('_ml_name'), 'visible_name');

        $this->SelectOrder($capig['sort_order'], 'ASC');
        $this->SelectOrder($caatg['sort_order'], 'ASC');
    }
}

class SELECT_CUSTOMER_ACCOUNT_ATTRIBUTES_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Account::getTables();
        $atg_table = $tables['ca_attrs_to_groups']['columns'];

        $this->addSelectField($atg_table['ag_id'], 'ag_id');
        $this->addSelectField($atg_table['group_id'], 'group_id');
        $this->addSelectField($atg_table['attr_id'], 'attr_id');
    }
}

class SELECT_CUSTOMER_ACCOUNT_STATUS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Account::getTables();
        $table = $tables['ca_customers']['columns'];

        $this->addSelectField($table['customer_status'], 'status');
        $this->WhereValue($table['customer_account'], DB_EQ, $params['customer_account']);
    }
}

class SELECT_CUSTOMER_ACCOUNT_LANGUAGE extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Account::getTables();
        $table = $tables['ca_customers']['columns'];

        $this -> addSelectField($table['customer_lng'], 'language');
        $this -> WhereValue($table['customer_account'], DB_EQ, $params['customer_account']);
    }
}

class SELECT_CUSTOMER_ACCOUNT_NOTIFICATION_LANGUAGE extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Account::getTables();
        $table = $tables['ca_customers']['columns'];

        $this -> addSelectField($table['notification_lng'], 'language');
        $this -> WhereValue($table['customer_account'], DB_EQ, $params['customer_account']);
    }
}

class UPDATE_CUSTOMER_ACCOUNT_LANGUAGE extends DB_Update
{
    function UPDATE_CUSTOMER_ACCOUNT_LANGUAGE()
    {
        parent :: DB_Update('ca_customers');
    }

    function initQuery($params)
    {
        $tables = Customer_Account::getTables();
        $table = $tables['ca_customers']['columns'];

        $this -> AddUpdateValue($table['customer_lng'], $params['customer_lng']);
        $this -> WhereValue($table['customer_account'], DB_EQ, $params['customer_account']);
    }
}

class UPDATE_CUSTOMER_ACCOUNT_NOTIFICATION_LANGUAGE extends DB_Update
{
    function UPDATE_CUSTOMER_ACCOUNT_NOTIFICATION_LANGUAGE()
    {
        parent :: DB_Update('ca_customers');
    }

    function initQuery($params)
    {
        $tables = Customer_Account::getTables();
        $table = $tables['ca_customers']['columns'];

        $this -> AddUpdateValue($table['notification_lng'], $params['notification_lng']);
        $this -> WhereValue($table['customer_account'], DB_EQ, $params['customer_account']);
    }
}

class UPDATE_CUSTOMER_AFFILIATE_ID extends DB_Update
{
    function UPDATE_CUSTOMER_AFFILIATE_ID()
    {
        parent :: DB_Update('ca_customers');
    }

    function initQuery($params)
    {
        $tables = Customer_Account::getTables();
        $table = $tables['ca_customers']['columns'];

        $this -> AddUpdateValue($table['affiliate_id'], $params['affiliate_id']);
        $this -> WhereValue($table['customer_account'], DB_EQ, $params['customer_account']);
    }
}

class SELECT_ALL_CUSTOMERS_EMAILS extends DB_Select
{
    function initQuery($params)
    {
        $tables = modApiStaticFunc("Customer_Account", "getTables");
        $attrs_table = $tables['ca_person_info_attrs']['columns'];
        $atg_table = $tables['ca_attrs_to_groups']['columns'];
        $data_table = $tables['ca_person_info_data']['columns'];

        $this->addSelectField($data_table['data_value'], 'value');
        $this->addSelectTable('ca_person_info_attrs');
        $this->addSelectTable('ca_attrs_to_groups');
        $this->addSelectTable('ca_person_info_data');
        $this->WhereValue($attrs_table['attr_name'], DB_EQ, 'Email');
        $this->WhereAND();
        $this->Where($atg_table['attr_id'], DB_EQ, $attrs_table['attr_id']);
        $this->WhereAND();
        $this->Where($data_table['ag_id'], DB_EQ, $atg_table['ag_id']);
        $this->WhereAND();
        $this->WhereValue($data_table['data_value'], DB_NEQ, '');
        $this->SelectGroup('value');
    }
}

class SELECT_CUSTOMER_ACCOUNTS extends DB_Select
{
    function initQuery($params)
    {
        $tables = modApiStaticFunc("Customer_Account", "getTables");
        $c  = $tables['ca_customers']['columns'];

        $this->addSelectField($c['customer_id'], 'customer_id');
        $this->addSelectField($c['customer_account'], 'customer_account');

        if($params['customer_ids'] !== NULL)
        {
            $this->Where($c['customer_id'], DB_IN, "('".implode("','",$params['customer_ids'])."')");
        }
    }
}

class SELECT_CUSTOMER_STATUSES extends DB_Select
{
    function initQuery($params)
    {
        $tables = modApiStaticFunc("Customer_Account", "getTables");
        $c  = $tables['ca_customers']['columns'];

        $this->addSelectField($c['customer_id'], 'customer_id');
        $this->addSelectField($c['customer_status'], 'customer_status');

        if($params['customer_ids'] !== NULL)
        {
            $this->Where($c['customer_id'], DB_IN, "('".implode("','",$params['customer_ids'])."')");
        }
    }
}

class SELECT_CUSTOMER_NAMES extends DB_Select
{
    function initQuery($params)
    {
        $tables = modApiStaticFunc("Customer_Account", "getTables");
        $pid1 = $tables['ca_person_info_data']['columns'];
        $pid2 = $this->addTableAlias($pid1, 'pid2', 'ca_person_info_data');
        $pid3 = $this->addTableAlias($pid1, 'pid3', 'ca_person_info_data');
        $pid4 = $this->addTableAlias($pid1, 'pid4', 'ca_person_info_data');
        $pid5 = $this->addTableAlias($pid1, 'pid5', 'ca_person_info_data');
        $pid6 = $this->addTableAlias($pid1, 'pid6', 'ca_person_info_data');

        $this->addSelectField($pid1['customer_id'], 'customer_id');
        $this->addSelectField('CONCAT('.$pid1['data_value'].', CONCAT(" ",'.$pid2['data_value'].'))', 'name_ci');
        $this->addSelectField('CONCAT('.$pid3['data_value'].', CONCAT(" ",'.$pid4['data_value'].'))', 'name_bi');
        $this->addSelectField('CONCAT('.$pid5['data_value'].', CONCAT(" ",'.$pid6['data_value'].'))', 'name_si');

        $this->Where($pid1['ag_id'], DB_EQ, 5);
        $this->WhereAND();
        $this->Where($pid2['ag_id'], DB_EQ, 6);
        $this->WhereAND();
        $this->WhereField($pid1['customer_id'], DB_EQ, $pid2['customer_id']);
        $this->WhereAND();
        $this->Where($pid3['ag_id'], DB_EQ, 14);
        $this->WhereAND();
        $this->Where($pid4['ag_id'], DB_EQ, 15);
        $this->WhereAND();
        $this->WhereField($pid1['customer_id'], DB_EQ, $pid4['customer_id']);
        $this->WhereAND();
        $this->WhereField($pid1['customer_id'], DB_EQ, $pid3['customer_id']);
        $this->WhereAND();
        $this->Where($pid5['ag_id'], DB_EQ, 24);
        $this->WhereAND();
        $this->Where($pid6['ag_id'], DB_EQ, 25);
        $this->WhereAND();
        $this->WhereField($pid1['customer_id'], DB_EQ, $pid5['customer_id']);
        $this->WhereAND();
        $this->WhereField($pid1['customer_id'], DB_EQ, $pid6['customer_id']);
    }
}

class SELECT_ORDER_CURRENCY_CODES extends DB_Select
{
    function initQuery($params)
    {
        $co_tables = Checkout::getTables();
        $order_prices_table = $co_tables['order_prices']['columns'];

        $this -> addSelectField('DISTINCT(' . $order_prices_table['currency_code'] . ')', 'currency_code');
    }
}

class SELECT_ATTRS_GROUP_IDS_BY_ATTR_NAMES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Account :: getTables();
        $caatg  = $tables['ca_attrs_to_groups']['columns'];
        $capia  = $tables['ca_person_info_attrs']['columns'];

        $this -> addSelectField($caatg['ag_id'], 'ag_id');
        $this -> addSelectTable('ca_person_info_attrs');

        $this -> Where($caatg['attr_id'], DB_EQ, $capia['attr_id']);
        $this -> WhereAND();
        $this -> Where($capia['attr_name'], DB_IN, '(\'' . join('\',\'', $params) . '\')');
    }
}

class SELECT_SEARCH_CUSTOMERS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Customer_Account :: getTables();
        $caatg  = $tables['ca_attrs_to_groups']['columns'];
        $capia  = $tables['ca_person_info_attrs']['columns'];
        $capid  = $tables['ca_person_info_data']['columns'];
        $cac    = $tables['ca_customers']['columns'];
        $cacg   = $tables['ca_customer_groups']['columns'];
        $capig  = $tables['ca_person_info_groups']['columns'];

        $this -> addSelectField($cac['customer_id'], 'customer_id');
        $this -> addSelectField($cac['customer_account'], 'customer_account');
        $this -> addSelectField($cac['customer_status'], 'customer_status');
        $this -> addSelectField($cac['group_id'], 'group_id');

        if ($params['type'] == 'custom' &&
            !preg_match("/^[0-9]+$/", $params['search_string'])
            && @is_array($params['attr_group_ids']))
        {
            $this -> addWhereOpenSection();
            $this -> WhereValue($cac['customer_account'], DB_LIKE, '%' . $params['search_string'] . '%');
            $this -> WhereOR();
            foreach($params['attr_group_ids'] as $k => $v)
            {
                $conditions = array('cpid' . $k . '.customer_id', DB_EQ, $cac['customer_id'], DB_AND, 'cpid' . $k . '.ag_id', DB_EQ, '\''. $v . '\'');
                $this -> addLeftJoinOnConditions('ca_person_info_data', $conditions, 'cpid' . $k);
                if ($k > 0)
                    $this -> WhereOR();
                $this -> WhereValue('cpid' . $k . '.data_value', DB_LIKE, '%' . $params['search_string'] . '%');
            }
            // search customer group
            $conditions = array($cac['group_id'], DB_EQ, 'cacg.group_id');
            $this -> addLeftJoinOnConditions('ca_customer_groups', $conditions, 'cacg');
            $this -> WhereOR();
            $this -> WhereValue('cacg.group_name', DB_LIKE, '%' . $params['search_string'] . '%');
            $this -> addWhereCloseSection();
        }
        elseif ($params['type'] == 'custom')
        {
            if (preg_match("/^[0-9]+$/", $params['search_string']))
            {
                $this -> addWhereOpenSection();
                $this -> Where($cac['customer_id'], DB_EQ, intval($params['search_string']));
                $this -> WhereOR();
                $this -> WhereValue($cac['customer_account'], DB_LIKE, '%' . $params['search_string'] . '%');
                $this -> WhereOR();
                $this -> WhereValue($cac['customer_account'], DB_LIKE, '%' . $params['search_string'] . '%' . PSEUDO_CUSTOMER_SUFFIX);
                $this -> addWhereCloseSection();
            }
            else
            {
                //Impossible: handled in __searchCustomers;
                _fatal(array( "CODE" => "CORE_061"), 'SELECT_SEARCH_CUSTOMERS', '$this->_customers_search_filter[\'type\'] == \'custom\'<br>BUT<br>!preg_match("/^[0-9]+$/", $this->_customers_search_filter[\'search_string\'])');
            }
        }
        elseif ($params['type'] == 'letter' && $params['search_string'] != '')
        {
            if ($params['letter_filter_by'] == 'customer_name')
            {
                $this -> addSelectTable('ca_person_info_attrs');
                $this -> addLeftJoin('ca_attrs_to_groups', $caatg['attr_id'], DB_EQ, $capia['attr_id']);
                $this -> addLeftJoin('ca_person_info_data', $capid['customer_id'], DB_EQ, $cac['customer_id']);
                $this -> addLeftJoin('ca_person_info_groups', $capig['group_id'], DB_EQ, $caatg['group_id']);

                $this -> addWhereOpenSection();
                $this -> WhereValue($capig['group_id'], DB_EQ, 1);
                $this -> WhereAND();
                $this -> WhereValue($capia['attr_name'], DB_EQ, 'LastName');
                $this -> WhereAND();
                $this -> Where($capid['ag_id'], DB_EQ, $caatg['ag_id']);
                $this -> WhereAND();
                $this -> WhereValue($capid['data_value'], DB_LIKE, $params['search_string'].'%');
                $this -> addWhereCloseSection();
            }
            else
            {
                $like_exp_a = $params['search_string'].'%';
                $like_exp_b = $params['search_string'].'%'.PSEUDO_CUSTOMER_SUFFIX;
                $this -> addWhereOpenSection();
                $this -> WhereValue($cac['customer_account'], DB_LIKE, $like_exp_a);
                $this -> WhereOR();
                $this -> WhereValue($cac['customer_account'], DB_LIKE, $like_exp_b);
                $this -> addWhereCloseSection();
            }
        }

        // getting full name
        $pid1 = $this -> convertAliasColumns($capid, 'pid1');
        $pid2 = $this -> convertAliasColumns($capid, 'pid2');
        $pid3 = $this -> convertAliasColumns($capid, 'pid3');
        $pid4 = $this -> convertAliasColumns($capid, 'pid4');
        $pid5 = $this -> convertAliasColumns($capid, 'pid5');
        $pid6 = $this -> convertAliasColumns($capid, 'pid6');

        $this -> addSelectField('IF(TRIM(CONCAT(' . $pid1['data_value'] . ', \' \', ' . $pid2['data_value'] .
                                ')) <> \'\', TRIM(CONCAT(' . $pid1['data_value'] . ', \' \', ' . $pid2['data_value'] .
                                ')), IF(TRIM(CONCAT(' . $pid3['data_value'] . ', \' \', ' . $pid4['data_value'] .
                                ')) <> \'\', TRIM(CONCAT(' . $pid3['data_value'] . ', \' \', ' . $pid4['data_value'] .
                                ')), IF(TRIM(CONCAT(' . $pid5['data_value'] . ', \' \', ' . $pid6['data_value'] .
                                ')) <> \'\', TRIM(CONCAT(' . $pid5['data_value'] . ', \' \', ' . $pid6['data_value'] .
                                ')), \'N\A\')))', 'name');

        $this -> addLeftJoinOnConditions('ca_person_info_data', array($pid1['ag_id'], DB_EQ, 5, DB_AND,
                                         $pid1['customer_id'], DB_EQ, $cac['customer_id']), 'pid1');
        $this -> addLeftJoinOnConditions('ca_person_info_data', array($pid2['ag_id'], DB_EQ, 6, DB_AND,
                                         $pid2['customer_id'], DB_EQ, $cac['customer_id']), 'pid2');
        $this -> addLeftJoinOnConditions('ca_person_info_data', array($pid3['ag_id'], DB_EQ, 14, DB_AND,
                                         $pid3['customer_id'], DB_EQ, $cac['customer_id']), 'pid3');
        $this -> addLeftJoinOnConditions('ca_person_info_data', array($pid4['ag_id'], DB_EQ, 15, DB_AND,
                                         $pid4['customer_id'], DB_EQ, $cac['customer_id']), 'pid4');
        $this -> addLeftJoinOnConditions('ca_person_info_data', array($pid5['ag_id'], DB_EQ, 24, DB_AND,
                                         $pid5['customer_id'], DB_EQ, $cac['customer_id']), 'pid5');
        $this -> addLeftJoinOnConditions('ca_person_info_data', array($pid6['ag_id'], DB_EQ, 25, DB_AND,
                                         $pid6['customer_id'], DB_EQ, $cac['customer_id']), 'pid6');

        // getting order information
        $co_tables = Checkout::getTables();
        $orders_table = $co_tables['orders']['columns'];
        $order_prices_table = $co_tables['order_prices']['columns'];

        // orders_count
        $this -> addSelectField('IF(' . $orders_table['id'] . ' IS NOT NULL, COUNT(' . $orders_table['id'] . '), 0)', 'orders_count');
        // left join criteria
        $this -> addLeftJoin('orders', $cac['customer_id'], DB_EQ, $orders_table['person_id']);

        // getting currency converter rates
        $cconv_tables = Currency_Converter::getTables();
        $tmp_rates_table = $cconv_tables['cconv_temp_cur_rates']['columns'];

        if ($params['currency_code'] != $params['main_currency_code'])
        {
            $conv = $this -> convertAliasColumns($tmp_rates_table, 'conv');
            // total_amount
            $this -> addSelectField('SUM(IF(' . $order_prices_table['order_id'] . ' IS NOT NULL, ' . $tmp_rates_table['rate'] . ' * ' . $order_prices_table['order_total'] . ' / IF(' . $conv['rate'] . ' <> 0, ' . $conv['rate'] . ', 1.00)' . ', 0))', 'total_amount');
            // fully_paid_amount
            $this -> addSelectField('SUM(IF(' . $order_prices_table['order_id'] . ' IS NOT NULL, IF(' . $orders_table['payment_status_id'] . '=' . ORDER_PAYMENT_STATUS_FULLY_PAID . ', ' . $tmp_rates_table['rate'] . ' * ' . $order_prices_table['order_total'] . ' / IF(' . $conv['rate'] . ' <> 0, ' . $conv['rate'] . ', 1.00)' . ', 0), 0))', 'fully_paid_amount');
        }
        else
        {
            // total_amount
            $this -> addSelectField('SUM(IF(' . $order_prices_table['order_id'] . ' IS NOT NULL, ' . $tmp_rates_table['rate'] . '*' . $order_prices_table['order_total'] . ', 0))', 'total_amount');
            // fully_paid_amount
            $this -> addSelectField('SUM(IF(' . $order_prices_table['order_id'] . ' IS NOT NULL, IF(' . $orders_table['payment_status_id'] . '=' . ORDER_PAYMENT_STATUS_FULLY_PAID . ', ' . $tmp_rates_table['rate'] . '*' . $order_prices_table['order_total'] . ', 0), 0))', 'fully_paid_amount');
        }
        // left join criteria
        $this -> addLeftJoinOnConditions('order_prices', array($order_prices_table['order_id'], DB_EQ, $orders_table['id'], DB_AND, $order_prices_table['currency_type'], DB_EQ, '\'CURRENCY_TYPE_MAIN_STORE_CURRENCY\''));
        $this -> addLeftJoin('cconv_temp_cur_rates', $tmp_rates_table['code'], DB_EQ, $order_prices_table['currency_code']);
        if ($params['currency_code'] != $params['main_currency_code'])
            $this -> addLeftJoin('cconv_temp_cur_rates', $tmp_rates_table['code'], DB_EQ, '\'' . $params['currency_code'] . '\'', 'conv');
        $this -> addLeftJoinOnConditions('ca_customer_groups', array($cacg['group_id'], DB_EQ, $cac['group_id']));

        // grouping
        $this -> SelectGroup($cac['customer_id']);

        // sorting
        if (in_array($params['sort_by'], array('group_id', 'orders_count', 'total_amount', 'fully_paid_amount', 'name', 'customer_account')))
        {
            $sb = $params['sort_by'];
            if($sb=='group_id') $sb=$cacg['group_name'];
            $this -> SelectOrder($sb, ((@$params['sort_dir'] != 'asc') ? 'DESC' : 'ASC'));
        }

        // paginating
        if (isset($params['paginator']) && is_array($params['paginator']))
        {
            list($offset, $count) = $params['paginator'];
            $this -> SelectLimit($offset, $count);
        }
    }
}

class SELECT_ANONYMOUS_USERS extends DB_Select
{
    function initQuery($params)
    {
        $anon_name = $params['anon_name'];

        $tables = modApiStaticFunc("Customer_Account", "getTables");
        $c = $tables['ca_customers']['columns'];

        $this->addSelectField($c['customer_account']);
        $this->WhereValue($c['customer_account'], DB_LIKE, "'".$anon_name."%'");
    }
}


?>