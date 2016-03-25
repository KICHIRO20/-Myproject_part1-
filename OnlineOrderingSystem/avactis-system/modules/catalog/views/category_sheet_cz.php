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
 *
 * @package Catalog sheet
 * @access  public
 * @author  Loginov Leonid
 */
class CategorySheet
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**
     * Gets a template format for the given view.
     */
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'category-sheet-config.ini'
           ,'files' => array(
                'Container'      => TEMPLATE_FILE_SIMPLE
               ,'ContainerEmpty' => TEMPLATE_FILE_SIMPLE
               ,'Item'           => TEMPLATE_FILE_SIMPLE
               ,'Branch'         => TEMPLATE_FILE_SIMPLE
               ,'Product'        => TEMPLATE_FILE_SIMPLE

            )
           ,'options' => array(
            )
        );
        return $format;
    }

    /**
     * The view constructor.
     *
     * @ finish the functions on this page
     */
    function CategorySheet()
    {
        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("CategorySheet"))
        {
            $this->NoView = true;
        }
    }


    /**
     * Returns the Subcategories Listing view.
     *
     * @return string the Subcategories List view.
     */
    function output()
    {
        global $application;

        $args = func_get_args();
        $this->root_dir_of_catalog = isset($args[0]) ? $args[0] : 1 ;
        $this->show_products = isset($args[1]) ? $args[1] : true ;
        $this->total_cols = isset($args[2]) ? $args[2] : 3 ;
        $this->current_product = null;

        #define whether to output the view or not
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "CategorySheet", "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, "CategorySheet", "Warnings");
        }


        $this->templateFiller = new TemplateFiller();


        $this->template = $application->getBlockTemplate('CategorySheet');
        $this->templateFiller->setTemplate($this->template);

        // register new inner tags
        $application->registerAttributes(array(
                        'Local_Items'=>'',
                        'Local_CategoryLevel'=>'',
                        'Local_CategoryBranch'=>'',
                        'Local_CategoryProducts'=>'',
                        'Local_WidthCol'=>'',
                        'Local_TotalCol'=>'',
                      ));

        $this->catalog_tree = modApiFunc("Catalog", "getSubcategoriesFullListWithParent", $this->root_dir_of_catalog, false,false);

        if(!empty($this->catalog_tree))
            unset($this->catalog_tree[0]);

        if(empty($this->catalog_tree)) {
            $retval = $this->templateFiller->fill("ContainerEmpty");
        } else {
            $retval = $this->templateFiller->fill("Container");
        }

        return $retval;
    }

    function createCategoriesSheet()
    {
        // remove root directory
        $base_level = $this->catalog_tree[1]['level'];

        $branches = array();
        $current_branch_id = 0;
        foreach ($this->catalog_tree as $key => $item)
        {
            if ($item['level'] == $base_level)
                $current_branch_id++;

            $item['level'] = $item['level'] - $base_level + 1;
            $branches[$current_branch_id][] = $item;
        }

        $this->total_branch_in_col = (int)(count($branches) / $this->total_cols);
        if($this->total_branch_in_col == 0)
            $this->total_branch_in_col = 1;

        $this->width_col = (int)(100 / $this->total_cols);
        $pointer = 0;

        $result = "";
        $this->content_one_row = "";
        foreach($branches as $branch)
        {
            foreach ($branch as $key => $item)
            {
                $this->category_level = $item['level'];

                $this->category_products = "";
                if($this->show_products)
                {
                    $filter = new CProductListFilter();
                    $params = $filter->getProductListParamsObject();
                    $params->category_id = $item['id'];
                    $products_ids = Catalog::getProductListByFilter($params, true);

                    if(!empty($products_ids))
                    {
                        foreach($products_ids as $product_key => $product_item)
                        {
                            $this->current_product = new CProductInfo($product_item['product_id']);
                            $this->category_products .= $this->templateFiller->fill("Product");
                        }
                    }
                }
                $this->current_category =  new CCategoryInfo($item['id']);
                $this->content_one_row .= $this->templateFiller->fill("Branch");
            }

            $pointer++;
            if($pointer == $this->total_branch_in_col)
            {
                $result .= $this->templateFiller->fill("Item");
                $pointer = 0;
                $this->content_one_row = "";
            }

        }
        return $result;

    }


    function getTag($tag, $arg_list = array())
    {
        $value = null;

        switch ($tag)
        {
            case 'Local_Items':
                $value = $this->createCategoriesSheet();
            break;

            case 'Local_CategoryBranch':
                $value = $this->content_one_row;
            break;

            case 'Local_CategoryLevel':
                $value = $this->category_level;
            break;

            case 'Local_CategoryProducts':
                $value = $this->category_products;
            break;

            case 'Local_WidthCol':
                $value = $this->width_col;
            break;

            case 'Local_TotalCol':
                $value = $this->total_cols;
            break;

            default:
                list($entity, $tag) = getTagName($tag);
                if ($entity == 'category' && is_object($this->current_category))
                {
                    $value = $this->current_category->getCategoryTagValue($tag);
                }

                if ($entity == 'product' && is_object($this->current_product))
                {
                    $value = $this->current_product->getProductTagValue($tag);
                }
            break;
        }
        return $value;
    }

}
?>