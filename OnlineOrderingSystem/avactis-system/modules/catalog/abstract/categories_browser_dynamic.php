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
 * @author Sergey Galanin
 *
 */
define('CB_MODE_MANAGE', 'manage');

class CategoriesBrowserDynamic
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

	function CategoriesBrowserDynamic($mode, $tree_id = null)
    {
    	global $application;
    	$this->mTmplFiller = &$application->getInstance('TmplFiller');
    	$this->unq_prefix = $tree_id ? $tree_id : uniqid('pb_');
        $this->cats = modApiFunc("Catalog", "getSubcategoriesFullListWithParent", 1, false);
        $this->cats_tree = modApiFunc('Catalog', 'convertCategoriesListToTree', $this->cats);
        if ($mode == CB_MODE_MANAGE) {
            $this->params = array(
                    'onchange' => 'ManageCategories_onTreeChanged',
                    'onselect' => 'ManageCategories_onNodeSelected',
                    'beforedelete' => 'ManageCategories_confirmDelete',
                    'ischanged' => 'ManageCategories_isTreeChanged',
                    'onproducts' => 'ManageCategories_onGoToProducts',
                    'pushrollback' => 'ManageCategories_pushRollback',
                    'overwriterollback' => 'ManageCategories_overwriteRollback',
                );
        }
    }

    function output($params)
    {
        $tags = array(
            'UnqPrefix' => $this->unq_prefix,
            'JavaScript_Code' => $this->outputScript($params),
        );

        return $this->mTmplFiller->fill("catalog/categories_browser_dynamic/", "container.tpl.html", $tags);
    }

    function outputBox()
    {
        $vars = array(
            'UnqPrefix' => $this->unq_prefix,
            );
        return $this->mTmplFiller->fill('catalog/categories_browser_dynamic/', 'container.tpl.html', $vars);
    }

    function outputJSON()
    {
        return $this->outputButtons($this->cats_tree);
    }

    function outputScript()
    {
        $tags = array(
            'UnqPrefix' => $this->unq_prefix,
            'JSON_Structure' => $this->outputButtons($this->cats_tree),
            'onTreeChanged'  => $this->params['onchange'],
            'onNodeSelected' => $this->params['onselect'],
            'beforeDelete' => $this->params['beforedelete'],
            'isTreeChanged' => $this->params['ischanged'],
            'onGoToProducts' => $this->params['onproducts'],
            'urlGoToProducts' => $this->urlGoToProducts(),
            'pushRollBack' => $this->params['pushrollback'],
            'overwriteRollBack' => $this->params['overwriterollback'],
            'PreselectedNodeId' => $this->getCurentCategoryId(),
        );

        return $this->mTmplFiller->fill("catalog/categories_browser_dynamic/", "javascript_code.tpl.html",$tags);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    function outputButtons( $cat_node, $comma = '' )
    {
    	$children = '';
        $state = '';
        $keys = array_keys($cat_node->children);
        if (sizeof($keys) > 0) {
        	while (is_null($key = array_shift($keys)) == false) {
        		$children .= $this->outputButtons(
        		      $cat_node->children[$key], empty($keys) ? '' : ',');
        	}
        	$tags = array( 'Children' => $children );
        	$children = $this->mTmplFiller->fill("catalog/categories_browser_dynamic/", "button.children.tpl.html", $tags);
        	$state = "state: 'open',";
        }

    	$tags = array(
            'UnqPrefix' => $this->unq_prefix,
            'CatId' => $cat_node->id,
            'CatName' => htmlspecialchars(escapeJSScript(trim($cat_node->name))),
    	    'NodeType' => $cat_node->id == 1 ? 'root' : 'folder',
    	    'State' => $state,
            'Children' => $children,
            'Comma' => $comma,
        );
        return $this->mTmplFiller->fill("catalog/categories_browser_dynamic/", "button.tpl.html",$tags);
    }

    function getCurentCategoryId()
    {
        $c_id = modApiFunc('CProductListFilter','getCurrentCategoryId');
        return $c_id ? '\''.$this->unq_prefix.'_cat_'.$c_id.'\'' : 'false';
    }

    function urlGoToProducts()
    {
        $request = new Request();
        $request->setView('ProductList');
        $request->setKey('asc_action', 'SetCurrCat');
        $request->setKey('category_id', '');
        return $request->getURL();
    }

    var $unq_prefix;
    var $cats;
    var $cats_tree;
};

?>