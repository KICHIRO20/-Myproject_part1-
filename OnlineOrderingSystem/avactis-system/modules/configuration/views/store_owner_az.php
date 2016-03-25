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
 * Configuration Module, Store Owner View.
 *
 * @package Configuration
 * @author Alexey Florinsky
 */
class StoreOwner
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
    function StoreOwner()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');

        $this->copyFormData = false;
        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->SessionPost = modApiFunc("Session", "get", "SessionPost");
            $this->copyFormData = true;
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
    }

    /**
     *
     */
    function output()
    {
        global $application;

        $application->registerAttributes(array(
            'StoreOwnerName'
           ,'StoreOwnerWebsite'
           ,'StoreOwnerPhones'
           ,'StoreOwnerFax'
           ,'StoreOwnerStreetLine1'
           ,'StoreOwnerStreetLine2'
           ,'StoreOwnerCity'
           ,'StoreOwnerState'
           ,'StoreOwnerPostcode'
           ,'StoreOwnerCountry'
           ,'StoreOwnerEmail'
           ,'StoreOwnerEmailFrom'
           ,'StoreOwnerSiteAdministratorEmail'
           ,'StoreOwnerSiteAdministratorEmailFrom'
           ,'StoreOwnerOrdersDepartmentEmail'
           ,'StoreOwnerOrdersDepartmentEmailFrom'
           ,'StoreOwnerPageTitle'

           ,'InputControlID'
           ,'InputControlName'
           ,'InputControlValue'
           ,'InputControlSize'
           ,'InputControlLabel'
           ,'InputControlOptions'
           ,'InputControlChecked'
           ,'InputControlOnChange'

           ,'InputControlID2'
           ,'InputControlName2'
           ,'InputControlValue2'
           ,'InputControlSize2'
           ,'InputControlLabel2'
           ,'InputControlOptions2'
           ,'InputControlChecked2'
           ,'InputControlOnChange2'

           ,'JavascriptCountriesAndStates'
           ,'Errors'
           ,'ErrorMessages'
           ,'ResultMessage'
           ,'ResultMessageRow'
        ));

        return modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","container.tpl.html", array());
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
            return $this->mTmplFiller->fill("configuration/owner_profile/", "result-message.tpl.html",array());
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
		    case 'StoreOwnerName':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_OWNER_NAME
		           ,'value' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_NAME]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_NAME)
		           ,'size' => 20
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","text-field.tpl.html", array());
		        break;

		    case 'StoreOwnerWebsite':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_OWNER_WEBSITE
		           ,'value' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_WEBSITE]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_WEBSITE)
		           ,'size' => 20
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","text-field.tpl.html", array());
		        break;

		    case 'StoreOwnerPhones':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_OWNER_PHONES
		           ,'value' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_PHONES]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_PHONES)
		           ,'size' => 20
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","text-field.tpl.html", array());
		        break;

		    case 'StoreOwnerFax':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_OWNER_FAX
		           ,'value' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_FAX]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_FAX)
		           ,'size' => 20
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","text-field.tpl.html", array());
		        break;

		    case 'StoreOwnerStreetLine1':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_OWNER_STREET_LINE_1
		           ,'value' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_STREET_LINE_1]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_STREET_LINE_1)
		           ,'size' => 20
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","text-field.tpl.html", array());
		        break;

		    case 'StoreOwnerStreetLine2':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_OWNER_STREET_LINE_2
		           ,'value' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_STREET_LINE_2]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_STREET_LINE_2)
		           ,'size' => 20
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","text-field.tpl.html", array());
		        break;

		    case 'StoreOwnerCity':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_OWNER_CITY
		           ,'value' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_CITY]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_CITY)
		           ,'size' => 20
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","text-field.tpl.html", array());
		        break;

		    case 'StoreOwnerState':
		        $country_id = modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_COUNTRY);
                $states_count_in_country = modApiFunc("Location", "getCountStatesInCountry", $country_id);
		        $this->_input_control = array(
		            'id2' => SYSCONFIG_STORE_OWNER_STATE . "_menu_select"
		           ,'name2' => SYSCONFIG_STORE_OWNER_STATE . "_menu_select"
		           ,'size2' => 1
		           ,'selected2' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_STATE]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_STATE)
		           ,'options2' => (empty($country_id) || !is_numeric($country_id) ) ? array() :
		                          modApiFunc("Location", "getStates", $country_id)

		           ,'id' => SYSCONFIG_STORE_OWNER_STATE . "_text_div"
		           ,'name' => SYSCONFIG_STORE_OWNER_STATE . "_text_div"
		           ,'value' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_STATE]:($states_count_in_country? "":modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_STATE))
		           ,'size' => 20
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","select-box-with-text-field.tpl.html", array());
		        break;

		    case 'StoreOwnerPostcode':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_OWNER_POSTCODE
		           ,'value' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_POSTCODE]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_POSTCODE)
		           ,'size' => 20
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","text-field.tpl.html", array());
		        break;

		    case 'StoreOwnerCountry':
		        $countries = modApiFunc("Location", "getCountries");

		        $this->_input_control = array(
		            'id' => SYSCONFIG_STORE_OWNER_COUNTRY
		           ,'name' => SYSCONFIG_STORE_OWNER_COUNTRY
		           ,'size' => 1
		           ,'selected' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_COUNTRY]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_COUNTRY)
		           ,'options' => $countries
		           ,'on_change' => "refreshStatesList('" . SYSCONFIG_STORE_OWNER_COUNTRY . "', '" . SYSCONFIG_STORE_OWNER_STATE . "_menu_select', '" .SYSCONFIG_STORE_OWNER_STATE. "_text_div');"
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","select-box.tpl.html", array());
		        break;

		    case 'StoreOwnerEmail':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_OWNER_EMAIL
		           ,'value' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_EMAIL]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_EMAIL)
		           ,'size' => 20
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","text-field-short.tpl.html", array());
		        break;

            case 'StoreOwnerEmailFrom':
                $this->_input_control = array(
                    'name' => SYSCONFIG_STORE_OWNER_EMAIL_FROM
                   ,'value' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_EMAIL_FROM]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_EMAIL_FROM)
                   ,'size' => 20
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","text-field-short.tpl.html", array());
                break;

		    case 'StoreOwnerSiteAdministratorEmail':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL
		           ,'value' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL)
		           ,'size' => 20
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","text-field-short.tpl.html", array());
		        break;

            case 'StoreOwnerSiteAdministratorEmailFrom':
                $this->_input_control = array(
                    'name' => SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL_FROM
                   ,'value' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL_FROM]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL_FROM)
                   ,'size' => 20
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","text-field-short.tpl.html", array());
                break;

		    case 'StoreOwnerOrdersDepartmentEmail':
		        $this->_input_control = array(
		            'name' => SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL
		           ,'value' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL)
		           ,'size' => 20
		        );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","text-field-short.tpl.html", array());
		        break;

            case 'StoreOwnerOrdersDepartmentEmailFrom':
                $this->_input_control = array(
                    'name' => SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL_FROM
                   ,'value' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL_FROM]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL_FROM)
                   ,'size' => 20
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","text-field-short.tpl.html", array());
                break;

            case 'StoreOwnerPageTitle':
                $this->_input_control = array(
                    'name' => SYSCONFIG_STORE_OWNER_PAGE_TITLE
                   ,'value' => ($this->copyFormData)? $this->SessionPost[SYSCONFIG_STORE_OWNER_PAGE_TITLE]:modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_PAGE_TITLE)
                   ,'size' => 20
                );
                $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","text-field.tpl.html", array());
                break;

		    case 'InputControlID': $value = $this->_input_control['id']; break;
		    case 'InputControlName': $value = $this->_input_control['name']; break;
		    case 'InputControlValue': $value = $this->_input_control['value']; break;
		    case 'InputControlLabel': $value = $this->_input_control['label']; break;
		    case 'InputControlSize': $value = $this->_input_control['size']; break;
		    case 'InputControlChecked': $value = ($this->_input_control['checked'] ? " checked" : ""); break;
		    case 'InputControlOnChange': $value = $this->_input_control['on_change']; break;
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

		    case 'InputControlID2': $value = $this->_input_control['id2']; break;
		    case 'InputControlName2': $value = $this->_input_control['name2']; break;
		    case 'InputControlValue2': $value = $this->_input_control['value2']; break;
		    case 'InputControlLabel2': $value = $this->_input_control['label2']; break;
		    case 'InputControlSize2': $value = $this->_input_control['size2']; break;
		    case 'InputControlChecked2': $value = ($this->_input_control['checked2'] ? " checked" : ""); break;
		    case 'InputControlOnChange2': $value = $this->_input_control['on_change2']; break;
		    case 'InputControlOptions2':
		        $value = "";
		        foreach ($this->_input_control['options2'] as $key => $label)
		        {
		            $selected = "";
		            if (isset($this->_input_control['selected2']) && $this->_input_control['selected2'] == $key)
		            {
		            	$selected = " selected";
		            }
		        	$value .= "<option value=\"$key\"$selected>$label</option>";
		        }
		        break;

		    case 'JavascriptCountriesAndStates':
		       $onChangeStatements = "    refreshStatesList('" . SYSCONFIG_STORE_OWNER_COUNTRY . "', '" . SYSCONFIG_STORE_OWNER_STATE . "_menu_select" . "', '" . SYSCONFIG_STORE_OWNER_STATE . "_text_div" . "');" . "\n";
		       $value = modApiFunc("Checkout", "getJavascriptSynchronizeCountriesAndStatesLists") .
		                modApiFunc("Location", "getJavascriptCountriesStatesArrays") .
                        //Concatenate all OnChange instruction and add them to body.onload()
                        "<SCRIPT LANGUAGE=\"JavaScript\">" . "\n" .
                        "<!--\n" . "\n" .
                        "var onload_bak = window.onload;" . "\n" .
                        "window.onload = function()" . "\n" .
                        "{" . "\n" .
                        "    if(onload_bak){onload_bak();}" . "\n" .
                        $onChangeStatements .
                        "}" . "\n" .
                        "//-->" . "\n" .
                        "</SCRIPT>" . "\n";
		       break;
		    case 'Errors':
                $value = "";
                if ($this->copyFormData)
                {
                    $value = modApiFunc('TmplFiller', 'fill', "configuration/owner_profile/","errors.tpl.html", array());
                }
                break;
            case 'ErrorMessages':
                $value = "";
                foreach ($this->SessionPost['ViewState']['ErrorsArray'] as $errorKey)
                {
                    $value.= $this->MessageResources->getMessage($errorKey)."<br>";
                }
                break;
            case 'ResultMessageRow':
                $value = $this->outputResultMessage();
                break;
            case 'ResultMessage':
                $value = $this->_Template_Contents['ResultMessage'];
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


    /**#@-*/

}
?>