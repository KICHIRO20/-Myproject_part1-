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

class AddCustomAttribute
{

    /**
     * @ describe the function AddCustomAttribute->.
     */
    function AddCustomAttribute()
    {
        if(modApiFunc("Session", "is_Set", "AddCustomAttributeForm"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'AddCustomAttributeForm');
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
    	$this->form['type_id'] = $request->getValueByKey('type_id');
    	$this->form['product_type_id'] = $request->getValueByKey('product_type_id');
    	$this->form['view_tag'] = '';
    	$this->form['name'] = '';
    	$this->form['descr'] = '';
    	$this->form['default'] = '';
    }

    function copyFormData()
    {
    	$this->form = modApiFunc("Session", "get", "AddCustomAttributeForm");
        if(isset($this->form["ErrorsArray"]) &&
           count($this->form["ErrorsArray"]) > 0)
        {
            $this->errors = $this->form["ErrorsArray"];
            unset($this->form["ErrorsArray"]);
        }
    }

    /**
     * @ describe the function AddCustomAttribute->getErrors.
     */
    function getErrors()
    {
        global $application;
    	if (sizeof($this->errors) == 0)
    	{
    		return;
    	}
    	$result = "";
    	$application->registerAttributes(array('Error'));
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
           ,'TypeID'
           ,'InputStyleClass'
           ,'AttributeInputTypeValues'
            )
        );

        $template = "";
        switch ($this->form['type_id'])
        {
        	case 1: $template = "add_single_line.tpl.html";
        		break;
        	case 2: $template = "add_big_text.tpl.html";
        		break;
        	case 3: $template = "add_select.tpl.html";
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

    /**
     * @ describe the function AddCustomAttribute->getTag.
     */
    function getTag($tag)
    {
    	$value = null;
    	switch ($tag)
    	{
    		case 'FormID':
    			$value = $this->form['form_id'];
    			break;
    		case 'TypeID':
    		    $value = $this->form['type_id'];
    		    break;
    		case 'ProductTypeID':
    		    $value = $this->form['product_type_id'];
    		    break;
    		case 'ProductTypeName':
    		    $value = prepareHTMLDisplay($this->product_type_desc['name']);
    		    break;
    		case 'AttributeTag':
    		    $value = prepareHTMLDisplay($this->form['view_tag']);
    		    break;
    		case 'AttributeName':
    		    $value = prepareHTMLDisplay($this->form['name']);
    		    break;
    		case 'AttributeDescr':
    		    $value = prepareHTMLDisplay($this->form['descr']);
    		    break;
    		case 'AttributeInputTypeValues':
    		    $value = $this->form['input_type_values'];
    			break;
    		case 'AttributeDefault':
    		    $value = prepareHTMLDisplay($this->form['default']);
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