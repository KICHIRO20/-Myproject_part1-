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
class GetMarketPlaceExtension extends AjaxAction
{
	function GetMarketPlaceExtension()
	{
	}

	function onAction()
	{
		global $application;
		$request = $_POST;
		$extension_name =  $request["extn_name"];
		$_state = modApiFunc("License", "checkLicense");
		$licenseInfo = modApiFunc("License","getLicenseInfo", $_state);
		$data = array(
				'license' 	  => $licenseInfo["license_key"],
				'store_url'	  => $_SERVER['HTTP_HOST'].$_SERVER['CONTEXT_PREFIX'],
				'extn_name'  => $extension_name,
				'store_version'   => PRODUCT_VERSION_NUMBER,
			     );
		$marketplace_server = $application->getAppIni('MARKETPLACE_SERVER');

		loadCoreFile('bouncer.php');
		$bnc = new Bouncer();
		$bnc->setMethod('POST');
		$bnc->setPOSTstring($bnc->prepareDATAstring($data));
		$bnc->setURL($marketplace_server."/download_extension.php?asc_action=SendExtension");
		$bnc->setProto('HTTPS');
		$result = $bnc->RunRequest();
		$response = $result['body'];
		if ($response!=false && $bnc->responseCode < 400){
			if(strpos($response, "ERR_")===0)
			{
				modApiFunc("Session","set","ErrorMessage",$response);
				//return $response['err_msg'];
			}
			else{
				$file = fopen($application->getAppIni(PATH_CACHE_DIR).$extension_name.".zip",'w+');
				fwrite($file,$response);
				fclose($file);
				$installed = modApiFunc("Extension_Manager","installExtension",$extension_name);
				if($installed){
					modApiFunc("Session","set","InstallMessage","1");
					CCacheFactory::clearAll();
					$request = new Request();
					$request->setView(CURRENT_REQUEST_URL);
				        $request->setKey('identifier', 'ExtensionManager_ListView');

					$application->redirect($request);
				}else{
					modApiFunc("Session","set","ErrorMessage","ERR_CHK_ZIP_OR_PERMISSIONS");
				}
			}
		}else{
                                        modApiFunc("Session","set","ErrorMessage","ERR_MARKETPLACE_NOT_AVAILABLE");
		}
	}
}
?>