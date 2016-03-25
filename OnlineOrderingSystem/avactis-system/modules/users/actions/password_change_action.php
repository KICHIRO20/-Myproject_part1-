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

_use(dirname(__FILE__).'/password_update_action.php');

/**
 * Sign In Action.
 *
 * @package Users
 * @author Alexander Girin
 */
class PasswordChange extends PasswordUpdate
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * SignIn action constructor.
     */
    function PasswordChange()
    {
        global $application;
        $request = new Request();
        $request->setView  ( 'AdminPasswordChange' );
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
        $nErrors = 0;

        if ($SessionPost["SendByEmail"]=="true")
        {
            $SessionPost["SendByEmail"] = true;
        }
        else
        {
            $SessionPost["SendByEmail"] = false;
        }
        if(!$this->isEqNewAndVerifyPasswords($SessionPost['New_Password'], $SessionPost['Verify_New_Password']))
        {
            $nErrors++;
            $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_002";
        }
        if (!$this->isAllFieldsFilled(array($SessionPost['New_Password'],
                                            $SessionPost['Verify_New_Password']
                                           )
                                     )
           )
        {
            $nErrors++;
            $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_008";
        }
        if (!$this->isValidNewPasswordLength($SessionPost['New_Password']))
        {
            $nErrors++;
            $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_004";
        }
        if (!$this->isComplicatedNewPassword($SessionPost['New_Password']))
        {
            $nErrors++;
            $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_005";
        }
        if (!$this->isDifferentFromEmail($SessionPost['AdminEmail'], $SessionPost['New_Password']))
        {
            $nErrors++;
            $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_007";
        }

        $request = new Request();
        if($nErrors == 0)
        {
			$SessionPost["ViewState"]["hasCloseScript"] = "true";
            modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

            $need_update = false;
            if ($SessionPost["SendByEmail"])
            {
                modApiFunc("Users", "letterAboutNewPassword", $SessionPost['AdminEmail'], $SessionPost['New_Password_Open']);
                $need_update = true;
            }
            modApiFunc("Users", "updateAcountInfo", modApiFunc("Users", "getSelectedUserID"), $SessionPost['AdminEmail'], $SessionPost['New_Password'], $need_update);

            $request->setView('AdminPasswordChange');
        }
        else
        {
            $request->setView  ( 'AdminPasswordChange' );
            modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        }

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