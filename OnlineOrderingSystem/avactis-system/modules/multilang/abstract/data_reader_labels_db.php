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

loadClass('DataReaderDefault');

class DataReaderLabelsDB extends DataReaderDefault
{
    function DataReaderLabelsDB()
    {
    }

    function initWork($settings)
    {
        $this -> clearWork();
        $this -> _settings = $settings;

        if (!isset($settings['languages']))
            $this -> _settings['languages'] = array();

        $this -> _settings['lng_numbers'] = array();
        foreach($this -> _settings['languages'] as $k => $v)
            $this -> _settings['lng_numbers'][$k] = modApiFunc(
                                                        'MultiLang',
                                                        '_readLanguageNumber',
                                                        $v
                                                    );

        if ($settings['labels'] == 'found' &&
            modApiFunc('Session', 'is_set', 'LABEL_FILTER'))
        {
            // Loading the filter from the session
            $this -> _search_filter = modApiFunc('Session', 'get',
                                                 'LABEL_FILTER');

            // setting lng if it is not set
            if (!$this -> _search_filter['lng'])
                $this -> _search_filter['lng'] = modApiFunc('MultiLang',
                                                         'getDefaultLanguage');
        }
        else
        {
            $this -> _search_filter = array('asc_action' => 'ShowAllLabels');
            $this -> _search_filter['lng'] = modApiFunc('MultiLang',
                                                        'getDefaultLanguage');
        }

        $this -> _process_info['status'] = 'INITED';
        $this -> _process_info['items_count'] = modApiFunc(
                                                    'MultiLang',
                                                    'searchLabelCount',
                                                    $this -> _search_filter
                                                );
        $this -> _process_info['items_processing'] = 0;
        $this -> _sent_count = 0;
    }

    function doWork()
    {
        $this -> _process_info['items_count'] = modApiFunc(
                                                    'MultiLang',
                                                    'searchLabelCount',
                                                    $this -> _search_filter
                                                );
        $this -> _process_info['status'] = 'HAVE_MORE_DATA';

        $data = array();

        if ($this -> _sent_count < $this -> _process_info['items_count'])
        {
            // we have some more data to export
            // read it by portions of 500 labels (not one by one)
            $this -> _search_filter['limit'] = array($this -> _sent_count, 500);

            $pre_data = modApiFunc('MultiLang', 'searchLabels',
                                   $this -> _search_filter);

            // getting the label for other languages
            $label = modApiFunc('MultiLang', 'mapMLField',
                                'resource_labels', 'res_text', 'Resources');

            $def_lng = modApiFunc('MultiLang', 'getDefaultLanguage');

            $translations = array();
            foreach($this -> _settings['languages'] as $kk => $vv)
                if ($vv != $def_lng && $vv != $this -> _search_filter['lng'])
                    $translations[$vv] = modApiFunc(
                        'MultiLang',
                        'getLabelTranslationByIDs',
                        $pre_data,
                        $this -> _settings['lng_numbers'][$kk]
                    );

            if (is_array($pre_data) && !empty($pre_data))
            {
                foreach($pre_data as $v)
                {
                    $new_data = array(
                        $this -> _settings['headers'][0] => $v['prefix'],
                        $this -> _settings['headers'][1] => $v['label']
                    );
                    foreach($this -> _settings['languages'] as $kk => $vv)
                    {
                        $h = $this -> _settings['headers'][2 + $kk];
                        if ($vv == $def_lng)
                        {
                            // default language
                            $new_data[$h] = $v['def_value'];
                        }
                        elseif ($vv == $this -> _search_filter['lng'])
                        {
                            // active language
                            if ($v['value'] !== null)
                            {
                                $new_data[$h] = $v['value'];
                                // if value is empty -> set it to a space
                                if (!$new_data[$h])
                                    $new_data[$h] = ' ';
                            }
                            else
                            {
                                $new_data[$h] = '';
                                // $new_data[$h] = $v['def_value'];
                            }
                        }
                        else
                        {
                            if (!isset($translations[$vv][$v['id']]))
                            {
                                $new_data[$h] = '';
                                //$new_data[$h] = $v['def_value'];
                            }
                            else
                            {
                                $new_data[$h] = $translations[$vv][$v['id']];
                                // if value is empty -> set it to a space
                                if (!$new_data[$h])
                                    $new_data[$h] = ' ';
                            }
                        }
                    }
                    $data[] = $new_data;
                }
                $this -> _sent_count += count($pre_data);
                $this -> _process_info['items_processing'] = $this->_sent_count;
                if ($this -> _sent_count >= $this -> _process_info['items_count'])
                    $this -> _process_info['status'] = 'NO_MORE_DATA';
            }
            else
            {
                $this -> _process_info['status'] = 'NO_MORE_DATA';
            }

            // removing the limits
            unset($this -> _search_filter['limit']);

            return $data;
        }
        else
        {
            $this -> _process_info['items_processing'] = $this -> _sent_count;
            $this -> _process_info['status'] = 'NO_MORE_DATA';
            return $data;
        }
    }

    function finishWork()
    {
        $this -> clearWork();
    }

    function loadWork()
    {
        if (modApiFunc('Session', 'is_set', 'DataReaderLabelsDBSettings'))
            $this -> _settings = modApiFunc('Session', 'get',
                                            'DataReaderLabelsDBSettings');
        if (modApiFunc('Session', 'is_set', 'DataReaderLabelsDBFilter'))
            $this -> _search_filter = modApiFunc('Session', 'get',
                                                 'DataReaderLabelsDBFilter');
        if (modApiFunc('Session','is_set','DataReaderLabelsDBSentCount'))
            $this -> _sent_count = modApiFunc('Session', 'get',
                                              'DataReaderLabelsDBSentCount');
    }

    function clearWork()
    {
        modApiFunc('Session', 'un_set', 'DataReaderLabelsDBSettings');
        modApiFunc('Session', 'un_set', 'DataReaderLabelsDBFilter');
        modApiFunc('Session', 'un_set', 'DataReaderLabelsDBSentCount');

        $this -> _last = '';
        $this -> _sent_count = 0;
        $this -> _settings = null;
        $this -> _search_filter = array('asc_action' => 'ShowAllLabels');
        $this -> _search_filter['lng'] = modApiFunc('MultiLang',
                                                    'getDefaultLanguage');
    }

    function saveWork()
    {
        modApiFunc('Session', 'set', 'DataReaderLabelsDBSettings',
                   $this -> _settings);
        modApiFunc('Session', 'set', 'DataReaderLabelsDBFilter',
                   $this -> _search_filter);
        modApiFunc('Session', 'set', 'DataReaderLabelsDBSentCount',
                   $this-> _sent_count);
    }

    var $_sent_count;
    var $_settings;
    var $_search_filter;
}

?>