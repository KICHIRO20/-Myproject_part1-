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

_use(dirname(__FILE__).'/add_category_info_action.php');
loadModuleFile('catalog/abstract/category_class.php');

/**
 * Catalog module.
 * This action is responsible for adding a new category.
 *
 * @package Catalog
 * @access  public
 * @author  Alexander Girin
 */
class UpdateCategoryInfo extends AddCategoryInfo
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     */
    function UpdateCategoryInfo()
    {
    }

    function initData($data)
    {
        $this->new_name = $data["Subcategory"];
        $this->old_id = $data["CategoryID"];
    }

    function saveDataToDB($data)
    {
        modApiFunc("Catalog", "updateCategory",
                   $data["CategoryID"],
                   $data["Subcategory"],
                   $data["CategoryStatus"],
                   $data["CategoryDescription"],
                   $data["ViewState"]["LargeImage"],
                   $data["ViewState"]["SmallImage"],
                   $data["ImageDescription"],
                   $data["PageTitle"],
                   $data["MetaKeywords"],
                   $data["MetaDescription"],
                   $data["CategoryShowProductsRecursivelyStatus"],
                   $data["SEO_URL_prefix"]
                  );
    }

    /**
     * Redirects after action.
     */
    function redirect()
    {
        global $application;

        $request = new Request();
        $request->setView('EditCategory');
        $request->setKey('tree_id', modApiFunc('Request', 'getValueByKey', 'tree_id'));

        // getting the category name for category tree
        // Note: assume the data language for the tree page is not changed
        //       otherwise the data language for that page should be set below
        // Note: Since the redirect is taken place we do not worry about
        //       restoring the language
        modApiFunc('MultiLang', 'setLanguage',
                   modApiFunc('MultiLang', 'getResourceLanguage'));
        $catInfo = new CCategoryInfo($this->old_id);
        $request->setKey('new_name', urlencode($catInfo->getCategoryTagValue('name')));

        $request->setKey('old_id', $this->old_id);
        $application->redirect($request);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**#@-*/
}
?>