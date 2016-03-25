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
 * The viewer is used to switch between language in customer zone
 */
class SelectLanguage
{
    /**
     * Constructor
     */
    function SelectLanguage()
    {
        global $application;

        // initializing the template engine
        $this -> mTmplFiller = new TemplateFiller();

        $this -> _templates = array(
            'container' => 'SelectLanguageContainer',
            'empty'     => 'SelectLanguageEmpty',
            'language'  => 'SelectLanguageLanguage'
        );

        // hiding the viewer content if any error
        $this -> NoView = false;
        if ($application -> issetBlockTagFatalErrors('SelectLanguage'))
            $this -> NoView = true;
    }

    /**
     * Returns the template format
     */
    function getTemplateFormat()
    {
        $format = array(
            'layout-file' => 'select-language-block.ini',
            'files'       => array(
                'SelectLanguageContainer' => TEMPLATE_FILE_SIMPLE,
                'SelectLanguageEmpty'     => TEMPLATE_FILE_SIMPLE,
                'SelectLanguageLanguage'  => TEMPLATE_FILE_SIMPLE,
            ),
            'options'     => array(
            )
        );
        return $format;
    }

    /**
     * The main function to output the viewer content.
     */
    function output($lang = '')
    {
        global $application;

        // showing nothing if any error
        if ($this -> NoView)
            return '';

        // getting the list of languages
        $this -> _languages = modApiFunc('MultiLang', 'getLanguageList', true);

        if ($lang)
            return $this -> showURLs($lang);

        // setting up the template engine
        $template_block = $application -> getBlockTemplate('SelectLanguage');
        $this -> mTmplFiller -> setTemplate($template_block);
        $this->_index_url = getpageurl('index');

        // if the list of languages is empty or exactly one language
        // then showing empty template
        if (!is_array($this -> _languages) || count($this -> _languages) <= 1)
            return $this -> mTmplFiller -> fill($this -> _templates['empty']);

        $_tags = array(
            'OnChangeAction' => $this -> showOnChangeAction(),
            'Languages'      => $this -> showLanguages(),
            'DefaultURL'     => urlencode(modApiFunc('Request', 'selfURL')),
            'URLs'           => $this -> showURLs()
        );
        $this -> _Template_Contents = $_tags;
        $application -> registerAttributes($this -> _Template_Contents);

        return $this -> mTmplFiller -> fill($this -> _templates['container']);
    }

    /**
     * Shows multilang category URLs if exist
     */
    function showCategoryURLs()
    {
        if (count($this -> _languages) <= 1)
            return '';

        $urls = array();

        $_lang = modApiFunc('MultiLang', 'getLanguage');

        $def_lng = modApiFunc('MultiLang', 'getDefaultLanguage');
        modApiFunc('MultiLang', 'setLanguage', $def_lng);
        $request = new Request;
        $request -> setView('ProductList');
        $request -> setAction('SetCurrCat');
        $request -> setKey('category_id', modApiFunc('Request', 'getValueByKey', 'category_id'));
        $def_url = urlencode($request -> getURL());
        $urls[$def_lng] = $def_url;

        foreach($this -> _languages as $v)
        {
            if ($v['lng'] == $def_lng)
                continue;

            modApiFunc('MultiLang', 'setLanguage', $v['lng']);
            $request = new Request;
            $request -> setView('ProductList');
            $request -> setAction('SetCurrCat');
            $request -> setKey('category_id', modApiFunc('Request', 'getValueByKey', 'category_id'));
            $url = urlencode($request -> getURL());
            if ($url != $def_url)
                $urls[$v['lng']] = $url;
        }

        modApiFunc('MultiLang', 'setLanguage', $_lang);

        return $urls;
    }

    /**
     * Shows multilang product URLs if exist
     */
    function showProductURLs()
    {
        if (count($this -> _languages) <= 1)
            return '';

        $urls = array();

        $_lang = modApiFunc('MultiLang', 'getLanguage');

        $def_lng = modApiFunc('MultiLang', 'getDefaultLanguage');
        modApiFunc('MultiLang', 'setLanguage', $def_lng);
        $request = new Request;
        $request -> setView('ProductInfo');
        $request -> setAction('SetCurrentProduct');
        $request -> setKey('prod_id', modApiFunc('Request', 'getValueByKey', 'prod_id'));
        $def_url = urlencode($request -> getURL());
        $urls[$def_lng] = $def_url;

        foreach($this -> _languages as $v)
        {
            if ($v['lng'] == $def_lng)
                continue;

            modApiFunc('MultiLang', 'setLanguage', $v['lng']);
            $request = new Request;
            $request -> setView('ProductInfo');
            $request -> setAction('SetCurrentProduct');
            $request -> setKey('prod_id', modApiFunc('Request', 'getValueByKey', 'prod_id'));
            $url = urlencode($request -> getURL());
            if ($url != $def_url)
                $urls[$v['lng']] = $url;
        }

        modApiFunc('MultiLang', 'setLanguage', $_lang);

        return $urls;
    }

