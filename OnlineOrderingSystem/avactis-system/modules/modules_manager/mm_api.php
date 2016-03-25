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

loadModuleFile('modules_manager/abstract/module_info.php');
loadModuleFile('modules_manager/dbqueries/common.php');

/**
 * Modules_Manager module.
 * This module is used to control all modules in the system. This module only
 * contains info on other modules. This module only helps to load other modules.
 *
 * @package Modules_Manager
 * @author Alexey Kolesnikov
 */
class Modules_Manager
{
	//------------------------------------------------
	//               PUBLIC DECLARATION
	//------------------------------------------------

	/**#@+
	 * @access public
	 */

	/**
	 * Modules_Manager constructor.
	 */
	function Modules_Manager()
	{
		global $application;

		$this->modules_directory = "/avactis-system/modules/";
		$this->add_modules_directory = "/avactis-extensions/";
		$this->store_dir = $application->getAppIni("PATH_ASC_ROOT");
		include($this->store_dir.$this->add_modules_directory."avactis_hooks.php");
	}

	/**
	 * Returns table meta description for this module.
	 */
	static function getTables()
	{
		$tables = array();

		// main table of module description
		$module = 'module';
		$tables[$module] = array();
		$tables[$module]['columns'] = array (
				'id'            => 'module.module_id'
				,'name'          => 'module.module_name'
				,'groups'        => 'module.module_groups'
				,'description'   => 'module.module_description'
				,'version'       => 'module.module_version'
				,'author'        => 'module.module_author'
				,'contact'       => 'module.module_contact'
				,'system'        => 'module.module_system'
				,'date'          => 'module.module_date'
				,'active'        => 'module.module_active'
				,'updated'       => 'module.module_updated'
				);
		$tables[$module]['types'] = array (
				'id'            => DBQUERY_FIELD_TYPE_INT . ' NOT NULL auto_increment'
				,'name'          => DBQUERY_FIELD_TYPE_CHAR50 . ' NOT NULL DEFAULT \'\''
				,'groups'        => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
				,'description'   => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
				,'version'       => DBQUERY_FIELD_TYPE_CHAR20 . ' NOT NULL DEFAULT \'\''
				,'author'        => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
				,'contact'       => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
				,'system'        => DBQUERY_FIELD_TYPE_CHAR1 . ' NOT NULL default 0'
				,'date'          => DBQUERY_FIELD_TYPE_DATE . ' NOT NULL default \'0000-00-00\''
				,'active'        => DBQUERY_FIELD_TYPE_CHAR1 . ' NOT NULL default 0'
				,'updated'       => DBQUERY_FIELD_TYPE_CHAR1 . ' NOT NULL default 1'
				);
		$tables[$module]['primary'] = array (
				'id'
				);
		$tables[$module]['indexes'] = array (
				//             'MM_modules_name' => 'name'
				);

		// a table of class description
		$module_class = 'module_class';
		$tables[$module_class] = array();
		$tables[$module_class]['columns'] = array (
				//             'id'        => 'module_class.module_class_id'
				'module_id' => 'module_class.module_id'
				,'name'      => 'module_class.module_class_name'
				,'file'      => 'module_class.module_class_file'
				,'type'      => 'module_class.module_class_type'
				,'active'    => 'module_class.module_class_active'
				);
		$tables[$module_class]['types'] = array (
				//             'id'        => DBQUERY_FIELD_TYPE_INT . ' NOT NULL auto_increment'
				'module_id' => DBQUERY_FIELD_TYPE_INT . ' NOT NULL'
				,'name'      => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
				,'file'      => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
				,'type'      => DBQUERY_FIELD_TYPE_CHAR20 . ' NOT NULL DEFAULT \'\''
				,'active'    => DBQUERY_FIELD_TYPE_CHAR1 . ' NOT NULL DEFAULT \'0\''
				);
		$tables[$module_class]['primary'] = array (
				//            'id'
				);
		$tables[$module_class]['indexes'] = array (
				'MM_module_class_module_id' => 'module_id'
				//           ,'MM_module_class_name' => 'name'
				);

		global $application;
		return $application->addTablePrefix($tables);
	}

	/**
	 * Installs the specified module in the system.
	 *
	 * The install() method is called statically.
	 * To call other methods of this class from this method,
	 * the static call is used, for example,
	 * Modules_Manager::getTables() instead of $this->getTables().
	 */
	function install()
	{
		global $application;
		$tables = Modules_Manager::getTables();

		$query = new DB_Table_Create($tables);
	}

	/**
	 * Uninstalls the module.
	 *
	 * The uninstall() method is called statically.
	 * To call other methods of this class from this method,
	 * the static call is used, for example,
	 * Modules_Manager::getTables() instead of $this->getTables().
	 */
	function uninstall()
	{
		global $application;
		$tables = Modules_Manager::getTables();

		$application->db->DB_Query("DROP TABLE module_class");
		$application->db->DB_Query("DROP TABLE module");
	}




	/**
	 * Installs the specified module in the system.
	 *
	 * @param ModuleInfo $moduleInfo
	 */
	function installModule($moduleInfo)
	{
		if ($this->isModuleInstalled($moduleInfo->name))
		{
			return;
		}
		//                                     ,             api
		$this->includeAPIFileOnce($moduleInfo->name);

		// start module insatllation,
		// use the static method call install()
		call_user_func(array($moduleInfo->name,'install'));

		$tables = $this->getTables();

		$module_tbl            = 'module';
		$module_columns        = $tables[$module_tbl]['columns'];

		// Save main module info
		$arr = array
			(
			 $module_columns['name']         => $moduleInfo->name
			 ,$module_columns['groups']       => $moduleInfo->groups
			 ,$module_columns['description']  => $moduleInfo->description
			 ,$module_columns['version']      => $moduleInfo->version
			 ,$module_columns['author']       => $moduleInfo->author
			 ,$module_columns['contact']      => $moduleInfo->contact
					/** For extensions if the extension in avactis-extensions directory**/
			 ,$module_columns['system']       => strpos($this->apiFiles[_ml_strtolower($moduleInfo->name)],$this->add_modules_directory)>-1?'E':$moduleInfo->systemModule
			 ,$module_columns['active']       => TRUE
			 ,$module_columns['updated']      => TRUE
			);
		$moduleId = $this->dbInsert($module_tbl, $arr);

		$this->insertModuleClassInfo($moduleId, $moduleInfo);
		CCacheFactory::clearAll();
	}
	/*
	 * This function is used to execute the sql file(s) for upgrade
	 */
	function executeUpgradeSQL($oldModuleVersion,$upgradeFolder)
	{
		global $application;
		//If module is not installed then don't execute the upgrade script. Install module should handle.
		if ($oldModuleVersion <=0){
			return;
		}

		if(is_dir($upgradeFolder))
		{
			$sqlFiles = scandir($upgradeFolder);
			foreach($sqlFiles as $file)
			{
				if(strpos($file,".sql")!==false)
				{
					$sqlversion = str_replace(".sql","",$file);
					if(version_compare($oldModuleVersion,$sqlversion) < 0)
					{
						$table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
						$result  = modApiFunc("DB_MySQL", "importData",$upgradeFolder.$file, $table_prefix, "", "");
						CCacheFactory::clearAll();
					}
				}
			}
		}
	}

