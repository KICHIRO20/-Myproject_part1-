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

class ML_ExportLabels
{
    function ML_ExportLabels()
    {
        // initializing the template engine
        $this -> mTmplFiller = new TmplFiller();

        // filling search filter
        $this -> setSearchFilter();

        // getting the list of languages
        $this -> _languages = modApiFunc('MultiLang', 'getLanguageList', false);
    }

    function setSearchFilter()
    {
        if (modApiFunc('Session', 'is_set', 'LABEL_FILTER'))
        {
            // Normally the filter can be found in the session
            $this -> _search_filter = modApiFunc('Session', 'get',
                                                 'LABEL_FILTER');

            // setting lng if it is not set
            if (!$this -> _search_filter['lng'])
                $this -> _search_filter['lng'] = modApiFunc('MultiLang',
                                                         'getDefaultLanguage');
        }
        else
        {
            // otherwise (abnormal!) set it to all labels
            $this -> _search_filter = array('asc_action' => 'ShowAllLabels');
            $this -> _search_filter['lng'] = modApiFunc('MultiLang',
                                                        'getDefaultLanguage');
        }
    }

    function output()
    {
        global $application;

        $total = modApiFunc('MultiLang', 'getLabelCount', 'all',
                            $this -> _search_filter['lng']);
        $found = modApiFunc('MultiLang', 'searchLabelCount',
                            $this -> _search_filter);

        $template_contents = array(
           'TotalCount'       => $total,
           'ResultCount'      => $found,
           'AllLabelField'    => $this -> outputAllLabelField($total, $found),
           'AllLabelBegin'    => ($total != $found)
                                     ? '<label for="all_labels">' : '',
           'AllLabelEnd'      => ($total != $found) ? '</label>' : '',
           'ResultLabelField' => $this -> outputResultLabelField($total,
                                                                 $found),
           'ResultLabelBegin' => ($total != $found)
                                     ? '<label for="found_labels">' : '',
           'ResultLabelEnd'   => ($total != $found) ? '</label>' : '',
           'ShowFields'       => $this -> outputFields()
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        return $this -> mTmplFiller -> fill(
                   'multilang/export_labels/',
                   'container.tpl.html',
                   array()
               );
    }

    function outputAllLabelField($total, $found)
    {
        if ($total == $found)
            return '<input type="hidden" name="labels" value="all" id="all_labels" checked="checked" />';

        return '<input type="radio" id="all_labels" name="labels" value="all" />';
    }

    function outputResultLabelField($total, $found)
    {
        if ($total == $found)
            return '';

        return '<input type="radio" id="found_labels" name="labels" value="found" checked="checked" />';
    }

    function outputFields()
    {
        global $application;

        $template_contents = array(
            'Checkbox' => '<input type="checkbox" name="label_prefix" value="prefix" disabled="disabled" checked="checked" id="l_prefix" />',
            'Label'    => '<label for="l_prefix">' .
                          getMsg('ML', 'ML_LABEL_TYPE') . '</label>'
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        $result = $this -> mTmplFiller -> fill(
                      'multilang/export_labels/',
                      'field.tpl.html',
                      array()
                  );

        $template_contents = array(
            'Checkbox' => '<input type="checkbox" name="label_name" value="name" disabled="disabled" checked="checked" id="l_name" />',
            'Label'    => '<label for="l_name">' .
                          getMsg('ML', 'ML_LABEL_NAME') . '</label>'
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        $result .= $this -> mTmplFiller -> fill(
                       'multilang/export_labels/',
                       'field.tpl.html',
                       array()
                   );

        $def_lng = modApiFunc('MultiLang', 'getLanguageList', false,
                              modApiFunc('MultiLang', 'getDefaultLanguage'));
        if ($def_lng)
            $def_lng = array('code' => $def_lng[0]['lng'],
                             'name' => $def_lng[0]['lng_name']);
        else
            $def_lng = array('code' => '0', 'name' => getMsg('ML', 'ML_DEFAULT'));

        $template_contents = array(
            'Checkbox' => '<input type="checkbox" name="lngs[]" value="' .
                          $def_lng['code'] .
                          '" checked="checked" id="lng_' .
                          $def_lng['code'] . '" />',
            'Label'    => '<label for="lng_' . $def_lng['code'] . '">' .
                          getMsg('ML', 'ML_EXPORT_LABEL_VALUE') . ' (' .
                          getMsg('ML', 'ML_IN') . ' ' . $def_lng['name'] .
                          ')</label>'
        );
        $this -> _Template_Contents = $template_contents;
        $application -> registerAttributes($this -> _Template_Contents);
        $result .= $this -> mTmplFiller -> fill(
                       'multilang/export_labels/',
                       'field.tpl.html',
                       array()
                   );

        if (!is_array($this -> _languages))
            return $result;

        foreach($this -> _languages as $v)
        {
            if ($v['lng'] == $def_lng['code'])
                continue;

            $template_contents = array(
                'Checkbox' => '<input type="checkbox" name="lngs[]" value="' .
                              $v['lng'] . '"' .
                              (($v['lng'] == $this -> _search_filter['lng'])
                              ? ' checked="checked"' : '')  . ' id="lng_' .
                              $v['lng'] . '" />',
                'Label'    => '<label for="lng_' . $v['lng'] . '">' .
                              getMsg('ML', 'ML_EXPORT_LABEL_VALUE') . ' (' .
                              getMsg('ML', 'ML_IN') . ' ' . $v['lng_name'] .
                              ')</label>'
            );
            $this -> _Template_Contents = $template_contents;
            $application -> registerAttributes($this -> _Template_Contents);
            $result .= $this -> mTmplFiller -> fill(
                           'multilang/export_labels/',
                           'field.tpl.html',
                           array()
                       );
        }

        return $result;
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this -> _Template_Contents);
    }

    var $_search_filter;
    var $mTmplFiller;
    var $_Template_Contents;
    var $_languages;
};
?>