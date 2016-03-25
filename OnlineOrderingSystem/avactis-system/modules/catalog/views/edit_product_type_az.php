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

class EditProductType extends Catalog_ProdInfo_Base
{

    /**
     * A constructor.
     */
    function EditProductType()
    {
        $this->getProductType();
        if(modApiFunc("Session", "is_Set", "EditProductTypeForm"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'EditProductTypeForm');
        }
        else
        {
            $this->initFormData();
        }
    }

    /**
     * @ describe the function EditProductType->.
     */
    function initFormData()
    {
        //
    	$this->form_id = rand(1, 999999999);

    	//                custom attributes
    	$custom_group_id = 7; // @ hard-coded
    	if (array_key_exists($custom_group_id, $this->product_type_ext))
    	{
        	$group = $this->product_type_ext[$custom_group_id]['attr'];
        	foreach ($group as $view_tag => $attr)
        	{
                modApiFunc('Catalog', 'addTempCustomAttribute', $this->form_id, $attr);
        	}
    	}
    	else
    	{
    		$this->product_type_ext[$custom_group_id] = array();
    	}
    	$this->product_type_ext[$custom_group_id] = array('id'=>7, 'name'=>'Custom Attributes', 'sort'=>9);
    	$this->product_type_ext[$custom_group_id]['attr'] = modApiFunc('Catalog', 'getTempCustomAttributes', $this->form_id);

    // look through the product_type_ext contents and convert the elments that
    // have allow_html=1, if such exist.
        foreach ($this->product_type_ext as $id_group => $group)
        {
            //create a reference
            $group = &$this->product_type_ext[$id_group];

            if(isset($group['attr']) && is_array($group['attr']))
            {
            	foreach ($group['attr'] as $id => $attr)
            	{
            	    //create a reference
                    $attr = &$group['attr'][$id];
                    $attr['default'] = ($attr['allow_html'] && $attr['visible']) ? prepareHTMLDisplay($attr['default']) : modApiFunc("Localization", "FloatToFormatStr", $attr['default'], $attr["patt_type"]);
                    //clean up the reference
                    unset($attr);
            	}
            }

        	//clean up the reference
        	unset($group);
        }



    }

    function copyFormData()
    {
        $EditProductTypeForm = modApiFunc("Session", "get", "EditProductTypeForm");
        $this->ViewState = $EditProductTypeForm["ViewState"];
        $this->form_id = $EditProductTypeForm['form_id'];

        //Remove some data, that should not be sent to action one more time, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }

        // replace custom attributes with data from the temporary DB
        unset($this->product_type_ext[7]);
        $custom_attributes = modApiFunc('Catalog', 'getTempCustomAttributes', $this->form_id);
        if (sizeof($custom_attributes) > 0)
        {
            $this->product_type_ext[7] = array('id'=>7, 'name'=>'Custom Attributes', 'sort'=>9);
        	$this->product_type_ext[7]['attr'] = $custom_attributes; // @ hard-coded
        }
        // copy data from the user form
        foreach ($this->product_type_ext as $group_id => $group)
        {
        	foreach ($group['attr'] as $attr_id => $attr)
        	{
        		if ($attr['input_type_name'] != 'read-only' && isset($EditProductTypeForm[$attr['view_tag']]))
        		{
                    if(empty($EditProductTypeForm[$attr['view_tag']]['default']))
                        $EditProductTypeForm[$attr['view_tag']]['default'] = '';
        			$this->product_type_ext[$group_id]['attr'][$attr_id]['default'] = prepareHTMLDisplay($EditProductTypeForm[$attr['view_tag']]['default']);
        			if ($attr['vanishing'])
        			{
        			    if (isset($EditProductTypeForm[$attr['view_tag']]['visible']))
        			    {
                			$this->product_type_ext[$group_id]['attr'][$attr_id]['visible'] = true;
        			    }
        			    else
        			    {
                			$this->product_type_ext[$group_id]['attr'][$attr_id]['visible'] = false;
        			    }
        			}
        			if (isset($attr['unit_type_values']))
        			{
            			$this->product_type_ext[$group_id]['attr'][$attr_id]['unit_type_value'] = $EditProductTypeForm[$attr['view_tag']]['unit_type_value'];
        			}
        		}
        	}
        }
        // an addition for the type name and description
        $this->product_type_name = prepareHTMLDisplay($EditProductTypeForm['TypeName']);
        $this->product_type_descr = prepareHTMLDisplay($EditProductTypeForm['TypeDescr']);

    }

