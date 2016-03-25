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
 * Catalog Products List in current category.
 *
 * @author Alexander Girin
 * @package Catalog
 * @access  public
 */
class ProductList
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
    	    'layout-file'        => 'product-list-config.ini'
    	   ,'files' => array(
    	        'Container'      => TEMPLATE_FILE_SIMPLE
    	       ,'ContainerEmpty' => TEMPLATE_FILE_SIMPLE
    	       ,'Separator'      => TEMPLATE_FILE_SIMPLE
    	       ,'Item'           => TEMPLATE_FILE_PRODUCT_TYPE
               ,'ItemOutOfStock' => TEMPLATE_FILE_PRODUCT_TYPE
    	    )
    	   ,'options' => array(
    	        'Columns'        => TEMPLATE_OPTION_REQUIRED
    	    )
    	);
    	return $format;
    }

    /**
     * The view constructor.
     *
     * @ finish the functions on this page
     */
    function ProductList()
    {
        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("ProductList"))
        {
            $this->NoView = true;
        }
    }

    function getProductsListCount()
    {
        global $application;
        $pl = ($this->pl) ? $this->pl : $this->getPL();
        $this->pl = null;
        $items = "";
        $col =1;
        $columns = $this->columns;
        $this->_Product_Info = null;
        reset($pl);
        $outputed_products = array();
        $newrow = 0;
        while(!empty($pl))
        {
            $productInfo_array = array_shift($pl);
            $this->_Product_Info = &$application->getInstance('CProductInfo', $productInfo_array['product_id']);
            $outputed_products[] = $productInfo_array['product_id'];
        };
        $return  = count($outputed_products);
        return $return;
    }

    /**
     * Generates the main page containing a product list.
     */
    function getDefaultProductsList()
    {
        //             OutOfStock -                             .
        //                                   OutOfStock -                      .
        $store_show_absent = modApiFunc("Configuration", "getValue", "store_show_absent");

        global $application;

        $pl = ($this->pl) ? $this->pl : $this->getPL();

        $this->pl = null;

        $items = "";
        $col =1;
        $columns = $this->columns;
        $this->_Product_Info = null;
        reset($pl);

        $__templateFiller = new TemplateFiller();
        $__templateFiller->setTemplate($this->pl_tag_settings->getTemplate());

        $disable_trtd = $application->getAppIni('PRODUCT_LIST_DISABLE_TR_TD');
        if ($disable_trtd != null && strtolower($disable_trtd) === 'yes')
            $disable_trtd = true;
        else
            $disable_trtd = false;

        $outputed_products = array();
        $newrow = 0;
        while(!empty($pl))
        {
            $productInfo_array = array_shift($pl);

            if ($newrow == 1)
            {
                if ($disable_trtd == false) $items .= '</td></tr>';
                else $items .= $__templateFiller->fill('Separator');
                $newrow = 0;
            }
            if ($col == 1)
            {
                if ($disable_trtd == false) $items .= '<tr><td class="hasRightBorder">';
                $col++;
            }
            elseif( $col > 1 && $col != $columns )
            {
                if ($disable_trtd == false) $items .= '</td><td class="hasRightBorder">';
                $col++;
            }
            else
            {
                if ($disable_trtd == false) $items .= '</td><td class="hasNOborder">';
                $col++;
            }
            if ($col > $columns)
            {
                $newrow = 1;
                $col = 1;
            }

            $this->_Product_Info = &$application->getInstance('CProductInfo', $productInfo_array['product_id']);

            $outputed_products[] = $productInfo_array['product_id'];
            $low_stock_level = $this->_Product_Info->getProductTagValue('LowStockLevel', PRODUCTINFO_NOT_LOCALIZED_DATA);

            $stock_method = $this->_Product_Info->whichStockControlMethod();
            if ($stock_method == PRODUCT_OPTIONS_INVENTORY_TRACKING)
            {
                $qty_in_stock = modApiFunc('Product_Options','getQuantityInStockByInventoryTable','product', $productInfo_array['product_id'], true);
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

            modApiFunc("tag_param_stack", "push", __CLASS__, array(array("key" => TAG_PARAM_PROD_ID, "value" => $productInfo_array['product_id'])));
            $items .= $__templateFiller->fill($template, $this->_Product_Info->getProductTagValue('TypeId'));
            modApiFunc("tag_param_stack", "pop", __CLASS__);
        };

        unset($pl);
        modApiFunc('EventsManager','throwEvent', 'ProductlistDisplayed', $outputed_products);

        if ($disable_trtd == false)
        {
            while( $col <= $columns )
            {
                $items .= '</td><td> ';
                $col++;
            }
            $items .= '</td></tr>';
        }

        return $items;
    }

    function getProductsList()
    {
        //             OutOfStock -                             .
        //                                   OutOfStock -                      .
        $store_show_absent = modApiFunc("Configuration", "getValue", "store_show_absent");

        global $application;

        $pl = ($this->pl) ? $this->pl : $this->getPL();

        $this->pl = null;

        $items = "";
        $col =1;
        $columns = 4;
        $this->_Product_Info = null;
        reset($pl);

        $__templateFiller = new TemplateFiller();
        $__templateFiller->setTemplate($this->pl_tag_settings->getTemplate());

        $disable_trtd = $application->getAppIni('PRODUCT_LIST_DISABLE_TR_TD');
        if ($disable_trtd != null && strtolower($disable_trtd) === 'yes')
            $disable_trtd = true;
        else
            $disable_trtd = false;

        $outputed_products = array();
        $newrow = 0;
        while(!empty($pl))
        {
            $productInfo_array = array_shift($pl);

            if ($newrow == 1)
            {
                if ($disable_trtd == false) {
			$items .= '</td></tr>';
		}
		else {
			$items .= '</div></div>';
			$items .= $__templateFiller->fill('Separator');
			// $items .= '</div>';
		}
                $newrow = 0;
            }
            if ($col == 1)
            {
                if ($disable_trtd == false) {
			$items .= '<tr><td class="hasRightBorder">';
		}
		else {
			$items .= '<div class="products-grid row-fluid "><div class="span3 item">';
		}
                $col++;
            }
            elseif( $col > 1 && $col != $columns )
            {
                if ($disable_trtd == false) {
			$items .= '</td><td class="hasRightBorder">';
		}
		else {
			$items .= '</div><div class="span3 item">';
		}
                $col++;
            }
            else
            {
                if ($disable_trtd == false) {
			$items .= '</td><td class="hasNOborder">';
		}
		else {
			$items .= '</div><div class="span3 item">';
		}
                $col++;
            }
            if ($col > $columns)
            {
                $newrow = 1;
                $col = 1;
            }

            $this->_Product_Info = &$application->getInstance('CProductInfo', $productInfo_array['product_id']);

            $outputed_products[] = $productInfo_array['product_id'];
            $low_stock_level = $this->_Product_Info->getProductTagValue('LowStockLevel', PRODUCTINFO_NOT_LOCALIZED_DATA);

            $stock_method = $this->_Product_Info->whichStockControlMethod();
            if ($stock_method == PRODUCT_OPTIONS_INVENTORY_TRACKING)
            {
                $qty_in_stock = modApiFunc('Product_Options','getQuantityInStockByInventoryTable','product', $productInfo_array['product_id'], true);
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

            modApiFunc("tag_param_stack", "push", __CLASS__, array(array("key" => TAG_PARAM_PROD_ID, "value" => $productInfo_array['product_id'])));
            $items .= $__templateFiller->fill($template, $this->_Product_Info->getProductTagValue('TypeId'));
            modApiFunc("tag_param_stack", "pop", __CLASS__);
        };

        unset($pl);
        modApiFunc('EventsManager','throwEvent', 'ProductlistDisplayed', $outputed_products);

        if ($disable_trtd == false)
        {
            while( $col <= $columns )
            {
                $items .= '</td><td> ';
                $col++;
            }
            $items .= '</td></tr>';
        }
	else {
		while( $col <= $columns )
            	{
                	$items .= '</div><div> ';
                	$col++;
            	}
		$items .= '</div></div>';
	}

        return $items;
    }

    function getPL()
    {   // temporary workaround
        // NEED TO FIX
        //$op = new PRODUCT_LIST_PARAMS();
        //$this->pl_tag_settings->filter->params['filter'] = $op->params['filter'];

        $pl = modApiFunc('Catalog', 'getProductListByFilter', $this->pl_tag_settings->filter, RETURN_AS_ID_LIST);
        return $pl;
    }

    function getPagParam()
    {
        return $this->pagParam;
    }

    function getPagName()
    {
    	return $this->pagName;
    }

    function getBlockName()
    {
        return 'ProductList';
    }

    function registerAttributes()
    {
    	global $application;
    	$_template_tags = array(
				    			'ProductOptionsForm'=> ''
				    			,'Local_HiddenFields' => ''
				    			,'Local_JSfuncProductFormSubmit' => ''
				    			,'Local_ProductFormStart' => ''
				    			,'Local_ProductFormEnd' => ''
				    			,'Local_ProductAddToCart' => ''
				    			,'ProductDetailedImages' => ''
				    			,'Local_FormQuantityFieldName' => ''
				    			,'Local_ProductQuantityOptions' => ''
				    			,'Local_Columns' => ''
				    			,'Local_Thumbnail' => ''
				    			,'Local_ThumbnailSide' => ''
				    			,'ProductColorSwatchImages' => ''
							,'Local_ItemCount' =>''
				    	);

        $application->registerAttributes($_template_tags,'ProductList');
    }

    /**
     * Returns the Product Listing view.
     *
     * @return string the Products List view.
     */
    function output()
    {
        global $application;
        #define whether to output the view or not
        if ($this->NoView)
        {
            $application->outputTagErrors(true, __CLASS__, "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, __CLASS__, "Warnings");
        }

        //                                           .
        $in_param = @func_get_arg(0);

        //                        -                        CProductListTagSettings,
        if (_is_a($in_param, 'CProductListTagSettings'))
        {
            $this->pl_tag_settings = $in_param;
        }
        else
        {
            //                            CProductListTagSettings
            $this->pl_tag_settings = new CProductListTagSettings();

            //                                       ,
            if ($in_param === false || $in_param === null)
            {
                $this->pl_tag_settings->filter = modApiFunc('CProductListFilter','getProductListParamsObject');
                //                                 (default                                       )
                //                                                   -
                $this->pl_tag_settings->filter->use_paginator = true;
            }
            else
            {
                //                        ,                  CProductListTagSettings,
                //                 Category Id,
                $category_id_to_read_from = $in_param;
                if (modApiFunc('Catalog','isCorrectCategoryId', $category_id_to_read_from))
                {
                    //                         ,                                         Show products from all subcategories
                    $this->pl_tag_settings->product_list_filter_object->disableSynchronization();
                    $this->pl_tag_settings->product_list_filter_object->changeCurrentCategoryId($category_id_to_read_from);
                    $this->pl_tag_settings->updateFilterParams();
                }
            }
        }

        //
        $this->pagParam = $this->pl_tag_settings->filter->category_id;
        $category = new CCategoryInfo($this->pagParam);
        $category_status = $category->getCategoryTagValue('status');
        if($category_status == CATEGORY_STATUS_OFFLINE)
        {
            $request = new Request();
            $request -> setView('Index');
            $application -> redirect($request);
            return;
        }

        $this->pagName = modApiFunc('CProductListFilter', 'getMnfPaginatorName');
        if (!$this->pagName)
            $this->pagName = "Catalog_ProdsList_".$this->pl_tag_settings->filter->category_id;
        modAPIFunc('paginator', 'setCurrentPaginatorName', $this->getPagName());

        //
        $this->registerAttributes();
        $this->templateFiller = new TemplateFiller();
        $this->templateFiller->setTemplate($this->pl_tag_settings->getTemplate());
        $this->_product_form_action = getpageurl('CartContent');

        $cols = @func_get_arg(1);
        if ($cols !== false && is_numeric($cols))
        {
            $this->columns = $cols;
        }
        else
        {
            $this->columns = intval($application->getBlockOption($this->pl_tag_settings->getTemplate(), "Columns"));
        }

        //
        $this->pl = $this->getPL();

        if (NULL == $this->pl)
        {
            $retval = $this->templateFiller->fill("ContainerEmpty");
        }
        else
        {
            $retval = $this->templateFiller->fill("Container");
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
        	case 'Local_Items':
			$skin = modApiFunc('Look_Feel', 'getCurrentSkin');
             		if(($skin == 'digiCenter') || ($skin == 'foodCourt') || ($skin == 'flowers')) {
        		$value = $this->getProductsList();
			}
			else {
        			$value = $this->getDefaultProductsList();
			}
        		break;

            case 'Local_Columns':
                $value = $this->columns;
                break;

	    case 'Local_ItemCount':
                $value = $this->getProductsListCount();
                break;

            # override the PaginatorRows tag behavior
            case 'PaginatorLine':
                $value = getPaginatorLine($this->getPagName(), $this->getBlockName(), $this->getPagParam());
                break;

            # override the PaginatorRows tag behavior
            case 'PaginatorDropdown':
                $value = getPaginatorDropdown($this->getPagName(), $this->getBlockName());
                break;

            case 'CategoryName':
                $catobj = &$application->getInstance('CCategoryInfo', $this->pl_tag_settings->filter->category_id);
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
                if(empty($this->_product_form_action))
                    $this->_product_form_action = getpageurl('CartContent');
                $buy_link = $this->_Product_Info->getProductTagValue('BuyLink');
                $redirect = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_SHOW_CART);
                $script = (($redirect) ? $this->_product_form_action : $_SERVER["PHP_SELF"]);
                $pid = $this->_Product_Info->getProductTagValue('ID');
                $value = '<form action="' . $script . '" name="ProductForm_'.$pid.'" id="ProductForm_'.$pid.'" method="post">
                          <input type="hidden" name="asc_action" value="AddToCart" />
                          <input type="hidden" name="prod_id" value="' . $pid . '" />
                        <script type="text/javascript">
                        function ProductFormSubmit_'.$pid.'()
                        {
                            document.forms[\'ProductForm_'.$pid.'\'].submit();
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

            case 'Local_Thumbnail':
                break;

            case 'Local_ThumbnailSide':
                $pi_settings = modApiFunc('Product_Images','getSettings');
                $value = $pi_settings['MAIN_IMAGE_SIDE'];
                break;

            case 'ProductColorSwatchImages':
               	$value = getColorSwatchImages($this->_Product_Info->getProductTagValue('ID'));
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

    /**
     * The current catalog id. It is used for internal cycle processing.
     *
     * @var integer
     */
    var $_CatID;

    /**
     * The current selected product info. It is used for internal cycle processing.
     *
     * @var array
     */
    var $_Product_Info;


    /**
     * Reference to the object TemplateFiller.
     *
     * @var TemplateFiller
     */
    var $templateFiller;

    /**
     * The current selected template.
     *
     * @var array
     */
    var $template;

    var $_resetFilters;
    var $pl_tag_settings = null;
    var $columns=null;
    var $_product_form_action = null;

    /**#@-*/
}
?>