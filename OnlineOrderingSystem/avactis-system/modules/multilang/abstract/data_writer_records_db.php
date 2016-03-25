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

class DataWriterRecordsDB extends DataWriterDefault
{
    function DataWriterRecordsDB()
    {
    }

    function initWork($settings)
    {
        $this -> clearWork();
        $this -> _settings = $settings;

        modApiFunc('MultiLang', 'clearTmpMLRecords');

        $this -> _process_info['status'] = 'INITED';
    }

    function doWork($data)
    {
        if ($data['status'] == 1)
        {
            // from DB to multilang_data
            modApiFunc('MultiLang', 'addTmpMLRecords',
                       $data['label'], $data['data']);
        }
        elseif ($data['status'] == 2)
        {
            // from multilang_data to DB
            modApiFunc('MultiLang', 'processMLRecords',
                       $data['label'], $data['data']);
        }
    }

    function finishWork()
    {
        modApiFunc('MultiLang', 'deleteLanguageRecords',
                   $this -> _settings['new_lng']);
        modApiFunc('MultiLang', 'deleteLanguageRecords',
                   $this -> _settings['old_lng']);
        modApiFunc('MultiLang', 'processTmpMLRecords',
                   $this -> _settings['old_lng']);
        modApiFunc('MultiLang', '_changeDefaultLanguage',
                   $this -> _settings['new_lng']);
    }

    function saveWork()
    {
        modApiFunc('Session', 'set',
                   'DataWriterRecordsDBSettings', $this -> _settings);
    }

    function loadWork()
    {
        if (modApiFunc('Session', 'is_set', 'DataWriterRecordsDBSettings'))
            $this -> _settings = modApiFunc('Session', 'get',
                                            'DataWriterRecordsDBSettings');
        else
            $this -> _settings = null;
    }

    function clearWork()
    {
        modApiFunc('Session', 'un_set', 'DataWriterRecordsDBSettings');
        $this -> _settings = null;
    }

    var $_settings;
};

?>