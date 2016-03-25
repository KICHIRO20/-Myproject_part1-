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
 * Catalog->ProductInfo View.
 * Views product info for the AdminZone.
 *
 * @package Catalog
 * @access  public
 * @author Alexey Kolesnikov
 */
class ProductInfo extends Catalog_ProdInfo_Base
{

    /**
     * @ describe the function ProductInfo->getAttributes.
     */
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
    		$result .= $this->TemplateFiller->fill("catalog/product_info/", "group.tpl.html", array());

    		// output the attribute
    		$this->_counter = 0;

    		if($group['id'] == 1 && !$product_type_has_been_outputed)
    		{
    		    $product_type_has_been_outputed = 1;
    		    //Add "ProductType" row to first group.
    		    $this->_counter++;
    		    $this->_attr = array();
   		    	$this->_attr['name'] = '<div class="required">'.$obj->getMessage( new ActionMessage('PRD_PRDTYPE_NAME')).'*</div>';
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
    		    // skip invisible attributes
    		    if (!$attr['visible'])
    		    {
    		    	continue;
    		    }
    		    $this->_counter++;
    		    $this->_attr = array();
    		    $template = "";
    		    if ($attr['required'])
    		    {
    		    	$this->_attr['name'] = '<div class="required">'.$attr['name'].'*</div>';
    		    }
    		    else
    		    {
    		    	$this->_attr['name'] = $attr['name'];
    		    }
    		    $this->_attr['tag'] = $attr['view_tag'];
    		    $this->_attr['value'] = modApiFunc("Localization", "format", $attr['value'], $attr['patt_type']);
    		    $this->_attr['unit'] = ($attr['value'] != null && $attr['patt_type'] != "currency") ? $attr['unit_type_value'] : '';

    		    if ($attr['input_type_name'] == 'select')
    		    {
    		        $this->_attr['value'] = array_key_exists($attr['value'], $attr['input_type_values']) ? $attr['input_type_values'][$attr['value']] : '';
                    //                          Product Status.
                    if($attr['view_tag'] == 'Available')
                    {
                        //Product Status: Online/Offline
                        switch($attr['value'])
                        {
                            case PRODUCT_STATUS_ONLINE:
                                $this->_attr['value'] = "<b>".$this->_attr['value']."</b>";
                                break;
                            case PRODUCT_STATUS_OFFLINE:
                                $this->_attr['value'] = "<span style='color: #FF0000;'><b>".$this->_attr['value']."</b></span>";
                                break;
                        }
                    }

            		$template = "attr-select.tpl.html";
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
    		    	if (!$attr['value']['exists'])
    		    	{
                        $template = "attr-no-image.tpl.html";
    		    	}
    		    	elseif ($attr['view_tag'] == 'LargeImage')
    		    	{
                        $template = "attr-large-image.tpl.html";
    		    	}
    		    	elseif ($attr['view_tag'] == 'SmallImage')
    		    	{
                        $template = "attr-small-image.tpl.html";
    		    	}
    		    }
    		    else
    		    {
                    $template = "attr.tpl.html";
    		    }
                $result .= $this->TemplateFiller->fill("catalog/product_info/", $template, array());
    		}
    		$result .= $this->TemplateFiller->fill("catalog/product_info/", "show-state.tpl.html", array());
    	}
    	return $result;
    }

    /**
     * Outputs the product info.
     */
    function output()
    {
        global $application;

        $this->groups = $this->getProductInfo();
        $this->TemplateFiller = $application->getInstance('TmplFiller');

        $request = new Request();
        if ($request->getValueByKey( 'del_info' ) != "true")
        {
            $application->registerAttributes(array('EditLink'));
        }

        $application->registerAttributes(array('Local_ProductBookmarks'));

        $application->registerAttributes(array(
             'Button'
            ,'GroupName'
            ,'GroupID'
            ,'Counter'
            ,'additionalJS'
        ));

        return $this->TemplateFiller->fill("catalog/product_info/", "container.tpl.html", array());
    }

    /**
     * @ describe the function ProductInfo->getTag.
     */
    function getTag($tag)
    {
        global $application;
    	$value = null;
    	switch ($tag)
    	{
    	    case 'Items':
    	        $value = $this->getAttributes();
    	        break;

    	    case 'AttributeName':
    	        $value = $this->_attr['name'];
    	        break;

    	    case 'AttributeValue':
                if ($this->_attr['tag'] == 'TaxClass')
                {
                    $value = prepareHTMLDisplay($this->_attr['value']);
                }
                else
                {
        	        $value = $this->_attr['value'];
                }
    	        break;

    	    case 'AttributeTag':
    	        $value = $this->_attr['tag'];
    	        break;

    	    case 'AttributeUnit':
    	        $value = $this->_attr['unit'];
    	        break;

    	    case 'AttributeImageURL':
    	        $value = $this->_attr['ImageURL'];
    	        break;

    	    case 'AttributeImageWidth':
    	        $value = $this->_attr['ImageWidth'];
    	        break;

    	    case 'AttributeImageHeight':
    	        $value = $this->_attr['ImageHeight'];
    	        break;

    	    case 'AttributeImage':
    	        $value = '<img src="' . $this->_attr['ImageURL'] . '" width="' . $this->_attr['ImageWidth'] . '" height="' . $this->_attr['ImageHeight'] . '">'  ;
    	        break;

    	    case 'Counter':
    	        $value = $this->_counter;
    	        break;

    	    case 'EditLink':
                $request = new Request();
                $request->setView  ( 'Catalog_EditProduct' );
                $request->setAction( 'SetCurrentProduct' );
                $request->setKey   ( 'prod_id', $this->prod_id );
                $value = $request->getURL();
                break;
            case 'Button':
                $request = &$application->getInstance('Request');
                if ($request->getValueByKey( 'del_info' ) == "true")
                {
                    $value = $this->TemplateFiller->fill("catalog/product_info/", "button_close.tpl.html", array());
                }
                else
                {
                    $value = $this->TemplateFiller->fill("catalog/product_info/", "button_edit.tpl.html", array());
                }
                break;

    	    case 'Breadcrumb':
                $obj = &$application->getInstance('Breadcrumb');
                $value = $obj->output(false);
    	        break;

            case 'Local_ProductBookmarks':
                $value = getProductBookmarks('details',$this->prod_id);
                break;

            case 'additionalJS':
                if(modApiFunc('Session','is_set','mustReloadParent'))
                {
                    modApiFunc('Session','un_set','mustReloadParent');
                    $value = "if (window.opener && window.opener.document.ProductSearchForm && window.opener.document.ProductSearchForm.active && window.opener.document.ProductSearchForm.active.value == 'Y') window.opener.document.ProductSearchForm.submit(); else if (window.opener) window.opener.location.reload();\n";
                };
                break;

    		default:
                list($entity, $tag) = getTagName($tag);
        	    if ($entity == 'product')
        	    {
        	        $value = getKeyIgnoreCase($tag, $this->product_info);
        	    }
        	    elseif ($entity == 'group')
        	    {
        	        $value = getKeyIgnoreCase($tag, $this->_group);
        	    }
        		break;
    	}
    	return $value;
    }

    var $TemplateFiller;
    var $groups;
    var $_group;
    var $_attr;
    var $_counter;
}
?>