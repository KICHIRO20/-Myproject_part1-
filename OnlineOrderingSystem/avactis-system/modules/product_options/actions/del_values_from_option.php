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

class del_values_from_option extends AjaxAction
{
    function del_values_from_option()
    {
    }

    function onAction()
    {
        global $application;

        $oid=$_POST["option_id"];
        unset($_POST["option_id"]);

        $data=array(
            "option_id" => $oid
           ,"values_ids" => array_keys($_POST["toDeleteValues"])
        );

        $check_result=modApiFunc("Product_Options","checkDataFor","delValuesFromOption",$data);
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
            $del_result=modApiFunc("Product_Options","delValuesFromOption",$data);
            if($del_result==false)
            {
                modApiFunc("Session","set","ResultMessage","MSG_VALUES_NOT_DELETED");
            }
            else
            {
                modApiFunc("Session","set","ResultMessage","MSG_VALUES_DELETED");
            };

            $request = new Request();
            $request->setView('PO_EditOption');
            $request->setKey('option_id',$oid);
            $application->redirect($request);
        }

    }
};

?>