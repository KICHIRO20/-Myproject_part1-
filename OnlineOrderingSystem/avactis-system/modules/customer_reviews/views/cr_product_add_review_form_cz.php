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

class ProductAddReviewForm
{
    function ProductAddReviewForm()
    {
        global $application;

        $this -> mTmplFiller = new TemplateFiller();
        $this -> _templates = array(
            'container'        => 'ProductAddReviewFormContainer',
            'empty'            => 'ProductAddReviewFormEmpty',
            'forbidden'        => 'ProductAddReviewFormForbidden',
            'ip_used'          => 'ProductAddReviewFormIPUsed',
            'success'          => 'ProductAddReviewFormSuccess',
            'error'            => 'ProductAddReviewFormError',
            'success_message'  => 'ProductAddReviewFormSuccessMessage',
            'error_message'    => 'ProductAddReviewFormErrorMessage',
            'review'           => 'ProductAddReviewFormReview',
            'rating'           => 'ProductAddReviewFormRating',
            'rating_rate'      => 'ProductAddReviewFormRatingRate',
            'rating_header'    => 'ProductAddReviewFormRatingHeader',
            'rating_rate_cell' => 'ProductAddReviewFormRatingRateCell'
        );

        // getting rate values
        $this -> _values = modApiFunc('Customer_Reviews', 'getRateValues');

        // getting rate list
        $this -> _rates = modApiFunc('Customer_Reviews',
                                     'getCustomerReviewsRates', 0, 'Y');

        $this -> NoView = false;
        if ($application -> issetBlockTagFatalErrors('ProductAddReviewForm'))
            $this -> NoView = true;
    }

    function getTemplateFormat()
    {
        $format = array(
            'layout-file' => 'product-add-review-form-block.ini',
            'files'       => array(
                'ProductAddReviewFormContainer'      => TEMPLATE_FILE_SIMPLE,
                'ProductAddReviewFormEmpty'          => TEMPLATE_FILE_SIMPLE,
                'ProductAddReviewFormForbidden'      => TEMPLATE_FILE_SIMPLE,
                'ProductAddReviewFormIPUsed'         => TEMPLATE_FILE_SIMPLE,
                'ProductAddReviewFormSuccess'        => TEMPLATE_FILE_SIMPLE,
                'ProductAddReviewFormError'          => TEMPLATE_FILE_SIMPLE,
                'ProductAddReviewFormSuccessMessage' => TEMPLATE_FILE_SIMPLE,
                'ProductAddReviewFormErrorMessage'   => TEMPLATE_FILE_SIMPLE,
                'ProductAddReviewFormReview'         => TEMPLATE_FILE_SIMPLE,
                'ProductAddReviewFormRating'         => TEMPLATE_FILE_SIMPLE,
                'ProductAddReviewFormRatingRate'     => TEMPLATE_FILE_SIMPLE,
                'ProductAddReviewFormRatingHeader'   => TEMPLATE_FILE_SIMPLE,
                'ProductAddReviewFormRatingRateCell' => TEMPLATE_FILE_SIMPLE
            ),
            'options'     => array(
            )
        );
        return $format;
    }

