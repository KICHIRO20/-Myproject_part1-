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

class AddProductTypeAction extends AjaxAction
{
    /**
     * @ describe the function AddProductType->.
     */

    /**
     * Validates the user input. It checks "New Product Type Name".
     */
    function isValidTypeName($data)//, &$error_message_text)
    {
        $retval = (is_string($data) &&
                _ml_strlen(trim($data)) > 0 &&
                _ml_strlen(trim($data)) < 129);
        return $retval;
    }

    /**
     * Validates the user input. It checks "New Product Type Description".
     */
    function isValidTypeDescr($data)//, &$error_message_text)
    {
        $retval = (is_string($data) &&
                _ml_strlen(trim($data)) > 0 &&
                _ml_strlen(trim($data)) < 513);
        return $retval;
    }

    function onAction()
    {
    	global $application;
    	$request = $application->getInstance('Request');

    	$AddProductTypeForm = array();
        $AddProductTypeForm = $_POST;
        $AddProductTypeForm["ViewState"]["ErrorsArray"] = array();

        switch ($AddProductTypeForm['FormSubmitValue'])
        {
            case 'Reload':
            {
            }
            break;

            case 'DeleteCustomAttribute':
            {
                modApiFunc('Catalog', 'deleteTempCustomAttribute', $AddProductTypeForm['form_id'], $AddProductTypeForm['custom_attribute']);
            }
            break;

            case 'Save':
        	{
                $product_type = modApiFunc('Catalog', 'getProductType', 1);
                $base_product_type = $product_type['attr'];
                $custom_attributes = modApiFunc('Catalog', 'getTempCustomAttributes', $AddProductTypeForm['form_id']);

                // delete all base custom attributes
                //foreach ($base_product_type as $view_tag => $attr)
                //{
                //    if ($attr['type'] == 'custom')
                //    {
                //    	unset($base_product_type[$view_tag]);
                //    }
                //}

                // add only temporary custom attributes
                if (is_array($custom_attributes)) {
                    $base_product_type = $base_product_type + $custom_attributes;
                }
                $product_type = array();
                $nErrors = 0;
                foreach ($base_product_type as $view_tag => $attr)
                {
                    if (isset($AddProductTypeForm[$view_tag]['default']))
                    {
                    	$base_product_type[$view_tag]['default'] = modApiFunc("Localization", "FormatStrToFloat", $AddProductTypeForm[$view_tag]['default'], $base_product_type[$view_tag]["patt_type"]);
                    	if (isset($AddProductTypeForm[$view_tag]['unit_type_value']))
                    	{
                    		$base_product_type[$view_tag]['unit_type_value'] = $AddProductTypeForm[$view_tag]['unit_type_value'];
                    	}
                    }
                	$base_product_type[$view_tag]['visible'] = isset($AddProductTypeForm[$view_tag]['visible']) ? true : false;
//                    if ($attr['type'] != 'artificial' && $attr['required'] && isEmptyKey('default', $AddProductTypeForm[$view_tag]))
//                    {
//                        $AddProductTypeForm["ViewState"]["ErrorsArray"][] = new ActionMessage(array("error.required", $attr['name']));
//                    	$nErrors++;
//                    }
                }


                if (!$this->isValidTypeName($AddProductTypeForm['TypeName']))
                {
                    // a product type is not inputted
                    $AddProductTypeForm["ViewState"]["ErrorsArray"]['TypeName'] = new ActionMessage("ADDPRDTYPE_001");
                	$nErrors++;
                }
                if (!$this->isValidTypeDescr($AddProductTypeForm['TypeDescr']))
                {
                    // a product type description is not defined
                    $AddProductTypeForm["ViewState"]["ErrorsArray"]['TypeDescr'] = new ActionMessage("ADDPRDTYPE_002");
                	$nErrors++;
                }
                if ($nErrors == 0)
                {
            		$base_product_type['SalePrice']['visible'] = true;
            		$base_product_type['TypeName'] = $AddProductTypeForm['TypeName'];
            		$base_product_type['TypeDescr'] = $AddProductTypeForm['TypeDescr'];
            		modApiFunc('Catalog', 'addProductType', $base_product_type);
            		modApiFunc('Catalog', 'removeTempCustomAttributes', $AddProductTypeForm['form_id']);
                        $request = new Request();
                        $request->setView('ManageProductTypes');
            		$application->redirect($request);
                }
        	}
    		break;
        }
        modApiFunc('Session', 'set', 'AddProductTypeForm', $AddProductTypeForm);
    }
}
?>