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

class DataReaderLabelsCSV extends DataReaderDefault
{
    function DataReaderLabelsCSV()
    {
    }

    function initWork($settings)
    {
        $this -> _settings = array(
            'src_file' => $settings['src_file'],
            'csv_delimiter' => isset($settings['csv_delimiter'])
                                   ? $settings['csv_delimiter'] : 'DETECT',
            'file_position' => 0,
            'lines_count' => 0,
            'items_processing' => 0,
            'bulk' => 500,
            'bulk_number' => 0
        );

        $this -> _info = array();

        $this -> _open_src_file();

        if ($this -> _settings['csv_delimiter'] == 'DETECT')
            $this -> _detect_delimiter();

        $this -> _calc_lines_count();
        $this -> _fetch_headers();

        if (!isset($this -> _info['format']))
        {
            $this -> _info['format'] = getMsg('ML', 'ML_EXPORT_CSV_FILE');
            if ($this -> _settings['csv_delimiter'] == ',')
                $this -> _info['delimiter'] = getMsg('ML', 'ML_EXPORT_COMMA');
            elseif ($this -> _settings['csv_delimiter'] == ';')
                $this -> _info['delimiter'] = getMsg('ML', 'ML_EXPORT_SEMICOLON');
            elseif ($this -> _settings['csv_delimiter'] == "\t")
                $this -> _info['delimiter'] = getMsg('ML', 'ML_EXPORT_TABULATION');
            else
                $this -> _info['delimiter'] = $this -> _settings['csv_delimiter'];
        }

        if (is_array($this -> _csv_headers))
        {
            $lng_found = false;
            foreach($this -> _csv_headers as $k => $v)
                switch($k)
                {
                    case 'labeltype':
                        if ($v >= 0)
                        {
                            $this -> _info['columns']['type'] = getMsg('ML', 'ML_IMPORT_LABELS_FOUND_IN', $v);
                        }
                        else
                        {
                            $this -> _info['columns']['type'] = getMsg('ML', 'ML_IMPORT_LABELS_NOT_FOUND');
                            if (!$this -> _errors)
                                $this -> _errors = getMsg('ML', 'ML_IMPORT_LABELS_TYPE_COLUMN_NOT_FOUND');
                        }
                        break;

                    case 'labelname':
                        if ($v >= 0)
                        {
                            $this -> _info['columns']['name'] = getMsg('ML', 'ML_IMPORT_LABELS_FOUND_IN', $v);
                        }
                        else
                        {
                            $this -> _info['columns']['name'] = getMsg('ML', 'ML_IMPORT_LABELS_NOT_FOUND');
                            if (!$this -> _errors)
                                $this -> _errors = getMsg('ML', 'ML_IMPORT_LABELS_NAME_COLUMN_NOT_FOUND');
                        }
                        break;

                    default:
                        if ($v >= 0)
                        {
                            $this -> _info['columns'][$k] = getMsg('ML', 'ML_IMPORT_LABELS_FOUND_IN', $v);
                            $lng_found = true;
                        }
                        else
                        {
                            $this -> _info['columns'][$k] = getMsg('ML', 'ML_IMPORT_LABELS_NOT_FOUND');
                        }
                }

            if (!$lng_found && !$this -> _errors)
            {
                $this -> _errors = getMsg('ML', 'ML_IMPORT_LABELS_LANGUAGES_NOT_FOUND');
                $this -> _info['error'] = $this -> _errors;
            }
        }

        $this -> _process_info['status'] = 'INITED';
        $this -> _process_info['items_count'] = $this -> _settings['lines_count'] - 2;
        $this -> _process_info['items_processing'] = 0;
        $this -> _process_info['global']['info'] = $this -> _info;

        return $this -> _csv_headers;
    }

    function doWork()
    {
        $data = $this -> _fetch_data();
        $this -> _process_info['items_count'] = $this -> _settings['lines_count'] - 2;

        $this -> _settings['bulk_number']++;
        $this -> _settings['items_processing'] = min($this -> _settings['items_processing']
                                                     + $this -> _settings['bulk'],
                                                     $this -> _settings['lines_count'] - 2);

        $this -> _process_info['items_processing'] = $this -> _settings['items_processing'];

        if ($this -> _process_info['items_processing'] >= ($this -> _settings['lines_count'] - 2))
            $this -> _process_info['status'] = 'NO_MORE_DATA';

        $this -> _process_info['global']['info'] = $this -> _info;

        return array(
            'item_number' => $this -> _settings['bulk_number'],
            'item_data' => $data
        );
    }