	/*
	  This function is not used anywhere. It will be removed in next release.
	* This function is called for executing any upgrade script of the extension during extension installation

	function runUpgrade($moduleInfo)
	{
		global $application;

		$oldVersionexplode = explode(".",$this->getModuleVersion($moduleInfo->name));
		$oldModuleVersion = $oldVersionexplode[2];

		$upgradeFolder = $this->store_dir.$moduleInfo->directory."/upgrade/";
		$this->executeUpgradeSQL($oldModuleVersion,$upgradeFolder);
	}
	*/

	/**
	 * Updates the specified module in the system.
	 *
	 * @param ModuleInfo $moduleInfo
	 */
	function updateModule($moduleInfo)
	{
		global $application;

		if(version_compare($this->getModuleVersion($moduleInfo->name), $moduleInfo->version) >= 0)
		{
			return;
		}

 		/** check any table changes in the api **/
		$this->includeAPIFileOnce($moduleInfo->name);
		/** Call to update function in the api
		 *
		 * Structure of UPDATE Module
		 * function update(){
		 * 		avactis_db_delta(Extension_Name::getTables());
		 *      or if supportable
		 *      avactis_db_delta($this->getTables());
		 *
		 *  }
		 *  if you are using plain queries you can use
		 *
		 *  The structure of plain query should be:
		 *  function getQueries(){
		 *  	global $application
		 *  	$table_prefix=$application->getAppIni('DB_TABLE_PREFIX');
		 *  	$queries="
		 *  	CREATE TABLE {$table_prefix}.avactis_attribute_taxonomies (
		 *  	attribute_id bigint(20) NOT NULL auto_increment,
		 *  	attribute_name varchar(200) NOT NULL,
		 *  	attribute_label longtext NULL,
		 *  	attribute_type varchar(200) NOT NULL,
		 *  	attribute_orderby varchar(200) NOT NULL,
		 *  	 PRIMARY KEY  (attribute_id),
		 *  	KEY attribute_name (attribute_name)
		 *  	);";
		 *  	return $queries;
		 *  }
		 *  function update(){
		 *  	do_dbDelta(Extension_Name::getQueries());
		 *      or if supportable
		 *      do_dbDelta($this->getQueries());
		 *
		 *  }
		 */
		call_user_func(array($moduleInfo->name,'update'));

		$tables = $this->getTables();

		$module_tbl            = 'module';
		$module_columns        = $tables[$module_tbl]['columns'];
		$module_class_tbl      = 'module_class';
		$module_class_columns  = $tables[$module_class_tbl]['columns'];

		// Update main module info
		$query = new DB_Update($module_tbl);
		$query->addUpdateValue($module_columns['groups'], $moduleInfo->groups);
		$query->addUpdateValue($module_columns['description'], $moduleInfo->description);
		$query->addUpdateValue($module_columns['version'], $moduleInfo->version);
		$query->addUpdateValue($module_columns['author'], $moduleInfo->author);
		$query->addUpdateValue($module_columns['contact'], $moduleInfo->contact);
		$query->addUpdateValue($module_columns['system'], strpos($this->apiFiles[_ml_strtolower($moduleInfo->name)],$this->add_modules_directory)?'E':$moduleInfo->systemModule);
		$query->addUpdateValue($module_columns['updated'], TRUE);
		$query->WhereValue($module_columns['name'], DB_EQ, $moduleInfo->name);
		$application->db->getDB_Result($query);

		// get module_id
		$query = new DB_Select();
		$query->addSelectTable($module_tbl);
		$query->addSelectField($module_columns['id']);
		$query->WhereValue($module_columns['name'], DB_EQ, $moduleInfo->name);
		list(list($moduleId)) = $application->db->getDB_Result($query, QUERY_RESULT_NUM);

		// delete all records from the table module_class that is refferred to the current module
		$query = new DB_Delete($module_class_tbl);
		$query->WhereValue($module_class_columns['module_id'], DB_EQ, $moduleId);
		$application->db->getDB_Result($query);

		$this->insertModuleClassInfo($moduleId, $moduleInfo);

/*
//	Extesntion upgrade should be handled by extension itself.
//	So database upgrade queries should be handled by extentions's 'update' function.

		$upgradeFolder = $this->store_dir.$moduleInfo->directory."/upgrade/";
		$this->executeUpgradeSQL($oldModuleVersion,$upgradeFolder);
*/
	}

	/**
	 * Fills out the table module_class
	 */
	function insertModuleClassInfo($moduleId, $moduleInfo)
	{
		global $application;
		$tables = $this->getTables();

		$module_class_tbl      = 'module_class';
		$module_class_columns  = $tables[$module_class_tbl]['columns'];

		// save info on the main file of the API module
		$arr = array
			(
			 $module_class_columns['module_id']  => $moduleId
			 ,$module_class_columns['name']       => $moduleInfo->name
			 ,$module_class_columns['file']       => $moduleInfo->mainFile
			 ,$module_class_columns['type']       => 'api'
			 ,$module_class_columns['active']     => TRUE
			);
		$this->dbInsert($module_class_tbl, $arr);

		// save info about all actions
		foreach ($moduleInfo->actionFiles as $actionName => $actionFile)
		{
			$arr = array
				(
				 $module_class_columns['module_id'] => $moduleId
				 ,$module_class_columns['name']      => $actionName
				 ,$module_class_columns['file']      => $actionFile
				 ,$module_class_columns['type']      => 'action'
				 ,$module_class_columns['active']    => TRUE
				);
			$this->dbInsert($module_class_tbl, $arr);
		}

		// save info about all view in the CustomerZone
		foreach ($moduleInfo->czViewFiles as $viewName => $viewFile)
		{
			$arr = array
				(
				 $module_class_columns['module_id'] => $moduleId
				 ,$module_class_columns['name']      => $viewName
				 ,$module_class_columns['file']      => $viewFile
				 ,$module_class_columns['type']      => 'view_cz'
				 ,$module_class_columns['active']    => TRUE
				);
			$this->dbInsert($module_class_tbl, $arr);
		}

		// save info about all view in the AdminZone
		foreach ($moduleInfo->azViewFiles as $viewName => $viewFile)
		{
			$arr = array
				(
				 $module_class_columns['module_id'] => $moduleId
				 ,$module_class_columns['name']      => $viewName
				 ,$module_class_columns['file']      => $viewFile
				 ,$module_class_columns['type']      => 'view_az'
				 ,$module_class_columns['active']    => TRUE
				);
			$this->dbInsert($module_class_tbl, $arr);
		}
	}

