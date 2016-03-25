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
 * Wishlist_Content viewer
 *
 * @package WishList
 * @author Sergey Kulitsky
 */
class WishlistContent
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Gets a template format for the given view.
     */
    function getTemplateFormat()
    {
    	$format = array(
    	    'layout-file'        => 'wishlist-content-config.ini'
    	   ,'files' => array(
    	        'Container'      => TEMPLATE_FILE_SIMPLE
    	       ,'ContainerEmpty' => TEMPLATE_FILE_SIMPLE
    	       ,'Item'           => TEMPLATE_FILE_PRODUCT_TYPE
    	    )
    	   ,'options' => array(
    	        'Columns'        => TEMPLATE_OPTION_REQUIRED
    	    )
    	);
    	return $format;
    }

    /**
     * WishlistContent constructor.
     */
    function WishlistContent()
    {
        global $application;

        if(modApiFunc('Session', 'is_Set', 'WishlistResultMessage'))
        {
            $this -> ResultMessage = modApiFunc('Session', 'get',
                                                'WishlistResultMessage');
            modApiFunc('Session', 'un_set', 'WishlistResultMessage');
        }
        else
        {
            $this -> ResultMessage = NULL;
        }
        if (modApiFunc('Session', 'is_Set', 'WishlistErrorMessage'))
        {
            $this -> ErrorMessage = modApiFunc('Session', 'get',
                                               'WishlistErrorMessage');
            modApiFunc('Session', 'un_set', 'WishlistErrorMessage');
        }
        else
        {
            $this -> ErrorMessage = '';
        }

        #check if block level fatal errors exist
        $this -> NoView = false;
        if ($application -> issetBlockTagFatalErrors('WishlistContent'))
            $this->NoView = true;

        $this -> templateFiller = new TemplateFiller();
        $this -> template = $application -> getBlockTemplate('WishlistContent');
        $this -> templateFiller -> setTemplate($this -> template);
    }

    /**
     * Returns the wishlist products
     */
    function getWishlistContent()
    {
        global $application;
        $wc = modApiFunc('Wishlist', 'getWishlistContent');

        $disable_trtd = $application -> getAppIni('PRODUCT_LIST_DISABLE_TR_TD');
        if ($disable_trtd != null && strtolower($disable_trtd) === 'yes')
            $disable_trtd = true;
        else
            $disable_trtd = false;

        $items = "";
        $col = 1;
        $columns = intval($application -> getBlockOption($this->template,
                                                         'Columns'));
        $this -> _Item = null;
        $i = 0;
        foreach($wc as $productInfo)
        {
            $Info = array();
            $Info['Local_ProductLargeImageSrc'] = $productInfo['LargeImageSrc'];
            $Info['Local_ProductName'] = $productInfo['Name'];
            $Info['Local_ProductSmallImageSrc'] = $productInfo['SmallImageSrc'];
            $Info['Local_ProductSmallImageWidth'] = $productInfo['SmallImageWidth'];
            $Info['Local_ProductLargeImageWidth'] = $productInfo['LargeImageWidth'];
            $Info['Local_ProductInfoLink'] = $productInfo['InfoLink'];

            $request = new Request();
            $request -> setView('Wishlist');
            $request -> setAction('RemoveProductFromWishlist');
            $request -> setKey('wl_id', $productInfo['wl_id']);
            $Info['Local_RemoveProductLink'] = $request -> getURL();

            $request = new Request();
            $request -> setView('Wishlist');
            $Info['Local_FormAction'] = $request -> getURL();

            $request = new Request();
            $request -> setView('CartContent');
            $request -> setAction('AddToCart');
            $request -> setKey('wl_id', $productInfo['wl_id']);
            $Info['Local_AddToCartLink'] = $request -> getURL();

            $Info['Local_FormId'] = "Product_Quan_" . $productInfo['ID'];
            $Info['Local_FormActionFieldName'] = 'asc_action';
            $Info['Local_FormActionFieldValue'] = 'UpdateWishlist';
            $Info['Local_FormWLIdArrayFieldName'] = 'wl_id[' . $i . ']';
            $Info['Local_WLID'] = $productInfo['wl_id'];

            $Info['Local_WLItemSalePrice']=modApiFunc("Localization", "currency_format", $productInfo['CartItemSalePrice']);
            $Info['Local_WLItemWeight']=modApiFunc("Localization", "format", $productInfo['CartItemWeight'],"weight")." ".modApiFunc("Localization","getUnitTypeValue","weight");
            $Info['Local_WLItemPerItemShippingCost']=modApiFunc("Localization", "currency_format", $productInfo['CartItemPerItemShippingCost']);
            $Info['Local_WLItemPerItemHandlingCost']=modApiFunc("Localization", "currency_format", $productInfo['CartItemPerItemHandlingCost']);

            $unit_values = $productInfo['attributes']['SalePrice']['unit_type_values'];
            $unit_id = $productInfo['attributes']['SalePrice']['unit_type_value'];
            $Info['Local_ProductSubtotal'] = modApiFunc("Localization", "currency_format", $productInfo['Total']);
            $Info['Local_FormQuantityFieldName']      = 'quantity['. $productInfo['wl_id'] .']';

            $Info['Local_ProductQuantity'] = $productInfo['Quantity_In_Cart'];

            $Info['ImageSize'] = 'width="'.$productInfo['SmallImageWidth'].'" height="'.$productInfo['SmallImageHeight'].'"';

            $Info['Local_ProductOptionsSelected'] = getOptionsCombination($productInfo['Options']);
            $Info['Local_ProductQuantityOptions'] = modApiFunc('Cart', 'getProductQuantityOptions', $productInfo['Quantity_In_Cart'], $productInfo['ID'], true, true);

            if ($col == 1)
            {
                if ($disable_trtd == false) $items .= '<tr><td>';
                $col++;
            }
            else
            {
                if ($disable_trtd == false)  $items .= '<td>';
                $col++;
            }
            if ($col > $columns)
            {
                $col = 1;
            }
            $this -> _Template_Contents = $Info;
            $application -> registerAttributes($this -> _Template_Contents);
            modApiFunc("tag_param_stack", "push", __CLASS__, array(array('key' => 'prod_id', 'value' => $productInfo['ID'])));
            $items .= $this -> templateFiller -> fill('Item', $productInfo['TypeID']);
            modApiFunc("tag_param_stack", "pop", __CLASS__);
            $i++;
        }
        return $items;
    }

    /**
     * Outputs the wishlist content view.
     *
     * @todo $request->setView  ( '' ) - define the view name
     */
    function output()
    {
        global $application;

        if (!modApiFunc('Customer_Account', 'getCurrentSignedCustomer'))
        {
            $request = new Request();
            $request -> setView('CustomerAccountHome');
            $application -> redirect($request);
        }

        #Define whether to output the view or not
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "WishlistContent", "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, "WishlistContent", "Warnings");
        }

        $wc = modApiFunc('Wishlist', 'getWishlistContent');

        $_tags = array(
            'Local_ClearLink' => $this -> getClearLink(),
            'Local_FormAction' => $this -> getFormAction(),
            'Local_Items' => $this -> getWishlistContent(),
            'Local_WishlistResultMessage' => $this -> ResultMessage,
            'Local_WishlistErrorMessage' => $this -> ErrorMessage
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        $unit_values = '';
        $unit_id = '';
        if (empty($wc))
        {
            $retval = $this -> templateFiller -> fill("ContainerEmpty");
        }
        else
        {
            $retval = $this -> templateFiller -> fill("Container");
        }
        return $retval;
    }

    function getClearLink()
    {
        $request = new Request();
        $request -> setView('Wishlist');
        $request -> setAction('ClearWishlist');
        return $request->getURL();
    }

    function getFormAction()
    {
        $request = new Request();
        $request -> setView('Wishlist');
        return $request->getURL();
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * A reference to the TemplateFiller object.
     *
     * @var TemplateFiller
     */
    var $templateFiller;

    /**
     * The current specified template.
     *
     * @var array
     */
    var $template;

    var $_Template_Contents;

    /**#@-*/

}
?>