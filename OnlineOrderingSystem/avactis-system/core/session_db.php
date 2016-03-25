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

class SessionDBHandler
{
    function SessionDBHandler($link = 'db_link')
    {
    	global $application;
        global $$link;
        $this->__session_table = $application->getAppIni('DB_TABLE_PREFIX') . $this->__session_table;
        $this->__db_link = $$link;
    }

    /**
     * Open function, this works like a constructor in classes and is executed
     * when the session is being opened. The open function expects two parameters,
     * where the first is the save path and the second is the session name.
     *
     * @param string $path Session save path
     * @param string $name Session name
     */
    function _open($path, $name)
    {
    	return TRUE;
    }

    /**
     * Close function, this works like a destructor in classes and is executed
     * when the session operation is done.
     */
    function _close()
    {
    	$this->_unlock();
        return TRUE;
    }

    /**
     * Read function must return string value always to make save handler work
     * as expected. Return empty string if there is no data to read. Return values
     * from other handlers are converted to boolean expression. TRUE for success,
     * FALSE for failure.
     *
     * @param string $ses_id Session ID
     * @return mixed Empty string or TRUE for success, FALSE for failure
     */
    function _read($ses_id)
    {
    	$ses_id = mysqli_real_escape_string($this->__db_link, $ses_id);
        $this->_lock($ses_id);
        $session_sql = "SELECT * FROM " . $this->__session_table . " WHERE ses_id = '$ses_id'";
        $session_res = mysqli_query($this->__db_link, $session_sql);
        if (!$session_res)
        {
            return '';
        }

        $session_num = @mysqli_num_rows($session_res);
        if ($session_num > 0)
        {
            $session_row = mysqli_fetch_assoc($session_res);
            return $session_row["ses_value"];
        }
        else
        {
            return '';
        }
    }

    /**
     * Write new data to database.
     *
     * NOTE: The "write" handler is not executed until after the output stream
     * is closed. Thus, output from debugging statements in the "write" handler
     * will never be seen in the browser. If debugging output is necessary, it
     * is suggested that the debug output be written to a file instead.
     *
     * @param string $ses_id Session  ID
     * @param string $data Session data
     * @return boolean TRUE for success, FALSE for failure
     */
    function _write($ses_id, $data)
    {
    	$ses_id = mysqli_real_escape_string($this->__db_link, $ses_id);
        $data = mysqli_real_escape_string($this->__db_link, $data);
        $session_sql = " REPLACE  INTO " . $this->__session_table . " (ses_id, ses_time, ses_value)"
                     . " VALUES ('$ses_id', '" . time() . "', '$data')";
        $session_res = mysqli_query($this->__db_link, $session_sql);
        if (!$session_res)
        {
        	return FALSE;
        }
        else
        {
        	return TRUE;
        }
    }

    /**
     * The destroy handler, this is executed when a session is destroyed
     * with session_destroy() and takes the session id as its only parameter.
     *
     * @param string $ses_id Session ID
     * @return boolean TRUE for success, FALSE for failure
     */
    function _destroy($ses_id)
    {
    	$ses_id = mysqli_real_escape_string($this->__db_link, $ses_id);
        $session_sql = "DELETE FROM " . $this->__session_table . " WHERE ses_id = '$ses_id'";
        $session_res = mysqli_query($this->__db_link, $session_sql);
        if (!$session_res)
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    /**
     * The garbage collector, this is executed when the session garbage
     * collector is executed and takes the max session lifetime as its only parameter.
     *
     * @param int $life Max session lifetime
     * @return boolean TRUE for success, FALSE for failure
     */
    function _gc($life)
    {
    	$ses_life = time()-$life;
        $session_sql = "DELETE FROM " . $this->__session_table . " WHERE ses_time < $ses_life";
        $session_res = mysqli_query($this->__db_link, $session_sql);
        if (!$session_res)
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }


    function isSessionLocked($ses_id)
    {
    	$ses_id = mysqli_real_escape_string($this->__db_link, $ses_id);
        $session_sql = "SELECT ses_locked FROM ".$this->__session_table." WHERE ses_id='" . $ses_id . "' AND ses_locked = 1";
        $session_res = mysqli_query($this->__db_link, $session_sql);
        if (!$session_res)
        {
            return false;
        }

        $session_num = @mysqli_num_rows($session_res);
        if ($session_num > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function _lock($ses_id)
    {
    	$s = time();
    	while($this->isSessionLocked($ses_id) === TRUE)
        {
            // random delay from 10ms to 300ms (step 10ms)
            usleep(rand(1,30)*100000);
            // proceed if session was accidentially locked
            if (time() - $s > 5) break;
        }

        $ses_id = mysqli_real_escape_string($this->__db_link, $ses_id);
        $session_sql = " UPDATE " . $this->__session_table
                     . " SET ses_locked = 1"
                     . " WHERE ses_id='$ses_id'";
        $session_res = mysqli_query($this->__db_link, $session_sql);
        if ($session_res)
        {
            $this->__locked_ses_id = $ses_id;
        }
    }

    function _unlock()
    {
    	$session_sql = " UPDATE " . $this->__session_table
                     . " SET ses_locked = 0"
                     . " WHERE ses_id='".$this->__locked_ses_id."'";
        $session_res = mysqli_query($this->__db_link, $session_sql);
        if (!$session_res)
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    var $__session_table = "sessions";
    var $__locked_ses_id;
    var $__db_link;
}

/* Change the save_handler to use the class functions */
function __set_session_db_handler()
{
    global $application;
    if ($application->db->DB_isTableExists($application->getAppIni('DB_TABLE_PREFIX')."sessions") != null)
    {
        global $__session_db_handler_object;
        session_set_save_handler (array(&$__session_db_handler_object, '_open'),
                                  array(&$__session_db_handler_object, '_close'),
                                  array(&$__session_db_handler_object, '_read'),
                                  array(&$__session_db_handler_object, '_write'),
                                  array(&$__session_db_handler_object, '_destroy'),
                                  array(&$__session_db_handler_object, '_gc'));
    }
}
?>