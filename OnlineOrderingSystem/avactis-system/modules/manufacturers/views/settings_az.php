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

class MNF_Settings
{
    function MNF_Settings()
    {
        loadCoreFile('html_form.php');
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('MNF', $msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("manufacturers/settings/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
    }

    function output()
    {
        global $application;

        $settings = modApiFunc('Manufacturers','getSettings');

        $ag_select = array(
            "select_name" => "pi_sets[AUTO_GEN_MAIN_SMALL_IMAGE]"
           ,"selected_value" => $settings['AUTO_GEN_MAIN_SMALL_IMAGE']
           ,"values" => array(
                array('value' => 'Y', 'contents' => getMsg('MNF','LBL_YES'))
               ,array('value' => 'N', 'contents' => getMsg('MNF','LBL_NO'))
           )
        );

        $ag_param = '';
        if(!function_exists('gd_info'))
        {
            $ag_select["selected_value"] = "N";
            $ag_param = 'disabled';
        };

        $template_contents = array(
            'TSField' => HtmlForm::genInputTextField('25','pi_sets[THUMB_SIDE]','5',$settings['THUMB_SIDE'])
           ,'TPLField' => HtmlForm::genInputTextField('25','pi_sets[THUMBS_PER_LINE]','5',$settings['THUMBS_PER_LINE'])
           ,'AGField' => HtmlForm::genDropdownSingleChoice($ag_select, $ag_param)
           ,'MISField' => HtmlForm::genInputTextField('25','pi_sets[MAIN_IMAGE_SIDE]','5',$settings['MAIN_IMAGE_SIDE'],$ag_param)
           ,"ResultMessage"      => $this->outputResultMessage()
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("manufacturers/settings/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
};

?>