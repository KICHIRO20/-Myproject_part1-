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
 * Admin Module, Add Admin Member View.
 *
 * @package Users
 * @author Alexey Florinsky
 */
class AdminMemberAdd
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
    function AdminMemberAdd()
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

        $this->POST  =
            array(
                  "FirstName" => $SessionPost["FirstName"]
                 ,"LastName"  => $SessionPost["LastName"]
                 ,"Email"     => $SessionPost["Email"]
                 ,"Password"  => ""
                 ,"VerifyPassword" => ""
                 ,"SendByEmail" => $SessionPost["SendByEmail"]
                 //,"Options"    => $SessionPost["Options"]
                 ,"Permissions" => isset($SessionPost["Permissions"]) ? $SessionPost["Permissions"] : array()
            );
    }

    function initFormData()
    {
        $this->ViewState =
            array(
                "hasCloseScript" => "false"
                 );
        $this->POST  =
            array(
                  "FirstName" => ""
                 ,"LastName"  => ""
                 ,"Email"     => ""
                 ,"Password"  => ""
                 ,"VerifyPassword" => ""
                 ,"SendByEmail" => false
                 ,"Permissions" => isset($SessionPost["Permissions"]) ? $SessionPost["Permissions"] : array()
            );
    }

    /**
     * @return String Return html code for hidden form fields representing
     * @var $this->ViewState array.
     */
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
     * @return String Return html code representing @var $this->ErrorsArray array.
     */
    function outputErrors()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        $retval = "";
        if(isset($this->ErrorsArray) && count($this->ErrorsArray) >0)
        {
            $retval = "<div class=\"note note-warning note-bordered font-red\">";
            foreach ($this->ErrorsArray as $key => $value)
            {
                $retval .= "<i class=\"fa fa-info-circle\"></i>&nbsp;".$obj->getMessage(new ActionMessage($value));
            }
            $retval.= "</div>";
        }
        return $retval;
    }

    function outputPageTitle()
    {
        return $this->MessageResources->getMessage(new ActionMessage("ADMIN_ADD_MEMBER_PAGE_TITLE"));
    }

    function outputPasswordFields()
    {
        global $application;
        $template_contents = array(
                                   "Password" => $this->POST["Password"]
                                  ,"VerifyPassword" => $this->POST["VerifyPassword"]
                                  ,"SendByEmail" => $this->POST["SendByEmail"]? " CHECKED":""
                                 );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "users/admin_member_add/","password_fields.tpl.html", array());
    }

    function outputPasswordRequirements()
    {
        return modApiFunc('TmplFiller', 'fill', "users/admin_member_add/","password_requirements.tpl.html", array());
    }

    /**
     *
     *
     * @
     * @param
     * @return
     */
