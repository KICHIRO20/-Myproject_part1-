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

class ImportProductsView
{
    function ImportProductsView()
    {
        loadCoreFile('html_form.php');
    }

    function output()
    {
        global $application;

        $ptypes_select = array(
            'select_name'    => 'TargetPType'
           ,'id'             => 'TargetPType'
           ,'selected_value' => '0'
           ,'values'         => array()
        );

        $ptypes = modApiFunc('Catalog', 'getProductTypes');
        foreach($ptypes as $ptype)
            $ptypes_select['values'][]=array('value'=>$ptype['id'],'contents'=>$ptype['name']);


        $cats_select = array(
            'select_name'    => 'TargetCategory'
           ,'id'             => 'TargetCategory'
           ,'selected_value' => 1
           ,'values'         => array()
        );

        $cats = modApiFunc("Catalog", "getSubcategoriesFullListWithParent", 1, false);

        foreach($cats as $cat)
            $cats_select['values'][]=array('value'=>$cat['id'],'contents'=>str_repeat('&nbsp;&nbsp;',$cat['level']).$cat['name']);

        $template_contents = array(
            'PTypesList' => HtmlForm::genDropdownSingleChoice($ptypes_select,' style="width: 290px;"')
           ,'CategoriesList' => HtmlForm::genDropdownSingleChoice($cats_select,' style="width: 290px;"')
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("catalog/import_products/", "container-2.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }
};

?>