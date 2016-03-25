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
 * @package ProductOptions
 * @author Egor V. Derevyankin
 *
 */

class ProductOptionsWarnings
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'product-options-options-warnings.ini'
           ,'files' => array(
                'OptionsWarningsContainer' => TEMPLATE_FILE_SIMPLE
               ,'OptionsWarningMessage' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function ProductOptionsWarnings()
    {
        global $application;

        #check if fatal errors exist of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("ProductOptionsWarnings"))
        {
            $this->NoView = true;
        }
    }

    function outputWarningsList()
    {
        $html_code = "";
        foreach($this->discard_by as $key => $discard_msg)
        {
            $this->cur_dis_key = $key;
            $html_code .= $this->templateFiller->fill("OptionsWarningMessage");
        };
        return $html_code;
    }

    function output()
    {
        if(!modApiFunc('Session','is_set','OptionsDiscardedBy'))
        {
            return '';
        };

        global $application;

        $request = &$application->getInstance('Request');
        $this->__sets = modApiFunc('Product_Options','getOptionsSettingsForEntity','product',$request->getValueByKey('prod_id'));
        $this->discard_by = modApiFunc('Session','get','OptionsDiscardedBy');
        modApiFunc('Session','un_set','OptionsDiscardedBy');
        if(is_string($this->discard_by))
            $this->discard_by = array($this->discard_by);
        $this->MR = &$application->getInstance('MessageResources','messages','CustomerZone');

        $_template_tags = array(
                                "Local_OptionsWarningsList" => ''
                               ,"Local_WarningIndex" => ''
                               ,"Local_WarningMessage" => ''
                               );
        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('ProductOptionsWarnings');
        $this->templateFiller->setTemplate($this->template);

        return $this->templateFiller->fill("OptionsWarningsContainer");
    }

    function getTag($tag)
    {
        $value = null;
        switch($tag)
        {
            case 'Local_OptionsWarningsList':
                $value = $this->outputWarningsList();
                break;
            case 'Local_WarningIndex':
                if (count($this->discard_by) == 1)
                    $value = "";
                else
                    $value = $this->cur_dis_key+1;
                break;
            case 'Local_WarningMessage':
                if(array_key_exists($this->discard_by[$this->cur_dis_key], $this->__sets))
                    $value = $this->__sets[$this->discard_by[$this->cur_dis_key]];
                else
                    $value = $this->MR->getMessage($this->discard_by[$this->cur_dis_key]);
                break;
        }
        return $value;
    }

    var $MR;
    var $discard_by;
    var $cur_dis_key;
    var $__sets;
};

?>