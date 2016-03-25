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
 * @package ProductOptions
 * @author Egor V. Derevyankin
 *
 */

class update_values_sort extends AjaxAction
{
    function update_values_sort()
    {
    }

    function onAction()
    {
        global $application;
        $sort_array=explode("|",$_POST["valuesSortOrder_hidden"]);
        if(modApiFunc("Product_Options","updateValuesSortOrder",$sort_array))
        {
            modApiFunc("Session","set","ResultMessage","MSG_VALUES_SORT_ORDER_UPDATED");
        }
        else
        {
            modApiFunc("Session","set","ResultMessage","MSG_VALUES_SORT_ORDER_NOT_UPDATED");
        };
        $request = new Request();
        $request->setView('PO_EditOption');
        $request->setKey('option_id',$_POST["option_id"]);
        $application->redirect($request);
    }

};

?>