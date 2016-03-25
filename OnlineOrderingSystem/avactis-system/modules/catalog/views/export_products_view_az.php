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

class ExportProductsView
{
    function ExportProductsView()
    {
        loadCoreFile('html_form.php');
    }

    function outputCategoriesPaths($cats)
    {
        $js_code = 'var categories_paths = new Array();'."\n";

        $last_parents_on_levels = array();

        for($i=0;$i<count($cats);$i++)
        {
            $cat = $cats[$i];
            $cpath = '';
            if(isset($last_parents_on_levels[$cat['level']-1]))
                $cpath = $last_parents_on_levels[$cat['level']-1]['path'];
            $cpath.='/'.$cat['name'];

            $last_parents_on_levels[$cat['level']] = array('id'=>$cat['id'],'path'=>$cpath);
            $js_code .= 'categories_paths['.$cat['id'].'] = \''.addslashes(_ml_substr($cpath,1)).'\';'."\n";
        };

        return $js_code;
    }

    function output()
    {
        global $application;

        $cats_select = array(
            'select_name'    => 'ProductCategory'
           ,'id'             => 'ProductCategory'
           ,'selected_value' => 1
           ,'values'         => array()
        );

        $cats = modApiFunc("Catalog", "getSubcategoriesFullListWithParent", 1, false);

        foreach($cats as $cat)
            $cats_select['values'][]=array('value'=>$cat['id'],'contents'=>str_repeat('&nbsp;&nbsp;',$cat['level']).$cat['name']);

        $template_contents = array(
            'ProductListSubcategories' => HtmlForm::genDropdownSingleChoice($cats_select,' style="width: 290px; font-family: courier new; font-size: 11px;"')
           ,'CategoriesPaths' => $this->outputCategoriesPaths($cats)
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("catalog/export_products/", "container-2.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }
};

?>