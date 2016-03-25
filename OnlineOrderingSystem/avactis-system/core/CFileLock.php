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


class CFileLock
{
    /**
     * $mode LOCK_SH | LOCK_EX
     */
    static public function lock($mode, $lock_file_name = 'global')
    {
        if (self::$_fIsLocked) {
            return false;
        }
        $lock_file = CConf::get('cache_dir') . $lock_file_name . '.lock';
        self::$_fLockFileHandler = new CFile($lock_file);
        self::$_fLockFileHandler->open('w');
        if (!self::$_fLockFileHandler->isError())
        {
            CProfiler::lockStart();
            self::$_fLockFileHandler->lock($mode);
            CProfiler::lockStop();
            self::$_fIsLocked = true;
            return true;
        }
        else
        {
            return false;
        }
    }

    static public function unlock()
    {
        if (self::$_fIsLocked == false)
        {
            return;
        }
        self::$_fLockFileHandler->unlock();
        self::$_fLockFileHandler->close();
        self::$_fIsLocked = false;
    }

    static protected $_fLockFileHandler;
    static protected $_fIsLocked = false;
}


