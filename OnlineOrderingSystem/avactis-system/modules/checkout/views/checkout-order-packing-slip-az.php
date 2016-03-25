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
 *
 * @package Checkout
 * @access  public
 */

loadModuleFile('checkout/views/checkout-order-invoice-az.php');

class OrderPackingSlip extends OrderInvoice
{
    function OrderPackingSlip()
    {
        parent::OrderInfo();
        $this->template_folder = "order-packing-slip";
        $this->initFormData();
    }

    function output()
    {
        $res = parent::output();
        return $res;
    }

    function getTag($tag)
    {
        $value = null;
        $value = parent::getTag($tag);
        return $value;
    }
}
?>