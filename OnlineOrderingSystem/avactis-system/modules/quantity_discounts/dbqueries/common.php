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

class SELECT_QUANTITY_DISCOUNTS_SETTINGS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Quantity_Discounts::getTables();
        $columns = $tables['quantity_discounts_settings']['columns'];

        $this->addSelectField($columns["key"], "set_key");
        $this->addSelectField($columns["value"], "set_value");
    }
}

class SELECT_QUANTITY_DISCOUNTS_RATES extends DB_Select
{
    function initQuery($params)
    {
        $cost_type_id = $params['cost_type_id'];
        $b_active_only = $params['b_active_only'];
        $current_customer_group = $params['current_customer_group'];

        $tables = Quantity_Discounts::getTables();
        $tr = $tables['quantity_discounts_rates_table']['columns'];

        $this->addSelectField($tr["id"], "id");
        $this->addSelectField($tr["product_id"], "product_id");
        $this->addSelectField($tr["rv_from"], "rv_from");
        $this->addSelectField($tr["cost_type_id"], "cost_type_id");
        $this->addSelectField($tr["b_active"], "b_active");
        $this->addSelectField($tr["cost"], "cost");
        $this->addSelectField($tr["customer_group_id"], "customer_group_id");

        $this->WhereValue('', '', '1');

        if($cost_type_id !== NULL)
        {
            $this->WhereAnd();
            $this->WhereValue($tr["cost_type_id"], DB_EQ, $cost_type_id);
        }
        if($b_active_only === true)
        {
            $this->WhereAND();
            $this->WhereValue($tr["b_active"], DB_EQ, 1 /* YES */);
        }
        if($current_customer_group !== NULL)
        {
            $this->WhereAnd();
            $this->WhereValue($tr["customer_group_id"], DB_EQ, $current_customer_group);
        }
        $this->SelectOrder($tr['customer_group_id']);
        $this->SelectOrder($tr['rv_from']);
    }
}

?>