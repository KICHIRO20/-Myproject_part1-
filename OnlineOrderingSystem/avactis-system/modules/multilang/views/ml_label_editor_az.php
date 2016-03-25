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
 * @package MultiLang
 * @author Sergey Kulitsky
 *
 */

/**
 * Definition of LanguageSettings viewer
 * The viewer is used to manage active languages in the store
 */
class LabelEditor
{
    /**
     * Constructor
     */
    function LabelEditor()
    {
        // initializing the template engine
        $this -> mTmplFiller = new TmplFiller();

        // loading the html patterns
        loadCoreFile('html_form.php');

        // setting the paginator
        modApiFunc('paginator', 'setCurrentPaginatorName', 'ML_Labels_AZ');

        // filling search filter
        $this -> setSearchFilter();

        // filling quick navigation
        $this -> fillQuickNavigationData();

        // getting the list of modules
        $this -> _modules = modApiFunc('MultiLang', 'getResourceModuleList');
    }

    /**
     * Pre-fills quick navigation data
     */
    function fillQuickNavigationData()
    {
        $this -> _quick_navigation_data = array(
            'all'           => modApiFunc('MultiLang',
                                          'getLabelCount', 'all',
                                          $this -> _search_filter['lng']),
            'storefront'    => modApiFunc('MultiLang',
                                          'getLabelCount', 'storefront',
                                          $this -> _search_filter['lng']),
            'custom'        => modApiFunc('MultiLang',
                                          'getLabelCount', 'custom',
                                          $this -> _search_filter['lng']),
            'nontranslated' => modApiFunc('MultiLang',
                                          'getLabelCount', 'nontranslated',
                                          $this -> _search_filter['lng']),
            'translated'    => modApiFunc('MultiLang',
                                          'getLabelCount', 'translated',
                                          $this -> _search_filter['lng']),
        );
    }

