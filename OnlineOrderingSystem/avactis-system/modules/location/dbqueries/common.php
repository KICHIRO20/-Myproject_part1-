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

class SELECT_COUNTRIES_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Location::getTables();
        $c  = $tables['countries']['columns'];

        $this->addSelectField($c["id"], "id");
        $this -> setMultiLangAlias('_name', 'countries', $c['name'],
                                   $c['id'], 'Location');
        $this -> addSelectField($this -> getMultiLangAlias('_name'), 'name');
        $this->WhereValue($c["active"], DB_EQ, "true");

        if (!$params['all_data'] && sizeof($params['ExclusionCountries'])>0)
        {
            $this->WhereAnd();
            $this->WhereField($c["id"], DB_NOT." ".DB_IN, "(".implode(",", $params['ExclusionCountries']).")");
        }

        if($params['with_states_only'] == true)
        {
            $s = $tables['states']['columns'];
            $this->addSelectField($this->fCount("*"), "cnt");
            $this->addLeftJoin('states', $s['c_id'], DB_EQ, $c['id']);
            $this->SelectGroup($c["id"]);
            $this->Having("cnt", DB_GT, "1");
        }
    }
}

class SELECT_DEFAULT_COUNTRY_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Location::getTables();
        $c  = $tables['countries']['columns'];

        $this->addSelectField($c["id"], "id");
        $this->WhereValue($c["default"], DB_EQ, "true");
    }
}

class SELECT_DEFAULT_STATE_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Location::getTables();
        $s  = $tables['states']['columns'];

        $this->addSelectField($s["id"], "id");
        $this->WhereValue($s["c_id"], DB_EQ, $params['c_id']);
        $this->WhereAnd();
        $this->WhereValue($s["default"], DB_EQ, "true");
    }
}

class SELECT_STATES_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Location::getTables();
        $s  = $tables['states']['columns'];

        $this->addSelectField($s["id"], "id");
        $this -> setMultiLangAlias('_name', 'states', $s['name'],
                                   $s['id'], 'Location');
        $this -> addSelectField($this -> getMultiLangAlias('_name'), 'name');
        $this->WhereValue($s["active"], DB_EQ, "true");
        $this->WhereAnd();
        $this->WhereValue($s["c_id"], DB_EQ, $params['country_id']);
        if (!$params['all_data'] && sizeof($params['ExclusionStates'])>0)
        {
            $this->WhereAnd();
            $this->WhereField($s["id"], DB_NOT." ".DB_IN, "(".implode(",", $params['ExclusionStates']).")");
        }
    }
}

class SELECT_COUNT_STATES_IN_COUNTRY extends DB_Select
{
    function initQuery($params)
    {
        $tables = Location::getTables();
        $s  = $tables['states']['columns'];

        $this->addSelectField($this->fCount($s["id"]), "count");
        $this->WhereValue($s["c_id"], DB_EQ, $params['country_id']);
    }
}

class SELECT_COUNTRY_BY_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Location::getTables();
        $c  = $tables['countries']['columns'];

        $this -> setMultiLangAlias('_name', 'countries', $c['name'],
                                   $c['id'], 'Location');
        $this -> addSelectField($this -> getMultiLangAlias('_name'), 'name');
        $this->WhereValue($c["id"], DB_EQ, $params['country_id']);
    }
}

class SELECT_STATE_BY_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Location::getTables();
        $s  = $tables['states']['columns'];

        $this -> setMultiLangAlias('_name', 'states', $s['name'],
                                   $s['id'], 'Location');
        $this -> addSelectField($this -> getMultiLangAlias('_name'), 'name');
        $this->WhereValue($s["id"], DB_EQ, $params['state_id']);
    }
}

class SELECT_COUNTRIES_FULL_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Location::getTables();
        $c  = $tables['countries']['columns'];

        $this->addSelectField($c["id"], "id");
        $this -> setMultiLangAlias('_name', 'countries', $c['name'],
                                   $c['id'], 'Location');
        $this -> addSelectField($this -> getMultiLangAlias('_name'), 'name');
        $this->addSelectField($c["code"], "code");
        $this->addSelectField($c["active"], "active");
        $this->addSelectField($c["default"], "dflt");
    }
}

class SELECT_STATES_FULL_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Location::getTables();
        $s  = $tables['states']['columns'];

        $this->addSelectField($s["id"], "id");
        $this -> setMultiLangAlias('_name', 'states', $s['name'],
                                   $s['id'], 'Location');
        $this -> addSelectField($this -> getMultiLangAlias('_name'), 'name');
        $this->addSelectField($s["code"], "code");
        $this->addSelectField($s["active"], "active");
        $this->addSelectField($s["default"], "dflt");
        $this->WhereValue($s["c_id"], DB_EQ, $params['country_id']);
        $this->SelectOrder($s["id"]);
    }
}

