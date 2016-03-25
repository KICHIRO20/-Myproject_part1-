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

class SELECT_SM_DSR_RATES extends DB_Select
{
    function initQuery($params)
    {
        $tables = modApiStaticFunc($params['ApiClassName'], "getTables");
        $columns = $tables['sm_dsr_rates']['columns'];

        $this->addSelectField($columns['rate_id']);
        $this->WhereValue($columns['method_id'], DB_EQ, $params['method_id']);
        $this->WhereAND();
        $this->WhereValue($columns['dst_country'], DB_EQ, $params['new_rate_data']["country_id"]);
        $this->WhereAND();
        $this->WhereValue($columns['dst_state'], DB_EQ, $params['new_rate_data']["state_id"]);
        $this->WhereAND();
        $this->addWhereOpenSection();
        $this->addWhereOpenSection();
        $this->Where($columns['wrange_from'], DB_LTE, sprintf("%.2f",$params['rate_data']["wrange_from"]));
        $this->WhereAND();
        $this->Where($columns['wrange_to'], DB_GTE, sprintf("%.2f",$params['rate_data']["wrange_from"]));
        $this->addWhereCloseSection();
        $this->WhereOR();
        $this->addWhereOpenSection();
        $this->Where($columns['wrange_from'], DB_LTE, sprintf("%.2f",$params['rate_data']["wrange_to"]));
        $this->WhereAND();
        $this->Where($columns['wrange_to'], DB_GTE, sprintf("%.2f",$params['rate_data']["wrange_to"]));
        $this->addWhereCloseSection();
        $this->addWhereCloseSection();
    }
}

class SELECT_SM_DSR_RATES_BY_METHOD extends DB_Select
{
    function initQuery($params)
    {
        $tables = modApiStaticFunc($params['ApiClassName'], "getTables");
        $columns = $tables['sm_dsr_rates']['columns'];

        $this->addSelectField($columns['rate_id'],'rate_id');
        $this->addSelectField($columns['dst_country'],'dst_country');
        $this->addSelectField($columns['dst_state'],'dst_state');
        $this->addSelectField($columns['wrange_from'],'wrange_from');
        $this->addSelectField($columns['wrange_to'],'wrange_to');
        $this->addSelectField($columns['bcharge_abs'],'bcharge_abs');
        $this->addSelectField($columns['bcharge_perc'],'bcharge_perc');
        $this->addSelectField($columns['acharge_pi_abs'],'acharge_pi_abs');
        $this->addSelectField($columns['acharge_pi_perc'],'acharge_pi_perc');
        $this->addSelectField($columns['acharge_pwu_abs'],'acharge_pwu_abs');
        $this->addSelectField($columns['acharge_pwu_perc'],'acharge_pwu_perc');
        $this->WhereValue($columns['method_id'], DB_EQ, $params['method_id']);
        $this->SelectOrder($columns['dst_country'],'ASC');
        $this->SelectOrder($columns['dst_state'],'ASC');
        $this->SelectOrder($columns['wrange_from'],'ASC');
    }
}

class SELECT_SM_DSR_METHODS_BY_METHOD extends DB_Select
{
    function initQuery($params)
    {
        $tables = modApiStaticFunc($params['ApiClassName'], "getTables");
        $methods_table = $tables['sm_dsr_methods']['columns'];

        $this->addSelectTable('sm_dsr_methods');
        $this->addSelectField($methods_table['id'],'id');
        $this->Where($methods_table['id'], DB_EQ, intval($params['method_id']));
    }
}

class SELECT_SM_DSR_METHODS_BY_METHOD_EXT extends DB_Select
{
    function initQuery($params)
    {
        $tables = modApiStaticFunc($params['ApiClassName'], "getTables");
        $methods_table = $tables['sm_dsr_methods']['columns'];

        $this->addSelectTable('sm_dsr_methods');
        $this->addSelectField($methods_table['id'],'id');
        $this -> setMultiLangAlias('_name', 'sm_dsr_methods',
                                   $methods_table['method_name'],
                                   $methods_table['id'],
                                   'Shipping_Module_DSR');
        $this -> addSelectField($this -> getMultiLangAlias('_name'),
                                'method_name');
        $this->addSelectField($methods_table['method_code'],'method_code');
        $this->addSelectField($methods_table['destination'],'destination');
        $this->addSelectField($methods_table['available'],'available');
        $this->Where($methods_table['id'], DB_EQ, intval($params['method_id']));
    }
}