    /**
     * Sets the search filter based on the request
     */
    function setSearchFilter()
    {
        global $application;
        $r = &$application -> getInstance('Request');

        // getting data from request
        $this -> _search_filter = array();
        $this -> _search_filter['asc_action'] = $r -> getValueByKey(
                                                    'asc_action'
                                                );

        // restoring the filter if no action is provided
        // use case: changing the page or rows per page (paginator links)
        if (!in_array($this -> _search_filter['asc_action'],
                      array('ShowAllLabels', 'ShowStorefrontLabels',
                            'ShowCustomLabels', 'ShowNonTranslatedLabels',
                            'ShowTranslatedLabels', 'FilterLabels')))
        {
            if (modApiFunc('Session', 'is_set', 'LABEL_FILTER'))
            {
                $this -> _search_filter = modApiFunc('Session', 'get',
                                                     'LABEL_FILTER');

                // setting lng if it is not set
                if (!$this -> _search_filter['lng'])
                    $this -> _search_filter['lng'] = modApiFunc('MultiLang',
                                                          'getDefaultLanguage');
                // filling up the paginator data
                $this -> _search_filter['paginator'] = null;
                $this -> _search_filter['paginator'] = modApiFunc(
                    'MultiLang', 'searchPgLabels',
                    $this -> _search_filter, PAGINATOR_ENABLE
                );
                return;
            }
            else
                $this -> _search_filter['asc_action'] = 'ShowAllLabels';
        }

        // Force to show all labels by default
        if (!$this -> _search_filter['asc_action'])
            $this -> _search_filter['asc_action'] = 'ShowAllLabels';

        // setting language for quick navigation links
        $tmp_lng = '';

        // getting it from the session if set
        if (modApiFunc('Session', 'is_set', 'LABEL_FILTER'))
        {
            // from previous request if set
            $tmp_lng = modApiFunc('Session', 'get', 'LABEL_FILTER');
            $tmp_lng = $tmp_lng['lng'];
        }

        // if still not set set it to default
        if (!$tmp_lng)
            $tmp_lng = modApiFunc('MultiLang', 'getDefaultLanguage');

        $this -> _search_filter['lng'] = $tmp_lng;

        // pre-filling filter for different asc_actions
        switch($this -> _search_filter['asc_action']) {
            case 'ShowAllLabels':
                break;

            case 'ShowStorefrontLabels':
                $this -> _search_filter['type'] = 'storefront';
                break;

            case 'ShowCustomLabels':
                $this -> _search_filter['type'] = 'CZ_CUSTOM';
                break;

            case 'ShowNonTranslatedLabels':
                $this -> _search_filter['status'] = 'nontranslated';
                break;

            case 'ShowTranslatedLabels':
                $this -> _search_filter['status'] = 'translated';
                break;

            case 'FilterLabels':
                $this -> _search_filter['lng'] = $r -> getValueByKey('lng');
                $this -> _search_filter['label'] = array(
                    'value'   => $r -> getValueByKey('label'),
                    'exactly' => $r -> getValueByKey('label_exactly')
                );
                $this -> _search_filter['pattern'] = array(
                    'value'   => $r -> getValueByKey('pattern'),
                    'exactly' => $r -> getValueByKey('label_pattern_exactly'),
                );
                $this -> _search_filter['type'] = $r -> getValueByKey('type');
                $this -> _search_filter['status'] = $r -> getValueByKey('status');
                break;
        }

        // clearing data is it includes all labels
        if (isset($this -> _search_filter['label']['value'])
            && !$this -> _search_filter['label']['value'])
            unset($this -> _search_filter['label']);

        if (isset($this -> _search_filter['pattern']['value'])
            && !$this -> _search_filter['pattern']['value'])
            unset($this -> _search_filter['pattern']);

        if (isset($this -> _search_filter['type'])
            && $this -> _search_filter['type'] == 'all')
            unset($this -> _search_filter['type']);

        if (isset($this -> _search_filter['status'])
            && ($this -> _search_filter['status'] == 'all'
                || !$this -> _search_filter['status']))
            unset($this -> _search_filter['status']);

        if (!isset($this -> _search_filter['lng'])
            || !$this -> _search_filter['lng'])
            $this -> _search_filter['lng'] = modApiFunc('MultiLang',
                                                        'getLanguage');

        // saving the filter in the session
        modApiFunc('Session', 'set', 'LABEL_FILTER', $this -> _search_filter);

        // filling up the paginator data
        $this -> _search_filter['paginator'] = null;
        $this -> _search_filter['paginator'] = modApiFunc(
                                                   'MultiLang',
                                                   'searchPgLabels',
                                                   $this -> _search_filter,
                                                   PAGINATOR_ENABLE
                                               );
    }

    /**
     * The main function to output the viewer content.
     */
    function output()
    {
        global $application;

        // getting the list of languages
        $this -> _languages = modApiFunc('MultiLang', 'getLanguageList', false);

        // getting the list of labels
        $this -> _labels = modApiFunc('MultiLang', 'searchLabels',
                                      $this -> _search_filter);

        $request = new Request();
        $request -> setView('LanguageList');
        $form_action = $request -> getURL();

        $template_contents = array(
            'ResultMessage'        => $this -> outputResultMessage(),
            'ConfigurationMessage' => $this -> outputConfigurationMessage(),
            'LabelFilter'          => $this -> outputLabelFilter(),
            'SearchResults'        => $this -> outputSearchResults()
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'multilang/label_editor/',
                   'container.tpl.html',
                   array()
               );
    }

