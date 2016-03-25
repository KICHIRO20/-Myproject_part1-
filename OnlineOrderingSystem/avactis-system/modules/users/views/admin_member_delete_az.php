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
 * Admin Module, Delete Admin Member View.
 *
 * @package Users
 * @author Alexey Florinsky
 */
class AdminMemberDelete
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
    function AdminMemberDelete()
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
            $this->initFormData();
        }
    }

    function copyFormData()
    {
        // eliminate copying on construction
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState =
            $SessionPost["ViewState"];

        //Remove some data, that should not be sent to action one more time, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }
        $this->POST  = $SessionPost;
    }

    function initFormData()
    {
        $this->ViewState =
            array(
                "hasCloseScript" => "false"
                 );
        $this->POST  = array();
    }

    function outputViewState()
    {
        $retval = "";
        foreach ($this->ViewState as $key => $value)
        {
            $retval .= "<input type=\"hidden\" name=\"ViewState[" .$key . "]\" value=\"" . $value . "\">";
        }
        return $retval;
    }

    /**
     *
     */
    function outputAdminList()
    {
        global $application;
        $retval = "";
        $data = modApiFunc("Users", "getDeleteAdminMembersID");
        $removable_admins = $data['removable_admins'];
        $unremovable_admins = $data['unremovable_admins'];
        foreach ($removable_admins as $id)
        {
            $admin_info = modApiFunc("Users", "getUserInfo", $id);

            $request = new Request();
            $request->setView  ('AdminMemberInfo');
            $request->setAction('SetSelectedUser');
            $request->setKey   ( 'edit', false);
            $request->setKey('uid', $id);
            $UserLink = $request->getURL();

            $template_contents = array(
                                       "UserLink" => $UserLink
                                      ,"Name" => prepareHTMLDisplay($admin_info["firstname"]." ".$admin_info["lastname"])
                                      );
            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $retval.= modApiFunc('TmplFiller', 'fill', "users/admin_member_delete/","item.tpl.html", array());
        }
        foreach ($unremovable_admins as $info)
        {
        	$id = $info['id'];
            $admin_info = modApiFunc("Users", "getUserInfo", $id);

            $request = new Request();
            $request->setView  ('AdminMemberInfo');
            $request->setAction('SetSelectedUser');
            $request->setKey   ( 'edit', false);
            $request->setKey('uid', $id);
            $UserLink = $request->getURL();

            $template_contents = array(
                                       "UserLink" => $UserLink
                                      ,"Name" => prepareHTMLDisplay($admin_info["firstname"]." ".$admin_info["lastname"])
                                      ,"Reason" => nl2br(htmlspecialchars($info['msg']))
                                      );
            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $retval.= modApiFunc('TmplFiller', 'fill', "users/admin_member_delete/","item_unremovable.tpl.html", array());
        }
        return $retval;
    }

    /**
     *
     */
    function output()
    {
        global $application;
        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }
        loadCoreFile('html_form.php');
        $HtmlForm = new HtmlForm();
        $request = new Request();
        $request->setView  ('AdminMemberDelete');
        $request->setAction('ConfirmDeleteAdmins');
        $formAction = $request->getURL();

        $template_contents = array(
                                   "FORM" => $HtmlForm->genForm($formAction, "POST", "DeleteAdminsForm")
                                  ,"HiddenArrayViewState"=> $this->outputViewState()
                                  ,"Items" => $this->outputAdminList()
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);

        //                                         -               "Delete"
        $data = modApiFunc("Users", "getDeleteAdminMembersID");
        $removable_admins = $data['removable_admins'];
        if(sizeof($removable_admins) > 0)
        {
            return modApiFunc('TmplFiller', 'fill', "users/admin_member_delete/","admin_member_delete.tpl.html", array());
        }
        else
        {
            return modApiFunc('TmplFiller', 'fill', "users/admin_member_delete/","admin_member_delete.all_unremovable.tpl.html", array());
            modApiFunc("Users", "unsetDeleteAdminMembersID");
        }
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