class SELECT_SM_DSR_METHODS_BY_AVAILABILITY extends DB_Select
{
    function initQuery($params)
    {
        $tables = modApiStaticFunc($params['ApiClassName'], "getTables");
        $methods_table = $tables['sm_dsr_methods']['columns'];

        $this->addSelectTable('sm_dsr_methods');
        $this->addSelectField($methods_table['id'],'id');
        $this -> setMultiLangAlias('_name', 'sm_dsr_methods',
                                   $methods_table['method_name'],
                                   $methods_table['id'],
                                   'Shipping_Module_DSR');
        $this -> addSelectField($this -> getMultiLangAlias('_name'),
                                'method_name');
        $this->addSelectField($methods_table['method_code'],'method_code');
        $this->addSelectField($methods_table['destination'],'destination');
        $this->addSelectField($methods_table['available'],'available');
        if($params['available_condition']!="")
            $this->Where($methods_table['available'], DB_EQ, $params['available_condition']);

        $this->SelectOrder($methods_table['id']);
    }
}

class SELECT_SM_DSR_CACHED_RATES extends DB_Select
{
    function initQuery($params)
    {
        $tables = modApiStaticFunc($params['ApiClassName'], "getTables");
        $rc_table = $tables['sm_dsr_rates_cache']['columns'];

        $this->addSelectTable('sm_dsr_rates_cache');
        $this->addSelectField($rc_table['rate'],'rate');
        $this->Where($rc_table['hash'], DB_EQ, "'".$params['hash']."'");
        $this->WhereAND();
        $this->Where($rc_table['method_id'], DB_EQ, $params['method_id']);
        $this->WhereAND();
        $this->Where($rc_table['expire'], DB_GT, time());
    }
}

class SELECT_SM_DSR_RATES_EXT extends DB_Select
{
    function initQuery($params)
    {
        $tables = modApiStaticFunc($params['ApiClassName'], "getTables");
        $columns=$tables['sm_dsr_rates']['columns'];

        $this->addSelectField($columns['dst_country'],'dst_country');
        $this->addSelectField($columns['dst_state'],'dst_state');
        $this->addSelectField($columns['bcharge_abs'],'bcharge_abs');
        $this->addSelectField($columns['bcharge_perc'],'bcharge_perc');
        $this->addSelectField($columns['acharge_pi_abs'],'acharge_pi_abs');
        $this->addSelectField($columns['acharge_pi_perc'],'acharge_pi_perc');
        $this->addSelectField($columns['acharge_pwu_abs'],'acharge_pwu_abs');
        $this->addSelectField($columns['acharge_pwu_perc'],'acharge_pwu_perc');
        $this->WhereValue($columns['method_id'], DB_EQ, $params['method_id']);
        $this->WhereAND();

        $this->addWhereOpenSection();
        $this->WhereValue($columns['dst_country'], DB_EQ, $params['shipping_info_country_id']);
        $this->WhereOR();
        $this->WhereValue($columns['dst_country'], DB_EQ, ALL_OTHER_COUNTRIES_COUNTRY_ID);
        $this->addWhereCloseSection();
        $this->WhereAND();

        $this->addWhereOpenSection();
        $this->WhereValue($columns['dst_state'], DB_EQ, $params['shipping_info_state_id']);
        $this->WhereOR();
        $this->WhereValue($columns['dst_state'], DB_EQ, ALL_OTHER_STATES_STATE_ID);
        $this->WhereOR();
        $this->WhereValue($columns['dst_state'], DB_EQ, STATE_UNDEFINED_STATE_ID);
        $this->addWhereCloseSection();

        $this->WhereAND();
        $this->WhereValue($columns['wrange_from'], DB_LTE, $params['pak_weight']);
        $this->WhereAND();
        $this->WhereValue($columns['wrange_to'], DB_GTE, $params['pak_weight']);
    }
}

?>