	/**
	 * Reinstalls this module in the system.
	 *
	 * @ it's more a service function, which will be used in creating.
	 * @  backup is not supported!
	 * @param ModuleInfo $moduleInfo
	 */
	function reinstallModule($moduleInfo)
	{
		if ($moduleInfo->name == 'Modules_Manager')
		{
			return; // it can't reinstall itself
		}
		$this->uninstallModule($moduleInfo);
		$this->installModule($moduleInfo);
	}

		/**
		 * @ describe the function Modules_Manager->uninstallModule.
		 * @param ModuleInfo $moduleInfo
		 */
		function uninstallModule($moduleInfo)
		{
			global $application;

			if ($moduleInfo->name == 'Modules_Manager')
			{
				return; //  it can't reinstall itself
			}
			// start module insatllation,
			// use the static method call install()
			$modname = $moduleInfo->name;
			$this->includeAPIFileOnce($modname);
			call_user_func(array($modname,'uninstall'));

			//  delete from the specified module list
			$tables = $this->getTables();

			$module_tbl            = 'module';
			$module_columns        = $tables[$module_tbl]['columns'];
			$module_class_tbl      = 'module_class';
			$module_class_columns  = $tables[$module_class_tbl]['columns'];

			$db_select = new DB_Select();
			$db_select->addSelectTable($module_tbl);
			$db_select->addSelectField($module_columns['id']);
			$db_select->WhereValue($module_columns['name'], DB_EQ, $moduleInfo->name);
			list(list($moduleId)) = $application->db->getDB_Result($db_select, QUERY_RESULT_NUM);

			$getAllExtensionTables = array_keys($modname::getTables());

			foreach($getAllExtensionTables as $table)
			{
				if (DB_MySQL::DB_isTableExists($application->getAppIni('DB_TABLE_PREFIX').$table))
				{
					$del_query = new DB_Table_Delete($table);
					$application->db->getDB_Result($del_query);
				}
			}

			/*Remove extension directory if this is an extension -- BEGIN */
			$directory = $application->getAppIni('PATH_ADD_MODULES_DIR').strtolower($moduleInfo->name)."/";
			if (is_dir($directory))
			{
				 modApiFunc('Shell','removeDirectory',($directory));
			}
			/*Remove extension directory if this is an extension -- END */
			// delete all linked records from module_class
			$db_delete = new DB_Delete($module_class_tbl);
			$db_delete->WhereValue($module_class_columns['module_id'], DB_EQ, $moduleId);
			$application->db->getDB_Result($db_delete);

			// delete a record from module
			$db_delete = new DB_Delete($module_tbl);
			$db_delete->WhereValue($module_columns['id'], DB_EQ, $moduleId);
			$application->db->getDB_Result($db_delete);
			CCacheFactory::clearAll();
		}

		/**
		 * Returns TRUE if the module with the specified name is installed in
		 * the system.
		 */
		function isModuleInstalled($moduleName, $force_update=false)
		{
			global $application;
			$tables = Modules_Manager::getTables();

			$module_tbl            = 'module';
			$module_columns        = $tables[$module_tbl]['columns'];
			$module_class_tbl      = 'module_class';
			$module_class_columns  = $tables[$module_class_tbl]['columns'];

			if (!DB_MySQL::DB_isTableExists($application->getAppIni('DB_TABLE_PREFIX').$module_tbl))
			{
				return false;
			}

			static $installed_modules = null;

			if ($force_update)
				$installed_modules = null;

			if ($installed_modules == null)
			{
				$result = execQuery('SELECT_ALL_MODULE_NAMES',array());

				$installed_modules = array();
				foreach($result as $resultItem)
				{
					$installed_modules[] = _ml_strtolower($resultItem['module_name']);
				}
			}

			if (in_array(_ml_strtolower($moduleName), $installed_modules))
			{
				return true;
			}
			else
			{
				return false;
			}
		}



		/**
		 * Returns Module's version if the module with the specified name is installed.
		 */
		function getModuleVersion($moduleName)
		{
			global $application;
			$tables = $this->getTables();

			$module_tbl            = 'module';
			$module_columns        = $tables[$module_tbl]['columns'];

			if (!DB_MySQL::DB_isTableExists($application->getAppIni('DB_TABLE_PREFIX').$module_tbl))
			{
				// the table 'module' doesn't exist, so update should not be performed
				return '0.0.0';
			}

			static $updated_modules = null;
			if ($updated_modules == null)
			{
				$db_select = new DB_Select();
				$db_select->addSelectTable($module_tbl);
				$db_select->addSelectField($module_columns['version'], 'version');
				$db_select->WhereValue($module_columns['name'], DB_EQ,$moduleName );
				$result = $application->db->getDB_Result($db_select);
				$updated_modules = array();
				foreach($result as $resultItem)
				{
					$resultversion[] = $resultItem['version'];
				}
			}
			if (!empty($resultversion))
				return $resultversion[0];
			else
				return '0.0.0';
		}

