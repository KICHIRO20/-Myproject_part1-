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

class SELECT_RELATED_PRODUCTS extends DB_SELECT
{
    function initQuery($params)
    {
        $tables = Related_Products::getTables();
        $rp_table = $tables['rp_links']['columns'];

        $this->addSelectTable('rp_links');
        $this->addSelectField($rp_table['rp_id'], 'rp_id');
        if (!is_array($params['product_id']))
        {
            $this->WhereValue($rp_table['product_id'], DB_EQ, $params['product_id']);
        }
        else
        {
            $this->Where($rp_table['product_id'], DB_IN, "(".implode(", ",$params['product_id']).")");
            $this->SelectGroup($rp_table['rp_id']);

        }
        $this->SelectOrder($rp_table['sort_order']);
    }
}


?>