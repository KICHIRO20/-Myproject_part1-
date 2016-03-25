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
 * Location module.
  *
 * @package Location
 * @access  public
 * @author  Alexander Girin
 */
class UpdateStates extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     */
    function UpdateStates()
    {
    }

    function updateDataInDB($data)
    {
        foreach ($data as $key => $val)
        {
            if ((_ml_substr($key, 0, 3) == "hf_"))
            {
                modApiFunc("Location", "updateState", $val, (isset($data["ci_".$val])? $data["ci_".$val]:NULL), (isset($data["cb_".$val])? "true":"false"), (isset($data["default_state"]) && $data["default_state"] == $val ? "true":"false"));
            }
        }
    }

    /**
     * Action: UpdateCountries.
     *
     */
    function onAction()
    {
        global $application;

        $SessionPost = array();
        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        $SessionPost = $_POST;
        switch($SessionPost["ViewState"]["FormSubmitValue"])
        {
            case "changeCountry":
                break;
            case "update":
            case "UpdateAndChangeCountry":
            {
                $nErrors = 0;
                $SessionPost["ViewState"]["ErrorsArray"] = array();

                loadCoreFile('html_form.php');
                $HtmlForm1 = new HtmlForm();

                $error_message_text = "";

                $nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);

                if($nErrors == 0)
                {
                    unset($SessionPost["ViewState"]["ErrorsArray"]);
                    $this->updateDataInDB($SessionPost);
                    if ($SessionPost["ViewState"]["FormSubmitValue"] == "update")
                    {
                        $SessionPost["ViewState"]["hasCloseScript"] = "true";
                        modApiFunc('Session','set','ResultMessage','MNG_STATE_RESULT_MESSAGE');
                    }
                }
                else{
                 modApiFunc('Session','set','ResultMessage','MNG_STATE_RESULT_ERROR_MESSAGE');
                }
                break;
            }
            default :
                _fatal(array( "CODE" => "CORE_051"), __CLASS__, __FUNCTION__, $request->getValueByKey('FormSubmitValue'));
                break;
        }

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        // get view name by action name.
        $request = new Request();
        $request->setView("StatesList");
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