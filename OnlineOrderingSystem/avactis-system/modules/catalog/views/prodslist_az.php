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
     * The view constructor.
     *
     * @ finish the functions on this page
     */
    function ProductList()
    {
        $this->CatID = modApiFunc('CProductListFilter','getCurrentCategoryId');
        $this->_Cat_Info = new CCategoryInfo($this->CatID);
        $this -> psf_filter = array();

        if (modApiFunc('Session', 'is_set', 'SearchProductFormFilter'))
        {
            // if product search form is used
            $this -> psf_filter = modApiFunc('Session', 'get', 'SearchProductFormFilter');

            // getting the PRODUCT_LIST_PARAMS object to manipulate
            $obj_params = new PRODUCT_LIST_PARAMS();
            $obj_params -> category_id = 1;
            $obj_params -> select_mode_recursiveness = IN_CATEGORY_RECURSIVELY;
            $obj_params -> product_id_list_to_select = $this -> psf_filter['result'];
            $obj_params -> use_paginator = true;

            // setting the paginator
            $this -> paginator_name = 'Catalog_ProdsList_PSF_' . count($this -> psf_filter['result']);
            $paginator = modApiFunc('paginator', 'setCurrentPaginatorName', $this -> paginator_name);

            // getting the products
            $this -> pl = modApiFunc('Catalog', 'getProductListByFilter', $obj_params);
            $this -> ProdNumInCat = count($this -> psf_filter['result']) - 1;
        }
        else
        {
            // otherwise browsing by category
            $this -> paginator_name = 'Catalog_ProdsList_' . $this->CatID;
            $paginator = modAPIFunc('paginator', 'setCurrentPaginatorName', $this -> paginator_name);
            $this -> pl = modApiFunc('Catalog', 'getProductListByGlobalFilter', PAGINATOR_ENABLE, RETURN_AS_ID_LIST);
            $this -> ProdNumInCat = $this->_Cat_Info->getCategoryTagValue('productsnumber');
        }

        $SizeOfList = sizeof($this->pl);

        $paginator_offset = modApiFunc('paginator', 'getCurrentPaginatorOffset');

        if($paginator_offset >= 0 && sizeof($this->pl) > 0)
        {
            $this->From = 1 + $paginator_offset;
            $this->To =  -1 + $this->From + sizeof($this->pl);
        }
        else
        {
            $this->From = 0;
            $this->To =   0;
        }
        $this->_build_cats_paths();

        loadClass('CategoriesBrowser');
        $this->cb_params = array(
            'show_category_path' => true
           ,'category_path_prefix' => ''
           ,'show_products_count' => true
           ,'buttons' => array()
        );
    }

    function _build_cats_paths()
    {
        $cats = modApiFunc('Catalog','getSubcategoriesFullListWithParent',1,false);

        $paths = array();

        $last_parents_on_levels = array();

        for($i=0;$i<count($cats);$i++)
        {
            $cat = $cats[$i];
            $cpath = '';
            if(isset($last_parents_on_levels[$cat['level']-1]))
                $cpath = $last_parents_on_levels[$cat['level']-1]['path'];
            $cpath.='/'.$cat['name'];

            $last_parents_on_levels[$cat['level']] = array('id'=>$cat['id'],'path'=>$cpath);
            $paths[$cat['id']]=_ml_substr($cpath,1);
        };
	$this->_cats_paths=$paths;
    }

    /**
     * Returns direct subcategories html-select drop down list.
     *
     * @ finish the functions on this page
     */

    function getSubcategoriesList()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        $a = modApiFunc("Catalog", "getSubcategoriesFullListWithParent", 1, false);
        $pq = modApiFunc("Catalog", "getProductsQuantityByCategories");
        $pr_quan_in_cat = array();
        foreach ($pq as $val)
        {
            $pr_quan_in_cat[$val["c_id"]] = $val["count_p_id"];
        }
        if(count($a) == 0)
        {
            return "<option>".$obj->getMessage(new ActionMessage('MNG_CTGR_EMPTY'))."</option>";
        }
        else
        {
            $max_len = 0;
            $i = 0;
            foreach ($a as $value)
            {
                $len = _ml_strlen($value["name"])+$value["level"]*2;
                if ($len > $max_len)
                {
                    $max_len = $len;
                }
                if (isset($pr_quan_in_cat[$value["id"]]))
                {
                    $a[$i]["count_p_id"] = $pr_quan_in_cat[$value["id"]];
                }
                else
                {
                    $a[$i]["count_p_id"] = 0;
                }
                $i++;
            }

            $select_list = "";
            foreach ($a as $key => $value)
            {
                $spaces = "";
                for ($i=0; $i<$value["level"]; $i++)
                {
                    $spaces .= "&nbsp;&nbsp;";
                }
                $select_list .= "<option value=\"" . $value["id"] . "\"";
                if ($value["id"]==modApiFunc('CProductListFilter','getCurrentCategoryId'))
                {
                    $select_list .= " SELECTED ";
                }
                $select_list .= ">";
                $select_list .= $spaces.prepareHTMLDisplay($value["name"]);
                $spaces = "";
                for ($i=0; $i<($max_len - (_ml_strlen($value["name"])+$value["level"]*2) + 2); $i++)
                {
                    $spaces .= "&nbsp;";
                }
                $select_list.= " (".$value["count_p_id"].")";
                $select_list .= "</option>";
            }
            return $select_list;
        }
    }

    /**
     *
     *
     * @return HTML code
     */
    function outputCategoryStatistics()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        if ($this->_Cat_Info->getCategoryTagValue('subcategoriesnumber')==1)
        {
            return $obj->getMessage(new ActionMessage(array('MNG_PRD_HEADER_002', ($this->_Cat_Info->getCategoryTagValue('productsnumberrecursively_all_product_links')-$this->_Cat_Info->getCategoryTagValue('productsnumber_non_recursively')), $this->_Cat_Info->getCategoryTagValue('subcategoriesnumber'))));
        }
        else
        {
            return $obj->getMessage(new ActionMessage(array('MNG_PRD_HEADER_003', ($this->_Cat_Info->getCategoryTagValue('productsnumberrecursively_all_product_links')-$this->_Cat_Info->getCategoryTagValue('productsnumber_non_recursively')), $this->_Cat_Info->getCategoryTagValue('subcategoriesnumber'))));
        }

    }




    /**
     * @                      ProductList->getProductList.
     */
    function getProductList()
    {
        global $application;

        # HTML code for Items() tag
        $result_html = "";

        # Current line number in the Product List
        $current_line_number = 0;

        # Minimal lines number in the Product List,
        # if current Product List will be contain less lines then $min_lines_number,
        # then add empty lines to the Product List
        $min_lines_number = 5;

        $paginator_offset = modApiFunc('paginator', 'getCurrentPaginatorOffset');

        $size_of_pl = count($this->pl);

        $pl = $this->pl;
        unset($this->pl);
        $objCurrentCat = new CCategoryInfo($this->CatID);
        $prod_parent_recursive_status_id = $objCurrentCat->getCategoryTagValue('RecursiveStatus');

        while(!empty($pl))
        {
            $productInfo_array = array_shift($pl);
            $this->_Current_Product = new CProductInfo($productInfo_array['product_id']);
            $checked = '';

            # Add additional tags
            $this->_Current_Product->setAdditionalProductTag('Checked', $checked);
            $this->_Current_Product->setAdditionalProductTag('i', $current_line_number);
            $this->_Current_Product->setAdditionalProductTag('N', ++$current_line_number + $paginator_offset);
            //$current_line_number++;

            # Redefine InfoLink tag, because CProductInfo generate this link for Customer Zone
            $request = new Request();
            $request->setView  ( 'Catalog_EditProduct' );
            $request->setAction( 'SetCurrentProduct' );
            $request->setKey   ( 'prod_id', '' );
            $this->_Current_Product->setAdditionalProductTag('InfoLink', $request->getURL().$this->_Current_Product->getProductTagValue('ID'));

            # Setup current product,
            # this will be used in getTag method to get tags value

            # Redefine SmallImage tag, because we need to display
            # all images 50x50 size
            if( $this->_Current_Product->getProductTagValue('SmallImage') != '' )
            {
                $_src = $this->_Current_Product->getProductTagValue('SmallImageSrc');
                $height = $this->_Current_Product->getProductTagValue('SmallImageHeight');
                $width = $this->_Current_Product->getProductTagValue('SmallImageWidth');
                $str_height = "height=50";
                $str_width = "width=50";
                if ($height > 50 || $width > 50)
                {
                    if ($height > $width)
                    {
                        $str_width = ''; //                           ,
                    }
                    else
                    {
                        $str_height = ''; //                           ,
                    }
                }
                $_img = "<img src='$_src' $str_width $str_height border=0>";
                $this->_Current_Product->setAdditionalProductTag('SmallImage', $_img);
            }

            # Register Additional tags
            $application->registerAttributes( $this->_Current_Product->getAdditionalProductTagList() );
            $application->registerAttributes(array('ProductAvailable', 'ProductOfflineStatusReason', 'ProductOfflineStatusColor','ProductCatsCount','ProductCatsList'));
            $application->registerAttributes(array('ProductThumbnail'));

            # Get HTML code for one product
            //             Offline -

            $prod_status_id = $this->_Current_Product->getProductTagValue('Available', PRODUCTINFO_NOT_LOCALIZED_DATA);
            if($prod_status_id == PRODUCT_STATUS_ONLINE &&
               $prod_parent_recursive_status_id == CATEGORY_STATUS_ONLINE)
            {
                $result_html .= modApiFunc('TmplFiller', 'fill', "catalog/prod_list/", ($current_line_number == $size_of_pl && $size_of_pl >= $min_lines_number ) ? "list_bottom_item.tpl.html" : "list_item.tpl.html", array());
            }
            else if($prod_status_id == PRODUCT_STATUS_OFFLINE ||
                    $prod_parent_recursive_status_id == CATEGORY_STATUS_OFFLINE)
            {
                $result_html .= modApiFunc('TmplFiller', 'fill', "catalog/prod_list/", ($current_line_number == $size_of_pl && $size_of_pl >= $min_lines_number ) ? "list_bottom_item_offline.tpl.html" : "list_item_offline.tpl.html", array());
            };

            $this->_Current_Product->_destruct();
            $this->_Current_Product = null;
            unset($productInfo_array);
        }

        # If no product to display, then display one line with NA values
        if($current_line_number== 0)
        {
            $result_html .= modApiFunc('TmplFiller', 'fill', "catalog/prod_list/", "list_item_empty_na_values.tpl.html", array());
            $current_line_number++;
        }

        # Add additional empty lines to the Product List block
        for(;$current_line_number < $min_lines_number; $current_line_number++)
        {
            $result_html .= modApiFunc('TmplFiller', 'fill', "catalog/prod_list/", $current_line_number ==  $min_lines_number - 1 ? "list_bottom_item_empty.tpl.html" : "list_item_empty.tpl.html", array());
        }


        modApiFunc('Catalog', 'unsetEditableProductsID');
        return $result_html;
    }

    /**
     * Returns the Product Listing view.
     *
     * @return string the Products List view.
     *
     * @ finish the functions on this page
     */
    function output()
    {
        global $application;
        $application->registerAttributes(array(
             'Add_Product_Link'
            ,'Move_Products_Link'
            ,'Copy_Products_Link'
            ,'Edit_Product_Link'
            ,'Delete_Products_Link'
            ,'SortProdHref'
            ,'SortAlertMessage'
            ,'AlertMessage'
            ,'ProductsNumber'
            ,'ProductsNumberRecursively'
            ,'SubcategoriesNumber'
            ,'From'
            ,'To'
            ,'CategoryStatistics'
            ,'CurrensySign'
            ,'Local_CategoriesBrowser_GoTo'
            ,'Local_CategoriesBrowser_MoveTo'
            ,'Local_CategoriesBrowser_CopyTo'
            ,'CurrentCategoryPath'
            ,'HeaderData'
            ,'SearchFilterData'
            ,'FooterData'
            ,'IfSearchFormActive'
            ,'Local_NoImageSrc'
            ,'Local_SelectCategoriesProducts'
        ));

        $application->registerAttributes($this->__getSortTagsArrayWithPrefix($this->__sort_tag_prefix));

        $retval = modApiFunc('TmplFiller', 'fill', "catalog/prod_list/","list.tpl.html", array());
        return $retval;
    }

    /**
     * @ describe the function ProductList->getTag.
     */
    function getTag($tag)
    {
        global $application;
    	$value = null;
    	$CatID = modApiFunc('CProductListFilter','getCurrentCategoryId');
    	switch ($tag)
    	{
            case 'CurrentCategoryPath':
                $value = str_replace("/","&nbsp;&gt;&gt;&nbsp;",$this->_cats_paths[modApiFunc('CProductListFilter','getCurrentCategoryId')]);
                break;
            case 'Local_CategoriesBrowser_GoTo':
                $cb_obj = new CategoriesBrowser();
                $this->cb_params['category_path_prefix'] = getMsg('CTL','PRFX_TARGET_CATEGORY');
                $this->cb_params['buttons'] =
                    array('go' => array(
                            'label' => 'BTN_GO'
                           ,'callback' => 'OnGoButtonClick(%CID%);'
                           ,'default_state' => 'disabled'
                           ,'enable_condition' => 'category_selected'
                          )
                         ,'cancel' => array(
                            'label' => 'BTN_CANCEL'
                           ,'callback' => 'hideBlock(\'categories_browser_goto\');'
                           ,'default_state' => 'enabled'
                          )
                    );

                $value = $cb_obj->output($this->cb_params);

                break;
            case 'Local_CategoriesBrowser_MoveTo':
                $cb_obj = new CategoriesBrowser();
                $this->cb_params['category_path_prefix'] = getMsg('CTL','PRFX_TARGET_CATEGORY');
                $this->cb_params['buttons'] =
                    array('move' => array(
                            'label' => 'BTN_MOVE_PRD'
                           ,'callback' => 'OnMoveButtonClick(%CID%);'
                           ,'default_state' => 'disabled'
                           ,'enable_condition' => 'category_selected'
                          )
                         ,'cancel' => array(
                            'label' => 'BTN_CANCEL'
                           ,'callback' => 'hideBlock(\'categories_browser_moveto\');'
                           ,'default_state' => 'enabled'
                          )
                    );
                $value = $cb_obj->output($this->cb_params);
                break;
            case 'Local_CategoriesBrowser_CopyTo':
                $cb_obj = new CategoriesBrowser();
                $this->cb_params['category_path_prefix'] = getMsg('CTL','PRFX_TARGET_CATEGORY');
                $this->cb_params['buttons'] =
                    array('move' => array(
                            'label' => 'BTN_COPY_PRD'
                           ,'callback' => 'OnCopyButtonClick(%CID%);'
                           ,'default_state' => 'disabled'
                           ,'enable_condition' => 'category_selected'
                          )
                         ,'cancel' => array(
                            'label' => 'BTN_CANCEL'
                           ,'callback' => 'hideBlock(\'categories_browser_copyto\');'
                           ,'default_state' => 'enabled'
                          )
                    );
                $value = $cb_obj->output($this->cb_params);
                break;
            case 'ProductListSubcategories':
                $value = $this->getSubcategoriesList();
                break;

            case 'Breadcrumb':
                $value = modApiFunc("Breadcrumb", "output", true, "ProductList");
                break;

    		case 'Items':
    			$value = $this->getProductList();
    			break;

    		case 'CategoryName':
                $categoryInfo = new CCategoryInfo($CatID);
                $value = $categoryInfo->getCategoryTagValue('name');
    		    break;

    		case 'PaginatorLine':
                $obj = &$application->getInstance($tag);
                $value = $obj->output($this -> paginator_name, "ProductList");
                break;

            # override the PaginatorRows tag behavior
            case 'PaginatorRows':
                $obj = &$application->getInstance($tag);
                $value = $obj->output($this -> paginator_name, 'ProductList');
                break;

    	    case 'Add_Product_Link':
                $request = new Request();
//                $request->setView  ( 'SelectProductType' );
                $request->setView  ( 'Catalog_AddProduct' );
                $request->setKey   ( 'category_id', $CatID );
                $value = $request->getURL();
    	        break;

    	    case 'Move_Products_Link':
                $request = new Request();
                $request->setView  ( 'MoveProducts' );
                $request->setAction( 'SetEditableProducts' );
                $value = $request->getURL();
    	        break;

    	    case 'Copy_Products_Link':
                $request = new Request();
                $request->setView  ( 'CopyProducts' );
                $request->setAction( 'SetEditableProducts' );
                $value = $request->getURL();
    	        break;

    	    case 'Edit_Product_Link':
                $request = new Request();
                $request->setView  ( 'Catalog_EditProduct' );
                $request->setAction( 'SetCurrentProduct' );
                $request->setKey('prod_id', '');
                $value = $request->getURL();
    	        break;

    	    case 'Delete_Products_Link':
                $request = new Request();
                $request->setView  ( 'DeleteProducts' );
                $request->setAction( 'SetEditableProducts' );
                $value = $request->getURL();
    	        break;

    	    case 'SortProdHref':
                $request = new Request();
                $request->setView  ( 'SortProducts' );
                $value = $request->getURL();
    	        break;
            case 'ProductsInCatTotal':
                $value = $this->ProdNumInCat;
                break;
            case 'AlertMessage':
                $MessageResources = &$application->getInstance('MessageResources');
                $err_mes = new ActionMessage(array('PRDLST_006'));
                $value = $MessageResources->getMessage($err_mes);
                break;
            case 'SortAlertMessage':
                if ($this->ProdNumInCat==0)
                {
                    $MessageResources = &$application->getInstance('MessageResources');
                    $err_mes = new ActionMessage(array('PRDLST_007'));
                    $value = $MessageResources->getMessage($err_mes);
                }
                elseif ($this->ProdNumInCat==1)
                {
                    $MessageResources = &$application->getInstance('MessageResources');
                    $err_mes = new ActionMessage(array('PRDLST_008'));
                    $value = $MessageResources->getMessage($err_mes);
                }
                else
                {
                    $value = '';
                }
                break;
            case 'ProductsNumber':
                $value = $this->_Cat_Info->getCategoryTagValue('productsnumber_non_recursively');
                break;
            case 'ProductsNumberRecursively':
                $value = $this->_Cat_Info->getCategoryTagValue('productsnumberrecursively') - $this->_Cat_Info->getCategoryTagValue('productsnumber');
                break;
            case 'SubcategoriesNumber':
                $value = $this->_Cat_Info->getCategoryTagValue('subcategoriesnumber');
                break;
            case 'From':
                $value = $this->From;
                break;
            case 'To':
                $value = $this->To;
                break;
            case 'CategoryStatistics':
                $value = $this->outputCategoryStatistics();
                break;
            case 'CurrensySign':
                $value = modApiFunc("Localization", "getCurrencySign");
                break;

            case 'ProductOfflineStatusReason':
                $prod_status_id = $this->_Current_Product->getProductTagValue('Available', PRODUCTINFO_NOT_LOCALIZED_DATA);
                $value = $prod_status_id == PRODUCT_STATUS_ONLINE ? getMsg('SYS',"PRODUCT_PARENT_STATUS_ONLINE") : "";
                break;

            case 'ProductOfflineStatusColor':
                //                   Offline.
                //                                    Online,                 ,      Offline -        .
                $prod_status_id = $this->_Current_Product->getProductTagValue('Available', PRODUCTINFO_NOT_LOCALIZED_DATA);
                $value = $prod_status_id == PRODUCT_STATUS_ONLINE ? "rgb(175, 175, 175)" : "#FF0000";
                break;

            case 'ProductCatsCount':
                $value = count($this->_Current_Product->getCategoriesIDs());
                break;

            case 'ProductCatsList':
                $_ps = array();
                $_cts = $this->_Current_Product->getCategoriesIDs();
                foreach($_cts as $cid)
                    $_ps[] = $this->_cats_paths[$cid];

                asort($_ps);
                $_ps = array_map("addslashes",array_map("_ml_htmlentities",$_ps));
                $value = implode("<br>",$_ps);
                break;

            case 'ProductQuantityInStock':
                if ($this->_Current_Product->whichStockControlMethod() == PRODUCT_OPTIONS_INVENTORY_TRACKING)
                {
                    $value = "";
                    $inv_qty = modApiFunc('Product_Options','getQuantityInStockByInventoryTable','product', $this->_Current_Product->getProductTagValue('ID'));
                    if(Validator::isValidInt($inv_qty) == true)
                        $value = $inv_qty . ' ' .modApiFunc('Localization', 'getUnitTypeValue', 'item');
                }
                else
                {
                    $value = $this->_Current_Product->getProductTagValue('QuantityInStock', PRODUCTINFO_NOT_LOCALIZED_DATA);
                    if (Validator::isValidInt($value) == true)
                    {
                        $value = $this->_Current_Product->getProductTagValue('QuantityInStock', PRODUCTINFO_LOCALIZED_DATA);
                    }
                }
                break;

            case 'HeaderData':
                if (empty($this -> psf_filter))
                    $value = modApiFunc('TmplFiller', 'fill', "catalog/prod_list/","list-header-category-data.tpl.html", array());
                else
                    $value = modApiFunc('TmplFiller', 'fill', "catalog/prod_list/","list-header-searchform-data.tpl.html", array());
                break;

            case 'SearchFilterData':
                $value = $this -> getSearchFilterDataText();
                break;

            case 'FooterData':
                if (empty($this -> psf_filter))
                    $value = modApiFunc('TmplFiller', 'fill', "catalog/prod_list/","list-footer-category-data.tpl.html", array());
                else
                    $value = modApiFunc('TmplFiller', 'fill', "catalog/prod_list/","list-footer-searchform-data.tpl.html", array());
                break;

            case 'IfSearchFormActive':
                $value = (empty($this -> psf_filter)) ? '' : 'Y';
                break;

            case 'ProductThumbnail':
                $value = modApiFunc('TmplFiller', 'fill', 'catalog/prod_list/', 'list_item_image.tpl.html', array());
                break;

            case 'Local_NoImageSrc':
                $value = $application->getAppIni('URL_IMAGES_DIR') . 'noimage.png';
                break;

            case 'Local_SelectCategoriesProducts':

        				$categorylist =  modApiFunc("Catalog", "getSubcategoriesFullListWithParent", 1, false);

							foreach($categorylist as $val=>$data)
							{

								$catvalid = $data["id"];
								if(($data['level'] == 2))
								{


							$value = '<option value="'.$catvalid.'" '.($CatID == $catvalid ? 'selected' : '') .'>&nbsp;&nbsp;&nbsp;'.$data["name"].'</option>';

								}
								else
								{
							$value = '<option value="'.$catvalid.'" '.($CatID == $catvalid ? 'selected' : '') .'>'.$data["name"].'</option>';

								}
							}


            	break;

    	    default:
                if ( _ml_strpos($tag, 'Local_SortBy') === 0 )
                {
                      $tag = _ml_substr($tag, _ml_strlen('Local_SortBy'));
                      $value = $this->getSortLink($tag);
                      break;
                }
    	        if ( _ml_strpos($tag, 'Product') === 0 )
        	    {
                      $tag = _ml_substr($tag, _ml_strlen('Product'));
        	    }
        	    if ( is_object($this->_Current_Product) && $this->_Current_Product->isTagExists($tag) )
        	    {
                	$value = $this->_Current_Product->getProductTagValue($tag);
        	    }
        		break;
    	}
    	return $value;
    }

    function getSortLink($tag)
    {
        # THIS FUNCTION TEMPORARY DISABLED
        return getMsg('CTL', 'LINK_NAME_'.getKeyIgnoreCase($tag, $this->__sort_tag_suffix));



        list($curr_sort_field, $curr_sort_direction) = modApiFunc('CProductListFilter','getCurrentSortField');
        $_sort_field = getKeyIgnoreCase($tag, $this->__sort_tag_suffix);
        if ($curr_sort_field == $_sort_field)
        {
            if ($curr_sort_direction == SORT_DIRECTION_ASC)
            {
                $_sort_direction = SORT_DIRECTION_DESC;
                $_template_name = "sort_link_asc.tpl.html";
            }
            else
            {
                $_sort_direction = SORT_DIRECTION_ASC;
                $_template_name = "sort_link_desc.tpl.html";
            }
        }
        else
        {
            $_sort_direction = SORT_DIRECTION_ASC;
            $_template_name = "sort_link.tpl.html";
        }

        $_request = new Request();
        $_request->setView('ProductList');
        $_request->setAction('SetProductListSortField');
        $_request->setKey('field', $_sort_field.','.$_sort_direction);

        $params = array(    'LINK_HREF' => $_request->getURL(),
                            'LINK_NAME' => getMsg('CTL', 'LINK_NAME_'.$_sort_field)    );

        $value = modApiFunc('TmplFiller', 'fill', "catalog/prod_list/", $_template_name, $params);

        return $value;
    }

    /**
     * Gets search filter data text
     */
    function getSearchFilterDataText()
    {
        // if empty filter -> returns empty string
        if (empty($this -> psf_filter))
            return '';

        $output = array();
        if ($this -> psf_filter['pattern'] != '')
        {
            $line = getMsg('CTL', 'PSF_SEARCH_PATTERN') . ' ';
            if ($this -> psf_filter['pattern_type'] == 'all')
                $line .= '<b>' . getMsg('CTL', 'PSF_ALL_WORDS') . '</b>';
            elseif ($this -> psf_filter['pattern_type'] == 'any')
                $line .= '<b>' . getMsg('CTL', 'PSF_ANY_WORD') . '</b>';
            else
                $line .= '<b>' . getMsg('CTL', 'PSF_EXACT_PHRASE') . '</b>';
            $line .= ' ' . getMsg('CTL', 'PSF_OF') . ' ';
            $line .= '<b>"' . prepareHTMLDisplay($this -> psf_filter['pattern']) . '"</b>';
            $line .= ' ' . getMsg('CTL', 'PSF_IN') . ' ';
            $in_fields = array();
            if (isset($this -> psf_filter['in_name']))
                $in_fields[] = '<b>' . getMsg('SYS', 'PRD_NAME_NAME') . '</b>';
            if (isset($this -> psf_filter['in_id']))
                $in_fields[] = '<b>' . getMsg('SYS', 'PRD_ID_NAME') . '</b>';
            if (isset($this -> psf_filter['in_sku']))
                $in_fields[] = '<b>' . getMsg('SYS', 'PRD_SKU_NAME') . '</b>';
            if (isset($this -> psf_filter['in_descr']))
                $in_fields[] = '<b>' . getMsg('SYS', 'PRD_SHRDESCR_NAME') . '</b>';
            if (isset($this -> psf_filter['in_det_descr']))
                $in_fields[] = '<b>' . getMsg('SYS', 'PRD_DETDESCR_NAME') . '</b>';
            if (isset($this -> psf_filter['in_title']))
                $in_fields[] = '<b>' . getMsg('SYS', 'PRD_PAGETTL_NAME') . '</b>';
            if (isset($this -> psf_filter['in_keywords']))
                $in_fields[] = '<b>' . getMsg('SYS', 'PRD_METAKWRD_NAME') . '</b>';
            if (isset($this -> psf_filter['in_meta_descr']))
                $in_fields[] = '<b>' . getMsg('SYS', 'PRD_METADESCR_NAME') . '</b>';
            $line .= join(', ', $in_fields);
            $output[] = $line;
        }
        if ($this -> psf_filter['category'] > 0)
        {
            $line = getMsg('CTL', 'PSF_CATEGORY') . ' ';
            $tmp_info = new CCategoryInfo($this -> psf_filter['category']);
            $line .= '<b>' . $tmp_info -> getCategoryTagValue('name') . '</b>';
            if (isset($this -> psf_filter['recursive']))
                $line .= ' (' . _ml_strtolower(getMsg('CTL', 'PSF_INCLUDE_SUBCATEGORIES')) . ')';
            $output[] = $line;
        }
        if ($this -> psf_filter['manufacturer'] > 0)
        {
            $line = getMsg('CTL', 'PSF_MANUFACTURER') . ' ';
            $tmp_info = modApiFunc('Manufacturers', 'getManufacturerInfo', $this -> psf_filter['manufacturer']);
            $line .= '<b>' . $tmp_info['manufacturer_name'] . '</b>';
            $output[] = $line;
        }
        if ($this -> psf_filter['price_min'] !== ''
            || $this -> psf_filter['price_max'] !== '')
        {
            $line = getMsg('CTL', 'PSF_PRICE_RANGE') . ' ';
            $line .= (($this -> psf_filter['price_min'] !== '')
                     ? '<b>' . $this -> psf_filter['price_min'] . '</b>'
                     : '<b>0.00</b>');
            $line .= (($this -> psf_filter['price_max'] !== '')
                     ? ' - <b>' . $this -> psf_filter['price_max'] . '</b>'
                     : '<b>+</b>');
            $output[] = $line;
        }

        // request to reset filter
        $request = new Request();
        $request -> setView('ProductList');
        $request -> setAction('SetCurrCat');
        $request -> setKey('category_id', 1);
        $request = $request -> getURL();
        $output[] = '<input type="hidden" value="0" id="categoryval_id" /><a href="javascript:gotoCategory();" style="font-weight:bold; font-size:11px; color:#000; font-family:Tahoma,sans-serif; text-decoration:underline;">' . getMsg('CTL', 'PSF_RESET_FILTER') . '</a>';
        $output[] = '';
//        $output[] = '<div class="button button_10em" title="' . getMsg('CTL', 'PSF_RESET_FILTER') . '" onclick="javascript:go(\'' . $request . '\');">' . getMsg('CTL', 'PSF_RESET_FILTER') . '</div>';

        if (count($this -> psf_filter['result']) > 1)
            $output[] = getMsg('CTL', 'PSF_SHOWING') . ' ' .
                '<b>' . $this -> From . '</b> - <b>' . $this -> To . '</b>' .
                ' ' . getMsg('CTL', 'PSF_OF') . ' <b>' .
                (count($this -> psf_filter['result']) - 1) . '</b>';
        else
            $output[] = getMsg('CTL', 'PSF_NO_PRODUCTS_FOUND');
        if (@$this -> psf_filter['overflow'])
            $output[] = ' <span style="color: red">' . getMsg('CTL', 'PSF_OVERFLOWTEXT') . '</span>';

        return join('<br />', $output);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    function __getSortTagsArrayWithPrefix($prefix)
    {
        $tags = array_keys($this->__sort_tag_suffix);
        foreach($tags as $key=>$value)
        {
            $tags[$key] = $prefix.$value;
        }
        return $tags;
    }


    var $__sort_tag_prefix = 'Local_SortBy';

    var $__sort_tag_suffix = array(
                    'Default'               => SORT_BY_PRODUCT_SORT_ORDER,
                    'SalePrice'             => SORT_BY_PRODUCT_SALE_PRICE,
                    'ListPrice'             => SORT_BY_PRODUCT_LIST_PRICE,
                    'Name'                  => SORT_BY_PRODUCT_NAME,
                    'DateAdded'             => SORT_BY_PRODUCT_DATE_ADDED,
                    'DateUpdated'           => SORT_BY_PRODUCT_DATE_UPDATED,
                    'QuantityInStock'       => SORT_BY_PRODUCT_QUANTITY_IN_STOCK,
                    'SKU'                   => SORT_BY_PRODUCT_SKU,
                    'PerItemShippingCost'   => SORT_BY_PRODUCT_PER_ITEM_SHIPPING_COST,
                    'PerItemHandlingCost'   => SORT_BY_PRODUCT_PER_ITEM_HANDLING_COST,
                    'Weight'                => SORT_BY_PRODUCT_WEIGHT,
                );

    var $_Current_Product = array();
    var $_Cat_Info = array();
    /**#@-*/

    /**
     *
     */
    var $ProdNumInCat;

    /**
     *
     */
    var $pl;

    var $_cats_paths;

    var $cb_params;

    var $psf_filter;

    var $paginator_name;
}
?>