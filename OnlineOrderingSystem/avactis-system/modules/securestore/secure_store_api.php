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
 * Polling module
 *
 * @package Cart
 * @author Pragati, Sanket
 */
class SecureStore
{
	//------------------------------------------------
	//               PUBLIC DECLARATION
	//------------------------------------------------

	/**#@+
	 * @access public
	 */

	/**
	 * Module constructor.
	 */
	function SecureStore()
	{
		$this->ErrorMessage = NULL;
		$this->ModifiedFileResult = NULL;
		$this->NewlyAddedFileResult = NULL;
		$this->MailSendResultMessage = NULL;
	}

	function loadState()
	{
		//$this->ErrorMessage = NULL;
		if(modApiFunc('Session', 'is_Set', 'ErrorMessage'))
		{
			$this->setErrorMessage(modApiFunc('Session', 'get', 'ErrorMessage'));
		}
		else
		{
			$this->setErrorMessage = '';
		}
		if(modApiFunc('Session', 'is_Set', 'MailSendResultMessage'))
		{
			$this->setMailSendResultMessage(modApiFunc('Session', 'get', 'MailSendResultMessage'));
		}
		else
		{
			$this->setMailSendResultMessage = '';
		}
		if(modApiFunc('Session', 'is_Set', 'ModifiedFileResult'))
		{
			$this->setModifiedFileResult(modApiFunc('Session', 'get', 'ModifiedFileResult'));
		}
		else
		{
			$this->setModifiedFileResult = '';
		}
		if(modApiFunc('Session', 'is_Set', 'NewlyAddedFileResult'))
		{
			$this->setNewlyAddedFileResult(modApiFunc('Session', 'get', 'NewlyAddedFileResult'));
		}
		else
		{
			$this->setNewlyAddedFileResult = '';
		}
	}


	/**
	 * Installs the specified module in the system.
	 *
	 * The install() method is called statically.
	 * To call other methods of this class from this method,
	 * the static call is used, for example,
	 * Cart::getTables() instead of $this->getTables()
	 */
	function install()
	{
	}

	/**
	 * Deinstalls the module.
	 *
	 * The uninstall() method is called statically.
	 * To call other methods of this class from this method,
	 * the static call is used, for example,
	 * Cart::getTables() instead of $this->getTables().
	 *
	 * @todo finish the functions on this page
	 */
	function uninstall()
	{
	}

	/**
	 * Checks if the module was installed.
	 *
	 * @todo finish the functions on this page
	 * @return
	 */
	function isInstalled()
	{
	}
	/**
	 * Sets the current checkout step ID.
	 *
	 * @return mixed Current step ID or NULL if category is not selected
	 */
	function setErrorMessage($data)
	{
		$this->ErrorMessage = $data;
	}

	/**
	 * Gets the current checkout step ID.
	 *
	 * @return mixed Current step ID or NULL if step is not defined
	 */
	function getErrorMessage()
	{
		return $this->ErrorMessage;
	}

	/**
	 * Sets the current checkout step ID.
	 *
	 * @return mixed Current step ID or NULL if category is not selected
	 */
	function setMailSendResultMessage($data)
	{
		$this->MailSendResultMessage = $data;
	}

	/**
	 * Gets the current checkout step ID.
	 *
	 * @return mixed Current step ID or NULL if step is not defined
	 */
	function getMailSendResultMessage()
	{
		return $this->MailSendResultMessage;
	}

	/**
	 * Sets the current checkout step ID.
	 *
	 * @return mixed Current step ID or NULL if category is not selected
	 */
	function setModifiedFileResult($data)
	{
		$this->ModifiedFileResult = $data;
	}

	/**
	 * Gets the current checkout step ID.
	 *
	 * @return mixed Current step ID or NULL if step is not defined
	 */
	function getModifiedFileResult()
	{
		return $this->ModifiedFileResult;
	}

	/**
	 * Sets the current checkout step ID.
	 *
	 * @return mixed Current step ID or NULL if category is not selected
	 */
	function setNewlyAddedFileResult($data)
	{
		$this->NewlyAddedFileResult = $data;
	}

	/**
	 * Gets the current checkout step ID.
	 *
	 * @return mixed Current step ID or NULL if step is not defined
	 */
	function getNewlyAddedFileResult()
	{
		return $this->NewlyAddedFileResult;
	}
	/**
	 * Gets the current checkout step ID.
	 *
	 * @return mixed Current step ID or NULL if step is not defined
	 */
	function CheckShellEnabled()
	{
		if(function_exists('shell_exec')) {
			$value = "Y";
		}
		else{
			$value = "N";
		}
		return $value;
	}

	/**
	 * Gets the Modified file list.
	 Access - the last time the file was read
	 Modify - the last time the file was modified (content has been modified)
	 Change - the last time meta data of the file was changed (e.g. permissions)
	 ctime atime mtime
	 $computername = "pragati-Vostro-1440";
	 $ip = gethostbyname($computername);
	 exec('find -type f -mtime -'. print $cmd , $out);
	 exec("ping ".$ip." -n 1 -w 90 && exit", $out);
	 exec('whoami', $out);
	 */
	function ModifiedFileList($pass = NULL)
	{
		$Modified = '';
		$mod = array();
		if($pass == ""){
			$mod = shell_exec('find ../../avactis-system/ -not -path "*/cache/*" -type f -mtime -3');
		}
		else{
			$mod = shell_exec('find ../../avactis-system/ -not -path "*/cache/*" -type f -mtime -'.$pass);
		}
		$final = str_replace("../../avactis-system","<br/>../../avactis-system",$mod);
		modApiFunc('Session','set','ModifiedFileResult',$final);
	}

