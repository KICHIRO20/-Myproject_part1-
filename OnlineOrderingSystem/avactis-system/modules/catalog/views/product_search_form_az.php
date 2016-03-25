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
 * Showing search form
 *
 * @author Sergey Kulitsky
 * @package Catalog
 * @access public
 */
class ProductSearchForm
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**
     * The viewer constructor
     */
    function ProductSearchForm()
    {
        // initializing the template filler
        $this -> mTmplFiller = new TmplFiller();

        $this -> Filter = array();
        if (modApiFunc('Session', 'is_set', 'SearchProductFormFilter'))
            $this -> Filter = modApiFunc('Session', 'get', 'SearchProductFormFilter');

        $this -> _template_dir = 'catalog/product_search_form/';
    }

    /**
     * Returns the Product Group Edit view.
     */
    function output()
    {
        global $application;

        $template_contents = array(
            'OverFlowText'       => $this -> outputOverFlowText(),
            'PatternValue'       => prepareHTMLDisplay($this -> getPatternValue()),
            'AllChecked'         => $this -> getPatternTypeChecked('all'),
            'AnyChecked'         => $this -> getPatternTypeChecked('any'),
            'ExactlyChecked'     => $this -> getPatternTypeChecked('exactly'),
            'InNameChecked'      => $this -> getSearchInChecked('in_name'),
            'InSKUChecked'       => $this -> getSearchInChecked('in_sku'),
            'InDescrChecked'     => $this -> getSearchInChecked('in_descr'),
            'InDetDescrChecked'  => $this -> getSearchInChecked('in_det_descr'),
            'InTitleChecked'     => $this -> getSearchInChecked('in_title'),
            'InKeywordsChecked'  => $this -> getSearchInChecked('in_keywords'),
            'InMetaDescrChecked' => $this -> getSearchInChecked('in_meta_descr'),
            'InIDChecked'        => $this -> getSearchInChecked('in_id'),
            'RecursiveChecked'   => ((isset($this -> Filter['recursive']))
                                    ? 'checked="checked"' : ''),
            'ManufacturerList'   => $this -> getManufacturerList(),
            'PriceMinValue'      => @$this -> Filter['price_min'],
            'PriceMaxValue'      => @$this -> Filter['price_max'],
            'CategoryList'       => $this -> getSubcategoriesList(),
            'isFormActive'       => empty($this -> Filter) ? 'N' : 'Y'
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   $this -> _template_dir,
                   'container.tpl.html',
                   array()
               );
    }

    /**
     * Outputs overfill text if any
     */
    function outputOverFlowText()
    {
        if (isset($this -> Filter['overflow'])
            && $this -> Filter['overflow'])
            return $this -> mTmplFiller -> fill(
                       $this -> _template_dir,
                       'overflow-text.tpl.html',
                       array()
                   );
        return '';
    }

    /**
     * Returns the pattern value
     */
    function getPatternValue()
    {
        return @$this -> Filter['pattern'];
    }

    /**
     * Returns if search in field checked
     */
    function getSearchInChecked($field)
    {
        $criteria = isset($this -> Filter[$field]);
        $criteria = $criteria || !@$this -> Filter['pattern'];

        return (($criteria) ? ' checked="checked"' : '');
    }

    /**
     * Returns if pattern type is checked
     */
    function getPatternTypeChecked($type)
    {
        $criteria = (@$this -> Filter['pattern_type'] == $type
                    || ($type == 'all' && !@$this -> Filter['pattern']));

        return (($criteria) ? ' checked="checked"' : '');
    }

    /**
     * Outputs subcategories as options for 'Category' select box
     */
    function getSubcategoriesList()
    {
        $cat_list = modApiFunc('Catalog', 'getSubcategoriesFullListWithParent',
                               1, false);

        $output = '';
        if (is_array($cat_list) && !empty($cat_list))
            foreach($cat_list as $key => $value)
            {
                $spaces = "";
                for($i = 0; $i < $value['level']; $i++)
                    $spaces .= "&nbsp;&nbsp;";
                $output .= '<option value="' . $value['id'] . '"';
                if ((isset($this -> Filter['category'])
                     && $value['id'] == @$this -> Filter['category'])
                    || (!isset($this -> Filter['category'])
                     && $value['id'] == modApiFunc('CProductListFilter', 'getCurrentCategoryId')))
                    $output .= ' selected="selected"';
                $output .= '>';
                $output .= $spaces . prepareHTMLDisplay($value['name']);
                $output .= "</option>";
            }

        return $output;
    }

    /**
     * Outputs manufacturers as options for Manufacturer select box
     */
    function getManufacturerList()
    {
        $man_list = modApiFunc('Manufacturers',
                               'getManufacturerProductAttributeValues',
                               false, true);

        $output = '';
        if (is_array($man_list) && !empty($man_list))
            foreach($man_list as $v)
                $output .= '<option value="' . $v['id'] . '"' .
                           ((@$this -> Filter['manufacturer'] == $v['id'])
                               ? ' selected="selected"' : '') . '>' .
                           prepareHTMLDisplay($v['value']) . '</option>';

        return $output;
    }

    /**
     * Processes the tags
     */
    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this -> _Template_Contents);
    }

    var $mTmplFiller;
    var $Filter;
    var $_Template_Contents;
    var $_template_dir;
}
?>