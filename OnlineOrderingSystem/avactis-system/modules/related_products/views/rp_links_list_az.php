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
 * @package RelatedProducts
 * @author Egor V. Derevyankin
 *
 */

class RP_LinksList
{
    function RP_LinksList()
    {}

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('RP',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("related_products/misc/", "result-message.tpl.html",array());
        }
        elseif(modApiFunc("Session","is_set","Errors"))
        {
            $return_html_code="";
            $errors=modApiFunc("Session","get","Errors");
            modApiFunc("Session","un_set","Errors");
            foreach($errors as $ekey => $eval)
            {
                $template_contents=array(
                    "ErrorMessage" => getMsg('RP',$eval)
                );
                $this->_Template_Contents=$template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $return_html_code.=$this->mTmplFiller->fill("related_products/misc/", "error-message.tpl.html",array());
            }

            return $return_html_code;
        }
        else
            return "";
    }

    function out_RelatedList()
    {
        $html_code = '';

        if(!empty($this->RPLinks))
        {
            global $application;

            foreach($this->RPLinks as $rp_id)
            {
                $obj = new CProductInfo($rp_id);

                $tags = array(
                    'RelatedID' => $rp_id
                   ,'RelatedName' => $obj->getProductTagValue('Name')
                   ,'jsControlPListFunc' => ' '.str_replace(array('%PID%'),array($rp_id),$this->pb_obj->getControlPListFunction())
                );

                $this->_Template_Contents = $tags;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $html_code .= $this->mTmplFiller->fill("related_products/rp_links_list/", "rp_item.tpl.html",array());
            };
        };

        return $html_code;
    }

    function out_jsRelatedArray()
    {
        $js_code = 'var related_array = new Array();'."\n";

        if(!empty($this->RPLinks))
        {
            foreach($this->RPLinks as $rp_id)
            {
                $js_code .= 'related_array[related_array.length] = '.$rp_id.";\n";
            };
        };

        return $js_code;
    }

    function out_RPSortForm()
    {
        global $application;

        $tags = array(
        );

        $this->_Template_Contents = $tags;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("related_products/rp_links_list/", "sort_form.tpl.html",array());
    }

    function output()
    {
        global $application;
        $request = new Request();
        $this->product_id = $request->getValueByKey('product_id');

        $prod_obj = &$application->getInstance('CProductInfo',$this->product_id);
        $this->RPLinks = modApiFunc('Related_Products','getRPIDsForProduct',$this->product_id);

        $pbrowser_params = array(
            'show_category_path' => true
           ,'buttons' => array(
                'add' => array(
                    'label' => 'BTN_ADD'
                   ,'callback' => 'addProductToRPList(%PID%,%PNAME%);'
                   ,'default_state' => 'disabled'
                   ,'enable_condition' => 'product_selected'
                )
            )
           ,'choosed_control_array' => 'related_array'
        );

        $request->setView('related_products');
        $request->setAction('save_rp_links');
        $request->setKey('product_id', $this->product_id);

        loadClass('ProductsBrowser');
        $this->pb_obj = new ProductsBrowser();

        $template_contents = array(
            'ProductName' => $prod_obj->getProductTagValue('Name')
           ,'Local_ProductBookmarks' => getProductBookmarks('related',$this->product_id)
           ,'ResultMessage' => $this->outputResultMessage()
           ,'Local_ProductsBrowser' => $this->pb_obj->output($pbrowser_params) //ProductsBrowser($pbrowser_params)
           ,'jsRelatedArray' => $this->out_jsRelatedArray()
           ,'RelatedList' => $this->out_RelatedList()
           ,'RPFormAction' => $request->getURL()
           ,'RPSortForm' => $this->out_RPSortForm()
           ,'jsControlPListFunc' => str_replace(array('%PID%'),array('product_id'),$this->pb_obj->getControlPListFunction())
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("related_products/rp_links_list/", "container.tpl.html",array());
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
    var $RPLinks;
    var $pb_obj;
};

?>