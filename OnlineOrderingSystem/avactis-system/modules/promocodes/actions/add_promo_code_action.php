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
class AddPromoCode extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * AddPromoCode constructor
     */
    function AddPromoCode()
    {
    }

    /**
     *            Promo Code'
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $promo_code = $request->getValueByKey('promo_code');

        $promo_code_id = modApiFunc("PromoCodes", "getPromoCodeIdByPromoCode", $promo_code);
        if($promo_code_id === false)
        {
            $b_applicable = false;
        }
        else
        {
            $b_applicable = modApiFunc("PromoCodes", "isPromoCodeApplicableWithoutMinSubtotal", $promo_code_id);
            $area_applicable = modApiFunc("PromoCodes", 'isPromoCodeEffectiveAreaNotEmpty', $promo_code_id);
        }

        if($b_applicable === true && $area_applicable === true)
        {
            modApiFunc('PromoCodes', 'setPromoCodeId', $promo_code_id);
            $request = new Request();
            $request->setView(CURRENT_REQUEST_URL);
            $application->redirect($request);
        }
        else
        {
            //               Promo Code       .
            modApiFunc("PromoCodes", "setAddPromoCodeError", "CART_ADD_PROMO_CODE_ERROR_001");
            $request = new Request();
            $request->setView(CURRENT_REQUEST_URL);
            $application->redirect($request);
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