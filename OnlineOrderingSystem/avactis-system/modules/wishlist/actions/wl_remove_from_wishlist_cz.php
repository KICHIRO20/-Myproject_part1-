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
 *
 * @package Wishlist
 * @author Sergey Kulitsky
 */
class RemoveProductFromWishlist extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * RemoveProductFromWishlist constructor.
     */
    function RemoveProductFromWishlist()
    {
    }

    /**
     * Rmoves the product from the wishlist.
     */
    function onAction()
    {
        global $application;
        $request = $application -> getInstance('Request');

        $wl_id = $request -> getValueByKey('wl_id');

        modApiFunc('Wishlist', 'removeFromWishlist', $wl_id);

        modApiFunc('Session', 'set', 'WishlistResultMessage',
                   getMsg('CZ', 'WL_ITEM_REMOVED'));

        $request = new Request();
        $request -> setView(CURRENT_REQUEST_URL);
        $application -> redirect($request);
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