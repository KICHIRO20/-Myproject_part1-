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

class AddCustomAttributeAction extends AjaxAction
{
    /**
     * @ describe the function AddCustomAttribute->.
     */

     function AddCustomAttributeAction(){
     }


    function onAction()
    {


    	$AddCustomAttributeForm = array();
        $AddCustomAttributeForm = $_POST;


        $AddCustomAttributeForm["ErrorsArray"] = array();
        // convert to the universal user interface.
        $AddCustomAttributeForm['view_tag'] = _ml_ucfirst(_ml_strtolower($AddCustomAttributeForm['view_tag']));

        // check "Attribute Tag"
        if (isEmptyKey('view_tag', $AddCustomAttributeForm))
        {
            // a required attribute
        	$AddCustomAttributeForm["ErrorsArray"]["view_tag"] = new ActionMessage("ADDCUSTOM_001");
        }
        if (preg_match("/[^a-zA-Z0-9_]+/", $AddCustomAttributeForm['view_tag']))
        {
            // the attribute should require the rules of naming the PHP variables
        	$AddCustomAttributeForm["ErrorsArray"]["view_tag"] = new ActionMessage("ADDCUSTOM_002");
        }
        if ($AddCustomAttributeForm['product_type_id'] != "")
        {
            // there should be no copies of real custom attributes...
            $product_type = modApiFunc('Catalog', 'getProductType', $AddCustomAttributeForm['product_type_id']);
            foreach ($product_type['attr'] as $key => $value)
            {
                if (_ml_strtolower($AddCustomAttributeForm['view_tag']) === _ml_strtolower($key))
                {
                	$AddCustomAttributeForm["ErrorsArray"]["view_tag"] = new ActionMessage(array("ADDCUSTOM_005",'Product'.$AddCustomAttributeForm['view_tag'].'Custom'));
                	break;
                }
            }
            // ...             custom
            $custom_attributes = modApiFunc('Catalog', 'getTempCustomAttributes', $AddCustomAttributeForm['form_id']);
            if (is_array($custom_attributes) && array_key_exists($AddCustomAttributeForm['view_tag'], $custom_attributes))
            {
            	$AddCustomAttributeForm["ErrorsArray"]["view_tag"] = new ActionMessage(array("ADDCUSTOM_005", 'Product'.$AddCustomAttributeForm['view_tag'].'Custom'));
            }
        }
        else
        {
            // there should be no copies
            $custom_attributes = modApiFunc('Catalog', 'getTempCustomAttributes', $AddCustomAttributeForm['form_id']);
            if (is_array($custom_attributes) && array_key_exists($AddCustomAttributeForm['view_tag'], $custom_attributes))
            {
            	$AddCustomAttributeForm["ErrorsArray"]["view_tag"] = new ActionMessage(array("ADDCUSTOM_005", 'Product'.$AddCustomAttributeForm['view_tag'].'Custom'));
            }
        }

        // check "Attribute Tag"
        if (isEmptyKey('name', $AddCustomAttributeForm))
        {
            //
            // a required attribute
        	$AddCustomAttributeForm["ErrorsArray"]["name"] = new ActionMessage("ADDCUSTOM_003");
        }

        // check "Attribute Description"
        if (isEmptyKey('descr', $AddCustomAttributeForm))
        {
            //   a required attribute
        	$AddCustomAttributeForm["ErrorsArray"]["descr"] = new ActionMessage("ADDCUSTOM_004");
        }

        if (empty($AddCustomAttributeForm["ErrorsArray"]))
        {
            $input_type_name = "";
            $input_type_id = "";
            switch ($AddCustomAttributeForm['type_id'])
            {
            	case 1:
            	    $input_type_name = 'text';
            	    $input_type_id = 1;
            		break;
            	case 2:
            	    $input_type_name = 'textarea';
            	    $input_type_id = 2;
            		break;
            	case 3:
            	    $input_type_name = 'select';
                    $res = execQuery('SELECT_MAX_INPUT_TYPE_ID');
            	    $input_type_id = $res[0]['max_id'] + 1;
                    foreach($AddCustomAttributeForm['input_type_values'] as $key => $item)
                    {
                        if(empty($item)){
                            unset($AddCustomAttributeForm['input_type_values'][$key]);
                        }
                    }
            		break;
                default:
                    $input_type_name = 'text';
            }

            $AddCustomAttributeForm['input_type_values'] = isset($AddCustomAttributeForm['input_type_values']) ? $AddCustomAttributeForm['input_type_values'] : array();

            $attr = array(
                'id'                => null
               ,'pta_id'            => null
               ,'name'              => prepareHTMLDisplay($AddCustomAttributeForm['name'])
               ,'descr'             => prepareHTMLDisplay($AddCustomAttributeForm['descr'])
               ,'size'              => 70
               ,'min'               => 2
               ,'max'               => 255
               ,'view_tag'          => prepareHTMLDisplay($AddCustomAttributeForm['view_tag'])
               ,'group'             => array('id'=>6, 'name'=>'Custom Attributes', 'sort'=>6) // @ hard-coded
               ,'required'          => false
               ,'visible'           => true
               ,'default'           => ''
               ,'sort'              => 1
               ,'type'              => 'custom'
               ,'patt'              => null
               ,'patt_type'         => null
               ,'input_type_id'     => $input_type_id
               ,'input_type_name'   => $input_type_name
               ,'unit_type_value'   => null
               ,'vanishing'         => true
               ,'allow_html'        => ($input_type_id == 2)? 1:0
               ,'input_type_values' => $AddCustomAttributeForm['input_type_values']
    		   );
            modApiFunc('Catalog', 'addTempCustomAttribute', $AddCustomAttributeForm['form_id'], $attr);
            $AddCustomAttributeForm["close"] = true;
        }
        modApiFunc('Session', 'set', 'AddCustomAttributeForm', $AddCustomAttributeForm);
    }
}

?>