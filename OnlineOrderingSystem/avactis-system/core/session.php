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
 * Session class is intended for storing variable values across multiple
 * web pages. Session data can be stored to database or files. Main public
 * methods are set(), get() and isRegistered(), which allow to set, get session
 * variables values and check, if a variable is registered with the current
 * session. SessionSESSION is a stub class, using the php $_SESSION variable
 * for storing contents.
 *
 * @package Core
 * @author Vadim Lyalikov
 * @access  public
 */
class Session
{

    //Helper function. Just to notice. Not used yet.
    //see http://php.net/manual/en/function.session-unset.php#68738
    function session_clean1($logout=false)
     {
      $v=array();
      foreach($_SESSION as $x=>$y)
       if($x!="redirector"&&($x!="user"||$logout))
        $v[]=$x;

      foreach($v as $x)
       unset($_SESSION[$x]);
      return;
     }


    /**
     * function clears current session
     *
     */
    function session_clean()
    {
        session_unset();
    }

    /**
     * Initializes the session: e.g. retrieves session data from the database.
     */
    function start($sid = "")
    {
        global $zone, $application;

        $drop_session_cookie = false;

        if ($zone == "AdminZone")
        {
            ini_set("session.cookie_lifetime", 0);
            session_name("AZSESSID");
            if ($application->db->DB_isTableExists($application->getAppIni('DB_TABLE_PREFIX')."settings") != null)
            {
                $duration_cfg = (int)modApiFunc("Settings", "getParamValue", "ADMIN_SESSION_DURATION", "ADM_SESSION_DURATION_VALUE");
            }
            else
            {
                $duration_cfg = 3600;
            }
            $ClientSessionLifetime = $duration_cfg;
        }
        else
        {
        	if (isset($_COOKIE['save_session']) && $_COOKIE['save_session'] == "save")
        	{
        	    if ($application->db->DB_isTableExists($application->getAppIni('DB_TABLE_PREFIX')."settings") != null)
                {
                    $cz_duration_cfg = (int)modApiFunc("Settings", "getParamValue", "CUSTOMER_ACCOUNT_SETTINGS", "CUSTOMER_SESSION_DURATION_VALUE");
                }
                else
                {
                    $cz_duration_cfg = 3600*24*30; //30 days
                }

        		ini_set("session.cookie_lifetime", $cz_duration_cfg);
                ini_set("session.gc_maxlifetime",  $cz_duration_cfg);
        	}
        	else
        	{
        		ini_set("session.cookie_lifetime", 0);
                #ini_set("session.gc_maxlifetime",  0);
                $drop_session_cookie = true;

        	}

            session_name("CZSESSID");
        }

        if ($sid)
        {
            session_id($sid);
        }

        $session_save_handler = $application->getAppIni('SESSION_SAVE_HANDLER');
        if ($session_save_handler == 'DB')
        {
            // redefine session handler
            __set_session_db_handler();
        }
        elseif ($session_save_handler != 'PHP_INI')
        {
            ini_set("session.save_handler", $session_save_handler);
        }

        $session_save_path = $application->getAppIni('SESSION_SAVE_PATH');
        if ($session_save_path == 'AVACTIS_CACHE_DIR')
        {
            session_save_path($application->getAppIni("PATH_CACHE_DIR"));
        }
        elseif ($session_save_path != 'PHP_INI')
        {
            session_save_path($session_save_path);
        }

        session_start();

        global $application;
        $HTTP_URL = md5($application->getAppIni('HTTP_URL'));
        if (!isset($_COOKIE['HTTP_URL']))
        {
            setcookie('HTTP_URL', $HTTP_URL, time());
        }
        elseif ($_COOKIE['HTTP_URL']!=$HTTP_URL)
        {
            setcookie('HTTP_URL', $HTTP_URL, time());
            session_destroy();

            if ($session_save_handler == 'DB')
            {
                // redefine session handler (http://bugs.php.net/bug.php?id=32330)
                // redefine session handler
                __set_session_db_handler();
            }
            session_start();
        }

        if ($zone == "CustomerZone")
        {
            $sess = session_get_cookie_params();
            if ($drop_session_cookie)
                $t = 0;
            else
                $t = time()+$sess['lifetime'];
            setcookie(session_name(), session_id(), $t, '/');
            if ($application -> getCurrentProtocol() == 'http')
                $temp_url = $application->getAppIni('SITE_HTTPS_URL');
            else
                $temp_url = $application->getAppIni('SITE_URL');
            $temp_url = parse_url($temp_url);
            if (isset($temp_url['host']))
                setcookie(session_name(), session_id() , $t, '/', $temp_url['host']);
        }

        //
        //                                   ,                                   .
        //        ,                   .
        //         ,                                ,                   .
        //                                                                  .
        //                          IP                             .
        //                                                                  .
        //                                                   .
        //
        $CURRENT_PRODUCT_VERSION = PRODUCT_VERSION;

        $current_hash = "";
        if ($zone != "AdminZone")
        {
            loadModuleFile('configuration/const.php');
            /*_use(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'configuration'.DIRECTORY_SEPARATOR.'configuration_api.php');

            $tables = Configuration::getTables();
            $ss = $tables['store_settings']['columns'];

            $s = new DB_Select();
            $s->addSelectTable('store_settings');
            $s->addSelectValue('variable_value','value');
            $s->WhereValue($ss['name'], DB_EQ, SYSCONFIG_CHECKOUT_FORM_HASH);
            $v = $application->db->getDB_Result($s);
            $current_hash = $v[0]['value'];*/

            $cache = CCacheFactory::getCache('hash');
            $current_hash = $cache->read(SYSCONFIG_CHECKOUT_FORM_HASH);
        }

        $current_ips = getClientIPs();

        if((!array_key_exists('PRODUCT_VERSION', $_SESSION) ||
            $_SESSION['PRODUCT_VERSION'] != $CURRENT_PRODUCT_VERSION && $zone != "AdminZone")||
//            ! @array_intersect($_SESSION['REMOTE_ADDR'],  $current_ips) ||
//  @$_SESSION['HTTP_USER_AGENT'] != @$_SERVER['HTTP_USER_AGENT'] || commented so that SIM Method of Authorize.Net redirect to orderplaced page
            (!array_key_exists(SYSCONFIG_CHECKOUT_FORM_HASH, $_SESSION) ||
                $current_hash != $_SESSION[SYSCONFIG_CHECKOUT_FORM_HASH] && $zone != "AdminZone") )
        {
            if(!empty($_SESSION['CartContent']) && is_array($_SESSION['CartContent']))
            {
                foreach($_SESSION['CartContent'] as $cart_id => $cart_content)
                {
                    modApiFunc('Cart', 'removeGCfromDB', $cart_content['product_id']);
                }
            }
            //see this::session_clean1()
            session_unset();
            session_destroy();

            if ($session_save_handler == 'DB')
            {
                // redefine session handler (http://bugs.php.net/bug.php?id=32330)
                // redefine session handler
                __set_session_db_handler();
            }
            session_start();
        }
        $_SESSION['PRODUCT_VERSION'] = $CURRENT_PRODUCT_VERSION;
        $_SESSION[SYSCONFIG_CHECKOUT_FORM_HASH] = $current_hash;
        $_SESSION['REMOTE_ADDR'] = $current_ips;
        $_SESSION['HTTP_USER_AGENT'] = @$_SERVER['HTTP_USER_AGENT'];
        //

        foreach($_SESSION as $key => $val)
        {
            $this->keyvalList[$key] = $val;
//            $this->set($key, $val);
        }
        $this->started = TRUE;

        if ($zone == "AdminZone")
        {
            if (!$this->is_Set('ClientSessionLifetime'))
            {
                $this->set('ClientSessionLifetime', time());
            }
            else
            {
                $delta_time = time()-$this->get('ClientSessionLifetime');
                if (($delta_time > $ClientSessionLifetime)&&($ClientSessionLifetime!=0))
                {
                    $this->un_Set('currentUserID');
                }
                $this->set('ClientSessionLifetime', time());
            }
        }
    }

