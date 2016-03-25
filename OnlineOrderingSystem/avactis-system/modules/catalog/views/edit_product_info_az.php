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
 * Catalog->EditProductInfo View.
 * Views edit product form.
 *
 * @package Catalog
 * @access  public
 * @author Alexey Kolesnikov
 */
class EditProductInfo extends Catalog_ProdInfo_Base
{
    var $use_wysiwyg_editor = false;

    /**
     * A constructor.
     */
    function EditProductInfo()
    {
        $this->product_info_ext = $this->getProductInfo();
        if(modApiFunc("Session", "is_Set", "EditProductInfoForm"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'EditProductInfoForm');
        }
        else
        {
            $this->initFormData();
        }

        $this->use_wysiwyg_editor = modApiFunc('Session','is_set','ProductInfoWYSIWYGEditorEnabled');
    }

    function isStockQuantityUsable()
    {
        $__for_it_opts = modApiFunc("Product_Options","getOptionsList",'product',$this->prod_id,USED_FOR_INV);
        $r =  !(count($__for_it_opts)>0 and modApiFunc("Product_Options","__hasEntityPrivilegesFor",'product','inventory'));
        return $r;
    }

    /**
     * Copies info, iputted by user in the form and saves it to the local
     * array _POST.
     * It clears the error array.
     */
    function copyFormData()
    {
		global $application;
        // eliminate copying on construction
        $EditProductInfoForm = modApiFunc("Session", "get", "EditProductInfoForm");

        $this->ViewState = $EditProductInfoForm["ViewState"];
        $this->ViewState['LargeImage'] = str_replace($application->appIni['URL_IMAGES_DIR'],'',$this->ViewState['LargeImage']);
        $this->ViewState['SmallImage'] = str_replace($application->appIni['URL_IMAGES_DIR'],'',$this->ViewState['SmallImage']);

        //Remove some data, that should not be sent to action one more time, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }

        foreach ($EditProductInfoForm as $key => $value)
        {
        	if (!is_array($value))
        	{
                $this->POST[$key]  = $value;
                $this->POST[$key.'_escaped']  = prepareHTMLDisplay($value);
        	}
        	else
        	{
                $this->POST[$key]  = $value;
        	}
        }
        $this->POST['ID'] = $this->product_info['ID'];
        $this->POST['TypeID'] = $this->product_info['TypeID'];
        $this->POST['TypeName'] = $this->product_info['TypeName'];

        //                                                         EditProduct
        if(modApiFunc('Session','is_set','SavedOk'))
        {
            $val = modApiFunc('Session','get','SavedOk');
            if($val == 1)
            {
                $this->SavedOk = 1;
                modApiFunc('Session','un_set','SavedOk');
            }
            else
            {
                $this->SavedOk = 2;
            }
        }
        else
        {
            $this->SavedOk = 2;
        }
    }

    /**
     * Loads product info to the local array _POST.
     * It is used to initialize the editing form at first call of the given view.
     */
    function initFormData()
    {
        //                                                         EditProduct
        if(modApiFunc('Session','is_set','SavedOk'))
        {
            $val = modApiFunc('Session','get','SavedOk');
            if($val == 1)
            {
                $this->SavedOk = 1;
                modApiFunc('Session','un_set','SavedOk');
            }
            else
            {
                $this->SavedOk = 2;
            }
        }
        else
        {
            $this->SavedOk = 2;
        }

        $this->ViewState =
            array(
                "hasCloseScript" => "false",
                "LargeImage" => $this->product_info['attributes']['LargeImage']['value']['exists'] ? $this->product_info['attributes']['LargeImage']['value']['url'] : '',
                "SmallImage" => $this->product_info['attributes']['SmallImage']['value']['exists'] ? $this->product_info['attributes']['SmallImage']['value']['url'] : ''
                 );

        foreach ($this->product_info_ext as $group)
        {
        	foreach ($group['attr'] as $attr)
        	{
            	//$this->POST[$attr['view_tag']] = ($attr['allow_html'] && $attr['visible']) ? prepareHTMLDisplay($attr['value']) : $attr['value'];
                $this->POST[$attr['view_tag']] = $attr['value'];
                $this->POST[$attr['view_tag'].'_escaped'] = ($attr['allow_html']) ? prepareHTMLDisplay($attr['value']) : $attr['value'];
        	}
        }
        $this->POST['TypeID'] = $this->product_info['TypeID'];
        $this->POST['TypeName'] = $this->product_info['TypeName'];
    }

