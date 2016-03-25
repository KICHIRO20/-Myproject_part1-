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

loadModuleFile('checkout/views/checkout-order-info-az.php');

class OrderInvoice extends OrderInfo
{
    function OrderInvoice()
    {
    	parent::OrderInfo();
    	$this->isInfo=false;
        $this->template_folder = "order-invoice";
        $this->initFormData();
    }

    function output()
    {
        global $application;

        $request = &$application -> getInstance('Request');
        $this -> _print = $request -> getValueByKey('do_print');
        $application->registerAttributes(array(
            'PersonInfoShipping'
           ,'PersonInfoBilling'
           ,'PrintCommand'
        ));
        $res = parent::output();
        return $res;
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case 'PersonInfoShipping':
                $value = $this->getPersonInfo('Shipping');
                break;

            case 'PersonInfoBilling':
                $value = $this->getPersonInfo('Billing');
                break;
            case 'OrderTrackId':
                if (modApiFunc("Checkout", "getDeleteOrdersFlag") == "true")
                {
                    $value = nl2br($this->_order['TrackId']);
                }
                else
                {
                    $value = nl2br($this->_order['TrackId']);
                }
                break;
            case 'PrintCommand':
                if ($this -> _print != 'Y')
                {
                    $value = '';
                }
                else
                {
                    $value = modApiFunc('TmplFiller', 'fill',
                                        'checkout/' . $this -> template_folder . '/',
                                        'print_js_code.tpl.html', array());
                }
                break;

            default:
                $value = parent::getTag($tag);
                break;
        }
        return $value;
    }

    var $_print;
}
?>