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

class DataReaderRecordsDB extends DataReaderDefault
{
    function DataReaderRecordsDB()
    {
    }

    function initWork($settings)
    {
        $this -> clearWork();

        if (!$settings['bulk_number'])
            $settings['bulk_number'] = 1000;

        $this -> _settings = $settings;

        $total_count = 0;

        $tmp_labels = array();
        foreach($settings['new_data'] as $v)
            $tmp_labels[] = $v['label'];
        $total_count += modApiFunc('MultiLang', 'getTotalLanguageRecordNumber',
                                   $settings['new_lng'], $tmp_labels);

        if ($settings['old_lng'])
        {
            $tmp_labels = array();
            foreach($settings['old_data'] as $v)
                $tmp_labels[] = $v['label'];
            $tmp = modApiFunc('MultiLang', 'getAllMLRecordNumbers', $tmp_labels);
            foreach($tmp as $class => $tables)
                foreach($tables as $table => $fields)
                    foreach($fields as $field => $value)
                        $total_count += $value;
        }

        $this -> _process_info['status'] = 'INITED';
        $this -> _process_info['items_count'] = $total_count;
        $this -> _process_info['items_processing'] = 0;
        $this -> _sent_count = 0;
    }

    function doWork()
    {
        $this -> _process_info['status'] = 'HAVE_MORE_DATA';

        $total_count = 0;

        $tmp_labels = array();
        foreach($this -> _settings['new_data'] as $v)
            $tmp_labels[] = $v['label'];
        $total_count += modApiFunc('MultiLang', 'getTotalLanguageRecordNumber',
                                   $this -> _settings['new_lng'], $tmp_labels);

        if ($this -> _settings['old_lng'])
        {
            $tmp_labels = array();
            foreach($this -> _settings['old_data'] as $v)
                $tmp_labels[] = $v['label'];
            $tmp = modApiFunc('MultiLang', 'getAllMLRecordNumbers',
                              $tmp_labels);
            foreach($tmp as $class => $tables)
                foreach($tables as $table => $fields)
                    foreach($fields as $field => $value)
                        $total_count += $value;
        }

        $this -> _process_info['items_count'] = $total_count;

        // statuses:
        //     0 - nothing
        //     1 - from DB to multilang_data
        //     2 - from multilang_data to DB
        $data = array('status' => 0, 'data' => array());

        if ($this -> _sent_count < $this -> _process_info['items_count'])
        {
            // we have some more data, trying to retrieve it...
            // the scheme of getting data is the following:
            // 1. all old data -> multilang_data table with tmp language
            //     1.1. table by table (each operation touches one table only)
            // 2. all new data -> DB
            //     2.1. table by table (each operation touches one table only)

            // looking for current label in old_data
            foreach($this -> _settings['old_data'] as $k => $v)
                if (!$v['finished'])
                {
                    // the label is found...
                    // getting the data
                    $portion = modApiFunc('MultiLang', 'getMLRecordPortion',
                                          $v['label'], $v['pos'],
                                          $this -> _settings['bulk_number']);

                    if (!empty($portion))
                    {
                        // portion is not empty!
                        // we need to process the data
                        $data['status'] = 1;
                        $data['label'] = $v['label'];
                        $data['data'] = $portion;

                        // setting the new position for the label
                        $this -> _settings['old_data'][$k]['pos'] += count($portion);

                        // checking if there are still data for the label
                        if (count($portion) < $this -> _settings['bulk_number'])
                            $this -> _settings['old_data'][$k]['finished'] = true;

                        // returning the data for processing
                        $this -> _sent_count += count($portion);
                        $this -> _process_info['items_processing'] = $this -> _sent_count;
                        if ($this -> _sent_count >= $this -> _process_info['items_count'])
                            $this -> _process_info['status'] = 'NO_MORE_DATA';
                        return $data;
                    }
                    else
                    {
                        // no data left for the label
                        $this -> _settings['old_data'][$k]['finished'] = true;
                    }
                }

            // we are here because all the data from DB has been copied
            // processing the data from multilang_data table

            // looking for current label in new_data
            foreach($this -> _settings['new_data'] as $k => $v)
                if (!$v['finished'])
                {
                    // the label is found...
                    // getting the data
                    $portion = modApiFunc('MultiLang',
                                          'getLanguageRecordPortion',
                                          $this -> _settings['new_lng'],
                                          $v['label'], $v['pos'],
                                          $this -> _settings['bulk_number']);

                    if (!empty($portion))
                    {
                        // portion is not empty!
                        // we need to process the data
                        $data['status'] = 2;
                        $data['label'] = $v['label'];
                        $data['data'] = $portion;

                        // setting the new position for the label
                        $this -> _settings['new_data'][$k]['pos'] += count($portion);

                        // checking if there are still data for the label
                        if (count($portion) < $this -> _settings['bulk_number'])
                            $this -> _settings['new_data'][$k]['finished'] = true;

                        // returning the data for processing
                        $this -> _sent_count += count($portion);
                        $this -> _process_info['items_processing'] = $this -> _sent_count;
                        if ($this -> _sent_count >= $this -> _process_info['items_count'])
                            $this -> _process_info['status'] = 'NO_MORE_DATA';
                        return $data;
                    }
                    else
                    {
                        // no data left for the label
                        $this -> _settings['new_data'][$k]['finished'] = true;
                    }
                }

            // if we are still here then no more data to process
            $this -> _process_info['items_processing'] = $this -> _sent_count;
            $this -> _process_info['status'] = 'NO_MORE_DATA';
        }
        else
        {
            $this -> _process_info['items_processing'] = $this -> _sent_count;
            $this -> _process_info['status'] = 'NO_MORE_DATA';
        }

        return $data;
    }

    function finishWork()
    {
        $this -> clearWork();
    }

    function loadWork()
    {
        if (modApiFunc('Session', 'is_set', 'DataReaderRecordsDBSettings'))
            $this -> _settings = modApiFunc('Session', 'get',
                                            'DataReaderRecordsDBSettings');
        if (modApiFunc('Session','is_set','DataReaderRecordsDBSentCount'))
            $this -> _sent_count = modApiFunc('Session', 'get',
                                              'DataReaderRecordsDBSentCount');
    }

    function clearWork()
    {
        modApiFunc('Session', 'un_set', 'DataReaderRecordsDBSettings');
        modApiFunc('Session', 'un_set', 'DataReaderRecordsDBSentCount');

        $this -> _sent_count = 0;
        $this -> _settings = null;
    }

    function saveWork()
    {
        modApiFunc('Session', 'set', 'DataReaderRecordsDBSettings',
                   $this -> _settings);
        modApiFunc('Session', 'set', 'DataReaderRecordsDBSentCount',
                   $this-> _sent_count);
    }

    var $_sent_count;
    var $_settings;
}

?>