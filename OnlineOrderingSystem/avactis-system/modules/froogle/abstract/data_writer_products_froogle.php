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
 *            tab-delimited                                     ,           Google Base.
 *                     :
 * froogle_export_file -          ,                                   .                           cache.
 * froogle_expires_date - expiration_date,   .              google base
 * froogle_location - location,   .              google base
 * froogle_payment_notes - payment_notes,   .              google base
 * froogle_payment_accepted - payment_accepted,   .              google base
 *
 * @author Egor Makarov, Ravil Garafutdinov
 */

loadClass('DataWriterDefault');
loadCoreFile('cstring.php');

class DataWriterProductsFroogle extends DataWriterDefault
{
    function DataWriterProductsFroogle()
    {

    }

    /*
     * this function gets a string field,
     * strips HTML tags, then strips \t symbols
     * and ';' symbols. And converts special characters to HTML symbols
     */
    function sterilizeTextField($s)
    {
        $str = new CString();
        $s = htmlspecialchars(str_replace(";", "", $str->mergeWhiteSpace($str->stripHTML($s))));
        return $s;
    }

    function initWork($settings)
    {
        $this->clearWork();

        $this->_settings = array(
            'froogle_export_file' => $settings['froogle_export_file'],
            'froogle_expires_date' => $settings['froogle_expires_date'],
            'froogle_location' => $settings['froogle_location'],
            'froogle_payment_notes' => $settings['froogle_payment_notes'],
            'froogle_payment_accepted' => $settings['froogle_payment_accepted'],
            'froogle_storefront_link' => $settings['froogle_storefront_link']
        );
        $this->_fileForExport = fopen($this->_settings['froogle_export_file'], "w");
        $this->_process_info['status'] = 'INITED';
        $this->_writeHeader();
    }

    function getGTIN($product_info)
    {
        $gtin = '';

        if(!empty($product_info["ProductUPC"])) $gtin = $product_info["ProductUPC"];
        if(!empty($product_info["ProductEAN"])) $gtin = $product_info["ProductEAN"];
        if(!empty($product_info["ProductJAN"])) $gtin = $product_info["ProductJAN"];
        if(!empty($product_info["ProductISBN"])) $gtin = $product_info["ProductISBN"];

        return $this->sterilizeTextField($gtin);
    }

