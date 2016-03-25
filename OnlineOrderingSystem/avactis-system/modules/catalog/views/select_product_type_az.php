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
 * Catalog->SelectProductType View.
 * Views the form to select the product type.
 * It is used as the first step when adding the product.
 *
 * @package Catalog
 * @access  public
 * @author Alexey Kolesnikov
 */
class SelectProductType
{
    /**
     * Outputs the form to select the product type.
     */
    function output()
    {
        global $application;

        $this->TemplateFiller = $application->getInstance('TmplFiller');
        $application->registerAttributes(array(
             'TypeID'
            ,'TypeName'
            ,'TypeSelected'
        ));
        return $this->TemplateFiller->fill("catalog/product_add/", "select-type.tpl.html", array());
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
    		case 'Items':
                $types = modApiFunc('Catalog', 'getProductTypes');
                $value = '';
                foreach ($types as $type)
                {
                    $this->_type = $type;
                    $value .= $this->TemplateFiller->fill("catalog/product_add/", "type.tpl.html", array());
                }
    			break;

    	    case 'TypeID':
    	        $value = $this->_type['id'];
    	        break;

    	    case 'TypeName':
    	        $value = $this->_type['name'];
    	        break;

    	    case 'TypeSelected':
    	        $value = $this->_type['id'] == 1 ? 'checked' : '';
    	        break;

    		default:
    			break;
    	}
    	return $value;
    }
    var $TemplateFiller;
    var $_type;
}
?>