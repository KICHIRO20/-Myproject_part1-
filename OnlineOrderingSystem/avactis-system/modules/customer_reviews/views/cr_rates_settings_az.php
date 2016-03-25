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
 * @package Customer_Reviews
 * @author Sergey E. Kulitsky
 *
 */

/**
 * Definition of CR_Rates_Settings viewer
 * The viewer is used to manage rate list in admin zone
 */
class CR_Rates_Settings
{
    /**
     * Constructor
     */
    function CR_Rates_Settings()
    {
        // loading the prototypes of form fields
        loadCoreFile('html_form.php');

        // initializing the template filler
        $this -> mTmplFiller = new TmplFiller();
    }

    /**
     * The main function to output the given view
     */
    function output()
    {
        global $application;

        $template_contents = array(
            'Settings'        => $this -> outputSettings(),
            'ResultMessage'   => $this -> outputResultMessage(),
            'ModeHiddenField' => $this -> outputField('hidden', 'mode',
                                                      'update'),
            'NewRateField'    => $this -> outputField('rate',
                                                      'rate_new[rate_label]',
                                                      ''),
            'NewVisibleField' => $this -> outputField('visible',
                                                      'rate_new[visible]',
                                                      'Y'),
            'RateSortForm'    => $this -> outputSortForm(),
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'customer_reviews/rates_settings/',
                   'container.tpl.html',
                   array()
               );
    }

    /**
     * Outputs the result message
     * Note: the message is taken from the session
     * Use case: it contains the result of the previous action
     */
    function outputResultMessage()
    {
        global $application;

        if (modApiFunc('Session', 'is_set', 'ResultMessage'))
        {
            $msg = modApiFunc('Session', 'get', 'ResultMessage');
            modApiFunc('Session', 'un_set', 'ResultMessage');
            $template_contents = array(
                "ResultMessage" => getMsg('CR', $msg)
            );
            $this -> _Template_Contents=$template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            return $this -> mTmplFiller -> fill(
                       'customer_reviews/rates_settings/',
                       'result-message.tpl.html',
                       array()
                   );
        }
        else
        {
            return '';
        }
    }

    /**
     * Outputs the form field for the given params
     */
    function outputField($field_type, $field_name, $def_value,
                         $onchange = '', $id = '')
    {
        $return_value = '';

	switch($field_type)
        {
            case 'hidden':
               $return_value = '<input type="hidden"' .
                               HtmlForm :: genHiddenField($field_name,
                                                          $def_value) .
                               ' id="' . $field_name . '" />';
               break;
            case 'rate':
               $return_value = '<input type="text"' .
                               HtmlForm :: genInputTextField(
                                               '255',
                                               $field_name,
                                               '70',
                                               $def_value,
                                               $onchange .
                                                   'style="width: 98%;" class="form-control input-sm input-large"'
                                           ) .
                               ' />';
               break;
            case 'visible':
               $return_value = HtmlForm::genDropdownSingleChoice(array(
                   "select_name"    => $field_name,
                   "selected_value" => $def_value,
                   "onChange"       => $onchange,
                   "values"         => array(
                                           array(
                                               'value'    => 'Y',
                                               'contents' => getMsg('CR',
                                                                    'CR_SHOW')
                                           ),
                                           array(
                                               'value'    => 'N',
                                               'contents' => getMsg('CR',
                                                                    'CR_HIDE')
                                           )
                                       )
               ));
               break;
            case 'checkbox':
               $return_value = HtmlForm :: genCheckbox(array(
                   "value"      => $def_value,
                   "name"       => $field_name,
                   "onclick"    => $onchange,
                   "id"         => $id,
                   "is_checked" => ''
               ));
               break;
        }

        return $return_value;
    }

    /**
     * Outputs the rate list (record by record)
     */
    function outputSettings()
    {
        global $application;

        $settings = modApiFunc('Customer_Reviews', 'getRatesSettings');

	$output = '';
        if (is_array($settings) && !empty($settings)) {
            foreach($settings as $setting) {
                $template_contents=array(
                    "RateField"     => $this -> outputField(
                                           'rate',
                                           'rates[' .
                                           $setting['cr_rl_id'] .
                                           '][rate_label]',
                                           prepareHTMLDisplay($setting['rate_label']),
                                           'onChange="javascript: onStatusChanged(' .
                                               $setting['cr_rl_id'] . ');" '
                                       ),
                    "VisibleField"  => $this -> outputField(
                                           'visible',
                                           'rates[' .
                                               $setting['cr_rl_id'] .
                                               '][visible]',
                                           $setting['visible'],
                                           'javascript: onStatusChanged(' .
                                               $setting['cr_rl_id'] . ');'
                                       ),
                    "CheckboxField" => $this -> outputField(
                                           'checkbox',
                                           'selected_rates[' .
                                               $setting['cr_rl_id'] . ']',
                                           $setting['cr_rl_id'],
                                           'javascript: checkRates(this);',
                                           'select_' . $setting['cr_rl_id']
                                       )

                );
                $this -> _Template_Contents = $template_contents;
                $application -> registerAttributes($this -> _Template_Contents);
                $output .= $this -> mTmplFiller -> fill(
                                        'customer_reviews/rates_settings/',
                                        'item.tpl.html', array()
                                    );
            }

            $output .= $this -> mTmplFiller -> fill(
                                    'customer_reviews/rates_settings/',
                                    'item-actions.tpl.html', array()
                                );
        }
        else
        {
            $output = modApiFunc('TmplFiller', 'fill',
                                 'customer_reviews/rates_settings/',
                                 'no-items.tpl.html', array()
                                );
        }

        return $output;
    }

    /**
     * Outputs the sort form
     */
    function outputSortForm()
    {
        global $application;

        $template_contents=array(
            'SortModeField'   => $this -> outputField('hidden', 'mode', 'sort'),
            'SortResultField' => $this -> outputField(
                                              'hidden',
                                              'cr_rates_sort_order.hidden',
                                              ''
                                          ),
            'SortItems'       => $this -> outputSortItems()
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'customer_reviews/rates_settings/',
                   'sort-form.tpl.html',
                   array()
               );
    }

    /**
     * Fills the sort selert box
     */
    function outputSortItems()
    {
        global $application;

        $settings = modApiFunc('Customer_Reviews','getRatesSettings');
        $output = '';
        if (is_array($settings) && !empty($settings))
            foreach($settings as $setting)
                $output .= '<option value="' . $setting['cr_rl_id'] . '">' .
                           prepareHTMLDisplay($setting['rate_label']) . '</option>';

        return $output;
    }

    /**
     * Processes the tags
     */
    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $mTmplFiller;
}

?>