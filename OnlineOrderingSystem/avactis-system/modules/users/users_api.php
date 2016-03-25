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
//define('INCORRECT_LOGIN_TIME_COUNT', 5);
//define('LOGIN_BLOCK_TIME', 10);
define('LOGIN_DELTA_TIME', 10);

define("ADMIN_OPTION_QUANTITY", 3); //Commented by Girin according to the Task ASC-82

define('ACCESS_DEFAULT',        0);
define('ACCESS_NONE',           1);
define('ACCESS_VIEW',           2);
define('ACCESS_MANAGE',         3);

define('PERMISSION_ADMIN',      1);
define('PERMISSION_SETTINGS',   2);
define('PERMISSION_CATALOG',    3);
define('PERMISSION_ORDERS',     4);
define('PERMISSION_CUSTOMERS',  5);
define('PERMISSION_REVIEWS',    6);
define('PERMISSION_REPORTS',    7);
define('PERMISSION_MARKETING',  8);
define('PERMISSION_DESIGN',     9);


/**
 * Class Users is a module, which works on accounts in the admin zone and
 * in the customer zone.
 * ???
 *
 * @ finish the functions on this page
 * @author Alexander Girin
 * @access public
 * @package Users
 */
class Users
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * A Users class constructor.
     *
     * @ ???
     */
    function Users()
    {
    }

    function init()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');

        $this->IncorrectLoginTimeCountArray = array(3 => "3", 5 => "5", 10 =>"10", 20 => "20");
        $this->LoginBlockTimeArray = array(5    => $this->MessageResources->getMessage("SIGN_IN_TIMEOUT_001")
                                          ,10   => $this->MessageResources->getMessage("SIGN_IN_TIMEOUT_002")
                                          ,15   => $this->MessageResources->getMessage("SIGN_IN_TIMEOUT_003")
                                          ,30   => $this->MessageResources->getMessage("SIGN_IN_TIMEOUT_004")
                                          ,60   => $this->MessageResources->getMessage("SIGN_IN_TIMEOUT_005")
                                          ,120  => $this->MessageResources->getMessage("SIGN_IN_TIMEOUT_006")
                                          ,300  => $this->MessageResources->getMessage("SIGN_IN_TIMEOUT_007")
                                          ,600  => $this->MessageResources->getMessage("SIGN_IN_TIMEOUT_008")
                                          ,1440 => $this->MessageResources->getMessage("SIGN_IN_TIMEOUT_009")
                                          );
        $this->LoginDeltaTimeArray = array("10"   => $this->MessageResources->getMessage("SIGN_IN_DELTA_TIME_001")
                                          ,"20"   => $this->MessageResources->getMessage("SIGN_IN_DELTA_TIME_002")
                                          ,"30"   => $this->MessageResources->getMessage("SIGN_IN_DELTA_TIME_003")
                                          ,"60"   => $this->MessageResources->getMessage("SIGN_IN_DELTA_TIME_004")
                                          ,"120"  => $this->MessageResources->getMessage("SIGN_IN_DELTA_TIME_005")
                                          ,"180"  => $this->MessageResources->getMessage("SIGN_IN_DELTA_TIME_006")
                                          );
    }

    /**
     * Gets the "incorrect logins count" array.
     *
     * @return
     */
    function getIncorrectLoginTimeCountArray()
    {
        return $this->IncorrectLoginTimeCountArray;
    }

    /**
     * Gets the "time of system lockups" array.
     *
     * @return
     */
    function getLoginBlockTimeArray()
    {
        return $this->LoginBlockTimeArray;
    }

    /**
     * @ describe the function Users->install.
     *
     * The install() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Users::getTables() instead of $this->getTables()
     */

    function install()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');

        $tables = Users::getTables();           #the array of the Catalog module tables
        $query = new DB_Table_Create($tables);

        # Dump data for table 'attribute_groups'
        $table = 'admin';                       #the name of the filled table
        $columns = $tables[$table]['columns'];  #the array of field names of the table

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue('Admin', $columns['firstname']);
        $query->addInsertValue('Name', $columns['lastname']);
        $query->addInsertValue('admin@localhost', $columns['email']);
        $query->addInsertValue('21232f297a57a5a743894a0e4a801fc3', $columns['password']);
        $query->addInsertValue('d41d8cd98f00b204e9800998ecf8427e', $columns['old_pass']);
        $query->addInsertExpression($query->fNow(), $columns['created']);
        $query->addInsertValue('0000-00-00', $columns['modified']);
        $query->addInsertValue('0000-00-00', $columns['logdate']);
        $query->addInsertValue('0', $columns['lognum']);
        $query->addInsertValue('false', $columns['remember_email']);
        $query->addInsertValue('15', $columns['options']);
        $application->db->getDB_Result($query);
    }

    /**
     * @ describe the function Users->install.
     *
     * The uninstall() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Users::getTables() instead of $this->getTables()
     */
    function uninstall()
    {

    }

    /**
     * Returns meta description of database tables, specified for storing Users data.
     *
     * @return array tables meta info
     */
    function getTables()
    {
        $tables = array ();

        $admin = 'admin';
        $tables[$admin] = array();
        $tables[$admin]['columns'] = array
            (
                'id'                => 'admin.admin_id'
               ,'firstname'         => 'admin.admin_firstname'
               ,'lastname'          => 'admin.admin_lastname'
               ,'email'             => 'admin.admin_email_address'
               ,'password'          => 'admin.admin_password'
               ,'old_pass'          => 'admin.admin_old_password'
               ,'created'           => 'admin.admin_created'
               ,'modified'          => 'admin.admin_modified'
               ,'logdate'           => 'admin.admin_logdate'
               ,'lognum'            => 'admin.admin_lognum'
               ,'remember_email'    => 'admin.admin_remember_email'
               ,'options'           => 'admin.admin_options'
               ,'lng'               => 'admin.admin_lng'
            );
        $tables[$admin]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'firstname'         => DBQUERY_FIELD_TYPE_CHAR50
               ,'lastname'          => DBQUERY_FIELD_TYPE_CHAR50
               ,'email'             => DBQUERY_FIELD_TYPE_CHAR50
               ,'password'          => DBQUERY_FIELD_TYPE_CHAR50
               ,'old_pass'          => DBQUERY_FIELD_TYPE_CHAR50
               ,'created'           => DBQUERY_FIELD_TYPE_DATE . ' default \'0000-00-00\''
               ,'modified'          => DBQUERY_FIELD_TYPE_DATE . ' default \'0000-00-00\''
               ,'logdate'           => DBQUERY_FIELD_TYPE_DATE . ' default \'0000-00-00\''
               ,'lognum'            => DBQUERY_FIELD_TYPE_INT
               ,'remember_email'    => DBQUERY_FIELD_TYPE_CHAR5
               ,'options'           => DBQUERY_FIELD_TYPE_INT
               ,'lng'               => DBQUERY_FIELD_TYPE_CHAR2
            );
        $tables[$admin]['primary'] = array
            (
                'id'
            );
        $tables[$admin]['indexes'] = array
            (
            );

        $admin_ip = 'admin_ip';
        $tables[$admin_ip] = array();
        $tables[$admin_ip]['columns'] = array
            (
                'id'                => 'admin_ip.admin_ip_id'
               ,'a_id'              => 'admin_ip.admin_id'
               ,'address'           => 'admin_ip.admin_ip_address'
               ,'count'             => 'admin_ip.admin_ip_incorrect_login_count'
               ,'time'              => 'admin_ip.admin_ip_last_login_time'
            );
        $tables[$admin_ip]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'a_id'              => DBQUERY_FIELD_TYPE_INT
               ,'address'           => DBQUERY_FIELD_TYPE_CHAR20
               ,'count'             => DBQUERY_FIELD_TYPE_INT
               ,'time'              => DBQUERY_FIELD_TYPE_INT
            );
        $tables[$admin_ip]['primary'] = array
            (
                'id'
            );
        $tables[$admin_ip]['indexes'] = array
            (
                'IDX_ai' => 'a_id'
            );

        $admin_ip = 'admin_permissions';
        $tables[$admin_ip] = array();
        $tables[$admin_ip]['columns'] = array
            (
                'admin_id'          => 'admin_permissions.admin_id',
                'permission'        => 'admin_permissions.permission',
                'access_level'      => 'admin_permissions.access_level',
            );
        $tables[$admin_ip]['types'] = array
            (
                'admin_id'          => DBQUERY_FIELD_TYPE_INT,
                'permission'        => DBQUERY_FIELD_TYPE_INT,
                'access_level'      => DBQUERY_FIELD_TYPE_INT,
            );
        $tables[$admin_ip]['primary'] = array
            (
            );
        $tables[$admin_ip]['indexes'] = array
            (
                'UNIQUE KEY IDX_ap' => 'admin_id, permission',
            );

        global $application;
        return $application->addTablePrefix($tables);
    }

    /**
     * Restores the module state.
     */
    function loadState()
    {
        if(modApiFunc('Session', 'is_Set', 'currentUserID'))
        {
            $uid = modApiFunc('Session', 'get', 'currentUserID');
            $this->setCurrentUserID($uid);
        }
        else
        {
            $this->currentUserId = NULL;
        }
        if(modApiFunc('Session', 'is_Set', 'selectedUserID'))
        {
            $uid = modApiFunc('Session', 'get', 'selectedUserID');
            $this->setSelectedUserID($uid);
        }
        else
        {
            $this->selectedUserId = NULL;
        }
        if(modApiFunc('Session', 'is_Set', 'deleteAdminMembersID'))
        {
            $array_id = modApiFunc('Session', 'get', 'deleteAdminMembersID');
            $this->setDeleteAdminMembersID($array_id);
        }
        else
        {
            $this->deleteAdminMembersID = array();
        }
        if(modApiFunc('Session', 'is_Set', 'currentUserPasswordUpdate'))
        {
            $uid = modApiFunc('Session', 'get', 'currentUserPasswordUpdate');
            $this->currentUserPasswordUpdate = true;
        }
        else
        {
            $this->currentUserPasswordUpdate = NULL;
        }
        if(modApiFunc('Session', 'is_Set', 'showEditButtonInInfo'))
        {
            $this->showEditButtonInInfo = modApiFunc('Session', 'get', 'showEditButtonInInfo');
        }
        else
        {
            $this->showEditButtonInInfo = true;
        }
    }

    /**
     * Saves the module state.
     */
    function saveState()
    {
        if ($this->currentUserID != NULL)
        {
            modApiFunc('Session', 'set', 'currentUserID', $this->currentUserID);
        }
        if ($this->selectedUserID != NULL)
        {
            modApiFunc('Session', 'set', 'selectedUserID', $this->selectedUserID);
        }
        if (sizeof($this->deleteAdminMembersID) != 0)
        {
            modApiFunc('Session', 'set', 'deleteAdminMembersID', $this->deleteAdminMembersID);
        }
        modApiFunc('Session', 'set', 'currentUserPasswordUpdate', $this->currentUserPasswordUpdate);
        modApiFunc('Session', 'set', 'showEditButtonInInfo', $this->showEditButtonInInfo);
    }

    /**
     * Sets up a current user.
     */
    function setCurrentUserID($uid)
    {
        $this->currentUserID = $uid;
    }

    /**
     * Unsets the current user.
     */
    function unsetCurrentUserID()
    {
        modApiFunc('Session', 'un_Set', 'currentUserID');
        $this->currentUserID = NULL;
    }

    /**
     * Gets a current user ID.
     */
    function getCurrentUserID()
    {
        return $this->currentUserID;
    }

    /**
     * Gets the account info by email.
     *
     * @param string $email admin e-mail
     * @return array the account info
     */
    function getAcountInfoByEmail($email)
    {
        global $application;

        $tables = $this->getTables();
        $columns = $tables['admin']['columns'];
        $query = new DB_Select();
        $query->AddSelectField($columns['id'], 'id');
        $query->AddSelectField($columns['email'], 'email');
        $query->AddSelectField($columns['password'], 'password');
        $query->AddSelectField($columns['old_pass'], 'old_password');
        $query->WhereValue($columns['email'], DB_EQ, $email);
        $acountInfo = $application->db->getDB_Result($query);
        return $acountInfo;
    }

    /**
     * Gets the account info by id.
     *
     * @param string $email admin e-mail
     * @return array the account info
     */
    function getAcountInfoById($uid)
    {
        global $application;

        $tables = $this->getTables();
        $columns = $tables['admin']['columns'];
        $query = new DB_Select();
        $query->AddSelectField($columns['id'], 'id');
        $query->AddSelectField($columns['email'], 'email');
        $query->AddSelectField($columns['password'], 'password');
        $query->AddSelectField($columns['old_pass'], 'old_password');
        $query->WhereValue($columns['id'], DB_EQ, $uid);
        $acountInfo = $application->db->getDB_Result($query);
        return $acountInfo;
    }

    /**
     * Gets the account language by id
     */
    function getAccountLanguageById($uid)
    {
        global $application;

        $tables = $this -> getTables();
        $columns = $tables['admin']['columns'];
        $query = new DB_Select();
        $query -> AddSelectField($columns['lng'], 'lng');
        $query -> WhereValue($columns['id'], DB_EQ, $uid);
        $info = $application -> db -> getDB_Result($query);
        return @$info[0]['lng'];
    }

    /**
     * Updates admin account language
     */
    function updateAccountLanguage($uid, $lng)
    {
        global $application;
        $tables = $this -> getTables();
        $table = 'admin';
        $columns = $tables[$table]['columns'];

        $query = new DB_Update($table);
        $query -> addUpdateValue($columns['lng'], $lng);
        $query -> addUpdateExpression($columns['modified'], $query -> fNow());
        $query -> WhereValue($columns['id'], DB_EQ, $uid);
        return $application -> db -> getDB_Result($query);
    }

    /**
     * Updates the admin account info.
     *
     * @param integer $uid -  admin id
     * @param string $email admin e-mail
     * @param string $password admin password
     * @return array the account info
     */
    function updateAcountInfo($uid, $email, $password, $need_update = false)
    {

        global $application;
        $tables = $this->getTables();
        $table = 'admin';
        $columns = $tables[$table]['columns'];

        $query = new DB_Update($table);
        $query->addUpdateValue($columns['email'], $email);
        $query->addUpdateValue($columns['password'], $password);
        if ($need_update)
        {
            $query->addUpdateValue($columns['old_pass'], md5(""));
        }
        else
        {
            $query->addUpdateValue($columns['old_pass'], $password);
        }
        $query->addUpdateExpression($columns['modified'], $query->fNow());
        $query->WhereValue($columns['id'], DB_EQ, $uid);
        return $application->db->getDB_Result($query);
    }

    /**
     * Gets information on removed ip address from the database,                       login to AdminZone
     *
     * @param string $ip - ip address
     * @return array
     */
    function getIpInfo($ip)
    {
        global $application;
        $tables = $this->getTables();
        $table = 'admin_ip';
        $columns = $tables[$table]['columns'];

        $query = new DB_Select();
        $query->addSelectField($columns['id'], 'id');
        $query->addSelectField($columns['a_id'], 'a_id');
        $query->addSelectField($columns['address'], 'address');
        $query->addSelectField($columns['count'], 'count');
        $query->addSelectField($columns['time'], 'time');
        $query->WhereValue($columns['address'], DB_EQ, $ip);
        return $application->db->getDB_Result($query);
    }

    /**
     * Logs the data about correct login in the admin area to the database.
     *
     * @param integer $uid - admin id
     * @return
     */
    function correctLogin($uid)
    {
        global $application;
        $tables = $this->getTables();
        $table = 'admin_ip';
        $columns = $tables[$table]['columns'];

        $ip = $_SERVER['REMOTE_ADDR'];
        $result = $this->getIpInfo($ip);
        if (sizeof($result)==0)
        {
            $query = new DB_Insert($table);
            $query->addInsertValue($uid, $columns['a_id']);
            $query->addInsertValue($ip, $columns['address']);
            $query->addInsertValue(0, $columns['count']);
            $query->addInsertValue(time(), $columns['time']);
            $application->db->getDB_Result($query);
        }
        else
        {
            $query = new DB_Update($table);
            $query->addUpdateValue($columns['a_id'], $uid);
            $query->addUpdateValue($columns['address'], $ip);
            $query->addUpdateValue($columns['count'], 0);
            $query->addUpdateValue($columns['time'], time());
            $query->WhereValue($columns['address'], DB_EQ, $ip);
            $application->db->getDB_Result($query);
        }

        $table = 'admin';
        $columns = $tables[$table]['columns'];

        $query = new DB_Update($table);
        $query->addUpdateExpression($columns['logdate'], $query->fNow());
        $query->addUpdateExpression($columns['lognum'], '('.$columns['lognum'].'+1)');
        $query->WhereValue($columns['id'], DB_EQ, $uid);
        $application->db->getDB_Result($query);
    }

    /**
     * Logs the data about incorrect login in the admin area to the database.
     *
     * @param integer $uid - admin id
     * @return
     */
    function incorrectLogin()
    {
        global $application;
        $tables = $this->getTables();
        $table = 'admin_ip';
        $columns = $tables[$table]['columns'];

        $ip = $_SERVER['REMOTE_ADDR'];
        $result = $this->getIpInfo($ip);
        if (sizeof($result)==0)
        {
            $query = new DB_Insert($table);
            $query->addInsertValue($ip, $columns['address']);
            $query->addInsertValue(1, $columns['count']);
            $query->addInsertValue(time(), $columns['time']);
            $application->db->getDB_Result($query);
        }
        else
        {
            $count = $result[0]['count']+1;
            if ((time()-$result[0]['time'])>(LOGIN_DELTA_TIME*60))
            {
                $count = 1;
            }
            if ($count>=modApiFunc("Configuration", "getValue", "store_signin_count"))
            {
                modApiFunc("Session", "set", "AdminZoneIsBlocked", true);
                $this->letterAboutBlockAdminZone();
            }
            $query = new DB_Update($table);
            $query->addUpdateValue($columns['address'], $ip);
            $query->addUpdateValue($columns['count'], $count);
            if ($count == 1)
            {
                $query->addUpdateValue($columns['time'], time());
            }
            $query->WhereValue($columns['address'], DB_EQ, $ip);
            $application->db->getDB_Result($query);
        }
    }

    /**
     * Generates a new user password.
     *
     * @param string $email -  user e-mail
     * @return array
     */
    function generateNewAdminPassword($email)
    {

        global $application;
        $tables = $this->getTables();
        $table = 'admin';
        $columns = $tables[$table]['columns'];

        $newPassword = '';
        for ($i=0; $i<8; $i++)
        {
            $symbol = '';
            do
            {
                $symbol = _byte_chr(rand(48, 122));
            }
            while (! preg_match("/[0-9a-zA-Z]/", $symbol));
            $newPassword.= $symbol;
        }

        $query = new DB_Update($table);
        $query->addUpdateValue($columns['password'], md5($newPassword));
        $query->addUpdateExpression($columns['modified'], $query->fNow());
        $query->WhereValue($columns['email'], DB_EQ, $email);
        $application->db->getDB_Result($query);

        $this->letterAboutNewPassword($email, $newPassword);
    }

    /**
     * Prepares a letter for admin about blocking the admin area.
     *
     * @ paste e-mail addresses from config
     */
    function letterAboutBlockAdminZone()
    {

        $email_text = modApiFunc('TmplFiller', 'fill', "./../letters/","block_admin_zone.tpl.txt",
                                                      array(
                                                            "IP_ADDRESS" => $_SERVER['REMOTE_ADDR']
                                                           ,"COUNT"      => modApiFunc("Configuration", "getValue", "store_signin_count")
                                                           ,"ACCESS_TIME"=> $this->LoginDeltaTimeArray[LOGIN_DELTA_TIME]
                                                           ,"TIME"       => $this->LoginBlockTimeArray[modApiFunc("Configuration", "getValue", "store_signin_timeout")]
                                                           )
                                );
        $email_subj = modApiFunc('TmplFiller', 'fill', "./../letters/","block_admin_zone_subj.tpl.txt",
                                                      array(
                                                            "IP_ADDRESS" => $_SERVER['REMOTE_ADDR']
                                                           )
                                );
        loadCoreFile('ascHtmlMimeMail.php');
        $mail = new ascHtmlMimeMail();
        $mail->setText($email_text);
        $mail->setSubject($email_subj);
        $from_email = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_EMAIL);
        if (!$this->isValidEmail($from_email))
        {
            $admin_info = $this->getAcountInfoById(1);
            $from_email = isset($admin_info[0]["email"])? $admin_info[0]["email"]:"";
            if (!$this->isValidEmail($from_email))
            {
                $from_email = "Avactis Shopping Cart Software";
            }
        }
        $to_email = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL);
        if (!$this->isValidEmail($to_email))
        {
            $admin_info = $this->getAcountInfoById(1);
            $to_email = isset($admin_info[0]["email"])? $admin_info[0]["email"]:"";
            if (!$this->isValidEmail($to_email))
            {
                $to_email = "avactis@localhost";
            }
        }
        $from = $from_email;
        $mail->setFrom($from);
        return $mail->send(array($to_email));
    }

    /**
     * Prepares a letter to a user about changing his password.
     *
     * @ paste e-mail addresses from config
     */
    function letterAboutNewPassword($email, $password)
    {

        $email_text = modApiFunc('TmplFiller', 'fill', "./../letters/","new_admin_password.tpl.txt",
                                                      array(
                                                            "Email"    => $email
                                                           ,"Password" => $password
                                                           )
                                );

        $email_subj = modApiFunc('TmplFiller', 'fill', "./../letters/","new_admin_password_subj.tpl.txt",
                                                      array()
                                );

        loadCoreFile('ascHtmlMimeMail.php');
        $mail = new ascHtmlMimeMail();
        $mail->setText($email_text);
        $mail->setSubject($email_subj);
        $from_email = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_EMAIL);
        if (!$this->isValidEmail($from_email))
        {
            $admin_info = $this->getAcountInfoById(1);
            $from_email = isset($admin_info[0]["email"])? $admin_info[0]["email"]:"";
            if (!$this->isValidEmail($from_email))
            {
                $from_email = "Avactis Shopping Cart Software";
            }
        }
        $from = $from_email;
        $mail->setFrom($from);
        return $mail->send(array($email));
    }

    /**
     * Checks, if AdminZone is blocked or not.
     *
     * @ paste e-mail addresses from config
     */
    function isBlocked()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $result = $this->getIpInfo($ip);
        if (sizeof($result)!=0)
        {
            if ($result[0]['count']>=modApiFunc("Configuration", "getValue", "store_signin_count")&&(time()<($result[0]['time']+modApiFunc("Configuration", "getValue", "store_signin_timeout")*60)))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Sets up a current zone.
     */
    function setZone($zone)
    {
        $this->zone = $zone;
    }

    /**
     * Gets a current zone.
     */
    function getZone()
    {
        global $zone;
        return $zone;
    }

    /**
     * Checks, whether a user has a login or not.
     */
    function isUserSignedIn()
    {
        if ($this->getCurrentUserID()!=NULL)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Checks, whether the email matches regular expressions.
     *
     * @param string $email - admin email
     * @return boolean true, if the email matches, false otherwise
     */
    function isValidEmail($email)
    {
        $retval = true;
        if (! preg_match("/^[a-z0-9]+([\.\-_][a-z0-9_-]+)*@[a-z0-9\.\-]+?\.[a-z]{2,4}$/i", trim($email)))
        {
            $retval=false;
        }
        return $retval;
    }

    /**
     * Checks, whether an e-mail address exists in the database.
     *
     * @param string $email - email address
     * @param integer $uid - user id
     * @return true, if it exists, false otherwise
     */
    function isEmailExists($email, $uid)
    {
        global $application;
        $tables = $this->getTables();
        $table = 'admin';
        $a = $tables[$table]['columns'];

        $query = new DB_Select();
        $query->addSelectField($a['id'], 'id');
        $query->WhereValue($a['email'], DB_EQ, $email);
        $query->WhereAnd();
        $query->WhereValue($a['id'], DB_NEQ, $uid);
        $result = $application->db->getDB_Result($query);
        if (sizeof($result) == 0)
        {
            return false;
        }
        return true;
    }

    /**
     * Sets up a flag, that indicates the changing of the admin zone access
     * password.
     *
     * @return
     */
    function setPasswordUpdate()
    {
        $this->currentUserPasswordUpdate = true;
    }

    /**
     * Unsets the flag, that indicates the changing of the admin zone access
     * password.
     *
     * @return
     */
    function unsetPasswordUpdate()
    {
        $this->currentUserPasswordUpdate = NULL;
    }

    /**
     * Returns the flag value, that indicates the changing of the admin zone access
     * password.
     *
     * @return
     */
    function getPasswordUpdate()
    {
        return $this->currentUserPasswordUpdate;
    }

    /**
     *
     *
     * @
     * @param
     * @return
     */
    function getAdminMembersList()
    {
        global $application;
        $tables = $this->getTables();
        $a = $tables["admin"]["columns"];

        $query = new DB_Select();
        $query->addSelectField($a['id'], 'id');
        $query->addSelectField($a['firstname'], 'firstname');
        $query->addSelectField($a['lastname'], 'lastname');
        $query->addSelectField($a['email'], 'email');
        $query->addSelectField($a['lognum'], 'lognum');
        $query->addSelectField($a['logdate'], 'logdate');
        $query->addSelectField($a['created'], 'created');
        $query->addSelectField($a['modified'], 'modified');
        return $application->db->getDB_Result($query);
    }

    /**
     * Gets the selected user id.
     */
    function getSelectedUserID()
    {
        return $this->selectedUserID;
    }

    /**
     * Sets up the selected user id.
     */
    function setSelectedUserID($uid)
    {
        $this->selectedUserID = $uid;
    }

    /**
     * Unsets the selected user id.
     */
    function unsetSelectedUserID()
    {
        modApiFunc('Session', 'un_Set', 'selectedUserID');
        $this->selectedUserID = NULL;
    }

    /**
     *  Gets the selected user id.
     */
    function getShowEditButtonInInfo()
    {
        return $this->showEditButtonInInfo;
    }

    /**
     *  Sets up the selected user id.
     */
    function setShowEditButtonInInfo($val)
    {
        $this->showEditButtonInInfo = $val;
    }

    /**
     * Gets an array of selected admins id to delete.
     */
    function getDeleteAdminMembersID()
    {
        return $this->deleteAdminMembersID;
    }

    /**
     * Sets up an array of selected admins id to delete.
     */
    function setDeleteAdminMembersID($array_id)
    {
        $this->deleteAdminMembersID = $array_id;
    }

    /**
     * Unsets the array of selected admins id to delete.
     */
    function unsetDeleteAdminMembersID()
    {
        modApiFunc('Session', 'un_Set', 'deleteAdminMembersID');
        $this->deleteAdminMembersID = NULL;
    }


    /**
     * Gets detailed user info.
     *
     * @
     * @param
     * @return
     */
    function getUserInfo($uid)
    {
        global $application;
        $tables = $this->getTables();
        $a = $tables["admin"]["columns"];
        $query = new DB_Select();
        $query->addSelectField($a['id'], 'id');
        $query->addSelectField($a['firstname'], 'firstname');
        $query->addSelectField($a['lastname'], 'lastname');
        $query->addSelectField($a['email'], 'email');
        $query->addSelectField($a['lognum'], 'lognum');
        $query->addSelectField($a['logdate'], 'logdate');
        $query->addSelectField($a['created'], 'created');
        $query->addSelectField($a['modified'], 'modified');
        $query->addSelectField($a['options'], 'options');
        $query->WhereValue($a['id'], DB_EQ, $uid);
        $user_info = $application->db->getDB_Result($query);

        return @$user_info[0];
    }

    /**
     *
     *
     * @
     * @param
     * @return
     */
    function addAdmin($firs_name, $last_name, $e_mail, $password, $options, $need_update)
    {

        global $application;
        $tables = $this->getTables();

        $a = $tables['admin']['columns'];

        $query = new DB_Insert('admin');
        $query->addInsertValue($firs_name, $a['firstname']);
        $query->addInsertValue($last_name, $a['lastname']);
        $query->addInsertValue($e_mail, $a['email']);
        $query->addInsertValue($password, $a['password']);
        if ($need_update)
        {
            $query->addInsertValue(md5(""), $a['old_pass']);
        }
        else
        {
            $query->addInsertValue($password, $a['old_pass']);
        }
        $query->addInsertExpression($query->fNow(), $a['created']);
//        $query->addInsertValue('0000-00-00', $a['modified']);
//        $query->addInsertValue('0000-00-00', $a['logdate']);
        $query->addInsertValue('0', $a['lognum']);
        $query->addInsertValue('false', $a['remember_email']);
        $admin_options = 0;
        for ($i=1; $i<=sizeof($options); $i++)
        {
            $admin_options += $options[$i]? pow(2, ($i-1)):0;
        }
        $query->addInsertValue($admin_options, $a['options']);
        $application->db->getDB_Result($query);
        return $application->db->DB_Insert_Id();
    }

    /**
     *
     *
     * @
     * @param
     * @return
     */
    function updateAdmin($id, $firs_name, $last_name, $e_mail, $options)
    {

        global $application;
        $tables = $this->getTables();

        $a = $tables['admin']['columns'];

        $query = new DB_Update('admin');
        $query->addUpdateValue($a['firstname'], $firs_name);
        $query->addUpdateValue($a['lastname'], $last_name);
        $query->addUpdateValue($a['email'], $e_mail);
        $query->addUpdateExpression($a['modified'], $query->fNow());
        $admin_options = 0;
        for ($i=1; $i<=sizeof($options); $i++)
        {
            $admin_options += $options[$i]? pow(2, ($i-1)):0;
        }
        $query->addUpdateValue($a['options'], $admin_options);
        $query->WhereValue($a['id'], DB_EQ, $id);
        $application->db->getDB_Result($query);
    }

    /**
     * Deletes admins from the database.
     *
     * @param array $array_id - admin id array to delete
     * @return
     */
    function deleteAdmins($array_id)
    {

        if (!is_array($array_id))
        {
            return;
        }
        global $application;
        $tables = $this->getTables();

        $a = $tables['admin']['columns'];
        $query = new DB_Delete('admin');
        $query->WhereField($a['id'], DB_IN, "(".implode(", ", $array_id).")");
        $application->db->getDB_Result($query);
    }

    function getAccessLevelsArray()
        {
       return array(
           ACCESS_DEFAULT => $this->MessageResources->getMessage('ACCESS_DEFAULT'),
            ACCESS_NONE    => $this->MessageResources->getMessage('ACCESS_NONE'),
            ACCESS_VIEW    => $this->MessageResources->getMessage('ACCESS_VIEW'),
            ACCESS_MANAGE  => $this->MessageResources->getMessage('ACCESS_MANAGE'),
                           );
        }

    function getPermissionsArray()
    {
        return array(
            PERMISSION_ADMIN => array(
                'name' => $this->MessageResources->getMessage('PERMISSION_ADMIN'),
                'accesses' => array(ACCESS_NONE, ACCESS_MANAGE),
            ),
            PERMISSION_SETTINGS =>  array(
                'name' => $this->MessageResources->getMessage('PERMISSION_SETTINGS'),
                'accesses' => array(ACCESS_NONE, ACCESS_MANAGE),
            ),
            PERMISSION_CATALOG =>  array(
                'name' => $this->MessageResources->getMessage('PERMISSION_CATALOG'),
                'accesses' => array(ACCESS_NONE, ACCESS_MANAGE),
            ),
            PERMISSION_ORDERS =>  array(
                'name' => $this->MessageResources->getMessage('PERMISSION_ORDERS'),
                'accesses' => array(ACCESS_NONE, ACCESS_MANAGE),
            ),
            PERMISSION_CUSTOMERS =>  array(
                'name' => $this->MessageResources->getMessage('PERMISSION_CUSTOMERS'),
                'accesses' => array(ACCESS_NONE, ACCESS_MANAGE),
            ),
            PERMISSION_REVIEWS =>  array(
                'name' => $this->MessageResources->getMessage('PERMISSION_REVIEWS'),
                'accesses' => array(ACCESS_NONE, ACCESS_MANAGE),
            ),
            PERMISSION_REPORTS =>  array(
                'name' => $this->MessageResources->getMessage('PERMISSION_REPORTS'),
                'accesses' => array(ACCESS_NONE, ACCESS_MANAGE),
            ),
            PERMISSION_MARKETING =>  array(
                'name' => $this->MessageResources->getMessage('PERMISSION_MARKETING'),
                'accesses' => array(ACCESS_NONE, ACCESS_MANAGE),
            ),
            PERMISSION_DESIGN =>  array(
                'name' => $this->MessageResources->getMessage('PERMISSION_DESIGN'),
                'accesses' => array(ACCESS_NONE, ACCESS_MANAGE),
            ),
        );
    }


    function getAdminPermissions($uid)
    {
       $permissions = array();
       $r = execQuery('USERS_GET_USER_PERMISSIONS', array('admin_id' => $uid));
       if ($r) {
           foreach ($r as $row) {
               $permissions[ $row['permission'] ] = $row['access_level'];
           }
       }
       return $permissions;
    }

    function setAdminPermissions($uid, $permissions)
    {
        global $application;

        $tables = Users::getTables();
        $table = 'admin_permissions';
        $columns = & $tables[$table]['columns'];

        $query = new DB_Multiple_Replace($table);
        foreach ($permissions as $p => $a) {
           $query->addReplaceValuesArray(array($uid, $p, $a));
         }
        $application->db->getDB_Result($query);
     }


    function checkPermission($uid, $check_permission)
        {
        if (isset($check_permission)) {
            if (! isset($this->admin_access_cache[$uid])) {
                $this->admin_access_cache[$uid] = $this->getAdminPermissions($uid);
            }
            if (isset($this->admin_access_cache[$uid][$check_permission]) &&
                    $this->admin_access_cache[$uid][$check_permission] == ACCESS_NONE) {
                //                                             ;                   -            .
                //                                (                ,            )                  ;
                //                                                                            .
                return false;
        }
    }
        // anybody is allowed to do anything by default
        return true;
     }

    function checkCurrentUserPermission($check_permission)
    {
        return $this->checkPermission($this->getCurrentUserID(), $check_permission);
    }


    function checkCurrentUserAccess($view_name)
    {
       return $this->checkUserAccess($this->getCurrentUserID(), $view_name);
    }

    function checkUserAccess($uid, $view_name)
    {
        return $this->checkPermission($uid, $this->getPermissionForView($view_name));
    }

    function getPermissionForView($view_name)
    {
        if (! isset($this->views_permissions)) {
            $this->views_permissions = array();
            $permissions_views = $this->getPermissionsForViews();
            foreach ($permissions_views as $p => $vs) {
                foreach ($vs as $v) {
                    $this->views_permissions[$v] = $p;
                }
            }
        }
        return @$this->views_permissions[$view_name];
    }

    function getPermissionsForViews()
    {
       return array(
           PERMISSION_ADMIN => array(
               // 'Admin Page', 'Administration' section
               'AdminMembers', 'ReportsResetData',
               'TimelineView', 'TimelineItemView',
               // 'Admin Page', 'Tools' section
               'Backup', 'BackupCreate', 'BackupDeleteProgress', 'BackupDelete', 'BackupInfo', 'BackupProgress', 'BackupRestore',
                'ServerInfo', 'CacheSettings', 'LicenseInfo', 'HTTPSSettings', 'MailParamList','AddMarketPlaceExtensions',
		//Extension Installer
		'MM_ListView',
           ),
            PERMISSION_SETTINGS => array(
                // 'Admin Page', 'Advanced Settings & Configuration' section
                'SettingParamList',
                // 'Store Settings' page, 'Store Configuration' section
                'GeneralSettings', 'StoreOwner',
                'CheckoutPaymentModulesList', 'CheckoutPaymentModuleSettings',
                'ShippingCostCalculatorSection', 'CheckoutShippingModulesList',

                'ShippingCostCalculatorSettings', 'ShippingTesterWindow', 'FreeShippingRulesList', 'AddFsRule', 'EditFsRuleArea', 'EditFsRule',

                'NotificationsList',
                'PF_Settings', 'PI_Settings', 'MR_Settings', 'QB_Settings',
                // 'Store Settings' page, 'Checkout and Customer Account Settings' section
                'CheckoutInfoList', 'ManageCustomFields',
                'RegisterFormEditor', 'CreditCardSettings', 'CreditCardAttributes',
                // 'Store Settings' page, 'Location/Taxes/Localization' section
                'CountriesSettings', 'LanguageSettings', 'LabelEditor', 'StatesSettings',
                'TaxSettings', 'AddTaxClass',
                'AddTaxDisplayOption', 'AddTaxName', 'AddTaxRate',
                'TaxCalculator', 'EditTaxClass', 'EditTaxDisplayOption',
                'EditTaxName', 'EditTaxRate', 'ShippingModulesListForTaxes',
                'TaxRateByZip_Sets', 'TaxRateByZip_AddNewSet',
                'DateTimeSettings', 'NumberSettings', 'WeightSettings',
                // 'Store Settings' page, 'Currency Settings' section
                'CurrencySettings', 'CurrencyRateEditor',
            ),
            PERMISSION_CATALOG => array(
                'ProductList',
                'AddProductInfo', 'CopyProducts', 'DeleteProducts', 'EditCustomAttribute', 'EditProductInfo',
                'MoveProducts', 'AddCustomAttribute', 'ProductGroupEdit', 'ProductInfo', 'SortProducts',

                'SearchForm', 'SearchResult',

                'NavigationBar',
                'AddCategory', 'DeleteCategory', 'EditCategory',
                'MoveCategory', 'SortCategories', 'ViewCategory',

                'ManageProductTypes', 'SelectProductType', 'EditProductType',
                'DeleteProductType', 'EditProductType', 'AddProductType',

                'ManufacturersList', 'AddManufacturer', 'EditManufacturer', 'SortManufacturers',
                'ExportProductsView', 'ImportProductsView', 'Froogle_Export',

                'PO_OptionsList', 'PO_CRulesEditor', 'PO_CRulesList',
                'PO_EditExs', 'PO_EditOption', 'PO_InvEditor',
                'PO_InvPage', 'PO_AddOption',

                'PF_FilesList', 'PI_ImagesList',
            ),

            PERMISSION_ORDERS => array(
                'ManageOrders', 'OrderInfo', 'OrderInvoice', 'OrderPackingSlip',
            ),
            PERMISSION_CUSTOMERS => array(
                'CustomersList', 'CustomerInfo', 'CustomerAccountInfo',
            ),
            PERMISSION_REVIEWS => array(
                'ManageCustomerReviews', 'CR_Review_Data', 'CR_Select_Product',
            ),
            PERMISSION_REPORTS => array(
                // Report on the Home page
                'ReportRecentVisitorStatisticsShort', 'ReportTop10SellersByItemsLast30Days',
                'ChartOrdersByDayLast10Days', 'ChartOrdersByDayLast10Months',
                'ReportGroupPage',
            ),
            PERMISSION_MARKETING => array(
                'discounts_manage_global_discounts_az', 'manage_quantity_discounts_az',

                'PromoCodesNavigationBar', 'EditPromoCodeArea', 'EditPromoCode', 'AddPromoCode',

                'Newsletter_List', 'Newsletter_Compose', 'NewsSettings', 'Subscriptions_Signature',

                'Subscriptions_Manage', 'Subscriptions_EditTopic', 'Subscriptions_SortTopics',
                'Subscriptions_Export', 'Subscriptions_DeleteTopics', 'Subscriptions_Subscribe',

                'TransactionTrackingSettings',

                'GiftCertificateListView',
            ),
            PERMISSION_DESIGN => array(
                'SkinList',
                'LayoutCMS',
            ),
        );
    }


    function checkCurrentUserAction($action_name)
    {
        return $this->checkUserAction($this->getCurrentUserID(), $action_name);
    }


    function checkUserAction($uid, $action_name)
    {
        return $this->checkPermission($uid, $this->getPermissionForAction($action_name));
    }


    function getPermissionForAction($action_name)
    {
        if (! isset($this->actions_permissions)) {
            $this->actions_permissions = array();
            $permissions_actions = $this->getPermissionsForActions();
            foreach ($permissions_actions as $p => $as) {
                foreach ($as as $a) {
                    $this->actions_permissions[$a] = $p;
                }
            }
        }
        return @$this->actions_permissions[$action_name];
     }


    function getPermissionsForActions()
    {
        return array(
            PERMISSION_ADMIN => array(
                'AddAdmin', 'EditAdmin', 'SetDeleteAdminMembers', 'ConfirmDeleteAdmins',
            ),
            PERMISSION_SETTINGS => array(
                'UpdateGeneralSettings',
            ),
            PERMISSION_CATALOG => array(
            ),
            PERMISSION_ORDERS => array(
            ),
            PERMISSION_CUSTOMERS => array(
            ),
            PERMISSION_REVIEWS => array(
            ),
            PERMISSION_REPORTS => array(
                'getReportContent',
            ),
            PERMISSION_MARKETING => array(
            ),
            PERMISSION_DESIGN => array(
                'Change_Skin',
                'add_new_page', 'delete_page', 'get_available_blocks', 'get_layout_tmpl', 'save_layout_tmpl',
            ),
       );
    }


    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Current Zone - AdminZone or CustomerZone.
     */
    var $zone;

    /**
     * Current User ID
     */
    var $currentUserID = NULL;

    /**
     * Selected User ID.
     */
    var $selectedUserID;

    var $deleteAdminMembersID = array();

    /**
     * Current User Need to change the password.
     */
    var $currentUserPasswordUpdate = NULL;

    /**
     * The array of max count of logins , after which the login is temporary blocked.
     */
    var $IncorrectLoginTimeCountArray;

    /**
     * The array of delta time, during which logins are performed.
     */
    var $LoginDeltaTimeArray;

    /**
     * The array of delta time, during which the login is blocked.
     */
    var $LoginBlockTimeArray;

    var $admin_access_cache = array();
    var $views_permissions;
    var $actions_permissions;
    /**#@-*/
}
?>