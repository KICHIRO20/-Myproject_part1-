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
 * @package PromoCodes
 * @author Vadim Lyalikov
 */
class RemovePromoCode extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * AddToCart constructor
     */
    function RemovePromoCode()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;

        modApiFunc('PromoCodes', 'removePromoCode');
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