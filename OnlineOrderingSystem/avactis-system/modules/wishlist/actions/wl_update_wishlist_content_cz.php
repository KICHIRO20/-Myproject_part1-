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
 * @package Wishlist
 * @author Sergey Kulitsky
 */
class UpdateWishlistContent extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * UpdateWishlistContent constructor.
     */
    function UpdateWishlistContent()
    {
    }

    /**
     * Updates the cart contents.
     */
    function onAction()
    {
        global $application;
        $request = $application -> getInstance('Request');

        $quantity = $request->getValueByKey('quantity');

        if (is_array($quantity))
            foreach($quantity as $wl_id => $value)
                modApiFunc('Wishlist', 'updateWishlistRecord', $wl_id, $value);

        modApiFunc('Session', 'set', 'WishlistResultMessage',
                   getMsg('CZ', 'WL_ITEM_UPDATED'));

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
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