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
 * Catalog module.
 * Action handler on SetCurrentCategory.
 *
 * @package Catalog
 * @access  public
 */
class SetCurrCat extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     *
     * @ finish the functions on this page
     */
    function SetCurrCat()
    {
    }

    /**
     * Sets current inventory category from Request.
     *
     * Action: setCurrCat
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $catid = $request->getValueByKey( 'category_id' );

        if ($catid != NULL)
        {
            // clearing the product search data
            if (modApiFunc('Session', 'is_set', 'SearchProductFormFilter'))
                modApiFunc('Session', 'un_set', 'SearchProductFormFilter');
            modApiFunc('CProductListFilter','changeCurrentCategoryId',$catid);
        }
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