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

class SELECT_MENU_PARENT extends DB_Select
{
    function initQuery($params)
    {
        $tables = MenuManager::getTables();
        $menu  = $tables['menu_admin']['columns'];

        $this->addSelectField($menu['menu_name'], 'menu_name');
        $this->addSelectField($menu['menu_desc'], 'menu_desc');
        $this->addSelectField($menu['icon_image'], 'icon_image');
        $this->addSelectField($menu['parent'], 'parent');
       	$this->addSelectField($menu['has_subcategory'], 'has_subcategory');
        $this->addSelectField($menu['menu_url'], 'menu_url');
        $this->addSelectField($menu['group_name'], 'group_name');
        $this->addSelectField($menu['new_window'], 'new_window');
        $this->WhereValue($menu['group_name'], DB_EQ, $params['group_name']);
        $this->WhereAnd();
        $this->WhereValue($menu['parent'], DB_EQ, $params['parent']);
		$this->WhereAnd();
		$this->WhereValue($menu['visibility'], DB_EQ, 1);
       	$this->SelectOrder($menu['id'],'ASC');
    }
}

class SELECT_ADMIN_FILES extends DB_Select
{
    function initQuery($params)
    {
        $tables = MenuManager::getTables();
        $page = $tables['admin_pages']['columns'];

        $this->addSelectField($page['id'],'id');
        $this->addSelectField($page['identifier'],'identifier');
        $this->addSelectField($page['classname'],'classname');
        $this->addSelectField($page['title'],'title');
        $this->addSelectField($page['heading'],'heading');
        $this->addSelectField($page['help_identifier'],'help_identifier');
        $this->addSelectField($page['item_value'],'item_value');
		$this->addSelectField($page['onload_js'],'onload_js');
		$this->addSelectField($page['parent'],'parent');
        $this->WhereValue($page['identifier'], DB_EQ, $params['identifier']);
    }
}

?>