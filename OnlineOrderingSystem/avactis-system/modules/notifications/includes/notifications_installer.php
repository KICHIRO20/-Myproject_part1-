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
 * @package Notifications
 * @author Egor V. Derevyankin
 *
 */

define('NOTIFICATIONS_INSTALL_DATA_XML_FILE',dirname(__FILE__).'/install_data.xml');
define('NOTIFICATIONS_INSTALL_DATA_BLOCKTAGS_DIR',dirname(__FILE__).'/install_data/blocktags/');
define('NOTIFICATIONS_INSTALL_DATA_NOTIFICATIONS_DIR',dirname(__FILE__).'/install_data/notifications/');

/*
 *         ,                                           ,                      Checkout
 *                      .
 * :   -        ,                                                  ModulesManager' .
 */
_use(dirname(__FILE__).'/../../checkout/const.php');

class Notifications_Installer
{
    function Notifications_Installer()
    {
        $this->infotags = array();
        $this->blocktags = array();
        $this->actions = array();
        loadCoreFile('db_multiple_insert.php');

        global $application;
        $this->MR = &$application->getInstance('MessageResources','notifications-messages','AdminZone');
        $this->SYS_MR = &$application->getInstance('MessageResources','system-messages','AdminZone');

        //                                RemovePersonInfoType
        //                   ,          -                            .
        modApiFunc('EventsManager',
               'addEventHandler',
               'RemoveAdmin',
               'Notifications',
               'OnRemoveAdmin');
    }

    function doInstall()
    {
        loadCoreFile('obj_xml.php');
        $parser = new xml_doc(file_get_contents(NOTIFICATIONS_INSTALL_DATA_XML_FILE));
        $parser->parse();

        foreach($parser->xml_index as $tag)
        {
            if($tag->name == 'NOTIFICATIONS_INSTALL_DATA')
            {
                foreach($tag->children as $id_child)
                {
                    switch($id_child->name)
                    {
                        case 'INFOTAGS':
                            $this->__install_ProcessInfotags($id_child);
                            break;
                        case 'BLOCKTAGS':
                            $this->__install_ProcessBlocktags($id_child);
                            break;
                        case 'ACTIONS':
                            $this->__install_ProcessActions($id_child);
                            break;
                        case 'NOTIFICATIONS':
                            $this->__install_ProcessNotifications($id_child);
                            break;
                    };
                };
            };
        };

        $this->__install_addEventsHandlers();

        return;
    }

    function __install_ProcessInfotags($xml_tag)
    {
        global $application;

        foreach($xml_tag->children as $child)
        {
            if($child->name == 'GROUP')
            {
                foreach($child->children as $group_child)
                {
                    if($group_child->name == 'TAG')
                    {
                        $infotag_name = $group_child->attributes['NAME'];
                        if(!array_key_exists('NOPREFIX',$group_child->attributes) or $group_child->attributes['NOPREFIX']!='true')
                        {
                            $infotag_name = $child->attributes['NAME'].$infotag_name;
                        };

                        $query = new DB_Insert('notification_infotags');
                        $query->addInsertValue($this->__str2tag($infotag_name),'infotag_name');

                        $application->db->PrepareSQL($query);
                        $application->db->DB_Exec();

                        $this->infotags[$child->attributes['NAME']][$group_child->attributes['NAME']] = $application->db->DB_Insert_Id();
                    };
                };
            };
        };

        return;
    }

    function __install_ProcessBlocktags($xml_tag)
    {
        global $application;

        foreach($xml_tag->children as $child)
        {
            if($child->name == 'BLOCKTAG')
            {
                $query = new DB_Insert('notification_blocktags');
                $query->addInsertValue($this->__str2tag($child->attributes['NAME']), 'blocktag_name');

                $application->db->PrepareSQL($query);
                $application->db->DB_Exec();

                $blocktag_id = $application->db->DB_Insert_Id();

                $this->blocktags[$child->attributes['NAME']]['id'] = $blocktag_id;

                foreach($child->children as $blocktag_child)
                {
                    switch($blocktag_child->name)
                    {
                        case 'TEXTFILE':
                            $this->blocktags[$child->attributes['NAME']]['textfile'] = $blocktag_child->attributes['NAME'];
                            break;
                        case 'INFOTAGS':
                            foreach($blocktag_child->children as $infotags_child)
                            {
                                if($infotags_child->name == 'GROUP')
                                {
                                    $infotags_ids = $this->__process_infotags_group_tag($infotags_child);

                                    $query = new DB_Multiple_Insert('infotags_to_blocktag');
                                    $query->setInsertFields(array('notification_blocktag_id','notification_infotag_id'));
                                    foreach($infotags_ids as $infotag_id)
                                    {
                                        $query->addInsertValuesArray(array(
                                            'notification_blocktag_id' => $blocktag_id
                                           ,'notification_infotag_id' => $infotag_id
                                        ));
                                    };

                                    $application->db->PrepareSQL($query);
                                    $application->db->DB_Exec();
                                };
                            };
                            break;
                    };
                };
            };
        };

        return;
    }

