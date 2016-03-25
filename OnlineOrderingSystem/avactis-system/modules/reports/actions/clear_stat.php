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

class ClearStat extends AjaxAction
{
	function onAction()
	{
		global $application;
		$request = $application->getInstance('Request');

		$ResetReports = array();
		$ResetReports = $_POST;

		switch ($ResetReports['ResetReportScope'])
		{
			case 'ClearVisitorStat':
				modApiFunc('Reports','clearVisitorStat');
				break;

			case 'ClearSaleStat':
				modApiFunc('Reports','clearSaleStat');
				break;

			case 'ClearAllStat':
				modApiFunc('Reports','clearAllStat');
				break;

			default:

				break;
		}
	}
}

?>