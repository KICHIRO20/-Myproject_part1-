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

class MngProductCats
{
    function MngProductCats()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $this->product_id = $request->getValueByKey('product_id');
        $prodObj = &$application->getInstance('CProductInfo',$this->product_id);
        $this->productCats = $prodObj->getCategoriesIDs();
        $this->allCats = modApiFunc('Catalog','getSubcategoriesFullListWithParent',1,false);
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('CTL',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("catalog/product_categories/", "result-message.tpl.html",array());
        }
        elseif(modApiFunc("Session","is_set","Errors"))
        {
            $return_html_code="";
            $errors=modApiFunc("Session","get","Errors");
            modApiFunc("Session","un_set","Errors");
            foreach($errors as $ekey => $eval)
            {
                $template_contents=array(
                    "ErrorMessage" => getMsg('CTL',$eval)
                );
                $this->_Template_Contents=$template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $return_html_code.=$this->mTmplFiller->fill("catalog/product_categories/", "error-message.tpl.html",array());
            }

            return $return_html_code;
        }
        else
            return "";
    }

    function out_jsCatsArray()
    {
        $js_code = 'cats_arr = new Array('."\n";

        foreach($this->allCats as $k => $cat_info)
        {
            if($k > 0)
                $js_code .= ',';
            $js_code .= '{status: \''.(in_array($cat_info['id'],$this->productCats)?'s':'n').'\','.
                        'display_string: \''.str_repeat('&nbsp;',$cat_info['level']*3).addslashes($cat_info['name']).'\','.
                        'el_id: \''.$cat_info['id'].'\'}'."\n";
        };

        $js_code .= ');'."\n";
        return $js_code;
    }

    function output()
    {
        global $application;

        if(modApiFunc('Session','is_set','mustReloadParent'))
        {
            modApiFunc('Session','un_set','mustReloadParent');
            $must_reload = true;
        }
        else
        {
            $must_reload = false;
        };

        $prodObj = &$application->getInstance('CProductInfo',$this->product_id);

        $template_contents = array(
            'Local_ProductBookmarks' => getProductBookmarks('categories',$this->product_id)
           ,'ResultMessage' => $this->outputResultMessage()
           ,'jsCatsArray' => $this->out_jsCatsArray()
           ,'ProductID' => $this->product_id
           ,'ProductName' => $prodObj->getProductTagValue('Name')
           ,'reloadPrent' => $must_reload ? 'if(!(!window.opener || window.opener.closed)){if (window.opener && window.opener.document.ProductSearchForm && window.opener.document.ProductSearchForm.active && window.opener.document.ProductSearchForm.active.value == \'Y\') window.opener.document.ProductSearchForm.submit(); else window.opener.location.reload();};' : ''
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("catalog/product_categories/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        if ($tag == 'ProductInfoLink') {
            $cz_layouts = LayoutConfigurationManager::static_get_cz_layouts_list();
            LayoutConfigurationManager::static_activate_cz_layout(array_shift(array_keys($cz_layouts)));
            $request = new CZRequest();
            $request->setView  ( 'ProductInfo' );
            $request->setAction( 'SetCurrentProduct' );
            $request->setKey   ( 'prod_id', $this->product_id);
            $request->setProductID($this->product_id);
            return $request->getURL();
        }
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $product_id;
    var $productCats;
    var $allCats;
};

?>