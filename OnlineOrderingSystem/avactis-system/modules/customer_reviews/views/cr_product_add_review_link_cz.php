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

class ProductAddReviewLink
{
    function ProductAddReviewLink()
    {
        global $application;

        $this -> mTmplFiller = new TemplateFiller();
        $this -> _templates = array(
            'container'        => 'ProductAddReviewLinkContainer',
            'empty'            => 'ProductAddReviewLinkEmpty',
            'forbidden'        => 'ProductAddReviewLinkForbidden',
            'ip_used'          => 'ProductAddReviewLinkIPUsed',
        );

        $this -> _view = CURRENT_REQUEST_URL;

        $this -> NoView = false;
        if ($application -> issetBlockTagFatalErrors('ProductAddReviewForm'))
            $this -> NoView = true;
    }

    function getTemplateFormat()
    {
        $format = array(
            'layout-file' => 'product-add-review-link-block.ini',
            'files'       => array(
                'ProductAddReviewLinkContainer' => TEMPLATE_FILE_SIMPLE,
                'ProductAddReviewLinkEmpty'     => TEMPLATE_FILE_SIMPLE,
                'ProductAddReviewLinkForbidden' => TEMPLATE_FILE_SIMPLE,
                'ProductAddReviewLinkIPUsed'    => TEMPLATE_FILE_SIMPLE,
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
     *      1: View Name (use '' to use the same view as on the page)
     *      2: container_template (use '' to use default)
     *      3: empty_template (use '' to use default)
     *      4: forbidden_template (use '' to use default)
     *      5: ip_used_template (use '' to use default)
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
     * case 2: multiple reviews from the same IP is disabled
     *         and the review from the IP exists
     *
     *         will be shown:
     *         ip_used_template (in any case)
     *
     * case 3: authorization is needed to add review
     *
     *         will be shown:
     *         forbidden_template (in any case)
     *
     * case 4: review can be added
     *
     *         will be shown:
     *         container_template (in any case)
     */
    function output()
    {
        global $application;

        $product_id = 0;

        if ($this -> NoView)
            return '';

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

        // if the second param is not empty assume it is the View name
        // Note: be sure it is defined in the storefron-layout.ini
        if (func_num_args() > 1 && func_get_arg(1) != '')
            $this -> _view = func_get_arg(1);

        // if the number of params is more than 2 assume the other params
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
                        );
        for ($i = 0; $i < count($mapping); $i++)
            if (func_num_args() > (2 + $i) && func_get_arg(2 + $i) != '')
                $this -> _templates[$mapping[$i]] = func_get_arg(2 + $i);

        // getting setting for checkout type
        $checkout_type = modApiFunc('Customer_Account', 'getSettings');
        $checkout_type = $checkout_type['CHECKOUT_TYPE'];

        // getting the product data
        $this -> _product_data = modApiFunc(
                                     'Customer_Reviews',
                                     'getBaseProductInfo',
                                     array('product_id' => $product_id)
                                 );

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

        // setting up the template engine
        $template_block = $application -> getBlockTemplate('ProductAddReviewLink');
        $this -> mTmplFiller -> setTemplate($template_block);

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
            return $this -> outputEmpty();

        // if multiple reviews from the same IP is disabled
        // while a review for the IP exists -> show ip_used template
        if ($reviews_multiple == 'NO' && $ip_reviews > 0)
            return $this -> outputIPUsed();

        // if the customer is anonymous while writing reviews is available
        // for signed in customers -> show forbidden_template
        if ($reviews_writing == 'REGONLY'
            && !modApiFunc('Customer_Account', 'getCurrentSignedCustomer'))
            return $this -> outputForbidden();

        // registering tags
        $_tags = array(
            'Local_Link' => $this -> getLocalLink($product_id)
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['container']);
    }

    function getLocalLink($prod_id)
    {
        if ($this -> _view == CURRENT_REQUEST_URL)
            return '#add_review_' . $prod_id;

        $cid = modApiFunc('CProductListFilter','getCurrentCategoryId');

        $r = new Request;
        $r -> setView($this -> _view);
        $r -> setAction('SetCurrentProduct');
        $r -> setKey('prod_id', $prod_id);
        $r -> setProductID($prod_id);
        $r -> setCategoryID($cid);
        $r -> setAnchor('add_review_' . $prod_id);

        return $r -> getURL();
    }

    /**
     * Outputs Empty Template
     */
    function outputEmpty()
    {
        global $application;

        $_tags = array(
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['empty']);
    }

    /**
     * Outputs IP_Used Template
     */
    function outputIPUsed()
    {
        global $application;

        $_tags = array(
            'Local_Link' => $this -> getLocalLink($this -> _product_data['product_id'])
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['ip_used']);
    }

    /**
     * Outputs Forbidden Template
     * use case: customer is anonyous while posting a review
     *           is available for signed-in customers
     */
    function outputForbidden()
    {
        global $application;

        if ($this -> _view == CURRENT_REQUEST_URL)
        {
            $return_url = modApiFunc('Request', 'selfURL');
            if (_ml_strpos($return_url, '#') !== false)
                $return_url = _ml_substr($return_url, 0, _ml_strpos($return_url, '#'));
            $return_url .= '#add_review_' .
                           $this -> _product_data['product_id'];
        }
        else
        {
            $r = new Request;
            $r -> setView($this -> _view);
            $r -> setAction('SetCurrentProduct');
            $r -> setKey('prod_id', $this -> _product_data['product_id']);
            $r -> setAnchor('add_review_' .
                            $this -> _product_data['product_id']);
            $return_url = $r -> getURL();
        }

        $r = new Request;
        $r -> setView($this -> _view);
        $r -> setAction('sign_in_required');

        $r -> setKey('returnURL', urlencode($return_url));

        $_tags = array(
            'Local_Link' => $r -> getURL(),
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['forbidden']);
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
    var $_product_data;
    var $_templates;
    var $_view;
};

?>