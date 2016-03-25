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
 * @package Bestsellers
 * @author Egor V. Derevyankin
 *
 */

class BS_LinksList
{
    function BS_LinksList()
    {
        loadCoreFile('html_form.php');
    }

    function out_ReloadCategoryReview()
    {
        global $application;
        $res = '';
        if(modApiFunc("Session","is_set","ResultMessage")) {
            $vars = array('TreeID' => $this->tree_id, 'CategoryID' => $this->category_id);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $res = $this->mTmplFiller->fill("bestsellers/misc/", "reload_category_review.tpl.html", $vars);
        }
        return $res;
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('BS',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("bestsellers/misc/", "result-message.tpl.html",array());
        }
        elseif(modApiFunc("Session","is_set","Errors"))
        {
            $return_html_code="";
            $errors=modApiFunc("Session","get","Errors");
            modApiFunc("Session","un_set","Errors");
            foreach($errors as $ekey => $eval)
            {
                $template_contents=array(
                    "ErrorMessage" => getMsg('BS',$eval)
                );
                $this->_Template_Contents=$template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $return_html_code.=$this->mTmplFiller->fill("bestsellers/misc/", "error-message.tpl.html",array());
            }

            return $return_html_code;
        }
        else
            return "";
    }

    function out_BestsellersList()
    {
        $html_code = '';

        if(!empty($this->BSLinks))
        {
            global $application;

            foreach($this->BSLinks as $bs_id)
            {
                $obj = new CProductInfo($bs_id);

                $tags = array(
                    'BestsellerID' => $bs_id
                   ,'BestsellerName' => $obj->getProductTagValue('Name')
                   ,'jsControlPListFunc' => ' '.str_replace(array('%PID%'),array($bs_id),$this->pb_obj->getControlPListFunction())
                );

                $this->_Template_Contents = $tags;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $html_code .= $this->mTmplFiller->fill("bestsellers/bs_links_list/", "bs_item.tpl.html",array());
            };
        };

        return $html_code;
    }

    function out_jsBestsellersArray()
    {
        $js_code = 'var bestsellers_array = new Array();'."\n";

        if(!empty($this->BSLinks))
        {
            foreach($this->BSLinks as $bs_id)
            {
                $js_code .= 'bestsellers_array[bestsellers_array.length] = '.$bs_id.";\n";
            };
        };

        return $js_code;
    }

    function out_BSSortForm()
    {
        global $application;

        $tags = array(
        );

        $this->_Template_Contents = $tags;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("bestsellers/bs_links_list/", "sort_form.tpl.html",array());
    }

    function out_Breadcrumb()
    {
        $path_array = modApiFunc('Catalog','getCategoryFullPath',$this->category_id);
        $_names = array();

        foreach($path_array as $inf)
        {
            $_names[] = $inf['name'];
        };

        return implode("&nbsp;&gt;&gt;&nbsp;",$_names);
    }

    function out_SettingsForm()
    {
        global $application;

        $yes_no_array = array(
            array('value' => 'Y', 'contents' => getMsg('BS','LBL_YES'))
           ,array('value' => 'N', 'contents' => getMsg('BS','LBL_NO'))
        );

        $set_afs_select = array(
            'select_name' => 'sets[ADD_BS_FROM_STAT]'
           ,'selected_value' => $this->sets['ADD_BS_FROM_STAT']
           ,'onChange' => 'setSettingsFormState();'
           ,'values' => $yes_no_array
        );

        $multipliers = array(1,7,30,365);

        $set_fsp_value = $this->sets['BS_FROM_STAT_PERIOD'];
        end($multipliers);
        do{
            $divider = 3600 * 24 * current($multipliers);
            prev($multipliers);
        }while($set_fsp_value % $divider != 0);

        $set_fsp_value = $set_fsp_value / $divider;

        $set_fsp_select = array(
            'select_name' => 'sets[BS_FROM_STAT_PERIOD][type]'
           ,'selected_value' => $divider
           ,'values' => array(
                array('value' => (3600 * 24 * 1), 'contents' => getMsg('BS','LBL_DAY'))
               ,array('value' => (3600 * 24 * 7), 'contents' => getMsg('BS','LBL_WEEK'))
               ,array('value' => (3600 * 24 * 30), 'contents' => getMsg('BS','LBL_MONTH'))
               ,array('value' => (3600 * 24 * 365), 'contents' => getMsg('BS','LBL_YEAR'))
           )
        );

        $set_sr_select = array(
            'select_name' => 'sets[SHOW_RECURSIVELY]'
           ,'selected_value' => $this->sets['SHOW_RECURSIVELY']
           ,'values' => $yes_no_array
        );

        $tags = array(
            'setsAFSselect' => HtmlForm::genDropdownSingleChoice($set_afs_select)
           ,'setsFSCvalue' => prepareHTMLDisplay($this->sets['BS_FROM_STAT_COUNT'])
           ,'setsFSPvalue' => $set_fsp_value
           ,'setsFSPtype' => HtmlForm::genDropdownSingleChoice($set_fsp_select)
           ,'setsSRselect' => HtmlForm::genDropdownSingleChoice($set_sr_select)
        );

        $this->_Template_Contents = $tags;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("bestsellers/bs_links_list/", "settings_form.tpl.html",array());
    }

    function output()
    {
        global $application;
        $request = new Request();
        $this->category_id = $request->getValueByKey('category_id');
        $this->tree_id = $request->getValueByKey('tree_id');

        //$cat_obj = &$application->getInstance('CCategoryInfo',$this->category_id);
        $this->BSLinks = modApiFunc('Bestsellers_API','getHardBSLinksForCategory',$this->category_id);
        $this->sets = modApiFunc('Bestsellers_API','getSettings',$this->category_id);

        $pbrowser_params = array(
            'show_category_path' => true
           ,'buttons' => array(
                'add' => array(
                    'label' => 'BTN_ADD'
                   ,'callback' => 'addProductToBSList(%PID%,%PNAME%);'
                   ,'default_state' => 'disabled'
                   ,'enable_condition' => 'product_selected'
                )
            )
           ,'choosed_control_array' => 'bestsellers_array'
        );

        $request->setView('PopupWindow');
       // $request->setView('BS_LinksList');
        $request->setAction('save_bs_links_and_settings');
        $request->setKey('category_id', $this->category_id);
        $request->setKey('tree_id', $this->tree_id);

        loadClass('ProductsBrowser');
        $this->pb_obj = new ProductsBrowser();

        $template_contents = array(
            'ReloadCategoryReview' => $this->out_ReloadCategoryReview(),
            'Breadcrumb' => $this->out_Breadcrumb()
           ,'Local_CategoryBookmarks' => getCategoryBookmarks('bestsellers',$this->category_id)
           ,'ResultMessage' => $this->outputResultMessage()
           ,'Local_ProductsBrowser' => $this->pb_obj->output($pbrowser_params) //ProductsBrowser($pbrowser_params)
           ,'jsBestsellersArray' => $this->out_jsBestsellersArray()
           ,'BestsellersList' => $this->out_BestsellersList()
           ,'BSFormAction' => $request->getURL()
           ,'BSSortForm' => $this->out_BSSortForm()
           ,'jsControlPListFunc' => str_replace(array('%PID%'),array('product_id'),$this->pb_obj->getControlPListFunction())
           ,'SettingsForm' => $this->out_SettingsForm()
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("bestsellers/bs_links_list/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $category_id;
    var $BSLinks;
    var $pb_obj;
    var $sets;
};

?>