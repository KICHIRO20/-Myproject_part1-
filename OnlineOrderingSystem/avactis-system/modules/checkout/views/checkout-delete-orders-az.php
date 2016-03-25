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
 * Catalog module.
 * Catalog Category Delete view.
 *
 * @author Alexander Girin
 * @package Catalog
 * @access  public
 */
class DeleteOrders
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * The view constructor.
     *
     * @ finish the functions on this page
     */
    function DeleteOrders()
    {
        global $application;
        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            // eliminate copying on construction
            $SessionPost = modApiFunc("Session", "get", "SessionPost");
            $this->ViewState = $SessionPost["ViewState"];

            //Remove some data, that should not be sent to action one more time, from ViewState.
            if($this->ViewState["hasError"] == "true")
            {
                $this->ErrorsArray = $this->ViewState["ErrorsArray"];
                unset($this->ViewState["ErrorsArray"]);
            }

            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->ViewState =
                array(
                    "hasError"          => "false",
                    "hasCloseScript"    => "false"
                     );
            $this->POST = array();
        }
    }

    /**
     * Returns the HTML code of the hidden fields of the array ViewState.
     *
     * @return HTML code
     */
    function outputViewState()
    {
        $retval = "";
        foreach ($this->ViewState as $key => $value)
        {
            $retval .= "<input type=\"hidden\" name=\"ViewState[" .$key . "]\" value=\"" . $value . "\">";
        }
        return $retval;
    }


    /**
     * Returns the HTML code of the deleted object list.
     *
     * @return HTML code
     */
    function outputListItems()
    {
        global $application;
        $retval = '';
        $orders = modApiFunc("Checkout", "getOrdersIDForDelete");
        foreach ($orders as $orderId)
        {
            $this->_Template_Contents = array();
            $order_id = sprintf("%d", $orderId);
            $currency_id = modApiFunc("Localization", "whichCurrencyToDisplayOrderIn", $order_id);
            modApiFunc("Localization", "pushDisplayCurrency", $currency_id, $currency_id);
            $orderInfo = modApiFunc("Checkout", "getOrderInfo", $order_id, $currency_id);

            $request = new Request();
            $request->setView  ('OrderInfo');
            $request->setAction('SetCurrentOrder');
            $request->setKey('order_id', $orderInfo["ID"]);
            $request->setKey('delete', 'true');
            $OrderInfoLink = $request->getURL();

            $customer_obj = &$application->getInstance('CCustomerInfo',modApiFunc('Customer_Account','getCustomerAccountNameByCustomerID',$orderInfo['PersonId']));

            $request = new Request();
            $request->setView('PopupWindow');
            $request->setKey('page_view', 'CustomerAccountInfo');
            $request->setKey('customer_id', $orderInfo['PersonId']);
            $CustomerInfoLink = $request->getURL();

            $this->_Template_Contents['OrderId'] = $orderInfo["ID"];
            $this->_Template_Contents['OrderInfoLink'] = $OrderInfoLink;
            $this->_Template_Contents['Customer'] = prepareHTMLDisplay($customer_obj->getDisplayAccountName());
            $this->_Template_Contents['CustomerInfoLink'] = $CustomerInfoLink;
            $this->_Template_Contents['OrderDate'] = modApiFunc("Localization", "date_format", $orderInfo["Date"]);
            $this->_Template_Contents['OrderAmount'] =  modApiFunc("Localization", "currency_format", $orderInfo["Total"]);
            $this->_Template_Contents['OrderStatus'] = $orderInfo["Status"];
            $this->_Template_Contents['PaymentStatus'] = $orderInfo["PaymentStatus"];

            $application->registerAttributes($this->_Template_Contents);
            $retval.= modApiFunc('TmplFiller', 'fill', "checkout/delete-orders/","item.tpl.html", array());
            modApiFunc("Localization", "popDisplayCurrency");
        }

        return $retval;
    }

    /**
     * Returns the Orders Delete view.
     *
     * @ finish the functions on this page
     */
    function output()
    {
        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        global $application;
        $this->_Template_Contents = array();
        $application->registerAttributes(
            array(
                'HiddenArrayViewState'  => ''
               ,'DeleteOrdersAction'    => ''
               ,'Items'                 => ''
               ,'HiddenFieldOrdersId'   => ''
            )
        );

        $retval = modApiFunc('TmplFiller', 'fill', "checkout/delete-orders/","list.tpl.html", array());
        return $retval;
    }

    /**
     * @
     */
    function getTag($tag)
    {
        global $application;
        $value = NULL;
        switch ($tag)
        {
            case 'HiddenArrayViewState':
                $value = $this->outputViewState();
                break;
            case 'Items':
                $value = $this->outputListItems();
                break;
            case 'HiddenFieldOrdersId':
                $value = implode("|", modApiFunc("Checkout", "getOrdersIDForDelete"));
                break;
            case 'DeleteOrdersAction':
                $request = new Request();
                $request->setView  ('DeleteOrders');
                $request->setAction('DeleteOrdersAction');
                $value = $request->getURL();
                break;
            default:
                if (array_key_exists($tag, $this->_Template_Contents))
                {
                    $value = $this->_Template_Contents[$tag];
                }
                break;
        }
        return $value;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $mTmplFiller;

    /**#@-*/

}
?>