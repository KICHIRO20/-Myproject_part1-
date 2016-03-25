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

loadClass('DataWriterDefault');

class DataWriterCache extends DataWriterDefault
{
    function DataWriterCache()
    {
    }

    function initWork($settings)
    {
        $this->clearWork();

        $this->_settings = array(
            'cache_dir' => $settings['cache_dir']
        );

        $this->_make_cache_dir();

        $this->_process_info['status'] = 'INITED';
    }

    function doWork($data)
    {
        if($this->_settings['cache_dir'] == null)
        {
            $this->_errors = 'can not write to cache. cache folder not created';
            return;
        };
        if(!is_dir($this->_settings['cache_dir']))
        {
            $this->_errors = 'can not write to cache. cache folder is not folder';
            return;
        };
        if(!is_writable($this->_settings['cache_dir']))
        {
            $this->_errors = 'can not write to cache. cache folder is not writable';
            return;
        };

        $cfh = fopen($this->_settings['cache_dir'].'/item_'.$data['item_number'].'.dcc','w');
        fwrite($cfh,serialize($data));
        fclose($cfh);
    }

    function finishWork()
    {
    }

    function saveWork()
    {
        modApiFunc('Session','set','DataWriterCacheSettings',$this->_settings);
    }

    function loadWork()
    {
        if(modApiFunc('Session','is_set','DataWriterCacheSettings'))
            $this->_settings = modApiFunc('Session','get','DataWriterCacheSettings');
        else
            $this->_settings = null;
    }

    function clearWork()
    {
        modApiFunc('Session','un_set','DataWriterCacheSettings');
        $this->_settings = null;
    }

    function _make_cache_dir()
    {
        $cache_dir = $this->_settings['cache_dir'];
        $this->_settings['cache_dir'] = null;
        if(!file_exists($cache_dir))
        {
            if(!@mkdir($cache_dir))
            {
                $err_msg = 'can not make cache folder `'.$cache_dir.'`';
                if(ini_get('track_errors'))
                    $err_msg .= ' '.$php_errormsg;
                $this->_errors = $err_msg;
                return;
            };
        }
        else
        {
            if(!is_dir($cache_dir) or !is_writable($cache_dir))
            {
                $this->_errors = 'cache folder is not folder or not writable';
                return;
            };
            if(!modApiFunc('Shell','clearFolderContent',$cache_dir))
            {
                $this->_errors = 'cannot clear cache folder.';
                return;
            };
        };
        $this->_settings['cache_dir'] = $cache_dir;
    }

    var $_settings;
};

?>