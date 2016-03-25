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
class UpdateCountries extends AjaxAction
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
    function UpdateCountries()
    {
    }

    function updateDataInDB($data)
    {
        for ($val=0; $val <= 237; $val++)
        {
            modApiFunc("Location", "updateCountry", $val, (isset($data["ci_".$val])? $data["ci_".$val]:NULL), (isset($data["cb_".$val])? "true":"false"), (isset($data["default_country"]) && $data["default_country"] == $val ? "true":"false"));
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
            case "update" :
            {
                $nErrors = 0;
                $SessionPost["ViewState"]["ErrorsArray"] = array();

                $live_countries_present = false;
//                foreach ($SessionPost as $key => $val)
//                {
//                    if ((_ml_substr($key, 0, 3) == "hf_"))
//                    {
//                        if (isset($SessionPost["cb_".$val]))
//                            $live_countries_present = true;
//                    }
//                }
                for ($i=0; $i <=237; $i++)
                {
                    if (isset($SessionPost["cb_".$i]))
                        $live_countries_present = true;
                }
                if (!$live_countries_present)
                {
                    $SessionPost["ViewState"]["ErrorsArray"][] = "error";//getMsg("MNG_CNTR_NO_SELECTED_ERROR");
                }

                loadCoreFile('html_form.php');
                $HtmlForm1 = new HtmlForm();

                $error_message_text = "";

                $nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);

                if($nErrors == 0)
                {
                    unset($SessionPost["ViewState"]["ErrorsArray"]);
                    $this->updateDataInDB($SessionPost);
                    $SessionPost["ViewState"]["hasCloseScript"] = "true";
                    modApiFunc('Session','set','ResultMessage','MNG_CNTR_RESULT_MESSAGE');
                }
                else{
                	modApiFunc('Session','set','ResultMessage','MNG_CNTR_RESULT_ERROR_MESSAGE');
                }
                break;
            }
            default :

                break;
        }

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        // get view name by action name.
        $request = new Request();
        $request->setView("CountriesList");
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