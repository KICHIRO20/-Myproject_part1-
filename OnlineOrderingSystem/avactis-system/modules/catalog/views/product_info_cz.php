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

loadModuleFile('catalog/abstract/product_info.php');
loadCoreFile('JSON.php');

/**
 * Catalog->ProductInfo View.
 * Views product info for the CustomerZone.
 *
 * @package Catalog
 * @access  public
 * @author Alexey Kolesnikov
 */
class ProductInfo extends Catalog_ProdInfo_Base
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
    	    'layout-file'        => 'product-info-config.ini'
    	   ,'files' => array(
    	        'Item'           => TEMPLATE_FILE_PRODUCT_TYPE
               ,'ItemOutOfStock' => TEMPLATE_FILE_PRODUCT_TYPE
    	    )
    	   ,'options' => array(
    	    )
    	);
    	return $format;
    }

    function ProductInfo()
    {
        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("ProductInfo"))
        {
            $this->NoView = true;
        }

    }

    /**
     * Returns the product info.
     *
     * @return string.
     */
    function output()
    {
        global $application;

        #Define whether to output the view or not
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "ProductInfo", "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, "ProductInfo", "Warnings");
        }


        $catalog = &$application->getInstance('Catalog');
        $this->prod_id = @func_get_arg(0);
        if ($this->prod_id === false)
        {
            $this->prod_id = $catalog->getCurrentProductID();
        }

        if (!$this->prod_id || $catalog->getBaseProductInfo($this->prod_id)==null)
        {
            $request = new Request();
            $request -> setView('Index');
            $application -> redirect($request);
            return;
        }
        else
        {
            $product_object = new CProductInfo($this->prod_id);
            $ms_vis = $product_object->getProductTagValue('MembershipVisibility');
            $cur_group_id = modApiFunc('Customer_Account','getCurrentSignedCustomerGroupID');
            if($product_object->getProductTagValue('Available') == 'Offline'
                || ($ms_vis != '-1' && !in_array($cur_group_id, explode('|', $ms_vis))))
            {
                $request = new Request();
                $request -> setView('Index');
                $application -> redirect($request);
                return;
            }
        }

        /* Determining the IDs of the previous and next product, relively to the current one */

        $this->next_product_id = 0;
        $this->previous_product_id = 0;

        $product_ids = $catalog->getProductListByGlobalFilter( PAGINATOR_DISABLE ); // in the order set in storefront
        $products_count = count( $product_ids );

        for( $p = 0; $p < $products_count; $p++ )
        {
            if( $product_ids[ $p ]['product_id'] == $this->prod_id )
            {
                if( $p-1 >= 0 )                 $this->previous_product_id  = $product_ids[ $p-1 ]['product_id'];
                if( $p+1 < $products_count )    $this->next_product_id      = $product_ids[ $p+1 ]['product_id'];
                break;
            }
        }

        modApiFunc("tag_param_stack", "push", __CLASS__, array(array("key" => TAG_PARAM_PROD_ID, "value" => $this->prod_id)));

        # Get current selected product info.
        $product_info = modApiFunc('Catalog', 'getProductInfo', $this->prod_id);

        # save info
        $this->_ProductInfo = $product_info;

        #
        $_template_tags = array(
                    'Local_HiddenFields'=>''
                   ,'Local_ProductID' => ''
                   ,'Local_JSfuncProductFormSubmit'=>''
                   ,'Local_ProductStockWarnings' => ''
                   ,'ProductOptionsForm'=>''
                   ,'Local_ProductFormStart' => ''
                   ,'Local_ProductFormEnd' => ''
                   ,'Local_ProductAddToCart' => ''
                   ,'Local_FormQuantityFieldName' => ''
                   ,'Local_ProductQuantityOptions' => ''
                   ,'RelatedProducts' => '' //                        Related
                   ,'NextProductID' => ''
                   ,'PreviousProductID' => ''
                   ,'Local_ProductSettingsJSON' => ''
                   ,'Local_ThumbnailSide' => ''
                   ,'ProductColorSwatchImages' => ''
                  );



        $application->registerAttributes($_template_tags,'ProductInfo');
		$templateFiller = new TemplateFiller();
        # define the template for the given view.
        $template = $application->getBlockTemplate('ProductInfo');
        $templateFiller->setTemplate($template);
        $this->_product_form_action = getpageurl('CartContent');


        $obj_product = new CProductInfo($this->prod_id);
        $stock_method = $obj_product->whichStockControlMethod();
        //             OutOfStock -                             .
        //                                   OutOfStock -                      .
        $store_show_absent = modApiFunc("Configuration", "getValue", "store_show_absent");
        if ($stock_method == PRODUCT_OPTIONS_INVENTORY_TRACKING)
        {
            $qty_in_stock = modApiFunc('Product_Options','getQuantityInStockByInventoryTable','product', $this->prod_id, true);
        }
        else
        {
            $qty_in_stock = $this->_ProductInfo['attributes']['QuantityInStock']['value'];
        }

        $low_stock_level = $this->_ProductInfo['attributes']['LowStockLevel']['value'];

        $b_out_of_stock = ($qty_in_stock !== "" && $qty_in_stock <1);

        switch($store_show_absent)
        {
            case STORE_SHOW_ABSENT_SHOW_BUY:
                //                                  ,                     -
                //
                $template = "Item";
                break;
            case STORE_SHOW_ABSENT_SHOW_NOT_BUY:
                //                        ,
                //            LowStock -            Low Stock! Buy Now!
                if($b_out_of_stock === true)
                {
                    $template = "ItemOutOfStock";
                }
                else
                {
                    $template = "Item";
                }
                break;
            case STORE_SHOW_ABSENT_NOT_SHOW_NOT_BUY:
                //                           ,
                if($b_out_of_stock === true)
                {
                    $request = new Request();
                    $request -> setView('Index');
                    $application -> redirect($request);
                    return "";
                }
                else
                {
                    $template = "Item";
                }
                break;
            default:
                //STORE_SHOW_ABSENT_SHOW_BUY
                $template = "Item";
                break;
        }

        $result = $templateFiller->fill($template, $product_info['TypeID']);
        modApiFunc("tag_param_stack", "pop", __CLASS__);
        modApiFunc('EventsManager','throwEvent', 'ProductInfoDisplayed', $this->prod_id);
        return $result;
    }

    /**
     * Processes tags in the templates for the given view.
     *
     * @return string tag value, if tag has been processed. NULL, otherwise.
     */
    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case 'Local_ProductID':
                $value = $this->_ProductInfo['ID'];
                break;

            case 'Local_JSfuncProductFormSubmit':
                $value = "<script type=\"text/javascript\">".
                         "function ProductFormSubmit_".$this->_ProductInfo['ID']."()".
                         "{".
                         " document.forms['ProductForm_".$this->_ProductInfo['ID']."'].submit();".
                         "};".
                         "</script>";
                break;

            case 'Local_ProductStockWarnings':
		        if(!modApiFunc('Session','is_set','StockDiscardedBy'))
		        {
		            $value = '';
		        }
		        else
		        {
		            $stock_discarded_by = modApiFunc('Session','get','StockDiscardedBy');
		            modApiFunc('Session','un_set','StockDiscardedBy');
		            $value = $stock_discarded_by;//cz_getMsg($stock_discarded_by);
		        }
                break;


            case 'Local_ProductFormStart':
                $skin = modApiFunc('Look_Feel', 'getCurrentSkin');
             	if(($skin == 'digiCenter') || ($skin == 'foodCourt') || ($skin == 'flowers'))
             	{
             		if(empty($this->_product_form_action))
                    $this->_product_form_action = getpageurl('CartContent');
                $redirect = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_SHOW_CART);
                $script = (($redirect) ? $this->_product_form_action : $_SERVER["PHP_SELF"]);
                $value = '<form action="' . $script . '" name="ProductForm_'.$this->_ProductInfo['ID'].'" id="product_addtocart_form" method="post" enctype="multipart/form-data">
                          ';
             	}
             	else
             	{
                if(empty($this->_product_form_action))
                    $this->_product_form_action = getpageurl('CartContent');
                $redirect = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_SHOW_CART);
                $script = (($redirect) ? $this->_product_form_action : $_SERVER["PHP_SELF"]);
                $value = '<form action="' . $script . '" name="ProductForm_'.$this->_ProductInfo['ID'].'" id="ProductForm_'.$this->_ProductInfo['ID'].'" method="post" enctype="multipart/form-data">
                          <input type="hidden" name="asc_action" value="AddToCart" />
                          <input type="hidden" name="prod_id" value="' . $this->_ProductInfo['ID'] . '" />
                        <script type="text/javascript">
                        function ProductFormSubmit_'.$this->_ProductInfo['ID'].'()
                        {
                            document.forms[\'ProductForm_'.$this->_ProductInfo['ID'].'\'].submit();
                        };
                        </script>';
             	}
                break;

            case 'Local_ProductFormEnd':
                $value = '</form>';
                break;

            case 'Local_ProductAddToCart':
                $value = 'javascript: ProductFormSubmit_'.$this->_ProductInfo['ID'].'();';
                break;

            case 'ProductOptionsForm':
                $value = getOptionsChoice($this->_ProductInfo['ID']);
                break;

            case 'Local_FormQuantityFieldName':
                $value = 'quantity_in_cart';
                break;

            case 'NextProductID':
                $value = $this->next_product_id;
                break;

            case 'PreviousProductID':
                $value = $this->previous_product_id;
                break;

            case 'Local_ProductQuantityOptions':
                $force_quantity = false;
                if (modApiFunc('Request', 'getValueByKey',
                               'presetCombination') != '')
                {
                    list($type, $customer_id, $wl_id) = explode(
                        '_',
                        modApiFunc('Request', 'getValueByKey',
                        'presetCombination')
                    );
                    if ($type == 'wl')
                    {
                        $wl_info = modApiFunc('Wishlist',
                                              'getWishlistRecordCartData',
                                              $wl_id, $customer_id);
                        if ($wl_info
                            && @$wl_info['parent_entity'] == 'product'
                            && @$wl_info['entity_id'] == $this -> _ProductInfo['ID'])
                        {
                            $qty_in_cart = $wl_info['qty'];
                            $force_quantity = true;
                        }
                    }
                }
                else
                {
                    $qty_in_cart = modApiFunc("Cart", "getProductQuantity", $this->_ProductInfo['ID']);
                }
                $value = modApiFunc("Cart", "getProductQuantityOptions",
                                    $qty_in_cart, $this->_ProductInfo['ID'],
                                    false, false, $force_quantity);
                break;

            case 'Local_ProductSettingsJSON':
                $product_object = new CProductInfo($this->_ProductInfo['ID']);
                $options_settings = modApiFunc('Product_Options', 'getOptionsSettingsForEntity', 'product', $this->_ProductInfo['ID']);
                $settings = array(
                    'product_id' => (int) $this->_ProductInfo['ID'],
                    'sale_price' => (float) getValProductSalePrice(),
                    'list_price' => (float) getValProductListPrice(),
                    'currency_settings' => modApiFunc('Localization', 'getCurrencySettings'),
                    'aanic' => $options_settings['AANIC'],
                    'aanis' => $options_settings['AANIS'],
                    'labels' => array(
                        'comb_unavailable' => $options_settings['WRN_CI_CR'], // getLabel('OPT_COMB_UNAVAILABLE')
                        'inv_unavailable' => $options_settings['WRN_CI_INV'], // getLabel('OPT_COMB_UNAVAILABLE')
                        'comb_limit_stock' => $options_settings['WRN_CI_INV'], // getLabel('OPT_COMB_LIMIT_STOCK')
                        'comb_out_of_stock' => $options_settings['WRN_CI_INV'], // getLabel('OPT_COMB_OUT_OF_STOCK')
                    ),
                );

                $json = new Services_JSON();
                $value = $json->encode($settings);
                break;

            case 'Local_ThumbnailSide':
                $pi_settings = modApiFunc('Product_Images','getSettings');
                $value = $pi_settings['MAIN_IMAGE_SIDE'];
                break;

			case 'ProductColorSwatchImages':
               	$value = getColorSwatchImages($this->_ProductInfo['ID']);
               	break;

    	    default:
   	        list($entity, $tag) = getTagName($tag);
        	    if ($entity == 'product')
        	    {
        	        //$value = getKeyIgnoreCase($tag, $this->_ProductInfo);

                    //               ,               default               switch' .
                    //           ,               $this->_ProductInfo                                          (            ),
                    //                                   getValProduct*,                ,
                    //                .                                             .
                    $product_object = new CProductInfo($this->_ProductInfo['ID']);
                    $value = $product_object->getProductTagValue($tag);

        	    }

        		break;
        }

        return $value;
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Current produst info.
     *
     * @var array
     */
    var $_ProductInfo;
    var $previous_product_id;
    var $next_product_id;
    var $_product_form_action = null;

    /**#@-*/
}
?>