    function finishWork()
    {
        $this -> _close_src_file();
    }

    function saveWork()
    {
        modApiFunc('Session', 'set', 'DataReaderLabelsCSVSettings',
                   $this -> _settings);
        modApiFunc('Session', 'set', 'DataReaderLabelsCSVHeaders',
                   $this -> _csv_headers);
        $this -> _close_src_file();
    }

    function loadWork()
    {
        if (modApiFunc('Session', 'is_set', 'DataReaderLabelsCSVSettings'))
            $this -> _settings = modApiFunc('Session', 'get',
                                            'DataReaderLabelsCSVSettings');
        else
            $this -> _settings = null;

        if (modApiFunc('Session', 'is_set', 'DataReaderLabelsCSVHeaders'))
            $this -> _csv_headers = modApiFunc('Session', 'get',
                                               'DataReaderLabelsCSVHeaders');
        else
            $this -> _csv_headers = null;

        $this -> _info = array();

        $this -> _open_src_file();
    }

    function clearWork()
    {
        modApiFunc('Session', 'un_set', 'DataReaderLabelsCSVSettings');
        modApiFunc('Session', 'un_set', 'DataReaderLabelsCSVHeaders');
        $this -> _settings = null;
        $this -> _src_file_handler = null;
        $this -> _csv_headers = null;
        $this -> _info = array();
    }

    function _open_src_file()
    {
        if ($this -> _settings != null)
        {
            if (!is_string($this -> _settings['src_file'])
                || !$this -> _settings['src_file'])
            {
                $this -> _errors = getMsg('DCONV', 'CANT_OPEN_FILE_01');
                $this -> _info['format'] = getMsg('ML',
                                            'ML_INPORT_LABELS_UNKNOWN_FORMAT');
                $this -> _info['error'] = $this -> _errors;
                $this -> _src_file_handler = null;
                return;
            };
            if (!file_exists($this -> _settings['src_file'])
                || !is_file($this -> _settings['src_file']))
            {
                $this -> _errors = getMsg('DCONV', 'CANT_OPEN_FILE_02');
                $this -> _info['format'] = getMsg('ML',
                                            'ML_INPORT_LABELS_UNKNOWN_FORMAT');
                $this -> _info['error'] = $this -> _errors;
                $this -> _src_file_handler = null;
                return;
            };
            if (!is_readable($this -> _settings['src_file']))
            {
                $this -> _errors = getMsg('DCONV', 'CANT_OPEN_FILE_03');
                $this -> _info['format'] = getMsg('ML',
                                            'ML_INPORT_LABELS_UNKNOWN_FORMAT');
                $this -> _info['error'] = $this -> _errors;
                $this -> _src_file_handler = null;
                return;
            };
            if (filesize($this -> _settings['src_file']) == 0)
            {
                $this -> _errors = getMsg('DCONV', 'CANT_OPEN_FILE_04');
                $this -> _info['format'] = getMsg('ML',
                                            'ML_INPORT_LABELS_UNKNOWN_FORMAT');
                $this -> _info['error'] = $this -> _errors;
                $this -> _src_file_handler = null;
                return;
            };

            if (asc_detect_eol($this -> _settings['src_file']) == "\r")
                asc_mac2nix($this -> _settings['src_file']);

            $this -> _src_file_handler = fopen($this -> _settings['src_file'], 'r');
            fseek($this -> _src_file_handler, $this -> _settings['file_position']);
        }
        else
        {
            $this -> _errors = getMsg('DCONV', 'CANT_OPEN_FILE_01');
            $this -> _info['format'] = getMsg('ML',
                                           'ML_INPORT_LABELS_UNKNOWN_FORMAT');
            $this -> _info['error'] = $this -> _errors;
            $this -> _src_file_handler = null;
        }
    }

    function _close_src_file()
    {
        if ($this -> _src_file_handler != null)
        {
            fclose($this -> _src_file_handler);
            $this -> _src_file_handler = null;
        };
    }