    /**
     * Checks if the session is initialized with start().
     *
     * @return boolean TRUE, if the session is initialized, FALSE - otherwise.
     */
    function isStarted()
    {
        return (TRUE == $this->started);
    }

    /**
     * Checks if a variable with the given name is stored in
     * the current session.
     *
     * @param string $key  the variable name
     * @return boolean TRUE, if there is a variable with given name in session
     * contents, FALSE - otherwise.
     */
    function is_Set($key)
    {
        return isset($this->keyvalList[$key]);
    }

    /**
     * Removes a variable from the list of variables which are stored in
     * the session.
     *
     * @param string $key the variable name
     */
    function un_Set($key)
    {
        unset($this->keyvalList[$key]);
        unset($_SESSION[$key]);
    }

    /**
     * Gets the session variable value by name.
     *
     * @param string $key the variable name
     * @return mixed  the value of the variable.
     */
    function get($key)
    {
        if($this->isStarted())
        {
            if(isset($this->keyvalList[$key]))
                return ($this->keyvalList[$key]);
            elseif($key=='__ASC_FORM_ID__')
				_fatal(array("CODE"=>"SESSION_001","MESSAGE"=>"Access denied due to security reason"));
//                die('Access denied due to security reason');
            else
				_fatal(array("CODE"=>"SESSION_002","MESSAGE"=>"Session::get() !isset(\$this->keyvalList[$key])"));
//                die("Session::get() !isset(\$this->keyvalList[$key])");
        }
        else
				_fatal(array("CODE"=>"SESSION_003","MESSAGE"=>"Session::getKey() !\$this->isStarted()"));
//            die("Session::getKey() !\$this->isStarted()");
    }

    /**
     * Sets a session variable value.
     *
     * @param string $key The variable name.
     * @param string $val The variable value.
     */
    function set($key, $val)
    {
        $this->keyvalList[$key] = $val;
        // save to DB, or _SESSION[__JC_SESSION] or something
        $_SESSION[$key] = $val;
    }

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * A "Session started" flag.
     */
    var $started = FALSE;

    /**
     * A session id.
     */
    var $id = NULL;

    /**
     * Session contents (temporary stub).
     */
    var $keyvalList = array();

    /**#@-*/
}

?>