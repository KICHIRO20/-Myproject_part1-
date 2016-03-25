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
 * @package Manufacturers
 * @author Vadim Lyalikov
 *
 */

class ManufacturersFilter
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'manufacturers-filter.ini'
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

    function ManufacturersFilter()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("ManufacturersBox"))
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
        $selected_mnf_id = modApiFunc("CProductListFilter", "getCurrentManufactureId");
        foreach($this->_Items as $item)
        {
            if($item['id'] == MANUFACTURER_NOT_DEFINED)
            {
                continue;
            }
            $this->_Manufacturer = $item;
            modApiFunc("tag_param_stack", "push", __CLASS__, array
            (
                array("key" => TAG_PARAM_MNF_ID, "value" => $this->_Manufacturer['id'])
            ));

            //                       -
            if($selected_mnf_id == $this->_Manufacturer['id'])
            {
                $item_res = $this->templateFiller->fill('ItemSelected');
            }
            else
            {
                $item_res = $this->templateFiller->fill('Item');
            }

            modApiFunc("tag_param_stack", "pop", __CLASS__);
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
        $this->template = $application->getBlockTemplate('ManufacturersFilter');
        $this->templateFiller->setTemplate($this->template);

        $this->_Items = modApiFunc('Manufacturers','getManufacturerProductAttributeValues', true, false);
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
                   ,'Local_ManufacturerProductsLink' => ''
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
            case 'Local_ManufacturerProductsLink':
                $value = $this->getManufacturerURL($this->_Manufacturer['id']);
                break;
            case 'Local_Items':
                $value = $this->outputItems();
                break;
        };

        return $value;
    }

    function getManufacturerURL($id)
    {
                $r = new Request();
                $r->setView('ProductList');
                $r->setAction('SetCurrMnf');
                $r->setKey( 'mnf_id', $id );

                return ($r->getURL());
    }

    var $_Manufacturer;
    var $_Items;
}

?>