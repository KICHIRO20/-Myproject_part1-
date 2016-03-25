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
 * This action is called if there is no 'asc_action' parameter in
 * HTTP GET/POST request.
 *
 * @ describe the parameters of this action.
 *
 * @package Modules_Manager
 * @author Vadim Lyalikov
 */
class ActionIsNotSetAction extends AjaxAction
{
	function ActionIsNotSetAction()
	{
	    global $application;
	}

        /**
	 * @ describe the function ReinstallModuleAction->.
	 */
	function onAction()
	{
	    global $application;
	}
}
?>