    /**
     * Outputs edit product form.
     */
    function output()
    {
        global $application;

        if(isset($this->ViewState["hasCloseScript"]) && $this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        $arr = $this->product_info_ext;

        foreach ($arr as $group_id => $group)
        {
        	foreach ($group['attr'] as $attr_id => $attr)
        	{
        		if (isset($this->POST[$attr['view_tag']]))
        		{
        			$arr[$group_id]['attr'][$attr_id]['value'] = $this->POST[$attr['view_tag']];
                    $arr[$group_id]['attr'][$attr_id]['value_escaped'] = $this->POST[$attr['view_tag'].'_escaped'];
        		}
        		else if (!isset($arr[$group_id]['attr'][$attr_id]['value']))
        		{
                    $arr[$group_id]['attr'][$attr_id]['value'] = '';
                    $arr[$group_id]['attr'][$attr_id]['value_escaped'] = '';
        		}
        	}
        }
        $this->groups = $arr;

        $this->MessageResources = &$application->getInstance('MessageResources');
        $this->TemplateFiller = $application->getInstance('TmplFiller');
        $application->registerAttributes(array(
             'ViewStateClose'
            ,'ViewStateLargeImage'
            ,'ViewStateSmallImage'
            ,'ViewStateSmallImageRelativePath'
            ,'AttributePatternType'
            ,'AttributeFormat'
            ,'ErrorIndex'
            ,'Error'
            ,'Error_List'
            ,'SavedOkMessage'
            ,'InputStyleClass'
            ,'UseWYSIWYGFor'
    	    ,'GroupName'
    	    ,'GroupID'
    	    ,'Counter'
    	    ,'AttributeSelectOptions'
    	    ,'AttributeOptionValue'
    	    ,'AttributeOptionSelected'
    	    ,'AttributeOptionName'
            ,'AddLink'
            ,'AddLinkText'
    	    ,'SubmitUploadImagesScript'
            ,'ImagesUploadErrorMessage'
            ,'additionalJS'
            ,'AutoGenSmallImageComment'
            ,'ViewStateLargeImageWidth'
            ,'ViewStateLargeImageHeight'
            ,'ResultMessageRow'
            ,'AutoGenSmallImageComment'
            ,'AttributeValueEscaped'
            ,'InvEditorLink'
            ,'AttributeCheckboxes'
            ,'AttributeNextCheckboxColumn'
            ,'AttributeCheckedCheckbox'
            ,'AttributeDisabledCheckbox'
            ,'AttributeWorldVisible'
            ,'AttributeCustomVisible'
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

        if ($this->use_wysiwyg_editor === true)
        {
            return $output.$this->TemplateFiller->fill("catalog/product_edit/", "container.tpl.html", array());
        }
        else
        {
            return $output.$this->TemplateFiller->fill("catalog/product_edit/", "container-without-wysiwyg.tpl.html", array());
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
    		$result .= $this->TemplateFiller->fill("catalog/product_edit/", "group.tpl.html", array());
    		// output the attribute
    		$this->_counter = 0;

    		if($group['id'] == 1 && !$product_type_has_been_outputed)
    		{
    		    $product_type_has_been_outputed = 1;
    		    //Add "ProductType" row to first group.
    		    $this->_counter++;
    		    $this->_attr = array();
   		    	$this->_attr['name'] = '<div>'.$obj->getMessage( new ActionMessage('PRD_PRDTYPE_NAME')).'*</div>';
                $this->_attr['tag'] = 'TypeID';
    		    $this->_attr['unit'] = '';
    		    $this->_attr['pattern_type'] = 'integer';
        		$template = "attr.tpl.html";
                $product_type_desc = modApiFunc('Catalog', 'getProductType', $this->product_info['TypeID']);
        	    $this->_attr['value'] = $product_type_desc['name'];
    		    $result .= $this->TemplateFiller->fill("catalog/product_edit/", $template, array());
    		}

    		foreach ($group['attr'] as $attr)
    		{
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
    		    $this->_attr['value'] = modApiFunc("Localization", "FloatToFormatStr", $attr['value'], $attr["patt_type"]);
                    if ($attr['view_tag'] == 'SEOPrefix')
                    {
                        $this->_attr['value_escaped'] = htmlspecialchars(urldecode($attr['value']));
                    }
    		    elseif (isset($attr['value_escaped']))
    		    {
                    $this->_attr['value_escaped'] = modApiFunc("Localization", "FloatToFormatStr", $attr['value_escaped'], $attr["patt_type"]);
    		    }
    		    else
    		    {
    		        $this->_attr['value_escaped'] = modApiFunc("Localization", "FloatToFormatStr", $attr['value'], $attr["patt_type"]);
    		    }
                $this->_attr['unit'] = $attr['unit_type_value'] != null ? $attr['unit_type_value'] : '';
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

    		        if ($this->isStockQuantityUsable() == false)
    		        {
                        $req_to_inve = new Request();
                        $req_to_inve->setView('PO_InvEditor');
                        $req_to_inve->setKey('parent_entity','product');
                        $req_to_inve->setKey('entity_id',$this->prod_id);
                        $this->_attr['InvEditorLink'] = '"'.$req_to_inve->getURL().'"';

                        if ( $attr['id'] == SKU_PRODUCT_ATTRIBUTE_ID ||
                             $attr['id'] == MIN_QUANITY_PRODUCT_ATTRIBUTE_ID ||
                             $attr['id'] == LOW_STOCK_LEVEL_PRODUCT_ATTRIBUTE_ID )
                        {
                            $template = "attr-text-stock-quantity-isnt-usable.tpl.html";
                        }

                        if ( $attr['id'] == QUANTITY_IN_STOCK_PRICE_PRODUCT_ATTRIBUTE_ID )
                        {
                            $template = "attr-text-stock-sum-of-inventory.tpl.html";
                            $this->_attr['value'] = modApiFunc('Product_Options','getQuantityInStockByInventoryTable','product', $this->prod_id);
                            if ($this->_attr['value'] === null)
                            {
                                $this->_attr['value'] = '';
                                $template = "attr-text-stock-sum-of-inventory-undef.tpl.html";
                            }
                        }
    		        }
    		    }
    		    elseif ($attr['input_type_name'] == 'textarea')
    		    {
    		        if ( ($attr['allow_html'] and $this->use_wysiwyg_editor === true) or (!$attr['allow_html']))
    		        {
            		    $template = "attr-textarea.tpl.html";
    		        }
    		        else
    		        {
    		            $template = "attr-textarea-preview.tpl.html";
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
    		        	    'selected' => $value == $attr['value']
    		        	    );
    		        }
            		$template = "attr-select.tpl.html";
    		    }
                elseif ($attr['input_type_name'] == 'checkboxgroup')
                {
                    $this->_attr_options = array();
                    if($attr['view_tag'] == 'MembershipVisibility')
                    {
                        $cgroups = modApiFunc('Customer_Account','getGroups', 'exclude unsigned');
                        $vcgroups = modApiFunc('Customer_Account','getVisibleExistingGroups',$attr['value']);
                        $radio = (empty($vcgroups) ? "world" : "custom");
                        $this->_attr_option[$radio] = " checked";
                        $i = 1;
                        foreach($cgroups as $value => $name)
                        {
                            $this->_attr_options[] = array (
                                'value' => $value,
                                'name' => $name,
                                'checked' => (isset($vcgroups[$value]) ? " checked" : ""),
                                'disabled' => (empty($vcgroups) ? " disabled" : ""),
                                'next_column' => (($i % 2 == 0) ? "</tr><tr>" : "")
                                );
                            $i++;
                        }
                        $template = "attr-checkboxgroup.tpl.html";
                    }
                }
    		    elseif ($attr['input_type_name'] == 'image')
    		    {
                    $imagesUrl = $application->getAppIni('URL_IMAGES_DIR');
                    if ($application->getCurrentProtocol() == "https" && $application->getAppIni('HTTPS_URL_IMAGES_DIR'))
                    {
                        $imagesUrl = $application->getAppIni('HTTPS_URL_IMAGES_DIR');
                    }
    		        $this->_attr['ImageURL'] = $imagesUrl . $attr['value']['url'];
    		        $this->_attr['ImageWidth'] = $attr['value']['width'];
    		        $this->_attr['ImageHeight'] = $attr['value']['height'];
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
                $result .= $this->TemplateFiller->fill("catalog/product_edit/", $template, array());
    		}

    		//Patch to create "images Upload" button row:
    		if($group['id'] == 2 &&
    		   ($this->ViewState['LargeImage'] == "" ||
    		    $this->ViewState['SmallImage'] == ""))
    		{
    		    $this->_counter++;
    		    if(modApiFunc("Catalog", "isImageFolderNotWritable"))
    		    {
    		        $result .= $this->TemplateFiller->fill("catalog/product_edit/", "upload_ctrl.images.error.tpl.html", array());
    		    }
    		    else
    		    {
    		        $result .= $this->TemplateFiller->fill("catalog/product_edit/", "upload_ctrl.images.tpl.html", array());
    		    }
    		}
    		$result .= $this->TemplateFiller->fill("catalog/product_edit/", "show-state.tpl.html", array());
    	}
    	return $result;
    }

    /**
     *                   "                            "
     */
    function getSavedOkMessage()
    {
        if(!isset($this->TemplateFiller))
        {
            $this->TemplateFiller = $application->getInstance('TmplFiller');
        }
        if($this->SavedOk == 1)
        {
            $res = $this->TemplateFiller->fill("catalog/product_edit/", "saved_ok_msg.tpl.html", array());
            return $res;
        }
        else
        {
            return '';
        }
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('SYS',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("catalog/product_edit/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
    }

    /**
     * @ describe the function EditProductInfo->getErrors.
     */
    function getErrors()
    {
        global $application;
    	if (!is_array($this->ErrorsArray) || sizeof($this->ErrorsArray) == 0)
    	{
    		return "";
    	}
    	$result = "";

    	$i = 0;
    	foreach ($this->ErrorsArray as $error)
    	{
    	    $i++;
    	    $this->_error_index = $i;
    		$this->_error = $this->MessageResources->getMessage($error);
    		$result .= $this->TemplateFiller->fill("catalog/product_edit/", "error.tpl.html", array());
    	}

        $this->_error_list = $result;
        $result = $this->TemplateFiller->fill("catalog/product_edit/", "error_list.tpl.html", array());
    	return $result;
    }

    /**
     * @ describe the function EditProductInfo->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $imagesUrl = $application->getAppIni('URL_IMAGES_DIR');
        if ($application->getCurrentProtocol() == "https" && $application->getAppIni('HTTPS_URL_IMAGES_DIR'))
        {
            $imagesUrl = $application->getAppIni('HTTPS_URL_IMAGES_DIR');
        }
    	$value = null;
    	switch ($tag)
    	{
    	    case 'Items':
    	        $value = $this->getAttributes();
    	        break;

    	    case 'InvEditorLink':
    	        $value = isset($this->_attr['InvEditorLink']) ? $this->_attr['InvEditorLink'] : '';
    	        break;

    	    case 'Errors':
    	        $value = $this->getErrors();
    	        break;
            case "Error_List":
                $value = $this->_error_list;
                break;
    	    case 'AttributeName':
    	        $value = $this->_attr['name'];
    	        break;

    	    case 'AttributeValue':
    	        $value = $this->_attr['value'];
    	        break;

            case 'AttributeValueEscaped':
                $value = $this->_attr['value_escaped'];
                break;

    	    case 'AttributeTag':
    	        $value = $this->_attr['tag'];
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

    	    case 'AttributeImageURL':
    	        // there appears a bug if this string is used -
    	        // $this->_attr['ImageURL'];
    	        $value = $imagesUrl . $this->ViewState['LargeImage'];
    	        break;

    	    case 'AttributeImageWidth':
    	        $value = $this->_attr['ImageWidth'];
    	        break;

    	    case 'AttributeImageHeight':
    	        $value = $this->_attr['ImageHeight'];
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
        	        $value .= $this->TemplateFiller->fill("catalog/product_edit/", "attr-select-option.tpl.html", array());
    	        }
    	        break;

            case 'AttributeCheckboxes':
                $value = "";
                foreach ($this->_attr_options as $opt_value)
                {
                    $this->_attr_option = $opt_value;
                    $value .= $this->TemplateFiller->fill("catalog/product_edit/", "attr-checkboxgroup-item.tpl.html", array());
                }
                break;

            case 'AttributeNextCheckboxColumn':
                $value = $this->_attr_option['next_column'];
                break;

            case 'AttributeCheckedCheckbox':
                $value = $this->_attr_option['checked'];
                break;

            case 'AttributeDisabledCheckbox':
                $value = $this->_attr_option['disabled'];
                break;

            case 'AttributeWorldVisible':
                $value = @$this->_attr_option['world'];
                break;

            case 'AttributeCustomVisible':
                $value = @$this->_attr_option['custom'];
                break;

    	    case 'AttributeOptionValue':
    	        $value = $this->_attr_option['value'];
    	        break;

    	    case 'AttributeOptionSelected':
    	        if ($this->_attr_option['selected'])
    	            $value = " selected";
    	        break;

            case 'AttributeOptionName':
                $name = $this->_attr_option['name'];
                $value = ($this->_attr['tag'] != 'MembershipVisibility' ? prepareHTMLDisplay($name) : $name);
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
        	        $value = $imagesUrl . $this->ViewState['LargeImage'];
    	        }
    	        break;

            case 'ViewStateLargeImageWidth':
                $value = '';
                if ($this->ViewState['LargeImage'])
                {
                    $image_path = $application->getAppIni('PATH_IMAGES_DIR') . $this->ViewState['LargeImage'];
                    $sizes = getimagesize($image_path);
                    $value = $sizes[0];
                }
                break;

            case 'ViewStateLargeImageHeight':
                $value = '';
                if ($this->ViewState['LargeImage'])
                {
                    $image_path = $application->getAppIni('PATH_IMAGES_DIR') . $this->ViewState['LargeImage'];
                    $sizes = getimagesize($image_path);
                    $value = $sizes[1];
                }
                break;

    	    case 'ViewStateSmallImage':
    	        $value = '';
    	        if ($this->ViewState['SmallImage'])
    	        {
        	        $value = $imagesUrl . $this->ViewState['SmallImage'];
    	        }
    	        break;

    	    case 'ViewStateSmallImageRelativePath':
    	        //Hidden Value
    	        $value = '';
    	        if ($this->ViewState['SmallImage'])
    	        {
        	        $value = $imagesUrl . $this->ViewState['SmallImage'];
    	        }
    	        break;

    	    case 'ViewStateClose':
    	        $value = $this->ViewState['hasCloseScript'];
    	        break;

    	    case 'ErrorIndex':
    	        $value = $this->_error_index;
    	        break;

    	    case 'Error':
    	        $value = $this->_error;
    	        break;

            case 'SavedOkMessage':
                $value = $this->getSavedOkMessage();
                break;

    	    case 'InputStyleClass':
    	        $classes = array();
    	        if ($this->_error != '') { $classes[] = 'error'; }
    	        if ($this->allow_html) { $classes[] = 'tiny_mce'; }
    	        $value = implode(' ', $classes);
    	        break;

    	    case 'SubmitUploadImagesScript':
    	        $value = 'onclick="CatalogEditProduct.FormSubmitValue.value = \'UploadImages\';CatalogEditProduct.submit();disableButtons(new Array(\'SaveButton1\', \'SaveButton2\', \'CancelButton1\', \'CancelButton2\', \'UploadButton\'));return true;"';
    	        break;

    	    case 'ImagesUploadErrorMessage':
    	        $value = $this->MessageResources->getMessage("SETUP_WARNING_IMAGE_FOLDER_IS_NOT_WRITABLE", array("0" => modApiFunc("Catalog", "getImagesDir")));
    	        break;

    	    case 'UseWYSIWYGFor':
    	        $value = array();
    	        foreach ($this->product_info['attributes'] as $view_tag => $attr)
    	        {
                    //                                    ,      PriceExcludingTaxes.
                    if(isset($attr['allow_html']))
                    {
        	        	if($attr['allow_html'] && $attr['visible'])
        	        	    $value[] = $view_tag;
                    }
    	        }
    	        $value = implode(', ', $value);
    	        break;

    	    case 'Breadcrumb':
                $obj = &$application->getInstance('Breadcrumb');
                $value = $obj->output(false);
    	        break;

            case 'Local_ProductBookmarks':
                $value = getProductBookmarks('details',$this->prod_id,'edit');
                break;

            case 'CancelLink':
                $req = new Request();
                $req->setView('Catalog_ProdInfo');
                $req->setAction('SetCurrentProduct');
                $req->setKey('prod_id',$this->prod_id);
                $value = $req->getURL();
                break;

            case 'additionalJS':
                if(modApiFunc('Session','is_set','mustReloadParent'))
                {
                    modApiFunc('Session','un_set','mustReloadParent');
                    $value = "if (window.opener && window.opener.document.ProductSearchForm && window.opener.document.ProductSearchForm.active && window.opener.document.ProductSearchForm.active.value == 'Y') window.opener.document.ProductSearchForm.submit(); else if (window.opener) window.opener.location.reload();\n";
                };
                break;
/*(
            case 'AutoGenSmallImageComment':
                $is_present_large_image = false;
                foreach($this->_group['attr'] as $attr_info)
                {
                    if($attr_info['view_tag'] == 'LargeImage' and $attr_info['visible'] == 1)
                    {
                        $is_present_large_image = true;
                    };
                };
                if($this->_attr['tag'] == 'SmallImage' and $is_present_large_image and $this->ViewState['LargeImage'] == '')
                {
                    $pi_settings = modApiFunc('Product_Images','getSettings');
                    if($pi_settings['AUTO_GEN_MAIN_SMALL_IMAGE'] == 'Y' and function_exists('gd_info'))
                    {
                        $value = getMsg('PI','COMMENT_AUTO_GEN_SMALL_IMAGE');
                    };
                };
                */
            case 'AutoGenSmallImageComment':
                $is_present_large_image = false;
                foreach($this->_group['attr'] as $attr_info)
                {
                    if($attr_info['view_tag'] == 'LargeImage' && $attr_info['visible'] == 1)
                    {
                        $is_present_large_image = true;
		                $pi_settings = modApiFunc('Product_Images','getSettings');
		                if($pi_settings['AUTO_GEN_MAIN_SMALL_IMAGE'] == 'Y' && function_exists('gd_info'))
		                {
		                    $value = getMsg('PI','COMMENT_AUTO_GEN_SMALL_IMAGE');
		                };
                    };
                };
                break;

            case 'ResultMessageRow':
            	$value = $this->outputResultMessage();
                break;

            case 'ResultMessage':
                $value = $this->_Template_Contents[$tag];
                break;

            case 'ProductInfoLink':
                $cz_layouts = LayoutConfigurationManager::static_get_cz_layouts_list();
                LayoutConfigurationManager::static_activate_cz_layout(array_shift(array_keys($cz_layouts)));
                $request = new CZRequest();
                $request->setView  ( 'ProductInfo' );
                $request->setAction( 'SetCurrentProduct' );
                $request->setKey   ( 'prod_id', $this->prod_id);
                $request->setProductID($this->prod_id);
                $value = $request->getURL();
                break;

    	    case 'NoImagePath':
        	    $value = $imagesUrl . 'noimage.png';
    	        break;

    	    default:
        	    if (_ml_strpos($tag, 'Product') === 0)
        	    {
//                    $tag = preg_replace('/_/', '', $tag);
//                    $tag = preg_replace('/([A-Z]{1,})/', '_$1', $tag);
//                    $arr = preg_split('/_/', $tag, -1, PREG_SPLIT_NO_EMPTY);
//                    $entity = _ml_strtolower(array_shift($arr));
//                    $tag = implode('', $arr);
                    $tag = _ml_substr($tag, _ml_strlen('Product'));
            	    if (array_key_exists($tag, $this->POST)) {
                    	$value = $this->POST[$tag];
            	    }
        	    }
        	    if (_ml_strpos($tag, 'Group') === 0)
        	    {
//                    $tag = preg_replace('/_/', '', $tag);
//                    $tag = preg_replace('/([A-Z]{1,})/', '_$1', $tag);
//                    $arr = preg_split('/_/', $tag, -1, PREG_SPLIT_NO_EMPTY);
//                    $entity = _ml_strtolower(array_shift($arr));
//                    $tag = _ml_strtolower(implode('', $arr));
                    $tag = _ml_strtolower(_ml_substr($tag, _ml_strlen('Group')));
            	    if (array_key_exists($tag, $this->_group)) {
                    	$value = $this->_group[$tag];
            	    }
        	    }
        		break;
    	}
    	return $value;
    }

    /**
     * Pointer to the received from action or prepared FORM data.
     */
    var $POST;

    /**
     * View state structure. It comes from action.
     * $SessionPost["ViewState"] structure example:
     * <br>array
     * <br>(
     * <br>    "hasCloseScript"  = "false"           //true/false
     * <br>    "ErrorsArray"     =  array()          //true/false
     * <br>    "LargeImage"      = "image.jpg"       //
     * <br>    "SmallImage"      = "small_image.jpg" //
     * <br>)
     */
    var $ViewState;

    /**
     * List of error ids. It comes from action.
     */
    var $ErrorsArray;

    var $product_info_ext;
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