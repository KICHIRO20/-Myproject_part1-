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

class update_review_data extends AjaxAction
{
    function update_review_data()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application -> getInstance('Request');

        // getting posted review data
        $review_data = $request -> getValueByKey('review_data');

        // getting mode
        $mode = $request -> getValueByKey('mode');

        if ($mode == 'product_changed')
        {
            $review_data['datetime'] = $review_data['year'] . '-' .
                                       $review_data['month'] . '-' .
                                       $review_data['day'] . ' ' .
                                       $review_data['hour'] . ':' .
                                       $review_data['minute'] . ':' .
                                       $review_data['second'];

            // product has been changed, continue...
            modApiFunc('Session', 'set', 'SavedReviewData', $review_data);
            $req_to_redirect = new Request();
            $req_to_redirect -> setView(CURRENT_REQUEST_URL);
            $req_to_redirect -> setKey('page_view', 'CR_Review_Data');
            $req_to_redirect -> setKey('cr_id', $review_data['cr_id']);
            $application -> redirect($req_to_redirect);
            return;
        }

        // validating data
        $error = $this -> validatePostedData($review_data);

        if ($error)
        {
            // if there is an error save the data and reload the page
            modApiFunc('Session', 'set', 'SavedReviewData', $review_data);
            $req_to_redirect = new Request();
            $req_to_redirect -> setView(CURRENT_REQUEST_URL);
            $req_to_redirect -> setKey('page_view', 'CR_Review_Data');
            $req_to_redirect -> setKey('cr_id', $review_data['cr_id']);
            $application -> redirect($req_to_redirect);
        }
        else
        {
            // no errors... ready to save the data
            $cr_id = $this -> savePostedData($review_data);

            // reload the parent window while reloading
            modApiFunc('Session', 'set', 'CR_ReloadParentWindow', 1);

            // setting ResultMessage
            if (@$review_data['cr_id'] <= 0)
                modApiFunc('Session', 'set', 'ResultMessage',
                           'CR_MSG_CUSTOMER_REVIEW_ADDED');
            else
                modApiFunc('Session', 'set', 'ResultMessage',
                           'CR_MSG_CUSTOMER_REVIEW_UPDATED');

            // prepare the redirect
            $req_to_redirect = new Request();
            $req_to_redirect -> setView(CURRENT_REQUEST_URL);
            $req_to_redirect -> setKey('page_view', 'CR_Review_Data');
            $req_to_redirect -> setKey('cr_id', $cr_id);
            $application -> redirect($req_to_redirect);
        }
    }

    function validatePostedData($review_data)
    {
        // checking if product specified
        if (!isset($review_data['product_id'])
            || $review_data['product_id'] <= 0)
        {
            modApiFunc('Session','set','ResultMessage',
                       'CR_MSG_REVIEW_SPECIFY_PRODUCT');
            return 'PS';
        }

        // checking if author is specified
        if (!isset($review_data['author'])
            || $review_data['author'] == '')
        {
            modApiFunc('Session','set','ResultMessage',
                       'CR_MSG_REVIEW_SPECIFY_AUTHOR');
            return 'AS';
        }

        // getting product info
        $product_info = modApiFunc(
                           'Customer_Reviews',
                           'getBaseProductInfo',
                           array('product_id' => $review_data['product_id'])
                        );

        // checking id the product is still alive
        if (!$product_info['product_id'])
        {
            modApiFunc('Session','set','ResultMessage',
                       'CR_MSG_REVIEW_INVALID_PRODUCT');
            return 'PD';
        }

        if (in_array($product_info['product_cr'], array(5, 6))
            && (!isset($review_data['review'])
                || $review_data['review'] == ''))
        {
            modApiFunc('Session','set','ResultMessage',
                       'CR_MSG_REVIEW_SPECIFY_REVIEW');
            return 'RS';
        }

        return '';
    }

    function savePostedData($review_data)
    {
        global $application;

        // if it is a new review create a fake review firstly
        if (@$review_data['cr_id'] <= 0)
        {
            execQuery('INSERT_FAKE_CUSTOMER_REVIEW', array());
            $mysql = &$application -> getInstance('DB_MySQL');
            $review_data['cr_id'] = $mysql -> DB_Insert_Id();
        }

        // updating review information
        // Note: adjusting the date since we explicitly set the datetime
        execQuery('UPDATE_CUSTOMER_REVIEW_RECORD',
                  array(
                      'cr_id' => $review_data['cr_id'],
                      'datetime' => 'DATE_SUB(\'' . $review_data['year'] . '-' .
                                    $review_data['month'] . '-' .
                                    $review_data['day'] . ' ' .
                                    $review_data['hour'] . ':' .
                                    $review_data['minute'] . ':' .
                                    $review_data['second'] . '\', ' .
                                    modApiFunc('Localization', 'getSQLInterval') .
                                    ')',
                      'author' => $review_data['author'],
                      'ip_address' => $review_data['ip_address'],
                      'product_id' => $review_data['product_id'],
                      'review' => $review_data['review'],
                      'status' => $review_data['status']
                  ));

        // deleting all the rates for the review before adding the current ones
        // use case: additional checking the DB does not contain fake records
        execQuery('DELETE_CUSTOMER_REVIEW_RATE_RECORD',
                  array('cr_id' => $review_data['cr_id']));

        // adding the current rates
        if (is_array($review_data['rating']))
            foreach($review_data['rating'] as $cr_rl_id => $rate)
                execQuery('INSERT_CUSTOMER_REVIEW_RATE_RECORD',
                          array('cr_id' => $review_data['cr_id'],
                                'cr_rl_id' => $cr_rl_id, 'rate' => $rate));

        return $review_data['cr_id'];
    }
}