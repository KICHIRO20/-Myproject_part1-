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
 * @package Gift Certificate
 * @author Andrei Zhuravlev
 */
class AddGCToCart extends AjaxAction
{
    var $__errors = array();
    var $templateFiller;
    var $template;

    function AddGCToCart()
    {
    }

    function onAction()
    {
        global $application;

        $request = $application->getInstance('Request');
        #$msgres = $application->getInstance("MessageResources", "messages");

        # Preparing template filler for product description
        $this->templateFiller = $application->getInstance('TmplFiller');

        $is_error = false;

        $errors = $this->verify($_POST);

        if (!empty($errors))
        {
            modApiFunc("Session","set","GC_errors",$errors);
            modApiFunc("Session","set","SessionPost", $_POST);

            $req = new Request();
            $req->setView("GiftCertificate");
            #_print($req->getURL());die;
            $application->redirect($req);
            return;
        }

        # create new GC as product of gift certificate product type
        $product_type = modApiFunc('Catalog', 'getProductType', GC_PRODUCT_TYPE_ID);

        $product_info["Name"] = $this->getGCName($_POST['gc_from'],$_POST['gc_to']);
        $product_info["SalePrice"] = $this->getGCAmount($_POST['gc_amount']);
        $product_info["SmallImage"] = "";
        $product_info["LargeImage"] = "";

        # Shipping Information
        if ($_POST['gc_sendtype'] == 'P')
        {
            $product_info["FreeShipping"] = modApiFunc("Settings","getParamValue","GIFT_CERTIFICATES","GC_FREE_SHIPPING");
            $product_info["NeedShipping"] = modApiFunc("Settings","getParamValue","GIFT_CERTIFICATES","GC_NEED_SHIPPING");
            $product_info["Weight"] = modApiFunc("Settings","getParamValue","GIFT_CERTIFICATES","GC_WEIGHT");
            $product_info["PerItemShippingCost"] = modApiFunc("Settings","getParamValue","GIFT_CERTIFICATES","GC_PER_ITEM_SHIPPING_COST");
            $product_info["PerItemHandlingCost"] = modApiFunc("Settings","getParamValue","GIFT_CERTIFICATES","GC_PER_ITEM_HANDLING_COST");
        }
        else
        {
            $product_info["NeedShipping"] = "2"; // No shipping needeed for e-mail delivery
        }


        $gc_data = array();
        foreach($_POST as $p => $v)
        {
            if ($p == "gc_country_id" && !empty($v))
                $gc_data["gc_country"] = modApiFunc('Location','getCountry',$v);
            else if ($p == "gc_state_id" && !empty($v))
                $gc_data["gc_state"] = modApiFunc('Location','getState',$v);
            else
                $gc_data[$p] = addslashes($v);
        }

        if ($_POST['gc_sendtype'] == 'P')
            $product_info["DetailedDescription"] = $this->templateFiller->fill("",getTemplateFileAbsolutePath("gift-certificate/create-gc-form/default/gc-info-description-post.tpl.html"),$gc_data);
        else if ($_POST['gc_sendtype'] == 'E')
            $product_info["DetailedDescription"] = $this->templateFiller->fill("",getTemplateFileAbsolutePath("gift-certificate/create-gc-form/default/gc-info-description-email.tpl.html"),$gc_data);


        foreach ($product_type['attr'] as $view_tag => $attr)
        {
            if (preg_match("/gc_/i", $view_tag))
                if (isset($_POST[$view_tag]) && !empty($_POST[$view_tag]))
                    $product_info[$view_tag] = trim($_POST[$view_tag]);
                else
                    $product_info[$view_tag] = getLabel('GIFTCERTIFICATE_EMPTY_FIELD');
        }

        $category_id = -1;

        $prod_id = modApiFunc('Catalog', 'addProductInfo', GC_PRODUCT_TYPE_ID, $category_id, $product_info); // create new GC product in database

        $data=array(
            'parent_entity' => 'product'
           ,'entity_id' => $prod_id
           ,'options' => array()
           ,'qty' => 1
        );

        $options_sent = "no";

        if (!$is_error)
        {
            if(!empty($stock_discarded_by_warning))
            {
                modApiFunc('Session','set','StockDiscardedBy',$stock_discarded_by_warning);
            }

            modApiFunc('Cart', 'addToCart', $data);
            $request = new Request();
            #$request->setView(CURRENT_REQUEST_URL);
            $request->setView('CartContent');
            $application->redirect($request);
        }
        else
        {
            if($discard_by != 'none')
            {
                modApiFunc('Session','set','OptionsDiscardedBy',$discard_by);
            }
            if($stock_discarded_by != 'none')
            {
                modApiFunc('Session','set','StockDiscardedBy',$stock_discarded_by);
            }
            modApiFunc('Session','set','sentCombination',$data['options']);
            $request = new Request();
            $request->setView(CURRENT_REQUEST_URL);
            /*$request->setView('ProductInfo');
            $request->setAction('SetCurrentProduct');
            $request->setKey('prod_id',$prod_id);
            $request->setProductID($prod_id);
            $p = new CProductInfo($prod_id);
            $request->setCategoryID($p->chooseCategoryID());*/
            $application->redirect($request);
        };
    }

    function getGCName($from="", $to="")
    {
        return getLabel("GIFTCERTIFICATE_NAME");
    }

    function getGCAmount($amount)
    {
        if ($amount > 0 && is_numeric($amount))
        {
            $default_currency_code = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getMainStoreCurrency"));
            $local_currency_code = modApiFunc("Localization", "getCurrencyCodeById", modApiFunc("Localization", "getLocalDisplayCurrency"));
            $amount = modApiFunc("Currency_Converter", "convert", $amount, $local_currency_code, $default_currency_code);
            return $amount;
        }
        else
        {
            $this->__error[] = "BAD_AMOUNT";
            return -1;
        }
    }

    function verify($data)
    {
        $errors = array();

        # common for all GCs

        if (empty($data['gc_from']))
            $errors[] = GC_E_FIELD_FROM;
        if (empty($data['gc_to']))
            $errors[] = GC_E_FIELD_TO;
        if (empty($data['gc_amount']) || ($data['gc_amount'] <= 0) )
            $errors[] = GC_E_FIELD_AMOUNT;
        if (!preg_match('/^[0-9\.]+$/',$data['gc_amount']))
            $errors[] = GC_E_FIELD_AMOUNT_SEPARATOR;

        if ($data['gc_sendtype'] == GC_SENDTYPE_EMAIL)
        {
            if (empty($data['gc_email']) || !modApiFunc('Users', 'isValidEmail', $data['gc_email']))
                $errors[] = GC_E_FIELD_EMAIL;
        }
        else //$data['gc_sendtype'] == GC_SENDTYPE_POST
        {
            if (empty($data['gc_fname']))
                $errors[] = GC_E_FIELD_FNAME;
            if (empty($data['gc_lname']))
                $errors[] = GC_E_FIELD_LNAME;
            if (empty($data['gc_country_id']))
                $errors[] = GC_E_FIELD_COUNTRYID;
            if (empty($data['gc_state_id']))
                $errors[] = GC_E_FIELD_STATEID;
            if (empty($data['gc_address']))
                $errors[] = GC_E_FIELD_ADDRESS;
            if (empty($data['gc_city']))
                $errors[] = GC_E_FIELD_CITY;
            if (empty($data['gc_zip']))
                $errors[] = GC_E_FIELD_ZIP;
            /*  if (empty($data['gc_phone']))
                $errors[] = GC_E_FIELD_PHONE; */

        }

        return $errors;
    }

}
?>