		/**
		 *
		 *
		 * @return null
		 */
		function initModules()
		{
			global $application, $zone;

			$lang = _ml_strtolower($application->getAppIni('LANGUAGE'));
			$moduleListCache = $application->getMMCache();

			if(isset($_SERVER['QUERY_STRING']) and $_SERVER['QUERY_STRING']=='update_all_modules')
			{
				CCacheFactory::clearAll();
				$this->resetAllModulesStatus();
				$_die_after_init = true;
			}
			else
			{
				$_die_after_init = false;
			};

			if (!$this->isModuleInstalled('Modules_Manager'))
			{
				$this->installModule($this->getModuleInfoFromFile($this->modules_directory.'Modules_Manager'));
				CCacheFactory::clearAll();
			}
			else
			{
				/* Upgrade Main Store SQL- start*/
				$mmInfo = $this->getModuleInfoFromFile($this->modules_directory.'Modules_Manager');
				if(version_compare($this->getModuleVersion($mmInfo->name), $mmInfo->version) < 0)
				{
					$oldVersionexplode = explode(".",$this->getModuleVersion($mmInfo->name));
					$oldModuleVersion = $oldVersionexplode[2];
					$upgradeFolder = $application->appIni['PATH_SYSTEM_DIR']."dbupdates/";
					$this->executeUpgradeSQL($oldModuleVersion,$upgradeFolder);
				}
				/* Upgrade Main Store SQL-end */
			}
			if (!$this->isModuleInstalled('Resources'))
			{
				$this->installModule($this->getModuleInfoFromFile($this->modules_directory.'Resources'));

				// aquire system and CZ messages
				modApiFunc("Resources", "addResourceIniToDB", $application->getAppIni('PATH_ADMIN_RESOURCES').'system-messages-'.$lang.'.ini', 'SYS', 'system_messages', 'AZ');
				$_path = dirname(dirname(dirname(dirname(__FILE__)))).'/avactis-themes/system/resources/messages.ini';
				modApiFunc("Resources", "addResourceIniToDB", $_path, 'CZ', 'customer_messages', 'CZ');

				CCacheFactory::clearAll();
			}

			if (!$this->isModuleInstalled('MultiLang'))
			{
				modApiFunc("Resources", "addResourceIniToDB", $application->getAppIni('PATH_ADMIN_RESOURCES').'multilang-messages-'.$lang.'.ini', 'ML', 'MultiLang', 'AZ');
				$this->installModule($this->getModuleInfoFromFile($this->modules_directory.'multilang'));
				$this->isModuleInstalled('MultiLang', true); //                  ,
				CCacheFactory::clearAll();
			}

			if (!$this->isModuleInstalled('Configuration'))
			{
				modApiFunc("Resources", "addResourceIniToDB", $application->getAppIni('PATH_ADMIN_RESOURCES').'configuration-messages-'.$lang.'.ini', 'CFG', 'Configuration', 'AZ');
				$this->installModule($this->getModuleInfoFromFile($this->modules_directory.'configuration'));
				$this->isModuleInstalled('Configuration', true); //                  ,
				CCacheFactory::clearAll();
			}

			if (!$this->isModuleInstalled('EventsManager'))
			{
				$this->installModule($this->getModuleInfoFromFile($this->modules_directory.'eventsmanager'));
				$this->isModuleInstalled('EventsManager', true); //                  ,
				CCacheFactory::clearAll();
			}

			if ($application->db->DB_isTableExists($application->getAppIni('DB_TABLE_PREFIX')."sessions") == false)
			{
				$tables['sessions'] = array();
				$tables['sessions']['columns'] = array (
						'ses_id'       => 'sessions.ses_id'
						,'ses_time'     => 'sessions.ses_time'
						,'ses_value'    => 'sessions.ses_value'
						,'ses_locked'   => 'sessions.ses_locked'
						);
				$tables['sessions']['types'] = array (
						'ses_id'       => DBQUERY_FIELD_TYPE_CHAR32 . ' NOT NULL DEFAULT \'\''
						,'ses_time'     => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
						,'ses_value'    => DBQUERY_FIELD_TYPE_LONGTEXT . ' NOT NULL '
						,'ses_locked'   => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT 0'
						);
				$tables['sessions']['primary'] = array (
						'ses_id'
						);
				$tables['sessions']['indexes'] = array (
						'IDX_time' => 'ses_time',
						'IDX_locked' => 'ses_locked'
						);

				$query = new DB_Table_Create($application->addTablePrefix($tables));
			}


			$this->moduleList = $moduleListCache->read('ModulesList');
			$this->apiFiles = $moduleListCache->read('apiFiles');
			$this->modulesResFiles = $moduleListCache->read('modulesResFiles');
			$this->shortNamesToResFiles = $moduleListCache->read('shortNamesToResFiles');
			$this->actionList = $moduleListCache->read('actionList');
			$this->czViewList = $moduleListCache->read('czViewList');
			$this->azViewList = $moduleListCache->read('azViewList');
			$this->czAliasesList = $moduleListCache->read('czAliasesList');
			$this->hookList = $moduleListCache->read('hookList');
			$this->blocksList = $moduleListCache->read('blocksList');
			$this->SectionByView = $moduleListCache->read('SectionByView');
			$this->ViewBySection = $moduleListCache->read('ViewBySection');
			$this->storefrontLayout = $moduleListCache->read('storefrontLayout');
			if (!$this->hookList)
				$this->hookList = array();

			if (! isset($this->moduleList))
			{
				$this->moduleList = array();
				/** installed Extensions Array **/
				$installedExtensionArray=$this->getInstalledExtensionModuleInfo();

				/* Processing for avactis-extensions directory-Begin*/
				$dir = @dir($application->getAppIni("PATH_ADD_MODULES_DIR"));
				if(is_dir($application->getAppIni("PATH_ADD_MODULES_DIR")) && is_readable($application->getAppIni("PATH_ADD_MODULES_DIR"))){
					while ($file = $dir->read())
					{
						if ($file != '..' && $file != '.' && is_dir($application->getAppIni("PATH_ADD_MODULES_DIR") . $file))
						{
							$moduleInfo = $this->getModuleInfoFromFile(($this->add_modules_directory . $file));
							$isExtensionNotInstalled=true;
							if(isset($installedExtensionArray[$moduleInfo->name])){
								$isExtensionNotInstalled=$installedExtensionArray[$moduleInfo->name]['module_active'];
							}
							if ($isExtensionNotInstalled && $moduleInfo != null)
							{

								$this->moduleList[$moduleInfo->name] = $moduleInfo;

							}
						}
					}
				}
				/* Processing for avactis-extensions directory-End*/
				$dir = @dir($application->getAppIni("PATH_MODULES_DIR"));
				while ($file = $dir->read())
				{
					if ($file != '..' && $file != '.' && !is_dir($application->getAppIni("PATH_ADD_MODULES_DIR"). $file) && is_dir($application->getAppIni("PATH_MODULES_DIR"). $file) )
					{
						$moduleInfo = $this->getModuleInfoFromFile(($this->modules_directory . $file));
						if ($moduleInfo != null)
						{
							//
							$this->moduleList[$moduleInfo->name] = $moduleInfo;
						}
					}
				}

				asort($this->moduleList);

				foreach($this->moduleList as $module_name=>$moduleInfo)
				{

					// check if the files of the module exist.
					$this->checkModuleFiles($moduleInfo);


					// save the class name of the module and the whole path to its main file in the list
					$this->apiFiles[_ml_strtolower($moduleInfo->name)] = $moduleInfo->mainFile;
					// save all extra API classes and their files
					if ( is_array($moduleInfo->extraAPIFiles))
					{
						foreach($moduleInfo->extraAPIFiles as $class=>$file)
							$this->apiFiles[_ml_strtolower($class)] = $file;
					}

					if(($moduleInfo->shortName != null) and isset($this->modulesResFiles[$moduleInfo->shortName]))
					{
						unset($this->moduleList[$moduleInfo->name]);
						CTrace::err('Duplicate Short Name '.$moduleInfo->shortName);
						continue;
					}

					$res_file_suffix = '-'._ml_strtolower($application->getAppIni('LANGUAGE')).'.ini';
					$res_files_common_location = $application->getAppIni('PATH_ADMIN_RESOURCES');
					if (($moduleInfo->resFile != null) and ($moduleInfo->shortName != null))
					{
						/*
						 *        $this->modulesResFiles
						 *                        .                                                   .
						 */
						$res_file_in_module = $this->store_dir.$moduleInfo->directory.'resources/'.$moduleInfo->resFile.$res_file_suffix;
						$res_file_in_common_location = $res_files_common_location.$moduleInfo->resFile.$res_file_suffix;

						if ($moduleInfo->shortName == null)
						{
							_fatal("Module {$moduleInfo->name} has no short name.");
						}
						if (isset($this->shortNamesToResFiles[$moduleInfo->resFile]))
						{
							_fatal("Duplicate Res File for {$moduleInfo->name}.");
						}
						else
						{
							$this->shortNamesToResFiles[$moduleInfo->resFile] = $moduleInfo->shortName;
						}

						if (file_exists($res_file_in_module))
						{
							$this->modulesResFiles[$moduleInfo->shortName] = $res_file_in_module;
						}
						else if (file_exists($res_file_in_common_location))
						{
							$this->modulesResFiles[$moduleInfo->shortName] = $res_file_in_common_location;
						}

					}

					//                                   API              .
					//                                  .
					if (PRODUCT_VERSION_TYPE == 'TRUNK')
						$this->checkModuleAPIClass($moduleInfo);

					//                   ,
					//                                ,                       .
					// get text resources from the res file and put it into the database
					if (!$this->isModuleInstalled($moduleInfo->name))
					{
						if (!isset($moduleInfo->shortName))
							_fatal("No short name for {$moduleInfo->name}!");
						
							
							  if (isset($this->modulesResFiles[$moduleInfo->shortName]) && file_exists($this->modulesResFiles[$moduleInfo->shortName]))
							  
						{
							modApiFunc("Resources", "dropMessageMetaByMetaId", $moduleInfo->shortName);
							modApiFunc("Resources", "dropMessageGroupByMetaId", $moduleInfo->shortName);
							modApiFunc("Resources", "addResourceIniToDB", $this->modulesResFiles[$moduleInfo->shortName], $moduleInfo->shortName, $moduleInfo->name, 'AZ');
						}
							  else
							  {
								  // no res file for module
							  }
					}
					$this->installModule($moduleInfo);

					// update       ,
					//       ,                modules               updated                   .
					$this->updateModule($moduleInfo);

					//                       actions
					foreach ($moduleInfo->actionFiles as $actionName => $actionFile)
					{
						$this->actionList[$actionName] =  $actionFile;
					}

					// save the list of all views for CustomerZone
					foreach ($moduleInfo->czViewFiles as $viewName => $viewFile)
						$this->czViewList[$viewName] = $viewFile;

					// save the list of all views for AdminZone
					foreach ($moduleInfo->czAliases as $alias => $viewName)
						$this->czAliasesList[$alias] = $viewName;

					// save the list of all views for AdminZone
					foreach ($moduleInfo->azViewFiles as $viewName => $viewFile)
						$this->azViewList[$viewName] = $viewFile;

					// save a list of all hooks
					foreach ($moduleInfo->hookMap as $hookName => $actionList)
					{
						foreach ($actionList as $actionName)
						{
							if (!key_exists($actionName, $this->hookList))
								$this->hookList[$actionName] = array();
							$this->hookList[$actionName][$hookName] = $moduleInfo->hookFiles[$hookName];
						}
					}

                // save a list of blocks
                if(file_exists($this->store_dir.$moduleInfo->directory.'blocks.yml'))
                    $this->blocksList[] = $this->store_dir.$moduleInfo->directory.'blocks.yml';
                // sections by view
                if (!empty($moduleInfo -> SectionByView))
                    $this -> SectionByView = $moduleInfo -> SectionByView;

                // views by section
                if (!empty($moduleInfo -> ViewBySection))
                    $this -> ViewBySection = $moduleInfo -> ViewBySection;

                // storefront Layout addition
                if ($moduleInfo->storefrontLayout
                    && file_exists($this->store_dir.$moduleInfo->directory.$moduleInfo->storefrontLayout))
                {
                    if (!$this -> storefrontLayout)
                        $this -> storefrontLayout = array();
                    $this -> storefrontLayout = array_merge(
                        $this -> storefrontLayout,
                        _parse_ini_file($this->store_dir.$moduleInfo->directory.$moduleInfo->storefrontLayout, true)
                    );
                }
            }

            if (PRODUCT_VERSION_TYPE != 'TRUNK') {
                $moduleListCache->write('ModulesList', $this->moduleList);
                $moduleListCache->write('apiFiles', $this->apiFiles);
                $moduleListCache->write('modulesResFiles', $this->modulesResFiles);
                $moduleListCache->write('shortNamesToResFiles', $this->shortNamesToResFiles);
                $moduleListCache->write('actionList', $this->actionList);
                $moduleListCache->write('czViewList', $this->czViewList);
                $moduleListCache->write('azViewList', $this->azViewList);
                $moduleListCache->write('czAliasesList', $this->czAliasesList);
                $moduleListCache->write('hookList', $this->hookList);
                $moduleListCache->write('blocksList', $this->blocksList);
                $moduleListCache->write('SectionByView', $this->SectionByView);
                $moduleListCache->write('ViewBySection', $this->ViewBySection);
                $moduleListCache->write('storefrontLayout', $this->storefrontLayout);
            }
        }
        else {
            CTrace::inf('Bypass loading of module list (use cached).');
        }

        if (! defined('COMPILED_MODULES_LOADED')) {
            foreach ($this->moduleList as $module_name => $moduleInfo) {
                // if the file of module constants is specfifed, then load it.
		    if ($moduleInfo->constantsFile !== null) {
			    $this->includeFile( $moduleInfo->constantsFile);
		    }


		// load common db queries
		$common_db_queries_file = $moduleInfo->directory.'dbqueries/common.php';
                if (file_exists($this->store_dir.$common_db_queries_file)) {
			$this->includeFile($common_db_queries_file);
                }
		//load extension default hooks
		if ($moduleInfo->ext_def_hooks!==null){
			$this->includeFile($moduleInfo->ext_def_hooks);
		}
		$this->includeFile($moduleInfo->directory.'/asc-hooks.php');
            }
        }
        else {
            CTrace::inf('Bypass constants and queries including (use precompiled).');
        }

        if (! defined('MODULES_VIEWS_REGISTERED')) {
            if ($zone == 'CustomerZone') {
                foreach ($this->moduleList as $module_name => $moduleInfo) {
                    $this->registerViewList($moduleInfo->czViewFiles);
                    $this->registerAliasesList($moduleInfo->czAliases);
                }
            }
            else {
                foreach ($this->moduleList as $module_name => $moduleInfo) {
                    $this->registerViewList($moduleInfo->azViewFiles);
                }
            }
        }
        else {
            CTrace::inf('Bypass modules views registering (use precompiled).');
        }

        if($_die_after_init === true)
        {
            die('Message');
        };
    }