    /**
     * Shows multilang paginator URLs if exist
     */
    function showPaginatorURLs()
    {
        if (count($this -> _languages) <= 1)
            return '';

        $urls = array();

        $_lang = modApiFunc('MultiLang', 'getLanguage');

        $def_lng = modApiFunc('MultiLang', 'getDefaultLanguage');
        modApiFunc('MultiLang', 'setLanguage', $def_lng);
        $request = new Request;
        $request -> setView('ProductList');
        $request -> setAction('Paginator_SetPage');
        $request -> setKey('pgname', modApiFunc('Request', 'getValueByKey', 'pgname'));
        $request -> setKey('pgnum', modApiFunc('Request', 'getValueByKey', 'pgnum'));
        $def_url = urlencode($request -> getURL());
        $urls[$def_lng] = $def_url;

        foreach($this -> _languages as $v)
        {
            if ($v['lng'] == $def_lng)
                continue;

            modApiFunc('MultiLang', 'setLanguage', $v['lng']);
            $request = new Request;
            $request -> setView('ProductList');
            $request -> setAction('Paginator_SetPage');
            $request -> setKey('pgname', modApiFunc('Request', 'getValueByKey', 'pgname'));
            $request -> setKey('pgnum', modApiFunc('Request', 'getValueByKey', 'pgnum'));
            $url = urlencode($request -> getURL());
            if ($url != $def_url)
                $urls[$v['lng']] = $url;
        }

        modApiFunc('MultiLang', 'setLanguage', $_lang);

        return $urls;
    }

    function getJSValues($urls)
    {
        $value = '';
        if (count($urls) > 1)
            foreach($urls as $k => $v)
                $value .= '    URLs[\'' . $k . '\'] = \'' . $v . '\';' . "\n";

        return $value;
    }

    /**
     * Shows multilang URLs if exist
     */
    function showURLs($lang = '')
    {
        $urls = '';

        $action = modApiFunc('Request', 'getValueByKey', 'asc_action');
        switch($action)
        {
            case 'SetCurrCat':
                $urls = $this -> showCategoryURLs();
                break;

            case 'Paginator_SetPage':
                $pgname = modApiFunc('Request', 'getValueByKey', 'pgname');
                if (_ml_substr($pgname, 0, 18) == 'Catalog_ProdsList_')
                    $urls = $this -> showPaginatorURLs();
                break;

            case 'SetCurrentProduct':
                $urls = $this -> showProductURLs();
                break;
        }

        if ($lang)
        {
            if (isset($urls[$lang]))
                return $urls[$lang];

            if (isset($urls[modApiFunc('MultiLang', 'getDefaultLanguage')]))
                return $urls[modApiFunc('MultiLang', 'getDefaultLanguage')];

            return urlencode(modApiFunc('Request', 'selfURL'));
        }

        $value = $this -> getJSValues($urls);

        return $value;
    }

    /**
     * Shows onchange action for the language select box
     */
    function showOnChangeAction()
    {
        if(empty($this->_index_url)) $this->_index_url = getpageurl('index');
        return "document.location='".$this->_index_url."?asc_action=ChangeLanguage&amp;lng=' + this.value + '&amp;returnURL=' + getReturnURL(this.value)";
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
            $_tags = array(
                'Value'    => $v['lng'],
                'Language' => $v['lng_name'],
                'Selected' => (($v['lng'] == modApiFunc('MultiLang',
                                                        'getLanguage'))
                                  ? ' selected="selected"' : '')
            );
            $this -> _Template_Contents = $_tags;
            $application -> registerAttributes($this -> _Template_Contents);
            $result .= $this -> mTmplFiller -> fill($this -> _templates['language']);
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

    var $NoView;
    var $mTmplFiller;
    var $_Template_Contents;
    var $_templates;
    var $_languages;
    var $_index_url = null;
}