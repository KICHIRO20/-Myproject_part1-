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
_use(dirname(__FILE__).'/move_category_az.php');
/**
 * Catalog module.
 * Catalog Move Products view.
 *
 * @package Catalog
 * @access  public
 * @author  Girin Alexander
 */
class MoveProducts extends MoveCategory
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    function getLinkToView($cid)
    {
        global $application;

        $_request = new Request();
        $_request->setView  ( 'MoveProducts' );
        $_request->setAction( "SetMoveToCat" );
        $_request->setKey   ( "category_id", $cid );

        return $_request->getURL();
    }

    function outputMoveHref()
    {
        $request = new Request();
        $request->setView  ( 'MoveProducts' );
        $request->setAction( 'MoveToProducts' );
        return $request->getURL();
    }

    function outputMoveObject()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        $products_count = sizeof(modApiFunc('Catalog', 'getEditableProductsID'));
        return $obj->getMessage(new ActionMessage(array('MOVE_PRD_HEADER', $products_count)));
    }

    function outputMoveSubject()
    {
        // do not modify it! (submit renaming to af!)
        // its value refers to Page Help
        return 'products';
    }

    function outputAction()
    {
        return 'MoveToProducts';
    }

    function outputNewLocation($moveto_category_full_path)
    {
        return $this->outputLocationBreadcrumb($moveto_category_full_path, true, "MoveProducts");
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