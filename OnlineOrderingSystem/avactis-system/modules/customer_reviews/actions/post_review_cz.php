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

class post_review extends AjaxAction
{
    function post_review()
    {
        // change the wording for errors here
        // do not change the keys of the array
        $this -> _errors = array(
            'no_author'  => getLabel('CR_ERROR_NO_AUTHOR'),
            'no_review'  => getLabel('CR_ERROR_NO_REVIEW'),
            'no_rating'  => getLabel('CR_ERROR_NO_RATING'),
            'no_product' => getLabel('CR_ERROR_NO_PRODUCT'),
            'disabled'   => getLabel('CR_ERROR_DISABLED'),
            'multiple'   => getLabel('CR_ERROR_MULTIPLE'),
            'anonymous'  => getLabel('CR_ERROR_ANONYMOUS')
        );

        // change the wording for success here
        // do not change the keys of the array
        $this -> _success = array(
            'added'    => getLabel('CR_SUCCESS_ADDED'),
            'accepted' => getLabel('CR_SUCCESS_ACCEPTED')
        );

        // setting posted cr_id to 0
        $this -> _cr_id_posted = 0;
    }

    function onAction()
    {
        global $application;
        $request = &$application -> getInstance('Request');

        // getting posted review data
        $review_data = array(
            'author'     => $request -> getValueByKey('author'),
            'review'     => $request -> getValueByKey('review'),
            'rating'     => $request -> getValueByKey('rating'),
            'product_id' => $request -> getValueByKey('product_id')
        );

        // saving product_id in stand alone variable
        // we will need it while redirecting
        $product_id = $review_data['product_id'];

        // getting settings for customer reviews
        // if customer reviews are enabled
        $reviews_enable = modApiFunc('Settings', 'getParamValue',
                                     'CUSTOMER_REVIEWS',
                                     'CUSTOMER_REVIEWS_ENABLE');

        // if only registered customers can post the reviews
        $reviews_writing = modApiFunc('Settings', 'getParamValue',
                                      'CUSTOMER_REVIEWS',
                                      'CUSTOMER_REVIEWS_WRITING');

        // if multiple reviews from the same IP is enabled
        $reviews_multiple = modApiFunc('Settings', 'getParamValue',
                                       'CUSTOMER_REVIEWS',
                                       'CUSTOMER_REVIEWS_MULTIPLE');

        $reviews_approving = modApiFunc('Settings', 'getParamValue',
                                        'CUSTOMER_REVIEWS',
                                        'CUSTOMER_REVIEWS_APPROVING');

        // the number of reviews for the product posted from the IP
        $ip_reviews = modApiFunc('Customer_Reviews',
                                 'getReviewsCountForProductByIP',
                                 $product_id, $_SERVER['REMOTE_ADDR']);

        // getting the product data
        $product_data = modApiFunc('Customer_Reviews', 'getBaseProductInfo',
                                   array('product_id' => $product_id));

        // getting the list of rates
        $rating = modApiFunc('Customer_Reviews', 'getCustomerReviewsRates',
                             0, 'Y');

        // validating the data
        $error = '';
        $review_data['error'] = '';

        // checking the author
        if ($review_data['author'] == '')
        {
            $error = 'no_author';
            $review_data['error'] .= "\n" . $this -> _errors[$error];
        }

        // checking the review
        if ($review_data['review'] == ''
            && in_array($product_data['product_cr'], array(5, 6)))
        {
            $error = 'no_review';
            $review_data['error'] .= "\n" . $this -> _errors[$error];
        }

        // checking the rating
        if (is_array($rating) && !empty($rating)
            && in_array($product_data['product_cr'], array(5, 7)))
        {
            if (!is_array($review_data['rating']))
            {
                $error = 'no_rating';
            }
            else
            {
                foreach($rating as $k => $v)
                    if (!isset($review_data['rating'][$v['cr_rl_id']]))
                        $error = 'no_rating';
                    else
                        $rating[$k]['rate'] = $review_data['rating'][$v['cr_rl_id']];
            }
        }

        // filling the error (to avoid several messages)
        if ($error == 'no_rating')
            $review_data['error'] .= "\n" . $this -> _errors[$error];

        // checking the product_id
        // abnormal error...
        if ($product_id <= 0)
        {
            $error = 'no_product';
            $review_data['error'] .= "\n" . $this -> _errors[$error];
        }

        // if posting reviews is still available
        // use case: settings have been changed
        // before customer submits the form
        if ($reviews_enable == 'NO'
            || $reviews_writing == 'NONE'
            || !in_array($product_data['product_cr'], array(5, 6, 7)))
        {
            $error = 'disabled';
            $review_data['error'] .= "\n" . $this -> _errors[$error];
        }

        // if multiple reviews from the same IP is disabled
        // while a review for the IP exists
        if ($reviews_multiple == 'NO' && $ip_reviews > 0)
        {
            $error = 'multiple';
            $review_data['error'] .= "\n" . $this -> _errors[$error];
        }

        // if the customer is anonymous while writing reviews is available
        // for signed in customers
        if ($reviews_writing == 'REGONLY'
            && !modApiFunc('Customer_Account', 'getCurrentSignedCustomer'))
        {
            $error = 'anonymous';
            $review_data['error'] .= "\n" . $this -> _errors[$error];
        }
	 $moduleexists = modApiFunc('Modules_Manager','isModulePresentInAvactisExtensionsFolder','captcha');

        if($moduleexists == 1)
       	 {
		if (!modApiFunc('Captcha', 'validateCaptcha', 'review'))
        	{
            	$error = 'captcha';
            	$review_data['error'] .= "\n" . getMsg('CS', 'E_INCORRECT_CAPTCHA');
        	}
	}
        // if any error save the error in review_data
        if ($error)
        {
            $review_data['error'] = _ml_substr($review_data['error'], 1);
        }
        else
        {
            // no errors, we are ready to save the data
            // filling the missing information before saving
            // datetime
            $review_data['datetime'] = '\'' . date('Y-m-d H:i:s') . '\'';

            // status (depending if the review needs to be approved)
            $review_data['status'] = 'P';
            if ($reviews_approving == 'NONE'
                || ($reviews_approving == 'ANONYMOUS'
                    && modApiFunc('Customer_Account',
                                  'getCurrentSignedCustomer')))
                $review_data['status'] = 'A';

            // IP address
            $review_data['ip_address'] = $_SERVER['REMOTE_ADDR'];

            // inserting fake review
            execQuery('INSERT_FAKE_CUSTOMER_REVIEW', array());
            $mysql = &$application -> getInstance('DB_MySQL');
            $review_data['cr_id'] = $mysql -> DB_Insert_Id();
            $this -> _cr_id_posted = $review_data['cr_id'];

            // updating the data
            execQuery('UPDATE_CUSTOMER_REVIEW_RECORD', $review_data);

            // saving the rating
            if (is_array($rating))
                foreach($rating as $v)
                    execQuery('INSERT_CUSTOMER_REVIEW_RATE_RECORD',
                              array('cr_id' => $review_data['cr_id'],
                                    'cr_rl_id' => $v['cr_rl_id'],
                                    'rate' => $v['rate']));

            // review has been changed, setting up the success message
            // depending on the status
            // other data is not needed so we clear it
            if ($review_data['status'] == 'A')
                $review_data = array('success' => $this -> _success['added']);
            else
                $review_data = array('success' => $this -> _success['accepted']);
        }

        // saving the review_data in the session
        if (modApiFunc('Session', 'is_set', 'PostingReviewData'))
        {
            $posting_review_data = modApiFunc('Session', 'get',
                                              'PostingReviewData');

        }
        else
        {
            $posting_review_data = array();
        }
        $posting_review_data[$product_id] = $review_data;
        modApiFunc('Session', 'set', 'PostingReviewData', $posting_review_data);

        // all is done, redirecting...
        $return_url = $request -> getValueByKey('return_url');

        // removing the anchor if any
        if (_ml_strpos($return_url, '#') !== false)
            $return_url = _ml_substr($return_url, 0, _ml_strpos($return_url, '#'));

        if ($return_url != '')
        {
            $req_to_redirect = new Request($return_url);
        }
        else
        {
            // abnormal situation...
            $req_to_redirect = new Request();
            $req_to_redirect -> setView(CURRENT_REQUEST_URL);
            $req_to_redurect -> setAction('SetCurrentProduct');
            $req_to_redirect -> setKey('prod_id', $product_id);
        }

        // setting the anchor
        $req_to_redirect -> setAnchor('add_review_' . $product_id);

        // redirecting
        $application -> redirect($req_to_redirect);
    }

    var $_errors;
    var $_success;
    var $_cr_id_posted;
}