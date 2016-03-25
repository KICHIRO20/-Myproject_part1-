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
 * @package Shipping Cost Calculator
 * @access  public
 * @author Ravil Garafutdinov
 */
class ShippingCalculator
{
    /**
     * Gets a template format for the given view.
     */
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'shipping-calculator.ini'
           ,'files' => array(
                 'Container'      => 'shipping-calculator-container.tpl.html'
                ,'Empty'          => 'shipping-calculator-empty.tpl'
                ,'FreeShipping'   => 'shipping-calculator-freeshipping.tpl.html'
                ,'Item'           => 'shipping-calculator-item.tpl'
                ,'Results'        => 'shipping-calculator-results.tpl'
                ,'Chosen'         => 'chosen-one.tpl.html'
                ,'RememberButton' => 'remember-button.tpl'
                ,'FormCountry'    => 'form-country-select.tpl.html'
                ,'FormState'      => 'form-state-select.tpl.html'
                ,'FormZip'        => 'form-zip-select.tpl.html'
                ,'FormHidden'     => 'form-hidden-select.tpl.html'
                ,'InfoField'      => 'info-field.tpl.html'
            )
           ,'options' => array()
        );
        return $format;
    }

    function ShippingCalculator()
    {
        global $application;

        if (modApiFunc("Session", "is_Set", "ShippingCalculatorPost"))
        {
            $this->destination = modApiFunc("Session", "get", "ShippingCalculatorPost");
        }
        else
        {
            $this->destination = array();
        }

        $this->warning = false;
        $this->choice = modApiFunc('Shipping_Cost_Calculator', 'getCustomerChoice');
        $this->chosenName = '';
        $this->chosenCostFormatted = '';

        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("ShippingCostCalculatorSection"))
        {
            $this->NoView = true;
        }
    }

    function output()
    {
        global $application;

        $cOpts = '';
        $sOpts = '';
        $results = '';
        $page = "Container";
        $chosenName = 'bla-bla';
        $chosenCost = '$lots';

        $formatted_cart = modApiFunc("Shipping_Cost_Calculator", "formatCart", modApiFunc("Cart", "getCartContent"));

        if (empty($formatted_cart['products']))
            return '';

        if (!empty($this->destination))
        {
            $page = "Results";
            modApiFunc("Shipping_Cost_Calculator", "setCart", $formatted_cart);
            $shipping_info = modApiFunc("Checkout", "getPrerequisiteValidationResults", "shippingInfo");

            if (isset($this->destination['DstCountry']))
            {
                $shipping_info['validatedData']['Country']['value'] = $this->destination['DstCountry'];
            }
            if (isset($this->destination['DstState_menu_select']))
            {
                $shipping_info['validatedData']['Statemenu']['value'] = $this->destination['DstState_menu_select'];
            }
            if (isset($this->destination['DstZip']))
            {
                $shipping_info['validatedData']['Postcode']['value'] = $this->destination['DstZip'];
            }

            $shipping_info['isMet'] = 'true';
            modApiFunc("Shipping_Cost_Calculator", "setShippingInfo", $shipping_info);
            $shippingCosts = modApiFunc('Shipping_Cost_Calculator', 'calculateShippingCost');

            // will fill in shipping costs and check the chosen one
            $results = $this->getResults($shippingCosts);

            if ($this->choice !== false)
            {
                $page = "Chosen";
                $chosenName = $this->chosenName;
                $chosenCost = $this->chosenCostFormatted;
            }
        }

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setAction("CalculateShippingCZ");
        $act = $request->getURL();

        $template_contents = array(
                 "Local_Results"    => $results
                ,"Local_FormAction" => $act
                ,"Local_DestinationFields" => $this->outputFields()
                ,"Local_InfoFields" => $this->outputInfoFields()
                ,"JavascriptSynchronizeCountriesAndStatesLists" => modApiFunc("Location", "getJavascriptCountriesStatesArrays", true, array(), array(), true, true) . modApiFunc("Checkout", "getJavascriptSynchronizeCountriesAndStatesLists") .
                                    //Combine all the OnChange instructions and add them to body.onload()
                                    "<script type=\"text/javascript\">" . "\n" .
                                    "<!--\n" . "\n" .
                                    "var onload_bak = window.onload;" . "\n" .
                                    "window.onload = function()" . "\n" .
                                    "{" . "\n" .
                                    "    if(onload_bak){onload_bak();}" . "\n" .
                                    "    refreshStatesList('DstCountry', 'DstState_menu_select', 'stub_state_text_input');" . //$onChangeStatements
                                    "}" . "\n" .
                                    "//-->" . "\n" .
                                    "</script>" . "\n"
                ,"Local_ChosenMethodName" => $chosenName
                ,"Local_ChosenMethodCost" => $chosenCost
                ,"Local_ChosenMethodUnavailableWarning" => ($this->warning === true) ? getLabel('SCC_CHOSEN_METHOD_NO_MORE_AVAILABLE') : ''

        );

        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);

        $this->templateFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate('ShippingCalculator');
        $this->templateFiller->setTemplate($this->template);

        $retval = $this->templateFiller->fill($page);
        return $retval;
    }

    function getResults($shippingCosts)
    {
        global $application;

        if (empty($shippingCosts)
            || isset($shippingCosts['Shipping_Module_All_Inactive']))
        {
            $this->choice = false;
            modApiFunc('Shipping_Cost_Calculator', 'clearCustomerChoice');

            $this->templateFiller = &$application->getInstance('TemplateFiller');
            $this->template = $application->getBlockTemplate('ShippingCalculator');
            $this->templateFiller->setTemplate($this->template);
            return $this->templateFiller->fill('Empty');
        }

        if (isset($shippingCosts['Shipping_Not_Needed']))
        {
            $this->choice = false;
            modApiFunc('Shipping_Cost_Calculator', 'clearCustomerChoice');

            $this->templateFiller = &$application->getInstance('TemplateFiller');
            $this->template = $application->getBlockTemplate('ShippingCalculator');
            $this->templateFiller->setTemplate($this->template);
            return $this->templateFiller->fill('FreeShipping');
        }

        $choice = $this->choice;
        $taken = false;

        $results = '';
        foreach ($shippingCosts as $api_name => $methods)
        {
            $api_info = modApiFunc($api_name,"getInfo");
            $this->current_api_id = $api_info["GlobalUniqueShippingModuleID"];

            if (isset($methods['methods']))
            {
                foreach ($methods['methods'] as $costs)
                {
                    if (isset($costs['shipping_cost']['TotalShippingAndHandlingCost']))
                    {
                        $selected = '';
                        if ($choice !== false)
                        {
                            if ($this->current_api_id . '_' . $costs['id'] == $choice['module'] . '_' . $choice['method'])
                            {
                                $selected = 'checked="checked"';
                                $taken = true;

                                $this->chosenName = $costs['method_name'];
                                $this->chosenCostFormatted = modApiFunc('Localization', 'currency_format', $costs['shipping_cost']['TotalShippingAndHandlingCost']);
                            }
                        }

                        $template_contents = array(
                             'Local_MethodName' => $costs['method_name']
                            ,'Local_MethodCost' => modApiFunc('Localization', 'currency_format', $costs['shipping_cost']['TotalShippingAndHandlingCost'])
                            ,'Local_RadioValue' => $this->current_api_id . '_' . $costs['id']
                            ,'Local_RadioSelected' => $selected
                        );

                        $this->_Template_Contents = $template_contents;
                        $application->registerAttributes($this->_Template_Contents);

                        $this->templateFiller = &$application->getInstance('TemplateFiller');
                        $this->template = $application->getBlockTemplate('ShippingCalculator');
                        $this->templateFiller->setTemplate($this->template);

                        $results .= $this->templateFiller->fill('Item');
                    }
                }
            }
        }

        // add 'Remember' button
        $this->templateFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate('ShippingCalculator');
        $this->templateFiller->setTemplate($this->template);
        $results .= $this->templateFiller->fill('RememberButton');

        // check if we still have the chosen method available
        if ($taken == false && $this->choice !== false)
        {
            $this->choice = false;
            modApiFunc('Shipping_Cost_Calculator', 'clearCustomerChoice');
            $this->warning = true;
        }

        return $results;
    }

    function outputFields()
    {
        global $application;
        $settings = modApiFunc('Shipping_Cost_Calculator', 'getSettings');
        $results = '';

        if ($settings['FS_COUNTRY_HIDE'] == FS_HIDE)
        {
            $current_country = $settings['FS_COUNTRY_ASSUME'];

            $template_contents = array(
                   "Local_HiddenSelectName" => 'DstCountry'
                  ,"Local_HiddenSelectValue" => $current_country
            );

            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);

            $this->templateFiller = &$application->getInstance('TemplateFiller');
            $this->template = $application->getBlockTemplate('ShippingCalculator');
            $this->templateFiller->setTemplate($this->template);

            $results .= $this->templateFiller->fill('FormHiddenSelect');
        }
        else
        {
            $cOpts = modApiFunc("Checkout", "genCountrySelectList", isset($this->destination["DstCountry"]) ? $this->destination["DstCountry"] : '', false, true);

            $template_contents = array(
                  "CountriesOptions" => $cOpts
            );

            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);

            $this->templateFiller = &$application->getInstance('TemplateFiller');
            $this->template = $application->getBlockTemplate('ShippingCalculator');
            $this->templateFiller->setTemplate($this->template);

            $results .= $this->templateFiller->fill('FormCountry');
        }

        if ($settings['FS_STATE_HIDE'] == FS_HIDE)
        {
            $current_state = $settings['FS_STATE_ASSUME'];

            $template_contents = array(
                   "Local_HiddenSelectName" => 'DstState_menu_select'
                  ,"Local_HiddenSelectValue" => $current_state
            );

            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);

            $this->templateFiller = &$application->getInstance('TemplateFiller');
            $this->template = $application->getBlockTemplate('ShippingCalculator');
            $this->templateFiller->setTemplate($this->template);

            $results .= $this->templateFiller->fill('FormHiddenSelect');
        }
        else
        {
            $sOpts = modApiFunc("Checkout", "genStateSelectList", isset($this->destination["DstState_menu_select"]) ? $this->destination["DstState_menu_select"] : '', isset($this->destination["DstCountry"]) ? $this->destination["DstCountry"] : '', true);

            $template_contents = array(
                 "StatesOptions"    => $sOpts
            );

            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);

            $this->templateFiller = &$application->getInstance('TemplateFiller');
            $this->template = $application->getBlockTemplate('ShippingCalculator');
            $this->templateFiller->setTemplate($this->template);

            $results .= $this->templateFiller->fill('FormState');
        }

        if ($settings['FS_ZIP_HIDE'] == FS_HIDE)
        {
            $current_zip = $settings['FS_ZIP_ASSUME'];

            $template_contents = array(
                   "Local_HiddenInputName" => 'DstZip'
                  ,"Local_HiddenInputValue" => $current_zip
            );

            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);

            $this->templateFiller = &$application->getInstance('TemplateFiller');
            $this->template = $application->getBlockTemplate('ShippingCalculator');
            $this->templateFiller->setTemplate($this->template);

            $results .= $this->templateFiller->fill('FormHiddenInput');

        }
        else
        {
            $template_contents = array(
                 "DstZipValue"      => isset($this->destination["DstZip"]) ? $this->destination["DstZip"] : ''
            );

            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);

            $this->templateFiller = &$application->getInstance('TemplateFiller');
            $this->template = $application->getBlockTemplate('ShippingCalculator');
            $this->templateFiller->setTemplate($this->template);

            $results .= $this->templateFiller->fill('FormZip');
        }

        return $results;
    }

    function outputInfoFields()
    {
        global $application;
        $results = '';
        $settings = modApiFunc('Shipping_Cost_Calculator', 'getSettings');

        $current_country = isset($this->destination["DstCountry"]) ? modApiFunc('Location', 'getCountry', $this->destination['DstCountry']) : '';
        $current_state = isset($this->destination["DstState_menu_select"]) ? modApiFunc('Location', 'getState', $this->destination['DstState_menu_select']) : '';
        $current_zip = isset($this->destination["DstZip"]) ? $this->destination["DstZip"] : '';
        $fields = array(
             'country' => array('caption' => getLabel('SCC_COUNTRY_COLON'), 'value' => $current_country, 'is_hidden' => $settings['FS_COUNTRY_HIDE'])
            ,'state'   => array('caption' => getLabel('SCC_STATE_COLON'),   'value' => $current_state,   'is_hidden' => $settings['FS_STATE_HIDE'])
            ,'zip'     => array('caption' => getLabel('SCC_ZIP_COLON'),     'value' => $current_zip,     'is_hidden' => $settings['FS_ZIP_HIDE'])
        );

        foreach ($fields as $field)
        {
            if ($field['is_hidden'] == FS_HIDE)
                continue;

            $template_contents = array(
                  "Local_FieldCaption" => $field['caption']
                 ,"Local_FieldValue"   => $field['value']
            );

            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);

            $this->templateFiller = &$application->getInstance('TemplateFiller');
            $this->template = $application->getBlockTemplate('ShippingCalculator');
            $this->templateFiller->setTemplate($this->template);

            $results .= $this->templateFiller->fill('InfoField');
        }

        return $results;
    }

    function getTag($tag)
    {
        global $application;
        $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
        return $value;
    }

    var $_Template_Contents;
    var $MessageResources;
    var $destination;
    var $choice;
    var $chosenName;
    var $chosenCostFormatted;
    var $current_api_id;
    var $warning;

};

?>