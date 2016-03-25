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
loadModuleFile('catalog/abstract/product_class.php');

/**
 * Catalog module.
 * Editing a group of products
 *
 * @author Sergey Kulitsky
 * @package Catalog
 * @access public
 */
class ProductGroupEdit
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**
     * The viewer constructor
     */
    function ProductGroupEdit()
    {
        // loading the prototypes of form fields
        loadCoreFile('html_form.php');

        // getting the list of attributes to show
        $settings = modApiFunc('Settings', 'getParamListByGroup',
                               'PRODUCT_GROUP_EDIT', SETTINGS_WITH_DESCRIPTION);
        $this -> _attrs = array('ID' => 'YES');
        if (is_array($settings))
            foreach($settings as $v)
            {
                if ($v['param_name'] == 'CTL_PGE_TABULATION')
                    $this -> _tabbing = $v['param_current_value'];
                elseif ($v['param_current_value'] == 'YES')
                    $this -> _attrs[$v['param_name']] = 'YES';
            }

        // initializing the template filler
        $this -> mTmplFiller = new TmplFiller();

        // filling the headermap
        $this -> _headermap = array(
            'ID'                       => 'PRD_ID_NAME',
            'CTL_PGE_NAME'             => 'PRD_NAME_NAME',
            'CTL_PGE_SALEPRC'          => 'PRD_SALEPRC_NAME',
            'CTL_PGE_LISTPRC'          => 'PRD_LISTPRC_NAME',
            'CTL_PGE_QUINSTOCK'        => 'PRD_QUINSTOCK_NAME',
            'CTL_PGE_LOWLEV'           => 'PRD_LOWLEV_NAME',
            'CTL_PGE_SKU'              => 'PRD_SKU_NAME',
            'CTL_PGE_QUINORDER'        => 'PRD_QUINORDER_NAME',
            'CTL_PGE_AVAIL'            => 'PRD_AVAIL_NAME',
            'CTL_PGE_TAXCLASS'         => 'PRD_TAX_CLASS_NAME',
            'CTL_PGE_MANUFACTURER'     => 'PRD_MANUFACTURER_NAME',
            'CTL_PGE_SHIPPRC'          => 'PRD_SHIPPRC_NAME',
            'CTL_PGE_HANDPRC'          => 'PRD_HANDRC_NAME',
            'CTL_PGE_WEIGHT'           => 'PRD_WEIGHT_NAME',
            'CTL_PGE_FREESHIP'         => 'PRD_FREESHIP_NAME',
            'CTL_PGE_NEEDSHIP'         => 'PRD_NEEDSHIP_NAME',
            'CTL_PGE_CUSTOMER_REVIEWS' => 'PRD_CUSTOMER_REVIEWS_NAME'
        );

        // filling the attribute map
        $this -> _attrmap = array(
            'ID'                       => 'ID',
            'CTL_PGE_NAME'             => 'Name',
            'CTL_PGE_SALEPRC'          => 'SalePrice',
            'CTL_PGE_LISTPRC'          => 'ListPrice',
            'CTL_PGE_QUINSTOCK'        => 'QuantityInStock',
            'CTL_PGE_LOWLEV'           => 'LowStockLevel',
            'CTL_PGE_SKU'              => 'SKU',
            'CTL_PGE_QUINORDER'        => 'MinQuantity',
            'CTL_PGE_AVAIL'            => 'Available',
            'CTL_PGE_TAXCLASS'         => 'TaxClass',
            'CTL_PGE_MANUFACTURER'     => 'Manufacturer',
            'CTL_PGE_SHIPPRC'          => 'PerItemShippingCost',
            'CTL_PGE_HANDPRC'          => 'PerItemHandlingCost',
            'CTL_PGE_WEIGHT'           => 'Weight',
            'CTL_PGE_FREESHIP'         => 'FreeShipping',
            'CTL_PGE_NEEDSHIP'         => 'NeedShipping',
            'CTL_PGE_CUSTOMER_REVIEWS' => 'CustomerReviews'
        );
    }

    /**
     * Returns the Product Group Edit view.
     */
    function output()
    {
        global $application;

        $this -> _Prod_IDs = array();

        // getting the list of products
        if (modApiFunc('Session', 'is_set', 'PGE_PRODUCTS'))
            $this -> _Prod_IDs = modApiFunc('Session', 'get', 'PGE_PRODUCTS');

        // rebuild the Product ids to be usual non-associated array
        $this -> _Prod_IDs = array_values($this -> _Prod_IDs);

        // checking the product ids...
        foreach($this -> _Prod_IDs as $k => $v)
            if (!modApiFunc('Catalog', 'isCorrectProductId', $v))
                unset($this -> _Prod_IDs[$k]);

        // getting information if parent window should be reloaded
        $this -> _ReloadParentWindow = '';
        if (modApiFunc('Session', 'is_set', 'PGE_RELOAD_PARENT'))
        {
            $this -> _ReloadParentWindow = modApiFunc('Session', 'get',
                                                      'PGE_RELOAD_PARENT');
            modApiFunc('Session', 'un_set', 'PGE_RELOAD_PARENT');
        }

        // getting errors
        $this -> _Errors = '';
        if (modApiFunc('Session', 'is_set', 'PGE_ERRORS'))
        {
            $this -> _Errors = modApiFunc('Session', 'get', 'PGE_ERRORS');
            modApiFunc('Session', 'un_set', 'PGE_ERRORS');
        }

        // getting posted data
        $this -> _Posted_Data = array();
        if (modApiFunc('Session', 'is_set', 'PGE_POSTED_DATA'))
        {
            $this -> _Posted_Data = modApiFunc('Session', 'get',
                                               'PGE_POSTED_DATA');
            modApiFunc('Session', 'un_set', 'PGE_POSTED_DATA');
        }

        $template_contents = array(
            'EditProductHeader' => $this -> outputHeader(),
            'EditProductList'   => $this -> outputProductList(),
            'ResultMessage'     => $this -> outputResultMessage(),
            'UpdateButton'      => $this -> outputUpdateButton(),
//            'ReloadParentCode'  => $this -> outputReloadParentCode(),
            'FormAction'        => '',
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'catalog/product_group_edit/',
                   'container.tpl.html',
                   array()
               );
    }

    /**
     * Outputs the result message
     */
    function outputResultMessage()
    {
        global $application;

        if (!is_array($this -> _Errors) && $this -> _Errors != 'Success')
            return '';

        $template_contents = array(
            'ResultMessage'      => (($this -> _Errors == 'Success')
                                    ? getMsg('CTL', 'CTL_PGE_MSG_SUCCESS')
                                    : getMsg('CTL', 'CTL_PGE_MSG_ERROR')),
            'ResultMessageColor' => (($this -> _Errors == 'Success')
                                    ? 'green'
                                    : 'red')
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'catalog/product_group_edit/',
                   'result-message.tpl.html',
                   array()
               );
    }

    /**
     * Outputs reload parent js code if needed
     */
    function outputReloadParentCode()
    {
        global $application;

        if ($this -> _ReloadParentWindow != 1)
            return '';

        return $this -> mTmplFiller -> fill(
                   'catalog/product_group_edit/',
                   'reload-parent-js.tpl.html',
                   array()
               );
    }

    /**
     * Outputs the update button if the list of products is not empty
     */
    function outputUpdateButton()
    {
        if (empty($this -> _Prod_IDs))
            return '';

        return $this -> mTmplFiller -> fill(
                   'catalog/product_group_edit/',
                   'update-button.tpl.html',
                   array()
               );
    }

    /**
     * Outputs the header of the form
     */
    function outputHeader()
    {
        global $application;

        $output = '';
        if (!is_array($this -> _attrs))
            return $output;

        // output error column
        if (is_array($this -> _Errors) && !empty($this -> _Errors))
            $output .= $this -> mTmplFiller -> fill(
                           'catalog/product_group_edit/',
                           'header-error.tpl.html',
                           array()
                       );

        foreach($this -> _attrs as $k => $v)
        {
            if ($v != 'YES')
                continue;

            $template_contents = array(
                'HeaderName' => getMsg('SYS', @$this -> _headermap[$k])
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $output .= $this -> mTmplFiller -> fill(
                           'catalog/product_group_edit/',
                           'header-cell.tpl.html',
                           array()
                       );
        }

        return $output;
    }

    /**
     * Outputs the list of products
     */
    function outputProductList()
    {
        global $application;

        if (empty($this -> _Prod_IDs))
        {
            $template_contents = array(
                'ColumnNumber' => count($this -> _attrs) +
                                  (empty($this -> _Errors) ? 0 : 1)
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            return $this -> mTmplFiller -> fill(
                       'catalog/product_group_edit/',
                       'empty.tpl.html',
                       array()
                   );
        }

        $output = '';
        foreach($this -> _Prod_IDs as $k => $v)
        {
            $prod_info = modApiFunc('Catalog_ProdInfo_Base',
                                    'getProductInfo', $v);

            $prod_attrs = array();

            if (is_array($prod_info))
                foreach($prod_info as $group)
                    foreach($group['attr'] as $attr)
                        $prod_attrs[$attr['view_tag']] = $attr;
            unset($prod_info);

            $template_contents = array(
                'ProductData'      => $this -> outputProduct($k, $v, $prod_attrs),
                'ProductError'     => $this -> outputError($k, $v),
                'ProductErrorCell' => $this -> outputErrorCell($k, $v)
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $output .= $this -> mTmplFiller -> fill(
                           'catalog/product_group_edit/',
                           'product.tpl.html',
                           array()
                       );
        }

        return $output;
    }

    /**
     * Outputs error cell
     */
    function outputErrorCell($index, $prod_id)
    {
        global $application;

        // if no errors -> output empty string
        if (!is_array($this -> _Errors) || empty($this -> _Errors))
            return '';

        $template_contents = array(
            'LineBgColor' => (($index % 2) ? '#EEEEEE' : '#FFFFFF'),
            'CellError'   => ((!isset($this -> _Errors[$prod_id])
                               || !is_array($this -> _Errors[$prod_id])
                               || empty($this -> _Errors[$prod_id]))
                             ? ''
                             : '<img id="img_error_' . $prod_id . '" border="0" src="images/plus.gif" onclick="showError(' . $prod_id . ')" />')
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'catalog/product_group_edit/',
                   'product-cell-error.tpl.html',
                   array()
               );
    }

    /**
     * Outputs the product errors
     */
    function outputError($index, $prod_id)
    {
        global $application;

        if (!isset($this -> _Errors[$prod_id])
            || !is_array($this -> _Errors[$prod_id])
            || empty($this -> _Errors[$prod_id]))
            return '';

        $error_msg = join('<br />', $this -> _Errors[$prod_id]);

        $template_contents = array(
            'ColumnNumber'        => count($this -> _attrs) +
                                     (empty($this -> _Errors) ? 0 : 1),
            'ProductErrorMessage' => $error_msg,
            'Product_ID'          => $prod_id,
            'LineBgColor'         => (($index % 2) ? '#EEEEEE' : '#FFFFFF')
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'catalog/product_group_edit/',
                   'product-error.tpl.html',
                   array()
               );
    }

    /**
     * Outputs the product data
     */
    function outputProduct($index, $prod_id, $prod_attrs)
    {
        global $application;

        $output = '';
        $v_index = 0;
        foreach($this -> _attrs as $k => $v)
        {
            $template_contents = array(
                'LineBgColor' => (($index % 2) ? '#EEEEEE' : '#FFFFFF'),
                'CellData'    => $this -> outputAttr($prod_id, @$prod_attrs[$this -> _attrmap[$k]], $index, $v_index)
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $output .= $this -> mTmplFiller -> fill(
                           'catalog/product_group_edit/',
                           'product-cell.tpl.html',
                           array()
                       );
            $v_index++;
        }

        return $output;
    }

    /**
     * Output the attribute data
     */
    function outputAttr($prod_id, $attr, $index, $v_index)
    {
        if (!$attr['visible'])
            return getMsg('CTL', 'CTL_PGE_NA');

        $output = '';

        // setting the style and value depending on the error
        $style = '';
        $value = @$attr['value'];
        if (isset($this -> _Errors[$prod_id]) && isset($this -> _Errors[$prod_id][$attr['view_tag']]))
        {
            $style = ' style="border: 1px solid; border-color: red;"';
            if (isset($this -> _Posted_Data[$prod_id][$attr['view_tag']]))
                $value = $this -> _Posted_Data[$prod_id][$attr['view_tag']];
        }

        // tabbing
        $tabbing = ($this -> _tabbing == 'RIGHT')
                   ? count($this -> _attrs) * $index + $v_index
                   : count($this -> _Prod_IDs) * $v_index + $index;
        $tabbing = ' tabindex="' . $tabbing . '"';

        switch($attr['input_type_name'])
        {
            case 'read-only':
                $output = prepareHTMLDisplay($value);
                break;

            case 'select':
                $values = array();
                foreach($attr['input_type_values'] as $k => $v)
                    $values[] = array('value' => $k, 'contents' => $v);

                $output = HtmlForm :: genDropdownSingleChoice(array(
                              'onChange' => '',
                              'class' => 'input-sm',
                              'select_name' => 'posted_data[' . $prod_id .
                                               '][' . $attr['view_tag'] . ']',
                              'values' => $values,
                              'selected_value' => $value
                          ), $style . $tabbing);
                break;

            case 'text':
                $size = 40;
                if ($attr['size'] < 50)
                    $size = 8;
                if ($attr['size'] < 20)
                    $size = 5;
                $output = '<input class="form-control input-sm" type="text"' .
                          HtmlForm :: genInputTextField(
                              $attr['max'],
                              'posted_data[' . $prod_id . '][' .
                              $attr['view_tag'] . ']',
                              $size,
                              $value
                          ) .
                          $style . $tabbing . ' />';
                break;

            default:
                $output = getMsg('CTL', 'CTL_PGE_NA');;
        }

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
    var $_Errors;
    var $_Posted_Data;
    var $_Prod_IDs;
    var $_ReloadParentWindow;
    var $_Template_Contents;
    var $_headermap;
    var $_attrmap;
    var $_attrs;
    var $_tabbing;
}
?>