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
 * This action is responsible for uninstall some action.
 *
 * @ describe the parameters of this action
 *
 * @package Modules_Manager
 * @author Alexey Kolesnikov
 */
class UninstallExtensionAction extends AjaxAction
{

	function UninstallExtensionAction()
	{
	    global $application;
		$request = $_POST;
		$this->extn_name = $request['extn_name'];
	}

	/**
	 * @ describe the function ReinstallModuleAction->.
	 */
	function onAction()
	{
	    global $application;
	    $request = $application->getInstance('Request');
	    $this->extn_name=$request->getValueByKey('extn_name');
	    $extension_name=$this->extn_name;
	    if ($this->extn_name!='Modules_Manager')
	    {
	   	$pathdir = $application->getAppIni('PATH_ADD_MODULES_DIR');
	    	$this->extn_name = strtolower($this->extn_name);
	    	if(is_dir($pathdir.$this->extn_name) && $this->extn_name!="")
		{
			modApiFunc('Extension_Manager','updateExtensionStatus',$extension_name,true);
			modApiFunc("Modules_Manager", "initModules");
			$moduleInfo = modApiFunc('Modules_Manager', 'getModuleInfoFromFile', '/avactis-extensions/'.$this->extn_name);
			$backup_created = modApiFunc('Extension_Manager', 'backup_extension',$moduleInfo);
			if($backup_created){
				modApiFunc('Modules_Manager', 'uninstallModule', $moduleInfo);
				modApiFunc("Session","set","UninstallMessage",1);
			}else{
				modApiFunc('Extension_Manager','updateExtensionStatus',$extension_name,false);
				modApiFunc("Session","set","ErrorMessage","ERR_EXTN_BACKUP_FAILED");
			}
		}else{
			modApiFunc("Session","set","ErrorMessage","ERR_UNINSTALL_FAILED");
		}


	    }

	}

	/** The module id for uninstallation */
	var $extn_name;
}
?>