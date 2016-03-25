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
 * Catalog module.
 * ProductSet tag - display custom product set.
 *
 * @author Alexey Florinsky
 * @package Catalog
 * @access  public
 */
class ProductSet
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * The view constructor.
     *
     */
    function ProductSet()
    {
    }

    /**
     * Generates the main page containing a product list.
     */
    function renderProductInfo($product_id_to_display)
    {
        //             OutOfStock -                             .
        //                                   OutOfStock -                      .
        $store_show_absent = modApiFunc("Configuration", "getValue", "store_show_absent");

        global $application;

        $this->_Product_Info = new CProductInfo($product_id_to_display);
        modApiFunc("tag_param_stack", "push", __CLASS__, array(array("key" => TAG_PARAM_PROD_ID, "value" => $product_id_to_display)));

        $low_stock_level = $this->_Product_Info->getProductTagValue('LowStockLevel', PRODUCTINFO_NOT_LOCALIZED_DATA);

        $stock_method = $this->_Product_Info->whichStockControlMethod();

        if ($stock_method == PRODUCT_OPTIONS_INVENTORY_TRACKING)
        {
            $qty_in_stock = modApiFunc('Product_Options','getQuantityInStockByInventoryTable','product', $product_id_to_display, true);
        }
        else
        {
            $qty_in_stock = $this->_Product_Info->getProductTagValue('QuantityInStock', PRODUCTINFO_NOT_LOCALIZED_DATA);
        }
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
            default:
                //STORE_SHOW_ABSENT_SHOW_BUY
                $template = "Item";
                break;
        }
        $items = $this->__tmpl_filler->fill('', $this->__tag_settings->template[$template], array(), true);
        modApiFunc("tag_param_stack", "pop", __CLASS__);

        #modApiFunc('EventsManager','throwEvent', 'ProductInfoDisplayed', $product_id_to_display);
        return $items;
    }

    function getProductListToDisplay()
    {
        $pl = modApiFunc('Catalog', 'getProductListByFilter', $this->__tag_settings->filter, RETURN_AS_ID_LIST);
        return $pl;
    }

    function registerAttributes()
    {
    	global $application;
        $application->registerAttributes(array(
                                'ProductOptionsForm'=> ''
                               ,'Local_HiddenFields' => ''
                               ,'Local_JSfuncProductFormSubmit' => ''
                               ,'Local_ProductFormStart' => ''
                               ,'Local_ProductFormEnd' => ''
                               ,'Local_ProductAddToCart' => ''
                               ,'ProductDetailedImages' => ''
                               ,'Local_FormQuantityFieldName' => ''
                               ,'Local_ProductQuantityOptions' => ''
                               ,'Local_ProductNumberToDisplay' => ''
                               ,'Local_ProductIdToDisplay' => ''
                               ,'Local_ProductDetails' => ''
                               ,'Local_CurrentProductIdToDisplay' => ''
                               ,'Local_Columns' => ''
                              ));
    }

    /**
     * Returns the Product Listing view.
     *
     * @return string the Products List view.
     */
    function output()
    {
        global $application;

        $this->__index=0;

        //                                           .
        $in_param = @func_get_arg(0);

        //                        -                        CProductSetTagSettings,
        if (_is_a($in_param, 'CProductSetTagSettings'))
        {
            $this->__tag_settings = $in_param;
        }
        else
        {
            //                            CProductSetTagSettings
            $this->__tag_settings = new CProductSetTagSettings();
        }

        //                                         hash
        $this->__filter_hash = $this->__tag_settings->filter->category_id;
        $this->__paginator_name = "Catalog_ProductSet_".$this->__filter_hash;
        modAPIFunc('paginator', 'setCurrentPaginatorName', $this->__paginator_name);

        //                    -      (         )
        $this->registerAttributes();

        //
        $this->__tmpl_filler = new TmplFiller($this->__tag_settings->template['Directory']);
        $cols = @func_get_arg(1);
        if ($cols !== false && is_numeric($cols))
        {
            $this->columns = $cols;
        }
        else
        {
            $this->columns = 3;
        }

        //
        $this->__product_list_to_display = $this->getProductListToDisplay();

        if (NULL == $this->__product_list_to_display || empty($this->__product_list_to_display))
        {
            $retval = $this->__tmpl_filler->fill("", $this->__tag_settings->template['ContainerEmpty'], array(), true);
        }
        else
        {
            $retval = $this->__tmpl_filler->fill("", $this->__tag_settings->template['Container'], array(), true);
        }
        return $retval;
    }

    /**
     * Processes tags in the templates for the given view.
     *
     * @return string tag value, if tag has been processed. NULL, otherwise.
     */
    function getTag($tag, $arg_list = array())
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case 'Local_Columns':
                $value = $this->columns;
                break;

            case 'Local_ProductNumberToDisplay':
                $value = count($this->__product_list_to_display);
                break;

            case 'Local_NextProductIdToDisplay':
                if ($this->__index >= count($this->__product_list_to_display))
                {
                    $value = null;
                }
                else
                {
                    $value = $this->__product_list_to_display[$this->__index]['product_id'];
                    $this->__index++;
                }
                $this->__current_product_id_to_display = $value;
                break;

            case 'Local_CurrentProductIdToDisplay':
                return $this->__current_product_id_to_display;
                break;

            case 'Local_ProductDetails':
                $pid = $this->getTag('Local_NextProductIdToDisplay');
                if ($pid == null)
                    $value = '';
                else
                    $value = $this->renderProductInfo($pid);
                break;

            # override the PaginatorRows tag behavior
            case 'PaginatorLine':
                $value = getPaginatorLine($this->__paginator_name, 'ProductList', $this->__filter_hash); //
                break;

            # override the PaginatorRows tag behavior
            case 'PaginatorDropdown':
                $value = getPaginatorDropdown($this->__paginator_name, 'ProductList');
                break;

            case 'CategoryName':
                $catobj = new CCategoryInfo($this->__tag_settings->filter->category_id);
                $value = $catobj->getCategoryTagValue('name');
                break;

            case 'ProductOptionsForm':
                $value = getOptionsChoice($this->_Product_Info->getProductTagValue('ID'));
                break;

            case 'RelatedProducts':
                $value = getRelatedProducts($this->_Product_Info->getProductTagValue('ID'));
                break;

            case 'Local_JSfuncProductFormSubmit':
                $value = "<script type=\"text/javascript\">".
                         "function ProductFormSubmit_".$this->_Product_Info->getProductTagValue('ID')."()".
                         "{".
                         " document.forms['ProductForm_".$this->_Product_Info->getProductTagValue('ID')."'].submit();".
                         "};".
                         "</script>";
                break;

            case 'Local_ProductFormStart':
                $value = '<form action="cart.php" name="ProductForm_'.$this->_Product_Info->getProductTagValue('ID').'" id="ProductForm_'.$this->_Product_Info->getProductTagValue('ID').'" method="post">
                          <input type="hidden" name="asc_action" value="AddToCart" />
                          <input type="hidden" name="prod_id" value="' . $this->_Product_Info->getProductTagValue('ID') . '" />
                        <script type="text/javascript">
                        function ProductFormSubmit_'.$this->_Product_Info->getProductTagValue('ID').'()
                        {
                            document.forms[\'ProductForm_'.$this->_Product_Info->getProductTagValue('ID').'\'].submit();
                        };
                        </script>';
                break;

            case 'Local_ProductFormEnd':
                $value = '</form>';
                break;

            case 'Local_ProductAddToCart':
                $value = 'javascript: ProductFormSubmit_'.$this->_Product_Info->getProductTagValue('ID').'();';
                break;

            case 'ProductDetailedImages':
                $value = getProductDetailedImages($this->_Product_Info->getProductTagValue('ID'));
                break;

            case 'Local_FormQuantityFieldName':
                $value = 'quantity_in_cart';
                break;

            case 'Local_ProductQuantityOptions':
                $qty_in_cart = modApiFunc("Cart", "getProductQuantity", $this->_Product_Info->getProductTagValue('ID'));
                $value = modApiFunc("Cart", "getProductQuantityOptions", $qty_in_cart, $this->_Product_Info->getProductTagValue('ID'));
                break;

    	    default:
    	        list($entity, $tag) = getTagName($tag);
        	    if ($entity == 'product' && is_object($this->_Product_Info))
        	    {
        	        $value = $this->_Product_Info->getProductTagValue($tag);
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

    var $__tag_settings = null;
    var $__filter_hash = null;
    var $__paginator_name = null;
    var $__tmpl_filler = null;
    var $__product_list_to_display = null;
    var $_Product_Info = null;
    var $__index = 0;
    var $__current_product_id_to_display = null;
    var $columns = 3;


    /**#@-*/
}
?>