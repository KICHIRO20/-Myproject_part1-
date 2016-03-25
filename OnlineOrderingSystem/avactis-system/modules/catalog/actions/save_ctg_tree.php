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
 */

/**
 * This class saves a whole categories tree received from the Manage Categories page.
 */
class save_ctg_tree extends AjaxAction
{
    function save_ctg_tree()
    {}

    function onAction()
    {
        $request = new Request();
        $tree_str = $request->getValueByKey('tree_str');
        $tree_id = $request->getValueByKey('tree_id');
        $ctg_id = $request->getValueByKey('ctg_id');

        modApiFunc('Catalog', 'saveFullCategoriesStructure', $tree_str);
        modApiFunc('CProductListFilter','changeCurrentCategoryId', $ctg_id);

        global $_RESULT;
        loadClass('CategoriesBrowserDynamic');
        $cb_obj = new CategoriesBrowserDynamic(CB_MODE_MANAGE, $tree_id);
        $_RESULT['tree_json'] = $cb_obj->outputJSON();
        $_RESULT['ctg_id'] = modApiFunc('Catalog', 'getEditableCategoryID');
        $_RESULT['tree_id'] = $tree_id;
    }
}

?>