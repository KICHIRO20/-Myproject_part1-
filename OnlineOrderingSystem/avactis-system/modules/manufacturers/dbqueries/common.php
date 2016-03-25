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

class SELECT_ALL_MANUFACTURERS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Manufacturers::getTables();
        $m = $tables['manufacturers']['columns'];

        $this->addSelectTable('manufacturers');
        $this->addSelectField('*');
        $this->SelectOrder($m['sort_order'],'ASC');
    }
}

class SELECT_MANUFACTURERS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Manufacturers::getTables();
        $tables_ctl = Catalog::getTables();
        $m = $tables['manufacturers']['columns'];
        $pa = $tables_ctl['product_attributes']['columns'];

        $setting_filtering = $params['setting_filtering'];
        $setting_nonempty = $params['setting_nonempty'];
        $zone = $params['zone'];
        $b_only_active = $params['b_only_active'];
        $return_all = $params['return_all'];
        $_ids = $params['_ids'];

        $this->addSelectTable('manufacturers');
        $this->addSelectField("DISTINCT(".$m['manufacturer_id'].")", 'id');
        $this->addSelectField($m['manufacturer_name'],  'value');
        if($b_only_active === true)
        {
           $this->WhereValue($m['manufacturer_active'], DB_EQ, DB_TRUE);
        }

        if ($return_all == false && $setting_nonempty == "HIDE_EMPTY" && $zone == "CustomerZone")
        {
            $this->addSelectTable('product_attributes');
            if($b_only_active === true)
                $this->WhereAND();
            $this->WhereField($m['manufacturer_id'], DB_EQ, $pa['attr_value']);
            $this->WhereAND();
            $this->WhereValue($pa['a_id'], DB_EQ, modApiFunc('Catalog', 'getManufacturerAttrId'));

            if (!empty($_ids))
            {
                foreach ($_ids as $i)
                {
                    $ids[] = $i['product_id'];
                }
                $this->WhereAND();
                $this->WhereField($pa['p_id'], DB_IN, '('.implode(',', $ids).')');
            }
        }

        $this->SelectOrder($m['sort_order'],'ASC');
    }
}
?>