    function _fetch_line_as_array()
    {
        if ($this -> _src_file_handler == null)
        {
            $this -> _errors = getMsg('DCONV', 'CANT_FETCH_LINE_01');
            $this -> _info['error'] = $this -> _errors;
            return null;
        };

        if (feof($this -> _src_file_handler))
        {
            $this -> _process_info['status'] = 'NO_MORE_DATA';
            return null;
        };

        $data_array = convertImportDataArray(
            fgetcsv($this -> _src_file_handler, 262144,
                    $this -> _settings['csv_delimiter'])
        );
        $this -> _settings['file_position'] = ftell($this -> _src_file_handler);

        if (feof($this -> _src_file_handler))
            $this -> _process_info['status'] = 'NO_MORE_DATA';
        else
            $this -> _process_info['status'] = 'HAVE_MORE_DATA';

        return $data_array;
    }

    function _fetch_headers()
    {
        if($this -> _settings['csv_delimiter'] == null)
        {
            $this -> _errors = getMsg('ML', 'ML_IMPORT_LABELS_HEADERS_NOT_FOUND');
            $this -> _info['format'] = getMsg('ML',
                                            'ML_INPORT_LABELS_INVALID_FORMAT');
            $this -> _info['error'] = $this -> _errors;
            return;
        };

        $this -> _csv_headers = $this -> _find_header_columns(
                                    $this -> _fetch_line_as_array()
                                );

        if ($this -> _csv_headers == null)
        {
            $this -> _errors = getMsg('ML', 'ML_IMPORT_LABELS_HEADERS_NOT_FOUND');
            $this -> _info['format'] = getMsg('ML',
                                            'ML_INPORT_LABELS_INVALID_FORMAT');
            $this -> _info['error'] = $this -> _errors;
            return;
        };
    }

