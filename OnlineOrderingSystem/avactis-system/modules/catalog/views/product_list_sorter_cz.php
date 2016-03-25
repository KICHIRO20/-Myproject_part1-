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
 * View-class to create product sorter component.
 *
 * @package Catalog
 * @access  public
 * @author Alexey Florinsky
 */
class ProductListSorter
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
    	    'layout-file'        => 'product-list-sorter-config.ini'
    	   ,'files' => array(
               'Container'       => TEMPLATE_FILE_SIMPLE,
               'Item'            => TEMPLATE_FILE_SIMPLE,
               'ItemAsc'         => TEMPLATE_FILE_SIMPLE,
    	       'ItemDesc'        => TEMPLATE_FILE_SIMPLE,
    	   )
    	   ,'options' => array(
    	    )
    	);
    	return $format;
    }

    function ProductListSorter()
    {
        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("ProductListSorter"))
        {
            $this->NoView = true;
        }

        $_links_to_display = array_filter(array_map('trim', explode(',', $application->getAppIni('PRODUCT_LIST_SORTER_LINKS'))));
        foreach ($_links_to_display as $item)
        {
            $this->__sort_field_list_to_display[$item] = cz_getMsg($item);
        }
    }

/*    function getViewCacheKey()
    {
        //
        return md5(serialize(modApiFunc('CProductListFilter','getCurrentSortField')));
    }//*/

    function outputLinkList()
    {
        global $application;
        $templateFiller = new TemplateFiller();
        # define the template for the given view.
        $template = $application->getBlockTemplate('ProductListSorter');
        $templateFiller->setTemplate($template);

        $html = '';
        $_link_tags = array_flip($this->__sort_field_list);
        foreach ($this->__sort_field_list_to_display as $item_sort_field => $item_display)
        {
            $this->_local_name_tag = $item_display;
            $this->_local_href_tag = $this->getTag($this->__href_tag_prefix.$_link_tags[$item_sort_field], array(SORT_DIRECTION_ASC));

            list($curr_sort_field, $curr_sort_direction) = modApiFunc('CProductListFilter','getCurrentSortField');

            //                                                          ,
            if ($item_sort_field == $curr_sort_field)
            {
                //                                         ,
                if ($curr_sort_direction == SORT_DIRECTION_ASC)
                {
                    $this->_local_href_tag = $this->getTag($this->__href_tag_prefix.$_link_tags[$item_sort_field], array(SORT_DIRECTION_DESC));
                    $html .= $templateFiller->fill('ItemAsc');
                }
                else
                {
                    $html .= $templateFiller->fill('ItemDesc');
                }
            }
            //                                       "     "       ,                ASC
            else
            {
                $html .= $templateFiller->fill('Item');
            }
        }

        return $html;
    }

    /**
     * Outputs the search form.
     *
     * @return string.
     */
    function output()
    {
        global $application;

        #define whether to output the view or not
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "ProductListSorter", "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, "ProductListSorter", "Warnings");
        }

        $this->__current_category_id = modApiFunc('CProductListFilter','getCurrentCategoryId');

        # Register Additional tags
        $application->registerAttributes(
            array(  'Local_LinkList',
                    'Local_Href',
                    'Local_Name',
            ));

        $application->registerAttributes($this->__getSortTagsArrayWithPrefix($this->__href_tag_prefix));

        $templateFiller = new TemplateFiller();
        # define the template for the given view.
        $template = $application->getBlockTemplate('ProductListSorter');
        $templateFiller->setTemplate($template);
        $result = $templateFiller->fill("Container");

        return $result;
    }

    /**
     * Processes tags in the templates for the given view.
     *
     * @return string tag value, if tag has been processed. NULL, otherwise.
     */
    function getTag($tag)
    {
        $args = func_get_args();
        array_shift($args);
        $second_arg = @array_shift($args);
        $sort_direction = @$second_arg[0];
        if ($sort_direction === SORT_DIRECTION_DESC)
        {
            $sort_direction = SORT_DIRECTION_DESC;
        }
        else
        {
            $sort_direction = SORT_DIRECTION_ASC;
        }

        global $application;
        $value = null;
        switch ($tag)
        {
            case 'Local_LinkList':
                $value = $this->outputLinkList();
                break;

            case 'Local_Href':
                $value = $this->_local_href_tag;
                break;

            case 'Local_Name':
                $value = $this->_local_name_tag;
                break;

            default:
                if (_ml_strpos($tag, $this->__href_tag_prefix) === 0)
                {
                    $sort_field_key = _ml_substr($tag, _ml_strlen($this->__href_tag_prefix));
                    $sort_field = getKeyIgnoreCase($sort_field_key, $this->__sort_field_list);
                    $_request = new Request();
                    #$_request->setView('ProductList');
                    #$_request->setCategoryID($this->__current_category_id);
                    $_request->setView(CURRENT_REQUEST_URL);
                    $_request->setAction('SetProductListSortField');
                    $_request->setKey('field', $sort_field.','.$sort_direction);
                    $value = $_request->getURL();
                }
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

    function __getSortTagsArrayWithPrefix($prefix)
    {
        $tags = array_keys($this->__sort_field_list);
        foreach($tags as $key=>$value)
        {
            $tags[$key] = $prefix.$value;
        }
        return $tags;
    }

    var $__href_tag_prefix = 'Local_Href_SortBy';

    var $__sort_field_list = array(
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

    var $__sort_field_list_to_display = array();


    var $_local_href_tag;
    var $_local_name_tag;
    var $__current_category_id;
    /**#@-*/
}
?>