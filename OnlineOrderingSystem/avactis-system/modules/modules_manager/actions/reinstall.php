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
 * This action is responsible for reinstall some action.
 *
 * @ describe the parameters of this action
 *
 * @package Modules_Manager
 * @author Alexey Kolesnikov
 */
class ReinstallModuleAction extends AjaxAction
{

	function ReinstallModuleAction()
	{
	    global $application;
		$request = &$application->getInstance('Request');

		$this->moduleName = $request->getValueByKey('module_name');
	}

	/**
	 * @ describe the function ReinstallModuleAction->.
	 */
	function onAction()
	{
	    global $application;

	    if ($this->moduleName)
	    {
    	    modApiFunc('Modules_Manager', "includeAPIFileOnce", $this->moduleName);
    	    $moduleInfo = modApiFunc('Modules_Manager', 'getModuleInfoFromFile', $this->moduleName);
    		modApiFunc('Modules_Manager', 'reinstallModule', $moduleInfo);
	    }

	}

	/** The module id for reinstallation */
	var $moduleName;
}
?>