    /**
     *                        ModuleInfo.
     *                       ,                 NULL.
     */
    function getModuleInfoByName($module_name)
    {
        return getKeyIgnoreCase($module_name, $this->moduleList);
    }

    /**
     * Returns the array of active modules from the DB.
     * The array $groups contains a list of groups, each of them must
     * include each returned module.
     * @return array(ModuleInfo)
     */
    function getActiveModules($groups = NULL)
    {
        global $application;
        $tables = $this->getTables();

        $module_tbl            = 'module';
        $module_columns        = $tables[$module_tbl]['columns'];
        $module_class_tbl      = 'module_class';
        $module_class_columns  = $tables[$module_class_tbl]['columns'];

        static $modules = null;
        if ($modules == null)
        {
            $modules = execQuery('SELECT_ACTIVE_MODULES',array());
        }

        static $modules_info = null;
        if ($modules_info == null)
        {
            //
            $classes = execQuery('SELECT_ACTIVE_MODULE_CLASSES',array());

            $modules_info = array();
            foreach($classes as $classItem)
            {
                $modules_info[$classItem['module_id']][] = $classItem;
            }
            unset($classes);
        }


        $listModules = array();

        // load info about each module
        foreach ($modules as $module)
        {
            $moduleInfo = new ModuleInfo();
            $moduleInfo->id = $module['module_id']; //$mId;
            $moduleInfo->name = $module['module_name']; //$mName;
            $moduleInfo->groups = $module['module_groups']; //$mGroups;
		$moduleInfo->directory = $application->getAppIni("PATH_ADD_MODULES_DIR"). _ml_strtolower($module['module_name']) /*$mName*/ . '/';
		if(!is_dir($moduleInfo->directory)){
		        $moduleInfo->directory = $this->modules_directory._ml_strtolower($module['module_name']) /*$mName*/ . '/';
        	}else{
			$moduleInfo->directory = $this->add_modules_directory._ml_strtolower($module['module_name']) /*$mName*/ . '/';
		}
            $moduleInfo->description = $module['module_description']; //$mDescription;
            $moduleInfo->version = $module['module_version']; //$mVersion;
            $moduleInfo->author = $module['module_author']; //$mAuthor;
            $moduleInfo->contact = $module['module_contact']; //$mContact;
            $moduleInfo->systemModule = $module['module_system'] /*$mSystem*/ == '1' ? true : false;

            //Check if all groups from $groups exist in the group list,
            // that include a module.
            if(!empty($groups) && sizeof($groups) > 0)
            {
                $moduleGroups = explode(',', $moduleInfo->groups);
                foreach($groups as $group)
                {
                    if(!in_array($group, $moduleGroups))
                    {
                        continue 2;
                    };
                }
            }

            $classes = isset($modules_info[$module['module_id']]) ? $modules_info[$module['module_id']] : array();

            foreach ($classes as $class)
            {
                $mcName = $class['module_class_name'];
                $mcFile = $class['module_class_file'];
                $mcType = $class['module_class_type'];

                switch ($mcType)
                {
                    case 'api':
                        $moduleInfo->mainFile = $mcFile;
                        break;

                    case 'action':
                        $moduleInfo->actionFiles[$mcName] = $mcFile;
                        break;

                    case 'view_cz':
                        $moduleInfo->czViewFiles[$mcName] = $mcFile;
                        break;

                    case 'view_az':
                        $moduleInfo->azViewFiles[$mcName] = $mcFile;
                        break;

                    //
                    //                    case 'hook':

                    default:
                        break;
                }
            }
            array_push($listModules, $moduleInfo);
        }

        return $listModules;
    }



