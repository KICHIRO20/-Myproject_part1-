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

loadClass('DataWriterDefault');

class DataWriterLabelsDB extends DataWriterDefault
{
    function DataWriterLabelsDB()
    {
    }

    function initWork($settings)
    {
        $this -> clearWork();
        $this -> _settings = $settings;
        $this -> _process_info['status'] = 'INITED';
    }

    function doWork($data)
    {
        global $application;

        if (!is_array($data))
            return;

        $data = $data['item_data'];
        if (!is_array($data))
            return;

        $label_types = array();
        $label_names = array();
        $lngs = array();
        foreach($data as $record)
            if (isset($record['labeltype']) && isset($record['labelname']))
            {
                $label_types[] = $record['labeltype'];
                $label_names[] = $record['labelname'];
                if ($record['lng'] != $this -> _settings['def_lng']
                    && $record['lng'] != 'default')
                    $lngs[$record['lng']] = $record['lng'];
            }

        $meta_info = modApiFunc('Resources', 'getMetaByShortNames',
                                $label_types);

        $label_ids = modApiFunc('Resources', 'getMsgIDs',
                                $label_types, $label_names);

        $label = modApiFunc('MultiLang', 'mapMLField',
                            'resource_labels', 'res_text', 'Resources');

        $trns = array();
        foreach($lngs as $v)
            $trns[$v]  = modApiFunc('MultiLang',
                                    'getLabelTranslationByIDs',
                                    array_values($label_ids), $v);

        foreach($data as $record)
        {
            if (!is_array($record))
                continue;

            if (!isset($record['labeltype']) || !isset($record['labelname'])
                || !$record['labeltype'] || !$record['labelname']
                || !isset($record['labelid']))
                continue;

            if (!isset($meta_info[$record['labeltype']]))
                continue;

            // filtering in total
            if ($record['labelid'] > 0
                && $this -> _settings['filter']['tt']['found'] == '-')
                continue;

            if ($record['labelid'] <= 0
                && $this -> _settings['filter']['tt']['new'] == '-')
                continue;

            // filtering by type
            // separating avactis and custom labels
            $l_type = $record['labeltype'];
            if ($record['labeltype'] == 'CZ'
                && substr($record['labelname'], 0, 7) == "CUSTOM_")
                $l_type = 'CZ_CUSTOM';

            $found = ($record['labelid'] > 0);
            if ($record['lng'] != $this -> _settings['def_lng'])
                $found = $found &&
                         isset($trns[$record['lng']][$record['labelid']]);

            if ($found)
            {
                if ($this -> _settings['filter']['type']['found'][0] == '+'
                    && !in_array($l_type,
                                 $this -> _settings['filter']['type']['found']))
                    continue;

                if ($this -> _settings['filter']['type']['found'][0] == '-'
                    && in_array($l_type,
                                $this -> _settings['filter']['type']['found']))
                    continue;
            }
            else
            {
                if ($this -> _settings['filter']['type']['new'][0] == '+'
                    && !in_array($l_type,
                                 $this -> _settings['filter']['type']['new']))
                    continue;

                if ($this -> _settings['filter']['type']['new'][0] == '-'
                    && in_array($l_type,
                                $this -> _settings['filter']['type']['new']))
                    continue;
            }

            // filtering by language
            if ($record['labelid'] > 0)
            {
                if ($this -> _settings['filter']['lang']['found'][0] == '+'
                    && !in_array($record['lng'],
                                 $this -> _settings['filter']['lang']['found']))
                    continue;

                if ($this -> _settings['filter']['lang']['found'][0] == '-'
                    && in_array($record['lng'],
                                $this -> _settings['filter']['lang']['found']))
                    continue;
            }
            else
            {
                if ($this -> _settings['filter']['lang']['new'][0] == '+'
                    && !in_array($record['lng'],
                                 $this -> _settings['filter']['lang']['new']))
                    continue;

                if ($this -> _settings['filter']['lang']['new'][0] == '-'
                    && in_array($record['lng'],
                                $this -> _settings['filter']['lang']['new']))
                    continue;
            }

            // all filters are passed...
            // writing data
            if ($record['lng'] == $this -> _settings['def_lng'])
            {
                // record is in default language
                $labelid = @$label_ids[$record['labeltype'] . '_' .
                                       $record['labelname']]['id'];
                if (!$labelid)
                {
                    // new label
                    if (!modApiFunc('Resources', 'addLabelToDB',
                                    $record['labelname'], $record['value'],
                                    $record['labeltype']))
                    {
                        // abnormal situation...
                        $label_ids = array_merge(
                            $label_ids,
                            modApiFunc('Resources', 'getMsgIDs',
                                       $record['labeltype'],
                                       $record['labelname'])
                        );
                        modApiFunc('Resources', 'updateLabelText',
                                   @$label_ids[$record['labeltype'] . '_' .
                                               $record['labelname']]['id'],
                                   $record['value']);
                    }
                    else
                    {
                        $label_ids[$record['labeltype'] . '_' .
                                   $record['labelname']] = array(
                            'id'   => $application -> db -> DB_Insert_Id(),
                            'text' => $record['value']
                        );
                    }
                }
                else
                {
                    modApiFunc('Resources', 'updateLabelText',
                               $labelid, $record['value']);
                    $label_ids[$record['labeltype'] . '_' .
                               $record['labelname']]['text'] = $record['value'];
                }
            }
            else
            {
                // MultiLang record...
                $labelid = @$label_ids[$record['labeltype'] . '_' .
                                       $record['labelname']]['id'];

                if ($labelid)
                {
                    // label exists, updating it
                    modApiFunc('MultiLang', 'setMLValue', $label, $labelid,
                               $record['value'], $record['lng']);
                }
                else
                {
                    // adding a translation of non-existent label...
                    // firstly trying to add a label itself
                    if (!modApiFunc('Resources', 'addLabelToDB',
                                    $record['labelname'], $record['value'],
                                    $record['labeltype']))
                    {
                        // label exists (abnormal)...
                        $label_ids = array_merge(
                            $label_ids,
                            modApiFunc('Resources', 'getMsgIDs',
                                       $record['labeltype'],
                                       $record['labelname'])
                        );
                    }
                    else
                    {
                        $label_ids[$record['labeltype'] . '_' .
                                   $record['labelname']] = array(
                            'id'   => $application -> db -> DB_Insert_Id(),
                            'text' => $record['value']
                        );
                    }

                    // then retrivin the id again
                    $labelid = @$label_ids[$record['labeltype'] . '_' .
                                           $record['labelname']]['id'];

                    modApiFunc('MultiLang', 'setMLValue', $label, $labelid,
                               $record['value'], $record['lng']);
                }
            }
        }
    }

    function finishWork()
    {
    }

    function saveWork()
    {
        modApiFunc('Session', 'set',
                   'DataWriterLabelsDBSettings', $this -> _settings);
    }

    function loadWork()
    {
        if (modApiFunc('Session', 'is_set', 'DataWriterLabelsDBSettings'))
            $this -> _settings = modApiFunc('Session', 'get',
                                            'DataWriterLabelsDBSettings');
        else
            $this -> _settings = null;
    }

    function clearWork()
    {
        modApiFunc('Session', 'un_set', 'DataWriterLabelsDBSettings');
        $this -> _settings = null;
    }

    var $_settings;
};

?>