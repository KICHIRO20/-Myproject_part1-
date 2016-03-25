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

class ProductReviewList
{
    function ProductReviewList()
    {
        global $application;

        $this -> mTmplFiller = new TemplateFiller();
        $this -> _templates = array(
            'container'    => 'ProductReviewListContainer',
            'item'         => 'ProductReviewListItem',
            'empty'        => 'ProductReviewListEmpty',
            'forbidden'    => 'ProductReviewListForbidden',
            'disabled'     => 'ProductReviewListDisabled',
            'paginator'    => 'ProductReviewListPaginator',
            'message'      => 'ProductReviewListMessage',
            'messageempty' => 'ProductReviewListMessageEmpty',
            'rating'       => 'ProductReviewListRating',
            'ratingrate'   => 'ProductReviewListRatingRate',
            'ratingempty'  => 'ProductReviewListRatingEmpty'
        );

        // by default paginator is used
        // for positive number paginator will not be used
        // and the last _count_to_show reviews will be shown
        $this -> _count_to_show = 0;

        // by default paginator block is CustomerReviews
        // can be overwritten in output
        $this -> _pagblock = 'ProductInfo';

        $this -> NoView = false;
        if ($application -> issetBlockTagFatalErrors('ProductReviewList'))
            $this -> NoView = true;
    }