/*
    function outputOptionsList()
    {
        global $application;
        $options = modApiFunc("Users", "getOptionsList");
        $uid = modApiFunc("Users", "getSelectedUserID");
        $current_user = modApiFunc("Users", "getCurrentUserID");

        $retval = "";
        $i = 1;
        foreach ($options as $option)
        {
            $template_contents = array(
                                       "OptionId"    => $option["id"]
                                      ,"HiddenOption"=> $this->POST["Options"][$i]? $i:""
                                      ,"Option"      => $i
                                      ,"OptionName"  => $option["name"]
                                      ,"Checked"     => $this->POST["Options"][$i]? " CHECKED":""
                                      );
            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);
            if (modApiFunc("Users", "havePermission", ADMIN_OPTION_EDIT_ADMINS, $current_user))
            {
                $retval.= modApiFunc('TmplFiller', 'fill', "users/admin_member_add/","option_item.tpl.html", array());
            }
            else
            {
                $retval.= modApiFunc('TmplFiller', 'fill', "users/admin_member_add/","option_item_disabled.tpl.html", array());
            }
            $i++;
        }
        return $retval;
    }
*/

    function outputPermissionsList()
     {
        global $application;
        if(!empty($this->POST['NoAccess']))
        {
            $application->registerAttributes(array('Local_NoAccess'));
            $this->_Template_Contents['Local_NoAccess'] = "Sorry, You have no access to this content.";
            return modApiFunc('TmplFiller', 'fill', 'users/admin_member_add/', 'permissions_no_access.tpl.html', array());
        }

        $retval = '';
        $application->registerAttributes(array(
            'Local_PermissionId', 'Local_PermissionName',
            'Local_AccessId', 'Local_AccessName', 'Local_AccessChecked',
            'Local_Accesses',
        ));

        $permissions = modApiFunc('Users', 'getPermissionsArray');
        $accesses = modApiFunc('Users', 'getAccessLevelsArray');

        foreach ($permissions as $p_id => $p) {
            $this->_Template_Contents['Local_PermissionId'] = $p_id;
            $this->_Template_Contents['Local_PermissionName'] = $p['name'];
            $acc_val = '';
            foreach ($p['accesses'] as $a) {
                $this->_Template_Contents['Local_AccessId'] = $a;
                $this->_Template_Contents['Local_AccessName'] = $accesses[$a];
                if (! isset($this->POST['Permissions'][$p_id])) {
                    $this->POST['Permissions'][$p_id] = ACCESS_MANAGE;
                }
                $this->_Template_Contents['Local_AccessChecked'] = $this->POST['Permissions'][$p_id] == $a ? 'checked="checked"' : '';
                $acc_val .= modApiFunc('TmplFiller', 'fill', 'users/admin_member_add/', 'select_access.tpl.html', array());
             }
            $this->_Template_Contents['Local_Accesses'] = $acc_val;
            $retval .= modApiFunc('TmplFiller', 'fill', 'users/admin_member_add/', 'permission_row.tpl.html', array());
         }
       return $retval;
     }



    /**
     *
     */
    function outputFormActionJS()
    {
        return "Add";
    }

    /**
     *
     */
    function outputFormAction()
    {
        $request = new Request();
        $request->setView  ('AdminMemberAdd');
        $request->setAction('AddAdmin');
        return $request->getURL();
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
        $formAction = $this->outputFormAction();

        $template_contents = array(
                                   "FORM" => $HtmlForm->genForm($formAction, "POST", "")
                                  ,"FormAction" => $this->outputFormActionJS()
                                  ,"HiddenArrayViewState"=> $this->outputViewState()
                                  ,"PageTitle" => $this->outputPageTitle()
                                  ,"FirstName" => $HtmlForm->genInputTextField("128", "FirstName", "25", prepareHTMLDisplay($this->POST["FirstName"]))
                                  ,"LastName" => $HtmlForm->genInputTextField("128", "LastName", "25", prepareHTMLDisplay($this->POST["LastName"]))
                                  ,"Email" => $HtmlForm->genInputTextField("128", "Email", "25", $this->POST["Email"])
                                  ,"PasswordFields" => $this->outputPasswordFields()
                                  ,"PasswordRequirements" => $this->outputPasswordRequirements()
                                  ,"PermissionsList"  => $this->outputPermissionsList()
                                  ,"Errors"      => $this->outputErrors()
                                  ,"PSWUPD_000" => $this->MessageResources->getMessage(new ActionMessage("PSWUPD_000"))
                                  ,"PSWUPD_009" => $this->MessageResources->getMessage(new ActionMessage("PSWUPD_009"))
                                  ,"PSWUPD_002" => $this->MessageResources->getMessage(new ActionMessage("PSWUPD_002"))
                                  ,"PSWUPD_004" => $this->MessageResources->getMessage(new ActionMessage("PSWUPD_004"))
                                  ,"PSWUPD_005" => $this->MessageResources->getMessage(new ActionMessage("PSWUPD_005"))
                                  ,"PSWUPD_007" => $this->MessageResources->getMessage(new ActionMessage("PSWUPD_007"))
                                  ,"PSWUPD_011" => $this->MessageResources->getMessage(new ActionMessage("PSWUPD_011"))
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "users/admin_member_add/","admin_member_add.tpl.html", array());
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