    /**
     * Outputs the result message
     */
    function outputResultMessage()
    {
        global $application;

        if (modApiFunc('Session', 'is_set', 'ResultMessage'))
        {
            $msg = modApiFunc('Session', 'get', 'ResultMessage');
            modApiFunc('Session', 'un_set', 'ResultMessage');
            $template_contents = array(
                'ResultMessage' => getMsg('ML', $msg)
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            return $this -> mTmplFiller -> fill(
                       'multilang/label_editor/',
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
     * Outputs message(s) concerning the current PHP configuration
     */
    function outputConfigurationMessage()
    {
        global $application;

        $result = '';
        if (!$application -> multilang_core -> _mb_enabled)
            $result .= $this -> mTmplFiller -> fill(
                           'multilang/label_editor/',
                           'mb_string_not_found.tpl.html',
                           array()
                       );

        return $result;
    }

    /**
     * Outputs the Filter form
     */
    function outputLabelFilter()
    {
        global $application;
        $template_contents = array(
            'DefLanguage'   => modApiFunc('MultiLang', 'getDefaultLanguage'),
            'Language'      => $this -> _search_filter['lng'],
            'ActionField'   => '<input type="hidden" ' .
                               HtmlForm :: genHiddenField(
                                   'asc_action',
                                   'FilterLabels'
                               ) .
                               ' />',
            'HLAllLabels'   => $this -> outputLabel('ShowAllLabels'),
            'HLSFLabels'    => $this -> outputLabel('ShowStorefrontLabels'),
            'HLCLabels'     => $this -> outputLabel('ShowCustomLabels'),
            'HLNTLabels'    => $this -> outputLabel('ShowNonTranslatedLabels'),
            'HLTLabels'     => $this -> outputLabel('ShowTranslatedLabels'),
            'HLLanguage'    => $this -> outputLabel('FilterLabels', 'lng'),
            'HLLabel'       => $this -> outputLabel('FilterLabels', 'label'),
            'HLLPattern'    => $this -> outputLabel('FilterLabels', 'pattern'),
            'HLLType'       => $this -> outputLabel('FilterLabels', 'type'),
            'HLLStatus'     => $this -> outputLabel('FilterLabels', 'status'),
            'CountAll'      => $this -> outputCount('all'),
            'CountSF'       => $this -> outputCount('storefront'),
            'CountC'        => $this -> outputCount('custom'),
            'CountNT'       => $this -> outputCount('nontranslated'),
            'CountT'        => $this -> outputCount('translated'),
            'LangSelect'    => $this -> outputLangSelect(),
            'LabelField'    => '<input class="form-control input-sm input-large" type="text"' .
                               HtmlForm :: genInputTextField(
                                 '255', 'label', 60,
                                 prepareHTMLDisplay(
                                   @$this -> _search_filter['label']['value'])
                               ) . ' />',
            'LabelCheckBox' => HtmlForm :: genCheckbox(array(
                'value' => 'Y',
                'is_checked' => (
                   (@$this -> _search_filter['label']['exactly'] == 'Y')
                   ? 'checked'
                   : ''
                ),
                'name' => 'label_exactly',
                'id' => 'label_exactly'
            ),"class=''"),
            'ValueField'    => '<input class="form-control input-sm input-large" type="text"' .
                               HtmlForm :: genInputTextField(
                                 '255', 'pattern', 60,
                                 prepareHTMLDisplay(
                                   @$this -> _search_filter['pattern']['value'])
                               ) . ' />',
            'ValueCheckBox' => HtmlForm :: genCheckbox(array(
                'value' => 'Y',
                'is_checked' => (
                   (@$this -> _search_filter['pattern']['exactly'] == 'Y')
                   ? 'checked'
                   : ''
                ),
                'name' => 'label_pattern_exactly',
                'id' => 'label_pattern_exactly'
            ),"class=''"),
            'TypeSelect'    => $this -> outputTypeSelect(),
            'StatusSelect'  => HtmlForm :: genDropdownSingleChoice(array(
                'select_name' => 'status',
                'selected_value' => @$this -> _search_filter['status'],
                'id' => 'status',
                'values' => array(
                    array(
                        'value' => 'all',
                        'contents' => getMsg('ML', 'ML_ALL_LABELS'),
                    ),
                    array(
                        'value' => 'translated',
                        'contents' => getMsg('ML', 'ML_TRANSLATED_LABELS'),
                    ),
                    array(
                        'value' => 'nontranslated',
                        'contents' => getMsg('ML', 'ML_NON_TRANSLATED_LABELS'),
                    )
                )
            ))
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'multilang/label_editor/',
                   'label_filter.tpl.html',
                   array()
               );
    }

    /**
     * Outputs the additional style for active elements in the search form
     */
    function outputLabel($label, $add_label = '')
    {
        $output = '';

        $condition = ($label == $this -> _search_filter['asc_action']);
        if ($label == 'FilterLabels')
            $condition = ($condition && @$this -> _search_filter[$add_label]);

        if ($condition)
            $output = ' color: blue;';

        return $output;
    }

    /**
     * Outputs the number of labels for quick navigation links
     */
    function outputCount($type)
    {
        return intval(@$this -> _quick_navigation_data[$type]);
    }

    /**
     * Outputs language filter
     */
    function outputLangSelect()
    {
        $values = array();
        $default_language = modApiFunc('MultiLang', 'getDefaultLanguage');

        // if list of languages is empty create a hidden field
        if (!is_array($this -> _languages) || empty($this -> _languages))
            return getMsg('ML', 'ML_DEFAULT') . '<input type="hidden" ' .
                   HtmlForm :: genHiddenField('lng', '') . ' />';

        foreach($this -> _languages as $v)
            $values[] = array(
                'value' => $v['lng'],
                'contents' => $v['lng_name'] .
                (($v['lng'] == $default_language)
                    ? ' (' . _ml_strtolower(getMsg('ML', 'ML_DEFAULT')) . ')'
                    : '')
            );

        return HtmlForm :: genDropdownSingleChoice(array(
                   'select_name' => 'lng',
                   'selected_value' => @$this -> _search_filter['lng'],
                   'id' => 'lng',
                   'values' => $values,
                   'onChange' => 'checkLanguage(this); this.form.submit();'
               ));
    }

    /**
     * Outputs label type select box
     */
    function outputTypeSelect()
    {
        $values = array(
                      array(
                          'value'    => 'all',
                          'contents' => getMsg('ML', 'ML_ALL_LABELS')
                      ),
                      array(
                          'value'    => 'storefront',
                          'contents' => getMsg('ML', 'ML_STOREFRONT_LABELS')
                      ),
                      array(
                          'value'    => 'storefront_cz',
                          'contents' => '&nbsp;&nbsp;' .
                                        getMsg('ML', 'ML_AVACTIS_LABELS')
                      ),
                      array(
                          'value'    => 'CZ_CUSTOM',
                          'contents' => '&nbsp;&nbsp;' .
                                        getMsg('ML', 'ML_CUSTOM_LABELS')
                      ),
                      array(
                          'value' => 'admin',
                          'contents' => getMsg('ML', 'ML_ADMINZONE_LABELS'),
                      )
                  );

        if (is_array($this -> _modules))
            foreach($this -> _modules as $m)
                $values[] = array(
                                'value'    => $m['shortname'],
                                'contents' => '&nbsp;&nbsp;' . $m['module']
                            );

        return HtmlForm :: genDropdownSingleChoice(array(
                   'select_name' => 'type',
                   'selected_value' => @$this -> _search_filter['type'],
                   'id' => 'type',
                   'values' => $values
               ));
    }

    /**
     * Outputs the result of searching
     */
    function outputSearchResults()
    {
        global $application;

        $template_contents = array(
            'CurLang'        => $this -> _search_filter['lng'],
            'SearchTotal'    => $this -> outputSearchTotal(),
            'TopButtons'     => $this -> outputTopButtons(),
            'ResultForm'     => $this -> outputResultForm(),
            'BottomButtons'  => $this -> outputBottomButtons(),
            'ResActionField' => '<input type="hidden" ' .
                                HtmlForm :: genHiddenField(
                                    'asc_action',
                                    'ML_UpdateLabels'
                                ) .
                                ' />',
            'ResModeField'   => '<input type="hidden" ' .
                                HtmlForm :: genHiddenField(
                                    'mode',
                                    'update'
                                ) .
                                ' />',
            'LngField'       => '<input type="hidden" ' .
                                HtmlForm :: genHiddenField(
                                    'lng',
                                    $this -> _search_filter['lng']
                                ) .
                                ' />'
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'multilang/label_editor/',
                   'search-results.tpl.html',
                   array()
               );
    }

    /**
     * Outputs the total line for search results
     */
    function outputSearchTotal()
    {
        $total = modAPIFunc('Paginator', 'getCurrentPaginatorTotalRows');

        if ($total > 0)
        {
            $output = $total . ' ' .
                      (($total == 1)
                          ? getMsg('ML', 'ML_ONE_LABEL_FOUND')
                          : getMsg('ML', 'ML_SEVERAL_LABELS_FOUND')
                      ) . ' ' . getMsg('ML', 'ML_SHOWING') . ' ';
            if (isset($this -> _search_filter['paginator']))
                $output .= ($this -> _search_filter['paginator'][0] + 1) .
                           ' - ' .
                           min($this -> _search_filter['paginator'][0] +
                               $this -> _search_filter['paginator'][1], $total);
            else
                $output .= '1 - ' . $total;
        }
        else
        {
            $output = getMsg('ML', 'ML_NO_LABELS_FOUND');
        }

        return $output;
    }

    /**
     * Outputs the top buttons
     * Note: if the search result is empty it returns an empty string
     */
    function outputTopButtons()
    {
        global $application;

        $output = '';

        $template_contents = array(
            'DisabledButton' => $this -> disableDeleteButton()
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);

        if (count($this -> _labels) > 0)
            $output = $this -> mTmplFiller -> fill(
                          'multilang/label_editor/',
                          'search-results-buttons-top.tpl.html',
                          array()
                      );
        else
            $output = $this -> mTmplFiller -> fill(
                          'multilang/label_editor/',
                          'search-results-nobuttons.tpl.html',
                          array()
                      );

        return $output;
    }

    /**
     * Outputs the bottom buttons
     * Note: if the search result is empty it returns an empty string
     * Note: separated from outputTopButtons for customizing purposes
     *       (to have ability to use different styles and id for the buttons)
     */
    function outputBottomButtons()
    {
        global $application;

        $output = '';

        $template_contents = array(
            'DisabledButton' => $this -> disableDeleteButton(),
            'CurLang'        => $this -> _search_filter['lng']
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);

        if (count($this -> _labels) > 0)
            $output = $this -> mTmplFiller -> fill(
                          'multilang/label_editor/',
                          'search-results-buttons-bottom.tpl.html',
                          array()
                      );

        return $output;
    }

    /**
     * Makes "remove translation" button disabled for default language
     */
    function disableDeleteButton()
    {
        if ($this -> _search_filter['lng'] != modApiFunc('MultiLang',
                                                         'getDefaultLanguage'))
            return '';

        return ' button_disabled';
    }

    /**
     * Outputs the form for the found labels
     */
    function outputResultForm()
    {
        if (count($this -> _labels) <= 0)
            return '';

        global $application;

        $def_lng = modApiFunc('MultiLang', 'getDefaultLanguage');

        $template_contents = array(
            'ResCheckbox'    => HtmlForm :: genCheckbox(array(
                'value'      => 'Y',
                'name'       => '',
                'id'         => 'SelectAll',
                'is_checked' => false,
                'onclick'    => 'javascript: selectItems(\'SearchResults\');'
            ),"class=''"),
            'ResultRecords'  => $this -> outputResultRecords(),
            'ValueWidth'     => (($this -> _search_filter['lng'] == $def_lng)
                                ? '1em' : '33%'),
            'DefValueWidth'  => (($this -> _search_filter['lng'] == $def_lng)
                                ? '66%' : '33%'),
            'Translation'    => (($this -> _search_filter['lng'] == $def_lng)
                                ? '' : $this -> outputTranslation()),
            'OriginalValue'  => $this -> outputOriginalValue()
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'multilang/label_editor/',
                   'search-results-form.tpl.html',
                   array()
               );
    }

    /**
     * Outputs label for translation fields
     * pattern: Translation (to __LNG_NAME__)
     */
    function outputTranslation()
    {
        $lang = modApiFunc('MultiLang', 'getLanguageList', false,
                           $this -> _search_filter['lng']);

        if (!$lang)
            $lang = getMsg('ML', 'ML_DEFAULT');
        else
            $lang = $lang[0]['lng_name'];

        return getMsg('ML', 'ML_VALUE') . ' (' . getMsg('ML', 'ML_TO') . ' ' .
               $lang . ')';
    }

    /**
     * Outputs label for original value fields
     * pattern: Original value (in __LNG_NAME__)
     */
    function outputOriginalValue()
    {
        $lang = modApiFunc('MultiLang', 'getLanguageList', false,
                           modApiFunc('MultiLang', 'getDefaultLanguage'));

        if (!$lang)
            return getMsg('ML', 'ML_DEFAULT_VALUE');

        return getMsg('ML', 'ML_DEFAULT_VALUE') . ' (' .
               getMsg('ML', 'ML_IN') . ' ' .  $lang[0]['lng_name'] . ')';
    }

    /**
     * Outputs a label for the search result form
     */
    function outputResultRecords()
    {
        global $application;

        $output = '';

        foreach($this -> _labels as $record)
        {
            $record = modApiFunc('MultiLang', 'getLabelInformation',
                                 $record, $this -> _modules,
                                 $this -> _search_filter['lng']);
            $template_contents = array(
                'LabelID'        => $record['id'],
                'LabelName'      => $record['sh_label'],
                'LabelZone'      => $record['zone'],
                'LabelType'      => $record['module_name'],
                'LabelUsage'     => htmlspecialchars($record['usage']),
                'LabelStatus'    => $record['status'],
                'LabelValue'     => $this -> outputLabelValue($record),
                'LabelDefValue'  => $this -> outputLabelDefValue($record),
                'CurLang'        => $this -> _search_filter['lng'],
                'DeleteCheckBox' => HtmlForm :: genCheckbox(array(
                    'value'      => $record['id'],
                    'name'       => 'label_id[]',
                    'id'         => 'select_' .
                                    $record['id'],
                    'is_checked' => false,
                    'onclick'    => 'javascript: selectRow(this);'
                ),"class='form-control input-sm'"),
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $output .= $this -> mTmplFiller -> fill(
                           'multilang/label_editor/',
                           'search-results-form-record.tpl.html',
                           array()
                       );
        }

        return $output;
    }

    /**
     * Outputs the value textarea for a label
     */
    function outputLabelValue($record)
    {
        // showing nothing for default language
        if ($this -> _search_filter['lng'] == modApiFunc('MultiLang',
                                                         'getDefaultLanguage'))
            return '&nbsp;';

        return '<textarea class="form-control input-sm input-medium" name="posted_data[' . $record['id'] . '][value]"' .
               ' rows="4" onchange="onStatusChanged(' .
               $record['id'] . ')">' .
               htmlspecialchars((($record['value'] === NULL)
                                ? $record['def_value']
                                : $record['value'])) . '</textarea>';
    }

    /**
     * Outputs the default value textarea for a label
     */
    function outputLabelDefValue($record)
    {
        return '<textarea class="form-control input-sm input-medium" name="posted_data[' . $record['id'] . '][def_value]"'
               . ' rows="4" style="' .
               (($this -> _search_filter['lng'] != modApiFunc('MultiLang',
                                                         'getDefaultLanguage'))
                  ? ' color: black;"' .
               ' disabled="disabled"' : '"') .
               ' onchange="onStatusChanged(' .
               $record['id'] . ')">' .
               htmlspecialchars($record['def_value']) . '</textarea>';
    }

    /**
     * Outputs the Paginator line
     * Note: it is required not to register the tag in the viewer
     *       for proper output
     * See: see the getTag function as well
     */
    function outputPaginatorLine()
    {
        global $application;

        $obj = &$application -> getInstance('PaginatorLine');
        return $obj -> output('ML_Labels_AZ', 'LabelEditor');
    }

    /**
     * Outputs the Paginator rows
     * Note: it is required not to register the tag in the viewer
     *       for proper output
     * See: see the getTag function as well
     */
    function outputPaginatorRows()
    {
        global $application;

        $obj = &$application -> getInstance('PaginatorRows');
        return $obj -> output('ML_Labels_AZ', 'LabelEditor',
                              'PGNTR_ML_LABEL_ITEMS');
    }

    /**
     * Processes local tags
     */
    function getTag($tag)
    {
        if ($tag == 'PaginatorLine')
            return $this -> outputPaginatorLine();

        if ($tag == 'PaginatorRows')
            return $this -> outputPaginatorRows();

        return getKeyIgnoreCase($tag, $this -> _Template_Contents);
    }

    var $mTmplFiller;
    var $_Template_Contents;
    var $_languages;
    var $_quick_navigation_data;
    var $_search_filter;
    var $_labels;
    var $_modules;
}