    function getTemplateFormat()
    {
        $format = array(
            'layout-file' => 'product-review-list-block.ini',
            'files'       => array(
                'ProductReviewListContainer'    => TEMPLATE_FILE_SIMPLE,
                'ProductReviewListItem'         => TEMPLATE_FILE_SIMPLE,
                'ProductReviewListEmpty'        => TEMPLATE_FILE_SIMPLE,
                'ProductReviewListForbidden'    => TEMPLATE_FILE_SIMPLE,
                'ProductReviewListDisabled'     => TEMPLATE_FILE_SIMPLE,
                'ProductReviewListPaginator'    => TEMPLATE_FILE_SIMPLE,
                'ProductReviewListMessage'      => TEMPLATE_FILE_SIMPLE,
                'ProductReviewListMessageEmpty' => TEMPLATE_FILE_SIMPLE,
                'ProductReviewListRating'       => TEMPLATE_FILE_SIMPLE,
                'ProductReviewListRatingRate'   => TEMPLATE_FILE_SIMPLE,
                'ProductReviewListRatingEmpty'  => TEMPLATE_FILE_SIMPLE
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
     *      1: items to show (use 0 to use paginator)
     *      2: paginator block (use '' to use default)
     *      3: container_template (use '' to use default)
     *      4: item_template (use '' to use default)
     *      5: empty_template (use '' to use default)
     *      6: forbidden_template (use '' to use default)
     *      7: disabled_template (use '' to use default)
     *      8: paginator_template (use '' to use default)
     *      9: message_template (use '' to use default)
     *     10: messageempty_template (use '' to use default)
     *     11: rating_template (use '' to use default)
     *     12: ratingrate_template (use '' to use default)
     *     13: ratingempty_template (use '' to use default)
     * Note: to use custom templates be sure to dedcribe them in
     *       - avactis-system/admin/blocks_ini/product-review-list-block.ini
     *       - getTemplateFormat() function of the class
     *
     * The following scheme of templates will be shown
     * case 1: there is no rating for the product
     *         or the rating is hidden from the customer
     *
     *         will be shown:
     *         empty_template (in any case)
     *
     * case 2: the rating for the product is disabled
     *
     *         will be shown:
     *         disabled_template (in any case)
     *
     * case 3: the rating is hidden from the customer
     *
     *         will be shown:
     *         forbidden_template (in any case)
     *
     * case 4: rating is available and can be shown
     *
     *         will be shown:
     *         container_template (in any case)
     *         -> item_template (in any case for every review)
     *            -> rating_template (if rating is available)
     *               -> ratingrate_template (for every rate)
     *                  as Local_RatingList tag
     *            -> ratingempty_template (if no rating or it is hidden)
     *               as Local_ReviewRating tag
     *            -> message_template (if message is available)
     *            -> messageempty_template (if no message or it is hidden)
     *               as Local_ReviewMessage tag
     *            as Local_ReviewList tag
     *         -> paginator_template (if paginator is used)
     */
    function output()
    {
        global $application;

        $this->product_id = 0;

        if ($this -> NoView)
            return '';

        // getting the product_id from the params if any
        if (func_num_args() > 0)
            $this->product_id = func_get_arg(0);

        // if no product_id trying to obtain it from the stack of tags
        if ($this->product_id <= 0)
        {
            $param = modApiFunc('tag_param_stack',
                                'find_first_param_by_priority',
                                array(TAG_PARAM_PROD_ID));

            if ($param !== PARAM_NOT_FOUND
                && $param['key'] == TAG_PARAM_PROD_ID)
                $this->product_id = $param['value'];
        }

        // if still no product_id trying to get it from Catalog class
        if ($this->product_id <= 0)
            $this->product_id = modApiFunc('Catalog', 'getCurrentProductId');

        // if the number of params is more than 1 assume the second param
        // is the number of reviews to show (for 0 the paginator will be used)
        if (func_num_args() > 1)
            $this -> _count_to_show = intval(func_get_arg(1));

        // if the number of params is more than 2 assume the third param
        // is the name of paginator block
        // it should be defined in storefront-layout.ini
        // by default it is ProductInfo
        if (func_num_args() > 2 && func_get_arg(2) != '')
            $this -> _pagblock = func_get_arg(2);

        // setting paginator name
        $this -> _pagname = 'CR_' . $this -> _pagblock . '_' . $this->product_id;

        // if the number of params is more than 3 assume the other params
        // to be the names of template to show. Only non-empty names are
        // used so to use a default template please set '' (empty string)
        // for it the order in which the templates should be specified is
        // set in the mapping array. Please do not change the order to
        // avoid problems with existing pages. However you can extend
        // the array for your purposes
        // Note: be sure the template exists:
        //           in the ProductReviewList section of
        //              the product-review-list-block.ini
        //           in the getTemplateFormat function
        //           in the $this -> _templates array
        $mapping = array('container',
                         'item',
                         'empty',
                         'forbidden',
                         'disabled',
                         'paginator',
                         'message',
                         'messageempty',
                         'rating',
                         'ratingrate',
                         'ratingempty'
                        );
        for ($i = 0; $i < count($mapping); $i++)
            if (func_num_args() > (3 + $i) && func_get_arg(3 + $i) != '')
                $this -> _templates[$mapping[$i]] = func_get_arg(3 + $i);

        // getting the product data
        $this -> _product_data = modApiFunc(
                                     'Customer_Reviews',
                                     'getBaseProductInfo',
                                     array('product_id' => $this->product_id)
                                 );

        // getting the store settings
        // if customer reviews are enabled
        $reviews_enable = modApiFunc('Settings', 'getParamValue',
                                     'CUSTOMER_REVIEWS',
                                     'CUSTOMER_REVIEWS_ENABLE');

        // if only registered customers can view the reviews
        $reviews_viewing = modApiFunc('Settings', 'getParamValue',
                                      'CUSTOMER_REVIEWS',
                                      'CUSTOMER_REVIEWS_VIEWING');

        // getting the total count of reviews
        $this -> _total_count = modApiFunc('Customer_Reviews',
                                           'getProductCustomerReviewNumber',
                                           $this->product_id);

        // setting up the template engine
        $template_block = $application -> getBlockTemplate('ProductReviewList');
        $this -> mTmplFiller -> setTemplate($template_block);

        // setting up the paginator if necessary
        if ($this -> _count_to_show == 0)
            modApiFunc('paginator', 'setCurrentPaginatorName',
                       $this -> _pagname);

        // building up the filter
        $this -> _filter = array(
            'product' => array('id' => $this->product_id),
            'status'  => 'A',
            'visible' => 'Y'
        );

        // adding limits to the filter (depending on using paginator or not)
        if ($this -> _count_to_show <= 0)
        {
            $this -> _filter['paginator'] = null;
            $this -> _filter['paginator'] = modApiFunc(
                                                'Customer_Reviews',
                                                'searchPgCustomerReviews',
                                                $this -> _filter,
                                                PAGINATOR_ENABLE
                                            );
        }
        else
        {
            $this -> _filter['limit'] = array(0, $this -> _count_to_show);
        }

        // getting the reviews to show
        $this -> _found_reviews = modApiFunc('Customer_Reviews',
                                             'searchCustomerReviews',
                                             $this -> _filter);

        // registering tags
        $_tags = array(
            'Local_ProductID'     => $this -> product_id,
            'Local_ReviewList'    => $this -> outputReviews(),
            'Local_PaginatorData' => $this -> outputPaginatorData()
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        // checking the settings and the result
        // if no rating for the product is found -> show empty_template
        if ($this -> _total_count <= 0)
            return $this -> mTmplFiller -> fill($this -> _templates['empty']);

        // if the rating is disabled -> show disabled_template
        // if the rating is disabled for the product -> show disabled_template
        // Note: 5, 6 and 7 are pre-set values for message+rate,
        //       message and rate_only
        if ($reviews_enable == 'NO'
            || !in_array($this -> _product_data['product_cr'], array(5, 6, 7)))
            return $this -> mTmplFiller -> fill($this -> _templates['disabled']);

        // if the customer is anonymous while the reviews are available
        //    for signed in customers -> show forbidden_template
        if ($reviews_viewing == 'REGONLY'
            && !modApiFunc('Customer_Account', 'getCurrentSignedCustomer'))
            return $this -> mTmplFiller -> fill($this -> _templates['forbidden']);

        return $this -> mTmplFiller -> fill($this -> _templates['container']);
    }

    function outputReviews()
    {
        global $application;

        $output = '';

        // for each found reviews showing the item_template
        foreach($this -> _found_reviews as $v)
        {
            // registering tags
            $_tags = array(
                'Local_ReviewAuthor'  => prepareHTMLDisplay($v['author']),
                'Local_ReviewDate'    => $v['date'],
                'Local_ReviewRating'  => $this -> outputRating($v),
                'Local_ReviewMessage' => $this -> outputMessage($v)
            );
            $this -> _Template_Contents = $_tags;
            $application -> registerAttributes($this -> _Template_Contents);

            $output .= $this -> mTmplFiller -> fill(
                           $this -> _templates['item']
                       );
        }

        return $output;
    }

    function outputRating($record)
    {
        global $application;

        // if rating is not available or hidden
        // outputting rating_empty template
        if (!in_array($this -> _product_data['product_cr'], array(5, 7))
            || empty($record['rating_cz']))
            return $this -> mTmplFiller -> fill(
                       $this -> _templates['ratingempty']
                   );

        // registering tags
        $_tags = array(
            'Local_RatingList' => $this -> outputRatingList($record['rating_cz'])
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['rating']);
    }

    function outputRatingList($rating)
    {
        global $application;

        if (!is_array($rating) || empty($rating))
            return '';

        $output = '';
        foreach($rating as $v)
        {
            // registering tags
            $_tags = array(
                'Local_RateLabel'  => $v['rate_label'],
                'Local_RateRating' => intval($v['rate'] * 20)
            );
            $this -> _Template_Contents = $_tags;
            $application -> registerAttributes($this -> _Template_Contents);

            $output .= $this -> mTmplFiller -> fill($this -> _templates['ratingrate']);
        }

        return $output;
    }

    function outputMessage($record)
    {
        global $application;

        // if message is not available or hidden
        // outputting message_empty template
        if (!in_array($this -> _product_data['product_cr'], array(5, 6))
            || !$record['review'])
            return $this -> mTmplFiller -> fill(
                       $this -> _templates['messageempty']
                   );

        // registering tags
        $_tags = array(
            'Local_ReviewText' => str_replace("\n", '<br />',
                prepareHTMLDisplay($record['review'])
            )
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['message']);
    }

    function outputPaginatorData()
    {
        // if paginator is not used returning empty string
        if ($this -> _count_to_show != 0)
            return '';

        // otherwise showing the paginator template
        return $this -> mTmplFiller -> fill($this -> _templates['paginator']);
    }

    function getTag($tag)
    {
        // overriding paginator line
        if ($tag == 'PaginatorLine')
            return getPaginatorLine($this -> _pagname, $this -> _pagblock, modApiFunc('CProductListFilter','getCurrentCategoryId'), $this->product_id);

        // overriding paginator dropdown
        if ($tag == 'PaginatorDropdown')
            return getPaginatorDropdown($this -> _pagname, $this -> _pagblock,
                                        'customer reviews');

        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $NoView;
    var $mTmplFiller;
    var $_Template_Contents;
    var $_filter;
    var $_found_reviews;
    var $_count_to_show;
    var $_pagname;
    var $_pagblock;
    var $_product_data;
    var $_templates;
    var $_total_count;
    var $product_id;
};

?>