    function output()
    {
        global $application;
        $request = new Request();
        $request->setView('EditCustomAttribute');
        $request->setKey('form_id', $this->form_id);
        $request->setKey('product_type_id', $this->product_type_id);
        $edit_custom_attribute_href = $request->getURL();

        $this->MessageResources = &$application->getInstance('MessageResources');
        $this->TemplateFiller = $application->getInstance('TmplFiller');
        $application->registerAttributes(array(
            'FormID'
           ,'TypeID'
           ,'TypeName'
           ,'TypeDescr'
           ,'GroupName'
           ,'GroupID'
           ,'Vanishing'
           ,'AttributeVanishingChecked'
           ,'AttributeCustomControls'
           ,'AttributePatternType'
           ,'AttributeFormat'
           ,'AttributeUnitSelect'
           ,'AttributeUnitOptions'
           ,'AttributeUnitOptionValue'
           ,'AttributeUnitOptionName'
           ,'AttributeUnitOptionSelected'
           ,'Counter'
           ,'EditCustomAttributeLink'
           ,'AttributeSelectOptions'
           ,'AttributeOptionValue'
           ,'AttributeOptionName'
           ,'AttributeOptionSelected'
           ,'AttributeUnitOptionPattern'
           ,'AddLink'
           ,'AddLinkText'
           ,'Error'
           ,'InputStyleClass'
           ,'AttributeHelpIcon'
           ,'OptionsLink'
           ,'AttributeInputClass'
            )
        );

        $output = modApiFunc('TmplFiller', 'fill',
                              './../../js/','validate.msgs.js.tpl',
                              array(
                                    "INTEGER" => $this->MessageResources->getMessage( new ActionMessage(array('PRDADD_001')) )
                                   ,"FLOAT"   => $this->MessageResources->getMessage( new ActionMessage(array('PRDADD_002')) )
                                   ,"STRING1024"=> $this->MessageResources->getMessage( new ActionMessage(array('CATADD_001')) )
                                   ,"STRING128"=> $this->MessageResources->getMessage( new ActionMessage(array('CATADD_002')) )
                                   ,"STRING256"=> $this->MessageResources->getMessage( new ActionMessage(array('CATADD_003')) )
                                   ,"STRING512"=> $this->MessageResources->getMessage( new ActionMessage(array('CATADD_004')) )
                                   ,"CURRENCY"=> addslashes($this->MessageResources->getMessage( new ActionMessage(array('CURRENCY_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 12.35, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.00, "currency")))))
                                   ,"WEIGHT"   => addslashes($this->MessageResources->getMessage( new ActionMessage(array('WEIGHT_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 23.325, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.2, "weight")))))
                                   ,"ITEM"     => $this->MessageResources->getMessage( new ActionMessage(array('ITEM_FIELD')))
                                   )
                            );

        return $output.$this->TemplateFiller->fill("catalog/product_type_edit/", "container.tpl.html", array());
    }

    /**
     * @ describe the function ->.
     */
    function getProductType()
    {
        $this->product_type_id = modApiFunc('Catalog', 'getCurrentProductTypeID');
        $this->product_type_ext = parent::getProductType();
        foreach ($this->product_type_ext as $group_id => $group)
        {
        	foreach ($group['attr'] as $attr_id => $attr)
        	{
        		// specify the attributes, which can't have default values.
        		if ($attr['view_tag'] == 'LargeImage' || $attr['view_tag'] == 'SmallImage' || $attr['view_tag'] == 'ImageAltText' || $attr['view_tag'] == 'ID' || $attr['view_tag'] == 'Name')
        		{
        			$this->product_type_ext[$group_id]['attr'][$attr_id]['input_type_name'] = 'read-only';
        		}
        		// specify the attributes, which can't be invisible
        		if ($attr['view_tag'] == 'ID' || $attr['view_tag'] == 'Name' || $attr['view_tag'] == 'SalePrice')
        		{
        			$this->product_type_ext[$group_id]['attr'][$attr_id]['vanishing'] = false;
        		}
        		else
        		{
        			$this->product_type_ext[$group_id]['attr'][$attr_id]['vanishing'] = true;
        		}
        	}
        }
        $product_type = modApiFunc('Catalog', 'getProductType', $this->product_type_id);
        $this->product_type_name = $product_type['name'];
        $this->product_type_descr = $product_type['description'];
    }

    /**
     * @ describe the function EditProductInfo->getErrors.
     */
    function getErrors()
    {
        global $application;
    	if (!is_array($this->ErrorsArray) || sizeof($this->ErrorsArray) == 0)
    	{
    		return;
    	}
    	$result = "";
    	foreach ($this->ErrorsArray as $error)
    	{
    		$this->_error = $this->MessageResources->getMessage($error);
    		$result .= $this->TemplateFiller->fill("catalog/product_type_edit/", "error.tpl.html", array());
    	}
    	return $result;
    }

    /**
     * @ describe the function EditProductType->getAttributes.
     */
    function getAttributes()
    {
    	$result = "";
    	foreach ($this->product_type_ext as $group)
    	{
		    if (!is_array($group['attr'])) continue;
    	    // output the name of the attribute group
    	    $this->_group = $group;
    		$result .= $this->TemplateFiller->fill("catalog/product_type_edit/", "group.tpl.html", array());
    		$this->_counter = 0;
    		foreach ($group['attr'] as $attr)
    		{
    		    $this->_attr = array();
    		    if ($attr['required'])
    		    {
    		    	$this->_attr['name'] = '<div class="required">'.$attr['name'].'*</div>';
    		    }
    		    else
    		    {
    		    	$this->_attr['name'] = $attr['name'];
    		    }
    		    $this->_counter++;

    		    $this->_attr['id'] = $attr['id'];
    		    $this->_attr['type'] = $attr['type'];
    		    $this->_attr['tag'] = $attr['view_tag'];
    		    $this->_attr['default'] = $attr['default'];
    		    $this->_attr['size'] = $attr['size'];
    		    $this->_attr['max'] = $attr['max'];
    		    $this->_attr['unit'] = array_key_exists('unit_type_values', $attr) ? $attr['unit_type_values'] : '';
    		    $this->_attr['unit_pattern'] = array_key_exists('unit_type_values_pattern', $attr) ? $attr['unit_type_values_pattern'] : '';
    		    $this->_attr['pattern_type'] = $attr['patt_type'];
    		    $this->_attr['unit_type_value'] = $attr['unit_type_value'];
    		    $this->_attr['vanishing'] = $attr['vanishing'];
    		    $this->_attr['visible'] = $attr['visible'];
    		    $this->_error = isset($this->ErrorsArray[$attr['view_tag']]) ? $this->MessageResources->getMessage($this->ErrorsArray[$attr['view_tag']]) : '';

                if (isset($attr['additional_link']))
                {
                    $this->_attr['additional_link'] = $attr['additional_link'];
                    $this->_attr['additional_link_text'] = $attr['additional_link_text'];
                }
    		    if ($attr['input_type_name'] == 'text')
    		    {
        		    $template = "attr-text.tpl.html";
    		    }
    		    elseif ($attr['input_type_name'] == 'textarea')
    		    {
        		    $template = "attr-textarea.tpl.html";
    		    }
    		    elseif ($attr['input_type_name'] == 'select')
    		    {
                    $this->_attr_options = array();
    		        foreach ($attr['input_type_values'] as $value => $name)
    		        {
    		        	$this->_attr_options[] = array (
    		        	    'value' => $value,
    		        	    'name' => $name,
    		        	    'selected' => $value == $attr['default']
    		        	    );
    		        }
        		    $template = "attr-select.tpl.html";
    		    }
    		    else
    		    {
        		    $template = "attr.tpl.html";
    		    }

                $result .= $this->TemplateFiller->fill("catalog/product_type_edit/", $template, array());
    		}
    	}
    	return $result;
    }

    /**
     * @ describe the function EditProductType->getTag.
     */
    function getTag($tag)
    {
        global $application;
    	$value = null;
    	switch ($tag)
    	{
    	    case 'FormID':
    	        $value = $this->form_id;
    	        break;

    	    case 'TypeID':
    	        $value = $this->product_type_id;
    	        break;

    	    case 'TypeName':
    	        $value = $this->product_type_name;
    		    $this->_error = isset($this->ErrorsArray['TypeName']) ? $this->MessageResources->getMessage($this->ErrorsArray['TypeName']) : '';
    	        break;

    	    case 'TypeDescr':
    	        $value = $this->product_type_descr;
    		    $this->_error = isset($this->ErrorsArray['TypeDescr']) ? $this->MessageResources->getMessage($this->ErrorsArray['TypeDescr']) : '';
    	        break;

    		case 'Items':
    			$value = $this->getAttributes();
    			break;

    	    case 'Errors':
    	        $value = $this->getErrors();
    	        break;

    		case 'GroupName':
    		    $value = $this->_group['name'];
    		    break;

    		case 'GroupID':
    		    $value = $this->_group['id'];
    		    break;

    		case 'AttributeName':
    		    $value = $this->_attr['name'];
    		    break;

    		case 'AttributeTag':
    		    $value = $this->_attr['tag'];
    		    break;

    		case 'AttributeDefault':
    		    $value = $this->_attr['default'];
    		    break;

    		case 'AttributeSize':
    		    $value = $this->_attr['size'];
    		    break;

    		case 'AttributeMax':
    		    $value = $this->_attr['max'];
    		    break;

    	    case 'AttributePatternType':
                $value = $this->_attr['pattern_type'];
    	        break;
    	    case 'AttributeFormat':
        	        $value = modApiFunc("Localization", "format_settings_for_js", $this->_attr['pattern_type']);
    	        break;

    		case 'AttributeUnitSelect':
    		    $value = $this->_attr['unit_type_value'];
		    	break;

    		case 'AttributeUnitOptions':
    		    $value = '';
    		    foreach ($this->_unit_options as $unit)
    		    {
    	            $this->_unit_option = $unit;
    		    	$value .= $this->TemplateFiller->fill("catalog/product_type_edit/", 'unit.tpl.html', array());
    		    }
    		    break;

    	    case 'AttributeUnitOptionValue':
    	        $value = $this->_unit_option['value'];
    	        break;

    	    case 'AttributeUnitOptionSelected':
    	        if ($this->_unit_option['selected'])
    	            $value = " selected";
    	        break;

    	    case 'AttributeUnitOptionName':
    	        $value = $this->_unit_option['name'];
    	        break;

    	    case 'AttributeUnitOptionPattern':
    	        $value = $this->_unit_option['pattern'];
    	        break;

            case 'AddLink':
                $value = isset($this->_attr['additional_link'])? $this->_attr['additional_link']:"";
                break;

            case 'AddLinkText':
                $value = isset($this->_attr['additional_link_text'])? $this->_attr['additional_link_text']:"";
                break;

    	    case 'Vanishing':
    	        $value = '';
    	        if (!$this->_attr['vanishing'])
    	            break;
		    	$value = $this->TemplateFiller->fill("catalog/product_type_edit/", 'attr-vanishing.tpl.html', array());
    	        break;

    	    case 'AttributeVanishingChecked':
    	        $value = $this->_attr['visible'] ? ' checked' : '';
    	        break;

    	    case 'AttributeCustomControls':
    	        if ($this->_attr['type'] != 'custom')
    	            break;
		    	$value = $this->TemplateFiller->fill("catalog/product_type_edit/", 'attr-custom-controls.tpl.html', array());
    	        break;

    	    case 'AttributeSelectOptions':
    	        $value = "";
    	        foreach ($this->_attr_options as $opt_value)
    	        {
    	            $this->_attr_option = $opt_value;
        	        $value .= $this->TemplateFiller->fill("catalog/product_type_edit/", "attr-select-option.tpl.html", array());;
    	        }
    	        break;

    	    case 'AttributeOptionValue':
    	        $value = $this->_attr_option['value'];
    	        break;

    	    case 'AttributeOptionSelected':
    	        if ($this->_attr_option['selected'])
    	            $value = " selected";
    	        break;

    	    case 'AttributeOptionName':
    	        $value = prepareHTMLDisplay($this->_attr_option['name']);
    	        break;

    	    case 'Counter':
    	        $value = $this->_counter;
    	        break;

            case 'EditCustomAttributeLink':
                $request = new Request();
                $request->setView('EditCustomAttribute');
                $request->setKey('form_id', $this->form_id);
                $request->setKey('product_type_id', $this->product_type_id);
                $value = $request->getURL();
                break;

    	    case 'Error':
    	        $value = $this->_error;
    	        break;

    	    case 'InputStyleClass':
    	        $value = $this->_error != '' ? "error" : '';
    	        break;

    	    case 'AttributeHelpIcon':
    	        if ($this->_attr['id'] != null)
    	        {
		    	    $value = $this->TemplateFiller->fill("catalog/product_type_edit/", 'help-icon.tpl.html', array());
    	        }
    	        break;

            case 'OptionsLink':
                $request = new Request();
                $request->setView('PO_OptionsList');
                $request->setKey('parent_entity','ptype');
                $request->setKey('entity_id',$this->product_type_id);
                $value = $request->getURL();
                break;

            case 'AttributeInputClass':
                $value = in_array($this->_attr['tag'], array('ShortDescription', 'DetailedDescription')) ? 'tiny_mce' : '';
                break;

    	    default:
    			break;
    	}
    	return $value;
    }

    var $TemplateFiller;
    var $MessageResources;
    var $ViewState;
    var $ErrorsArray;
    var $product_type_ext;
    var $product_type_name;
    var $product_type_descr;
    var $form_id;
    var $_group;
    var $_attr;
    var $_counter;
    var $_unit_options;
    var $_unit_option;
    var $_attr_options;
    var $_attr_option;
}
?>