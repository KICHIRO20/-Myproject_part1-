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

class UpdateCustomAttribute extends AjaxAction
{
    /**
     * @ describe the function UpdateCustomAttribute->.
     */
    function onAction()
    {
    	$EditCustomAttributeForm = array();
        $EditCustomAttributeForm = $_POST;
        $EditCustomAttributeForm["ErrorsArray"] = array();

        // check "Attribute Name"
        if (isEmptyKey('name', $EditCustomAttributeForm))
        {
        	// a required attribute
        	$EditCustomAttributeForm["ErrorsArray"]["name"] = new ActionMessage("ADDCUSTOM_003");
        }

        // check "Attribute Description"
        if (isEmptyKey('descr', $EditCustomAttributeForm))
        {
        	//  a required attribute
        	$EditCustomAttributeForm["ErrorsArray"]["descr"] = new ActionMessage("ADDCUSTOM_004");
        }

        if (empty($EditCustomAttributeForm["ErrorsArray"]))
        {
            $EditCustomAttributeForm['input_type_values'] = isset($EditCustomAttributeForm['input_type_values']) ? $EditCustomAttributeForm['input_type_values'] : array() ;
            if(!empty($EditCustomAttributeForm['input_type_values']))
            {
                foreach($EditCustomAttributeForm['input_type_values'] as $key => $item){
                    if(empty($item)){
                        unset($EditCustomAttributeForm['input_type_values'][$key]);
                    }
                }
            }

        	$custom_attributes = modApiFunc('Catalog', 'getTempCustomAttributes', $EditCustomAttributeForm['form_id']);
        	$orig_attr = $custom_attributes[$EditCustomAttributeForm['view_tag']];
            $attr = array(
                'id'                => $orig_attr['id']
               ,'pta_id'            => $orig_attr['pta_id']
               ,'name'              => prepareHTMLDisplay($EditCustomAttributeForm['name'])
               ,'descr'             => prepareHTMLDisplay($EditCustomAttributeForm['descr'])
               ,'size'              => $orig_attr['size']
               ,'min'               => $orig_attr['min']
               ,'max'               => $orig_attr['max']
               ,'view_tag'          => $orig_attr['view_tag']
               ,'group'             => $orig_attr['group']
               ,'required'          => $orig_attr['required']
               ,'visible'           => $orig_attr['visible']
               ,'default'           => $orig_attr['default']
               ,'sort'              => $orig_attr['sort']
               ,'type'              => $orig_attr['type']
               ,'patt'              => $orig_attr['patt']
               ,'patt_type'         => $orig_attr['patt_type']
               ,'input_type_id'     => $orig_attr['input_type_id']
               ,'input_type_name'   => $orig_attr['input_type_name']
               ,'unit_type_value'   => $orig_attr['unit_type_value']
               ,'vanishing'         => $orig_attr['vanishing']
               ,'input_type_values' => $EditCustomAttributeForm['input_type_values']
    		   );
            modApiFunc('Catalog', 'updateTempCustomAttribute', $EditCustomAttributeForm['form_id'], $attr);
            $EditCustomAttributeForm["close"] = true;
        }

        modApiFunc('Session', 'set', 'EditCustomAttributeForm', $EditCustomAttributeForm);
    }
}

?>