class SELECT_COUNTRY_CODE_BY_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Location::getTables();
        $c  = $tables['countries']['columns'];

        if ($params['code']=="ISO2")
        {
            $this->addSelectField($c["code"], "code");
        }
        else
        {
            $this->addSelectField($c["code3"], "code");
        }
        $this->WhereValue($c["id"], DB_EQ, $params['country_id']);
    }
}

class SELECT_STATE_CODE_BY_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Location::getTables();
        $s  = $tables['states']['columns'];

        $this->addSelectField($s["code"], "code");
        $this->WhereValue($s["id"], DB_EQ, $params['state_id']);
    }
}

class SELECT_COUNTRY_CODE_BY_COUNTRY_NAME extends DB_Select
{
    function initQuery($params)
    {
        $tables = Location::getTables();
        $c  = $tables['countries']['columns'];

        if ($params['code']=="ISO2")
        {
            $this->addSelectField($c["code"], "code");
        }
        else
        {
            $this->addSelectField($c["code3"], "code");
        }
        $this -> setMultiLangAlias('_name', 'countries', $c['name'],
                                   $c['id'], 'Location');
        $this -> WhereValue($this -> getMultiLangAlias('_name'), DB_EQ,
                            $params['country_name']);
        $this -> WhereOR();
        $this->WhereValue($c["name"], DB_EQ, $params['country_name']);
    }
}

class SELECT_STATE_CODE_BY_STATE_NAME extends DB_Select
{
    function initQuery($params)
    {
        $tables = Location::getTables();
        $s  = $tables['states']['columns'];

        $this->addSelectField($s["code"], "code");
        $this -> setMultiLangAlias('_name', 'states', $s['name'],
                                   $s['id'], 'Location');
        $this -> WhereValue($this -> getMultiLangAlias('_name'), DB_EQ,
                            $params['state_name']);
        $this -> WhereOR();
        $this->WhereValue($s["name"], DB_EQ, $params['state_name']);
    }
}

class SELECT_COUNTRY_ID_BY_COUNTRY_CODE extends DB_Select
{
    function initQuery($params)
    {
        $tables = Location::getTables();
        $c  = $tables['countries']['columns'];

        $this->addSelectField($c["id"], "id");
        $this->WhereValue($c["code"], DB_EQ, $params['country_code']);
    }
}

class SELECT_STATE_ID_BY_STATE_CODE extends DB_Select
{
    function initQuery($params)
    {
        $tables = Location::getTables();
        $s  = $tables['states']['columns'];

        $this->addSelectField($s["id"], "id");
        $this->WhereValue($s["code"], DB_EQ, $params['state_code']);
    }
}

class UPDATE_COUNTRY_INFO extends DB_Update
{
    function UPDATE_COUNTRY_INFO()
    {
        parent::DB_Update('countries');
    }

    function initQuery($params)
    {
        $tables = Location::getTables();
        $c  = $tables['countries']['columns'];

        $this->addUpdateValue($c["active"], $params['c_active']);
        $this->addUpdateValue($c["default"], $params['c_default']);
        if ($params['c_name'])
        {
            $this->addMultiLangUpdateValue($c['name'], $params['c_name'],
                                           $c['id'], $params['c_id'],
                                           'Location');
        }
        $this->WhereValue($c["id"], DB_EQ, $params['c_id']);
    }
}

class UPDATE_STATE_INFO extends DB_Update
{
    function UPDATE_STATE_INFO()
    {
        parent::DB_Update('states');
    }

    function initQuery($params)
    {
        $tables = Location::getTables();
        $s  = $tables['states']['columns'];

        $this->addUpdateValue($s["active"], $params['s_active']);
        $this->addUpdateValue($s["default"], $params['s_default']);
        if ($params['s_name'])
        {
            $this->addMultiLangUpdateValue($s["name"], $params['s_name'],
                                           $s['id'], $params['s_id'],
                                           'Location');
        }
        $this->WhereValue($s["id"], DB_EQ, $params['s_id']);
    }
}

class SELECT_STATE_ID_BY_STATE_CODE_AND_CID extends DB_Select
{
    function initQuery($params)
    {
        $tables = Location::getTables();
        $s  = $tables['states']['columns'];

        $this->addSelectField($s["id"], "id");
        $this->WhereValue($s["code"], DB_EQ, $params['state_code']);
        $this->WhereAnd();
        $this->WhereValue($s["c_id"], DB_EQ, $params['country_id']);
    }
}

?>