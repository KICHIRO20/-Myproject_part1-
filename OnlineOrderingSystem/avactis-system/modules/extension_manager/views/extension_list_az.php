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
class ExtensionManager_ListView
{

	function ExtensionManager_ListView()
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
	function outputInstallMessage()
	{
		global $application;
		if(modApiFunc("Session","is_set","InstallMessage"))
		{
			modApiFunc("Session","un_set","InstallMessage");
			$application->registerAttributes(
					array(
						'Local_ConfigureURL',
					     ));

			$this->mTmplFiller = &$application->getInstance('TmplFiller');
			return $this->mTmplFiller->fill("extension_manager/list/", "result-message.tpl.html",array());
		}
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
                                'id' => 'category_filter_by'
				,'select_name' => 'category_filter_by'
				,'selected_value' => $this->search_filter['category_filter_by']
				,'class'=>"select2-container input-small select2me inline"
				,'values' => array_unique($values,SORT_REGULAR)
				);

		return HtmlForm::genDropdownSingleChoice($filter_select);

	}

        function outputExtensionFilterSelect()
	{

		$result = execQuery('SELECT_ALL_MARKETPLACE_EXTENSION_DETAILS');
		$ext_filter_select = array(
                                'id'=>'extension_filter_by'
				,'select_name' => 'extension_filter_by'
				,'class'=>"select2-container input-small select2me inline"
				,'values' => array(
                                                     array('value' => getMsg('SYS','CUSTOMERS_SEARCH_ALL'),'contents' => getMsg('SYS','CUSTOMERS_SEARCH_ALL')),
                                                     array('value' => "F", 'contents' => getXMsg("ExtManager","EXTN_FREE")),
                                                     array('value' => "B", 'contents' => getXMsg("ExtManager","EXTN_BUY")),
                                                     array('value' => "I", 'contents' => getXMsg("ExtManager","EXTN_PREMIUM"))
                                                  )
				);
		return HtmlForm::genDropdownSingleChoice($ext_filter_select);

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
					'ListExtensionItems',
					'InstallMessage',
					'UninstallMessage',
					'SelectExtensionType',
                                        'SelectExtensionFilterType',
					'ErrorMessage',
					'ReloadMarketPlace',
                                        'GetRegisterStoreMsg'
				     ));
		$this->mTmplFiller = &$application->getInstance('TmplFiller');

		return $this->mTmplFiller->fill("extension_manager/list/", "list.tpl.html",array());

	}
	function outputExtDetailList()
	{
		global $application;

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
		foreach ($extensionList as $key=>$moduleInfo)
		{
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
			$flag = modApiFunc("Extension_Manager", "isExtensionInstalled", $moduleInfo['extension_name']);
			if($flag=="Y"){
				continue;
			}
			/* For extensions present in store - start */
			$template_contents = array(
					'Extn_ID' 			=> 	$moduleInfo['extension_id']
					,'Extn_Display_Name'=> 	$moduleInfo['display_name']
					,'Extn_Name' 		=> 	$moduleInfo['extension_name']
					,'Store_Url' 		=> 	modApiFunc('Request', 'selfURL')
					,'License_Key' 		=> 	$licenseKey
					,'Extn_Desc' 		=> 	$moduleInfo['extension_desc']
					,'Extn_Price' 		=> 	$moduleInfo['extension_price']
					,'Extn_ReadMeLink' 	=> 	$moduleInfo['extension_detail_link'].'&licence='.$licenseKey
					,'Extn_Category'    =>  $moduleInfo['extension_category']
					,'Extn_Image' 		=> 	$moduleInfo['extension_image']
					,'Extn_Filename' 	=>	$moduleInfo['extension_filename']
					,'Extn_AVersion'	=> 	$moduleInfo['latestversion']
					,'Extn_CVersion'	=> 	$moduleInfo['latestcompatibleversion']
					,'Extn_type'		=>   $moduleInfo['extension_type']
					,'IsExtnInstalled' 	=> 	$flag
					,'Module_UButton' 	=> 	'N'
					,'Count'			=>	$count++
					);
			$this->_Template_Contents = $template_contents;
			$application->registerAttributes($this->_Template_Contents);
			$this->mTmplFiller = &$application->getInstance('TmplFiller');

			$html .= $this->mTmplFiller->fill("extension_manager/list/", "list_item.tpl.html",array());
		}
		return $html;
	}
       function OutputRegisterStatus()
	{
		global $application;
		$_state = modApiFunc("License", "checkLicense");
		$licenseInfo = modApiFunc("License","getLicenseInfo", $_state);
		if($_state == "APP_REG"){
			echo "<div class='note note-success'>".$licenseInfo['license_message_home']."</div>";
		}
		else
		{
			echo "<div class='note note-danger'>".$licenseInfo['license_message_home']."</div>";
		}
	}
	function getTag($tag)
	{
		global $application;
		$value = null;
		switch ($tag)
		{
			case 'ListExtensionItems':
				$value = $this->outputExtDetailList();
				if($value == ""){
					$value = '<div style="font-weight:bold;padding: 44px;text-align: center;">'.getMsg('SYS','EMPTY_EXT_LIST_MSG').'</div>';
				}
				break;

			case 'InstallMessage':
				$value =  $this->outputInstallMessage();
				break;

			case 'UninstallMessage':
				$value = $this->outputUninstallMessage();
				break;

			case 'SelectExtensionType':
				$value = $this->outputExtensionTypeFilterSelect();
				break;

                        case 'SelectExtensionFilterType':
                               $value = $this->outputExtensionFilterSelect();
                               break;

			case 'ReloadMarketPlace':
				$value= "manage-extensions.php?reload";
				break;

			case  'ErrorMessage':
				$value = $this->outputErrorMessage();
				break;
			case 'Local_ConfigureURL':
				$value = 'configure-extensions.php';
				break;
                        case 'GetRegisterStoreMsg':
                                $value = $this->OutputRegisterStatus();
                                break;
			default: $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
				 break;

		}
		return $value;
	}

	/** A reference to Application. */
	var $application;
	/** A reference to Template Filler. */

	var $mTmplFiller;

	var $_Template_Contents;
}

?>