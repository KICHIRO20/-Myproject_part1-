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

_use(dirname(__FILE__).'/add_promo_code_info_action.php');

/**
 * Catalog module.
 * This action is responsible for adding new category.
  *
 * @package PromoCodes
 * @access  public
 * @author  Vadim Lyalikov
 */
class UpdatePromoCodeInfo extends AddPromoCodeInfo
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
    function UpdatePromoCodeInfo()
    {
        $this->pcid = false;
        if (isset($_POST["PromoCodeID"]))
            $this->pcid = $_POST["PromoCodeID"];
    }

    function saveDataToDB($data)
    {
        modApiFunc("PromoCodes", "updatePromoCode",
                   $data["PromoCodeID"],
                   $data["PromoCodeCampaignName"],
                   $data["PromoCodePromoCode"],
                   $data["PromoCodeBIgnoreOtherDiscounts"],
                   $data["PromoCodeStatus"],
                   $data["PromoCodeMinSubtotal"],
                   $data["PromoCodeDiscountCost"],
                   $data["PromoCodeDiscountCostTypeID"],
                   $data["PromoCodeStartDateFYear"],
                   $data["PromoCodeStartDateMonth"],
                   $data["PromoCodeStartDateDay"],
                   $data["PromoCodeEndDateFYear"],
                   $data["PromoCodeEndDateMonth"],
                   $data["PromoCodeEndDateDay"],
                   $data["PromoCodeTimesToUse"],
                   $data["FreeShipping"],
                   $data["FreeHandling"],
                   $data["StrictCart"]
             );
     }

    /**
     * Redirect after action
     */
    function redirect()
    {
        global $application;

        $request = new Request();
        $request->setView('EditPromoCode');
        $request->setAction('SetEditablePromoCode');
        $request->setKey('PromoCode_id', $this->pcid);
        $application->redirect($request);
    }

    var $pcid;
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