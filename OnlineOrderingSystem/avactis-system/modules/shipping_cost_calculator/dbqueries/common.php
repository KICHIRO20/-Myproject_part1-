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

loadModuleFile('shipping_cost_calculator/shipping_cost_calculator_api.php');

class SELECT_SCC_FS_RULES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Shipping_Cost_Calculator::getTables();
        $c = $tables['scc_fs_rules']['columns'];

        $this->addSelectTable('scc_fs_rules');

        $this->addSelectField($c['id'], 'id');
        $this->addSelectField($c['rule_name'], 'rule_name');
        $this->addSelectField($c['min_subtotal'], 'min_subtotal');
        $this->addSelectField($c['cats'], 'cats');
        $this->addSelectField($c['prods'], 'prods');
        $this->addSelectField($c['dirty_cart'], 'dirty_cart');
    }
}

class SELECT_SCC_FS_RULE_BY_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Shipping_Cost_Calculator::getTables();
        $c = $tables['scc_fs_rules']['columns'];

        $this->addSelectTable('scc_fs_rules');

        $this->addSelectField($c['id'], 'id');
        $this->addSelectField($c['rule_name'], 'rule_name');
        $this->addSelectField($c['min_subtotal'], 'min_subtotal');
        $this->addSelectField($c['cats'], 'cats');
        $this->addSelectField($c['prods'], 'prods');
        $this->addSelectField($c['dirty_cart'], 'dirty_cart');

        $this->WhereValue($c['id'], DB_EQ, $params['id']);
    }
}

class SELECT_SCC_FS_RULE_BY_NAME extends DB_Select
{
    function initQuery($params)
    {
        $tables = Shipping_Cost_Calculator::getTables();
        $c = $tables['scc_fs_rules']['columns'];

        $this->addSelectTable('scc_fs_rules');

        $this->addSelectField($c['id'], 'id');
        $this->addSelectField($c['rule_name'], 'rule_name');
        $this->addSelectField($c['min_subtotal'], 'min_subtotal');
        $this->addSelectField($c['cats'], 'cats');
        $this->addSelectField($c['prods'], 'prods');
        $this->addSelectField($c['dirty_cart'], 'dirty_cart');

        $this->WhereValue($c['rule_name'], DB_EQ, $params['name']);
    }
}

class SCC_FS_RULE_SELECT_PRODUCTS_AFFECTED extends DB_Select
{
    function initQuery($params)
    {
        $id = $params['id'];

        $tables = Shipping_Cost_Calculator::getTables();
        $c = $tables['scc_fs_rules']['columns'];

        $this->addSelectField($c['prods'], 'prods');
        $this->addSelectField($c['cats'], 'cats');
        $this->WhereValue($c['id'], DB_EQ, $id);
    }
}

?>