    function _fetch_data()
    {
        $raw = array();

        for($i = 0; $i < $this -> _settings['bulk']; $i++)
        {
            $raw_line = $this -> _fetch_line_as_array();

            if ($raw_line == null)
                break;

            $raw[] = $raw_line;
        }

        if (empty($raw))
            return null;

        // in data we will keep values for all languages separately
        // so $data is an array of arrays
        // Note: in action the records will be shown separately as well
        $data = array();
        $label_types = array();
        $label_names = array();

        foreach($raw as $raw_line)
        {
            // checking the label type and label name
            $label_type = @$raw_line[$this -> _csv_headers['labeltype']];
            $label_name = @$raw_line[$this -> _csv_headers['labelname']];

            // if label name is empty -> invalid record!
            if (!$label_name)
            {
                if (!isset($this -> _info['total']))
                    $this -> _info['total'] = array();

                if (!isset($this -> _info['total']['error']))
                    $this -> _info['total']['error'] = 0;

                $this -> _info['total']['error']++;

                // do nothing...
                continue;
            }

            // if the type is empty assume it is a custom label
            if (!$label_type)
            {
                $label_type = 'CZ';
                if (_ml_substr($label_name, 0, 7) !== 'CUSTOM_')
                    $label_name = 'CUSTOM_' . $label_name;
                $this -> _warnings = getMsg('ML',
                                        'ML_IMPORT_LABELS_EMPTY_TYPE_WARNING');
                $this -> _info['warning'] = $this -> _warnings;
            }

            $label_types[] = $label_type;
            $label_names[] = $label_name;
        }

        // if empty $label_types -> do nothing...
        if (empty($label_types))
            return null;

        // checking if label type exists
        // if it does not assume the record is invalid
        $meta_info = modApiFunc('Resources', 'getMetaByShortNames',
                                $label_types);

        foreach($label_types as $k => $label_type)
        {
            if (!isset($meta_info[$label_type]))
            {
                if (!isset($this -> _info['total']))
                    $this -> _info['total'] = array();

                if (!isset($this -> _info['total']['error']))
                    $this -> _info['total']['error'] = 0;

                $this -> _info['total']['error']++;

                unset($label_types[$k]);
                unset($label_names[$k]);
            }
        }

        // here we have valid label types
        // and valid label names since we assume a new label can be added
        // even for admin label types
        // so now we need to look through the languages and check the label
        // for each of them

        // getting the default language
        $def_lng = modApiFunc('MultiLang', 'getDefaultLanguage');

        // firstly getting the map value for resource_labels table
        $ml_label = modApiFunc('MultiLang', 'mapMLField',
                               'resource_labels', 'res_text', 'Resources');

        // secondly we get label ids for the labels
        $label_ids = modApiFunc('Resources', 'getMsgIDs',
                                $label_types, $label_names);

        // thirdly we get translation for all languages
        $trns = array();
        foreach($this -> _csv_headers as $header => $column)
        {
            // skipping missing languages and label keys
            if ($column < 0 || $header == 'labeltype'
                || $header == 'labelname' || $header == 'default'
                || $header == $def_lng)
                continue;

            $trns[$header]  = modApiFunc('MultiLang',
                                         'getLabelTranslationByIDs',
                                         array_values($label_ids), $header);
        }

        // looking through the data
        foreach($raw as $raw_line)
        {
            $label_type = @$raw_line[$this -> _csv_headers['labeltype']];
            $label_name = @$raw_line[$this -> _csv_headers['labelname']];

            if (!$label_type)
            {
                $label_type = 'CZ';
                if (_ml_substr($label_name, 0, 7) !== 'CUSTOM_')
                    $label_name = 'CUSTOM_' . $label_name;
            }

            if (!in_array($label_type, $label_types)
                || !in_array($label_name, $label_names))
                continue;

            // looking through the languages
            foreach($this -> _csv_headers as $header => $column)
            {
                // skipping missing languages and label keys
                if ($column < 0 || $header == 'labeltype'
                    || $header == 'labelname')
                    continue;

                // convert empty language
                if ($header == 'default')
                    $lng = '';
                else
                    $lng = $header;

                // getting the label id
                $labelid = @$label_ids[$label_type . '_' . $label_name]['id'];
                if (!$labelid)
                    $labelid = 0;

                $found = ($labelid > 0);

                // checking if the label exists
                if ($lng != $def_lng)
                {
                    $found = $found && isset($trns[$lng][$labelid]);

                    // skipping empty values for non-found records
                    // (if record is not found and its new value is empty
                    // then actually we need to do nothing...
                    if (!$raw_line[$column] && !$found)
                        continue;

                    // checking if found label will acually be updated
                    if ($found
                        && ($raw_line[$column] == $trns[$lng][$labelid]))
                        continue;
                }
                else
                {
                    // checking if found label will acually be updated
                    if ($found
                        && $raw_line[$column] ==
                           $label_ids[$label_type . '_' . $label_name]['text'])
                        continue;
                }

                // filling the _info array...
                // total...
                if (!isset($this -> _info['total']))
                    $this -> _info['total'] = array();
                if ($found)
                {
                    if (!isset($this -> _info['total']['found']))
                        $this -> _info['total']['found'] = 0;
                    $this -> _info['total']['found']++;
                }
                else
                {
                    if (!isset($this -> _info['total']['new']))
                        $this -> _info['total']['new'] = 0;
                    $this -> _info['total']['new']++;
                }

                // language
                if (!isset($this -> _info['language']))
                    $this -> _info['language'] = array();

                if (!isset($this -> _info['language'][$header]))
                    $this -> _info['language'][$header] = array();

                if ($found)
                {
                    if (!isset($this -> _info['language'][$header]['found']))
                        $this -> _info['language'][$header]['found'] = 0;
                    $this -> _info['language'][$header]['found']++;
                }
                else
                {
                    if (!isset($this -> _info['language'][$header]['new']))
                        $this -> _info['language'][$header]['new'] = 0;
                    $this -> _info['language'][$header]['new']++;
                }

                // label type
                if (!isset($this -> _info['type']))
                    $this -> _info['type'] = array();

                // separating avactis and custom labels
                $_l_type = $label_type;
                if ($label_type == 'CZ'
                    && _ml_substr($label_name, 0, 7) == 'CUSTOM_')
                    $_l_type = 'CZ_CUSTOM';

                if (!isset($this -> _info['type'][$_l_type]))
                    $this -> _info['type'][$_l_type] = array();

                if ($found)
                {
                    if (!isset($this -> _info['type'][$_l_type]['found']))
                        $this -> _info['type'][$_l_type]['found'] = 0;
                    $this -> _info['type'][$_l_type]['found']++;
                }
                else
                {
                    if (!isset($this -> _info['type'][$_l_type]['new']))
                        $this -> _info['type'][$_l_type]['new'] = 0;
                    $this -> _info['type'][$_l_type]['new']++;
                }

                // gathering the info
                $record = array('labelid'   => $labelid,
                                'labeltype' => $label_type,
                                'labelname' => $label_name,
                                'lng'       => $lng,
                                'found'     => $found,
                                'value'     => $raw_line[$column]);

                $data[] = $record;
            }
        }

        if (empty($data))
            return null;

        return $data;
    }

