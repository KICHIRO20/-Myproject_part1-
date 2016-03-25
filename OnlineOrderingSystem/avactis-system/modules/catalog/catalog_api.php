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

loadModuleFile('catalog/abstract/catalog_search.php');
loadModuleFile('catalog/abstract/product_class.php');
loadModuleFile('catalog/abstract/category_class.php');
loadModuleFile('catalog/abstract/product_list_tag_settings.php');

/**
 * Catalog module.
 * It works with inventory.
 *
 * @package Catalog
 * @author Alexander Girin
 * @access public
 */
class Catalog
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Catalog module constructor.
     */
    function Catalog()
    {
        $this -> initMLAttributes();
    }

    /**
     *                                          .
     */
    function loadState()
    {
        // restore the current selected product
        if(modApiFunc('Session', 'is_Set', 'moveToCategoryID'))
        {
            $this->setMoveToCategoryID(modApiFunc('Session', 'get', 'moveToCategoryID'));
        }
        else
        {
            $this->moveToCategoryID = 1;
        }

        // restore the current selected category
        if(modApiFunc('Session', 'is_Set', 'editableCategoryID'))
        {
            $this->setEditableCategoryID(modApiFunc('Session', 'get', 'editableCategoryID'));
        }
        else
        {
            $this->editableCategoryID = NULL;
        }

        //  restore the current selected edited product
        if(modApiFunc('Session', 'is_Set', 'editableProductsID'))
        {
            $this->setEditableProductsID(modApiFunc('Session', 'get', 'editableProductsID'));
        }
        else
        {
            $this->editableProductsID = array();
        }

        // restore the current selected product
        if(modApiFunc('Session', 'is_Set', 'currentProductID'))
        {
            $this->currentProductID = modApiFunc('Session', 'get', 'currentProductID');
        }
        else
        {
            $this->currentProductID = NULL;
        }

        //   restore the current selected product type
        if(modApiFunc('Session', 'is_Set', 'currentProductTypeID'))
        {
            $this->currentProductTypeID = modApiFunc('Session', 'get', 'currentProductTypeID');
        }

        /**
         * Restore the variable, wich is used to define the product info output
         * when deleting and browsing its attributes.
         */
        if(modApiFunc('Session', 'is_Set', 'DisplayDeleteInfo'))
        {
            $this->DisplayDeleteInfo = modApiFunc('Session', 'get', 'DisplayDeleteInfo');
            modApiFunc('Session', 'un_Set', 'DisplayDeleteInfo');
        }

        $this->registerAttributes();
    }

    /**
     * Saves the module state.
     */
    function saveState()
    {
        modApiFunc('Session', 'set', 'currentProductID', $this->currentProductID);
        modApiFunc('Session', 'set', 'currentProductTypeID', $this->currentProductTypeID);
        modApiFunc('Session', 'set', 'editableCategoryID', $this->editableCategoryID);
        modApiFunc('Session', 'set', 'editableProductsID', $this->editableProductsID);
        modApiFunc('Session', 'set', 'moveToCategoryID', $this->moveToCategoryID);
        modApiFunc('Session', 'set', 'DisplayDeleteInfo', $this->DisplayDeleteInfo);
    }

    /**
     * Returns the meta description of the database tables, specified for storing
     * the Catalog module data.
     *
     * @return array tables meta info
     */
    function getTables ()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $categories = 'categories';
        $tables[$categories] = array();
        $tables[$categories]['columns'] = array
            (
                'id'                => 'categories.category_id'
               ,'left'              => 'categories.category_left'
               ,'right'             => 'categories.category_right'
               ,'level'             => 'categories.category_level'
               ,'status'            => 'categories.category_status'
               ,'sort_order'        => 'categories.category_sort_order'
            );
        $tables[$categories]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'left'              => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'right'             => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'level'             => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'status'            => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 1' //1 - online, 2 - offline
               ,'sort_order'        => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
            );
        $tables[$categories]['primary'] = array
            (
                'id'
            );
        $tables[$categories]['indexes'] = array
            (
                'IDX_lrl' => 'left, right, level'
               ,'IDX_left' => 'left'
               ,'IDX_right' => 'right'
               ,'IDX_level' => 'level'
               ,'IDX_lr' => 'left, right'
            );

        $categories_descr = 'categories_descr';
        $tables[$categories_descr] = array();
        $tables[$categories_descr]['columns'] = array
            (
                'id'                => 'categories_descr.category_id'
               ,'name'              => 'categories_descr.category_name'
               ,'descr'             => 'categories_descr.category_descr'
               ,'image_file'        => 'categories_descr.category_image_file'
               ,'image_small_file'  => 'categories_descr.category_image_small_file'
               ,'image_descr'       => 'categories_descr.category_image_descr'
               ,'page_title'        => 'categories_descr.category_page_title'
               ,'meta_keywords'     => 'categories_descr.category_meta_keywords'
               ,'meta_descr'        => 'categories_descr.category_meta_descr'
               ,'show_prod_recurs'  => 'categories_descr.category_show_prod_recurs'
               ,'seo_url_prefix'    => 'categories_descr.category_seo_url_prefix'
            );
        $tables[$categories_descr]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT ''"
               ,'descr'             => DBQUERY_FIELD_TYPE_LONGTEXT
               ,'image_file'        => DBQUERY_FIELD_TYPE_CHAR255
               ,'image_small_file'  => DBQUERY_FIELD_TYPE_CHAR255
               ,'image_descr'       => DBQUERY_FIELD_TYPE_CHAR255
               ,'page_title'        => DBQUERY_FIELD_TYPE_CHAR255
               ,'meta_keywords'     => DBQUERY_FIELD_TYPE_TEXT
               ,'meta_descr'        => DBQUERY_FIELD_TYPE_TEXT
               ,'show_prod_recurs'  => DBQUERY_FIELD_TYPE_INT . ' NOT NULL DEFAULT '.CATEGORY_DONOTSHOW_PRODUCTS_RECURSIVELY
               ,'seo_url_prefix'    => DBQUERY_FIELD_TYPE_CHAR255
            );
        $tables[$categories_descr]['primary'] = array
            (
                'id'
            );

        $products = 'products';
        $tables[$products] = array();
        $tables[$products]['columns'] = array
            (
                'id'                => 'products.product_id'
               ,'pt_id'             => 'products.product_type_id'
               ,'name'              => 'products.product_name'
               ,'date_added'        => 'products.product_date_added'
               ,'date_updated'      => 'products.product_date_updated'
               ,'date_available'    => 'products.product_date_available'
            );
        $tables[$products]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'pt_id'             => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255
               ,'date_added'        => DBQUERY_FIELD_TYPE_DATE . ' default \'0000-00-00\''
               ,'date_updated'      => DBQUERY_FIELD_TYPE_DATE . ' default \'0000-00-00\''
               ,'date_available'    => DBQUERY_FIELD_TYPE_DATE . ' default \'0000-00-00\''
            );
        $tables[$products]['primary'] = array
            (
                'id'
            );
        $tables[$products]['indexes'] = array
            (
                'IDX_pti' => 'pt_id'
            );

        $p_to_c = 'products_to_categories';
        $tables[$p_to_c] = array();
        $tables[$p_to_c]['columns'] = array(
            'record_id'     =>  $p_to_c.'.record_id'
           ,'product_id'    =>  $p_to_c.'.product_id'
           ,'category_id'   =>  $p_to_c.'.category_id'
           ,'sort_order'    =>  $p_to_c.'.sort_order'
        );
        $tables[$p_to_c]['types'] = array(
            'record_id'     =>  DBQUERY_FIELD_TYPE_INT.' NOT NULL auto_increment'
           ,'product_id'    =>  DBQUERY_FIELD_TYPE_INT.' NOT NULL DEFAULT 0'
           ,'category_id'   =>  DBQUERY_FIELD_TYPE_INT.' NOT NULL DEFAULT 0'
           ,'sort_order'    =>  DBQUERY_FIELD_TYPE_INT.' NOT NULL DEFAULT 0'
        );
        $tables[$p_to_c]['primary'] = array(
            'record_id'
        );
        $tables[$p_to_c]['indexes'] = array(
            'UNIQUE KEY IDX_pc' => 'product_id,category_id'
           ,'IDX_cp' => 'category_id,product_id'
           ,'IDX_sort_order' => 'sort_order'
        );

        $product_types = 'product_types';
        $tables[$product_types] = array();
        $tables[$product_types]['columns'] = array
            (
                'id'                => 'product_types.product_type_id'
               ,'name'              => 'product_types.product_type_name'
               ,'descr'             => 'product_types.product_type_descr'
            );
        $tables[$product_types]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255
               ,'descr'             => DBQUERY_FIELD_TYPE_LONGTEXT
            );
        $tables[$product_types]['primary'] = array
            (
                'id'
            );

        $attributes = 'attributes';
        $tables[$attributes] = array();
        $tables[$attributes]['columns'] = array
            (
                'id'                => 'attributes.attribute_id'
               ,'ag_id'             => 'attributes.attribute_group_id'
               ,'it_id'             => 'attributes.input_type_id'
               ,'ut'                => 'attributes.unit_type'
               ,'view_tag'          => 'attributes.attribute_view_tag'
               ,'name'              => 'attributes.attribute_name'
               ,'descr'             => 'attributes.attribute_descr'
               ,'type'              => 'attributes.attribute_type'
               ,'allow_html'        => 'attributes.attribute_allow_html'
               ,'min'               => 'attributes.attribute_min_value'
               ,'max'               => 'attributes.attribute_max_value'
               ,'size'              => 'attributes.attribute_html_size'
               ,'multilang'         => 'attributes.attribute_multilang'
               ,'sort_order'        => 'attributes.attribute_sort_order'
            );
        $tables[$attributes]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'ag_id'             => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'it_id'             => DBQUERY_FIELD_TYPE_INT
               ,'ut'                => DBQUERY_FIELD_TYPE_CHAR10
               ,'view_tag'          => DBQUERY_FIELD_TYPE_CHAR100
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255
               ,'descr'             => DBQUERY_FIELD_TYPE_LONGTEXT
               ,'type'              => DBQUERY_FIELD_TYPE_CHAR10
               ,'allow_html'        => DBQUERY_FIELD_TYPE_INT
               ,'min'               => DBQUERY_FIELD_TYPE_CHAR255
               ,'max'               => DBQUERY_FIELD_TYPE_CHAR255
               ,'size'              => DBQUERY_FIELD_TYPE_CHAR255
               ,'multilang'         => DBQUERY_FIELD_TYPE_CHAR1
               ,'sort_order'        => DBQUERY_FIELD_TYPE_INT
            );
        $tables[$attributes]['primary'] = array
            (
                'id'
            );
        $tables[$attributes]['indexes'] = array
            (
                'IDX_pagi' => 'ag_id'
               ,'IDX_iti'  => 'it_id'
               ,'IDX_aso'  => 'sort_order'
            );

        $attribute_groups = 'attribute_groups';
        $tables[$attribute_groups] = array();
        $tables[$attribute_groups]['columns'] = array
            (
                'id'                => 'attribute_groups.attribute_group_id'
               ,'name'              => 'attribute_groups.attribute_group_name'
               ,'sort_order'        => 'attribute_groups.attribute_group_sort_order'
            );
        $tables[$attribute_groups]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255
               ,'sort_order'        => DBQUERY_FIELD_TYPE_INT
            );
        $tables[$attribute_groups]['primary'] = array
            (
                'id'
            );
        $tables[$attribute_groups]['indexes'] = array
            (
                'IDX_agso'  => 'sort_order'
            );

        $product_type_attributes = 'product_type_attributes';
        $tables[$product_type_attributes] = array();
        $tables[$product_type_attributes]['columns'] = array
            (
                'id'                => 'product_type_attributes.product_type_attr_id'
               ,'pt_id'             => 'product_type_attributes.product_type_id'
               ,'a_id'              => 'product_type_attributes.attribute_id'
               ,'type_attr_visible' => 'product_type_attributes.product_type_attr_visibility'
               ,'type_attr_required'=> 'product_type_attributes.product_type_attr_required'              //new field
               ,'type_attr_def_val' => 'product_type_attributes.product_type_attr_default_value'
            );
        $tables[$product_type_attributes]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'pt_id'             => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'a_id'              => DBQUERY_FIELD_TYPE_INT
               ,'type_attr_visible' => DBQUERY_FIELD_TYPE_BOOL
               ,'type_attr_required'=> DBQUERY_FIELD_TYPE_BOOL
               ,'type_attr_def_val' => DBQUERY_FIELD_TYPE_LONGTEXT
            );
        $tables[$product_type_attributes]['primary'] = array
            (
                'id'
            );
        $tables[$product_type_attributes]['indexes'] = array
            (
                'IDX_pti' => 'pt_id'
               ,'IDX_ai'  => 'a_id'
               ,'IDX_aid_vis'  => 'a_id, type_attr_visible'
               ,'IDX_ptid_aid_vis'  => 'pt_id, a_id, type_attr_visible'
               ,'UNIQUE KEY IDX_ptid_aid'  => 'pt_id, a_id'
            );

        $product_attributes = 'product_attributes';
        $tables[$product_attributes] = array();
        $tables[$product_attributes]['columns'] = array
            (
                'id'                => 'product_attributes.product_attr_id'
               ,'p_id'              => 'product_attributes.product_id'
               ,'a_id'              => 'product_attributes.attribute_id'
               ,'attr_value'        => 'product_attributes.product_attr_value'
               /*
               ,'type_attr_visible' => 'product_attributes.product_type_attr_visibility'
               */
            );
        $tables[$product_attributes]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'p_id'              => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'a_id'              => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'attr_value'        => DBQUERY_FIELD_TYPE_LONGTEXT
               /*
               ,'type_attr_visible' => DBQUERY_FIELD_TYPE_CHAR1 . ' NOT NULL DEFAULT "1"'
               */
            );
        $tables[$product_attributes]['primary'] = array
            (
                'id'
            );
        $tables[$product_attributes]['indexes'] = array
            (
                'IDX_pi'  => 'p_id'
               ,'IDX_ai'  => 'a_id'
               ,'UNIQUE KEY IDX_pid_aid'  => 'p_id, a_id'
            );

        $product_images = 'product_images';
        $tables[$product_images] = array();
        $tables[$product_images]['columns'] = array
            (
                'id'                => 'product_images.image_id'
               ,'pa_id'             => 'product_images.product_attr_id'
               ,'name'              => 'product_images.image_name'
               ,'width'             => 'product_images.image_width'
               ,'height'            => 'product_images.image_height'
            );
        $tables[$product_images]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'pa_id'             => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT ''"
               ,'width'             => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'height'            => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
            );
        $tables[$product_images]['primary'] = array
            (
                'id'
            );
        $tables[$product_images]['indexes'] = array
            (
                'IDX_pi'  => 'pa_id'
            );

        $input_types = 'input_types';
        $tables[$input_types] = array();
        $tables[$input_types]['columns'] = array
            (
                'id'                => 'input_types.input_type_id'
               ,'ut_id'             => 'input_types.unit_type_id'
               ,'name'              => 'input_types.input_type_name'
            );
        $tables[$input_types]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'ut_id'             => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR50
            );
        $tables[$input_types]['primary'] = array
            (
                'id'
            );
        $tables[$input_types]['indexes'] = array
            (
                'IDX_uti' => 'ut_id'
            );

        $input_type_values = 'input_type_values';
        $tables[$input_type_values] = array();
        $tables[$input_type_values]['columns'] = array
            (
                'id'                => 'input_type_values.input_type_value_id'
               ,'it_id'             => 'input_type_values.input_type_id'
               ,'value'             => 'input_type_values.input_type_value'
            );
        $tables[$input_type_values]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'it_id'             => DBQUERY_FIELD_TYPE_INT
               ,'value'             => DBQUERY_FIELD_TYPE_CHAR50
            );
        $tables[$input_type_values]['primary'] = array
            (
                'id'
            );
        $tables[$input_type_values]['indexes'] = array
            (
                'IDX_iti' => 'it_id'
            );

        $catalog_temp = 'catalog_temp';
        $tables[$catalog_temp] = array();
        $tables[$catalog_temp]['columns'] = array
            (
                'id'                => 'catalog_temp.catalog_temp_id'
               ,'form_id'           => 'catalog_temp.catalog_temp_form_id'
               ,'value'             => 'catalog_temp.catalog_temp_value'
            );
        $tables[$catalog_temp]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'form_id'           => DBQUERY_FIELD_TYPE_INT
               ,'value'             => DBQUERY_FIELD_TYPE_LONGTEXT
            );
        $tables[$catalog_temp]['primary'] = array
            (
                'id'
            );

        $products_search = 'products_search';
        $tables[$products_search] = array();
        $tables[$products_search]['columns'] = array
            (
                'id'                => 'products_search.products_search_id'
               ,'pattern'           => 'products_search.products_search_pattern'
               ,'time'              => 'products_search.products_search_time'
               ,'words'             => 'products_search.products_search_words'
            );
        $tables[$products_search]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'pattern'           => DBQUERY_FIELD_TYPE_TEXT
               ,'time'              => DBQUERY_FIELD_TYPE_DATETIME
               ,'words'             => DBQUERY_FIELD_TYPE_TEXT
            );
        $tables[$products_search]['primary'] = array
            (
                'id'
            );

        $products_search_result = 'products_search_result';
        $tables[$products_search_result] = array();
        $tables[$products_search_result]['columns'] = array
            (
                'id'                => 'products_search_result.products_search_id'
               ,'p_id'              => 'products_search_result.product_id'
               ,'relevance'         => 'products_search_result.relevance'
            );
        $tables[$products_search_result]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL  DEFAULT 0'
               ,'p_id'              => DBQUERY_FIELD_TYPE_INT .' NOT NULL  DEFAULT 0'
               ,'relevance'         => DBQUERY_FIELD_TYPE_INT .' NOT NULL  DEFAULT 0'
            );
        $tables[$products_search_result]['primary'] = array
            (
                'id', 'p_id'
            );
        $tables[$products_search_result]['indexes'] = array
            (
                'IDX_relevance' => 'relevance'
            );


        global $application;
        return $application->addTablePrefix($tables);
    }

    function resetFullCategoryStructure()
    {
        if (isset($this->all_categories)) {
            foreach (array_keys($this->all_categories) as $i) {
                unset($this->all_categories[$i]);
            }
        }
        unset($this->all_categories);
    }

    function fetchFullCategoryStructure()
    {
        if (! isset($this->all_categories)) {
            $this->all_categories = array();
            $this->left_index = array();
            $data = execQuery('SELECT_ALL_CATEGORIES_BASIC_INFO', array());
            if ($data) {
                foreach ($data as & $rec) {
                    $rec['loc'] = array();
                    $rec['parent'] = null;
                    $rec['children'] = array();
                    $this->all_categories[ (int) $rec['id'] ] = $rec;
                    $this->left_index[ (int) $rec['left1'] ] = & $this->all_categories[ (int) $rec['id'] ];
                }
                ksort($this->left_index);
                $prev_level = -1;
                $prev_node = null;

                foreach ($this->left_index as & $node) {
                    $this_level = (int) $node['level'];
                    if ($this_level == $prev_level) { // stay same level
//                        CTrace::dbg('stay: '.$node['name']);
                        if (@ $prev_node['parent']) {
                            $prev_node['parent']['children'][] = & $node;
                        }
                        $node['parent'] = & $prev_node['parent'];
                    }
                    elseif ($this_level > $prev_level) { // descend to next level
//                        CTrace::dbg('descend: '.$node['name']);
                        $node['parent'] = & $prev_node;
                        if ($prev_node) {
                            $prev_node['children'][] = & $node;
                        }
                    }
                    elseif ($this_level < $prev_level) { // rise to prev level
                        $k = $prev_level - $this_level;
                        for ($j = 0; $j <= $k; $j ++) {
                            $prev_node = & $prev_node['parent'];
                        }
//                        CTrace::dbg('rise: '.$prev_node['name'].'/'.$node['name']);
                        $node['parent'] = & $prev_node;
                        if ($prev_node) {
                            $prev_node['children'][] = & $node;
                        }
                    }
                    $prev_node = & $node;
                    $prev_level = $this_level;
                }
                //
                $label_ids = modApiFunc('MultiLang', 'getLabelIDs', 'Catalog', 'categories_descr');
                $l_name = $label_ids['category_name'];
                $l_descr = $label_ids['category_descr'];
                $l_image_descr = $label_ids['category_image_descr'];
                $l_page_title = $label_ids['category_page_title'];
                $l_meta_keywords = $label_ids['category_meta_keywords'];
                $l_meta_descr = $label_ids['category_meta_descr'];
                $l_seo_prefix = $label_ids['category_seo_url_prefix'];
                $data = execQuery('SELECT_ML_ALL_LANGUAGES_RECORD_VALUES', array('label' => $label_ids));
                $index = array();
                foreach ($data as $row) {
                    $index[ $row['label_key'] ][ $row['lng'] ][ $row['label'] ] = $row['value'];
                }
                unset($data);
                $default_language = modApiFunc('MultiLang', 'getDefaultLanguageNumber');
                $languages = modApiFunc('MultiLang', 'getLanguageList', false);
                foreach ($this->all_categories as $cid => & $c) {
                    if (isset($index[$cid])) {
                        foreach ($languages as $lng) {
                            $lng_number = $lng['number'];
                            if ($lng_number == $default_language) {
                                continue;
                            }
                            if (isset($index[$cid][$lng_number])) {
                                $c['loc'][$lng_number] = array(
                                        'id' => $c['id'],
                                        'left1' => $c['left1'],
                                        'right1' => $c['right1'],
                                        'level' => $c['level'],
                                        'status' => $c['status'],
                                        'largeimage_file' => $c['largeimage_file'],
                                        'smallimage_file' => $c['smallimage_file'],
                                        'show_prod_recurs' => $c['show_prod_recurs'],
                                        'name' => isset($index[$cid][$lng_number][$l_name]) ? $index[$cid][$lng_number][$l_name] : $c['name'],
                                        'descr' => isset($index[$cid][$lng_number][$l_descr]) ? $index[$cid][$lng_number][$l_descr] : $c['descr'],
                                        'image_descr' => isset($index[$cid][$lng_number][$l_image_descr]) ? $index[$cid][$lng_number][$l_image_descr] : $c['image_descr'],
                                        'page_title' => isset($index[$cid][$lng_number][$l_page_title]) ? $index[$cid][$lng_number][$l_page_title] : $c['page_title'],
                                        'meta_keywords' => isset($index[$cid][$lng_number][$l_meta_keywords]) ? $index[$cid][$lng_number][$l_meta_keywords] : $c['meta_keywords'],
                                        'meta_descr' => isset($index[$cid][$lng_number][$l_meta_descr]) ? $index[$cid][$lng_number][$l_meta_descr] : $c['meta_descr'],
                                        'seo_url_prefix' => isset($index[$cid][$lng_number][$l_seo_prefix]) ? $index[$cid][$lng_number][$l_seo_prefix] : $c['seo_url_prefix'],
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    function fetchBaseCategoryInfo($cid)
    {
        $this->fetchFullCategoryStructure();

        $cid = intval($cid);

        if (! isset($this->all_categories[$cid])) {
            return false;
        }

        $default_language = modApiFunc('MultiLang', 'getDefaultLanguageNumber');
        $current_language = modApiFunc('MultiLang', 'getLanguageNumber');

        if ($default_language != $current_language && isset($this->all_categories[$cid]['loc'][$current_language])) {
            return $this->all_categories[$cid]['loc'][$current_language];
        }

        return $this->all_categories[$cid];
    }

    function _getOnlineSubcategories(& $c, & $result)
    {
        foreach ($c['children'] as & $sc) {
            if ($sc['status'] == CATEGORY_STATUS_ONLINE) {
                $result[] = $sc['id'];
                $this->_getOnlineSubcategories($sc, $result);
            }
        }
    }

    function fetchOnlineSubcategories($cid)
    {
        $this->fetchFullCategoryStructure();

        $cid = intval($cid);
        if (isset($this->all_categories[$cid])) {
            $result = array((string)$cid);
            $this->_getOnlineSubcategories($this->all_categories[$cid], $result);
            return $result;
        }
        return array();
    }

    function hasOnlineSubcategories($cid)
    {
        $this->fetchFullCategoryStructure();

        $cid = intval($cid);
        if (isset($this->all_categories[$cid]) && $this->all_categories[$cid]['status'] == CATEGORY_STATUS_ONLINE) {
            foreach ($this->all_categories[$cid]['children'] as & $sc) {
                if ($sc['status'] == CATEGORY_STATUS_ONLINE) {
                    return true;
                }
            }
        }
        return false;
    }

    function getCategoryRecursiveStatus($cid)
    {
        $this->fetchFullCategoryStructure();

        $c = & $this->all_categories[intval($cid)];
        $status = CATEGORY_STATUS_ONLINE;
        while ($c && $status == CATEGORY_STATUS_ONLINE) {
            if ($c['status'] == CATEGORY_STATUS_OFFLINE) {
                $status = CATEGORY_STATUS_OFFLINE;
            }
            $c = & $c['parent'];
        }
        return $status;
    }

    function getParentCategoryId($cid)
    {
        $this->fetchFullCategoryStructure();

        $c = & $this->all_categories[intval($cid)];
        return $c['parent'] ? $c['parent']['id'] : 1;
    }

	function getMultiCatByPid($pid)
	{
		$cats = array();
		$mcats = execQuery('SELECT_MULTI_CATS_BY_PID',array('pid' => $pid));
		foreach($mcats as $key=>$val)
		{
			array_push($cats,$val['mcats']);
		}
		return $cats;
	}

    /**
     * Returns the category technical specifications: left, right, level.
     * Caching is not used.
     */
    function fetchCategoryInfo($cid)
    {
        $this->fetchFullCategoryStructure();

        $cid = intval($cid);
        if (isset($this->all_categories[$cid])) {
            return array(
                'left' => $this->all_categories[$cid]['left1'],
                'right' => $this->all_categories[$cid]['right1'],
                'status' => $this->all_categories[$cid]['status'],
                'level' => $this->all_categories[$cid]['level'],
            );
        }
        return false;
    }

    /**
     * Installs the  module.
     *
     * The install() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Catalog::getTables() instead of $this->getTables()
     */
    function install()
    {
        _use(dirname(__FILE__).'/includes/xml_install.inc');
    }

    /**
     * Uninstalls the module.
     * It deletes all module tables.
     *
     * The uninstall() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Catalog::getTables() instead of $this->getTables()
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(Catalog::getTables());
        global $application;
        $application->db->getDB_Result($query);
    }

    /**
     * Sets the edited category ID.
     * If the category ID is not correct, the current id remains unchanged.
     *
     * @param integer $cid category id
     */
    function setEditableCategoryID($ecid)
    {
        if ($this->isCorrectCategoryId($ecid))
        {
            $this->editableCategoryID = $ecid;
        }
    }

    /**
     * Gets the edited category ID.
     *
     * @return integer Editable category ID.
     */
    function getEditableCategoryID()
    {
        return $this->editableCategoryID;
    }

    /**
     * Sets "move to" category ID.
     * If the category ID is not correct, the current id remains unchanged.
     *
     * @param integer $cid category id
     */
    function setMoveToCategoryID($mcid)
    {
        if ($this->isCorrectCategoryId($mcid))
        {
            $this->moveToCategoryID = $mcid;
        }
    }

    /**
     * Gets "move to" category ID.
     *
     * @return integer "move to" category ID
     */
    function getMoveToCategoryID()
    {
        return $this->moveToCategoryID;
    }

    /**
     * Unsets the edited catalog category ID.
     */
    function unsetEditableCategoryID()
    {
        $this->editableCategoryID=NULL;
    }

    /**
     * Unsets "move to" catalog category ID.
     */
    function unsetMoveToCategoryID()
    {
        $this->moveToCategoryID = 1;
    }

    /**
     * Gets the current product ID.
     *
     * @return integer Current product ID.
     */
    function getCurrentProductID()
    {
        return $this->currentProductID;
    }

    /**
     * Sets the current product ID.
     * If the product ID is not correct, the current id remains unchanged.
     *
     * @param integer $pid product id
     */
    function setCurrentProductID($pid)
    {
        global $application;
        if ($this->isCorrectProductId($pid))
        {
            //                 storefront -          ,                  Offline:
            if (modApiFunc('Users', 'getZone') == "CustomerZone")
            {
                $prod = new CProductInfo($pid);

                if($prod->haveOnlineCategory())
                {
                    $this->currentProductID = $pid;
                }
                else
                {
                    $query = new Request($application->getAppIni('SITE_URL'));
                    $application->redirect($query);
                }
            }
            else
            {
                $this->currentProductID = $pid;
            }
        }
        else
        {
        }
    }

    /**
     * Gets the current product type ID.
     *
     * @return integer Current product type ID.
     */
    function getCurrentProductTypeID()
    {
        return $this->currentProductTypeID;
    }

    /**
     * Sets the current product type ID.
     * If the product type ID is not correct, the current id remains unchanged.
     *
     * @param integer $ptid Product type id.
     */
    function setCurrentProductTypeID($ptid)
    {
        if ($this->isCorrectProductTypeId($ptid))
        {
            $this->currentProductTypeID = $ptid;
        }
    }

    /**
     * Gets the edited product array.
     *
     * @ should it be saved in the module state?
     * @return array the edited product array.
     */
    function getEditableProductsID()
    {
        return $this->editableProductsID;
    }

    /**
     * Sets the edited product array.
     * Each id is checked on validity. If the invalid id is found, it should be
     * deleted from the array.
     */
    function setEditableProductsID($prodId_array)
    {
        $this->editableProductsID = array();
        foreach ($prodId_array as $prodId)
        {
            if ($this->isCorrectProductId($prodId))
            {
                $this->editableProductsID[] = $prodId;
            }
        }
    }

    /**
     * Unsets hte edited product ID.
     */
    function unsetEditableProductsID()
    {
        $this->editableProductsID=array();
    }

    /**
     * Registers all custom attributes, specified in the system for any
     * product type.
     */
    function registerAttributes()
    {
        global $application;
        $result = execQuery('SELECT_CUSTOM_PRODUCT_TAGS', array());
        for ($i=0; $i<sizeof($result); $i++)
        {
            $application->registerAttributes(array('Product'.$result[$i]['view_tag'].'Custom'));
        }
    }

    /**
     * Gets the category full path.
     * The structure of the returned array:
     * <code>
     * array (
     *     '0' => array (
     *         'id'   => the category id.
     *         'name' => the category name.
     *         ),
     *     '1' => ...
     * )
     * </code>
     *
     * @param integer $cid the category id.
     * @return array Array of categories representing current category tree path.
     */
    function getCategoryFullPath($cid)
    {
        $this->fetchFullCategoryStructure();

        $default_language = modApiFunc('MultiLang', 'getDefaultLanguageNumber');
        $current_language = modApiFunc('MultiLang', 'getLanguageNumber');

        $c = & $this->all_categories[ intval($cid) ];
        $retval = array();
        while ($c) {
            $retval[] = array(
                'id' => $c['id'],
                'name' => $current_language != $default_language && isset($c['loc'][$current_language])
                        ? $c['loc'][$current_language]['name'] : $c['name'],
            );
            $c = & $c['parent'];
        }
        return array_reverse($retval);
    }

    /**
     * Gets the current category full path.
     *
     * @return array Array of categories representing current category tree path
     * or NULL if current category is not set.
     * @see Catalog::getCategoryFullPath()
     */
    function getCurrentCategoryFullPath()
    {
        return $this->getCategoryFullPath(modApiFunc('CProductListFilter','getCurrentCategoryId'));
    }


    /**
     * Gets the category list in current category.
     *
     * @return array Array of CCategoryInfo objects or NULL if the current category
     *         is unavailable.
     * @see Catalog::getDirectSubcategoriesListFull()
     */
    function getDirectSubcategoriesListInCurrentCategory()
    {
        return $this->getDirectSubcategoriesListFull(modApiFunc('CProductListFilter','getCurrentCategoryId'));
    }


    /**
     * Gets the category list in current category.
     * Each element in the array is the CCategoryInfo object.
     *
     * @return array Array of CCategoryInfo objects or NULL if the current category
     *         is unavailable.
     */
    function getDirectSubcategoriesListFull($cid, $b_online_only = false)
    {
        global $application;
        $this->fetchFullCategoryStructure();

        $c = & $this->all_categories[ intval($cid) ];
        $retval = array();
        foreach ($c['children'] as $sc)
        {
            if (! $b_online_only || ($b_online_only && $sc['status'] == CATEGORY_STATUS_ONLINE)) {
                $sci = & $application->getInstance('CCategoryInfo', $sc['id']);
                array_push($retval, $sci);
            }
        }
        return $retval;
    }

    /**
     * Gets a list of all children subcategories in the selected category.
     *
     * @author Alexander Girin
     * @ check if this function is needed
     * @param integer $cid the selected category id.
     * @return
     */
    function getSubcategoriesFullListWithParent($cid, $use_peginator=true, $with_descr=true)
    {
        $this->fetchFullCategoryStructure();
        $default_language = modApiFunc('MultiLang', 'getDefaultLanguageNumber');
        $current_language = modApiFunc('MultiLang', 'getLanguageNumber');

        $result = array();
        $this->_addCategory($this->all_categories[ intval($cid) ], $result, $current_language, $default_language);
        return $result;
    }

    function _addCategory(& $c, & $result, $current_language, $default_language)
    {
        if ($c) {
            $result[] = array(
                'id' => $c['id'],
                'c_left' => $c['left1'],
                'c_right' => $c['right1'],
                'level' => $c['level'],
                'name' => $current_language != $default_language && isset($c['loc'][$current_language])
                        ? $c['loc'][$current_language]['name'] : $c['name'],
            );
            foreach ($c['children'] as & $sc) {
                $this->_addCategory($sc, $result, $current_language, $default_language);
            }
        }
    }

    function getSubcategoryIdsWithParent($cid)
    {
        $this->fetchFullCategoryStructure();
        $result = array();
        if (isset($this->all_categories[ intval($cid) ])) {
            $result[] = (string) $cid;
            $this->_addCategoryId($this->all_categories[ intval($cid) ], $result);
        }
        return $result;
    }

    function _addCategoryId(& $c, & $result)
    {
        foreach ($c['children'] as & $sc) {
            if (isset($this->all_categories[ intval($sc['id']) ])) {
                $result[] = (string) $sc['id'];
                if ($sc['children']) {
                    $this->_addCategoryId($sc, $result);
                }
            }
        }
    }

    /**
     * Saves the sort of subcategories to the categories in the DB.
     *
     * @author Alexander Girin
     * @param array $catSortOrderArray the array of categories id, whose order
     *               defines sort_order of the categories.
     * @return
     */
    function setCategorySortOrder($catSortOrderArray)
    {
        global $application;
        /**
         * <pre>
         * BEFORE
         *
         * |
         * +-\ root (L_root, R_root)
         * | |                         L_1 = L_root + 1
         * | |-- subnode1 (L_1, R_1)
         * | |-- subnode2 (L_2, R_2)
         * | |-- subnode3 (L_3, R_3)
         * |                           R_3 = R_root - 1
         * |
         *
         * AFTER (new order: subnode2, subnode3, subnode1
         *
         * |
         * +-\ root (L_root, R_root)
         * | |              * = L_root + 1
         * | |-- subnode2 ( * , * + (R_2 - L_2))
         * | |-- subnode3 ( * , * + (R_3 - L_3))
         * | |-- subnode1 ( * , * + (R_1 - L_1))
         * |                   (* + (R_1 - L_1)) = R_root - 1
         * |
         * </pre>
         */

        /**
         * To make changes just update the indexes 'left' and 'right' only
         * into the subtree, whose root is the parent category for all sorted ones.
         * It can be used for optimization. The problem: how to change them all
         * together at once? If update the categories separately, then the errors
         * occur in the tree structure.
         */

        /**
         * Here is one of the answers to the previous question
         */

        // getting the exclusive rights
        $application -> enterCriticalSection('setCategorySortOrder');

        // firstly we check if all the subcategories belong to the same category
        $parent_ids = execQuery('SELECT_PARENT_CATEGORY_IDS_FOR_CATEGORIES', array('cat_ids' => $catSortOrderArray));

        // if there are several parent categories (or none of them) then do nothing, category tree was changed
        if (count($parent_ids) != 1)
        {
            $application -> leaveCriticalSection();
            return;
        }

        $parent_cat_id = $parent_ids[0]['parent_id'];

        // secondly we check if these subcategories are ALL subcategories of the parent category
        $other_cats = execQuery('SELECT_OTHER_SUBCATEGORIES_FOR_A_CATEGORY', array('id' => $parent_cat_id, 'cat_ids' => $catSortOrderArray));

        // if there are other subcategories then do nothing, category tree was changed
        if (!empty($other_cats))
        {
            $application -> leaveCriticalSection();
            return;
        }

        // getting base info for parent category
        $ParentCatInfo = $this -> fetchCategoryInfo($parent_cat_id);

        // locking tables
        $query = new DB_MYSQL_Lock_Tables();
        $query -> addTableToLock('categories', DB_LOCK_MODE_WRITE);
        $query -> addTableToLock('categories_descr', DB_LOCK_MODE_WRITE);
        $query -> addTableToLock('products', DB_LOCK_MODE_WRITE);
        $query -> addTableToLock('store_settings', DB_LOCK_MODE_WRITE);
        $query -> addTableToLock('products_to_categories', DB_LOCK_MODE_WRITE);
        $query -> addTableToLock('events_manager', DB_LOCK_MODE_WRITE);
        $application -> db -> PrepareSQL($query);
        $application -> db -> DB_Exec($query);

        // clearing "old" sort data
        execQuery('UPDATE_CLEAR_SORT_CATEGORY_FIELD', array());

        // setting up the new offset inside the parent category. Based on it the current offset for each subcategory will be calculated
        $offset = $ParentCatInfo['left'] + 1;

        foreach($catSortOrderArray as $cat_id)
        {
            // getting base info for the subcategory
            $CatInfo = $this -> fetchCategoryInfo($cat_id);
            // calculating the offset for the category
            execQuery('UPDATE_SET_SORT_CATEGORY_FIELD', array('left' => $CatInfo['left'],
                                                              'right' => $CatInfo['right'],
                                                              'sort_order' => $offset - $CatInfo['left']));
            // recalculating the offset
            $offset += $CatInfo['right'] - $CatInfo['left'] + 1;
        }

        // all is done -> resorting... but a paranoidal check before
        if ($offset == $ParentCatInfo['right'])
        {
            execQuery('UPDATE_SORT_CATEGORIES', array());
            modApiFunc('EventsManager', 'throwEvent', 'CategorySortOrderChanged', $parent_cat_id);
        }
        else
        {
            execQuery('UPDATE_CLEAR_SORT_CATEGORY_FIELD', array());
        }

        // unlocking tables
        $query = new DB_MYSQL_Unlock_Tables();
        $application -> db -> PrepareSQL($query);
        $application -> db -> DB_Exec($query);

        $application -> leaveCriticalSection();
    }

    /**
     * Saves the sort of products to the categories in the DB.
     *
     * @author Alexander Girin
     * @param array $catSortOrderArray the array of products id, whose order
     * defines sort_order of the products.
     * @return
     */
    function setProductsSortOrder($prodSortOrderArray,$category_id)
    {
        foreach ($prodSortOrderArray as $prodSort => $prodId)
        {
            execQuery('UPDATE_PRODUCT_SORT_ORDER', array('p_sort' => $prodSort, 'p_id' => $prodId, 'c_id' => $category_id));
        }

        modApiFunc('EventsManager','throwEvent','ProductsSortOrderChanged',$category_id);
    }

    /**
     * Generates product sort order in the category with the ID = $cid to append
     * to the end.
     *
     * @author Alexander Girin
     * @param integer $cid - the category ID, where the product is added
     * @return integer max sort order + 1
     */
    function generateProductSortOrder($cid)
    {
        $result = execQuery('SELECT_MAX_PRODUCT_SORT_ORDER_IN_CATEGORY', array('category_id'=>$cid));
        return ($result[0]['max']+1);
    }

    /**
     *                                             id                                   .
     *
     *               storefront.         ,             id
     *           id,                                  ,        : offline, out of stock    . .
     *
     *       ,                                         id-         $id_list,
     *                  .
     *
     */
    function filterProductIdListByGlobalFilter($id_list)
    {
        // default
        loadClass('CProductListFilter');
        $f = new CProductListFilter();
        $params_obj = $f->getProductListParamsObject();

        //                       :
        $params_obj->product_id_list_to_select = $id_list;

        //
        $params_obj->category_id = 1;
        $params_obj->select_mode_recursiveness = IN_CATEGORY_RECURSIVELY;
        $params_obj->select_mode_uniqueness = UNIQUE_PRODUCTS;

        $result = execQuery('SELECT_PRODUCT_LIST', $params_obj->getParams());
        $plain_res = array();
        foreach($result as $item)
        {
            $plain_res[] = $item['product_id'];
        }
        return array_intersect($id_list, $plain_res);
    }

    function getProductListByGlobalFilter($use_paginator = PAGINATOR_ENABLE, $return_as = RETURN_AS_ID_LIST)
    {
        //
        $obj_params = modApiFunc('CProductListFilter','getProductListParamsObject');

        if ($use_paginator == PAGINATOR_ENABLE)
        {
            $obj_params->use_paginator = true;
        }
        else /* PAGINATOR_DISABLE */
        {
            $obj_params->use_paginator = false;
        }
        return $this->getProductListByFilter($obj_params, $return_as);
    }

    function getProductListByFilter($obj_params, $return_as = RETURN_AS_ID_LIST)
    {
        if (_is_a($obj_params, 'PRODUCT_LIST_PARAMS') == false)
        {
            die("ERROR: ".__CLASS__.'::'.__FUNCTION__.' First param $obj_params must be an object of PRODUCT_LIST_PARAMS class.');
        }

        $result = execQuery('SELECT_PRODUCT_LIST', $obj_params->getParams());

        if($return_as == RETURN_AS_ID_LIST)
        {
            return $result;
        };

        // return result as CProductInfo objects array
        $products_listing = array();
        for ($i=0; $i<sizeof($result); $i++)
        {
            $offset=0;
            if ($obj_params->use_paginator)
            {
                $offset = modApiFunc('paginator', 'getCurrentPaginatorOffset');
            }
            if (!is_numeric($offset))
            {
                $offset=0;
            }
            $product_info = new CProductInfo($result[$i]['product_id']);
            $product_info->setAdditionalProductTag('N', ($i+1+$offset) );
            array_push($products_listing, $product_info);
        }
        return $products_listing;
    }

    function _getPtypesAttrsByPIDs($pids)
    {
        $res = execQuery('SELECT_PRODUCT_TYPES_BY_PRODUCT_IDS', array('pid' => $pids));

        $attrs = array();
        for($i=0;$i<count($res);$i++)
        {
            $_ptattrs = $this->getProductTypeAttributes($res[$i]['pt_id']);
            foreach($_ptattrs as $_key => $_ptainf)
                if($_ptainf['visible'] != 1)
                    unset($_ptattrs[$_key]);
            $attrs = array_merge($attrs,$_ptattrs);
        };

        return $attrs;
    }

    function _getPTypeIDByProductID($pid)
    {
        $res = execQuery('SELECT_PRODUCT_TYPES_BY_PRODUCT_IDS', array('pid' => $pid));
        return $res[0]['pt_id'];
    }

    /**
     * Deletes the product array by the array of products id.
     *
     * @author Alexander Girin
     * @param array $ProdsId the array of products id
     * @return
     */
    function deleteProductsArray($ProdsId)
    {
        global $application;
        $result = execQuery('SELECT_PRODUCT_IMAGES_BY_PRODUCT_IDS', array('pids' => $ProdsId));

        $imagesId = array();
        foreach ($result as $imageInfo)
        {
            //@unlink($application->getAppIni('PATH_IMAGES_DIR').$imageInfo["name"]);
            $imagesId[] = $imageInfo["id"];
        }

        execQuery('DELETE_PRODUCT_IMAGES_BY_IMAGE_IDS', array('i_ids' => $imagesId));

        execQuery('DELETE_PRODUCT_ATTRIBUTES_BY_PRODUCT_IDS', array('pids' => $ProdsId));

        // Select a category list, to which these products are referred.
        $categories_list = execQuery('SELECT_CATEGORY_LIST_BY_PRODUCT_IDS', array('pids' => $ProdsId));

        execQuery('DELETE_PRODUCTS_BY_PRODUCT_IDS', array('pids' => $ProdsId));

        execQuery('DELETE_PRODUCTS_TO_CATEGORIES_BY_PRODUCT_IDS', array('pids' => $ProdsId));

        modApiFunc('EventsManager','throwEvent','ProductsDeleted',$ProdsId, $categories_list);
    }

     /**
     * Deletes the category array by the array of categories id.
     *
     * @author Alexander Girin
     * @param array $CatsId the array of categories id
     * @param integer $ParentCatId parent category id
     * @return
     */
    function deleteCategoriesArray($CatsId)
    {
            $result = execQuery('SELECT_CATEGORY_IMAGES_BY_CATEGORY_IDS', array('cids' => $CatsId));

            foreach ($result as $images)
            {
                @unlink($application->getAppIni('PATH_IMAGES_DIR').$images['image_large']);
                @unlink($application->getAppIni('PATH_IMAGES_DIR').$images['image_small']);
            }

            if (is_array($CatsId))
                foreach($CatsId as $cid)
                {
                    $cInfo = $this->fetchCategoryInfo($cid);
                    if (@$cInfo['left'])
                    {
                         execQuery('DELETE_CATEGORY_DESCR_BY_CATEGORY_IDS', array('cids' => array($cid)));
                         execQuery('DELETE_CATEGORIES_BY_PARENT_CATEGORY_RANGE', array('left' => $cInfo['left'], 'right' => $cInfo['right']));
                         execQuery('UPDATE_CATEGORY_RANGES_BY_PARENT_CATEGORY_RANGE_AND_DELTA', array('left' => $cInfo['left'], 'right' => $cInfo['right'], 'delta' => ($cInfo['right'] - $cInfo['left']) + 1));
                    }
                }

            //del links
            $this->delAllProductLinks('category_id',$CatsId);

            modApiFunc('EventsManager','throwEvent','CategoriesDeleted',$CatsId, NULL);
    }

    /**
     * Moves the category.
     *
     * @author Alexander Girin
     * @param integer $newParentCatId the new category id,
     *        to which the category is moved
     * @param integer $cid the moved category id
     * @return
     */
    function moveCategory($newParentCatId, $cid)
    {
        global $application;

        $NewParentCatInfo = $this->fetchCategoryInfo($newParentCatId);
        $CatInfo = $this->fetchCategoryInfo($cid);

        $catobj = new CCategoryInfo($cid);
        $CurrentParentCatInfo = $catobj->getCategoryTagValue('parentid');

        if ($NewParentCatInfo == FALSE || $CatInfo == FALSE )
        {
            return;
        }

        $tables = $this->getTables();
        $c = $tables['categories']['columns']; # categories is a database table

        $deltaLevel = -($CatInfo['level']-1)+$NewParentCatInfo['level'];

        if ($NewParentCatInfo['left']  < $CatInfo['left'] &&
            $NewParentCatInfo['right'] > $CatInfo['right'] &&
            $NewParentCatInfo['level'] < $CatInfo['level'] - 1)
        {
            $query = new DB_Update('categories');
            $query->addUpdateExpression($c['level'],
                                        $query->fIf($c['left'].' '.DB_BETWEEN.' '.$CatInfo['left']
                                                              .' '.DB_AND    .' '.$CatInfo['right'],
                                                    $c['level'] . sprintf('%+d', $deltaLevel),
                                                    $c['level']
                                                   )
                                       );
            $query->addUpdateExpression($c['right'],
                                        $query->fIf($c['right'].' '.DB_BETWEEN.' '.($CatInfo['right']+1)
                                                               .' '.DB_AND    .' '.($NewParentCatInfo['right']-1),
                                                    $c['right'].'-'.($CatInfo['right']-$CatInfo['left']+1),
                                                    $query->fIf($c['left'].' '.DB_BETWEEN.' '.$CatInfo['left']
                                                                          .' '.DB_AND    .' '.$CatInfo['right'],
                                                                $c['right'].'+'.((($NewParentCatInfo['right']-
                                                                                   $CatInfo['right']-
                                                                                   $CatInfo['level']+
                                                                                   $NewParentCatInfo['level'])/2)*2+
                                                                                   $CatInfo['level']-
                                                                                   $NewParentCatInfo['level']-1),
                                                                $c['right']
                                                               )
                                                   )
                                       );
            $query->addUpdateExpression($c['left'],
                                        $query->fIf($c['left'].' '.DB_BETWEEN.' '.($CatInfo['right']+1)
                                                               .' '.DB_AND   .' '.($NewParentCatInfo['right']-1),
                                                    $c['left'].'-'.($CatInfo['right']-$CatInfo['left']+1),
                                                    $query->fIf($c['left'].' '.DB_BETWEEN.' '.$CatInfo['left']
                                                                          .' '.DB_AND    .' '.$CatInfo['right'],
                                                                $c['left'].'+'.((($NewParentCatInfo['right']-
                                                                                  $CatInfo['right']-
                                                                                  $CatInfo['level']+
                                                                                  $NewParentCatInfo['level'])/2)*2+
                                                                                  $CatInfo['level']-
                                                                                  $NewParentCatInfo['level']-1),
                                                                $c['left']
                                                               )
                                                   )
                                       );
            $query->WhereField($c['left'], DB_BETWEEN,"'". ($NewParentCatInfo['left']+1)."'"
                                          .' '.DB_AND." '".($NewParentCatInfo['right']-1)."'"
                              );
            $application->db->getDB_Result($query);
        }
        elseif ($NewParentCatInfo['left'] < $CatInfo['left'])
        {
            $query = new DB_Update('categories');
            $query->addUpdateExpression($c['level'],
                                        $query->fIf($c['left'].' '.DB_BETWEEN.' '.$CatInfo['left']
                                                              .' '.DB_AND    .' '.$CatInfo['right'],
                                                    $c['level'] . sprintf('%+d', $deltaLevel),
                                                    $c['level']
                                                   )
                                       );
            $query->addUpdateExpression($c['left'],
                                        $query->fIf($c['left'].' '.DB_BETWEEN.' '.$NewParentCatInfo['right']
                                                              .' '.DB_AND    .' '.($CatInfo['left']-1),
                                                    $c['left'].'+'.($CatInfo['right']-$CatInfo['left']+1),
                                                    $query->fIf($c['left'].' '.DB_BETWEEN.' '.$CatInfo['left']
                                                                          .' '.DB_AND    .' '.$CatInfo['right'],
                                                                $c['left'].'-'.($CatInfo['left']-
                                                                                $NewParentCatInfo['right']),
                                                                $c['left']
                                                               )
                                                   )
                                       );
            $query->addUpdateExpression($c['right'],
                                        $query->fIf($c['right'].' '.DB_BETWEEN.' '.$NewParentCatInfo['right']
                                                               .' '.DB_AND    .' '.$CatInfo['left'],
                                                    $c['right'].'+'.($CatInfo['right']-$CatInfo['left']+1),
                                                    $query->fIf($c['right'].' '.DB_BETWEEN.' '.$CatInfo['left']
                                                                           .' '.DB_AND    .' '.$CatInfo['right'],
                                                                $c['right'].'-'.($CatInfo['left']-
                                                                                $NewParentCatInfo['right']),
                                                                $c['right']
                                                               )
                                                   )
                                       );
            $query->WhereField($c['left'], DB_BETWEEN,"'". $NewParentCatInfo['left']."'"
                                          .' '.DB_AND." '".$CatInfo['right']."'"
                              );
            $query->WhereOR();
            $query->WhereField($c['right'], DB_BETWEEN,"'". $NewParentCatInfo['left']."'"
                                           .' '.DB_AND." '".$CatInfo['right']."'"
                              );
            $application->db->getDB_Result($query);
        }
        else
        {
            $query = new DB_Update('categories');
            $query->addUpdateExpression($c['level'],
                                        $query->fIf($c['left'].' '.DB_BETWEEN.' '.$CatInfo['left']
                                                              .' '.DB_AND    .' '.$CatInfo['right'],
                                                    $c['level'] . sprintf('%+d', $deltaLevel),
                                                    $c['level']
                                                   )
                                       );
            $query->addUpdateExpression($c['left'],
                                        $query->fIf($c['left'].' '.DB_BETWEEN.' '.$CatInfo['right']
                                                              .' '.DB_AND    .' '.$NewParentCatInfo['right'],
                                                    $c['left'].'-'.($CatInfo['right']-$CatInfo['left']+1),
                                                    $query->fIf($c['left'].' '.DB_BETWEEN.' '.$CatInfo['left']
                                                                          .' '.DB_AND    .' '.$CatInfo['right'],
                                                                $c['left'].'+'.($NewParentCatInfo['right']-
                                                                                $CatInfo['right']-1),
                                                                $c['left']
                                                               )
                                                   )
                                       );
            $query->addUpdateExpression($c['right'],
                                        $query->fIf($c['right'].' '.DB_BETWEEN.' '.($CatInfo['right']+1)
                                                               .' '.DB_AND    .' '.($NewParentCatInfo['right']-1),
                                                    $c['right'].'-'.($CatInfo['right']-$CatInfo['left']+1),
                                                    $query->fIf($c['right'].' '.DB_BETWEEN.' '.$CatInfo['left']
                                                                           .' '.DB_AND    .' '.$CatInfo['right'],
                                                                $c['right'].'+'.($NewParentCatInfo['right']-
                                                                                 $CatInfo['right']-1),
                                                                $c['right']
                                                               )
                                                   )
                                       );
            $query->WhereField($c['left'], DB_BETWEEN,"'". $CatInfo['left']."'"
                                          .' '.DB_AND." '".$NewParentCatInfo['right']."'"
                              );
            $query->WhereOR();
            $query->WhereField($c['right'], DB_BETWEEN,"'". $CatInfo['left']."'"
                                           .' '.DB_AND." '".$NewParentCatInfo['right']."'"
                              );
            $application->db->getDB_Result($query);
        }

        modApiFunc('EventsManager','throwEvent','CategoryMoved', array( 'OLD_PARENT_CATEGORY_ID'=>$CurrentParentCatInfo,
                                                                        'NEW_PARENT_CATEGORY_ID'=>$newParentCatId,
                                                                        'CATEGORY_ID_LIST'=>$cid));
    }

    /**
     * Moves products.
     *
     * @author Alexander Girin
     * @param integer $newParentCatId the new category id,
     *        to which the category is moved
     * @param integer $productsID the moved category id
     * @param integer $catsprods the array of copied products and respective categories to which the products need to be copied
     * @return
     */
   function moveProducts($oldParentCatId, $catsprods,$productsID)
    {
        if(!is_array($productsID) or empty($productsID))
        {
            return;
        }

        $this->delProductLinksFromCategory($oldParentCatId,$productsID);
        foreach($catsprods as $pid=>$newParentCatId)
        {
            $prodObj = new CProductInfo($pid);
            if(!in_array($newParentCatId,$prodObj->getCategoriesIDs()))
                $this->addProductLinkToCategory($pid, $newParentCatId);
        }
        modApiFunc('EventsManager','throwEvent','ProductsMoved', array( 'OLD_PARENT_CATEGORY_ID'=>$oldParentCatId,
                                                                        'NEW_PARENT_CATEGORY_ID'=>$newParentCatId,
                                                                        'PRODUCT_ID_LIST'=>$productsID));
    }

	//For displaying categorywise products - 31 aug
	function displayProductsCategorywise($product_idsarray)
	{
		$result = execQuery('SELECT_CATEGORY_LIST_BY_PRODUCT_IDS',array('pids'=>$product_idsarray));
		return $result;
    }

    /**
     * Copies products.
     *
     * @author Alexander Girin
     * @param integer $newParentCatId the new category id, to which products
     *         are copied
     * @param array $catsprods the array of copied products and respective categories to which the products need to be copied
     * @return
     */
    function copyProducts($catsprods)
    {
        // copy results
        $_copy_result=array();

        $parentCategoriesList = array();
        $current_language = modApiFunc('MultiLang', 'getLanguage');
        modApiFunc('MultiLang', 'setLanguage', modApiFunc('MultiLang', 'getDefaultLanguage'));

        foreach($catsprods as $pId=>$newParentCatId)
        {

            $prd = new CProductInfo($pId);
            $new_pId=$prd->clone_to_category($newParentCatId);

            $parentCategoriesList = array_merge($parentCategoriesList,$prd->getCategoriesIDs());//$prd->getProductTagValue('categoryid');
            $prd = null;

            // adding copy result
            $_copy_result[$pId]=$new_pId;
        }

        modApiFunc('MultiLang', 'setLanguage', $current_language);

        // saving copy results to session
        modApiFunc("Session","set","CopyProductsResult",$_copy_result);
        modApiFunc('EventsManager','throwEvent','ProductsCopied', array('PRODUCT_ID_LIST'=>$_copy_result,
                                                                        'SOURCE_CATEGORY_LIST'=>$parentCategoriesList,
                                                                        'TARGET_CATEGORY_ID'=>$newParentCatId));

    }

    /**
     * Gets a product name or product name list by the product Id (products ID).
     *
     * @ check if this function is needed. getProductInfo
     * @author Alexander Girin
     * @param mixed $prodId product id (products ID)
     * @return mixed product name (products name)
     */
    function getProductNameById($prodId, $use_paginator=true)
    {
        $params = array('pid' => $prodId, 'paginator' => null);
        if ($use_paginator)
        {
            $params['paginator'] = execQueryPaginator('SELECT_PRODUCT_NAME_BY_ID', $params);
        }
        return execQuery('SELECT_PRODUCT_NAME_BY_ID', $params);
    }

    /**
     * Returns complete attribute info for the specified product type.
     *
     * @param integer $type_id product type id.
     * @param string the attribute name.
     * @ change it for more optimized version
     */
    function getAttributeInfo($type_id, $name)
    {
        $attributes = $this->getProductTypeAttributes($type_id);
        if (!array_key_exists($name, $attributes))
        {
            return '';
        }

        $attr = $attributes[$name];
        $result = '';
        if ($attr['input_type_name'] == 'select')
        {
            $result = 'test';
        }
        else
        {
            $result = $attr;
        }
        return $result;
    }


    /**
     * Gets complete product information with the id and returned as
     * associative array.<br>
     * It is used caching.
     * The array structure:
     * <code>
     * array(
     *   'attribute' => 'value' //  describe all attributes
     *  ,'attributes' => array (
     *     'attribute' => array (
     *      ,'id'       => // the attribute id
     *      ,'name'     => // the attribute name
     *      ,'size'     => // the input field size
     *      ,'min'      => // the min number of inputted characters
     *      ,'max'      => // the max number of inputted characters
     *      ,'view_tag' => // the attribute tag to use in the templates
     *      ,'group'    => array ( // info about the group, to which the
                                      attribute refers
     *         'id'   => // the id
     *        ,'name' => // the name
     *        ,'sort' => // the sorting number
     *       )
     *      ,'required' => // the flag the required input
     *      ,'visible'  => // the flag of the product visibility
     *      ,'default'  => // the default value on inputting
     *      ,'sort'     => // the sorting serial number
     *      ,'input_type_id'     => // the type id of input field
     *      ,'input_type_name'   => // the type name of input field
     *      ,'input_type_values' => array ( // ???
     *         'key' => 'value'
     *       )
     *      ,'unit_type_id'     => // the unit id
     *      ,'unit_type_name'   => // the unit name
     *      ,'unit_type_value'  => // the unit value
     *      ,'unit_type_values' => array ( // ???
     *         'key' => 'value'
     *       )
     *      ,'pa_id'    => // the attribute id for the specified product
     *      ,'value'    => // the attribute value for the specified product
     *     )
     *   )
     * )
     * </code>
     * @param integer $prod_id
     * @return array the array containing product info.
     * @see Catalog::getProductType()
     * @see Catalog::getProductTypeAttributes()
     */
    function getProductInfo($prod_id, $localized=true, $quantity=1)
    {
        global $application;
        global $zone;

        if (!$this->isCorrectProductId($prod_id))
        {
            return null;
        }

        $result = execQuery('SELECT_GENERAL_PRODUCT_ATTRIBUTES', array('prod_id' => $prod_id));

        if (sizeof($result)<=0)
        {
            return null;
        }

        /**
         * Get real attribute values for the given product,
         * results are in $product_attributes.
         */
        $attr_result = execQuery('SELECT_REAL_PRODUCT_ATTRIBUTES', array('prod_id' => $prod_id));

        $product_attributes = array();
        for ($j=0; $j<sizeof($attr_result); $j++)
        {
            $view_tag = $attr_result[$j]['view_tag'];
            $product_attributes[$view_tag] = $attr_result[$j];
        }

        //          SalePrice,
        //                                TaxClass.
        $tax_class_id = NULL;
        foreach ($product_attributes as $index => $attribute)
        {
            if (strtolower($index) == 'taxclass')
            {
                $tax_class_id = $product_attributes[$index]['value'];
                break;
            }
        }

        $price_including_included_taxes_if_any = NULL;
        foreach ($product_attributes as $index => $attribute)
        {
            if (strtolower($index) == 'saleprice')
            {
                if($zone == 'CustomerZone')
                {
                    $membership = modApiFunc('Customer_Account','getCurrentSignedCustomerGroupID');
                    $fixed_price = modApiFunc('Quantity_Discounts',
                                              'getFixedPrice',
                                              $prod_id,
                                              $quantity,
                                              $product_attributes[$index]['value'],
                                              $membership);
                    $product_attributes[$index]['value'] = $fixed_price;
                }

                $price_including_included_taxes_if_any = $product_attributes[$index]['value'];

                $product_attributes['salepriceexcludingtaxes'] = $product_attributes[$index];
                $product_attributes['salepriceexcludingtaxes']['id'] = NULL;
                $product_attributes['salepriceexcludingtaxes']['view_tag'] = NULL;

                //     TaxClass             -        ,                     .
                if($tax_class_id !== NULL)
                {
                    //                                         .
                    $price_excluding_taxes = modApiFunc("Catalog", "computePriceExcludingTaxes", $product_attributes[$index]['value'], $tax_class_id, false, $quantity, $prod_id);
                    $product_attributes[$index]['value'] = $price_excluding_taxes;

                    //              _   _                         'salepriceexcludingtaxes':
                    $product_attributes['salepriceexcludingtaxes']['value'] = $price_excluding_taxes;
                }
                break;
            }
        }

        //          SalePrice,                               "          "       .
        //     TaxClass             -        ,                     .
        $display_product_price_including_taxes = modApiFunc('Settings', 'getParamValue', 'TAXES_PARAMS', "DISPLAY_PRICES_W_INCLUDED_TAXES");

        foreach ($product_attributes as $index => $attribute)
        {
            if (strtolower($index) == 'saleprice')
            {
                $product_attributes['salepriceincludingtaxes'] = $product_attributes[$index];
                $product_attributes['salepriceincludingtaxes']['id'] = NULL;
                $product_attributes['salepriceincludingtaxes']['view_tag'] = NULL;

                if($tax_class_id !== NULL)
                {
                    //                                         .
                    $price_including_taxes = $price_including_included_taxes_if_any;
                    //                                             ,
                    //        ,             "          "
                    //modApiFunc("Catalog", "computePriceIncludingTaxes", $product_attributes[$index]['value'], $tax_class_id);

                    //              _c_                          'salepriceincludingtaxes':
                    $product_attributes['salepriceincludingtaxes']['value'] = $price_including_taxes;

                    if($display_product_price_including_taxes == DB_TRUE)
                    {
                        $product_attributes[$index]['value'] = $price_including_taxes;
                    }
                }
                break;
            }
        }

        $product = array(); //the array of tags and their values
        $product['attributes'] = array();

        // add info with the artificial attributes
        $product['Updated'] = modApiFunc("Localization", "SQL_date_format", $result[0]['date_updated']);
        $product['Added'] = modApiFunc("Localization", "SQL_date_format", $result[0]['date_added']);
        $product['TypeID'] = $result[0]['type_id'];
        $product['TypeName'] = $result[0]['type_name'];
        //$product['Catalog_ID'] = $result[0]['catalog_id'];

        // define the attribute list or the given product type
        $product_type = $this->getProductType($result[0]['type_id']);

        // copy attributes to the product description
        foreach ($product_type['attr'] as $view_tag => $attr)
        {
            $product[$view_tag] = ''; // initialize the attribute value.
            $product['attributes'][$view_tag] = array();
            $product_attribut = &$product['attributes'][$view_tag];
            $product_attribut['id'] = $attr['id'];
            $product_attribut['name'] = $attr['name'];
            $product_attribut['size'] = $attr['size'];
            $product_attribut['min'] = $attr['min'];
            $product_attribut['max'] = $attr['max'];
            $product_attribut['view_tag'] = $attr['view_tag'];
            $product_attribut['group'] = $attr['group'];
            $product_attribut['required'] = $attr['required'];
            $product_attribut['visible'] = $attr['visible'];
            $product_attribut['default'] = $attr['default'];
            $product_attribut['sort'] = $attr['sort'];
            $product_attribut['allow_html'] = $attr['allow_html'];
            $product_attribut['multilang'] = $attr['multilang'];
            $product_attribut['patt'] = $attr['patt'];
            $product_attribut['patt_type'] = $attr['patt_type'];
            $product_attribut['input_type_id'] = $attr['input_type_id'];
            $product_attribut['input_type_name'] = $attr['input_type_name'];
            $product_attribut['input_type_values'] = isset($attr['input_type_values']) ? $attr['input_type_values'] : null;
            $product_attribut['unit_type_value'] = $attr['unit_type_value'];
            $product_attribut['unit_type_values'] = isset($attr['unit_type_values']) ? $attr['unit_type_values'] : null;
            $product_attribut['unit_type_values_pattern'] = isset($attr['unit_type_values_pattern']) ? $attr['unit_type_values_pattern'] : null;
            $product_attribut['pa_id'] = null;
            $product_attribut['value'] = null;
            $product_attribut['additional_link'] = isset($attr['additional_link'])? $attr['additional_link']:"";
            $product_attribut['additional_link_text'] = isset($attr['additional_link_text'])? $attr['additional_link_text']:"";
            // add real attribute values for the given product
            if (!array_key_exists($view_tag, $product_attributes))
            {
                $imagesUrl = $application->getAppIni('URL_IMAGES_DIR');
                if ($application->getCurrentProtocol() == "https" && $application->getAppIni('HTTPS_URL_IMAGES_DIR'))
                {
                    $imagesUrl = $application->getAppIni('HTTPS_URL_IMAGES_DIR');
                }
                if ($product_attribut['input_type_name'] == 'image')
                {
                    $product_attribut['value'] = array(
                       'url' => ''
                      ,'exists' => false
                      ,'width' => 1
                      ,'height' => 1
                       );
                    // The image is not found. Output a standard one, if necessary.
                    $product[$view_tag] = '';
                    $product[$view_tag.'Src'] = ''; //$imagesUrl.$product_attribut['value']['url'];
                    $product[$view_tag.'Width'] = '1'; //$product_attribut['value']['width'];
                    $product[$view_tag.'Height'] = '1'; //$product_attribut['value']['height'];
                }
            }
            else
            {
                //  the id is in the product_attributes table
                $product_attribut['pa_id'] = $product_attributes[$view_tag]['id'];
                // the attribute value
                $product_attribut['value'] = $product_attributes[$view_tag]['value'];
                if (!$product_attribut['allow_html'] && $localized)
                {
                    // Correct, because the HTML code is unavailable.
                    $product_attribut['value'] = prepareHTMLDisplay($product_attributes[$view_tag]['value']);
                }
                if ($localized)
                {
                    $product[$view_tag] = modApiFunc("Localization", "format", $product_attribut['value'], $product_attribut['patt_type']);
                }
                else
                {
                    $product[$view_tag] = $product_attribut['value'];
                }
                // determine the URL of the image  remove it later
                if ($product_attribut['input_type_name'] == 'image') //LargeImage, SmallImage for products, as for 2005 oct.
                {
                    $imagesUrl = $application->getAppIni('URL_IMAGES_DIR');
                    if ($application->getCurrentProtocol() == "https" && $application->getAppIni('HTTPS_URL_IMAGES_DIR'))
                    {
                        $imagesUrl = $application->getAppIni('HTTPS_URL_IMAGES_DIR');
                    }
                    if ($product_attributes[$view_tag]['image_name'] != null && $application->isImageFileValid($product_attributes[$view_tag]['image_name']))
                    {
                        $product_attribut['value'] = array(
                           'url' => $product_attributes[$view_tag]['image_name']
                          ,'exists' => true
                          ,'width' => $product_attributes[$view_tag]['image_width']
                          ,'height' => $product_attributes[$view_tag]['image_height']
                           );
                    }
                    else
                    {
                        $product_attribut['value'] = array(
                           'url' => ''
                          ,'exists' => false
                          ,'width' => 1
                          ,'height' => 1
                           );
                    }
                    // generate the values for the derived image attributes
                    if ($product_attribut['value']['exists'])
                    {
                        $product[$view_tag] = '<img src="'.$imagesUrl.$product_attribut['value']['url'].'" width="'.$product_attribut['value']['width'].'" height="'.$product_attribut['value']['height'].'" alt="'.$product_attributes['ImageAltText']['value'].'"/>';
                        $product[$view_tag.'Src'] = $imagesUrl.$product_attribut['value']['url'];
                        $product[$view_tag.'Width'] = $product_attribut['value']['width'];
                        $product[$view_tag.'Height'] = $product_attribut['value']['height'];
                    }
                    else
                    {
                        $product[$view_tag] = '';

                        $product[$view_tag.'Src'] = '';
                        $product[$view_tag.'Width'] = '1';
                        $product[$view_tag.'Height'] = '1';
                    }

                }

                // add the Unit value, if it exists
                if ($product_attribut['patt_type']!='currency' && $product_attribut['unit_type_value'])
                {
                    $product[$view_tag] .= ' '.$product_attribut['unit_type_value'];
                }
                // Correct the attribute value, if it is specified input_type for it.
                if (isset($product_attribut['input_type_values']) && $product_attribut['value'] != '')
                {
                    $product[$view_tag] = $product_attribut['input_type_values'][$product_attributes[$view_tag]['value']];
                }
            }
            // if the attribute is not visible, delete values for CZ.
            if (!$attr['visible'])
            {
                $product[$view_tag] = '';
            }
        }

        // image for Gift Certificate
        if ($product['TypeID'] == -1)
        {
            $imageInfo = modApiFunc('GiftCertificateApi', 'getImageInfo');
            if($imageInfo['largeimage']['is_exist']===true)
            {
                $product['LargeImage'] = "<img src='".$imageInfo['largeimage']['url']."' width='".$imageInfo['largeimage']['width']."' height='".$imageInfo['largeimage']['height']."' alt='' />";
                $product['LargeImageSrc'] = $imageInfo['largeimage']['url'];
                $product['LargeImageWidth'] = $imageInfo['largeimage']['width'];
                $product['LargeImageHeight'] = $imageInfo['largeimage']['height'];
            }
            if($imageInfo['smallimage']['is_exist']===true)
            {
                $product['SmallImage'] = "<img src='".$imageInfo['smallimage']['url']."' width='".$imageInfo['smallimage']['width']."' height='".$imageInfo['smallimage']['height']."' alt='' />";
                $product['SmallImageSrc'] = $imageInfo['smallimage']['url'];
                $product['SmallImageWidth'] = $imageInfo['smallimage']['width'];
                $product['SmallImageHeight'] = $imageInfo['smallimage']['height'];
            }
        }

        $product['ID'] = $result[0]['id'];
        $product['attributes']['ID']['value'] = $result[0]['id'];
        $product['Name'] = $localized ? prepareHTMLDisplay($result[0]['name']):$result[0]['name'];
        $product['attributes']['Name']['value'] = $localized ? prepareHTMLDisplay($result[0]['name']):$result[0]['name'];

        $product['salepriceincludingtaxes'] = $localized ? modApiFunc("Localization", "format", $product_attributes['salepriceincludingtaxes']['value'], 'currency'):$product_attributes['salepriceincludingtaxes']['value'];
        //                         .     SalePrice.
        $product['attributes']['salepriceincludingtaxes']['value'] = $product_attributes['salepriceincludingtaxes']['value'];
        $product['salepriceexcludingtaxes'] = $localized ? modApiFunc("Localization", "format", $product_attributes['salepriceexcludingtaxes']['value'], 'currency'):$product_attributes['salepriceexcludingtaxes']['value'];
        //                         .     SalePrice.
        $product['attributes']['salepriceexcludingtaxes']['value'] = $product_attributes['salepriceexcludingtaxes']['value'];
        #  create required references
        ### InfoLink
        $request = new Request();
        $request->setView  ( 'ProductInfo' );
        $request->setAction( 'SetCurrentProduct' );
        $request->setKey   ( 'prod_id', $product['ID']);
        //$request->setCategoryID($product['Catalog_ID']);
        $request->setProductID($product['ID']);

        $_cProductInfo = new CProductInfo($product['ID']);
        $product['InfoLink'] = $_cProductInfo->getProductTagValue('InfoLink');

        ### BuyLink
        $request = new Request();
        modApiFunc("Configuration", "getValue", "store_show_cart")? $request->setView('CartContent'):$request->setView(CURRENT_REQUEST_URL);
        $request->setAction( 'AddToCart' );
        $request->setKey   ( 'prod_id', $product['ID']);
        //$request->setCategoryID($product['Catalog_ID']);
        $request->setProductID($product['ID']);
        $product['BuyLink'] = $request->getURL();

        ### CategoryLink.
        /*
        $request = new Request();
        $request->setView('ProductList');
        $request->setAction( "SetCurrCat" );
        $request->setKey   ( "category_id", $product['Catalog_ID']);
        $request->setCategoryID($product['Catalog_ID']);
        $request->setProductID($product['ID']);
        $product['CategoryLink'] = $request->getURL();
        */

        #                  .                       view                                                          .
        $prodObj = new CProductInfo($product['ID']);
        $product['CategoryLink'] = $prodObj->getProductTagValue('CategoryLink');
        $product['CategoryID'] = $prodObj->getProductTagValue('CategoryID');

        return $product;
    }

    /**
     * It is similar to getProductInfo().
     * It is outputted standard or only custom attributes info, which is
     * specified by the in-parameter.
     * Including info about artificial attributes, such as Name,
     * is specified by the in-parameter.
     * The attribute values are not processed fro inputting (for example,
     * by the function prepareHTMLDisplay).
     * </code>
     *
     * @param integer $prod_id
     * @return array the array containing product info.
     * @see Catalog::getProductType()
     * @see Catalog::getProductTypeAttributes()
     */
    function getProductInfoRaw($prod_id)
    {
        if (!$this->isCorrectProductId($prod_id))
        {
            return null;
        }

        $result = execQuery('SELECT_GENERAL_PRODUCT_ATTRIBUTES', array('prod_id' => $prod_id));

        if (sizeof($result)<=0)
        {
            return null;
        }

        /**
         * Get real attribute values for the given product,
         * results are $product_attributes.
         */
        $attr_result = execQuery('SELECT_REAL_PRODUCT_ATTRIBUTES', array('prod_id' => $prod_id));

        $product_attributes = array();

        for ($j=0; $j<sizeof($attr_result); $j++)
        {
            $view_tag = $attr_result[$j]['view_tag'];
            $product_attributes[$view_tag] = $attr_result[$j];
        }

        $product = array(); //the array of tags and their values
        $product['attributes'] = array();

        // define the attribute list or the given product type
        $product_type = $this->getProductType($result[0]['type_id']);

        // copy attributes to the product description
        foreach ($product_type['attr'] as $view_tag => $attr)
        {
            // don't pass artificial attributes
            //if(!empty($product_attributes[$view_tag]['value']))
            if($attr['visible']==1 and isset($product_attributes[$view_tag]))
            {
                $product[$view_tag] = $product_attributes[$view_tag]['value']; //  initialize the attribute value.
                $product['attributes'][$view_tag] = array();
                $product_attribut = &$product['attributes'][$view_tag];

                $product_attribut['id'] = $attr['id'];
                $product_attribut['view_tag'] = $attr['view_tag'];
                $product_attribut['attr_type'] = $product_attributes[$view_tag]['attr_type'];
                $product_attribut['name'] = $attr['name'];
                //
                //  the attributes value
                $product_attribut['value'] = $product_attributes[$view_tag]['value'];
            }
        }

        //add artificial attributes
        $product['ID'] = $result[0]['id'];
        $product['attributes']['ID']['value'] = $result[0]['id'];
        $product['Name'] = $result[0]['name'];
        $product['attributes']['Name']['value'] = $result[0]['name'];
        $product['TypeID'] = $result[0]['type_id'];
        $product['attributes']['TypeID']['value'] = $result[0]['type_id'];

        return $product;
    }

    /**
     * Saves product info after editing.
     * It also checks whether each attribute for the given product exists.
     * If some attribute is not defined, a new record is created for it.
     *
     * @param $product_id integer the product id
     * @param $product_type_id integer hte product type id
     * @param $product_info array the associative array containing product info.
     * @         $product_info
     * @see Catalog::getProductType()
     */
    function updateProductInfo($product_id, $product_type_id, $product_info)
    {
        global $application;
        $tables = $this->getTables();

        $productAvailableChanged = false;
        $current_info = $this->getProductInfo($product_id);
        if ($current_info["Available"] == "Online" && @$product_info["Available"] == '4'
            || $current_info["Available"] == "Offline" && @$product_info["Available"] == '3')
        {
            $productAvailableChanged = true;
        }

        $product_type = $this->getProductType($product_type_id);

        $product_info['product_id'] = $product_id;
        execQuery('UPDATE_GENERAL_PRODUCT_INFO', $product_info);
        unset($product_info['product_id']);

        $pa  = $tables['product_attributes']['columns'];
        $pi  = $tables['product_images']['columns'];
        foreach ($product_type['attr'] as $view_tag => $attr)
        {
            // skip artificial and invisible attributes
            if ($view_tag == 'ID' || $view_tag == 'Name' || !$attr['visible'])
            {
                continue;
            }

            // prepare SEO URL tag
            if($view_tag == 'SEOPrefix' and isset($product_info[$view_tag]))
            {
                $product_info[$view_tag] = $this->_prepareSEOURL($product_info[$view_tag]);
            }

            // define if the value for the given attributes exists in the DB.
            $result = execQuery('SELECT_BASIC_PRODUCT_ATTRIBUTE_DATA', array('aid' => $attr['id'], 'pid' => $product_id));
            $pa_id = 0;

            //                                               (basename).                  product_attributes
            //        :                                                          ,                     - http
            //       .                                   ,                                     .
            //     product_attributes                  ,                          .
/*            if ($attr['input_type_name'] == 'image' && $product_info[$view_tag])
            {
                $product_info[$view_tag] = basename($product_info[$view_tag]);
            }
            */

            if ($result && isset($product_info[$view_tag]))
            {
                // The attribute exists already. Update.
                $pa_id = $result[0]['id'];
                execQuery('UPDATE_PRODUCT_ATTRIBUTE_VALUE', array('value' => $product_info[$view_tag], 'pid' => $product_id, 'aid' => $attr['id']));
            }
            elseif (isset($product_info[$view_tag]))
            {
                // The attribute doesn't exist. Add.
                execQuery('INSERT_NEW_PRODUCT_ATTRIBUTE', array('pa_value' => $product_info[$view_tag], 'p_id' => $product_id, 'a_id' => $attr['id']));
            }
            // If it is an image, save it to the separate table
            if ($attr['input_type_name'] == 'image' && isset($product_info[$view_tag]))
            {
                $file_name = str_replace($application->appIni['URL_IMAGES_DIR'],'',$product_info[$view_tag]);

                //                                             :
                if(!$result)
                {
                    $b_file_changed = true;
                }
                else
                {
                    //                            product_images
                    $image_result = execQuery('SELECT_PRODUCT_IMAGE_NAME_BY_ATTR_ID', array('pa_id' => $pa_id));

                    if(sizeof($image_result) !== 1)
                    {
                        //      ?
                        $b_file_changed = true;
                    }
                    else
                    {
                        $old_file_name = $image_result[0]['name'];
                        $b_file_changed = ($old_file_name != $file_name);
                    }
                }

                //!$file_name -                ?
                if ($b_file_changed || !$file_name)
                {
                    $result = execQuery('SELECT_PRODUCT_IMAGE_NAME_BY_ATTR_ID', array('pa_id' => $pa_id));
                    foreach ($result as $image)
                    {
                        //@unlink($application->getAppIni('PATH_IMAGES_DIR').$image['name']);
                    }
                    // delete all the images for this attribute
                    execQuery('DELETE_PRODUCT_IMAGE_BY_ATTR_ID', array('pa_id' => $pa_id));

                    //                   ,                                                      .
                    if ($b_file_changed && $file_name)
                    {
                        // if the image has been uploaded, save it to the folder and to the base.
                        $image_info = $application->saveUploadImage("", $file_name);
                        $image_info['pa_id'] = $pa_id;
                        execQuery('INSERT_NEW_PRODUCT_IMAGE', $image_info);
                    }
                }
            }
        }

        if ($productAvailableChanged)
        {
           modApiFunc('EventsManager','throwEvent','ProductOnlineOffline', $product_id);
        }
        modApiFunc('EventsManager','throwEvent','ProductInfoChanged', $product_id);
	/** Hook for doing action on product info updated **/
        do_action("az_catalog_prodinfo_updated",$product_id);
    }

    function _updateProduct($product_id, $product_info)
    {
        global $application;

        $product_info['product_id'] = $product_id;
        execQuery('UPDATE_GENERAL_PRODUCT_INFO', $product_info);
        unset($product_info['product_id']);

        //dirty hack
        $attributes_not_for_import = array('Manufacturer');

        foreach($product_info as $attr_name => $attr_value)
        {
            if($attr_name == 'Name' || in_array($attr_name, $attributes_not_for_import))
                continue;

            //$attr_info = $this->_getAttrInfoByViewTag($attr_name);
            $attr_id = $this->_getAttrIdByViewTagAndProductTypeId($attr_name, $product_info['ptype_id']);
            if($attr_id == null)
                continue;

            if($attr_name == 'SEOPrefix')
                $attr_value = $this->_prepareSEOURL($attr_value);

            //$attr_id = $attr_info['attribute_id'];

            $pa_info = execQuery('SELECT_PRODUCT_ATTRIBUTES_BY_ATTRIBUTE_ID', array('attribute_id' => $attr_id, 'product_id' => $product_id));
            if ($pa_info)
                execQuery('UPDATE_PRODUCT_ATTRIBUTE_VALUE', array('value' => $attr_value, 'pid' => $product_id, 'aid' => $attr_id));
            else
                execQuery('INSERT_NEW_PRODUCT_ATTRIBUTE', array('pa_value' => $attr_value, 'p_id' => $product_id, 'a_id' => $attr_id));

        };

        modApiFunc('EventsManager','throwEvent','ProductInfoChanged', $product_id);
    }

    function _getAttrIdByViewTagAndProductTypeId($attr_name, $ptid)
    {
        $r = execQuery('SELECT_ATTR_ID_BY_VIEW_TAG_AND_PRODUCT_TYPE_ID', array('ptid'=>$ptid, 'view_tag'=>$attr_name));
        if (empty($r) || count($r)>1)
        {
            return null;
        }
        else
        {
            return $r[0]['attr_id'];
        }
    }

    function _attachImageToProduct($product_id, $image_path, $image_tag_name)
    {
        if($image_path == '')
            return;

        global $application;

        $attr_info = $this->_getAttrInfoByViewTag($image_tag_name);
        if($attr_info == null)
            return;
        $attr_id = $attr_info['attribute_id'];

        $res = execQuery('SELECT_BASIC_PRODUCT_ATTRIBUTE_DATA', array('aid' => $attr_id, 'pid' => $product_id));

        if(!empty($res))
        {
            $res_img = execQuery('SELECT_PRODUCT_IMAGE_NAME_BY_ATTR_ID', array('pa_id' => $res[0]['id']));
            if(!empty($res_img))
            {
                $filename = $application->getAppIni('PATH_IMAGES_DIR').$res_img[0]['name'];
                //@unlink($filename);
                execQuery('DELETE_PRODUCT_IMAGE_BY_ATTR_ID', array('pa_id' => $res[0]['id']));
            };
            execQuery('DELETE_PRODUCT_ATTRIBUTE_BY_ATTR_ID', array('id' => $res[0]['id']));
        };

        $image_info = getimagesize($image_path);
        $image_info['ext'] = str_replace("image/",".",$image_info['mime']);
        $image_info['width'] = $image_info[0];
        $image_info['height'] = $image_info[1];

        do
        {
            $image_file_path = $application->getUploadImageName($image_path);
            $image_file_name = basename($image_file_path);
        }
        while(file_exists($image_file_path));

        if(@copy($image_path, $image_file_path))
        {
            execQuery('INSERT_NEW_PRODUCT_ATTRIBUTE', array('p_id' => $product_id, 'a_id' => $attr_id, 'pa_value' => $application -> getAppIni('URL_IMAGES_DIR') . $image_file_name));
            $aid = $application->db->DB_Insert_Id();

            execQuery('INSERT_NEW_PRODUCT_IMAGE', array('pa_id' => $aid, 'name' => $image_file_name, 'width' => $image_info['width'], 'height' => $image_info['height']));
        };
    }

    function _getAttrInfoByViewTag($tag,$full=false)
    {
        $res = execQuery('SELECT_ATTRIBUTE_INFO_BY_TAG', array('tag' => $tag));

        if(empty($res) or count($res)>1)
            return null;

        $info = array_shift($res);

        if($full)
        {
            $res = execQuery('SELECT_INPUT_TYPE_NAME_BY_ID', array('id' => $info['input_type_id']));
            $info['input_type_name'] = _ml_strtolower($res[0]['input_type_name']);
        };
        return $info;
    }

    /**
     * Saves new product info.
     *
     * @param $product_type_id integer the product type id
     * @param $category_id array the array containing product info.
     * @param $product_info array the array containing product info.
     * @return int $product_id - new product ID
     * @ describe $product_info
     * @see Catalog::getProductType()
     */
    function addProductInfo($product_type_id, $category_id, $product_info)
    {
        global $application;

        $product_type = $this->getProductType($product_type_id);

        $res = execQuery('INSERT_NEW_PRODUCT', array('p_type_id' => $product_type_id, 'p_name' => $product_info['Name']));


        $product_id = $application->db->DB_Insert_Id();

        //<add_link>
        $this->addProductLinkToCategory($product_id,$category_id);
        //</add_link>

        foreach ($product_type['attr'] as $view_tag => $attr)
        {
            // skip artificial attributes
            if ($view_tag == 'ID' || $view_tag == 'Name')
            {
                continue;
            }

            // prepare SEO URL tag
            if($view_tag == 'SEOPrefix' and isset($product_info[$view_tag]))
            {
                $product_info[$view_tag] = $this->_prepareSEOURL($product_info[$view_tag]);
            }

            execQuery('INSERT_NEW_PRODUCT_ATTRIBUTE', array('p_id' => $product_id, 'a_id' => $attr['id'], 'pa_value' => (isset($product_info[$view_tag]) ? $product_info[$view_tag] : $attr['default'])));
            $pa_id = $application->db->DB_Insert_Id();

            //  If it is an image, save it to the separate table.
            if ($attr['input_type_name'] == 'image')
            {
                if ($product_info[$view_tag])
                {
                    //  define if the value for the given attributes exists in the DB.
                    $tmp_name = str_replace($application->appIni['URL_IMAGES_DIR'],'',$product_info[$view_tag]);

                    $image_info = $application->saveUploadImage("", $tmp_name);
                    $image_info['pa_id'] = $pa_id;
                    execQuery('INSERT_NEW_PRODUCT_IMAGE', $image_info);
                }
            }

        }

        modApiFunc('EventsManager','throwEvent','ProductAdded', $product_id);

        //return the new product ID to use later in different hooks
        return $product_id;
    }

    /**
     * Adds a new product type.
     */
    function addProductType($product_type)
    {
        global $application;

        execQuery('INSERT_NEW_PRODUCT_TYPE', array('name' => $product_type['TypeName'], 'descr' => $product_type['TypeDescr']));
        $product_type_id = $application->db->DB_Insert_Id();

        foreach ($product_type as $view_tag => $attr)
        {
            if (!is_array($attr))
                continue;

            $attr['pt_id'] = $product_type_id;

            if ($attr['type'] == 'custom')
            {
                // create a new custom attribute
                $attr['group_id'] = $attr['group']['id'];
                execQuery('INSERT_NEW_ATTRIBUTE', $attr);
                $attr['id'] = $application->db->DB_Insert_Id();

                // associate with the given type
                execQuery('INSERT_NEW_PRODUCT_TYPE_ATTRIBUTE', $attr);
            }
            elseif ($attr['type'] == 'standard')
            {
                execQuery('INSERT_NEW_PRODUCT_TYPE_ATTRIBUTE', $attr);
            }
        }
        return $product_type_id;
    }


    function getCustomAttrID($attr_tag_name)
    {
        $result = execQuery('SELECT_CUSTOM_ATTRIBUTE_ID_BY_TAG', array('tag' => $attr_tag_name));

        if (isset($result[0]))
            return $result[0]['id'];

        return 0;
    }


    /**
     * Adds a new generated product type.
     */
    function addGeneratedProductType($product_type)
    {
        global $application;

        execQuery('INSERT_NEW_PRODUCT_TYPE', array('name' => $product_type['TypeName'], 'descr' => $product_type['TypeDescr']));
        $product_type_id = $application->db->DB_Insert_Id();

        foreach ($product_type as $view_tag => $attr)
        {

            if ($attr['type'] == 'custom')
            {
                $attr['pt_id'] = $product_type_id;

                // search, if the attribute with the same name exists already in the database
                $attribute_id = $this->getCustomAttrID($attr['view_tag']);

                // if such attribute doesn't exist,then create it
                if (!$attribute_id)
                {
                    // create a new custom attribute
                    $attr['group_id'] = $attr['group']['id'];
                    execQuery('INSERT_NEW_ATTRIBUTE', $attr);
                    $attr['id'] = $application->db->DB_Insert_Id();
                }

                // associate with the given type
                execQuery('INSERT_NEW_PRODUCT_TYPE_ATTRIBUTE', $attr);
            }
            elseif ($attr['type'] == 'standard')
            {
                execQuery('INSERT_NEW_PRODUCT_TYPE_ATTRIBUTE', $attr);
            }
        }
        return $product_type_id;
    }


    function createCustomAttr($attr_name)
    {
        $attr = array(
                'id'                => null
               ,'pta_id'            => null
               ,'name'              => prepareHTMLDisplay($attr_name)
               ,'descr'             => prepareHTMLDisplay($attr_name." attribute has been generated automatically from CSV file.")
               ,'size'              => 70
               ,'min'               => 2
               ,'max'               => 255
               ,'view_tag'          => prepareHTMLDisplay($attr_name)
               ,'group'             => array('id'=>6, 'name'=>'Custom Attributes', 'sort'=>6) // @ hard-coded
               ,'required'          => false
               ,'visible'           => true
               ,'default'           => ''
               ,'sort'              => 1
               ,'type'              => 'custom'
               ,'patt'              => null
               ,'patt_type'         => null
               ,'input_type_id'     => 1
               ,'input_type_name'   => 'text'
               ,'unit_type_value'   => null
               ,'vanishing'         => true
               ,'allow_html'        => 0
               );
        return $attr;
    }

    function createProductTypeInfo($base_product_type, $attr_names)
    {

        $names = array();
        $is_custom = array();

        foreach ($attr_names as $attr_name)
        {
            $names[] = $attr_name['name'];
            $is_custom[$attr_name['name']] = $attr_name['isCustom'];
        }

        $attr_in_base = array();
        foreach ($base_product_type as $view_tag => $attr)
        {
            $attr_in_type = false;
            if (in_array($view_tag, $names))
            {
                $attr_in_type = true;
                $attr_in_base[] = $view_tag;
            }
            else
            {

                // The name of Custom attributes are distorted: XXX turns to Xxx. To find them you should ignore the register
                if ($attr['type'] == 'custom')
                {
                    foreach ($names as $name)
                    {
                        if (_ml_strcasecmp($view_tag, $name) == 0)
                        {
                            $attr_in_base[] = $name;
                            $attr_in_type = true;
                            break;
                        }
                    }
                }
            }

            if ($attr_in_type)
            {
                $base_product_type[$view_tag]['visible'] = 1;
            }
            else
            {
                $base_product_type[$view_tag]['visible'] = 0;
            }
        }

        // Create those attributes that doesn't exist in the base type
        foreach ($names as $name)
        {
            // search those attributes, that doesn't exist in the base type
            if (!in_array($name, $attr_in_base))
            {
                $new_custom_attr = $this->createCustomAttr($name);
                $base_product_type[$name] = $new_custom_attr;
            }
        }
        return $base_product_type;
    }

    /**
     * Creates a new product type based on the list of atribute names.
     */

    function createProductType($attr_names, $new_product_type_name)
    {
            // Gets an attribute list of base type
        $product_type = modApiFunc('Catalog', 'getProductType', 1);
        $new_product_type = $this->createProductTypeInfo($product_type['attr'], $attr_names);

        $new_product_type['TypeName'] = $new_product_type_name;
        $new_product_type['TypeDescr'] = 'This type has been generated automatically during import from CSV file';

        $product_type_id = modApiFunc('Catalog', 'addGeneratedProductType', $new_product_type);
        // return the type id
        return $product_type_id;
    }

    function extendProductType($product_type_id, $attr_names)
    {
        // Get a list of base type attributes
        $product_type = modApiFunc('Catalog', 'getProductType', $product_type_id);
        $extended_product_type = $this->createProductTypeInfo($product_type['attr'], $attr_names);

        $extended_product_type['TypeName'] = $product_type['name'];
        $extended_product_type['TypeDescr'] = $product_type['description'];
        modApiFunc('Catalog', 'updateProductType', $product_type_id, $extended_product_type);
    }

    /**
     * Updates the existing product type.
     */
    function updateProductType($product_type_id, $product_type)
    {
        global $application;

        // find the current custom attributes for this type
        $result = execQuery('SELECT_CUSTOM_ATTRIBUTES_BY_PRODUCT_TYPE', array('pt_id' => $product_type_id));

        $current_custom_attributes = array();
        if (sizeof($result) > 0)
        {
            for ($j=0; $j<sizeof($result); $j++)
            {
                $current_custom_attributes[] = $result[$j]['a_id'];
            }
        }

        // update the name and product type description
        execQuery('UPDATE_PRODUCT_TYPE_RECORD', array('name' => $product_type['TypeName'], 'descr' => $product_type['TypeDescr'], 'pt_id' => $product_type_id));

        /*
        $result_vis = execQuery('SELECT_PRODUCT_TYPE_ATTRIBUTE_VISIBILITY', array('pt_id' => $product_type_id));
        $attr_visibility = array();
        if (is_array($result_vis)) {
            foreach ($result_vis as $rec) {
                $attr_visibility[ $rec['attribute_id'] ] = $rec['product_type_attr_visibility'] == '1';
            }
        }
        */

        $custom_attributes = array();
        $attributes = array();
        // save current attributes
        foreach ($product_type as $view_tag => $attr)
        {

            if (!is_array($attr))
                continue;

            $attr['pt_id'] = $product_type_id;

            if ($attr['type'] == 'custom')
            {
                if($attr['input_type_name'] == 'select' && !empty($attr['input_type_values']))
                {
                    $res = execQuery('SELECT_INPUT_TYPE', array('input_type_id'=>$attr['input_type_id']));
                    if(empty($res))
                    {
                        execQuery('INSERT_INPUT_TYPE', array('input_type_id'=>$attr['input_type_id'], 'input_type_name'=>strtoupper($attr['input_type_name'])));
                        foreach ($attr['input_type_values'] as $key => $value)
                        {
                            execQuery('INSERT_INPUT_TYPE_VALUE', array('input_type_id'=>$attr['input_type_id'], 'input_type_value'=>$value));
                        }
                    }
                    else
                    {
                        $prev_input_type_values = array();
                        $_prev_input_type_values = execQuery('SELECT_INPUT_TYPE_VALUES', array('input_type_id'=>$attr['input_type_id']));
                        foreach($_prev_input_type_values as $_prev_item)
                        {
                            $prev_input_type_values[$_prev_item['input_type_value_id']] = $_prev_item['input_type_value'];
                        }

                        $removed_input_type_values_ids = array();
                        foreach($prev_input_type_values as $prev_key => $prev_item)
                        {
                            if(!isset($attr['input_type_values'][$prev_key]))
                            {
                            $removed_input_type_values_ids[] = $prev_key;
                            }
                            else
                            {
                                $prev_input_type_values[$prev_key] = $attr['input_type_values'][$prev_key];
                            }
                        }

                        foreach($attr['input_type_values'] as $key => $item)
                        {
                            if(isset($prev_input_type_values[$key]))
                                execQuery('UPDATE_INPUT_TYPE_VALUE', array('value_id' => $key, 'value' => $item));
                            else
                                execQuery('INSERT_INPUT_TYPE_VALUE', array('input_type_id' => $attr['input_type_id'], 'input_type_value' => $item));

                            if($item == getMsg('SYS','PRTYPE_VALUE_NOT_SELECTED'))
                                $input_type_not_selected_id = $key;
                        }


                        if(!empty($removed_input_type_values_ids))
                        {
                            execQuery('DELETE_INPUT_TYPE_VALUES_BY_IDS', array('removed_input_type_values_ids' => $removed_input_type_values_ids));
                            foreach($removed_input_type_values_ids as $key => $item){
                                execQuery('UPDATE_CUSTOM_INPUT_TYPE_VALUES_FOR_PRODUCTS', array('attribute_id' => $attr['id'], 'current_product_attr_value' => $item, 'not_selected_product_attr_value' => $input_type_not_selected_id));
                            }
                        }
                    }
                }

                if ($attr['id'] == null)
                {
                    //                custom attribute
                    //  create a new custom attribute
                    $attr['group_id'] = $attr['group']['id'];
                    execQuery('INSERT_NEW_ATTRIBUTE', $attr);
                    $attr['id'] = $application->db->DB_Insert_Id();
                    $custom_attributes[] = $attr['id'];

                    //  associate with the given type
                    execQuery('INSERT_NEW_PRODUCT_TYPE_ATTRIBUTE', $attr);
                }
                else
                {
                    // update existing custom attribute
                    execQuery('UPDATE_ATTRIBUTE_RECORD', $attr);
                    $custom_attributes[] = $attr['id'];

                    execQuery('UPDATE_PRODUCT_TYPE_ATTRIBUTE_RECORD', $attr);
                }
            }
            elseif ($attr['type'] == 'standard')
            {
                execQuery('UPDATE_PRODUCT_TYPE_ATTRIBUTE_RECORD', $attr);
                $attributes[] = $attr['id'];
            }

            /*
            if (isset($attr_visibility[ $attr['id'] ]) && $attr_visibility[ $attr['id'] ] !== $attr['visible']) {
                CTrace::dbg(sprintf('Update visibility: pt_id=%d, a_id=%d, v=%s', $product_type_id, $attr['id'], $attr['visible'] ? '1': '0'));
                execQuery('UPDATE_PRODUCT_ATTIBUTES_VISIBILITY', array('pt_id' => $product_type_id, 'a_id' => $attr['id'], 'visible' => $attr['visible']));
            }
            */
        }

        //determine deleted attributes
        $diff = array_diff($current_custom_attributes, $custom_attributes);

        if (sizeof($diff) > 0)
        {
            // delete unused attributes from the attributes table
            execQuery('DELETE_ATTRIBUTES_BY_IDS', array('ids' => $diff));

            //delete real values of unused attributes from the product_attributes table
            execQuery('DELETE_PRODUCT_ATTRIBUTES_BY_ATTR_IDS', array('a_ids' => $diff));

            // delete unused attributes from the product_type_attributes table
            // !!! for this product type only
            execQuery('DELETE_PRODUCT_TYPE_ATTRIBUTES_BY_ID_AND_ATTR_IDS', array('pt_id' => $product_type_id, 'a_ids' => $diff));
        }

        modApiFunc('EventsManager','throwEvent','ProductTypeInfoChanged', $product_type_id);
    }

    /**
     * Returns a list of all required attributes for the product type with the
     * specified id.
     */
    function getProductTypeAttributes($product_type_id)
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');

        $attr_result = execQuery('SELECT_PRODUCT_TYPE_ATTRIBUTES', array('pt_id' => (($product_type_id) ? $product_type_id : 1)));

        $product_type = array();
        for ($j=0; $j<sizeof($attr_result); $j++)
        {
            $view_tag = $attr_result[$j]['view_tag'];
            $product_type[$view_tag] = array();
            $product_type[$view_tag]['id'] = $attr_result[$j]['id'];
            $product_type[$view_tag]['pta_id'] = $attr_result[$j]['pta_id'];
            $product_type[$view_tag]['name'] = $attr_result[$j]['name'];
            $product_type[$view_tag]['descr'] = $attr_result[$j]['descr'];
            if ($attr_result[$j]['type']=="standard")
            {
                $product_type[$view_tag]['name'] = $obj->getMessage( new ActionMessage($product_type[$view_tag]['name']) );
                $product_type[$view_tag]['descr'] = $obj->getMessage( new ActionMessage($product_type[$view_tag]['descr']) );
            }
            $product_type[$view_tag]['size'] = $attr_result[$j]['size'];
            $product_type[$view_tag]['min'] = $attr_result[$j]['min'];
            $product_type[$view_tag]['max'] = $attr_result[$j]['max'];
            $product_type[$view_tag]['view_tag'] = $view_tag;
            $product_type[$view_tag]['group'] = array (
                 'id' => $attr_result[$j]['group_id']
                ,'name' => $obj->getMessage($attr_result[$j]['group_name'])
                ,'sort' => $attr_result[$j]['group_sort']
            );
            $product_type[$view_tag]['required'] = $attr_result[$j]['required'];
            $product_type[$view_tag]['visible'] = $attr_result[$j]['visible'];
            $product_type[$view_tag]['default'] = ($product_type_id==0? "":prepareHTMLDisplay($attr_result[$j]['default_value']));
            if ($view_tag == 'ShortDescription' || $view_tag == 'DetailedDescription')
            {
                $product_type[$view_tag]['default'] = $product_type_id==0? "":$attr_result[$j]['default_value'];
            }
            $product_type[$view_tag]['multilang'] = $attr_result[$j]['multilang'];
            $product_type[$view_tag]['sort'] = $attr_result[$j]['sort'];
            $product_type[$view_tag]['type'] = $attr_result[$j]['type'];
            $product_type[$view_tag]['allow_html'] = $attr_result[$j]['allow_html'];
            $product_type[$view_tag]['patt_type'] = $attr_result[$j]['ut'];
            $product_type[$view_tag]['input_type_id'] = $attr_result[$j]['input_type_id'];
            $product_type[$view_tag]['input_type_name'] = _ml_strtolower($attr_result[$j]['input_type_name']);
            $product_type[$view_tag]['unit_type_values_pattern'] = modApiFunc("Localization", "getPattern", $attr_result[$j]['ut']);
            $product_type[$view_tag]['patt'] = $product_type[$view_tag]['unit_type_values_pattern']['patt_value'];
            $product_type[$view_tag]['unit_type_value'] = modApiFunc("Localization", "getUnitTypeValue", $attr_result[$j]['ut']);
        }

        // add artificial attributes
        $view_tag = 'ID';
        $product_type[$view_tag] = array(
            'id'                => null
           ,'pta_id'            => null
           ,'name'              => $obj->getMessage( new ActionMessage('PRD_ID_NAME'))
           ,'descr'             => $obj->getMessage( new ActionMessage('PRD_ID_DESCR'))
           ,'size'              => 70
           ,'min'               => 2
           ,'max'               => 255
           ,'view_tag'          => $view_tag
           ,'group'             => array('id'=>1, 'name'=>'Key Product Details', 'sort'=>1) // @ hard-coded
           ,'required'          => true
           ,'visible'           => true
           ,'default'           => null
           ,'multilang'         => 'N'
           ,'sort'              => -2
           ,'type'              => 'artificial'
           ,'allow_html'        => 0
           ,'patt'              => null
           ,'patt_type'         => 'integer'
           ,'input_type_id'     => null
           ,'input_type_name'   => 'read-only'
           ,'unit_type_value'  => null
           ,'unit_type_values_pattern' => null
        );
        $view_tag = 'Name';
        $pattern = modApiFunc("Localization", "getPattern", 'string256');
        $product_type[$view_tag] = array(
            'id'                => null
           ,'pta_id'            => null
           ,'name'              => $obj->getMessage( new ActionMessage('PRD_NAME_NAME'))
           ,'descr'             => $obj->getMessage( new ActionMessage('PRD_NAME_DESCR'))
           ,'size'              => 70
           ,'min'               => 2 //: NOT USED?
           ,'max'               => 256
           ,'view_tag'          => $view_tag
           ,'group'             => array('id'=>1, 'name'=>'Key Product Details', 'sort'=>1) // @ hard-coded
           ,'required'          => true
           ,'visible'           => true
           ,'default'           => null
           ,'multilang'         => 'Y'
           ,'sort'              => -1
           ,'type'              => 'artificial'
           ,'allow_html'        => 0
           ,'patt_type'         => 'string256'
           ,'input_type_id'     => 1 //WHY null?
           ,'input_type_name'   => 'text'
           ,'unit_type_value'  => null
           ,'unit_type_values_pattern' => $pattern
           ,'patt'              => $pattern['patt_value']
        );

        // define values for attributes of the select type (input_type_values)
        $cache = &$this->_internal_cache['input_type_values'];
        foreach ($product_type as $attr_id => $attr)
        {
            // if it is not select, then skip it
//            if ($attr['input_type_name'] != 'select')
//            {
//              continue;
//            }
            $input_type_id = $attr['input_type_id'];
            // if this type hasn't been extracted from the DB yet, then make a query.
            if (!array_key_exists($input_type_id, $cache))
            {
                if ($input_type_id == 6)
                {
                    $input_type_result = modApiFunc("Taxes", "getProductTaxClasses");
                    $cache[$input_type_id] = $input_type_result;

//                    $request = new Request();
//                    $request->setView  ( 'ProductTaxClassSettings' );
//                    $request->setAction( 'AddFromCatalog' );
                                                                         //Commented by Girin according the task ASC-128
                    $product_type[$attr_id]['additional_link'] = "";     //$application->href($request);
                    $product_type[$attr_id]['additional_link_text'] = "";//$obj->getMessage( new ActionMessage('PRD_TAX_CLS_ADD_LINK'));
                }
                else if($input_type_id == CTLG_INPUT_TYPE_MANUFACTURER)
                {
                    $input_type_result = modApiFunc("Manufacturers", "getManufacturerProductAttributeValues", true, false, true);
                    $cache[$input_type_id] = $input_type_result;
                }
                else
                {
                    $result = execQuery('SELECT_ALL_INPUT_TYPE_VALUES',array());
                    if (is_array($result))
                        foreach($result as $k => $v)
                            $result[$k]['value'] = $this -> getInputTypeActualValue($v['value']);
                    if(!isset($cache[$input_type_id]))
                    {
                        $cache[$input_type_id] = array();
                    }
                    for($i=0; $i< sizeof($result); $i++)
                    {
                        if($result[$i]['it_id'] == $input_type_id)
                        {
                            //
                            $cache[$input_type_id][] = $result[$i];
                        }
                    }
                    $input_type_result = $cache[$input_type_id];
                }
            }
            else
            {
                // This type has already been called from the base. Take it from the table.
                $input_type_result = $cache[$input_type_id];
            }
            if (sizeof($input_type_result)<=0)
            {
                continue;
            }
            $product_type[$attr_id]['input_type_values'] = array();
            foreach ($input_type_result as $value)
            {
                $product_type[$attr_id]['input_type_values'][$value['id']] = $value['value'];
            }
        }
        return $product_type;
    }

    /**
     * Returns the product type list.
     */
    function getProductTypes()
    {
        $attr_result = execQuery('SELECT_PRODUCT_TYPE_LIST', array());

        $product_types = array();
        for ($j=0; $j<sizeof($attr_result); $j++)
        {
            #
            # Make GC Product type invisible
            #
            if ($attr_result[$j]['id'] == GC_PRODUCT_TYPE_ID)
                continue;
            ################################

            $type = array();
            $type['id'] = $attr_result[$j]['id'];
            $type['name'] = prepareHTMLDisplay($attr_result[$j]['name']);
            $type['description'] = prepareHTMLDisplay($attr_result[$j]['description']);
            $product_types[] = $type;
        }
        return $product_types;
    }

    /**
     * Returns the product type description.
     */
    function getProductType($product_type_id)
    {
        $result = execQuery('SELECT_PRODUCT_TYPE_BY_ID', array('product_type_id'=>($product_type_id==0? 1:$product_type_id)));

        if (sizeof($result) <= 0)
        {
            return null;
        }
        $product_type = array();
        $product_type['id'] = $result[0]['id'];
        $product_type['name'] = prepareHTMLDisplay($result[0]['name']);
        $product_type['description'] = prepareHTMLDisplay($result[0]['description']);
        $product_type['attr'] = $this->getProductTypeAttributes($product_type_id);

        return $product_type;
    }

    /**
     * Saves info about custom attribute to the temporary table.
     */
    function addTempCustomAttribute($form_id, $info)
    {
        // find a record in the temporary table for form_id
        $result = execQuery('SELECT_CATALOG_TEMP_BY_FORM_ID', array('form_id' => $form_id));

        if (sizeof($result) <= 0)
        {
            $arr = array();
            $arr[$info['view_tag']] = $info;
            execQuery('INSERT_CATALOG_TEMP_RECORD', array('value' => serialize($arr), 'form_id' => $form_id));
        }
        else
        {
            $arr = unserialize($result[0]['value']);
            // determine sort_order
            $sort_order = 0;
            foreach ($arr as $tag => $attr)
            {
                if ($arr[$tag]['sort'] > $sort_order)
                {
                    $sort_order = $arr[$tag]['sort'];
                }
            }
            $info['sort'] = $sort_order + 1;
            $arr[$info['view_tag']] = $info;
            execQuery('UPDATE_CATALOG_TEMP_RECORD', array('value' => serialize($arr), 'form_id' => $form_id));
        }
    }

    /**
     * Updates info about custom attribute in the temporary table.
     */
    function updateTempCustomAttribute($form_id, $info)
    {
        $attr = $this->getTempCustomAttributes($form_id);
        $attr[$info['view_tag']] = $info;

        execQuery('UPDATE_CATALOG_TEMP_RECORD', array('value' => serialize($attr), 'form_id' => $form_id));
    }

    /**
     * Deletes info about specified attribute from the temporary table.
     */
    function deleteTempCustomAttribute($form_id, $view_tag)
    {
        $attributes = $this->getTempCustomAttributes($form_id);
        unset($attributes[$view_tag]);

        execQuery('UPDATE_CATALOG_TEMP_RECORD', array('value' => serialize($attributes), 'form_id' => $form_id));
    }
    /**
     * Returns the custom attributes list, associated with the given form.
     */
    function getTempCustomAttributes($form_id)
    {
        // find a record in the temporary table for form_id
        $result = execQuery('SELECT_CATALOG_TEMP_BY_FORM_ID', array('form_id' => $form_id));

        if (sizeof($result) > 0)
        {
            return unserialize($result[0]['value']);
        }
        return null;
    }

    /**
     * Deletes all custom attributes for the given form.
     */
    function removeTempCustomAttributes($form_id)
    {
        execQuery('DELETE_CATALOG_TEMP_BY_FORM_ID', array('form_id' => $form_id));
        return null;
    }

    function updateCategory($cat_id, $cat_name, $cat_status, $description, $image_filename, $image_small_filename, $image_description, $page_title, $meta_keywords, $meta_description, $show_products_recursively, $seo_url_prefix)
    {
        global $application;

        $tables =  $this->getTables();

        $image_files_array = array('image_file' => $image_filename, 'image_small_file' => $image_small_filename);
        foreach ($image_files_array as $field_name => $image_name)
        {
            $file_name = $image_name;

            $params = array("field_name" => $field_name, "cat_id"=> $cat_id);

            $result = execQuery('SELECT_CATEGORY_FIELD', $params);

            if(!empty($result))
            {
                $old_image_filename = $result[0]['name'];

                //                                     -               .
                if($old_image_filename != $file_name)
                {
                    @unlink($application->getAppIni('PATH_IMAGES_DIR').$old_image_filename);
                    if(!empty($file_name))
                    {
                        //                        -                           .
                        // if the image has been uploaded, save it to the folder and to the base.
                        $image_info = $application->saveUploadImage("", $file_name);
                        $image_files_array[$field_name] = $file_name;
                    }
                }
            }
        }

        $params = array(
            "cat_id" => $cat_id,
            "cat_name" => $cat_name,
            "description" => $description,
            "image_files_array" => $image_files_array,
            "image_description" => $image_description,
            "page_title" => $page_title,
            "meta_keywords" => $meta_keywords,
            "meta_description" => $meta_description,
            "show_products_recursively" => $show_products_recursively,
            "seo_url" => $this->_prepareSEOURL($seo_url_prefix)
        );

        execQuery("UPDATE_CATEGORY",$params);

        $params = array(
            "cat_id" => $cat_id,
            "cat_status" => $cat_status
        );

        execQuery("UPDATE_CATEGORY_STATUS",$params);


        modApiFunc('EventsManager','throwEvent','CategoryInfoChanged', $cat_id);
    }



    function addCategory($parent_cat_id, $cat_name, $cat_status, $description, $image_filename, $image_small_filename, $image_description, $page_title, $meta_keywords, $meta_description, $show_products_recursively, $seo_url_prefix)
    {
        global $application;

        $tables =  $this->getTables();

        $CategoryInfo = $this->fetchCategoryInfo($parent_cat_id);

        if ($CategoryInfo == FALSE)
        {
            return $inserted_id = 1;
        }

        execQuery('UPDATE_CATEGORIES_STRUCTURE',$CategoryInfo);

        # Dump data for table 'attribute_groups'
        $params = array(
                'left'  => $CategoryInfo['right'],
                'right' => $CategoryInfo['right'] + 1,
                'level' => $CategoryInfo['level'] + 1,
                'cat_status' => $cat_status,
        );

        execQuery('INSERT_CATEGORY_TO_TREE',$params);

        $inserted_id = $application->db->DB_Insert_Id();

        $tmp_small_image_name = basename($image_small_filename);
        $tmp_large_image_name = basename($image_filename);
        $image_small_info = $application->saveUploadImage("", $tmp_small_image_name);
        $image_large_info = $application->saveUploadImage("", $tmp_large_image_name);

        $params = array(
            "inserted_id" => $inserted_id,
            "cat_name" => $cat_name,
            "description" => $description,
            "image_large_info" => $image_large_info,
            "image_small_info" => $image_small_info,
            "image_description" => $image_description,
            "page_title" => $page_title,
            "meta_keywords" => $meta_keywords,
            "meta_description" => $meta_description,
            "show_products_recursively" => $show_products_recursively,
            "seo_url" => $this->_prepareSEOURL($seo_url_prefix)
        );

        execQuery('INSERT_NEW_CATEGORY',$params);

        modApiFunc('EventsManager','throwEvent','CategoryAdded', $inserted_id);
        return $inserted_id;
    }

    /**
     * Sets up an option which means that product info is outputted while
     * deleting the product.
     *
     * @ deprecated. Remove the fucntion?
     */
    function setDisplayDeleteInfo()
    {
        $this->DisplayDeleteInfo = true;
    }

    /**
     * Gets a product info output mode.
     */
    function getDisplayDeleteInfo()
    {
        return $this->DisplayDeleteInfo;
    }

    /**
     * Checks if the product id is valid.
     *
     * @param integer $product_id
     * @return boolean
     * @ caching
     */
    function isCorrectProductId($product_id)
    {
        static $cache = array();
        if (isset($cache[$product_id]))
        {
            return $cache[$product_id];
        }

        // The id can't be a string or have a negative value.
        if (empty($product_id) || !is_numeric($product_id) || $product_id <= 0)
        {
            return false;
        }

        $params = array('product_id' => (int)$product_id);
        $result = execQuery('SELECT_COUNT_OF_PRODUCT_ID', $params);

        // There should be only one product with such an id.
        if ($result[0]['count_p_id'] != 1)
        {
            $r = false;
        }
        else
        {
            $r = true;
        }
        $cache[$product_id] = $r;
        return $r;
    }

    /**
     * Checks if the category id is valid.
     *
     * @param integer $category_id
     * @return boolean
     * @ caching
     */
    function isCorrectCategoryId($category_id)
    {
        //  The id can't be a string or have a negative value.
        if (empty($category_id) || !is_numeric($category_id) || $category_id <= 0)
        {
            return false;
        }
        $cids = execQuery('IS_CORRECT_CATEGORY_ID', array("category_id" =>$category_id ));

		if (count($cids) > 0)
			return true;
		 else
			return false;
    }

    function isCorrectManufacturerId($mnf_id)
    {
        global $application;

        //  The id can't be a string or have a negative value.
        if($mnf_id == MANUFACTURER_NOT_DEFINED)
        {
            return true;
        }

        if (empty($mnf_id) || !is_numeric($mnf_id) || $mnf_id <= 0)
        {
            return false;
        }
        $mnfs = modApiFunc("Manufacturers", "getManufacturersList");
        foreach($mnfs as $mnf_info)
        {
            if($mnf_info['manufacturer_id'] == $mnf_id)
               return true;
        }
        return false;
    }


    /**
     * Checks if the product type id is valid.
     *
     * @param integer $product_type_id
     * @return boolean
     * @ caching
     */
    function isCorrectProductTypeId($product_type_id)
    {
        //  The id can't be a string or have a negative value.
        if (empty($product_type_id) || !is_numeric($product_type_id) || $product_type_id <= 0)
        {
            return false;
        }

        $params = array('product_type_id'=>(int)$product_type_id);
        $result = execQuery('SELECT_COUNT_OF_PRODUCT_TYPE_ID', $params);

        // There should be only one product type with such an id.
        if ($result[0]['count_pt_id'] != 1)
        {
            return false;
        }
        return true;
    }

    /**
     * Returns current product type filter if it's present in session
     *
     * @return array
     */

    function getCurrentProductTypeFilter()
    {
        if (modApiFunc('Session', 'is_Set', 'CurrentProductTypeFilter'))
        {
            return unserialize(modApiFunc('Session', 'get', 'CurrentProductTypeFilter'));
        }
        return array();
    }

    function setCurrentProductTypeFilter($filter)
    {
        if (!is_array($filter))
        {
            $filter = array($filter);
        }
        modApiFunc('Session', 'set', 'CurrentProductTypeFilter', serialize($filter));
    }

    /**
     * Returns the value "Product Type" by default. It is used, for example,
     * at first outputting "Product Type", when the user himself hasn't selected
     * the type of the added product, but it is necesary to display the input
     * field.
     *
     * @return integer
     */
    function getDefaultProductTypeID()
    {
        return 0;
    }

    /**
     * Returns the value Home "Root category".
     *
     * @return integer
     */
    function getHomeCategoryID()
    {
        return 1;
    }

    /**
     * Gets a product quantity depending on the type.
     *
     * @return bool true if a product quantity is less than the one specified
     *              in the license key, false otherwise
     */
    function getProductsQuantityByType($typeId)
    {
        $result = execQuery('SELECT_PRODUCT_COUNT_BY_PRODUCT_TYPE', array('pt_id' => $typeId));
        $prods_number = $result[0]['count_p_id'];
        return $prods_number;
    }

    /**
     * Gets a product quantity in each of the category.
     *
     * @return
     */
    function getProductsQuantityByCategories()
    {
        return execQuery('SELECT_PRODUCT_COUNT_BY_CATEGORY', array());
    }


    /**
     * Deletes the product type.
     *
     * @author Alexandr Girin
     * @
     * @param integer $typeId - product type ID
     * @return
     */
    function deleteProductType($typeId)
    {
        execQuery('DELETE_PRODUCT_TYPE_ATTRIBUTES_BY_PRODUCT_TYPE', array('pt_id' => $typeId));
        execQuery('DELETE_PRODUCT_TYPE_BY_ID', array('pt_id' => $typeId));

        modApiFunc('EventsManager','throwEvent','ProductTypeDeleted', $typeId);
    }

    function getImagesDir()
    {
        global $application;
        return $application->getAppIni("PATH_IMAGES_DIR");
    }

    /**
     * Checks if it is possible to write to the image folder.
     *
     * @author Vadim Lyalikov
     * @
     * @return
     */
    function isImageFolderNotWritable()
    {
        global $application;
        $dir_fs_name = $application->getAppIni("PATH_IMAGES_DIR");
        return !is_dir_writable($dir_fs_name);
    }

    /**
     * Changes the product attribute "TaxClass" for "Non-Taxable" when deleting
     * TaxClass on the tax setting page.
     */
    function changeAttributeValueForAllProducts($attr_id, $attr_old_value, $attr_new_value)
    {
        execQuery('UPDATE_PRODUCT_ATTRIBUTE_VALUE_BY_OLD_VALUE', array('aid' => $attr_id, 'old_value' => $attr_old_value, 'value' => $attr_new_value));

        modApiFunc('EventsManager','throwEvent','ProductAttributeValueChangedForAllProducts', array('ATTRIBUTE_ID'=>$attr_id,
                                                                                                    'ATTRIBUTE_OLD_VALUE'=>$attr_old_value,
                                                                                                    'ATTRIBUTE_NEW_VALUE'=>$attr_new_value));
    }

    /**
     * Updates the product quantity in the stock after the order confirmation.
     */
    function updateProductsQuantity($products, $mult)
    {
        $_affected_products = array();
        foreach ($products as $prodInfo)
        {
            //: the attribute is used by the id, it needs to be rewritten
            //check if the attribute of the QuantityInStock product is visible
            if (!modApiFunc("Catalog", "isProductAttributeVisible", $prodInfo['storeProductID'], 3))
            {
                continue;
            }

            $prodObj = new CProductInfo($prodInfo['storeProductID']);
            if($prodObj->_fProductIDIsIncorrect === false)
            {
                //                                    ,      QuantityInStock
                $productQuantityInStock = $prodObj->getProductTagValue('QuantityInStock', PRODUCTINFO_NOT_LOCALIZED_DATA);
                if (!is_numeric($productQuantityInStock))
                {
                    /*
                                              ,                   ,                      ,
                                      QuantityInStock,  . .                    .
                                                     .
                     */
                    continue;
                }

                    execQuery('UPDATE_PRODUCT_QUANTITY_ATTRIBUTE', array('mult' => $mult, 'qty' => $prodInfo['qty'], 'p_id' => $prodInfo['storeProductID']));

                $_affected_products[] = $prodInfo['storeProductID'];

                $cats_ids = $prodObj->getCategoriesIDs();
                foreach($cats_ids as $category_id)
                {
                    modAPIFunc('paginator', 'resetPaginator', "Catalog_ProdsList_".$category_id);
                };
            }
        }
        modApiFunc('EventsManager','throwEvent','ProductQuantityChanged', $_affected_products);
    }

    /**
     * Checks if the product attribute is visible.
     *
     * @param integer $pid - the product id
     * @param integer $aid - the attribute id
     * @return boolean
     */
    function isProductAttributeVisible($pid, $aid)
    {
        $result = execQuery('SELECT_PRODUCT_TYPE_ATTRIBUTE_VISIBLE_VALUE', array('aid' => $aid, 'pid' => $pid));

        if (sizeof($result) && $result[0]['visibility'] == '1')
        {
            return true;
        }
        return;
    }

    function getProductTagValuesHash($product_id)
    {

        $ProductObj = new CProductInfo($product_id);
        $tagsHash = $ProductObj->getProductTagValuesHash(PRODUCTINFO_NOT_LOCALIZED_DATA,
                                                                 PRODUCTINFO_LONG_TAG_NAMES);

        return $tagsHash;
    }

   /**
    * Gets base product info
    */
   function getBaseProductInfo($prod_id, $attr_name = 'ALL')
   {
       $result = execQuery('SELECT_BASE_PRODUCT_INFO', array('product_id' => $prod_id));
       $result = @$result[0];

       if ($attr_name == 'ALL')
           return $result;

       return @$result[$attr_name];
   }

    /*
     * Deletes product links from all categories
     */
    function delProductLinksFromAllCategories($products_ids)
    {
        if(!is_array($products_ids) or empty($products_ids))
            return false;

        foreach($products_ids as $prod_id)
        {
            $cats = execQuery('SELECT_ALL_PRODUCT_CATEGORIES',
                              array('product_id' => $prod_id));
            if (is_array($cats))
                foreach($cats as $cid)
                {
                    execQuery('DELETE_PRODUCTS_TO_CATEGORIES_BY_PRODUCT_IDS',
                              array('cids' => array($cid['category_id']),
                                    'pids' => array($prod_id)));

                    modApiFunc('EventsManager', 'throwEvent',
                               'ProductLinksToCategoryDeleted',
                               array($prod_id), $cid['category_id']);
                }
        }

        return true;
    }

    /**
     *
     */
    function addProductLinkToCategory($product_id,$category_id)
    {
        global $application;

        execQuery('INSERT_PRODUCT_TO_CATEGORIES_RECORD', array('pid' => $product_id, 'cid' => $category_id));
        $record_id = $application->db->DB_Insert_Id();

        execQuery('UPDATE_PRODUCTS_TO_CATEGORIES_SORT_ORDER', array('sort_order' => $record_id, 'record_id' => $record_id));

        modApiFunc('EventsManager','throwEvent','ProductLinkToCategoryAdded', $product_id, $category_id);

        return $record_id;
    }

    /**
     *
     */
    function delProductLinksFromCategory($category_id,$products_ids)
    {
        if(!is_array($products_ids) or empty($products_ids))
            return false;

        execQuery('DELETE_PRODUCTS_TO_CATEGORIES_BY_PRODUCT_IDS', array('cids' => array($category_id), 'pids' => $products_ids));

        modApiFunc('EventsManager','throwEvent','ProductLinksToCategoryDeleted', $products_ids, $category_id);
        return true;
    }

    /**
     *
     */
    function delProductLinksFromCategories($product_id,$categories_ids)
    {
        if(!is_array($categories_ids) or empty($categories_ids))
            return false;

        execQuery('DELETE_PRODUCTS_TO_CATEGORIES_BY_PRODUCT_IDS', array('cids' => $categories_ids, 'pids' => array($product_id)));

        modApiFunc('EventsManager','throwEvent','ProductLinkToCategoriesDeleted', $product_id, $categories_ids);
        return true;
    }

    /**
     *
     *
     *                 ,
     *
     * @param string $by_id = enum('product_id','category_id')
     * @param array $ids
     */
    function delAllProductLinks($by_id,$ids)
    {
        if(!is_array($ids) or empty($ids))
            return false;
        if(!in_array($by_id,array('product_id','category_id')))
            return false;

        if($by_id == 'category_id')
        {
            execQuery('DELETE_PRODUCTS_TO_CATEGORIES_BY_PRODUCT_IDS', array('cids' => $ids));
            modApiFunc('EventsManager','throwEvent','AllCategoryLinksDeleted', $ids);
        }
        else
        {
            execQuery('DELETE_PRODUCTS_TO_CATEGORIES_BY_PRODUCT_IDS', array('pids' => $ids));
            modApiFunc('EventsManager','throwEvent','AllProductLinksDeleted', $ids);
        }
        return true;
    }

    function doesProductHaveLinks($product_id)
    {
        $res = execQuery('SELECT_COUNT_OF_PRODUCT_LINKS', array('pid' => $product_id));
        return ($res[0]['count_rids'] > 0);
    }

    function getRealProductsCount()
    {
        $res = execQuery('SELECT_COUNT_OF_UNIQUE_PRODUCTS', array());
        return $res[0]['product_count'];
    }

    function getCategoriesCount()
    {
        $res = execQuery('SELECT_COUNT_OF_CATEGORIES', array());
        return $res[0]['category_count'];
    }

    function getCategoryFullPathAsCategoriesIDs($category_id,$include_self=true)
    {
        $cats = $this->getCategoryFullPath($category_id);

        if($include_self)
            $retarr = array($category_id);
        else
            $retarr = array();

        for($i=0;$i<count($cats);$i++)
        {
            if($cats[$i]["id"]!=$category_id)
                array_unshift($retarr,$cats[$i]["id"]);
        };

        return $retarr;
    }

    function _prepareSEOURL($seo_string)
    {
        return preg_replace("/[^a-z0-9_\-]/i","-",$seo_string);
    }

    /**
     *                          ,                                 ,
     * (                         ),                                     ,                     .
     */
    function computePriceExcludingTaxes($price_including_taxes, $tax_class_id, $b_force_AZ = false, $quantity=1, $prod_id=0)
    {
        //  AZ          :
        //FIMXE:                   Tax Calculator
        if (modApiFunc('Users', 'getZone') == "AdminZone" &&
            $b_force_AZ === false)
        {
            return $price_including_taxes;
        }

        //:          ,                 TaxClass               :                       .
        $products = array();
        $products[] = array("ID" => NULL,//:              : NULL                           ,
                   //                                                       ,             .
                   // OrderLevelDiscount                                    .
                   //                           (2008 jun 03)               -              .
                            "CartItemSalePrice" => $price_including_taxes,
                            "CartItemSalePriceExcludingTaxes" => NULL, //
                            "Quantity_In_Cart" => 1,
                            "ShippingPrice" => 0.0,
                            "TaxClass" => $tax_class_id
                            );

        $country_id = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_COUNTRY);
        $state = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_STATE);
        //            ,                                       ,         ,    ProductInfo,
        //                      .
        //                             -                                       .
        //                          $price_including_taxes
        if(!is_numeric($country_id) ||
           ($country_id < 1) ||
           !is_numeric($state) ||
           ($state < 1))
        {
            ////                :
            //_fatal(array( "CODE" => "CORE_057"), __CLASS__, __FUNCTION__);
            if(!is_numeric($country_id))
            {
                $country_id = NULL;
            }
            if(!is_numeric($state))
            {
                $state = NULL;
            }
            //             ,                      ,                          ,
            //                                      . :
            //                       -             .

            $addressesArray = array
            (
                 "Default"  => array(
                                     "CountryId" => $country_id,
                                     "StateId" => $state
                                    ),
                 "Shipping" => array(
                                     "CountryId" => $country_id,
                                     "StateId" => $state
                                    ),
                 "Billing"  => array(
                                     "CountryId" => $country_id,
                                     "StateId" => $state
                                    ),
                 "StoreOwner" => array(
                                     "CountryId" => $country_id,
                                     "StateId" => $state
                                    )
            );
        }
        else
        {
            $addressesArray = array
            (
                 "Default"  => array(
                                     "CountryId" => $country_id,
                                     "StateId" => $state
                                    ),
                 "Shipping" => array(
                                     "CountryId" => $country_id,
                                     "StateId" => $state
                                    ),
                 "Billing"  => array(
                                     "CountryId" => $country_id,
                                     "StateId" => $state
                                    ),
                 "StoreOwner" => array(
                                     "CountryId" => $country_id,
                                     "StateId" => $state
                                    )
            );
        }

        $sm_uuid = modApiFunc("Checkout", "getAllInactiveModuleId", "shipping");
        modApiFunc('Taxes',
                   'setTaxDebug',
                   $products,
                   0.0,
                   $sm_uuid,
                   PRICE_N_A, //: .              : PRICE_N_A                           ,
                   //                                                       ,             .
                   // OrderLevelDiscount                                    .
                   //                           (2008 jun 03)               -              .
                   $addressesArray
                  );

        $TaxAmounts = modApiFunc("Taxes", "getTax", true, true, false, true); //included only, debug, not trace, symbolic

        if($TaxAmounts == "fatal")
        {
            //                                   -                   .
            //                       .         $price_including_taxes
            return  $price_including_taxes;
        }
        //                                                :
        //  T1 = k1 x + b1
        //  T2 = k2 x + b2
        //  T3 = k3 x + b3
        // ,    x -                          .
        // . .
        // price_including_taxes = x + T1 + T2 + T3
        // =>                     _  _       _        x:
        // x = (price_including_taxes - (b1 + b2 + b3)) / (1 + k1 + k2 + k3)
        // sum_b = b1 + b2 + b3
        // sum_k = k1 + k2 + k3
        $sum_k = 0.0;
        $sum_b = 0.0;

        $TaxNames = modApiFunc("Taxes", "getTaxNames");

        if (empty($TaxAmounts['products']))
        {
            //                   .
            //                      .         $price_including_taxes
            return  $price_including_taxes;
        }

        $membership = modApiFunc('Customer_Account','getCurrentSignedCustomerGroupID');
        foreach ($TaxAmounts['products'][1] as $tax_id => $tax_amount)
        {
            //                        .                      .
            if(!is_numeric($tax_id))
                continue;

            //                        -                          ,
            //                              .
            //               $price_including_taxes

            //          -               PRICE_N_A -                 ,           .

            //          -                                          SalePrice -
            //                              ,
            //               $price_including_taxes

            //           "            " -
            if($TaxNames[$tax_id]["included_into_price"] == "false")
                continue;

            if (is_array($tax_amount))
            {
                //                                                            .
                if(array_key_exists("k", $tax_amount) &&
                   array_key_exists("b", $tax_amount))
                {
                    $sum_k += $tax_amount["k"];
                    $sum_b += $tax_amount["b"];
                }
                else
                {
                    continue;
                }
            }
            elseif ($tax_amount == TAX_NOT_LINEAR)
            {
                //_fatal(array( "CODE" => "CORE_058"), __CLASS__, __FUNCTION__);
                //:
                ////: report error,   CZ                      .                     .
                //                                              /
                //                          .
                return  $price_including_taxes;
            }
            elseif ($tax_amount == PRICE_N_A)
            {
                //                                  .  . .         b = 0, k = 0.
                //                  ,                                      .
                continue;
            }
            else
            {
                //                          .                                .
                continue;
            }
        }

        $price_including_taxes = modApiFunc('Quantity_Discounts', 'getFixedPrice', $prod_id, $quantity, $price_including_taxes, $membership);
        $price_excluding_taxes = ($price_including_taxes - $sum_b) / (1 + $sum_k);
        return $price_excluding_taxes;
    }

    function purgeProductManufacturers($mnf_ids)
    {
        execQuery('UPDATE_PURGE_PRODUCT_MANUFACTURERS', $mnf_ids);
    }

    /**
     * Saves full categories structure received from the page 'Manage Categories'
     * after clicking the 'Save Changes' button.
     * @param $ctg_list Plain-text list of categories. Rows separated by \n, fields in each row by \t.
     * @return none
     */
    function saveFullCategoriesStructure($ctg_list)
    {
        $old_ctgs = $this->getSubcategoriesFullListWithParent($this->getHomeCategoryID(), false);
        $old_tree = $this->convertCategoriesListToTree($old_ctgs);
        $old_indx = $this->makeIndexedCategoriesList($old_tree);

        $new_ctgs = $this->convertCategoriesPlainListIntoArray($ctg_list);
        $new_tree = $this->convertCategoriesListToTree($new_ctgs);
        $this->fillKeysInCategoriesTree($new_tree);
        $new_indx = $this->makeIndexedCategoriesList($new_tree);

        $this->prepareNodesLists($old_indx, $new_indx, $to_upd, $to_add, $to_del);
        $this->appendNodesWithoutId($new_tree, $to_add);

        $events_cnt = 0;
        $events_cnt += $this->deleteExistingCategories($to_del);
        $events_cnt += $this->updateExistingCategories($to_upd);
        $events_cnt += $this->createNewCategories($to_add);
        if ($events_cnt > 0) {
            modApiFunc('EventsManager', 'throwEvent', 'CategoryInfoChanged', 1);
        }
    }

// Additional query functions

    function selectCategoryProducts($cat_id)
    {
        return execQuery('SELECT_CATEGORY_PRODUCTS', array('cat_id' => $cat_id));
    }

    function selectSortedCategoryProducts($cat_id)
    {
        return execQuery('SELECT_SORTED_CATEGORY_PRODUCTS', array('cat_id' => $cat_id));
    }

    function selectProductSearchResultPg($params, $pg_enable)
    {
        if ($pg_enable == PAGINATOR_ENABLE)
            $params['use_paginator'] = true;

        return execQueryPaginator('SELECT_PRODUCT_SEARCH_RESULTS', $params);
    }

    function selectProductSearchResult($params)
    {
        return execQuery('SELECT_PRODUCT_SEARCH_RESULTS', $params);
    }

    function selectProductSearchPattern($search_id)
    {
        return execQuery('SELECT_PRODUCT_SEARCH_PATTERN', array('search_id' => $search_id));
    }

    function selectOldProductSearchRecords($time_stamp)
    {
        return execQuery('SELECT_OLD_PRODUCT_SEARCH_RECORDS', array('time_stamp' => $time_stamp));
    }

    function deleteOldProductSearchRecords($search_ids)
    {
        execQuery('DELETE_OLD_PRODUCT_SEARCH_RECORDS', array('search_ids' => $search_ids));
        execQuery('DELETE_OLD_PRODUCT_SEARCH_RESULT_RECORDS', array('search_ids' => $search_ids));
    }

    function insertProductSearchRecord($pattern, $words)
    {
        execQuery('INSERT_PRODUCT_SEARCH_RECORD', array('pattern' => $pattern, 'words' => $words));
    }

    function insertProductSearchResultRecord($search_id, $pid, $relevance)
    {
        execQuery('INSERT_PRODUCT_SEARCH_RESULT_RECORD', array('search_id' => $search_id, 'pid' => $pid, 'relevance' => $relevance));
    }

    function updateProductSearchTime($search_id)
    {
        execQuery('UPDATE_PRODUCT_SEARCH_TIME', array('search_id' => $search_id));
    }

    function insertNewProduct($pt_id, $name)
    {
        execQuery('INSERT_NEW_PRODUCT', array('p_type_id' => $pt_id, 'p_name' => $name));
    }

    function insertNewProductAttribute($p_id, $a_id, $pa_value)
    {
        execQuery('INSERT_NEW_PRODUCT_ATTRIBUTE', array('p_id' => $p_id, 'a_id' => $a_id, 'pa_value' => $pa_value));
    }

    function insertNewProductImage($pa_id, $name, $width, $height)
    {
        execQuery('INSERT_NEW_PRODUCT_IMAGE', array('pa_id' => $pa_id, 'name' => $name, 'width' => $width, 'height' => $height));
    }

    /**
     * Gets if an attribute is multilangual
     * @param is either attribute_id or view_tag name
     */
    function isMLAttribute($attr)
    {
        if (!$attr)
            return false;

        if (in_array($attr, $this -> MultiLangAttributes)
            || isset($this -> MultiLangAttributes[$attr]))
            return true;

        return false;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Converts plain-text list received from browser into PHP array.
     * @return array
     */
    function convertCategoriesPlainListIntoArray($ctg_list)
    {
        $rows = explode("\n", trim($ctg_list));
        $ctgs = array();
        foreach($rows as $row) {
            $fields = explode("\t", trim($row), 3);
            $ctgs[] = array(
                    'id' => $fields[0],
                    'level' => $fields[1],
                    'name' => trim($fields[2]),
            );
        }
        return $ctgs;
    }

    /**
     * Converts plain array of categories into tree.
     * @param $cats array Array of categories
     * @return array Root node of the tree
     */
    function convertCategoriesListToTree($cats)
    {
        if (! is_array($cats)) {
            return array();
        }
        $pseudo_cat = array('id' => -1, 'name' => '?');
        $root_parent = null;
        $root_cat = $pseudo_cat;
        $root_node = $this->createCatNode($root_cat, $root_parent);
        $prev_level = -1;
        $prev_node = &$root_node;
        for ($i=0, $n=sizeof($cats); $i<$n; $i++) {
            $c = &$cats[$i];
            $this_level = (int) $c['level'];
            if ($this_level == $prev_level) { // stay same level
                if ($prev_node->parent) {
                    $prev_node = &$this->addCatNode($c, $prev_node->parent);
                }
            }
            elseif ($this_level > $prev_level) { // descend to next level
                $prev_node = &$this->addCatNode($c, $prev_node);
                $prev_level = $this_level;
            }
            elseif ($this_level < $prev_level) { // rise to prev level
                $k = $prev_level - $this_level;
                for ($j = 0; $j < $k; $j++) {
                    $prev_node = & $prev_node->parent;
                }
                $prev_node = &$this->addCatNode($c, $prev_node->parent);
                $prev_level = $this_level;
            }
        }

        //debug filling keys:
        //$this->fillKeysInCategoriesTree($root_node->children[0]);
        return $root_node->children[0];
    }

    /**
     * Creates category node for the tree.
     * @param $cat array Category's fields
     * @param $parent_node object Reference to a parent node
     * @return object Category node
     */
    function createCatNode($cat, &$parent_node)
    {
        return (object) array(
                'id'        => $cat['id'],
                'level'     => @$cat['level'],
                'left'      => @$cat['c_left'],
                'right'     => @$cat['c_right'],
                'name'      => $cat['name'],
                'children'  => array(),
                'parent'    => &$parent_node,
        );
    }

    /**
     * Creates node object and adds category node into parent's children list.
     * @param $cat array Category node
     * @param $parent_node object Reference to a parent node
     * @return object Node created
     */
    function &addCatNode($cat, &$parent_node) {
        $new_node = $this->createCatNode($cat, $parent_node);
        $parent_node->children[] = & $new_node;
        return $new_node;
    }

    /**
     * Fills left and right keys according the Nested Set rules.
     * @param $root_node object Reference to a starting node
     * @param $key integer Initial key value
     * @return integer Resulting key value (right + 1)
     */
    function fillKeysInCategoriesTree(&$root_node, $key = 1, $level = 0)
    {
        $root_node->level = $level;
        $root_node->left = $key ++;
        if(sizeof($root_node->children) > 0) {
            foreach(array_keys($root_node->children) as $i) {
                $key = $this->fillKeysInCategoriesTree($root_node->children[$i], $key, $level+1);
            }
        }
        $root_node->right = $key ++;
        //debug filling keys:
        //$root_node->name = $root_node->name .' ('.$root_node->left.') ('.$root_node->right.')';
        return $key;
    }

    /**
     * Makes recursive tree index.
     * @param $root_node Starting node for indexing
     * @return array Tree index
     */
    function makeIndexedCategoriesList(&$root_node)
    {
        $indx = array();
        $this->fillCategoryIndex($root_node, $indx);
        return $indx;
    }

    /**
     * Fills categories tree index.
     * @param $root_node object Starting node for indexing
     * @param $indx array Resulting index
     * @return none
     */
    function fillCategoryIndex(&$root_node, &$indx)
    {
        if (is_numeric($root_node->id)) {
            // add node only when it has correct id
            $indx[ $root_node->id ] = &$root_node;
        }
        if (sizeof($root_node->children) > 0) {
            foreach(array_keys($root_node->children) as $i) {
                $this->fillCategoryIndex($root_node->children[$i], $indx);
            }
        }
    }


    /**
     * Compares the indexed lists of old & new categories and separates them into lists
     * of categories to delete, to update and to create.
     * @param $old_indx array Indexed list of old (existing in DB) categories
     * @param $new_indx array Indexed list of new (submitted by user) categories
     * @param $to_upd array Output list of categories to update
     * @param $to_add array Output list of categories to create
     * @param $to_del array Output list of categories to delete
     */
    function prepareNodesLists(&$old_indx, &$new_indx, &$to_upd, &$to_add, &$to_del)
    {
        foreach(array_keys($old_indx) as $o_id) {
            if (! isset($new_indx[$o_id])) {
                $to_del[] = &$old_indx[$o_id];
            }
        }
        foreach(array_keys($new_indx) as $n_id) {
            if (isset($old_indx[$n_id])) {
                if (! $this->isCategoriesEqual($old_indx[$n_id], $new_indx[$n_id])) {
                    $to_upd[] = &$new_indx[$n_id];
                }
            }
            else {
                $to_add[] = &$new_indx[$n_id];
            }
        }
    }

    /**
     * Compare two versions of the category node in order that find out the category need update.
     * @param $old_node
     * @param $new_node
     * @return boolean
     */
    function isCategoriesEqual(&$old_node, &$new_node)
    {
        return
            $old_node->level == $new_node->level &&
            $old_node->left == $new_node->left &&
            $old_node->right == $new_node->right &&
            ($old_node->name == $new_node->name || trim($new_node->name) == '');
    }

    /**
     * Walk through the tree and collect all categories nodes without ids.
     * @param $new_tree object Starting node
     * @param $to_add array Resulting nodes array
     * @return none
     */
    function appendNodesWithoutId(&$new_tree, &$to_add)
    {
        if (! is_numeric($new_tree->id)) {
            $to_add[] = &$new_tree;
        }
        if (sizeof($new_tree->children) > 0) {
            foreach(array_keys($new_tree->children) as $i) {
                $this->appendNodesWithoutId($new_tree->children[$i], $to_add);
            }
        }
    }

    /**
     * Update existing categories have been changed by user (even if neighboor categories have been moved).
     * @param $to_upd array List of categories nodes to update
     * @return integer
     * @see updateExistingCategory()
     */
    function updateExistingCategories(&$to_upd)
    {
        if (sizeof($to_upd) == 0) {
            return 0;
        }

        $ids = array();
        foreach(array_keys($to_upd) as $i) {
            $ids[] = $this->updateExistingCategory($to_upd[$i]);
        }
        return sizeof($to_upd);
    }

    /**
     * Update single category. Keys of neighboor nodes are not corrected.
     * @param $ctg_node object Category node to update
     * @return integer ID of the category has been updated.
     * @see updateExistingCategories()
     */
    function updateExistingCategory(&$ctg_node)
    {
        global $application;

        $tables =  $this->getTables();

        // update structure record
        $params = array(
            'left' => $ctg_node->left,
            'right' => $ctg_node->right,
            'level' => $ctg_node->level,
            'id' => $ctg_node->id
        );

        execQuery('UPDATE_EXISTING_CATEGORY_TREE',$params);

        // update description record
        execQuery('UPDATE_CATEGORY_DESCR', array('name' => $ctg_node->name, 'id' => $ctg_node->id));

        return $ctg_node->id;
    }

    /**
     * Create new categories
     * @param $to_add array List of categories node to add
     * @return integer Count of nodes have been created.
     */
    function createNewCategories(&$to_add)
    {
        if (sizeof($to_add) == 0) {
            return 0;
        }

        $ids = array();
        foreach(array_keys($to_add) as $i) {
            $ids[] = $this->createNewCategory($to_add[$i]);
        }
        return sizeof($to_add);
    }

    /**
     * Create single category node. Keys of neighboor nodes are not corrected.
     * @param $new_node object Node to create
     * @return integer ID of node has been created
     */
    function createNewCategory(&$new_node)
    {
        global $application;

        $tables =  $this->getTables();

        // create structure record
        $params = array(
            'left'  => $new_node->left,
            'right' => $new_node->right,
            'level' => $new_node->level,
        );

        execQuery('INSERT_CATEGORY_TO_TREE', $params);

        $inserted_id = $application->db->DB_Insert_Id();

        // create description record
        $params = array(
            'inserted_id' => $inserted_id,
            'cat_name' => $new_node->name,
            'recursion' => CATEGORY_DONOTSHOW_PRODUCTS_RECURSIVELY
        );

        execQuery('INSERT_CATEGORY_DESCR', $params);

        return $inserted_id;
    }

    /**
     * Destroy arbitrary categories without a correction of the Nested Set key fields.
     * @param $to_del array List of categories nodes to delete
     * @return integer Deleted categories count
     */
    function deleteExistingCategories(&$to_del)
    {
        global $application;

        if (sizeof($to_del) == 0) {
            return 0;
        }

        $ids = array();
        foreach(array_keys($to_del) as $i) {
            $ids[] = $to_del[$i]->id;
        }

        // delete categories' products
        $result = execQuery('SELECT_CATEGORIES_PRODUCTS', array('cats_ids' => $ids));
        $pids = array();
        foreach ($result as $row) {
            $pids[ $row['id'] ] = true;
        }
        $result = execQuery('SELECT_LINKED_OTHER_CATEGORIES_PRODUCTS', array('pids' => array_keys($pids), 'cats_ids' => $ids));

        foreach ($result as $row) {
            unset($pids[ $row['id'] ]);
        }

        if (! empty($pids)) {
            modApiFunc('Catalog', 'deleteProductsArray', array_keys($pids));
        }

        // delete categories images
        $result = execQuery('SELECT_CATEGORY_IMAGES', array('ids' => $ids));

        foreach ($result as $images)
        {
            @unlink($application->getAppIni('PATH_IMAGES_DIR').$images['image_large']);
            @unlink($application->getAppIni('PATH_IMAGES_DIR').$images['image_small']);
        }

        // delete structure record
        execQuery('DELETE_CATEGORIES_FROM_TREE', array('ids'=>$ids));

        // delete description records
        execQuery('DELETE_CATEGORY_DESCR_BY_CATEGORY_IDS', array('cids'=>$ids));

        // delete products links
        $this->delAllProductLinks('category_id', $ids);

        return sizeof($to_del);
    }

    function initMLAttributes()
    {
        $this -> MultiLangAttributes = array();

        $res = execQuery('SELECT_MULTILANG_ATTRIBUTES', array());
        if (is_array($res))
            foreach($res as $v)
                $this -> MultiLangAttributes[$v['id']] = $v['view_tag'];
    }

    function getAllMLAttributeIDs()
    {
        $result = array();
        foreach($this -> MultiLangAttributes as $k => $v)
            $result[] = $k;

        return $result;
    }

    function imagesPresent()
    {
        $res = execQuery('SELECT_PRODUCT_IMAGES_NUMBER', array());

        if ($res[0]['NUM'] === "0")
            return false;
        else
            return true;
    }

    function checkCatalogTree()
    {
        global $application;

        $result = array();
        $left = array();
        $right = array();
        $level = array();
        $maxnum = 0;
        $error = false;

        $ct = $this -> getTables();
        $ct = $ct['categories']['columns'];
        $_query = 'SELECT ' . $ct['id'] . ' AS catid, ' .
                  $ct['left'] . ' AS catleft, ' .
                  $ct['right'] . ' AS catright, ' .
                  $ct['level'] . ' AS catlevel FROM ' .
                  $application -> getAppIni('DB_TABLE_PREFIX') .
                  'categories ORDER BY ' . $ct['left'];

        $data = $application -> db -> DB_Query($_query, 'db_link', false);
        if ($data !== false)
            while($row = mysqli_fetch_assoc($data))
            {
                $result[] = $row;
                // left array: catleft -> id
                $left[$row['catleft']] = $row['catid'];
                // right array: id -> catleft
                $right[$row['catid']] = $row['catright'];
                // level array: id -> level
                $level[$row['catid']] = $row['catlevel'];
                $maxnum += 2;
            }

        // checking the global integrity
        if (@$left[1] != 1 || @$right[1] != $maxnum || @$level[1] != 0)
            $error = true;

        // walking throuth the tree...
        if (!$error)
        {
            $_subtree = array();
            $_subtree[0] = 1;
            $_node = 1;
            $_level = 0;

            while($_node < $maxnum)
            {
                $_node++;
                if ($_level < 0)
                {
                    $error = true;
                    break;
                }
                if ($right[$_subtree[$_level]] == $_node)
                {
                    // we have a leave here...
                    $_level--;
                }
                elseif ($right[$_subtree[$_level]] > $_node)
                {
                    // we have a subtree here...
                    $_level++;
                    if (!isset($left[$_node]))
                    {
                        // broken tree
                        $error = true;
                        break;
                    }
                    $_subtree[$_level] = $left[$_node];
                    if ($level[$_subtree[$_level]] != $_level)
                    {
                        // incorrect level
                        $error = true;
                        break;
                    }
                }
                else
                {
                    // we have an error
                    $error = true;
                    break;
                }
            }

            if ($_level != -1)
            {
                // we have an error here
                $error = true;
            }
        }

        // generating an error for empty tree
        if (empty($result))
            $result[] = array('catid' => 'The', 'catleft' => 'tree',
                              'catright' => 'is', 'catlevel' => 'empty');

        if (!$error)
            return array();

        return $result;
    }

    function removeGCProduct($product_id)
    {
        if (empty($product_id))
            return false;

        $product = modApiFunc("Catalog","getProductInfo",$product_id);
        if ($product['TypeID'] === GC_PRODUCT_TYPE_ID)
        {
            modApiFunc("Catalog","deleteProductsArray",array($product_id));
        }
    }

    function updateMembershipVisibilityAttr($params)
    {
        if(is_array($params))
        {
            $attribute_id = modApiFunc('Catalog', 'getMembershipVisibilityAttrId');
            foreach($params as $name=>$value)
            {
                switch($name)
                {
                    case 'delete_group':
                        $res = execQuery('SELECT_PRODUCT_ATTRIBUTES_BY_ATTRIBUTE_ID', array(
                                        'attribute_id' => $attribute_id
                                      ));
                        $groups = modApiFunc('Customer_Account', 'getGroups', 'exclude unsigned');
                        foreach($res as $pa)
                        {
                            if($pa['attr_value']=='-1') continue;
                            $attr_groups = explode('|',$pa['attr_value']);
                            foreach($attr_groups as $i=>$attr_gr)
                                if(!in_array($attr_gr, array_keys($groups))) unset($attr_groups[$i]);
                            $new_attr_value = implode('|', $attr_groups);
                            if($pa['attr_value'] != $new_attr_value)
                            {
                                execQuery('UPDATE_PRODUCT_ATTRIBUTE_VALUE',
                                            array(
                                                   'pid' => $pa['pid'],
                                                   'aid' => $attribute_id,
                                                   'value' => $new_attr_value
                                                ));
                            }
                        }
                    break;
                }
            }
        }
    }

    function getManufacturerAttrId()
    {
        global $application;
        if ($application->db->DB_isTableExists($application->getAppIni('DB_TABLE_PREFIX').'attributes'))
        {
            $attr_id = execQuery('SELECT_PRODUCT_ATTRIBUTE_ID_BY_NAME_AND_TAG', array(
                'name' => 'PRD_MANUFACTURER_NAME', 'view_tag' => 'Manufacturer',
            ));
            return $attr_id[0]['attribute_id'];
        }
        return MANUFACTURER_PRODUCT_ATTRIBUTE_ID;
    }

    function getCustomerReviewsAttrId()
    {
        global $application;
        if ($application->db->DB_isTableExists($application->getAppIni('DB_TABLE_PREFIX').'attributes'))
        {
            $attr_id = execQuery('SELECT_PRODUCT_ATTRIBUTE_ID_BY_NAME_AND_TAG', array(
                'name' => 'PRD_CUSTOMER_REVIEWS_NAME', 'view_tag' => 'CustomerReviews',
            ));
            return $attr_id[0]['attribute_id'];
        }
        return CUSTOMER_REVIEWS_PRODUCT_ATTRIBUTE_ID;
    }

    function getMembershipVisibilityAttrId()
    {
        global $application;
        if ($application->db->DB_isTableExists($application->getAppIni('DB_TABLE_PREFIX').'attributes'))
        {
            $attr_id = execQuery('SELECT_PRODUCT_ATTRIBUTE_ID_BY_NAME_AND_TAG', array(
                'name' => 'PRD_MEMBERSHIP_VISIBILITY_NAME', 'view_tag' => 'MembershipVisibility',
            ));
            return $attr_id[0]['attribute_id'];
        }
        return MEMBERSHIP_VISIBILITY_PRODUCT_ATTRIBUTE_ID;
    }

    function getInputTypeActualValue($value)
    {
        if (_ml_strpos($value, 'PRDTYPE_VALUE_') === 0)
            return getMsg('SYS', $value);

        return $value;
    }

    /* For displaying selected value in select box for categories - 27th august */
   /* function selectCategoryProductsforselectbox($category_id)
    {
    	$result = execQuery('SELECT_CATEGORY_PRODUCTS',array('cat_id' => $category_id));
 	return $result;
    }*/

    /* to get subcategories one level below only*/
    function getSubCategories($parent_cat_id)
    {
        $result = execQuery('SELECT_OTHER_SUBCATEGORIES_FOR_A_CATEGORY', array('id' => $parent_cat_id));
        return $result;
    }

    /**
     * The list of Catalog views.
     */
    var $ViewsList;


    /**
     * Editable Category ID.
     */
    var $editableCategoryID = NULL;
    /**
     * Current Category Additional (besides ID) parameters
     * concerned with category tree storing method
     */
    var $currentCategoryLeft  = NULL;
    var $currentCategoryRight = NULL;
    var $currentCategoryLevel = NULL;

    /**
     * Current Product ID.
     */
    var $currentProductID;

    var $moveToCategoryID = null;

    /**
     * Current Product Type ID.
     */
    var $currentProductTypeID;

    /**
     * List of action handlers.
     */
    var $ActionHandlersList;

    /**
     * List of multilang attributes (id -> viewtag)
     * use function isMLAttribute($param)
     * param is atrribute_id or view_tag
     */
    var $MultiLangAttributes;

    /**
     * It is used to define the product info output when deleting or viewing
     * its attributes.
     */
    var $DisplayDeleteInfo = false;

    var $_internal_cache = array(
        'input_type_values' => array()
       ,'unit_type_values' => array()
    );

    var $all_categories;

    /**#@-*/

}
?>