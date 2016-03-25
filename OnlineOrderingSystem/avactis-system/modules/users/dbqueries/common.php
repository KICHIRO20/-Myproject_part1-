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

loadModuleFile('users/users_api.php');

class USERS_GET_USER_PERMISSIONS extends DB_Select
{
    function initQuery($params)
    {
        $stables = Users::getTables();
        $atable = 'admin_permissions';
        $acolumns = $stables[$atable]['columns'];

        $this->addSelectField($acolumns['permission']);
        $this->addSelectField($acolumns['access_level']);
        $this->WhereValue($acolumns['admin_id'], DB_EQ, $params['admin_id']);
    }
}

?>