    function _calc_lines_count()
    {
        if ($this -> _src_file_handler == null)
        {
            $this -> _errors = getMsg('DCONV', 'CANT_CALC_LINES_01');
            $this -> _settings['lines_count'] = 0;
            return;
        };
        if ($this -> _settings['csv_delimiter'] == null)
        {
            $this -> _errors = getMsg('DCONV', 'CANT_CALC_LINES_02');
            $this -> _settings['lines_count'] = 0;
            return;
        };

        $counter = 0;
        while(!feof($this -> _src_file_handler))
        {
            fgetcsv($this -> _src_file_handler, 262144,
                    $this -> _settings['csv_delimiter']);
            $counter++;
        };

        rewind($this -> _src_file_handler);

        $this -> _settings['lines_count'] = $counter;
    }

    function _detect_delimiter()
    {
        if ($this -> _src_file_handler == null)
        {
            $this -> _errors = getMsg('DCONV', 'CANT_DETECT_DELIMITER');
            $this -> _settings['csv_delimiter'] = null;
            return;
        };

        foreach ($this -> CSV_DELIMITERS as $delimiter)
        {
            $_tmp_arr = convertImportDataArray(
                        fgetcsv($this -> _src_file_handler, 262144, $delimiter)
            );
            rewind($this -> _src_file_handler);
            if ($this -> _check_headers_array($_tmp_arr))
            {
                $this->_settings['csv_delimiter'] = $delimiter;
                return;
            };
        }

        $this -> _errors = getMsg('DCONV', 'INVALID_CSV_FORMAT');
        $this -> _settings['csv_delimiter'] = null;
        return;
    }

    function _check_headers_array($arr)
    {
        if (empty($arr))
            return false;

        if ($this -> _find_header_columns($arr, true))
            return true;

        return false;
    }

    function _find_header_columns($arr, $check = false)
    {
        // returns false if not an array
        if (!is_array($arr) || empty($arr))
           return false;

        // building the array of fields
        $result = array('labeltype' => -1, 'labelname' => -1);

        // getting the active languages
        $lngs = modApiFunc('MultiLang', 'getLanguageList', false);
        if (!is_array($lngs) || empty($lngs))
            $lngs = array(0 => array('lng' => 'default',
                                     'lng_name' => getMsg('ML', 'ML_DEFAULT')));

        // adding the languages to the list of fields
        foreach($lngs as $v) {
            $result[$v['lng']] = -1;
            $lngs[$v['lng']] = $v;
        }

        // finding the column indexes in the $arr
        $found = array();
        foreach($result as $key => $value)
        {
            switch($key)
            {
                case 'labeltype':
                case 'labelname':
                    foreach($arr as $k => $v)
                    {
                        if (in_array($k, $found))
                            continue;
                        if (_ml_strpos(
                              _ml_strtolower(
                                str_replace(
                                  array(' ', '_', '(', ')'), '', $v)), $key)
                            !== false)
                        {
                            $result[$key] = $k;
                            $found[$k] = $k;
                            break;
                        }
                    }
                    break;

                default:
                    foreach($arr as $k => $v)
                    {
                        if (in_array($k, $found))
                            continue;
                        if (_ml_strpos(
                              _ml_strtolower(
                                str_replace(
                                  array(' ', '_', '(', ')'), '', $v)),
                              'labelvalue')
                            !== false)
                        {
                            if (_ml_strpos(' ' . _ml_strtolower(str_replace(array(' ', '_', '(', ')'), ' ', $v)) . ' ', ' ' . _ml_strtolower($key) . ' ') !== false
                                || _ml_strpos(_ml_strtolower($v), _ml_strtolower($lngs[$key]['lng_name'])) !== false)
                            {
                                $result[$key] = $k;
                                $found[$k] = $k;
                                break;
                            }
                        }
                    }
                    break;
            }
        }

        if ($check)
        {
            if ($result['labeltype'] >= 0 && $result['labelname'] >= 0)
                return true;

            return false;
        }

        return $result;
    }

    var $_settings;
    var $_src_file_handler;
    var $_csv_headers;
    var $_info;

    var $CSV_DELIMITERS = array(",", ";", "\t");
};

?>