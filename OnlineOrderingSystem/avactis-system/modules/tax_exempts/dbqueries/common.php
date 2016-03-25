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
class SELECT_ORDERS_FULL_TAX_EXEMPTION_DATA extends DB_Select
{
    function initQuery($params)
    {
        $tables = TaxExempts::getTables();
        $columns = $tables['order_full_tax_exempts']['columns'];

        $this->addSelectField($columns['order_id'], 'order_id');
        $this->addSelectField($columns['exempt_status'], 'exempt_status');
        $this->addSelectField($columns['exempt_reason_customer_input'], 'exempt_reason_customer_input');
        $this->WhereValue('', '', '1');

        if($params['order_id'] !== NULL)
        {
            $this->WhereAnd();
            $this->WhereValue($columns["order_id"], DB_EQ, $params['order_id']);
        }
        if($params['b_exempted_only'] === true)
        {
            $this->WhereAnd();
            $this->WhereValue($columns["exempt_status"], DB_EQ, DB_TRUE);
        }
    }
}
?>