    /**
     *
     * @author Alexandr Girin
     */
    function getTablesAndRecordsCount($count_records = true)
    {
	    global $application;

	    $avactis_tables = array();

	    $tables = $this->getTables();

	    $module_tbl            = 'module';
	    $module_columns        = $tables[$module_tbl]['columns'];
	    $module_class_tbl      = 'module_class';
	    $module_class_columns  = $tables[$module_class_tbl]['columns'];

	    $db_select = new DB_Select();
	    $db_select->addSelectField($module_columns['name']);
	    $modules = $application->db->getDB_Result($db_select, QUERY_RESULT_NUM);

	    $total_records = 0;
	    foreach ($modules as $module)
	    {
		    if (method_exists($application->getInstance($module[0]), "getTables"))
		    {
			    $tables = modApiFunc($module[0], "getTables");
			    if (sizeof($tables))
			    {
				    foreach ($tables as $tableName => $tableInfo)
				    {
					    if (method_exists($application->getInstance($module[0]), "getIgnoredTablesForBackup"))
					    {
						    $ignoredTables = modApiFunc($module[0], "getIgnoredTablesForBackup");
						    //print_r($ignoredTables);
						    if (in_array($tableName, $ignoredTables))
						    {
							    continue;

						    }
					    }

					    if ($count_records)
					    {
						    $query = new DB_Select();
						    $query->addSelectTable($tableName);
						    $query->addSelectField($query->fCount('*'), 'count');
						    $result = $application->db->getDB_Result($query);
						    $avactis_tables[] = array('table_name' => $application->getAppIni('DB_TABLE_PREFIX').$tableName, 'records_count' => $result[0]['count']);
						    $total_records+= $result[0]['count'];
					    }
					    else
					    {
						    $avactis_tables[] = $application->getAppIni('DB_TABLE_PREFIX').$tableName;
					    }
				    }
			    }
		    }
	    }
	    if ($count_records)
	    {
		    $avactis_tables['Total_Records'] = $total_records;
	    }
	    return $avactis_tables;
    }

