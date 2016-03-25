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
 * Password Update view in admin zone.
 *
 * @package Users
 * @author Alexander Girin
 */
class AdminPasswordUpdate
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * View constructor.
     */
    function AdminPasswordUpdate()
    {
        global $application;

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
               ,"Password"        => (isset($SessionPost["Password"])) ? $SessionPost["Password"] : ''
            );
    }

    /**
     * Initializes form contents on the first view output.
     */
    function initFormData()
    {
        $acountInfo = modApiFunc("Users", "getAcountInfoById", modApiFunc("Users", "getCurrentUserID"));
        $this->ViewState =
            array(
                 );
        $this->POST  =
            array(
                "AdminEmail"      => $acountInfo[0]['email']!='admin@localhost'? $acountInfo[0]['email']:''
               ,"Password"        => $acountInfo[0]['password']
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
        $obj = &$application->getInstance('MessageResources');
        $retval = '';

        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();

        $request = new Request();
        $request->setView   ( "AdminPasswordUpdate" );

        $request->setAction( "PasswordUpdate" );
        $form_action = $request->getURL();
        $this->_Template_Content = array(
                                         "HiddenArrayViewState"   => $this->outputViewState()
                                        ,"FORM"                   => $HtmlForm1->genForm($form_action,
                                                                                           "POST",
                                                                                           "")
                                        ,"Email"=> $HtmlForm1->genInputTextField("255",
                                                                                   "AdminEmail",
                                                                                   "40",
                                                                                   $this->POST["AdminEmail"])
                                        ,"Password"=> $HtmlForm1->genHiddenField("Password",
                                                                                   $this->POST["Password"])
                                        ,"Old_Password"=> $HtmlForm1->genInputTextField("255",
                                                                                      "Old_Password",
                                                                                      "40",
                                                                                      "")
                                        ,"New_Password"=> $HtmlForm1->genInputTextField("255",
                                                                                      "New_Password",
                                                                                      "40",
                                                                                      "")
                                        ,"Verify_New_Password"=> $HtmlForm1->genInputTextField("255",
                                                                                      "Verify_New_Password",
                                                                                      "40",
                                                                                      "")
//                                                        ,"SubmitScript"=> $HtmlForm1->genSubmitScript("PasswordUpdateForm")
                                        ,"Errors"                 => $this->outputErrors()
                                        ,"PSWUPD_000" => $obj->getMessage(new ActionMessage("PSWUPD_000"))
                                        ,"PSWUPD_001" => $obj->getMessage(new ActionMessage("PSWUPD_001"))
                                        ,"PSWUPD_002" => $obj->getMessage(new ActionMessage("PSWUPD_002"))
                                        ,"PSWUPD_003" => $obj->getMessage(new ActionMessage("PSWUPD_003"))
                                        ,"PSWUPD_004" => $obj->getMessage(new ActionMessage("PSWUPD_004"))
                                        ,"PSWUPD_005" => $obj->getMessage(new ActionMessage("PSWUPD_005"))
                                        ,"PSWUPD_006" => $obj->getMessage(new ActionMessage("PSWUPD_006"))
                                        ,"PSWUPD_007" => $obj->getMessage(new ActionMessage("PSWUPD_007"))
                                        );
        $application->registerAttributes($this->_Template_Content);
        $retval = modApiFunc('TmplFiller', 'fill', "users/","password_update.tpl.html", array());
        return $retval;
    }

    /**
     * @                      AddCategory->getTag.
     */
    function getTag($tag)
    {
        $value = null;
        if (array_key_exists($tag, $this->_Template_Content))
        {
            $value = $this->_Template_Content[$tag];
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

    /**
     * The array of form state variables.
     */
    var $ViewState;

    /**
     * The POST variables array.
     */
    var $POST;

    /**#@-*/

}
?>