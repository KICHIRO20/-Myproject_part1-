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
 * @package Customer Reviews
 * @author Sergey E. Kulitsky
 *
 */
$moduleInfo = array
    (
        'name'          => 'Customer_Reviews',
        'shortName'     => 'CR',
        'groups'        => 'Main',
        'description'   => 'Customer Reviews module',
        'version'       => '0.1.47700',
        'author'        => 'Sergey E. Kulitsky',
        'contact'       => '',
        'systemModule'  => false,
        'mainFile'      => 'customer_reviews_api.php',
        'constantsFile' => 'const.php',
        'resFile'       => 'customer-reviews-messages',
        'extraAPIFiles' => array(
            'CProductReviewInfo' => 'abstract/product_review_info.php'
        ),
        'actions' => array(
            'AdminZone' => array(
                'update_cr_rates_settings' => 'update_cr_rates_settings.php',
                'update_customer_reviews'  => 'update_customer_reviews.php',
                'update_review_data'       => 'update_review_data.php'
            ),
            'CustomerZone' => array(
                'post_review'      => 'post_review_cz.php',
                'sign_in_required' => 'sign_in_required_cz.php'
            )
        ),
        'views' => array(
            'AdminZone' => array(
                  'CR_Rates_Settings'     => 'cr_rates_settings_az.php',
                  'ManageCustomerReviews' => 'cr_manage_customer_reviews_az.php',
                  'CR_Review_Data'        => 'cr_review_data_az.php',
                  'CR_Select_Product'     => 'cr_select_product_az.php'
            ),
            'CustomerZone' => array(
                  'ProductRating'         => 'cr_product_rating_cz.php',
                  'ProductDetailedRating' => 'cr_product_detailed_rating_cz.php',
                  'ProductReviewList'     => 'cr_product_review_list_cz.php',
		          'ProductAddReviewForm'  => 'cr_product_add_review_form_cz.php',
                  'ProductAddReviewLink'  => 'cr_product_add_review_link_cz.php'
            ),
            'Aliases' => array(
                'ProductLastReviews'            => 'ProductReviewList',
                'ProductShortReviewInfo'        => 'ProductDetailedRating',
                'ProductAddReviewFormDropdown'  => 'ProductAddReviewForm'
            )
        )
    );

?>