    /**
     * Loads the file of the specified view, creates view object
     * and returns its reference.
     */
    function &getViewObject ($viewName)
    {
	    global $application;

	    // return the reference to the class if it has been loaded
	    if (class_exists($viewName))
	    {
		    return $application->getInstance($viewName);
	    }

	    // define a current zone.
	    $users = &$application->getInstance('users');
	    $zone = $users->getZone();

        // define the file of object description
        $viewList = null;
        switch ($zone)
        {
            case 'CustomerZone':
                $viewList = &$this->czViewList;
                break;
            case 'AdminZone':
                $viewList = &$this->azViewList;
                break;
        }

        // check, if the class is registered in the list
        if (is_null($viewList) || !key_exists($viewName, $viewList))
        {
            _warning("$viewName not found");
            return null;
        }
        $viewFile = $viewList[$viewName];

        //
        $this->includeFile($viewFile);
        return $application->getInstance($viewName);
    }

    /**
     * Loads the file of the specified event handler, creates an object
     * and returns a reference to it.
     *
     * @return object action
     */
    function &getActionObject( $actionName )
    {
        global $application;

        //  returns a reference to the class, if it has been loaded
        if (class_exists($actionName))
        {
            return $application->getInstance($actionName);
        }

        //  check, if the class is registered in the list
        if (!key_exists($actionName, $this->actionList))
        {
            _warning("$actionName not found");
            $res = null;
            return $res;
        }
        // define the file name for given action
        $actionFile = $this->actionList[$actionName];
        // load the file
        $this->includeFile($actionFile);
        // create an object copy and return a reference to the object
        return  $application->getInstance($actionName);
    }

    /**
     * Returns a list of hooks which respond to the given action.
     *
     * @param $actionName
     * @return Array or null
     */
    function getHooksClassesList($actionName)
    {
        if (key_exists($actionName, $this->hookList))
        {
            $hookList = $this->hookList[$actionName];
            $result = array();
            foreach ($hookList as $hookName => $hookFile)
            {
                // load the class if it has not been loaded
                if (!class_exists($hookName))
                {
                    $this->includeFile($hookFile);
                }
                // save hook in the list
                array_push($result, $hookName);
            }
            return $result;
        }
        else
        {
            return null;
        }
    }

    /**
     * Returns a list of file's paths with YAML blocks.
     *
     * @return Array
     */
    function getYAMLBlocksList()
    {
        return $this->blocksList;
    }

    function getSectionByView()
    {
        if (!$this -> SectionByView)
            return array();

        return $this -> SectionByView;
    }

    function getViewBySection()
    {
        if (!$this -> ViewBySection)
            return array();

        return $this -> ViewBySection;
    }

    function getStorefrontLayout()
    {
        if (!$this -> storefrontLayout)
            return array();

        return $this -> storefrontLayout;
    }

    /**#@-*/

    //------------------------------------------------
    //              PRIVATE DECLARATION
    //------------------------------------------------

    /**#@+
    * @access private
    */

    /**
     * Loads this module in the system.
     *
     * @param ModuleInfo $moduleInfo
     * @return null
     */
    function includeFile($file)
    {
	global $application;
        _use($this->store_dir.$file);
    }

    /**
     * Registers view as separate functions.
     * And it's not necessary this view class to be loaded in the system.
     *
     * @param ModuleInfo $moduleInfo
     */
    function registerViewList(&$viewList)
    {
        foreach (array_keys($viewList) as $view)
        {
            if (! function_exists($view)) {
                eval($this->getViewFunction($view));
            }

            if (! function_exists('get'.$view)) {
                eval($this->getViewGetFunction($view));
            }
        }
    }

    function getViewFunction($view)
    {
        return "
function $view() { \$args = func_get_args(); return __block_tag_output('$view', \$args); }";
    }

    function getViewGetFunction($view)
    {
        return "
function get$view()
{
    \$args = func_get_args();
    ob_start();
     __block_tag_output('$view', \$args);
    return ob_get_clean();
}";
    }

    function registerAliasesList($aliasesList)
    {
        if(!empty($aliasesList))
        {
            foreach ($aliasesList as $alias_name => $view_name) {
                if (! function_exists($alias_name)) {
                    eval($this->getAliasFunction($alias_name, $view_name));
                }
                if (! function_exists('get'.$alias_name)) {
                    eval($this->getAliasGetFunction($alias_name, $view_name));
                }
            }
        };
    }

    function getAliasFunction($alias_name, $view_name)
    {
        return "
function $alias_name()
{
    \$args = func_get_args();
    return __block_tag_alias('$view_name', '$alias_name', \$args);
}";
    }

    function getAliasGetFunction($alias_name, $view_name)
    {
        return "
function get$alias_name()
{
    \$args = func_get_args();
    ob_start();
    __block_tag_alias('$view_name', '$alias_name', \$args);
    return ob_get_clean();
}";
    }

    function includeAPIFileOnce($module_name)
    {
      if ( !class_exists($module_name) )
        {
            if (!isset($this->apiFiles[_ml_strtolower($module_name)]))
            {
              //  AF
                 _fatal(array( "CODE" => "CORE_045"), $module_name);
              //   die('ModulesManager::includeAPIFileOnce unknown class name ['.$module_name.']');
            }
            else
            {
                $this->includeFile($this->apiFiles[_ml_strtolower($module_name)]);
            }
        }
    }

    function includeViewFileOnce($viewName)
    {
      global $zone;
        if ($zone == 'CustomerZone')
        {
          if (isset($this->czViewList[$viewName]))
                $this->includeFile($this->czViewList[$viewName]);
        }
        else
        {
            if (getKeyIgnoreCase($viewName, $this->azViewList) !== null)
                $this->includeFile(getKeyIgnoreCase($viewName, $this->azViewList));

        }
    }

    /**
     * Loads module info from the database.
     *
     * @param $id integer
     * @return ModuleInfo
     */
    function getModuleInfoFromDB($id)
    {
        global $application;
//
//        $tables = $this->getTables();
//        $mc = $tables['module_class']['columns'];
//
//        $query = new DB_Select('module_class');
//        $query->addSelectField($mc['id'], 'id');
//        $query->addSelectField($mc['name'], 'name');
//        $query->WhereValue($mc['module_id'], DB_EQ, $id);
//        $modules = $application->db->getDB_Result($query);
    }

    /**
     * Loads module info from the file system.
     *
     * @param $name module name. It matches a catalog name for this module.
     * @return ModuleInfo
     */
    function getModuleInfoFromFile($fs_module_dir)
    {
	global $application;
        $info = $this->store_dir._ml_strtolower($fs_module_dir) . "/info.php";
        if (file_exists($info))
        {
            unset($moduleInfo);
            include($info);
            $moduleInfo['directory'] = $fs_module_dir;
            $moduleInfoClass = new ModuleInfo();
            $moduleInfoClass->loadFromArray($moduleInfo);
            return $moduleInfoClass;
        } else {
            #            _warning("Module " + $module + " has wrong structure");
            return null;
        }
    }