    function __install_ProcessActions($xml_tag)
    {
        global $application;

        foreach($xml_tag->children as $child)
        {
            if($child->name == 'ACTION')
            {
                $action_name = $child->attributes['NAME'];
                $action_lang = $this->MR->getMessage($child->attributes['LANG_CODE']);

                $query = new DB_Insert('notification_actions');
                $query->addInsertValue($action_name, 'action_code');
                $query->addInsertValue($action_lang, 'action_name');
                $application->db->PrepareSQL($query);
                $application->db->DB_Exec();

                $action_id = $application->db->DB_Insert_Id();

                $this->actions[$action_name]['id'] = $action_id;

                foreach($child->children as $action_child)
                {
                    if($action_child->name == 'INFOTAGS')
                    {
                        foreach($action_child->children as $infotags_child)
                        {
                            if($infotags_child->name =='GROUP')
                            {
                                $infotags_ids = $this->__process_infotags_group_tag($infotags_child);

                                $query = new DB_Multiple_Insert('infotags_to_action');
                                $query->setInsertFields(array('notification_action_id','notification_infotag_id'));
                                foreach($infotags_ids as $infotag_id)
                                {
                                    $query->addInsertValuesArray(array(
                                        'notification_action_id' => $action_id
                                       ,'notification_infotag_id' => $infotag_id
                                    ));
                                };

                                $application->db->PrepareSQL($query);
                                $application->db->DB_Exec();
                            };
                        };
                    }
                    elseif($action_child->name == 'BLOCKTAGS')
                    {
                        foreach($action_child->children as $blocktags_child)
                        {
                            if($blocktags_child->name == 'BLOCKTAG')
                            {
                                $blocktag_id = $this->blocktags[$blocktags_child->attributes['NAME']]['id'];

                                $query = new DB_Insert('blocktags_to_action');
                                $query->addInsertValue($action_id, 'notification_action_id');
                                $query->addInsertValue($blocktag_id, 'notification_blocktag_id');
                                $application->db->PrepareSQL($query);
                                $application->db->DB_Exec();

                                $this->actions[$action_name]['blocktags'][] = $blocktags_child->attributes['NAME'];
                            };
                        };
                    }
                    elseif($action_child->name == 'OPTIONS')
                    {
                        foreach($action_child->children as $options_child)
                        {
                            if($options_child->name == 'OPTION')
                            {
                                $option_name = $options_child->attributes['NAME'];

                                $query = new DB_Insert('notification_action_options');
                                $query->addInsertValue($action_id, 'notification_action_id');
                                $query->addInsertValue($options_child->attributes['LANG_CODE'],'option_name');
                                $query->addInsertValue($options_child->attributes['INPUT_TYPE'],'option_input_type');
                                $application->db->PrepareSQL($query);
                                $application->db->DB_Exec();

                                $option_id = $application->db->DB_Insert_Id();
                                $this->actions[$action_name]['options'][$option_name]['id'] = $option_id;

                                foreach($options_child->children as $option_child)
                                {
                                    if($option_child->name == 'VALUE')
                                    {
                                        $value_name = $option_child->attributes['NAME'];
                                        $value_key = $option_child->attributes['KEY'];
                                        if(preg_match("/const\((.+)\)/i",$value_key,$matches))
                                        {
                                            $value_key = constant($matches[1]);
                                        };
                                        $value_lang = $option_child->attributes['LANG_CODE'];

                                        $query = new DB_Insert('notification_action_option_values');
                                        $query->addInsertValue($option_id,'notification_action_option_id');
                                        $query->addInsertValue($value_lang,'option_value_name');
                                        $query->addInsertValue($value_key,'option_key');
                                        $application->db->PrepareSQL($query);
                                        $application->db->DB_Exec();

                                        $value_id = $application->db->DB_Insert_Id();
                                        $this->actions[$action_name]['options'][$option_name]['values'][$value_name] = $value_id;
                                    };
                                };
                            };
                        };
                    };
                };
            };
        };

        return;
    }

