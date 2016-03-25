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

class SELECT_ACTIVE_MODULE_NAMES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Modules_Manager::getTables();
        $module_columns  = $tables['module']['columns'];

        $this->addSelectTable('module');
        $this->addSelectField($module_columns['name']);
        $this->WhereValue($module_columns['active'], DB_EQ, TRUE);
    }
}

class SELECT_ACTIVE_MODULES extends DB_Select
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
        $this->WhereValue($module_columns['active'], DB_EQ, TRUE);
    }
}

class SELECT_ACTIVE_MODULE_CLASSES extends DB_Select
{
    function initQuery($params)
    {
        $tables = Modules_Manager::getTables();
        $module_class_columns  = $tables['module_class']['columns'];

        $this->addSelectTable('module_class');
        $this->addSelectField($module_class_columns['name']);
        $this->addSelectField($module_class_columns['file']);
        $this->addSelectField($module_class_columns['type']);
        $this->addSelectField($module_class_columns['module_id']);
        $this->WhereValue($module_class_columns['active'], DB_EQ, TRUE);
    }
}

class SELECT_ALL_MODULE_NAMES extends DB_Select
{
	function initQuery($params)
	{
			$tables = Modules_Manager::getTables();
			$module_columns  = $tables['module']['columns'];

			$this->addSelectTable('module');
			$this->addSelectField($module_columns['name']);
			$this->addSelectField($module_columns['active']);
	}
}

class SELECT_ALL_INSTALL_EXTENSION_MODULE extends DB_Select
{
	function initQuery($params)
	{
			$tables = Modules_Manager::getTables();
			$module_columns  = $tables['module']['columns'];

			$this->addSelectTable('module');
			$this->addSelectField('*');
			$this->WhereValue($module_columns['system'], DB_EQ, "E");
	}
}


?>