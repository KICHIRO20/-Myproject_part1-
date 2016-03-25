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

class update_option_of_entity extends AjaxAction
{
    function update_option_of_entity()
    {
    }

    function onAction()
    {
        global $application;

        $oid = $_POST["option_id"];
        $pe = $_POST["parent_entity"];
        $eid = $_POST["entity_id"];
        unset($_POST["option_id"],$_POST["entity_id"],$_POST["parent_entity"]);

        $data=array(
            "option_id" => $oid
           ,"parent_entity" => $pe
           ,"entity_id" => $eid
           ,"option_name" => $_POST["Option"]["OptionName"]
           ,"display_name" => $_POST["Option"]["DisplayName"]
           ,"display_descr" => $_POST["Option"]["DisplayDescr"]
           ,"option_type" => $_POST["Option"]["OptionType"]
           ,"show_type" => $_POST["Option"]["ShowType"][$_POST["Option"]["OptionType"]]
        );

        if($data["option_type"]=="SS")
        {
            $data["discard_avail"]=_ml_substr($_POST["Option"]["DiscardAvail"],0,1);
            if($data["discard_avail"]=='Y')
                $data["discard_value"]=$_POST["Option"]["DiscardValue"];
        };
        if($data["option_type"]=="UF")
        {
            $data["discard_avail"]=_ml_substr($_POST["Option"]["DiscardAvail"],0,1);
        };

        if($data["option_type"]=="CI" and in_array($data["show_type"],array('CBSI','CBTA')))
        {
            $data["checkbox_text"]=$_POST["Option"]["CheckBoxText"];
        };

        if(!in_array($data["option_type"],array("CI","UF")))
        {
            $data["use_for_it"]=_ml_substr($_POST["Option"]["UseForIT"],0,1);
        }
        else
        {
            $data["use_for_it"]='N';
        }

        $check_result=modApiFunc("Product_Options","checkDataFor","updateOptionOfEntity",$data);
        if(!empty($check_result))
        {
            modApiFunc("Session","set","Errors",$check_result);
            $request = new Request();
            $request->setKey("option_id",$oid);
            $request->setView('PO_EditOption');
            $application->redirect($request);
        }
        else
        {
            $update_result=modApiFunc("Product_Options","updateOptionOfEntity",$data);
            if($update_result==false)
            {
                modApiFunc("Session","set","SessionPost",$_POST);
                modApiFunc("Session","set","ResultMessage","MSG_OPTION_NOT_UPDATED");
            }
            else
            {
                modApiFunc("Session","set","ResultMessage","MSG_OPTION_UPDATED");
            };

            $request = new Request();
            $request->setKey("option_id",$oid);
            $request->setView('PO_EditOption');
            $application->redirect($request);
        }


    }
}

?>