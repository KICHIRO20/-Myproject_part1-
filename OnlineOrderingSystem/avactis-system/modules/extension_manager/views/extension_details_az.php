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
 * Get Extension Details from Market Place .
 *
 * @package Extension_Manager
 * @author Avactis
 */
class ExtensionDetails{

	function ExtensionDetails(){

	}
	function output(){
		global $application;
		$request=$application->getInstance('Request');
		$ext_name=$request->getValueByKey('ext_name');
		$update=$request->getValueByKey('update');
		$reset=$request->getValueByKey('reset');
		$upgrade=$request->getValueByKey('upgrade');
		$isReset=isset($reset)?true:false;
		$isUpgrade=isset($upgrade)?true:false;
		if($isReset){
				$this->resetExtensionDetails($ext_name);
				return;
		}
		if(isset($ext_name)){
			$extensionDetail=$this->getExtensionDetail($ext_name);
			if($extensionDetail==false){
		 		loadCoreFile('bouncer.php');
				$bnc = new Bouncer();
				$bnc->setMethod('POST');
				$data=modApiFunc("Extension_Manager", 'getLicenseDetail');
				$data['ext_name']=$ext_name;
				if(isset($update)){
					$data['previous']=$isUpgrade;
				}
				$bnc->setPOSTstring($bnc->prepareDATAstring($data));
				$bnc->setURL($application->getAppIni('MARKETPLACE_SERVER').'/download_extension.php?asc_action=GetExtensionDetails');
				$bnc->setProto('HTTPS');
				$result = $bnc->RunRequest();
				if ($result!=false && $bnc->responseCode < 400){
					$response= unserialize($result['body']);
					if(isset($response) && !empty($response)){
						$this->setExtensionDetails($ext_name, $response);
						return $response;
					}
					return $this->noOutput('A server connection error has occurred.  Please try again in a few moments');
				}
			}else {
				return $extensionDetail;
			}
		}
		$this->noOutput();
	}

	/**
	 * Initilizing cache for Extension Details
	 */
	function initExtension(){
		global $application;
		$extensionDetailCache = $application->getMMCache();
		$this->extensionList=$extensionDetailCache->read('Extension_Cache_Detail');
	}

	/**
	 * Get Extension Detail
	 * @param string $extension_name
	 * @return searilize data|boolean
	 */
	function getExtensionDetail($extension_name){
		if(!isset($this->extensionList) || empty($this->extensionList)){
			$this->initExtension();
		}
		$extensionDetail=$this->extensionList[$extension_name];
		if(isset($extensionDetail) && !empty($extensionDetail)){
			$createtime=$extensionDetail['create_time'];
			$currentTime=strtotime('now');
			if(($currentTime-$createtime) < 86400){
				return $extensionDetail['detail'];
			}
		}
		return false;
	}

	function setExtensionDetails($extension_name,$details){
		global $application;
		if(!isset($this->extensionList) || empty($this->extensionList)){
			$this->initExtension();
		}

		$this->extensionList[$extension_name]['detail']=$details;
		$this->extensionList[$extension_name]['create_time']=strtotime('now');

		$extensionDetailCache = $application->getMMCache();
		$extensionDetailCache->write('Extension_Cache_Detail',$this->extensionList);


	}

	function resetExtensionDetails($extension_name){
		global $application;
		if(!isset($this->extensionList) || empty($this->extensionList)){
			$this->initExtension();
		}

		$this->extensionList[$extension_name]['create_time']=0;

		$extensionDetailCache = $application->getMMCache();
		$extensionDetailCache->write('Extension_Cache_Detail',$this->extensionList);
	}

	function noOutput($message=""){
		global $application;
		return $application->getInstance('TmplFiller')->fill("extension_manager/list/", "error.tpl.html",array('err_msg'=>$message));
	}
	var $extensionList;
}
?>