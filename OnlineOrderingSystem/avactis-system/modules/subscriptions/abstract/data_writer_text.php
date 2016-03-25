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
 * @package
 * @author
 *
 */

loadClass('DataWriterDefault');

class DataWriterText extends DataWriterDefault
{
    function DataWriterText()
    {
    }

    function initWork($settings)
    {
        $this->clearWork();

        $this->out_file = $settings['out_file'];
        @unlink($this->out_file);

        $this->_process_info['status'] = 'INITED';
    }

    function doWork($data)
    {
        if (is_array($data)) {
            $fp = @fopen($this->out_file, 'a');
            if ($fp) {
                fwrite($fp, implode('', $data));
                fwrite($fp, "\n");
                fclose($fp);
            }
        }
    }

    function finishWork()
    {
    }

    function saveWork()
    {
        modApiFunc('Session', 'set', 'DataWriterTextOutfile', $this->out_file);
    }

    function loadWork()
    {
        $this->out_file = null;
        if(modApiFunc('Session', 'is_set', 'DataWriterTextOutfile')) {
            $this->out_file = modApiFunc('Session', 'get', 'DataWriterTextOutfile');
        };
    }

    function clearWork()
    {
        modApiFunc('Session', 'un_set', 'DataWriterTextOutfile');
        $this->out_file = null;
    }


    var $out_file;

};

?>