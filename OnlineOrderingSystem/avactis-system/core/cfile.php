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
 *                          ,                                      .
 *
 */
class CFile
{
	var $__file_name = null;
    var $__file_handler = null;
	var $__is_error = false;
    var $__error_code = null;

	function CFile ($filename)
	{
        $this->__file_name = $filename;
        $this->__file_handler = null;
        $this->__is_error = false;
        $this->__error_code = null;
	}

    function open($open_mode)
    {
        if ($this->__is_error)  return false;

        CProfiler::ioStart();
        $this->__file_handler = @fopen($this->__file_name, $open_mode);
        CProfiler::ioStop();
        if (!$this->__file_handler)
        {
            $this->__is_error = true;
        }

        return !$this->__is_error;
    }

    function close()
    {
        if ($this->__is_error)  return false;

        CProfiler::ioStart();
        if (fclose($this->__file_handler) === false)
        {
            $this->__is_error = true;
        }
        CProfiler::ioStop();
        $this->__file_handler = null;
        return !$this->__is_error;
    }

    function write($data)
    {
        if ($this->__is_error)  return false;

        CProfiler::ioStart($this->__file_name, 'write');
        if (fwrite($this->__file_handler, $data) === false)
        {
            $this->__is_error = true;
        }
        CProfiler::ioStop();

        return !$this->__is_error;
    }

    function read()
    {
        if ($this->__is_error)  return false;

        CProfiler::ioStart($this->__file_name, 'read');
        $data = '';
        while (!feof($this->__file_handler))
        {
            $t = fread($this->__file_handler, 512);
            if ($t === false)
            {
                $this->__is_error = true;
                return false;
            }
            else
            {
                $data .= $t;
            }
        }
        CProfiler::ioStop();

        return $data;
    }

    function lock($lock_mode)
    {
        if ($this->__is_error)  return false;

        if (flock($this->__file_handler, $lock_mode) === false)
        {
            $this->__is_error = true;
        }
        return !$this->__is_error;
    }

    function unlock()
    {
        if ($this->__is_error)  return false;

        if (flock($this->__file_handler, LOCK_UN) === false)
        {
            $this->__is_error = true;
        }
        return !$this->__is_error;
    }

    function getContent()
    {
        if ($this->__is_error)  return false;

        CProfiler::ioStart();
        $this->open('r');
        $this->lock(LOCK_SH);
        $d = $this->read();
        $this->unlock();
        $this->close();
        CProfiler::ioStop();
        return $d;
    }

    function getLines()
    {
        if ($this->__is_error)  return false;

        CProfiler::ioStart($this->__file_name, 'read');
        $d = file($this->__file_name, FILE_IGNORE_NEW_LINES);
        CProfiler::ioStop();
        return $d;

    }

    function putContent($data, $open_mode = 'w')
    {
        if ($this->__is_error)  return false;

        CProfiler::ioStart();
        $this->open($open_mode);
        $this->lock(LOCK_EX);
        $this->write($data);
        $this->unlock();
        $this->close();
        CProfiler::ioStop();
        return !$this->__is_error;
    }

    function appendContent($data)
    {
    	$this->putContent($data, 'a');
    }

    function delete()
    {
        if ($this->__is_error)  return false;

        CProfiler::ioStart($this->__file_name, 'delete');
        $this->__is_error = !unlink($this->__file_name);
        CProfiler::ioStop();
        return !$this->__is_error;
    }

    function isError()
    {
        return $this->__is_error;
    }
}
?>