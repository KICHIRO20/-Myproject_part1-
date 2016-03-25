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

class FindUpdatedFile
{

	function FindUpdatedFile()
	{

	}

	function output()
	{
		global $application;
		$_request = new Request();
		$_request->setView("MM_ListView");
		$uninstallHref = $_request->getURL();
		$this->MessageResources = &$application->getInstance('MessageResources');
		$this->TemplateFiller = $application->getInstance('TmplFiller');
		//$this->TemplateFiller->setTemplatePath("avactis-extensions/");
		$application->registerAttributes(array(
					'Local_FormActionURL',
					'Local_FormSendMailActionURL',
					'Local_ErrorMessage',
					'Local_CheckShellEnabled',
					'Local_ModifiedResultOutput',
					'Local_NewlyAddedResultOutput',
					'Local_MailResultMessage',
					));

		// $application->registerAttributes($this->_Template_Contents);
		//  $return_html_code.=$this->mTmplFiller->fill("checkout/checkout-info/", "error.tpl.html", array());
		$value = modApiFunc('SecureStore','CheckShellEnabled');
		if($value == 'Y'){
			return $this->TemplateFiller->fill("securestore/", "container.tpl.html", array());
		}else{
			return $this->TemplateFiller->fill("securestore/", "display_error.tpl.html", array());
		}
	}

	/*
	 * @ describe the function ManageOrders->.
	 */
	function getTag($tag)
	{
		global $application;
		$value = null;
		switch ($tag)
		{
			case 'Local_FormActionURL':
				$r = new Request();
				$r->setView(CURRENT_REQUEST_URL);
				$r->setAction('FindUpdatedFileAction');
				$value = $r->getURL();
				break;

			case 'Local_FormSendMailActionURL':
				$r = new Request();
				$r->setView(CURRENT_REQUEST_URL);
				$r->setAction('SendResultMailAction');
				$value = $r->getURL();
				break;

			case 'Local_ErrorMessage':
				$value = modApiFunc('SecureStore','CheckErrorMessage');
				break;

			case 'Local_MailResultMessage':
				$value = modApiFunc('SecureStore','checkSessionForMailSendResultMessage');
				if(modApiFunc('Session','is_set','MailSendResultMessage')){
					modApiFunc('Session','set','MailSendResultMessage',"");
				}
				break;

			case 'Local_CheckShellEnabled':
				$chk = modApiFunc('SecureStore','CheckShellEnabled');
				if($chk == 'Y') {
					$value = "";
				}
				else{
					$value = getMsg('SS', 'SHELL_NOT_ENABLED');
				}
				break;

			case 'Local_ModifiedResultOutput':
				$value = modApiFunc('SecureStore','checkSessionForModifiedFile');
				break;

			case 'Local_NewlyAddedResultOutput':
				$value = modApiFunc('SecureStore','checkSessionForNewlyAddedFile');
				break;

			default:
				break;
		}
		return $value;
	}


	var $TemplateFiller;
}
?>