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
 * @package FeaturedProducts
 * @author Egor V. Derevyankin
 *
 */

class FP_LinksList
{
    function FP_LinksList()
    {}

    function out_ReloadCategoryReview()
    {
        global $application;
        $res = '';
        if(modApiFunc("Session","is_set","ResultMessage")) {
            $vars = array('TreeID' => $this->tree_id, 'CategoryID' => $this->category_id);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $res = $this->mTmplFiller->fill("featured_products/misc/", "reload_category_review.tpl.html", $vars);
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
                "ResultMessage" => getMsg('FP',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("featured_products/misc/", "result-message.tpl.html",array());
        }
        elseif(modApiFunc("Session","is_set","Errors"))
        {
            $return_html_code="";
            $errors=modApiFunc("Session","get","Errors");
            modApiFunc("Session","un_set","Errors");
            foreach($errors as $ekey => $eval)
            {
                $template_contents=array(
                    "ErrorMessage" => getMsg('FP',$eval)
                );
                $this->_Template_Contents=$template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $return_html_code.=$this->mTmplFiller->fill("featured_products/misc/", "error-message.tpl.html",array());
            }

            return $return_html_code;
        }
        else
            return "";
    }

    function out_FeaturedList()
    {
        $html_code = '';

        if(!empty($this->FPLinks))
        {
            global $application;

            foreach($this->FPLinks as $fp_id)
            {
                $obj = new CProductInfo($fp_id);

                $tags = array(
                    'FeaturedID' => $fp_id
                   ,'FeaturedName' => $obj->getProductTagValue('Name')
                   ,'jsControlPListFunc' => ' '.str_replace(array('%PID%'),array($fp_id),$this->pb_obj->getControlPListFunction())
                );

                $this->_Template_Contents = $tags;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $html_code .= $this->mTmplFiller->fill("featured_products/fp_links_list/", "fp_item.tpl.html",array());
            };
        };

        return $html_code;
    }

    function out_jsFeaturedArray()
    {
        $js_code = 'var featured_array = new Array();'."\n";

        if(!empty($this->FPLinks))
        {
            foreach($this->FPLinks as $fp_id)
            {
                $js_code .= 'featured_array[featured_array.length] = '.$fp_id.";\n";
            };
        };

        return $js_code;
    }

    function out_FPSortForm()
    {
        global $application;

        $tags = array(
        );

        $this->_Template_Contents = $tags;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("featured_products/fp_links_list/", "sort_form.tpl.html",array());
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

    function output()
    {
        global $application;
        $request = new Request();
        $this->category_id = $request->getValueByKey('category_id');
        $this->tree_id = $request->getValueByKey('tree_id');

        //$cat_obj = &$application->getInstance('CCategoryInfo',$this->category_id);
        $this->FPLinks = modApiFunc('Featured_Products','getFPIDsForCategory',$this->category_id);

        $pbrowser_params = array(
            'show_category_path' => true
           ,'buttons' => array(
                'add' => array(
                    'label' => 'BTN_ADD'
                   ,'callback' => 'addProductToFPList(%PID%,%PNAME%);'
                   ,'default_state' => 'disabled'
                   ,'enable_condition' => 'product_selected'
                )
            )
           ,'choosed_control_array' => 'featured_array'
        );

        $request->setView('PopupWindow');
        $request->setAction('save_fp_links');
        $request->setKey('category_id', $this->category_id);
        $request->setKey('tree_id', $this->tree_id);

        loadClass('ProductsBrowser');
        $this->pb_obj = new ProductsBrowser();

        $template_contents = array(
            'ReloadCategoryReview' => $this->out_ReloadCategoryReview(),
            'Breadcrumb' => $this->out_Breadcrumb()
           ,'Local_CategoryBookmarks' => getCategoryBookmarks('featured',$this->category_id)
           ,'ResultMessage' => $this->outputResultMessage()
           ,'Local_ProductsBrowser' => $this->pb_obj->output($pbrowser_params) //ProductsBrowser($pbrowser_params)
           ,'jsFeaturedArray' => $this->out_jsFeaturedArray()
           ,'FeaturedList' => $this->out_FeaturedList()
           ,'FPFormAction' => $request->getURL()
           ,'FPSortForm' => $this->out_FPSortForm()
           ,'jsControlPListFunc' => str_replace(array('%PID%'),array('product_id'),$this->pb_obj->getControlPListFunction())
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("featured_products/fp_links_list/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $category_id;
    var $FPLinks;
    var $pb_obj;
};

?>