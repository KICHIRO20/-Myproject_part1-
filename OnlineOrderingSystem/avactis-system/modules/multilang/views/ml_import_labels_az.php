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

class ML_ImportLabels
{
    function ML_ImportLabels()
    {
        // initializing the template engine
        $this -> mTmplFiller = new TmplFiller();

        // getting the list of languages
        $this -> _languages = modApiFunc('MultiLang', 'getLanguageList', false);

        // getting the list of label types
        $this -> _modules = modApiFunc('MultiLang', 'getResourceModuleList');
    }

    function output()
    {
        global $application;

        $def_lng = modApiFunc('MultiLang', 'getDefaultLanguage');
        if (!$def_lng)
            $def_lng = 'default';

        $template_contents = array(
            'FillLanguageArray' => $this -> outputLanguageArray(),
            'FillTypeArray'     => $this -> outputTypeArray(),
            'ColumnsInfo'       => $this -> outputColumnsInfo(),
            'InfoByLanguage'    => $this -> outputInfoByLanguage(),
            'InfoByType'        => $this -> outputInfoByType(),
            'DefaultLanguage'   => $def_lng
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'multilang/import_labels/',
                   'container.tpl.html',
                   array()
               );
    }

    function outputLanguageArray()
    {
        $result = '';

        if (!is_array($this -> _languages) || empty($this -> _languages))
        {
            // show default language only
            return 'Res["language"]["default"] = new Array();' . "\n" .
                   'Res["language"]["default"]["new"] = 0' . "\n" .
                   'Res["language"]["default"]["found"] = 0' . "\n";

        }

        foreach($this -> _languages as $v)
        {
            $result .= 'Res["language"]["' . $v['lng'] .
                       '"] = new Array();' . "\n" .
                       'Res["language"]["' . $v['lng'] .
                       '"]["new"] = 0' . "\n" .
                       'Res["language"]["' . $v['lng'] .
                       '"]["found"] = 0' . "\n";
        }

        return $result;
    }

    function outputTypeArray()
    {
        $result = '';

        if (is_array($this -> _modules))
            foreach($this -> _modules as $k => $v)
            {
                $result .= 'Res["type"]["' . $k . '"] = new Array();' . "\n" .
                           'Res["type"]["' . $k . '"]["new"] = 0' . "\n" .
                           'Res["type"]["' . $k . '"]["found"] = 0' . "\n";
            }

        $result .= 'Res["type"]["CZ"] = new Array();' . "\n" .
                   'Res["type"]["CZ"]["new"] = 0' . "\n" .
                   'Res["type"]["CZ"]["found"] = 0' . "\n";

        $result .= 'Res["type"]["CZ_CUSTOM"] = new Array();' . "\n" .
                   'Res["type"]["CZ_CUSTOM"]["new"] = 0' . "\n" .
                   'Res["type"]["CZ_CUSTOM"]["found"] = 0' . "\n";

        return $result;
    }

    function outputColumnsInfo()
    {
        global $application;

        if (!is_array($this -> _languages) || empty($this -> _languages))
        {
            // show default language only
            $template_contents = array(
                'RowName'  => 'i_columns_default',
                'RowTitle' => getMsg('ML', 'ML_DEFAULT')
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            return $this -> mTmplFiller -> fill(
                       'multilang/import_labels/',
                       'info_row.tpl.html',
                       array()
                   );
        }

        $result = '';
        $def_lng = modApiFunc('MultiLang', 'getDefaultLanguage');
        foreach($this -> _languages as $v)
        {
            $template_contents = array(
                'RowName'  => 'i_columns_' . $v['lng'],
                'RowTitle' => $v['lng_name']
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $result .= $this -> mTmplFiller -> fill(
                           'multilang/import_labels/',
                           'info_row.tpl.html',
                           array()
                       );
        }

        return $result;
    }

    function outputInfoByLanguage()
    {
        global $application;

        if (!is_array($this -> _languages) || empty($this -> _languages))
        {
            // show default language only
            $template_contents = array(
                'BlockName'  => 'i_language_default',
                'BlockTitle' => getMsg('ML', 'ML_DEFAULT'),
                'BlockValue' => 'default'
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            return $this -> mTmplFiller -> fill(
                       'multilang/import_labels/',
                       'info_block.tpl.html',
                       array()
                   );
        }

        $result = '';
        $def_lng = modApiFunc('MultiLang', 'getDefaultLanguage');
        foreach($this -> _languages as $v)
        {
            $template_contents = array(
                'BlockName'  => 'i_language_' . $v['lng'],
                'BlockTitle' => $v['lng_name'],
                'BlockValue' => $v['lng']
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $result .= $this -> mTmplFiller -> fill(
                           'multilang/import_labels/',
                           'info_block.tpl.html',
                           array()
                       );
        }

        return $result;
    }

    function outputInfoByType()
    {
        global $application;

        $result = '';

        if (is_array($this -> _modules))
            foreach($this -> _modules as $k => $v)
            {
                $template_contents = array(
                    'BlockName'  => 'i_type_' . $k,
                    'BlockTitle' => $v['module'],
                    'BlockValue' => $k
                );
                $this -> _Template_Contents = $template_contents;
                $application -> registerAttributes($this -> _Template_Contents);
                $result .= $this -> mTmplFiller -> fill(
                               'multilang/import_labels/',
                               'info_block.tpl.html',
                               array()
                           );
            }

        $template_contents = array(
            'BlockName'  => 'i_type_CZ',
            'BlockTitle' => getMsg('ML', 'ML_AVACTIS_LABELS'),
            'BlockValue' => 'CZ'
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        $result .= $this -> mTmplFiller -> fill(
                       'multilang/import_labels/',
                       'info_block.tpl.html',
                       array()
                   );

        $template_contents = array(
            'BlockName'  => 'i_type_CZ_CUSTOM',
            'BlockTitle' => getMsg('ML', 'ML_CUSTOM_LABELS'),
            'BlockValue' => 'CZ_CUSTOM'
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        $result .= $this -> mTmplFiller -> fill(
                       'multilang/import_labels/',
                       'info_block.tpl.html',
                       array()
                   );

        return $result;
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this -> _Template_Contents);
    }

    var $mTmplFiller;
    var $_Template_Contents;
    var $_languages;
    var $_modules;
};
?>