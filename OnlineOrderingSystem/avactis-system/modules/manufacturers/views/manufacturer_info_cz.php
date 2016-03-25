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

class ManufacturerInfo
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'manufacturer-info-config.ini'
           ,'files' => array(
                'Manufacturer' => TEMPLATE_FILE_SIMPLE
               ,'ManufacturerNotDefined' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function ManufacturerInfo()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("ManufacturerInfo"))
        {
            $this->NoView = true;
        }
    }

    function output()
    {
        if($this->NoView)
        {
            return '';
        };

        global $application;
        $args=func_get_args();
        $this->_ManufacturerId = NULL;
        if(sizeof($args) > 0)
        {
            $this->_ManufacturerId = $args[0];
            modApiFunc("tag_param_stack", "push", __CLASS__, array(array("key" => TAG_PARAM_MNF_ID, "value" => $this->_ManufacturerId)));
        }

        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('ManufacturerInfo');
        $this->templateFiller->setTemplate($this->template);

        //mnf_id                     ,           $args[0]         .         , store block
        //  'Manufacturer'                                     ProductInfo,       mnf_id
        //                                    .
        $mnf_id = __info_tag_output_find_tag_params('manufacturer');
        $mnf_info = modApiFunc("Manufacturers", "getManufacturerInfo", $mnf_id);

        if($mnf_id == PARAM_NOT_FOUND          /*       <?php  ManufacturerInfo(); ?>                                    */ ||
           $mnf_id == MANUFACTURER_NOT_DEFINED /*                     Manufacturer */ ||
           $mnf_info === NULL                  /* <?php  Manufacturer('asdf') ?> */)
        {
            $ret = $this->templateFiller->fill('ManufacturerNotDefined');
        }
        else
        {
            $ret = $this->templateFiller->fill('Manufacturer');
        }

        if(sizeof($args) > 0)
        {
            modApiFunc("tag_param_stack", "pop", __CLASS__);
        }
        return $ret;
    }

    function getTag($tag)
    {
        return null;
    }
}

?>