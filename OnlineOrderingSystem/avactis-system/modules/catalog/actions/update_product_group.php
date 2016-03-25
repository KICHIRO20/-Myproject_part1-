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
 * @package Catalog
 * @author Sergey Kulitsky
 *
 */

class UpdateProductGroup extends AjaxAction
{
    function UpdateProductGroup()
    {
    }

    function onAction()
    {
        global $application;
        $MessageResources = &$application->getInstance('MessageResources');

        // getting the posted data from the request
        $request = &$application -> getInstance('Request');
        $posted_data = $request -> getValueByKey('posted_data');

        // array of errors if any
        // structure: $errors[$prod_id][$view_tag] = $error_descr
        $errors = array();

        if (!is_array($posted_data))
            $posted_data = array();

        // $prod_ids - array of product ids
        $prod_ids = array();

        // if it is needed to reload the parent window
        $mustReloadParent = false;

        foreach($posted_data as $prod_id => $posted_attrs)
        {
            // saving the $prod_id in the $prod_ids
            $prod_ids[$prod_id] = $prod_id;

            // getting product type by product id
            $prod_type_id = modApiFunc('Catalog', 'getBaseProductInfo',
                                          $prod_id, 'p_type_id');

            // getting product type info
            $prod_type_info = modApiFunc('Catalog', 'getProductType',
                                         $prod_type_id);

            // prod_update_info - array with attrs to update
            $prod_update_info = array();

            // array of errors for the product
            $prod_errors = array();

            // cycle by product type attrs
            foreach($prod_type_info['attr'] as $view_tag => $attr)
            {
                // skip invisible attributes
                if (!$attr['visible'])
                    continue;

                // skip attributes which were not posted
                if (!isset($posted_attrs[$view_tag]))
                    continue;

                // validating posted data
                // if required
                if ($attr['required'] && !$posted_attrs[$view_tag])
                    $prod_errors[$view_tag] = $MessageResources -> getMessage(
                        new ActionMessage(array('error.required',
                                                $attr['name']))
                    );

                // formatting
                if ($posted_attrs[$view_tag])
                {
                    switch($attr['patt_type'])
                    {
                        case 'string128':
                            if (_ml_strlen($posted_attrs[$view_tag]) > 128)
                                $prod_errors[$view_tag] = $attr['name'] . ': ' .
                                                          getMsg('SYS', 'PRDADD_008');
                            break;

                        case 'string256':
                            if (_ml_strlen($posted_attrs[$view_tag]) > 256)
                                $prod_errors[$view_tag] = $attr['name'] . ': ' .
                                                          getMsg('SYS', 'PRDADD_009');
                            break;

                        case 'string512':
                            if (_ml_strlen($posted_attrs[$view_tag]) > 512)
                                $prod_errors[$view_tag] = $attr['name'] . ': ' .
                                                          getMsg('SYS', 'PRDADD_010');
                            break;

                        case 'string1024':
                            if (_ml_strlen($posted_attrs[$view_tag]) > 1024)
                                $prod_errors[$view_tag] = $attr['name'] . ': ' .
                                                          getMsg('SYS', 'PRDADD_007');
                            break;

                        case 'item':
                            if (!is_numeric($posted_attrs[$view_tag]))
                                $prod_errors[$view_tag] = $attr['name'] . ': ' .
                                                          getMsg('SYS', 'ITEM_FIELD');
                            else
                                $posted_attrs[$view_tag] = intval($posted_attrs[$view_tag]);
                            break;

                        case 'weight':
                        case 'currency':
                            if (!is_numeric($posted_attrs[$view_tag]))
                            {
                                $prod_errors[$view_tag] = $attr['name'] . ': ' .
                                    $MessageResources -> getMessage(
                                        new ActionMessage(array('CURRENCY_FIELD',
                                                          '67.78', '5.00', '123.45'))
                                    );
                            }
                            else
                            {
                                $tmp = floatval($posted_attrs[$view_tag]);
                                $tmp = explode('.', $tmp);
                                $posted_attrs[$view_tag] = $tmp[0] . '.' . _ml_substr(@$tmp[1] . '00', 0, 2);
                            }
                            break;
                    }
                }

                // if has pattern
                if (!isset($prod_errors[$view_tag]) && $posted_attrs[$view_tag] && isset($attr['patt']))
                {
                    // stripping new lines
                    $stripped_data = str_replace("\n", '', $posted_attrs[$view_tag]);
                    if (!preg_match($attr['patt'], $stripped_data))
                        $prod_errors[$view_tag] = $MessageResources -> getMessage(
                            new ActionMessage(array('error.wrongPattern',
                                                    $attr['name'],
                                                    $attr['patt_type']))
                        );
                }

                // fill update info if no error
                if (!isset($prod_errors[$view_tag]))
                    $prod_update_info[$view_tag] = modApiFunc('Localization', 'FormatStrToFloat', $posted_attrs[$view_tag], $attr['patt_type']);
            }

            // checking product name if exists
            if (isset($posted_attrs['Name']))
            {
                // validating the product name
                if (!is_string($posted_attrs['Name'])
                    || _ml_strlen(trim($posted_attrs['Name'])) <= 0
                    || _ml_strlen(trim($posted_attrs['Name'])) >= 257)
                    $prod_errors['Name'] = $MessageResources -> getMessage(
                        new ActionMessage("PRDADD_004")
                    );
                else
                    $prod_update_info['Name'] = $posted_attrs['Name'];
            }

            // if product has an error add them to total errors
            if (!empty($prod_errors))
                $errors[$prod_id] = $prod_errors;

            // in any case save the correct data if any
            if (!empty($prod_update_info))
            {
                modApiFunc('Catalog', 'updateProductInfo',
                           $prod_id, $prod_type_id, $prod_update_info);
                $mustReloadParent = true;
            }
        }

        // all is done, saving the errors and the posted data if any error
        if (!empty($errors))
        {
            modApiFunc('Session', 'set', 'PGE_ERRORS', $errors);
            modApiFunc('Session', 'set', 'PGE_POSTED_DATA', $posted_data);
        }
        else
        {
            // otherwise telling everything is Ok
            modApiFunc('Session', 'set', 'PGE_ERRORS', 'Success');
            // clearing past data if any
            if (modApiFunc('Session', 'is_set', 'PGE_POSTED_DATA'))
                modApiFunc('Session', 'un_set', 'PGE_POSTED_DATA');
        }

        // saving the $prod_ids array in session for the group edit form
        modApiFunc('Session', 'set', 'PGE_PRODUCTS', $prod_ids);

        // saving in session if parent window should be reloaded
        if ($mustReloadParent)
            modApiFunc('Session', 'set', 'PGE_RELOAD_PARENT', 1);

        // final redirect back to product group edit form
        $redirect = new Request();
        $redirect -> setView('CatalogProductGroupEdit');

        $application -> redirect($redirect);
    }
}