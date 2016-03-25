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

class SELECT_EXTENSION_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Extension_Manager::getTables();
        $excolumns  = $tables['marketplace_ext_data']['columns'];

        //$this->addSelectTable('marketplace_ext_data');
        $this->addSelectField($excolumns['id']);
	$this->addSelectField($excolumns['display_name']);
        $this->addSelectField($excolumns['name']);
        $this->addSelectField($excolumns['desc']);
        $this->addSelectField($excolumns['link']);
        $this->addSelectField($excolumns['price']);
        $this->addSelectField($excolumns['image']);
        $this->addSelectField($excolumns['category']);
        $this->addSelectField($excolumns['type']);
        $this->addSelectField($excolumns['latestversion']);
        $this->addSelectField($excolumns['latestcompatibleversion']);
        $this->addSelectField($excolumns['file']);
    }
}

class SELECT_ALL_MARKETPLACE_EXTENSION_DETAILS extends DB_Select
{
    function initQuery($params)
    {
    	$tables = Modules_Manager::getTables();
        $module = $tables['marketplace_ext_data']['columns'];

        $this->addSelectTable('marketplace_ext_data');
        $this->addSelectField('*');

    }
}

class SELECT_ACTIVE_EXTENSIONS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Modules_Manager::getTables();
        $module_columns  = $tables['module']['columns'];


        $this->addSelectTable('module');
        $this->addSelectField($module_columns['id']);
        $this->addSelectField($module_columns['name']);
        $this->addSelectField($module_columns['groups']);
        $this->addSelectField($module_columns['description']);
        $this->addSelectField($module_columns['version']);
        $this->addSelectField($module_columns['author']);
        $this->addSelectField($module_columns['contact']);
        $this->addSelectField($module_columns['system']);
        $this->addSelectField($module_columns['date']);
        $this->WhereValue($module_columns['system'], DB_EQ, 'E');
    }
}
class SELECT_ACTIVE_EXTENSIONS_CHECK extends DB_Select
{
    function initQuery($params)
    {
        $tables = Modules_Manager::getTables();
        $module_columns  = $tables['module']['columns'];
        $this->addSelectField($module_columns['version']);
        $this->addSelectField($module_columns['groups']);
        $this->addSelectField($module_columns['description']);
        $this->addSelectField($module_columns['version']);
        $this->addSelectField($module_columns['author']);
        $this->addSelectField($module_columns['contact']);
        $this->addSelectField($module_columns['active']);
        $this->addSelectField($module_columns['updated']);
        $this->WhereValue($module_columns['name'], DB_EQ, $params['name']);
        $this->WhereAnd();
        $this->WhereValue($module_columns['system'], DB_EQ, 'E');
    }
}
/* For marketplace extensions - end*/

class UPDATE_EXTENSION_STATUS extends DB_Update{
	function UPDATE_EXTENSION_STATUS(){
		parent::DB_Update('module');
	}
    function initQuery($params)
    {
        $tables =Modules_Manager::getTables();
        $module_columns  =$tables['module']['columns'];

        $this->addUpdateValue($module_columns['active'],$params['active']);
        $this->WhereValue($module_columns['name'], DB_EQ, $params['name']);
        $this->WhereAND();
        $this->WhereValue($module_columns['system'], DB_EQ, "E");
    }
}

?>