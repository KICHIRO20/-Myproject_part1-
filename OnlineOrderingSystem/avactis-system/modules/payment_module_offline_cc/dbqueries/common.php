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

class UPDATE_PM_OFFLINE_CC_SETTINGS extends DB_Update
{
    function UPDATE_PM_OFFLINE_CC_SETTINGS()
    {
        parent::DB_Update('pm_offline_cc_settings');
    }

    function initQuery($params)
    {
        $tables = Payment_Module_Offline_CC::getTables();
        $s = $tables['pm_offline_cc_settings']['columns'];

        $this->addUpdateValue($s['value'], $params['value']);
        $this->WhereValue($s['key'], DB_EQ, $params['key']);
    }
}

class DELETE__PM_OFFLINE_CC_SETTINGS extends DB_Delete
{
    function DELETE__PM_OFFLINE_CC_SETTINGS()
    {
        parent::DB_Delete('pm_offline_cc_settings');
    }

    function initQuery($params)
    {
    }
}

class INSERT_PM_OFFLINE_CC_SETTINGS extends DB_Insert
{
    function INSERT_PM_OFFLINE_CC_SETTINGS()
    {
        parent::DB_Insert('pm_offline_cc_settings');
    }

    function initQuery($params)
    {
        $tables = Payment_Module_Offline_CC::getTables();
        $s = $tables["pm_offline_cc_settings"]['columns'];

        $this->addInsertValue($params['key'], $s["key"]);
        $this->addInsertValue($params['value'], $s["value"]);
    }
}

?>