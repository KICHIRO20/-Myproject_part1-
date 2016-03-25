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
 * Sign In Action.
 *
 * @package Users
 * @author Alexander Girin
 */
class SignIn extends AjaxAction
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
    function SignIn()
    {
        global $application;
    }

    /**
     * Identifies and authenticates a user.
     *
     * @param string $email - admin email
     * @param $password - admin password
     * @param array $acountInfo - reference to the array, that contains account info
     * @return boolean true, if a user is identified and authenticated,
     * false, otherwise
     */
    function isValidAcount($email, $password, &$acountInfo)
    {
        $retval = false;
        $acountInfo = modApiFunc("Users", "getAcountInfoByEmail", $email);
        if (sizeof($acountInfo)==1)
        {
            $acountInfo = $acountInfo[0];
            if ($acountInfo['password']==$password)
            {
                $retval =true;
            }
        }
        return $retval;
    }

    /**
     * Checks, if the password is changed.
     *
     * @param array $acountInfo - reference to the array, that contains account info
     * @return boolean true, if the password was changed, false, otherwise
     */
    function isPasswordChanged(&$acountInfo)
    {
        if ($acountInfo['password']!=$acountInfo['old_password'])
        {
            modApiFunc("Users", "setPasswordUpdate");
            return true;
        }
        return false;
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
        if (isset($SessionPost['RememberEmail'])&&$SessionPost['RememberEmail']=='true')
        {
            if (!isset($_COOKIE['ac_remember_email']))
            {
                setcookie('ac_remember_email', '', -3600);
            }
            setcookie('ac_remember_email', $SessionPost['AdminEmail'], time()+31536000);
        }
        else
        {
            if (isset($_COOKIE['ac_remember_email']))
            {
                setcookie('ac_remember_email', '', -3600);
            }
        }

        $nErrors = 0;
        $acountInfo = NULL;
        if(!$this->isValidAcount($SessionPost['AdminEmail'], $SessionPost['Password'], $acountInfo))
        {
            $nErrors++;
            $SessionPost["ViewState"]["ErrorsArray"][] = "SIGNIN_001";
            modApiFunc("Users", "incorrectLogin");
        }

        $request = new Request();
        if($nErrors == 0)
        {
//            unset($SessionPost["ViewState"]["ErrorsArray"]);
            modApiFunc("Users", "setCurrentUserID", $acountInfo['id']);
            if ($this->isPasswordChanged($acountInfo))
            {
                $request->setView('AdminPasswordUpdate');
            }
            else
            {
                // Commented by AF: $request->setView('Maximize');
                $request->setView('HomeTab');
            }
            modApiFunc("Users", "saveState");
            modApiFunc("Users", "correctLogin", $acountInfo['id']);
        }
        else
        {
            $request->setView  ( 'AdminSignIn' );
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