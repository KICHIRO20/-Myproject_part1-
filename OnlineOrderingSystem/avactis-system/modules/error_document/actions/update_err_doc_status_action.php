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
 * Error_Document module.
 *
 * @package Error_Document
 * @author HBWSL
 */

class UpdateErrDocStatus extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     */
    function UpdateErrDocStatus()
    {
    }

    /**
     *
     */
    function onAction()
    {
		global $application;
		$request = $application -> getInstance('Request');
        $errdoc_status = $request -> getValueByKey('errdoc_status');
		$arr_err_code = array(404=>'404.php', 500=>'internal-server-error.html');
		if($errdoc_status)
			modApiFunc('Error_Document','writeHtaccessCode',$arr_err_code);
		else
			modApiFunc('Error_Document','removeHtaccessCode');
		modApiFunc('Error_Document','updateStatus',$errdoc_status);
		$msg = ($errdoc_status) ? getxmsg('ERRD','ENABLED_ERRDOC') : getxmsg('ERRD','DISABLED_ERRDOC');
		$data = array(
			'errdoc_msg'=> $msg,
		);
		echo json_encode($data);
		exit;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Corresponding View file.
     */

    /**#@-*/
}
?>