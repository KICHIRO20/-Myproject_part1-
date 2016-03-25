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
 * @package ProductImages
 * @author Egor V. Derevyankin
 *
 */

class PI_Settings
{
    function PI_Settings()
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
                "ResultMessage" => getMsg('PI', $msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("product_images/settings/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
    }

    function output()
    {
        global $application;

        $settings = modApiFunc('Product_Images','getSettings');

        $detailedFullImageResizeSelect = array(
            "select_name" => "pi_sets[RESIZE_DETAILED_LARGE_IMAGE]"
           ,"selected_value" => $settings['RESIZE_DETAILED_LARGE_IMAGE']
           ,"values" => array(
                array('value' => 'Y', 'contents' => getMsg('PI','LBL_YES'))
               ,array('value' => 'N', 'contents' => getMsg('PI','LBL_NO'))
               )
           );

        $fullImageResizeSelect = array(
            "select_name" => "pi_sets[RESIZE_LARGE_IMAGE]"
           ,"selected_value" => $settings['RESIZE_LARGE_IMAGE']
           ,"values" => array(
                array('value' => 'Y', 'contents' => getMsg('PI','LBL_YES'))
               ,array('value' => 'N', 'contents' => getMsg('PI','LBL_NO'))
               )
           );

           $ag_select = array(
            "select_name" => "pi_sets[AUTO_GEN_MAIN_SMALL_IMAGE]"
           ,"selected_value" => $settings['AUTO_GEN_MAIN_SMALL_IMAGE']
           ,"values" => array(
                array('value' => 'Y', 'contents' => getMsg('PI','LBL_YES'))
               ,array('value' => 'N', 'contents' => getMsg('PI','LBL_NO'))
           )
           );

        $ag_param = '';
        if(!function_exists('gd_info'))
        {
            $ag_select["selected_value"] = "N";
            $ag_param = 'disabled';
        };

        $cat_ag_select = array(
            "select_name" => "pi_sets[AUTO_GEN_CAT_SMALL_IMAGE]"
           ,"selected_value" => $settings['AUTO_GEN_CAT_SMALL_IMAGE']
           ,"values" => array(
                array('value' => 'Y', 'contents' => getMsg('PI','LBL_YES'))
               ,array('value' => 'N', 'contents' => getMsg('PI','LBL_NO'))
           )
           );

        $cat_ag_param = '';
        if(!function_exists('gd_info'))
        {
            $cat_ag_select["selected_value"] = "N";
            $cat_ag_param = 'disabled';
        };

        $template_contents = array(
            'TSField' => HtmlForm::genInputTextField('25','pi_sets[THUMB_SIDE]','5',$settings['THUMB_SIDE'])
           ,'TPLField' => HtmlForm::genInputTextField('25','pi_sets[THUMBS_PER_LINE]','5',$settings['THUMBS_PER_LINE'])
           ,'TPLSetting' => $application->getAppIni('PRODUCT_LIST_DISABLE_TR_TD')
           ,'AGField' => HtmlForm::genDropdownSingleChoice($ag_select, $ag_param)
           ,'CatAGField' => HtmlForm::genDropdownSingleChoice($cat_ag_select, $cat_ag_param)
           ,'fullImageResizeField' => HtmlForm::genDropdownSingleChoice($fullImageResizeSelect, $ag_param)
           ,'detailedFullImageResizeField' => HtmlForm::genDropdownSingleChoice($detailedFullImageResizeSelect, $ag_param)
           ,'MISField' => HtmlForm::genInputTextField('25','pi_sets[MAIN_IMAGE_SIDE]','5',$settings['MAIN_IMAGE_SIDE'],$ag_param)
           ,'CatMISField' => HtmlForm::genInputTextField('25','pi_sets[CAT_IMAGE_SIDE]','5',$settings['CAT_IMAGE_SIDE'],$cat_ag_param)
           ,'firField' => HtmlForm::genInputTextField('25','pi_sets[LARGE_IMAGE_SIZE]','5',$settings['LARGE_IMAGE_SIZE'],$ag_param)
           ,'dfirField' => HtmlForm::genInputTextField('25','pi_sets[DETAILED_LARGE_IMAGE_SIZE]','5',$settings['DETAILED_LARGE_IMAGE_SIZE'],$ag_param)
           ,"ResultMessage"      => $this->outputResultMessage()
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_images/settings/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
};

?>