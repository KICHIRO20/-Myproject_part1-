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
class LanguageSettings
{
    /**
     * Constructor
     */
    function LanguageSettings()
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

        // getting the list of languages
        $this -> _languages = modApiFunc('MultiLang', 'getLanguageList', false);

        // getting the codes of active languages
        $lng_codes = array();
        if (is_array($this -> _languages))
            foreach($this -> _languages as $v)
                $lng_codes[] = $v['lng'];

        // getting all languages except the ones from the _languages array
        $this -> _all_languages = modApiFunc('MultiLang', 'getAllLanguages',
                                             $lng_codes);

        $request = new Request();
        $request -> setView('LanguageSettings');
        $form_action = $request -> getURL();
        $template_contents = array(
            'UpdateLanguagesForm'  => HtmlForm :: genForm($form_action, "POST",
                                                          "language_settings"),
            'HiddenData'           => $this -> outputHiddenData(),
            'ConfigurationMessage' => $this -> outputConfigurationMessage(),
            'ResultMessage'        => $this -> outputResultMessage(),
            'AvailableLanguages'   => $this -> outputAvailableLanguages(),
            'NewLanguages'         => $this -> outputNewLanguages(),
            'DefaultLanguage'      => modApiFunc('MultiLang', '_readDefaultLanguage')
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'multilang/language_settings/',
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
                "ResultMessage" => getMsg('ML', $msg)
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            return $this -> mTmplFiller -> fill(
                       'multilang/language_settings/',
                       'result_message.tpl.html',
                       array()
                   );
        }
        else
        {
            return '';
        }
    }

    /**
     * Outputs hidden fields
     */
    function outputHiddenData()
    {
        return '<input type="hidden"' . HtmlForm :: genHiddenField('asc_action', 'UpdateLanguages') . ' />' .
               '<input type="hidden"' . HtmlForm :: genHiddenField('mode', 'update') . ' />';
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
                           'multilang/language_settings/',
                           'mb_string_not_found.tpl.html',
                           array()
                       );

        return $result;
    }

    /**
     * Outputs the list of available languages
     */
    function outputAvailableLanguages()
    {
        global $application;

        if (!is_array($this -> _languages) || empty($this -> _languages))
            return $this -> mTmplFiller -> fill(
                       'multilang/language_settings/',
                       'no_available_languages.tpl.html',
                       array()
                   );

        $result = '';
        foreach($this -> _languages as $v)
        {
            $template_contents = array(
                'Code'          => $v['lng'],
                'Name'          => $this -> outputLanguageName($v),
                'ActiveStatus'  => $this -> outputLanguageActiveStatus($v),
                'DefaultStatus' => $this -> outputLanguageDefaultStatus($v),
                'DeleteBox'     => $this -> outputLanguageDeleteBox($v)
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $result .= $this -> mTmplFiller -> fill(
                           'multilang/language_settings/',
                           'language.tpl.html',
                           array()
                       );
        }

        $result .= $this -> mTmplFiller -> fill(
                       'multilang/language_settings/',
                       'buttons.tpl.html',
                       array()
                   );

        return $result;
    }

    /**
     * Outputs the language name input box
     */
    function outputLanguageName($lng)
    {
        return '<input type="text"' .
               HtmlForm :: genInputTextField(
                   255, 'posted_data[' . $lng['lng'] . '][lng_name]', 32,
                   $lng['lng_name'],
                   ' class="form-control input-sm input-large" onchange="SelectLanguage(\'' .
                       $lng['lng'] . '\')"'
               ) . ' />';
    }

    /**
     * Outputs the language active select box
     */
    function outputLanguageActiveStatus($lng)
    {
        return HtmlForm::genDropdownSingleChoice(array(
                   "select_name"    => 'posted_data[' . $lng['lng'] . '][is_active]',
                   "selected_value" => $lng['is_active'],
                   "class" => "input-sm input-small",
                   "onChange"       => 'SelectLanguage(\'' . $lng['lng'] . '\')',
                   "values"         => array(
                                           array(
                                               'value'    => 'Y',
                                               'contents' => getMsg('ML',
                                                                    'ML_YES')
                                           ),
                                           array(
                                               'value'    => 'N',
                                               'contents' => getMsg('ML',
                                                                    'ML_NO')
                                           )
                                       )
               ));
    }

    /**
     * Outputs the language default radio button
     */
    function outputLanguageDefaultStatus($lng)
    {
        return '<input type="radio" name="default_lng" value="' . $lng['lng'] .
               '" onclick="ChangeDefault(this);" id="radio_' . $lng['lng'] .
               '"'. (($lng['is_default'] == 'Y') ? ' checked="checked"' : '') .
               ' />';
    }

    /**
     * Outputs the language delete checkbox
     */
    function outputLanguageDeleteBox($lng)
    {
        return HtmlForm :: genCheckbox(array(
                   "value"      => $lng['lng'],
                   "name"       => 'to_delete[' . $lng['lng'] . ']',
                   "onclick"    => 'HighLightLanguage(this)',
                   "id"         => 'select_' . $lng['lng'],
                   "is_checked" => ''
               ),
				"class='form-contrl input-sm'");
    }

    /**
     * Outputs 'Add new language' section
     */
    function outputNewLanguages()
    {
        global $application;

        if (!is_array($this -> _all_languages)
            || empty($this -> _all_languages))
            return '';

        $template_contents = array(
            'NewCode'          => $this -> _all_languages[0]['lng'],
            'NewLanguage'      => $this -> outputNewLanguage(),
            'NewActiveStatus'  => $this -> outputNewActiveStatus(),
            'NewDefaultStatus' => $this -> outputNewDefaultStatus(),
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'multilang/language_settings/',
                   'add_new_language.tpl.html',
                   array()
               );
    }

    /**
     * Outputs New_Language select box
     */
    function outputNewLanguage()
    {
        $values = '';
        foreach($this -> _all_languages as $v)
            $values[] = array('value' => $v['lng'],
                              'contents' => $v['lng_name']);

        return HtmlForm::genDropdownSingleChoice(array(
                   "select_name"    => 'new_lng[lng]',
                   "selected_value" => $this -> _all_languages[0]['lng'],
                   "onChange"       => 'document.getElementById(\'new_lng_code\').innerHTML = this.value;',
                   "values"         => $values
               ), 'style="width: 95%;"');
    }

    /**
     * Outputs New_Language active select box
     */
    function outputNewActiveStatus()
    {
        return HtmlForm::genDropdownSingleChoice(array(
                   "select_name"    => 'new_lng[is_active]',
                   "selected_value" => 'Y',
                   "onChange"       => '',
                   "values"         => array(
                                           array(
                                               'value'    => 'Y',
                                               'contents' => getMsg('ML',
                                                                    'ML_YES')
                                           ),
                                           array(
                                               'value'    => 'N',
                                               'contents' => getMsg('ML',
                                                                    'ML_NO')
                                           )
                                       )
               ));
    }

    /**
     * Outputs New_Language default select box
     */
    function outputNewDefaultStatus()
    {
        return HtmlForm::genDropdownSingleChoice(array(
                   "select_name"    => 'new_lng[is_default]',
                   "selected_value" => 'N',
                   "onChange"       => '',
                   "values"         => array(
                                           array(
                                               'value'    => 'Y',
                                               'contents' => getMsg('ML',
                                                                    'ML_YES')
                                           ),
                                           array(
                                               'value'    => 'N',
                                               'contents' => getMsg('ML',
                                                                    'ML_NO')
                                           )
                                       )
               ));
    }

    /**
     * Processes local tags
     */
    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this -> _Template_Contents);
    }

    var $mTmplFiller;
    var $_Template_Contents;
    var $_languages;
    var $_all_languages;
}