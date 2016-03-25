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

class ProductDetailedRating
{
    function ProductDetailedRating()
    {
        global $application;

        $this -> mTmplFiller = new TemplateFiller();
        $this -> _templates = array(
            'container'   => 'ProductDetailedRatingContainer',
            'item'        => 'ProductDetailedRatingItem',
            'empty'       => 'ProductDetailedRatingEmpty',
            'add_review'  => 'ProductDetailedRatingAddReview',
            'no_rating'   => 'ProductDetailedRatingNoRating'
        );

        $this -> NoView = false;
        if ($application -> issetBlockTagFatalErrors('ProductDetailedRating'))
            $this -> NoView = true;
    }

    function getTemplateFormat()
    {
        $format = array(
            'layout-file' => 'product-detailed-rating-block.ini',
            'files'       => array(
                'ProductDetailedRatingContainer'  => TEMPLATE_FILE_SIMPLE,
                'ProductDetailedRatingItem'       => TEMPLATE_FILE_SIMPLE,
                'ProductDetailedRatingEmpty'      => TEMPLATE_FILE_SIMPLE,
                'ProductDetailedRatingAddReview'  => TEMPLATE_FILE_SIMPLE,
                'ProductDetailedRatingNoRating'   => TEMPLATE_FILE_SIMPLE
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
     *         available local tags: Local_RatingList, Local_ProductInfoLink
     *      2: item_template (use '' to use default)
     *         available local tags: Local_Rate_Label, Local_Rate_Rating
     *      3: empty_template (use '' to use default)
     *         no local tags available
     *      4: add_review_template (use '' to use default)
     *         available local tags: Local_ProductInfoLink
     *      5: no_rating_template (use '' to use default)
     *         available local tags: Local_ProductInfoLink
     * Note: to use custom templates be sure to dedcribe them in
     *       - avactis-system/admin/blocks_ini/product-detailed-rating-block.ini
     *       - getTemplateFormat() function of the class
     *
     * The following scheme of templates will be shown
     * case 1: the rating for the product is disabled
     *         or the rating is hidden from the customer
     *
     *         will be shown:
     *         empty_template (in any case)
     *
     * case 2: there is no rating for the product
     *         but the customer reviews are enabled for the product
     *
     *         will be shown:
     *         add_review_template
     *
     * case 3: reviews are enable for the product but the rating is disabled
     *         (customer_reviews for the product = 6)
     *
     *         will be shown:
     *         no_rating_template
     *
     * case 4: rating is available and can be shown
     *
     *         will be shown:
     *         container_template (in any case)
     *         -> item_template (in any case for every rate)
     *            as Local_RatingList tag
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

        // if the number of params is more than 1 assume the second param
        // is the name of container template to show
        // Note: be sure the template exists in the ProductDetailedRating
        //       section of the product-detailed-rating-block.ini as well as
        //       in the getTemplateFormat function
        // Note: if you want to specify the template without specifying
        //       the product_id then set the first param to 0
        if (func_num_args() > 1 && func_get_arg(1) != '')
            $this -> _templates['container'] = func_get_arg(1);

        // if the number of params is more than 2 assume the third param
        // is the name of item template to show
        // Note: be sure the template exists in the ProductDetailedRating
        //       section of the product-detailed-rating-block.ini as well as
        //       in the getTemplateFormat function
        if (func_num_args() > 2 && func_get_arg(2) != '')
            $this -> _templates['item'] = func_get_arg(2);

        // if the number of params is more than 3 assume the fourth param
        // is the name of empty template to show
        // Note: be sure the template exists in the ProductDetailedRating
        //       section of the product-detailed-rating-block.ini as well as
        //       in the getTemplateFormat function
        if (func_num_args() > 3 && func_get_arg(3) != '')
            $this -> _templates['empty'] = func_get_arg(3);

        // if the number of params is more than 4 assume the fifth param
        // is the name of add_review template to show
        // Note: be sure the template exists in the ProductDetailedRating
        //       section of the product-detailed-rating-block.ini as well as
        //       in the getTemplateFormat function
        if (func_num_args() > 4 && func_get_arg(4) != '')
            $this -> _templates['add_review'] = func_get_arg(4);

        // if the number of params is more than 5 assume the sixth param
        // is the name of no_rating template to show
        // Note: be sure the template exists in the ProductDetailedRating
        //       section of the product-detailed-rating-block.ini as well as
        //       in the getTemplateFormat function
        if (func_num_args() > 5 && func_get_arg(5) != '')
            $this -> _templates['add_review'] = func_get_arg(5);

        // getting the product data
        $product_data = modApiFunc(
                            'Customer_Reviews',
                            'getBaseProductInfo',
                            array('product_id' => $product_id)
                        );

        // getting the data
        $this -> _data = modApiFunc('Customer_Reviews',
                                    'getDetailedProductRating',
                                    $product_id);

        if (is_array($this -> _data))
            foreach($this -> _data as $k => $v)
            {
                // remove a rate from the list if it has no rating
                if ($v['total_count'] <= 0)
                    unset($data[$k]);
            }

        // getting the store settings
        // if customer reviews are enabled
        $reviews_enable = modApiFunc('Settings', 'getParamValue',
                                     'CUSTOMER_REVIEWS',
                                     'CUSTOMER_REVIEWS_ENABLE');

        // if only registered customers can view the reviews
        $reviews_viewing = modApiFunc('Settings', 'getParamValue',
                                      'CUSTOMER_REVIEWS',
                                      'CUSTOMER_REVIEWS_VIEWING');

        // setting the template engine
        $template_block = $application -> getBlockTemplate('ProductDetailedRating');
        $this -> mTmplFiller -> setTemplate($template_block);

        // checking the settings and the result
        // if the rating is disabled for the product -> show nothing
        // if the reviews are disabled -> show nothing
        // Note: 5 and 7 are pre-filled values for message+rate and rate_only
        if (!in_array($product_data['product_cr'], array(5, 6, 7))
            || $reviews_enable == 'NO')
            return $this -> mTmplFiller -> fill($this -> _templates['empty']);

        // if no rating for the product is found -> show add_review
        // if the customer is anonymous while the reviews are available
        //    for signed in customers -> show add_review
        if (empty($this -> _data)
            || ($reviews_viewing == 'REGONLY'
                && !modApiFunc('Customer_Account', 'getCurrentSignedCustomer')))
            return $this -> outputAddReview($product_id);

        // if rating is disabled for the product while reviews are enabled
        // (customer_reviews = 6) -> show no_rating template
        if (!in_array($product_data['product_cr'], array(5, 7)))
            return $this -> outputNoRating($product_id);

        // registering tags
        $_tags = array(
            'Local_RatingList'      => $this -> outputRating(),
            'Local_ProductInfoLink' => $this -> getProductInfoLink($product_id)
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['container']);
    }

    function getProductInfoLink($prod_id)
    {
        $cid = modApiFunc('CProductListFilter','getCurrentCategoryId');

        $r = new Request();
        $r -> setView('ProductInfo');
        $r -> setAction('SetCurrentProduct');
        $r -> setKey('prod_id', $prod_id);
        $r -> setProductID($prod_id);
        $r -> setCategoryID($cid);

        return $r -> getURL();
    }

    function outputAddReview($product_id)
    {
        global $application;

        $_tags = array(
            'Local_ProductInfoLink' => $this -> getProductInfoLink($product_id)
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['add_review']);
    }

    function outputNoRating($product_id)
    {
        global $application;

        $_tags = array(
            'Local_ProductInfoLink' => $this -> getProductInfoLink($product_id)
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['no_rating']);
    }

    function outputRating()
    {
        global $application;

        $output = '';

        // if the rating is empty or rating is disabled for the product
        if (!is_array($this -> _data) || empty($this -> _data))
            return '';

        foreach($this -> _data as $v)
        {
            $_tags = array(
                'Local_Rate_Label'  => $v['rate_label'],
                'Local_Rate_Rating' => $v['total_rating']
            );
            $this -> _Template_Contents = $_tags;
            $application -> registerAttributes($this -> _Template_Contents);

            $output .= $this -> mTmplFiller -> fill($this -> _templates['item']);
        }

        return $output;
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $mTmplFiller;
    var $_data;
    var $_Template_Contents;
    var $_templates;
    var $NoView;
};

?>