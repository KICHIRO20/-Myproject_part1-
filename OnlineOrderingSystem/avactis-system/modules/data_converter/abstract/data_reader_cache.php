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
 * @package DataConverter
 * @author Egor V. Derevyankin
 *
 */

loadClass('DataReaderDefault');

class DataReaderCache extends DataReaderDefault
{
    function DataReaderCache()
    {
    }

    function initWork($settings)
    {
        $this->clearWork();

        $this->_settings = array(
            'cache_dir' => $settings['cache_dir']
           ,'items_count' => 0
           ,'items_processing' => 0
           ,'autoclear' => $settings['autoclear']
        );

        $this->_check_cache_dir();
        $this->_calc_files_count();

        $this->_process_info['status'] = 'INITED';
        $this->_process_info['items_count'] = $this->_settings['items_count'];
        $this->_process_info['items_processing'] = $this->_settings['items_processing'];
    }

    function doWork()
    {
        $this->_process_info['status'] = 'HAVE_MORE_DATA';
        $this->_process_info['items_count'] = $this->_settings['items_count'];

        if($this->_settings['items_count'] == $this->_settings['items_processing'])
        {
            $this->_process_info['status'] = 'NO_MORE_DATA';
            return null;
        };
        $this->_settings['items_processing']++;
        $fname = $this->_settings['cache_dir'].'item_'.$this->_settings['items_processing'].'.dcc';
        $data = unserialize(file_get_contents($fname));
        if($this->_settings['autoclear']==true)
            unlink($fname);

        $this->_process_info['items_processing'] = $this->_settings['items_processing'];
        if($this->_settings['items_count'] == $this->_settings['items_processing'])
            $this->_process_info['status'] = 'NO_MORE_DATA';

        return $data;
    }

    function finishWork()
    {
        if($this->_settings['autoclear']==true)
            @rmdir($this->_settings['cache_dir']);
    }

    function saveWork()
    {
        modApiFunc('Session','set','DataReaderCacheSettings',$this->_settings);
    }

    function loadWork()
    {
        if(modApiFunc('Session','is_set','DataReaderCacheSettings'))
            $this->_settings = modApiFunc('Session','get','DataReaderCacheSettings');
        else
            $this->_settings = null;
    }

    function clearWork()
    {
        modApiFunc('Session','un_set','DataReaderCacheSettings');
        $this->_settings = null;
    }

    function _check_cache_dir()
    {
        $cache_dir = $this->_settings['cache_dir'];
        $this->_settings['cache_dir'] = null;
        if($this->_settings == null)
        {
            $this->_errors = 'can not check cache folder. folder not defined';
            return;
        };
        if(!is_dir($cache_dir) or !is_readable($cache_dir))
        {
            $this->_errors = 'can not check cache folder. folder is not folder or not readable';
            return;
        };

        $this->_settings['cache_dir'] = $cache_dir;
    }

    function _calc_files_count()
    {
        if($this->_settings['cache_dir'] == null)
        {
            $this->_settings['items_count'] = 0;
            return;
        };

        $counter = 0;
        $tmp_dir = dir($this->_settings['cache_dir']);
        while(($file = $tmp_dir->read()) !== false)
        {
            if(preg_match("/^item_\d+\.dcc$/",$file))
                $counter++;
        };
        $tmp_dir->close();
        $this->_settings['items_count'] = $counter;
    }

    var $_settings;
}

?>