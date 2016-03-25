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
/**
 * Menu Manager module
 *
 * @package MenuManager
 * @author Prasada
 */
class MenuManager
{
	//------------------------------------------------
	//               PUBLIC DECLARATION
	//------------------------------------------------

	/**#@+
	 * @access public
	 */

	/**
	 * Module constructor.
	 */
	function MenuManager()
	{

	}

	function importMenus($csv)
	{
		global $application;
		loadCoreFile('csv_parser.php');
		$csv_parser = new CSV_Parser();

		$tables = MenuManager::getTables();
		$table = 'menu_admin';
		$columns = $tables[$table]['columns'];

		list($flt,$menus) = $csv_parser->parse_file($csv);
		if(count($menus) > 0)
		{
			foreach($menus as $key => $menu)
			{
				$query = new DB_Insert($table);
				$query->addInsertValue($menu["menu_name"], $columns['menu_name']);
				$query->addInsertValue($menu["menu_description"], $columns['menu_desc']);
				$query->addInsertValue($menu["icon_image"], $columns['icon_image']);
				$query->addInsertValue($menu["parent"], $columns['parent']);
				$query->addInsertValue($menu["has_subcategory"], $columns['has_subcategory']);
				$query->addInsertValue($menu["visibility"], $columns['visibility']);
				$query->addInsertValue($menu["menu_url"], $columns['menu_url']);
				$query->addInsertValue($menu["group_name"], $columns['group_name']);
				$query->addInsertValue($menu["new_window"], $columns['new_window']);

				$application->db->getDB_Result($query);
			};
		};
	}

	function removeMenus($csv)
	{
		global $application;
		loadCoreFile('csv_parser.php');
		$csv_parser = new CSV_Parser();

		$tables = MenuManager::getTables();
		$table = 'menu_admin';
		$columns = $tables[$table]['columns'];

		list($flt,$menus) = $csv_parser->parse_file($csv);
		if(count($menus) > 0)
		{
			foreach($menus as $key => $menu)
			{
				$query = new DB_Delete($table);
				$query->WhereValue($columns['menu_name'], DB_EQ, $menu["menu_name"]);
				$application->db->getDB_Result($query);
			};
		};
	}

	function importPages($csv)
	{
		global $application;
		loadCoreFile('csv_parser.php');
		$csv_parser = new CSV_Parser();

		$tables = MenuManager::getTables();
		$table = 'admin_pages';            #the name of the filled table
			$columns = $tables[$table]['columns'];  #the array of field names of the table

			list($flt,$pages) = $csv_parser->parse_file($csv);
		if(count($pages) > 0)
		{
			foreach($pages as $key => $page)
			{
				$query = new DB_Insert($table);
				$query->addInsertValue($page["identifier"], $columns['identifier']);
				$query->addInsertValue($page["classname"], $columns['classname']);
				$query->addInsertValue($page["title"], $columns['title']);
				$query->addInsertValue($page["heading"], $columns['heading']);
				$query->addInsertValue($page["help_identifier"], $columns['help_identifier']);
				$query->addInsertValue($page["item_value"], $columns['item_value']);
				$query->addInsertValue($page["onload_js"], $columns['onload_js']);
				$query->addInsertValue($page["parent"], $columns['parent']);

				$application->db->getDB_Result($query);
			};
		};
	}

	function removePages($csv)
	{
		global $application;
		loadCoreFile('csv_parser.php');
		$csv_parser = new CSV_Parser();

		$tables = MenuManager::getTables();
		$table = 'admin_pages';
		$columns = $tables[$table]['columns'];

		list($flt,$pages) = $csv_parser->parse_file($csv);
		if(count($pages) > 0)
		{
			foreach($pages as $key => $page)
			{
				$query = new DB_Delete($table);
				$query->WhereValue($columns['identifier'], DB_EQ, $page["identifier"]);
				$application->db->getDB_Result($query);
			};
		};
	}

	function install()
	{
		global $application;
		$tables = MenuManager::getTables();
		$query = new DB_Table_Create($tables);

		MenuManager::importMenus(dirname(__FILE__)."/includes/menus.csv");

		MenuManager::importPages(dirname(__FILE__)."/includes/pages.csv");
	}


	function uninstall()
	{
		$query = new DB_Table_Delete(MenuManager::getTables());
		global $application;
		$application->db->getDB_Result($query);
	}


