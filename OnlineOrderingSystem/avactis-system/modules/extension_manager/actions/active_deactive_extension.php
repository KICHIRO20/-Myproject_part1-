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
class ActivateDeactivateExtension extends AjaxAction
{
	function ActivateDeactivateExtension()
	{
	}

	function onAction()
	{
		global $application;
		$request = $application->getInstance('Request');
		$extennsion_name=$request->getValueByKey('extn_name');
		$extension_display_name=$request->getValueByKey('extn_display_name');
		$extennsion_status=$request->getValueByKey('status')=='active'?true:false;
		modApiFunc('Extension_Manager','updateExtensionStatus',$extennsion_name,$extennsion_status);
		modApiFunc("Session","set","StatusMessage",$extennsion_status);
		modApiFunc("Session","set","Extension_Name",isset($extension_display_name)?$extension_display_name:$extennsion_name);

	}
}
?>