    /**
     * The output of the Viewer
     * Params: the following optional params can be accepted
     *      0: product_id (use 0 to use default)
     *      1: container_template (use '' to use default)
     *      2: empty_template (use '' to use default)
     *      3: forbidden_template (use '' to use default)
     *      4: ip_used_template (use '' to use default)
     *      5: success_template (use '' to use default)
     *      6: error_template (use '' to use default)
     *      7: success_message_template (use '' to use default)
     *      8: error_message_template (use '' to use default)
     *      9: review_template (use '' to use default)
     *     10: rating_template (use '' to use default)
     *     11: rating_rate_template (use '' to use default)
     *     12: rating_header_template (use '' to use default)
     *     13: rating_rate_cell_template (use '' to use default)
     * Note: to use custom templates be sure to dedcribe them in
     *       - avactis-system/admin/blocks_ini/product-add-review-form-block.ini
     *       - getTemplateFormat() function of the class
     *
     * The following scheme of templates will be shown
     * case 1: adding review is disabled for the product
     *
     *         will be shown:
     *         empty_template (in any case)
     *
     * case 2: a new review has just been posted and
     *         posting a new review is not available
     *         (since multiple reviews from the same IP is disabled)
     *
     *         will be shown:
     *         success_message template
     *
     * case 3: an attempt to post a new review has just been done
     *         while posting a new review is not available
     *         (since multiple reviews from the same IP is disabled)
     *         use case: several attempts was done from different windows
     *
     *         will be shown:
     *         error_message template
     *
     * case 3: multiple reviews from the same IP is disabled
     *         and the review from the IP exists
     *
     *         will be shown:
     *         ip_used_template (in any case)
     *
     * case 4: authorization is needed to add review
     *
     *         will be shown:
     *         forbidden_template (in any case)
     *
     * case 5: review can be added
     *
     *         will be shown:
     *         container_template (in any case)
     *         -> error_template (if any error)
     *            as Local_ErrorMessage() tag
     *         -> success_template (if any success message)
     *            as Local_SuccessMessage() tag
     *         -> rating_template (if rating is available)
     *            -> rating_rate_template (for every rate)
     *               -> rating_header_template (for every rate value)
     *                  as Local_RatingHeader() tag
     *               -> rating_rate_cell_template (for every rate value)
     *                  as Local_RatingCells() tag
     *               as Local_RatingData() tag
     *            as Local_Rating tag
     *         -> review_template (if review is available)
     *            as Local_Review() tag
     */
    function output()
    {
        global $application;

        $product_id = 0;

        if ($this -> NoView)
            return '';

        loadClass('CCustomerInfo');

        // getting the product_id from the params if any
        if (func_num_args() > 0)
            $product_id = func_get_arg(0);

        // if no product_id trying to obtain it from the stack of tags
        if ($product_id <= 0)
        {
            $param = modApiFunc('tag_param_stack',
                                'find_first_param_by_priority',
                                array(TAG_PARAM_PROD_ID));

            if ($param !== PARAM_NOT_FOUND
                && $param['key'] == TAG_PARAM_PROD_ID)
                $product_id = $param['value'];
        }

        // if still no product_id trying to get it from Catalog class
        if ($product_id <= 0)
            $product_id = modApiFunc('Catalog', 'getCurrentProductId');

        // if the number of params is more than 1 assume the other params
        // to be the names of template to show. Only non-empty names are
        // used so to use a default template please set '' (empty string)
        // for it the order in which the templates should be specified is
        // set in the mapping array. Please do not change the order to
        // avoid problems with existing pages. However you can extend
        // the array for your purposes
        // Note: be sure the template exists:
        //           in the ProductAddReviewForm section of
        //              the product-add-review-form-block.ini
        //           in the getTemplateFormat function
        //           in the $this -> _templates array
        $mapping = array('container',
                         'empty',
                         'forbidden',
                         'ip_used',
                         'success',
                         'error',
                         'success_message',
                         'review',
                         'rating',
                         'rating_rate',
                         'rating_header',
                         'rating_rate_cell'
                        );
        for ($i = 0; $i < count($mapping); $i++)
            if (func_num_args() > (1 + $i) && func_get_arg(1 + $i) != '')
                $this -> _templates[$mapping[$i]] = func_get_arg(1 + $i);

        // getting the product data
        $this -> _product_data = modApiFunc(
                                     'Customer_Reviews',
                                     'getBaseProductInfo',
                                     array('product_id' => $product_id)
                                 );

        // processing Session data if available
        $this -> _data = array();

        // checking if session data is available
        if (modApiFunc('Session', 'is_set', 'PostingReviewData'))
        {
            // getting session data
            $posting_data = modApiFunc('Session', 'get', 'PostingReviewData');

            // if session data for the product is available
            // here we presume there can be several add_review forms on a page
            if (isset($posting_data[$product_id]))
            {
                // keeping the data for output
                $this -> _data = $posting_data[$product_id];

                // removing session data for the product from the session
                unset($posting_data[$product_id]);

                // storing the new data in the session
                if (empty($posting_data))
                    modApiFunc('Session', 'un_set', 'PostingReviewData');
                else
                    modApiFunc('Session', 'set',
                               'PostingReviewData', $posting_data);
            }
        }

        // if author is not set in the session while the customer is logged in
        // then sets it to the firstname + lastname of the current customer
        if (modApiFunc('Customer_Account', 'getCurrentSignedCustomer'))
        {
            if (!isset($this -> _data))
                $this -> _data = array();

            if (!isset($this -> _data['author'])
                || !$this -> _data['author'])
            {
                $c_info = new CCustomerInfo(modApiFunc('Customer_Account',
                                                       'getCurrentSignedCustomer'));

                $this -> _data['author'] = $c_info -> getPersonInfo('FirstName',
                                                                    'customer')
                                           . ' ' .
                                           $c_info -> getPersonInfo('LastName',
                                                                    'customer');
            }
        }

        // getting setting for checkout type
        $checkout_type = modApiFunc('Customer_Account','getSettings');
        $checkout_type = $checkout_type['CHECKOUT_TYPE'];

        // getting the store settings
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

        // the number of reviews for the product posted from the IP
        $ip_reviews = modApiFunc('Customer_Reviews',
                                 'getReviewsCountForProductByIP',
                                 $product_id, $_SERVER['REMOTE_ADDR']);

        // is AJAX should be used for adding reviews
        $isAJAXActive = modApiFunc('Settings', 'getParamValue',
                                   'CUSTOMER_REVIEWS',
                                   'CUSTOMER_REVIEWS_AJAX_ACTIVE');

        // setting up the template engine
        $template_block = $application -> getBlockTemplate('ProductAddReviewForm');
        $this -> mTmplFiller -> setTemplate($template_block);

        // registering tags
        $_tags = array(
            'Local_FormAction'   => $_SERVER['PHP_SELF'],
            'Local_ProductID'    => $product_id,
            'Local_ProductName'  => $this -> _product_data['product_name'],
            'Local_ReturnURL'    => prepareHTMLDisplay(modApiFunc('Request',
                                                                  'selfURL')),
            'Local_Error'        => $this -> outputError(),
            'Local_Success'      => $this -> outputSuccess(),
            'Local_Author'       => prepareHTMLDisplay(@$this -> _data['author']),
            'Local_Rating'       => $this -> outputRating(),
            'Local_Review'       => $this -> outputReview(),
            'Local_isAJAXActive' => ($isAJAXActive == 'YES') ? '1' : ''
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        // checking the settings and the result
        // if reviews are disabled -> show empty_template
        // if writing reviews is disabled -> show empty_template
        // if reviews are disabled for the product -> show empty_template
        // if checkout type = quick while writing reviews is available
        // for signed in customers -> show empty_template
        if ($reviews_enable == 'NO'
            || $reviews_writing == 'NONE'
            || !in_array($this -> _product_data['product_cr'], array(5, 6, 7))
            || ($checkout_type == CHECKOUT_TYPE_QUICK
                && $reviews_writing == 'REGONLY'
                && !modApiFunc('Customer_Account', 'getCurrentSignedCustomer')))
            return $this -> mTmplFiller -> fill($this -> _templates['empty']);

        // if multiple reviews from the same IP is disabled
        // while a new review has just been posted
        if ($reviews_multiple == 'NO' && $ip_reviews > 0
            && @$this -> _data['success'] != '')
            return $this -> outputSuccessMessage();

        // if multiple reviews from the same IP is disabled
        // while an attempt to post a review has just been done
        if ($reviews_multiple == 'NO' && $ip_reviews > 0
            && @$this -> _data['error'] != '')
            return $this -> outputErrorMessage();

        // if multiple reviews from the same IP is disabled
        // while a review for the IP exists -> show ip_used template
        if ($reviews_multiple == 'NO' && $ip_reviews > 0)
            return $this -> outputIPUsed();

        // if the customer is anonymous while writing reviews is available
        // for signed in customers -> show forbidden_template
        if ($reviews_writing == 'REGONLY'
            && !modApiFunc('Customer_Account', 'getCurrentSignedCustomer'))
            return $this -> outputForbidden();


        return $this -> mTmplFiller -> fill($this -> _templates['container']);
    }

    /**
     * Outputs IP-Used Template
     * use case: customer has already posted a review
     *           while only one review per IP is allowed
     */
    function outputIPUsed()
    {
        global $application;

        $_tags = array(
            'Local_ProductID' => @$this -> _product_data['product_id']
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['ip_used']);
    }

    /**
     * Outputs Success Message
     * use case: a new review has just been posted successfully
     *           while posting new reviews is not available
     *           (since multiple reviews from the same IP is disabled)
     */
    function outputSuccessMessage()
    {
        global $application;

        $_tags = array(
            'Local_Success'   => @$this -> _data['success'],
            'Local_ProductID' => @$this -> _product_data['product_id']
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['success_message']);
    }

    /**
     * Outputs Error Message
     * use case: an attempt to post a review has just been done
     *           while posting new reviews is not available
     *           (since multiple reviews from the same IP is disabled)
     */
    function outputErrorMessage()
    {
        global $application;

        $_tags = array(
            'Local_Error'   => @$this -> _data['error'],
            'Local_ProductID' => @$this -> _product_data['product_id']
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['error_message']);
    }

    /**
     * Outputs Forbidden Template
     * use case: customer is anonyous while posting a review
     *           is available for signed-in customers
     */
    function outputForbidden()
    {
        global $application;

        $return_url = modApiFunc('Request', 'selfURL');
        if (_ml_strpos($return_url, '#') !== false)
            $return_url = _ml_substr($return_url, _ml_strpos($return_url, '#'));
        $return_url .= '#add_review_' . $this -> _product_data['product_id'];

        $r = new Request;
        $r -> setView(CURRENT_REQUEST_URL);
        $r -> setAction('sign_in_required');

        $r -> setKey('returnURL', urlencode($return_url));

        $_tags = array(
            'Local_SignInURL' => $r -> getURL(),
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['forbidden']);
    }

    /**
     * Outputs Success String (Local_Success tag for main container)
     * use case: a new review has just been posted
     *           while posting new reviews is still available
     */
    function outputSuccess()
    {
        global $application;

        // if no success available returns nothing
        if (!isset($this -> _data['success'])
            || $this -> _data['success'] == '')
            return '';

        $_tags = array('Local_Success' => $this -> _data['success']);
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['success']);
    }

