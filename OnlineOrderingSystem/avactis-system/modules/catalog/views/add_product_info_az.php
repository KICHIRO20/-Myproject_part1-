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

/**
 * Catalog->AddProductInfo View.
 * Views the form to add a new product.
 *
 * @package Catalog
 * @access  public
 * @author Alexey Kolesnikov
 */
class AddProductInfo extends Catalog_ProdInfo_Base
{
    var $use_wysiwyg_editor = false;

    /**
     * A constructor.
     */
    function AddProductInfo()
    {
        $this->use_wysiwyg_editor = true;
        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
            if ((isset($this->POST['addAnother']) && $this->POST['addAnother'] == "true")||
                (isset($this->POST['FormSubmitValue']) && $this->POST['FormSubmitValue'] == "SetTypeID"))
            {
                $this->product_type_id = $this->ViewState['TypeID'];
                $this->product_type_ext = $this->getProductType();
                $this->initFormData(false);
                $this->ViewState['TypeID'] = $this->product_type_id;
            }
            else
            {
                if($this->ViewState['TypeID'] == "")
                {
                    $this->product_type_id = modApiFunc('Catalog', 'getDefaultProductTypeID');
                }
                else
                {
                    $this->product_type_id = $this->ViewState['TypeID'];
                }
                $this->product_type_ext = $this->getProductType();
            }
        }
        else
        {
    	    //   -                                      General Products,
            $this->product_type_id = 1;// modApiFunc('Catalog', 'getDefaultProductTypeID'); // Default Product Type to display at first time.

            if (modApiFunc('Session','is_set','AddProduct_LastUsedProductType'))
            {
                $this->product_type_id = modApiFunc('Session','get','AddProduct_LastUsedProductType');
            }

            $this->product_type_ext = $this->getProductType();
            $this->initFormData(false);
            $this->ViewState['TypeID'] = $this->product_type_id;
            //$this->initFormData();
        }
        modApiFunc('Session','set','AddProduct_LastUsedProductType', $this->product_type_id);
    }

    /**
     * Copies info, iputted by the user in the form and saves it to the local
     * array _POST.
     * It clears the error array.
     */
    function copyFormData()
    {
		global $application;
        // eliminate copying on construction
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState = $SessionPost["ViewState"];
        $this->ViewState['LargeImage'] = str_replace($application->appIni['URL_IMAGES_DIR'],'',$this->ViewState['LargeImage']);
        $this->ViewState['SmallImage'] = str_replace($application->appIni['URL_IMAGES_DIR'],'',$this->ViewState['SmallImage']);

        //Remove some data, that should not be sent to action one more time, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }

        foreach ($SessionPost as $key => $value)
        {
        	if (!is_array($value))
        	{
                $this->POST[$key]  = prepareHTMLDisplay($value);
        	}
        	else
        	{
                $this->POST[$key]  = $value;
        	}
        }
    }

    /**
     * Loads product info to the local array _POST.
     * It is used to initialize the editing form at first call of the given view.
     */
    function initFormData($bTypeNotDefined = true)
    {
        $this->ViewState =
            array(
                "hasCloseScript" => "false",
                "LargeImage" => "",
                "SmallImage" => "",
                "TypeID"     => "");

        if($bTypeNotDefined)
        {
            foreach ($this->product_type_ext as $group_id => $group)
            {
            	foreach ($group['attr'] as $attr_id => $attr)
            	{
                	$this->POST[$attr['view_tag']] = modApiFunc("Localization", "FloatToFormatStr", $attr['default'], $attr["patt_type"]);//$attr['default'];
                	$this->POST[$attr['view_tag']."_hidden"] = modApiFunc("Localization", "FloatToFormatStr", $attr['default'], $attr["patt_type"]);//$attr['default'];
                	$this->product_type_ext[$group_id]['attr'][$attr_id]['visible'] = true;
                	if ($attr['type'] == 'custom')
                	{
                		unset($this->product_type_ext[$group_id]['attr'][$attr_id]);
                	}
            	}
            }
        }
    }

    function getAttributes()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');

        $result = "";
        $product_type_has_been_outputed = 0;
    	foreach ($this->groups as $group)
    	{
    	    // output the name of the attribute group
    	    $this->_group = $group;
    		$result .= $this->TemplateFiller->fill("catalog/product_add/", "group.tpl.html", array());

    		// output the attribute
    		$this->_counter = 0;

    		if($group['id'] == 1 && !$product_type_has_been_outputed)
    		{
    		    $product_type_has_been_outputed = 1;

                $obj = &$application->getInstance('MessageResources');
    		    //Add "ProductType" row to first group.
    		    $this->_counter++;
    		    $this->_attr = array();
    		    $template = "";
   		    	$this->_attr['name'] = '<div >'.$obj->getMessage( new ActionMessage('PRD_PRDTYPE_NAME')).'*</div>';
   		    	$this->_attr['descr'] = $obj->getMessage( new ActionMessage('PRD_PRDTYPE_DESCR'));
   		    	//: check for sanity
    		    $this->_attr['tag'] = 'SelectTypeID';
    		    $this->_attr['unit'] = '';
    		    $this->_attr['pattern_type'] = 'integer';
    		    //end

    		    if(true)//$this->ViewState['TypeID'] == '')
    		    //Product type has not been yet selected
    		    {
                    $this->_attr_options = array();

        		    if($this->ViewState['TypeID'] == '')
        		    {
    		        	$this->_attr_options[] = array (
    		        	    'value' => "",
    		        	    'name' => $obj->getMessage('ADD_PRD_PRDTYPE_SELECT'),
    		        	    'selected' => true
    		        	    );
        		    }

                    $types = modApiFunc('Catalog', 'getProductTypes');
    		        foreach ($types as $type)
    		        {
    		        	$this->_attr_options[] = array (
    		        	    'value' => $type['id'],
    		        	    'name' => $type['name'],
    		        	    'selected' => $type['id'] == $this->ViewState['TypeID']
    		        	    );
    		        }
            		$template = "attr-select_product-type.tpl.html";
       		        $result .= $this->TemplateFiller->fill("catalog/product_add/", $template, array());
    		    }
    		    else
    		    //Product type has been already selected
    		    {
            		$template = "attr.tpl.html";
                    $product_type_desc = modApiFunc('Catalog', 'getProductType', $this->product_type_id);
            	    $this->_attr['value'] = $product_type_desc['name'];
       		        $result .= $this->TemplateFiller->fill("catalog/product_add/", $template, array());
    		    }
    		}
    		foreach ($group['attr'] as $attr)
    		{
                if ($attr['name']=="Product ID")
                {
                    $attr['visible'] = false;
                }
    		    if (!$attr['visible']) continue;
    		    $this->_counter++;
    		    $this->_attr = array();
    		    $template = "";
    		    if ($attr['required'])
    		    {
    		    	$this->_attr['name'] = '<div>'.$attr['name'].'*</div>';
    		    }
    		    else
    		    {
    		    	$this->_attr['name'] = $attr['name'];
    		    }
    		    $this->_attr['tag'] = $attr['view_tag'];
    		    $this->_attr['tag_hid'] = $attr['view_tag']."_hidden";
    		    $this->_attr['value'] = modApiFunc("Localization", "FloatToFormatStr", $attr['default'], $attr["patt_type"]);//$attr['default'];
    		    $this->_attr['def_value'] = base64_encode(modApiFunc("Localization", "FloatToFormatStr", $attr['default_value'], $attr["patt_type"]));//$attr['default_value'];
                $this->_attr['unit'] = modApiFunc("Localization", "getUnitTypeValue", $attr['patt_type']);
    		    $this->_attr['pattern_type'] = $attr['patt_type'];
                $this->_error = isset($this->ErrorsArray[$attr['view_tag']]) ? $this->MessageResources->getMessage($this->ErrorsArray[$attr['view_tag']]) : '';

                if (isset($attr['additional_link']))
                {
                    $this->_attr['additional_link'] = $attr['additional_link'];
                    $this->_attr['additional_link_text'] = $attr['additional_link_text'];
                }
    		    if ($attr['input_type_name'] == 'text')
    		    {
    		        $this->_attr['size'] = $attr['size'];
    		        $this->_attr['max'] = $attr['max'];
            		$template = "attr-text.tpl.html";
    		    }
    		    elseif ($attr['input_type_name'] == 'textarea')
    		    {
    		        if ($this->use_wysiwyg_editor == false && $attr['allow_html'])
    		        {
                        $template = "attr-textarea-no-wysiwyg.tpl.html";
    		        }
    		        else
    		        {
                        $template = "attr-textarea.tpl.html";
    		        }
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
                elseif ($attr['input_type_name'] == 'checkboxgroup')
                {
                    $this->_attr_options = array();
                    if($attr['view_tag'] == 'MembershipVisibility')
                    {
                        $cgroups = modApiFunc('Customer_Account','getGroups','exclude unsigned');
                        $i = 1;
                        foreach($cgroups as $value => $name)
                        {
                            $this->_attr_options[] = array (
                                'value' => $value,
                                'name' => $name,
                                'next_column' => (($i % 2 == 0) ? "</tr><tr>" : "")
                                );
                            $i++;
                        }
                        $template = "attr-checkboxgroup.tpl.html";
                    }
                }
    		    elseif ($attr['input_type_name'] == 'image')
    		    {
//    		        $this->_attr['ImageURL'] = $attr['value']['url'];
//    		        $this->_attr['ImageWidth'] = $attr['value']['width'];
//    		        $this->_attr['ImageHeight'] = $attr['value']['height'];
    		    	if ($attr['view_tag'] == 'LargeImage')
    		    	{
    		    	    if ($this->ViewState['LargeImage'] == '')
    		    	    {
    		    	        if(modApiFunc("Catalog", "isImageFolderNotWritable"))
    		    	        {
                                $template = "attr-no-image.error.tpl.html";
    		    	        }
    		    	        else
    		    	        {
                                $template = "attr-no-image.tpl.html";
    		    	        }
    		    	    }
    		    	    else
    		    	    {
                            $template = "attr-large-image.tpl.html";
    		    	    }
    		    	}
    		    	elseif ($attr['view_tag'] == 'SmallImage')
    		    	{
    		    	    if ($this->ViewState['SmallImage'] == '')
    		    	    {
    		    	        if(modApiFunc("Catalog", "isImageFolderNotWritable"))
    		    	        {
                                $template = "attr-no-image.error.tpl.html";
    		    	        }
    		    	        else
    		    	        {
                                $template = "attr-no-image.tpl.html";
    		    	        }
    		    	    }
    		    	    else
    		    	    {
                            $template = "attr-small-image.tpl.html";
    		    	    }
    		    	}
    		    }
    		    else
    		    {
                    $template = "attr.tpl.html";
    		    }
                $this->allow_html = $attr['allow_html'];
    		    $result .= $this->TemplateFiller->fill("catalog/product_add/", $template, array());
    		}

    		//Patch to create "* = Required Field" text row:
    		if($group['id'] == 1)
    		{
    		    $this->_counter++;
    		    $result .= $this->TemplateFiller->fill("catalog/product_add/", "required.tpl.html", array());
    		}
    		//end Patch to create "* = Required Field" text row.

    		//Patch to create "images Upload" button row:
    		if($group['id'] == 2 &&
    		   ($this->ViewState['LargeImage'] == "" ||
    		    $this->ViewState['SmallImage'] == ""))
    		{
    		    $this->_counter++;
    		    if(modApiFunc("Catalog", "isImageFolderNotWritable"))
    		    {
    		        $result .= $this->TemplateFiller->fill("catalog/product_add/", "upload_ctrl.images.error.tpl.html", array());
    		    }
    		    else
    		    {
    		        $result .= $this->TemplateFiller->fill("catalog/product_add/", "upload_ctrl.images.tpl.html", array());
    		    }
    		}
    		$result .= $this->TemplateFiller->fill("catalog/product_add/", "show-state.tpl.html", array());
    	}
    	return $result;
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
    	$this->_error_index = 0;
    	foreach ($this->ErrorsArray as $error)
    	{
    	    $this->_error_index++;
    		$this->_error = $this->MessageResources->getMessage($error);
    		$result .= $this->TemplateFiller->fill("catalog/product_add/", "error.tpl.html", array());
    	}
    	return $result;
    }

    /**
     * Outputs the form to add a new product.
     */
    function output()
    {
        global $application;



        if($this->ViewState["hasCloseScript"] == "true")
        {
       // modApiFunc("application", "closeChild_UpdateParent");

            $req = new Request();
            $req->setView('Catalog_EditProduct');
            $req->setAction('SetCurrentProduct');
            $req->setKey('prod_id',$this->ViewState['new_product_id']);
            $application->jsRedirect($req);

            return;
        }

        $arr = $this->product_type_ext;
        foreach ($arr as $group_id => $group)
        {
            $visible = false;
        	foreach ($group['attr'] as $attr_id => $attr)
        	{
        	    if (!$attr['visible'])
        	    {
        	    	continue;
        	    }
        	    $visible = true;
                $arr[$group_id]['attr'][$attr_id]['default_value'] = $arr[$group_id]['attr'][$attr_id]['default'];
                if (_ml_strtolower($arr[$group_id]['attr'][$attr_id]['input_type_name']) != 'select')
            		if (isset($this->POST[$attr['view_tag']]))
            		{
                        if ( $this->POST[$attr['view_tag']] != ""
                             && $this->POST[$attr['view_tag']] != base64_decode($this->POST[$attr['view_tag']."_hidden"]) )
                        {
                            $arr[$group_id]['attr'][$attr_id]['default'] = $this->POST[$attr['view_tag']];
                        }
            		}
        	}

            if (!$visible)
        	{
        		unset($arr[$group_id]);
        	}
        }

        $this->groups = $arr;

        $this->MessageResources = &$application->getInstance('MessageResources');
        $this->TemplateFiller = $application->getInstance('TmplFiller');
        // register full set of internal attributes
        $application->registerAttributes(array(
             'ViewStateClose'
            ,'refreshParent'
            ,'ViewStateLargeImage'
            ,'LargeImageWidth'
            ,'LargeImageHeight'
            ,'ViewStateSmallImage'
            ,'ViewStateSmallImageRelativePath'
            ,'ViewStateTypeID'
            ,'AttributePatternType'
            ,'AttributeFormat'
            ,'InputStyleClass'
            ,'nOnFocusMode'
            ,'UseWYSIWYGFor'
	        ,'GroupName'
	        ,'GroupID'
	        ,'Counter'
	        ,'AttributeSelectOptions'
	        ,'AttributeOptionValue'
	        ,'AttributeOptionSelected'
	        ,'AttributeOptionName'
	        ,'AttributeDefaultValue'
	        ,'AttributeTagHidden'
            ,'AddLink'
            ,'AddLinkText'
            ,'RequiredFieldText'
            ,'SubmitUploadImagesScript'
            ,'ImagesUploadErrorMessage'
            ,'ErrorIndex'
            ,'Error'
            ,'AutoGenSmallImageComment'
            ,'AttributeCheckboxes'
            ,'AttributeNextCheckboxColumn'
            ,'NoImagePath'
        ));

        $application->registerAttributes(array('Local_ProductBookmarks','CancelLink'));

        $output = modApiFunc('TmplFiller', 'fill',
                              './../../js/','validate.msgs.js.tpl',
                              array(
                                    "INTEGER" => $this->MessageResources->getMessage( new ActionMessage(array('PRDADD_001')) )
                                   ,"FLOAT"   => $this->MessageResources->getMessage( new ActionMessage(array('PRDADD_002')) )
                                   ,"STRING1024"=> $this->MessageResources->getMessage( new ActionMessage(array('PRDADD_007')) )
                                   ,"STRING128"=> $this->MessageResources->getMessage( new ActionMessage(array('PRDADD_008')) )
                                   ,"STRING256"=> $this->MessageResources->getMessage( new ActionMessage(array('PRDADD_009')) )
                                   ,"STRING512"=> $this->MessageResources->getMessage( new ActionMessage(array('PRDADD_010')) )
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
        if ($this->use_wysiwyg_editor == true)
        {
            return $output.$this->TemplateFiller->fill("catalog/product_add/", "container.tpl.html", array());
        }
        else
        {
            return $output.$this->TemplateFiller->fill("catalog/product_add/", "container-no-wysiwyg.tpl.html", array());
        }
    }

    /**
     * @ describe the function AddProductInfo->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');

        $imagesUrl = $application->getAppIni('URL_IMAGES_DIR');
        if ($application->getCurrentProtocol() == "https" && $application->getAppIni('HTTPS_URL_IMAGES_DIR'))
        {
            $imagesUrl = $application->getAppIni('HTTPS_URL_IMAGES_DIR');
        }

        $pathImagesDir = $application->getAppIni('PATH_IMAGES_DIR');

    	$value = null;
    	switch ($tag)
    	{
    	    case 'Items':
    	        $value = $this->getAttributes();
    	        break;

    	    case 'Errors':
    	        $value = $this->getErrors();
    	        break;

    	    case 'AttributeName':
    	        $value = $this->_attr['name'];
    	        break;

    	    case 'AttributeValue':
    	        $value = $this->_attr['value'];
    	        break;

            case 'AttributeDefaultValue':
            	$value = $this->_attr['def_value'];
            	break;

    	    case 'AttributeTag':
    	        $value = $this->_attr['tag'];
    	        break;

            case 'AttributeTagHidden':
    	        $value = $this->_attr['tag_hid'];
    	        break;

    	    case 'AttributeUnit':
    	        $value = $this->_attr['unit'];
    	        break;

    	    case 'AttributePatternType':
    	        $value = $this->_attr['pattern_type'];
    	        break;
    	    case 'AttributeFormat':
        	        $value = modApiFunc("Localization", "format_settings_for_js", $this->_attr['pattern_type']);
    	        break;

    	    case 'AttributeSize':
    	        $value = $this->_attr['size'];
    	        break;

    	    case 'AttributeMax':
    	        $value = $this->_attr['max'];
    	        break;

    	    case 'Counter':
    	        $value = $this->_counter;
    	        break;

    	    case 'AttributeSelectOptions':
    	        $value = "";
    	        foreach ($this->_attr_options as $opt_value)
    	        {
    	            $this->_attr_option = $opt_value;
        	        $value .= $this->TemplateFiller->fill("catalog/product_add/", "attr-select-option.tpl.html", array());
    	        }
    	        break;

            case 'AttributeCheckboxes':
                $value = "";
                foreach ($this->_attr_options as $opt_value)
                {
                    $this->_attr_option = $opt_value;
                    $value .= $this->TemplateFiller->fill("catalog/product_add/", "attr-checkboxgroup-item.tpl.html", array());
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
                if ($this->_attr['tag'] == 'TaxClass' && $this->_attr['tag'] != 'MembershipVisibility')
                {
                    $value = prepareHTMLDisplay($this->_attr_option['name']);
                }
                else
                {
                    $value = $this->_attr_option['name'];
                }
    	        break;

            case 'AttributeNextCheckboxColumn':
                $value = $this->_attr_option['next_column'];
                break;

            case 'AddLink':
                $value = isset($this->_attr['additional_link'])? $this->_attr['additional_link']:"";
                break;

            case 'AddLinkText':
                $value = isset($this->_attr['additional_link_text'])? $this->_attr['additional_link_text']:"";
                break;


    	    case 'ViewStateLargeImage':
    	        $value = '';
    	        if ($this->ViewState['LargeImage'])
    	        {
        	        $images_dir = $imagesUrl;
        	        $value = $images_dir . $this->ViewState['LargeImage'];
    	        }
    	        break;

    	    case 'ViewStateSmallImage':
    	        $value = '';
    	        if ($this->ViewState['SmallImage'])
    	        {
        	        $images_dir = $imagesUrl;
        	        $value = $images_dir . $this->ViewState['SmallImage'];
    	        }
    	        break;

    	    case 'LargeImageWidth':
    	        $value = '';
    	        if ($this->ViewState['LargeImage']) {
    	            list($width, $height) = getimagesize($pathImagesDir . $this->ViewState['LargeImage']);
    	            $value = $width;
    	        }
    	        break;

    	    case 'LargeImageHeight':
    	        $value = '';
    	        if ($this->ViewState['LargeImage']) {
    	            list($width, $height) = getimagesize($pathImagesDir . $this->ViewState['LargeImage']);
    	            $value = $height;
    	        }
    	        break;

    	    case 'ViewStateSmallImageRelativePath':
    	        //Hidden Value
    	        $value = '';
    	        if ($this->ViewState['SmallImage'])
    	        {
        	        $images_dir = $imagesUrl;
        	        $value = $images_dir . $this->ViewState['SmallImage'];
    	        }
    	        break;

    	    case 'ViewStateTypeID':
    	        $value = $this->ViewState['TypeID'];
    	        break;

    	    case 'ProductTypeID':
    	        $value = $this->product_type_id;
    	        break;

    	    case 'ProductTypeName':
                $product_type_desc = modApiFunc('Catalog', 'getProductType', $this->product_type_id);
    	        $value = $product_type_desc['name'];
    	        break;

    	    case 'ViewStateClose':
    	        $value = $this->ViewState['hasCloseScript'];
    	        break;

            case 'refreshParent':
                $value = '';
                if (isset($this->POST['addAnother']) && $this->POST['addAnother'] == "true")
                {
                    $value = "window.opener.location.reload();\n";
                    $value.= "window.focus();";
                }
                break;

    	    case 'ErrorIndex':
    	        $value = $this->_error_index;
    	        break;

    	    case 'Error':
    	        $value = $this->_error;
    	        break;

    	    case 'InputStyleClass':
                $classes = array();
                if ($this->_error != '') { $classes[] = 'error'; }
                if ($this->allow_html) { $classes[] = 'tiny_mce'; }
                $value = implode(' ', $classes);
    	        break;

    	    case 'SubmitUploadImagesScript':
    	        $value = 'onclick="if(formElementOnFocus()){CatalogAddProduct.FormSubmitValue.value = \'UploadImages\';CatalogAddProduct.submit();_disableButtons();return true;}"';
    	        break;

    	    case 'nOnFocusMode':
    	        $value = $this->ViewState['TypeID'] == "" ? 1 : 2;
    	        break;

    	    case 'UseWYSIWYGFor':
    	        $value = array();
    	        foreach ($this->product_type as $view_tag => $attr)
    	        {
    	        	if($attr['allow_html'] && $attr['visible'])
    	        	    $value[] = $view_tag;
    	        }
    	        $value = implode(', ', $value) ;
    	        break;

    	    case 'ImagesUploadErrorMessage':
    	        $value = $obj->getMessage("SETUP_WARNING_IMAGE_FOLDER_IS_NOT_WRITABLE", array("0" => modApiFunc("Catalog", "getImagesDir")));
    	        break;

    	    case 'RequiredFieldText':
    	        $value = $obj->getMessage(new ActionMessage('REQUIRED_FIELD_LABEL'));
    	        break;

    	    case 'Breadcrumb':
                $obj = &$application->getInstance('Breadcrumb');
                $value = $obj->output(false);
    	        break;

            case 'Local_ProductBookmarks':
                $value = getProductBookmarks('details',$this->prod_id,'add');
                break;

    	    case 'AutoGenSmallImageComment':
                $is_present_large_image = false;
                foreach($this->_group['attr'] as $attr_info)
                {
                    if($attr_info['view_tag'] == 'LargeImage' and $attr_info['visible'] == 1)
                    {
                        $is_present_large_image = true;
						$pi_settings = modApiFunc('Product_Images','getSettings');
	                    if($pi_settings['AUTO_GEN_MAIN_SMALL_IMAGE'] == 'Y' and function_exists('gd_info'))
	                    {
	                        $value = getMsg('PI','COMMENT_AUTO_GEN_SMALL_IMAGE');
	                    };
                    };
                };
                break;

    	    case 'NoImagePath':
        	    $value = $imagesUrl . 'noimage.png';
    	        break;

    	    default:
    	        list($entity, $tag) = getTagName($tag);
        	    if ($entity == 'product')
        	    {
        	        $value = getKeyIgnoreCase($tag, $this->POST);
        	    }
        	    elseif ($entity == 'group')
        	    {
        	        $value = getKeyIgnoreCase($tag, $this->_group);
        	    }
        		break;
    	}
    	return $value;
    }

    var $POST;
    var $product_type_ext;
    /**
     * View state structure. It comes from action.
     * $SessionPost["ViewState"] structure example:
     * <br>array
     * <br>(
     * <br>    "hasCloseScript"  = "false"           //true/false
     * <br>    "ErrorsArray"     =  array()          //true/false
     * <br>    "LargeImage"      = "image.jpg"       //
     * <br>    "SmallImage"      = "small_image.jpg" //
     * <br>    "TypeID"          = "1"               //integer, product type id
     * <br>)
     */
    var $ViewState;

    /**
     * List of error ids. It comes from action.
     */
    var $ErrorsArray;
    var $TemplateFiller;
    var $MessageResources;
    var $groups;
    var $_group;
    var $_attr;
    var $_attr_options;
    var $_attr_option;
    var $_counter;
    var $_error_index;
    var $_error;
}
?>