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
 * @package Froogle
 * @author Egor Makarov
 */
class Froogle
{
    function Froogle()
    {
    }

    function install()
    {
        _use(dirname(__FILE__) . "/includes/install.inc");
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Froogle::getTables());
    }

    /**
     *                                            .   .                    Google Base 'payment_accepted'.
     * @return array
     */
    function getPaymentAcceptedList()
    {
    	return array('Cash', 'Check', 'Visa', 'MasterCard', 'AmericanExpress', 'Discover', 'WireTransfer', 'GoogleCheckout');
    }

    function getSettings()
    {
        global $application;
        $tables = $this->getTables();

        $query = new DB_Select();
        $query->addSelectTable('frg_settings');
        $query->addSelectField('*');
        $res=$application->db->getDB_Result($query);

        $settings=array();

        foreach($res as $k => $sval)
            $settings[$sval['setting_key']] = $sval['setting_value'];

        return $settings;
    }

    function updateSettings($settings)
    {
        global $application;
        $tables = $this->getTables();
        $stable = $tables['frg_settings']['columns'];

        foreach($settings as $skey => $sval)
        {
            $query = new DB_Update('frg_settings');
            $query->addUpdateValue($stable['setting_value'],$sval);
            $query->WhereValue($stable['setting_key'], DB_EQ, $skey);
            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        };
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array();

        $table = 'frg_settings';
        $tables[$table] = array();
        $tables[$table]['columns'] = array(
            'setting_id'    => $table.'.setting_id'
           ,'setting_key'   => $table.'.setting_key'
           ,'setting_value' => $table.'.setting_value'
        );
        $tables[$table]['types'] = array(
            'setting_id'    => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
           ,'setting_key'   => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'setting_value' => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
        );
        $tables[$table]['primary'] = array(
            'setting_id'
        );

        global $application;
        return $application->addTablePrefix($tables);
    }

}
?>