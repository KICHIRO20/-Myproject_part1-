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
 *
 * @package Manufacturers
 * @access  public
 * @author  Vadim Lyalikov
 *
 */


class AddManufacturer
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     */
    function AddManufacturer()
    {
        global $application;

        $this->MessageResources = &$application->getInstance('MessageResources',"manufacturers-messages", "AdminZone");

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


    function copyFormData()
    {
        // eliminate copying on construction
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState =
            $SessionPost["ViewState"];

        //Remove some data, that should not be resent to action, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }

        $this->POST  =
            array(
                "ManufacturerNameText" => ($SessionPost["ManufacturerName"]),
                "ManufacturerUrlText" => ($SessionPost["ManufacturerUrl"]),
                "ManufacturerDescText" => ($SessionPost["ManufacturerDesc"]),
                "ManufacturerStatusValue" => ($SessionPost["ManufacturerStatus"]),
                "ManufacturerImage" => ($SessionPost["ManufacturerImage"])
				);
    }

    function initFormData()
    {
        $this->ViewState =
            array(
                "hasCloseScript" => "false",
                 );
        $this->POST  =
            array(
                "ManufacturerNameText" => "",
                "ManufacturerUrlText" => NULL,
                "ManufacturerDescText" => "",
                "ManufacturerStatusValue" => DB_FALSE, //NO, disabled
                "ManufacturerImage" => new image_obj()
            );
    }

    function outputManufacturerSummary()
    {
    	return "";
    }
    /**
     *
     */
    function getLinkToManufacturersList()
    {
        $_request = new Request();
        $_request->setView  ( 'ManufacturersList' );
        return $_request->getURL();
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

    function outputSubtitle()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm;
        $this->_Template_Contents["SubmitSaveScript"] = $HtmlForm1->genSubmitScript("AddManufacturerForm");
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("manufacturers/add_manufacturer/", "subtitle.tpl.html", array());
    }

    function outputManufacturerId()
    {
        return "";
    }

    function outputViewStateConstants()
    {
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();
        $retval = "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("asc_action", "add_manufacturer") . ">";
        return $retval;
    }

    /**
     * @return String Return html code representing @var $this->ErrorsArray array.
     */
    function outputErrors()
    {
        global $application;
    	if (!is_array($this->ErrorsArray) || sizeof($this->ErrorsArray) == 0)
    	{
    		return;
    	}
    	$result = "";
    	$application->registerAttributes(array('ErrorIndex', 'Error'));
    	$this->_error_index = 0;
    	foreach ($this->ErrorsArray as $error)
    	{
    	    $this->_error_index++;
    		$this->_error = $this->MessageResources->getMessage($error);
    		$result .= $this->mTmplFiller->fill("manufacturers/add_manufacturer/", "error.tpl.html", array());
    	}
    	return $result;
    }

    function outputStatus()
    {
    	$selected_index = $this->POST["ManufacturerStatusValue"];
    	$options = array
    	(
    	    array("value" => DB_TRUE, "contents" => getMsg('MNF', "MNF_STATUS_ACTIVE")),
    	    array("value" => DB_FALSE, "contents" => getMsg('MNF', "MNF_STATUS_INACTIVE"))
    	);

        $HtmlForm1 = new HtmlForm();
        $value = $HtmlForm1->genDropdownSingleChoice(array
            (
                "onChange"=> "",
                "select_name"=> "ManufacturerStatus",
                "values" => $options,
                "selected_value" => $selected_index
            )
        );

        return $value;
    }

    /**
     * Return the "Manufacturers -> Add Manufacturer" view html code.
     *
     */
    function output()
    {
        global $application;
        loadCoreFile('html_form.php');
        $this->Hints = &$application->getInstance('Hint');
        $HtmlForm1 = new HtmlForm();

        $this->MessageResources = &$application->getInstance('MessageResources',"manufacturers-messages", "AdminZone");
        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        $manufacturer_summary = $this->outputManufacturerSummary();
        $this->_Template_Contents = array
        (
            "ManufacturerSummary" => $manufacturer_summary
        );
        $application->registerAttributes($this->_Template_Contents);
        $template_contents= array(
                           "Subtitle"            => $this->outputSubtitle(),
                           "Errors"              => $this->outputErrors(),
                           "ManufacturerId"         => $this->outputManufacturerId(),
                           "ManufacturerSummary"    => $manufacturer_summary,

                           "ManufacturerNameError"  => isset($this->ErrorMessages['ERR_AZ_MNF_ADD_MNF_001']) ? $this->ErrorMessages['ERR_AZ_MNF_ADD_MNF_001'] : "",
                           "ManufacturerNameInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_MNF_ADD_MNF_001']) ? "error" : "",
                           "ManufacturerName"       => $HtmlForm1->genInputTextField("128", "ManufacturerName", "75", prepareHTMLDisplay($this->POST["ManufacturerNameText"])),
						   "ManufacturerIdFieldHint" => $this->Hints->getHintLink(array('MANUFACTURER_ID_NAME', 'manufacturers-messages')),
                           "ManufacturerNameFieldHint" => $this->Hints->getHintLink(array('MNF_NAME_NAME', 'manufacturers-messages')),

                           "ManufacturerImage"      => getimage_input_az('mnf_image', $this->POST["ManufacturerImage"]),
                           "ManufacturerImageFieldHint" => $this->Hints->getHintLink(array('MNF_IMAGE_NAME', 'manufacturers-messages')),

                           "ManufacturerUrlError"  => isset($this->ErrorMessages['ERR_AZ_MNF_ADD_MNF_004']) ? $this->ErrorMessages['ERR_AZ_MNF_ADD_MNF_004'] : "",
                           "ManufacturerUrlInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_MNF_ADD_MNF_004']) ? "error" : "",
                           "ManufacturerUrl"         => $HtmlForm1->genInputTextField("128", "ManufacturerUrl", "75", prepareHTMLDisplay($this->POST["ManufacturerUrlText"] === NULL ? '' : $this->POST["ManufacturerUrlText"])),
                           "ManufacturerUrlFieldHint" => $this->Hints->getHintLink(array('MNF_URL_NAME', 'manufacturers-messages')),

                           "ManufacturerDescError"  => isset($this->ErrorMessages['ERR_AZ_MNF_ADD_MNF_002']) ? $this->ErrorMessages['ERR_AZ_MNF_ADD_MNF_002'] : "",
                           "ManufacturerDescInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_MNF_ADD_MNF_002']) ? "error" : "",
                           "ManufacturerDesc"       => $HtmlForm1->genInputTextAreaField("77", "ManufacturerDesc", "10"),
                           "ManufacturerDescText"   => prepareHTMLDisplay($this->POST["ManufacturerDescText"]),
                           "ManufacturerDescFieldHint" => $this->Hints->getHintLink(array('MNF_DESC_NAME', 'manufacturers-messages')),

                           "ManufacturerStatusError"  => isset($this->ErrorMessages['ERR_AZ_MNF_ADD_MNF_003']) ? $this->ErrorMessages['ERR_AZ_MNF_ADD_MNF_003'] : "",
                           "ManufacturerStatusInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_MNF_ADD_MNF_003']) ? "error" : "",
                           "ManufacturerStatus"       => $this->outputStatus(),
                           "ManufacturerStatusFieldHint" => $this->Hints->getHintLink(array('MNF_STATUS_NAME', 'manufacturers-messages')),

                           "AddManufacturerForm"     => $HtmlForm1->genForm(modApiFunc("application", "getPagenameByViewname","ManufacturersList",-1,-1,'AdminZone'), "POST", "AddManufacturer"),
                           "HiddenFormSubmitValue"=> $HtmlForm1->genHiddenField("FormSubmitValue", "Save"),
                           "HiddenArrayViewStateConstants"=> $this->outputViewStateConstants(),
                           "HiddenArrayViewState"=> $this->outputViewState(),

                           "SubmitSaveScript" => $HtmlForm1->genSubmitScript("AddManufacturerForm"));
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);

        $SpecMessageResources = &$application->getInstance('MessageResources');
        //: correct error codes
        return $output = $this->mTmplFiller->fill("manufacturers/add_manufacturer/", "list.tpl.html",array());
    }

    /**
     * @                      AddManufacturer->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
        if ($value == null)
        {
            switch ($tag)
        	{
        	    case 'ErrorIndex':
        	        $value = $this->_error_index;
        	        break;

        	    case 'Error':
        	        $value = $this->_error;
        	        break;
        	};
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
     * Pointer to the template filler object.
     * Needs to track sequences of identical templates, like lists.
     */
    var $mTmplFiller;
    /**#@-*/

    /**
     * Pointer to the received from action or prepared FORM data.
     */
    var $POST;

    var $ViewState;

    /**
     * List of error ids. Comes from action.
     */
    var $ErrorsArray;
    var $ErrorMessages;

    var $_Template_Contents;

    var $MessageResources;
    var $_error_index;
    var $_error;
}
?>