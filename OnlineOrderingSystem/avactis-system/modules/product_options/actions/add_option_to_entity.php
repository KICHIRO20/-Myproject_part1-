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

class add_option_to_entity extends AjaxAction
{
    function add_option_to_entity()
    {
    }

    function onAction()
    {
        global $application;

        $eid = $_POST["entity_id"];
        $pe = $_POST["parent_entity"];
        unset($_POST["entity_id"], $_POST["parent_entity"]);

        $data=array(
            "parent_entity" => $pe
           ,"entity_id" => $eid
           ,"option_name" => $_POST["NewOption"]["OptionName"]
           ,"display_name" => $_POST["NewOption"]["DisplayName"]
           ,"display_descr" => $_POST["NewOption"]["DisplayDescr"]
           ,"option_type" => $_POST["NewOption"]["OptionType"]
           ,"show_type" => $_POST["NewOption"]["ShowType"][$_POST["NewOption"]["OptionType"]]
        );

        if($data["option_type"]=="SS")
        {
            $data["discard_avail"]=_ml_substr($_POST["NewOption"]["DiscardAvail"],0,1);
            if($data["discard_avail"]=='Y')
                $data["discard_value"]=$_POST["NewOption"]["DiscardValue"];
        };
        if($data["option_type"]=="UF")
        {
            $data["discard_avail"]=_ml_substr($_POST["NewOption"]["DiscardAvail"],0,1);
        };

        if($data["option_type"]=="CI" and in_array($data["show_type"],array('CBSI','CBTA')))
        {
            $data["checkbox_text"]=$_POST["NewOption"]["CheckBoxText"];
        };

        if(!in_array($data["option_type"],array("CI","UF")))
        {
            $data["use_for_it"]=_ml_substr($_POST["NewOption"]["UseForIT"],0,1);
        }
        else
        {
            $data["use_for_it"]='N';
        }

        $check_result=modApiFunc("Product_Options","checkDataFor","addOptionToEntity",$data);
        if(($pe=="product") and !modApiFunc("Catalog","isCorrectProductId",$data["entity_id"]))
            $check_result[]="E_INVALID_PRODUCT_ID";
        if(!empty($check_result))
        {
            modApiFunc("Session","set","Errors",$check_result);
            $request = new Request();
            $request->setKey("parent_entity",$pe);
            $request->setKey("entity_id",$eid);
            $request->setView('PO_AddOption');
            $application->redirect($request);
        }
        else
        {
            $add_result=modApiFunc("Product_Options","addOptionToEntity",$data);
            if($add_result==false)
            {
                modApiFunc("Session","set","SessionPost",$_POST);
                modApiFunc("Session","set","ResultMessage","MSG_OPTION_NOT_ADDED");
                $request = new Request();
                $request->setKey("parent_entity",$pe);
                $request->setKey("entity_id",$eid);
                $request->setView('PO_AddOption');
                $application->redirect($request);
            }
            else
            {
                modApiFunc("Session","set","ResultMessage","MSG_OPTION_ADDED");
                $request = new Request();
                $request->setKey("option_id",$add_result);
                $request->setView('PO_EditOption');
                $application->redirect($request);
            };
        }


    }
}

?>