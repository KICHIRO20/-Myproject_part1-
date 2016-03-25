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

class CWorker
{
    function CWorker()
    {
    }

    function initWork()
    {
    }

    function doWork()
    {
    }

    function finishWork()
    {
    }

    function saveWork()
    {
    }

    function loadWork()
    {
    }

    function clearWork()
    {
    }

    function getErrors()
    {
        return empty($this->_errors) ? array() : array($this->_errors);
    }

    function getWarnings()
    {
        return empty($this->_warnings) ? array() : array($this->_warnings);
    }

    function getMessages()
    {
        return empty($this->_messages) ? array() : array($this->_messages);
    }

    function getProcessInfo()
    {
        return $this->_process_info;
    }

    var $_errors='';
    var $_warnings='';
    var $_messages='';
    var $_process_info=array();

}

?>