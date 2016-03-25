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
class PasswordUpdate extends AjaxAction
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
    function PasswordUpdate()
    {
        global $application;
    }

    /**
     * Checks, whether the email matches the regular expressions.
     *
     * @param string $email - admin email
     * @return boolean true, if email matches , false otherwise
     *
    function isValidEmail($email)
    {
        $retval = true;
        if (! preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,6})$/i", $email))
        {
            $retval=false;
        }
        return $retval;
    }
*/

    /**
     * Checks, if the old password is valid.
     *
     * @param string $password - admin password
     * @param array $acountInfo - reference to the array that contains account info
     * @return boolean true, if password matches, false, otherwise
     */
    function isValidOldPassword($password, &$acountInfo)
    {
        $retval = false;
        $acountInfo = modApiFunc("Users", "getAcountInfoById", modApiFunc("Users", "getCurrentUserID"));

        if (sizeof($acountInfo)==1)
        {
            $acountInfo = $acountInfo[0];
            if ($acountInfo['password']==md5($password))
            {
                $retval =true;
            }
        }
        return $retval;
    }

    /**
     * Checks, whether the password confirmation is valid.
     *
     * @param string $new_password - a new admin password
     * @param string $verify_new_password - new password confirmation
     * @return boolean true, if passwords match, false, otherwise
     */
    function isEqNewAndVerifyPasswords($new_password, $verify_new_password)
    {
        $retval = false;
        if ($new_password==$verify_new_password)
        {
            $retval =true;
        }
        return $retval;
    }

    /**
     * Checks, whether all required fields are filled out.
     *
     * @param string $fields - field array
     * @return boolean true ,if all fields are filled out, false, otherwise
     */
    function isAllFieldsFilled($fields)
    {
        foreach ($fields as $value)
        {
            if ($value == '')
            {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks, whether the password length is valid.
     *
     * @param string $new_password - a new admin password
     * @return boolean true, if the length is from 8 to 32, false, otherwise
     */
    function isValidNewPasswordLength($new_password)
    {
        $retval = true;
        $lenght = _ml_strlen($new_password);
        if ($lenght<8||$lenght>32)
        {
            $retval = false;
        }
        return $retval;
    }

    /**
     * Checks, if the password is complicated.
     *
     * @param string $new_password - a new admin password
     * @return boolean true, if password is complicated enough, false otherwise
     */
    function isComplicatedNewPassword($new_password)
    {
        $groups = 0;
        if (preg_match("/[0-9]/", $new_password))
        {
            $groups++;
        }
        if (preg_match("/[a-z]/", $new_password))
        {
            $groups++;
        }
        if (preg_match("/[A-Z]/", $new_password))
        {
            $groups++;
        }
        if (preg_match("/[\\x21-\\x2f\\x3a-\\x40]/", $new_password))
        {
            $groups++;
        }
        if ($groups>=2)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Checks, if a new password matches the old one.
     *
     * @param string $old_password - the old admin password
     * @param string $new_password - the new admin password
     * @return boolean true, if passwords are different, false, otherwise
     */
    function isDifferentFromOldPassword($old_password, $new_password)
    {
        $retval = true;
        if ($old_password == $new_password)
        {
            $retval = false;
        }
        return $retval;
    }

    /**
     * Checks, if a new password match the email address.
     *
     * @param string $email - admin email
     * @param string $new_password - the new admin password
     * @return boolean true, if passwords are different, false, otherwise
     */
    function isDifferentFromEmail($email, $new_password)
    {
        $retval = true;
        if ($email == $new_password)
        {
            $retval = false;
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

        $nErrors = 0;
        $acountInfo = NULL;
        if (!modApiFunc("Users", "isValidEmail",($SessionPost['AdminEmail'])))
//        if (!$this->isValidEmail($SessionPost['AdminEmail']))
        {
            $nErrors++;
            $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_000";
        }
        if(!$this->isValidOldPassword($SessionPost['Old_Password'], $acountInfo))
        {
            $nErrors++;
            $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_001";
        }
        if(!$this->isEqNewAndVerifyPasswords($SessionPost['New_Password'], $SessionPost['Verify_New_Password']))
        {
            $nErrors++;
            $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_002";
        }
        if (!$this->isAllFieldsFilled(array($SessionPost['AdminEmail'],
                                            $SessionPost['Old_Password'],
                                            $SessionPost['New_Password'],
                                            $SessionPost['Verify_New_Password']
                                           )
                                     )
           )
        {
            $nErrors++;
            $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_003";
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
        if (!$this->isDifferentFromOldPassword($SessionPost['Old_Password'], $SessionPost['New_Password']))
        {
            $nErrors++;
            $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_006";
        }
        if (!$this->isDifferentFromEmail($SessionPost['AdminEmail'], $SessionPost['New_Password']))
        {
            $nErrors++;
            $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_007";
        }

        $request = new Request();
        if($nErrors == 0)
        {
            // Commented by AF: $request->setView('Maximize');
            $request->setView('HomeTab');
            $this->ViewFilename = $request->getURL();
            modApiFunc("Users", "updateAcountInfo", $acountInfo['id'], $SessionPost['AdminEmail'], $SessionPost['New_Password']);
            modApiFunc("Users", "unsetPasswordUpdate");
            modApiFunc("Users", "saveState");
        }
        else
        {
            $request->setView  ( 'AdminPasswordUpdate' );
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