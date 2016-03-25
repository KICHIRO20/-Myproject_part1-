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
 * Password Recovery Action.
 *
 * @package Users
 * @author Alexander Girin
 */
class PasswordRecovery extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Password Recovery action constructor.
     */
    function PasswordRecovery()
    {
        global $application;
    }

    /**
     * Checks, if a specified email exists in the system.
     *
     * @param string $email - admin email
     * @param array $acountInfo - reference to the array that contains account info
     * @return boolean true, if email exists, false otherwise
     */
    function isValidEmail($email, &$acountInfo)
    {
        $retval = true;
        $acountInfo = modApiFunc("Users", "getAcountInfoByEmail", $email);
        if (sizeof($acountInfo)==0)
        {
            $retval=false;
        }
        return $retval;
    }

    /**
     * Action process.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = array();
        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        $SessionPost = $_POST;
        $SessionPost["ViewState"] = array();
        $nErrors = 0;
        $acountInfo = NULL;
        if (!$this->isValidEmail($SessionPost['AdminEmail'], $acountInfo))
        {
            $nErrors++;
            $SessionPost["ViewState"]["ErrorsArray"][] = "PSWREC_001";
        }

        if($nErrors == 0)
        {

            modApiFunc("Users", "generateNewAdminPassword", $SessionPost['AdminEmail']);

        }

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        $request = new Request();
        $request->setView  ( 'AdminPasswordRecovery' );
        $application->redirect($request);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */


    /**#@-*/

}
?>