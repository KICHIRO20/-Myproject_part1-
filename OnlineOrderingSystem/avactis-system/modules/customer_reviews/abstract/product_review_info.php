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
loadModuleFile('customer_reviews/customer_reviews_api.php');

/**
 * @package Customer Reviews
 * @author Sergey E. Kulitsky
 */

class CProductReviewInfo
{
    function CProductReviewInfo($pid)
    {
        $this -> _productid_invalid = false;

        if ($pid == null || !is_numeric($pid)
            || !modApiFunc('Catalog', 'isCorrectProductId', $pid))
        {
            $this -> _productid_invalid = true;
        }
        $this -> _productid = $pid;

        $this -> _settings['enable'] = modApiFunc('Settings', 'getParamValue',
                                                  'CUSTOMER_REVIEWS',
                                                  'CUSTOMER_REVIEWS_ENABLE');
        $this -> _settings['viewing'] = modApiFunc('Settings', 'getParamValue',
                                                   'CUSTOMER_REVIEWS',
                                                   'CUSTOMER_REVIEWS_VIEWING');
        $this -> _product_data = modApiFunc('Customer_Reviews',
                                            'getBaseProductInfo',
                                            array('product_id' => $pid));

        $this -> _signin_customer = modApiFunc('Customer_Account',
                                               'getCurrentSignedCustomer');
    }

    /**
     * Returns Review info tag value
     */
    function getReviewTagValue($tag)
    {
        $output = '';

        // if productid is incorrect returns '' in any case
        if ($this -> _productid_invalid)
            return '';

        switch($tag)
        {
            case 'number':
                $output = modApiFunc('Customer_Reviews',
                                     'getProductCustomerReviewNumber',
                                     $this -> _productid);
                if (!$this -> checkReviewAvail(6, 7))
                    $output = 0;
                break;

            case 'averagerating':
                $output = modApiFunc('Customer_Reviews',
                                     'getTotalProductRating',
                                     $this -> _productid);

                $output = $output['total_rating'];

                if (!$this -> checkReviewAvail(7))
                    $output = 0;

                break;

            case 'link':
                $cid = modApiFunc('CProductListFilter','getCurrentCategoryId');

                $r = new Request();
                $r -> setView('ProductInfo');
                $r -> setAction('SetCurrentProduct');
                $r -> setKey('prod_id', $this -> _productid);
                $r -> setProductID($this -> _productid);
                $r -> setCategoryID($cid);
                $r -> setAnchor('customer_reviews_' . $this -> _productid);
                return $r -> getURL();
        }

        return $output;
    }

    /**
     * Checks if customer reviews is available for the product
     * @param status1 and status2 indicates if we want to check review or rate
     *        for review status1 = 6, status2 = -1
     *        for rate status1 = 7, status2 = -1 should be 7
     *        for either review or rate status1 = 6, status2 = 7
     *        for both review and rates status should be -1
     */
    function checkReviewAvail($status1 = -1, $status2 = -1)
    {
        if (!in_array($this -> _product_data['product_cr'], array(5, $status1, $status2))
            || $this -> _settings['enable'] == 'NO'
            || ($this -> _settings['viewing'] == 'REGONLY'
                && !$this -> _signin_customer))
            return false;

        return true;
    }

    var $_productid;
    var $_product_data;
    var $_productid_invalid;
    var $_settings;
    var $_signin_customer;
}
?>