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

class SELECT_MR_SETTINGS extends DB_Select
{
    function initQuery($params)
    {
        $tables=Mod_Rewrite::getTables();

        $this->addSelectTable('mr_settings');
        $this->addSelectField('*');
    }
}

class SELECT_MR_SCHEMES extends DB_Select
{
    function initQuery($params)
    {
        $tables=Mod_Rewrite::getTables();
        $scheme_table = $tables['mr_schemes']['columns'];

        $this->addSelectTable('mr_schemes');
        $this->addSelectField('*');
        if (isset($params['scheme_id']) && !empty($params['scheme_id']))
            $this->WhereValue($scheme_table['scheme_id'], DB_EQ, $params['scheme_id']);
    }
}

class SELECT_MR_INTEGRITY_INFO extends DB_Select
{
    function initQuery($params)
    {
        $tables=Mod_Rewrite::getTables();
        $integrity_table = $tables['mr_integrity']['columns'];

        $cz_layout_path = $params['cz_layout_path'];

        $this->addSelectTable('mr_integrity');
        $this->addSelectField('*');
        $this->WhereValue($integrity_table['layout_path'], DB_EQ, $cz_layout_path);


    }
}


?>