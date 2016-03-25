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

class update_values_of_option extends AjaxAction
{
    function update_values_of_option()
    {
    }

    function onAction()
    {
        global $application;

        $oid=$_POST["option_id"];
        unset($_POST["option_id"]);

        $data=array(
            "option_id" => $oid
           ,"values" => array()
        );

        foreach($_POST["UpdateValues"] as $vid => $vdata)
        {
            $data["values"][$vid]=array(
                "value_name" => $vdata["Name"]
               ,"is_default" => (isset($vdata["IsDefault"]) ? 'Y' : 'N')
            );
            foreach($vdata as $key => $value)
            {
                if(preg_match("/_modifier$/",$key))
                $data["values"][$vid][$key]=floatval(str_replace(",",".",trim($value)));
            };
        };
        if(isset($_POST["IsDefault"]))
            $data["values"][intval($_POST["IsDefault"])]["is_default"]="Y";

        $check_result=modApiFunc("Product_Options","checkDataFor","updateValuesOfOption",$data);
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

            $update_result=modApiFunc("Product_Options","updateValuesOfOption",$data);
            if(in_array(false,$update_result))
            {
                modApiFunc("Session","set","ResultMessage","MSG_VALUES_NOT_UPDATED");
            }
            else
            {
                modApiFunc("Session","set","ResultMessage","MSG_VALUES_UPDATED");
            };

            $request = new Request();
            $request->setView('PO_EditOption');
            $request->setKey('option_id',$oid);
            $application->redirect($request);
        }
    }
};

?>