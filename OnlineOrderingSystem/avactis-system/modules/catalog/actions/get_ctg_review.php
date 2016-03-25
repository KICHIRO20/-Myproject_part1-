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
 * This class generates a category review on the Manage Categories page.
 */
class get_ctg_review extends AjaxAction
{
    function get_ctg_review()
    {}

    function onAction()
    {
        global $application;

        $category_id = modApiFunc('Request', 'getValueByKey', 'category_id');
        modApiFunc('CProductListFilter', 'changeCurrentCategoryId', $category_id);

        $review = modApiFunc('TmplFiller', 'fill', "catalog/category_review/", "body.tpl.html", array());

        global $_RESULT;
        $_RESULT['review'] = $review;
//        $application->_exit();
        return null;
    }

}

?>