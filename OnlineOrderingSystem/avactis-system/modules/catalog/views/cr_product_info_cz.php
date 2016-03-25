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

/**
 * A clone of ProductInfo class
 * used to show its own set of templates
 * since mechanism of aliases does not work for ProductInfo
 *
 * @package Catalog
 * @access  public
 * @author Sergey E. Kulitsky
 */

class CustomerReviewsProductInfo extends Catalog_ProdInfo_Base
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
    	    'layout-file'        => 'cr-product-info-config.ini',
    	    'files' => array(
    	        'Item'           => TEMPLATE_FILE_SIMPLE,
                'ItemOutOfStock' => TEMPLATE_FILE_SIMPLE
    	    ),
    	    'options' => array(
    	    )
    	);
    	return $format;
    }

    function CustomerReviewsProductInfo()
    {
        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("CustomerReviewsProductInfo"))
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
            $application->outputTagErrors(true, "CustomerReviewsProductInfo", "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, "CustomerReviewsProductInfo", "Warnings");
        }


        $catalog = &$application->getInstance('Catalog');
        $this->prod_id = @func_get_arg(0);
        if ($this->prod_id === false)
        {
            $this->prod_id = $catalog->getCurrentProductID();
        }
        if (!$this->prod_id)
        {
        	return;
        }

        modApiFunc("tag_param_stack", "push", __CLASS__, array(array("key" => TAG_PARAM_PROD_ID, "value" => $this->prod_id)));

        # Get current selected product info.
        $product_info = modApiFunc('Catalog', 'getProductInfo', $this->prod_id);

        # save info
        $this->_ProductInfo = $product_info;

        #                Related
        $this->_rp_output = getRelatedProducts($this->prod_id);

        #
        $application->registerAttributes(array(
                    'Local_HiddenFields'=>''
                   ,'Local_JSfuncProductFormSubmit'=>''
                   ,'Local_ProductStockWarnings' => ''
                   ,'ProductOptionsForm'=>''
                   ,'Local_ProductFormStart' => ''
                   ,'Local_ProductFormEnd' => ''
                   ,'Local_ProductAddToCart' => ''
                   ,'Local_FormQuantityFieldName' => ''
                   ,'Local_ProductQuantityOptions' => ''
                   ,'RelatedProducts' => '' //                        Related
                  ));

        $templateFiller = new TemplateFiller();
        # define the template for the given view.
        $template = $application->getBlockTemplate('CustomerReviewsProductInfo');
        $templateFiller->setTemplate($template);


        $obj_product = new CProductInfo($this->prod_id);
        $stock_method = $obj_product->whichStockControlMethod();
        if ($stock_method == PRODUCT_OPTIONS_INVENTORY_TRACKING)
        {
            //      stock                                    ,                product info
            //
            $template = "Item";
        }
        else
        {
            //             OutOfStock -                             .
            //                                   OutOfStock -                      .
            $store_show_absent = modApiFunc("Configuration", "getValue", "store_show_absent");
            $qty_in_stock = $this->_ProductInfo['attributes']['QuantityInStock']['value'];
            $low_stock_level = $this->_ProductInfo['attributes']['LowStockLevel']['value'];

            $b_out_of_stock = ($qty_in_stock !== "" && $qty_in_stock <1);

            switch($store_show_absent)
            {
                case STORE_SHOW_ABSENT_SHOW_BUY:
                    //                       <          ,                   > -
                    //
                    $template = "Item";
                    break;
                case STORE_SHOW_ABSENT_SHOW_NOT_BUY:
                    //             <          ,                      >
                    //            LowStock -           <Low Stock! Buy Now!>
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
                    //             <             ,                      >
                    if($b_out_of_stock === true)
                    {
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
                $value = '<form action="cart.php" name="ProductForm_'.$this->_ProductInfo['ID'].'" id="ProductForm_'.$this->_ProductInfo['ID'].'" method="post" enctype="multipart/form-data">
                          <input type="hidden" name="asc_action" value="AddToCart" />
                          <input type="hidden" name="prod_id" value="' . $this->_ProductInfo['ID'] . '" />
                        <script type="text/javascript">
                        function ProductFormSubmit_'.$this->_ProductInfo['ID'].'()
                        {
                            document.forms[\'ProductForm_'.$this->_ProductInfo['ID'].'\'].submit();
                        };
                        </script>';
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

            case 'Local_ProductQuantityOptions':
                $qty_in_cart = modApiFunc("Cart", "getProductQuantity", $this->_ProductInfo['ID']);
                $value =modApiFunc("Cart", "getProductQuantityOptions", $qty_in_cart, $this->_ProductInfo['ID']);
                break;

            case 'RelatedProducts':
                $value = $this->_rp_output; //                           output
                break;

    	    default:
    	        list($entity, $tag) = getTagName($tag);
        	    if ($entity == 'product')
        	    {
        	        $value = getKeyIgnoreCase($tag, $this->_ProductInfo);
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
    var $_rp_output;

    /**#@-*/
}
?>