	/**
	 * Gets the Modified file list.
	 */
	function NewlyAddedFileList($pass = NULL)
	{
		$newFile = '';
		$new = array();
		$value = modApiFunc('SecureStore','checkSessionForModifiedFile');
		if($pass == ""){
			$new = shell_exec('find ../../avactis-system/ -not -path "*/cache/*" -type f -ctime -3');
		}
		else{
			$new = shell_exec('find ../../avactis-system/ -not -path "*/cache/*" -type f -ctime -'.$pass);
		}
		$newArray = $new;
		$aTemp = explode('<br/>', $value);
		foreach($aTemp as $temp){
			$newArray = str_replace($temp,"",$newArray);
		}
		$newfinal = str_replace("../../avactis-system","<br/>../../avactis-system",$newArray);
		modApiFunc('Session','set','NewlyAddedFileResult',$newfinal);
	}

	function SendMailToAdmin(){
		$value = "";
		$Nvalue = "";
		$Mod = modApiFunc('SecureStore','checkSessionForModifiedFile');
		$New = modApiFunc('SecureStore','checkSessionForNewlyAddedFile');

		$Msg = getMsg('SS', 'FIND_UPDATE_NO_RESULT_MSG');
		$NewA = getMsg('SS', 'NEWLY_ADDED_FILE');
		$NewM = getMsg('SS', 'MODIFIED_FILE');
		if($Mod !== $Msg) {
			$value = $NewM."<br/><br/>".$Mod;
		}
		if($New !== $Msg){
			$Nvalue = "<br/><br/>".$NewA."<br/><br/>".$New;
		}
		if($value !== "" || $Nvalue !== ""){
			$Mail_Body = $value.$Nvalue;
			$email = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL);
			loadCoreFile('ascHtmlMimeMail.php');
			$mail = new ascHtmlMimeMail();
			$mail -> setText($Mail_Body);
			$mail -> setSubject(getMsg('SS', 'SS_SEND_FILES_DETAILS_SUBJECT'));
			$mail -> setFrom(modApiFunc('Notifications', 'getExtendedEmail', $email, 'EMAIL_STORE_OWNER', true));
			$mail -> send(array($email));
			if(!$mail -> send(array($email))) {
				CTrace::wrn ('Mailer error: ' . $mail->ErrorInfo);
			} else {
				CTrace::inf('Mail has been sent for file changes in last days');
			}
			$tl_type = getMsg('NTFCTN','NTFCTN_TL_TYPE');
			$to = $email;
			$subj = getMsg('SS', 'SS_SEND_FILES_DETAILS_SUBJECT');
			$tl_header = getMsg('SS', 'SS_SEND_FILES_DETAILS_SUBJECT');
			$tl_body = $Mail_Body;
			modApiFunc('Timeline', 'addLog', $tl_type, $tl_header, $tl_body);
			$Msg = getMsg('SS', 'MAIL_SENT');
			modApiFunc('Session','set','MailSendResultMessage',$Msg);
		}
		else{
			$Msg = getMsg('SS', 'ERROR_TO_SEND_MAIL');
			modApiFunc('Session','set','MailSendResultMessage',$Msg);
		}
		modApiFunc('Session','set','ModifiedFileResult',"");
		modApiFunc('Session','set','NewlyAddedFileResult',"");
	}

	function checkSessionForMailSendResultMessage()
	{
		if(modApiFunc('Session','is_set','MailSendResultMessage')){
			$value = modApiFunc('Session','get','MailSendResultMessage');
			if($value == ""){
				$value = "";
			}
		}
		else{
			$value = "";
		}
		return $value;
	}

	function checkSessionForModifiedFile()
	{
		$Msg = getMsg('SS', 'FIND_UPDATE_NO_RESULT_MSG');
		if(modApiFunc('Session','is_set','ModifiedFileResult')){
			$value = modApiFunc('Session','get','ModifiedFileResult');
			if($value == ""){
				$value = $Msg;
			}
		}else{
			$value = $Msg;
		}
		return $value;
	}

	function checkSessionForNewlyAddedFile()
	{
		$Msg = getMsg('SS', 'FIND_UPDATE_NO_RESULT_MSG');
		if(modApiFunc('Session','is_set','NewlyAddedFileResult')){
			$value = modApiFunc('Session','get','NewlyAddedFileResult');
			if($value == ""){
				$value = $Msg;
			}
		}else{
			$value = $Msg;
		}
		return $value;
	}

	function CheckErrorMessage()
	{
		if(modApiFunc('Session','is_set','ErrorMessage'))
			$value = modApiFunc('Session','get','ErrorMessage');
		else
			$value = "";

		return $value;
	}

	/**
	 * Gets the array of meta description of module tables.
	 *
	 * @todo May be add more tables
	 * @return array - meta description of module tables
	 */


	function getTables()
	{
		$tables = array ();
	}


	//------------------------------------------------
	//              PRIVATE DECLARATION
	//------------------------------------------------

	/**#@+
	 * @access private
	 */

	/*
	 * polling Content (stored in session)
	 */
	var $run_shell;

}
?>