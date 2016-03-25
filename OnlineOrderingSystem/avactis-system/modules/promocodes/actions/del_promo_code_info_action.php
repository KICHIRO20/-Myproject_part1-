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
 * This action is responsible for deleting a promo code.
 *
 * @package PromoCodes
 * @access  public
 * @author  Vadim Lyalikov
 */
class DelPromoCodeInfo extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor
     */
    function DelPromoCodeInfo()
    {
    }

    /**
     * Get Action name
     * @return string Action name
     */
    function ACT_NM()
    {
        return 'DelPromoCodeInfo';
    }


    /**
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = array();
        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        $SessionPost = $_POST;

        $PromoCode_id = $request->getValueByKey('PromoCode_id');
        if(!empty($PromoCode_id) &&
           ctype_digit($PromoCode_id) === TRUE)
        {
        	modApiFunc("PromoCodes", "deleteRowsFromPromoCode", array($PromoCode_id));
        }

        // get view name by action name.
        $this->redirect();
    }

    /**
     * Redirect after action
     */
    function redirect()
    {
        global $application;

        $request = new Request();
        $request->setView('PromoCodesNavigationBar');
        $application->redirect($request);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Corresponding View file.
     */
    var $ViewFilename;

    /**#@-*/
}
?>