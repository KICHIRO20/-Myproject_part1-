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
 * @package Manufacturers
 * @access  public
 */
class SaveSortedManufacturers extends AjaxAction
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
    function SaveSortedManufacturers()
    {
    }


    /**
     *
     *
     * Action: SaveSortCat.
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = $_POST;

        $mnfsSortOrder = $request->getValueByKey( 'ObjectList_hidden' );
        $mnfsSortOrderArray = explode('|', $mnfsSortOrder);

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        if ($mnfsSortOrderArray != NULL)
        {
            modApiFunc('Manufacturers', 'setManufacturersSortOrder', $mnfsSortOrderArray);
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