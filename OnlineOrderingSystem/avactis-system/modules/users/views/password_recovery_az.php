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
 * Password Recovery view in admin zone.
 *
 * @package Users
 * @author Alexander Girin
 */
class AdminPasswordRecovery
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
    function AdminPasswordRecovery()
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
            );
    }

    /**
     * Initializes form contents on the first view output.
     */
    function initFormData()
    {
        $this->ViewState =
            array(
                 );
        $this->POST  =
            array(
                "AdminEmail"      => ''
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
            foreach ($this->ErrorsArray as $key => $value)
            {
                $retval .= $obj->getMessage(new ActionMessage($value));
            }
        }
        return $retval;
    }

    /**
     * Outputs form contents.
     */
    function output()
    {
        global $application;
        $retval = '';

        if ($this->outputErrors()==""&&$this->POST["AdminEmail"]!="")
        {
            $request = new Request();
            $request->setView   ( "" );
            $this->_Template_Content = array(
                                             "Email"        => $this->POST["AdminEmail"]
                                            ,"ContinueLink" => $request->getURL()
                                            );
            $application->registerAttributes($this->_Template_Content);
            $retval = modApiFunc('TmplFiller', 'fill', "users/","password_recovery_success.tpl.html", array());
        }
        else
        {
            loadCoreFile('html_form.php');
            $HtmlForm1 = new HtmlForm();

            $request = new Request();
            $request->setView   ( "AdminPasswordRecovery" );
            $request->setAction( "PasswordRecovery" );
            $form_action = $request->getURL();
            $this->_Template_Content = array(
                                             "HiddenArrayViewState"   => $this->outputViewState()
                                            ,"FORM"                   => $HtmlForm1->genForm($form_action,
                                                                                               "POST",
                                                                                               "PasswordRecoveryForm")
                                            ,"Email"=> $HtmlForm1->genInputTextField("255",
                                                                                       "AdminEmail",
                                                                                       "40",
                                                                                       $this->POST["AdminEmail"])
//                                            ,"SubmitScript"=> $HtmlForm1->genSubmitScript("PasswordRecoveryForm")
                                            ,"Errors"                 => $this->outputErrors()
                                            );
            $application->registerAttributes($this->_Template_Content);
            $retval = modApiFunc('TmplFiller', 'fill', "users/","password_recovery.tpl.html", array());
        }
        return $retval;
    }

    /**
     * @ describe the finction AddCategory->getTag.
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