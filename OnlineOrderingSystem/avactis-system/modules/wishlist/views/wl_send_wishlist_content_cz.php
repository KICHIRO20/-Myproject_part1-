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
 * @package WishList
 * @author Sergey E. Kulitsky
 *
 */

class SendWishlistContent
{
    function SendWishlistContent()
    {
        global $application;

        $this -> mTmplFiller = new TemplateFiller();
        $this -> _templates = array(
            'container' => 'SendWishlistContentContainer',
            'item'      => 'SendWishlistContentItem'
        );

        $this -> NoView = false;
        if ($application -> issetBlockTagFatalErrors('SendWishlistContent'))
            $this -> NoView = true;
    }

    function getTemplateFormat()
    {
        $format = array(
            'layout-file' => 'wl-send-wishlist-content-block.ini',
            'files'       => array(
                'SendWishlistContentContainer' => TEMPLATE_FILE_SIMPLE,
                'SendWishlistContentItem' => TEMPLATE_FILE_SIMPLE
            ),
            'options'     => array(
            )
        );
        return $format;
    }

    function getOptionValue($cdata, $odata)
    {
        if (is_numeric($cdata))
        {
            foreach(array_keys($odata['values']) as $i)
                if ($odata['values'][$i]['value_id'] == $cdata)
                    return $odata['values'][$i]['value_name'];

            if ($cdata == 0)
                return $odata['discard_value'];
        }
        elseif (is_array($cdata) and !isset($cdata['val']))
        {
            $output_names = array();
            foreach(array_keys($odata['values']) as $i)
                if (array_key_exists($odata['values'][$i]['value_id'], $cdata))
                    $output_names[] = $odata['values'][$i]['value_name'];

            return implode(', ', $output_names);
        }
        elseif (is_array($cdata) and isset($cdata['val']))
        {
            if (isset($cdata['is_file']))
                return basename($cdata['val']);
            else
                return $cdata['val'];
        }

        return '';
    }

    /**
     * The output of the Viewer
     */
    function output()
    {
        global $application;

        if ($this -> NoView)
            return '';

        // wishlist is available only for signed in customers
        // so showing nothing for anonymous ones
        if (!modApiFunc('Customer_Account', 'getCurrentSignedCustomer'))
            return '';

        // setting up the template engine
        $template_block = $application -> getBlockTemplate('SendWishlistContent');
        $this -> mTmplFiller -> setTemplate($template_block);

        $wc = modApiFunc('Wishlist', 'getWishlistContent');
        $this -> WC = array();

        loadClass('CCustomerInfo');
        $this -> CI = new CCustomerInfo(modApiFunc('Customer_Account',
                                                   'getCurrentSignedCustomer'));

        foreach($wc as $productInfo)
        {
            $Info = array();
            $Info['Local_ProductLargeImageSrc'] = $productInfo['LargeImageSrc'];
            $Info['Local_ProductName'] = $productInfo['Name'];
            $Info['Local_ProductSmallImageSrc'] = $productInfo['SmallImageSrc'];
            $Info['Local_ProductSmallImageWidth'] = $productInfo['SmallImageWidth'];
            $Info['Local_ProductInfoLink'] = $productInfo['InfoLink'];
            $Info['Local_WLID'] = $productInfo['wl_id'];
            $Info['Local_customerID'] = $productInfo['customer_id'];

            $request = new Request();
            $request -> setView('ProductInfo');
            $request -> setAction('SetCurrentProduct');
            $request -> setKey('prod_id', $productInfo['ID']);
            $request -> setKey('presetCombination', 'wl_' . $productInfo['customer_id'] . '_' . $productInfo['wl_id']);
            $Info['Local_ProductOrderLink'] = $request -> getURL('', true);

            $Info['Local_WLItemSalePrice'] = modApiFunc("Localization", "currency_format", $productInfo['CartItemSalePrice']);
            $Info['Local_WLItemWeight'] = modApiFunc("Localization", "format", $productInfo['CartItemWeight'],"weight")." ".modApiFunc("Localization","getUnitTypeValue","weight");
            $Info['Local_WLItemPerItemShippingCost'] = modApiFunc("Localization", "currency_format", $productInfo['CartItemPerItemShippingCost']);
            $Info['Local_WLItemPerItemHandlingCost'] = modApiFunc("Localization", "currency_format", $productInfo['CartItemPerItemHandlingCost']);

            $unit_values = $productInfo['attributes']['SalePrice']['unit_type_values'];
            $unit_id = $productInfo['attributes']['SalePrice']['unit_type_value'];
            $Info['Local_ProductSubtotal'] = modApiFunc("Localization", "currency_format", $productInfo['Total']);

            $Info['Local_ProductQuantity'] = $productInfo['Quantity_In_Cart'];

            $Info['ImageSize'] = 'width="' . $productInfo['SmallImageWidth'] . '" height="' . $productInfo['SmallImageHeight'] . '"';

            $option_text = '';
            foreach(array_keys($productInfo['Options']) as $oid)
            {
                $odata = modApiFunc('Product_Options', 'getOptionInfo',
                                    $oid, true);
                $option_text .= $odata['option_name'] .
                                getMsg('CZ', 'PRODUCT_OPTIONS_COLON') . ' ' .
                                $this -> getOptionValue(
                                    $productInfo['Options'][$oid],
                                    $odata
                                ) . "\n";
            }
            $Info['Local_ProductOptionsSelected'] = $option_text;

            $Info['Local_ProductQuantityOptions'] = modApiFunc('Cart', 'getProductQuantityOptions', $productInfo['Quantity_In_Cart'], $productInfo['ID'], true, true);

            $this -> WC[] = $Info;
        }

        $_tags = array(
            'Local_PersonName' => $this -> CI -> getPersonInfo('FirstName',
                                                               'customer') .
                                  ' ' .
                                  $this -> CI -> getPersonInfo('LastName',
                                                               'customer') .
                                  ' (' .
                                  $this -> CI -> getPersonInfo('Email',
                                                               'customer') .
                                  ')',
            'Local_Items'      => $this -> outputItems()
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['container']);
    }

    function outputItems()
    {
        global $application;
        $output = '';

        foreach($this -> WC as $i => $wc) {
            $_tags = array(
                'Local_Index'            => ($i + 1),
                'Local_ProductName'      => $wc['Local_ProductName'],
                'Local_WLItemSalePrice'  => $wc['Local_WLItemSalePrice'],
                'Local_ProductQuantity'  => $wc['Local_ProductQuantity'],
                'Local_ProductSubtotal'  => $wc['Local_ProductSubtotal'],
                'Local_ProductOrderLink' => $wc['Local_ProductOrderLink'],
                'Local_ProductOptions'   => $wc['Local_ProductOptionsSelected']
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

    var $NoView;
    var $mTmplFiller;
    var $_Template_Contents;
    var $WC;
    var $CI;
};

?>