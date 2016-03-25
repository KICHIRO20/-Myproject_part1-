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
 * PromoCodes module.
 * Action handler on .
 *
 * @package PromoCodes
 * @access  public
 */
class SetEditablePromoCode extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor
     *
     * @ finish the functions on this page
     */
    function SetEditablePromoCode()
    {
    }


    /**
     * Set current promo code from Request.
     *
     * Action: SetEditablePromoCode
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $pcid = $request->getValueByKey( 'PromoCode_id' );

        if ($pcid != NULL)
        {
            modApiFunc('PromoCodes', 'setEditablePromoCodeID', $pcid);
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