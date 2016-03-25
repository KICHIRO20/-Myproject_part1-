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
 * @package CustomerAccount
 * @author Egor V. Derevyankin
 *
 */

class Customer_Account_Installer
{
    function Customer_Account_Installer()
    {
        $this->attr_lang_code = array();
    }

    function doInstall()
    {
        loadCoreFile('obj_xml.php');

        $parser = new xml_doc(file_get_contents(CUSTOMER_ACCOUNT_INSTALL_DATA_XML));
        $parser->parse();

        $groups_ids = array();
        $attrs_ids = array();

        foreach($parser->xml_index as $tag)
        {
            if($tag->name == 'CUSTOMER_ACCOUNT_INSTALL_DATA')
            {
                foreach($tag->children as $id_child)
                {
                    switch($id_child->name)
                    {
                        case 'PERSON_INFO_GROUPS':
                            $groups_ids = $this->__install_ProcessPersonInfoGroups($id_child);
                            break;
                        case 'PERSON_INFO_ATTRS':
                            $attrs_ids = $this->__install_ProcessPersonInfoAttrs($id_child);
                            break;
                        case 'ATTRS_TO_GROUPS':
                            $this->__install_ProcessAttrsToGroups($id_child,$groups_ids,$attrs_ids);
                            break;
                        case 'SETTINGS':
                            $this->__install_ProcessSettings($id_child);
                            break;
                    };
                };
            };
        };

        $this->__install_AddAttrsVisibleNames();
        $this->__install_addEventsHandlers();

        return;
    }

    function __install_ProcessPersonInfoGroups($pig_tag)
    {
        $ids = array();

        foreach($pig_tag->children as $child)
        {
            if($child->name == 'GROUP')
            {
                list($name, $id) = $this->__install_ProcessPIG($child, (count($ids)+1));
                $ids[$name] = $id;
            };
        };

        return $ids;
    }

    function __install_ProcessPIG($tag, $so)
    {
        global $application;
        $tables = Customer_Account::getTables();
        $pig_table = $tables['ca_person_info_groups']['columns'];

        $name = '';
        $lang_code = '';

        foreach($tag->children as $child)
        {
            switch($child->name)
            {
                case 'NAME': $name = $child->contents; break;
                case 'LANG_CODE': $lang_code = $child->contents; break;
            };
        };

        $query = new DB_Insert('ca_person_info_groups');
        $query->addInsertValue($name, $pig_table['group_name']);
        $query->addInsertValue($lang_code, $pig_table['lang_code']);
        $query->addInsertValue($so, $pig_table['sort_order']);
        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();

        return array($name, $application->db->DB_Insert_Id());
    }

    function __install_ProcessPersonInfoAttrs($pia_tag)
    {
        $ids = array();

        foreach($pia_tag->children as $child)
        {
            if($child->name == 'ATTR')
            {
                list($name, $id) = $this->__install_ProcessPIA($child);
                $ids[$name] = $id;
            };
        };

        return $ids;
    }

    function __install_ProcessPIA($tag)
    {
        global $application;
        $tables = Customer_Account::getTables();
        $pia_table = $tables['ca_person_info_attrs']['columns'];

        $name = '';
        $lang_code = '';

        foreach($tag->children as $child)
        {
            switch($child->name)
            {
                case 'NAME': $name = $child->contents; break;
                case 'LANG_CODE': $lang_code = $child->contents; break;
            };
        };

        $query = new DB_Insert('ca_person_info_attrs');
        $query->addInsertValue($name, $pia_table['attr_name']);
        $query->addInsertValue($lang_code, $pia_table['lang_code']);
        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();

        $attr_id = $application->db->DB_Insert_Id();

        $this->attr_lang_code[$attr_id] = $lang_code;

        return array($name, $attr_id);
    }

    function __install_ProcessAttrsToGroups($atg_tag, $g_ids, $a_ids)
    {
        global $application;
        $tables = Customer_Account::getTables();
        $atg_table = $tables['ca_attrs_to_groups']['columns'];

        foreach($atg_tag->children as $child)
        {
            if($child->name == 'GROUP')
            {
                $i = 0;
                foreach($child->children as $a_tag)
                {
                    if($a_tag->name == 'ATTR')
                    {
                        $query = new DB_Insert('ca_attrs_to_groups');
                        $query->addInsertValue($g_ids[$child->attributes['NAME']], $atg_table['group_id']);
                        $query->addInsertValue($a_ids[$a_tag->attributes['NAME']], $atg_table['attr_id']);
                        if(array_key_exists('VISIBLE',$a_tag->attributes))
                            $query->addInsertValue($a_tag->attributes['VISIBLE'], $atg_table['is_visible']);
                        if(array_key_exists('REQUIRED',$a_tag->attributes))
                            $query->addInsertValue($a_tag->attributes['REQUIRED'], $atg_table['is_required']);
                        $query->addInsertValue(++$i, $atg_table['sort_order']);
                        $application->db->getDB_Result($query);
                    };
                };
            };
        };

        return;
    }

    function __install_AddAttrsVisibleNames()
    {
        global $application;
        $MR = &$application->getInstance('MessageResources','customer-account-messages','AdminZone');
        $tables = Customer_Account::getTables();
        $atg_table = $tables['ca_attrs_to_groups']['columns'];

        foreach($this->attr_lang_code as $attr_id => $lang_code)
        {
            $query = new DB_Update('ca_attrs_to_groups');
            $query->addUpdateValue($atg_table['visible_name'], $MR->getMessage($lang_code));
            $query->WhereValue($atg_table['attr_id'], DB_EQ, $attr_id);
            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        };

        return;
    }

    function __install_ProcessSettings($tag)
    {
        global $application;
        $tables = Customer_Account::getTables();
        $settings_table = $tables['ca_settings']['columns'];

        foreach($tag->children as $s_child)
        {
            if($s_child->name == 'SETTING')
            {
                $key = $s_child->attributes['KEY'];
                $value = $s_child->attributes['VALUE'];

                if(preg_match("/^const\((.+)\)/i",$value,$matches))
                {
                    $value = constant($matches[1]);
                };

                $query = new DB_Insert('ca_settings');
                $query->addInsertValue($key, $settings_table['setting_key']);
                $query->addInsertValue($value, $settings_table['setting_value']);
                $application->db->PrepareSQL($query);
                $application->db->DB_Exec();
            };
        };
    }

    function __install_addEventsHandlers()
    {
        modApiFunc('EventsManager','addEventHandler','OrderCreated','Customer_Account','OnOrderCreated');
        modApiFunc('EventsManager','addEventHandler','CheckoutPersonInfoFieldUpdated','Customer_Account','OnCheckoutPersonInfoFieldUpdated');
        modApiFunc('EventsManager','addEventHandler','CheckoutAttributesSortOrderUpdated','Customer_Account','OnCheckoutAttributesSortOrderUpdated');
    }

    var $attr_lang_code;
};

?>