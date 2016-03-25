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

class CProductListTagSettings
{
    function CProductListTagSettings()
    {
        global $application;
        $this->__template = $application->getBlockTemplate('ProductList');//

        //                c default
        loadClass('CProductListFilter');
        $this->product_list_filter_object = new CProductListFilter();
        $this->updateFilterParams();
    }

    function updateFilterParams()
    {
        $this->filter = $this->product_list_filter_object->getProductListParamsObject();
        $this->filter->use_paginator = true;
    }

    function getTemplate()
    {
        return $this->__template;
    }

    function setTemplateDirectory($dir)
    {


    }

    var $__template = array();
    var $filter = null;
    var $product_list_filter_object = null;
}

class CProductSetTagSettings
{
    function CProductSetTagSettings()
    {
        global $application;
        $this->template = array(
            'Directory'         => 'catalog/product-set/default/',
            'Container'         => 'product-set-container.tpl.html',
            'ContainerEmpty'    => 'product-set-container-empty.tpl.html',
            'Item'              => 'product-set-item.tpl.html',
            'ItemOutOfStock'    => 'product-set-item-out-of-stock.tpl.html'
        );

        //                c default
        loadClass('CProductListFilter');
        $f = new CProductListFilter();
        $this->filter = $f->getProductListParamsObject();
        $this->filter->use_paginator = true;
    }

    function getTemplate()
    {
        return $this->template;
    }

    function setTemplateDirectory($dir)
    {

    }

    var $template = array();
    var $filter = null;
}

?>