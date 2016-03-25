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

/**
 * Catalog->AttributeHelp View.
 * Views supplemental info about attribute.
 *
 * @package Catalog
 * @access  public
 * @author Alexey Kolesnikov
 */
class AttributeHelp
{
    /**
     * Outputs the form to select the product type.
     */
    function output($type_id, $view_tag, $attr_of_obj="prod_attr")
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        $this->TemplateFiller = $application->getInstance('TmplFiller');
        switch ($attr_of_obj)
        {
            case 'prod_attr':
                if (!(_ml_strpos($view_tag, "TypeID")===false))
                {
                    $this->attr['name'] = $obj->getMessage( new ActionMessage('PRD_PRDTYPE_NAME'));
                    $this->attr['descr'] = $obj->getMessage( new ActionMessage('PRD_PRDTYPE_DESCR'));
                    $this->attr['view_tag'] = 'TypeName';
                }
                elseif (!(_ml_strpos($view_tag, "ProductType")===false))
                {
                    $obj = &$application->getInstance('MessageResources');
                    switch ($view_tag)
                    {
                        case 'ProductTypeId':
                            $this->attr['name'] = $obj->getMessage( new ActionMessage('PT_TYPEID_NAME'));
                            $this->attr['descr'] = $obj->getMessage( new ActionMessage('PT_TYPEID_DESCR'));
                            $this->attr['view_tag'] = 'TypeID';
                            break;
                        case 'ProductType':
                            $this->attr['name'] = $obj->getMessage( new ActionMessage('PT_TYPENAME_NAME'));
                            $this->attr['descr'] = $obj->getMessage( new ActionMessage('PT_TYPENAME_DESCR'));
                            $this->attr['view_tag'] = 'TypeName';
                            break;
                        case 'ProductTypeDescr':
                            $this->attr['name'] = $obj->getMessage( new ActionMessage('PT_TYPEDESCR_NAME'));
                            $this->attr['descr'] = $obj->getMessage( new ActionMessage('PT_TYPEDESCR_DESCR'));
                            $this->attr['view_tag'] = 'TypeDescr';
                            break;
                    }
                }
                else
                {
                    $this->product_type = modApiFunc('Catalog', 'getProductType', $type_id);
                    $this->attr = $this->product_type['attr'][$view_tag];
                    if ($this->attr["type"] == "custom")
                    {
                        $this->attr['view_tag'] .= "Custom";
                    }
                }
                $this->attr['view_tag'] = "Product".$this->attr['view_tag'];
//                return $this->TemplateFiller->fill("catalog/attribute_help/", "container.tpl.html", array());
				return '<i class="fa fa-question-circle popovers" data-container="body" data-placement="right" data-html="true" data-content="'.$this->attr['descr'].'</br></br><b>PHP '.getmsg('SYS','HELP_ATTR_TAG').'</b> '.$this->attr['view_tag'].'();" data-original-title="'.$this->attr['name'].'"></i>';
                break;
            case 'cat_attr':
                switch ($view_tag)
                {
                    case 'SubcategoryID':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CAT_ID_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CAT_ID_DESCR'));
                        $this->attr['view_tag'] = 'CategoryID';
                        break;
                    case 'Subcategory':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CAT_NAME_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CAT_NAME_DESCR'));
                        $this->attr['view_tag'] = 'CategoryName';
                        break;
                    case 'CategoryDescription':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CAT_DESCR_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CAT_DESCR_DESCR'));
                        $this->attr['view_tag'] = 'CategoryDescription';
                        break;
                    case 'LargeImage':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CAT_LRGIMG_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CAT_LRGIMG_DESCR'));
                        $this->attr['view_tag'] = 'CategoryLargeImageSrc';
                        break;
                    case 'SmallImage':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CAT_SMLIMG_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CAT_SMLIMG_DESCR'));
                        $this->attr['view_tag'] = 'CategorySmallImageSrc';
                        break;
                    case 'ImageDescription':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CAT_IMGDESCR_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CAT_IMGDESCR_DESCR'));
                        $this->attr['view_tag'] = 'CategoryImageAltText';
                        break;
                    case 'PageTitle':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CAT_PAGETTL_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CAT_PAGETTL_DESCR'));
                        $this->attr['view_tag'] = 'CategoryPageTitle';
                        break;
                    case 'MetaKeywords':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CAT_METAKWRD_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CAT_METAKWRD_DESCR'));
                        $this->attr['view_tag'] = 'CategoryMetaKeywords';
                        break;
                    case 'MetaDescription':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CAT_METADESCR_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CAT_METADESCR_DESCR'));
                        $this->attr['view_tag'] = 'CategoryMetaDescription';
                        break;
                }
//                return $this->TemplateFiller->fill("catalog/attribute_help/", "container.tpl.html", array());
				return '<i class="fa fa-question-circle popovers" data-container="body" data-placement="right" data-html="true" data-content="'.$this->attr['descr'].'</br></br><b>PHP '.getmsg('SYS','HELP_ATTR_TAG').'</b> '.$this->attr['view_tag'].'();" data-original-title="'.$this->attr['name'].'"></i>';
                break;
            case 'custsl_attr':
                switch ($view_tag)
                {
                    case 'AttributeTag':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CUSTSL_TAG_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CUSTSL_TAG_DESCR'));
                        $this->attr['view_tag'] = '';
                        break;
                    case 'AttributeName':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CUSTSL_NAME_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CUSTSL_NAME_DESCR'));
                        $this->attr['view_tag'] = '';
                        break;
                    case 'AttributeDescr':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CUSTSL_DESCR_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CUSTSL_DESCR_DESCR'));
                        $this->attr['view_tag'] = '';
                        break;
                    case 'AttributeValues':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CUSTSL_VALUES_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CUSTSL_VALUES_DESCR'));
                        $this->attr['view_tag'] = '';
                        break;
                }
//                return $this->TemplateFiller->fill("catalog/attribute_help/", "cust_container.tpl.html", array());
				return '<i class="fa fa-question-circle popovers" data-container="body" data-placement="right" data-html="true" data-content="'.$this->attr['descr'].'" data-original-title="'.$this->attr['name'].'"></i>';
                break;
            case 'custbt_attr':
                switch ($view_tag)
                {
                    case 'AttributeTag':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CUSTBT_TAG_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CUSTBT_TAG_DESCR'));
                        $this->attr['view_tag'] = '';
                        break;
                    case 'AttributeName':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CUSTBT_NAME_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CUSTBT_NAME_DESCR'));
                        $this->attr['view_tag'] = '';
                        break;
                    case 'AttributeDescr':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CUSTBT_DESCR_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CUSTBT_DESCR_DESCR'));
                        $this->attr['view_tag'] = '';
                        break;
                    case 'AttributeValues':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CUSTSL_VALUES_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CUSTSL_VALUES_DESCR'));
                        $this->attr['view_tag'] = '';
                        break;
                    case 'AttributeDefaultValue':
                        $this->attr['name'] = $obj->getMessage( new ActionMessage('CUSTBT_DEFVAL_NAME'));
                        $this->attr['descr'] = $obj->getMessage( new ActionMessage('CUSTBT_DEFVAL_DESCR'));
                        $this->attr['view_tag'] = '';
                        break;
                }
//                return $this->TemplateFiller->fill("catalog/attribute_help/", "cust_container.tpl.html", array());
				return '<i class="fa fa-question-circle popovers" data-container="body" data-placement="right" data-html="true" data-content="'.$this->attr['descr'].'" data-original-title="'.$this->attr['name'].'"></i>';
                break;
        }
    }

    /**
     * @ describe the function SelectProductType->getTag.
     */
    function getTag($tag)
    {
    	global $application;
    	$value = null;
    	switch ($tag)
    	{
    		case 'ProductTypeName':
    		    $value = $this->product_type['name'];
    			break;
    		case 'AttributeTag':
    		    $value = $this->attr['view_tag'];
    			break;
    		case 'AttributeName':
    		    $value = $this->attr['name'];
    			break;
    		case 'AttributeDescr':
    		    $value = $this->attr['descr'];
    			break;
    		case 'AttributeType':
    		    if ($this->attr['type'] == 'standard')
    		    {
    		    	$value = 'General';
    		    }
    		    elseif ($this->attr['type'] == 'custom')
    		    {
    		    	$value = 'Custom';
    		    }
    		    else
    		    {
    		    	$value = 'Unknown';
    		    }
    			break;
    		case 'AttributeDefault':
		        $default = $this->attr['default'];
    		    if ($this->attr['input_type_name'] == 'select')
    		    {
    		    	$value = $this->attr['input_type_values'][$default];
    		    }
    		    else
    		    {
    		        $value = $default;
    		    }
    			break;
    	}
    	return $value;
    }
    var $TemplateFiller;
    var $product_type;
    var $attr = array();
    var $_unit;
}
?>