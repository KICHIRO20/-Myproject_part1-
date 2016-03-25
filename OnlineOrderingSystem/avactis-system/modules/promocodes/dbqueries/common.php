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

class PROMOCODE_SELECT_PRODUCTS_AFFECTED extends DB_Select
{
    function initQuery($params)
    {
        $id = $params['id'];

        $tables = PromoCodes::getTables();
        $c = $tables['promo_codes_coupons_table']['columns'];

        $this->addSelectField($c['products_affected'], 'prods');
        $this->addSelectField($c['categories_affected'], 'cats');
        $this->WhereValue($c['id'], DB_EQ, $id);
    }
}

?>