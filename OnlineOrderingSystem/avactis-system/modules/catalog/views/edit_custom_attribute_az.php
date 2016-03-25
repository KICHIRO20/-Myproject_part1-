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

class EditCustomAttribute
{

    /**
     * @ describe the function EditCustomAttribute->.
     */
    function EditCustomAttribute()
    {
        if(modApiFunc("Session", "is_Set", "EditCustomAttributeForm"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'EditCustomAttributeForm');
        }
        else
        {
        	$this->initFormData();
        }
    }

    function initFormData()
    {
    	global $application;
    	$request = $application->getInstance('Request');

    	$this->form['form_id'] = $request->getValueByKey('form_id');
    	$this->form['product_type_id'] = $request->getValueByKey('product_type_id');
    	$view_tag = $request->getValueByKey('view_tag');

    	$custom_attributes = modApiFunc('Catalog', 'getTempCustomAttributes', $this->form['form_id']);
    	$this->form = array_merge($this->form, $custom_attributes[$view_tag]);

        $this->id_input_type_value_not_selected = null;

        if(isset($this->form['input_type_values']) && !empty($this->form['input_type_values'])){
            foreach($this->form['input_type_values'] as $key => $item)
                if($item == getMsg('SYS','PRTYPE_VALUE_NOT_SELECTED')){
                    $this->id_input_type_value_not_selected = $key;
                    unset($this->form['input_type_values'][$key]);
                }

        }

    }

    function copyFormData()
    {
    	$this->form = modApiFunc("Session", "get", "EditCustomAttributeForm");
        if(isset($this->form["ErrorsArray"]) &&
           count($this->form["ErrorsArray"]) > 0)
        {
            $this->errors = $this->form["ErrorsArray"];
            unset($this->form["ErrorsArray"]);
        }
        $this->form['view_tag'] = prepareHTMLDisplay($this->form['view_tag']);
        $this->form['name'] = prepareHTMLDisplay($this->form['name']);
        $this->form['descr'] = prepareHTMLDisplay($this->form['descr']);
        if(isset($this->form['default']))
            $this->form['default'] = prepareHTMLDisplay($this->form['default']);
    }

    /**
     * @ describe the function EditCustomAttribute->getErrors.
     */
    function getErrors()
    {
        global $application;
    	if (sizeof($this->errors) == 0)
    	{
    		return;
    	}
    	$result = "";
    	foreach ($this->errors as $error)
    	{
    		$this->_error = $this->MessageResources->getMessage($error);
    		$result .= $this->TemplateFiller->fill("catalog/custom_attribute/", "error.tpl.html", array());
    	}
    	return $result;
    }

    /**
     * @ describe the function ->.
     */
    function output()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');
        $this->TemplateFiller = $application->getInstance('TmplFiller');
        if (isset($this->form['close']))
        {
            return $this->TemplateFiller->fill("catalog/custom_attribute/", "close.tpl.html", array());
        }

        $this->product_type_desc = modApiFunc('Catalog', 'getProductType', $this->form['product_type_id']);

        $application->registerAttributes(array(
            'FormID'
           ,'InputTypeID'
           ,'InputTypeName'
           ,'Error'
           ,'AttributeInputTypeValues'
           ,'IdInputTypeValueNotSelected'
            )
        );

        $template = "";
        switch ($this->form['input_type_name'])
        {
        	case 'text': $template = "edit_single_line.tpl.html";
        		break;
        	case 'textarea': $template = "edit_big_text.tpl.html";
        		break;
        	case 'select': $template = "edit_select.tpl.html";
        		break;
        }

        $output = modApiFunc('TmplFiller', 'fill',
                              './../../js/','validate.msgs.js.tpl',
                              array(
                                    "STRING1024"=> $this->MessageResources->getMessage( new ActionMessage(array('CATADD_001')) ),
                                    "STRING128"=> $this->MessageResources->getMessage( new ActionMessage(array('CATADD_002')) ),
                                    "STRING256"=> $this->MessageResources->getMessage( new ActionMessage(array('CATADD_003')) ),
                                    "STRING512"=> $this->MessageResources->getMessage( new ActionMessage(array('CATADD_004')) )
                                   )
                            );

        return $output.$this->TemplateFiller->fill("catalog/custom_attribute/", $template, array());
    }

    function outputAttributeInputTypeValues()
    {
        $out = '';
        if(!empty($this->form['input_type_values']))
            foreach($this->form['input_type_values'] as $key => $item){
                $out .= '<div><input type="text" class="form-control input-sm input-medium" name="input_type_values['.$key.']" value="'.$item.'" size="75" maxlength="128" /></div>';
            }
        return $out;
    }

    /**
     * @ describe the function EditCustomAttribute->getTag.
     */
    function getTag($tag)
    {
    	global $application;
    	$value = null;
    	switch ($tag)
    	{
    		case 'FormID':
    		    $value = $this->form['form_id'];
    			break;
    		case 'AttributeTag':
    		    $value = $this->form['view_tag'];
    			break;
    		case 'InputTypeID':
    		    $value = $this->form['input_type_id'];
    			break;
    		case 'InputTypeName':
    		    $value = $this->form['input_type_name'];
    			break;
    		case 'ProductTypeID':
    		    $value = $this->form['product_type_id'];
    			break;
    		case 'ProductTypeName':
    		    $value = $this->product_type_desc['name'];
    			break;
    		case 'AttributeTag':
    		    $value = $this->form['view_tag'];
    			break;
    		case 'AttributeName':
    		    $value = $this->form['name'];
    			break;
    		case 'AttributeDescr':
    		    $value = $this->form['descr'];
    			break;
    		case 'IdInputTypeValueNotSelected':
    		    $value =  $this->id_input_type_value_not_selected;
    			break;
    		case 'AttributeInputTypeValues':
    		    $value = $this->outputAttributeInputTypeValues();
    			break;
    		case 'AttributeDefault':
    		    $value = $this->form['default'];
    			break;
    	    case 'Errors':
    	        $value = $this->getErrors();
    	        break;
    	    case 'Error':
    	        $value = $this->_error;
    	        break;
    	    case 'InputStyleClass':
    	        $value = $this->_error != '' ? "error" : '';
    	        break;

    		default:
    			break;
    	}
    	return $value;
    }
    var $TemplateFiller;
    var $MessageResources;

    var $form;
    var $errors = array();
    var $_error;
    var $product_type_desc;
}

?>