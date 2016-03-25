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
 * Configuration Module, GeneralSettings View.
 *
 * @package Configuration
 * @author Alexey Florinsky
 */
class GeneralSettings
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * AvactisHomeNews constructor.
     */
    function GeneralSettings()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');
    }

    /**
     *
     */
    function output()
    {
        global $application;

        $application->registerAttributes(array(
            'GSOnline'
           ,'GSOfflineKey'
           ,"GSStoreURL"
           ,'GSShowAbsent'
           ,'GSAllowBuyMoreThanStock'
           ,"GSReturnProductToStockOrderDeleted"
           ,"GSReturnProductToStockOrderCancelled"
           ,"GSEnableWishList"
           ,'GSTime'
           ,'GSTimeShift'
           ,'GSSignCount'
           ,'GSShowCart'
           ,'GSSignTimeout'
           ,'GSMinSubtotalToCheckout'
           ,'GSPaginatorDefaultRowsPerPageAZ'
           ,'GSPaginatorPagesPerLineAZ'
           ,'GSPaginatorRowsPerPageValuesAZ'
           ,'GSPaginatorDefaultRowsPerPageCZ'
           ,'GSPaginatorPagesPerLineCZ'
           ,'GSPaginatorRowsPerPageValuesCZ'
           ,'GSAddToCartDefaultQuantity'
           ,'GSAddToCartMaxQuantity'
           ,'GSAddToCartLimitMaxQuantityByStock'
           ,'GSADisplayProductPriceIncludingTaxes'
           ,'GSHideStateTextInput'
           ,'GSAddToCartAddNotReplace'
           ,'GSNextOrderId'
           ,'InputControlName'
           ,'InputControlValue'
           ,'InputControlSize'
           ,'InputControlLabel'
           ,'InputControlOptions'
           ,'InputControlChecked'
           ,'ElementEvent'
           ,'DependentControl'

           ,'RPperLine'
           ,'RP_RandomCheckbox'
           ,'RP_RandomThreshold'
           ,'FPperLine'
           ,'FP_RandomCheckbox'
           ,'FP_RandomThreshold'
           ,'BSperLine'
           ,'BS_RandomCheckbox'
           ,'BS_RandomThreshold'

           ,'ResultMessageRow'
           ,'ResultMessage'

           ,'FormErrors'
           ,'ErrorIndex'
           ,'Error'
        ));

        $obj = &$application->getInstance('MessageResources');
        $output = modApiFunc('TmplFiller', 'fill',
                              './../../js/','validate.msgs.js.tpl',
                              array(
                                    "INTEGER" => $obj->getMessage( new ActionMessage(array('PRDADD_001')) )
                                   ,"FLOAT"   => $obj->getMessage( new ActionMessage(array('PRDADD_002')) )
                                   ,"STRING1024"=> $obj->getMessage( new ActionMessage(array('PRDADD_007')) )
                                   ,"STRING128"=> $obj->getMessage( new ActionMessage(array('PRDADD_008')) )
                                   ,"STRING256"=> $obj->getMessage( new ActionMessage(array('PRDADD_009')) )
                                   ,"STRING512"=> $obj->getMessage( new ActionMessage(array('PRDADD_010')) )
                                   ,"CURRENCY"=> addslashes($obj->getMessage( new ActionMessage(array('CURRENCY_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 12.35, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.00, "currency")))))
                                   ,"WEIGHT"   => addslashes($obj->getMessage( new ActionMessage(array('WEIGHT_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 23.325, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.2, "weight")))))
                                   ,"ITEM"     => $obj->getMessage( new ActionMessage(array('ITEM_FIELD')))
                                   )
                            );
        return $output.modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","container.tpl.html", array());
    }

    function getErrors()
    {
        $this->ErrorsArray = array();

        if(modApiFunc("Session","is_set","form_errors"))
            $this->ErrorsArray = modApiFunc("Session","get","form_errors");

        if(empty($this->ErrorsArray))
            return false;

        $result = "";
        $this->_error_index = 0;
        foreach ($this->ErrorsArray as $error)
        {
            $this->_error_index++;
            $this->_error = $this->MessageResources->getMessage($error);
            $result .= modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","error.tpl.html", array());
        }

        $this->ErrorsArray = modApiFunc("Session","un_set","form_errors");
    	return $result;
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('SYS',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("configuration/general_settings/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
    }

	function getTag($tag)
	{
		global $application;
		$value = null;
		switch ($tag)
		{
		    case 'GSOnline':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_ONLINE
		           ,'size' => 1
		           ,'selected' => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_ONLINE)
		           ,'options' => array(
		                '1' => $this->MessageResources->getMessage("GNRL_SET_ONLINE_LABEL")
		               ,'0' => $this->MessageResources->getMessage("GNRL_SET_CLOSED_LABEL")
		           )
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","select-box.tpl.html", array());
		        break;

		    case 'GSOfflineKey':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_OFFLINE_KEY
		           ,'value' => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OFFLINE_KEY)
		           ,'size' => 20
                   ,'event' => "id=\"store_offline_key\" onkeyup=\"javascript: showOfflineKey(this);\" onfocus=\"javascript: showOfflineKey(this);\""
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","text-field.tpl.html", array());
		        break;

		    case "GSStoreURL":
		        $i=0;
		        $value = "";
		        $config_array = LayoutConfigurationManager::static_get_cz_layouts_list();
                foreach($config_array as $k => $v)
                {
                    $value .= "        url[$i] = \"" . $v["SITE_URL"] . "index.php?\" + key;\n";
                    $i++;
                }
                $value = "        i=$i;\n" . $value;

		        break;

		    case 'GSTime':
		        $value = modApiFunc("Localization", "timestamp_time_format", time()).", ".modApiFunc("Localization", "timestamp_date_format", time());
		        break;

		    case 'GSTimeShift':
		        $hours = array();
		        for ($h = -24; $h <= 24; $h++) $hours[$h] = $h;
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_TIME_SHIFT
		           ,'size' => 1
		           ,'selected' => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_TIME_SHIFT)
		           ,'options' => $hours
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","select-box.tpl.html", array());
		        break;

		    case 'GSSignCount':
                $SignInCount = modApiFunc("Users", "getIncorrectLoginTimeCountArray");
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_SIGNIN_COUNT
		           ,'size' => 1
		           ,'selected' => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_SIGNIN_COUNT)
		           ,'options' => $SignInCount
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","select-box.tpl.html", array());
		        break;

		    case 'GSSignTimeout':
                $Timeout = modApiFunc("Users", "getLoginBlockTimeArray");
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_SIGNIN_TIMEOUT
		           ,'size' => 1
		           ,'selected' => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_SIGNIN_TIMEOUT)
		           ,'options' => $Timeout
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","select-box.tpl.html", array());
		        break;

		    case 'GSShowAbsent':
		        $current = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_SHOW_ABSENT);
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_SHOW_ABSENT
		           ,'value' => STORE_SHOW_ABSENT_SHOW_BUY
		           ,'label' => $this->MessageResources->getMessage("GNRL_SET_SHOW_ABSENT_SHOW_BUY_LABEL")
		           ,'checked' => $current == STORE_SHOW_ABSENT_SHOW_BUY
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","radio-box.tpl.html", array());
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_SHOW_ABSENT
		           ,'value' => STORE_SHOW_ABSENT_SHOW_NOT_BUY
		           ,'label' => $this->MessageResources->getMessage("GNRL_SET_SHOW_ABSENT_SHOW_NOT_BUY_LABEL")
		           ,'checked' => $current == STORE_SHOW_ABSENT_SHOW_NOT_BUY
		        );
                $value .= modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","radio-box.tpl.html", array());
                $this->_input_control = array(
                    'name' => SYSCONFIG_STORE_SHOW_ABSENT
                   ,'value' => STORE_SHOW_ABSENT_NOT_SHOW_NOT_BUY
                   ,'label' => $this->MessageResources->getMessage("GNRL_SET_SHOW_ABSENT_NOT_SHOW_NOT_BUY_LABEL")
                   ,'checked' => $current == STORE_SHOW_ABSENT_NOT_SHOW_NOT_BUY
                );
                $value .= modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","radio-box.tpl.html", array());
		        break;

		    case 'GSAllowBuyMoreThanStock':
                $this->_input_control = array(
                    'name' => SYSCONFIG_STORE_ALLOW_BUY_MORE_THAN_STOCK
                   ,'size' => 1
                   ,'selected' => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_ALLOW_BUY_MORE_THAN_STOCK)
                   ,'options' => array(
                        '1' => $this->MessageResources->getMessage("GNRL_SET_YES_LABEL")
                       ,'0' => $this->MessageResources->getMessage("GNRL_SET_NO_LABEL")
                   )
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","select-box.tpl.html", array());
                break;

		    case "GSReturnProductToStockOrderDeleted":
                $this->_input_control = array(
                    'name' => SYSCONFIG_STORE_RETURN_PRODUCT_TO_STOCK_ORDER_DELETED
                   ,'size' => 1
                   ,'selected' => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_RETURN_PRODUCT_TO_STOCK_ORDER_DELETED)
                   ,'options' => array(
                        '1' => $this->MessageResources->getMessage("GNRL_SET_YES_LABEL")
                       ,'0' => $this->MessageResources->getMessage("GNRL_SET_NO_LABEL")
                   )
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","select-box.tpl.html", array());
		        break;

		    case "GSReturnProductToStockOrderCancelled":
                $this->_input_control = array(
                    'name' => SYSCONFIG_STORE_RETURN_PRODUCT_TO_STOCK_ORDER_CANCELLED
                   ,'size' => 1
                   ,'selected' => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_RETURN_PRODUCT_TO_STOCK_ORDER_CANCELLED)
                   ,'options' => array(
                        '1' => $this->MessageResources->getMessage("GNRL_SET_YES_LABEL")
                       ,'0' => $this->MessageResources->getMessage("GNRL_SET_NO_LABEL")
                   )
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","select-box.tpl.html", array());
                break;

		    case "GSEnableWishList":
                $this->_input_control = array(
                    'name' => SYSCONFIG_STORE_ENABLE_WISHLIST
                   ,'size' => 1
                   ,'selected' => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_ENABLE_WISHLIST)
                   ,'options' => array(
                        '1' => $this->MessageResources->getMessage("GNRL_SET_YES_LABEL")
                       ,'0' => $this->MessageResources->getMessage("GNRL_SET_NO_LABEL")
                   )
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","select-box.tpl.html", array());
                break;

            case 'GSShowCart':
                       $current = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_SHOW_CART);
                       $this->_input_control = array(
                           'name' => SYSCONFIG_STORE_SHOW_CART
                          ,'value' => 1
                          ,'label' => $this->MessageResources->getMessage("GNRL_SET_ENABLED_LABEL")
                          ,'checked' => $current
                       );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","radio-box.tpl.html", array());
                       $this->_input_control = array(
                           'name' => SYSCONFIG_STORE_SHOW_CART
                          ,'value' => 0
                          ,'label' => $this->MessageResources->getMessage("GNRL_SET_DISABLED_LABEL")
                          ,'checked' => !$current
                       );
                $value .= modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","radio-box.tpl.html", array());
                break;
            case 'GSMinSubtotalToCheckout':
                $this->_input_control = array(
                    'name' => SYSCONFIG_MIN_SUBTOTAL_TO_BEGIN_CHECKOUT
                   ,'value' => modApiFunc("Localization", "FloatToFormatStr", modApiFunc('Configuration', 'getValue', SYSCONFIG_MIN_SUBTOTAL_TO_BEGIN_CHECKOUT), "currency")
                   ,'size' => 10
                   ,'event' => modApiFunc("Localization", "format_settings_for_js", "currency") . " onblur=\"formatInput(this);\" patterntype=\"currency\""
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","text-field.tpl.html", array());
                break;

            case 'GSPaginatorPagesPerLineAZ':
            case 'GSPaginatorPagesPerLineCZ':
                if ($tag == 'GSPaginatorPagesPerLineAZ')
                {
                    $const_pages_per_line = SYSCONFIG_STORE_PAGINATOR_PAGES_PER_LINE_AZ;
                }
                else
                {
                    $const_pages_per_line = SYSCONFIG_STORE_PAGINATOR_PAGES_PER_LINE_CZ;
                }

		        $this->_input_control = array(
		            'name' => $const_pages_per_line
		           ,'value' => modApiFunc('Configuration', 'getValue', $const_pages_per_line)
		           ,'size' => 5
                   ,'event' => ""
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","text-field.tpl.html", array());
		        break;
            case 'GSPaginatorDefaultRowsPerPageAZ':
            case 'GSPaginatorDefaultRowsPerPageCZ':
                if ($tag == 'GSPaginatorDefaultRowsPerPageAZ')
                {
                    $const_rows_per_page = SYSCONFIG_STORE_PAGINATOR_DEFAULT_ROWS_PER_PAGE_AZ;
                    $const_rows_per_page_values = SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_AZ;
                }
                else
                {
                    $const_rows_per_page = SYSCONFIG_STORE_PAGINATOR_DEFAULT_ROWS_PER_PAGE_CZ;
                    $const_rows_per_page_values = SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_CZ;
                }
                $rows_per_page_values = unserialize(modApiFunc('Configuration', 'getValue', $const_rows_per_page_values));
                $rows_per_page_array = array();
                foreach ($rows_per_page_values as $val)
                {
                    $rows_per_page_array[$val] = $val;
                }
                $this->_input_control = array(
                    'name' => $const_rows_per_page
                   ,'size' => 1
                   ,'selected' => modApiFunc('Configuration', 'getValue', $const_rows_per_page)
                   ,'options' => $rows_per_page_array
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","select-box.tpl.html", array());
                break;

            case 'GSPaginatorRowsPerPageValuesAZ':
            case 'GSPaginatorRowsPerPageValuesCZ':
                if ($tag == 'GSPaginatorRowsPerPageValuesAZ')
                {
                    $const_rows_per_page = SYSCONFIG_STORE_PAGINATOR_DEFAULT_ROWS_PER_PAGE_AZ;
                    $const_rows_per_page_values = SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_AZ;
                }
                else
                {
                    $const_rows_per_page = SYSCONFIG_STORE_PAGINATOR_DEFAULT_ROWS_PER_PAGE_CZ;
                    $const_rows_per_page_values = SYSCONFIG_STORE_PAGINATOR_ROWS_PER_PAGE_VALUES_CZ;
                }
                $rows_per_page_values = unserialize(modApiFunc('Configuration', 'getValue', $const_rows_per_page_values));
                $rows_per_page_array = array();
                foreach ($rows_per_page_values as $val)
                {
                    $rows_per_page_array[$val] = $val;
                }
                $this->_input_control = array(
                    'name' => $const_rows_per_page_values
                   ,'size' => sizeof($rows_per_page_array)>3? sizeof($rows_per_page_array):3
                   ,'selected' => ""
                   ,'options' => $rows_per_page_array
                   ,'value' => implode("|", $rows_per_page_array)
                   ,'dependent_cntrol' => $const_rows_per_page
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","select-box-and-text-field.tpl.html", array());
                break;

            case 'GSAddToCartDefaultQuantity':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_ADD_TO_CART_DEFAULT_QUANTITY
		           ,'value' => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_ADD_TO_CART_DEFAULT_QUANTITY)
		           ,'size' => 5
                   ,'event' => ""
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","text-field.tpl.html", array());
		        break;

            case 'GSAddToCartMaxQuantity':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_ADD_TO_CART_MAX_QUANTITY
		           ,'value' => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_ADD_TO_CART_MAX_QUANTITY)
		           ,'size' => 5
                   ,'event' => ""
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","text-field.tpl.html", array());
		        break;

		    case 'GSAddToCartLimitMaxQuantityByStock':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_ADD_TO_CART_LIMIT_MAX_QUANTITY_BY_STOCK
		           ,'size' => 1
		           ,'selected' => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_ADD_TO_CART_LIMIT_MAX_QUANTITY_BY_STOCK)
		           ,'options' => array(
		                '1' => $this->MessageResources->getMessage("GNRL_SET_YES_LABEL")
		               ,'0' => $this->MessageResources->getMessage("GNRL_SET_NO_LABEL")
		           )
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","select-box.tpl.html", array());
		        break;

		    case 'GSAddToCartAddNotReplace':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_ADD_TO_CART_ADD_NOT_REPLACE
		           ,'size' => 1
		           ,'selected' => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_ADD_TO_CART_ADD_NOT_REPLACE)
		           ,'options' => array(
		                '1' => $this->MessageResources->getMessage("GNRL_SET_FIELD_017_ADD")
		               ,'0' => $this->MessageResources->getMessage("GNRL_SET_FIELD_017_REPLACE")
		           )
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","select-box.tpl.html", array());
		        break;

            case 'GSNextOrderId':
                $this->_input_control = array(
                    'name' => SYSCONFIG_STORE_NEXT_ORDER_ID
                   ,'value' => modApiFunc('Checkout', 'getNextOrderId')
                   ,'size' => 10
                   ,'event' => ""
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","text-field.tpl.html", array());
                break;

		    case 'InputControlName': $value = $this->_input_control['name']; break;
		    case 'InputControlValue': $value = $this->_input_control['value']; break;
		    case 'InputControlLabel': $value = $this->_input_control['label']; break;
		    case 'InputControlSize': $value = $this->_input_control['size']; break;
		    case 'InputControlChecked': $value = ($this->_input_control['checked'] ? " checked" : ""); break;
            case 'ElementEvent': $value = (isset($this->_input_control['event'])? $this->_input_control['event']:"");break;
            case 'DependentControl': $value = (isset($this->_input_control['dependent_cntrol'])? $this->_input_control['dependent_cntrol']:"");break;
		    case 'InputControlOptions':
		        $value = "";
		        foreach ($this->_input_control['options'] as $key => $label)
		        {
		            $selected = "";
		            if (isset($this->_input_control['selected']) && $this->_input_control['selected'] == $key)
		            {
		            	$selected = " selected";
		            }
		        	$value .= "<option value=\"$key\"$selected>$label</option>";
		        }
		        break;

//================== related products ===============================
            case 'RPperLine':
                $this->_input_control = array(
                    'name' => SYSCONFIG_RP_PER_LINE
                   ,'value' => modApiFunc('Configuration', 'getValue', SYSCONFIG_RP_PER_LINE)
                   ,'size' => 10
                   ,'event' => ""
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","text-field.tpl.html", array());
                break;

            case 'RP_RandomCheckbox':
                $checked = modApiFunc('Configuration', 'getValue', SYSCONFIG_RP_RANDOM_CHECKBOX);
                $this->_input_control = array(
                    'name' => SYSCONFIG_RP_RANDOM_CHECKBOX
                   ,'checked' => $checked
                   ,'value' => 'on'
                   ,'label' => ""
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","check-box.tpl.html", array());
                break;

            case 'RP_RandomThreshold':
                $this->_input_control = array(
                    'name' => SYSCONFIG_RP_RANDOM_THRESHOLD
                   ,'value' => modApiFunc('Configuration', 'getValue', SYSCONFIG_RP_RANDOM_THRESHOLD)
                   ,'size' => 6
                   ,'event' => ""
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","text-field.tpl.html", array());
                break;
//================== related products ===============================

//================== featured products ===============================
            case 'FPperLine':
                $this->_input_control = array(
                    'name' => SYSCONFIG_FP_PER_LINE
                   ,'value' => modApiFunc('Configuration', 'getValue', SYSCONFIG_FP_PER_LINE)
                   ,'size' => 10
                   ,'event' => ""
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","text-field.tpl.html", array());
                break;

            case 'FP_RandomCheckbox':
                $checked = modApiFunc('Configuration', 'getValue', SYSCONFIG_FP_RANDOM_CHECKBOX);
                $this->_input_control = array(
                    'name' => SYSCONFIG_FP_RANDOM_CHECKBOX
                   ,'checked' => $checked
                   ,'value' => 'on'
                   ,'label' => ""
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","check-box.tpl.html", array());
                break;

            case 'FP_RandomThreshold':
                $this->_input_control = array(
                    'name' => SYSCONFIG_FP_RANDOM_THRESHOLD
                   ,'value' => modApiFunc('Configuration', 'getValue', SYSCONFIG_FP_RANDOM_THRESHOLD)
                   ,'size' => 6
                   ,'event' => ""
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","text-field.tpl.html", array());
                break;
//================== featured products ===============================

//================== bestsellers ===============================
            case 'BSperLine':
                $this->_input_control = array(
                    'name' => SYSCONFIG_BS_PER_LINE
                   ,'value' => modApiFunc('Configuration', 'getValue', SYSCONFIG_BS_PER_LINE)
                   ,'size' => 10
                   ,'event' => ""
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","text-field.tpl.html", array());
                break;

            case 'BS_RandomCheckbox':
                $checked = modApiFunc('Configuration', 'getValue', SYSCONFIG_BS_RANDOM_CHECKBOX);
                $this->_input_control = array(
                    'name' => SYSCONFIG_BS_RANDOM_CHECKBOX
                   ,'checked' => $checked
                   ,'value' => 'on'
                   ,'label' => ""
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","check-box.tpl.html", array());
                break;

            case 'BS_RandomThreshold':
                $this->_input_control = array(
                    'name' => SYSCONFIG_BS_RANDOM_THRESHOLD
                   ,'value' => modApiFunc('Configuration', 'getValue', SYSCONFIG_BS_RANDOM_THRESHOLD)
                   ,'size' => 6
                   ,'event' => ""
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/general_settings/","text-field.tpl.html", array());
                break;
//================== bestsellers ===============================

            case 'ResultMessageRow':
            	$value = $this->outputResultMessage();
            	break;
            case 'ResultMessage':
            	$value = $this->_Template_Contents['ResultMessage'];
            	break;

//================== errors ===============================
            case 'FormErrors':
                $value = $this->getErrors();
                break;

            case 'ErrorIndex':
                $value = $this->_error_index;
                break;

            case 'Error':
                $value = $this->_error;
                break;


		    default:
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

	var $_input_control;

    /**#@-*/

}
?>