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
 * Admin Module, Password was been Reseted View.
 *
 * @package Users
 * @author Alexey Florinsky, Alexander Girin
 */
class AdminMemberPasswordReset
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
    function AdminMemberPasswordReset()
    {
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

    /**
     * Restores form contents from session.
     */
    function copyFormData()
    {
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState = $SessionPost["ViewState"];

        //Remove some data, that should not be sent to action one more time, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }

        $this->POST  =
            array(
                "AdminEmail"      => $SessionPost["AdminEmail"]
               ,"SendByEmail"     => $SessionPost["SendByEmail"]
            );
    }

    /**
     * Initializes form contents on the first view output.
     */
    function initFormData()
    {
        $uid = modApiFunc("Users", "getSelectedUserID");
        $acountInfo = modApiFunc("Users", "getAcountInfoById", $uid);
        $this->ViewState =
            array(
                "hasCloseScript" => "false"
                 );
        $this->POST  =
            array(
                "AdminEmail"      => $acountInfo[0]['email']
               ,"SendByEmail"     => false
            );
    }

    /**
     * Outputs hidden fields form state.
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
     * Outputs errors.
     */
    function outputErrors()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        $retval = "";
        if(isset($this->ErrorsArray) && count($this->ErrorsArray) >0)
        {
            $retval = "<table>";
            foreach ($this->ErrorsArray as $key => $value)
            {
                $retval .= "<tr><td><span class='required'>-&nbsp;".$obj->getMessage(new ActionMessage($value))."</span>";
            }
            $retval.= "</table>";
        }
        return $retval;
    }

    /**
     * Outputs form contents.
     */
    function output()
    {
        global $application;
        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild");
            return;
        }
        $obj = &$application->getInstance('MessageResources');
        $uid = modApiFunc("Users", "getSelectedUserID");
        $admin_info = modApiFunc("Users", "getUserInfo", $uid);
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();

        $request = new Request();
        $request->setView   ("AdminPasswordChange");
        $request->setAction("PasswordChange");
        $form_action = $request->getURL();
        $template_contents = array(
                                   "HiddenArrayViewState"   => $this->outputViewState()
                                  ,"FORM"                   => $HtmlForm1->genForm($form_action,
                                                                                   "POST",
                                                                                   "")
                                  ,"Email"=> $HtmlForm1->genHiddenField("AdminEmail", $this->POST["AdminEmail"])
                                  ,"FirstName"  => prepareHTMLDisplay($admin_info["firstname"])
                                  ,"LastName"   => prepareHTMLDisplay($admin_info["lastname"])
                                  ,"SendByEmail"=> $this->POST["SendByEmail"]? " CHECKED":""
                                  ,"Errors"                 => $this->outputErrors()
                                  ,"PSWUPD_002" => $obj->getMessage(new ActionMessage("PSWUPD_002"))
                                  ,"PSWUPD_004" => $obj->getMessage(new ActionMessage("PSWUPD_004"))
                                  ,"PSWUPD_005" => $obj->getMessage(new ActionMessage("PSWUPD_005"))
                                  ,"PSWUPD_007" => $obj->getMessage(new ActionMessage("PSWUPD_007"))
                                  ,"PSWUPD_008" => $obj->getMessage(new ActionMessage("PSWUPD_008"))
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "users/admin_member_info/","admin_member_passwd_reset.tpl.html", array());
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