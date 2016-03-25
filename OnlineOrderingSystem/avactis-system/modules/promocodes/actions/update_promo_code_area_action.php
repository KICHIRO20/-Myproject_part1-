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
class UpdatePromoCodeArea extends AddPromoCodeInfo
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
    function UpdatePromoCodeArea()
    {
    }

    function saveDataToDB($data)
    {
        modApiFunc("PromoCodes", "updatePromoCodeArea", $data);
    }

    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $pcid = $request->getValueByKey('PromoCodeID');
        $categories_affected = $request->getValueByKey('cat_to_save');
        $products_affected   = $request->getValueByKey('prod_to_save');

        $data['pcid'] = $pcid;

        if ($categories_affected)
            $data['cats'] = implode('|', $categories_affected);
        else
            $data['cats'] = '';

        if ($products_affected)
            $data['prods'] = implode('|', $products_affected);
        else
            $data['prods'] = '';

        if ($pcid)
            $this->saveDataToDB($data);

        $SessionPost = array('ViewState' => array('hasCloseScript' => false));
        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        $request->setView('EditPromoCodeArea');
        $request->setKey('PromoCode_id', $pcid);
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