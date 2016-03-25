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
 * Promo Codes Navigator.
 *
 * @package PromoCodes
 * @access  public
 * @author  Vadim Lyalikov
 */
class PromoCodesNavigationBar
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
    function PromoCodesNavigationBar()
    {
        global $application;

        modAPIFunc('Paginator', 'setCurrentPaginatorName', "PromoCodes_List");

        $codes = modApiFunc('PromoCodes', 'getPromoCodesNumber');

        $session = &$application->getInstance('Session');

        if ($session->is_Set('PromoCodeAdded'))
        {
            modApiFunc('Paginator', 'setPaginatorPageToLast','PromoCodes_List', $codes);
            $session->un_Set('PromoCodeAdded');
        }

        $this->mTmplFiller = &$application->getInstance('TmplFiller');
    }

    function getLinkToPromoCodesAddPromoCode()
    {
        $_request = new Request();
        $_request->setView  ( 'AddPromoCode' );

        global $application;
        return $_request->getURL();
    }

    /**
     * Return the PromoCode Navigator list view.
     *
     * @ finish the functions on this page
     */
    function outputPromoCodesList()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');

        # Get CPromoCodeInfo object list
        $PromoCodesList = modApiFunc('PromoCodes', 'getPromoCodesListFullAZ', null, true);

        $this->PromoCodesSize = sizeof($PromoCodesList);
        $retval = "";
        $count = 0;
        foreach ($PromoCodesList as $key => $value)
        {
            $checked = "";
            $available = modApiFunc("PromoCodes", "isPromoCodeApplicableWithoutMinSubtotal", $value["id"]);
            $area_not_empty = modApiFunc("PromoCodes", "isPromoCodeEffectiveAreaNotEmpty", $value["id"]);
            $this->_Current_PromoCode = &$value;
            $this->_Current_PromoCode["cssstyle"] = ($available === true && $area_not_empty === true) ? "color: black" : "color: #AAAAAA";
            $request = new Request();
            $request->setView  ( 'EditPromoCode' );
            $request->setAction( 'SetEditablePromoCode' );
            $request->setKey   ( 'PromoCode_id', $value["id"]);
            $href = $request->getURL();
            $this->_Current_PromoCode["editpromo_codehref"] = $href;

            /*
            if ($value['ID'] == modApiFunc('PromoCodes', 'getEditablePromoCodeID'))
            {
                $checked = 'CHECKED';
            }
            $this->_Current_PromoCode['Name'] = $checked == 'CHECKED' ?
                        '<span class="required">'. prepareHTMLDisplay($value["Name"]) .'</span>'
                        : prepareHTMLDisplay($value["Name"]);
            */
//            $this->_Current_PromoCode->setAdditionalPromoCodeTag('Name', $value->getPromoCodeTagValue('Name'));
//            $this->_Current_PromoCode->setAdditionalPromoCodeTag('ID', $value->getPromoCodeTagValue('ID'));
//            $this->_Current_PromoCode->setAdditionalPromoCodeTag('Checked', $checked);
//            $this->_Current_PromoCode->setAdditionalPromoCodeTag('Link', $this->getLinkToPromoCodesNavigator($value->getPromoCodeTagValue('ID')));
//print_r($this->_Current_PromoCode);die();
            $tags = array("PromoCodeId" => "",
                          "PromoCodeMin_Subtotal" => "",
                          "PromoCodePromo_Code" => "",
                          "PromoCodeCampaign_Name" => "",
                          "PromoCodeB_Ignore_Other_Discounts" => "",
                          "PromoCodeStatus" => "",
                          "PromoCodeDiscounted_Items_Qty" => "",
                          "PromoCodeDiscount_Cost" => "",
                          "PromoCodeDiscount_Cost_Type_ID" => "",
                          "PromoCodeStart_Date" => "",
                          "PromoCodeEnd_Date" => "",
                          "PromoCodeTimes_To_Use" => "",
                          "PromoCodeTimes_Used" => "",
                          "PromoCodeCSSStyle" => "",
                          "PromoCodeEditPromo_CodeHref" => ""
                         );
            $application->registerAttributes($tags);
            $application->registerAttributes($this->_Current_PromoCode);//->getAdditionalPromoCodeTagList());
            $retval .= $this->mTmplFiller->fill("promo_codes/promo_codes_navigator/", "list_item.tpl.html", array());

            $count++;
        }

        $min_list_size = 10;
        if($count== 0)
        {
            $retval .= $this->mTmplFiller->fill("promo_codes/promo_codes_navigator/", "list_item_empty_na_values.tpl.html", array());
            $count++;
        }

        for(;$count < $min_list_size; $count++)
        {
            $retval .= $this->mTmplFiller->fill("promo_codes/promo_codes_navigator/", $count == $min_list_size -1 ? "list_bottom_item_empty.tpl.html" : "list_item_empty.tpl.html", array());
        }

        modApiFunc('PromoCodes', 'unsetEditablePromoCodeID');
        return $retval;
    }

    /**
     * Return the PromoCodes Navigator view.
     *
     * @ finish the functions on this page
     */
    function output()
    {
        global $application;

        # Get CPromoCodeInfo object list
        $PromoCodesList = modApiFunc('PromoCodes', 'getPromoCodesListFullAZ');
        $this->PromoCodesSize = sizeof($PromoCodesList);

        $application->registerAttributes(array(
             'PromoCode_CurrentPath'
            ,'AddPromoCodeHref'
            ,'DelPromoCodeHref'
            ,'EditPromoCodeHref'
            ,'SortPromoCodeHref'
            ,'AlertMessage'
            ,'PaginatorLine'
            ,'PaginatorRows'

        ));
        $retval = $this->mTmplFiller->fill("promo_codes/promo_codes_navigator/", "list.tpl.html", array());
        return $retval;
    }

    /**
     * @                      PromoCodesNavigationBar->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
        	case 'Items':
        		$value = $this->outputPromoCodesList();
        		break;

            case 'AddPromoCodeHref':
                $request = new Request();
                $request->setView  ( 'AddPromoCode' );
                $value = $request->getURL();
        	    break;

        	case 'DelPromoCodeHref':
                $request = new Request();
                $request->setView  ( 'PromoCodesNavigationBar' );
                $request->setAction( 'DelPromoCodeInfo' );
                $request->setKey   ( 'PromoCode_id', '');
                $value = $request->getURL();
        	    break;

        	case 'EditPromoCodeHref':
                $request = new Request();
                $request->setView  ( 'EditPromoCode' );
                $request->setAction( 'SetEditablePromoCode' );
                $request->setKey   ( 'PromoCode_id', '');
                $value = $request->getURL();
        	    break;

                # paginator
                case 'PaginatorLine':
                $obj = &$application->getInstance($tag);
                $value = $obj->output("PromoCodes_List", "PromoCodesNavigationBar");
                break;

                # override the PaginatorRows tag behavior
                case 'PaginatorRows':
                $obj = &$application->getInstance($tag);
                $value = $obj->output("PromoCodes_List", "PromoCodesNavigationBar");
                break;


            case 'AlertMessage':
                $MessageResources = &$application->getInstance('MessageResources',"promo-codes-messages", "AdminZone");
                if ($this->PromoCodesSize==0)
                {
                    $err_mes = new ActionMessage(array('NB_009'));
                }
                else
                {
                    $err_mes = new ActionMessage(array('NB_006'));
                }
                $value = "'".$MessageResources->getMessage($err_mes)."'";
                break;

            case 'PromoCodeDiscounted_Items_Qty':
                if(!empty($this->_Current_PromoCode))
                {
                    $cats = explode('|', $this->_Current_PromoCode['cats']);
                    $prods = explode('|', $this->_Current_PromoCode['prods']);
                    $cats_num  = !(isset($cats[0])  && $cats[0] != NULL)  ? '0' : count($cats);
                    $prods_num = !(isset($prods[0]) && $prods[0] != NULL) ? '0' : count($prods);
                    $value = $cats_num . '/' . $prods_num;
                }
                break;

        	default:
        	    if (_ml_strpos($tag, 'PromoCode') === 0)
        	    {
                    $tag = _ml_substr($tag, _ml_strlen('PromoCode'));
        	    }
        	    $tag = _ml_strtolower($tag);
                if(!empty($this->_Current_PromoCode))
                {
                    if (isset($this->_Current_PromoCode[$tag]))
                    {
                        $value = $this->_Current_PromoCode[$tag];
                        //             ,              ,           .
                    	switch($tag)
                        {
                        	case "promo_code":
                            {
                            	$value = prepareHTMLDisplay($value);
                                break;
                            }
                            case "editpromo_codehref":
                            {
                                break;
                            }
                            case "campaign_name":
                            {
                                $value = prepareHTMLDisplay($value);
                                break;
                            }
                        	case "min_subtotal":
                        	{
                        		$value = modApiFunc("Localization", "currency_format", $value);
                        		break;
                        	}
                        	case "start_date":
                        	{
                        		$value = modApiFunc("Localization", "date_format", $value, false);
                        		break;
                        	}
                        	case "end_date":
                        	{
                        		$value = modApiFunc("Localization", "date_format", $value, false);
                        		break;
                        	}
                        	case "status":
                        	{
                        		switch($value)
                        	    {
                        	        case 1:
                        	        {
                        	        	$value = getMsg('PROMOCODES', "PROMOCODES_MODULE_STATUS_ACTIVE");
                        	        	break;
                        	        }
                        	        case 2:
                        	        {
                        	        	$value = getMsg('PROMOCODES', "PROMOCODES_MODULE_STATUS_INACTIVE");
                        	        	break;
                        	        }
                        	        default:
                        	        {
                        	        	//: report error;
                        	        	break;
                        	        }
                        	    }
                        		break;
                        	}
                        	case "b_ignore_other_discounts":
                        	{
                        		switch($value)
                        	    {
                        	        case 1:
                        	        {
                        	        	$value = getMsg('PROMOCODES', "PROMOCODES_MODULE_IGNORE_OTHER_DISCOUNTS_TEXT_YES");
                        	        	break;
                        	        }
                        	        case 2:
                        	        {
                        	        	$value = getMsg('PROMOCODES', "PROMOCODES_MODULE_IGNORE_OTHER_DISCOUNTS_TEXT_NO");
                        	        	break;
                        	        }
                        	        default:
                        	        {
                        	        	//: report error;
                        	        	break;
                        	        }
                        	    }
                        	    break;
                        	}
                        	case "discount_cost":
                        	{
                        		if(isset($this->_Current_PromoCode["discount_cost_type_id"]))
                        		{
                                    switch($this->_Current_PromoCode["discount_cost_type_id"])
                                    {
                                    	case 1 /* FLAT RATE */:
                                    	{
                                    		$value = modApiFunc("Localization", "currency_format", $value);
                                    		break;
                                    	}
                                    	case 2 /* PERCENT */:
                                    	{
                                    		$value = modApiFunc("Localization", "num_format", $value) . "%";
                                    		break;
                                    	}
                                    	default:
                                    	{
                                    		//: report error.
                                    		exit(1);
                                    	}
                                    }
                        		}
                        		else
                        		{
                        			//: report error;
                        			exit(1);
                        		}
                        		break;
                        	}
                        	default:
                        	{
                        		break;
                        	}
                        }
    	            }
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

    /**
     * Pointer to the template filler object.
     * Needs to track sequences of identical templates, like lists.
     */
    var $mTmplFiller;

    var $_Current_PromoCode = array();
    /**#@-*/

}
?>