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
 * Modules_Manager list view main class.
 *
 * @package Modules_Manager
 * @author Alexey Kolesnikov
 */
class ExtensionManager_Manage
{

	function ExtensionManager_Manage()
	{
		global $application;
		$this->application = &$application;
		$this->mTmplFiller = &$application->getInstance('TmplFiller');
		$this->search_filter = array(
				'category_filter_by' => 'all'
				);
		if(isset($_GET['category_filter_by']))
		{
			$cat_filter = $_GET['category_filter_by'];

			if($cat_filter != null)
			{
				$this->search_filter['category_filter_by'] = $cat_filter;
			}
		}

	}
	function outputStatusMessage()
	{
		global $application;
		if(modApiFunc("Session","is_set","StatusMessage"))
		{
			$status=modApiFunc("Session","get","StatusMessage")?"Activated":"Deactivate";
			modApiFunc("Session","un_set","StatusMessage");
		}else{
			return'';
		}
			if(modApiFunc("Session","is_set","Extension_Name"))
		{
			$extension_name=modApiFunc("Session","get","Extension_Name");
			modApiFunc("Session","un_set","Extension_Name");

		}else{
			return'';
		}

		$this->mTmplFiller = &$application->getInstance('TmplFiller');
		return $this->mTmplFiller->fill("extension_manager/list/", "status-message.tpl.html",array('ext_name'=>$extension_name,'status'=>$status));

	}
	function outputErrorMessage()
	{
		global $application;

		if(modApiFunc("Session","is_set","ErrorMessage"))
		{
			$error_msg = modApiFunc("Session", "get", "ErrorMessage");
			modApiFunc("Session","un_set","ErrorMessage");
			$this->mTmplFiller = &$application->getInstance('TmplFiller');
			return $this->mTmplFiller->fill("extension_manager/list/", "error-message.tpl.html",array(
						"Local_ErrorMessage"=>getXMsg("ExtManager",$error_msg)));
		}
	}
	function outputUninstallMessage()
	{
		global $application;

		if(modApiFunc("Session","is_set","UninstallMessage"))
		{
			modApiFunc("Session","un_set","UninstallMessage");
			$this->mTmplFiller = &$application->getInstance('TmplFiller');
			return $this->mTmplFiller->fill("extension_manager/list/", "uninstall-message.tpl.html",array());
		}
	}

	function outputExtensionTypeFilterSelect()
	{

		$result = execQuery('SELECT_ALL_MARKETPLACE_EXTENSION_DETAILS');
		$values[] = array('value' => getMsg('SYS','CUSTOMERS_SEARCH_ALL'),'contents' => getMsg('SYS','CUSTOMERS_SEARCH_ALL')); # For displaying All extensions
			if(!empty($result))
			{
				foreach($result as $content)
				{
					$ext_cat = $content['extension_category'];
					$ext_cat_display = ucwords(str_replace('_',' ',$content['extension_category']));



					$values[] =  array('value' => "$ext_cat", 'contents' => "$ext_cat_display");

				}
			}
		$filter_select = array(
				'select_name' => 'category_filter_by'
				,'selected_value' => $this->search_filter['category_filter_by']
				,'class' => 'form-contrl input-sm input-small inline'
				,'onChange' => "window.location = 'configure-extensions.php?category_filter_by='+this.value;"
				,'values' => array_unique($values,SORT_REGULAR)
				);

		return HtmlForm::genDropdownSingleChoice($filter_select);

	}


