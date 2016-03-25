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

class SELECT_FEATURED_PRODUCTS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Featured_Products::getTables();
        $fp_table = $tables['fp_links']['columns'];

        $this->addSelectTable('fp_links');
        $this->addSelectField($fp_table['fp_id'], 'fp_id');
        $this->Where($fp_table['category_id'], DB_IN, "(".implode(", ",$params['categories_ids']).")");
        $this->SelectGroup($fp_table['fp_id']);
        $this->SelectOrder($fp_table['sort_order']);

    }
}


?>