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
 * Checkout module.
 * Action handler on .
 *
 * @package Checkout
 * @access  public
 * @author Alexander Girin
 */
class SetOrdersForDeleteAction extends AjaxAction
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
    function SetOrdersForDeleteAction()
    {
    }


    /**
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');
        $values = $request->getValueByKey( 'order_id' );
        if (sizeof($values)>0)
        {
            modApiFunc("Checkout", "setOrdersIDForDelete", $values);
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