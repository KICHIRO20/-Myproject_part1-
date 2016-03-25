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
 * @package QuickBooks
 * @author Egor V. Derevyankin
 *
 */

loadClass('DataWriterDefault');

class DataWriterOrdersIIF extends DataWriterDefault
{
    function DataWriterOrdersIIF()
    {
    }

    /**
     *               -
     *
     * @param array $settings -        settings
     * @param ref array of strings $warnings -                      ,
     * @param ref array of strings $errors -              ,
     */
    function initWork($settings)
    {
        $this->clearWork();
        $this->_settings = $settings;
        $this->_out_file_handler = fopen($this->_settings['out_file'],'w');
        $this->_process_info['status'] = 'INITED';
    }


    /**
     *                            .
     *
     * @param array $data
     * @param ref array of strings $warnings -                      ,
     * @param ref array of strings $errors -              ,
     */
    function doWork($data)
    {

        // Converting some data
        // order date (DATE)
        $__tmp = explode(' ', $data['Date']);
        $_tmp = explode('-', $__tmp[0]);
        $order_date = $_tmp[1].'/'.$_tmp[2].'/'.$_tmp[0];
        // order name (NAME)
        $order_name = $data['Billing']['attr']['Firstname']['value'].', '.$data['Billing']['attr']['Lastname']['value'].' - '.$data['Billing']['attr']['Email']['value'];
        // order billing name (BADDR1)
        $order_billing_name = $data['Billing']['attr']['Firstname']['value'].' '.$data['Billing']['attr']['Lastname']['value'];
        // order shipping name (SADDR)
        $order_shipping_name = $data['Shipping']['attr']['Firstname']['value'].' '.$data['Shipping']['attr']['Lastname']['value'];
        // order id
        $order_id = $this->_settings['QB_ORDERS_PREFIX'].$data['ID'];
        // order memo
        $order_memo = "";
        if (is_array($data['Comments']))
            foreach($data['Comments'] as $comment)
                if (isset($comment['content']) && $comment['content'])
                    $order_memo .= " - " . $comment['content'];
        $order_memo = "Website Order" . (($order_memo) ? ': ' . str_replace(array("\n","\r","\t"),'',_ml_substr($order_memo, 3)) : '');

        $out_data = "";

        // General information header
        $headers = array(
            "ACCNT" => array("NAME","ACCNTTYPE","SCD","EXTRA","HIDDEN")
        );

        foreach($headers as $header => $hfields)
            $out_data.="!".$header."\t".implode("\t",$hfields)."\n";

        // General information
        $out_data .= "ACCNT\t".$this->_settings['ACC_TAX']."\tINC\t\t\tN\n";
        $out_data .= "\n";

        // Customer information header
        $headers = array(
           "CUST" => array("NAME","BADDR1","BADDR2","BADDR3","BADDR4","BADDR5"
                ,"SADDR1","SADDR2","SADDR3","SADDR4","SADDR5","PHONE1","FAXNUM"
                ,"EMAIL","CONT1","SALUTATION","COMPANYNAME","FIRSTNAME"
                ,"LASTNAME","CUSTFLD1")
        );

        foreach($headers as $header => $hfields)
            $out_data.="!".$header."\t".implode("\t",$hfields)."\n";

        // Customer information
        $cust_info = array("CUST"
            ,$order_name
            ,$order_billing_name
            ,$data['Billing']['attr']['Streetline1']['value'].' '.$data['Billing']['attr']['Streetline2']['value']
            ,'"'.$data['Billing']['attr']['City']['value'].', '.$data['Billing']['attr']['State']['value'].' '.$data['Billing']['attr']['Postcode']['value'].'"'
            ,$data['Billing']['attr']['Country']['value']
            ,''
            ,$order_shipping_name
            ,$data['Shipping']['attr']['Streetline1']['value'].' '.$data['Shipping']['attr']['Streetline2']['value']
            ,'"'.$data['Shipping']['attr']['City']['value'].', '.$data['Shipping']['attr']['State']['value'].' '.$data['Shipping']['attr']['Postcode']['value'].'"'
            ,$data['Shipping']['attr']['Country']['value']
            ,''
            ,@$data['Billing']['attr']['Phone']['value']
            ,@$data['Billing']['attr']['Fax']['value']
            ,@$data['Billing']['attr']['Email']['value']
            ,$data['Billing']['attr']['Firstname']['value'].', '.$data['Billing']['attr']['Lastname']['value']
            ,'' // Salutation; here comes something like Mr., Ms., etc
            ,@$data['Billing']['attr']['Company']['value']
            ,$data['Billing']['attr']['Firstname']['value']
            ,$data['Billing']['attr']['Lastname']['value']
            ,@$data['Billing']['attr']['Email']['value']
            );

        $out_data .= implode("\t",$cust_info)."\n";
        $out_data .= "\n";

        // Declare inventory items
        if ($this->_settings['OP_AS_INV'] == 'Y')
        {
            $headers = array(
                "INVITEM" => array("NAME","INVITEMTYPE","DESC","PURCHASEDESC"
                ,"ACCNT","ASSETACCNT","COGSACCNT","PRICE","COST","TAXABLE"
                ,"REORDERPOINT")
            );

            foreach($headers as $header => $hfields)
                $out_data.="!".$header."\t".implode("\t",$hfields)."\n";

            foreach($data['Products'] as $pinfo)
            {
                $prod_info = array("INVITEM"
                    ,($pinfo['SKU']!='') ? $pinfo['SKU'] : $pinfo['storeProductID']
                    ,'INVENTORY'
                    ,$pinfo['name'].(!empty($pinfo['options']) ? '\n'.modApiFunc("Product_Options","prepareTextForOrdersExport",$pinfo['options']) : '')
                    ,$pinfo['name'].(!empty($pinfo['options']) ? '\n'.modApiFunc("Product_Options","prepareTextForOrdersExport",$pinfo['options']) : '')
                    ,$this->_settings['ACC_PRODUCT']
                    ,$this->_settings['ACC_INVENTORY']
                    ,$this->_settings['ACC_COGS']
                    ,$pinfo['SalePrice']
                    ,0
                    ,'N'
                    ,$this->_settings['MIN_QIS']
                );

                $out_data .= implode("\t",$prod_info)."\n";
            }
	    $out_data .= "\n";
        }

	// Order invoice headers
        $headers = array(
            "TRNS" => array("TRNSTYPE","DATE","ACCNT","NAME","CLASS"
                    ,"AMOUNT","DOCNUM","MEMO","ADDR1","ADDR2","ADDR3","ADDR4"
                    ,"ADDR5","SHIPVIA","SADDR1","SADDR2","SADDR3","SADDR4"
                    ,"SADDR5","TOPRINT")
           ,"SPL" => array("TRNSTYPE","DATE","ACCNT","NAME","CLASS"
                    ,"AMOUNT","DOCNUM","MEMO","PRICE","QNTY","INVITEM"
                    ,"TAXABLE","EXTRA")
           ,"ENDTRNS" => array()
         );

        foreach($headers as $header => $hfields)
            $out_data.="!".$header."\t".implode("\t",$hfields)."\n";

        $trns_lines = array();

        // Order Totals
        $trns_lines[] = array("TRNS"
                ,"INVOICE"
                ,$order_date
                ,"Accounts Receivable"
                ,$order_name
                ,$this->_settings['TRNS_CLASS']
                ,$data['Total']
                ,$order_id
                ,$order_memo
                ,$order_billing_name
                ,$data['Billing']['attr']['Streetline1']['value'].' '.$data['Billing']['attr']['Streetline2']['value']
                ,'"'.$data['Billing']['attr']['City']['value'].', '.$data['Billing']['attr']['State']['value'].' '.$data['Billing']['attr']['Postcode']['value'].'"'
                ,$data['Billing']['attr']['Country']['value']
                ,''
                ,''
                ,$order_shipping_name
                ,$data['Shipping']['attr']['Streetline1']['value'].' '.$data['Shipping']['attr']['Streetline2']['value']
                ,'"'.$data['Shipping']['attr']['City']['value'].', '.$data['Shipping']['attr']['State']['value'].' '.$data['Shipping']['attr']['Postcode']['value'].'"'
                ,$data['Shipping']['attr']['Country']['value']
                ,''
                ,'Y'
        );

        // Order product information
        foreach($data['Products'] as $pinfo)
        {
            $trns_lines[] = array("SPL"
                ,"INVOICE"
                ,$order_date
                ,$this->_settings['ACC_PRODUCT']
                ,$order_name
                ,$this->_settings['TRNS_CLASS']
                ,$pinfo['SalePrice']*$pinfo['qty']*(-1)
                ,$order_id
                ,$pinfo['name'].(!empty($pinfo['options']) ? '\n'.modApiFunc("Product_Options","prepareTextForOrdersExport",$pinfo['options']) : '')
                ,$pinfo['SalePrice']
                ,$pinfo['qty']*(-1)
                ,(isset($pinfo['SKU']) && $pinfo['SKU']!='') ? $pinfo['SKU'] : $pinfo['storeProductID']
                ,"N"
                ,''
            );
        }

        // Global Discount
        if ($data['Price']['SubtotalGlobalDiscount'] > 0)
            $trns_lines[] = array("SPL"
                ,"INVOICE"
                ,$order_date
                ,$this->_settings['ACC_GLOBAL_DISCOUNT']
                ,$order_name
                ,$this->_settings['TRNS_CLASS']
                ,$data['Price']['SubtotalGlobalDiscount']
                ,$order_id
                ,"GLOBAL_DISCOUNT"
                ,($data['Price']['SubtotalGlobalDiscount'])*(-1)
                ,-1
                ,"GLOBAL_DISCOUNT"
                ,"N"
                ,''
            );

        // Coupon (PromoCode) Discount
        if ($data['Price']['SubtotalPromoCodeDiscount'] > 0)
            $trns_lines[] = array("SPL"
                ,"INVOICE"
                ,$order_date
                ,$this->_settings['ACC_PROMOCODE_DISCOUNT']
                ,$order_name
                ,$this->_settings['TRNS_CLASS']
                ,$data['Price']['SubtotalPromoCodeDiscount']
                ,$order_id
                ,"COUPON_DISCOUNT"
                ,($data['Price']['SubtotalPromoCodeDiscount'])*(-1)
                ,-1
                ,"COUPON_DISCOUNT"
                ,"N"
                ,''
            );

        // Quantity Discount
        if ($data['Price']['QuantityDiscount'] > 0)
            $trns_lines[] = array("SPL"
                ,"INVOICE"
                ,$order_date
                ,$this->_settings['ACC_QUANTITY_DISCOUNT']
                ,$order_name
                ,$this->_settings['TRNS_CLASS']
                ,$data['Price']['QuantityDiscount']
                ,$order_id
                ,"WHOLESALE_DISCOUNT"
                ,($data['Price']['QuantityDiscount'])*(-1)
                ,-1
                ,"WHOLESALE_DISCOUNT"
                ,"N"
                ,''
            );

        // Shipping
        $trns_lines[] = array("SPL"
                ,"INVOICE"
                ,$order_date
                ,$this->_settings['ACC_SHIPPING']
                ,$order_name
                ,$this->_settings['TRNS_CLASS']
                ,($data['Price']['TotalShippingAndHandlingCost'])*(-1)
                ,$order_id
                ,$data['ShippingMethod']
                ,$data['Price']['TotalShippingAndHandlingCost']
                ,-1
                ,"SHIPPING"
                ,"N"
                ,''
        );

        // Taxes
        if (is_array($data['Price']['taxes']))
            foreach($data['Price']['taxes'] as $tax_info)
                if ($tax_info['value'] > 0)
                    $trns_lines[] = array("SPL"
                        ,"INVOICE"
                        ,$order_date
                        ,$this->_settings['ACC_TAX']
                        ,$order_name
                        ,$this->_settings['TRNS_CLASS']
                        ,($tax_info['value'])*(-1)
                        ,$order_id
                        ,$tax_info['name']
                        ,$tax_info['value']
                        ,-1
                        ,"TAX"
                        ,"N"
                        ,''
                    );

        // Magic tax line
        $trns_lines[] = array("SPL"
                ,"INVOICE"
                ,$order_date
                ,$this->_settings['ACC_TAX']
                ,$order_name
                ,$this->_settings['TRNS_CLASS']
                ,0
                ,$order_id
                ,"TAX"
                ,''
                ,''
                ,''
                ,"N"
                ,"AUTOSTAX"
        );
        $trns_lines[] = array("ENDTRNS");

        foreach($trns_lines as $line)
            $out_data .= implode("\t",$line)."\n";

	$out_data .= "\n";

        fwrite($this->_out_file_handler,$out_data);
    }

