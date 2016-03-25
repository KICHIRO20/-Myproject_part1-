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
_use(dirname(__FILE__).'/sort_categories_az.php');
/**
 * Catalog module.
 * Catalog Sort Products view.
 *
 * @author Alexander Girin
 * @package Catalog
 * @access  public
 */
class SortProducts extends SortCategories
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Returns the HTML code of the sorted objects type.
     *
     * @return HTML code
     */
    function outputSortObject($type)
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        switch ($type)
        {
            case "CS": $res = 'SORT_PRD_OBJ_CAP_SNGL'; break;
            case "CP": $res = 'SORT_PRD_OBJ_CAP_PLRL'; break;
            case "S": $res = 'SORT_PRD_OBJ_SNGL'; break;
            case "P": $res = 'SORT_PRD_OBJ_PLRL'; break;
        }
        return $obj->getMessage(new ActionMessage($res));
    }

    /**
     * Returns the HTML code of the deleted objects type.
     *
     * @return HTML code
     */
    function outputSortSubject()
    {
        // do not modify it! (submit renaming to af!)
        // its value refers to Page Help
        return "products";
    }
    /**
     * Returns the HTML code of the sorted products list.
     *
     * @return HTML code
     */
    function outputOptionsList($CatID, &$OptionsListHiddenArray)
    {
       /*
        Execute a direct query to the database, passing over theCatalog API,
        for optimization.
        The point is that, to sort the products in the category means
        to select all products in the category. The sorting needs only
        the product ID and the product NAME. It's the reason why the direct
        query is much effective, than using the CProductInfo object.
        */
        global $application;
        $result = modApiFunc('Catalog', 'selectSortedCategoryProducts', $CatID);

        $OptionsList = '';
        foreach ($result as $prodInfo)
        {
            $OptionsList.= '<option value='.$prodInfo['p_id'].'>'.$prodInfo['p_name'].'</option>';
            array_push($OptionsListHiddenArray, $prodInfo['p_id']);
        }
        return $OptionsList;
    }

   /**
    * Returns the reference.
    *
    * @return
    */
    function outputSaveSortHref()
    {
        $request = new Request();
        $request->setView ( 'SortProducts' );
        $request->setAction( 'SaveSortedProducts' );
        return $request->getURL();
    }

    function getHiddenFiled()
    {
        $HtmlForm = new HtmlForm();
        return $HtmlForm->genHiddenField('asc_action', 'SaveSortedProducts');
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