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

class SELECT_TRANSACTION_TRACKING_MODULES extends DB_Select
{
    function initQuery($params)
    {
        $tables = TransactionTracking::getTables();
        $ttm = $tables['transaction_tracking_modules']['columns'];

        $this->addSelectTable('transaction_tracking_modules');
        $this->addSelectField($ttm['module_id'], 'module_id');
        $this->addSelectField($ttm['module_name'], 'module_name');
        $this->addSelectField($ttm['status_active'], 'status_active');
    }
}