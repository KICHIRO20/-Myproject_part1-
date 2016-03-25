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
 * Users module.
 *
 * @package Users
 * @author Alexandr Girin
 * @access  public
 */
class AddAdmin extends PasswordUpdate
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     *
     * @ finish the functions on this page
     */
    function AddAdmin()
    {
    }

    function saveDataToDB($data)
    {
        if(empty($data["Options"])) $data["Options"] = array();
        $uid = modApiFunc("Users", "addAdmin",
                   $data["FirstName"],
                   $data["LastName"],
                   $data["Email"],
                   $data["Password"],
                   $data["Options"],
                   $data['need_update']
                  );
        modApiFunc('Users', 'setAdminPermissions', $uid, $data['Permissions']);
    }

    /**
     *
     * @ finish the functions on this page
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

        $SessionPost['id'] = modApiFunc("Users", "getSelectedUserID");
        if (!$SessionPost['id'])
        {
            $SessionPost['id'] = 0;
        }

        if (!modApiFunc("Users", "isValidEmail", $SessionPost['Email']))
        {
            $nErrors++;
            $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_000";
        }
        if (modApiFunc("Users", "isEmailExists", $SessionPost['Email'], $SessionPost['id']))
        {
            $nErrors++;
            $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_010";
        }
        if ($SessionPost['FormAction']=="Add")
        {
            if(!$this->isEqNewAndVerifyPasswords($SessionPost['Password'], $SessionPost['VerifyPassword']))
            {
                $nErrors++;
                $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_002";
            }
            if (!$this->isAllFieldsFilled(array(
                                                $SessionPost['FirstName'],
                                                $SessionPost['LastName'],
                                                $SessionPost['Email'],
                                                $SessionPost['Password'],
                                                $SessionPost['VerifyPassword']
                                               )
                                         )
               )
            {
                $nErrors++;
                $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_009";
            }
            if (!$this->isValidNewPasswordLength($SessionPost['Password']))
            {
                $nErrors++;
                $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_004";
            }
            if (!$this->isComplicatedNewPassword($SessionPost['Password']))
            {
                $nErrors++;
                $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_005";
            }
            if (!$this->isDifferentFromEmail($SessionPost['Email'], $SessionPost['Password']))
            {
                $nErrors++;
                $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_007";
            }
        }
        else
        {
            if (!$this->isAllFieldsFilled(array(
                                                $SessionPost['FirstName'],
                                                $SessionPost['LastName'],
                                                $SessionPost['Email']
                                               )
                                         )
               )
            {
                $nErrors++;
                $SessionPost["ViewState"]["ErrorsArray"][] = "PSWUPD_011";
            }
        }

        if($nErrors == 0)
        {
            $SessionPost['need_update'] = false;
            if ($SessionPost["SendByEmail"])
            {
                modApiFunc("Users", "letterAboutNewPassword", $SessionPost['Email'], $SessionPost['Password_Open']);
                $SessionPost['need_update'] = true;
            }
            unset($SessionPost["ViewState"]["ErrorsArray"]);
//            $SessionPost['FirstName'] = prepareHTMLDisplay($SessionPost['FirstName']);
//            $SessionPost['LastName'] = prepareHTMLDisplay($SessionPost['LastName']);
            $this->saveDataToDB($SessionPost);

            $request = new Request();
            $request->setView('AdminMembersList');
            error_log($request->getURL());
            $application->redirect($request);
            return;

        }
        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
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