    /**
     *                  WriteData -                                 ,
     *                        . .
     *
     * @param ref array of strings $warnings -                      ,
     * @param ref array of strings $errors -              ,
     */
    function finishWork()
    {
        if($this->_out_file_handler != null)
            fclose($this->_out_file_handler);
    }


    /**
     *                                            -                           data writer
     */
    function loadWork()
    {
        if(modApiFunc('Session','is_set','DataWriterIIFSettings'))
        {
            $this->_settings = modApiFunc('Session','get','DataWriterIIFSettings');
            $this->_out_file_handler = fopen($this->_settings['out_file'],'a');
        };
    }

    /**
     *                 -
     */
    function clearWork()
    {
        modApiFunc('Session','un_set','DataWriterIIFSettings');
        $this->_settings = null;
        $this->_out_file_handler = null;
    }

    /**
     *                                             (   time-out  )
     */
    function saveWork()
    {
        if($this->_settings != null)
            modApiFunc('Session','set','DataWriterIIFSettings',$this->_settings);

        if($this->_out_file_handler != null)
            @fclose($this->_out_file_handler);
    }

    /**
     *                           ,
     * @param ref array of strings $warnings -                      ,
     * @param ref array of strings $errors -              ,
     */
/*    function checkState(&$warnings, &$errors)
    {
    }
*/

    var $_settings;
    var $_out_file_handler;
}

?>