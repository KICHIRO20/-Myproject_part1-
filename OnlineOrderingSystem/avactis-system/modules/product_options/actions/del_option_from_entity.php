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

class del_option_from_entity extends AjaxAction
{
    function del_option_from_entity()
    {
    }

    function onAction()
    {
        global $application;

        $data=array(
            "parent_entity" => $_POST["parent_entity"]
           ,"entity_id" => intval($_POST["entity_id"])
           ,"option_id" => intval($_POST["option_id"])
        );

        $check_result=modApiFunc("Product_Options","checkDataFor","delOptionFromEntity",$data);
        if(!empty($check_result))
        {
            modApiFunc("Session","set","Errors",$check_result);
            $request = new Request();
            $request->setKey("parent_entity",$data["parent_entity"]);
            $request->setKey("entity_id",$data["entity_id"]);
            $request->setView('PO_OptionsList');
            $application->redirect($request);
        }
        else
        {
            $del_result=modApiFunc("Product_Options","delOptionFromEntity",$data);
            if($del_result==true)
            {
                modApiFunc("Session","set","ResultMessage","MSG_OPTION_DELETED");
            }
            else
            {
                modApiFunc("Session","set","ResultMessage","MSG_OPTION_NOT_DELETED");
            };

            $request = new Request();
            $request->setView('PO_OptionsList');
            $request->setKey('parent_entity',$data["parent_entity"]);
            $request->setKey('entity_id',$data["entity_id"]);
            $application->redirect($request);
        };
    }
}

?>