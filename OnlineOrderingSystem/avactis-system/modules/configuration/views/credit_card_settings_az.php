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
 * CreditCardSettings view
 *
 * @package
 * @author Vadim Lyalikov
 */
class CreditCardSettings
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Modules_Manager constructor
     */
    function CreditCardSettings()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');

        $this->mTmplFiller = &$application->getInstance('TmplFiller');

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
     *                         POST
     */
    function copyFormData()
    {
        // eliminate copying on construction
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState = $SessionPost["ViewState"];
        //Remove some data, that should not be resent to action, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }

        $this->POST =
            array(
                "credit_card_type_name" => $SessionPost["credit_card_type_name"]
            );
    }

    /**
     *                         DB
     */
    function initFormData()
    {
        $this->POST = array();
        $settings = modApiFunc("Discounts", "getSettings");
//        $this->POST["status"] = modApiFunc("Shipping_Module_Flat_Shipping_Rates", "isActive");
        $this->POST["credit_card_type_name"] = "<enter new credit card type>";
        $this->ViewState =
            array(
                "hasCloseScript"  => "false"
               ,"FormSubmitValue" => "save"
                 );
    }


    /**
     * @return String Return html code for hidden form fields representing @var $this->ViewState array.
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
     *
     */
    function outputCCTypes()
    {
        global$application;
        $cc_types = modApiFunc("Configuration", "getCreditCardSettings", false);

        $retval = "";

        $name_input_id = 1;
        foreach($cc_types as $type)
        {
            $this->_CCType = array
            (
                "id"              => $type["id"]
               ,"name"            => $type["name"]
               ,"name_input_id"   => $name_input_id
               ,"tag"             => $type["tag"]
               ,"visible"         => $type["visible"]
               ,"type"            => _ml_substr($type["tag"], _ml_strlen("common_cc_type")) == "common_cc_type" ? "common" : $type["tag"]
               ,"StatusVisible"   => ($type["visible"] == DB_TRUE) ? "SELECTED" : ""
               ,"StatusInvisible" => ($type["visible"] == DB_TRUE) ? "" : "SELECTED"
            );
            $name_input_id++;
            if(_ml_strpos($this->_CCType["tag"], "without_validation") === 0)
            {
                $this->_CCType["tag"] = getMsg('SYS','CREDIT_CARDS_TAG_COMMON_TYPE');
            }
            $application->registerAttributes($this->_CCType);
            $retval .= $this->mTmplFiller->fill("configuration/credit_card_settings/", "credit_cards_list_item.tpl.html", array());
        }

        return $retval;
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('SYS',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("configuration/credit_card_settings/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
    }

    /**
     *
     */
    function output()
    {
        global $application;

        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();
        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        $this->CurrentRateUnitValue = "currency";
        $this->CurrentRateUnit = "Price";

        $request = new Request();
        $request->setView('CreditCardSettings');
        /* $request->setAction("UpdateCreditCardSettings"); */
        $form_action = $request->getURL();

        $request = new Request();
        $request->setView('SortCreditCardTypes');
        $sort_href = $request->getURL();

        $settings = modApiFunc("Discounts", "getSettings");
        $template_contents = array(
                                    "EditCreditCardSettingsForm"    => $HtmlForm1->genForm($form_action, "POST", "EditCreditCardSettingsForm")
                                   ,"HiddenArrayViewState"  => $this->outputViewState()

                                   ,"Items"                 => $this->outputCCTypes()
                                   ,"SortCreditCardTypesHref" => $sort_href
                                   ,'ResultMessageRow' => $this->outputResultMessage()
//                                   ,"RvFromValue"           => modApiFunc("Localization", "FloatToFormatStr", $this->POST["rv_from"], $this->CurrentRateUnitValue)
                                  );

        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);

        $obj = &$application->getInstance('MessageResources');
        $output = modApiFunc('TmplFiller', 'fill',
                              './../../js/','validate.msgs.js.tpl',
                              array(
                                    "STRING512"=> $obj->getMessage( new ActionMessage(array('PRDADD_010')) )
                                   )
                            );
        return $output.$this->mTmplFiller->fill("configuration/credit_card_settings/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        global $application;
        switch($tag)
        {
            case "ErrorIndex":
                $value = $this->_error_index;
                break;
            case "Error":
                $value = $this->_error;
                break;
            case 'ResultMessage':
                $value = $this->_Template_Contents['ResultMessage'];
                break;
            default:
                $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
                if($value === NULL)
                {
                    $value = getKeyIgnoreCase($tag, $this->_CCType);
                }
                break;
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

    var $POST;

    /**
     * View state structure. Comes from action.
     * $SessionPost["ViewState"] structure example:
     * <br>array
     * <br>(
     * <br>    "hasCloseScript"  = "false"           //true/false
     * <br>    "ErrorsArray"     =  array()          //true/false
     * <br>    "LargeImage"      = "image.jpg"       //
     * <br>    "SmallImage"      = "image_small.jpg" //
     * <br>)
     */
    var $ViewState;

    /**
     * List of error ids. Comes from action.
     */
    var $ErrorsArray;
    var $ErrorMessages;

    var $_Template_Contents;
    var $_Rate;

    var $MessageResources;
    var $_error_index;
    var $_error;

    /**#@-*/
}
?>