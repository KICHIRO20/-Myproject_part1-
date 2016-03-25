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
_use(dirname(__FILE__).'/delete_category_az.php');
/**
 * Catalog module.
 * Catalog Delete Products view.
 *
 * @package Catalog
 * @access  public
 * @author  Girin Alexander
 */
class DeleteProducts extends DeleteCategory
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Gets a category Id, whose categories are deleted.
     *
     * @return integer category id
     */
    function getCategoryID()
    {
        return modApiFunc('CProductListFilter','getCurrentCategoryId');
    }

    /**
     * Returns the HTML code of the type of the deleted object.
     *
     * @return HTML
     */
    function outputDeleteObject()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        return $obj->getMessage(new ActionMessage('DEL_PRD_HEADER'));
    }

    function outputDeleteSubject()
    {
        // do not modify it! (submit renaming to af!)
        // its value refers to Page Help
        return 'products';
    }

    /**
     * Returns the HTML code of the warning message.
     *
     * @return HTML code
     */
    function outputDeleteWarning($category_name)
    {
/*        global $application;
        $obj = &$application->getInstance('MessageResources');
        return $obj->getMessage(new ActionMessage(array('DEL_PRD_WARNING', $category_name)));*/
    }

    /**
     * Returns the HTML code of the list of the deleted object.
     *
     * @return HTML code
     */
    function outputListItems($CatID)
    {
        global $application;
        $proditems = '';
        $paginator = modApiFunc('paginator', 'setCurrentPaginatorName', "Catalog_DeleteProducts_$CatID");

        $deleteProductIDs = modApiFunc('Catalog', 'getEditableProductsID');

        foreach ($deleteProductIDs as $pid)
        {
            $this->_Current_Product = new CProductInfo($pid);

            $request = new Request();
            $request->setView  ( 'Catalog_ProdInfo' );
            $request->setAction( 'SetCurrentProduct' );
            $request->setKey   ( 'prod_id', $pid);
            $request->setKey   ( 'del_info', "true");
            $productInfo_Link = $request->getURL();

            $this->_Current_Product->setAdditionalProductTag('InfoLink', $productInfo_Link);
            $application->registerAttributes($this->_Current_Product->getAdditionalProductTagList());
            $proditems.= modApiFunc('TmplFiller', 'fill', "catalog/del_cat/","list_item_prod.tpl.html", array());
        }
        return $proditems;
    }

    /**
     * Returns the HTML code of the paginator.
     *
     * @return HTML code
     */
    function outputPaginator($CatID)
    {
        $paginator = modAPIFunc('application', 'output', 'PaginatorLine', "Catalog_DeleteProducts_$CatID", 'DeleteProducts');
        modAPIFunc('paginator', 'resetPaginator', "Catalog_DeleteProducts_$CatID");
        return $paginator;
    }

    /**
     * Returns the HTML code of the product number in the list outputted on
     * the page.
     *
     * @return HTML code
     */
    function outputRows_Per_Page($CatID)
    {
        return modAPIFunc('application', 'output', 'PaginatorRows', "Catalog_DeleteProducts_$CatID", 'DeleteProducts', 'products');
    }

    /**
     * Returns the Form Action reference.
     *
     * @return Form Action
     */
    function outputDeleteHref()
    {
        $request = new Request();
        $request->setView  ( 'DeleteProducts' );
        $request->setAction( 'ConfirmDeleteProducts' );
        return $request->getURL();
    }

    /**
     * Returns the name of the action class.
     *
     * @return Form Action
     */
    function outputAction()
    {
        return 'ConfirmDeleteProducts';
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