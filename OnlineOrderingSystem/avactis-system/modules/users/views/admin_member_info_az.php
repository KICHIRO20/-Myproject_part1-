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
 * Admin Module, Admin Member Info View.
 *
 * @package Users
 * @author Alexey Florinsky
 */
class AdminMemberInfo
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Modules_Manager constructor.
     */
    function AdminMemberInfo()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');
        $this->uid = modApiFunc("Users", "getSelectedUserID");
        $this->admin_info = modApiFunc("Users", "getUserInfo", $this->uid);
    }

    /**
     *
     */
    function outputButton($position)
    {
        global $application;

        $request = new Request();
        $request->setView  ('AdminMemberEdit');
        $request->setAction('SetSelectedUser');
        $request->setKey   ('uid', $this->admin_info["id"]);
        $EditLink = $request->getURL();

        $template_contents = array();
        $current_user = modApiFunc("Users", "getCurrentUserID");
        $template_contents = array("EditLink"  => $EditLink);
        switch ($position) {
                case "Top":
                    $template = "button_edit_top.tpl.html";
                    break;
                case "Bottom":
                    $template = "button_edit_bottom.tpl.html";
                    break;
            }
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $retval = modApiFunc('TmplFiller', 'fill', "users/admin_member_info/", $template, array());

        return $retval;
    }

    function outputPermissions()
    {
        global $application;
        $retval = '';

        $application->registerAttributes(array('Local_PermissionName', 'Local_AccesseName'));

        $admin_permissions = modApiFunc("Users", "getAdminPermissions", $this->uid);
        $permissions = modApiFunc('Users', 'getPermissionsArray');
        $accesses = modApiFunc('Users', 'getAccessLevelsArray');

        foreach ($permissions as $p_id => $p) {
            $this->_Template_Contents['Local_PermissionName'] = $p['name'];
            $this->_Template_Contents['Local_AccesseName'] = $accesses[(int)@$admin_permissions[$p_id]];
            $retval .= modApiFunc('TmplFiller', 'fill', 'users/admin_member_info/', 'permission_row.tpl.html', array());
        }

        return $retval;
    }

    /**
     *
     */
    function output()
    {
        global $application;

        $request = new Request();
        $request->setView  ('AdminPasswordChange');
        $request->setAction('SetSelectedUser');
        $request->setKey   ('uid', $this->admin_info["id"]);
        $PasswordChangeLink = $request->getURL();

        $template_contents = array(
                                   "FirstName" => prepareHTMLDisplay($this->admin_info["firstname"])
                                  ,"LastName" => prepareHTMLDisplay($this->admin_info["lastname"])
                                  ,"FirstNameInScript" => str_replace("'", "\'", $this->admin_info["firstname"])
                                  ,"LastNameInScript" => str_replace("'", "\'", $this->admin_info["lastname"])
                                  ,"Email" => $this->admin_info["email"]
                                  ,"LogNum" => $this->admin_info["lognum"]
                                  ,"LoggedIn" => ($this->admin_info["logdate"]&&$this->admin_info["logdate"]!="0000-00-00")? modApiFunc("Localization", "SQL_date_format", $this->admin_info["logdate"]):$this->MessageResources->getMessage("ADMIN_MEMBERS_LABEL_001")
                                  ,"Created"  => modApiFunc("Localization", "SQL_date_format", $this->admin_info["created"])
                                  ,"Modified" => ($this->admin_info["modified"]&&$this->admin_info["modified"]!="0000-00-00")? modApiFunc("Localization", "SQL_date_format", $this->admin_info["modified"]):$this->MessageResources->getMessage("ADMIN_MEMBERS_LABEL_002")
                                  ,"Permissions" => $this->outputPermissions()
                                  ,"ButtonsTop"=> $this->outputButton("Top")
                                  ,"ButtonsBottom"=> $this->outputButton("Bottom")
                                  ,"PasswordChangeLink"  => $PasswordChangeLink
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "users/admin_member_info/","admin_member_info.tpl.html", array());
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        if (array_key_exists($tag, $this->_Template_Contents))
        {
            $value = $this->_Template_Contents[$tag];
        }
        return $value;
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