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
 * @package Manufacturers
 * @author Vadim Lyalikov
 *
 */

class add_manufacturer extends AjaxAction
{
    function add_manufacturer()
    {
    }

    function isValidName($data)
    {
        $retval = (is_string($data) &&
                _ml_strlen(trim($data)) > 0 &&
                _ml_strlen(trim($data)) < 129);
        return $retval;
    }

    function isValidStatus($data)
    {
        $retval = ($data == DB_TRUE || $data == DB_FALSE);
        return $retval;
    }

    function isValidDesc($data)
    {
        $retval = (
                _ml_strlen(trim($data)) <= 1024
                  );
        return $retval;
    }

    function saveDataToDB($data)
    {
        modApiFunc("Manufacturers", "addManufacturer",
                   $data["ManufacturerImage"]->get_id(),
                   $data["ManufacturerName"],
                   $data["ManufacturerUrl"],
                   $data["ManufacturerDesc"],
                   $data["ManufacturerStatus"]
                  );
    }

    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = array();
        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        $SessionPost = $_POST;
        $SessionPost["ManufacturerStatus"] = DB_TRUE;
		$successRedirect=false;
        switch($request->getValueByKey('FormSubmitValue'))
        {
            case "Save" :
            {
                $nErrors = 0;
                $SessionPost["ViewState"]["ErrorsArray"] = array();
                loadCoreFile('html_form.php');
                $HtmlForm1 = new HtmlForm();

                $error_message_text = "";

                if(!$this->isValidName($SessionPost["ManufacturerName"], $error_message_text))
                {
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"]["ManufacturerName"] = new ActionMessage(array("ERR_AZ_MNF_ADD_MNF_001"));
                }

                loadCoreFile("URI.class.php");
		        $uri = new URI($SessionPost["ManufacturerUrl"]);
		        if($uri === false || $SessionPost["ManufacturerUrl"] == '')
		        {
		            $SessionPost["ManufacturerUrl"] = '';
		        }
		        else
		        {
                    $SessionPost["ManufacturerUrl"] = $uri->full;
		        }

                $image_obj = modApiFunc("Images", "processImageInput", "mnf_image");
                $error = $image_obj->get_error();
                $SessionPost["ManufacturerImage"] = $image_obj;
                if($error != NULL)
                {
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"]["ManufacturerImage"] = new ActionMessage(array("ERR_AZ_MNF_ADD_MNF_005"));
                    $SessionPost["Status"] = DB_TRUE;
                }

//                if(!$this->isValidStatus($SessionPost["ManufacturerStatus"], $error_message_text))
//                {
//                    $nErrors++;
//                    $SessionPost["ViewState"]["ErrorsArray"]["ManufacturerStatus"] = new ActionMessage(array("ERR_AZ_MNF_ADD_MNF_003"));
//                    $SessionPost["Status"] = DB_FALSE;
//                }

                if(!$this->isValidDesc($SessionPost["ManufacturerDesc"], $error_message_text))
                {
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"]["ManufacturerDesc"] = new ActionMessage(array("ERR_AZ_MNF_ADD_MNF_002"));
                    //"ERR_AZ_CAT_ADDCAT_003";
                }

                $nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);

                if($nErrors == 0)
                {
                    unset($SessionPost["ViewState"]["ErrorsArray"]);
                    $this->saveDataToDB($SessionPost);
                    $SessionPost["ViewState"]["hasCloseScript"] = "true";
                    $successRedirect=true;
                }
                break;
            }
            default : _fatal(array( "CODE" => "CORE_051"), __CLASS__, __FUNCTION__, $request->getValueByKey('FormSubmitValue'));
        }
		if($successRedirect){
			$this->redirectToManufacturerList();
		} else {
			modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        	// get view name by action name.
        	$this->redirect();
		}
    }

    /**
     * Redirects after action.
     */
    function redirect()
    {
        global $application;
        $request = new Request();
        $request->setView('AddManufacturer');
        $application->redirect($request);
    }
    function redirectToManufacturerList()
    {
    	global $application;
    	$request = new Request();
    	$request->setView('ManufacturersList');
    	$application->redirect($request);
    }

};

?>