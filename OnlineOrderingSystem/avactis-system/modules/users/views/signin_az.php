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
 * SignIn view in admin zone.
 *
 * @package Users
 * @author Alexander Girin
 */
class AdminSignIn
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
    function AdminSignIn()
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
        if (modApiFunc("Session", "is_Set", "AdminZoneIsBlocked"))
        {
            $this->initFormData();
            $this->ErrorsArray = array();
            modApiFunc("Session", "un_Set", "AdminZoneIsBlocked");
        }
    }

    /**
     * Restores form contents from session.
     */
    function copyFormData()
    {
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState = $SessionPost["ViewState"];

        //Remove some data, that should not be sent to actionone more time, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }

        $this->POST  =
            array(
                "AdminEmail"      => $SessionPost["AdminEmail"]
               ,"RememberEmail"   => isset($SessionPost["RememberEmail"])? $SessionPost["RememberEmail"]:''
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
                "AdminEmail"      => isset($_COOKIE['ac_remember_email'])? $_COOKIE['ac_remember_email']:''
               ,"RememberEmail"   => isset($_COOKIE['ac_remember_email'])? 'on':''
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
                $retval .= '<div class="alert alert-danger"> ' . $obj->getMessage(new ActionMessage($value)) . '</div>';
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

        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();

        $request = new Request();
        $request->setView   ( "AdminSignIn" );
        $request->setAction( "SignIn" );
        $form_action = $request->getURL();

        $request = new Request();
        $request->setView   ( "AdminPasswordRecovery" );
        $PasswordForgottenLink = $request->getURL();

        $this->_Template_Content = array(
                                         "HiddenArrayViewState"   => $this->outputViewState()
                                        ,"FORM"                   => $HtmlForm1->genForm($form_action,
                                                                                           "POST",
                                                                                           "")
                                        ,"Email"=> $HtmlForm1->genInputTextField("255",
                                                                                   "AdminEmail",
                                                                                   "40",

                                                                                   $this->POST["AdminEmail"])


                                        ,"Password"=> $HtmlForm1->genInputTextField("255",
                                                                                      "Password",
                                                                                      "40",

                                                                                      "")


                                        ,"RememberChecked"=>($this->POST["RememberEmail"]!='')? 'CHECKED':''
                                        ,"PasswordForgottenLink" => $PasswordForgottenLink
                                        ,"Errors"                 => $this->outputErrors()
                                        ,"Version"                => PRODUCT_VERSION_NUMBER . " " . PRODUCT_VERSION_TYPE . ', build '.PRODUCT_VERSION_BUILD
                                        );
        $application->registerAttributes($this->_Template_Content);
        $retval = modApiFunc('TmplFiller', 'fill', "users/","signin.tpl.html", array());
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