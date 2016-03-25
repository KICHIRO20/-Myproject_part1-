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
 * This action is responsible for setting marketplace asc_action url.
 *
 * @ describe the parameters of this action
 *
 * @package Modules_Manager
 * @author HBWSL
 */
class GetCoreUpgradeFile extends AjaxAction
{
	function GetCoreUpgradeFile()
	{
	}

	function onAction()
	{
		global $application;

		$request = $_POST;
		$_state = modApiFunc("License", "checkLicense");
		$licenseInfo = modApiFunc("License","getLicenseInfo", $_state);
		if(class_exists('ZipArchive'))
		{
			$this->marketplace_server = $application->getAppIni('MARKETPLACE_SERVER');

			$data = array(
					'license' 	  => $licenseInfo["license_key"],
					'store_url'	  => $_SERVER['HTTP_HOST'].$_SERVER['CONTEXT_PREFIX'],
					'store_version'   => PRODUCT_VERSION_NUMBER,
				     );
			loadCoreFile('bouncer.php');
			$bnc = new Bouncer();
			$bnc->setMethod('POST');
			$bnc->setPOSTstring($bnc->prepareDATAstring($data));
			$bnc->setURL($this->marketplace_server."/download_extension.php?asc_action=SendLatestCoreFile");
			$bnc->setProto('HTTPS');
			$result = $bnc->RunRequest();
			$response = $result['body'];
			if ($response!=false && $bnc->responseCode < 400){
				if(strpos($response, "ERR_")===0){
					modApiFunc("Session","set","ErrorMessage",$response);
				}else{
					$file = fopen($application->getAppIni('PATH_CACHE_DIR')."avactis-upgrade.zip",'w+');
					fwrite($file,$response);
					fclose($file);
					$core_response = modApiFunc("Extension_Manager","upgradeCore");
					if(strpos($core_response, "ERR_")===0){
						modApiFunc("Session","set","ErrorMessage",$core_response);
					}else{
						CCacheFactory::clearAll();
						$request = new Request();
						$request->setView(CURRENT_REQUEST_URL);
						$application->redirect($request);
					}
				}
			}else{
				$error = "ERR_MARKETPLACE_NOT_AVAILABLE";
				modApiFunc("Session","set","ErrorMessage",$error);
			}
		}else{
			$error = "ERR_CHK_ZIP_OR_PERMISSIONS";
			modApiFunc("Session","set","ErrorMessage",$error);
		}
	}
}
?>