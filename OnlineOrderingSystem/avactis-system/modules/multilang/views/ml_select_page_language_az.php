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
 * Definition of SelectLanguage viewer
 * The viewer is used to switch between language in admin zone
 */
class SelectPageLanguage
{
    /**
     * Constructor
     */
    function SelectPageLanguage()
    {
        // initializing the template engine
        $this -> mTmplFiller = new TmplFiller();
    }

    /**
     * The main function to output the viewer content.
     */
    function output($pages = 'DEFAULT', $views = 'DEFAULT', $actions = 'DEFAULT')
    {
        global $application;

        // getting the list of languages
        $this -> _languages = modApiFunc('MultiLang', 'getLanguageList', false);

        // if the list of languages is empty or exactly one language
        // then showing empty template
        if (!is_array($this -> _languages) || count($this -> _languages) <= 1)
            return $this -> mTmplFiller -> fill(
                       'multilang/select_page_language/',
                       'empty.tpl.html',
                       array()
                   );

        $template_contents = array(
            'ReturnURL'      => htmlspecialchars(modApiFunc('Request', 'selfURL')),
            'OnChangeAction' => $this -> showOnChangeAction(),
            'Languages'      => $this -> showLanguages(),
            'Pages'          => $this -> showData('pages', $pages),
            'Views'          => $this -> showData('views', $views),
            'Actions'        => $this -> showData('actions', $actions)
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'multilang/select_page_language/',
                   'container.tpl.html',
                   array()
               );
    }

    /**
     * Shows onchange action for the language select box
     */
    function showOnChangeAction()
    {

        return 'document.change_page_language.submit();';
    }

    /**
     * Shows array elements as hidden fields
     */
    function showData($field_name, $data)
    {
        $result = '';

        // setting the default values...
        if ($data == 'DEFAULT')
        {
            if ($field_name == 'pages')
                $data = array_pop(explode('/', $_SERVER['SCRIPT_NAME']));
            elseif ($field_name == 'views')
                $data = modApiFunc('Request', 'getValueByKey', 'page_view');
            elseif ($field_name == 'actions')
                $data = modApiFunc('Request', 'getCurrentAction');
        }

        // force $data to be an array
        if (!is_array($data))
            $data = array($data);

        // showing non-empty elements...
        foreach($data as $v)
            if ($v)
                $result .= '<input type="hidden" name="' . $field_name .
                           '[]" value="' . htmlspecialchars($v) . '" />' . "\n";

        return $result;
    }

    /**
     * Shows language list
     */
    function showLanguages()
    {
        global $application;

        if (!is_array($this -> _languages))
            return '';

        $result = '';
        foreach($this -> _languages as $v)
        {
            $template_contents = array(
                'Value'    => $v['lng'],
                'Language' => $v['lng_name'],
                'Selected' => (($v['lng'] == modApiFunc('MultiLang',
                                                        'getLanguage'))
                                  ? ' selected="selected"' : '')
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $result .= $this -> mTmplFiller -> fill(
                           'multilang/select_page_language/',
                           'language.tpl.html',
                           array()
                       );
        }

        return $result;
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
}