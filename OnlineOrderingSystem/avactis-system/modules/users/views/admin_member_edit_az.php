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
_use(dirname(__FILE__).'/admin_member_add_az.php');

/**
 * Admin Module, Edit Admin Member View.
 *
 * @package Users
 * @author Alexey Florinsky, Alexander Girin
 */
class AdminMemberEdit extends AdminMemberAdd
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * A constructor.
     *
     * @return
     */
    function AdminMemberEdit()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->initDBFormData();
        }
    }

    /**
     * Initializes admin editing data from database.
     *
     * @return
     */
    function initDBFormData()
    {
        $uid = modApiFunc("Users", "getSelectedUserID");
        $admin_info = modApiFunc("Users", "getUserInfo", $uid);

        $this->ViewState =
            array(
                "hasCloseScript" => "false"
                 );

        $options = array();
        $this->POST  =
            array(
                  "FirstName" => $admin_info["firstname"]
                 ,"LastName"  => $admin_info["lastname"]
                 ,"Email"     => $admin_info["email"]
                 ,"Password"  => ""
                 ,"VerifyPassword" => ""
                 ,"SendByEmail" => false
                 ,"Options"    => $options
                 ,"Permissions" => modApiFunc("Users", "getAdminPermissions", $uid)
            );
        if(modApiFunc("Users", "getCurrentUserID") == $uid)
            $this->POST["NoAccess"] = true;
    }

    function outputPageTitle()
    {
        return $this->MessageResources->getMessage(new ActionMessage("ADMIN_EDIT_MEMBER_PAGE_TITLE"));
    }

    function outputPasswordFields()
    {
        return "";
    }

    function outputPasswordRequirements()
    {
        return "";
    }

    /**
     *
     */
    function outputFormActionJS()
    {
        return "Edit";
    }

    /**
     *
     */
    function outputFormAction()
    {
        $request = new Request();
        $request->setView  ('AdminMemberEdit');
        $request->setAction('EditAdmin');
        return $request->getURL();
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