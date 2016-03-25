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

class ProductRating
{
    function ProductRating()
    {
        global $application;

        $this -> mTmplFiller = new TemplateFiller();

        $this -> _templates = array(
            'container'   => 'ProductRatingContainer',
            'empty'       => 'ProductRatingEmpty'
        );

        $this -> NoView = false;
        if ($application -> issetBlockTagFatalErrors('ProductRating'))
            $this -> NoView = true;
    }

    function getTemplateFormat()
    {
        $format = array(
            'layout-file' => 'product-rating-block.ini',
            'files'       => array(
                'ProductRatingContainer'       => TEMPLATE_FILE_SIMPLE,
                'ProductRatingEmpty'           => TEMPLATE_FILE_SIMPLE
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
     *         available local tags: Local_Rating
     *      2: empty_template (use '' to use default)
     *         no local tags available
     * Note: to use custom templates be sure to dedcribe them in
     *       - avactis-system/admin/blocks_ini/product-rating-block.ini
     *       - getTemplateFormat() function of the class
     *
     * The following scheme of templates will be shown
     * case 1: there is no rating for the product
     *         or the rating for the product is disabled
     *         or the rating is hidden from the customer
     *
     *         will be shown:
     *         empty_template (in any case)
     *
     * case 2: rating is available and can be shown
     *
     *         will be shown:
     *         container_template
     */
    function output()
    {
        global $application;
        $product_id = 0;

        if ($this -> NoView)
            return;

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
        // is the name of template to show
        // Note: be sure the template exists in the ProductRating section
        //       of the product-rating-block.ini as well as
        //       in the getTemplateFormat function
        // Note: if you want to specify the template without specifying
        //       the product_id then set the first param to 0
        if (func_num_args() > 1 && func_get_arg(1) != '')
            $this -> _templates['container'] = func_get_arg(1);

        // if the number of params is more than 2 assume the third param
        // is the name of empty template to show
        // Note: be sure the template exists in the ProductRating section
        //       of the product-rating-block.ini as well as
        //       in the getTemplateFormat function
        if (func_num_args() > 2 && func_get_arg(2) != '')
            $this -> _templates['empty'] = func_get_arg(2);

        // template to show
        $template_name = $this -> _templates['container'];

        // getting the data for the product
        $this -> _data = modApiFunc('Customer_Reviews',
                                    'getTotalProductRating',
                                    $product_id);

        // getting the store settings
        // if customer reviews are enabled
        $reviews_enable = modApiFunc('Settings', 'getParamValue',
                                     'CUSTOMER_REVIEWS',
                                     'CUSTOMER_REVIEWS_ENABLE');

        // if only registered customers can view the reviews
        $reviews_viewing = modApiFunc('Settings', 'getParamValue',
                                      'CUSTOMER_REVIEWS',
                                      'CUSTOMER_REVIEWS_VIEWING');

        // checking the settings and the result
        // if no rating for the product is found -> show nothing
        // if the rating is disabled for the product -> show nothing
        // if the reviews are disabled -> show nothing
        // if the customer is anonymous while the reviews are available
        //    for signed in customers -> show nothing
        // Note: 5 and 7 are pre-filled values for message+rate and rate only
        if (!in_array($this -> _data['product_cr'], array(5, 7))
            || $this -> _data['total_count'] <= 0
            || $reviews_enable == 'NO'
            || ($reviews_viewing == 'REGONLY'
                && !modApiFunc('Customer_Account', 'getCurrentSignedCustomer')))
            $template_name = $this -> _templates['empty'];

        // registering tags
        $_tags = array(
            'Local_Rating' => $this -> _data['total_rating']
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        $template_block = $application -> getBlockTemplate('ProductRating');
        $this -> mTmplFiller -> setTemplate($template_block);

        return $this -> mTmplFiller -> fill($template_name);
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