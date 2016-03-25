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
 * Admin Module, Admin Members View.
 *
 * @package Users
 * @author Alexandr Girin
 */
class AdminMembers
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
    function AdminMembers()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');
    }

    /**
     *
     */
    function outputButtons()
    {
        global $application;

        $request = new Request();
        $request->setView  ('AdminMemberAdd');
        $AddLink = $request->getURL();

        $template_contents = array(
                                   "AddLink" => $AddLink
                                  );

        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "users/admin_members/","buttons.tpl.html", array());
    }

    /**
     *
     *
     * @
     * @param
     * @return
     */
    function outputAdminList()
    {
        global $application;
        $retval = "";

        $admin_members = modApiFunc("Users", "getAdminMembersList");


        $i = 0;
        //$min_list_size = 10;
        foreach ($admin_members as $member)
        {
            $current_admin = (modApiFunc("Users", "getCurrentUserID") == $member["id"])? true:false;
            $request = new Request();
            $request->setView  ( 'AdminMemberInfo' );
            $request->setAction( 'SetSelectedUser' );
            $request->setKey   ( 'uid', $member["id"]);
            $request->setKey   ( 'edit', true);
            $link = $request->getURL();
            $template_contents = array(
                                       "AdminMemberInfoLink" => $link
                                      ,"AdminId" => $member["id"]
                                      ,"Style" => "visible"
                                      ,"AdminCheckBox" => ($current_admin)? "":"select_".$i
                                      ,"AdminCheckBoxName" => ($current_admin)? "":"selected_admins[".($i+1)."]"
                                      ,"AdminName" => prepareHTMLDisplay($member["firstname"]." ".$member["lastname"])
                                      ,"CurrentAdmin" => ($current_admin)? "<span class=\"font-red\">*</span>":""
                                      ,"Email" => $member["email"]
                                      ,"LogNum" => $member["lognum"]
                                      ,"LoggedIn" => ($member["logdate"]&&$member["logdate"]!="0000-00-00")? modApiFunc("Localization", "SQL_date_format", $member["logdate"]):$this->MessageResources->getMessage("ADMIN_MEMBERS_LABEL_001")
                                      ,"Created"  => modApiFunc("Localization", "SQL_date_format", $member["created"])
                                      ,"Modified" => ($member["modified"]&&$member["modified"]!="0000-00-00")? modApiFunc("Localization", "SQL_date_format", $member["modified"]):$this->MessageResources->getMessage("ADMIN_MEMBERS_LABEL_002")
                                      ,"Enabled" => ($current_admin)? " DISABLED":""
                                      );
            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);

            $retval.= modApiFunc('TmplFiller', 'fill', "users/admin_members/","item.tpl.html", array());
            if (!$current_admin)
            {
                $i++;
            }
        }

        for(;$i < $min_list_size; $i++)
        {
            $retval.= modApiFunc('TmplFiller', 'fill', "users/admin_members/","item_empty.tpl.html", array());
        }

        return $retval;
    }

    /**
     *
     */
    function output()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm = new HtmlForm();
        $request = new Request();
        $request->setView  ('AdminMemberDelete');
        $request->setAction('SetDeleteAdminMembers');
        $formAction = $request->getURL();

        $template_contents = array(
                                   "FORM"  => $HtmlForm->genForm($formAction, "POST", "AdminMembersList")
                                  ,"Items" => $this->outputAdminList()
                                  ,"Buttons" => $this->outputButtons()
                                  ,"Style" => "visible"
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "users/admin_members/","container.tpl.html", array());
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