    /**
     * An internal function to insert to the database.
     *
     * @return the id of the last inserted record in case it is generated
     *         automatically
     */
    function dbInsert($table, $data)
    {
        global $application;

        $db_insert = new DB_Insert($table);
        foreach ($data as $field => $value)
        {
            $db_insert->addInsertValue($value, $field);
        }
        $application->db->getDB_Result($db_insert);
        return $application->db->DB_Insert_Id();
    }

    /**
     * Checks if this module matches the building standards.
     *
     * @param ModuleInfo $moduleInfo
     * @return int error code.
     */
    function checkModuleFiles($moduleInfo)
    {
	global $application;
        // check if the main file exists.
	if (!file_exists( $this->store_dir.$moduleInfo->mainFile))
	{
            $err_params = array(
                                "CODE"    => "MDMNGR_001",
                                "MODULE"  => $moduleInfo->name,
                                "FILE"    => $this->store_dir.$moduleInfo->mainFile
                                );
            _fatal($err_params);
        }

        // check if actions files exist
        foreach ($moduleInfo->actionFiles as $actionName => $actionFile)
        {
        if (!file_exists($this->store_dir.$actionFile))
	{
                $err_params = array(
                                    "CODE"    => "MDMNGR_002",
                                    "MODULE"  => $moduleInfo->name,
                                    "FILE"    => $this->store_dir.$actionFile
                                    );
                _fatal($err_params);
          }
        }

        // check if view files exist in CustomerZone
        foreach ($moduleInfo->czViewFiles as $viewName => $viewFile)
        {
          if (!file_exists($this->store_dir.$viewFile))
          {
                $err_params = array(
                                    "CODE"    => "MDMNGR_003",
                                    "MODULE"  => $moduleInfo->name,
                                    "FILE"    => $this->store_dir.$viewFile
                                    );
                _fatal($err_params);
          }
        }

        // check if view files exist in AdminZone
        foreach ($moduleInfo->azViewFiles as $viewName => $viewFile)
        {
          if (!file_exists($this->store_dir.$viewFile))
          {
                $err_params = array(
                                    "CODE"    => "MDMNGR_004",
                                    "MODULE"  => $moduleInfo->name,
                                    "FILE"    => $this->store_dir.$viewFile
                                    );
                _fatal($err_params);
          }
        }

        return 0;
    }

    /**
     * Checks if the API class is correct.
     *
     * @param array $moduleInfo
     */
    function checkModuleAPIClass($moduleInfo)
    {
        $this->includeAPIFileOnce($moduleInfo->name);
        if (!is_callable(array($moduleInfo->name,'install')))
        {
            $err_params = array(
                                "CODE"    => "MDMNGR_005",
                                "MODULE"  => $moduleInfo->name,
                                );
            _fatal($err_params);
        }

        if (!is_callable(array($moduleInfo->name,'uninstall')))
        {
            $err_params = array(
                                "CODE"    => "MDMNGR_006",
                                "MODULE"  => $moduleInfo->name,
                                );
            _fatal($err_params);
        }
    }

    function getResFileByShortName($short_name)
    {
        global $application;
        CProfiler::ioStart($application->appIni['PATH_SYSTEM_DIR'].'shortname2path', 'parse');
        $map = parse_ini_file($application->appIni['PATH_SYSTEM_DIR'].'shortname2path');
        CProfiler::ioStop();
        if(isset($map[$short_name]))
            return $application->appIni['PATH_ASC_ROOT'].$map[$short_name];
        else
            return null;
    }

    function getShortNameByResFile($path)
    {
        global $application;

        if ($path == 'messages')
            return 'CZ';

        $postfix = '-'._ml_strtolower($application->getAppIni('LANGUAGE')).'.ini';
        $file = basename($path);
        $resname = str_replace($postfix, '', $file);

        if (isset($this->shortNamesToResFiles[$resname]))
            return $this->shortNamesToResFiles[$resname];

        return null;
    }

    /**
     *                  module_class,           module
     *               updated = 0.
     */
    function resetAllModulesStatus()
    {
        global $application;
        $tables = $this->getTables();
        $mt = $tables['module']['columns'];

        $q = new DB_Delete('module_class');
        $application->db->getDB_Result($q);

        $q = new DB_Update('module');
        $q->addUpdateValue($mt['updated'],0);
        $application->db->getDB_Result($q);

        return;
    }

	/**
	* To get the info of all Instlled Extensions Module info
	* @return all intalled extnesions module info
	*/
	function getInstalledExtensionModuleInfo(){
		$arrayOfInstalledModules=execQuery('SELECT_ALL_INSTALL_EXTENSION_MODULE',array());
		$installExtensionModulesArray=array();
		foreach ($arrayOfInstalledModules as $moduleInfo){
			$installExtensionModulesArray[$moduleInfo['module_name']]=$moduleInfo;
		}
		return $installExtensionModulesArray;
	}

/*** BELOW FUNCTION FOR SCANNING FOR AVACTIS_EXTENSION DIR IS TO BE DELETED ONCE HOOKS IMPLEMENTED FOR EXTENSIONS ***/

/* For checking if module exists in avactis-extensions folder */
                function isModulePresentInAvactisExtensionsFolder($modulefoldername)
                {
                        global $application;
                        $filename = $application->getAppIni('PATH_ADD_MODULES_DIR').$modulefoldername;

                        if (file_exists($filename))
                        {
                                return true;
                        }

                }


    /** A core system directory that includes modules. */
    var $modules_directory;

    /** A additional directory that includes custom modules. */
    var $add_modules_directory;

    /** Store root directory **/
    var $store_dir;

    /** A module list.*/
    var $moduleList = array();

    /** A list of all Actions of the type 'name' =&gt; 'file'. */
    var $actionList = array();

    /** A list of View CustomerZone of the type 'name' =&gt; 'file'. */
    var $czViewList = array();

    /** A list of all View for AdminZone of the type 'name' =&gt; 'file'. */
    var $azViewList = array();

    var $czAliasesList = array();

    /** A list of all Hooks of the type'actionName' =&gt; list('h1' =&gt; 'file_h1.php', 'h2' =&gt; 'file_h2.php', etc.) */
    var $hookList = array();

    var $apiFiles = array();

    var $modulesResFiles = array();
    var $shortNamesToResFiles = array();

    // A list of YAML-files
    var $blocksList = array();

    // list of sections by view
    var $SectionByView = array();

    // list of views by section
    var $ViewBySection = array();

    // storefron layout additon
    var $storefrontLayout = '';

    /**#@-*/
}

?>