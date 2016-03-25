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
 * @package Catalog
 * @author Ravil Garafutdinov
 *
 */

class ProductTypeFilter
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'product-type-filter.ini'
           ,'files' => array(
                'Container' => TEMPLATE_FILE_SIMPLE
               ,'ContainerEmpty' => TEMPLATE_FILE_SIMPLE
               ,'Item' => TEMPLATE_FILE_SIMPLE
               ,'ItemSelected' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function ProductTypeFilter()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("ProductTypeBox"))
        {
            $this->NoView = true;
        }
    }

/*    function getViewCacheKey()
    {
        return modApiFunc("CProductListFilter", "getCurrentManufactureId");
    }//*/

    function outputItems()
    {
    	$res = "";

    	$current_filter = modApiFunc("Catalog", "getCurrentProductTypeFilter");
        if (!is_array($current_filter) || empty($current_filter))
        {
            $current_filter = false;
        }

    	foreach($this->_Items as $item)
    	{
    		$this->_ProductType = $item;

            //                       -
            if($current_filter === false || in_array($this->_ProductType['id'], $current_filter))
            {
                $item_res = $this->templateFiller->fill('ItemSelected');
            }
            else
            {
                $item_res = $this->templateFiller->fill('Item');
            }

            $res .= $item_res;
    	}
    	return $res;
    }

    function output()
    {
        if($this->NoView)
        {
            return '';
        };

        global $application;

        $_template_tags = array(
            'Local_FormActionURL'
          );

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('ProductTypeFilter');
        $this->templateFiller->setTemplate($this->template);

        $this->_Items = modApiFunc("Catalog", 'getProductTypes');

        if (sizeof($this->_Items) == 0
            || count($this->_Items) == 1)
        {
            return $this->templateFiller->fill('ContainerEmpty');
        }
        else
        {
            $application->registerAttributes
            (
                array
                (
                    'Local_Items' => ''
                   ,'Local_ProductTypeFormLink' => ''
                   ,'Local_ProductTypeName' => ''
                   ,'Local_ProductTypeId' => ''
                )
            );
            return $this->templateFiller->fill('Container');

        };
    }

    function getTag($tag)
    {
        $value = null;

        switch($tag)
        {
        	case 'Local_ProductTypeFormLink':

                $r = new Request();
                $r->setView('ProductList');
                $r->setAction('SetProductTypeFilter');
                $value = $r->getURL();

                break;
        	case 'Local_ProductTypeName':
                $value = $this->_ProductType['name'];
                break;
        	case 'Local_ProductTypeId':
                $value = $this->_ProductType['id'];
                break;
        	case 'Local_Items':
        		$value = $this->outputItems();
        		break;
        };

        return $value;
    }

    function getManufacturerURL($id)
    {
    }

    var $_ProductType;
    var $_Items;
}