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
class LabelData
{
    /**
     * Constructor
     */
    function LabelData()
    {
        // initializing the template engine
        $this -> mTmplFiller = new TmplFiller();

        // loading the html patterns
        loadCoreFile('html_form.php');
    }

    /**
     * The main function to output the viewer content.
     */
    function output()
    {
        global $application;

        // getting the languages
        $lng = modApiFunc('Request', 'getValueByKey', 'lng');

        // if language is invalid -> change it to default
        if (!modApiFunc('MultiLang', 'checkLanguage', $lng, false))
            $lng = modApiFunc('MultiLang', 'getDefaultLanguage');

        $label_id = modApiFunc('Request', 'getValueByKey', 'label_id');
        $Label_Data = array();

        if (!$label_id)
            $label_id = 0;
        else
            $Label_Data = modApiFunc('MultiLang', 'searchLabels',
                                     array('label_id' => $label_id,
                                           'lng' => $lng));

        // getting label data
        if (!empty($Label_Data))
        {
            // label_id is specified and valid
            $Label_Data = array_pop($Label_Data);
        }
        else
        {
            // label_id is either not specified or not valid
            // assuming adding a new custom label
            $Label_Data = array('label' => 'CUSTOM_',
                                'prefix' => 'CZ');
        }

        // restoring label data from session if any
        // use case: restoring submitted from with an error
        if (modApiFunc('Session', 'is_set', 'SavedLabelData'))
        {
            $Label_Data = modApiFunc('Session', 'get', 'SavedLabelData');
            modApiFunc('Session', 'un_set', 'SavedLabelData');
        }

        // getting label information
        $Label_Data = modApiFunc('MultiLang', 'getLabelInformation',
                                 $Label_Data, '', $lng);

        $template_contents = array(
            'ResultMessage' => $this -> outputResultMessage(),
            'PageJSCode'    => $this -> outputJSCode(),
            'ActionField'   => '<input type="hidden" ' .
                               HtmlForm :: genHiddenField(
                                               'asc_action',
                                               'ML_UpdateLabelData'
                                           ) .
                               ' />',
            'LabelIDField'  => '<input type="hidden" ' .
                               HtmlForm :: genHiddenField(
                                   'label_data[id]',
                                   @$Label_Data['id']
                               ) .
                               ' />',
            'LanguageField' => '<input type="hidden" ' .
                               HtmlForm :: genHiddenField('lng', $lng) .
                               ' />',
            'LabelData'     => $this -> outputLabelData($Label_Data, $lng),
            'EditPageTitle' => ((@$Label_Data['id'] > 0)
                               ? getMsg('ML', 'ML_EDIT_LABEL')
                               : getMsg('ML', 'ML_ADD_LABEL'))
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'multilang/label_data/',
                   'container.tpl.html',
                   array()
               );
    }

    /**
     * Outputs label information
     */
    function outputLabelData($Label_Data, $lng)
    {
        global $application;

        $template_contents = array(
            'LabelName'      => ((_ml_substr($Label_Data['label'], 0, 7) !=
                                  'CUSTOM_')
                                    ? $Label_Data['sh_label']
                                    : 'CUSTOM_'),
            'LabelZone'      => $Label_Data['zone'],
            'LabelType'      => $Label_Data['module_name'],
            'LabelUsage'     => htmlspecialchars($Label_Data['usage']),
            'LabelUsageText' => htmlspecialchars(
                                  _ml_substr($Label_Data['usage'], 0,
                                             _ml_strpos($Label_Data['usage'],
                                                        '\'CUSTOM_') + 8)),
            'LabelStatus'    => $Label_Data['status'],
            'Language'       => $this -> outputLanguage($lng),
            'DefLanguage'    => $this -> outputDefaultLanguage(),
            'CustomName'     => $this -> outputCustomNameField($Label_Data),
            'LabelValue'     => $this -> outputLabelValue($Label_Data, $lng),
            'LabelDefValue'  => $this -> outputLabelDefValue($Label_Data)
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'multilang/label_data/',
                   'label-data.tpl.html',
                   array()
               );
    }

    /**
     * Outputs custom name field
     */
    function outputCustomNameField($Label_Data)
    {
        // if the label is not custom -> returning a space
        if (_ml_substr($Label_Data['label'], 0, 7) != 'CUSTOM_')
            return '&nbsp;';

        return '<input style="display:inline;" class="form-control input-sm input-large" type="text"' .
               HtmlForm :: genInputTextField(
                   '255', 'label_data[custom_label]', 60,
                   prepareHTMLDisplay(
                     ((isset($Label_Data['custom_label']))
                       ? $Label_Data['custom_label']
                       : _ml_substr($Label_Data['label'], 7)
                   ))
               ) . ' style="width: 100%;" onkeyup="showLabelUsage(this);" />';
    }

    /**
     * Outputs the language name
     */
    function outputLanguage($lng)
    {
        $lang = modApiFunc('MultiLang', 'getLanguageList', false, $lng);
        if (!$lang)
            return getMsg('ML', 'ML_DEFAULT');

        return $lang[0]['lng_name'] .
               ($lng == modApiFunc('MultiLang', 'getDefaultLanguage')
                ? ' (' . _ml_strtolower(getMsg('ML', 'ML_DEFAULT')) . ')'
                : '');
    }

    /**
     * Outputs default language name
     */
    function outputDefaultLanguage()
    {
        $lang = modApiFunc('MultiLang', 'getLanguageList', false,
                           modApiFunc('MultiLang', 'getDefaultLanguage'));
        if (!$lang)
            return '';

        return $lang[0]['lng_name'];
    }

    /**
     * Outputs the value textarea for the label
     */
    function outputLabelValue($data, $lng)
    {
        // for default language return empty string
        if ($lng == modApiFunc('MultiLang', 'getDefaultLanguage'))
            return '';

        return '<textarea class="tiny_mce" id="LabelValue" name="label_data[value]"' .
               ' rows="5" style="width: 100%;">' .
               htmlspecialchars(@$data['value']) . '</textarea>';
    }

    /**
     * Outputs the default value textarea for the label
     */
    function outputLabelDefValue($data)
    {
        return '<textarea class="tiny_mce" id="LabelDefValue" name="label_data[def_value]"' .
               ' rows="5" style="width: 100%;">' .
               htmlspecialchars(@$data['def_value']) . '</textarea>';
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
                'ResultImage'   => ((_ml_substr($msg, 0, 8) == 'ML_ERROR')
                                    ? 'warning.gif' : 'icon-info-circle.gif'),
                'ResultMessage' => getMsg('ML', $msg),
                'ResultColor'   => ((_ml_substr($msg, 0, 8) == 'ML_ERROR')
                                    ? 'red' : 'green'),
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            return $this -> mTmplFiller -> fill(
                       'multilang/label_data/',
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
     * Outputs the parent window reloading javascript code if needed
     */
    function outputJSCode()
    {
        if (modApiFunc('Session', 'is_set', 'ML_ReloadParentWindow'))
        {
            modApiFunc('Session', 'un_set', 'ML_ReloadParentWindow');
            return $this -> mTmplFiller -> fill(
                                'multilang/label_data/',
                                'reload-parent-js.tpl.html', array()
                            );
        }

        return '';
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
}