	/**
	 * Gets the array of meta description of module tables.
	 * @todo May be add more tables
	 * @return array - meta description of module tables
	 */
	function getTables()
	{
		static $tables;

		if (is_array($tables))
		{
			return $tables;
		}

		$tables = array();
		$table = 'menu_admin';
		$tables[$table] = array();
		$tables[$table]['columns'] = array (
				'id'            => $table.'.id'
				,'menu_name'     => $table.'.menu_name'
				,'menu_desc'     => $table.'.menu_description'
				,'icon_image'    => $table.'.icon_image'
				,'parent'        => $table.'.parent'
				,'has_subcategory'   => $table.'.has_subcategory'
				,'visibility'    => $table.'.visibility'
				,'menu_url'      => $table.'.menu_url'
				,'group_name'    => $table.'.group_name'
				,'new_window'	 => $table.'.new_window'
				);
		$tables[$table]['types'] = array (
				'id'            => DBQUERY_FIELD_TYPE_INT . ' NOT NULL auto_increment'
				,'menu_name'     => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL'
				,'menu_desc'     => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL'
				,'icon_image'	 => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL'
				,'parent'        => DBQUERY_FIELD_TYPE_CHAR255. ' NOT NULL DEFAULT \'0\''
				,'has_subcategory'   => DBQUERY_FIELD_TYPE_BOOL. ' NOT NULL'
				,'visibility'    => DBQUERY_FIELD_TYPE_BOOL. ' NOT NULL'
				,'menu_url'		 => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL'
				,'group_name'    => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL'
				,'new_window'	 => DBQUERY_FIELD_TYPE_BOOL . ' NOT NULL'
				);
		$tables[$table]['primary'] = array (
				'id'
				);

		$tables[$table]['indexes'] = array
			(
			 'IDX_menu_name' 	=> 'menu_name'
			);


		/* For admin pages - start */
		$table = 'admin_pages';
		//		$tables[$table] = array();
		$tables[$table]['columns'] = array (
				'id'            				=> $table.'.id'
				,'identifier'    				=> $table.'.identifier'
				,'classname'    				=> $table.'.classname'
				,'title'   		 				=> $table.'.title'
				,'heading'      				=> $table.'.heading'
				,'help_identifier'        		=> $table.'.help_identifier'
				,'item_value'       			=> $table.'.item_value'
				,'onload_js'					=> $table.'.onload_js'
				,'parent'						=> $table.'.parent'
				);

		$tables[$table]['types'] = array (
				'id'            			=> DBQUERY_FIELD_TYPE_INT . ' NOT NULL auto_increment'
				,'identifier'    			=> DBQUERY_FIELD_TYPE_CHAR255 . 'NOT NULL'
				,'classname'     			=> DBQUERY_FIELD_TYPE_CHAR255 . 'NOT NULL'
				,'title'   		 			=> DBQUERY_FIELD_TYPE_CHAR255 . 'NOT NULL'
				,'heading'       			=> DBQUERY_FIELD_TYPE_CHAR255 . 'NOT NULL'
				,'help_identifier'    		=> DBQUERY_FIELD_TYPE_CHAR255 . 'NOT NULL'
				,'item_value'       		=> DBQUERY_FIELD_TYPE_CHAR255 . 'NOT NULL'
				,'onload_js'				=> DBQUERY_FIELD_TYPE_CHAR255 . 'NULL'
				,'parent'					=> DBQUERY_FIELD_TYPE_CHAR255 . 'NOT NULL'
				);
		$tables[$table]['primary'] = array (
				'identifier'
				);
		$tables[$table]['indexes'] = array (
				'IDX_id' => 'id'
				);
		/* For admin pages - end */

		global $application;
		return $application->addTablePrefix($tables);
	}


	function getMenus($groupname,$parent)
	{
		$result = execQuery('SELECT_MENU_PARENT',array('group_name' => $groupname,'parent' => $parent));
		return $result;
	}

	function getPages($identifier)
	{
		$result = execQuery('SELECT_ADMIN_FILES',array('identifier'=>$identifier));
		return $result;
	}

	//------------------------------------------------
	//              PRIVATE DECLARATION
	//------------------------------------------------

	/**#@+
	 * @access private
	 */


}
?>