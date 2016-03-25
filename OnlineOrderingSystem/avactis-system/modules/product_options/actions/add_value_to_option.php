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

class add_value_to_option extends AjaxAction
{
    function add_value_to_option()
    {
    }

    function onAction()
    {
        global $application;

        $oid=$_POST["option_id"];
        unset($_POST["option_id"]);

        $data=array(
            "value_name" => $_POST["NewValue"]["Name"]
           ,"is_default" => (isset($_POST["NewValue"]["IsDefault"])?'Y':'N')
        );

        foreach($_POST["NewValue"] as $key => $value)
        {
            if(preg_match("/_modifier$/",$key))
                $data[$key]=floatval(str_replace(",",".",trim($value)));
        };

        $check_result=modApiFunc("Product_Options","checkDataFor","addValueToOption",$data);
        if(!empty($check_result))
        {
            modApiFunc("Session","set","Errors",$check_result);
            modApiFunc("Session","set","SessionPost",$_POST);
            $request = new Request();
            $request->setKey("option_id",$oid);
            $request->setView('PO_EditOption');
            $application->redirect($request);
        }
        else
        {
            $data["option_id"]=$oid;
            $add_result=modApiFunc("Product_Options","addValueToOption",$data);
            if($add_result==false)
            {
                modApiFunc("Session","set","SessionPost",$_POST);
                modApiFunc("Session","set","ResultMessage","MSG_VALUE_NOT_ADDED");
            }
            else
            {
                modApiFunc("Session","un_set","SessionPost");
                modApiFunc("Session","set","ResultMessage","MSG_VALUE_ADDED");
            };

            $request = new Request();
            $request->setView('PO_EditOption');
            $request->setKey("option_id",$oid);
            $application->redirect($request);
        };

    }
}

?>