	/**
	 * @access public
	 * @ remove the block that uses TemplateFiller
	 */
	function output()
	{
		global $application;

		$application->registerAttributes(
				array(
					'ListManageExtensionItems',
					'StatusMessage',
					'UninstallMessage',
					'SelectExtensionType',
					'ErrorMessage',
					'ReloadMarketPlace'
				     ));
		$this->mTmplFiller = &$application->getInstance('TmplFiller');

		return $this->mTmplFiller->fill("extension_manager/list/", "list_manage.tpl.html",array());

	}
	function outputExtDetailList()
	{
		global $application;

		$arrayOfInstallModules=modApiFunc('Modules_Manager','getInstalledExtensionModuleInfo');
		/** Geting the Licence Key from the file **/
	        loadCoreFile('licensekey.php');
//	        loadCoreFile('licensecert.php');
	        //loadCoreFile('licenseclient.class.php');
		$licenseKeyObj = new LicenseKey();
        	$licenseKey = $licenseKeyObj->getLicenseKey();

		/** Request Instance **/
		$request = $application->getInstance('Request');
		/** Get the reload parameter **/
		$reload=$request->getValueByKey('reload');
		modApiFunc('Extension_Manager', 'fetchMarketplaceExtData',isset($reload)); # Fetch Extension data from marketplace server.
		$extensionList = modApiFunc('Extension_Manager', 'getExtensionsList');
		$result = "";
		$ext_items = "";
		$count=0;

        foreach ($arrayOfInstallModules as $key=>$installModuleInfo)
		{
			$moduleInfo=$extensionList[$installModuleInfo['module_name']];
			$template_contents = array('Extn_Category' => $moduleInfo['extension_category']);
			$this -> _Template_Contents = $template_contents;
			$application -> registerAttributes($this -> _Template_Contents);
			$moduleError = 0;
			if($moduleInfo['extension_desc'] != "")
			{
				$ext_explode = explode(".",$moduleInfo['extension_desc']);
				$ext_name = $ext_explode[0];
			}
			else
			{
				$ext_name = $moduleInfo['extension_name'];
				$moduleInfo['extension_desc'] = "No description available";
			}
			$installedVersion='';
			$activeFlag=$installModuleInfo['module_active'];
			$installedVersion=$installModuleInfo['module_version'];

			if(version_compare($moduleInfo['latestcompatibleversion'],$installedVersion,'>')){
				$UprChk = "Y";
			}
			else{
				$UprChk = "N";
			}
			$extn_type=isset($moduleInfo['extension_type']) && !empty($moduleInfo['extension_type'])?$moduleInfo['extension_type']:$this->getTypeOfExtension($installModuleInfo['module_name'], $installModuleInfo['module_groups']);
			/* For extensions present in store - start */
			$template_contents = array(
					'Extn_ID' 			=> 	$moduleInfo['extension_id']
					,'Extn_Display_Name'=> 	$installModuleInfo['module_name']
					,'Extn_Name' 		=> 	$installModuleInfo['module_name']
					,'Extn_Desc' 		=> 	$moduleInfo['extension_desc']
					,'Extn_Price' 		=> 	$moduleInfo['extension_price']
					,'Extn_Category'    =>  $extn_type
					,'Extn_Image' 		=> 	$moduleInfo['extension_image']
					,'Extn_Filename' 	=>	$moduleInfo['extension_filename']
					,'Extn_AVersion'	=> 	$moduleInfo['latestversion']
					,'Extn_CVersion'	=> 	$moduleInfo['latestcompatibleversion']
					,'Extn_type'       =>   $moduleInfo['extension_type']
					,'IsExtnInstalled' 	=> 	$activeFlag
					,'Module_UButton' 	=> 	$UprChk
					,'Extn_IVersion'	=>	$installedVersion
					,'isActive'			=> $activeFlag
					,'isSettingExist'	=> 	modApiFunc('Settings','isGroupExist',$moduleInfo['extension_name'])
					,'Count'			=> $count++
				);
			$this->_Template_Contents = $template_contents;
			$application->registerAttributes($this->_Template_Contents);
			$this->mTmplFiller = &$application->getInstance('TmplFiller');

			$html .= $this->mTmplFiller->fill("extension_manager/list/", "list_item_manage.tpl.html",array());
		}
		return $html;
	}
	function getTag($tag)
	{
		global $application;
		$value = null;
		switch ($tag)
		{
			case 'ListManageExtensionItems':
				$value = $this->outputExtDetailList();
				if($value == ""){
					$value = '<div style="font-weight:bold;padding: 44px;text-align: center;">'.getMsg('SYS','NO_EXT_INSTALLED_MSG').'</div>';
				}
				break;

			case 'StatusMessage':
				$value =  $this->outputStatusMessage();
				break;

			case 'UninstallMessage':
				$value = $this->outputUninstallMessage();
				break;

			case 'SelectExtensionType':
				$value = $this->outputExtensionTypeFilterSelect();
				break;

			case 'ReloadMarketPlace':
				$value= "configure-extensions.php?reload";
				break;

			case  'ErrorMessage':
				$value = $this->outputErrorMessage();
				break;
			case 'Local_ConfigureURL':
				$value = 'configure-extensions.php';
				break;
			default: $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
				 break;

		}
		return $value;
	}

	function getTypeOfExtension($module_name,$module_group){
		$result = execQuery('SELECT_ALL_MARKETPLACE_EXTENSION_DETAILS');
		$module_name=strtolower($module_name);
		$module_group=strtolower($module_group);
		$prevExtensionCategory=array();
		foreach($result as $content){
			$ext_cat = $content['extension_category'];
			if(in_array($ext_cat,$prevExtensionCategory)){
				continue;
			}
			if(strpos($module_name,strtolower($ext_cat))!==false || strpos($module_group,$ext_cat)!==false){
				return $ext_cat;
			}
			$prevExtensionCategory[]=$ext_cat;
		}
		return '';
	}

	/** A reference to Application. */
	var $application;
	/** A reference to Template Filler. */

	var $mTmplFiller;

	var $_Template_Contents;
}

?>