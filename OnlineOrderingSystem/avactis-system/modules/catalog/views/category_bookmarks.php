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

_use(dirname(__FILE__).'/product_bookmarks.php');

class CategoryBookmarks extends ProductBookmarks
{
    function CategoryBookmarks()
    {
        parent::ProductBookmarks();
    }

    function _initBookmarks()
    {
    	$tree_id = modApiFunc('Request', 'getValueByKey', 'tree_id');
        $this->bms = array('details' => array(
                                'title' => 'CAT_DETAILS'
                               ,'icon' => 'c-details'
                               ,'location' => array(
                                    'view' => 'EditCategory'
                                   ,'action' => 'SetEditableCategory'
                                   ,'keys' => array(
                                                'category_id' => $this->entity_id,
                                   				'tree_id' => $tree_id,
                                              )
                                )
                             ),
                           'featured' => array(
                                 'title' => 'CAT_FEATURED'
                                ,'icon' => 'c-featured'
                                ,'location' => array(
                                    'view' => 'PopupWindow'
                                   ,'keys' => array(
                                                'page_view' => 'FP_LinksList'
                                               ,'category_id' => $this->entity_id,
                                   				'tree_id' => $tree_id,
                                              )
                                )
                             ),
                           'bestsellers' => array(
                                 'title' => 'CAT_BESTSELLERS'
                                ,'icon' => 'c-bestsellers'
                                ,'location' => array(
                                    'view' => 'PopupWindow'
                                   ,'keys' => array(
                                                'page_view' => 'BS_LinksList'
                                               ,'category_id' => $this->entity_id,
                                   				'tree_id' => $tree_id,
                                              )
                                )
                             ),
                          );

        $this->m_bms = array();

        $this->status_depends = array(
            'details_edit' => ''
           ,'details_add' => 'featured_notavail bestsellers_notavail'
        );

        $this->_need_right_space = true;
    }
};

?>