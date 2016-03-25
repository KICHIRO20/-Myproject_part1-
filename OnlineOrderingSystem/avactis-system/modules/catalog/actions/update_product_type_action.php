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

class UpdateProductTypeAction extends AjaxAction
{

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

    /**
     * @ describe the function UpdateProductType->.
     */
    function onAction()
    {
    	global $application;
    	$request = $application->getInstance('Request');

    	$EditProductTypeForm = array();
        $EditProductTypeForm = $_POST;
        $EditProductTypeForm["ViewState"]["ErrorsArray"] = array();
        $form_id = $EditProductTypeForm['form_id'];
        $type_id = $EditProductTypeForm['type_id'];

        switch ($EditProductTypeForm['FormSubmitValue'])
        {
            case 'Reload':
            {
                modApiFunc('Session', 'set', 'EditProductTypeForm', $EditProductTypeForm);
            }
            break;

            case 'DeleteCustomAttribute':
            {
                modApiFunc('Catalog', 'deleteTempCustomAttribute', $form_id, $EditProductTypeForm['custom_attribute']);
                modApiFunc('Session', 'set', 'EditProductTypeForm', $EditProductTypeForm);
            }
            break;

        	case 'Save':
        	{
                $product_type = modApiFunc('Catalog', 'getProductType', $type_id);
                $base_product_type = $product_type['attr'];
                $custom_attributes = modApiFunc('Catalog', 'getTempCustomAttributes', $form_id);

                // delete all base custom attributes
                foreach ($base_product_type as $view_tag => $attr)
                {
                    if ($attr['type'] == 'custom')
                    {
                    	unset($base_product_type[$view_tag]);
                    }
                }

                // add only temporary custom attributes
                if (is_array($custom_attributes)) {
                    $base_product_type = $base_product_type + $custom_attributes;
                }
                $product_type = array();
                $nErrors = 0;
                foreach ($base_product_type as $view_tag => $attr)
                {
                    if (isset($EditProductTypeForm[$view_tag]['default']))
                    {
                    	$base_product_type[$view_tag]['default'] = modApiFunc("Localization", "FormatStrToFloat", $EditProductTypeForm[$view_tag]['default'], $base_product_type[$view_tag]["patt_type"]);
                    	if (isset($EditProductTypeForm[$view_tag]['unit_type_value']))
                    	{
                    		$base_product_type[$view_tag]['unit_type_value'] = $EditProductTypeForm[$view_tag]['unit_type_value'];
                    	}
                    }
                	$base_product_type[$view_tag]['visible'] = isset($EditProductTypeForm[$view_tag]['visible']) ? true : false;
//                    if ($attr['type'] != 'artificial' && $attr['required'] && isEmptyKey('default', $EditProductTypeForm[$view_tag]))
//                    {
//                        $EditProductTypeForm["ViewState"]["ErrorsArray"][] = new ActionMessage(array("error.required", $attr['name']));
//                    	$nErrors++;
//                    }
                }

                if (!$this->isValidTypeName($EditProductTypeForm['TypeName']))
                {
                    // a product type is not inputted
                    $EditProductTypeForm["ViewState"]["ErrorsArray"]['TypeName'] = new ActionMessage("ADDPRDTYPE_001");
                	$nErrors++;
                }
                if (!$this->isValidTypeDescr($EditProductTypeForm['TypeDescr']))
                {
                    // a product type description is not defined
                    $EditProductTypeForm["ViewState"]["ErrorsArray"]['TypeDescr'] = new ActionMessage("ADDPRDTYPE_002");
                	$nErrors++;
                }
                if ($nErrors == 0)
                {
            		$base_product_type['SalePrice']['visible'] = true;
            		$base_product_type['TypeName'] = $EditProductTypeForm['TypeName'];
            		$base_product_type['TypeDescr'] = $EditProductTypeForm['TypeDescr'];
            		modApiFunc('Catalog', 'updateProductType', $type_id, $base_product_type);
            		modApiFunc('Catalog', 'removeTempCustomAttributes', $form_id);
                    modApiFunc('Session', 'un_Set', 'EditProductTypeForm');
                        $request = new Request();
                        $request->setView('ManageProductTypes');
                        $application->redirect($request);
                }
                else
                {
                    modApiFunc('Session', 'set', 'EditProductTypeForm', $EditProductTypeForm);
                }
        	}
    		break;
        }
    }
}
?>