    function doWork($data)
    {
        $str = new CString();

        foreach ($data as $i => $product)
        {
            $product_info = $product;
            $prodObj = new CProductInfo($product_info["ProductID"]);

            /*
            if($prodObj->getProductTagValue('Available', PRODUCTINFO_NOT_LOCALIZED_DATA) == PRODUCT_STATUS_OFFLINE)
                continue;

            $cats = $product_info["ProductAllCategoryPath"];
            if(!empty($cats) && is_array($cats) && !empty($cats[0]) && is_array($cats[0]))
            {
                foreach($cats[0] as $cat)
                {
                    $objCurrentCat = new CCategoryInfo($cat["id"]);
                    if($objCurrentCat->getCategoryTagValue('RecursiveStatus')==CATEGORY_STATUS_OFFLINE)
                        continue 2;
                }
            }
            */
     	    $product_entry = array();

            // Availablity
            if($prodObj->getProductTagValue('Available', PRODUCTINFO_NOT_LOCALIZED_DATA) == PRODUCT_STATUS_OFFLINE)
                $product_entry[] = FG_GOOGLE_PRODUCT_STATUS_OUT_OF_STOCK; //GOOGLE PRODUCT STATUS OUT OF STOCK
            else
                $product_entry[] = FG_GOOGLE_PRODUCT_STATUS_IN_STOCK;

            // id
            $product_entry[] = $product_info["ProductID"];
            $product_entry[] = $product_info["ProductTypeName"];

            // product_type
            // we have to delete ";"-symbol, as Google Base treats it as a delimiter
            if(!empty($product_info["ProductCategoryPath"]))
            {
                $categories = array();
                foreach ($product_info["ProductCategoryPath"] as $cat)
                {
                    $categories[] = $this->sterilizeTextField($cat["name"]);
                }
                array_shift($categories);
                $product_entry[] = implode(", ", $categories);
            }

            // title
            // we have to delete ";"-symbol, as Google Base treats it as a delimiter
            $product_entry[] = $this->sterilizeTextField($product_info["ProductName"]);

            // description
            // we have to delete ";"-symbol, as Google Base treats it as a delimiter
            $product_entry[] = $this->sterilizeTextField($product_info["ProductShortDescription"]);

            // price
            // sale price +           ,

            $options_modifiers = modApiFunc("Product_Options", "getModifiersOfDefaultCombination", "product", $product_info["ProductID"]);

            $price_modifier_summ = $options_modifiers['price'];
            $price_modifier_summ += $product_info["ProductSalePrice"];
            if($price_modifier_summ < 0)
               $price_modifier_summ = 0;
            $product_entry[] = number_format($price_modifier_summ, 2, ".", "");

            // condition
            $product_entry[] = "new";

            // link
            LayoutConfigurationManager::static_activate_cz_layout($this->_settings['froogle_storefront_link']);
            $request = new CZRequest();
            $request->setView('ProductInfo');
            $request->setAction('SetCurrentProduct');
            $request->setKey('prod_id',$product_info["ProductID"]);

            $product_entry[] = $request->getURL("", false, 'froogle');

            // image_link
            if(!empty($product_info["ProductLargeImageSrc"]))
                $product_entry[] = $product_info["ProductLargeImageSrc"];

            // expiration_date
            $product_entry[] = $this->_settings["froogle_expires_date"];

            // gtin
            $product_entry[] = $this->getGTIN($product_info);

            // brand
            $manufacturer_info = modApiFunc("Manufacturers", "getManufacturerInfo", $prodObj->getProductTagValue('Manufacturer', PRODUCTINFO_NOT_LOCALIZED_DATA));
            $product_entry[] = isset($manufacturer_info["manufacturer_name"]) ? $this->sterilizeTextField($manufacturer_info["manufacturer_name"]) : '';

            // mpn
            $product_entry[] = isset($product_info["ProductMPN"]) ? $this->sterilizeTextField($product_info["ProductMPN"]) : '';

            // google_product_category
            $product_entry[] = isset($product_info["ProductGpc"]) && $product_info["ProductGpc"] != getMsg('SYS','PRTYPE_VALUE_NOT_SELECTED') ? $product_info["ProductGpc"] : '';

            // gender
            $product_entry[] = isset($product_info["ProductGender"]) && $product_info["ProductGender"] != getMsg('SYS','PRTYPE_VALUE_NOT_SELECTED') ? $product_info["ProductGender"] : '';

            // age_group
            $product_entry[] = isset($product_info["ProductAgegroup"]) && $product_info["ProductAgegroup"] != getMsg('SYS','PRTYPE_VALUE_NOT_SELECTED') ? $product_info["ProductAgegroup"] : '';

            // apparel size
            $product_entry[] = isset($product_info["ProductApparelsize"]) ? $this->sterilizeTextField($product_info["ProductApparelsize"]) : '';

            // Apparel color
            $product_entry[] = isset($product_info["ProductApparelcolor"]) ? $this->sterilizeTextField($product_info["ProductApparelcolor"]) : '';

            // location
            if (isset($this->_settings["froogle_location"])
                && $this->_settings["froogle_location"] != null)
            {
                $product_entry[] = $this->sterilizeTextField($this->_settings["froogle_location"]);
            }

            // payment_notes
            if (isset($this->_settings["froogle_payment_notes"])
                && $this->_settings["froogle_payment_notes"] != null)
            {
                $product_entry[] = $this->sterilizeTextField($this->_settings["froogle_payment_notes"]);
            }

            // payment_accepted
            if (isset($this->_settings["froogle_payment_accepted"])
                && $this->_settings["froogle_payment_accepted"] != null)
            {
                $product_entry[] = $this->sterilizeTextField($this->_settings["froogle_payment_accepted"]);
            }

            $result = implode("\t", $product_entry);
            fwrite($this->_fileForExport, $result);
            fwrite($this->_fileForExport, "\n");
        }
    }

    function finishWork()
    {
    	if ($this->_fileForExport !== NULL)
            fclose($this->_fileForExport);
    }


    function loadWork()
    {
    	if (TRUE == modApiFunc("Session", "is_Set", "FroogleExportSettings"))
        {
        	$this->_settings = modApiFunc("Session", "get", "FroogleExportSettings");
            $this->_fileForExport = fopen($this->_settings['froogle_export_file'], "a");
        }
        else
        {
        	$this->_settings = NULL;
        }
    }

    function clearWork()
    {
        modApiFunc("Session", "un_Set", "FroogleExportSettings");
    }

    function saveWork()
    {
        if (NULL !== $this->_settings)
        {
            modApiFunc("Session", "set", "FroogleExportSettings", $this->_settings);
            @fclose($this->_fileForExport);
        }
        else
        {
            modApiFunc("Session", "un_Set", "FroogleExportSettings");
        }
    }

    /**
     *                                                               .
     * id product_type title description price condition link image_link expiration_date ?location ?payment_notes ?payment_accepted
     */
    function _writeHeader()
    {
    	fwrite($this->_fileForExport, "availability\tid\tproduct_type\ttitle\tdescription\tprice\tcondition\tlink\timage_link\texpiration_date\tgtin\tbrand\tmpn\tgoogle_product_category\tgender\tage_group\tsize\tcolor");

        if (isset($this->_settings["froogle_location"]) && $this->_settings["froogle_location"] != null)
        {
        	fwrite($this->_fileForExport, "\tlocation");
        }
        if (isset($this->_settings["froogle_payment_notes"]) && $this->_settings["froogle_payment_notes"] != null)
        {
        	fwrite($this->_fileForExport, "\tpayment_notes");
        }
        if (isset($this->_settings["froogle_payment_accepted"]) && $this->_settings["froogle_payment_accepted"] != null)
        {
            fwrite($this->_fileForExport, "\tpayment_accepted");
        }
        fwrite($this->_fileForExport, "\n");
    }

    var $_fileForExport;

    var $_settings;
}
?>