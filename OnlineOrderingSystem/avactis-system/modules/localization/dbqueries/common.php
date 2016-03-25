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

class SELECT_LOCALIZATION_PATTERN_BY_PATTERN_TYPE extends DB_Select
{
    function initQuery($params)
    {
        $pattern_type = $params['pattern_type'];

        $tables = Localization::getTables();
        $p = $tables['patterns']['columns'];

        $this->addSelectField($p['type'], 'patt_type');
        $this->addSelectField($p['value'], 'patt_value');
        $this->WhereValue($p['type'], DB_EQ, $pattern_type);
    }
}

class SELECT_ALL_LOCALIZATION_PATTERNS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Localization::getTables();
        $p = $tables['patterns']['columns'];

        $this->addSelectField($p['type'], 'patt_type');
        $this->addSelectField($p['value'], 'patt_value');
    }
}

class SELECT_LOCALIZATION_SETTINGS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Localization::getTables();
        $s = $tables['localization_settings']['columns'];

        $this->addSelectField($s["key"], "setting_key");
        $this->addSelectField($s["val"], "setting_val");
    }
}

class SELECT_LOCALIZATION_CURRENCY_CODE extends DB_Select
{
    function initQuery($params)
    {
        $tables = Localization::getTables();
        $cols = $tables['currencies']['columns'];

        $this->addSelectField($cols['code'], 'code');
        $this->Where($cols['id'], DB_EQ, $params['currency_id']);
    }
}

class SELECT_LOCALIZATION_FORMATS_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Localization::getTables();
        switch($params['entity'])
        {
            case 'date':
                $d  = $tables['date_time_formats']['columns'];
                $this->addSelectField($d["id"], "id");
                $this->addSelectField($d["format"], "format");
                $this->WhereValue($d["d_t"], DB_EQ, "date");
                break;
            case 'time':
                $t  = $tables['date_time_formats']['columns'];
                $this->addSelectField($t["id"], "id");
                $this->addSelectField($t["format"], "format");
                $this->WhereValue($t["d_t"], DB_EQ, "time");
                break;
            case 'negative':
                $t  = $tables['negative_formats']['columns'];
                $this->addSelectField($t["id"], "id");
                $this->addSelectField($t["format"], "format");
                break;
            case 'currency':
                $t  = $tables['currencies']['columns'];
                $this->addSelectField($t["id"], "id");
                $this->addSelectField($t["name"], "name");
                $this->addSelectField($t["code"], "code");
                $this->addSelectField($t["iso"], "iso");
                $this->addSelectField($t["sign"], "sign");
                $this->addSelectField($t["active"], "active");
                $this->addSelectField($t["default"], "dflt");
                $this->addSelectField($t["visible"], "visible");
                break;
            case 'positive_currency':
                $t  = $tables['positive_currency_formats']['columns'];
                $this->addSelectField($t["id"], "id");
                $this->addSelectField($t["format"], "format");
                break;
            case 'negative_currency':
                $t  = $tables['negative_currency_formats']['columns'];
                $this->addSelectField($t["id"], "id");
                $this->addSelectField($t["format"], "format");
                break;
        }
    }
}

class SELECT_LOCALIZATION_ADDITIONAL_CURRENCIES_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Localization::getTables();
        $t  = $tables['currencies']['columns'];
        $this->addSelectField($t["id"], "id");
        $this->addSelectField($t["name"], "name");
        $this->addSelectField($t["code"], "code");
        $this->addSelectField($t["sign"], "sign");
        $this->addSelectField($t["active"], "active");
        $this->addSelectField($t["default"], "dflt");
        $this->addSelectField($t["visible"], "visible");

        $this->WhereValue($t["active"], DB_EQ, 'true');
    }
}



// ---------------------------------------------------------------
//
//                      UPDATE QUERIES
//
// ---------------------------------------------------------------




/***class UPDATE_LOCALIZATION_SETTINGS extends DB_Update
{
    function initQuery($params)
    {
        $tables = Localization::getTables();
        $columns = $tables['localization_settings']['columns'];

        $this->addUpdateValue($columns['val'], $params['val']);
        $this->WhereValue($columns['key'], DB_EQ, $params['key']);
    }
}***/

class UPDATE_LOCALIZATION_CURRENCY_SIGN extends DB_Update
{
    function UPDATE_LOCALIZATION_CURRENCY_SIGN()
    {
        parent::DB_Update('currencies');
    }

    function initQuery($params)
    {
        $tables = Localization::getTables();
        $columns = $tables['currencies']['columns'];

        $this->addUpdateValue($columns['sign'], $params['sign']);
        $this->WhereValue($columns['id'], DB_EQ, $params['currency_id']);
    }
}

/*** class UPDATE_LOCALIZATION_PATTERN extends DB_Update
{
    function initQuery($params)
    {
        $tables = Localization::getTables();
        $p = $tables['patterns']['columns'];

        $this->addUpdateValue($p['value'], $params['pattern']);
        $this->WhereValue($p['type'], DB_EQ, $params['entity']);
    }
}***/


?>