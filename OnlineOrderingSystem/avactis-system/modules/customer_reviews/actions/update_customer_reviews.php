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

class update_customer_reviews extends AjaxAction
{
    function update_customer_reviews()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application -> getInstance('Request');
        $mode = $request -> getValueByKey('mode');

        switch($mode)
        {
            case 'update':
                $data = $request -> getValueByKey('data');

                if (is_array($data))
                    foreach($data as $cr_id => $record_data)
                        modApiFunc('Customer_Reviews', 'updateCustomerReview',
                                   $cr_id, $record_data);

                modApiFunc('Session','set','ResultMessage',
                           'CR_MSG_CUSTOMER_REVIEWS_UPDATED');
                break;

            case 'delete':
                $review_id = $request -> getValueByKey('review_id');

                if (is_array($review_id))
                    foreach($review_id as $cr_id)
                        modApiFunc('Customer_Reviews',
                                   'deleteCustomerReview', $cr_id);

                modApiFunc('Session','set','ResultMessage',
                           'CR_MSG_CUSTOMER_REVIEWS_DELETED');
                break;
        }

        // trying to restore GET data
        $cr_url = modApiFunc('Session', 'get', 'CR_URL');

        $req_to_redirect = new Request($cr_url);

        if ($cr_url == '')
            $req_to_redirect -> setView(CURRENT_REQUEST_URL);

        $application -> redirect($req_to_redirect);
    }
};

?>