    function __install_ProcessNotifications($xml_tag)
    {
        global $application;

        foreach($xml_tag->children as $child)
        {
            if($child->name == 'NOTIFICATION')
            {
                $_txt_content = array_map("rtrim",file(NOTIFICATIONS_INSTALL_DATA_NOTIFICATIONS_DIR.$child->attributes['TEXTFILE']));
                $ntf_action_name = $child->attributes['ACTION'];

                $ntf_action_id = $this->actions[$ntf_action_name]['id'];
                $ntf_name = array_shift($_txt_content);
                $ntf_subject = array_shift($_txt_content);
                $ntf_body = implode("\n",$_txt_content);

                //+ :             xml
                $ntf_from_email_custom_address = '';
                $ntf_from_email_code = 'EMAIL_STORE_OWNER';
                $ntf_active = 'checked';
                //-

                $query = new DB_Insert('notifications');
                $query->addInsertValue($ntf_action_id, 'notification_action_id');
                $query->addInsertValue($ntf_name, 'notification_name');
                $query->addInsertValue($ntf_subject, 'notification_subject');
                $query->addInsertValue($ntf_body, 'notification_body');
                $query->addInsertValue($ntf_from_email_custom_address, 'notification_from_email_custom_address');
                $query->addInsertValue($ntf_from_email_code, 'notification_from_email_code');
                $query->addInsertValue($ntf_active, 'notification_active');
                $application->db->PrepareSQL($query);
                $application->db->DB_Exec();

                $notification_id = $application->db->DB_Insert_Id();

                foreach($child->children as $ntf_child)
                {
                    if($ntf_child->name == 'RECIPIENTS')
                    {
                        $query = new DB_Multiple_Insert('notification_send_to');
                        $query->setInsertFields(array('notification_id','email','email_code'));
                        $insert_array = array(
                            'notification_id' => $notification_id
                           ,'email' => '' //:           xml
                           ,'email_code' => ''
                        );

                        foreach($ntf_child->children as $rec_child)
                        {
                            if($rec_child->name == 'RECIPIENT')
                            {
                                $insert_array['email_code'] = $rec_child->attributes['CODE'];
                                $query->addInsertValuesArray($insert_array);
                            };
                        };

                        $application->db->PrepareSQL($query);
                        $application->db->DB_Exec();
                    }
                    elseif($ntf_child->name == 'OPTIONS')
                    {
                        foreach($ntf_child->children as $options_child)
                        {
                            if($options_child->name == 'OPTION')
                            {
                                $option_name = $options_child->attributes['NAME'];
                                $default_value = $options_child->attributes['DEFAULT_VALUE'];

                                $option_values = $this->actions[$ntf_action_name]['options'][$option_name]['values'];

                                $query = new DB_Multiple_Insert('option_values_to_notification');
                                $query->setInsertFields(array('notification_action_option_value_id','notification_id','value'));
                                $insert_array = array(
                                    'notification_action_option_value_id' => 0
                                   ,'notification_id' => $notification_id
                                   ,'value' => $default_value
                                );

                                $_values = array();
                                foreach($options_child->children as $option_child)
                                {
                                    if($option_child->name == 'VALUE')
                                    {
                                        $value_name = $option_child->attributes['NAME'];
                                        $value_value = $option_child->attributes['VALUE'];
                                        $_values[$value_name] = $value_value;
                                    };
                                };

                                foreach($option_values as $value_name => $value_id)
                                {
                                    $_arr = $insert_array;
                                    $_arr['notification_action_option_value_id'] = $value_id;
                                    if(array_key_exists($value_name, $_values))
                                    {
                                        $_arr['value'] = $_values[$value_name];
                                    };

                                    $query->addInsertValuesArray($_arr);
                                };

                                $application->db->PrepareSQL($query);
                                $application->db->DB_Exec();
                            };
                        };
                    };
                };

                //                 blocktags
                $ntf_blocktags = array();
                if(array_key_exists('blocktags', $this->actions[$ntf_action_name]))
                {
                    $ntf_blocktags = $this->actions[$ntf_action_name]['blocktags'];
                };

                if(!empty($ntf_blocktags))
                {
                    $insert_array = array(
                        'notification_blocktag_id' => 0
                       ,'notification_id' => $notification_id
                       ,'blocktag_body' => ''
                    );

                    $query = new DB_Multiple_Insert('notification_blocktag_bodies');
                    $query->setInsertFields(array_keys($insert_array));

                    foreach($ntf_blocktags as $blocktag_name)
                    {
                        $blocktag_id = $this->blocktags[$blocktag_name]['id'];
                        $blocktag_txt = $this->blocktags[$blocktag_name]['textfile'];
                        $blocktag_body = file_get_contents(NOTIFICATIONS_INSTALL_DATA_BLOCKTAGS_DIR.$blocktag_txt);

                        $_arr = $insert_array;

                        $_arr['notification_blocktag_id'] = $blocktag_id;
                        $_arr['blocktag_body'] = $blocktag_body;

                        $query->addInsertValuesArray($_arr);
                    };

                    $application->db->PrepareSQL($query);
                    $application->db->DB_Exec();
                };
            };
        };

        return;
    }

