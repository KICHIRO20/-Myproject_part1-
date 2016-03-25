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

loadClass('DataWriterDefault');

class DataWriterProductsDB extends DataWriterDefault
{
    function DataWriterProductsDB()
    {
        global $application;
        $this->MR = &$application->getInstance('MessageResources','catalog-messages','AdminZone');
    }

    function initWork($settings)
    {
        $this->clearWork();
        $this->_settings = array();
        $this->_build_cats_paths();
        $this->_process_info['status'] = 'INITED';
    }

    function doWork($data)
    {
        if($data['item_status'] == 'illegal')
            return;

        if($data['item_status'] == 'new')
        {
            $product_info = $data['fine_data'];
            $ptype_id = $product_info['ptype_id'];
            unset($product_info['ptype_id']);
            $category_id = $this->_get_cat_id_by_path($product_info['Category']);
            unset($product_info['Category']);
            $product_images = array(
                'small' => isset($product_info['SmallImage']) ? $product_info['SmallImage'] : ''
               ,'large' => isset($product_info['LargeImage']) ? $product_info['LargeImage'] : ''
            );
            $product_info['SmallImage'] = '';
            $product_info['LargeImage'] = '';

            $new_product_id = modApiFunc('Catalog','addProductInfo',$ptype_id,$category_id,$product_info);
            modApiFunc('Catalog','_attachImageToProduct',$new_product_id,$product_images['small'],'SmallImage');
            modApiFunc('Catalog','_attachImageToProduct',$new_product_id,$product_images['large'],'LargeImage');

            $from_entity = "ptype";
            $to_entity = "product";
            $old_eid = $ptype_id;
            $new_eid = $new_product_id;

            $tmap = modApiFunc("Product_Options","copyAllOptionsFromEntityToEntity",$from_entity,$old_eid,$to_entity,$new_eid);
            modApiFunc("Product_Options","copyAllOptionsSettingsFromEntityToEntity",$from_entity,$old_eid,$to_entity,$new_eid);
            modApiFunc("Product_Options","copyAllCRulesFromEntityToEntity",$from_entity,$old_eid,$to_entity,$new_eid,$tmap);
//            modApiFunc("Product_Options","copyAllInventoryFromEntityToEntity",$from_entity,$old_eid,$to_entity,$new_eid,$tmap);
        };

        if($data['item_status'] == 'exist')
        {
            $product_info = $data['fine_data'];
            $product_id = $product_info['ID'];
            unset($product_info['ID']);
            $ptype_id = $product_info['ptype_id'];
            //unset($product_info['ptype_id']);
            unset($product_info['Category']);
            $product_images = array(
                'small' => isset($product_info['SmallImage']) ? $product_info['SmallImage'] : ''
               ,'large' => isset($product_info['LargeImage']) ? $product_info['LargeImage'] : ''
            );
            unset($product_info['SmallImage'], $product_info['LargeImage']);
            modApiFunc('Catalog','_updateProduct',$product_id,$product_info);
            modApiFunc('Catalog','_attachImageToProduct',$product_id,$product_images['small'],'SmallImage');
            modApiFunc('Catalog','_attachImageToProduct',$product_id,$product_images['large'],'LargeImage');
        };

    }

    function finishWork()
    {
    }

    function saveWork()
    {
        modApiFunc('Session','set','DataWriterProductsDBSettings',$this->_settings);
    }

    function loadWork()
    {
        if(modApiFunc('Session','is_set','DataWriterProductsDBSettings'))
            $this->_settings = modApiFunc('Session','get','DataWriterProductsDBSettings');
        else
            $this->_settings = null;
    }

    function clearWork()
    {
        modApiFunc('Session','un_set','DataWriterProductsDBSettings');
        $this->_settings = null;
    }

    function _build_cats_paths()
    {
        $cats = modApiFunc('Catalog','getSubcategoriesFullListWithParent',1,false);

        $paths = array();

        $last_parents_on_levels = array();

        for($i=0;$i<count($cats);$i++)
        {
            $cat = $cats[$i];
            $cpath = '';
            if(isset($last_parents_on_levels[$cat['level']-1]))
                $cpath = $last_parents_on_levels[$cat['level']-1]['path'];
            $cpath.='/'.$cat['name'];

            $last_parents_on_levels[$cat['level']] = array('id'=>$cat['id'],'path'=>$cpath);
            $paths[$cat['id']]=_ml_substr($cpath,1);
        };

        $this->_settings['cats_paths'] = $paths;
    }

    function _get_cat_id_by_path($path,$automake=true)
    {
        if(in_array($path,$this->_settings['cats_paths']))
            return array_search($path,$this->_settings['cats_paths']);

        if($automake)
            return $this->_make_cat_path($path);

        return null;
    }

    function _make_cat_path($path)
    {
        $sub_cats = array_filter(explode('/',$path));
        $sub_paths = array($sub_cats[0]);
        for($i=1;$i<count($sub_cats);$i++)
            $sub_paths[] = $sub_paths[$i-1].'/'.$sub_cats[$i];

        $maked_cat_id = 0;
        for($i=0;$i<count($sub_paths);$i++)
        {
            if(in_array($sub_paths[$i],$this->_settings['cats_paths']))
            {
                $maked_cat_id = array_search($sub_paths[$i],$this->_settings['cats_paths']);
                continue;
            };

            $maked_cat_id = modApiFunc('Catalog','addCategory',$maked_cat_id,$sub_cats[$i],CATEGORY_STATUS_ONLINE,'','','','','','','',CATEGORY_DONOTSHOW_PRODUCTS_RECURSIVELY,'');
            $this->_settings['cats_paths'][$maked_cat_id] = $sub_paths[$i];

            $this->_messages = str_replace('%01%',$sub_paths[$i],$this->MR->getMessage('DWPDB_CATEGORY_CREATED'));//'Category `'.$sub_paths[$i].'` created';
        }

        return $maked_cat_id;
    }

    var $_settings;
};

?>