    /**
     * Outputs Error String (Local_Error tag for main container)
     * use case: a new review cannot be added due to some errors
     */
    function outputError()
    {
        global $application;

        // if no error available returns nothing
        if (!isset($this -> _data['error']) || $this -> _data['error'] == '')
            return '';

        $_tags = array('Local_Error' => str_replace("\n", '<br />',
                                                    $this -> _data['error']));
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['error']);
    }

    /**
     * Outputs review template if available. If not outputs nothing
     */
    function outputReview()
    {
        global $application;

        // checking if posting message is available for the product
        // Note: checking other aspects (if module is turned on and so on)
        //       was done before
        if (!in_array($this -> _product_data['product_cr'], array(5, 6)))
            return '';

        $_tags = array(
            'Local_ReviewText' => prepareHTMLDisplay(@$this -> _data['review'])
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['review']);
    }

    /**
     * Outputs rating template if available. If not outputs nothing
     */
    function outputRating()
    {
        global $application;

        // checking if posting rating is available for the product
        // and the rate list is not empty
        // Note: checking other aspects (if module is turned on and so on)
        //       was done before
        if (!in_array($this -> _product_data['product_cr'], array(5, 7))
            || !is_array($this -> _rates) || empty($this -> _rates)
            || !is_array($this -> _values) || empty($this -> _values))
            return '';

        $_tags = array(
            'Local_RatingData'   => $this -> outputRatingData(),
            'Local_RatingHeader' => $this -> outputRatingHeader(),
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['rating']);
    }

    /**
     * Outputs Rating Header for every rate value
     */
    function outputRatingHeader()
    {
        global $application;

        $output = '';

        if (is_array($this -> _values))
            foreach($this -> _values as $k => $v)
            {
                $_tags = array(
                    'Local_RateHeaderName'  => $v['contents'],
                    'Local_RateHeaderImage' => $this -> outputRatingDiv($k + 1)
                );
                $this -> _Template_Contents = $_tags;
                $application -> registerAttributes($this -> _Template_Contents);

                $output .= $this -> mTmplFiller -> fill(
                               $this -> _templates['rating_header']);
            }

        return $output;
    }

    /**
     * Outputs Rating Image (actually the image of stars)
     * @param: $star_number - number of stars to show
     */
    function outputRatingDiv($star_number)
    {
        $percent = 20 * $star_number;

        return '<div class="ratings"><div class="rating-box">' .
               '<div class="rating" style="width:' . $percent .
               '%;"></div></div></div>';
    }

    /**
     * Outputs Rating Rates
     */
    function outputRatingData()
    {
        global $application;

        $output = '';

        foreach($this -> _rates as $k => $v)
        {
            $_tags = array(
                'Local_RateBgColor' => (($k % 2 == 1) ? '#EEEEEE' : '#FFFFFF'),
                'Local_RatingName'  => @$v['rate_label'],
                'Local_RatingCells' => $this -> outputRatingCells($k, $v),
                'Local_RateInputName' => 'rating[' . $v['cr_rl_id'] . ']',
            );
            $this -> _Template_Contents = $_tags;
            $application -> registerAttributes($this -> _Template_Contents);

            $output .= $this -> mTmplFiller -> fill(
                           $this -> _templates['rating_rate']);
        }

        return $output;
    }

    /**
     * Outputs Rating Cells
     */
    function outputRatingCells($k, $rate)
    {
        global $application;

        $output = '';

        foreach($this -> _values as $v)
        {
            $_tags = array(
                'Local_RateBgColor'   => (($k % 2 == 1)
                                             ? '#EEEEEE'
                                             : '#FFFFFF'),
                'Local_RateInputName'    => 'rating[' . $rate['cr_rl_id'] . ']',
                'Local_RateInputValue'   => $v['value'],
                'Local_RateHeaderName'   => $v['contents'],
                'Local_RateInputChecked' => (@$this -> _data['rating'][$rate['cr_rl_id']] == $v['value'])
                                            ? 'checked="checked"'
                                            : ''
            );
            $this -> _Template_Contents = $_tags;
            $application -> registerAttributes($this -> _Template_Contents);

            $output .= $this -> mTmplFiller -> fill(
                           $this -> _templates['rating_rate_cell']);
        }

        return $output;
    }

    /**
     * Processes local tags
     */
    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $NoView;
    var $mTmplFiller;
    var $_Template_Contents;
    var $_data;
    var $_product_data;
    var $_rates;
    var $_templates;
    var $_values;
};

?>