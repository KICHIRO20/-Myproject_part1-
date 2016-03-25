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
 * @package CustomerAccount
 * @author Egor V. Derevyankin
 *
 */

class FindUpdatedFileAction extends AjaxAction
{

	function FindUpdatedFileAction()
	{

	}

	function onAction()
	{
		global $application;
		$request = new Request();
		$find = $request->getValueByKey('find');
		if (preg_match('/^[0-9]*(\.[0-9]+)?$/',$find)) {
			//--------- Get Modified File list------------------
			modApiFunc('SecureStore','ModifiedFileList',$find);
			//--------- Get Newly Added File list------------------
			modApiFunc('SecureStore','NewlyAddedFileList',$find);
			modApiFunc('Session','set','MailSendResultMessage',"");
		} else {
			modApiFunc('Session','set','ModifiedFileResult',"");
			modApiFunc('Session','set','NewlyAddedFileResult',"");
		}
		//--------- Return To View Page------------------
		$req_to_redirect = new Request();
		$req_to_redirect -> setView(CURRENT_REQUEST_URL);
		$req_to_redirect -> setKey('identifier', 'FindUpdatedFile');
		$application->redirect($req_to_redirect);
	}
};

?>