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
 * @author Egor V. Derevyankin
 *
 */

class CategoriesBrowser
{
    function CategoriesBrowser()
    {}

    function out_initDtreeByCats()
    {
        $cats = $this->cats;

        $prods_qty = array();
        if(@$this->params['show_products_count'] == true)
        {
            $pre_prods_qty = modApiFunc('Catalog','getProductsQuantityByCategories');
            foreach($pre_prods_qty as $pq)
            {
                $prods_qty[$pq['c_id']] = $pq['count_p_id'];
            };
        };
        $js_code = '';

        $last_parents_on_levels = array();

        for($i=0;$i<count($cats);$i++)
        {
            $cat = $cats[$i];
            $parent_id = 0;

            if(isset($last_parents_on_levels[$cat['level']-1]))
            {
                $parent_id = $last_parents_on_levels[$cat['level']-1];
            };

            $last_parents_on_levels[$cat['level']] = $cat['id'];

            $cat_id = $cat['id'] - 1;
            $parent_id = $parent_id - 1;
            $callback = 'javascript: '.$this->unq_prefix.'_OnCategoryClick('.$cat['id'].');';

            $add_to_name = '';
            if(@$this->params['show_products_count'] == true)
            {
                $prods_count = array_key_exists($cat['id'], $prods_qty) ? $prods_qty[$cat['id']] : 0;
                $add_to_name = "&nbsp;<span style=\"color: gray; font-size: 7pt;\">(".$prods_count.")</span>";
            };

            $js_code .= $this->unq_prefix."_d.add(".$cat_id.",".$parent_id.",'".addslashes($cat['name']).$add_to_name."','".$callback."');\n";
        };

        return $js_code;
    }

    function out_jsCatsPathsArray()
    {
        $cats = $this->cats;

        $js_code = 'var '.$this->unq_prefix.'_categories_paths = new Array();'."\n";

        $last_parents_on_levels = array();

        for($i=0;$i<count($cats);$i++)
        {
            $cat = $cats[$i];
            $cpath = '';
            if(isset($last_parents_on_levels[$cat['level']-1]))
                $cpath = $last_parents_on_levels[$cat['level']-1]['path'] . ' &gt;&gt; ';

            $cpath .= $cat['name'];

            $last_parents_on_levels[$cat['level']] = array('id'=>$cat['id'],'path'=>$cpath);
            $js_code .= $this->unq_prefix.'_categories_paths['.$cat['id'].'] = \''.addslashes($cpath).'\';'."\n";
        };

        return $js_code;
    }

    function out_CategoryPath()
    {
        $html_code = '';

        if(@$this->params['show_category_path'])
        {
            global $application;
            $tags = array(
                'UnqPrefix' => $this->unq_prefix
               ,'Local_CatPathPrefix' => isset($this->params['category_path_prefix']) ? $this->params['category_path_prefix'] : ''
               ,'Local_CatPath' => getMsg('CTL','NO_CATEGORY_SELECTED')
               ,'jsCatsPathsArray' => $this->out_jsCatsPathsArray()
            );

            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("catalog/categories_browser/", "category_path.tpl.html",$tags);
        };

        return $html_code;
    }

    function out_Local_Buttons()
    {
        global $application;
        $html_code = '';

        foreach($this->params['buttons'] as $but_name => $but_info)
        {
            $style_classes = array(
                'button'
            );

            if(!isset($but_info['style_class']))
            {
                $style_classes[] = 'button_small';
            }
            else
            {
                $style_classes[] = $but_info['style_class'];
            };

            if(@$but_info['default_state'] == 'disabled')
            {
                $style_classes[] = 'button_disabled';
            }

            $tags = array(
                'ButtonID' => $this->unq_prefix.'_'.$but_name
               ,'ButtonClass' => implode(' ',$style_classes)
               ,'ButtonCallback' => (@$but_info['default_state'] == 'disabled') ? '' : 'javascript: '.$but_info['callback'].';'
               ,'ButtonLabel' => getMsg('SYS',$but_info['label'])
            );

            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("catalog/categories_browser/", "button.tpl.html",$tags);
        };

        return $html_code;
    }

    function out_Buttons()
    {
        $html_code = '';

        if(@!empty($this->params['buttons']))
        {
            global $application;
            $tags = array(
                'Local_Buttons' => $this->out_Local_Buttons()
            );

            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("catalog/categories_browser/", "buttons_container.tpl.html",$tags);
        };

        return $html_code;
    }

    function out_jsSetButtonsStates()
    {
        $js_code = '';

        if(@!empty($this->params['buttons']))
        {
            foreach($this->params['buttons'] as $but_name => $but_info)
            {
                $callback_js_string =  $but_info['callback'];

                $button_id = $this->unq_prefix."_".$but_name;

                if(@$but_info['enable_condition'] == 'category_selected')
                {
                    $condition = "cat_id != 0";
                };
                $js_code .= "if({$condition}) { enableButton('{$button_id}',function() { {$callback_js_string} } ); } else { disableButton('{$button_id}'); } ;\n";
            };
        };

        return $js_code;
    }

    function _prepareButtonsCallbacks()
    {
        if(@!empty($this->params['buttons']))
        {
            foreach($this->params['buttons'] as $but_name => $but_info)
            {
                $callback_js_string = str_replace(
                    array("%CID%")
                   ,array("cat_id")
                   ,$but_info['callback']
                );

                $this->params['buttons'][$but_name]['callback'] = $callback_js_string;
            };
        };
    }

    function output($params)
    {
        global $application;

        $this->unq_prefix = uniqid('pb_');
        $this->params = $params;
        $this->_prepareButtonsCallbacks();
        $this->cats = modApiFunc("Catalog", "getSubcategoriesFullListWithParent", 1, false);

        $tags = array(
            'UnqPrefix' => $this->unq_prefix
           ,'initDtreeByCats' => $this->out_initDtreeByCats()
           ,'CategoryPath' => $this->out_CategoryPath()
           ,'Buttons' => $this->out_Buttons()
           ,'jsSetButtonsStates' => $this->out_jsSetButtonsStates()
        );

        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("catalog/categories_browser/", "container.tpl.html",$tags);
    }

    var $unq_prefix;
    var $params;
    var $cats;
};

?>