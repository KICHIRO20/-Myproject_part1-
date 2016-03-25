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
class NotifyCoreUpgrade
{

	function NotifyCoreUpgrade()
	{
		global $application;
		$this->application = &$application;
		$this->mTmplFiller = &$application->getInstance('TmplFiller');


	}

	/**
	 * @access public
	 * @ remove the block that uses TemplateFiller
	 */
	function output()
	{
		global $application;
		$this->mTmplFiller = &$application->getInstance('TmplFiller');
		$latestVersion = modApiFunc('Extension_Manager', 'getLatestCoreVer');

		if(empty($latestVersion))
		{
			return $this->mTmplFiller->fill("extension_manager/avactis-core/", "remote_server_error.tpl.html",array());
		}

		if(PRODUCT_VERSION_NUMBER === $latestVersion)
		{
			return $this->mTmplFiller->fill("extension_manager/avactis-core/", "up_to_date.tpl.html",array());
		}

		$application->registerAttributes(
				array(
					'LatestCoreVersion',
					'Local_CoreUpgradeAction',
					'Local_ErrorMessage',
					'Local_StoreStatus',
					//'Local_StoreOffline',
					//'Local_SuccessMessage',
				     ));

		return $this->mTmplFiller->fill("extension_manager/avactis-core/", "notify_core_upgrade.tpl.html",array());

	}
	function getLocal_ErrorMessage(){
		global $application;

		if(modApiFunc("Session","is_set","ErrorMessage"))
		{
			$error_msg = modApiFunc("Session", "get", "ErrorMessage");
			modApiFunc("Session","un_set","ErrorMessage");
			$this->mTmplFiller = &$application->getInstance('TmplFiller');
			return $this->mTmplFiller->fill("extension_manager/avactis-core/", "error-message.tpl.html",array(
						"Local_ErrorMessagetxt"=>getXMsg("ExtManager","ERR_CORE_UPGRADE_FAILED").getXMsg("ExtManager",$error_msg)));
		}

	}

	/*function getLocal_SuccessMessage(){
		global $application;

		if(modApiFunc("Session","is_set","SuccessMessage"))
		{
			$success_msg = modApiFunc("Session", "get", "SuccessMessage");
			modApiFunc("Session","un_set","SuccessMessage");
			$this->mTmplFiller = &$application->getInstance('TmplFiller');
			return $this->mTmplFiller->fill("extension_manager/avactis-core/", "success-message.tpl.html",array(
						"Local_SuccessMessagetxt"=>getXMsg("ExtManager",$success_msg)));
		}

	}*/
	function getTag($tag)
	{
		global $application;
		$value = null;
		switch ($tag)
		{
			case 'LatestCoreVersion':
				$value = modApiFunc('Extension_Manager', 'getLatestCoreVer');
				break;
			case 'Local_ErrorMessage':
				$value = $this->getLocal_ErrorMessage();
				break;
			case 'Local_CoreUpgradeAction':
				$value = 'index.php?asc_action=GetCoreUpgradeFile';
				break;
			case 'Local_StoreStatus':
				$value = "javascript:openURLinNewWindow('store_settings_general.php', 'General Settings');";
				break;
			/*case 'Local_StoreOnline':
				$value = 'index.php?asc_action=UpdateGeneralSettings&store_online=1';
				break;
			case 'Local_SuccessMessage':
				$value = $this->getLocal_SuccessMessage();
				break;*/
			default:
				$value = getKeyIgnoreCase($tag, $this->_Template_Contents);
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