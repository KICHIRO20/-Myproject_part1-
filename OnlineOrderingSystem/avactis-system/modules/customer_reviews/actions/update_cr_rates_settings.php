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
 * @package Customer_Reviews
 * @author Sergey E. Kulitsky
 *
 */

class update_cr_rates_settings extends AjaxAction
{
    function update_cr_rates_settings()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application -> getInstance('Request');

        $mode = $request -> getValueByKey('mode');
        switch($mode)
        {
            case 'sort':
                $sort_array = explode('|', $request -> getValueByKey('cr_rates_sort_order_hidden'));
                modApiFunc('Customer_Reviews', 'updateRateSortOrder', $sort_array);
                modApiFunc('Session','set','ResultMessage','CR_MSG_RATES_SORTED');
                break;
            case 'add':
                $rate_new = $request -> getValueByKey('rate_new');
                $result = modApiFunc('Customer_Reviews', 'insertNewRate', $rate_new['rate_label'], $rate_new['visible']);
                if ($result)
                    modApiFunc('Session','set','ResultMessage','CR_MSG_RATE_ADDED');
                else
                    modApiFunc('Session','set','ResultMessage','CR_MSG_EMPTY_RATE_CANNOT_BE_ADDED');
                break;

            case 'update':
                modApiFunc('Customer_Reviews', 'updateRates', $request -> getValueByKey('rates'));
                modApiFunc('Session','set','ResultMessage','CR_MSG_RATES_SETTINGS_UPDATED');
                break;

            case 'delete':
                modApiFunc('Customer_Reviews', 'deleteRates', $request -> getValueByKey('selected_rates'));
                modApiFunc('Session','set','ResultMessage','CR_MSG_RATES_DELETED');
                break;
        }

        $req_to_redirect = new Request();
        $req_to_redirect -> setView(CURRENT_REQUEST_URL);
        $req_to_redirect -> setKey('page_view','CR_Rates_Settings');
        $application -> redirect($req_to_redirect);
    }
};

?>