    function __install_addEventsHandlers()
    {
        //Customer Account events
        modApiFunc('EventsManager','addEventHandler','CustomerRegistered','Notifications','OnCustomerRegistered');
        modApiFunc('EventsManager','addEventHandler','CustomerShouldActivateSelf','Notifications','OnCustomerShouldActivateSelf');
        modApiFunc('EventsManager','addEventHandler','AdminShouldActivateCustomer','Notifications','OnAdminShouldActivateCustomer');
        modApiFunc('EventsManager','addEventHandler','CustomerActivateSelf','Notifications','OnCustomerActivateSelf');
        modApiFunc('EventsManager','addEventHandler','AdminActivateCustomer','Notifications','OnAdminActivateCustomer');
        modApiFunc('EventsManager','addEventHandler','AdminDropCustomerPassword','Notifications','OnAdminDropCustomerPassword');
        modApiFunc('EventsManager','addEventHandler','CustomerPasswordDroped','Notifications','OnCustomerPasswordDroped');
        modApiFunc('EventsManager','addEventHandler','AccountWasAutoCreated','Notifications','OnAccountWasAutoCreated');

        //Product Options events
        modApiFunc('EventsManager','addEventHandler','InventoryLowLevel','Notifications','OnInventoryLowLevel');

        //Gift Certificates
        modApiFunc('EventsManager','addEventHandler','GiftCertificateCreated','Notifications','OnGiftCertificateCreated');
        modApiFunc('EventsManager','addEventHandler','GiftCertificatePurchased','Notifications','OnGiftCertificatePurchased');
    }

    function __str2tag($str)
    {
        return '{'.strval($str).'}';
    }

    function __process_infotags_group_tag($xml_tag)
    {
        $group_name = $xml_tag->attributes['NAME'];

        $only = array();
        $except = array();

        if(!empty($xml_tag->children))
        {
            foreach($xml_tag->children as $child)
            {
                if($child->name == 'EXCEPT')
                {
                    foreach($child->children as $infotag)
                    {
                        $except[] = $infotag->attributes['NAME'];
                    };
                }
                elseif($child->name == 'ONLY')
                {
                    foreach($child->children as $infotag)
                    {
                        $only[] = $infotag->attributes['NAME'];
                    };
                };
            };
        };

        $group_tags = $this->infotags[$group_name];

        if(!empty($only))
        {
            foreach($group_tags as $tag_name => $tag_id)
            {
                if(!in_array($tag_name,$only))
                {
                    unset($group_tags[$tag_name]);
                };
            };
        }
        elseif(!empty($except))
        {
            foreach($group_tags as $tag_name => $tag_id)
            {
                if(in_array($tag_name,$except))
                {
                    unset($group_tags[$tag_name]);
                };
            };
        };

        return array_values($group_tags);
    }

    var $infotags;
    var $blocktags;
    var $actions;
    var $MR;
    var $SYS_MR;
};

?>