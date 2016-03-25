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
 * Extension_Manager module
 *
 * @package Extension_Manager
 * @author Pragati
 */
class Extension_Manager
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
	function Extension_Manager()
	{

	}

	function install()
	{
		global $application;
		$tables = Extension_Manager::getTables();           #the array of the Cart module tables
		$query = new DB_Table_Create($tables);
	}

	function uninstall()
	{
		$query = new DB_Table_Delete(Extension_Manager::getTables());
		global $application;
		$application->db->getDB_Result($query);
	}

	function getExtensionsList()
	{
		global $application;
		$result = execQuery('SELECT_EXTENSION_LIST',array());
		$arrayOfExtensions=array();
		foreach($result as $extension){
			$arrayOfExtensions[strtolower($extension['extension_name'])]=$extension;
		}
		return $arrayOfExtensions;
	}

	function getTables()
	{
		$tables = array ();
		$table_name = 'marketplace_ext_data';
		$tables[$table_name] = array();
		$tables[$table_name]['columns'] = array
			(
			 'id'                			=> 'marketplace_ext_data.extension_id'
			 ,'display_name'				=> 'marketplace_ext_data.display_name'
			 ,'name'             				=> 'marketplace_ext_data.extension_name'
			 ,'desc'              			=> 'marketplace_ext_data.extension_desc'
			 ,'link'     					=> 'marketplace_ext_data.extension_detail_link'
			 ,'price'    					=> 'marketplace_ext_data.extension_price'
			 ,'image'             			=> 'marketplace_ext_data.extension_image'
			 ,'category'          			=> 'marketplace_ext_data.extension_category'
			 ,'type'              			=> 'marketplace_ext_data.extension_type'
			 ,'latestversion'     			=> 'marketplace_ext_data.latestversion'
			 ,'latestcompatibleversion'       		=> 'marketplace_ext_data.latestcompatibleversion'
			 ,'file'              			=> 'marketplace_ext_data.extension_filename'
			);

		$tables[$table_name]['types'] = array
			(
			 'id'                			=> DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
			 ,'display_name'                              => DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT ''"
			 ,'name'              			=> DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT ''"
			 ,'desc'              			=> DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT ''"
			 ,'link'         				=> DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT ''"
			 ,'price'        				=> DBQUERY_FIELD_TYPE_DECIMAL12_2 ." NOT NULL DEFAULT '0'"
			 ,'image'                			=> DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT ''"
			 ,'category'              			=> DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT ''"
			 ,'type'              			=> DBQUERY_FIELD_TYPE_CHAR2 ." NOT NULL DEFAULT 'P'"
			 ,'latestversion'         			=> DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT ''"
			 ,'latestcompatibleversion'       		=> DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT ''"
			 ,'file'              			=> DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT ''"
			);
		$tables[$table_name]['primary'] = array
			(
			 'id'
			);
		$tables[$table_name]['indexes'] = array
			(
			 'IDX_ext_id' => 'id'
			);

		global $application;
		return $application->addTablePrefix($tables);
	}
	/**
	 * Checks whether the xml update from Marketplace is required.
	 */
	function isMarketplaceUpdateRequired()
	{
		$result_build_date = execQuery('SELECT_SETTINGS_PARAM_BASE_INFO',array('group_name' => 'MARKETPLACE_LAST_BUILD_DATE', 'param_name' => 'MARKETPLACE_LAST_BUILD_DATE'));

		$result_ttl = execQuery('SELECT_SETTINGS_PARAM_BASE_INFO',array('group_name' => 'MARKETPLACE_TTL', 'param_name' => 'MARKETPLACE_TTL'));

		$ttl = '+'.$result_ttl[0]['param_current_value'].' minutes';

		$buildDate = $result_build_date[0]['param_current_value'];

		$xmlExpireTime = strtotime($ttl,$buildDate);
		return $xmlExpireTime <= time() ? true : false;
	}

	/**
	 * Get the marketplace Extension Data.
	 * @param boolean $paramReload is ture it will reload details from marketplace
	 */
	function fetchMarketplaceExtData($paramReload=false)
	{

		global $application;
		if($paramReload||$this->isMarketplaceUpdateRequired())
		{
			$this->marketplace_server = $application->getAppIni('MARKETPLACE_SERVER');
			loadCoreFile('bouncer.php');
			$bnc = new Bouncer();
			$bnc->setMethod('POST');
			$bnc->setPOSTstring($bnc->prepareDATAstring($this->getLicenseDetail()));
			$bnc->setURL($this->marketplace_server. "/download_extension.php?asc_action=ListMarketplaceExtensions");
			$bnc->setProto('HTTPS');
			$result = $bnc->RunRequest();
			if ($result!=false && $bnc->responseCode < 400)
			{
				$response = json_decode($result['body']);
				if(isset($response) && isset($response->extensions) && !empty($response->extensions)){
					$application->db->DB_Query('truncate '.$application->getAppIni('DB_TABLE_PREFIX').'marketplace_ext_data');
					$tables = self::getTables();
					$columns = $tables['marketplace_ext_data']['columns'];
					foreach($response->extensions as $extension){
						$query = new DB_Replace('marketplace_ext_data');
						$query->addReplaceValue($extension->extension_id,$columns['id']);
						$query->addReplaceValue($extension->display_name,$columns['display_name']);
						$query->addReplaceValue($extension->name,$columns['name']);
						$query->addReplaceValue($extension->description,$columns['desc']);
						$query->addReplaceValue($extension->detailed_link,$columns['link']);
						$query->addReplaceValue($extension->image_url,$columns['image']);
						$query->addReplaceValue($extension->type,$columns['type']);
						$query->addReplaceValue($extension->category,$columns['category']);
						$query->addReplaceValue($extension->latestversion,$columns['latestversion']);
						$query->addReplaceValue($extension->price,$columns['price']);
						$query->addReplaceValue($extension->ext_version,$columns['latestcompatibleversion']);
						$query->addReplaceValue($extension->filename,$columns['file']);
						$application->db->getDB_Result($query);
					}
					/* Update Last build date - start */
					$params = array('group_name' => 'MARKETPLACE_LAST_BUILD_DATE',
							'param_name' => 'MARKETPLACE_LAST_BUILD_DATE',
							'value' => time());
					execQuery('UPDATE_SETTINGS_PARAM_VALUE', $params);
					/* Update TTL coming from marketplace server */
					$params = array('group_name' => 'MARKETPLACE_TTL',
							'param_name' => 'MARKETPLACE_TTL',
							'value' => $response->MARKETPLACE_TTL);
					execQuery('UPDATE_SETTINGS_PARAM_VALUE', $params);
				}
				else
				{
					CTrace::wrn("Failed to parse response from marketplace. Response is ".print_r($result['body'],true));
				}
			} else{
				CTrace::wrn("Failed to fetch from marketplace. Error is ".$bnc->responseCode." ".$bnc->_error_message);
			}
		}
	}
	/* Functions for extension manager -start */

	function backup_extension($moduleInfo)
	{
		$backup_created = false;
		global $application;
		$modname = $moduleInfo->name;
		if	(!$this->isExtensionInstalled($modname)) return;
		modApiFunc('Modules_Manager','includeAPIFileOnce',$modname);

		$extensiondir = $application->getAppIni('PATH_ADD_MODULES_DIR')."/".strtolower($modname);
		$backupdir = $application->getAppIni('PATH_BACKUP_DIR');
		$getAllExtensionTables = array_keys($modname::getTables());

		/*Backup DB tables for extensions - start */
		$db_backup = backupDB_tables($extensiondir,$modname."_".date("Y-m-d").".sql",$getAllExtensionTables);
		if ( $db_backup){
			$backup_created = createZip($extensiondir, $backupdir,$modname.'_'.date('Y-m-d').'.zip',array());
		}
		return $backup_created;
	}
			/* Displays active extensions list - Start */
	function getActiveExtensions(){
		global $application;
		$tables = $this->getTables();

		$module_tbl            = 'module';
		$module_columns        = $tables[$module_tbl]['columns'];
		$module_class_tbl      = 'module_class';
		$module_class_columns  = $tables[$module_class_tbl]['columns'];

		static $extensions = null;
		if ($extensions == null)
		{
			$result = execQuery('SELECT_ACTIVE_EXTENSIONS',array());
			foreach($result as $row)
			{
				$ext_name = _ml_strtolower($row['module_name']);
				$extensions[$this->add_modules_directory.$ext_name] = $this->getModuleInfoFromFile($this->add_modules_directory.$ext_name);
			}

		}
		return $extensions;
	}

	function installExtension($extension_file_name){

		global $application;
		$request = $_REQUEST;
		$installed = false;
		$store_root_path =$application->getAppIni('PATH_ASC_ROOT');
		$cachedir = $application->getAppIni('PATH_CACHE_DIR');
		$extension_name = $extension_file_name.".zip";
		$response = array();
		/*if(file_exists($cachedir.$extension_name)) // Check if the downloaded extension is present in cache
		{
			array_push($response,getMsg('SYS','DOWNLOADING_EXT'));
		}*/
		$returnCode = copy($cachedir.$extension_name, $store_root_path.$extension_name);
		if($returnCode)
		{
			unlink($cachedir.$extension_name);
			$extracted = extractFiles($store_root_path.$extension_name,$store_root_path);
			if ($extracted){
				@unlink($store_root_path.$extension_name);
				$installed = true;
			}
		}
		return $installed;
	}
	/* Displays active extensions list - End */
	/* Displays if extension is installed- Start */
	function isExtensionInstalled($extensionName)
	{
		$value = execQuery('SELECT_ACTIVE_EXTENSIONS_CHECK',array('name' => $extensionName));
		if (empty($value))
			$result = "N";
		else
			$result = "Y";

		return $result;
	}
	/* Displays if extension is installed- Start */

	function getLatestCoreVer(){
		global $application;

		$_state = modApiFunc("License", "checkLicense");
		$licenseInfo = modApiFunc("License","getLicenseInfo", $_state);
		if($licenseInfo["license_key"]==LICENSE_KEY_UNDEFINED){
			$result['body']= PRODUCT_VERSION_NUMBER;
		}
		if($this->isMarketplaceUpdateRequired()){
			$this->marketplace_server = $application->getAppIni('MARKETPLACE_SERVER');

			$data = array(
					'license'         => $licenseInfo["license_key"],
					'store_url'       => urlencode(modApiFunc('Request', 'selfURL')),
					'store_version'   => PRODUCT_VERSION_NUMBER,
				     );
			loadCoreFile('bouncer.php');
			$bnc = new Bouncer();
			$bnc->setMethod('POST');
			$bnc->setPOSTstring($bnc->prepareDATAstring($data));
			$bnc->setURL($this->marketplace_server."/download_extension.php?asc_action=GetLatestCoreVersion");
			$bnc->setProto('HTTPS');
			$result = $bnc->RunRequest();
			if ($result!=false && $bnc->responseCode < 400){
				$latest_core_version = $result['body'];
				/* Update Latest core version */
				$params = array('group_name' => 'AVACTIS_LATEST_VERSION',
						'param_name' => 'AVACTIS_LATEST_VERSION',
						'value' => 	$latest_core_version);
				execQuery('UPDATE_SETTINGS_PARAM_VALUE', $params);
				return $latest_core_version;
			}
		}
		//Error while calling marketplace or TTL not expired then return from database
		$result= execQuery('SELECT_SETTINGS_PARAM_BASE_INFO',array('group_name' => 'AVACTIS_LATEST_VERSION', 'param_name' => 'AVACTIS_LATEST_VERSION'));
		$latest_core_version = $result[0]['param_current_value'];
		return $latest_core_version;
	}
	function upgradeCore(){
		global $application;
		//5. Turn the store off line
		modApiFunc('Configuration','setValue',array("store_online"=>0));
		//CCacheFactory::clearAll();
		//6. Backup database and avactis system folder
		/*$backup_dir = $application->getAppIni('PATH_BACKUP_DIR');
		  $system_dir = $application->getAppIni('PATH_SYSTEM_DIR');
		  $backup_filename = "avactis_system_backup.zip";
		  $db_backup_file = "avactis_db_backup.sql";
		  $exclude = array("backup","cache"); //exclude cache and backup directories from creating backup
		  $db_backup = backupDB_tables($backup_dir,$db_backup_file,"*");

		  if ($db_backup){
		  $file_backup = createZip($system_dir ,$backup_dir,$backup_filename,$exclude);
		  if($file_backup){	*/
		//7. Move zip at store root
		//8. Unzip and clear cache
		$response = array();
		$upgrade_file = "avactis-upgrade.zip";
		$store_root_path =$application->getAppIni('PATH_ASC_ROOT');
		$cachedir = $application->getAppIni('PATH_CACHE_DIR');

		$returnCode = copy($cachedir.$upgrade_file, $store_root_path.$upgrade_file);
		if($returnCode)
		{
			unlink($cachedir.$upgrade_file);

			$extracted = extractFiles($store_root_path.$upgrade_file,$store_root_path);
			if($extracted){
				unlink($store_root_path.$upgrade_file);

				//Make a test call to store, if okie then make a call to server for checking folder size,
				//If any issues, revert back to old system and db rollback
				//Else upgrade successful.
				$response = true;
			}else{
				$response['err_msg'] = "ERR_EXTRACT_FAILED";
			}
		}else{
			$response['err_msg'] = "ERR_MOVE_FAILED";
		}
		/*}else{
		  $response['err_msg'] = "ERR_FILE_BACKUP_FAILED";
		  }
		  }else{
		  $response['err_msg'] = "ERR_DB_BACKUP_FAILED";
		  }*/
		//Turning store back online
		modApiFunc('Configuration','setValue',array("store_online"=>1));
		//
		return $response['err_msg'];
	}

	/** Get License Details of store in array
	 * @return array contain license,store_url and stroe_version
         */
	function getLicenseDetail(){
		/** getting License status **/
		$_state = modApiFunc("License", "checkLicense");
		/** Getting License information array **/
		$licenseInfo = modApiFunc("License","getLicenseInfo", $_state);
		/** array containing license details **/
		$licenseDetailArray = array(
				'license'         => $licenseInfo["license_key"],
				'store_url'       => $licenseInfo["current_url"],
				'store_version'   => PRODUCT_VERSION_NUMBER,
		);
		return $licenseDetailArray;
	}

	/* Functions to extension manager -end */

	function getInstallExtensionInfo($extensionName){
		return execQuery('SELECT_ACTIVE_EXTENSIONS_CHECK',array('name' => $extensionName));
	}

	function updateExtensionStatus($extensionName,$status){
		execQuery('UPDATE_EXTENSION_STATUS',array('name' => $extensionName,'active'=>$status));
		CCacheFactory::clearAll();
	}

	var $ext;

	/**#@-*/

}
?>