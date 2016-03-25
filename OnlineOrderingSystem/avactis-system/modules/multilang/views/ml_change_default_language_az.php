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
 * Definition of ChangeDefaultLanguage viewer
 * The viewer is used to change default language in Avactis
 */
class ChangeDefaultLanguage
{
    /**
     * Constructor
     */
    function ChangeDefaultLanguage()
    {
        // initializing the template engine
        $this -> mTmplFiller = new TmplFiller();

        // initializing data
        $this -> _data = array();
    }

    /**
     * The main function to output the viewer content.
     */
    function output()
    {
        global $application;

        $def_lng = modApiFunc('MultiLang', 'getDefaultLanguage');

        $request = &$application -> getInstance('Request');
        $new_lng = $request -> getValueByKey('target');

        if (!modApiFunc('MultiLang', 'checkLanguage', $new_lng, false))
        {
            // incorrect language -> forcing redirect to language list
            $req_to_redirect = new Request();
            $req_to_redirect -> setView('LanguageList');
            $application -> redirect($req_to_redirect);
        }

        $this -> _data['old_lng'] = modApiFunc('MultiLang',
                                               '_readLanguageNumber',
                                               $def_lng);
        $this -> _data['new_lng'] = modApiFunc('MultiLang',
                                               '_readLanguageNumber',
                                               $new_lng);

        // getting the number of new language records
        $this -> _data['new_total'] = modApiFunc('MultiLang',
                                                 'getTotalLanguageRecordNumber',
                                                 $this -> _data['new_lng']);

        if (($this -> _data['old_lng'] <= 0
             && $this -> _data['new_total'] <= 0)
            || ($this -> _data['old_lng'] == $this -> _data['new_lng']))
        {
            // no old language and no new language data -> nothing to do...
            // old language = new language -> nothing to do
            modApiFunc('MultiLang', '_changeDefaultLanguage', $new_lng);
			exit("<script>window.location.replace('store_settings_languages.php');</script>");
        }

        // getting the language names
        if ($this -> _data['old_lng'] > 0)
        {
            $tmp = modApiFunc('MultiLang', 'getLanguageList',
                              false, $def_lng);
            $this -> _data['old_lng_name'] = $tmp[0]['lng_name'];
        }
        else
        {
            $this -> _data['old_lng_name'] = getMsg('ML', 'ML_DEFAULT');
        }

        $tmp = modApiFunc('MultiLang', 'getLanguageList',
                          false, $new_lng);
        $this -> _data['new_lng_name'] = $tmp[0]['lng_name'];

        // getting the number of records for all multilang labels
        if ($this -> _data['old_lng'])
            $this -> _data['old'] = modApiFunc('MultiLang',
                                               'getAllMLRecordNumbers');
        else
            $this -> _data['old'] = array();

        // getting the number of multlang records
        $this -> _data['new'] = modApiFunc('MultiLang',
                                           'getLanguageRecordNumbers',
                                           $this -> _data['new_lng']);

        // getting the sum
        $this -> _data['old_total'] = 0;
        foreach($this -> _data['old'] as $class => $tables)
            foreach($tables as $table => $fields)
                foreach($fields as $field => $value)
                    $this -> _data['old_total'] += $value;

        $template_contents = array(
            'CurrentDefLng'   => $this -> _data['old_lng_name'],
            'DesiredDefLng'   => $this -> _data['new_lng_name'],
            'DesiredLngCode'  => $new_lng,
            'TotalNewRecords' => $this -> _data['new_total'],
            'TotalOldRecords' => $this -> _data['old_total'],
            'NewDetails'      => $this -> outputDetails('new'),
            'OldDetails'      => $this -> outputDetails('old')
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'multilang/change_default_language/',
                   'container.tpl.html',
                   array()
               );
    }

    /**
     * Outputs details for old default language
     */
    function outputDetails($type)
    {
        global $application;

        if ($type != 'old' && $type != 'new')
            return '<tr><td>&nbsp;</td></tr>';

        $result = '';
        if (empty($this -> _data[$type]))
            return '<tr><td>&nbsp;</td></tr>';

        foreach($this -> _data[$type] as $class => $tables)
        {
            $template_contents = array(
                'Title'   => $class,
                'BGColor' => '#DDDDDD'
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $result .= $this -> mTmplFiller -> fill(
                           'multilang/change_default_language/',
                           'details_header.tpl.html',
                           array()
                       );

            foreach($tables as $table => $fields)
            {
                $template_contents = array(
                    'Title'   => '&nbsp;&nbsp;' . $table,
                    'BGColor' => '#EEEEEE'
                );
                $this -> _Template_Contents = $template_contents;
                $application -> registerAttributes(
                                    $this -> _Template_Contents
                                );
                $result .= $this -> mTmplFiller -> fill(
                               'multilang/change_default_language/',
                               'details_header.tpl.html',
                               array()
                           );

                foreach($fields as $field => $value)
                {
                    $template_contents = array(
                        'Title'    => $field,
                        'BoxType'  => $type,
                        'BoxLabel' => modApiFunc('MultiLang', 'mapMLField',
                                                 $table, $field, $class),
                        'BoxValue' => $value
                    );
                    $this -> _Template_Contents = $template_contents;
                    $application -> registerAttributes(
                                        $this -> _Template_Contents
                                    );
                    $result .= $this -> mTmplFiller -> fill(
                               'multilang/change_default_language/',
                               'details_data.tpl.html',
                               array()
                           );
                }
            }
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
    var $_data;
}