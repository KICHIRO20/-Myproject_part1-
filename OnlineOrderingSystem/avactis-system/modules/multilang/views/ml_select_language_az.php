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
class SelectLanguage
{
    /**
     * Constructor
     */
    function SelectLanguage()
    {
        // initializing the template engine
        $this -> mTmplFiller = new TmplFiller();
    }

    /**
     * The main function to output the viewer content.
     */
    function output()
    {
        global $application;

        // getting the list of languages
        $this -> _languages = modApiFunc('MultiLang', 'getLanguageList', false);

        // if the list of languages is empty or exactly one language
        // then showing empty template
        if (!is_array($this -> _languages) || count($this -> _languages) <= 1)
            return $this -> mTmplFiller -> fill(
                       'multilang/select_language/',
                       'empty.tpl.html',
                       array()
                   );

        $template_contents = array(
//            'OnChangeAction' => $this -> showOnChangeAction(),
            'Languages'      => $this -> showLanguages(),
            'CurrentLanguage' => modApiFunc('MultiLang','getLanguage'),
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'multilang/select_language/',
                   'container.tpl.html',
                   array()
               );
    }

    /**
     * Shows onchange action for the language select box
     */
    function showOnChangeAction($value)
    {

        return "document.location='admin.php?asc_action=ChangeLanguage&lng=".$value."&returnURL="
               . urlencode(modApiFunc('Request', 'selfURL')) . "'";
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
				'NewLanguageURL' => $this -> showOnChangeAction($v['lng']),
/*                'Selected' => (($v['lng'] == modApiFunc('MultiLang',
                                                        'getLanguage'))
                                  ? ' selected="selected"' : '')*/
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $result .= $this -> mTmplFiller -> fill(
                           'multilang/select_language/',
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